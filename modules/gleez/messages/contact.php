<?php

return [
    'name' => [
        'not_empty' => 'You must provide a username',
        'min_length' => 'The username must be at least :param2 characters long',
    ],
    'mail' => [
        'not_empty' => 'You must provide an email address',
        'email' => 'Mail must be a valid email address',
        'email_domain' => 'Mail must contain a valid email domain',
    ],
    'subject' => [
        'not_empty' => 'You must provide mail subject',
        'max_length' => 'Subject must be less than :param2 characters long',
    ],
    'category' => [
        'not_empty' => 'You must select a category',
    ],
    'body' => [
        'not_empty' => 'You must provide mail body',
        'max_length' => 'Body must be less than :param2 characters long',
    ],
];