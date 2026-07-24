<?php
/**
 * Setting the Routes
 *
 * @package    Gleez\Oauth2\Routing
 * @author     Gleez Team
 * @copyright  (c) 2011-2014 Gleez Technologies
 * @license    https://gleezcms.org/license Gleez CMS License
 */
if ( ! Route::cache())
{
    //User Backend routes
    Route::set('admin/oauth2', 'admin/oauth2(/<action>(/<id>))(/p<page>)', [
        'id' => '\d+',
        'page' => '\d+',
        'action' => 'list|add|edit|delete'
    ])->defaults([
        'directory' => 'admin',
        'controller' => 'oauth2',
        'action' => 'list',
    ]);

    Route::set('oauth2/provider', 'oauth2/<provider>(/<action>)', [
        'provider' => 'gleez|google|facebook|live|github'
    ])->defaults([
        'controller' => 'provider',
        'action' => 'index',
    ]);

    // OAuth2 frontend routes
    Route::set('oauth2/auth', 'oauth2/auth')->defaults([
        'controller' => 'authorize',
        'action' => 'index',
    ]);

    Route::set('oauth2/token', 'oauth2/token')->defaults([
        'controller' => 'token',
        'action' => 'index',
    ]);

    Route::set('oauth2/revoke', 'oauth2/revoke')->defaults([
        'controller' => 'revoke',
        'action' => 'index',
    ]);

    Route::set('oauth2/test', 'oauth2/test(/<action>)')->defaults([
        'controller' => 'oauthtest',
        'action' => 'coderequest',
    ]);

    Route::set('oauth2/me', 'oauth2/me(/<action>)')->defaults([
        'controller' => 'me',
        'action' => 'index',
    ]);

    Route::set('oauth2/client', 'oauth2/client(/<action>)(/<id>)', [
        'id' => '\d+',
        'action' => 'list|register|edit|view|delete'
    ])->defaults([
        'controller' => 'client',
        'action' => 'list',
    ]);
} 


/**
 * Define Module specific Permissions
 *
 * Definition of user privileges by default if the ACL is present in the system.
 * Note: Parameter `restrict access` indicates that these privileges have serious
 * implications for safety.
 *
 * @uses ACL Used to define the privileges
 */
if ( ! ACL::cache() )
{
    ACL::set('oauth2', [
        'administer oauth2' => [
            'title' => __('Administer OAuth2'),
            'restrict access' => TRUE,
            'description' => __('oAUth Management'),
        ],
        'access oaclient2' => [
            'title' => __('Access Clients'),
            'restrict access' => FALSE,
            'description' => __('Access to all OAuth2 Clients'),
        ],
        'edit oaclient2' => [
            'title' => __('Edit Client'),
            'restrict access' => FALSE,
            'description' => __('The ability to change OAuth2 Client'),
        ],
        'edit own oaclient2' => [
            'title' => __('Change own Client'),
            'restrict access' => TRUE,
            'description' => __('The ability to change own OAuth2 Client'),
        ],
        'delete oaclient2' => [
            'title' => __('Delete Client'),
            'restrict access' => FALSE,
            'description' => __('The ability to delete OAuth2 Client'),
        ],
        'delete own oaclient2' => [
            'title' => __('Delete own Client'),
            'restrict access' => TRUE,
            'description' => __('The ability to delete own OAuth2 Client'),
        ]
    ]);

    /** Cache the module specific permissions in production */
    ACL::cache(FALSE, Kohana::$environment === Kohana::PRODUCTION);
}
 