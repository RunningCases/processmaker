<?php

namespace ProcessMaker\BusinessModel\Cases;

use ProcessMaker\BusinessModel\Cases\Draft;
use ProcessMaker\BusinessModel\Cases\Inbox;
use ProcessMaker\BusinessModel\Cases\Paused;
use ProcessMaker\BusinessModel\Cases\Unassigned;
use ProcessMaker\Model\CaseList;
use ProcessMaker\Model\User;
use ProcessMaker\Util\DateTime;

class Home
{
    /**
     * This is the userId field.
     * @var string
     */
    private $userId = '';

    /**
     * Constructor of the class.
     * @param type $userId
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Get the userId field.
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Get the draft cases.
     * @param int $caseNumber
     * @param int $process
     * @param int $task
     * @param int $limit
     * @param int $offset
     * @param string $caseTitle
     * @param string $filterCases
     * @param string $sort
     * @param callable $callback
     * @return array
     */
    public function getDraft(
        int $caseNumber = 0,
        int $process = 0,
        int $task = 0,
        int $limit = 15,
        int $offset = 0,
        string $caseTitle = '',
        string $filterCases = '',
        string $sort = 'APP_NUMBER,DESC',
        callable $callback = null
    )
    {
        $list = new Draft();
        // Define the filters to apply
        $properties = [];
        $properties['caseNumber'] = $caseNumber;
        $properties['caseTitle'] = $caseTitle;
        $properties['filterCases'] = $filterCases;
        $properties['process'] = $process;
        $properties['task'] = $task;
        // Get the user that access to the API
        $usrUid = $this->getUserId();
        $properties['user'] = !empty($usrUid) ? User::getId($usrUid) : 0;
        $properties['start'] = $offset;
        $properties['limit'] = $limit;
        // Set the sort parameters
        $sort = explode(',', $sort);
        $properties['sort'] = $sort[0];
        $properties['dir'] = $sort[1];
        $list->setProperties($properties);
        $result = [];
        $result['data'] = DateTime::convertUtcToTimeZone($list->getData($callback));
        $result['total'] = $list->getPagingCounters();
        return $result;
    }

    /**
     * Get the inbox cases.
     * @param int $caseNumber
     * @param int $process
     * @param int $task
     * @param int $limit
     * @param int $offset
     * @param string $caseTitle
     * @param string $delegateFrom
     * @param string $delegateTo
     * @param string $filterCases
     * @param string $sort
     * @param callable $callback
     * @return array
     */
    public function getInbox(
        int $caseNumber = 0,
        int $process = 0,
        int $task = 0,
        int $limit = 15,
        int $offset = 0,
        string $caseTitle = '',
        string $delegateFrom = '',
        string $delegateTo = '',
        string $filterCases = '',
        string $sort = 'APP_NUMBER,DESC',
        callable $callback = null
    )
    {
        $list = new Inbox();
        // Define the filters to apply
        $properties = [];
        $properties['caseNumber'] = $caseNumber;
        $properties['caseTitle'] = $caseTitle;
        $properties['delegateFrom'] = $delegateFrom;
        $properties['delegateTo'] = $delegateTo;
        $properties['filterCases'] = $filterCases;
        $properties['process'] = $process;
        $properties['task'] = $task;
        // Get the user that access to the API
        $usrUid = $this->getUserId();
        $properties['user'] = !empty($usrUid) ? User::getId($usrUid) : 0;
        $properties['start'] = $offset;
        $properties['limit'] = $limit;
        // Set the pagination parameters
        $sort = explode(',', $sort);
        $properties['sort'] = $sort[0];
        $properties['dir'] = $sort[1];
        $list->setProperties($properties);
        $result = [];
        $result['data'] = DateTime::convertUtcToTimeZone($list->getData($callback));
        $result['total'] = $list->getPagingCounters();
        return $result;
    }

