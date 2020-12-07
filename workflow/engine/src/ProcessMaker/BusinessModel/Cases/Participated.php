<?php

namespace ProcessMaker\BusinessModel\Cases;

use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Task;

class Participated extends AbstractCases
{
    // Columns to see in the cases list
    public $columnsView = [
        // Columns view in the cases list
        'APP_DELEGATION.APP_NUMBER', // Case #
        'APP_DELEGATION.APP_NUMBER AS APP_TITLE', // Case Title @todo: Filter by case title, pending from other PRD
        'PROCESS.PRO_TITLE', // Process Name
        'TASK.TAS_TITLE',  // Pending Task
        'APPLICATION.APP_STATUS',  // Status
        'APPLICATION.APP_CREATE_DATE',  // Start Date
        'APPLICATION.APP_FINISH_DATE',  // Finish Date
        'USERS.USR_ID',  // Current UserId
        'APP_DELEGATION.DEL_TASK_DUE_DATE',  // Due Date related to the colors
        // Additional column for other functionalities
        'APP_DELEGATION.APP_UID', // Case Uid for Open case
        'APP_DELEGATION.DEL_INDEX', // Del Index for Open case
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
            // @todo: Filter by case title, pending from other PRD
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
        if (!empty($filter)) {
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
                    $query->caseInProgress();
                    $query->groupBy('APP_NUMBER');
                    break;
                case 'COMPLETED':
                    // Scope that search for the COMPLETED
                    $query->caseCompleted();
                    // Scope to set the last thread
                    $query->lastThread();
                    break;
            }
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
            // Get the detail related to the open thread
            if (!empty($item['PENDING'])) {
                $item['PENDING'] = $this->prepareTaskPending($item['PENDING']);
            }
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
                            $result[$i]['tas_color'] = (!empty($row)) ? $this->getTaskColor($thread['DEL_TASK_DUE_DATE']) : '';
                            $result[$i]['tas_color_label'] = (!empty($row)) ? self::TASK_COLORS[$result[$i]['tas_color']] : '';
                            $i++;
                        }
                        $item['PENDING'] = $result;
                    } else {
                        $result[$i]['tas_title'] = $item['TAS_TITLE'];
                        $result[$i]['user_id'] = $item['USR_ID'];
                        $result[$i]['due_date'] = $item['DEL_TASK_DUE_DATE'];
                        $result[$i]['tas_color'] = (!empty($row)) ? $this->getTaskColor($item['DEL_TASK_DUE_DATE']) : '';
                        $result[$i]['tas_color_label'] = (!empty($row)) ? self::TASK_COLORS[$result[$i]['tas_color']] : '';
                        $item['PENDING'] = $result;
                    }
                    break;
                case 'IN_PROGRESS':
                    $item['PENDING'] = $this->prepareTaskPending($item['PENDING']);
                    break;
                case 'COMPLETED':
                    $result = [];
                    $i = 0;
                    $result[$i]['tas_title'] = $item['TAS_TITLE'];
                    $result[$i]['user_id'] = $item['USR_ID'];
                    $result[$i]['due_date'] = $item['DEL_TASK_DUE_DATE'];
                    $result[$i]['tas_color'] = (!empty($row)) ? $this->getTaskColor($item['DEL_TASK_DUE_DATE']) : '';
                    $result[$i]['tas_color_label'] = (!empty($row)) ? self::TASK_COLORS[$result[$i]['tas_color']] : '';
                    $item['PENDING'] = $result;
                    break;
            }

            return $item;
        });

        return $results->values()->toArray();
    }

    /**
     * Get the number of rows corresponding to the Participate
     *
     * @return int
     */
    public function getCounter()
    {
        $query = Delegation::query()->select();
        // Scope that sets the queries for Participated
        $query->participated($this->getUserId());
        // Return the number of rows
        return $query->count();
    }
}
