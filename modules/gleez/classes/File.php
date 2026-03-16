<?php
/**
 * Gleez File Class
 *
 * @package    Gleez\Helpers
 * @author     Gleez Team
 * @version    1.1.3
 * @copyright  (c) 2011-2015 Gleez Technologies
 * @license    http://gleezcms.org/license  Gleez CMS License
 */
class File extends Kohana_File
{
	/**
	 * Generate a unique filename to avoid conflicts
	 *
	 * @since   1.0.1
	 *
	 * @param   string   $name           Filename [Optional]
	 * @param   integer  $length         Length of filename to return [Optional]
	 * @param   boolean  $remove_spaces  Remove spaces from file name [Optional]
	 * @param   string   $replacement    Replacement for spaces [Optional]
	 *
	 * @return  string
	 *
	 * @uses    Text::random
	 * @uses    UTF8::strtolower
	 */
	public static function getUnique($name = NULL, $length = 20, $remove_spaces = TRUE, $replacement = '_')
	{
		if (is_null($name))
		{
			return UTF8::strtolower(uniqid().Text::random('alnum', (int)$length));
		}
		else
		{
			// Find the file extension
			$ext    = strtolower(pathinfo($name, PATHINFO_EXTENSION));
			
			// Remove the extension from the filename
			$name   = substr($name, 0, -(strlen($ext) + 1));

			$retval = uniqid().($remove_spaces ? preg_replace('/\s+/u', $replacement, $name) : $name);
			$retval = is_null($length) ? $retval : substr($retval, 0, (int)$length);

			$retval = $retval.'.'.$ext;

			return $retval;
		}
	}

	/**
	 * Get file extension from it name
	 *
	 * @since   1.1.0
	 *
	 * @param   string  $file  Filename
	 *
	 * @return  string
	 */
	public static function getExt($file)
	{
		return pathinfo($file, PATHINFO_EXTENSION);
	}
}
