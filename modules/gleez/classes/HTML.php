<?php
/**
 * HTML Helper
 *
 * Provides generic methods for generating various HTML
 * tags and making output HTML safe.
 *
 * @package    Gleez\Helpers
 * @author     Gleez Team
 * @version    1.1.4
 * @copyright  (c) 2011-2015 Gleez Technologies
 * @license    https://gleezcms.org/license  Gleez CMS License
 */
class HTML extends Kohana_HTML
{
    /**
     * Creates a script link.
     *
     * Example:
     * ~~~
     * echo HTML::script('media/js/jquery.min.js');
     * ~~~
     *
     * @param string $file File name
     * @param array|null $attributes Default attributes
     * @param mixed $protocol Protocol to pass to URL::base()
     * @param bool $index Include the index page
     * @return  string
     * @throws Kohana_Exception
     * @uses    URL::site
     */
    public static function script(string $file, array $attributes = null, $protocol = null, bool $index = false): string
    {
		// Allow theme to serve its own media assets
        if (strpos($file, 'media/js') !== FALSE and Gleez::$installed and strpos($file, 'guide-media') === FALSE)
		{
			$theme = Theme::$active;
			$file = str_replace(array('media/js'), "media/{$theme}/js", $file);
		}

		return parent::script($file, $attributes, $protocol, $index);
	}

    /**
     * Creates a style sheet link element.
     *
     * Example:
     * ~~~
     * echo HTML::style('media/css/screen.css');
     * ~~~
     *
     * @param string $file File name
     * @param array|null $attributes Default attributes
     * @param mixed $protocol Protocol to pass to `URL::base()`
     * @param bool $index Include the index page
     * @return  string
     * @throws Kohana_Exception
     * @uses    URL::site
     */
    public static function style(string $file, array $attributes = null, $protocol = null, bool $index = false): string
    {
		// Allow theme to serve its own media assets
        if (strpos($file, 'media/css') !== FALSE and Gleez::$installed and strpos($file, 'guide-media') === FALSE)
		{
			$theme = Theme::$active;
			$file = str_replace(array('media/css'), "media/{$theme}/css", $file);
		}

		return parent::style($file, $attributes, $protocol, $index);
	}

	/**
	 * Creates a resized image link to resize images on fly with caching
	 *
	 * Width, height and type attributes are required to resize the image.
	 *
	 * Example:
	 * ~~~
	 * echo HTML::resize('media/img/logo.png', array('alt' => 'My Company', 'width' => 50, 'height' => 50, 'type' => 'ratio'));
	 * ~~~
	 *
	 * @param   string   $file        File name
	 * @param   array    $attributes  Default attributes + type = crop|ratio [Optional]
	 * @param   mixed    $protocol    Protocol to pass to `URL::base()` [Optional]
	 * @param   boolean  $index       Include the index page [Optional]
	 *
	 * @return  string
	 *
	 * @uses    URL::base
	 */
	public static function resize($file, array $attributes = NULL, $protocol = NULL, $index = FALSE)
	{
		if (strlen($file) <= 1)
		{
			return '';
		}

		if (isset($attributes['width']))
		{
			$width = $attributes['width'];
		}

		if (isset($attributes['height']))
		{
			$height = $attributes['height'];
		}

		if (isset($attributes['type']))
		{
			$type = $attributes['type'];
			unset($attributes['type']);
		}
		else
		{
			$type = 'crop';
		}

		if (strpos($file, '://') === FALSE)
		{
			if (isset($width) AND isset($height))
			{
				$file = (strpos($file, 'media/') === FALSE) ? $file : str_replace('media/', '', $file);
				$file = "media/imagecache/$type/{$width}x{$height}/$file";
			}

			// Auto detect index file
			$index = ($index == FALSE AND ! empty(Kohana::$index_file)) ? TRUE : $index;

			// Add the base URL
			$file = URL::base($protocol, $index).$file;
		}

		// Add the image link
		$attributes['src'] = $file;

		return '<img'.self::attributes($attributes).'>';
	}

	/**
	 * Print out a themed set of links
	 *
	 * @param   array  $links       Links
	 * @param   array  $attributes  Attributes, for example CSS class [Optional]
	 *
	 * @return  string
	 */
	public static function links($links, $attributes = array('class' => 'links'))
	{
		$output = '';

		if (count($links) > 0)
		{
			$output = '<ul'. self::attributes($attributes) .'>';

			$num_links = count($links);
			$i = 1;

			foreach ($links as $item)
			{
				$class = 'link-' . $i;

				// Add first, last and active classes to the list of links to help out themers.
				if ($i == 1)
				{
					$class .= ' first';
				}

				// Check if the menu item URI is or contains the current URI
				if(is_object($item) AND self::is_active($item->link))
				{
					$class .= ' active';
				}
				elseif(is_array($item) AND self::is_active($item['link']))
				{
					$class .= ' active';
				}

				if ($i == $num_links)
				{
					$class .= ' last';
				}
				$output .= '<li'.self::attributes(array('class' => $class)) .'>';

				if( is_object($item))
				{
					$output .= self::anchor($item->link, $item->name);
				}
				elseif( is_array($item))
				{
					$output .= self::anchor($item['link'], $item['name']);
				}

				$i++;
				$output .= "</li>".PHP_EOL;
			}
			$output .= '</ul>';
		}

		return $output;
	}

