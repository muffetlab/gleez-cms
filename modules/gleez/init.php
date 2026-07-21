<?php
/**
 * Setting the Routes
 *
 * @package    Gleez\Routing
 * @author     Sandeep Sangamreddi - Gleez
 * @copyright  (c) 2011-2014 Gleez Technologies
 * @license    https://gleezcms.org/license Gleez CMS License
 */
if ( ! Route::cache())
{
// -- Gleez media routes -------------------------------------------------------

	// Image resize
    Route::set('resize', 'media/imagecache/<type>/<dimensions>(/<file>)', [
		'dimensions' => '\d+x\d+',
		'type'       => 'crop|ratio|resize',
        'file' => '[\w.\-\/]+'
    ])->defaults([
		'controller' => 'resize',
		'action'     => 'image',
		'type'       => 'resize'
    ]);

	// Static file serving (CSS, JS, images)
    Route::set('media', 'media(/<theme>)/<file>', [
        'file' => '.+',
        'theme' => Theme::route_list()
    ])->defaults([
		'controller' => 'media',
		'action'     => 'serve',
		'file'       => NULL,
    ]);

// -- Gleez backend routes -----------------------------------------------------

    Route::set('admin/autocomplete', 'admin/autocomplete/<action>(/<string>)', [
		'string'     => '.+',
		'action'     => 'index|links',
    ])->defaults([
		'directory'  => 'admin',
		'controller' => 'autocomplete',
		'action'     => 'index',
    ]);

    Route::set('admin/login', 'admin/login', [
		'action'     => 'index|login',
    ])->defaults([
		'controller' => 'admin',
		'action'     => 'login',
    ]);

    Route::set('admin/module', 'admin/modules(/<action>)')->defaults([
		'directory'  => 'admin',
		'controller' => 'modules',
		'action'     => 'list',
    ]);

    Route::set('admin/page', 'admin/pages(/<action>(/<id>))', [
		'id'         => '\d+',
		'action'     => 'index|list|settings|bulk'
    ])->defaults([
		'directory'  => 'admin',
		'controller' => 'page',
		'action'     => 'list',
    ]);

    Route::set('admin/comment', 'admin/comments(/<action>(/<id>))(/p<page>)', [
		'id'         => '\d+',
		'page'       => '\d+',
		'action'     => 'index|list|process|view|delete|spam|pending'
    ])->defaults([
		'directory'  => 'admin',
		'controller' => 'comment',
		'action'     => 'list',
    ]);

    Route::set('admin/menu', 'admin/menus(/<action>(/<id>))(/p/<page>)', [
		'id'         => '\d+',
		'page'       => '\d+',
		'action'     => 'list|add|edit|delete|confirm'
    ])->defaults([
		'directory'  => 'admin',
		'controller' => 'menu',
		'action'     => 'list',
    ]);

    Route::set('admin/menu/item', 'admin/menu/manage/<id>(/<action>)(/p/<page>)', [
		'id'         => '\d+',
		'page'       => '\d+',
		'action'     => 'list|add|edit|delete|confirm',
		'slug'       => '[A-Za-z0-9-]+'
    ])->defaults([
		'directory'  => 'admin/Menu',
		'controller' => 'item',
		'action'     => 'list',
    ]);

    Route::set('admin/path', 'admin/paths(/<action>(/<id>))', [
		'id'         => '\d+',
		'action'     => 'list|add|edit|delete'
    ])->defaults([
		'directory'  => 'admin',
		'controller' => 'path',
		'action'     => 'list',
    ]);

    Route::set('admin/tag', 'admin/tags(/<action>(/<id>))', [
		'id'         => '\d+',
		'action'     => 'list|add|edit|delete'
    ])->defaults([
		'directory'  => 'admin',
		'controller' => 'tag',
		'action'     => 'list',
    ]);

    Route::set('admin/taxonomy', 'admin/taxonomy(/<action>(/<id>))(/p<page>)', [
		'id'         => '\d+',
		'page'       => '\d+',
		'action'     => 'list|add|edit|delete'
    ])->defaults([
		'directory'  => 'admin',
		'controller' => 'taxonomy',
		'action'     => 'list',
    ]);

    Route::set('admin/term', 'admin/terms(/<action>)/<id>(/p<page>)', [
		'id'         => '\d+',
		'page'       => '\d+',
		'action'     => 'list|add|edit|delete|confirm'
    ])->defaults([
		'directory'  => 'admin',
		'controller' => 'term',
		'action'     => 'list',
    ]);

    Route::set('admin/widget', 'admin/widgets(/<action>(/<id>))(/p<page>)', [
		'id'         => '\d+',
		'page'       => '\d+',
		'action'     => 'index|list|view|add|edit|delete|reset|confirm|clone'
    ])->defaults([
		'directory'  => 'admin',
		'controller' => 'widget',
    ]);

    Route::set('admin/format', 'admin/formats(/<action>(/<id>))', [
		'id'         => '\d+',
		'action'     => 'list|view|add|edit|delete|configure|reset'
    ])->defaults([
		'directory'  => 'admin',
		'controller' => 'format',
		'action'     => 'list'
    ]);

    Route::set('admin/blog', 'admin/blogs(/<action>(/<id>))', [
		'id'         => '\d+',
		'action'     => 'index|list|settings|bulk'
    ])->defaults([
		'directory'  => 'admin',
		'controller' => 'blog',
		'action'     => 'list',
    ]);

    Route::set('admin/setting', 'admin/settings(/<action>)')->defaults([
		'directory'  => 'admin',
		'controller' => 'setting',
    ]);

    Route::set('admin/tool', 'admin/tools(/<action>)')->defaults([
		'directory'  => 'admin',
		'controller' => 'tool',
    ]);

    Route::set('admin', 'admin(/<controller>)(/<action>)(/<id>)(/p<page>)', [
		'id'         => '\d+',
		'page'       => '\d+'
    ])->defaults([
		'directory'  => 'admin',
		'controller' => 'dashboard',
    ]);

// -- Gleez frontend routes ----------------------------------------------------

    Route::set('autocomplete', 'autocomplete/<action>(/<type>)(/<string>)', [
		'string'     => '(.*)',
		'action'     => 'index|user|nick|tag',
		'type'       => 'page|blog|forum'
    ])->defaults([
		'controller' => 'autocomplete',
		'type'       => 'blog',
    ]);

    Route::set('page', 'page(/<action>)(/<id>)(/p<page>)', [
		'id'         => '\d+',
		'page'       => '\d+',
		'action'     => 'index|list|view|add|edit|delete|term|tag'
    ])->defaults([
		'controller' => 'page',
		'action'     => 'index'
    ]);

    Route::set('comment', 'comment(/<action>(/<id>))(/p<page>)', [
		'id'         => '\d+',
		'page'       => '\d+',
		'action'     => 'list|process|add|view|edit|delete'
    ])->defaults([
		'controller' => 'comment',
		'action'     => 'list',
    ]);

    Route::set('comments', 'comments/<group>/<action>(/<id>)(/p<page>)(<format>)', [
		'id'         => '\d+',
		'page'       => '\d+',
		'format'     => '\.\w+',
    ])->defaults([
		'controller' => 'comments',
		'group'      => 'page',
		'action'     => 'public',
		'format'     => '.xhtml',
    ]);

    Route::set('rss', 'rss(/<controller>)(/<action>)(/<id>)(/p<page>)(/l<limit>)', [
		'id'         => '\d+',
		'page'       => '\d+',
		'limit'      => '\d+'
    ])->defaults([
		'directory'  => 'feeds',
		'controller' => 'base',
		'action'     => 'list',
    ]);

    Route::set('blog', 'blog(/<action>)(/<id>)(/p<page>)', [
		'id'         => '\d+',
		'page'       => '\d+',
		'action'     => 'index|list|view|add|edit|delete|tag|term'
    ])->defaults([
		'controller' => 'blog',
		'action'     => 'index'
    ]);

    Route::set('contact', 'contact(/<action>)')->defaults([
		'controller' => 'contact',
		'action'     => 'mail',
    ]);

    Route::set('welcome', 'welcome(/<action>)(/<id>)')->defaults([
		'controller' => 'welcome'
    ]);
}

