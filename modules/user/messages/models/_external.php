<?php

return [
    'pass' => [
        'not_empty' => 'You must provide a password',
        'min_length' => ':field must be at least :param2 characters long',
    ],
    'pass_confirm' => [
        'not_empty' => 'You must confirm password',
        'matches' => ':field must be the same as :param3',
    ],
    'old_pass' => [
        'not_empty' => 'You must provide old password',
        'check_password' => 'Old password is incorrect',
    ],
];
