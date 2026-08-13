<?php

/**
 * Action Model Class
 *
 * @package    Gleez\ORM\Action
 * @author     Sandeep Sangamreddi - Gleez
 * @copyright  (c) 2011-2014 Gleez Technologies
 */
class Model_Action extends ORM
{
	/**
	 * "Has many" relationships
	 * @var array
	 */
    protected $_has_many = [
        'roles' => [
            'model' => 'Role',
            'through' => 'action_roles'
        ],
    ];
}
