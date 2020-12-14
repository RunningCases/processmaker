<?php

namespace ProcessMaker\Services\Api;

use Exception;
use G;
use Luracast\Restler\RestException;
use Menu;
use ProcessMaker\BusinessModel\Cases\Draft;
use ProcessMaker\BusinessModel\Cases\Filter;
use ProcessMaker\BusinessModel\Cases\Inbox;
use ProcessMaker\BusinessModel\Cases\Participated;
use ProcessMaker\BusinessModel\Cases\Paused;
use ProcessMaker\BusinessModel\Cases\Search;
use ProcessMaker\BusinessModel\Cases\Supervising;
use ProcessMaker\BusinessModel\Cases\Unassigned;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\User;
use ProcessMaker\Services\Api;
use RBAC;
use stdClass;

class Home extends Api
{
    /**
     * Constructor of the class
     * We will to define the $RBAC definition
     */
    public function __construct()
    {
        global $RBAC;
        if (!isset($RBAC)) {
            $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
            $RBAC->sSystem = 'PROCESSMAKER';
            $RBAC->initRBAC();
            $RBAC->loadUserRolePermission($RBAC->sSystem, $this->getUserId());
        }
    }

    /**
     * Get the draft cases
     *
     * @url GET /draft
     *
     * @param int $caseNumber
     * @param int $process
     * @param int $task
     * @param string $caseTitle
     * @param string $paged
     * @param string $sort
     *
     * @return array
     *
     * @throws Exception
     *
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doGetDraftCases(
        int $caseNumber = 0,
        int $process = 0,
        int $task = 0,
        string $caseTitle = '',
        string $paged = '0,15',
        string $sort = 'APP_NUMBER,ASC'
    ) {
        try {
            $list = new Draft();
            // Define the filters to apply
            $properties = [];
            $properties['caseNumber'] = $caseNumber;
            $properties['caseTitle'] = $caseTitle;
            $properties['process'] = $process;
            $properties['task'] = $task;
            // Get the user that access to the API
            $usrUid = $this->getUserId();
            $properties['user'] = User::find($usrUid)->first()->USR_ID;
            // Set the pagination parameters
            $paged = explode(',', $paged);
            $sort = explode(',', $sort);
            $properties['start'] = $paged[0];
            $properties['limit'] = $paged[1];
            $properties['sort'] = $sort[0];
            $properties['dir'] = $sort[1];
            $list->setProperties($properties);
            $result = [];
            $result['data'] = $list->getData();
            $result['total'] = $list->getPagingCounters();
            return $result;
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Get the inbox cases
     *
     * @url GET /todo
     *
     * @param int $caseNumber
     * @param int $process
     * @param int $task
     * @param string $caseTitle
     * @param string $paged
     * @param string $sort
     *
     * @return array
     *
     * @throws Exception
     *
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doGetTodoCases(
        int $caseNumber = 0,
        int $process = 0,
        int $task = 0,
        string $caseTitle = '',
        string $paged = '0,15',
        string $sort = 'APP_NUMBER,ASC'
    ) {
        try {
            $list = new Inbox();
            // Define the filters to apply
            $properties = [];
            $properties['caseNumber'] = $caseNumber;
            $properties['caseTitle'] = $caseTitle;
            $properties['process'] = $process;
            $properties['task'] = $task;
            // Get the user that access to the API
            $usrUid = $this->getUserId();
            $properties['user'] = User::find($usrUid)->first()->USR_ID;
            // Set the pagination parameters
            $paged = explode(',', $paged);
            $sort = explode(',', $sort);
            $properties['start'] = $paged[0];
            $properties['limit'] = $paged[1];
            $properties['sort'] = $sort[0];
            $properties['dir'] = $sort[1];
            $list->setProperties($properties);
            $result = [];
            $result['data'] = $list->getData();
            $result['total'] = $list->getPagingCounters();
            return $result;
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Get the unassigned cases
     *
     * @url GET /unassigned
     *
     * @param int $caseNumber
     * @param int $process
     * @param int $task
     * @param string $caseTitle
     * @param string $paged
     * @param string $sort
     *
     * @return array
     *
     * @throws Exception
     *
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doGetUnassignedCases(
        int $caseNumber = 0,
        int $process = 0,
        int $task = 0,
        string $caseTitle = '',
        string $paged = '0,15',
        string $sort = 'APP_NUMBER,ASC'
    ) {
        try {
            $list = new Unassigned();
            // Define the filters to apply
            $properties = [];
            $properties['caseNumber'] = $caseNumber;
            $properties['caseTitle'] = $caseTitle;
            $properties['process'] = $process;
            $properties['task'] = $task;
            // Get the user that access to the API
            $usrUid = $this->getUserId();
            $properties['user'] = User::find($usrUid)->first()->USR_ID;
            // Set the pagination parameters
            $paged = explode(',', $paged);
            $sort = explode(',', $sort);
            $properties['start'] = $paged[0];
            $properties['limit'] = $paged[1];
            $properties['sort'] = $sort[0];
            $properties['dir'] = $sort[1];
            // todo: some queries related to the unassigned are using the USR_UID
            $list->setUserUid($usrUid);
            $list->setProperties($properties);
            $result = [];
            $result['data'] = $list->getData();
            $result['total'] = $list->getPagingCounters();
            return $result;
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Get the paused cases
     *
     * @url GET /paused
     *
     * @param int $caseNumber
     * @param int $process
     * @param int $task
     * @param string $caseTitle
     * @param string $paged
     * @param string $sort
     *
     * @return array
     *
     * @throws Exception
     *
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doGetPausedCases(
        int $caseNumber = 0,
        int $process = 0,
        int $task = 0,
        string $caseTitle = '',
        string $paged = '0,15',
        string $sort = 'APP_NUMBER,ASC'
    ) {
        try {
            $list = new Paused();
            // Define the filters to apply
            $properties = [];
            $properties['caseNumber'] = $caseNumber;
            $properties['caseTitle'] = $caseTitle;
            $properties['process'] = $process;
            $properties['task'] = $task;
            // Get the user that access to the API
            $usrUid = $this->getUserId();
            $properties['user'] = User::find($usrUid)->first()->USR_ID;
            // Set the pagination parameters
            $paged = explode(',', $paged);
            $sort = explode(',', $sort);
            $properties['start'] = $paged[0];
            $properties['limit'] = $paged[1];
            $properties['sort'] = $sort[0];
            $properties['dir'] = $sort[1];
            $list->setProperties($properties);
            $result = [];
            $result['data'] = $list->getData();
            $result['total'] = $list->getPagingCounters();
            return $result;
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Get the my cases
     *
     * @url GET /mycases
     *
     * @param int $caseNumber
     * @param int $process
     * @param int $task
     * @param string $caseTitle
     * @param string $startCaseFrom
     * @param string $startCaseTo
     * @param string $finishCaseFrom
     * @param string $finishCaseTo
     * @param string $filter
     * @param string $paged
     * @param string $sort
     *
     * @return array
     *
     * @throws Exception
     *
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doGetMyCases(
        int $caseNumber = 0,
        int $process = 0,
        int $task = 0,
        string $caseTitle = '',
        string $filter = 'IN_PROGRESS',
        string $startCaseFrom = '',
        string $startCaseTo = '',
        string $finishCaseFrom = '',
        string $finishCaseTo = '',
        string $paged = '0,15',
        string $sort = 'APP_NUMBER,ASC'
    ) {
        // Define the filters to apply
        $properties = [];
        $properties['caseNumber'] = $caseNumber;
        $properties['caseTitle'] = $caseTitle;
        $properties['process'] = $process;
        $properties['task'] = $task;
        // Get the user that access to the API
        $usrUid = $this->getUserId();
        $properties['user'] = User::find($usrUid)->first()->USR_ID;
        $properties['filter'] = $filter;
        $properties['startCaseFrom'] = $startCaseFrom;
        $properties['startCaseTo'] = $startCaseTo;
        $properties['finishCaseFrom'] = $finishCaseFrom;
        $properties['finishCaseTo'] = $finishCaseTo;
        // Set the pagination parameters
        $paged = explode(',', $paged);
        $sort = explode(',', $sort);
        $properties['start'] = $paged[0];
        $properties['limit'] = $paged[1];
        $properties['sort'] = $sort[0];
        $properties['dir'] = $sort[1];
        $result = [];
        try {
            if (!empty($filter)) {
                switch ($filter) {
                    case 'STARTED':
                    case 'IN_PROGRESS':
                    case 'COMPLETED':
                        $list = new Participated();
                        $list->setParticipatedStatus($filter);
                        $list->setProperties($properties);
                        $result['data'] = $list->getData();
                        $result['total'] = $list->getPagingCounters();
                        break;
                    case 'SUPERVISING':
                        // Scope that search for the SUPERVISING cases by specific user
                        $list = new Supervising();
                        $list->setProperties($properties);
                        $result['data'] = $list->getData();
                        $result['total'] = $list->getPagingCounters();
                        break;
                }
            }

            return $result;
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Get counters
     *
     * @url GET /counters
     *
     * @return array
     *
     * @throws Exception
     *
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doGetCountMyCases()
    {
        try {
            // Initializing variables
            $participatedStatuses = ['STARTED', 'IN_PROGRESS', 'COMPLETED', 'SUPERVISING'];
            $participatedLabels = array_combine($participatedStatuses, ['ID_OPT_STARTED', 'ID_IN_PROGRESS', 'ID_COMPLETED', 'ID_SUPERVISING']);
            $counters = [];

            // Get counters
            foreach ($participatedStatuses as $participatedStatus) {
                // Initializing counter object
                $counter = new stdClass();
                $counter->id = $participatedStatus;
                $counter->title = G::LoadTranslation($participatedLabels[$participatedStatus]);

                // Get counter value according to the participated status
                switch ($participatedStatus) {
                    case 'STARTED':
                    case 'IN_PROGRESS':
                    case 'COMPLETED':
                        $participated = new Participated();
                        $participated->setParticipatedStatus($participatedStatus);
                        $participated->setUserId($this->getUserId());
                        $counter->counter = $participated->getCounter();
                        break;
                    case 'SUPERVISING':
                        $supervising = new Supervising();
                        $supervising->setUserUid($this->getUserId());
                        $counter->counter = $supervising->getCounter();
                        break;
                    default:
                        $counter->counter = 0;
                }
                // Add counter
                $counters[] = $counter;
            }

            // Return counters in the expected format
            return $counters;
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Get the search cases
     *
     * @url GET /search
     *
     * @param int $caseNumber
     * @param int $process
     * @param int $task
     * @param int $user
     * @param string $caseTitle
     * @param string $priorities
     * @param string $caseStatuses
     * @param string $filterCases
     * @param string $dueDateFrom
     * @param string $dueDateTo
     * @param string $delegationDateFrom
     * @param string $delegationDateTo
     * @param string $paged
     * @param string $sort
     *
     * @return array
     *
     * @throws Exception
     *
     * @access protected
     * @class AccessControl {@permission PM_ALLCASES}
     */
    public function doGetSearchCases(
        int $caseNumber = 0,
        int $process = 0,
        int $task = 0,
        int $user = 0,
        string $caseTitle = '',
        string $priorities = '',
        string $caseStatuses = '',
        string $filterCases = '',
        string $dueDateFrom = '',
        string $dueDateTo = '',
        string $delegationDateFrom = '',
        string $delegationDateTo = '',
        string $paged = '0,15',
        string $sort = 'APP_NUMBER,ASC'
    ) {
        try {
            $list = new Search();
            // Define the filters to apply
            $properties = [];
            $properties['caseNumber'] = $caseNumber;
            $properties['caseTitle'] = $caseTitle;
            $properties['process'] = $process;
            $properties['task'] = $task;
            $properties['user'] = $user;
            $properties['priorities'] = explode(',', $priorities);
            $properties['caseStatuses'] = explode(',', $caseStatuses);
            $properties['filterCases'] = $filterCases;
            $properties['dueDateFrom'] = $dueDateFrom;
            $properties['dueDateTo'] = $dueDateTo;
            $properties['delegationDateFrom'] = $delegationDateFrom;
            $properties['delegationDateTo'] = $delegationDateTo;
            // Set the pagination parameters
            $paged = explode(',', $paged);
            $sort = explode(',', $sort);
            $properties['start'] = $paged[0];
            $properties['limit'] = $paged[1];
            $properties['sort'] = $sort[0];
            $properties['dir'] = $sort[1];
            $list->setProperties($properties);
            $result = [];
            $result['data'] = $list->getData();
            // We will to enable always the pagination
            $result['total'] = $list->getLimit() + 1;
            return $result;
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Get home menu
     *
     * @url GET /menu
     *
     * @return array
     *
     * @access protected
     */
    public function getMenu()
    {
        // Parse menu definition
        $menuInstance = new Menu();
        $menuInstance->load('home');

        // Initializing variables
        $optionsWithCounter = ['CASES_INBOX', 'CASES_DRAFT', 'CASES_PAUSED', 'CASES_SELFSERVICE'];
        $menuHome = [];

        // Build the Home menu
        for ($i = 0; $i < count($menuInstance->Options); $i++) {
            // Initializing option object
            $option = new stdClass();

            // Build the object according to the option menu type
            if ($menuInstance->Types[$i] === 'blockHeader') {
                $option->header = true;
                $option->title = $menuInstance->Labels[$i];
                $option->hiddenOnCollapse = true;
            } else {
                $option->href = $menuInstance->Options[$i];
                $option->id = $menuInstance->Id[$i];
                $option->title = $menuInstance->Labels[$i];
                $option->icon = $menuInstance->Icons[$i];
            }

            // Add additional attributes for some options
            if (in_array($menuInstance->Id[$i], $optionsWithCounter)) {
                $option->badge = new stdClass();
                $option->badge->text = '0';
                $option->badge->class = 'badge-custom';
            }
            if ($menuInstance->Id[$i] === 'CASES_SEARCH') {
                // Get advanced search filters for the current user
                $filters = Filter::getByUser($this->getUserId());

                // Initializing
                $child = [];
                foreach ($filters as $filter) {
                    $childFilter = new stdClass();
                    $childFilter->id = $filter->id;
                    $childFilter->page = '/advanced-search';
                    $childFilter->href = "{$childFilter->page}/{$filter->id}";
                    $childFilter->title = $filter->name;
                    $childFilter->icon = 'fas fa-circle';
                    $childFilter->filters = $filter->filters;
                    $child[] = $childFilter;
                }

                // Adding filters to the "Advanced Search" option
                $option->child = $child;
            }

            // Add option to the menu
            $menuHome[] = $option;
        }

        // Return menu
        return $menuHome;
    }

    /**
     * Get the search cases
     *
     * @url GET /:app_number/pending-tasks
     *
     * @param int $app_number
     *
     * @return array
     *
     * @throws Exception
     *
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function getPendingTasks(int $app_number)
    {
        $result = Delegation::getPendingTask($app_number);

        return $result;
    }

    /**
     * Get all processes, paged optionally, can be sent a text to filter results by "PRO_TITLE"
     *
     * @url GET /processes
     *
     * @param string $text
     * @param string $category
     * @param int $offset
     * @param int $limit
     *
     * @return array
     *
     * @throws Exception
     *
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function getProcesses($text = null, $category = null, $offset = null, $limit = null)
    {
        try {
            $processes = Process::getProcessesForHome($text, $category, $offset, $limit);
            return $processes;
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Get the tasks counters for todo, draft, paused and unassigned
     * 
     * @url GET /tasks/counter
     * @return array
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function getTasksCounters()
    {
        $result = [];

        $usrUid = $this->getUserId();
        $usrId = User::find($usrUid)->first()->USR_ID;
        $inbox = new Inbox();
        $inbox->setUserUid($usrUid);
        $inbox->setUserId($usrId);
        $result['todo'] = $inbox->getCounter();

        $draft = new Draft();
        $draft->setUserUid($usrUid);
        $draft->setUserId($usrId);
        $result['draft'] = $draft->getCounter();

        $paused = new Paused();
        $paused->setUserUid($usrUid);
        $paused->setUserId($usrId);
        $result['paused'] = $paused->getCounter();


        $unassigned = new Unassigned();
        $unassigned->setUserUid($usrUid);
        $unassigned->setUserId($usrId);
        $result['unassigned'] = $unassigned->getCounter();

        return $result;
    }
}
