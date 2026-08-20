<?php

/**
 * Manager for rendering meta tags (<link> and <meta>)
 *
 * @package    Gleez\Helpers
 * @author     Gleez Team
 * @version    1.0.1
 * @copyright  (c) 2011-2015 Gleez Technologies
 */
class Meta
{
	/**
	 * An array of meta links
	 * @var array
	 */
    public static $links = [];

	/**
	 * An array of meta tags
	 * @var array
	 */
    public static $tags = [];

    /**
     * Meta Link wrapper
     *
     * Gets or sets Meta Links
     *
     * @param string|null $handle The link URL
     * @param array $attrs An associative array of link settings
     * @return array|string Setting returns asset array, getting returns asset HTML
     * @throws Kohana_Exception
     * @uses    URL::site
     * @uses    URL::is_absolute
     */
    public static function links(string $handle = null, array $attrs = [])
	{
		// Return all meta links
        if (is_null($handle)) {
			return self::all_links();
		}

        $attrs['href'] = URL::is_absolute($handle) ? $handle : URL::site($handle, true);

		// Make sure have only one 'canonical' link per request
        if (isset($attrs['rel']) && $attrs['rel'] == 'canonical') {
			$handle = 'canonical';
		}

        return self::$links[$handle] = ['url' => $attrs['href'], 'attrs' => $attrs];
	}

	/**
	 * Get a single Meta Link
	 *
     * @param string $handle Asset name
     * @return string|null Asset HTML or null when not found
	 * @uses    Arr::get
	 * @uses    HTML::attributes
	 */
    public static function get_link(string $handle): ?string
    {
        if (!isset(self::$links[$handle])) {
            return null;
		}

		$asset       = self::$links[$handle];

        return '<link' . HTML::attributes($asset['attrs']) . '>';
	}

	/**
	 * Get all Meta Links
	 *
	 * @return  string   Asset HTML
	 */
    public static function all_links(): string
    {
        if (empty(self::$links)) {
            return '';
		}

        $assets = [];

        foreach (self::_sort(self::$links) as $handle => $data) {
			$assets[] = self::get_link($handle);
		}

        $assets = array_filter($assets);

        return empty($assets) ? '' : implode(PHP_EOL, $assets) . PHP_EOL;
	}

	/**
	 * Meta Tag wrapper
	 *
	 * Gets or sets Meta Tags
	 *
     * @param string|null $handle The meta tag name
     * @param string|null $value The meta tag value
     * @return array|string Setting returns asset array, getting returns asset HTML
	 */
    public static function tags(string $handle = null, string $value = null)
	{
        // Return all meta tags
        if (is_null($handle)) {
			return self::all_tags();
		}

        $attrs = $handle === 'charset' ? [] : ['name' => $handle, 'content' => $value];

        return self::$tags[$handle] = ['handle' => $handle, 'value' => $value, 'attrs' => $attrs];
	}

	/**
	 * Get a single Meta tag
	 *
     * @param string $handle Asset name
     * @return string|null Asset HTML or null when not found
	 * @uses    HTML::attributes
	 */
    public static function get_tag(string $handle): ?string
    {
        if (!isset(self::$tags[$handle])) {
            return null;
		}

		$asset       = self::$tags[$handle];

        if ($asset['handle'] == 'charset') {
			return '<meta charset="'.$asset['value'].'">';
		}

        return '<meta' . HTML::attributes($asset['attrs']) . '>';
	}

	/**
	 * Get all Meta Tags
	 *
	 * @return  string   Asset HTML
	 */
    public static function all_tags(): string
    {
        if (empty(self::$tags)) {
            return '';
		}

        $assets = [];

        foreach (self::_sort(self::$tags) as $handle => $data) {
			$assets[] = self::get_tag($handle);
		}

        $assets = array_filter($assets);

        return empty($assets) ? '' : implode(PHP_EOL, $assets) . PHP_EOL;
	}

	/**
	 * Sorts assets based on dependencies
	 *
     * @param array $assets Array of assets
	 * @return  array  Sorted array of assets
	 */
    protected static function _sort(array $assets): array
    {
        return System::sortDependencies($assets);
	}

	/**
	 * Enforce static usage
	 */
	private function __construct() {}
	private function __clone() {}

}