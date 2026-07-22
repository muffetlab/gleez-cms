<?php

return [
    // Default Page Status (eg: draft, review, publish, etc.)
    'default_status' => 'draft',

    // Pages per page (eg: 5, 10, 15, etc)
    'items_per_page' => 15,

    // Enable captcha
    'use_captcha' => FALSE,

    // Enable to set page author
    'use_authors' => TRUE,

    // Enable teaser
    'use_excerpt' => FALSE,

    // Enable comments
    'use_comment' => TRUE,

    // View submitted info in views
    'use_submitted' => TRUE,

    // Enable taxonomy. Array of term id's for sets or FALSE to disable
    'use_category' => FALSE,

    // Enable tags
    'use_tags' => TRUE,

    // Enable login buttons above comment form
    'use_provider_buttons' => TRUE,

    // Enable per page caching for performance
    'use_cache' => FALSE,

    // Allow people to post Comment(s): 0 - disabled, 1 - read, 2 - read/write
    'comment' => 0,

    // Comment display mode
    'comment_default_mode' => 0,

    // Allow anonymous commenting (with contact information)
    'comment_anonymous' => FALSE,

    // Comments per page
    'comments_per_page' => 20,

    // Comments displayed with the older/new comments ('asc' OR 'desc')
    'comment_order' => 'asc',

    // Use primary image?
    'primary_image' => TRUE,
];
