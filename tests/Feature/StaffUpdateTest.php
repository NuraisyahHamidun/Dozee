<?php

use App\Models\Manager;
use App\Models\Salesman;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    // Set up manager
    $this->manager = Manager::create([
        'name' => 'Manager Test',
        'username' => 'managertest',
        'email' => 'manager@test.com',
        'password' => Hash::make('password'),
    ]);

    // Set up salesman
    $this->salesman = Salesman::create([
        'manager_id' => $this->manager->manager_id,
        'name' => 'Staff Old Name',
        'username' => 'staffoldusername',
        'email' => 'staff@test.com',
        'password' => Hash::make('password'),
        'address' => 'Old Address',
    ]);
});

test('manager can update salesman name and username', function () {
    $response = $this->actingAs($this->manager, 'manager')
        ->patch(route('accounts.update', $this->salesman->salesman_id), [
            'name' => 'Staff New Name',
            'username' => 'staffnewusername',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Staff profile updated successfully.');

    $this->salesman->refresh();
    expect($this->salesman->name)->toBe('Staff New Name');
    expect($this->salesman->username)->toBe('staffnewusername');
});

test('manager cannot set duplicate username', function () {
    // Create another salesman
    Salesman::create([
        'manager_id' => $this->manager->manager_id,
        'name' => 'Other Staff',
        'username' => 'duplicateusername',
        'email' => 'other@test.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->actingAs($this->manager, 'manager')
        ->patch(route('accounts.update', $this->salesman->salesman_id), [
            'name' => 'Staff New Name',
            'username' => 'duplicateusername',
        ]);

    $response->assertSessionHasErrors('username');
    
    $this->salesman->refresh();
    expect($this->salesman->username)->toBe('staffoldusername'); // unchanged
});

test('manager cannot edit email and address of salesman on update', function () {
    $response = $this->actingAs($this->manager, 'manager')
        ->patch(route('accounts.update', $this->salesman->salesman_id), [
            'name' => 'Staff New Name',
            'username' => 'staffnewusername',
            'email' => 'newemail@test.com',
            'address' => 'New Address',
        ]);

    $this->salesman->refresh();
    expect($this->salesman->name)->toBe('Staff New Name');
    expect($this->salesman->username)->toBe('staffnewusername');
    expect($this->salesman->email)->toBe('staff@test.com'); // unchanged
    expect($this->salesman->address)->toBe('Old Address'); // unchanged
});

test('non manager cannot update salesman profile', function () {
    $response = $this->actingAs($this->salesman, 'salesman')
        ->patch(route('accounts.update', $this->salesman->salesman_id), [
            'name' => 'Staff New Name',
            'username' => 'staffnewusername',
        ]);

    $response->assertRedirect();
});
