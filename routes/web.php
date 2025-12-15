<?php

use App\Http\Controllers\HelperController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Manager\ManagerAuthController;
use App\Http\Controllers\Manager\TrackingController;
use App\Livewire\Frontend\About;
use App\Livewire\Frontend\Blog;
use App\Livewire\Frontend\BlogSingle;
use App\Livewire\Frontend\ContactUs;
use App\Livewire\Frontend\HomePage;
use App\Livewire\Frontend\Privacy;
use App\Livewire\Frontend\ProjectSingle;
use App\Livewire\Frontend\ProjectsMap;
use App\Livewire\Frontend\ProjectsPage;
use App\Livewire\Frontend\Services;
use App\Livewire\Frontend\Terms;
use App\Livewire\Mannager\Campaigns;
use App\Livewire\Mannager\CreateOrder;
use App\Livewire\Mannager\CustomersList;
use App\Livewire\Mannager\ManageOrders;
use App\Livewire\Mannager\ManagerDashboard;
use App\Livewire\Mannager\OrderDetails;
use App\Livewire\Mannager\OrderPermissions;
use App\Livewire\Mannager\SalesManagers;
use App\Livewire\Mannager\SessionJourneys;
use App\Livewire\Mannager\TrackingAnalytics;
use App\Models\Project;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class)->name('frontend.home');
// Route::get('/units/create', [CreateUnit::class, 'render'])->name('filament.resources.units.create');

Route::get('/projects', ProjectsPage::class)->name('frontend.projects');
Route::get('/projects-map', ProjectsMap::class)->name('frontend.projects.map');
Route::get('/project/{slug}', ProjectSingle::class)->name('frontend.projects.single');
Route::get('/media/{path}', [ImageController::class, 'show'])->where('path', '.*')->name('media.show');

Route::get('/privacy', Privacy::class)->name('frontend.privacy');
Route::get('/terms', Terms::class)->name('frontend.terms');

Route::get('/about', About::class)->name('frontend.about');
Route::get('/blog', Blog::class)->name('frontend.blog');
Route::get('/blog/{slug}', BlogSingle::class)->name('frontend.blog.single');
Route::get('/services', Services::class)->name('frontend.services');
Route::get('/contact-us', ContactUs::class)->name('frontend.contactus');

// Manager routes protected by the 'manager' role
Route::middleware(['auth', 'role:sales_manager'])->group(function () {
    Route::get('/crm', ManagerDashboard::class)->name('manager.dashboard');
    Route::get('/crm/orders', ManageOrders::class)->name('manager.orders');
    Route::get('/crm/orders/{id}', OrderDetails::class)->name('manager.order-details');
    // customerlist
    Route::get('/crm/customerlist', CustomersList::class)->name('manager.customerlist');
    Route::get('/crm/sales-managers', SalesManagers::class)->name('manager.sales-managers');
    Route::get('/crm/{order}/permissions', OrderPermissions::class)->name('manager.permissions');

    Route::get('crm/create-order', CreateOrder::class)->name('manager.create-order');

    Route::get('/crm/analytics', TrackingAnalytics::class)->name('manager.analytics');
    Route::get('/crm/analytics/campaigns', Campaigns::class)->name('manager.analytics.campaigns');
    Route::get('/crm/journeys', SessionJourneys::class)->name('manager.journeys');
    Route::prefix('crm/tracking')->group(function () {

        // Public tracking endpoints (no authentication required)
        Route::post('/track', [TrackingController::class, 'track']);
        Route::post('/units/{unit}/track', [TrackingController::class, 'trackUnit']);
        Route::post('/projects/{project}/track', [TrackingController::class, 'trackProject']);

        // Analytics endpoints (require authentication)
        Route::middleware('auth')->group(function () {
            Route::get('/analytics', [TrackingController::class, 'getAnalytics']);
            Route::get('/analytics/units', [TrackingController::class, 'getUnitAnalytics']);
            Route::get('/analytics/projects', [TrackingController::class, 'getProjectAnalytics']);
            Route::get('/analytics/conversion-rates', [TrackingController::class, 'getConversionRates']);
            Route::get('/popular/units', [TrackingController::class, 'getPopularUnits']);
            Route::get('/popular/projects', [TrackingController::class, 'getPopularProjects']);
        });
    });
});

// Route::middleware(['auth', 'permission:view_dashboard'])->group(function () {
//     // Route::get('/restricted-dashboard', CustomDashboard::class)->name('restricted.dashboard');
//     Route::get('/manager-dashboard', ManagerDashboard::class)->name('manager.dashboard');
// });

Route::get('crm/login', [ManagerAuthController::class, 'showLoginForm'])->name('login');
Route::post('crm/login', [ManagerAuthController::class, 'login']);
Route::post('/logout', [ManagerAuthController::class, 'logout'])->name('logout');

// Route::get('/single/{slug}', function ($slug) {
//     $projects = Project::all();
//     $bestMatch = null;
//     $highestSimilarity = 0;

//     foreach ($projects as $project) {
//         similar_text($slug, $project->slug, $percent);
//         if ($percent > $highestSimilarity) {
//             $highestSimilarity = $percent;
//             $bestMatch = $project;
//         }
//     }
//     if ($bestMatch && $highestSimilarity > 60) { // نسبة تقارب 60% كحد أدنى
//         return redirect('project/' . $bestMatch->slug);
//     }

//     abort(404);
// });

Route::get('/single/{slug}', function ($slug) {
    $slugMap = [
        'جادة-الياسمين-36' => 'gad-alyasmyn-36',
        'اصال-فيلا' => 'asal-fyla',
        'شقق-فال-العارض' => 'shkk-fal-alaaard',
        'فلل-رسوخ-النرجس' => 'fll-rsokh-alnrgs',
        'تاون-هاوس-يمام-8' => 'taon-haos-ymam-8',
        'ادوار-يمام-7' => 'adoar-ymam-7',
        'صحار-النرجس' => 'shar-alnrgs',
        'زنك-4-و-5' => 'znk-4-o-5',
        'اي-كورت-النرجس' => 'ay-kort-alnrgs',
        'عزوم-النرجس' => 'aazom-alnrgs',
        'شقق-شادو-ريزيدنس' => 'shkk-shado-ryzydns',
        'اصال-اليرموك' => 'asal-alyrmok',
    ];

    if (! array_key_exists($slug, $slugMap)) {
        abort(404);
    }

    return redirect('project/'.$slugMap[$slug]);
});

// Route::get('/project/download/{project}/{file}', [HelperController::class, 'downloadPdf'])
//     ->name('project.download');

// Route::get('/run-storage-link', function () {
//     try {
//         if (!File::exists(public_path('storage'))) {
//             File::makeDirectory(public_path('storage'), 0755, true);
//         }
//         File::copyDirectory(storage_path('app/public'), public_path('storage'));
//         return 'Files copied successfully from storage/app/public to public/storage.';
//     } catch (\Exception $e) {
//         return 'Error: ' . $e->getMessage();
//     }
// });

Route::get('/run-storage-link', function () {
    try {
        $targetFolder = storage_path('app/public');
        $linkFolder = public_path('storage');

        // Remove existing directory if it exists
        if (file_exists($linkFolder)) {
            File::deleteDirectory($linkFolder);
        }

        // Copy the directory
        File::copyDirectory($targetFolder, $linkFolder);

        return 'Storage directory has been copied successfully.';
    } catch (\Exception $e) {
        return 'Error: '.$e->getMessage();
    }
});
