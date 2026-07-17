<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\Manager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/manager/register');

        $response->assertStatus(200);
    }

    public function test_new_managers_can_register_with_valid_phone_number_and_staff_code_is_generated()
    {
        $response = $this->post('/manager/register', [
            'name' => 'Manager Test',
            'username' => 'managertest',
            'email' => 'manager@test.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'address' => '123 Manager Way',
            'phone_number' => '012-3456789',
        ]);

        $response->assertRedirect(route('manager.login'));
        
        $this->assertDatabaseHas('manager', [
            'name' => 'Manager Test',
            'username' => 'managertest',
            'email' => 'manager@test.com',
            'phone_number' => '012-3456789',
        ]);

        $manager = Manager::where('username', 'managertest')->first();
        $this->assertNotNull($manager->staff_code);
        
        // Assert format: STF-6789-XX
        $this->assertStringStartsWith('STF-6789-', $manager->staff_code);
        $this->assertEquals(11, strlen($manager->staff_code)); // STF- (4) + 6789 (4) + - (1) + XX (2) = 11 characters
    }

    public function test_manager_registration_fails_with_invalid_phone_number()
    {
        // Missing phone number
        $response = $this->post('/manager/register', [
            'name' => 'Manager Test',
            'username' => 'managertest',
            'email' => 'manager@test.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'address' => '123 Manager Way',
        ]);
        $response->assertSessionHasErrors(['phone_number']);

        // Invalid: too long (12 digits total, 13 with dash)
        $response = $this->post('/manager/register', [
            'name' => 'Manager Test',
            'username' => 'managertest',
            'email' => 'manager@test.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'address' => '123 Manager Way',
            'phone_number' => '012-345678901',
        ]);
        $response->assertSessionHasErrors(['phone_number' => 'Invalid phone number format. Only numbers allowed (10–11 digits). Example: 0123456789 or 012-3456789']);

        // Invalid: non-numeric
        $response = $this->post('/manager/register', [
            'name' => 'Manager Test',
            'username' => 'managertest',
            'email' => 'manager@test.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'address' => '123 Manager Way',
            'phone_number' => 'abc-de12345',
        ]);
        $response->assertSessionHasErrors(['phone_number' => 'Invalid phone number format. Only numbers allowed (10–11 digits). Example: 0123456789 or 012-3456789']);

        // Invalid: too short (9 digits total, 10 with dash)
        $response = $this->post('/manager/register', [
            'name' => 'Manager Test',
            'username' => 'managertest',
            'email' => 'manager@test.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'address' => '123 Manager Way',
            'phone_number' => '012-345678',
        ]);
        $response->assertSessionHasErrors(['phone_number' => 'Invalid phone number format. Only numbers allowed (10–11 digits). Example: 0123456789 or 012-3456789']);
    }

    public function test_new_managers_can_register_with_international_phone_number_format()
    {
        $response = $this->post('/manager/register', [
            'name' => 'Manager Test International',
            'username' => 'managertestint',
            'email' => 'managerint@test.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'address' => '123 Manager Way',
            'phone_number' => '601-23456789',
        ]);

        $response->assertRedirect(route('manager.login'));
        
        $this->assertDatabaseHas('manager', [
            'name' => 'Manager Test International',
            'username' => 'managertestinternational',
            'email' => 'managerint@test.com',
            'phone_number' => '601-23456789',
        ]);

        $manager = Manager::where('username', 'managertestinternational')->first();
        $this->assertNotNull($manager->staff_code);
        
        // Assert format: STF-6789-XX
        $this->assertStringStartsWith('STF-6789-', $manager->staff_code);
        $this->assertEquals(11, strlen($manager->staff_code));
    }

    public function test_manager_registration_preserves_old_inputs_on_validation_failure()
    {
        $response = $this->post('/manager/register', [
            'name' => 'Failed Manager',
            'username' => 'failed_man',
            'email' => 'invalid-email', // will fail validation
            'address' => '456 Failed Rd',
            'phone_number' => '012-3456789',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);

        $response->assertStatus(302); // redirect back
        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHasInput('name', 'Failed Manager');
        $response->assertSessionHasInput('username', 'failedmanager');
        $response->assertSessionHasInput('address', '456 Failed Rd');
        $response->assertSessionHasInput('phone_number', '012-3456789');
        $this->assertFalse(session()->hasOldInput('password'));
    }

    public function test_manager_registration_auto_generates_unique_username_with_numeric_suffix_on_duplicate()
    {
        // Register first manager
        Manager::create([
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john1@example.com',
            'password' => Hash::make('Password@123'),
            'address' => 'Address 1',
            'phone_number' => '012-3456789',
        ]);

        // Register second manager with same name
        $response = $this->post('/manager/register', [
            'name' => 'John Doe',
            'email' => 'john2@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'address' => 'Address 2',
            'phone_number' => '012-3456788',
        ]);

        $response->assertRedirect(route('manager.login'));

        $this->assertDatabaseHas('manager', [
            'name' => 'John Doe',
            'username' => 'johndoe1',
            'email' => 'john2@example.com',
        ]);
    }
}
