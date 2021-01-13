<?php

namespace ProcessMaker\BusinessModel\Cases;

use ProcessMaker\Model\Application;
use ProcessMaker\Model\AppNotes;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\User;

class Participated extends AbstractCases
{
    // Columns to see in the cases list
    public $columnsView = [
        // Columns view in the cases list
        'APP_DELEGATION.APP_NUMBER', // Case #
        'APP_DELEGATION.DEL_TITLE', // Case Title
        'PROCESS.PRO_TITLE', // Process Name
        'TASK.TAS_TITLE',  // Pending Task
        'APPLICATION.APP_STATUS',  // Status
        'APPLICATION.APP_CREATE_DATE',  // Start Date
        'APPLICATION.APP_FINISH_DATE',  // Finish Date
        'APP_DELEGATION.DEL_TASK_DUE_DATE',  // Due Date related to the colors
        'USERS.USR_ID',  // Current UserId
        // Additional column for other functionalities
        'APP_DELEGATION.APP_UID', // Case Uid for Open case
        'APP_DELEGATION.DEL_INDEX', // Del Index for Open case
        'APP_DELEGATION.PRO_UID', // Process Uid for Case notes
        'APP_DELEGATION.TAS_UID', // Task Uid for Case notes
    ];

    /**
     * Get the columns related to the cases list
     * @return array
     */
    public function getColumnsView()
    {
        return $this->columnsView;
    }

    /**
     * Scope filters
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filters($query)
    {
        // Specific case
        if ($this->getCaseNumber()) {
            $query->case($this->getCaseNumber());
        }
        // Specific case title
        if (!empty($this->getCaseTitle())) {
            $query->title($this->getCaseTitle());
        }
        // Scope to search for an specific process
        if ($this->getProcessId()) {
            $query->processId($this->getProcessId());
        }
        // Specific task
        if ($this->getTaskId()) {
            $query->task($this->getTaskId());
        }
        // Specific status
        if ($this->getCaseStatus()) {
            $query->status($this->getCaseStatus());
        }
        // Specific start case date from
        if (!empty($this->getStartCaseFrom())) {
            $query->startDateFrom($this->getStartCaseFrom());
        }
        // Specific by start case date to
        if (!empty($this->getStartCaseTo())) {
            $query->startDateTo($this->getStartCaseTo());
        }
        // Specific finish case date from
        if (!empty($this->getFinishCaseFrom())) {
            $query->finishCaseFrom($this->getFinishCaseFrom());
        }
        // Filter by finish case date to
        if (!empty($this->getFinishCaseTo())) {
            $query->finishCaseTo($this->getFinishCaseTo());
        }
        // Specific case uid PMFCaseLink
        if (!empty($this->getCaseUid())) {
            $query->appUid($this->getCaseUid());
        }

        return $query;
    }

    /**
     * Get the data corresponding to Participated
     *
     * @return array
     */
    public function getData()
    {
        // Start the query for get the cases related to the user
        $query = Delegation::query()->select($this->getColumnsView());
        // Join with process
        $query->joinProcess();
        // Join with task
        $query->joinTask();
        // Join with users
        $query->joinUser();
        // Join with application
        $query->joinApplication();
        // Scope to Participated
        $query->participated($this->getUserId());
        // Add filter
        $filter = $this->getParticipatedStatus();
        switch ($filter) {
            case 'STARTED':
                // Scope that search for the STARTED by user
                $query->caseStarted();
                break;
            case 'IN_PROGRESS':
                // Scope that search for the TO_DO
                $query->selectRaw(
                    'CONCAT(
                                        \'[\',
                                        GROUP_CONCAT(
                                            CONCAT(
                                                \'{"tas_id":\',
                                                APP_DELEGATION.TAS_ID,
                                                \', "user_id":\',
                                                APP_DELEGATION.USR_ID,
                                                \', "due_date":"\',
                                                APP_DELEGATION.DEL_TASK_DUE_DATE,
                                                \'"}\'
                                            )
                                        ),
                                        \']\'
                                  ) AS PENDING'
                );
                // Only cases in progress
                $query->caseInProgress();
                // Group by AppNumber
                $query->groupBy('APP_NUMBER');
                break;
            case 'COMPLETED':
                // Scope that search for the COMPLETED
                $query->caseCompleted();
                // Scope to set the last thread
                $query->lastThread();
                break;
        }
        /** Apply filters */
        $this->filters($query);
        /** Apply order and pagination */
        // The order by clause
        $query->orderBy($this->getOrderByColumn(), $this->getOrderDirection());
        // The limit by clause
        $query->offset($this->getOffset())->limit($this->getLimit());
        //Execute the query
        $results = $query->get();
        // Prepare the result
        $results->transform(function ($item, $key) use ($filter) {
            // Apply the date format defined in environment
            $item['APP_CREATE_DATE_LABEL'] = !empty($item['APP_CREATE_DATE']) ? applyMaskDateEnvironment($item['APP_CREATE_DATE']): null;
            $item['APP_FINISH_DATE_LABEL'] = !empty($item['APP_FINISH_DATE']) ? applyMaskDateEnvironment($item['APP_FINISH_DATE']): null;
            // Calculate duration
            $startDate = (string)$item['APP_CREATE_DATE'];
            $endDate = !empty($item['APP_FINISH_DATE']) ? $item['APP_FINISH_DATE'] : date("Y-m-d H:i:s");
            $item['DURATION'] = getDiffBetweenDates($startDate, $endDate);
            // Get total case notes
            $item['CASE_NOTES_COUNT'] = AppNotes::total($item['APP_NUMBER']);
            // Define data according to the filters
            switch ($filter) {
                case 'STARTED':
                    $result = [];
                    $i = 0;
                    if ($item['APP_STATUS'] === 'TO_DO') {
                        $taskPending = Delegation::getPendingThreads($item['APP_NUMBER']);
                        foreach ($taskPending as $thread) {
                            // todo this need to review
                            $result[$i]['tas_title'] = $thread['TAS_TITLE'];
                            $result[$i]['user_id'] = $thread['USR_ID'];
                            $result[$i]['due_date'] = $thread['DEL_TASK_DUE_DATE'];
                            $result[$i]['delay'] = getDiffBetweenDates($thread['DEL_TASK_DUE_DATE'],  date("Y-m-d H:i:s"));
                            $result[$i]['tas_color'] = (!empty($thread['DEL_TASK_DUE_DATE'])) ? $this->getTaskColor($thread['DEL_TASK_DUE_DATE']) : '';
                            $result[$i]['tas_color_label'] = (!empty($result[$i]['tas_color'])) ? self::TASK_COLORS[$result[$i]['tas_color']] : '';
                            // Get the user tooltip information
                            $result[$i] = User::getInformation($thread['USR_ID']);
                            $i++;
                        }
                        $item['PENDING'] = $result;
                    } else {
                        $result[$i]['tas_title'] = $item['TAS_TITLE'];
                        $result[$i]['user_id'] = $item['USR_ID'];
                        $result[$i]['due_date'] = $item['DEL_TASK_DUE_DATE'];
                        $result[$i]['delay'] = getDiffBetweenDates($item['DEL_TASK_DUE_DATE'],  date("Y-m-d H:i:s"));
                        $result[$i]['tas_color'] = (!empty($item['DEL_TASK_DUE_DATE'])) ? $this->getTaskColor($item['DEL_TASK_DUE_DATE']) : '';
                        $result[$i]['tas_color_label'] = (!empty($result[$i]['tas_color'])) ? self::TASK_COLORS[$result[$i]['tas_color']] : '';
                        // Get the user tooltip information
                        $result[$i] = User::getInformation($item['USR_ID']);
                        $item['PENDING'] = $result;
                    }
                    break;
                case 'IN_PROGRESS':
                    // Get the detail related to the open thread
                    if (!empty($item['PENDING'])) {
                        $result = $this->prepareTaskPending($item['PENDING']);
                        $item['PENDING'] = !empty($result['THREAD_TASKS']) ? $result['THREAD_TASKS'] : [];
                    }
                    break;
                case 'COMPLETED':
                    $result = [];
                    $i = 0;
                    $result[$i]['tas_title'] = $item['TAS_TITLE'];
                    $result[$i]['user_id'] = $item['USR_ID'];
                    $result[$i]['due_date'] = $item['DEL_TASK_DUE_DATE'];
                    $result[$i]['delay'] = getDiffBetweenDates($item['DEL_TASK_DUE_DATE'],  date("Y-m-d H:i:s"));
                    $result[$i]['tas_color'] = (!empty($item['DEL_TASK_DUE_DATE'])) ? $this->getTaskColor($item['DEL_TASK_DUE_DATE']) : '';
                    $result[$i]['tas_color_label'] = (!empty($result[$i]['tas_color'])) ? self::TASK_COLORS[$result[$i]['tas_color']] : '';
                    // Get the user tooltip information
                    $result[$i] = User::getInformation($item['USR_ID']);
                    $item['PENDING'] = $result;
                    break;
            }

            return $item;
        });

