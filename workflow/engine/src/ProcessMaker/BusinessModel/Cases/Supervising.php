<?php

namespace ProcessMaker\BusinessModel\Cases;

use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\ProcessUser;

class Supervising extends AbstractCases
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
     * Gets the data for the Cases list Review
     * 
     * @return array
     */
    public function getData()
    {
        // Get the list of processes of the supervisor
        $processes = ProcessUser::getProcessesOfSupervisor($this->getUserUid());
        // We will prepare the queries if the user is supervisor
        if (!empty($processes)) {
            // Start the query for get the cases related to the user
            $query = Delegation::query()->select($this->getColumnsView());
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
            // Join with process
            $query->joinProcess();
            // Join with task
            $query->joinTask();
            // Join with users
            $query->joinUser();
            // Join with application
            $query->joinApplication();
            // Only cases in to_do
            $query->caseTodo();
            // Scope that return the results for an specific user
            $query->userId($this->getUserId());
            // Scope the specific array of processes supervising
            $query->processInList($processes);
            // Group by appNumber
            $query->groupBy('APP_NUMBER');
            /** Apply filters */
            $this->filters($query);
            /** Apply order and pagination */
            //The order by clause
            $query->orderBy($this->getOrderByColumn(), $this->getOrderDirection());
            //The limit clause
            $query->offset($this->getOffset())->limit($this->getLimit());
            //Execute the query
            $results = $query->get();
            // Prepare the result
            $results->transform(function ($item, $key) {
                // Get task color label
                $item['TAS_COLOR'] = $this->getTaskColor($item['DEL_TASK_DUE_DATE']);
                $item['TAS_COLOR_LABEL'] = self::TASK_COLORS[$item['TAS_COLOR']];
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

                return $item;
            });

            return $results->values()->toArray();
        } else {
            return [];
        }
    }

    /**
     * Gets the total of Review cases
     * 
     * @return int
     */
    public function getCounter()
    {
        // Get base query
        $query = Delegation::query()->select();
        // Only distinct APP_NUMBER
        $query->distinct();
        // Get the list of processes of the supervisor
        $processes = ProcessUser::getProcessesOfSupervisor($this->getUserUid());
        // Scope the specific array of processes supervising
        $query->processInList($processes);
        // Return the number of rows
        return $query->count(['APP_DELEGATION.APP_NUMBER']);
    }
}
