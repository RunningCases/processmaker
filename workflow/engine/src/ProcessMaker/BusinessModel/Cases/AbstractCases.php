<?php

namespace ProcessMaker\BusinessModel\Cases;

use Datetime;
use Exception;
use ProcessMaker\BusinessModel\Interfaces\CasesInterface;
use ProcessMaker\BusinessModel\Validator;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\User;

class AbstractCases implements CasesInterface
{
    // Constants for validate values
    const INBOX_STATUSES = ['READ', 'UNREAD'];
    const PARTICIPATED_STATUSES = ['STARTED', 'IN_PROGRESS', 'COMPLETED', 'SUPERVISING'];
    const RISK_STATUSES = ['ON_TIME', 'AT_RISK', 'OVERDUE'];
    const CASE_STATUSES = [1 => 'DRAFT', 2 => 'TO_DO', 3 => 'COMPLETED', 4 => 'CANCELED'];
    const ORDER_DIRECTIONS = ['DESC', 'ASC'];
    const CORRECT_CANCELED_STATUS = 'CANCELED';
    const INCORRECT_CANCELED_STATUS = 'CANCELLED';
    const PRIORITIES = [1 => 'VL', 2 => 'L', 3 => 'N', 4 => 'H', 5 => 'VH'];
    // Task Colors
    const TASK_COLORS = [1 => 'green', 2 => 'red', 3 => 'orange', 4 => 'blue', 5 => 'gray'];
    const COLOR_OVERDUE = 1;
    const COLOR_ON_TIME = 2;
    const COLOR_DRAFT = 3;
    const COLOR_PAUSED = 4;
    const COLOR_UNASSIGNED = 5;
    // Status values
    const STATUS_DRAFT = 1;
    const STATUS_TODO = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCELED = 4;

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

    // Filter by specific priorities
    private $priorities = [];

    // Filter by case status, know as "$filterStatus" in the old "participated last" class
    private $caseStatus = '';

    // Filter by case statuses
    private $caseStatuses = [1, 2, 3, 4];

    // Filter by a specific case, know as "$caseLink" in the old lists classes
    private $caseUid = '';

    // Filter by a specific case using case number
    private $caseNumber = 0;

    // Filter by specific cases using the case numbers like [1,4,8]
    private $casesNumbers = [];

    // Filter by only one range of case number
    private $caseNumberFrom = 0;
    private $caseNumberTo = 0;

    // Filter more than one range of case number
    private $rangeCasesFromTo = [];

    // Filter by a specific cases like 1,3-5,8,10-15
    private $filterCases = '';

    // Filter by a specific case title
    private $caseTitle = '';

    // Filter by specific cases, know as "$appUidCheck" in the old lists classes
    private $casesUids = [];

    // Filter range related to the start case date
    private $startCaseFrom = '';
    private $startCaseTo = '';

    // Filter range related to the finish case date
    private $finishCaseFrom = '';
    private $finishCaseTo = '';

    // Filter range related to the delegate date
    private $delegateFrom = '';
    private $delegateTo = '';

    // Filter range related to the finish date
    private $finishFrom = '';
    private $finishTo = '';

    // Filter range related to the due date
    private $dueFrom = '';
    private $dueTo = '';

    // Column by which the results will be sorted, know as "$sort" in the old lists classes
    private $orderByColumn = 'APP_NUMBER';

    // Sorts the data in descending or ascending order, know as "$dir" in the old lists classes
    private $orderDirection = 'DESC';

    // Results should be paged?
    private $paged = true;

    // Offset is used to identify the starting point to return rows from a result set, know as "$start" in the old lists classes
    private $offset = 0;

