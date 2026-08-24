<?php

use App\Livewire\Broker\ProjectDetails;
use App\Models\Broker;
use App\Models\Developer;
use App\Models\Project;
use App\Models\ProjectMedia;
use App\Models\ProjectType;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');

    $developer = Developer::create(['name' => 'Test Developer']);
    $projectType = ProjectType::create(['name' => 'Residential', 'slug' => 'residential']);

    $this->project = Project::create([
        'name' => 'Sunset Towers',
        'slug' => 'sunset-towers',
        'developer_id' => $developer->id,
        'project_type_id' => $projectType->id,
        'status' => true,
    ]);

    // Create an available unit so the broker can access the project details
    $this->unitAvailable = Unit::create([
        'title' => 'Penthouse 101',
        'slug' => 'penthouse-101',
        'project_id' => $this->project->id,
        'unit_type' => 'شقة',
        'unit_price' => 1200000,
        'building_number' => '1',
        'unit_number' => '101',
        'floor' => '10',
        'case' => '0', // available
    ]);

    $this->unitReserved = Unit::create([
        'title' => 'Apartment 202',
        'slug' => 'apartment-202',
        'project_id' => $this->project->id,
        'unit_type' => 'روف',
        'unit_price' => 850000,
        'building_number' => '1',
        'unit_number' => '202',
        'floor' => '2',
        'case' => '1', // reserved
    ]);

    $this->broker = Broker::create([
        'name' => 'Media Broker',
        'email' => 'broker@media.com',
        'password' => bcrypt('password'),
        'status' => Broker::STATUS_APPROVED,
        'contract_signed_at' => now(),
        'contract_signed_path' => 'broker-documents/1/contract/contract-signed.pdf',
        'contract_approved_at' => now(),
    ]);

    $this->actingAs($this->broker, 'broker');
});

it('downloads single project image media', function () {
    $file = UploadedFile::fake()->image('project_photo.jpg');
    $path = $file->store('projects/media', 'public');

    $media = ProjectMedia::create([
        'project_id' => $this->project->id,
        'media_type' => 'image',
        'media_url' => $path,
        'media_title' => 'Front Facade',
        'status' => 1,
    ]);

    $response = $this->get(route('broker.projects.media.download', [$this->project->id, $media->id]));

    $response->assertOk();
    $response->assertHeader('content-disposition');
});

it('downloads project images as zip archive', function () {
    $file1 = UploadedFile::fake()->image('img1.jpg');
    $file2 = UploadedFile::fake()->image('img2.jpg');

    $path1 = $file1->store('projects/media', 'public');
    $path2 = $file2->store('projects/media', 'public');

    ProjectMedia::create([
        'project_id' => $this->project->id,
        'media_type' => 'image',
        'media_url' => $path1,
        'status' => 1,
    ]);

    ProjectMedia::create([
        'project_id' => $this->project->id,
        'media_type' => 'image',
        'media_url' => $path2,
        'status' => 1,
    ]);

    $response = $this->get(route('broker.projects.download-images', $this->project->id));

    $response->assertOk();
    $response->assertHeader('content-type', 'application/zip');
});

it('filters units by status and search query in ProjectDetails', function () {
    Livewire::test(ProjectDetails::class, ['id' => $this->project->id])
        ->assertSee('Penthouse 101')
        ->assertSee('Apartment 202')
        ->set('unitStatusFilter', '0')
        ->assertSee('Penthouse 101')
        ->assertDontSee('Apartment 202')
        ->set('unitStatusFilter', '1')
        ->assertDontSee('Penthouse 101')
        ->assertSee('Apartment 202')
        ->set('unitStatusFilter', '')
        ->set('searchUnit', '101')
        ->assertSee('Penthouse 101')
        ->assertDontSee('Apartment 202');
});

it('downloads project price list pdf for broker', function () {
    $response = $this->get(route('broker.projects.pdf', $this->project->id));

    $response->assertOk();
    $response->assertHeader('content-type', 'application/pdf');
});
