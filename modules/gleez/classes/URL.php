<?php
/**
 * URL Class Helper
 *
 * @package    Gleez\Helpers
 * @author     Gleez Team
 * @version    1.1.1
 * @copyright  (c) 2011-2015 Gleez Technologies
 * @license    http://gleezcms.org/license  Gleez CMS License
 */
class URL extends Kohana_URL
{
	/**
	 * Get the canonical URL
	 *
	 * @param   mixed   $url         The request object or string URL
	 * @param   object  $pagination  The pagination object [Optional]
	 * @param   array   $qstring     The query string parameters [Optional]
	 * @param   mixed   $protocol    The route protocol [Optional]
	 * @return  string
	 *
	 * @uses    Request::uri
	 */
	public static function canonical($url, $pagination = NULL, $qstring = NULL, $protocol = TRUE)
	{
		if ($url instanceof Request)
		{
			return self::site($url->uri(), $protocol);
		}

		if ($pagination AND $pagination->current_page > 1)
		{
			$url .= '/p' . $pagination->current_page;
		}

		return self::site($url, $protocol).self::query($qstring);
	}

	/**
	 * Test whether a URL is absolute
	 *
	 * @param   string  $url  The URL to test
	 * @return  boolean
	 */
	public static function is_absolute($url)
	{
		return (strpos($url, '://') === FALSE);
	}

	/**
	 * Test whether a URL is remote
	 *
	 * @since   1.0.0  Initial functional
	 * @since   1.0.1  Better handling
	 *
	 * @param   string  $url  The URL to test
	 * @return  boolean
	 */
	public static function is_remote($url)
	{
		if((strpos($url, '://') !== FALSE))
		{
			$base = URL::base(TRUE);

			$host1 = str_replace('www.', '', parse_url($base, PHP_URL_HOST));
			$host2 = str_replace('www.', '', parse_url($url, PHP_URL_HOST));

			return trim($host1) === trim($host2);
		}

		return FALSE;
	}

	/**
	 * Splits url into array of it's pieces as follows:
	 * [scheme]://[user]:[pass]@[host]/[path]?[query]#[fragment]
	 * In addition it adds 'query_params' key which contains array of
	 * url-decoded key-value pairs
	 *
	 * @param   string  $url An URL
	 * @return  array
	 */
	public static function explode($url)
	{
		$url = parse_url($url);
		$url['query_params'] = array();

		// On seriously malformed URLs, parse_url() may return FALSE.
		if (isset($url['query']))
		{
			$pairs = explode('&', $url['query']);
			foreach($pairs as $pair)
			{
				if (trim($pair) == '')
				{
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
	 * @param   mixed    $protocol
	 * @param   boolean  $index
	 * @param   boolean  $with_query_params
	 *
	 * @return  string
	 */
	public static function current($protocol = NULL, $index = FALSE, $with_query_params = TRUE)
	{
		static $uri;
		$query = null;
		if (!$with_query_params)
		{
			$query = self::query();
		}

		if (empty($uri))
		{
			$uri = self::site(Request::current()->uri());
		}

		return self::base($protocol, $index) . str_replace($query, '', ltrim($uri, '/'));
	}

	/**
	 * Determine if current url is active
	 *
	 * @param   string  $url
	 * @return  boolean
	 */
	public static function is_active($url)
	{
		if (preg_match('#^[A-Z][A-Z0-9+.\-]+://#i', $url))
		{
			// Don't check URIs with a scheme ... not really a URI is it?
			return FALSE;
		}

		$current = explode('/', trim(str_replace(self::base(), '', self::current()), '/'));
		ksort($current);
		$url = explode('/', trim(str_replace(self::base(), '', $url), '/'));
		ksort($url);

		if (0 == count(array_diff($url, $current)))
		{
			return TRUE;
		}

		$result = FALSE;

		if (count($url) < count($current))
		{
			for ($i = 0; $i == count($url); $i++)
			{
				$result = $url[$i] == $current[$i] OR $result;
			}
		}

		return $result;
	}
}