    // Number of rows to return
    private $limit = 15;

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
     * @param string $priority
     *
     * @throws Exception
     */
    public function setPriority(string $priority)
    {
        // Validate the priority value
        if (!empty($priority)) {
            $priorityCode = array_search($priority, self::PRIORITIES);
            if (empty($priorityCode) && $priorityCode !== 0) {
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
     * Set priorities
     *
     * @param array $priorities
     *
     * @throws Exception
     */
    public function setPriorities(array $priorities)
    {
        $prioritiesCode = [];
        foreach ($priorities as $priority) {
            // Validate the priority value
            $priorityCode = array_search($priority, self::PRIORITIES);
            if (empty($priorityCode) && $priorityCode !== 0) {
                throw new Exception("Priority value {$priority} is not valid.");
            } else {
                array_push($prioritiesCode, $priorityCode);
            }
        }
        $this->priorities = $prioritiesCode;
    }

    /**
     * Get priorities
     *
     * @return array
     */
    public function getPriorities()
    {
        return $this->priorities;
    }

    /**
     * Set Case status
     *
     * @param string $status
     *
     * @throws Exception
     */
    public function setCaseStatus(string $status)
    {
        // Fix the canceled status, this is a legacy code error
        if ($status === self::INCORRECT_CANCELED_STATUS) {
            $status = self::CORRECT_CANCELED_STATUS;
        }
        $statusCode = 0;
        // Validate the status value
        if (!empty($status)) {
            $statusCode = array_search($status, self::CASE_STATUSES);
            if (empty($statusCode) && $statusCode !== 0) {
                throw new Exception("Case status '{$status}' is not valid.");
            }
        }
        $this->caseStatus = $statusCode;
    }

    /**
     * Get Case Status
     *
     * @return int
     */
    public function getCaseStatus()
    {
        return $this->caseStatus;
    }

    /**
     * Set Case statuses
     *
     * @param array $statuses
     *
     * @throws Exception
     */
    public function setCaseStatuses(array $statuses)
    {
        $statusCodes = [];
        foreach ($statuses as $status) {
            // Fix the canceled status, this is a legacy code error
            if ($status === self::INCORRECT_CANCELED_STATUS) {
                $status = self::CORRECT_CANCELED_STATUS;
            }
            // Validate the status value
            if (!empty($status)) {
                $statusCode = array_search($status, self::CASE_STATUSES);
                if (empty($statusCode) && $statusCode !== 0) {
                    throw new Exception("Case status '{$status}' is not valid.");
                } else {
                    array_push($statusCodes, $statusCode);
                }
            }
        }
        $this->caseStatuses = $statusCodes;
    }

    /**
     * Get Case Statuses
     *
     * @return array
     */
    public function getCaseStatuses()
    {
        return $this->caseStatuses;
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
     * Set range of case number from
     *
     * @param int $from
     */
    public function setCaseNumberFrom(int $from)
    {
        $this->caseNumberFrom = $from;
    }

    /**
     * Get from Case Number
     *
     * @return int
     */
    public function getCaseNumberFrom()
    {
        return $this->caseNumberFrom;
    }

    /**
     * Set range of case number to
     *
     * @param int $to
     */
    public function setCaseNumberTo(int $to)
    {
        $this->caseNumberTo = $to;
    }

    /**
     * Get to Case Number
     *
     * @return int
     */
    public function getCaseNumberTo()
    {
        return $this->caseNumberTo;
    }

    /**
     * Set more than one range of cases
     *
     * @param array $rangeCases
     */
    public function setRangeCasesFromTo(array $rangeCases)
    {
        $this->rangeCasesFromTo = $rangeCases;
    }

    /**
     * Get more than one range of cases
     *
     * @return array
     */
    public function getRangeCasesFromTo()
    {
        return $this->rangeCasesFromTo;
    }

    /**
     * Set filter of cases like '1,3-5,8,10-15'
     *
     * @param string $filterCases
     */
    public function setFilterCases(string $filterCases)
    {
        $this->filterCases = $filterCases;
        // Review the cases defined in the filter
        $rangeOfCases = explode(",", $filterCases);
        $specificCases = [];
        $rangeCases = [];
        foreach ($rangeOfCases as $cases) {
            if(is_numeric($cases)) {
                array_push($specificCases,$cases);
            } else {
                array_push($rangeCases,$cases);
            }
        }
        $this->setCasesNumbers($specificCases);
        $this->setRangeCasesFromTo($rangeCases);
    }

    /**
     * Get filter of cases
     *
     * @return string
     */
    public function getFilterCases()
    {
        return $this->filterCases;
    }

    /**
     * Set Case Title
     *
     * @param string $caseTitle
     */
    public function setCaseTitle(string $caseTitle)
    {
        $this->caseTitle = $caseTitle;
    }

    /**
     * Get Case Title
     *
     * @return string
     */
    public function getCaseTitle()
    {
        return $this->caseTitle;
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
     * Set start case from
     *
     * @param string $from
     *
     * @throws Exception
     */
    public function setStartCaseFrom(string $from)
    {
        if (!Validator::isDate($from, 'Y-m-d')) {
            throw new Exception("Value '{$from}' is not a valid date.");
        }
        $this->startCaseFrom = $from;
    }

    /**
     * Get start case from
     *
     * @return string
     */
    public function getStartCaseFrom()
    {
        return $this->startCaseFrom;
    }

    /**
     * Set start case to
     *
     * @param string $to
     *
     * @throws Exception
     */
    public function setStartCaseTo(string $to)
    {
        if (!Validator::isDate($to, 'Y-m-d')) {
            throw new Exception("Value '{$to}' is not a valid date.");
        }
        $this->startCaseTo = $to;
    }

    /**
     * Get start case to
     *
     * @return string
     */
    public function getStartCaseTo()
    {
        return $this->startCaseTo;
    }

    /**
     * Set finish case from
     *
     * @param string $from
     *
     * @throws Exception
     */
    public function setFinishCaseFrom(string $from)
    {
        if (!Validator::isDate($from, 'Y-m-d')) {
            throw new Exception("Value '{$from}' is not a valid date.");
        }
        $this->finishCaseFrom = $from;
    }

    /**
     * Get start case from
     *
     * @return string
     */
    public function getFinishCaseFrom()
    {
        return $this->finishCaseFrom;
    }

    /**
     * Set start case to
     *
     * @param string $to
     *
     * @throws Exception
     */
    public function setFinishCaseTo(string $to)
    {
        if (!Validator::isDate($to, 'Y-m-d')) {
            throw new Exception("Value '{$to}' is not a valid date.");
        }
        $this->finishCaseTo = $to;
    }

    /**
     * Get start case to
     *
     * @return string
     */
    public function getFinishCaseTo()
    {
        return $this->finishCaseTo;
    }

    /**
     * Set Newest Than value
     *
     * @param string $delegateFrom
     *
     * @throws Exception
     */
    public function setDelegateFrom(string $delegateFrom)
    {
        if (!Validator::isDate($delegateFrom, 'Y-m-d')) {
            throw new Exception("Value '{$delegateFrom}' is not a valid date.");
        }
        $this->delegateFrom = $delegateFrom;
    }

    /**
     * Get Newest Than value
     *
     * @return string
     */
    public function getDelegateFrom()
    {
        return $this->delegateFrom;
    }

    /**
     * Set Oldest Than value
     *
     * @param string $delegateTo
     *
     * @throws Exception
     */
    public function setDelegateTo(string $delegateTo)
    {
        if (!Validator::isDate($delegateTo, 'Y-m-d')) {
            throw new Exception("Value '{$delegateTo}' is not a valid date.");
        }
        $this->delegateTo = $delegateTo;
    }

    /**
     * Get Oldest Than value
     *
     * @return string
     */
    public function getDelegateTo()
    {
        return $this->delegateTo;
    }

    /**
     * Set finish date value
     *
     * @param string $from
     *
     * @throws Exception
     */
    public function setFinishFrom(string $from)
    {
        if (!Validator::isDate($from, 'Y-m-d')) {
            throw new Exception("Value '{$from}' is not a valid date.");
        }
        $this->finishFrom = $from;
    }

    /**
     * Get finish date value
     *
     * @return string
     */
    public function getFinishFrom()
    {
        return $this->finishFrom;
    }

    /**
     * Set finish date value
     *
     * @param string $to
     *
     * @throws Exception
     */
    public function setFinishTo(string $to)
    {
        if (!Validator::isDate($to, 'Y-m-d')) {
            throw new Exception("Value '{$to}' is not a valid date.");
        }
        $this->finishTo = $to;
    }

    /**
     * Get finish date value
     *
     * @return string
     */
    public function getFinishTo()
    {
        return $this->finishTo;
    }

    /**
     * Set due date from
     *
     * @param string $dueFrom
     *
     * @throws Exception
     */
    public function setDueFrom(string $dueFrom)
    {
        if (!Validator::isDate($dueFrom, 'Y-m-d')) {
            throw new Exception("Value '{$dueFrom}' is not a valid date.");
        }
        $this->dueFrom = $dueFrom;
    }

    /**
     * Get due date from
     *
     * @return string
     */
    public function getDueFrom()
    {
        return $this->dueFrom;
    }

    /**
     * Set due date to
     *
     * @param string $dueTo
     *
     * @throws Exception
     */
    public function setDueTo(string $dueTo)
    {
        if (!Validator::isDate($dueTo, 'Y-m-d')) {
            throw new Exception("Value '{$dueTo}' is not a valid date.");
        }
        $this->dueTo = $dueTo;
    }

    /**
     * Get due date to
     *
     * @return string
     */
    public function getDueTo()
    {
        return $this->dueTo;
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
     * Get task color according the due date
     *
     * @param string $pending
     *
     * @return int
     */
    public function prepareTaskPending($pending)
    {
        $taskPending = json_decode($pending, true);
        $result = [];
        $i = 0;
        foreach ($taskPending as $thread) {
            foreach ($thread as $key => $row) {
                if($key === 'tas_id') {
                    $result[$i][$key] = $row;
                    $result[$i]['tas_title'] = (!empty($row)) ? Task::where('TAS_ID', $row)->first()->TAS_TITLE : '';
                }
                if($key === 'user_id') {
                    $result[$i][$key] = $row;
                }
                if($key === 'due_date') {
                    $result[$i][$key] = $row;
                    // Get task color label
                    $result[$i]['tas_color'] = (!empty($row)) ? $this->getTaskColor($row) : '';
                    $result[$i]['tas_color_label'] = (!empty($row)) ? self::TASK_COLORS[$result[$i]['tas_color']] : '';
                }
            }
            $i ++;
        }

        return $result;
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
        // Filter by one case number
        if (!empty($properties['caseNumber'])) {
            $this->setCaseNumber($properties['caseNumber']);
        }
        // Filter by case title
        if (!empty($properties['caseTitle'])) {
            $this->setCaseTitle($properties['caseTitle']);
        }
        /** Apply filters related to MY CASES */
        // My cases filter: started, in-progress, completed, supervising
        if (get_class($this) === Participated::class && !empty($properties['filter'])) {
            $this->setParticipatedStatus($properties['filter']);
        }
        // Filter by one case status
        if (get_class($this) === Participated::class && !empty($properties['caseStatus'])) {
            $this->setCaseStatus($properties['caseStatus']);
        }
        // Filter date related to started date from
        if ((get_class($this) === Participated::class || get_class($this) === Supervising::class) && !empty($properties['startCaseFrom'])) {
            $this->setStartCaseFrom($properties['startCaseFrom']);
        }
        // Filter date related to started date to
        if ((get_class($this) === Participated::class || get_class($this) === Supervising::class) && !empty($properties['startCaseTo'])) {
            $this->setStartCaseTo($properties['startCaseTo']);
        }
        // Filter date related to finish date from
        if ((get_class($this) === Participated::class || get_class($this) === Supervising::class) && !empty($properties['finishCaseFrom'])) {
            $this->setFinishCaseFrom($properties['finishCaseFrom']);
        }
        //  Filter date related to finish date to
        if ((get_class($this) === Participated::class || get_class($this) === Supervising::class) && !empty($properties['finishCaseTo'])) {
            $this->setFinishCaseTo($properties['finishCaseTo']);
        }
        /** Apply filters related to SEARCH */
        // Add a filter with specific cases or range of cases like '1, 3-5, 8, 10-15'
        if (get_class($this) === Search::class && !empty($properties['filterCases'])) {
            $this->setFilterCases($properties['filterCases']);
        }
        // Filter by more than one case statuses like ['DRAFT', 'TO_DO']
        if (get_class($this) === Search::class && !empty($properties['caseStatuses'])) {
            $this->setCaseStatuses($properties['caseStatuses']);
        }
        // Filter by more than one priorities like ['VL', 'L', 'N']
        if (get_class($this) === Search::class && !empty($properties['priorities'])) {
            $this->setProperties($properties['priorities']);
        }
        // Filter date newest related to delegation/started date
        if (get_class($this) === Search::class && !empty($properties['delegationDateFrom'])) {
            $this->setDelegateFrom($properties['delegationDateFrom']);
        }
        // Filter date oldest related to delegation/started date
        if (get_class($this) === Search::class && !empty($properties['delegationDateTo'])) {
            $this->setDelegateTo($properties['delegationDateTo']);
        }
        // Filter date newest related to due date
        if (get_class($this) === Search::class && !empty($properties['dueDateFrom'])) {
            $this->setDueFrom($properties['dueDateFrom']);
        }
        // Filter date oldest related to due date
        if (get_class($this) === Search::class && !empty($properties['dueDateTo'])) {
            $this->setDueTo($properties['dueDateTo']);
        }
        // Filter by case uid
        if (!empty($properties['caseLink'])) {
            $this->setCaseUid($properties['caseLink']);
        }
        // Filter by array of case uids
        if (!empty($properties['appUidCheck'])) {
            $this->setCasesUids($properties['appUidCheck']);
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
