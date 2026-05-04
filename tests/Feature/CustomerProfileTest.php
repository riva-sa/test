<?php

use App\Livewire\Mannager\CustomerProfile;
use App\Models\OrderNote;
use App\Models\UnitOrder;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::create(['name' => 'sales_manager', 'guard_name' => 'web']);
    $this->user = auth()->user();
    $this->user->assignRole('sales_manager');
});

it('loads customer profile with orders and relationships', function () {
    $developer = \App\Models\Developer::create(['name' => 'Dev']);
    $projectType = \App\Models\ProjectType::create(['name' => 'سكني', 'slug' => 'residential']);
    $project = \App\Models\Project::create([
        'name' => 'مشروع تجريبي',
        'slug' => 'test-project',
        'developer_id' => $developer->id,
        'project_type_id' => $projectType->id,
    ]);

    $salesUser = User::factory()->create(['name' => 'موظف مبيعات']);

    $order = UnitOrder::create([
        'name' => 'عميل اختبار',
        'email' => 'test@example.com',
        'phone' => '0500000001',
        'status' => 0,
        'project_id' => $project->id,
        'assigned_sales_user_id' => $salesUser->id,
        'order_source' => 'manager',
        'ad_set' => 'مجموعة 1',
    ]);

    OrderNote::create([
        'unit_order_id' => $order->id,
        'note' => 'ملاحظة تجريبية',
        'user_id' => $this->user->id,
    ]);

    Livewire::test(CustomerProfile::class, ['phone' => '0500000001'])
        ->assertSee('عميل اختبار')
        ->assertSee('مشروع تجريبي')
        ->assertSee('موظف مبيعات')
        ->assertSee('مجموعة 1')
        ->assertSee('إضافة يدوية');
});

it('displays summary card with correct totals', function () {
    UnitOrder::create([
        'name' => 'عميل ملخص',
        'email' => 'summary@example.com',
        'phone' => '0500000002',
        'status' => 4,
    ]);

    UnitOrder::create([
        'name' => 'عميل ملخص',
        'email' => 'summary@example.com',
        'phone' => '0500000002',
        'status' => 1,
    ]);

    Livewire::test(CustomerProfile::class, ['phone' => '0500000002'])
        ->assertSee('عميل ملخص')
        ->assertSeeInOrder(['إجمالي الطلبات', '2']);
});

it('shows notes count per order', function () {
    $order = UnitOrder::create([
        'name' => 'عميل ملاحظات',
        'email' => 'notes@example.com',
        'phone' => '0500000003',
        'status' => 2,
    ]);

    OrderNote::create(['unit_order_id' => $order->id, 'note' => 'أولى', 'user_id' => $this->user->id]);
    OrderNote::create(['unit_order_id' => $order->id, 'note' => 'ثانية', 'user_id' => $this->user->id]);

    Livewire::test(CustomerProfile::class, ['phone' => '0500000003'])
        ->assertSee('2');
});

it('renders status with hex color codes', function () {
    UnitOrder::create([
        'name' => 'عميل حالة',
        'email' => 'status@example.com',
        'phone' => '0500000004',
        'status' => 0,
    ]);

    Livewire::test(CustomerProfile::class, ['phone' => '0500000004'])
        ->assertSee('جديد')
        ->assertSee('#3B82F6');
});
