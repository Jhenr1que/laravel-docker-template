<?php

return [
    'connection' => 'wordpress',

    'post_types' => [
        'post' => App\Models\BlogPost::class,
    ],

    'shortcodes' => [],

    'shortcode_parser' => Thunder\Shortcode\Parser\RegularParser::class,
];