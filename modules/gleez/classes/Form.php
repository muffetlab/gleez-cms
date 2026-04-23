<?php
/**
 * Form helper class
 *
 * @package    Gleez\Helpers
 * @author     Gleez Team
 * @version    1.2.1
 * @copyright  (c) 2011-2015 Gleez Technologies
 * @license    https://gleezcms.org/license  Gleez CMS License
 */
class Form extends Kohana_Form
{
    /**
     * Generates an opening HTML form tag.
     *
     * Examples:
     * ~~~
     * // Form will submit back to the current page using POST
     * echo Form::open();
     *
     * // Form will submit to 'search' using GET
     * echo Form::open('search', array('method' => 'get'));
     *
     * // When "file" inputs are present, you must include the "enctype"
     * echo Form::open(NULL, array('enctype' => 'multipart/form-data'));
     * ~~~
     *
     * @param mixed $action Form action, defaults to the current request URI, or Request class to use
     * @param array|null $attributes HTML attributes
     * @return string
     * @throws Kohana_Exception
     * @uses    Request::uri
     * @uses    Request::current
     * @uses    Request::query
     * @uses    URL::site
     * @uses    URL::is_remote
     * @uses    URL::explode
     * @uses    HTML::attributes
     * @uses    Assets::css
     * @uses    CSRF::key
     * @uses    CSRF::token
     */
    public static function open($action = null, array $attributes = null): string
    {
		// Dynamically sets destination url to from action if exists in url
        if (PHP_SAPI !== 'cli' and $desti = Request::current()->query('destination') and !empty($desti)) {
			// Properly parse the path and query
			$url = URL::explode($action);

			//On seriously malformed URLs, parse_url() may return FALSE.
			if (isset($url['path']) AND is_array($url['query_params']))
			{
				//add destination param
				$url['query_params']['destination'] = $desti;

				//set the form action parameter
                $attributes['action'] = $url['path'] . URL::query($url['query_params']);
			}
		}

        $out = parent::open($action, $attributes) . PHP_EOL;

		if (Gleez::$installed)
		{
			// Assign the global form css file
			Assets::css('form', 'media/css/form.css', array('weight' => 2));

			$action  = md5($action . CSRF::key());
			$out 	.= self::hidden('_token', CSRF::token(FALSE, $action)).PHP_EOL;
			$out 	.= self::hidden('_action', $action).PHP_EOL;
		}

		return $out;
	}

    /**
     * Creates a form input.
     *
     * If no type is specified, a "text" type input will be returned.
     *
     * Example:
     * ~~~
     * echo Form::input('username', $username);
     * ~~~
     *
     * @param string $name Input name
     * @param string|null $value Input value
     * @param array|null $attributes HTML attributes
     * @param string $url Input url (autocomplete url)
     * @return string
     * @throws Kohana_Exception
     * @uses    HTML::attributes
     * @uses    Assets::js
     * @uses    URL::site
     */
    public static function input(string $name, string $value = null, array $attributes = null, string $url = ''): string
    {
        if (!isset($attributes['type'])) {
            // Default type is text
            $attributes['type'] = 'text';
        }

        if (!isset($attributes['id']) && $attributes['type'] != 'hidden')
		{
            $attributes['id'] = self::_get_id_by_name($name);
		}

        if ($attributes['type'] === 'text' and !empty($url))
		{
            $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' form-autocomplete' : 'form-autocomplete';
            $attributes['id'] = $name;
            $attributes['autocomplete'] = "off";
            $attributes['data-url'] = URL::site($url, TRUE);
            $attributes['data-provide'] = 'typeahead';

			// Assign the typeahead js file
			Assets::js('greet.typeahead', 'media/js/greet.typeahead.js', 'gleez');
		}

        return parent::input($name, $value, $attributes);
	}

    /**
     * Creates a textarea form input.
     *
     * Example:
     * ~~~
     * echo Form::textarea('about', $about);
     * ~~~
     *
     * @param string $name Textarea name
     * @param string $body Textarea body
     * @param array|null $attributes HTML attributes
     * @param bool $double_encode Encode existing HTML characters
     * @return  string
     * @uses    HTML::attributes
     * @uses    HTML::chars
     */
    public static function textarea(string $name, string $body = '', array $attributes = null, bool $double_encode = true): string
    {
		if ( ! isset($attributes['id']))
		{
			$attributes['id'] = self::_get_id_by_name($name);
		}

        return parent::textarea($name, $body, $attributes, $double_encode);
	}

    /**
     * Creates a select form input.
     *
     * Example:
     * ~~~
     * echo Form::select('country', $countries, $country);
     * ~~~
     *
     * @param string $name Input name
     * @param array|null $options Available options
     * @param mixed $selected Selected option string, or an array of selected options
     * @param array|null $attributes HTML attributes
     * @return  string
     * @uses    HTML::attributes
     */
    public static function select(string $name, array $options = null, $selected = null, array $attributes = null): string
    {
		if (! isset($attributes['id']))
		{
			$attributes['id'] = self::_get_id_by_name($name);
		}

        if ($attributes['useSelect2'] ?? false) {
            unset($attributes['useSelect2']);
            $attributes['data-select2-provider'] = $name;
            Assets::select2($name);
        }

        return parent::select($name, $options, $selected, $attributes);
	}

