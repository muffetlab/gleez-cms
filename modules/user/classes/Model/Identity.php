<?php

/**
 * Identity Model Class
 *
 * @package    Gleez\User
 * @author     Gleez Team
 * @copyright  (c) 2011-2015 Gleez Technologies
 */
class Model_Identity extends ORM
{
    protected $_belongs_to = [
        'user' => ['foreign_key' => 'user_id']
    ];
}
