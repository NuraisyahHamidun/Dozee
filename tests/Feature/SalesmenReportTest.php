<?php

use App\Models\Manager;
use App\Models\Salesmen;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->manager = Manager::create([
        'name' => 'Manager Test',
        'username' => 'managertest',
        'email' => 'manager@test.com',
        'password' => Hash::make('password'),
    ]);

    $this->salesmen = Salesmen::create([
        'manager_id' => $this->manager->manager_id,
        'name' => 'Salesmen Test',
        'username' => 'salesmentest',
        'email' => 'salesmen@test.com',
        'password' => Hash::make('password'),
    ]);

    $this->otherSalesmen = Salesmen::create([
        'manager_id' => $this->manager->manager_id,
        'name' => 'Other Salesmen',
        'username' => 'other_salesmen',
        'email' => 'other_salesmen@test.com',
        'password' => Hash::make('password'),
    ]);
});

test('salesmen can access their own sales report page', function () {
    $response = $this->actingAs($this->salesmen, 'salesmen')
        ->get(route('reports.salesmen_personal.index'));

    $response->assertStatus(200);
    $response->assertSee('Personal Performance');
});

test('manager cannot access the salesmen sales report page', function () {
    $response = $this->actingAs($this->manager, 'manager')
        ->get(route('reports.salesmen_personal.index'));

    // Because the route is only in auth:salesmen middleware, it should redirect or throw 403.
    // Let's assert redirect or 302/403.
    $response->assertRedirect();
});

test('salesmen can export their report', function () {
    $response = $this->actingAs($this->salesmen, 'salesmen')
        ->get(route('reports.salesmen_personal.export', ['format' => 'excel']));

    $response->assertStatus(200);
    $response->assertHeader('Content-Disposition', 'attachment; filename=my_sales_report_' . now()->startOfMonth()->format('Y-m-d') . '_to_' . now()->format('Y-m-d') . '.xls');
});
