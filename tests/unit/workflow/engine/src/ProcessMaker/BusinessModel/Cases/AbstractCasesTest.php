<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use G;
use ProcessMaker\BusinessModel\Cases\AbstractCases;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\ProcessCategory;
use ProcessMaker\Model\User;
use Tests\TestCase;


/**
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases\AbstractCases
 */
class AbstractCasesTest extends TestCase
{
    /**
     * This check the getter and setter related to the category
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setCategoryUid()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getCategoryUid()
     * @test
     */
    public function it_return_set_get_category()
    {
        $category = factory(ProcessCategory::class)->create();
        $absCases = new AbstractCases();
        $absCases->setCategoryUid($category->CATEGORY_UID);
        $actual = $absCases->getCategoryUid();
        $this->assertEquals($category->CATEGORY_UID, $actual);
    }

    /**
     * This check the getter and setter related to the process
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setProcessUid()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getProcessUid()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setProcessId()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getProcessId()
     * @test
     */
    public function it_return_set_get_process()
    {
        $process = factory(Process::class)->create();
        $absCases = new AbstractCases();
        $absCases->setProcessUid($process->PRO_UID);
        $actual = $absCases->getProcessUid();
        $this->assertEquals($process->PRO_UID, $actual);
        $absCases->setProcessId($process->PRO_ID);
        $actual = $absCases->getProcessId();
        $this->assertEquals($process->PRO_ID, $actual);
    }

    /**
     * This check the getter and setter related to the user
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setUserId()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getUserId()
     * @test
     */
    public function it_return_set_get_user()
    {
        $users = factory(User::class)->create();
        $absCases = new AbstractCases();
        $absCases->setUserUid($users->USR_UID);
        $actual = $absCases->getUserUid();
        $this->assertEquals($users->USR_UID, $actual);
        $absCases->setUserId($users->USR_ID);
        $actual = $absCases->getUserId();
        $this->assertEquals($users->USR_ID, $actual);
    }

    /**
     * This check the getter and setter related to the search
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setValueToSearch()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getValueToSearch()
     * @test
     */
    public function it_return_set_get_search()
    {
        $absCases = new AbstractCases();
        $text = G::generateUniqueID();
        $absCases->setValueToSearch($text);
        $actual = $absCases->getValueToSearch();
        $this->assertEquals($text, $actual);
    }

    /**
     * This check the getter and setter related to the inbox status
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setInboxStatus()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getInboxStatus()
     * @test
     */
    public function it_return_set_get_inbox_status()
    {
        $absCases = new AbstractCases();
        $arguments = ['', 'ALL', 'READ', 'UNREAD'];
        $index = array_rand($arguments);
        $absCases->setInboxStatus($arguments[$index]);
        $actual = $absCases->getInboxStatus();
        if (empty($arguments[$index])) {
            $this->assertEquals($arguments[$index], 'ALL');
        } else {
            $this->assertEquals($arguments[$index], $actual);
        }
    }

    /**
     * This check the getter and setter related to the participated status
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setParticipatedStatus()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getParticipatedStatus()
     * @test
     */
    public function it_return_set_get_participated_status()
    {
        $absCases = new AbstractCases();
        $arguments = ['',  'ALL', 'STARTED', 'COMPLETED'];
        $index = array_rand($arguments);
        $absCases->setParticipatedStatus($arguments[$index]);
        $actual = $absCases->getParticipatedStatus();
        if (empty($arguments[$index])) {
            $this->assertEquals($arguments[$index], 'ALL');
        } else {
            $this->assertEquals($arguments[$index], $actual);
        }
    }

    /**
     * This check the getter and setter related to the risk status
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setRiskStatus()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getRiskStatus()
     * @test
     */
    public function it_return_set_get_risk_status()
    {
        $absCases = new AbstractCases();
        $arguments = ['', 'ALL', 'ON_TIME', 'AT_RISK', 'OVERDUE'];
        $index = array_rand($arguments);
        $absCases->setRiskStatus($arguments[$index]);
        $actual = $absCases->getRiskStatus();
        if (empty($arguments[$index])) {
            $this->assertEquals($arguments[$index], 'ALL');
        } else {
            $this->assertEquals($arguments[$index], $actual);
        }
    }

