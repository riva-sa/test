<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Unit;
use App\Models\UnitOrder;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectUnitOrderSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating projects, units, and orders...');

        $projectTypes = [
            ['name' => 'شقق', 'slug' => 'apartments'],
            ['name' => 'فلل', 'slug' => 'villas'],
            ['name' => 'تاون هاوس', 'slug' => 'townhouses'],
        ];

        foreach ($projectTypes as $type) {
            ProjectType::firstOrCreate(['slug' => $type['slug']], $type);
        }

        $cities = City::take(3)->get();
        
        $salesManagers = User::whereHas('roles', function ($q) {
            $q->where('name', 'sales_manager');
        })->get();

        if ($salesManagers->isEmpty()) {
            $salesManagers = User::take(3)->get();
        }

        $projectsData = [
            [
                'name' => 'مشروع الازدهار',
                'slug' => 'al-azdahar',
                'description' => 'مشروع سكني فاخر في الرياض',
                'address' => 'الرياض - حي النرجس',
                'project_type_id' => ProjectType::where('slug', 'apartments')->first()?->id,
                'sales_manager_id' => $salesManagers->first()?->id,
            ],
            [
                'name' => 'مشروع الغروب',
                'slug' => 'al-ghorob',
                'description' => 'فلل سوبر لوكس في جدة',
                'address' => 'جدة - حي الشمالية',
                'project_type_id' => ProjectType::where('slug', 'villas')->first()?->id,
                'sales_manager_id' => $salesManagers->get(1)?->id ?? $salesManagers->first()?->id,
            ],
            [
                'name' => 'مشروع الأحلام',
                'slug' => 'al-ahlam',
                'description' => 'تاون هاوس في المدينة المنورة',
                'address' => 'المدينة المنورة - حي السنابل',
                'project_type_id' => ProjectType::where('slug', 'townhouses')->first()?->id,
                'sales_manager_id' => $salesManagers->get(2)?->id ?? $salesManagers->first()?->id,
            ],
        ];

        $developers = \App\Models\Developer::take(3)->get();
        if ($developers->isEmpty()) {
            $developer = \App\Models\Developer::firstOrCreate(
                ['name' => 'شركة الازدهار للتطوير'],
                ['name' => 'شركة الازدهار للتطوير', 'description' => 'شركة تطوير عقاري']
            );
            $developers = collect([$developer]);
        }

        $projects = [];
        foreach ($projectsData as $projectData) {
            $projectData['city_id'] = $cities->random()->id;
            $projectData['status'] = true;
            $projectData['developer_id'] = $developers->random()->id;
            $project = Project::firstOrCreate(
                ['slug' => $projectData['slug']],
                $projectData
            );
            $projects[] = $project;
            $this->command->info("Created project: {$project->name}");
        }

        $unitTypes = ['شقة', 'فلة', 'تاون هاوس'];
        $Statuses = [0, 1, 2];
        
        $unitsData = [
            ['title' => 'شقة رقم 101', 'unit_type' => 'شقة', 'floor' => 1, 'unit_area' => 150, 'unit_price' => 500000, 'beadrooms' => 3, 'bathrooms' => 2, 'building_number' => 'A', 'unit_number' => '101'],
            ['title' => 'شقة رقم 102', 'unit_type' => 'شقة', 'floor' => 1, 'unit_area' => 180, 'unit_price' => 600000, 'beadrooms' => 4, 'bathrooms' => 3, 'building_number' => 'A', 'unit_number' => '102'],
            ['title' => 'شقة رقم 201', 'unit_type' => 'شقة', 'floor' => 2, 'unit_area' => 150, 'unit_price' => 520000, 'beadrooms' => 3, 'bathrooms' => 2, 'building_number' => 'A', 'unit_number' => '201'],
            ['title' => 'شقة رقم 202', 'unit_type' => 'شقة', 'floor' => 2, 'unit_area' => 200, 'unit_price' => 700000, 'beadrooms' => 5, 'bathrooms' => 4, 'building_number' => 'A', 'unit_number' => '202'],
            ['title' => 'فلة رقم A1', 'unit_type' => 'فلة', 'floor' => 1, 'unit_area' => 350, 'unit_price' => 1500000, 'beadrooms' => 5, 'bathrooms' => 4, 'building_number' => 'B', 'unit_number' => 'A1'],
            ['title' => 'فلة رقم A2', 'unit_type' => 'فلة', 'floor' => 2, 'unit_area' => 400, 'unit_price' => 1800000, 'beadrooms' => 6, 'bathrooms' => 5, 'building_number' => 'B', 'unit_number' => 'A2'],
            ['title' => 'تاون هاوس T1', 'unit_type' => 'تاون هاوس', 'floor' => 1, 'unit_area' => 250, 'unit_price' => 900000, 'beadrooms' => 4, 'bathrooms' => 3, 'building_number' => 'C', 'unit_number' => 'T1'],
            ['title' => 'تاون هاوس T2', 'unit_type' => 'تاون هاوس', 'floor' => 2, 'unit_area' => 280, 'unit_price' => 1000000, 'beadrooms' => 5, 'bathrooms' => 4, 'building_number' => 'C', 'unit_number' => 'T2'],
        ];

        $units = [];
        foreach ($projects as $project) {
            foreach ($unitsData as $index => $unitData) {
                $unitData['project_id'] = $project->id;
                $unitData['slug'] = $project->slug . '-' . ($index + 1);
                $unitData['sale_type'] = 'cash';
                $unitData['case'] = array_rand([0, 1, 2]);
                $unitData['status'] = true;
                $unitData['show_price'] = true;
                $unitData['visits_count'] = rand(0, 100);
                $unitData['views_count'] = rand(0, 500);
                
                $unit = Unit::firstOrCreate(
                    ['slug' => $unitData['slug']],
                    $unitData
                );
                $units[] = $unit;
                $this->command->info("Created unit: {$unit->title}");
            }
        }

        $statusLabels = [
            0 => 'جديد',
            1 => 'طلب مفتوح', 
            2 => 'معاملات بيعية',
            3 => 'مغلق',
            4 => 'مكتمل',
        ];

        $names = [
            'أحمد محمد',
            'محمد علي',
            'عبدالله خالد',
            ' Sultan ',
            'خالد إبراهيم',
            'عمر سعيد',
            'يوسف كريم',
            'أنور حسن',
        ];

        $phones = [
            '+966501234561',
            '+966501234562',
            '+966501234563',
            '+966501234564',
            '+966501234565',
            '+966501234566',
            '+966501234567',
            '+966501234568',
        ];

        $marketingSources = ['Facebook', 'Instagram', 'Snapchat', 'Google', 'TikTok'];
        $campaigns = ['Winter Sale', 'Summer Offer', 'Ramadan Discount', 'New Year'];

        $ordersCount = 0;
        foreach ($units as $index => $unit) {
            $numOrders = rand(1, 3);
            
            for ($i = 0; $i < $numOrders; $i++) {
                $orderData = [
                    'unit_id' => $unit->id,
                    'project_id' => $unit->project_id,
                    'name' => $names[array_rand($names)],
                    'email' => 'customer' . ($ordersCount + 1) . '@example.com',
                    'phone' => $phones[array_rand($phones)],
                    'status' => array_rand([0, 1, 2, 3, 4]),
                    'marketing_source' => $marketingSources[array_rand($marketingSources)],
                    'campaign_name' => $campaigns[array_rand($campaigns)],
                    'PurchaseType' => array_rand(['cash', 'installment']),
                    'PurchasePurpose' => array_rand(['living', 'invest']),
                    'order_source' => 'social_media',
                    'created_at' => now()->subDays(rand(0, 30)),
                    'updated_at' => now()->subDays(rand(0, 10)),
                ];

                $order = UnitOrder::create($orderData);
                $ordersCount++;
                $this->command->info("Created order #{$order->id} for unit: {$unit->title}");
            }
        }

        $this->command->info("Seeder completed!");
        $this->command->info("- Projects: " . count($projects));
        $this->command->info("- Units: " . count($units));
        $this->command->info("- Orders: " . $ordersCount);
    }
}