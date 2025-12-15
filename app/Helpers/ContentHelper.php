<?php

namespace App\Helpers;

use App\Models\ContentBlock;

class ContentHelper
{
    public static function get(string $key, string $default = ''): string
    {
        $block = ContentBlock::where('key', $key)->first();

        return $block ? $block->content : $default;

        // {!! \App\Helpers\ContentHelper::get('header_title', 'Default Header') !!}
    }
}
