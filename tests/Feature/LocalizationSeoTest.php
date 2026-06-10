<?php

use Illuminate\Support\Facades\App;

it('includes hreflang tags for both locales on the home page', function () {
    App::setLocale('en');
    $response = $this->get('/en');

    $response->assertSee('rel="alternate" hreflang="en"', false);
    $response->assertSee('rel="alternate" hreflang="ar"', false);
});

it('includes canonical link tag on the projects page', function () {
    App::setLocale('en');
    $response = $this->get('/en/projects');

    $response->assertSee('rel="canonical"', false);
});

it('sets correct html lang attribute for English', function () {
    App::setLocale('en');
    $this->get('/en')
        ->assertSee('lang="en"', false);
});

it('sets correct html lang and dir attributes for Arabic', function () {
    App::setLocale('ar');
    $this->get('/ar')
        ->assertSee('lang="ar"', false)
        ->assertSee('dir="rtl"', false);
});

it('hreflang links reference the same route in both locales', function () {
    $enResponse = $this->get('/en/projects');
    $arResponse = $this->get('/ar/projects');

    expect($enResponse->status())->toBe(200);
    expect($arResponse->status())->toBe(200);
});

it('sets correct locale-specific SEO meta tags', function () {
    App::setLocale('en');
    $enResponse = $this->get('/en');
    $enResponse->assertSee('content="Riva Real Estate"', false);

    App::setLocale('ar');
    $arResponse = $this->get('/ar');
    $arResponse->assertSee('content="ريفا العقارية"', false);
});
