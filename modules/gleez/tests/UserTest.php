<?php

/**
 * Tests the Config lib that's shipped with kohana
 *
 * @group Gleez
 * @group Gleez.core
 * @group Gleez.core.user
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
