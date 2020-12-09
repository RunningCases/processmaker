<?php

namespace ProcessMaker\Services\Api;

use Exception;
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
        string $sort ='APP_NUMBER,ASC'
    ){
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
            $result['total'] = $list->getCounter();
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
        string $sort ='APP_NUMBER,ASC'
    ){
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
            $result['total'] = $list->getCounter();
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
        string $sort ='APP_NUMBER,ASC'
    ){
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
            $result['total'] = $list->getCounter();
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
        string $sort ='APP_NUMBER,ASC'
    ){
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
            $result['total'] = $list->getCounter();
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
        string $sort ='APP_NUMBER,ASC'
    ){
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
                        $result['total'] = $list->getCounter();
                        break;
                    case 'SUPERVISING':
                        // Scope that search for the SUPERVISING cases by specific user
                        $list = new Supervising();
                        $list->setProperties($properties);
                        $result['data'] = $list->getData();
                        $result['total'] = $list->getCounter();
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
            $filters = ['STARTED', 'IN_PROGRESS', 'COMPLETED', 'SUPERVISING'];
            $result = [];
            foreach ($filters as $row) {
                switch ($row) {
                    case 'STARTED':
                    case 'IN_PROGRESS':
                    case 'COMPLETED':
                        $list = new Participated();
                        $list->setParticipatedStatus($row);
                        $list->setUserId($this->getUserId());
                        $result[strtolower($row)] = $list->getCounter();
                        break;
                    case 'SUPERVISING':
                        // Scope that search for the SUPERVISING cases by specific user
                        $list = new Supervising();
                        $list->setUserId($this->getUserId());
                        $result[strtolower($row)] = $list->getCounter();
                        break;
                    default:
                        $result[strtolower($row)] = 0;
                }
            }

            return $result;
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
        string $sort ='APP_NUMBER,ASC'
    ){
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
}
