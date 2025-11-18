<?php
/**
 * Request and response wrapper
 *
 * Uses the [Route] class to determine what [Controller]
 * to send the request to.
 *
 * @package    Gleez\Request
 * @version    1.2.0
 * @author     Gleez Team
 * @copyright  (c) 2011-2014 Gleez Technologies
 * @license    http://gleezcms.org/license Gleez CMS License
 */
class Request extends Kohana_Request
{
	/**
	 * Default maximum size of POST data
	 * @type string
	 */
	const DEFAULT_POST_MAX_SIZE = '1M';

	/**
	 * Request Redirect URL for ajax requests
	 * @var string
	 */
	public static $redirect_url;

	/**
	 * Response
	 * @var Response
	 */
	protected $_response;

	/**
	 * Returns the accepted content types
	 *
	 * If a specific type is defined, the quality of that type will be returned.
	 *
	 * Example:
	 * ~~~
	 * $types = Request::accept_type();
	 * ~~~
	 *
	 * @param   string  $type Content MIME type
	 * @return  mixed   An array of all types or a specific type as a string
	 * @uses    Request::_parse_accept
	 */
	public static function accept_type($type = NULL)
	{
		static $accepts;

		if ($accepts === NULL)
		{
			// Parse the HTTP_ACCEPT header
			$accepts = Request::_parse_accept($_SERVER['HTTP_ACCEPT'], array('*/*' => 1.0));
		}

		if (isset($type))
		{
			// Return the quality setting for this type
			return isset($accepts[$type]) ? $accepts[$type] : $accepts['*/*'];
		}

		return $accepts;
	}

	/**
	 * Checks whether the request called by mobile device by useragent string
	 * Preg is faster than for loop
	 *
	 * @return boolean
	 *
	 * @todo use Request::$user_agent but it is null
	 */
	public static function is_mobile()
	{
		$devices = 'android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos';

		if (isset($_SERVER['HTTP_USER_AGENT']))
		{
			return (preg_match("/$devices/i", $_SERVER['HTTP_USER_AGENT']) > 0);
		}

		return FALSE;
	}

	/**
	 * Whether or not current request is DataTables
	 *
	 * @param   mixed  Request  Request [Optional]
	 * @return  boolean
	 * @uses    Request::current
	 */
	public static function is_datatables(Request $request = NULL)
	{
		$request = ($request) ? $request : Request::current();

		return (bool) $request->query('draw');
	}

	/**
	* Check if we are running under HHVM
	*
	* @return Bool
	*/
	public static function isHHVM() {
		return defined('HHVM_VERSION');	
	}

	/**
	 * Gets POST max size in bytes
	 *
	 * @link    http://php.net/post-max-size
	 *
	 * @return  float
	 *
	 * @uses    Config::get
	 * @uses    Config::set
	 * @uses    Num::bytes
	 * @uses    Request::DEFAULT_POST_MAX_SIZE
	 */
	public static function get_post_max_size()
	{
		$max_size = Config::get('media.post_max_size', NULL);

		// Set post_max_size default value if it not exists
		if (is_null($max_size))
		{
			Config::set('media', 'post_max_size', $max_size = static::DEFAULT_POST_MAX_SIZE);
		}

		if(static::isHHVM())
		{
			//$php_settings = ini_get('post_max_size');
			$php_settings = ini_get('hhvm.server.max_post_size');
		}
		else 
		{
			// Get the post_max_size in bytes from php.ini
			$php_settings = Num::bytes(ini_get('post_max_size'));
		}

		// Get the post_max_size in bytes from `config/media`
		$gleez_settings = Num::bytes($max_size);

		return min($gleez_settings, $php_settings);
	}

	/**
	 * Redirects as the request response. If the URL does not include a
	 * protocol, it will be converted into a complete URL.
	 *
	 * Example:
	 * ~~~
	 * $request->redirect($url);
	 * ~~~
	 *
	 * [!!] No further processing can be done after this method is called!
	 *
	 * @param   string   $url   Redirect location
	 * @param   integer  $code  Status code: 301, 302, etc
	 * @return  void
	 * @uses    URL::site
	 * @uses    Request::send_headers
	 */
	public function redirect($url = '', $code = 302)
	{
		$referrer = $this->uri();

		if (strpos($referrer, '://') === FALSE)
		{
			$referrer = URL::site($referrer, TRUE, Kohana::$index_file);
		}

		if (strpos($url, '://') === FALSE)
		{
			// Make the URI into a URL
			$url = URL::site($url, TRUE, Kohana::$index_file);
		}

		// Check whether the current request is ajax request
		if ($this->is_ajax())
		{
			self::$redirect_url = $url;
			// Stop execution
			return;
		}

		if (($response = $this->response()) === NULL)
		{
			$response = $this->create_response();
		}

		echo $response->status($code)
			->headers('Location', $url)
			->headers('Referer', $referrer)
			->send_headers()
			->body();

		// Stop execution
		exit;
	}

    /**
     * Processes the request, executing the controller action that handles this
     * request, determined by the [Route].
     *
     * 1. Before the controller action is called, the [Controller::before] method
     * will be called.
     * 2. Next the controller action will be called.
     * 3. After the controller action is called, the [Controller::after] method
     * will be called.
     *
     * By default, the output from the controller is captured and returned, and
     * no headers are sent.
     *
     * Example:
     * ~~~
     * $request->execute();
     * ~~~
     *
     * @return Response
     * @throws HTTP_Exception_403
     * @throws HTTP_Exception_503
     * @throws Kohana_Exception
     * @throws Request_Exception
     * @uses    [Kohana::$profiling]
     * @uses    [Profiler]
     * @uses    Gleez::block_ips
     * @uses    Gleez::maintenance_mode
     */
    public function execute(): Response
    {
		if (Gleez::$installed)
		{
			// Deny access to blocked IP addresses
			Gleez::block_ips();

			// Check Maintenance Mode
			Gleez::maintenance_mode();
		}

		return parent::execute();
	}

	/**
	 * Set or get the response for this request
	 *
	 * @param   Response  $response  Response to apply to this request
	 * @return  Response
	 * @return  void
	 */
	public function response(Response $response = NULL)
	{
		if ($response === NULL)
		{
			// Act as a getter
			return $this->_response;
		}

		// Act as a setter
		$this->_response = $response;

		return $this;
	}

	/**
	 * Creates a response based on the type of request, i.e. an
	 * Request_HTTP will produce a Response_HTTP, and the same applies
	 * to CLI.
	 *
	 * Example:
	 * ~~~
	 * // Create a response to the request
	 * $response = $request->create_response();
	 * ~~~
	 *
	 * @param   boolean  $bind  Bind to this request
	 * @return  Response
	 * @since   3.1.0
	 */
	public function create_response($bind = TRUE)
	{
		$response = new Response(array('_protocol' => $this->protocol()));

		if ($bind)
		{
			// Bind a new response to the request
			$this->_response = $response;
		}

		return $response;
	}

	/**
	 * Check to see if the current request is a POST request
	 *
	 * Example:
	 * ~~~
	 * $this->request->is_post();
	 * ~~~
	 *
	 * @return  boolean  Whether the request is a POST request or not
	 */
	public function is_post()
	{
		return (self::POST === $this->_method);
	}
}