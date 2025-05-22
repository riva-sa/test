<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class Project extends Model
{
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
        'sales_manager_id'
    ];

    protected $casts =
    [
        'status' => 'boolean',
        'show_price' => 'boolean',
        'location' => 'array',
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
            $images = $this->hasMany(ProjectMedia::class)
                ->where('media_type', 'image')
                ->get();

            return $images->isNotEmpty() ? $images : collect([]); // إرجاع مجموعة فارغة إذا لم يكن هناك صور
        } catch (\Exception $e) {
            // التعامل مع الخطأ إذا حدث مشكلة في الاستعلام
            return collect([]); // إرجاع مجموعة فارغة في حالة الخطأ
        }
    }

    public function getMainImages()
    {
        try {
            $mainImage = $this->hasMany(ProjectMedia::class)
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
            $videos = $this->hasMany(ProjectMedia::class)
                ->where('media_type', 'video')
                ->get();

            return $videos->isNotEmpty() ? $videos : collect([]); // إرجاع مجموعة فارغة إذا لم يكن هناك فيديوهات
        } catch (\Exception $e) {
            return collect([]); // إرجاع مجموعة فارغة في حالة الخطأ
        }
    }

    public function getMediaPdf()
    {
        try {
            $pdfs = $this->hasMany(ProjectMedia::class)
                ->where('media_type', 'pdf')
                ->get();

            return $pdfs->isNotEmpty() ? $pdfs : collect([]); // إرجاع مجموعة فارغة إذا لم يكن هناك PDF
        } catch (\Exception $e) {
            return collect([]); // إرجاع مجموعة فارغة في حالة الخطأ
        }
    }

    public function getMainImage()
    {
        try {
            $mainImage = $this->hasMany(ProjectMedia::class)
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
            $firstPdf = $this->hasMany(ProjectMedia::class)
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
        $availableCount = $unitCases->filter(fn($case) => $case == 0)->count();
        $reservedCount = $unitCases->filter(fn($case) => $case == 1)->count();
        $soldCount = $unitCases->filter(fn($case) => $case == 2)->count();
        $underConstructionCount = $unitCases->filter(fn($case) => $case == 3)->count();

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
                'availability_percentage' => 0
            ];
        }

        $unitCases = $this->units()->pluck('case');

        $available = $unitCases->filter(fn($case) => $case == 0)->count();
        $reserved = $unitCases->filter(fn($case) => $case == 1)->count();
        $sold = $unitCases->filter(fn($case) => $case == 2)->count();
        $underConstruction = $unitCases->filter(fn($case) => $case == 3)->count();
        $total = $unitCases->count();

        $availabilityPercentage = $total > 0 ? round(($available / $total) * 100, 1) : 0;

        return [
            'status' => $this->dynamic_project_status,
            'available' => $available,
            'reserved' => $reserved,
            'sold' => $sold,
            'under_construction' => $underConstruction,
            'total' => $total,
            'availability_percentage' => $availabilityPercentage
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
        $minPrice = $this->units()->min('unit_price');
        $maxPrice = $this->units()->max('unit_price');

        if ($minPrice === $maxPrice) {
            if ($this->show_price) {
                return number_format($minPrice);
            }
            // return number_format($minPrice);
        }

        return number_format($minPrice) . ' الي ' . number_format($maxPrice);
    }

    public function getSpaceRangeAttribute()
    {
        $minSpace = $this->units()->min('unit_area');
        $maxSpace = $this->units()->max('unit_area');

        if ($minSpace === $maxSpace) {
            return $minSpace . ' م²';
        }

        return $minSpace . ' - ' . $maxSpace . ' م²';
    }

    public function getBedroomRangeAttribute()
    {
        $minBedrooms = $this->units()->min('beadrooms');
        $maxBedrooms = $this->units()->max('beadrooms');

        if ($minBedrooms === $maxBedrooms) {
            return $minBedrooms;
        }

        return $minBedrooms . ' - ' . $maxBedrooms;
    }

    public function getBathroomRangeAttribute()
    {
        $minBathrooms = $this->units()->min('bathrooms');
        $maxBathrooms = $this->units()->max('bathrooms');

        if ($minBathrooms === $maxBathrooms) {
            return $minBathrooms;
        }

        return $minBathrooms . ' - ' . $maxBathrooms;
    }

    public function getKitchenRangeAttribute()
    {
        $minKitchens = $this->units()->min('kitchen');
        $maxKitchens = $this->units()->max('kitchen');

        if ($minKitchens === $maxKitchens) {
            return $minKitchens;
        }

        return $minKitchens . ' - ' . $maxKitchens;
    }

}
