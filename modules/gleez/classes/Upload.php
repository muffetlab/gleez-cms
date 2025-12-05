<?php
/**
 * Upload helper class for working with uploaded files and [Validation].
 *
 * Example:
 * ~~~
 * $array = Validation::factory($_FILES);
 * ~~~
 *
 * [!!] Note: Remember to define your form with "enctype=multipart/form-data"
 *      or file uploading will not work!
 *
 * The following configuration properties can be set:
 *
 * - [Upload::$remove_spaces]
 * - [Upload::$default_directory]
 *
 * @package    Gleez\Helpers
 * @author     Gleez Team
 * @version    1.2.1
 * @copyright  (c) 2011-2015 Gleez Technologies
 * @license    http://gleezcms.org/license  Gleez CMS License
 */
class Upload extends Kohana_Upload
{
	/**
	 * Get PHP upload_max_filesize
	 *
	 * @return  integer
	 */
	public static function getUploadMaxFilesize()
	{
		$max_size = ini_get('upload_max_filesize');
		$mul = substr($max_size, -1);
		$mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));

		return $mul * (int) $max_size;
	}

    /**
     * Picture validation for image upload
     *
     * @param   array   $file        $_FILES item
     * @param   string  $upload_dir  Relative upload dir [Optional]
     *
     * Example:
     * ~~~
     * $filepath = Upload::uploadImage($_FILES);
     * ~~~
     *
     * @since   1.2.0
     *
     * @return  NULL|string          NULL when filed, otherwise file path
     *
     * @uses    System::mkdir
     * @uses    Message::error
     * @uses    Log::ERROR
     * @uses    Upload::valid
     * @uses    Upload::save
     * @uses    Config::get
     * @uses    File::getUnique
     */
    public static function uploadImage($file, $upload_dir = NULL)
    {
    	if (is_null($upload_dir))
    	{
    		$upload_dir = Kohana::$config->load('media')->get('upload_dir', 'media/pictures');
    	}

        $picture_path  = APPPATH . $upload_dir;
        $valid_formats = Kohana::$config->load('media')->get('supported_image_formats', array('jpg', 'gif', 'png'));
        $save          = TRUE;

        if ( ! is_dir($picture_path))
        {
            if ( ! System::mkdir($picture_path))
            {
                Message::error(__('Failed to create directory %dir for uploading picture.'));

                Kohana::$log->add(Log::ERROR, 'Failed to create directory :dir for uploading picture.',
                    array(':dir' => $picture_path)
                );

                $save = FALSE;
            }
        }
        // Check if there is an uploaded file and valid type
        if ($save AND self::valid($file) AND self::type($file, $valid_formats) and self::size($file, self::getUploadMaxFilesize()))
        {
            $filename = File::getUnique($file['name']).'.'.pathinfo($file['name'], PATHINFO_EXTENSION);
            $path     = self::save($file, $filename, $picture_path);

            if ($path)
            {
                return $upload_dir.DIRECTORY_SEPARATOR.$filename;
            }
        }

        return NULL;
    }
}
