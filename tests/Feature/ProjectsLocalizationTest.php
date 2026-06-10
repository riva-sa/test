<?php

it('displays projects page in Arabic by default', function () {
    $this->get('/projects')
        ->assertSee('المشاريع');
});

it('displays projects page in English when locale is en', function () {
    $this->get('/en/projects')
        ->assertSee('Projects');
});

it('displays project filter labels in correct locale', function () {
    $this->get('/en/projects')
        ->assertSee('Price')
        ->assertSee('Filter')
        ->assertSee('Sort');

    $this->get('/ar/projects')
        ->assertSee('السعر')
        ->assertSee('فلترة')
        ->assertSee('ترتيب');
});

it('displays status badges in correct locale', function () {
    $this->get('/en/projects')
        ->assertSee('Available');

    $this->get('/ar/projects')
        ->assertSee('متاح');
});

it('displays empty-state message in correct locale', function () {
    $this->get('/en/projects')
        ->assertDontSee('تعذر وجود نتائج');

    $this->get('/ar/projects')
        ->assertSee('نتائج');
});
