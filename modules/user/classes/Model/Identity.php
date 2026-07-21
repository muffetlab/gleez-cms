<?php
/**
 * Identity Model Class
 *
 * @package    Gleez\User
 * @author     Gleez Team
 * @copyright  (c) 2011-2015 Gleez Technologies
 * @license    https://gleezcms.org/license
 */
class Model_Identity extends ORM {

	/**
	 * Table columns
	 * @var array
	 */
    protected $_table_columns = [
        'id' => ['type' => 'int'],
        'user_id' => ['type' => 'int'],
        'recipient' => ['type' => 'int'],
        'provider' => ['type' => 'string'],
        'provider_id' => ['type' => 'string'],
        'refresh_token' => ['type' => 'string'],
    ];

    protected $_belongs_to = [
        'user' => ['foreign_key' => 'user_id']
    ];
}