	/**
	 * Print out a themed set of tabs
	 *
	 * @param   array  $tabs        Tabs
	 * @param   array  $attributes  Attributes, for example CSS class [Optional]
	 *
	 * @return  string Prepared HTML
	 *
     * @uses    HTML::chars
	 */
	public static function tabs($tabs, $attributes = array('class' => 'tabs'))
	{
		$output = '';

		if (count($tabs) > 0)
		{
			$output = '<ul'.self::attributes($attributes).'>';

			$num_links = count($tabs);
			$i = 1;

			foreach ($tabs as $tab)
			{
				$class = 'tab-' . $i;

				if(isset($tab['active']) OR ( isset($tab['link']) AND self::is_active($tab['link'])))
				{
					$class .= ' active';
				}

				// Add first, last and active classes to the list of links to help out themers.
				if ($i == 1) {
					$class .= ' first';
				}
				if ($i == $num_links) {
					$class .= ' last';
				}

				$output .= '<li'.self::attributes(array('class' => $class)).'>';

				// Sanitized link text
                $tab['text'] = HTML::chars($tab['text']);

				if(empty($tab['link']))
				{
					$output .= '<span class="active">'.$tab['text'].'</span>';
				}
				else
				{
					$output .= self::anchor($tab['link'], $tab['text']);
				}
				$i++;
				$output .= "</li>".PHP_EOL;
			}
			$output .= '</ul>';
		}

		return $output;
	}

	/**
	 * Takes a URI and will return bool true if it matches or is contained (at
	 * the start) of the current request URI.
	 *
	 * @param   string  $uri  URI
	 *
	 * @return  boolean
	 *
	 * @uses    URL::is_active
	 */
	public static function is_active($uri)
	{
		return URL::is_active($uri);
	}

	/**
	 * JavaScript source code block
	 *
	 * @param   string  $source  Script source
	 * @param   string  $type    Script type [Optional]
	 *
	 * @return  string
	 */
	public static function script_source($source, $type = 'text/javascript')
	{
		$compiled = '';

		if (is_array($source))
		{
			foreach ($source as $script)
			{
				$compiled .= self::script_source($script);
			}
		}
		else
		{
			$compiled = implode(PHP_EOL, array('<script type="'.$type.'">', trim($source), '</script>'));
		}

		return $compiled;
	}

	/**
	 * Create a image tag for sprite images
	 *
	 * @param   mixed   $class  Image class name
	 * @param   string  $title  Image title [Optional]
	 *
	 * @return  string  An HTML-prepared image
	 *
	 * @uses    Route::uri
	 * @uses    Route::get
	 */
	public static function sprite_img($class, $title = NULL)
	{
		$attr           = array();
		$attr['width']  = 16;
		$attr['height'] = 16;
		$image_class    = '';

		if (is_array($class))
		{
			foreach ($class as $name)
			{
				$image_class .= $name;
			}
		}
		elseif (is_string($class))
		{
			$image_class = $class;
		}

		$attr['class'] = 'icon ' . $image_class;

		if ( ! is_null($title))
		{
			$attr['title'] = $title;
		}

		return self::image(Route::get('media')->uri(array('file' => 'images/spacer.gif')), $attr);
	}

	/**
	 * Create a iconic button
	 *
	 * Example:
	 * ~~~
     * echo HTML::icon('/paths/edit/1', 'far fa-edit', array('class'=>'action-edit', 'title'=> __('Edit Alias')));
	 * ~~~
	 *
	 * @link    http://fontawesome.io/
	 *
	 * @param   string  $url    URL
	 * @param   string  $icon   FontAwesome like icon  class
	 * @param   array   $attrs  Attributes, for example CSS class or title [Optional]
	 *
	 * @return  string
	 */
	public static function icon($url, $icon, array $attrs = array())
	{
		return self::anchor($url, '<i class="fa '.$icon.'"></i>', $attrs);
	}

	/**
	 * Create a bootstrap label
	 *
	 * Example:
	 * ~~~
	 * echo HTML::label(__('Publish'), 'info');
	 * ~~~
	 *
	 * @param   string  $text   Text
	 * @param   string  $label  Bootstrap label class [Optional]
	 *
	 * @return  string
	 */
	public static function label($text, $label = 'default')
	{
		switch (strtolower($label))
		{
			case 'publish':
				$status = 'success';
			break;
			case 'private':
			case 'notice':
				$status = 'info';
			break;
			case 'archive':
				$status = 'archive';
			break;
			case 'debug':
			case 'draft':
				$status = 'default';
			break;
			case 'critical':
			case 'error':
			case 'emergency':
				$status = 'danger';
			break;
			case 'alert':
				$status = 'warning';
			break;
			default:
				$status = $label;
		}

		return '<span class="label label-'.strtolower($status).'">'.$text.'</span>';
	}

	/**
	 * Generates an array for select list with `items per page` values
	 *
	 * @return array
	 */
	public static function per_page()
	{
		return array(
			5 => 5,
			10 => 10,
			15 => 15,
			20 => 20,
			25 => 25,
			30 => 30,
			35 => 35,
			40 => 40,
			45 => 45,
			50 => 50,
			70 => 70,
			100 => 100,
			150 => 150,
			250 => 250,
			300 => 300,
		);
	}
}