    /**
     * Get the unassigned cases.
     * @param int $caseNumber
     * @param int $process
     * @param int $task
     * @param int $limit
     * @param int $offset
     * @param string $caseTitle
     * @param string $delegateFrom
     * @param string $delegateTo
     * @param string $filterCases
     * @param string $sort
     * @param callable $callback
     * @return array
     */
    public function getUnassigned(
        int $caseNumber = 0,
        int $process = 0,
        int $task = 0,
        int $limit = 15,
        int $offset = 0,
        string $caseTitle = '',
        string $delegateFrom = '',
        string $delegateTo = '',
        string $filterCases = '',
        string $sort = 'APP_NUMBER,DESC',
        callable $callback = null
    )
    {
        $list = new Unassigned();
        // Define the filters to apply
        $properties = [];
        $properties['caseNumber'] = $caseNumber;
        $properties['caseTitle'] = $caseTitle;
        $properties['delegateFrom'] = $delegateFrom;
        $properties['delegateTo'] = $delegateTo;
        $properties['filterCases'] = $filterCases;
        $properties['process'] = $process;
        $properties['task'] = $task;
        // Get the user that access to the API
        $usrUid = $this->getUserId();
        $properties['user'] = !empty($usrUid) ? User::getId($usrUid) : 0;
        $properties['start'] = $offset;
        $properties['limit'] = $limit;
        // Set the sort parameters
        $sort = explode(',', $sort);
        $properties['sort'] = $sort[0];
        $properties['dir'] = $sort[1];
        // todo: some queries related to the unassigned are using the USR_UID
        $list->setUserUid($usrUid);
        $list->setProperties($properties);
        $result = [];
        $result['data'] = DateTime::convertUtcToTimeZone($list->getData($callback));
        $result['total'] = $list->getPagingCounters();
        return $result;
    }

    /**
     * Get the paused cases.
     * @param int $caseNumber
     * @param int $process
     * @param int $task
     * @param int $limit
     * @param int $offset
     * @param string $caseTitle
     * @param string $delegateFrom
     * @param string $delegateTo
     * @param string $filterCases
     * @param string $sort
     * @param callable $callback
     * @return array
     */
    public function getPaused(
        int $caseNumber = 0,
        int $process = 0,
        int $task = 0,
        int $limit = 15,
        int $offset = 0,
        string $caseTitle = '',
        string $delegateFrom = '',
        string $delegateTo = '',
        string $filterCases = '',
        string $sort = 'APP_NUMBER,DESC',
        callable $callback = null
    )
    {
        $list = new Paused();
        // Define the filters to apply
        $properties = [];
        $properties['caseNumber'] = $caseNumber;
        $properties['caseTitle'] = $caseTitle;
        $properties['delegateFrom'] = $delegateFrom;
        $properties['delegateTo'] = $delegateTo;
        $properties['filterCases'] = $filterCases;
        $properties['process'] = $process;
        $properties['task'] = $task;
        // Get the user that access to the API
        $usrUid = $this->getUserId();
        $properties['user'] = !empty($usrUid) ? User::getId($usrUid) : 0;
        $properties['start'] = $offset;
        $properties['limit'] = $limit;
        // Set the sort parameters
        $sort = explode(',', $sort);
        $properties['sort'] = $sort[0];
        $properties['dir'] = $sort[1];
        $list->setProperties($properties);
        $result = [];
        $result['data'] = DateTime::convertUtcToTimeZone($list->getData($callback));
        $result['total'] = $list->getPagingCounters();
        return $result;
    }

    /**
     * Build the columns and data from the custom list.
     * @param string $type
     * @param int $id
     * @param array $arguments
     * @param array $defaultColumns
     */
    public function buildCustomCaseList(string $type, int $id, array &$arguments, array &$defaultColumns)
    {
        $caseList = CaseList::where('CAL_TYPE', '=', $type)
            ->where('CAL_ID', '=', $id)
            ->join('ADDITIONAL_TABLES', 'ADDITIONAL_TABLES.ADD_TAB_UID', '=', 'CASE_LIST.ADD_TAB_UID')
            ->get()
            ->first();
        if (!empty($caseList)) {
            $tableName = $caseList->ADD_TAB_NAME;

            //this gets the configured columns
            $columns = json_decode($caseList->CAL_COLUMNS);
            $columns = CaseList::formattingColumns($type, $caseList->ADD_TAB_UID, $columns);

            //this gets the visible columns from the custom List and the fields from the table
            if (!empty($columns)) {
                $defaultColumns = [];
            }
            $fields = [];
            foreach ($columns as $value) {
                if ($value['set'] === true) {
                    $defaultColumns[] = $value;
                    if ($value['source'] === $tableName) {
                        $fields[] = $value['field'];
                    }
                }
            }

            //this modifies the query
            if (!empty($tableName) && !empty($fields)) {
                $arguments[] = function ($query) use ($tableName, $fields) {
                    $query->leftJoin($tableName, "{$tableName}.APP_UID", "=", "APP_DELEGATION.APP_UID");
                    foreach ($fields as $value) {
                        $query->addSelect($value);
                    }
                };
            }
        }
    }

