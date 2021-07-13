<?php

namespace ProcessMaker\BusinessModel\Cases;

use G;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;

class Inbox extends AbstractCases
{
    // Columns to see in the cases list
    public $columnsView = [
        // Columns view in the cases list
        'APP_DELEGATION.APP_NUMBER', // Case #
        'APP_DELEGATION.DEL_TITLE', // Case Title
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
        // Filter only cases by specific cases like [1,3,5]
        if (!empty($this->getCasesNumbers()) && empty($this->getRangeCasesFromTo())) {
            $query->specificCases($this->getCasesNumbers());
        }
        // Filter only cases by range of cases like ['1-5', '10-15']
        if (!empty($this->getRangeCasesFromTo()) && empty($this->getCasesNumbers())) {
            $query->rangeOfCases($this->getRangeCasesFromTo());
        }
        // Filter cases mixed by range of cases and specific cases like '1,3-5,8'
        if (!empty($this->getCasesNumbers()) && !empty($this->getRangeCasesFromTo())) {
            $query->casesOrRangeOfCases($this->getCasesNumbers(), $this->getRangeCasesFromTo());
        }
        // Specific case title
        if (!empty($this->getCaseTitle())) {
            $query->title($this->getCaseTitle());
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
        // Join with task
        $query->JoinTask();
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
            // Get task status
            $item['TAS_STATUS'] = self::TASK_STATUS[$item['TAS_COLOR']];
            // Get delay
            $item['DELAY'] = getDiffBetweenDates($item['DEL_TASK_DUE_DATE'],  date("Y-m-d H:i:s"));
            // Apply the date format defined in environment
            $item['DEL_TASK_DUE_DATE_LABEL'] = applyMaskDateEnvironment($item['DEL_TASK_DUE_DATE']);
            $item['DEL_DELEGATE_DATE_LABEL'] = applyMaskDateEnvironment($item['DEL_DELEGATE_DATE']);

            return $item;
        });

        return $results->values()->toArray();
    }

    /**
     * Count how many cases the user has in TO_DO, does not apply filters
     *
     * @return int
     */
    public function getCounter()
    {
        $query = Delegation::query()->select();
        // Scope that sets the queries for List Inbox
        $query->inbox($this->getUserId());
        // Return the number of rows
        return $query->count(['APP_DELEGATION.APP_NUMBER']);
    }

    /**
     * Count how many cases the user has in TO_DO, needs to apply filters
     *
     * @return int
     */
    public function getPagingCounters()
    {
        $query = Delegation::query()->select();
        // Scope that sets the queries for List Inbox
        $query->inbox($this->getUserId());
        // Apply filters
        $this->filters($query);
        // Return the number of rows
        return $query->count(['APP_DELEGATION.APP_NUMBER']);
    }
}
