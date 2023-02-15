<?php

namespace ProcessMaker\BusinessModel;

use Exception;
use ProcessMaker\BusinessModel\User as BmUser;
use ProcessMaker\Model\DashletInstance;
use ProcessMaker\Model\GroupUser;
use ProcessMaker\Model\Groupwf;
use ProcessMaker\Model\ObjectPermission;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\ProcessUser;
use ProcessMaker\Model\TaskUser;
use RBAC;
use Tests\TestCase;
use ProcessMaker\Model\User;

/**
 * Class UserTest
 *
 * @coversDefaultClass \ProcessMaker\BusinessModel\User
 */
class UserTest extends TestCase
{
    /**
     * This method is called before the first test of this test class is run.
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::truncateNonInitialModels();
    }

    /**
     * This get guest value
     *
     * @covers \ProcessMaker\BusinessModel\User::getGuestUser()
     * @test
     */
    public function it_test_get_guest_user()
    {
        $user = new BmUser();
        $result = $user->getGuestUser();
        $this->assertNotEmpty($result);
    }

    /**
     * This checks the delete case admin
     *
     * @covers \ProcessMaker\BusinessModel\User::deleteGdpr()
     * @test
     */
    public function it_test_delete_user_gpdr_exception_when_user_is_admin()
    {
        $user = new BmUser();
        $this->expectException(Exception::class);
        $user->deleteGdpr(RBAC::ADMIN_USER_UID);
    }

    /**
     * This checks the delete case guest
     *
     * @covers \ProcessMaker\BusinessModel\User::deleteGdpr()
     * @test
     */
    public function it_test_delete_user_gpdr_exception_when_user_is_guest()
    {
        $user = new BmUser();
        $this->expectException(Exception::class);
        $user->deleteGdpr(RBAC::GUEST_USER_UID);
    }

    /**
     * This checks the delete case guest
     *
     * @covers \ProcessMaker\BusinessModel\User::deleteGdpr()
     *
     * @test
     */
    public function it_test_delete_user_gpdr()
    {
        // Create a user
        $user = User::factory()->create();
        // Assign the user in a group
        $groupwf = Groupwf::factory()->create();
        GroupUser::factory()->create([
            'GRP_UID' => $groupwf->GRP_UID,
            'GRP_ID' => $groupwf->GRP_ID,
            'USR_UID' => $user->USR_UID,
        ]);
        // Assign the user in a task
        TaskUser::factory()->create([
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1,
        ]);
        // Assign the user in a process owner
        Process::factory()->create([
            'PRO_CREATE_USER' => $user->USR_UID,
        ]);
        // Assign the user in a process permission
        ObjectPermission::factory()->create([
            'USR_UID' => $user->USR_UID,
            'OP_USER_RELATION' => 1,
        ]);
        // Assign the user in a process supervisor
        ProcessUser::factory()->create([
            'USR_UID' => $user->USR_UID,
            'PU_TYPE' => 'SUPERVISOR',
        ]);
        // Assign the user in a dashboard
        DashletInstance::factory()->create([
            'DAS_INS_OWNER_UID' => $user->USR_UID,
            'DAS_INS_OWNER_TYPE' => 'USER',
        ]);
        // Delete user
        $usr = new BmUser();
        $usr->deleteGdpr($user->USR_UID);
        // Check if the user relation with the table are removed
        $table = GroupUser::select()->where('USR_UID', $user->USR_UID)->first();
        $this->assertEmpty($table);
        $table = TaskUser::select()->where('USR_UID', $user->USR_UID)->first();
        $this->assertEmpty($table);
        $table = Process::select()->where('PRO_CREATE_USER', $user->USR_UID)->first();
        $this->assertEmpty($table);
        $table = ObjectPermission::select()->where('USR_UID', $user->USR_UID)->first();
        $this->assertEmpty($table);
        $table = ProcessUser::select()->where('USR_UID', $user->USR_UID)->first();
        $this->assertEmpty($table);
        $table = DashletInstance::select()->where('DAS_INS_OWNER_UID', $user->USR_UID)->first();
        $this->assertEmpty($table);
        $table = User::select()->where('USR_UID', $user->USR_UID)->first();
        // Set the important fields with an specific value
        $this->assertEquals($table->USR_STATUS, 'CLOSED');
        $this->assertEquals($table->USR_STATUS_ID, 0);
        $this->assertEquals($table->USR_FIRSTNAME, $usr::DELETE_USER);
        $this->assertEquals($table->USR_LASTNAME, $usr::DELETE_USER);
        // Clean the string fields
        $this->assertEmpty($table->USR_USERNAME);
        $this->assertEmpty($table->USR_EMAIL);
        $this->assertEmpty($table->USR_COUNTRY);
        $this->assertEmpty($table->USR_CITY);
        $this->assertEmpty($table->USR_LOCATION);
        $this->assertEmpty($table->USR_ADDRESS);
        $this->assertEmpty($table->USR_PHONE);
        $this->assertEmpty($table->USR_FAX);
        $this->assertEmpty($table->USR_CELLULAR);
        $this->assertEmpty($table->USR_ZIP_CODE);
        $this->assertEmpty($table->USR_TIME_ZONE);
        $this->assertEmpty($table->USR_EXTENDED_ATTRIBUTES_DATA);
        // Clean the date fields
        $this->assertEquals($table->USR_BIRTHDAY, '0000-00-00');
        $this->assertEquals($table->USR_DUE_DATE, '0000-00-00');
        // Clean the datetime fields
        $this->assertEquals($table->USR_DUE_DATE, '0000-00-00 00:00:00');
        $this->assertEquals($table->USR_DUE_DATE, '0000-00-00 00:00:00');
    }
}
