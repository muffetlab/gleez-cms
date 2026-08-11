<?php

return [
    'name' => [
        'not_empty' => 'You must provide a username',
        'min_length' => 'The username must be at least :param2 characters long',
        'max_length' => 'The username must be less than :param2 characters long',
        'invalid' => 'Password or username is incorrect',
        'blocked' => 'This account is blocked',
    ],
    'pass' => [
        'not_empty' => 'You must provide a password',
    ],
    'mail' => [
        'not_empty' => 'You must provide an email address',
        'invalid' => 'Password or username is incorrect',
    ],
    'too_many_attempts' => 'Too many failed login attempts. Please try again later.',
];