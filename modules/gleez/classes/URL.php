<?php

/**
 * URL Class Helper
 *
 * @package    Gleez\Helpers
 * @author     Gleez Team
 * @version    1.1.1
 * @copyright  (c) 2011-2015 Gleez Technologies
 */
class URL extends Kohana_URL
{
    /**
     * Get the canonical URL
     *
     * @param mixed $url The request object or string URL
     * @param object $pagination The pagination object
     * @param array|null $query The query string parameters
     * @param mixed $protocol The route protocol
     * @return  string
     * @throws Kohana_Exception
     * @uses    Request::uri
     */
    public static function canonical($url, $pagination = null, array $query = null, $protocol = true): string
    {
        if ($url instanceof Request) {
			return self::site($url->uri(), $protocol);
		}

        if ($pagination && $pagination->current_page > 1) {
			$url .= '/p' . $pagination->current_page;
		}

        return self::site($url, $protocol) . self::query($query);
	}

	/**
	 * Test whether a URL is absolute
	 *
     * @param string $url The URL to test
     * @return bool
	 */
    public static function is_absolute(string $url): bool
    {
        return (strpos($url, '://') === false);
	}

    /**
     * Test whether a URL is remote
     *
     * @param string $url The URL to test
     * @return bool
     * @throws Kohana_Exception
     * @since   1.0.1  Better handling
     * @since   1.0.0  Initial functional
     */
    public static function is_remote(string $url): bool
    {
        if ((strpos($url, '://') !== false)) {
            $base = URL::base(true);

			$host1 = str_replace('www.', '', parse_url($base, PHP_URL_HOST));
			$host2 = str_replace('www.', '', parse_url($url, PHP_URL_HOST));

			return trim($host1) === trim($host2);
		}

        return false;
	}

	/**
     * Splits URL into an array of its pieces as follows:
	 * [scheme]://[user]:[pass]@[host]/[path]?[query]#[fragment]
	 * In addition it adds 'query_params' key which contains array of
	 * url-decoded key-value pairs
	 *
     * @param string $url An URL
	 * @return  array
	 */
    public static function explode(string $url): array
    {
		$url = parse_url($url);
        $url['query_params'] = [];

        // On seriously malformed URLs, parse_url() may return false.
        if (isset($url['query'])) {
			$pairs = explode('&', $url['query']);
            foreach ($pairs as $pair) {
                if (trim($pair) == '') {
					continue;
				}

				list($sKey, $sValue) = explode('=', $pair);

				$url['query_params'][$sKey] = urldecode($sValue);
			}
		}

		return $url;
	}

    /**
     * Determine current url
     *
     * @param mixed $protocol
     * @param bool $index
     * @param bool $with_query_params
     * @return  string
     * @throws Kohana_Exception
     */
    public static function current($protocol = null, bool $index = false, bool $with_query_params = true): string
    {
		static $uri;
		$query = null;
        if (!$with_query_params) {
			$query = self::query();
		}

        if (empty($uri)) {
			$uri = self::site(Request::current()->uri());
		}

		return self::base($protocol, $index) . str_replace($query, '', ltrim($uri, '/'));
	}

    /**
     * Determine if current URL is active.
     *
     * @param string $url
     * @return bool
     * @throws Kohana_Exception
     */
    public static function is_active(string $url): bool
    {
        if (preg_match('#^[A-Z][A-Z0-9+.\-]+://#i', $url)) {
			// Don't check URIs with a scheme ... not really a URI is it?
            return false;
		}

		$current = explode('/', trim(str_replace(self::base(), '', self::current()), '/'));
		ksort($current);
		$url = explode('/', trim(str_replace(self::base(), '', $url), '/'));
		ksort($url);

        if (0 == count(array_diff($url, $current))) {
            return true;
		}

        $result = false;

        if (count($url) < count($current)) {
            for ($i = 0; $i == count($url); $i++) {
                $result = $url[$i] == $current[$i];
			}
		}

		return $result;
	}
}