/**
 * Define Module specific Permissions
 *
 * Definition of user privileges by default if the ACL is present in the system.
 * Note: Parameter `restrict access` indicates that these privileges have serious
 * implications for safety.
 *
 * @uses  ACL::cache
 * @uses  ACL::set
 */
if ( ! ACL::cache())
{
    ACL::set('comment', [
        'administer comment' => [
			'title' => __('Administer Comments'),
			'restrict access' => TRUE,
			'description' => __('Administer comments and comments settings'),
        ],
        'access comment' => [
			'title' => __('Access comments'),
			'restrict access' => FALSE,
			'description' => __('Access to any published comments'),
        ],
        'post comment' => [
			'title' => __('Post comments'),
			'restrict access' => FALSE,
			'description' => __('Ability to publish comments'),
        ],
        'skip comment approval' => [
			'title' => __('Skip comment approval'),
			'restrict access' => FALSE,
			'description' => __('Ability to publish comments without approval by the moderator'),
        ],
        'edit own comment' => [
			'title' => __('Edit own comments'),
			'restrict access' => FALSE,
			'description' => __('Ability to editing own comments'),
        ],
    ]);

    ACL::set('content', [
        'administer content' => [
			'title' => __('Administer content'),
			'restrict access' => TRUE,
			'description' => __('Most of the tasks associated with the administration of the contents of this website associated with this permission'),
        ],
        'access content' => [
			'title' => __('Access content'),
			'restrict access' => FALSE,
			'description' => __(''),
        ],
        'view own unpublished content' => [
			'title' => __('View own unpublished content'),
			'restrict access' => FALSE,
			'description' => __(''),
        ],
        'administer page' => [
			'title' => __('Administer pages'),
			'restrict access' => TRUE,
			'description' => __(''),
        ],
        'create page' => [
			'title' => __('Create pages'),
			'restrict access' => FALSE,
			'description' => __('The ability to create pages'),
        ],
        'edit own page' => [
			'title' => __('Edit own pages'),
			'restrict access' => FALSE,
			'description' => __(''),
        ],
        'edit any page' => [
			'title' => __('Edit any pages'),
			'restrict access' => FALSE,
			'description' => __(''),
        ],
        'delete own page' => [
			'title' => __('Delete own pages'),
			'restrict access' => FALSE,
			'description' => __(''),
        ],
        'delete any page' => [
			'title' => __('Delete any pages'),
			'restrict access' => FALSE,
			'description' => __(''),
        ],
    ]);

    ACL::set('site', [
        'administer menu' => [
			'title' => __('Administer Menus'),
			'restrict access' => TRUE,
			'description' => __(''),
        ],
        'administer paths' => [
			'title' => __('Administer Paths'),
			'restrict access' => FALSE,
			'description' => __(''),
        ],
        'administer site' => [
			'title' => __('Administer Site'),
			'restrict access' => TRUE,
			'description' => __(''),
        ],
        'administer tags' => [
			'title' => __('Administer Tags'),
			'restrict access' => FALSE,
			'description' => __(''),
        ],
        'administer terms' => [
			'title' => __('Administer Terms'),
			'restrict access' => FALSE,
			'description' => __(''),
        ],
        'administer formats' => [
			'title' => __('Administer Formats'),
			'restrict access' => TRUE,
			'description' => __('Managing the text formats of editor'),
        ],
    ]);

    ACL::set('contact', [
        'sending mail' => [
			'title' => __('Sending Mails'),
			'restrict access' => FALSE,
			'description' => __('Ability to send messages for administrators from your site'),
        ],
    ]);

    ACL::set('blog', [
        'administer blog' => [
			'title' => __('Administer Blog'),
			'restrict access' => TRUE,
			'description' => __('Administer Blog and Blog settings'),
        ],
        'create blog' => [
			'title' => __('Create Blog post'),
			'restrict access' => FALSE,
			'description' => '',
        ],
        'edit own blog' => [
			'title' => __('Edit own Blog post'),
			'restrict access' => FALSE,
			'description' => '',
        ],
        'edit any blog' => [
			'title' => __('Edit any Blog posts'),
			'restrict access' => FALSE,
			'description' => '',
        ],
        'delete own blog' => [
			'title' => __('Delete own Blog post'),
			'restrict access' => FALSE,
			'description' => '',
        ],
        'delete any blog' => [
			'title' => __('Delete any Blog posts'),
			'restrict access' => FALSE,
			'description' => '',
        ],
    ]);

	/** Cache the module specific permissions in production */
	ACL::cache(Kohana::$environment === Kohana::PRODUCTION);
}

