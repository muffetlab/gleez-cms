<?php
/**
 * Setting the Routes
 *
 * @package    Gleez\User\Routing
 * @author     Gleez Team
 * @copyright  (c) 2011-2014 Gleez Technologies
 * @license    https://gleezcms.org/license Gleez CMS License
 */
if ( ! Route::cache())
{
    //User Backend routes
    Route::set('admin/permission', 'admin/permissions(/<action>)(/<id>)', [
        'id' => '\d+',
        'action' => 'list|role|user'
    ])->defaults([
        'directory' => 'admin',
        'controller' => 'permission',
        'action' => 'list'
    ]);

    Route::set('admin/role', 'admin/roles(/<action>(/<id>))(/p<page>)', [
        'id' => '\d+',
        'page' => '\d+',
        'action' => 'list|add|edit|delete'
    ])->defaults([
        'directory' => 'admin',
        'controller' => 'role',
        'action' => 'list'
    ]);

    Route::set('admin/user', 'admin/users(/<action>(/<id>))(/p<page>)', [
        'id' => '\d+',
        'page' => '\d+',
        'action' => 'list|add|edit|delete'
    ])->defaults([
        'directory' => 'admin',
        'controller' => 'user',
        'action' => 'list',
    ]);

    //User Frontend routes
    Route::set('user', 'user(/<action>)(/<id>)(/<token>)', [
        'action' => 'edit|login|logout|view|register|confirm|password|profile|photo',
        'id' => '\d+'
    ])->defaults([
        'controller' => 'user',
        'action' => 'view',
        'token' => NULL,
    ]);

    Route::set('user/oauth', 'oauth/<controller>(/<action>)')->defaults([
        'directory' => 'oauth',
        'action' => 'index',
    ]);

    Route::set('user/reset', 'user/reset(/<action>)(/<id>)(/<token>)(/<time>)', [
        'action' => 'password|confirm_password',
        'id' => '\d+',
        'time' => '\d+'
    ])->defaults([
        'controller' => 'user',
        'action' => 'confirm_password',
        'token' => NULL,
        'time' => NULL,
    ]);

    Route::set('user/buddy', 'buddy(/<action>)(/<id>)(/p<page>)', [
        'action' => 'index|add|accept|reject|delete|sent|pending',
        'id' => '\d+',
        'page' => '\d+',
    ])->defaults([
        'controller' => 'buddy',
        'action' => 'index',
    ]);

    Route::set('user/message', 'message(/<action>)(/<id>)', [
        'id' => '\d+',
        'action' => 'index|inbox|outbox|drafts|list|view|edit|compose|delete|bulk'
    ])->defaults([
        'controller' => 'message',
        'action' => 'index'
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
    ACL::set('user', [
        'administer permissions' => [
            'title' => __('Administer permissions'),
            'restrict access' => TRUE,
            'description' => __('Managing user authority'),
        ],
        'administer users' => [
            'title' => __('Administer users'),
            'restrict access' => TRUE,
            'description' => __('Users management'),
        ],
        'access profiles' => [
            'title' => __('Access profiles'),
            'restrict access' => FALSE,
            'description' => __('Access to all profiles'),
        ],
        'edit profile' => [
            'title' => __('Editing profile'),
            'restrict access' => FALSE,
            'description' => __('The ability to change profile'),
        ],
        'change own username' => [
            'title' => __('Change own username'),
            'restrict access' => TRUE,
            'description' => __('The ability to change own username'),
        ]
    ]);

	/** Cache the module specific permissions in production */
	ACL::cache(FALSE, Kohana::$environment === Kohana::PRODUCTION);
}
