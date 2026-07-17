<?php

use App\Models\Manager;
use App\Models\Salesmen;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    // Set up a manager
    $this->manager = Manager::create([
        'name' => 'Manager Test',
        'username' => 'managertest',
        'email' => 'manager@test.com',
        'password' => Hash::make('password'),
    ]);

    // Set up a salesmen
    $this->salesmen = Salesmen::create([
        'manager_id' => $this->manager->manager_id,
        'name' => 'Salesmen Test',
        'username' => 'salestest',
        'email' => 'salesmen@test.com',
        'password' => Hash::make('password'),
    ]);

    // Set up a product (item)
    $this->product = Product::create([
        'item_name' => 'Test Product',
        'volume' => '1L',
        'price' => 10.00,
        'stock_qty' => 50,
        'category' => 'Laundry',
    ]);
});

test('salesmen can create a sale with pending status and date_create populated', function () {
    $response = $this->actingAs($this->salesmen, 'salesmen')
        ->post(route('sales.store'), [
            'event_name' => 'Test Event',
            'sale_date' => now()->setHour(12)->setMinute(0)->format('Y-m-d H:i:s'),
            'items' => [
                [
                    'product_id' => $this->product->item_id,
                    'quantity' => 2,
                    'promo_id' => null,
                ]
            ]
        ]);

    $response->assertRedirect(route('sales.index'));
    $this->assertDatabaseHas('sales_transaction', [
        'salesmen_id' => $this->salesmen->salesmen_id,
        'event_name' => 'Test Event',
        'status' => 'Pending',
    ]);

    $sale = Sale::first();
    expect($sale->status)->toBe('Pending');
    expect($sale->date_create)->not->toBeNull();
    expect($sale->date_modifier)->toBeNull();
    expect($sale->date_verify)->toBeNull();
});

test('salesmen cannot edit a pending sale', function () {
    // Create a pending sale
    $sale = Sale::create([
        'salesmen_id' => $this->salesmen->salesmen_id,
        'event_name' => 'Event A',
        'total_amount' => 10.00,
        'sale_date' => now(),
        'status' => 'Pending',
    ]);

    // Try to view edit page
    $response = $this->actingAs($this->salesmen, 'salesmen')
        ->get(route('sales.edit', $sale));

    $response->assertRedirect(route('sales.index'));
    $response->assertSessionHas('error', 'You cannot edit this sale until it is approved by a Manager.');

    // Try to update sale
    $responseUpdate = $this->actingAs($this->salesmen, 'salesmen')
        ->put(route('sales.update', $sale), [
            'event_name' => 'Updated Event',
            'items' => [
                [
                    'product_id' => $this->product->item_id,
                    'quantity' => 1,
                    'promo_id' => null,
                ]
            ]
        ]);

    $responseUpdate->assertRedirect(route('sales.index'));
    $responseUpdate->assertSessionHas('error', 'You cannot edit this sale until it is approved by a Manager.');
});

test('manager can approve sale and verification timestamp is set', function () {
    // Create pending sale
    $sale = Sale::create([
        'salesmen_id' => $this->salesmen->salesmen_id,
        'event_name' => 'Event A',
        'total_amount' => 10.00,
        'sale_date' => now(),
        'status' => 'Pending',
    ]);

    // Approve as manager
    $response = $this->actingAs($this->manager, 'manager')
        ->post(route('sales.approve', $sale));

    $response->assertRedirect(route('sales.index'));
    $response->assertSessionHas('success', 'Sale transaction approved successfully.');

    $sale->refresh();
    expect($sale->status)->toBe('Approved');
    expect($sale->date_verify)->not->toBeNull();
});

test('salesmen can edit approved sale and modification timestamp updates', function () {
    // Create approved sale
    $sale = Sale::create([
        'salesmen_id' => $this->salesmen->salesmen_id,
        'event_name' => 'Event A',
        'total_amount' => 10.00,
        'sale_date' => now(),
        'status' => 'Approved',
    ]);

    // Add item detail to link
    $sale->saleItems()->create([
        'item_id' => $this->product->item_id,
        'quantity' => 1,
    ]);

    // Edit as salesmen
    $response = $this->actingAs($this->salesmen, 'salesmen')
        ->put(route('sales.update', $sale), [
            'event_name' => 'Modified Event Name',
            'items' => [
                [
                    'detail_id' => $sale->saleItems->first()->detail_id,
                    'product_id' => $this->product->item_id,
                    'quantity' => 3,
                    'promo_id' => null,
                ]
            ]
        ]);

    $response->assertRedirect(route('sales.index'));
    $response->assertSessionHas('success', 'Sale updated successfully.');

    $sale->refresh();
    expect($sale->event_name)->toBe('Modified Event Name');
    expect($sale->date_modifier)->not->toBeNull();
});