        return $results->values()->toArray();
    }

    /**
     * Get the number of rows corresponding has Participation, does not apply filters
     *
     * @return int
     */
    public function getCounter()
    {
        // Get base query
        $query = Delegation::query()->select();
        // Join with application
        $query->joinApplication();
        // Scope that sets the queries for Participated
        $query->participated($this->getUserId());
        // Get filter
        $filter = $this->getParticipatedStatus();
        switch ($filter) {
            case 'STARTED':
                // Scope that search for the STARTED by user
                $query->caseStarted();
                break;
            case 'IN_PROGRESS':
                // Only distinct APP_NUMBER
                $query->distinct();
                // Scope for in progress cases
                $query->statusIds([self::STATUS_DRAFT, self::STATUS_TODO]);
                break;
            case 'COMPLETED':
                // Scope that search for the COMPLETED
                $query->caseCompleted();
                // Scope to set the last thread
                $query->lastThread();
                break;
        }
        // Return the number of rows
        return $query->count(['APP_DELEGATION.APP_NUMBER']);
    }

    /**
     * Count how many cases the user has Participation, needs to apply filters
     *
     * @return int
     */
    public function getPagingCounters()
    {
        // Get base query
        $query = Delegation::query()->select();
        // Join with application
        $query->joinApplication();
        // Scope that sets the queries for Participated
        $query->participated($this->getUserId());
        // Get filter
        $filter = $this->getParticipatedStatus();
        switch ($filter) {
            case 'STARTED':
                // Scope that search for the STARTED by user
                $query->caseStarted();
                break;
            case 'IN_PROGRESS':
                // Only distinct APP_NUMBER
                $query->distinct();
                // Scope for in progress cases
                $query->statusIds([self::STATUS_DRAFT, self::STATUS_TODO]);
                break;
            case 'COMPLETED':
                // Scope that search for the COMPLETED
                $query->caseCompleted();
                // Scope to set the last thread
                $query->lastThread();
                break;
        }
        // Apply filters
        $this->filters($query);
        // Return the number of rows
        return $query->count(['APP_DELEGATION.APP_NUMBER']);
    }
}
