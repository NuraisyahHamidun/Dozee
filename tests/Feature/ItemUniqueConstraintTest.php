<?php

use App\Models\Manager;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->manager = Manager::create([
        'name' => 'Manager Test',
        'username' => 'managertest',
        'email' => 'manager@test.com',
        'password' => Hash::make('password'),
    ]);

    $this->category = Category::create([
        'name' => 'Test Category',
    ]);
});

test('manager can create products with same name but different volumes', function () {
    // First product
    $response1 = $this->actingAs($this->manager, 'manager')
        ->post(route('products.store'), [
            'item_name' => 'Ultra White',
            'volume' => '1KG',
            'category_id' => $this->category->id,
            'price' => 10.00,
            'stock_qty' => 50,
        ]);
    
    $response1->assertRedirect();
    $this->assertDatabaseHas('item', ['item_name' => 'Ultra White', 'volume' => '1KG']);

    // Second product with same name but different volume
    $response2 = $this->actingAs($this->manager, 'manager')
        ->post(route('products.store'), [
            'item_name' => 'Ultra White',
            'volume' => '10KG',
            'category_id' => $this->category->id,
            'price' => 80.00,
            'stock_qty' => 10,
        ]);

    $response2->assertRedirect();
    $this->assertDatabaseHas('item', ['item_name' => 'Ultra White', 'volume' => '10KG']);
});

test('manager cannot create products with same name and same volume', function () {
    // First product
    $this->actingAs($this->manager, 'manager')
        ->post(route('products.store'), [
            'item_name' => 'Ultra White',
            'volume' => '1KG',
            'category_id' => $this->category->id,
            'price' => 10.00,
            'stock_qty' => 50,
        ]);

    // Second product with same name and same volume
    $response = $this->actingAs($this->manager, 'manager')
        ->post(route('products.store'), [
            'item_name' => 'Ultra White',
            'volume' => '1KG',
            'category_id' => $this->category->id,
            'price' => 12.00,
            'stock_qty' => 20,
        ]);

    $response->assertSessionHasErrors('item_name');
});
