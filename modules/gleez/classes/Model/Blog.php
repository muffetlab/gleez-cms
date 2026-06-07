<?php
/**
 * Gleez Blog Model
 *
 * @package    Gleez\ORM\Blog
 * @author     Sandeep Sangamreddi - Gleez
 * @copyright  (c) 2011-2015 Gleez Technologies
 * @license    https://gleezcms.org/license  Gleez CMS License
 */
class Model_Blog extends Post {

	/**
	 * Post table name
	 * @var string
	 */
	protected $_table_name = 'posts';

	/**
	 * Post type
	 * @var string
	 */
	protected $_post_type = 'blog';


    /**
     * Updates or Creates the record depending on loaded()
     *
     * @param Validation|null $validation Validation object [Optional]
     * @return  Post
     * @throws Cache_Exception
     * @throws Kohana_Exception
     * @throws ReflectionException
     * @uses    Config::get
     * @uses    Cache::delete
     * @uses    Config::load
     */
    public function save(Validation $validation = NULL): Kohana_ORM
    {
		$config = Kohana::$config->load('blog');
		$this->status = empty($this->status) ? $config->get('default_status', 'draft') : $this->status;

		if ( ! $config->use_comment)
		{
			$this->comment = empty($this->comment) ? $config->get('comment', 0) : $this->comment;
		}

		if( ! $config->use_excerpt)
		{
			$this->teaser = FALSE;
		}

        Cache::instance()->delete($this->type . ':recent_blogs');

		return parent::save($validation);
	}

	/**
	 * Set values from an array with support for one-one relationships
	 *
	 * This method should be used for loading in post data, etc.
	 *
     * @param array $values Array of column => value pairs
     * @param array $columns Array of columns to be set
	 * @return  ORM
	 */
    public function values(array $values, array $columns): Kohana_ORM
    {
		$this->type = $this->_post_type;

        return parent::values($values, $columns);
	}

    /**
     * Finds and loads a single database row into the object
     *
     * @return  Database_Result|ORM
     * @throws Kohana_Exception
     */
    public function find()
	{
		$this->where($this->_object_name.'.type', '=', $this->_post_type);

        return parent::find();
	}

    /**
     * Finds multiple database rows and returns an iterator of the rows found
     *
     * @return  Database_Result|ORM
     * @throws Kohana_Exception
     */
    public function find_all()
	{
		$this->where($this->_object_name.'.type', '=', $this->_post_type);

        return parent::find_all();
	}

    /**
     * Count the number of records in the table
     *
     * @return  integer
     * @throws Kohana_Exception
     */
    public function count_all(): int
    {
		$this->where($this->_object_name.'.type', '=', $this->_post_type);

        return parent::count_all();
	}

    /**
     * Deletes a single record or multiple records, ignoring relationships
     *
     * @param boolean $soft Make delete as soft or hard. Default hard [Optional]
     * @return  Post
     * @throws Kohana_Exception
     */
    public function delete(bool $soft = FALSE): Kohana_ORM
    {
		$this->where($this->_object_name.'.type', '=', $this->_post_type);

		return parent::delete($soft);
	}

}