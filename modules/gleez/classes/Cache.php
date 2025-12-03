<?php
/**
 * Gleez Core Cache Class
 *
 * [Gleez Cache](gleez/cache/index) provides a common interface to a variety of caching engines.
 * Tags are supported where available natively to the cache system. Cache supports multiple
 * instances of cache engines through a grouped singleton pattern.
 *
 * ### Supported cache engines
 *
 * * [APC](http://php.net/manual/en/book.apc.php)
 * * File
 * * [Memcache](http://memcached.org/)
 * * [Memcached-tags](http://code.google.com/p/memcached-tags/)
 * * [SQLite](http://www.sqlite.org/)
 * * [Wincache](http://php.net/manual/en/book.wincache.php)
 * * [MongoDB](http://www.mongodb.org/)
 *
 * ### Configuration settings
 *
 * Gleez Cache uses configuration groups to create cache instances. A configuration group can
 * use any supported driver, with successive groups using the same driver type if required.
 *
 * #### Configuration example
 *
 * Below is an example of a _memcache_ server configuration:
 * ~~~~
 * return array(
 *     'default' => array(          // Default group
 *         'driver'  => 'memcache', // Using Memcache driver
 *         'servers' => array(      // Available server definitions
 *             array(
 *                 'host'       => 'localhost',
 *                 'port'       => 11211,
 *                 'persistent' => FALSE
 *             )
 *         ),
 *         'compression' => FALSE,  // Use compression?
 *     ),
 * )
 * ~~~
 *
 * In cases where only one cache group is required, if the group is named `default` there is
 * no need to pass the group name when instantiating a cache instance.
 *
 * #### General cache group configuration settings
 *
 * Below are the settings available to all types of cache driver.
 *
 * Name           | Required | Description
 * -------------- | -------- | ---------------------------------------------------------------
 * driver         | __YES__  | (_string_) The driver type to use
 *
 * Details of the settings specific to each driver are available within the drivers documentation.
 *
 * @package    Gleez\Cache
 * @version    2.1
 * @author     Sandeep Sangamreddi - Gleez
 * @copyright  (c) 2011-2015 Gleez Technologies
 * @license    http://gleezcms.org/license Gleez CMS License
 */
abstract class Cache extends Kohana_Cache
{
	const ALL = 2;
	const SEPARATOR = ':';
	const DEFAULT_EXPIRE = 86400;
}

