<?php
/**
 * Input Filter
 *
 * Filter object to clean a string.
 *
 * [!!] Note: by design, this class does not do any permission checking.
 *
 * @package    Gleez\HTML
 * @author     Gleez Team
 * @version    1.1.2
 * @copyright  (c) 2011-2015 Gleez Technologies
 * @license    https://gleezcms.org/license  Gleez CMS License
 */
class Filter {

	/**
	 * An array of Filters
	 * @var array
	 */
    protected static $_filters = [];

	/**
	 * Indicates whether filters are cached
	 * @var boolean
	 */
	public static $cache = FALSE;

	/**
	 * Stores a named filter and returns it
	 *
	 * The "action" will always be set to "index" if it is not defined.
	 *
	 * Example:
	 * ~~~
	 * Filter::set('html', array('prepare callback' => FALSE, 'process callback' => 'Text::html' ) )
	 *          ->settings(array(
	 *                  'html_nofollow' => true,
	 *                  'allowed_html'  => '<a> <em> <strong> <cite> <blockquote>'
	 *          ));
	 * ~~~
	 *
     * @param string $name Filter name
     * @param array $callbacks Filter callbacks
     * @return  Filter
     */
    public static function set(string $name, array $callbacks = []): Filter
    {
		return Filter::$_filters[$name] = new Filter($name, $callbacks);
	}

	/**
	 * Retrieves a named filter
	 *
	 * Example:
	 * ~~~
	 * $filter = Filter::get('html');
	 * ~~~
	 *
     * @param string $name Filter name
	 * @return  Filter
	 * @throws  Kohana_Exception
	 */
    public static function get(string $name): Filter
    {
		if ( ! isset(Filter::$_filters[$name]))
		{
            throw new Kohana_Exception('The requested filter does not exist: :filter', [':filter' => $name]);
		}

		return Filter::$_filters[$name];
	}

	/**
	 * Retrieve(s) all named filters
	 *
	 * Example:
	 * ~~~
	 * $filters = Filter::all();
	 * ~~~
	 *
	 * @return  array
	 */
    public static function all(): array
    {
		return Filter::$_filters;
	}

    /**
     * Retrieve(s) all available format by name formats from config
     *
     * Example:
     * ~~~
     * $formats = Filter::formats();
     * ~~~
     *
     * @return  array
     * @throws Kohana_Exception
     * @uses    Config::load
     */
    public static function formats(): array
    {
        $config = Kohana::$config->load('input_filter');

        return array_map(function ($format) {
            return $format['name'];
        }, $config->formats);
	}

    /**
     * Setter/Getter for the filter cache
     *
     * If your filters will remain the same for a long period of time,
     * use this to reload the filters from the cache rather than redefining
     * them on every page load.
     *
     * Example:
     * ~~~
     * if ( ! Filter::cache())
     * {
     *     // Set filters here
     *     Filter::cache(TRUE);
     * }
     *
     * @param boolean $save Cache the current filters [Optional]
     * @param boolean $append Append, rather than replace, cached filters when loading [Optional]
     * @return  boolean
     * @throws Cache_Exception
     * @throws Kohana_Exception
     * @uses    Cache::get
     * @uses    Cache::set
     */
    public static function cache(bool $save = FALSE, bool $append = FALSE): bool
    {
		$cache = Cache::instance();

		if ($save)
		{
            // Cache all defined routes
            return $cache->set('Filter::cache()', Filter::$_filters);
		}
		else
		{
			if ($filters = $cache->get('Filter::cache()'))
			{
				if ($append)
				{
					// Append cached filters
					Filter::$_filters += $filters;
				}
				else
				{
					// Replace existing filters
					Filter::$_filters = $filters;
				}

				// Filters were cached
				return Filter::$cache = TRUE;
			}
			else
			{
				// Filters were not cached
				return Filter::$cache = FALSE;
			}
		}
	}

    /**
     * Method to run all enabled filters by the format id on given string
     *
     * @param object $text The text object to be filtered.
     * @return string  $text       The filtered text
     * @throws Kohana_Exception
     */
    public static function process($text): string
    {
        $config = Kohana::$config->load('input_filter');
        if (!array_key_exists($text->format, $config->get('formats')) || !isset($text->format))
		{
			//make sure a valid format id exists, if not set default format id
			$text->format = (int) $config->get('default_format', 1);
		}

		$filters = $config->formats[$text->format]['filters'];
		$filter_info = Filter::all();

		//sort filters by weight
        array_multisort(array_column($filters, 'weight'), SORT_ASC, $filters);

		// Give filters the chance to escape HTML-like data such as code or formulas.
		foreach ($filters as $name => $filter)
		{
			$prepare_callback = $filter_info[$name]->prepare_callback;
            if ($filter['status'] && !empty($prepare_callback))
			{
                $text->text = Filter::execute($prepare_callback, $text->text, $filter);
			}
		}

		// Perform filtering
		foreach ($filters as $name => $filter)
		{
			$process_callback = $filter_info[$name]->process_callback;
            if ($filter['status'] && !empty($process_callback))
			{
                $text->text = Filter::execute($process_callback, $text->text, $filter);
			}
		}

		return $text->text;
	}

