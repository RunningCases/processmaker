<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Services\Api;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Luracast\Restler\Data\ApiMethodInfo;
use Luracast\Restler\Defaults;
use Luracast\Restler\HumanReadableCache;
use Maveriks\Extension\Restler;
use ProcessMaker\BusinessModel\Cases\Unassigned;
use ProcessMaker\Services\Api\Metrics;
use ReflectionClass;
use Tests\TestCase;
use Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases\DraftTest;
use Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases\InboxTest;
use Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases\PausedTest;
use Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases\UnassignedTest;

/**
 * Class MetricsTest
 *
 * @coversDefaultClass @covers \ProcessMaker\Services\Api\Metrics
 */
class MetricsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Method set up.
     */
    public function setUp()
    {
        parent::setUp();
    }
    /**
     * Initialize Rest API.
     * @param string $userUid
     * @return Restler
     */
    private function initializeRestApi(string $userUid)
    {
        //server
        $reflection = new ReflectionClass('\ProcessMaker\Services\OAuth2\Server');

        $reflectionPropertyUserId = $reflection->getProperty('userId');
        $reflectionPropertyUserId->setAccessible(true);
        $reflectionPropertyUserId->setValue($userUid);

        $reflectionPropertyDSN = $reflection->getProperty('dsn');
        $reflectionPropertyDSN->setAccessible(true);
        $reflectionPropertyDSN->setValue('mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_DATABASE'));

        $reflectionPropertyUserName = $reflection->getProperty('dbUser');
        $reflectionPropertyUserName->setAccessible(true);
        $reflectionPropertyUserName->setValue(env('DB_USERNAME'));

        $reflectionPropertyPassword = $reflection->getProperty('dbPassword');
        $reflectionPropertyPassword->setAccessible(true);
        $reflectionPropertyPassword->setValue(env('DB_PASSWORD'));

        //application
        Defaults::$cacheDirectory = PATH_DB . config('system.workspace') . PATH_SEP;
        HumanReadableCache::$cacheDir = PATH_DB . config('system.workspace') . PATH_SEP;

        $rest = new Restler(true);
        $rest->setFlagMultipart(false);
        $rest->setAPIVersion('1.0');
        $rest->addAuthenticationClass('ProcessMaker\\Services\\OAuth2\\Server', '');
        $rest->addAuthenticationClass('ProcessMaker\\Policies\\AccessControl');
        $rest->addAuthenticationClass('ProcessMaker\\Policies\\ControlUnderUpdating');

        $rest->apiMethodInfo = new ApiMethodInfo();
        return $rest;
    }

    /**
     * Tests the getCountersList method with empty lists
     * 
     * @test
     */
    public function it_tests_get_counters_list_method_empty_lists()
    {
        $user = factory(\ProcessMaker\Model\User::class)->create();
        $this->initializeRestApi($user->USR_UID);

        $metrics = new Metrics();
        $res = $metrics->getCountersList();

        $this->assertEquals(0, $res[0]['Total']);
        $this->assertEquals(0, $res[1]['Total']);
        $this->assertEquals(0, $res[2]['Total']);
        $this->assertEquals(0, $res[3]['Total']);
    }

    /**
     * Tests the getCountersList method
     * 
     * @test
     */
    public function it_tests_get_counters_list_method_inbox()
    {
        $inbox = new InboxTest();
        $user = $inbox->createMultipleInbox(10);
        $this->initializeRestApi($user->USR_UID);
        $metrics = new Metrics();
        $res = $metrics->getCountersList();
        $this->assertEquals(10, $res[0]['Total']);
    }

    /**
     * Tests the getCountersList method
     * 
     * @test
     */
    public function it_tests_get_counters_list_method_draft()
    {
        $draft = new DraftTest();
        $user = $draft->createManyDraft(10);
        $this->initializeRestApi($user->USR_UID);
        $metrics = new Metrics();
        $res = $metrics->getCountersList();
        $this->assertNotEmpty($res);
    }

    /**
     * Tests the getCountersList method
     * 
     * @test
     */
    public function it_tests_get_counters_list_method_paused()
    {
        $paused = new PausedTest();
        $user = $paused->createMultiplePaused(5);
        $this->initializeRestApi($user->USR_UID);
        $metrics = new Metrics();
        $res = $metrics->getCountersList();
        $this->assertEquals(5, $res[2]['Total']);
    }

    /**
     * Tests the getCountersList method
     * 
     * @test
     */
    public function it_tests_get_counters_list_method_unassigned()
    {
        $unassignedTest = new UnassignedTest();
        $cases = $unassignedTest->createMultipleUnassigned(3);
        $unassigned = new Unassigned();
        $unassigned->setUserId($cases->USR_ID);
        $unassigned->setUserUid($cases->USR_UID);
        $this->initializeRestApi($cases->USR_UID);
        $metrics = new Metrics();
        $res = $metrics->getCountersList();
        $this->assertNotEmpty($res);
    }
}