    /**
     * This check the getter and setter related to the case status
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setCaseStatus()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getCaseStatus()
     * @test
     */
    public function it_return_set_get_case_status()
    {
        $absCases = new AbstractCases();
        $arguments = ['', 'ALL', 'DRAFT', 'TO_DO', 'COMPLETED', 'CANCELLED', 'CANCELED'];
        $index = array_rand($arguments);
        $absCases->setCaseStatus($arguments[$index]);
        $actual = $absCases->getCaseStatus();
        if (empty($arguments[$index])) {
            $this->assertEquals($arguments[$index], 'ALL');
        } else {
            $this->assertEquals($arguments[$index], $actual);
        }
    }

    /**
     * This check the getter and setter related to the case
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setCaseUid()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getCaseUid()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setCaseNumber()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getCaseNumber()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setCasesUids()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getCasesUids()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setCasesNumbers()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getCasesNumbers()
     * @test
     */
    public function it_return_set_get_case()
    {
        $application = factory(Application::class)->create();
        $absCases = new AbstractCases();
        $absCases->setCaseUid($application->APP_UID);
        $actual = $absCases->getCaseUid();
        $this->assertEquals($application->APP_UID, $actual);
        $absCases->setCaseNumber($application->APP_NUMBER);
        $actual = $absCases->getCaseNumber();
        $this->assertEquals($application->APP_NUMBER, $actual);
        $absCases->setCasesUids([$application->APP_UID]);
        $actual = $absCases->getCasesUids();
        $this->assertEquals([$application->APP_UID], $actual);
        $absCases->setCasesNumbers([$application->APP_NUMBER]);
        $actual = $absCases->getCasesNumbers();
        $this->assertEquals([$application->APP_NUMBER], $actual);
    }

    /**
     * This check the getter and setter related to the newest than date
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setNewestThan()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getNewestThan()
     * @test
     */
    public function it_return_set_get_newest_than()
    {
        $absCases = new AbstractCases();
        $text = date('Y-m-d');
        $absCases->setNewestThan($text);
        $actual = $absCases->getNewestThan();
        $this->assertEquals($text, $actual);
    }

    /**
     * This check the getter and setter related to the oldest than date
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setOldestThan()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getOldestThan()
     * @test
     */
    public function it_return_set_get_oldest_than()
    {
        $absCases = new AbstractCases();
        $text = date('Y-m-d');
        $absCases->setOldestThan($text);
        $actual = $absCases->getOldestThan();
        $this->assertEquals($text, $actual);
    }

    /**
     * This check the getter and setter related to the oldest than date
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setOrderByColumn()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getOrderByColumn()
     * @test
     */
    public function it_return_set_get_order_by_column()
    {
        $absCases = new AbstractCases();
        $text = 'APP_NUMBER';
        $absCases->setOrderByColumn($text);
        $actual = $absCases->getOrderByColumn();
        $this->assertEquals($text, $actual);
    }

    /**
     * This check the getter and setter related to the order direction
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setOrderDirection()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getOrderDirection()
     * @test
     */
    public function it_return_set_get_order_direction()
    {
        $absCases = new AbstractCases();
        $arguments = ['DESC', 'ASC'];
        $index = array_rand($arguments);
        $absCases->setOrderDirection($arguments[$index]);
        $actual = $absCases->getOrderDirection();
        $this->assertEquals($arguments[$index], $actual);
    }

    /**
     * This check the getter and setter related to the paged, offset and limit
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setPaged()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getPaged()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setOffset()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getOffset()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setLimit()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getLimit()
     * @test
     */
    public function it_return_set_get_paged_offset()
    {
        $absCases = new AbstractCases();
        $number = 1000;
        $absCases->setPaged($number);
        $actual = $absCases->getPaged();
        $absCases->setOffset($number);
        $actual = $absCases->getOffset();
        $this->assertEquals($number, $actual);
        $absCases->setLimit($number);
        $actual = $absCases->getLimit();
        $this->assertEquals($number, $actual);
    }

    /**
     * This check the setter related all the properties
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setProperties()
     * @test
     */
    public function it_return_set_get_properties()
    {
        $absCases = new AbstractCases();
        $properties = [
            'category' => G::generateUniqueID(),
            'process' => G::generateUniqueID(),
            'user' => G::generateUniqueID(),
            'search' => G::generateUniqueID(),
            'caseLink' => G::generateUniqueID(),
            'appUidCheck' => [G::generateUniqueID()],
        ];
        $absCases->setProperties($properties);
        $actual = $absCases->getCategoryUid();
        $this->assertEquals($properties['category'], $actual);
        $actual = $absCases->getProcessUid();
        $this->assertEquals($properties['process'], $actual);
        $actual = $absCases->getUserUid();
        $this->assertEquals($properties['user'], $actual);
        $actual = $absCases->getValueToSearch();
        $this->assertEquals($properties['search'], $actual);
    }
}