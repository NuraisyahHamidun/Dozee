<?php

use App\Models\Manager;
use App\Models\Salesman;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

beforeEach(function () {
    $this->manager = Manager::create([
        'name' => 'Manager Test',
        'username' => 'managertest',
        'email' => 'manager@test.com',
        'password' => Hash::make('password'),
    ]);

    $this->salesman = Salesman::create([
        'manager_id' => $this->manager->manager_id,
        'name' => 'Salesman Test',
        'username' => 'salesmantest',
        'email' => 'salesman@test.com',
        'password' => Hash::make('password'),
    ]);

    $this->product = Product::create([
        'item_name' => 'Test Item',
        'price' => 10.00,
        'stock_qty' => 100,
    ]);
});

test('backend validation rejects past transaction date', function () {
    $pastDate = Carbon::yesterday()->setHour(12)->format('Y-m-d H:i:s');
    
    $response = $this->actingAs($this->salesman, 'salesman')
        ->post(route('sales.store'), [
            'sale_date' => $pastDate,
            'items' => [
                [
                    'product_id' => $this->product->item_id,
                    'quantity' => 1,
                ]
            ]
        ]);

    $response->assertSessionHasErrors('sale_date');
});

test('backend validation rejects transaction time before 8 AM', function () {
    $earlyTime = Carbon::tomorrow()->setHour(7)->setMinute(59)->format('Y-m-d H:i:s');

    $response = $this->actingAs($this->salesman, 'salesman')
        ->post(route('sales.store'), [
            'sale_date' => $earlyTime,
            'items' => [
                [
                    'product_id' => $this->product->item_id,
                    'quantity' => 1,
                ]
            ]
        ]);

    $response->assertSessionHasErrors('sale_date');
});

test('backend validation rejects transaction time after 8 PM', function () {
    $lateTime = Carbon::tomorrow()->setHour(20)->setMinute(01)->format('Y-m-d H:i:s');

    $response = $this->actingAs($this->salesman, 'salesman')
        ->post(route('sales.store'), [
            'sale_date' => $lateTime,
            'items' => [
                [
                    'product_id' => $this->product->item_id,
                    'quantity' => 1,
                ]
            ]
        ]);

    $response->assertSessionHasErrors('sale_date');
});

test('backend validation accepts valid date and time', function () {
    $validDateTime = Carbon::tomorrow()->setHour(12)->setMinute(0)->format('Y-m-d H:i:s');

    $response = $this->actingAs($this->salesman, 'salesman')
        ->post(route('sales.store'), [
            'sale_date' => $validDateTime,
            'items' => [
                [
                    'product_id' => $this->product->item_id,
                    'quantity' => 1,
                ]
            ]
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();
});
