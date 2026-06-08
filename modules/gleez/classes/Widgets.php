<?php
/**
 * Widgets Core Class
 *
 * This class for handling widget(s) in template regions (sidebar left/right etc).
 *
 * @package    Gleez\Widget
 * @author     Gleez Team
 * @version    1.1.0
 * @copyright  (c) 2011-2015 Gleez Technologies
 * @license    https://gleezcms.org/license
 */
class Widgets {

	/**
	 * Widgets instance
     * @var Widgets
	 */
	protected static $instance;

	/**
	 * Associative array of widgets
     * @var array
	 */
	protected $_widgets = array();

	/**
	 * Associative array of widget regions that will be loaded
     * @var array
	 */
	protected $_regions = array();

	/**
	 * Count of widgets inside a region
     * @var array
	 */
	protected $_widget_count = array();

	/**
	 * Status of Widgets, if it's already loaded from the database
     * @var bool
	 */
	protected $_loaded = FALSE;

	/**
	 * Region name right|left etc
	 * @var string
	 */
	protected $_region;

	/**
	 * Render style html|json etc
	 * @var string
	 */
	protected $_format;

    /**
     * Singleton pattern
     *
     * @param string $region Region. By default `right`. [Optional]
     * @param string $format Format. By default `html`. [Optional]
     * @return Widgets instance
     * @throws Cache_Exception
     * @throws Kohana_Exception
     */
    public static function instance(string $region = 'right', string $format = 'html'): Widgets
    {
		if ( ! isset(Widgets::$instance))
		{
			new Widgets($region, $format);
		}

		return Widgets::$instance;
	}

    /**
     * Constructor, globally sets region and format
     *
     * @param $region
     * @param $format
     * @throws Cache_Exception
     * @throws Kohana_Exception
     */
	public function __construct($region, $format)
	{
		// Store the region locally
		$this->_region = $region;

		// Store the format locally
		$this->_format = $format;

		// Load the widgets from database
		$this->load();

		// Store the widgets instance
		Widgets::$instance = $this;
	}

	/**
	 * Add's a new widget to the widgets
	 *
     * @param string $region Widget region
     * @param string $name Unique widget name
     * @param object $widget Widget object
     * @return  Widgets
     * @throws  Kohana_Exception
     */
    public function add(string $region, string $name, $widget): Widgets
    {
		if ( ! is_object($widget))
		{
			throw new Kohana_Exception('Not a valid widget object: :widget', array(':widget' => $name));
		}

		if ( ! isset($this->_regions[$region]))
		{
			$this->_regions[$region] = array();
		}

        $this->_regions[$region][] = $name;

		// set default widget members
		$widget->config = FALSE;
		$widget->content = FALSE;
		$widget->visible = TRUE;

		$this->_widgets[$name] = $widget;

		return $this;
	}

	/**
	 * Retrieves a named widget
	 *
	 * Example:
	 * ~~~
	 * $widget = $region->get('login');
	 * ~~~
	 *
     * @param string $name Widget name
     * @return  object|null
	 */
    public function get(string $name)
	{
		if ( ! isset($this->_widgets[$name]))
		{
            return null;
		}

		return $this->_widgets[$name];
	}

	/**
	 * Remove a widget from the widgets or region from regions
	 *
	 * Example:
	 * ~~~
	 * // Removes right sidebar
	 * $widget = $region->remove('right');
	 *
	 * // Removes login widget
	 * $widget = $region->remove(FALSE, 'login');
	 * ~~~
	 *
     * @param string|null $region Region name [Optional]
     * @param string|null $widget Widget name [Optional]
	 */
    public function remove(string $region = NULL, string $widget = NULL)
	{
		if ( ! is_null($region))
		{
			if (isset($this->_regions[$region]))
			{
				unset($this->_regions[$region]);
			}
		}

		if ( ! is_null($widget))
		{
			if (isset($this->_widgets[$widget]))
			{
				unset($this->_widgets[$widget]);
			}
		}
	}

