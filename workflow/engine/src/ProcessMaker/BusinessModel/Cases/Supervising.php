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
     * Gets the data for the Cases list Review
     * 
     * @return array
     */
    public function getData()
    {
        // Get the list of processes of the supervisor
        $processes = ProcessUser::getProcessesOfSupervisor($this->getUserUid());
        // Start the query for get the cases related to the user
        $query = Delegation::query()->select($this->getColumnsView());
        // Join with process
        $query->joinProcess();
        // Join with users
        $query->joinUser();
        // Join with task and scope that sets the queries for List Inbox
        $query->inbox($this->getUserId());
        // Scope the specific array of processes supervising
        $query->processInList($processes);
        // Join with delegation for get the previous index
        $query->joinPreviousIndex();
        // Join with delegation for get the previous user
        $query->joinPreviousUser();
        // Scope to search for an specific case
        if (!empty($this->getCaseNumber())) {
            $query->case($this->getCaseNumber());
        }
        // Scope to search for an specific process
        if (!empty($this->getProcessId())) {
            $query->processId($this->getProcessId());
        }
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
     * Gets the total of Review cases
     * 
     * @return int
     */
    public function getCounter()
    {
        $query = Delegation::query()->select();
        // Get the list of processes of the supervisor
        $processes = ProcessUser::getProcessesOfSupervisor($this->getUserUid());
        // Scope the specific array of processes supervising
        $query->processInList($processes);

        return $query->count();
    }
}
