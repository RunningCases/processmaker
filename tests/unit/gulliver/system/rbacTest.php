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
}
