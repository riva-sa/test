<?php

namespace Tests\Feature\Api;

use App\Models\Developer;
use App\Models\Project;
use App\Models\ProjectMedia;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectCatalogControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.agent_api_key' => 'test-api-key']);
    }

    /** @test */
    public function it_requires_api_key_for_projects_and_units_endpoints()
    {
        $this->getJson('/api/projects')->assertStatus(401);
        $this->getJson('/api/projects/1')->assertStatus(401);
        $this->getJson('/api/units')->assertStatus(401);
        $this->getJson('/api/units/1')->assertStatus(401);
    }

    /** @test */
    public function it_returns_paginated_projects_with_media_and_units()
    {
        $developer = Developer::factory()->create(['name' => 'Rafen Dev']);
        $project = Project::factory()->create([
            'name' => 'Yasmeen Tower',
            'developer_id' => $developer->id,
            'status' => true,
        ]);

        ProjectMedia::create([
            'project_id' => $project->id,
            'media_type' => 'image',
            'media_url' => 'projects/images/main.jpg',
            'main' => 1,
        ]);

        ProjectMedia::create([
            'project_id' => $project->id,
            'media_type' => 'pdf',
            'media_url' => 'projects/pdfs/brochure.pdf',
            'media_title' => 'كتالوج المشروع',
        ]);

        Unit::factory()->create([
            'project_id' => $project->id,
            'title' => 'Villa A1',
            'unit_type' => 'Villa',
            'unit_price' => 1200000,
            'status' => true,
        ]);

        $response = $this->withHeader('X-AGENT-API-KEY', 'test-api-key')
            ->getJson('/api/projects');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath('data.0.name', 'Yasmeen Tower')
            ->assertJsonPath('data.0.developer.name', 'Rafen Dev')
            ->assertJsonPath('data.0.media.pdf_files.0.title', 'كتالوج المشروع');

        $this->assertCount(1, $response->json('data.0.units'));
    }

    /** @test */
    public function it_returns_single_project_by_id()
    {
        $project = Project::factory()->create([
            'name' => 'Single Project Test',
            'status' => true,
        ]);

        $response = $this->withHeader('X-AGENT-API-KEY', 'test-api-key')
            ->getJson('/api/projects/' . $project->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $project->id,
                    'name' => 'Single Project Test',
                ],
            ]);
    }

    /** @test */
    public function it_returns_paginated_units()
    {
        $project = Project::factory()->create(['name' => 'Parent Project']);
        Unit::factory()->create([
            'project_id' => $project->id,
            'title' => 'Luxury Flat',
            'unit_type' => 'Apartment',
            'status' => true,
        ]);

        $response = $this->withHeader('X-AGENT-API-KEY', 'test-api-key')
            ->getJson('/api/units');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath('data.0.title', 'Luxury Flat')
            ->assertJsonPath('data.0.project.name', 'Parent Project');
    }
}
