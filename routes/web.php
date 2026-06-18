<?php

use App\Http\Controllers\Api\Ai\AiSearchController;
use App\Http\Controllers\HelperController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Manager\ManagerAuthController;
use App\Http\Controllers\Manager\TrackingController;
use App\Livewire\Developer\DeveloperDashboard;
use App\Livewire\Frontend\About;
use App\Livewire\Frontend\Blog;
use App\Livewire\Frontend\BlogSingle;
use App\Livewire\Frontend\Careers;
use App\Livewire\Frontend\CareerSingle;
use App\Livewire\Frontend\ContactUs;
use App\Livewire\Frontend\HomePage;
use App\Livewire\Frontend\Privacy;
use App\Livewire\Frontend\ProjectSingle;
use App\Livewire\Frontend\ProjectsMap;
use App\Livewire\Frontend\ProjectsPage;
use App\Livewire\Frontend\Services;
use App\Livewire\Frontend\Terms;
use App\Livewire\Mannager\Announcements;
use App\Livewire\Mannager\BulkLeadImport;
use App\Livewire\Mannager\Campaigns;
use App\Livewire\Mannager\CreateOrder;
use App\Livewire\Mannager\CustomerProfile;
use App\Livewire\Mannager\CustomersList;
use App\Livewire\Mannager\ManageOrders;
use App\Livewire\Mannager\ManagerDashboard;
use App\Livewire\Mannager\Notifications;
use App\Livewire\Mannager\OrderDetails;
use App\Livewire\Mannager\OrderPermissions;
use App\Livewire\Mannager\SalesManagers;
use App\Livewire\Mannager\SessionJourneys;
use App\Livewire\Mannager\TrackingAnalytics;
use App\Models\Project;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

$registerPublicRoutes = function () {
    Route::get('/', HomePage::class)->name('frontend.home');
    Route::get('/projects', ProjectsPage::class)->name('frontend.projects');
    Route::get('/projects-map', ProjectsMap::class)->name('frontend.projects.map');
    Route::get('/project/{slug}', ProjectSingle::class)->name('frontend.projects.single');
    Route::get('/privacy', Privacy::class)->name('frontend.privacy');
    Route::get('/terms', Terms::class)->name('frontend.terms');
    Route::get('/about', About::class)->name('frontend.about');
    Route::get('/blog', Blog::class)->name('frontend.blog');
    Route::get('/blog/{slug}', BlogSingle::class)->name('frontend.blog.single');
    Route::get('/services', Services::class)->name('frontend.services');
    Route::get('/contact-us', ContactUs::class)->name('frontend.contactus');
    Route::get('/careers', Careers::class)->name('frontend.careers');
    Route::get('/careers/{slug}', CareerSingle::class)->name('frontend.careers.single');
};

// Unprefixed routes — registered first so they win inbound matching for /projects, /about, etc.
Route::middleware('set.locale')->group($registerPublicRoutes);

// Locale-prefixed routes — registered second so they own the name lookups used by route(...).
// {locale?} is optional, so route('frontend.projects') yields /projects and route(..., ['locale'=>'en']) yields /en/projects.
Route::middleware('set.locale')->prefix('{locale?}')->where(['locale' => 'ar|en'])->group($registerPublicRoutes);

Route::get('/media/{path}', [ImageController::class, 'show'])->where('path', '.*')->name('media.show');

