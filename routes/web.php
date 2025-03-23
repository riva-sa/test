<?php

use App\Livewire\Frontend\HomePage;
use App\Livewire\Frontend\ProjectSingle;
use App\Livewire\Frontend\ProjectsMap;
use App\Livewire\Frontend\ProjectsPage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\HelperController;
use App\Livewire\Frontend\About;
use App\Livewire\Frontend\Blog;
use App\Livewire\Frontend\BlogSingle;
use App\Livewire\Frontend\ContactUs;
use App\Livewire\Frontend\Services;

Route::get('/', HomePage::class)->name('frontend.home');
// Route::get('/units/create', [CreateUnit::class, 'render'])->name('filament.resources.units.create');

Route::get('/projects', ProjectsPage::class)->name('frontend.projects');
Route::get('/projects-map', ProjectsMap::class)->name('frontend.projects.map');
Route::get('/project/{slug}', ProjectSingle::class)->name('frontend.projects.single');

Route::get('/about', About::class)->name('frontend.about');
Route::get('/blog', Blog::class)->name('frontend.blog');
Route::get('/blog/{slug}', BlogSingle::class)->name('frontend.blog.single');
Route::get('/services', Services::class)->name('frontend.services');
Route::get('/contact-us', ContactUs::class)->name('frontend.contactus');


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
        return 'Error: ' . $e->getMessage();
    }
});


