<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Report;

use Illuminate\Support\Facades\DB;
use Exception;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Process;
use ProcessMaker\Report\Reporting;
use Tests\TestCase;

class ReportingTest extends TestCase
{
    /**
     * Field object reporting.
     * @var object
     */
    public $reporting;

    /**
     * Method setUp.
     */
    public function setUp()
    {
        parent::setUp();
        $this->reporting = new Reporting();
    }

    /**
     * Method tearDown.
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * This tests the method fillReportByUser().
     * @test
     * @covers \ProcessMaker\Report\Reporting::fillReportByUser()
     */
    public function it_should_test_method_fillReportByUser()
    {
        $dateInit = date("YYYY-MM-DD");
        $dateFinish = date("YYYY-MM-DD");

        factory(Delegation::class)->create([
            'DEL_DELEGATE_DATE' => $dateInit
        ]);

        $this->reporting->setPathToAppCacheFiles(PATH_METHODS . 'setup' . PATH_SEP . 'setupSchemas' . PATH_SEP);
        $this->reporting->fillReportByUser($dateInit, $dateFinish);

        $result = DB::table("USR_REPORTING")->get();
        $this->assertInstanceOf('Illuminate\Support\Collection', $result);
    }

    /**
     * This tests the method fillReportByUser() waiting for an exception.
     * @test
     * @covers \ProcessMaker\Report\Reporting::fillReportByUser()
     */
    public function it_should_test_method_fillReportByUser_waiting_for_an_exception()
    {
        //assertion Exception
        $this->expectException(Exception::class);

        $dateInit = date("YYYY-MM-DD");
        $dateFinish = date("YYYY-MM-DD");
        $this->reporting->fillReportByUser($dateInit, $dateFinish);
    }

    /**
     * This tests the method fillReportByProcess().
     * @test
     * @covers \ProcessMaker\Report\Reporting::fillReportByProcess()
     */
    public function it_should_test_method_fillReportByProcess()
    {
        $dateInit = date("YYYY-MM-DD");
        $dateFinish = date("YYYY-MM-DD");

        factory(Delegation::class)->create([
            'DEL_DELEGATE_DATE' => $dateInit
        ]);

        $this->reporting->setPathToAppCacheFiles(PATH_METHODS . 'setup' . PATH_SEP . 'setupSchemas' . PATH_SEP);
        $this->reporting->fillReportByProcess($dateInit, $dateFinish);

        $result = DB::table("PRO_REPORTING")->get();
        $this->assertInstanceOf('Illuminate\Support\Collection', $result);
    }

    /**
     * This tests the method fillReportByProcess() waiting for an exception.
     * @test
     * @covers \ProcessMaker\Report\Reporting::fillReportByProcess()
     */
    public function it_should_test_method_fillReportByProcess_waiting_for_an_exception()
    {
        //assertion Exception
        $this->expectException(Exception::class);

        $dateInit = date("YYYY-MM-DD");
        $dateFinish = date("YYYY-MM-DD");
        $this->reporting->fillReportByProcess($dateInit, $dateFinish);
    }
}
