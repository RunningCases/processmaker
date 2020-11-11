<?php

namespace ProcessMaker\BusinessModel\Cases;

use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;

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
        'APP_DELEGATION.DEL_TASK_DUE_DATE',  // Due Date
        'APP_DELEGATION.DEL_DELEGATE_DATE',  // Start Date
        'APP_DELEGATION.DEL_FINISH_DATE',  // Finish Date
        // Additional column for other functionalities
        'APP_DELEGATION.APP_UID', // Case Uid for PMFCaseLink
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
     * Get the data corresponding to Participated
     *
     * @return array
     */
    public function getData()
    {
        // Start the query for get the cases related to the user
        $query = Delegation::query()->select();
        // Join with process
        $query->joinProcess();
        // Join with task
        $query->joinTask();
        // Join with users
        $query->joinUser();
        // Scope to Participated
        $query->participated($this->getUserId());
        // Add filter
        $filter = $this->getParticipatedStatus();
        if (!empty($filter)) {
            switch ($filter) {
                case 'STARTED':
                    // Scope that search for the STARTED
                    $query->caseStarted();
                    break;
                case 'IN-PROGRESS':
                    // Scope that search for the TO_DO
                    $query->caseInProgress();
                    break;
                case 'COMPLETED':
                    // Scope that search for the COMPLETED
                    $query->caseCompleted();
                    break;
            }
        }
        // Scope to search for an specific process
        if (!empty($this->getProcessId())) {
            $query->processId($this->getProcessId());
        }
        // Scope the specific case status
        $status = $this->getCaseStatus();
        if (array_key_exists($status, Application::$app_status_values)) {
            $statusId = Application::$app_status_values[$status];
            $query->appStatusId($statusId);
        }
        // The order by clause
        $query->orderBy($this->getOrderByColumn(), $this->getOrderDirection());
        // The limit by clause
        $query->offset($this->getOffset())->limit($this->getLimit());
        //Execute the query
        $results = $query->get();
        // Prepare the result
        $results->transform(function ($item, $key) {
            // Get task color label
            $item['TAS_COLOR'] = $this->getTaskColor($item['DEL_TASK_DUE_DATE']);
            $item['TAS_COLOR_LABEL'] = self::TASK_COLORS[$item['TAS_COLOR']];
            // Apply the date format defined in environment
            $item['DEL_DELEGATE_DATE_LABEL'] = applyMaskDateEnvironment($item['DEL_DELEGATE_DATE']);
            $item['DEL_FINISH_DATE_LABEL'] = !empty($item['DEL_FINISH_DATE']) ? applyMaskDateEnvironment($item['DEL_FINISH_DATE']): null;
            // Calculate duration
            $startDate = $item['DEL_DELEGATE_DATE'];
            $endDate = !empty($item['DEL_FINISH_DATE']) ? $item['DEL_FINISH_DATE'] : date("Y-m-d H:i:s");
            $item['DURATION'] = getDiffBetweenDates($startDate, $endDate);

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
