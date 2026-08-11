<?php

return [
    'title' => [
        'not_empty' => ':field must not be empty',
    ],
    'body' => [
        'not_empty' => ':field must not be empty',
        'min_length' => 'Body must be at least :param2 characters long',
    ],
    'author' => [
        'not_empty' => ':field must not be empty',
        'invalid' => 'The username :param1 does not exist',
    ],
    'created' => [
        'not_empty' => ':field must not be empty',
        'invalid' => 'The date :param1 is invalid',
    ],
    'status' => [
        'not_empty' => ':field must not be empty',
    ],
    'categories' => [
        'not_empty' => ':field must not be empty',
        'invalid' => 'You must select at least one category',
    ],
    'pubdate' => [
        'not_empty' => ':field must not be empty',
        'invalid' => 'The publish date :param1 is invalid',
    ],
];