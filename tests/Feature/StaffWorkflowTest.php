<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Manager;
use App\Models\Salesman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class StaffWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $manager;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a manager for testing
        $this->manager = Manager::create([
            'name' => 'Test Manager',
            'email' => 'manager@test.com',
            'username' => 'test_manager',
            'password' => Hash::make('Password@123'),
            'address' => '123 Manager St',
        ]);
    }

    /** @test */
    public function manager_can_create_staff_and_staff_code_is_automatically_generated()
    {
        $response = $this->actingAs($this->manager, 'manager')
            ->post(route('accounts.store'), [
                'name' => 'New Staff Member',
                'username' => 'newstaff',
                'email' => 'newstaff@test.com',
                'password' => 'Password@123',
                'password_confirmation' => 'Password@123',
                'address' => '456 Staff Ave',
                'phone_number' => '012-3456789',
            ]);

        $response->assertRedirect(route('accounts.index'));
        
        $this->assertDatabaseHas('salesman', [
            'name' => 'New Staff Member',
            'username' => 'newstaffmember',
            'email' => 'newstaff@test.com',
            'phone_number' => '012-3456789',
        ]);

        $salesman = Salesman::where('username', 'newstaffmember')->first();
        
        $this->assertNotNull($salesman->staff_code);
        $this->assertStringStartsWith('STF-', $salesman->staff_code);
        $this->assertEquals(10, strlen($salesman->staff_code)); // STF- + 6 characters = 10 characters
    }

    /** @test */
    public function staff_creation_auto_generates_unique_username_with_numeric_suffix_on_duplicate()
    {
        // Create first salesman with name "Nur Aisyah"
        Salesman::create([
            'manager_id' => $this->manager->manager_id,
            'name' => 'Nur Aisyah',
            'username' => 'nuraisyah',
            'email' => 'nur1@test.com',
            'password' => Hash::make('Password@123'),
            'address' => '123 Street',
            'staff_code' => 'STF-AISY01',
            'phone_number' => '012-3456788',
        ]);

        // Create second salesman with same name via POST
        $response = $this->actingAs($this->manager, 'manager')
            ->post(route('accounts.store'), [
                'name' => 'Nur Aisyah',
                'email' => 'nur2@test.com',
                'password' => 'Password@123',
                'password_confirmation' => 'Password@123',
                'address' => '456 Street',
                'phone_number' => '012-3456788',
            ]);

        $response->assertRedirect(route('accounts.index'));

        $this->assertDatabaseHas('salesman', [
            'name' => 'Nur Aisyah',
            'username' => 'nuraisyah1',
            'email' => 'nur2@test.com',
            'phone_number' => '012-3456788',
        ]);
    }

    /** @test */
    public function staff_can_update_phone_number_and_profile_picture()
    {
        Storage::fake('public');

        $salesman = Salesman::create([
            'manager_id' => $this->manager->manager_id,
            'name' => 'Staff Test',
            'username' => 'stafftest',
            'email' => 'stafftest@test.com',
            'password' => Hash::make('Password@123'),
            'address' => '789 Staff Rd',
            'staff_code' => 'STF-TEST01',
            'phone_number' => '012-3456789',
        ]);

        $file = $this->createDummyImage();

        $response = $this->actingAs($salesman, 'salesman')
            ->post(route('salesman.profile.update'), [
                '_method' => 'PATCH',
                'name' => 'Staff Test Updated',
                'username' => 'stafftest',
                'email' => 'stafftest@test.com',
                'address' => '789 Staff Rd Updated',
                'phone_number' => '012-3456789',
                'profile_picture' => $file,
            ]);

        $response->assertRedirect(route('salesman.profile.edit'));
        
        $salesman->refresh();
        $this->assertEquals('Staff Test Updated', $salesman->name);
        $this->assertEquals('012-3456789', $salesman->phone_number);
        $this->assertNotNull($salesman->profile_picture);
        
        // Assert storage has the uploaded file
        Storage::disk('public')->assertExists($salesman->profile_picture);
    }

    protected function createDummyImage(): UploadedFile
    {
        $base64Gif = 'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
        $tempFile = tempnam(sys_get_temp_dir(), 'dummy_image');
        file_put_contents($tempFile, base64_decode($base64Gif));

        return new UploadedFile(
            $tempFile,
            'avatar.gif',
            'image/gif',
            null,
            true // test mode
        );
    }

    /** @test */
    public function phone_number_validation_rules()
    {
        $salesman = Salesman::create([
            'manager_id' => $this->manager->manager_id,
            'name' => 'Staff Test',
            'username' => 'stafftest',
            'email' => 'stafftest@test.com',
            'password' => Hash::make('Password@123'),
            'address' => '789 Staff Rd',
            'staff_code' => 'STF-TEST01',
        ]);

        // Invalid: missing (required)
        $response = $this->actingAs($salesman, 'salesman')
            ->post(route('salesman.profile.update'), [
                '_method' => 'PATCH',
                'name' => 'Staff Test',
                'username' => 'stafftest',
                'email' => 'stafftest@test.com',
                'address' => '789 Staff Rd',
            ]);
        $response->assertSessionHasErrors(['phone_number']);

        // Invalid: non-numeric
        $response = $this->actingAs($salesman, 'salesman')
            ->post(route('salesman.profile.update'), [
                '_method' => 'PATCH',
                'name' => 'Staff Test',
                'username' => 'stafftest',
                'email' => 'stafftest@test.com',
                'address' => '789 Staff Rd',
                'phone_number' => 'abcdefghij',
            ]);
        $response->assertSessionHasErrors(['phone_number' => 'Invalid phone format. Use 012-3456789 or 012-34567890']);

        // Invalid: too long (12 digits)
        $response = $this->actingAs($salesman, 'salesman')
            ->post(route('salesman.profile.update'), [
                '_method' => 'PATCH',
                'name' => 'Staff Test',
                'username' => 'stafftest',
                'email' => 'stafftest@test.com',
                'address' => '789 Staff Rd',
                'phone_number' => '012-345678901',
            ]);
        $response->assertSessionHasErrors(['phone_number' => 'Invalid phone format. Use 012-3456789 or 012-34567890']);

        // Invalid: too short (9 digits)
        $response = $this->actingAs($salesman, 'salesman')
            ->post(route('salesman.profile.update'), [
                '_method' => 'PATCH',
                'name' => 'Staff Test',
                'username' => 'stafftest',
                'email' => 'stafftest@test.com',
                'address' => '789 Staff Rd',
                'phone_number' => '012-345678',
            ]);
        $response->assertSessionHasErrors(['phone_number' => 'Invalid phone format. Use 012-3456789 or 012-34567890']);

        // Valid: 10 digits
        $response = $this->actingAs($salesman, 'salesman')
            ->post(route('salesman.profile.update'), [
                '_method' => 'PATCH',
                'name' => 'Staff Test',
                'username' => 'stafftest',
                'email' => 'stafftest@test.com',
                'address' => '789 Staff Rd',
                'phone_number' => '012-3456789',
            ]);
        $response->assertSessionHasNoErrors();

        // Valid: 11 digits
        $response = $this->actingAs($salesman, 'salesman')
            ->post(route('salesman.profile.update'), [
                '_method' => 'PATCH',
                'name' => 'Staff Test',
                'username' => 'stafftest',
                'email' => 'stafftest@test.com',
                'address' => '789 Staff Rd',
                'phone_number' => '012-34567890',
            ]);
        $response->assertSessionHasNoErrors();
    }

    /** @test */
    public function manager_profile_page_displays_staff_code_as_read_only()
    {
        $this->manager->staff_code = 'STF-MGR123';
        $this->manager->save();

        $response = $this->actingAs($this->manager, 'manager')
            ->get(route('manager.profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('STF-MGR123');
        $response->assertSee('readonly');
    }

    /** @test */
    public function manager_can_update_profile_picture_with_valid_image()
    {
        Storage::fake('public');

        $file = $this->createDummyImage();

        $response = $this->actingAs($this->manager, 'manager')
            ->post(route('manager.profile.update'), [
                '_method' => 'PATCH',
                'name' => 'Manager Test Updated',
                'username' => 'test_manager',
                'email' => 'manager@test.com',
                'phone_number' => '012-3456789',
                'address' => '123 Manager St Updated',
                'profile_picture' => $file,
            ]);

        $response->assertRedirect(route('manager.profile.edit'));
        
        $this->manager->refresh();
        $this->assertEquals('Manager Test Updated', $this->manager->name);
        $this->assertNotNull($this->manager->profile_picture);
        
        // Assert storage has the uploaded file
        Storage::disk('public')->assertExists($this->manager->profile_picture);
    }

    /** @test */
    public function manager_profile_picture_validation_rules()
    {
        // Invalid: non-image file type
        $invalidFile = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->manager, 'manager')
            ->post(route('manager.profile.update'), [
                '_method' => 'PATCH',
                'name' => 'Test Manager',
                'username' => 'test_manager',
                'email' => 'manager@test.com',
                'phone_number' => '012-3456789',
                'address' => '123 Manager St',
                'profile_picture' => $invalidFile,
            ]);

        $response->assertSessionHasErrors(['profile_picture']);

        // Invalid: file too large (e.g. 3MB)
        $largeFile = $this->createLargeDummyFile(3000);

        $response = $this->actingAs($this->manager, 'manager')
            ->post(route('manager.profile.update'), [
                '_method' => 'PATCH',
                'name' => 'Test Manager',
                'username' => 'test_manager',
                'email' => 'manager@test.com',
                'phone_number' => '012-3456789',
                'address' => '123 Manager St',
                'profile_picture' => $largeFile,
            ]);

        $response->assertSessionHasErrors(['profile_picture']);
    }

    /** @test */
    public function manager_old_profile_picture_is_deleted_when_new_one_is_uploaded()
    {
        Storage::fake('public');

        // Store old image
        $oldFile = $this->createDummyImage();
        $oldPath = $oldFile->store('profile_pictures', 'public');
        
        $this->manager->profile_picture = $oldPath;
        $this->manager->save();

        Storage::disk('public')->assertExists($oldPath);

        // Upload new image
        $newFile = $this->createDummyImage();

        $response = $this->actingAs($this->manager, 'manager')
            ->post(route('manager.profile.update'), [
                '_method' => 'PATCH',
                'name' => 'Test Manager',
                'username' => 'test_manager',
                'email' => 'manager@test.com',
                'phone_number' => '012-3456789',
                'address' => '123 Manager St',
                'profile_picture' => $newFile,
            ]);

        $response->assertRedirect(route('manager.profile.edit'));
        
        $this->manager->refresh();
        $this->assertNotEquals($oldPath, $this->manager->profile_picture);
        
        // Assert old file was deleted and new file exists
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($this->manager->profile_picture);
    }

    /** @test */
    public function manager_username_updates_automatically_when_name_is_edited()
    {
        // Manager's current username is "test_manager"
        // Let's create a manager to force suffixing on a name update if needed
        Manager::create([
            'name' => 'Manager Test Updated',
            'username' => 'managertestupdated',
            'email' => 'other_manager@test.com',
            'password' => Hash::make('Password@123'),
            'address' => '123 Manager St',
            'phone_number' => '012-3456787',
        ]);

        $response = $this->actingAs($this->manager, 'manager')
            ->post(route('manager.profile.update'), [
                '_method' => 'PATCH',
                'name' => 'Manager Test Updated',
                'username' => 'test_manager',
                'email' => 'manager@test.com',
                'phone_number' => '012-3456789',
                'address' => '123 Manager St Updated',
            ]);

        $response->assertRedirect(route('manager.profile.edit'));
        
        $this->manager->refresh();
        $this->assertEquals('Manager Test Updated', $this->manager->name);
        // Should have updated username, and since "managertestupdated" exists, it should be "managertestupdated1"
        $this->assertEquals('managertestupdated1', $this->manager->username);
    }

    /** @test */
    public function salesman_username_updates_automatically_when_name_is_edited()
    {
        $salesman = Salesman::create([
            'manager_id' => $this->manager->manager_id,
            'name' => 'Staff Test',
            'username' => 'stafftest',
            'email' => 'stafftest@test.com',
            'password' => Hash::make('Password@123'),
            'address' => '789 Staff Rd',
            'staff_code' => 'STF-TEST01',
        ]);

        // Create a conflicting username
        Manager::create([
            'name' => 'Staff Test Updated',
            'username' => 'stafftestupdated',
            'email' => 'conflicting@test.com',
            'password' => Hash::make('Password@123'),
            'address' => '123 Manager St',
            'phone_number' => '012-3456786',
        ]);

        $response = $this->actingAs($salesman, 'salesman')
            ->post(route('salesman.profile.update'), [
                '_method' => 'PATCH',
                'name' => 'Staff Test Updated',
                'username' => 'stafftest',
                'email' => 'stafftest@test.com',
                'address' => '789 Staff Rd Updated',
                'phone_number' => '012-3456789',
            ]);

        $response->assertRedirect(route('salesman.profile.edit'));
        
        $salesman->refresh();
        $this->assertEquals('Staff Test Updated', $salesman->name);
        // Should have updated username, and since "stafftestupdated" exists, it should be "stafftestupdated1"
        $this->assertEquals('stafftestupdated1', $salesman->username);
    }

    /** @test */
    public function manager_phone_number_validation_rules()
    {
        // Invalid: missing (required)
        $response = $this->actingAs($this->manager, 'manager')
            ->post(route('manager.profile.update'), [
                '_method' => 'PATCH',
                'name' => 'Manager Test',
                'username' => 'test_manager',
                'email' => 'manager@test.com',
                'address' => '123 Manager St',
            ]);
        $response->assertSessionHasErrors(['phone_number']);

        // Invalid: non-numeric
        $response = $this->actingAs($this->manager, 'manager')
            ->post(route('manager.profile.update'), [
                '_method' => 'PATCH',
                'name' => 'Manager Test',
                'username' => 'test_manager',
                'email' => 'manager@test.com',
                'address' => '123 Manager St',
                'phone_number' => 'abcdefghij',
            ]);
        $response->assertSessionHasErrors(['phone_number' => 'Invalid phone format. Use 012-3456789 or 012-34567890']);

        // Invalid: too long (12 digits)
        $response = $this->actingAs($this->manager, 'manager')
            ->post(route('manager.profile.update'), [
                '_method' => 'PATCH',
                'name' => 'Manager Test',
                'username' => 'test_manager',
                'email' => 'manager@test.com',
                'address' => '123 Manager St',
                'phone_number' => '012-345678901',
            ]);
        $response->assertSessionHasErrors(['phone_number' => 'Invalid phone format. Use 012-3456789 or 012-34567890']);

        // Invalid: too short (9 digits)
        $response = $this->actingAs($this->manager, 'manager')
            ->post(route('manager.profile.update'), [
                '_method' => 'PATCH',
                'name' => 'Manager Test',
                'username' => 'test_manager',
                'email' => 'manager@test.com',
                'address' => '123 Manager St',
                'phone_number' => '012-345678',
            ]);
        $response->assertSessionHasErrors(['phone_number' => 'Invalid phone format. Use 012-3456789 or 012-34567890']);

        // Valid: 10 digits
        $response = $this->actingAs($this->manager, 'manager')
            ->post(route('manager.profile.update'), [
                '_method' => 'PATCH',
                'name' => 'Manager Test',
                'username' => 'test_manager',
                'email' => 'manager@test.com',
                'address' => '123 Manager St',
                'phone_number' => '012-3456789',
            ]);
        $response->assertSessionHasNoErrors();

        // Valid: 11 digits
        $response = $this->actingAs($this->manager, 'manager')
            ->post(route('manager.profile.update'), [
                '_method' => 'PATCH',
                'name' => 'Manager Test',
                'username' => 'test_manager',
                'email' => 'manager@test.com',
                'address' => '123 Manager St',
                'phone_number' => '012-34567890',
            ]);
        $response->assertSessionHasNoErrors();
    }

    /** @test */
    public function manager_profile_page_displays_correct_phone_guidance_and_button_text()
    {
        $this->manager->phone_number = '012-3456789';
        $this->manager->save();

        $response = $this->actingAs($this->manager, 'manager')
            ->get(route('manager.profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('Format: 012-3456789 or 012-34567890 (numbers and dash only).');
        $response->assertSee('Save Changes');
        $response->assertSee('012-3456789');
    }

    protected function createLargeDummyFile(int $kilobytes): UploadedFile
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'large_dummy');
        $fp = fopen($tempFile, 'w');
        fseek($fp, $kilobytes * 1024 - 1);
        fwrite($fp, "\0");
        fclose($fp);

        return new UploadedFile(
            $tempFile,
            'large.jpg',
            'image/jpeg',
            null,
            true // test mode
        );
    }
}
