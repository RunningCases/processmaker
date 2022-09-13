<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\AppAssignSelfServiceValue;
use ProcessMaker\Model\AppAssignSelfServiceValueGroup;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\GroupUser;
use ProcessMaker\Model\Groupwf;
use ProcessMaker\Model\RbacUsers;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Class AppAssignSelfServiceValueTest
 *
 * @coversDefaultClass \ProcessMaker\Model\AppAssignSelfServiceValue
 */
class AppAssignSelfServiceValueTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test belongs to APP_NUMBER
     *
     * @covers \ProcessMaker\Model\AppAssignSelfServiceValue::appNumber()
     * @test
     */
    public function it_belong_app_number()
    {
        $table = AppAssignSelfServiceValue::factory()->create([
            'APP_NUMBER' => function () {
                return Delegation::factory()->create()->APP_NUMBER;
            }
        ]);
        $this->assertInstanceOf(Delegation::class, $table->appNumber);
    }

    /**
     * Test belongs to DEL_INDEX
     *
     * @covers \ProcessMaker\Model\AppAssignSelfServiceValue::index()
     * @test
     */
    public function it_belong_index()
    {
        $table = AppAssignSelfServiceValue::factory()->create([
            'DEL_INDEX' => function () {
                return Delegation::factory()->create()->DEL_INDEX;
            }
        ]);
        $this->assertInstanceOf(Delegation::class, $table->index);
    }

    /**
     * Test belongs to TAS_ID
     *
     * @covers \ProcessMaker\Model\AppAssignSelfServiceValue::task()
     * @test
     */
    public function it_belong_task()
    {
        $table = AppAssignSelfServiceValue::factory()->create([
            'TAS_ID' => function () {
                return Task::factory()->create()->TAS_ID;
            }
        ]);
        $this->assertInstanceOf(Task::class, $table->task);
    }

    /**
     * It tests getSelfServiceCasesByEvaluatePerUser()
     * 
     * @covers \ProcessMaker\Model\AppAssignSelfServiceValue::getSelfServiceCasesByEvaluatePerUser()
     * @test
     */
    public function it_should_self_service_by_value()
    {
        // Assign user in a group
        $rbacUser = RbacUsers::factory()->create();
        $user = User::factory()->create([
            'USR_UID' => $rbacUser['USR_UID']
        ]);
        $group = Groupwf::factory()->create();
        $table = GroupUser::factory()->create([
            'GRP_UID' => $group['GRP_UID'],
            'GRP_ID' => $group['GRP_ID'],
            'USR_UID' => $user['USR_UID'],
            'USR_ID' => $user['USR_ID'],
        ]);
        // Create the selfservice
        $self = AppAssignSelfServiceValue::factory()->create([
            'GRP_UID' => $group['GRP_UID'],
        ]);
        $table = AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => $self['ID'],
            'GRP_UID' => $group['GRP_UID'],
            'ASSIGNEE_ID' => $group['GRP_ID'],
            'ASSIGNEE_TYPE' => 2,
        ]);
        $result = AppAssignSelfServiceValue::getSelfServiceCasesByEvaluatePerUser($user['USR_UID']);
        $this->assertNotEmpty($result);
    }
}