    /**
     * Get the custom draft cases.
     * @param int $id
     * @param int $caseNumber
     * @param int $process
     * @param int $task
     * @param int $limit
     * @param int $offset
     * @param string $caseTitle
     * @param string $filterCases
     * @param string $sort
     * @return array
     */
    public function getCustomDraft(
        int $id,
        int $caseNumber = 0,
        int $process = 0,
        int $task = 0,
        int $limit = 15,
        int $offset = 0,
        string $caseTitle = '',
        string $filterCases = '',
        string $sort = 'APP_NUMBER,DESC'
    )
    {
        $arguments = func_get_args();
        array_shift($arguments);

        $type = 'draft';
        $defaultColumns = CaseList::formattingColumns($type, '', []);
        $this->buildCustomCaseList($type, $id, $arguments, $defaultColumns);

        $result = $this->getDraft(...$arguments);
        $result['columns'] = $defaultColumns;
        return $result;
    }

    /**
     * Get the custom inbox cases.
     * @param int $id
     * @param int $caseNumber
     * @param int $process
     * @param int $task
     * @param int $limit
     * @param int $offset
     * @param string $caseTitle
     * @param string $delegateFrom
     * @param string $delegateTo
     * @param string $filterCases
     * @param string $sort
     * @return array
     */
    public function getCustomInbox(
        int $id,
        int $caseNumber = 0,
        int $process = 0,
        int $task = 0,
        int $limit = 15,
        int $offset = 0,
        string $caseTitle = '',
        string $delegateFrom = '',
        string $delegateTo = '',
        string $filterCases = '',
        string $sort = 'APP_NUMBER,DESC'
    )
    {
        $arguments = func_get_args();
        array_shift($arguments);

        $type = 'inbox';
        $defaultColumns = CaseList::formattingColumns($type, '', []);
        $this->buildCustomCaseList($type, $id, $arguments, $defaultColumns);

        $result = $this->getInbox(...$arguments);
        $result['columns'] = $defaultColumns;
        return $result;
    }

    /**
     * Get the custom unassigned cases.
     * @param int $id
     * @param int $caseNumber
     * @param int $process
     * @param int $task
     * @param int $limit
     * @param int $offset
     * @param string $caseTitle
     * @param string $delegateFrom
     * @param string $delegateTo
     * @param string $filterCases
     * @param string $sort
     * @return array
     */
    public function getCustomUnassigned(
        int $id,
        int $caseNumber = 0,
        int $process = 0,
        int $task = 0,
        int $limit = 15,
        int $offset = 0,
        string $caseTitle = '',
        string $delegateFrom = '',
        string $delegateTo = '',
        string $filterCases = '',
        string $sort = 'APP_NUMBER,DESC'
    )
    {
        $arguments = func_get_args();
        array_shift($arguments);

        $type = 'unassigned';
        $defaultColumns = CaseList::formattingColumns($type, '', []);
        $this->buildCustomCaseList($type, $id, $arguments, $defaultColumns);

        $result = $this->getUnassigned(...$arguments);
        $result['columns'] = $defaultColumns;
        return $result;
    }

    /**
     * Get the custom paused cases.
     * @param int $id
     * @param int $caseNumber
     * @param int $process
     * @param int $task
     * @param int $limit
     * @param int $offset
     * @param string $caseTitle
     * @param string $delegateFrom
     * @param string $delegateTo
     * @param string $filterCases
     * @param string $sort
     * @return array
     */
    public function getCustomPaused(
        int $id,
        int $caseNumber = 0,
        int $process = 0,
        int $task = 0,
        int $limit = 15,
        int $offset = 0,
        string $caseTitle = '',
        string $delegateFrom = '',
        string $delegateTo = '',
        string $filterCases = '',
        string $sort = 'APP_NUMBER,DESC'
    )
    {
        $arguments = func_get_args();
        array_shift($arguments);

        $type = 'paused';
        $defaultColumns = CaseList::formattingColumns($type, '', []);
        $this->buildCustomCaseList($type, $id, $arguments, $defaultColumns);

        $result = $this->getPaused(...$arguments);
        $result['columns'] = $defaultColumns;
        return $result;
    }
}
