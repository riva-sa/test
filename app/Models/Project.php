<?php

namespace App\Models;

use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class Project extends Model
{
    use HasFactory, Trackable;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'developer_id',
        'project_type_id',
        'address',
        'city_id',
        'state_id',
        'country',
        'latitude',
        'longitude',
        'status',
        'show_price',
        'price',
        'bulding_style',
        'is_featured',
        'AdLicense',
        'location',
        'virtualTour',
        'sales_manager_id',
        'visits_count',
        'views_count',
        'shows_count',
        'orders_count',
        'last_visited_at',
        'last_viewed_at',
        'last_shown_at',
        'last_ordered_at',
    ];

    protected $casts =
        [
            'status' => 'boolean',
            'show_price' => 'boolean',
            'location' => 'array',
            'last_visited_at' => 'datetime',
            'last_viewed_at' => 'datetime',
            'last_shown_at' => 'datetime',
            'last_ordered_at' => 'datetime',
        ];

    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }

    public function projectMedia()
    {
        return $this->hasMany(ProjectMedia::class);
    }

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class);
    }

    // city
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    // state
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function guarantees()
    {
        return $this->belongsToMany(Guarantee::class);
    }

    public function landmarks()
    {
        return $this->belongsToMany(Landmark::class)->withPivot('distance')->withTimestamps();
    }

    public function getMediaImages()
    {
        try {
            $images = $this->projectMedia->where('media_type', 'image');

            return $images->isNotEmpty() ? $images : collect([]); // إرجاع مجموعة فارغة إذا لم يكن هناك صور
        } catch (\Exception $e) {
            // التعامل مع الخطأ إذا حدث مشكلة في الاستعلام
            return collect([]); // إرجاع مجموعة فارغة في حالة الخطأ
        }
    }

    public function getMainImages()
    {
        try {
            $mainImage = $this->projectMedia
                ->where('media_type', 'image')
                ->where('main', '1')
                ->first();

            return $mainImage ?: null; // إرجاع null إذا لم يكن هناك صورة رئيسية
        } catch (\Exception $e) {
            return null; // إرجاع null في حالة الخطأ
        }
    }

    public function getMediaVideos()
    {
        try {
            $videos = $this->projectMedia->where('media_type', 'video');

            return $videos->isNotEmpty() ? $videos : collect([]); // إرجاع مجموعة فارغة إذا لم يكن هناك فيديوهات
        } catch (\Exception $e) {
            return collect([]); // إرجاع مجموعة فارغة في حالة الخطأ
        }
    }

    public function getMediaPdf()
    {
        try {
            $pdfs = $this->projectMedia->where('media_type', 'pdf');

            return $pdfs->isNotEmpty() ? $pdfs : collect([]); // إرجاع مجموعة فارغة إذا لم يكن هناك PDF
        } catch (\Exception $e) {
            return collect([]); // إرجاع مجموعة فارغة في حالة الخطأ
        }
    }

    public function getMainImage()
    {
        try {
            $mainImage = $this->projectMedia
                ->where('media_type', 'image')
                ->where('main', 1)
                ->first();

            return $mainImage ?: null; // إرجاع null إذا لم يكن هناك صورة رئيسية
        } catch (\Exception $e) {
            return null; // إرجاع null في حالة الخطأ
        }
    }

    public function getFirstPdfUrl()
    {
        try {
            // إحضار أول ملف PDF فقط
            $firstPdf = $this->projectMedia
                ->where('media_type', 'pdf')
                ->first();

            // إذا وجد الملف، إرجاع رابط التحميل، وإذا لم يوجد إرجاع null
            return $firstPdf ? Storage::url($firstPdf->media_url) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function salesManager()
    {
        return $this->belongsTo(User::class, 'sales_manager_id');
    }
    // public function getDynamicProjectStatusAttribute()
    // {
    //     // Check if the project has any units
    //     if ($this->units()->count() == 0) {
    //         return 'تحت الانشاء';
    //     }

    //     $unitCases = $this->units()->pluck('case');
    //     if ($unitCases->every(fn($case) => $case == 2)) {
    //         return 'مباع بالكامل';
    //     }
    //     if ($unitCases->every(fn($case) => $case == 1)) {
    //         return 'محجوز بالكامل';
    //     }
    //     if ($unitCases->every(fn($case) => $case == 3)) { // تحت الانشاء
    //         return 'تحت الانشاء';
    //     }

    //     return 'متاح';
    // }

    // public function getDynamicProjectStatusAttribute()
    // {
    //     // Check if the project has any units
    //     if ($this->units()->count() == 0) {
    //         return 'تحت الانشاء';
    //     }

    //     $unitCases = $this->units()->pluck('case');

    //     // If there's at least one unit available (case == 0)
    //     if ($unitCases->contains(0)) {
    //         return 'متاح';
    //     }

    //     // If all units are sold (case == 2)
    //     if ($unitCases->every(fn($case) => $case == 2)) {
    //         return 'مباع بالكامل';
    //     }

    //     // If all units are reserved (case == 1)
    //     if ($unitCases->every(fn($case) => $case == 1)) {
    //         return 'محجوز بالكامل';
    //     }

    //     // If all units are under construction (case == 3)
    //     if ($unitCases->every(fn($case) => $case == 3)) {
    //         return 'تحت الانشاء';
    //     }

    //     // Otherwise, mixed statuses but no available units
    //     return 'مباع بالكامل';
    // }

    public function getDynamicProjectStatusAttribute()
    {
        // التحقق من وجود وحدات في المشروع
        if ($this->units()->count() == 0) {
            return 'تحت الانشاء';
        }

        // الحصول على جميع حالات الوحدات
        $unitCases = $this->units()->pluck('case');

        // إحصائيات الحالات
        $availableCount = $unitCases->filter(fn ($case) => $case == 0)->count();
        $reservedCount = $unitCases->filter(fn ($case) => $case == 1)->count();
        $soldCount = $unitCases->filter(fn ($case) => $case == 2)->count();
        $underConstructionCount = $unitCases->filter(fn ($case) => $case == 3)->count();

        $totalUnits = $unitCases->count();

        // الحالات الأساسية (كل الوحدات لها نفس الحالة)
        if ($availableCount == $totalUnits) {
            return "متاح ($totalUnits وحدة)";
        }

        if ($soldCount == $totalUnits) {
            return "مباع بالكامل ($totalUnits وحدة)";
        }

        if ($reservedCount == $totalUnits) {
            return "محجوز بالكامل ($totalUnits وحدة)";
        }

        if ($underConstructionCount == $totalUnits) {
            return "تحت الانشاء ($totalUnits وحدة)";
        }

        // الحالات المختلطة مع التفاصيل
        if ($availableCount > 0) {
            $statusParts = [];
            $statusParts[] = "متاح $availableCount";

            if ($reservedCount > 0) {
                $statusParts[] = "محجوز $reservedCount";
            }
            if ($soldCount > 0) {
                $statusParts[] = "مباع $soldCount";
            }
            if ($underConstructionCount > 0) {
                $statusParts[] = "تحت الإنشاء $underConstructionCount";
            }

            return implode(' | ', $statusParts);
        }

        // إذا لم يكن هناك وحدات متاحة
        if ($availableCount == 0) {
            $statusParts = [];

            if ($reservedCount > 0) {
                $statusParts[] = "محجوز $reservedCount";
            }
            if ($soldCount > 0) {
                $statusParts[] = "مباع $soldCount";
            }
            if ($underConstructionCount > 0) {
                $statusParts[] = "تحت الإنشاء $underConstructionCount";
            }

            return implode(' | ', $statusParts);
        }

        // الحالة الافتراضية
        return "المجموع $totalUnits وحدة";
    }

    // دالة مساعدة للحصول على معلومات مفصلة
    public function getProjectStatusDetailsAttribute()
    {
        if ($this->units()->count() == 0) {
            return [
                'status' => 'تحت الانشاء',
                'available' => 0,
                'reserved' => 0,
                'sold' => 0,
                'under_construction' => 0,
                'total' => 0,
                'availability_percentage' => 0,
            ];
        }

        $unitCases = $this->units()->pluck('case');

        $available = $unitCases->filter(fn ($case) => $case == 0)->count();
        $reserved = $unitCases->filter(fn ($case) => $case == 1)->count();
        $sold = $unitCases->filter(fn ($case) => $case == 2)->count();
        $underConstruction = $unitCases->filter(fn ($case) => $case == 3)->count();
        $total = $unitCases->count();

        $availabilityPercentage = $total > 0 ? round(($available / $total) * 100, 1) : 0;

        return [
            'status' => $this->dynamic_project_status,
            'available' => $available,
            'reserved' => $reserved,
            'sold' => $sold,
            'under_construction' => $underConstruction,
            'total' => $total,
            'availability_percentage' => $availabilityPercentage,
        ];
    }

    // دالة للحصول على حالة مبسطة للألوان
    public function getProjectStatusTypeAttribute()
    {
        $details = $this->project_status_details;

        if ($details['available'] > 0) {
            return 'available';
        } elseif ($details['total'] == $details['sold']) {
            return 'sold_out';
        } elseif ($details['total'] == $details['reserved']) {
            return 'fully_reserved';
        } elseif ($details['total'] == $details['under_construction']) {
            return 'under_construction';
        } else {
            return 'mixed';
        }
    }

    // public function getFirstPdfUrl()
    // {
    //     try {
    //         $firstPdf = $this->hasMany(ProjectMedia::class)
    //             ->where('media_type', 'pdf')
    //             ->first();

    //         // Generate a signed URL if the file exists
    //         if ($firstPdf) {
    //             return $firstPdf->media_url;
    //             // return URL::temporarySignedRoute('project.download', now()->addMinutes(30), [
    //             //     'project' => $this->id,
    //             //     'file' => $firstPdf->media_url,
    //             // ]);
    //         }

    //         return null;
    //     } catch (\Exception $e) {
    //         return null;
    //     }
    // }

    // Helper methods to calculate ranges
    public function getPriceRangeAttribute()
    {
        $minPrice = $this->units->min('unit_price');
        $maxPrice = $this->units->max('unit_price');

        if ($minPrice === $maxPrice) {
            if ($this->show_price) {
                return number_format($minPrice);
            }
            // return number_format($minPrice);
        }

        return number_format($minPrice).' الي '.number_format($maxPrice);
    }

    public function getSpaceRangeAttribute()
    {
        $minSpace = $this->units->min('unit_area');
        $maxSpace = $this->units->max('unit_area');

        if ($minSpace === $maxSpace) {
            return $minSpace.' م²';
        }

        return $minSpace.' - '.$maxSpace.' م²';
    }

    public function getBedroomRangeAttribute()
    {
        $minBedrooms = $this->units->min('beadrooms');
        $maxBedrooms = $this->units->max('beadrooms');

        if ($minBedrooms === $maxBedrooms) {
            return $minBedrooms;
        }

        return $minBedrooms.' - '.$maxBedrooms;
    }

    public function getBathroomRangeAttribute()
    {
        $minBathrooms = $this->units->min('bathrooms');
        $maxBathrooms = $this->units->max('bathrooms');

        if ($minBathrooms === $maxBathrooms) {
            return $minBathrooms;
        }

        return $minBathrooms.' - '.$maxBathrooms;
    }

    public function getKitchenRangeAttribute()
    {
        $minKitchens = $this->units->min('kitchen');
        $maxKitchens = $this->units->max('kitchen');

        if ($minKitchens === $maxKitchens) {
            return $minKitchens;
        }

        return $minKitchens.' - '.$maxKitchens;
    }

    // tracking methods
    // Scope for popular projects
    public function scopePopular($query, $days = 30)
    {
        return $query->selectRaw('*, (visits_count + views_count * 2 + shows_count * 3 + orders_count * 10) as popularity_score')
            ->where('last_visited_at', '>', now()->subDays($days))
            ->orderBy('popularity_score', 'desc');
    }

    // Scope for most visited projects
    public function scopeMostVisited($query, $days = 30)
    {
        return $query->where('last_visited_at', '>', now()->subDays($days))
            ->orderBy('visits_count', 'desc');
    }

    // Get total tracking stats including units
    public function getTotalTrackingStats()
    {
        $projectStats = [
            'visits' => $this->visits_count,
            'views' => $this->views_count,
            'shows' => $this->shows_count,
            'orders' => $this->orders_count,
        ];

        $unitStats = $this->units->reduce(function ($carry, $unit) {
            $carry['visits'] += $unit->visits_count;
            $carry['views'] += $unit->views_count;
            $carry['shows'] += $unit->shows_count;
            $carry['orders'] += $unit->orders_count;

            return $carry;
        }, ['visits' => 0, 'views' => 0, 'shows' => 0, 'orders' => 0]);

        return [
            'project_stats' => $projectStats,
            'units_stats' => $unitStats,
            'total_stats' => [
                'visits' => $projectStats['visits'] + $unitStats['visits'],
                'views' => $projectStats['views'] + $unitStats['views'],
                'shows' => $projectStats['shows'] + $unitStats['shows'],
                'orders' => $projectStats['orders'] + $unitStats['orders'],
            ],
        ];
    }

    // Get project conversion rate
    public function getConversionRate()
    {
        $totalStats = $this->getTotalTrackingStats()['total_stats'];
        if ($totalStats['visits'] == 0) {
            return 0;
        }

        return round(($totalStats['orders'] / $totalStats['visits']) * 100, 2);
    }
}
