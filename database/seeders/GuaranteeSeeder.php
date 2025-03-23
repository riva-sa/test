<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guarantee;

class GuaranteeSeeder extends Seeder
{
    public function run()
    {
        $guarantees = [
            ['name' => 'الانارة', 'description' => 'ضمان 3 سنوات', 'icon' => null, 'is_active' => true],
            ['name' => 'السباكة والادوات الصحية', 'description' => 'ضمان 5 سنوات', 'icon' => null, 'is_active' => true],
            ['name' => 'المصعد', 'description' => 'ضمان 10 سنوات', 'icon' => null, 'is_active' => true],
            ['name' => 'المواصير الحرارية', 'description' => 'ضمان 15 سنة', 'icon' => null, 'is_active' => true],
            ['name' => 'الهيكل الانشائي', 'description' => 'ضمان 10 سنوات', 'icon' => null, 'is_active' => true],
            ['name' => 'العزل', 'description' => 'ضمان 10 سنوات', 'icon' => null, 'is_active' => true],
            ['name' => 'الكهرباء', 'description' => 'ضمان 25 سنة', 'icon' => null, 'is_active' => true],
            ['name' => 'طبلون الكهرباء', 'description' => 'ضمان 25 سنة', 'icon' => null, 'is_active' => true],
            ['name' => 'تأمين', 'description' => 'ضمان لمدة 10 سنوات', 'icon' => null, 'is_active' => true],
        ];

        foreach ($guarantees as $guarantee) {
            Guarantee::create($guarantee);
        }
    }
}
