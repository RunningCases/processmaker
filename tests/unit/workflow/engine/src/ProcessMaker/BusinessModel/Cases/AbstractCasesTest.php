<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use G;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\BusinessModel\Cases\AbstractCases;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\ProcessCategory;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases\AbstractCases
 */
class AbstractCasesTest extends TestCase
{
    use DatabaseTransactions;

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
     * This check the getter and setter related to the task
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setTaskId()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getTaskId()
     * @test
     */
    public function it_return_set_get_task()
    {
        $task = factory(Task::class)->create();
        $absCases = new AbstractCases();
        $absCases->setTaskId($task->TAS_ID);
        $actual = $absCases->getTaskId();
        $this->assertEquals($task->TAS_ID, $actual);
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
     * This check the getter and setter related to the priority
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setPriority()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getPriority()
     * @test
     */
    public function it_return_set_get_priority()
    {
        $absCases = new AbstractCases();
        $arguments = ['', 'VL', 'L', 'N', 'H', 'VH'];
        $index = array_rand($arguments);
        $absCases->setPriority($arguments[$index]);
        $actual = $absCases->getPriority();
        $this->assertEquals($index, $actual);
        // Empty
        $absCases->setPriority('');
        $actual = $absCases->getPriority();
        $this->assertEquals(0, $actual);
    }

    /**
     * This check the getter and setter related to the priorities
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setPriorities()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getPriorities()
     * @test
     */
    public function it_return_set_get_priorities()
    {
        $absCases = new AbstractCases();
        $arguments = ['', 'VL', 'L', 'N', 'H', 'VH'];
        $index = array_rand($arguments);
        $absCases->setPriorities([$arguments[$index]]);
        $actual = $absCases->getPriorities();
        $this->assertEquals([$index], $actual);
    }

    /**
     * This check the getter and setter related to the case number
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setCaseNumber()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getCaseNumber()
     * @test
     */
    public function it_return_set_get_case_number()
    {
        $case = factory(Application::class)->create();
        $absCases = new AbstractCases();
        $absCases->setCaseNumber($case->APP_NUMBER);
        $actual = $absCases->getCaseNumber();
        $this->assertEquals($case->APP_NUMBER, $actual);
    }

    /**
     * This check the getter and setter related to the range of case number
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setCaseNumberFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setCaseNumberTo()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getCaseNumberFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getCaseNumberTo()
     * @test
     */
    public function it_return_set_get_range_case_number()
    {
        $case1 = factory(Application::class)->create();
        $case2 = factory(Application::class)->create([
            'APP_NUMBER' => $case1->APP_NUMBER + 1
        ]);
        $absCases = new AbstractCases();
        $absCases->setCaseNumberFrom($case1->APP_NUMBER);
        $absCases->setCaseNumberTo($case2->APP_NUMBER);
        $from = $absCases->getCaseNumberFrom();
        $to = $absCases->getCaseNumberTo();
        $this->assertEquals($case1->APP_NUMBER, $from);
        $this->assertEquals($case2->APP_NUMBER, $to);
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
        $arguments = ['READ', 'UNREAD'];
        $index = array_rand($arguments);
        $absCases->setInboxStatus($arguments[$index]);
        $actual = $absCases->getInboxStatus();
        if ($arguments[$index] === '') {
            $this->assertEmpty($actual);
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
        $arguments = ['STARTED', 'COMPLETED'];
        $index = array_rand($arguments);
        $absCases->setParticipatedStatus($arguments[$index]);
        $actual = $absCases->getParticipatedStatus();
        $this->assertEquals($arguments[$index], $actual);
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
        $arguments = ['ON_TIME', 'AT_RISK', 'OVERDUE'];
        $index = array_rand($arguments);
        $absCases->setRiskStatus($arguments[$index]);
        $actual = $absCases->getRiskStatus();
        $this->assertEquals($arguments[$index], $actual);
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
        $arguments = ['', 'DRAFT', 'TO_DO', 'COMPLETED', 'CANCELED'];
        $index = array_rand($arguments);
        $absCases->setCaseStatus($arguments[$index]);
        $actual = $absCases->getCaseStatus();
        $this->assertEquals($index, $actual);
        // Incorrect canceled status
        $absCases->setCaseStatuses(['CANCELLED']);
        $actual = $absCases->getCaseStatuses();
        $this->assertEquals([4], $actual);
    }

    /**
     * This check the getter and setter related to the case statuses
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setCaseStatuses()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getCaseStatuses()
     * @test
     */
    public function it_return_set_get_case_statuses()
    {
        $absCases = new AbstractCases();
        $arguments = ['', 'DRAFT', 'TO_DO', 'COMPLETED', 'CANCELED'];
        $index = array_rand($arguments);
        $absCases->setCaseStatuses([$arguments[$index]]);
        $actual = $absCases->getCaseStatuses();
        $this->assertEquals([$index], $actual);
        // Incorrect canceled status
        $absCases->setCaseStatuses(['CANCELLED']);
        $actual = $absCases->getCaseStatuses();
        $this->assertEquals([4], $actual);
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
     * This check the getter and setter related to the filter cases
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setFilterCases()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getFilterCases()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setCasesNumbers()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getCasesNumbers()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setRangeCasesFromTo()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getRangeCasesFromTo()
     * @test
     */
    public function it_return_set_get_filter_cases()
    {
        $absCases = new AbstractCases();
        $text = '1,3-5,8,10-15';
        $absCases->setFilterCases($text);
        $actual = $absCases->getFilterCases();
        $this->assertEquals($text, $actual);
        $actual = $absCases->getCasesNumbers();
        $this->assertEquals([1,8], $actual);
        $actual = $absCases->getRangeCasesFromTo();
        $this->assertEquals(['3-5','10-15'], $actual);
    }

    /**
     * This check the getter and setter related to the start case from
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setStartCaseFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getStartCaseFrom()
     * @test
     */
    public function it_return_set_get_start_case_from()
    {
        $absCases = new AbstractCases();
        $text = date('Y-m-d');
        $absCases->setStartCaseFrom($text);
        $actual = $absCases->getStartCaseFrom();
        $this->assertEquals($text, $actual);
    }

    /**
     * This check the getter and setter related to the start case to
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setStartCaseTo()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getStartCaseTo()
     * @test
     */
    public function it_return_set_get_start_case_to()
    {
        $absCases = new AbstractCases();
        $text = date('Y-m-d');
        $absCases->setStartCaseTo($text);
        $actual = $absCases->getStartCaseTo();
        $this->assertEquals($text, $actual);
    }

    /**
     * This check the getter and setter related to the newest than date
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setDelegateFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getDelegateFrom()
     * @test
     */
    public function it_return_set_get_delegation_from()
    {
        $absCases = new AbstractCases();
        $text = date('Y-m-d');
        $absCases->setDelegateFrom($text);
        $actual = $absCases->getDelegateFrom();
        $this->assertEquals($text, $actual);
    }

    /**
     * This check the getter and setter related to the oldest than date
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setDelegateTo()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getDelegateTo()
     * @test
     */
    public function it_return_set_get_delegation_to()
    {
        $absCases = new AbstractCases();
        $text = date('Y-m-d');
        $absCases->setDelegateTo($text);
        $actual = $absCases->getDelegateTo();
        $this->assertEquals($text, $actual);
    }

    /**
     * This check the getter and setter related to the finish case from
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setFinishCaseFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getFinishCaseFrom()
     * @test
     */
    public function it_return_set_get_finish_case_from()
    {
        $absCases = new AbstractCases();
        $text = date('Y-m-d');
        $absCases->setFinishCaseFrom($text);
        $actual = $absCases->getFinishCaseFrom();
        $this->assertEquals($text, $actual);
    }

    /**
     * This check the getter and setter related to the finish case to
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setFinishCaseTo()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getFinishCaseTo()
     * @test
     */
    public function it_return_set_get_finish_case_to()
    {
        $absCases = new AbstractCases();
        $text = date('Y-m-d');
        $absCases->setFinishCaseTo($text);
        $actual = $absCases->getFinishCaseTo();
        $this->assertEquals($text, $actual);
    }

    /**
     * This check the getter and setter related to the finish delegate from
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setFinishFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getFinishFrom()
     * @test
     */
    public function it_return_set_get_finish_delegate_from()
    {
        $absCases = new AbstractCases();
        $text = date('Y-m-d');
        $absCases->setFinishFrom($text);
        $actual = $absCases->getFinishFrom();
        $this->assertEquals($text, $actual);
    }

    /**
     * This check the getter and setter related to the finish delegate to
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setFinishTo()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getFinishTo()
     * @test
     */
    public function it_return_set_get_finish_delegate_to()
    {
        $absCases = new AbstractCases();
        $text = date('Y-m-d');
        $absCases->setFinishTo($text);
        $actual = $absCases->getFinishTo();
        $this->assertEquals($text, $actual);
    }

    /**
     * This check the getter and setter related to the due date from
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setDueFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getDueFrom()
     * @test
     */
    public function it_return_set_get_due_from()
    {
        $absCases = new AbstractCases();
        $text = date('Y-m-d');
        $absCases->setDueFrom($text);
        $actual = $absCases->getDueFrom();
        $this->assertEquals($text, $actual);
    }

    /**
     * This check the getter and setter related to the due date to
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setDueTo()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getDueTo()
     * @test
     */
    public function it_return_set_get_due_to()
    {
        $absCases = new AbstractCases();
        $text = date('Y-m-d');
        $absCases->setDueTo($text);
        $actual = $absCases->getDueTo();
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
     * This check the setter by default related to the properties
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setProperties()
     * @test
     */
    public function it_return_set_get_properties_default()
    {
        $absCases = new AbstractCases();
        $properties = [];
        $absCases->setProperties($properties);
        $actual = $absCases->getProcessId();
        $this->assertEquals(0, $actual);
        $actual = $absCases->getTaskId();
        $this->assertEquals(0, $actual);
        $actual = $absCases->getTaskId();
        $this->assertEquals(0, $actual);
        $actual = $absCases->getUserId();
        $this->assertEquals(0, $actual);
        $actual = $absCases->getCaseNumber();
        $this->assertEquals(0, $actual);
        $actual = $absCases->getOrderDirection();
        $this->assertEquals('DESC', $actual);
        $actual = $absCases->getOrderByColumn();
        $this->assertEquals('APP_NUMBER', $actual);
        $actual = $absCases->getOffset();
        $this->assertEquals(0, $actual);
        $actual = $absCases->getLimit();
        $this->assertEquals(15, $actual);
        // Home - Search
        $actual = $absCases->getPriorities();
        $this->assertEmpty($actual);
        $actual = $absCases->getCaseStatuses();
        $this->assertEquals([1,2,3,4], $actual);
        $actual = $absCases->getFilterCases();
        $this->assertEmpty($actual);
        $actual = $absCases->getDelegateFrom();
        $this->assertEmpty($actual);
        $actual = $absCases->getDelegateTo();
        $this->assertEmpty($actual);
        // Home - My cases
        $actual = $absCases->getParticipatedStatus();
        $this->assertEmpty($actual);
        $actual = $absCases->getCaseNumberFrom();
        $this->assertEmpty($actual);
        $actual = $absCases->getCaseNumberTo();
        $this->assertEmpty($actual);
        $actual = $absCases->getFinishCaseFrom();
        $this->assertEmpty($actual);
        $actual = $absCases->getFinishCaseTo();
        $this->assertEmpty($actual);
    }

    /**
     * This check the setter related all the properties
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setProperties()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setProcessId()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setTaskId()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setUserId()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setCaseNumber()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setCaseTitle()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getProcessId()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getTaskId()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getUserId()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getCaseNumber()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getCaseTitle()
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setParticipatedStatus()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setCaseStatus()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setStartCaseFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setStartCaseTo()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setFinishCaseFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setFinishCaseTo()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getParticipatedStatus()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getCaseStatus()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getStartCaseFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getStartCaseTo()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getFinishCaseFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getFinishCaseTo()
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setFilterCases()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setCaseStatuses()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setProperties()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setDelegateFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setDelegateTo()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setDueFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setDueTo()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getFilterCases()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getCaseStatuses()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getDelegateFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getDelegateTo()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getDueFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getDueTo()
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setCaseUid()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setCasesUids()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setOrderByColumn()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setOrderDirection()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setPaged()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setOffset()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::setLimit()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getCaseUid()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getCasesUids()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getOrderByColumn()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getOrderDirection()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getPaged()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getOffset()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getLimit()
     * @test
     */
    public function it_return_set_get_properties()
    {
        $absCases = new AbstractCases();
        $properties = [
            // Tasks - Cases
            'process' => rand(),
            'task' => rand(),
            'user' => rand(),
            'caseNumber' => rand(),
            'caseTitle' => G::generateUniqueID(),
            // Home - Search
            'caseStatuses' => ['TO_DO','DRAFT'],
            'filterCases'=> '1,3-5,8,10-15',
            // Home - My cases
            'filter'=> 'STARTED',
            'caseStatus' => 'TO_DO',
            'startCaseFrom' => date('Y-m-d'),
            'startCaseTo' => date('Y-m-d'),
            'finishCaseFrom' => date('Y-m-d'),
            'finishCaseTo' => date('Y-m-d'),
            // Other
            'caseLink' => G::generateUniqueID(),
            'appUidCheck' => [G::generateUniqueID()],
            'sort' => 'APP_NUMBER',
            'dir' => 'DESC',
            'paged' => true,
            'start' => 5,
            'limit' => 10,
        ];
        $absCases->setProperties($properties);
        // Tasks - Cases
        $actual = $absCases->getProcessId();
        $this->assertEquals($properties['process'], $actual);
        $actual = $absCases->getTaskId();
        $this->assertEquals($properties['task'], $actual);
        $actual = $absCases->getUserId();
        $this->assertEquals($properties['user'], $actual);
        $actual = $absCases->getCaseNumber();
        $this->assertEquals($properties['caseNumber'], $actual);
        $actual = $absCases->getCaseTitle();
        $this->assertEquals($properties['caseTitle'], $actual);
        // Home - Search
        $actual = $absCases->getCaseStatuses();
        $this->assertNotEmpty($actual);
        $actual = $absCases->getFilterCases();
        $this->assertEmpty($actual);
        $actual = $absCases->getCasesNumbers();
        $this->assertEmpty($actual);
        $actual = $absCases->getRangeCasesFromTo();
        $this->assertEmpty($actual);
        $actual = $absCases->getStartCaseFrom();
        $this->assertEmpty($actual);
        $actual = $absCases->getStartCaseTo();
        $this->assertEmpty($actual);
        $actual = $absCases->getFinishCaseFrom();
        $this->assertEmpty($actual);
        $actual = $absCases->getFinishCaseTo();
        $this->assertEmpty($actual);
        // Home - My cases
        $actual = $absCases->getParticipatedStatus();
        $this->assertEmpty($actual);
        $actual = $absCases->getCaseStatus();
        $this->assertEmpty($actual);
        $actual = $absCases->getStartCaseFrom();
        $this->assertEmpty($actual);
        $actual = $absCases->getStartCaseTo();
        $this->assertEmpty($actual);
        $actual = $absCases->getFinishCaseFrom();
        $this->assertEmpty($actual);
        $actual = $absCases->getFinishCaseTo();
        $this->assertEmpty($actual);
        // Other
        $actual = $absCases->getCaseUid();
        $this->assertEquals($properties['caseLink'], $actual);
        $actual = $absCases->getCasesUids();
        $this->assertEquals($properties['appUidCheck'], $actual);
        $actual = $absCases->getOrderByColumn();
        $this->assertEquals($properties['sort'], $actual);
        $actual = $absCases->getOrderDirection();
        $this->assertEquals($properties['dir'], $actual);
        $actual = $absCases->getPaged();
        $this->assertEquals($properties['paged'], $actual);
        $actual = $absCases->getOffset();
        $this->assertEquals($properties['start'], $actual);
        $actual = $absCases->getLimit();
        $this->assertEquals($properties['limit'], $actual);
    }

    /**
     * This check the get task color
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getTaskColor()
     * @test
     */
    public function it_return_task_color()
    {
        $absCases = new AbstractCases();
        $dueDate = date('Y-m-d');
        $result = $absCases->getTaskColor($dueDate);
        $this->assertNotEmpty($result);
    }

    /**
     * This check task color according the due date
     *
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::prepareTaskPending()
     * @test
     */
    public function it_return_task_pending_result()
    {
        $task = factory(Task::class, 2)->create();
        $absCases = new AbstractCases();
        $pending = '[
            {"tas_id":'.$task[0]->TAS_ID.', "user_id":1, "due_date":"2020-12-04 19:11:14"},
            {"tas_id":'.$task[1]->TAS_ID.', "user_id":2, "due_date":"2020-12-04 19:12:45"}
        ]';
        $result = $absCases->prepareTaskPending($pending);
        $this->assertNotEmpty($result);
    }
}