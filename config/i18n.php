<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Cấu hình đa ngôn ngữ - Holomia VR
     |--------------------------------------------------------------------------
     |
     | File này cấu hình danh sách ngôn ngữ được hỗ trợ trong hệ thống.
     | Mỗi ngôn ngữ có: code (khóa), tên hiển thị, cờ emoji, và font chữ
     | Google Fonts phù hợp (đặc biệt quan trọng với tiếng Nhật, Hàn, Trung, Ấn).
     |
     */

    'default' => 'vi',

    'supported' => [
        'vi' => [
            'name' => 'Tiếng Việt',
            'native' => 'Tiếng Việt',
            'flag' => '🇻🇳',
            'flag_code' => 'vn', // ← THÊM
            'font' => 'Be+Vietnam+Pro:wght@300;400;500;600',
            'font_family' => "'Be Vietnam Pro', sans-serif",
            'html_lang' => 'vi',
            'direction' => 'ltr',
        ],
        'en' => [
            'name' => 'English',
            'native' => 'English',
            'flag' => '🇬🇧',
            'flag_code' => 'gb', // ← THÊM
            'font' => 'Inter:wght@300;400;500;600',
            'font_family' => "'Inter', sans-serif",
            'html_lang' => 'en',
            'direction' => 'ltr',
        ],
        'ja' => [
            'name' => '日本語',
            'native' => '日本語',
            'flag' => '🇯🇵',
            'flag_code' => 'jp', // ← THÊM
            'font' => 'Noto+Sans+JP:wght@300;400;500;700',
            'font_family' => "'Noto Sans JP', sans-serif",
            'html_lang' => 'ja',
            'direction' => 'ltr',
        ],
        'zh' => [
            'name' => '中文',
            'native' => '中文（简体）',
            'flag' => '🇨🇳',
            'flag_code' => 'cn', // ← THÊM
            'font' => 'Noto+Sans+SC:wght@300;400;500;700',
            'font_family' => "'Noto Sans SC', sans-serif",
            'html_lang' => 'zh-CN',
            'direction' => 'ltr',
        ],
        'ko' => [
            'name' => '한국어',
            'native' => '한국어',
            'flag' => '🇰🇷',
            'flag_code' => 'kr', // ← THÊM
            'font' => 'Noto+Sans+KR:wght@300;400;500;700',
            'font_family' => "'Noto Sans KR', sans-serif",
            'html_lang' => 'ko',
            'direction' => 'ltr',
        ],
        'hi' => [
            'name' => 'हिन्दी',
            'native' => 'हिन्दी',
            'flag' => '🇮🇳',
            'flag_code' => 'in', // ← THÊM
            'font' => 'Noto+Sans+Devanagari:wght@300;400;500;700',
            'font_family' => "'Noto Sans Devanagari', sans-serif",
            'html_lang' => 'hi',
            'direction' => 'ltr',
        ],    ],

];
