<?php

use App\Livewire\Broker\Register;
use App\Models\Broker;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    City::firstOrCreate(['name' => 'الرياض']);
});

it('stores the IBAN as SA + the 22 entered digits and converts Arabic digits', function () {
    Livewire::test(Register::class)
        ->set('broker_type', Broker::TYPE_INDIVIDUAL)
        ->set('email', 'iban@broker.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('name', 'IBAN Broker')
        ->set('national_id', '١٠١٢٣٤٥٦٧٨') // Arabic-Indic digits
        ->set('whatsapp', '0512345678')
        ->set('city', 'الرياض')
        ->set('iban_number', '٨٠٠٠٠٠٠٠٠٠٠٦٠١٠١٧٣٠٣٠٠') // 22 Arabic-Indic digits
        ->set('employment_status', 'employee')
        ->set('heard_about_us', 'google')
        ->set('national_id_file', UploadedFile::fake()->create('id.pdf', 100, 'application/pdf'))
        ->set('fal_license_file', UploadedFile::fake()->create('fal.pdf', 100, 'application/pdf'))
        ->call('submit')
        ->assertHasNoErrors();

    $broker = Broker::where('email', 'iban@broker.com')->first();

    expect($broker)->not->toBeNull();
    expect($broker->iban)->toBe('SA8000000000060101730300');
    expect($broker->national_id)->toBe('1012345678');
});

it('rejects an IBAN number that is not exactly 22 digits', function () {
    Livewire::test(Register::class)
        ->set('step', 2)
        ->set('name', 'IBAN Broker')
        ->set('national_id', '1012345678')
        ->set('whatsapp', '0512345678')
        ->set('city', 'الرياض')
        ->set('iban_number', '12345') // too short
        ->set('employment_status', 'employee')
        ->set('heard_about_us', 'google')
        ->call('nextStep')
        ->assertHasErrors('iban_number');
});
