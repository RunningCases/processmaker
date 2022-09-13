<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use G;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Class UserTest
 *
 * @coversDefaultClass \ProcessMaker\Model\User
 */
class UserTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Set up function.
     */
    public function setUp(): void
    {
        parent::setUp();
        User::query()->delete();
    }

    /**
     * Tests the users filters scope with the usr uid filter
     *
     * @test
     */
    public function it_should_test_the_users_filters_scope_with_usr_uid()
    {
        $user = User::factory(4)->create();
        $filters = ['USR_UID' => $user[0]['USR_UID']];

        $userQuery = User::query()->select();
        $userQuery->userFilters($filters);
        $result = $userQuery->get()->values()->toArray();

        // Assert the expected numbers of rows in the result
        $this->assertCount(1, $result);

        // Assert the filter has been set successful
        $this->assertEquals($user[0]['USR_UID'], $result[0]['USR_UID']);
        $this->assertNotEquals($user[1]['USR_UID'], $result[0]['USR_UID']);
    }

    /**
     * Tests the users filters scope with the usr id filter
     *
     * @test
     */
    public function it_should_test_the_users_filters_scope_with_usr_id()
    {
        $user = User::factory(4)->create();
        $filters = ['USR_ID' => $user[0]['USR_ID']];
        $userQuery = User::query()->select();
        $userQuery->userFilters($filters);

        $result = $userQuery->get()->values()->toArray();

        // Assert the expected numbers of rows in the result
        $this->assertCount(1, $result);

        // Assert the filter has been set successful
        $this->assertEquals($user[0]['USR_ID'], $result[0]['USR_ID']);
        $this->assertNotEquals($user[1]['USR_ID'], $result[0]['USR_ID']);
    }

    /**
     * Tests the exception in the users filters scope
     *
     * @test
     */
    public function it_should_test_the_exception_in_users_filters_scope()
    {
        User::factory(4)->create();
        $filters = [];
        $userQuery = User::query()->select();

        //Expect an exception message
        $this->expectExceptionMessage("There are no filter for loading a user model");

        //Call the userFilters scope
        $userQuery->userFilters($filters);
    }

    /**
     * It test get users for the new home view
     *
     * @covers \ProcessMaker\Model\User::getUsersForHome()
     * @covers \ProcessMaker\Model\User::scopeActive()
     * @covers \ProcessMaker\Model\User::scopeWithoutGuest()
     * @test
     */
    public function it_should_test_get_users_for_home()
    {
        // Create five users (3 active, 1 on vacation, 1 inactive)
        User::factory()->create([
            'USR_USERNAME' => 'jsmith',
            'USR_FIRSTNAME' => 'John',
            'USR_LASTNAME' => 'Smith',
        ]);
        User::factory()->create([
            'USR_USERNAME' => 'asmith',
            'USR_FIRSTNAME' => 'Adam',
            'USR_LASTNAME' => 'Smith',
        ]);
        User::factory()->create([
            'USR_USERNAME' => 'wsmith',
            'USR_FIRSTNAME' => 'Will',
            'USR_LASTNAME' => 'Smith',
        ]);
        User::factory()->create([
            'USR_USERNAME' => 'wwallace',
            'USR_FIRSTNAME' => 'Williams',
            'USR_LASTNAME' => 'Wallace',
            'USR_STATUS' => 'VACATION',
        ]);
        User::factory()->create([
            'USR_USERNAME' => 'msmith',
            'USR_FIRSTNAME' => 'Marcus',
            'USR_LASTNAME' => 'Smith',
            'USR_STATUS' => 'INACTIVE',
        ]);

        // Assertions
        // Only will considerate the actives
        $this->assertCount(3, User::getUsersForHome());
        // Only will considerate the name Smith
        $this->assertCount(3, User::getUsersForHome('Smith'));
        // Only will considerate by default the actives
        $this->assertCount(3, User::getUsersForHome(null, null, 2));
        // Only will considerate by default the actives and limit
        $this->assertCount(1, User::getUsersForHome(null, 2, 1));
    }

    /**
     * It test get the user Id
     *
     * @covers \ProcessMaker\Model\User::getId()
     * @covers \ProcessMaker\Model\User::scopeUser()
     * @test
     */
    public function it_get_usr_id()
    {
        $user = User::factory()->create();
        // When the user exist
        $results = User::getId($user->USR_UID);
        $this->assertGreaterThan(0, $results);
        // When the user does not exist
        $results = User::getId(G::generateUniqueID());
        $this->assertEquals(0, $results);
    }

    /**
     * It test get the user information
     *
     * @covers \ProcessMaker\Model\User::scopeUserId()
     * @covers \ProcessMaker\Model\User::getInformation()
     * @test
     */
    public function it_get_information()
    {
        $user = User::factory()->create();
        // When the user exist
        $results = User::getInformation($user->USR_ID);
        $this->assertNotEmpty($results);
        $this->assertArrayHasKey('usr_username', $results);
        $this->assertArrayHasKey('usr_firstname', $results);
        $this->assertArrayHasKey('usr_lastname', $results);
        $this->assertArrayHasKey('usr_email', $results);
        $this->assertArrayHasKey('usr_position', $results);
    }

    /**
     * It test get the user information
     *
     * @covers \ProcessMaker\Model\User::scopeUserId()
     * @covers \ProcessMaker\Model\User::getAllInformation()
     * @test
     */
    public function it_get_all_information()
    {
        $user = User::factory()->create();
        // When the user exist
        $results = User::getAllInformation($user->USR_ID);
        $this->assertNotEmpty($results);
    }
    /**
     * It test get the createUser() method
     *
     * @covers \ProcessMaker\Model\User::createUser()
     * @test
     */
    public function it_should_test_the_create_user_method()
    {
        $usrData = [
            'USR_UID' => G::generateUniqueID(),
            'USR_USERNAME' => 'test',
            'USR_PASSWORD' => 'sample',
            'USR_FIRSTNAME' => 'test',
            'USR_LASTNAME' => 'test',
            'USR_EMAIL' => 'test@sample.com',
            'USR_DUE_DATE' => '2021-12-12',
            'USR_CREATE_DATE' => '2021-12-12',
            'USR_UPDATE_DATE' => '2021-12-12',
            'USR_STATUS' => 'ACTIVE',
            'USR_STATUS_ID' => 1,
            'USR_COUNTRY' => '',
            'USR_CITY' => '',
            'USR_LOCATION' => '',
            'USR_ADDRESS' => '',
            'USR_PHONE' => '',
            'USR_FAX' => '',
            'USR_CELLULAR' => '',
            'USR_ZIP_CODE' => '',
            'DEP_UID' => '',
            'USR_POSITION' => '',
            'USR_RESUME' => '',
            'ROL_CODE' => 0
        ];
        $res = User::createUser($usrData);
        $this->assertIsInt($res);
    }
}