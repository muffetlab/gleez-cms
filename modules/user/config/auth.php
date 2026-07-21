<?php
/**
 * The Gleez users auth configuration
 *
 * @package    Gleez\User\Config
 * @author     Gleez Team
 * @copyright  (c) 2011-2013 Gleez Technologies
 * @license    https://gleezcms.org/license Gleez CMS License
 */
return [
    'driver' => 'ORM',

    /**
     * Type of hash to use for passwords.
     * Any algorithm supported by the hash function can be used here.
     *
     * @link http://php.net/hash
     * @link http://php.net/hash_algos
     */
    'hash_method' => 'sha256',

    // Set the auto-login (remember me) cookie lifetime, in seconds. The default lifetime is two weeks.
    'lifetime' => 1209600,

    // Set the session key that will be used to store the current user.
    'session_key' => 'auth_user',

    // Use username for login and registration (TRUE) or use email as username (FALSE)?
    'username' => TRUE,

    // Allow user registration?
    'register' => TRUE,

    // Username rules for validation
    'name' => [
        'chars' => 'a-zA-Z0-9_\-\^\.',
        'length_min' => 4,
        'length_max' => 32,
    ],

    // Password rules for validation
    'password' => [
        'length_min' => 8,
        'length_max' => 32,
        'uppercase' => true,
        'lowercase' => true,
        'digits' => true,
        'symbols' => true,
    ],

    // Use confirm password field in registration?
    'confirm_pass' => TRUE,

    // Use nickname for registration (TRUE) or use username (FALSE)?
    'use_nick' => TRUE,

    // Use captcha for registration (TRUE)?
    'use_captcha' => TRUE,

    /**
     * The number of failed logins allowed can be specified here:
     * If the user mistypes their password X times,
     * then they will not be permitted to log in during the jail time.
     *
     * This helps prevent brute-force attacks.
     */
    'auth' => [
        // Define the maximum failed attempts to log in. Set 0 to disable the login jail.
        'max_failed_logins' => 5,

        // Define the time that user who archive the max_failed_logins will need to wait before his next attempt.
        'login_jail_time' => 900,
    ],

    // Enable buddy relationship (FALSE)?
    'enable_buddy' => FALSE,
];
