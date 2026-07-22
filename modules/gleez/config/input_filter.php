<?php

return [
    'default_format' => 1,
    'allowed_protocols' => '',
    'allowed_tags' => '',
    'admin_allowed_tags' => '',
    'formats' => [
        // #1
        1 => [
            'name' => __('Filtered HTML'),
            'weight' => 0,
            'filters' => [
                'html' => [
                    'name' => 'html',
                    'weight' => 0,
                    'status' => TRUE,
                    'settings' => [
                        'html_nofollow' => TRUE,
                        'allowed_html' => '<h2> <h3> <h4> <h5> <h6> <a> <abbr> <address> <em> <strong> <b> <i> <br> <hr> <p> <cite> <blockquote> <q> <code> <ul> <ol> <li> <dl> <dt> <dd> <img> <sub> <sup> <s>',
                        'url_length' => 72,
                    ]
                ],
                'html_corrector' => [
                    'name' => 'html_corrector',
                    'weight' => 3,
                    'status' => TRUE,
                    'settings' => []
                ],
                'url' => [
                    'name' => 'url',
                    'weight' => -2,
                    'status' => TRUE,
                    'settings' => ['url_length' => 72]
                ],
                'auto_p' => [
                    'name' => 'auto_p',
                    'weight' => 1,
                    'status' => TRUE,
                    'settings' => []
                ],
            ],
            // Comma separated list
            'roles' => [],
        ],
        // #2
        2 => [
            'name' => __('Plain Text'),
            'weight' => 1,
            'filters' => [
                'plain' => [
                    'name' => 'plain',
                    'weight' => 0,
                    'status' => TRUE,
                    'settings' => []
                ],
            ],
            // Comma separated list
            'roles' => [],
        ],
        // #3
        3 => [
            'name' => __('Full HTML'),
            'weight' => 1,
            'filters' => [
                'html_corrector' => [
                    'name' => 'html_corrector',
                    'weight' => 0,
                    'status' => TRUE,
                    'settings' => []
                ],
                'url' => [
                    'name' => 'url',
                    'weight' => 0,
                    'status' => TRUE,
                    'settings' => []
                ],
                'auto_p' => [
                    'name' => 'auto_p',
                    'weight' => 10,
                    'status' => TRUE,
                    'settings' => []
                ],
            ],
            // Comma separated list
            'roles' => ['admin'],
        ],
        // #4
        4 => [
            'name' => __('Markdown'),
            'weight' => 1,
            'filters' => [
                'markdown' => [
                    'name' => 'markdown',
                    'weight' => 0,
                    'status' => TRUE,
                    'settings' => []
                ],
            ],
            // Comma separated list
            'roles' => [],
        ],
    ]
];
