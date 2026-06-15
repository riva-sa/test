<?php

use App\Livewire\Mannager\BrokerApplications;
use App\Models\Broker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Admin');
});

it('does not activate the account just because the broker signed the contract', function () {
    $broker = Broker::create([
        'name' => 'Signed Broker',
        'email' => 'signed@broker.com',
        'password' => bcrypt('password'),
        'status' => Broker::STATUS_APPROVED,
        'approved_at' => now(),
        'contract_path' => 'broker-documents/1/contract/contract.pdf',
        'contract_sent_at' => now(),
        'contract_signed_path' => 'broker-documents/1/contract/contract-signed.pdf',
        'contract_signed_at' => now(),
    ]);

    expect($broker->contractSigned())->toBeTrue();
    expect($broker->awaitingContractApproval())->toBeTrue();
    expect($broker->isActive())->toBeFalse();
});

it('activates the account only after the admin approves the signed contract', function () {
    $broker = Broker::create([
        'name' => 'Signed Broker',
        'email' => 'signed@broker.com',
        'password' => bcrypt('password'),
        'status' => Broker::STATUS_APPROVED,
        'approved_at' => now(),
        'contract_path' => 'broker-documents/1/contract/contract.pdf',
        'contract_sent_at' => now(),
        'contract_signed_path' => 'broker-documents/1/contract/contract-signed.pdf',
        'contract_signed_at' => now(),
    ]);

    $this->actingAs($this->admin);

    Livewire::test(BrokerApplications::class)
        ->call('approveContract', $broker->id);

    $broker->refresh();

    expect($broker->contractApproved())->toBeTrue();
    expect($broker->contract_approved_by)->toBe($this->admin->id);
    expect($broker->isActive())->toBeTrue();
});

it('refuses to approve a contract the broker has not signed yet', function () {
    $broker = Broker::create([
        'name' => 'Unsigned Broker',
        'email' => 'unsigned@broker.com',
        'password' => bcrypt('password'),
        'status' => Broker::STATUS_APPROVED,
        'approved_at' => now(),
        'contract_path' => 'broker-documents/1/contract/contract.pdf',
        'contract_sent_at' => now(),
    ]);

    $this->actingAs($this->admin);

    Livewire::test(BrokerApplications::class)
        ->call('approveContract', $broker->id);

    expect($broker->fresh()->contractApproved())->toBeFalse();
});
