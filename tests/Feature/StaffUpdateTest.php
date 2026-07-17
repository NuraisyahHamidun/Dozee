<?php

use App\Models\Manager;
use App\Models\Salesmen;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    // Set up manager
    $this->manager = Manager::create([
        'name' => 'Manager Test',
        'username' => 'managertest',
        'email' => 'manager@test.com',
        'password' => Hash::make('password'),
    ]);

    // Set up salesmen
    $this->salesmen = Salesmen::create([
        'manager_id' => $this->manager->manager_id,
        'name' => 'Staff Old Name',
        'username' => 'staffoldusername',
        'email' => 'staff@test.com',
        'password' => Hash::make('password'),
        'address' => 'Old Address',
    ]);
});

test('manager can update salesmen name and username', function () {
    $response = $this->actingAs($this->manager, 'manager')
        ->patch(route('accounts.update', $this->salesmen->salesmen_id), [
            'name' => 'Staff New Name',
            'username' => 'staffnewusername',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Salesmen profile updated successfully.');

    $this->salesmen->refresh();
    expect($this->salesmen->name)->toBe('Staff New Name');
    expect($this->salesmen->username)->toBe('staffnewusername');
});

test('manager cannot set duplicate username', function () {
    // Create another salesmen
    Salesmen::create([
        'manager_id' => $this->manager->manager_id,
        'name' => 'Other Staff',
        'username' => 'duplicateusername',
        'email' => 'other@test.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->actingAs($this->manager, 'manager')
        ->patch(route('accounts.update', $this->salesmen->salesmen_id), [
            'name' => 'Staff New Name',
            'username' => 'duplicateusername',
        ]);

    $response->assertSessionHasErrors('username');
    
    $this->salesmen->refresh();
    expect($this->salesmen->username)->toBe('staffoldusername'); // unchanged
});

test('manager cannot edit email and address of salesmen on update', function () {
    $response = $this->actingAs($this->manager, 'manager')
        ->patch(route('accounts.update', $this->salesmen->salesmen_id), [
            'name' => 'Staff New Name',
            'username' => 'staffnewusername',
            'email' => 'newemail@test.com',
            'address' => 'New Address',
        ]);

    $this->salesmen->refresh();
    expect($this->salesmen->name)->toBe('Staff New Name');
    expect($this->salesmen->username)->toBe('staffnewusername');
    expect($this->salesmen->email)->toBe('staff@test.com'); // unchanged
    expect($this->salesmen->address)->toBe('Old Address'); // unchanged
});

test('non manager cannot update salesmen profile', function () {
    $response = $this->actingAs($this->salesmen, 'salesmen')
        ->patch(route('accounts.update', $this->salesmen->salesmen_id), [
            'name' => 'Staff New Name',
            'username' => 'staffnewusername',
        ]);

    $response->assertRedirect();
});
