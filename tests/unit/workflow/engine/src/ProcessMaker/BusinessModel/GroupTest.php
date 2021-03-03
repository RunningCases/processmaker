<?php

namespace ProcessMaker\BusinessModel;

use Exception;
use Tests\TestCase;

/**
 * Class GroupTest
 *
 * @coversDefaultClass \ProcessMaker\BusinessModel\Group
 */
class GroupTest extends TestCase
{
    /**
     * @var Group
     */
    protected $group;

    /**
     * Return instance Group
     *
     * @return Group
     */
    public function getInstanceGroup()
    {
        return $this->group;
    }

    /**
     * Set instance group
     *
     * @param Group $group
     */
    public function setInstanceGroup(Group $group)
    {
        $this->group = $group;
    }

    /**
     * Sets up the unit tests.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->setInstanceGroup(new Group());
    }

    /**
     * Information Group
     *
     * @return array Definition Data Group
     */
    public function testDataGroup()
    {
        $response = [
            'GRP_TITLE' => 'Group Test Unit',
            'GRP_STATUS' => 'ACTIVE'
        ];
        return $response;
    }

    /**
     * Create group
     *
     * @depends testDataGroup
     *
     * @param array $dataGroup Information Group
     *
     * @return string group Uid
     */
    public function testCreate($dataGroup)
    {
        $response = $this->getInstanceGroup()->create($dataGroup);

        $this->assertArrayHasKey('GRP_UID', $response);

        return $response['GRP_UID'];
    }

    /**
     * Get users from a group created recently.
     *
     * @depends testCreate
     * @param string $groupUid Uid group
     */
    public function testGetUsersOfGroup($groupUid)
    {
        $response = $this->getInstanceGroup()->getUsers('USERS', $groupUid);
        $this->assertCount(0, $response);
    }

    /**
     * Get available users for assign to a group
     *
     * @depends testCreate
     * @param string $groupUid Uid group
     */
    public function testGetUsersAvailable($groupUid)
    {
        $result = \ProcessMaker\Model\User::where('USERS.USR_STATUS', '<>', 'CLOSED')
        ->whereNotIn('USERS.USR_UID', ['00000000000000000000000000000002'])
        ->leftJoin('GROUP_USER', function($query) { 
            $query->on('GROUP_USER.USR_UID', '=', 'USERS.USR_UID');
        })
        ->get()
        ->toArray();

        $response = $this->getInstanceGroup()->getUsers('AVAILABLE-USERS', $groupUid);
        $this->assertCount(count($result), $response);
    }

    /**
     * Obtain assigned supervisors
     *
     * @depends testCreate
     * @param string $groupUid Uid group
     */
    public function testGetUsersSupervisor($groupUid)
    {
        $response = $this->getInstanceGroup()->getUsers('SUPERVISOR', $groupUid);
        $this->assertCount(0, $response);
    }

    /**
     * Delete group
     *
     * @depends  testCreate
     * @expectedException Exception
     *
     * @param string $groupUid Uid Group
     */
    public function testDelete($groupUid)
    {
        $this->getInstanceGroup()->delete($groupUid);
        $this->getInstanceGroup()->getGroup($groupUid);
    }
}
