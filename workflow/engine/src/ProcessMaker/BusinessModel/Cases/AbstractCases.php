<?php

namespace ProcessMaker\BusinessModel\Cases;

use Datetime;
use Exception;
use ProcessMaker\BusinessModel\Interfaces\CasesInterface;
use ProcessMaker\BusinessModel\Validator;

class AbstractCases implements CasesInterface
{
    // Constants for validate values
    const INBOX_STATUSES = ['', 'ALL', 'READ', 'UNREAD'];
    const PARTICIPATED_STATUSES = ['', 'ALL', 'STARTED', 'IN_PROGRESS', 'COMPLETED', 'SUPERVISING'];
    const RISK_STATUSES = ['', 'ALL', 'ON_TIME', 'AT_RISK', 'OVERDUE'];
    const CASE_STATUSES = ['', 'ALL', 'DRAFT', 'TO_DO', 'COMPLETED', 'CANCELLED', 'CANCELED'];
    const ORDER_DIRECTIONS = ['DESC', 'ASC'];
    const CORRECT_CANCELED_STATUS = 'CANCELED';
    const INCORRECT_CANCELED_STATUS = 'CANCELLED';
    const PRIORITIES = [1 => 'VL', 2 => 'L', 3 => 'N', 4 => 'H', 5 => 'VH'];
    const TASK_COLORS = [1 => 'green', 2 => 'red', 3 => 'orange', 4 => 'blue', 5 => 'gray'];
    const COLOR_OVERDUE = 1;
    const COLOR_ON_TIME = 2;
    const COLOR_DRAFT = 3;
    const COLOR_PAUSED = 4;
    const COLOR_UNASSIGNED = 5;

    // Filter by category from a process, know as "$category" in the old lists classes
    private $categoryUid = '';

    // Filter by process, know as "$process" in the old lists classes
    private $processUid = '';

    // Filter by process using the Id field
    private $processId = 0;

    // Filter by task using the Id field
    private $taskId = 0;

    // Filter by user, know as "$user" in the old lists classes
    private $userUid = '';

    // Filter by user using the Id field
    private $userId = 0;

    // Value to search, can be a text or an application number, know as "$search" in the old lists classes
    private $valueToSearch = '';

    // Filter cases depending if were read or not, know as "$filter" in the old lists classes
    private $inboxStatus = '';

    // Filter cases depending if the case was started or completed by the current user, know as "$filter" in the old lists classes
    private $participatedStatus = '';

    // Filter by risk status, know as "$filterStatus" in the old list "inbox" class
    private $riskStatus = '';

    // Filter by specific priority
    private $priority = 0;

    // Filter by case status, know as "$filterStatus" in the old "participated last" class
    private $caseStatus = '';

    // Filter by a specific case, know as "$caseLink" in the old lists classes
    private $caseUid = '';

    // Filter by a specific case using case number
    private $caseNumber = 0;

    // Filter by a specific range of case number
    private $fromCaseNumber = 0;
    private $toCaseNumber = 0;

    // Filter by specific cases, know as "$appUidCheck" in the old lists classes
    private $casesUids = [];

    // Filter by specific cases using the case numbers
    private $casesNumbers = [];

    // Filter recent cases starting by a specific date, know as "newestthan" in the old lists classes
    private $newestThan = '';

    // Filter old cases ending by a specific date, know as "oldestthan" in the old lists classes
    private $oldestThan = '';

    // Column by which the results will be sorted, know as "$sort" in the old lists classes
    private $orderByColumn = 'APP_DELEGATION.APP_NUMBER';

    // Sorts the data in descending or ascending order, know as "$dir" in the old lists classes
    private $orderDirection = 'DESC';

    // Results should be paged?
    private $paged = true;

    // Offset is used to identify the starting point to return rows from a result set, know as "$start" in the old lists classes
    private $offset = 0;

    // Number of rows to return
    private $limit = 25;

    /**
     * Set Category Uid value
     *
     * @param string $categoryUid
     */
    public function setCategoryUid(string $categoryUid)
    {
        $this->categoryUid = $categoryUid;
    }

    /**
     * Get Category Uid value
     *
     * @return string
     */
    public function getCategoryUid()
    {
        return $this->categoryUid;
    }

    /**
     * Set Process Uid value
     *
     * @param string $processUid
     */
    public function setProcessUid(string $processUid)
    {
        $this->processUid = $processUid;
    }

    /**
     * Get Process Uid value
     *
     * @return string
     */
    public function getProcessUid()
    {
        return $this->processUid;
    }

