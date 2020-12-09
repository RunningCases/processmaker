<?php

namespace ProcessMaker\BusinessModel\Cases;

use G;
use ProcessMaker\Model\Delegation;

class Inbox extends AbstractCases
{
    // Columns to see in the cases list
    public $columnsView = [
        // Columns view in the cases list
        'APP_DELEGATION.APP_NUMBER', // Case #
        'APP_DELEGATION.APP_NUMBER AS APP_TITLE', // Case Title @todo: Filter by case title, pending from other PRD
        'PROCESS.PRO_TITLE', // Process
        'TASK.TAS_TITLE',  // Task
        'USERS.USR_USERNAME',  // Current UserName
        'USERS.USR_FIRSTNAME',  // Current User FirstName
        'USERS.USR_LASTNAME',  // Current User LastName
        'APP_DELEGATION.DEL_TASK_DUE_DATE',  // Due Date
        'APP_DELEGATION.DEL_DELEGATE_DATE',  // Delegate Date
        'APP_DELEGATION.DEL_PRIORITY',  // Priority
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
            // @todo: Filter by case title, pending from other PRD
        }
        // Specific process
        if ($this->getProcessId()) {
            $query->processId($this->getProcessId());
        }
        // Specific task
        if ($this->getTaskId()) {
            $query->task($this->getTaskId());
        }
        // Specific case uid PMFCaseLink
        if (!empty($this->getCaseUid())) {
            $query->appUid($this->getCaseUid());
        }

        return $query;
    }

    /**
     * Get the data corresponding to List Inbox
     *
     * @return array
     */
    public function getData()
    {
        // Start the query for get the cases related to the user
        $query = Delegation::query()->select($this->getColumnsView());
        // Join with process
        $query->joinProcess();
        // Join with users
        $query->joinUser();
        // Join with application for add the initial scope for TO_DO cases
        $query->inbox($this->getUserId());
        /** Apply filters */
        $this->filters($query);
        /** Apply order and pagination */
        // Add any sort if needed
        if ($this->getOrderByColumn()) {
            $query->orderBy($this->getOrderByColumn(), $this->getOrderDirection());
        }
        // The limit by clause
        $query->offset($this->getOffset())->limit($this->getLimit());
        // Execute the query
        $results = $query->get();
        // Prepare the result
        $results->transform(function ($item, $key) {
            // Get priority label
            $priorityLabel = self::PRIORITIES[$item['DEL_PRIORITY']];
            $item['DEL_PRIORITY_LABEL'] = G::LoadTranslation("ID_PRIORITY_{$priorityLabel}");
            // Get task color label
            $item['TAS_COLOR'] = $this->getTaskColor($item['DEL_TASK_DUE_DATE']);
            $item['TAS_COLOR_LABEL'] = self::TASK_COLORS[$item['TAS_COLOR']];
            // Apply the date format defined in environment
            $item['DEL_TASK_DUE_DATE_LABEL'] = applyMaskDateEnvironment($item['DEL_TASK_DUE_DATE']);
            $item['DEL_DELEGATE_DATE_LABEL'] = applyMaskDateEnvironment($item['DEL_DELEGATE_DATE']);

            return $item;
        });

        return $results->values()->toArray();
    }

    /**
     * Get the number of rows corresponding to the List Inbox
     *
     * @return int
     */
    public function getCounter()
    {
        $query = Delegation::query()->select();

        // Scope that sets the queries for List Inbox
        $query->inbox($this->getUserId());

        // Return the number of rows
        return $query->count();
    }
}
