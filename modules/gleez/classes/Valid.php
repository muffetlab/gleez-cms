<?php
/**
 * Validation rules
 *
 * @package    Gleez\Security
 * @version    1.1.1
 * @author     Gleez Team
 * @copyright  (c) 2011-2015 Gleez Technologies
 * @license    http://gleezcms.org/license Gleez CMS License
 */
class Valid extends Kohana_Valid
{
	/**
	 * Checks whether a string is valid UTF-8
	 *
	 * This method takes care of various issues,
	 * such as illegal overlong encodings and illegal use of surrogates.
	 * It will return true if $field is UTF-8, and false otherwise.
	 *
	 * Example:
	 * ~~~
	 * Valid::utf8($text);
	 * ~~~
	 *
	 * @link    http://w3.org/International/questions/qa-forms-utf-8.html
	 *
	 * @since   1.1.0   First time this method was introduced
	 * @since   1.1.1   Replaced by a faster algorithm
	 *
	 * @param   string  $string  The text to check
	 *
	 * @return  boolean
	 */
	public static function utf8($string)
	{
		return $string === '' || preg_match('/^./su', $string) === 1;
	}
}
