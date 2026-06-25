<?php

use App\Models\Manager;
use App\Models\Salesman;
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

    $this->salesman = Salesman::create([
        'manager_id' => $this->manager->manager_id,
        'name' => 'Salesman Test',
        'username' => 'salesmantest',
        'email' => 'salesman@test.com',
        'password' => Hash::make('password'),
    ]);

    $this->otherSalesman = Salesman::create([
        'manager_id' => $this->manager->manager_id,
        'name' => 'Other Salesman',
        'username' => 'other_salesman',
        'email' => 'other_salesman@test.com',
        'password' => Hash::make('password'),
    ]);
});

test('salesman can access their own sales report page', function () {
    $response = $this->actingAs($this->salesman, 'salesman')
        ->get(route('reports.salesman_personal.index'));

    $response->assertStatus(200);
    $response->assertSee('Personal Performance');
});

test('manager cannot access the salesman sales report page', function () {
    $response = $this->actingAs($this->manager, 'manager')
        ->get(route('reports.salesman_personal.index'));

    // Because the route is only in auth:salesman middleware, it should redirect or throw 403.
    // Let's assert redirect or 302/403.
    $response->assertRedirect();
});

test('salesman can export their report', function () {
    $response = $this->actingAs($this->salesman, 'salesman')
        ->get(route('reports.salesman_personal.export', ['format' => 'excel']));

    $response->assertStatus(200);
    $response->assertHeader('Content-Disposition', 'attachment; filename=my_sales_report_' . now()->startOfMonth()->format('Y-m-d') . '_to_' . now()->format('Y-m-d') . '.xls');
});
