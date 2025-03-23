<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feature;

class FeatureSeeder extends Seeder
{
    public function run()
    {
        $features = [
            ['name' => 'منزل ذكي', 'description' => 'نظام المنزل الذكي', 'icon' => '', 'is_active' => true],
            ['name' => 'نظام الانذار من الحرائق', 'description' => 'نظام الحماية من الحرائق', 'icon' => '', 'is_active' => true],
            ['name' => 'كاميرات مراقبة', 'description' => 'نظام كاميرات المراقبة', 'icon' => '', 'is_active' => true],
            ['name' => 'دخول ذكي', 'description' => 'نظام الدخول الذكي', 'icon' => '', 'is_active' => true],
            ['name' => 'موقف خاص', 'description' => 'موقف سيارات خاص', 'icon' => '', 'is_active' => true],
            ['name' => 'واجهات حديثة', 'description' => 'تصاميم واجهات حديثة', 'icon' => '', 'is_active' => true],
            ['name' => 'حوش خاص', 'description' => 'حوش خاص للمنزل', 'icon' => '', 'is_active' => true],
            ['name' => 'مدخل خاص', 'description' => 'مدخل خاص للمنزل', 'icon' => '', 'is_active' => true],
            ['name' => 'مصعد', 'description' => 'مصعد خاص', 'icon' => '', 'is_active' => true],
            ['name' => 'بلكونة', 'description' => 'بلكونة خاصة', 'icon' => '', 'is_active' => true],
            ['name' => 'تكييف مخفي', 'description' => 'تكييف مركزي مخفي', 'icon' => '', 'is_active' => true],
            ['name' => 'جلسة خارجية', 'description' => 'جلسة خارجية مريحة', 'icon' => '', 'is_active' => true],
            ['name' => 'خزانات مستقلة', 'description' => 'خزانات مياه مستقلة', 'icon' => '', 'is_active' => true],
            ['name' => 'غرفة خادمة', 'description' => 'غرفة خادمة مخصصة', 'icon' => '', 'is_active' => true],
            ['name' => 'حديقة', 'description' => 'حديقة خارجية', 'icon' => '', 'is_active' => true],
            ['name' => 'تاسيس مصعد', 'description' => 'تأسيس مصعد مستقبلي', 'icon' => '', 'is_active' => true],
        ];

        foreach ($features as $feature) {
            Feature::create($feature);
        }
    }
}