    /**
     * Set Process Id value
     *
     * @param int $processId
     */
    public function setProcessId(int $processId)
    {
        $this->processId = $processId;
    }

    /**
     * Get Process Id value
     *
     * @return int
     */
    public function getProcessId()
    {
        return $this->processId;
    }

    /**
     * Set task Id value
     *
     * @param int $taskId
     */
    public function setTaskId(int $taskId)
    {
        $this->taskId = $taskId;
    }

    /**
     * Get task Id value
     *
     * @return int
     */
    public function getTaskId()
    {
        return $this->taskId;
    }

    /**
     * Set User Uid value
     *
     * @param string $userUid
     */
    public function setUserUid(string $userUid)
    {
        $this->userUid = $userUid;
    }

    /**
     * Get User Uid value
     *
     * @return string
     */
    public function getUserUid()
    {
        return $this->userUid;
    }

    /**
     * Set User Id value
     *
     * @param int $userId
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Get User Id value
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set value to search
     *
     * @param string $valueToSearch
     */
    public function setValueToSearch(string $valueToSearch)
    {
        $this->valueToSearch = $valueToSearch;
    }

    /**
     * Get value to search
     *
     * @return string
     */
    public function getValueToSearch()
    {
        return $this->valueToSearch;
    }

    /**
     * Set inbox status
     *
     * @param string $inboxStatus
     *
     * @throws Exception
     */
    public function setInboxStatus(string $inboxStatus)
    {
        // Convert the value to upper case
        $inboxStatus = strtoupper($inboxStatus);

        // Validate the inbox status
        if (!in_array($inboxStatus, self::INBOX_STATUSES)) {
            throw new Exception("Inbox status '{$inboxStatus}' is not valid.");
        }

        // If empty string is sent, use value 'ALL'
        if ($inboxStatus === '') {
            $inboxStatus = 'ALL';
        }

        $this->inboxStatus = $inboxStatus;
    }

    /**
     * Get inbox status
     *
     * @return string
     */
    public function getInboxStatus()
    {
        return $this->inboxStatus;
    }

    /**
     * Set participated status
     *
     * @param string $participatedStatus
     *
     * @throws Exception
     */
    public function setParticipatedStatus(string $participatedStatus)
    {
        // Convert the value to upper case
        $participatedStatus = strtoupper($participatedStatus);

        // Validate the participated status
        if (!in_array($participatedStatus, self::PARTICIPATED_STATUSES)) {
            throw new Exception("Participated status '{$participatedStatus}' is not valid.");
        }

        // If empty string is sent, use value 'ALL'
        if ($participatedStatus === '') {
            $participatedStatus = 'ALL';
        }

        $this->participatedStatus = $participatedStatus;
    }

    /**
     * Get participated status
     *
     * @return string
     */
    public function getParticipatedStatus()
    {
        return $this->participatedStatus;
    }

    /**
     * Set risk status
     *
     * @param string $riskStatus
     *
     * @throws Exception
     */
    public function setRiskStatus(string $riskStatus)
    {
        // Convert the value to upper case
        $riskStatus = strtoupper($riskStatus);

        // Validate the risk status
        if (!in_array($riskStatus, self::RISK_STATUSES)) {
            throw new Exception("Risk status '{$riskStatus}' is not valid.");
        }

        // If empty string is sent, use value 'ALL'
        if ($riskStatus === '') {
            $riskStatus = 'ALL';
        }

        $this->riskStatus = $riskStatus;
    }

    /**
     * Get risk value
     *
     * @return string
     */
    public function getRiskStatus()
    {
        return $this->riskStatus;
    }

    /**
     * Set priority value
     *
     * @param int $priority
     *
     * @throws Exception
     */
    public function setPriority(int $priority)
    {
        // Validate the priority value
        if (!empty($priority)) {
            if (!empty(self::PRIORITIES[$priority])) {
                $priorityCode = $priority;
            } else {
                throw new Exception("Priority value {$priority} is not valid.");
            }
        } else {
            // List all priorities
            $priorityCode = 0;
        }

        $this->priority = $priorityCode;
    }

    /**
     * Get priority status
     *
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set Case status
     *
     * @param string $caseStatus
     *
     * @throws Exception
     */
    public function setCaseStatus(string $caseStatus)
    {
        // Convert the value to upper case
        $caseStatus = strtoupper($caseStatus);

        // Validate the case status
        if (!in_array($caseStatus, self::CASE_STATUSES)) {
            throw new Exception("Case status '{$caseStatus}' is not valid.");
        }

        // If empty string is sent, use value 'ALL'
        if ($caseStatus === '') {
            $caseStatus = 'ALL';
        }

        // Fix the canceled status, this is a legacy code error
        if ($caseStatus === self::INCORRECT_CANCELED_STATUS) {
            $caseStatus = self::CORRECT_CANCELED_STATUS;
        }

        $this->caseStatus = $caseStatus;
    }

