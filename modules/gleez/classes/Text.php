<?php
/**
 * Text Class Helper
 *
 * Provides simple methods for working with text. Text helper for
 * formatting text for output for security Code taken from Drupal
 * filter module and text class
 *
 * @package    Gleez\Helpers
 * @author     Gleez Team
 * @version    1.3.3
 * @copyright  (c) 2011-2015 Gleez Technologies
 * @license    http://gleezcms.org/license  Gleez CMS License
 */
class Text extends Kohana_Text
{
	/**
	 * Scan input and make sure that all HTML tags are properly closed and nested.
	 *
	 * @param   string   Text string to filter html
	 *
	 * @return  mixed
	 */
	public static function htmlcorrector($text)
	{
		return static::dom_serialize(static::dom_load($text));
	}

	/**
	 * Parses an HTML snippet and returns it as a DOM object
	 *
	 * This function loads the body part of a partial HTML document and returns
	 * a full DOMDocument object that represents this document.
	 *
	 * You can use [Text::dom_serialize] to serialize this DOMDocument
	 * back to a HTML snippet.
	 *
	 * @param   string       Text string to filter html
	 *
	 * @return  DOMDocument
	 */
	public static function dom_load($text)
	{
		$dom = new DOMDocument;

		// Ignore warnings during HTML soup loading.
		@$dom->loadHTML('<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>' . $text . '</body></html>');

		return $dom;
	}

	/**
	 * Converts a DOM object back to an HTML snippet
	 *
	 * The function serializes the body part of a DOMDocument
	 * back to an HTML snippet.
	 *
	 * The resulting HTML snippet will be properly formatted
	 * to be compatible with HTML user agents.
	 *
	 * @param   DOMDocument  $dom_document  A DOMDocument object to serialize
	 *
	 * @return  string
	 */
	private static function dom_serialize(DOMDocument $dom_document)
	{
		$body_node    = $dom_document->getElementsByTagName('body')->item(0);
		$body_content = '';

		foreach ($body_node->getElementsByTagName('script') as $node)
		{
			static::escape_cdata_element($dom_document, $node);
		}

		foreach ($body_node->getElementsByTagName('style') as $node)
		{
			static::escape_cdata_element($dom_document, $node, '/*', '*/');
		}

		foreach ($body_node->childNodes as $child_node)
		{
			$body_content .= $dom_document->saveXML($child_node);
		}

		return preg_replace('|<([^> ]*)/>|i', '<$1 />', $body_content);
	}

	/**
	 * Adds comments around the <!CDATA section in a dom element
	 *
	 * This function attempts to solve the problem by creating a DocumentFragment,
	 * commenting the CDATA tag.
	 *
	 * @param  DOMDocument  $dom_document   The DOMDocument containing the $dom_element
	 * @param  DOMElement   $dom_element    The element potentially containing a CDATA node
	 * @param  string       $comment_start  String to use as a comment start marker to escape the CDATA declaration [Optional]
	 * @param  string       $comment_end    String to use as a comment end marker to escape the CDATA declaration [Optional]
	*/
	private static function escape_cdata_element(DOMDocument $dom_document, DOMElement $dom_element, $comment_start = '//', $comment_end = '')
	{
		foreach ($dom_element->childNodes as $node)
		{
			if (get_class($node) == 'DOMCdataSection')
			{
				$embed_prefix = PHP_EOL."<!--{$comment_start}--><![CDATA[{$comment_start} ><!--{$comment_end}".PHP_EOL;
				$embed_suffix = PHP_EOL."{$comment_start}--><!]]>{$comment_end}".PHP_EOL;

				// Prevent invalid cdata escaping as this would throw a DOM error.
				// This is the same behavior as found in libxml2.
				// Related W3C standard: http://www.w3.org/TR/REC-xml/#dt-cdsection
				// Fix explanation: http://en.wikipedia.org/wiki/CDATA#Nesting
				$data = str_replace(']]>', ']]]]><![CDATA[>', $node->data);

				$fragment = $dom_document->createDocumentFragment();
				$fragment->appendXML($embed_prefix . $data . $embed_suffix);

				$dom_element->appendChild($fragment);
				$dom_element->removeChild($node);
			}
		}
	}

