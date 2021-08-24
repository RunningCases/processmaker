<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\RbacRoles;
use Tests\TestCase;

/**
 * Class ProcessTest
 *
 * @coversDefaultClass \ProcessMaker\Model\RbacRoles
 */
class RbacRolesTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * It tests the getRolUidByCode() method
     * 
     * @test
     */
    public function it_should_test_the_get_rol_uid_by_code_method()
    {
        $rol1 = factory(RbacRoles::class)->create([
            'ROL_CODE' => 'TEST_ROLE'
        ]);

        $rolUid = RbacRoles::getRolUidByCode('TEST_ROLE');
        $this->assertEquals($rolUid['ROL_UID'], $rol1->ROL_UID);
    }
}
