<?php

use App\Livewire\Broker\Profile;
use App\Livewire\Mannager\BrokerApplications;
use App\Models\Broker;
use App\Models\Project;
use App\Models\Unit;
use App\Models\UnitOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function makeUnit(int $projectId, float $price): Unit
{
    static $n = 0;
    $n++;

    return Unit::create([
        'title' => "Unit {$n}",
        'slug' => "unit-{$n}",
        'project_id' => $projectId,
        'unit_type' => 'شقة',
        'unit_price' => $price,
        'building_number' => '1',
        'unit_number' => (string) $n,
        'floor' => '1',
        'case' => '0',
    ]);
}

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

    $developer = \App\Models\Developer::create(['name' => 'Dev']);
    $projectType = \App\Models\ProjectType::create(['name' => 'Residential', 'slug' => 'residential']);
    $this->project = Project::create([
        'name' => 'Project A',
        'slug' => 'project-a',
        'developer_id' => $developer->id,
        'project_type_id' => $projectType->id,
        'status' => true,
    ]);

    $this->broker = Broker::create([
        'name' => 'Commission Broker',
        'email' => 'commission@broker.com',
        'password' => bcrypt('password'),
        'status' => Broker::STATUS_APPROVED,
    ]);
});

it('computes percentage commission from the unit price', function () {
    $this->broker->update(['commission_type' => 'percentage', 'commission_value' => 2.5]);

    expect($this->broker->commissionForPrice(1_000_000))->toBe(25000.0);
    expect($this->broker->commissionLabel())->toBe('2.5% من قيمة كل وحدة مباعة');
});

it('computes fixed commission regardless of the unit price', function () {
    $this->broker->update(['commission_type' => 'fixed', 'commission_value' => 5000]);

    expect($this->broker->commissionForPrice(1_000_000))->toBe(5000.0);
    expect($this->broker->commissionForPrice(250_000))->toBe(5000.0);
    expect($this->broker->commissionLabel())->toBe('5,000.00 ريال لكل وحدة مباعة');
});

it('lets an admin edit broker data and set the commission', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    $this->actingAs($admin);

    Livewire::test(BrokerApplications::class)
        ->call('viewBroker', $this->broker->id)
        ->call('startEditing')
        ->set('editName', 'Updated Name')
        ->set('editCommissionType', 'percentage')
        ->set('editCommissionValue', '3')
        ->call('saveBroker')
        ->assertHasNoErrors();

    $this->broker->refresh();
    expect($this->broker->name)->toBe('Updated Name');
    expect($this->broker->commission_type)->toBe('percentage');
    expect((float) $this->broker->commission_value)->toBe(3.0);
});

it('rejects a percentage commission greater than 100', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    $this->actingAs($admin);

    Livewire::test(BrokerApplications::class)
        ->call('viewBroker', $this->broker->id)
        ->call('startEditing')
        ->set('editCommissionType', 'percentage')
        ->set('editCommissionValue', '150')
        ->call('saveBroker')
        ->assertHasErrors('editCommissionValue');
});

it('shows total commission for sold units on the broker profile', function () {
    $this->broker->update(['commission_type' => 'percentage', 'commission_value' => 2]);

    // Two completed (sold) orders + one non-completed order that must be ignored.
    $unitA = makeUnit($this->project->id, 1_000_000);
    $unitB = makeUnit($this->project->id, 500_000);
    $unitC = makeUnit($this->project->id, 800_000);

    foreach ([[$unitA, 4], [$unitB, 4], [$unitC, 1]] as [$unit, $status]) {
        UnitOrder::create([
            'name' => 'Client',
            'phone' => '+96650000000'.$unit->id,
            'status' => $status,
            'order_source' => UnitOrder::ORDER_SOURCE_BROKER,
            'broker_id' => $this->broker->id,
            'project_id' => $this->project->id,
            'unit_id' => $unit->id,
        ]);
    }

    $this->actingAs($this->broker->fresh(), 'broker');

    // 2% of (1,000,000 + 500,000) = 30,000 — unitC is not completed so excluded
    Livewire::test(Profile::class)
        ->assertViewHas('soldUnitsCount', 2)
        ->assertViewHas('totalCommission', 30000.0);
});
