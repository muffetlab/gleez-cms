<?php

return [
    'source' => [
        'not_empty' => ':field must not be empty',
    ],
    'alias' => [
        'not_empty' => ':field must not be empty',
        'invalid_source' => 'Invalid URL Path'
    ],
    'lang' => [
        'min_length' => 'Language must be at least :param2 characters long',
        'max_length' => 'Language must be less than :param2 characters long',
        'regex' => '',
    ],
];