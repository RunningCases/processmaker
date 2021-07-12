<?php

namespace ProcessMaker\BusinessModel\Cases;

use G;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;

class Draft extends AbstractCases
{
    // Columns to see in the cases list
    public $columnsView = [
        // Columns view in the cases list
        'APP_DELEGATION.APP_NUMBER', // Case #
        'APP_DELEGATION.DEL_TITLE', // Case Title
        'PROCESS.PRO_TITLE', // Process
        'TASK.TAS_TITLE',  // Task
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
     * Get data self-services cases by user
     *
     * @return array
     */
    public function getData()
    {
        $query = Delegation::query()->select($this->getColumnsView());
        // Join with process
        $query->joinProcess();
        // Join with task
        $query->joinTask();
        // Join with application for add the initial scope for DRAFT cases
        $query->draft($this->getUserId());
        /** Apply filters */
        $this->filters($query);
        /** Apply order and pagination */
        // Add any sort if needed
        if ($this->getOrderByColumn()) {
            $query->orderBy($this->getOrderByColumn(), $this->getOrderDirection());
        }
        // Add pagination to the query
        $query->offset($this->getOffset())->limit($this->getLimit());
        // Get the data
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
     * Count how many cases the user has in DRAFT, does not apply filters
     *
     * @return int
     */
    public function getCounter()
    {
        $query = Application::query()->select();
        // Add the initial scope for draft cases
        $query->statusId(Application::STATUS_DRAFT);
        // Filter the creator
        $query->creator($this->getUserUid());
        // Return the number of rows
        return $query->count(['APPLICATION.APP_NUMBER']);
    }

    /**
     * Count how many cases the user has in DRAFT, needs to apply filters
     *
     * @return int
     */
    public function getPagingCounters()
    {
        $query = Delegation::query()->select();
        // Add the initial scope for draft cases
        $query->draft($this->getUserId());
        // Apply filters
        $this->filters($query);
        // Return the number of rows
        return $query->count(['APP_DELEGATION.APP_NUMBER']);
    }
}
