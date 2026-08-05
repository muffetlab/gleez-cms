<?php

/**
 * Tests the User model functionality.
 *
 * @group gleez
 * @group gleez.user
 * @group gleez.user.user
 *
 */
class Gleez_UserTest extends Unittest_TestCase
{
    public function testInvalidUsers()
	{
        $this->expectException(Validation_Exception::class);

        $user = ORM::factory('User');
        $user->login(['name' => 'sundar1', 'password' => 'gleez1co']);
	}
}
