<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Util\Helpers;

use ProcessMaker\Model\User;
use Tests\TestCase;

class UpdateUserLastLoginTest extends TestCase
{
    /**
     * It tests the updateUserLastLogin function
     *
     * @test
     */
    public function it_should_test_the_update_last_login_date_function_when_it_does_not_fail()
    {
        $user = User::factory()->create();

        $userLog = ['USR_UID' => $user['USR_UID'], 'LOG_INIT_DATE' => date('Y-m-d H:i:s')];

        // Call the updateUserLastLogin function
        $result = updateUserLastLogin($userLog);

        // Asserts the update has been successful
        $this->assertEquals(1, $result);
    }

    /**
     * It tests the updateUserLastLogin function exception
     *
     * @test
     */
    public function it_should_test_the_update_last_login_date_function_when_it_fails()
    {
        $user = User::factory()->create();

        $userLog = ['USR_UID' => $user['USR_UID']];

        // Assert the expected exception
        $this->expectException('Exception');

        // Call the updateUserLastLogin function
        updateUserLastLogin($userLog);

        $userLog = null;

        // Assert the expected exception
        $this->expectExceptionMessage("There are no filter for loading a user model");

        // Call the updateUserLastLogin function
        updateUserLastLogin($userLog);

        $userLog = '';

        // Assert the expected exception
        $this->expectExceptionMessage("Illegal string offset 'USR_UID'");

        // Call the updateUserLastLogin function
        updateUserLastLogin($userLog);
    }
}