<?php

use App\Livewire\Broker\SubmitLead;
use App\Models\Broker;
use App\Models\Project;
use App\Models\Unit;
use App\Models\UnitOrder;
use App\Models\User;
use App\Notifications\CRMAlertNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'sales_manager', 'guard_name' => 'web']);

    // Create developer and project
    $this->developer = \App\Models\Developer::create(['name' => 'Test Developer']);
    $this->projectType = \App\Models\ProjectType::create(['name' => 'Commercial', 'slug' => 'commercial']);
    $this->project = Project::create([
        'name' => 'Main Project',
        'slug' => 'main-project',
        'developer_id' => $this->developer->id,
        'project_type_id' => $this->projectType->id,
        'status' => true,
    ]);

    // Create unit
    $this->unit = Unit::create([
        'title' => 'Unit 101',
        'slug' => 'unit-101',
        'project_id' => $this->project->id,
        'unit_type' => 'شقة',
        'unit_price' => 500000,
        'building_number' => '1',
        'unit_number' => '101',
        'floor' => '1',
        'case' => '0', // available
    ]);

    // Create current broker
    $this->broker = Broker::create([
        'name' => 'Submitting Broker',
        'email' => 'submitting@broker.com',
        'password' => bcrypt('password'),
        'status' => 'approved',
    ]);
});

it('rejects a duplicate lead without creating an order and alerts the team', function () {
    Notification::fake();

    // Create an admin to be notified
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    // Create another broker who originally submitted the client
    $originalBroker = Broker::create([
        'name' => 'Original Broker',
        'email' => 'original@broker.com',
        'password' => bcrypt('password'),
        'status' => 'approved',
    ]);

    // Create an assigned sales agent
    $salesAgent = User::factory()->create(['is_active' => true]);

    // Create existing order for the client phone number
    UnitOrder::create([
        'name' => 'Old Client Name',
        'phone' => '+966555555555',
        'status' => 1,
        'order_source' => 'broker',
        'broker_id' => $originalBroker->id,
        'assigned_sales_user_id' => $salesAgent->id,
    ]);

    // Act as current broker and submit lead with same phone
    $this->actingAs($this->broker, 'broker');

    Livewire::test(SubmitLead::class)
        ->set('name', 'New Client Name')
        ->set('phone', '555555555')
        ->set('countryCode', '+966')
        ->set('selectedProject', $this->project->id)
        ->set('selectedUnit', $this->unit->id)
        ->set('property_type', 'شقة')
        ->set('PurchaseType', 'cash')
        ->set('PurchasePurpose', 'personal')
        ->set('support_type', 'مدعوم')
        ->call('submit')
        ->assertNoRedirect()
        ->assertSet('existingClientFound', true);

    // 1. No new lead was created for the submitting broker
    expect(UnitOrder::where('broker_id', $this->broker->id)->exists())->toBeFalse();

    // 2. Admins are alerted about the rejected attempt
    Notification::assertSentTo(
        $admin,
        CRMAlertNotification::class,
        fn ($notification) => str_contains($notification->toArray($admin)['message'], 'مسجّل بالفعل لدى ريفا')
    );

    // 3. The original broker is alerted
    Notification::assertSentTo(
        $originalBroker,
        CRMAlertNotification::class,
        fn ($notification) => str_contains($notification->toArray($originalBroker)['message'], 'عميلك المسجّل مسبقاً')
    );

    // 4. The assigned sales agent is alerted
    Notification::assertSentTo(
        $salesAgent,
        CRMAlertNotification::class,
        fn ($notification) => str_contains($notification->toArray($salesAgent)['message'], 'تم رفض الطلب')
    );

    // 5. The submitting broker is NOT notified (they see the inline rejection message instead)
    Notification::assertNotSentTo($this->broker, CRMAlertNotification::class);
});

it('creates the lead normally when the client is not already in the CRM', function () {
    $this->actingAs($this->broker, 'broker');

    Livewire::test(SubmitLead::class)
        ->set('name', 'Fresh Client')
        ->set('phone', '500000000')
        ->set('countryCode', '+966')
        ->set('selectedProject', $this->project->id)
        ->set('selectedUnit', $this->unit->id)
        ->set('property_type', 'شقة')
        ->set('PurchaseType', 'cash')
        ->set('PurchasePurpose', 'personal')
        ->set('support_type', 'مدعوم')
        ->call('submit')
        ->assertRedirect(route('broker.leads'));

    expect(UnitOrder::where('phone', '+966500000000')->where('broker_id', $this->broker->id)->exists())->toBeTrue();
});

it('creates the lead normally when only project is selected and no unit', function () {
    $this->actingAs($this->broker, 'broker');

    Livewire::test(SubmitLead::class)
        ->set('name', 'Fresh Client 2')
        ->set('phone', '500000001')
        ->set('countryCode', '+966')
        ->set('selectedProject', $this->project->id)
        ->set('selectedUnit', null)
        ->set('property_type', 'شقة')
        ->set('PurchaseType', 'cash')
        ->set('PurchasePurpose', 'personal')
        ->set('support_type', 'مدعوم')
        ->call('submit')
        ->assertRedirect(route('broker.leads'));

    $order = UnitOrder::where('phone', '+966500000001')->where('broker_id', $this->broker->id)->first();
    expect($order)->not->toBeNull();
    expect($order->project_id)->toBe($this->project->id);
    expect($order->unit_id)->toBeNull();
});
