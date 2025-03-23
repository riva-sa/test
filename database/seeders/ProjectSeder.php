<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create projects type name description
        $projectsType = [
            [
                'name' => 'شقق',
                'slug' => 'شقق',
            ],
            [
                'name' => 'فلل',
                'slug' => 'فلل',
            ],
            [
                'name' => 'عمارات',
                'slug' => 'عمارات',
            ],
            [
                'name' => 'ادوار',
                'slug' => 'ادوار',
            ]
        ];

        foreach ($projectsType as $projectType) {
            \App\Models\ProjectType::create($projectType);
        }

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // First clear the tables to avoid duplicate entries
        DB::table('states')->truncate();
        DB::table('cities')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Define cities with their states
        $locations = [
            'الرياض' => [
                'الدرعية',
                'الخرج',
                'الزلفي',
                'المجمعة',
                'الدوادمي',
                'عفيف',
                'الغاط',
                'حوطة بني تميم',
                'شقراء',
                'ثادق',
            ],
            'مكة المكرمة' => [
                'جدة',
                'الطائف',
                'القنفذة',
                'الليث',
                'رابغ',
                'خليص',
                'الجموم',
                'الكامل',
                'تربة',
            ],
            'المدينة المنورة' => [
                'ينبع',
                'العلا',
                'المهد',
                'بدر',
                'خيبر',
                'الحناكية',
            ],
            'المنطقة الشرقية' => [
                'الدمام',
                'الظهران',
                'الأحساء',
                'الخبر',
                'القطيف',
                'الجبيل',
                'حفر الباطن',
                'الخفجي',
                'رأس تنورة',
            ],
            'عسير' => [
                'أبها',
                'خميس مشيط',
                'بيشة',
                'ظهران الجنوب',
                'تثليث',
                'سراة عبيدة',
                'رجال ألمع',
            ],
            'القصيم' => [
                'بريدة',
                'عنيزة',
                'الرس',
                'البدائع',
                'المذنب',
                'رياض الخبراء',
            ],
            'تبوك' => [
                'مدينة تبوك',
                'أملج',
                'تيماء',
                'ضباء',
                'الوجه',
                'حقل',
            ],
            'حائل' => [
                'مدينة حائل',
                'بقعاء',
                'الغزالة',
                'الشنان',
                'السليمي',
            ],
            'الحدود الشمالية' => [
                'عرعر',
                'رفحاء',
                'طريف',
                'العويقيلة',
            ],
            'جازان' => [
                'مدينة جازان',
                'صبيا',
                'أبو عريش',
                'صامطة',
                'الدرب',
                'الريث',
            ],
            'نجران' => [
                'مدينة نجران',
                'شرورة',
                'حبونا',
                'بدر الجنوب',
                'يدمة',
            ],
            'الباحة' => [
                'مدينة الباحة',
                'بلجرشي',
                'المندق',
                'المخواة',
                'القرى',
            ],
            'الجوف' => [
                'سكاكا',
                'القريات',
                'دومة الجندل',
                'طبرجل',
            ],
        ];

        // Insert the data
        foreach ($locations as $cityName => $states) {
            // Insert city
            $cityId = DB::table('cities')->insertGetId([
                'name' => $cityName,
                'country' => 'SA',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert states for this city
            foreach ($states as $stateName) {
                DB::table('states')->insert([
                    'name' => $stateName,
                    'country' => 'SA',
                    'status' => true,
                    'city_id' => $cityId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
