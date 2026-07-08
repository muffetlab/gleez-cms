<?php

return array(

    // Site name
	'site_name' => 'Gleez CMS',

    // Site slogan
	'site_slogan' => 'Light, Simple, Flexible Content Management System',

    // Site logo
	'site_logo' => '/media/images/logo.png',

    // Site favicon
	'site_favicon' => '/media/icons/favicon.ico',

    // Site email
	'site_email' => 'webmaster@gleezcms.org',

    // Site url used for background tasks
	'site_url' => 'www.gleezcms.org',

    // Site mission
	'site_mission' => '',

    // Keywords for search engines
	'keywords' => 'cms, cmf, gleez, kohana, php framework, site building',

    // Description for search engines
	'description' => 'Light, Simple, Flexible Content Management System',

    // Site title separator
	'title_separator' => ' :: ',

    // Default active site theme
	'theme' => 'cerber',

    // Default active admin theme
	'admin_theme' => 'cerber',

    // Mobile Theme or false
	'mobile_theme' => FALSE,

    // Maintenance Mode
	'maintenance_mode' => FALSE,

    // The module search paths. They are searched in the order given.
	'module_paths' => array(MODPATH),

    // The theme search paths. They are searched in the order given.
	'theme_paths' => array(THEMEPATH),

    // Offline message in Maintenance Mode
	'offline_message' => '',

    // Date Time Format
	'date_time_format' => 'Y-M-d H:i:s',

    // Date Format
	'date_format' => 'Y-M-d',

    // Time Format
	'time_format' => 'H:i:s',

    // Filter Default Format
	'filter_default_format' => '1',

    // Default controller
	'front_page' => 'welcome',

    // Default headers
	'headers' => array(
		'X-Powered-By'              => 'Gleez CMS (https://gleezcms.org)',
	//	'Content-Security-Policy'   => "script-src 'self' '{NONCE}' metrics.gleez.com; frame-ancestors 'self';"
	),

    // XML-RPC
    'xml_rpc' => 'xml-rpc',

    // Number of minutes, which indicates how long the channel can be cached without updating
	'feed_ttl' => 60,

    // Use Gravatar service?
	'use_gravatars' => FALSE,

    // Meta defaults
	'meta' => array(
		'links' => array(
			URL::site('media/icons/favicon.ico', TRUE) => array(
				'rel'  => 'shortcut icon',
				'type' => 'image/x-icon'
			),
			URL::site('rss', TRUE) => array(
				'rel'   => 'alternate',
				'type'  => 'application/rss+xml',
				'title' => 'Gleez RSS 2.0'
			),
			URL::site('', TRUE) => array(
				'rel'   => 'index',
				'title' => 'Gleez CMS'
			),
		),
		'tags' => array(
			'charset'    		      => Kohana::$charset,
			'generator'  		      => 'Gleez '.Gleez::VERSION.' (https://gleezcms.org)',
			'author'     		      => 'Gleez Team',
			'copyright'  		      => 'Copyright (c) Gleez Technologies (P) Limited 2011-2018. All rights reserved.',
			'robots'     		      => 'index, follow, noodp',
			'viewport'   		      => 'width=device-width, initial-scale=1.0',
		),
	),

	/**
	 * Default locale.
	 * Default to 'en_US'
	 */
	'locale' =>  'en',

	/**
	 * Allow locale override.
	 * Change the default locale, accepted values: FALSE|ALL|USER|CLIENT|URL|DOMAIN
	 */
	'locale_override' =>  FALSE,

	/**
	 * Locale cookie key.
	 * Default to 'lang'
	 */
	'locale_cookie' => 'lang',

	/**
	 * List of all supported languages. Array keys match language segment from the URI.
	 * A default fallback language can be set by I18n::$default.
	 *
	 * Options for each language:
	 *  i18n_code - The target language for the I18n class
	 *  locale    - Locale name(s) for setting all locale information (http://php.net/setlocale)
	 */
	'installed_locales' => array(
		'en' => array(
			'name'      => 'English',
			'i18n_code' => 'en-us',
			'locale'    => array('en_US.utf-8'),
		),
		'et' => array(
			'name'      => 'Estonian',
			'i18n_code' => 'et-ee',
			'locale'    => array('et_EE.utf-8'),
		),
		'it' => array(
			'name'      => 'Italian',
			'i18n_code' => 'it-it',
			'locale'    => array('it_IT.utf-8'),
		),
		'ro' => array(
			'name'      => 'Romanian',
			'i18n_code' => 'ro-ro',
			'locale'    => array('ro_RO.utf-8'),
		),
		'ru' => array(
			'name'      => 'Russian',
			'i18n_code' => 'ru-ru',
			'locale'    => array('ru_RU.utf-8'),
		),
		'zh' => array(
			'name'      => 'Chinese (Simplified)',
			'i18n_code' => 'zh-cn',
			'locale'    => array('zh_CN.utf-8'),
		),
		'id' => array(
			'name'      => 'Bahasa Indonesia',
			'i18n_code' => 'id-id',
			'locale'    => array('id_ID.utf-8'),
		),
	),

    // Default timezone
	'timezone' => 'Asia/Kolkata',

    // Allow timezone override.
	'timezone_override' => FALSE,

	/**
	 * Blocked ips.
     * Comma separated ip-addresses to block
	 */
    'blocked_ips' => '',

    // Default date first day
	'date_first_day' => 1,

	/**
	 * Site Private Key
	 * Default to null, generate a random key on installation
	 */
	'gleez_private_key' => NULL,

    // Number of seconds before password reset confirmation links expire
	'reset_password_expiration' => 86400,

    // Default session type
    'session_type' => 'database',

    // Define Google Analytics ID
	'google_ua' => NULL,
);
