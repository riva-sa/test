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

it('creates new lead and sends notifications when submitting a duplicate lead', function () {
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
    $existingOrder = UnitOrder::create([
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
        ->set('selectedProjects', [$this->project->id])
        ->set('selectedUnits', [$this->unit->id])
        ->set('property_type', 'شقة')
        ->set('PurchaseType', 'cash')
        ->set('PurchasePurpose', 'personal')
        ->set('support_type', 'مدعوم')
        ->call('submit')
        ->assertRedirect(route('broker.leads'));

    // 1. Verify lead was created
    $newOrder = UnitOrder::where('phone', '+966555555555')
        ->where('broker_id', $this->broker->id)
        ->first();
    expect($newOrder)->not->toBeNull();

    // 2. Verify notifications were sent to Admin
    Notification::assertSentTo(
        $admin,
        CRMAlertNotification::class,
        fn ($notification) => str_contains($notification->toArray($admin)['message'], 'عميلاً موجوداً مسبقاً')
    );

    // 3. Verify notification was sent to original broker
    Notification::assertSentTo(
        $originalBroker,
        CRMAlertNotification::class,
        fn ($notification) => str_contains($notification->toArray($originalBroker)['message'], 'تم تقديم طلب جديد لعميلك')
    );

    // 4. Verify notification was sent to assigned sales agent
    Notification::assertSentTo(
        $salesAgent,
        CRMAlertNotification::class,
        fn ($notification) => str_contains($notification->toArray($salesAgent)['message'], 'تم رفع طلب جديد له بواسطة الوسيط')
    );

    // 5. Verify notification was sent to current broker
    Notification::assertSentTo(
        $this->broker,
        CRMAlertNotification::class,
        fn ($notification) => str_contains($notification->toArray($this->broker)['message'], 'طلب عميل مكرر')
    );
});
