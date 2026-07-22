<?php
/**
 * Configuration for OAuth Server.
 *
 * @link  http://en.wikipedia.org/wiki/OAuth  Wikipedia OAuth
 */
return [
    'storage' => [
        'client_table' => 'gl_oauth_clients',
        'token_table' => 'gl_oauth_tokens',
        'code_table' => 'gl_oauth_codes',
        'user_table' => 'gl_users',
    ],
    'grant_types' => [
        'authorization_code' => 'authorization_code',
        'user_credentials' => 'user_credentials',
        'client_credentials' => 'client_credentials',
        'refresh_token' => 'refresh_token',
    ],
    'access_token_ttl' => 3600,
    'auth_code_lifetime' => 30,
    'redirect_status_code' => 302,
    'enforce_state' => true,
    'enforce_redirect' => false,
    'require_exact_redirect_uri' => true,
    'allow_implicit' => true,
    'allow_credentials_in_request_body' => true,
    'allow_public_clients' => false,
    'www_realm' => 'Service',
    'token_param_name' => 'access_token',
    'token_bearer_header_name' => 'Bearer',
    'includeRefreshToken' => true,
    'always_issue_new_refresh_token' => false,
    'refresh_token_ttl' => 1209600,

    /**
     * 3rd party providers supported/allowed.
     */
    'providers' => [
        /**
         * Facebook
         */
        'facebook' => [
            'enable' => FALSE,
            'id' => 'your client id',
            'secret' => 'your client secret',
            'callback' => URL::site('/oauth2/facebook/callback', 'http'),
            'scope' => 'email,read_stream,publish_stream',
            'icon' => 'facebook'
        ],
        /**
         * Github
         */
        'github' => [
            'enable' => FALSE,
            'id' => 'your client id',
            'secret' => 'your client secret',
            'callback' => URL::site('/oauth2/github/callback', 'http'),
            'scope' => 'userinfo.profile userinfo.email',
            'icon' => 'github'
        ],
        /**
         * Gleez
         */
        'gleez' => [
            'enable' => FALSE,
            'id' => 'your client id',
            'secret' => 'your client secret',
            'callback' => URL::site('/oauth2/gleez/callback', 'http'),
            'scope' => 'userinfo.profile userinfo.email',
            'icon' => 'gleez'
        ],
        /**
         * Google
         */
        'google' => [
            'enable' => FALSE,
            'id' => 'your client id',
            'secret' => 'your client secret',
            'callback' => URL::site('/oauth2/google/callback', 'http'),
            'scope' => 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email',
            'icon' => 'google-plus'
        ],
        /**
         * Windows Live
         */
        'live' => [
            'enable' => FALSE,
            'id' => 'your client id',
            'secret' => 'your client secret',
            'callback' => URL::site('/oauth2/live/callback', 'http'),
            'scope' => 'wl.basic,wl.birthday,wl.emails,wl.offline_access,wl.signin',
            'icon' => 'windows'
        ],
    ]
];