    /**
     * Get Case Status
     *
     * @return string
     */
    public function getCaseStatus()
    {
        return $this->caseStatus;
    }

    /**
     * Set Case Uid
     *
     * @param string $caseUid
     */
    public function setCaseUid(string $caseUid)
    {
        $this->caseUid = $caseUid;
    }

    /**
     * Get Case Uid
     *
     * @return string
     */
    public function getCaseUid()
    {
        return $this->caseUid;
    }

    /**
     * Set Case Number
     *
     * @param int $caseNumber
     */
    public function setCaseNumber(int $caseNumber)
    {
        $this->caseNumber = $caseNumber;
    }

    /**
     * Get Case Number
     *
     * @return int
     */
    public function getCaseNumber()
    {
        return $this->caseNumber;
    }

    /**
     * Set range of Case Number
     *
     * @param int $from
     * @param int $to
     */
    public function setRangeCaseNumber(int $from, int $to)
    {
        $this->fromCaseNumber = $from;
        $this->toCaseNumber = $to;
    }

    /**
     * Get from Case Number
     *
     * @return int
     */
    public function getFromCaseNumber()
    {
        return $this->fromCaseNumber;
    }

    /**
     * Get to Case Number
     *
     * @return int
     */
    public function getToCaseNumber()
    {
        return $this->toCaseNumber;
    }

    /**
     * Set Cases Uids
     *
     * @param array $casesUid
     */
    public function setCasesUids(array $casesUid)
    {
        $this->casesUids = $casesUid;
    }

    /**
     * Get Cases Uids
     *
     * @return array
     */
    public function getCasesUids()
    {
        return $this->casesUids;
    }

    /**
     * Set Cases Numbers
     *
     * @param array $casesNumbers
     */
    public function setCasesNumbers(array $casesNumbers)
    {
        $this->casesNumbers = $casesNumbers;
    }

    /**
     * Get Cases Numbers
     *
     * @return array
     */
    public function getCasesNumbers()
    {
        return $this->casesNumbers;
    }

    /**
     * Set Newest Than value
     *
     * @param string $newestThan
     *
     * @throws Exception
     */
    public function setNewestThan(string $newestThan)
    {
        if (!Validator::isDate($newestThan, 'Y-m-d')) {
            throw new Exception("Value '{$newestThan}' is not a valid date.");
        }
        $this->newestThan = $newestThan;
    }

    /**
     * Get Newest Than value
     *
     * @return string
     */
    public function getNewestThan()
    {
        return $this->newestThan;
    }

    /**
     * Set Oldest Than value
     *
     * @param string $oldestThan
     *
     * @throws Exception
     */
    public function setOldestThan(string $oldestThan)
    {
        if (!Validator::isDate($oldestThan, 'Y-m-d')) {
            throw new Exception("Value '{$oldestThan}' is not a valid date.");
        }
        $this->oldestThan = $oldestThan;
    }

    /**
     * Get Oldest Than value
     *
     * @return string
     */
    public function getOldestThan()
    {
        return $this->oldestThan;
    }

    /**
     * Set order by column
     *
     * @param string $orderByColumn
     */
    public function setOrderByColumn(string $orderByColumn)
    {
        // Convert the value to upper case
        $orderByColumn = strtoupper($orderByColumn);

        $this->orderByColumn = $orderByColumn;
    }

    /**
     * Get order by column
     *
     * @return string
     */
    public function getOrderByColumn()
    {
        return $this->orderByColumn;
    }

    /**
     * Set order direction
     *
     * @param string $orderDirection
     *
     * @throws Exception
     */
    public function setOrderDirection(string $orderDirection)
    {
        // Convert the value to upper case
        $orderDirection = strtoupper($orderDirection);

        // Validate the order direction
        if (!in_array($orderDirection, self::ORDER_DIRECTIONS)) {
            throw new Exception("Order direction '{$orderDirection}' is not valid.");
        }

        $this->orderDirection = $orderDirection;
    }

    /**
     * Get order direction
     *
     * @return string
     */
    public function getOrderDirection()
    {
        return $this->orderDirection;
    }

    /**
     * Set if is paged
     *
     * @param bool $paged
     */
    public function setPaged(bool $paged)
    {
        $this->paged = (bool) $paged;
    }