	/**
	 * Sets or gets region
	 *
	 * Example:
	 * ~~~
	 * // Sets region to right sidebar
	 * $widget = $region->region('right');
	 * ~~~
	 *
     * @param string|null $region Region name [Optional]
	 * @return  $this|string
	 */
    public function region(string $region = NULL)
	{
		if (is_null($region))
		{
			return $this->_region;
		}

		$this->_region = $region;

		return $this;
	}

	/**
	 * Sets or gets format
	 *
	 * Example:
	 * ~~~
	 * // Sets format to html output
	 * $widget = $region->format('html');
	 * ~~~
	 *
     * @param string|null $format Format name [Optional]
	 * @return  $this|string
	 */
    public function format(string $format = NULL)
	{
		if (is_null($format))
		{
			return $this->_format;
		}

		$this->_format = $format;

		return $this;
	}

	/**
	 * Renders the HTML output of widgets
	 *
	 * @return string
	 */
	public function __toString()
	{
		try
		{
			return $this->render();
		}
		catch (Exception $e)
		{
			return $e->getMessage();
		}
	}

    /**
     * Renders the HTML output for the widgets
     *
     * @param string|null $region Theme region [Optional]
     * @param string|null $format Widget format [Optional]
     * @return  string  HTML widgets
     * @throws Kohana_Exception
     */
    public function render(string $region = NULL, string $format = NULL): string
    {
		//set region, respect $this->region();
		if ( ! is_null($region))
		{
			$this->region($region);
		}

		//set format, respect $this->format();
		if ( ! is_null($format))
		{
			$this->format($format);
		}

		if ( ! isset($this->_regions[$this->_region]) OR is_null($this->_regions[$this->_region]))
		{
            return '';
		}

		$response = array();

        foreach ($this->_regions[$this->_region] as $name)
		{
            $response[] = $this->get_widget($name, TRUE, $this->_format);
		}

		return trim(implode(PHP_EOL.PHP_EOL, $response));
	}

    /**
     * Returns the named widget
     *
     * @param string $name Name of the widget
     * @param boolean $visible Visibility permission from widget or FALSE to skip
     * @param mixed $format The format of the output ex:xhtml, html or FALSE for object
     * @return  object|string|null Widget object, HTML string, or null
     * @throws Kohana_Exception
     */
    public function get_widget(string $name, bool $visible = FALSE, $format = FALSE)
	{
		if ( ! $widget = $this->get($name))
		{
            return null;
		}

        if ($visible) {
            $widget = $this->is_visible($widget);
        } else {
            $widget->visible = TRUE;
        }

		// Enable developers to override widget
		Module::event('Widget', $widget);
		Module::event('Widget_'.ucfirst($name), $widget);

        $response = null;

		if ($widget->status AND $widget->visible)
		{
			try
			{
                $widget->content = Widget::factory($name, $widget)->render();
				$response = ($format === FALSE) ? $widget : trim($this->_html($widget, $this->_region, $this->_format));
			}
			catch (Exception $e)
			{
				Kohana::$log->add(Log::ERROR, 'Error processing widget ":name": :msg', array(':name' => $name, ':msg' => $e->getMessage()));
			}
		}

		return $response;
	}

	/**
	 * Nicely outputs contents of $this->_widgets for debugging info
	 *
	 * @return   string
	 */
    public function debug(): string
    {
		return Debug::vars($this->_widgets);
	}