	/**
	 * Execute a filter on the given text
	 *
	 * @param  mixed   $callback   The callback to be executed.
     * @param string $text The text to be filtered.
	 * @param  object  $filter     The filter object.
	 *
	 * @return string  $text       The filtered text
	 */
    public static function execute($callback, string $text, $filter): string
    {
		$args = func_get_args();
		array_shift($args);

        if (is_string($callback) && strpos($callback, '::') !== FALSE)
		{
			// Make the static callback into an array
			$callback = explode('::', $callback, 2);
		}

        if ($callback && is_callable($callback))
		{
			try
			{
                if ($callback === ['Text', 'auto_p']) {
                    return Text::auto_p($text);
                }

				return  call_user_func_array($callback, $args);
			}
			catch (Exception $e)
			{
                Kohana::$log->add(Log::ERROR, 'Filter callback response :msg for filter: :filter', [
                    ':msg' => $e->getMessage(),
                    'filter' => $filter['name']
                ]);

				return $text;
			}
		}

		return $text;
	}

	/**
	 * Filter Title
	 * @var string
	 */
	protected $_title = '';

	/**
	 * The prepare and process callbacks for filter
	 * @var array
	 */
    protected $_callbacks = ['prepare callback' => FALSE, 'process callback' => FALSE];

	/**
	 * Filter Settings
	 * @var array
	 */
    protected $_settings = [];

	/**
	 * Filter Description
	 * @var string
	 */
	protected $_description = '';

	/**
	 * Class constructor
	 *
     * @param string $title Filter title
     * @param array $callbacks Filter callbacks
	 */
    public function __construct(string $title, array $callbacks = [])
	{
		$this->_title = $title;
		$this->_callbacks = $callbacks;
	}

    /**
     * @throws Kohana_Exception
     */
    public function __get($key)
	{
		if($key == 'title')
		{
			return $this->_title;
        } elseif ($key == 'description') {
			return $this->_description;
        } elseif ($key == 'prepare_callback') {
			return $this->_callbacks['prepare callback'];
        } elseif ($key == 'process_callback') {
			return $this->_callbacks['process callback'];
        } elseif ($key == 'callbacks') {
			return $this->_callbacks;
        } elseif ($key == 'settings') {
			return $this->_settings;
		}
		else
		{
            throw new Kohana_Exception('The requested property does not exist: :key', [':key' => $key]);
		}
	}

    /**
     * Set or get callbacks for filter
     *
     * Example:
     * ~~~
     * $filter->callbacks(array(
     *     'prepare callback'  => FALSE,
     *     'process callback'  => 'Text::html'
     * ));
     * ~~~
     *
     * If no parameter is passed, this method will act as a getter.
     *
     * @param array|null $callbacks key values
     * @return array|Filter
     */
	public function callbacks(array $callbacks = NULL)
	{
		if ($callbacks === NULL)
		{
			return $this->_callbacks;
		}

		$this->_callbacks = $callbacks;

		return $this;
	}

    /**
     * Set or get settings for filter
     *
     * Example:
     * ~~~
     * $filter->settings(array(
     *     'html_nofollow' => true,
     *     'allowed_html'  => '<a> <em> <strong> <cite> <blockquote>'
     * ));
     * ~~~
     *
     * If no parameter is passed, this method will act as a getter.
     *
     * @param array|null $settings key values
     * @return array|Filter
     */
	public function settings(array $settings = NULL)
	{
		if ($settings === NULL)
		{
			return $this->_settings;
		}

		$this->_settings = $settings;

		return $this;
	}

	/**
	 * Set or get title for filter
	 *
	 * Example:
	 * ~~~
	 * $filter->title(__('Limit allowed HTML tags'));
	 * ~~~
	 *
	 * If no parameter is passed, this method will act as a getter.
	 *
     * @param string|null $title Title
     * @return Filter|string
	 */
    public function title(string $title = NULL)
	{
		if ($title === NULL)
		{
			return $this->_title;
		}

		$this->_title = $title;

		return $this;
	}

	/**
	 * Set or get description for filter
	 *
	 * Example:
	 * ~~~
	 * $filter->description(__('Allowed HTML tags'));
	 * ~~~
	 *
	 * If no parameter is passed, this method will act as a getter.
	 *
     * @param string|null $description Description
	 * @return  string|Filter
	 */
    public function description(string $description = NULL)
	{
		if ($description === NULL)
		{
			return $this->_description;
		}

		$this->_description = $description;

		return $this;
	}

}