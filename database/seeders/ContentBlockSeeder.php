<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContentBlock;
use Illuminate\Support\Facades\DB;

class ContentBlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Disable foreign key checks for PostgreSQL
        DB::statement('SET CONSTRAINTS ALL DEFERRED');

        $contentBlocks = [
            [
                'key' => 'main_heading',
                'description' => 'Main heading',
                'content' => 'ريفا العقارية .. تفهمك'
            ],
            [
                'key' => 'tagline',
                'description' => 'Tagline',
                'content' => 'شركة ريفا العقارية متخصصة في إدارة وتسويق المبيعات العقارية'
            ],
            [
                'key' => 'management_title',
                'description' => 'Management section title',
                'content' => 'الإدارة'
            ],
            [
                'key' => 'management_content',
                'description' => 'Management content',
                'content' => '<p>نحن في ريفا العقارية نتميز بالاحترافية والإتقان في إدارة المبيعات العقارية. فريقنا المتمرس يقدم حلولاً مبتكرة تلبي احتياجات عملائنا بكفاءة. التزامنا بالجودة والرضا يجعلنا رواداً في القطاع العقاري</p>'
            ],
            [
                'key' => 'marketing_title',
                'description' => 'Marketing section title',
                'content' => 'التسويق'
            ],
            [
                'key' => 'marketing_content',
                'description' => 'Marketing content',
                'content' => '<p>في ريفا العقارية، نعتز بنجاحنا وإبداعنا في مجال التسويق العقاري. نبتكر استراتيجيات فعّالة تصل إلى جمهور واسع وتحقق نتائج مبهرة. فريقنا المتخصص يعمل بجد لضمان تميز كل حملة تسويقية. التزامنا بالجودة والإبداع يجعلنا في طليعة السوق العقارية</p>'
            ],
            [
                'key' => 'company_footer',
                'description' => 'Company name footer',
                'content' => 'ريفا العقارية.'
            ],
            [
                'key' => 'mission_title',
                'description' => 'Mission title',
                'content' => 'رسالتنا'
            ],
            [
                'key' => 'mission_content',
                'description' => 'Mission content',
                'content' => '<p>تقديم حلول تسويقية عقارية متكاملة وذات جودة عالية تلبي احتياجات وتطلعات عملائنا، من خلال الابتكار والتفاني في العمل. نهدف إلى بناء علاقات طويلة الأمد مع عملائنا وشركائنا من خلال تقديم خدمات متميزة تعتمد على الشفافية والاحترافية.</p>'
            ],
            [
                'key' => 'vision_title',
                'description' => 'Vision title',
                'content' => 'رؤيتنا'
            ],
            [
                'key' => 'vision_content',
                'description' => 'Vision content',
                'content' => '<p>نسعى في ريفا العقارية أن نكون الشركة الرائدة في إدارة المبيعات وتسويق العقاري على مستوى المملكة العربية السعودية، متميزين بخدماتنا المبتكرة والموثوقة، مع التزامنا بتحقيق النمو المستدام والتميز في جميع مبادراتنا.</p>'
            ],
            [
                'key' => 'values_title',
                'description' => 'Values title',
                'content' => 'قيمنا'
            ],
            [
                'key' => 'values_content',
                'description' => 'Values content',
                'content' => '<ol>
                    <li><strong>الاحترافية:</strong> نلتزم بأعلى معايير العمل الاحترافي في جميع تعاملاتنا.</li>
                    <li><strong>الابتكار:</strong> نسعى دائماً لتقديم حلول مبتكرة تلبي احتياجات السوق.</li>
                    <li><strong>الشفافية:</strong> نؤمن بالوضوح والأمانة في جميع علاقاتنا المهنية.</li>
                </ol>'
            ],
            [
                'key' => 'services_title',
                'description' => 'Services title',
                'content' => 'خدماتنا'
            ],
            [
                'key' => 'services_content',
                'description' => 'Services content',
                'content' => '<div class="row g-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body text-center py-4">
                                <i class="fas fa-home fs-1 text-primary mb-3"></i>
                                <h4 class="card-title">إدارة المبيعات العقارية</h4>
                                <p class="card-text">نقدم حلولاً متكاملة لإدارة عمليات البيع العقاري باحترافية وكفاءة عالية</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body text-center py-4">
                                <i class="fas fa-bullhorn fs-1 text-primary mb-3"></i>
                                <h4 class="card-title">التسويق العقاري</h4>
                                <p class="card-text">استراتيجيات تسويقية مبتكرة تصل إلى الجمهور المستهدف وتحقق أعلى العوائد</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body text-center py-4">
                                <i class="fas fa-chart-line fs-1 text-primary mb-3"></i>
                                <h4 class="card-title">الاستشارات العقارية</h4>
                                <p class="card-text">خبراؤنا يقدمون النصائح والتحليلات لمساعدتك في اتخاذ القرارات الاستثمارية الصائبة</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body text-center py-4">
                                <i class="fas fa-building fs-1 text-primary mb-3"></i>
                                <h4 class="card-title">إدارة المشاريع</h4>
                                <p class="card-text">إدارة متكاملة للمشاريع العقارية منذ التخطيط وحتى التسليم النهائي</p>
                            </div>
                        </div>
                    </div>
                </div>'
            ],
            [
                'key' => 'sales_management_title',
                'description' => 'Sales management service title',
                'content' => 'إدارة المبيعات العقارية'
            ],
            [
                'key' => 'sales_management_desc',
                'description' => 'Sales management service description',
                'content' => 'نقدم حلولاً متكاملة لإدارة عمليات البيع العقاري باحترافية وكفاءة عالية'
            ],
            [
                'key' => 'marketing_service_title',
                'description' => 'Marketing service title',
                'content' => 'التسويق العقاري'
            ],
            [
                'key' => 'marketing_service_desc',
                'description' => 'Marketing service description',
                'content' => 'استراتيجيات تسويقية مبتكرة تصل إلى الجمهور المستهدف وتحقق أعلى العوائد'
            ],
            [
                'key' => 'consultation_title',
                'description' => 'Consultation service title',
                'content' => 'الاستشارات العقارية'
            ],
            [
                'key' => 'consultation_desc',
                'description' => 'Consultation service description',
                'content' => 'خبراؤنا يقدمون النصائح والتحليلات لمساعدتك في اتخاذ القرارات الاستثمارية الصائبة'
            ],
            [
                'key' => 'project_management_title',
                'description' => 'Project management service title',
                'content' => 'إدارة المشاريع'
            ],
            [
                'key' => 'project_management_desc',
                'description' => 'Project management service description',
                'content' => 'إدارة متكاملة للمشاريع العقارية منذ التخطيط وحتى التسليم النهائي'
            ]
        ];

        foreach ($contentBlocks as $block) {
            ContentBlock::updateOrCreate(
                ['key' => $block['key']],
                $block
            );
        }

        // Re-enable foreign key checks
        DB::statement('SET CONSTRAINTS ALL IMMEDIATE');

        $this->command->info('Successfully seeded content blocks with keys for Riva Real Estate!');
    }
}
