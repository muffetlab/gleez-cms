<?php

return [
    // Subject length
    'subject_length' => 80,

    // Body length
    'body_length' => 400,

    // Use captcha?
    'use_captcha' => TRUE,

    // Mail type
    'types' => [
        '' => __('Please Choose Category'),
        'advertise' => __('Advertise'),
        'feedback' => __('Feedback'),
        'info' => __('Info'),
        'privacy' => __('Privacy'),
        'other' => __('Other'),
    ],
];