/**
 * Load the filter cache
 *
 * @uses  Filter::cache
 * @uses  Filter::set
 * @uses  Text::html
 * @uses  Text::htmlCorrector
 * @uses  Text::auto_p
 * @uses  HTML::chars
 * @uses  Text::autolink
 * @uses  Text::initialCaps
 * @uses  Text::markdown
 */
if ( ! Filter::cache())
{
    Filter::set('html', [
			'prepare callback' => FALSE,
			'process callback' => 'Text::html'
    ])
		->title(__('Limit allowed HTML tags'))
		->description(__('Limit Allowed HTML tags'))
        ->settings([
			'html_nofollow' => TRUE,
			'allowed_html'  => '<a> <em> <strong> <cite> <blockquote> <code> <ul> <ol> <li> <dl> <dt> <dd>'
        ]);

    Filter::set('html_corrector', [
			'prepare callback' => FALSE,
        'process callback' => 'Text::htmlCorrector'
    ])
		->title(__('Correct faulty and chopped off HTML'));

    Filter::set('auto_p', [
			'prepare callback' => FALSE,
			'process callback' => 'Text::auto_p'
    ])
		->title(__('Convert line breaks into HTML'))
		->description(__('Lines and paragraphs break automatically.'));

    Filter::set('plain', [
			'prepare callback' => FALSE,
            'process callback' => 'HTML::chars'
    ])
		->title(__('Display any HTML as plain text'))
		->description(__('No HTML tags allowed.'));

    Filter::set('url', [
			'prepare callback' => FALSE,
			'process callback' => 'Text::autolink'
    ])
		->title(__('Convert URLs into links'))
		->description(__('Web page addresses and e-mail addresses turn into links automatically.'))
        ->settings([
			'url_length' => 72
        ]);

    Filter::set('initial_caps', [
			'prepare callback' => FALSE,
        'process callback' => 'Text::initialCaps'
    ])
		->title(__('Adds Initialcaps'))
		->description(__('Adds <span class="initial"> tag around the initial letter of each paragraph'));

    Filter::set('markdown', [
			'prepare callback' => FALSE,
			'process callback' => 'Text::markdown'
    ])
		->title(__('Markdown'))
		->description(__('Allows content to be submitted using Markdown, a simple plain-text syntax that is filtered into valid HTML.'));

	// Cache the Filters in production
	Filter::cache(Kohana::$environment === Kohana::PRODUCTION);
}