	/**
	 * Run all the enabled filters on a piece of text.
	 *
	 * Note: Because filters can inject JavaScript or execute PHP code, security is
	 * vital here. When a user supplies a text format, you should validate it using
	 * filter_access() before accepting/using it. This is normally done in the
	 * validation stage of the Form API. You should for example never make a preview
	 * of content in a disallowed format.
	 *
	 * @param   string   $text       The text to be filtered
	 * @param   integer  $format_id  The format id of the text to be filtered. If no format is assigned, the fallback format will be used [Optional]
	 * @param   string   $langcode   The language code of the text to be filtered, e.g. 'en' for English. This allows filters to be language aware so language specific text replacement can be implemented [Optional]
	 * @param   boolean  $cache      Boolean whether to cache the filtered output in the {cache_filter} table. The caller may set this to FALSE when the output is already cached elsewhere to avoid duplicate cache lookups and storage [Optional]
	 *
	 * @return  mixed
	 *
	 * @uses    Config::load
	 * @uses    Config_Group::get
	 * @uses    Cache::get
	 * @uses    Cache::set
	 * @uses    Module::event
	 * @uses    Filter::process
	 *
	 * @todo    Make @params description shorter
	 */
	public static function markup($text, $format_id = NULL, $langcode = NULL, $cache = FALSE)
	{
		// Save some cpu cycles if text is empty or null
		if(empty($text))
		{
			return $text;
		}

		$format_id = is_null($format_id) ? Kohana::$config->load('inputfilter')->get('default_format', 1) : $format_id;
		$langcode  = is_null($langcode) ? I18n::$lang : $langcode;

		// Check for a cached version of this piece of text.
		$cache_id = $format_id . ':' . $langcode . ':' . hash('sha256', $text);
        if ($cache and $cached = Cache::instance()->get('cache_filter:' . $cache_id)) {
			return $cached;
		}

		// Convert all Windows and Mac newlines to a single newline, so filters
		// only need to deal with one possibility.
		$text = str_replace(array("\r\n", "\r"), "\n", $text);

		$textObj = new ArrayObject(array(
				'text' 	   => (string) $text,
				'format'   => (int)    $format_id,
				'langcode' => (string) $langcode,
				'cache'    => (bool)   $cache,
				'cache_id' => (string) $cache_id
		), ArrayObject::ARRAY_AS_PROPS);

		Module::event('inputfilter', $textObj);

		$text = (is_string($textObj->text)) ? $textObj->text : $text;

		$text = Filter::process($textObj); // run all filters

		// Store in cache with a minimum expiration time of 1 day.
		if ($cache)
		{
            Cache::instance()->set('cache_filter:' . $cache_id, $text, null, time() + Date::DAY);
		}

		return $text;
	}

	/**
	 * HTML filter
	 *
	 * Provides filtering of input into accepted HTML.
	 *
	 * @param $text
	 * @param $format
	 * @param $filter
	 * @return string
	 */
	public static function html($text, $format, $filter)
	{
		$text = (string) HTMLFilter::factory($text, $format, $filter)->render();

		if ($filter['settings']['html_nofollow'])
		{
			$html_dom = static::dom_load($text);
			$links = $html_dom->getElementsByTagName('a');
			foreach ($links as $link)
			{
				$link->setAttribute('rel', 'nofollow');

				//Shortens long URLs to http://www.example.com/long/url...
				if ($filter['settings']['url_length'])
				{
					$link->nodeValue = static::limit_chars($link->nodeValue,
										 (int) $filter['settings']['url_length'], '....');
				}
			}
			$text = static::dom_serialize($html_dom);
		}

		return trim($text);
	}

	/**
	 * Markdown filter. Allows content to be submitted using Markdown.
	 *
	 * @link http://michelf.ca/projects/php-markdown/
	 * @link http://littoral.michelf.ca/code/php-markdown/php-markdown-extra-1.2.6.zip
	 */
	public static function markdown($text, $format, $filter)
	{
		include_once Kohana::find_file('vendor/Markdown', 'markdown');

		return Markdown($text);
	}

	/**
	 * Adds &lt;span class="initial"&gt; tag around the initial letter of each paragraph
	 *
	 * @param   string  $text  String to be processed
	 *
	 * @return  string
	 *
	 * @link    http://drupal.org/project/more_filters
	 */
	public static function initialcaps($text)
	{
		// Adds <span class="initial"> tag around the initial letter of each paragraph.
		// Only add after an opening <p> tag, ignoring any leading spaces. First letter must be a letter or number (no symbols).
		// Works with contractions.
		$processed_text = preg_replace('/(<p[^>]*>\s*)([A-Z0-9])([A-Z\'\s]{1})/i', '$1<span class="initial">$2</span>$3', $text);
		return $processed_text;
	}
}