    /**
     * Install the widget into database during module install
     *
     * Defaults to inactive widget
     *
     * @param array $widget A widget array unique name and title are required
     * @param string $module The name of the module for this widget
     * @throws Kohana_Exception
     * @throws ORM_Validation_Exception
     * @throws ReflectionException
     */
    public static function install(array $widget, string $module)
	{
		if (isset($widget['name']) AND isset($widget['title']))
		{
			// name must be unique
			$values['name']   = @strtolower($widget['name']);
			$values['title']  = (string) $widget['title'];
            $values['module'] = $module;
			$values['status'] = 0;
			$values['region'] = '-1';

			try
			{
                ORM::factory('Widget')->values($values, ['name', 'title', 'module', 'status', 'region'])->save();
				Kohana::$log->add(Log::DEBUG, 'Insert widget where module: :module', array(':module' => $module));
			}
			catch (Database_Exception $e)
			{
				Kohana::$log->add(Log::ERROR, 'Unable to insert widgets: :mgs', array(':msg' => $e->getMessage()));
			}
		}
	}

    /**
     * Remove the widget from database during module uninstall
     *
     * @param string $module The name of the module for this widget
     * @throws Cache_Exception
     * @throws Kohana_Exception
     */
    public static function uninstall(string $module)
	{
		try
		{
            ORM::factory('Widget')->where('module', '=', $module)->delete();
            Cache::instance()->delete_all();

			Kohana::$log->add(Log::INFO, 'Deleted widgets where module: :module', array(':module' => $module));
		}
		catch (Database_Exception $e)
		{
			Kohana::$log->add(Log::ERROR, 'Unable to delete widgets: :msg', array(':msg' => $e->getMessage()));
		}
	}

    /**
     * Load the widgets from database
     *
     * @return $this|array|string
     * @throws Cache_Exception
     * @throws Kohana_Exception
     */
	protected function load()
	{
		// if the widgets have been loaded already, just return it.
		if ($this->_loaded)
		{
			return $this->_widgets;
		}

        $cache = Cache::instance();

        if (!$widgets = $cache->get('widgets:widgets')) {
            $_widgets = ORM::factory('Widget')
				->where('status', '=', '1')
				->order_by('region', 'ASC')
				->order_by('weight', 'ASC')
				->find_all();

			$widgets = array();

			foreach($_widgets as $_widget)
			{
				/** @var $_widget ORM */
				$widgets[] = (object)$_widget->as_array();
			}

            $cache->set('widgets:widgets', $widgets, Date::DAY);
		}

		foreach ($widgets as $widget)
		{
			$this->add($widget->region, $widget->name, $widget);
		}

		$this->_loaded = TRUE;

		return $this;
	}

    /**
     * @throws Kohana_Exception
     */
    protected function is_visible($widget)
	{
		static $current_route;
		$widget->visible = TRUE;

		if (is_null($current_route))
		{
			$current_route = Request::current()->uri();
			$current_route = UTF8::strtolower($current_route);
		}

		// role based widget access
		if ( ! User::belongsto($widget->roles))
		{
			$widget->visible = FALSE;
		}

		if ($widget->pages)
		{
			$pages = UTF8::strtolower($widget->pages);
			$page_match =  Path::match_path($current_route, $pages);

			$widget->visible = !($widget->visibility XOR $page_match);
		}

		return $widget;
	}

    /**
     * @throws View_Exception
     */
    private function _html($widget, $region, $format): string
    {
		$zebra = $id = FALSE;

		// Remove empty strings if content is string instead of view object
		if (is_string($widget->content))
		{
			//@todo needs a better way
			$widget->content = trim($widget->content);
		}

		// Don't render any widget if the content is null or empty
		if (empty($widget->content))
		{
            return '';
		}

		if ($region)
		{
			// All widgets get an independent counter for each region.
			if ( ! isset($this->_widget_count[$region]))
				$this->_widget_count[$region] = 1;

			// Same with zebra striping.
			$zebra = ($this->_widget_count[$region] % 2) ? 'odd' : 'even';
			$id    = $this->_widget_count[$region]++;
		}

		$widget->name = str_replace('/', '-', $widget->name);
        $widget->menu = strpos($widget->name, 'menu-') !== false;

		return View::factory('widgets/' .$format)
			->set('content', $widget->content)
			->set('title',   $widget->title)
			->set('widget',  $widget)
			->set('zebra',   $zebra)
			->set('id', $id)
			->render();
	}
}
