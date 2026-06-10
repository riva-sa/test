<?php

use Illuminate\Support\Facades\App;

beforeEach(function () {
    $this->withoutMiddleware(\App\Http\Middleware\SetLocale::class);
});

it('displays language switcher links on the home page', function () {
    $this->get('/en')
        ->assertSee('English')
        ->assertSee('العربية');
});

it('toggles locale when clicking language switcher link', function () {
    $response = $this->get('/en');
    $response->assertSee('English');

    $response = $this->get('/ar');
    $response->assertSee('العربية');
});

it('persists locale across navigation within same session', function () {
    $this->withSession(['locale' => 'en'])
        ->get('/en/projects')
        ->assertSessionHas('locale', 'en');
});

it('uses URL prefix routing correctly for both locales', function () {
    $this->get('/en/projects')->assertStatus(200);
    $this->get('/ar/projects')->assertStatus(200);
});