test('manager can reject sale and items return to stock', function () {
    $initialStock = $this->product->stock_qty; // 50

    // Create pending sale
    $sale = Sale::create([
        'salesmen_id' => $this->salesmen->salesmen_id,
        'event_name' => 'Event A',
        'total_amount' => 10.00,
        'sale_date' => now(),
        'status' => 'Pending',
    ]);

    $sale->saleItems()->create([
        'item_id' => $this->product->item_id,
        'quantity' => 5, // sold 5, so we expect stock to be incremented back on reject
    ]);

    // Reject as manager
    $response = $this->actingAs($this->manager, 'manager')
        ->post(route('sales.reject', $sale));

    $response->assertRedirect(route('sales.index'));
    $response->assertSessionHas('success');

    $sale->refresh();
    expect($sale->status)->toBe('Rejected');
    expect($sale->date_verify)->not->toBeNull();

    // Verify stock is restored
    $this->product->refresh();
    expect($this->product->stock_qty)->toBe($initialStock + 5);
});

test('salesmen can view dashboard and get chart data', function () {
    // Access dashboard view
    $response = $this->actingAs($this->salesmen, 'salesmen')
        ->get(route('dashboard'));

    $response->assertOk();

    // Access dashboard data API
    $apiResponse = $this->actingAs($this->salesmen, 'salesmen')
        ->get(route('dashboard.data', [
            'start_date' => now()->subDays(30)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'sort_by' => 'quantity'
        ]));

    $apiResponse->assertOk();
    $data = $apiResponse->json();
    expect($data)->toHaveKeys(['daily', 'monthly', 'promo', 'combo', 'topItems', 'apriori', 'categoryDistribution']);
});

test('manager can bulk approve selected sales', function () {
    // Create pending sales for the salesmen
    $sale1 = Sale::create([
        'salesmen_id' => $this->salesmen->salesmen_id,
        'event_name' => 'Event X',
        'total_amount' => 10.00,
        'sale_date' => now(),
        'status' => 'Pending',
    ]);
    $sale2 = Sale::create([
        'salesmen_id' => $this->salesmen->salesmen_id,
        'event_name' => 'Event Y',
        'total_amount' => 20.00,
        'sale_date' => now(),
        'status' => 'Pending',
    ]);
    $sale3 = Sale::create([
        'salesmen_id' => $this->salesmen->salesmen_id,
        'event_name' => 'Event Z',
        'total_amount' => 30.00,
        'sale_date' => now(),
        'status' => 'Pending',
    ]);

    // Approve selected $sale1 and $sale2
    $response = $this->actingAs($this->manager, 'manager')
        ->post(route('reports.salesmen.approve-selected', ['id' => $this->salesmen->salesmen_id]), [
            'sale_ids' => [$sale1->transaction_id, $sale2->transaction_id]
        ]);

    $response->assertOk();
    $response->assertJson([
        'success' => true,
        'approved_count' => 2,
    ]);

    $sale1->refresh();
    $sale2->refresh();
    $sale3->refresh();

    expect($sale1->status)->toBe('Approved');
    expect($sale1->date_verify)->not->toBeNull();
    expect($sale2->status)->toBe('Approved');
    expect($sale2->date_verify)->not->toBeNull();
    expect($sale3->status)->toBe('Pending'); // unaffected
    expect($sale3->date_verify)->toBeNull();
});

test('non-managers cannot bulk approve sales', function () {
    $sale = Sale::create([
        'salesmen_id' => $this->salesmen->salesmen_id,
        'event_name' => 'Event X',
        'total_amount' => 10.00,
        'sale_date' => now(),
        'status' => 'Pending',
    ]);

    $response = $this->actingAs($this->salesmen, 'salesmen')
        ->post(route('reports.salesmen.approve-selected', ['id' => $this->salesmen->salesmen_id]), [
            'sale_ids' => [$sale->transaction_id]
        ]);

    $response->assertRedirect();
});
