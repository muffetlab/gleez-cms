<?php

return [
    'name' => [
        'not_empty' => 'You must provide a username',
        'min_length' => 'The username must be at least :param2 characters long',
        'max_length' => 'The username must be less than :param2 characters long',
        'username_available' => 'This username is not available',
        'invalid' => 'This username or password is not valid',
        'unique' => 'This username already exists',
    ],
    'pass' => [
        'not_empty' => 'You must provide a password',
        'min_length' => ':field must be at least :param2 characters long',
    ],
    'pass_confirm' => [
        'matches' => ':field must be the same as :param3',
    ],
    'mail' => [
        'not_empty' => 'You must provide an email address',
        'email_available' => 'This email already exists',
        'unique' => 'This email already exists',
    ],
    '_external' => [
        'pass_confirm' => 'The values you entered in the password fields did not match',
        'old_pass' => [
            'check_password' => 'Old password is incorrect',
        ],
    ],
    'homepage' => [
        'url' => ':field must be a valid address with the http:// or https:// prefix',
    ],
    'bio' => [
        'max_length' => 'Bio must be less than :param2 characters long',
    ],
];
