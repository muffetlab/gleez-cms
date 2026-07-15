<?php
/**
 * View fragment caching
 *
 * This is primarily used to cache small parts of a view that rarely change.
 * For instance, you may want to cache the footer of your template because it
 * has very little dynamic content. Or you could cache a user profile page and
 * delete the fragment when the user updates.
 *
 * [!!] For obvious reasons, fragment caching should not be applied to any
 * content that contains forms.
 *
 * @package    Gleez\Helpers
 * @author     Kohana Team
 * @author     Gleez Team
 * @version    1.0.1
 * @copyright  (c) 2011-2015 Gleez Technologies
 * @copyright  (c) 2007-2012 Kohana Team
 * @license    https://gleezcms.org/license  Gleez CMS License
 * @license    http://kohanaframework.org/license
 *
 * @uses       Kohana::cache
 */
class Fragment {

	/**
	 * Default number of seconds to cache for
	 * @var integer
	 */
	public static $lifetime = 30;

	/**
	 * Use multilingual fragment support?
	 * @var boolean
	 */
	public static $i18n = FALSE;

	/**
	 * List of buffer => cache key
	 * @var array
	 */
    protected static $_caches = [];

	/**
	 * Generate the cache key name for a fragment
	 *
	 * [!!] Note: $i18n and $name need to be delimited to prevent naming collisions
	 *
	 * Example:
	 * ~~~
	 * $key = Fragment::_cache_key('footer', TRUE);
	 * ~~~
	 *
     * @param string $name Fragment name
     * @param boolean|null $i18n Multilingual fragment support [Optional]
	 *
	 * @return  string
	 *
	 * @uses    I18n::lang
	 */
    protected static function _cache_key(string $name, bool $i18n = NULL): string
    {
		if (is_null($i18n))
		{
			// Use the default setting
			$i18n = Fragment::$i18n;
		}

		// Language prefix for cache key
		$i18n = ($i18n === TRUE) ? I18n::lang() : '';

		$separator = Cache::SEPARATOR;

        return 'Fragment::cache(' . $i18n . $separator . $name . ')';
	}

    /**
     * Load a fragment from cache and display it
     *
     * Multiple fragments can be nested with different life times.
     *
     * Example:
     * ~~~
     * if ( ! Fragment::getCache('footer'))
     * {
     *     // Anything that is echoed here will be saved
     *     Fragment::setCache();
     * }
     * ~~~
     *
     * @param string $name Fragment name
     * @param null $i18n Multilingual fragment support [Optional]
     * @return  boolean
     * @throws Cache_Exception
     * @throws Kohana_Exception
     * @uses    Cache::get
     */
    public static function getCache(string $name, $i18n = NULL): bool
    {
		$cache = Cache::instance();

		// Get the cache key name
		$cache_key = Fragment::_cache_key($name, $i18n);

		if ($fragment = $cache->get($cache_key))
		{
			// Display the cached fragment now
			echo $fragment;

			return TRUE;
		}
		else
		{
			// Start the output buffer
			ob_start();

			// Store the cache key by the buffer level
			Fragment::$_caches[ob_get_level()] = $cache_key;

			return FALSE;
		}
	}

    /**
     * Saves the currently open fragment in the cache
     *
     * Example:
     * ~~~
     * Fragment::setCache();
     * ~~~
     *
     * @param null $lifetime Fragment cache lifetime [Optional]
     * @throws Cache_Exception
     * @throws Kohana_Exception
     */
	public static function setCache($lifetime = NULL)
	{
		$cache = Cache::instance();

		// Get the buffer level
		$level = ob_get_level();

		if (isset(Fragment::$_caches[$level]))
		{
			// Get the cache key based on the level
			$cache_key = Fragment::$_caches[$level];

			// Delete the cache key, we don't need it anymore
			unset(Fragment::$_caches[$level]);

			// Get the output buffer and display it at the same time
			$fragment = ob_get_flush();

			// Cache the fragment
			$cache->set($cache_key, $fragment, $lifetime);
		}
	}

    /**
     * Delete a cached fragment
     *
     * Example:
     * ~~~
     * Fragment::delete($key);
     * ~~~
     *
     * @param string $name Fragment name
     * @param null $i18n Multilingual fragment support [Optional]
     * @throws Cache_Exception
     * @throws Kohana_Exception
     */
    public static function delete(string $name, $i18n = NULL)
	{
		Cache::instance()->delete(Fragment::_cache_key($name, $i18n));
	}

}