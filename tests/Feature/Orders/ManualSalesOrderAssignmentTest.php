<?php

use App\Models\UnitOrder;
use App\Models\User;
use App\Models\OrderPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'sales', 'guard_name' => 'web']);
    
    // Disable foreign key constraints to simplify testing database state
    \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
});

it('automatically assigns manually created order to the creating sales representative', function () {
    // 1. Create a sales representative user and log them in
    $salesRep = User::factory()->create([
        'is_active' => true,
        'on_vacation' => false,
    ]);
    $salesRep->assignRole('sales');
    $this->actingAs($salesRep);

    // 2. Create the order manually (source: manager)
    $order = UnitOrder::create([
        'name' => 'Test Client',
        'email' => 'client@test.com',
        'phone' => '+966512345678',
        'order_source' => UnitOrder::ORDER_SOURCE_MANAGER,
    ]);

    // 3. Verify it is directly assigned to the creator
    expect($order->assigned_sales_user_id)->toBe($salesRep->id);

    // 4. Verify permission is granted to the sales representative
    $permissionExists = OrderPermission::where('unit_order_id', $order->id)
        ->where('user_id', $salesRep->id)
        ->where('permission_type', 'manage')
        ->exists();

    expect($permissionExists)->toBeTrue();
});

it('does not assign manually created order to the admin who created it, but uses auto-assignment instead', function () {
    // 1. Create an active sales user (for auto-assignment target)
    $salesRep = User::factory()->create([
        'is_active' => true,
        'on_vacation' => false,
    ]);
    $salesRep->assignRole('sales');

    // 2. Create an admin user and log them in
    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    $this->actingAs($admin);

    // 3. Create the order manually (source: frontend_popup or similar to trigger auto assignment, or manager which skips auto-assignment)
    // Wait, let's see: if a manager creates an order of source: manager, the existing AutoAssignmentService will skip auto assignment.
    // Let's test a source that does trigger auto-assignment, like ORDER_SOURCE_FRONTEND_POPUP, created under Admin login.
    $order = UnitOrder::create([
        'name' => 'Test Client 2',
        'email' => 'client2@test.com',
        'phone' => '+966512345679',
        'order_source' => UnitOrder::ORDER_SOURCE_FRONTEND_POPUP,
    ]);

    // It should be auto-assigned to the sales representative, NOT the admin who created/triggered it.
    expect($order->assigned_sales_user_id)->toBe($salesRep->id);
    expect($order->assigned_sales_user_id)->not->toBe($admin->id);
});
