<?php

namespace Tests\unit\gulliver\system;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use G;
use RBAC;
use Tests\TestCase;

class rbacTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests the initialization of values.
     * @test
     * @covers \RBAC::__construct()
     */
    public function it_should_initialize_properties_for_gmail_oauth()
    {
        $rbac = new RBAC();
        $authorizedActions = $rbac->authorizedActions;

        $this->assertArrayHasKey("emailServerGmailOAuth.php", $authorizedActions);

        $subset = [
            'code' => ['PM_SETUP']
        ];
        $this->assertContains($subset, $authorizedActions);
    }

    /**
     * This test the updateUser method.
     * @test
     * @covers \RBAC::updateUser()
     */
    public function it_should_test_updateUser_method()
    {
        $data = [
            'USR_UID' => '00000000000000000000000000000001',
            'USR_DUE_DATE' => '2050-01-01',
            'USR_STATUS' => 'ACTIVE'
        ];
        $rolCode = 'PROCESSMAKER_ADMIN';
        $rbac = new RBAC();
        $rbac->initRBAC();
        $result = $rbac->updateUser($data, $rolCode);

        //assert
        $user = \ProcessMaker\Model\RbacUsers::where('USR_UID', '=', $data['USR_UID'])->first()->toArray();
        $this->assertEquals($data['USR_DUE_DATE'], $user['USR_DUE_DATE']);
    }
}