    /**
     * Creates a button.
     *
     * Example:
     * ~~~
     * echo Form::button('login', 'Login', array('class' => 'pull-right'));
     * ~~~
     *
     * @param string $name Button name
     * @param string $body Button caption
     * @param array|null $attributes HTML attributes
     * @return  string
     * @uses    HTML::attributes
     */
    public static function button(string $name, string $body, array $attributes = null): string
    {
        if (!isset($attributes['id']))
		{
            $attributes['id'] = self::_get_id_by_name($name);
		}

        return parent::button($name, $body, $attributes);
	}

	/**
	 * Creates weight select field
	 *
	 * @param   string   $name      Input name
	 * @param   integer  $selected  Selected option int [Optional]
	 * @param   array    $attrs     HTML attributes [Optional]
	 * @param   integer  $delta     Delta [Optional]
	 *
	 * @return  string
	 *
	 * @uses    Form::select
	 */
	public static function weight($name, $selected = 0, array $attrs = NULL, $delta = 15)
	{
		$options = array();

		for ($n = (-1 * $delta); $n <= $delta; $n++)
		{
			$options[$n] = $n;
		}

		return self::select($name, $options, $selected, $attrs);
	}

	/**
	 * Create a form field for filtering
	 *
	 * @param   string $column  Column
	 * @param   array  $vals    Filter values
	 * @param   array  $attrs   Filter attributes [Optional]
	 *
	 * @return  string
	 *
	 * @uses    Arr::get
	 */
	public static function filter($column, array $vals, array $attrs = array())
	{
		if ( ! isset($attrs['style']))
		{
			// Default type is text
			$attrs['style'] = 'width: 100%';
		}

		return self::input("filter[$column]", Arr::get($vals, $column), $attrs);
	}

	/**
	 * Creates a form input for date.
	 *
	 *     echo Form::date('author_date', $created);
	 *
	 * @param   string  $name       input name
	 * @param   string  $value      input value
	 * @param   array   $attributes html attributes
	 * @return  string
	 * @uses    Form::input
	 * @link    https://getdatepicker.com/4/
	 */
	public static function date($name, $value = NULL, array $attrs = NULL)
	{
		$out = '';

		// Assign the datepicker assets
        Assets::css('bs.dt', 'media/css/bootstrap-datetimepicker.min.css', ['bootstrap']);
        Assets::js('bs.mm', 'media/js/moment/moment.min.js', ['bootstrap']);
        Assets::js('bs.dt', 'media/js/bootstrap-datetimepicker.min.js', ['bootstrap']);

		if ( ! isset($attrs['id']))
		{
			$attrs['id'] = Form::_get_id_by_name($name);
		}  

		// Set the input name
		$attrs['name']  = $name;
		$attrs['type']  = 'text'; 
		$attrs[]        = 'readonly';

        $options = [
            'format' => 'DD-MM-YYYY hh:mm:ss',
            'locale' => 'en',
            'viewMode' => 'days',
            'showTodayButton' => false,
            'widgetPositioning' => ['horizontal' => 'left', 'vertical' => 'bottom'],
            'ignoreReadonly' => true,
        ];

		// Add locale support to datepicker. @todo CH and latin support
        if (Gleez_I18n::$lang !== 'en-us')
		{
			$lang                                   = I18n::$lang;
            $options['locale'] = $lang;
            Assets::js('bs.mm.locale', "media/js/moment/locale/$lang.js", ['bs.mm']);
		}

        if (isset($attrs['format']))
		{
            $options['format'] = $attrs['format'];
            unset($attrs['format']);
		}

        if (isset($attrs['showTodayButton']))
		{
            $options['showTodayButton'] = $attrs['showTodayButton'];
            unset($attrs['showTodayButton']);
		}

        if (isset($attrs['viewMode']))
		{
            $options['viewMode'] = $attrs['viewMode'];
            unset($attrs['viewMode']);
		}

		// Set the input value
		if ($value == false)
		{
            $attrs['value'] = Date::formatted_time(time(), 'd-m-Y h:i:s');
		}
		elseif ($value != false && is_numeric($value))
		{
            $attrs['value'] = Date::formatted_time($value, 'd-m-Y h:i:s');
		}

        Assets::codes('bs.dt.' . $name, 'jQuery(document).ready(function ($) {
            $(\'[data-dtp-provider="' . $name . '"]\').datetimepicker(' . json_encode($options) . ');
        });', null, false, ['weight' => 1]);

        $out .= '<div' . HTML::attributes(['data-dtp-provider' => $name, 'class' => 'input-group date']) . '>';
		$out .= '<input'.HTML::attributes($attrs).'>';
		$out .= '<span class="input-group-addon"><i class="fa fa-calendar"></i></span>';
		$out .= '</div>';

		return $out;
	}

	/**
	 * Generates a valid HTML ID based the name.
	 *
	 * @param  string  $name   Element name
	 *
	 * @return string
	 */
	protected static function _get_id_by_name($name)
	{
		return 'form-'.str_replace(array('[]', '][', '[', ']', '\\'), array('', '_', '_', '', '_'), $name);
	}
}
