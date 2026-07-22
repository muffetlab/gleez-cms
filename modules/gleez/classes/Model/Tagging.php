<?php
/**
 * An adaptation of tagging
 *
 * @package    Gleez\ORM\Tagging
 * @author     Sandeep Sangamreddi - Gleez
 * @copyright  (c) 2011-2015 Gleez Technologies
 * @license    https://gleezcms.org/license  Gleez CMS License
 */
class Model_Tagging extends ORM {

	/**
	 * Table name
	 * @var string
	 */
	protected $_table_name = 'posts_tags';

	/**
	 * Table columns
	 * @var array
	 */
    protected $_table_columns = [
        'post_id' => ['type' => 'int'],
        'tag_id' => ['type' => 'int'],
        'author' => ['type' => 'int'],
        'type' => ['type' => 'string'],
        'created' => ['type' => 'int'],
    ];

	/**
	 * "Belongs to" relationships
	 * @var array
	 */
    protected $_belongs_to = [
        'user' => [
            'foreign_key' => 'author'
        ],
        'tags' => [
            'foreign_key' => 'tag_id'
        ],
        'posts' => [
            'foreign_key' => 'post_id'
        ]
    ];


	/**
	 * Auto-update columns for creation
	 * @var string
	 */
    protected $_created_column = [
        'column' => 'created',
        'format' => TRUE
    ];

}
