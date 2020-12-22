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
    public function setUp()
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
        $user = factory(User::class, 4)->create();
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
        $user = factory(User::class, 4)->create();
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
        factory(User::class, 4)->create();
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
        factory(User::class)->create([
            'USR_USERNAME' => 'jsmith',
            'USR_FIRSTNAME' => 'John',
            'USR_LASTNAME' => 'Smith',
        ]);
        factory(User::class)->create([
            'USR_USERNAME' => 'asmith',
            'USR_FIRSTNAME' => 'Adam',
            'USR_LASTNAME' => 'Smith',
        ]);
        factory(User::class)->create([
            'USR_USERNAME' => 'wsmith',
            'USR_FIRSTNAME' => 'Will',
            'USR_LASTNAME' => 'Smith',
        ]);
        factory(User::class)->create([
            'USR_USERNAME' => 'wwallace',
            'USR_FIRSTNAME' => 'Williams',
            'USR_LASTNAME' => 'Wallace',
            'USR_STATUS' => 'VACATION',
        ]);
        factory(User::class)->create([
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
        $user = factory(User::class)->create();
        // When the user exist
        $results = User::getId($user->USR_UID);
        $this->assertGreaterThan(0, $results);
        // When the user does not exist
        $results = User::getId(G::generateUniqueID());
        $this->assertEquals(0, $results);
    }
}