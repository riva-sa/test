<?php

use App\Models\UnitOrder;

it('has STATUS_COLORS constant with exactly 6 entries', function () {
    expect(UnitOrder::STATUS_COLORS)->toBeArray()->toHaveCount(6);
});

it('maps all status integers 0-5 to hex color codes', function () {
    $expectedColors = [
        0 => '#3B82F6',
        1 => '#F97316',
        2 => '#5457E3',
        3 => '#9CA3AF',
        4 => '#22C55E',
        5 => '#EAB308',
    ];

    foreach ($expectedColors as $status => $hex) {
        expect(UnitOrder::STATUS_COLORS[$status])->toBe($hex);
    }
});

it('returns correct hex code from statusColor method', function () {
    foreach (UnitOrder::STATUS_COLORS as $status => $expectedHex) {
        $order = new UnitOrder(['status' => $status]);
        expect($order->statusColor())->toBe($expectedHex);
    }
});

it('returns correct Arabic label from statusLabel method', function () {
    $expectedLabels = [
        0 => 'جديد',
        1 => 'طلب مفتوح',
        2 => 'معاملات بيعية',
        3 => 'مغلق',
        4 => 'مكتمل',
        5 => 'قائمة انتظار',
    ];

    foreach ($expectedLabels as $status => $label) {
        $order = new UnitOrder(['status' => $status]);
        expect($order->statusLabel())->toBe($label);
    }
});