    /**
     * Get if is paged
     *
     * @return bool
     */
    public function getPaged()
    {
        return $this->paged;
    }

    /**
     * Set offset value
     *
     * @param int $offset
     */
    public function setOffset(int $offset)
    {
        $this->offset = (int) $offset;
    }

    /**
     * Get offset value
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Set limit value
     *
     * @param int $limit
     */
    public function setLimit(int $limit)
    {
        $this->limit = (int) $limit;
    }

    /**
     * Get limit value
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Get task color according the due date
     *
     * @param string $dueDate
     *
     * @return int
     */
    public function getTaskColor(string $dueDate)
    {
        $currentDate = new DateTime('now');
        $dueDate = new DateTime($dueDate);
        if ($dueDate > $currentDate) {
            $taskColor = self::COLOR_OVERDUE;
        } else {
            $taskColor = self::COLOR_ON_TIME;
            if (get_class($this) === Draft::class) {
                $taskColor = self::COLOR_DRAFT;
            }
            if (get_class($this) === Paused::class) {
                $taskColor = self::COLOR_PAUSED;
            }
            if (get_class($this) === Unassigned::class) {
                $taskColor = self::COLOR_UNASSIGNED;
            }
        }

        return $taskColor;
    }

    /**
     * Set all properties
     *
     * @param array $properties
     */
    public function setProperties(array $properties)
    {
        // Filter by category
        if (!empty($properties['category'])) {
            $this->setCategoryUid($properties['category']);
        }
        // Filter by process
        if (!empty($properties['process'])) {
            $this->setProcessId($properties['process']);
        }
        // Filter by task
        if (!empty($properties['task'])) {
            $this->setTaskId($properties['task']);
        }
        // Filter by user
        if (!empty($properties['user'])) {
            $this->setUserId($properties['user']);
        }
        // Filter by priority
        if (!empty($properties['priority'])) {
            $this->setPriority($properties['priority']);
        }
        // Filter by case number
        if (!empty($properties['caseNumber'])) {
            $this->setCaseNumber($properties['caseNumber']);
        }
        // Filter by range of case number
        if (!empty($properties['caseNumberFrom']) && !empty($properties['caseNumberTo'])) {
            $this->setRangeCaseNumber($properties['caseNumberFrom'], $properties['caseNumberTo']);
        }
        // Filter by search
        if (!empty($properties['search'])) {
            $this->setValueToSearch($properties['search']);
        }
        // My cases filter: started, in-progress, completed, supervising
        if (!empty($properties['filter']) && get_class($this) === MyCases::class) {
            $this->setParticipatedStatus($properties['filter']);
        }
        // Filter by case status
        if (!empty($properties['filterStatus']) && get_class($this) === MyCases::class) {
            $this->setCaseStatus($properties['filterStatus']);
        }
        // Filter by case status
        if (!empty($properties['filterStatus']) && get_class($this) === Search::class) {
            $this->setCaseStatus($properties['filterStatus']);
        }
        // Filter by case uid
        if (!empty($properties['caseLink'])) {
            $this->setCaseUid($properties['caseLink']);
        }
        // Filter by array of case uids
        if (!empty($properties['appUidCheck'])) {
            $this->setCasesUids($properties['appUidCheck']);
        }
        // Filter date newest related to delegation date
        if (!empty($properties['newestthan'])) {
            $this->setNewestThan($properties['newestthan']);
        }
        // Filter date oldest related to delegation date
        if (!empty($properties['oldestthan'])) {
            $this->setOldestThan($properties['oldestthan']);
        }
        // Sort column
        if (!empty($properties['sort'])) {
            $this->setOrderByColumn($properties['sort']);
        }
        // Direction column
        if (!empty($properties['dir'])) {
            $this->setOrderDirection($properties['dir']);
        }
        // Paged
        if (!empty($properties['paged'])) {
            $this->setPaged($properties['paged']);
        }
        // Start
        if (!empty($properties['start'])) {
            $this->setOffset($properties['start']);
        }
        // Limit
        if (!empty($properties['limit'])) {
            $this->setLimit($properties['limit']);
        }
    }

    /**
     * Get the list data
     *
     * @throws Exception
     */
    public function getData()
    {
        throw new Exception("Method '" . __FUNCTION__ . "' should be implemented in the extended class '" . get_class($this) . "'.");
    }

    /**
     * Get the list counter
     *
     * @throws Exception
     */
    public function getCounter()
    {
        throw new Exception("Method '" . __FUNCTION__ . "' should be implemented in the extended class '" . get_class($this) . "'.");
    }
}