// Manager routes protected by the 'manager' role
Route::middleware(['auth', 'role:sales_manager,sales,Admin,developer,follow_up,project_manager'])->group(function () {
    Route::get('/crm', ManagerDashboard::class)->name('manager.dashboard');
    Route::get('/crm/orders', ManageOrders::class)->name('manager.orders');
    Route::get('/crm/orders/{id}', OrderDetails::class)->name('manager.order-details');
    // customerlist
    Route::get('/crm/customerlist', CustomersList::class)->name('manager.customerlist');
    Route::get('/crm/customers/{phone}', CustomerProfile::class)->name('manager.customer-profile');
    Route::get('/crm/sales-managers', SalesManagers::class)->name('manager.sales-managers');
    Route::get('/crm/blocked-numbers', \App\Livewire\Mannager\BlockedNumbers::class)->name('manager.blocked-numbers');

    // Notification routes
    Route::get('/crm/notifications', Notifications::class)->name('manager.notifications');
    Route::get('/crm/announcements', Announcements::class)->name('manager.announcements');

    // Sales Targets & Leaderboard routes
    Route::get('/crm/targets', \App\Livewire\Mannager\SalesTargets::class)->name('manager.targets');
    Route::get('/crm/leaderboard', \App\Livewire\Mannager\Leaderboard::class)->name('manager.leaderboard');
    Route::get('/crm/{order}/permissions', OrderPermissions::class)->name('manager.permissions');

    Route::get('crm/create-order', CreateOrder::class)->name('manager.create-order');

    Route::get('/crm/reports/auto-assignment', \App\Livewire\Mannager\AutoAssignmentReport::class)->name('manager.reports.auto-assignment');

    Route::get('/crm/bulk-lead-import', BulkLeadImport::class)->name('manager.bulk-lead-import');

    Route::get('/crm/analytics', TrackingAnalytics::class)->name('manager.analytics');
    Route::get('/crm/analytics/campaigns', Campaigns::class)->name('manager.analytics.campaigns');
    Route::get('/crm/analytics/projects/{id}', \App\Livewire\Mannager\ProjectAnalyticsDetail::class)->name('manager.analytics.projects.detail');
    Route::get('/crm/analytics/units/{id}', \App\Livewire\Mannager\UnitAnalyticsDetail::class)->name('manager.analytics.units.detail');
    Route::get('/crm/journeys', SessionJourneys::class)->name('manager.journeys');
    Route::get('/crm/activities', \App\Livewire\Mannager\SystemActivities::class)->name('manager.activities');
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

Route::middleware(['auth', 'role:developer'])->prefix('developer')->group(function () {
    Route::get('/', DeveloperDashboard::class)->name('developer.dashboard');
});

// ===== Broker Portal =====
Route::prefix('broker')->name('broker.')->group(function () {
    // Auth & registration (guests)
    Route::get('/register', \App\Livewire\Broker\Register::class)->name('register');
    Route::get('/login', [\App\Http\Controllers\Broker\BrokerAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Broker\BrokerAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [\App\Http\Controllers\Broker\BrokerAuthController::class, 'logout'])->name('logout');

    // Approved brokers only (contract signature is enforced inside broker.approved)
    Route::middleware(['auth:broker', 'broker.approved'])->group(function () {
        Route::get('/contract', \App\Livewire\Broker\Contract::class)->name('contract');
        // Inline previews — named broker.contract.* so the unsigned-contract redirect skips them
        Route::get('/contract/view', [\App\Http\Controllers\Broker\BrokerFileController::class, 'contract'])->name('contract.view');
        Route::get('/contract/signed/view', fn () => app(\App\Http\Controllers\Broker\BrokerFileController::class)->contract('signed'))->name('contract.signed-view');

        Route::get('/', \App\Livewire\Broker\Dashboard::class)->name('dashboard');
        Route::get('/profile', \App\Livewire\Broker\Profile::class)->name('profile');
        Route::get('/documents/{document}', [\App\Http\Controllers\Broker\BrokerFileController::class, 'document'])->name('documents.show');
        Route::get('/projects', \App\Livewire\Broker\Projects::class)->name('projects');
        Route::get('/projects/{id}', \App\Livewire\Broker\ProjectDetails::class)->name('projects.show');
        Route::get('/units/{unit}/floor-plan', [\App\Http\Controllers\Broker\BrokerFileController::class, 'unitFloorPlan'])->name('units.floor-plan');
        Route::get('/leads', \App\Livewire\Broker\MyLeads::class)->name('leads');
        Route::get('/leads/create', \App\Livewire\Broker\SubmitLead::class)->name('leads.create');
        Route::get('/leads/{id}', \App\Livewire\Broker\LeadDetails::class)->name('leads.show');
    });
});

// Broker applications management (CRM, admins only)
Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/crm/project-commissions', \App\Livewire\Mannager\ProjectCommissions::class)->name('manager.project-commissions');
    Route::get('/crm/broker-applications', \App\Livewire\Mannager\BrokerApplications::class)->name('manager.broker-applications');
    Route::get('/crm/broker-contract-template', \App\Livewire\Mannager\BrokerContractTemplateSettings::class)->name('manager.broker-contract-template');
    Route::get('/crm/broker-contract-template/file', [\App\Http\Controllers\Manager\ContractTemplateController::class, 'file'])->name('manager.broker-contract-template.file');
    Route::get('/crm/broker-documents/{document}', [\App\Http\Controllers\Manager\BrokerDocumentController::class, 'show'])->name('manager.broker-documents.show');
    Route::get('/crm/brokers/{broker}/contract/{type}', [\App\Http\Controllers\Manager\BrokerDocumentController::class, 'contract'])->name('manager.broker-contract.show');

    // Careers: job application attachments (CV, cover letter, portfolio)
    Route::get('/crm/job-applications/{application}/files/{type}', [\App\Http\Controllers\Manager\JobApplicationFileController::class, 'show'])->name('manager.job-application-files.show');
});

// Route::middleware(['auth', 'permission:view_dashboard'])->group(function () {
//     // Route::get('/restricted-dashboard', CustomDashboard::class)->name('restricted.dashboard');
//     Route::get('/manager-dashboard', ManagerDashboard::class)->name('manager.dashboard');
// });

Route::get('crm/login', [ManagerAuthController::class, 'showLoginForm'])->name('login');
Route::post('crm/login', [ManagerAuthController::class, 'login']);
Route::post('/logout', [ManagerAuthController::class, 'logout'])->name('logout');

// CRM Password Reset
Route::get('crm/reset-password/{token}', function (string $token) {
    return view('auth.crm-reset-password', ['token' => $token, 'email' => request('email')]);
})->name('password.reset');
Route::post('crm/reset-password', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = \Illuminate\Support\Facades\Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => bcrypt($password),
            ])->save();
        }
    );

    return $status === \Illuminate\Support\Facades\Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', 'تم تغيير كلمة المرور بنجاح. يمكنك تسجيل الدخول الآن.')
        : back()->withErrors(['email' => __($status)]);
})->name('password.update');

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

// Route::prefix('ai')
//     ->middleware(['throttle:60,1', 'api.ai.key'])   // throttle + custom API-key guard
//     ->group(function () {

//         // Combined single-call search (projects + units together)
//         Route::get('search',          [AiSearchController::class, 'search']);

//         // Dedicated endpoints (preferred for the chatbot form)
//         Route::get('search/projects', [AiSearchController::class, 'searchProjects']);
//         Route::get('search/units',    [AiSearchController::class, 'searchUnits']);
//     });
