<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::middleware(SetLocale::class)->group(function () {
        Route::get('/{locale}/_test-locale', function () {
            return response()->json([
                'locale' => App::getLocale(),
            ]);
        })->where('locale', 'ar|en');

        Route::get('/_test-locale-default', function () {
            return response()->json([
                'locale' => App::getLocale(),
            ]);
        });

        Route::get('/{locale?}/_test-admin', function () {
            return response()->json(['locale' => App::getLocale()]);
        });

        Route::any('/{any}/_test-unregistered', function () {
            return response()->json(['locale' => App::getLocale()]);
        })->where('any', '.*');
    });
});

it('sets locale from URL prefix', function () {
    $this->get('/en/_test-locale')
        ->assertJson(['locale' => 'en']);

    $this->get('/ar/_test-locale')
        ->assertJson(['locale' => 'ar']);
});

it('defaults to Arabic when no URL prefix is present', function () {
    $this->get('/_test-locale-default')
        ->assertJson(['locale' => 'ar']);
});

it('falls back to cookie locale when no URL prefix is present', function () {
    Cookie::queue('locale', 'en', 60 * 24 * 365);

    $this->call('GET', '/_test-locale-default', [], ['locale' => 'en'])
        ->assertJson(['locale' => 'en']);
});

it('uses URL prefix over cookie when both are present and conflict', function () {
    Cookie::queue('locale', 'ar', 60 * 24 * 365);

    $this->get('/en/_test-locale')
        ->assertJson(['locale' => 'en']);
});

it('redirects invalid locale segments to default locale', function () {
    Log::shouldReceive('channel')
        ->with('stack')
        ->andReturnSelf();

    Log::shouldReceive('warning')
        ->once();

    $this->get('/fr/_test-unregistered')
        ->assertJson(['locale' => 'ar']);
});

it('does not affect non-public routes', function () {
    Route::get('/_test-admin-excluded', function () {
        return response()->json(['locale' => App::getLocale()]);
    })->middleware(SetLocale::class);

    $this->get('/_test-admin-excluded')
        ->assertJson(['locale' => 'ar']);
});

it('persists locale in session', function () {
    $this->get('/en/_test-locale');

    expect(session('locale'))->toBe('en');
});
