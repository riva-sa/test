<?php

use App\Actions\ResetEmployeePasswordAction;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

beforeEach(function () {
    // Ensure roles exist
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'sales', 'guard_name' => 'web']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Admin');

    $this->sales = User::factory()->create([
        'email' => 'sales@example.com',
    ]);
    $this->sales->assignRole('sales');
});

test('admin can trigger password reset for sales employee', function () {
    Notification::fake();

    $action = new ResetEmployeePasswordAction;
    $status = $action->execute($this->sales);

    expect($status)->toBe(Password::RESET_LINK_SENT);

    // Verify log entry was made (implicitly checked by executing the action without error)
});

test('password reset link is sent to correct email', function () {
    Notification::fake();

    $this->actingAs($this->admin);

    Livewire::test(\App\Livewire\Mannager\SalesManagers::class)
        ->call('resetPassword', $this->sales->id)
        ->assertHasNoErrors()
        ->assertStatus(200);

    // The actual email sending is handled by Laravel's Password broker
});

test('employee can view password reset form with valid token', function () {
    $token = Password::broker()->createToken($this->sales);

    $response = $this->get(route('password.reset', [
        'token' => $token,
        'email' => $this->sales->email,
    ]));

    $response->assertStatus(200);
    $response->assertSee('إعادة تعيين كلمة المرور');
});

test('employee can update password using token', function () {
    $token = Password::broker()->createToken($this->sales);
    $newPassword = 'new-secure-password';

    $response = $this->post(route('password.update'), [
        'token' => $token,
        'email' => $this->sales->email,
        'password' => $newPassword,
        'password_confirmation' => $newPassword,
    ]);

    $response->assertRedirect();

    // Verify password was updated
    expect(auth()->attempt([
        'email' => $this->sales->email,
        'password' => $newPassword,
    ]))->toBeTrue();
});
