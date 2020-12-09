<?php

namespace ProcessMaker\BusinessModel\Cases;

use G;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;

class Search extends AbstractCases
{
    // Columns to see in the cases list
    public $columnsView = [
        // Columns view in the cases list
        'APP_DELEGATION.APP_NUMBER', // Case #
        'APP_DELEGATION.APP_NUMBER AS APP_TITLE', // Case Title @todo: Filter by case title, pending from other PRD
        'PROCESS.PRO_TITLE', // Process
        'TASK.TAS_TITLE',  // Task
        'APPLICATION.APP_STATUS',  // Status
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
        // Filter case by case number
        if ($this->getCaseNumber()) {
            $query->case($this->getCaseNumber());
        }
        // Filter cases by specific cases like [1,3,5]
        if (!empty($this->getCasesNumbers())) {
            $query->specificCases($this->getCasesNumbers());
        }
        // Filter cases by range of cases like ['1-5', '10-15']
        if (!empty($this->getRangeCasesFromTo())) {
            $query->rangeOfCases($this->getRangeCasesFromTo());
        }
        // Specific case title
        if (!empty($this->getCaseTitle())) {
            // @todo: Filter by case title, pending from other PRD
        }
        // Filter by process
        if ($this->getProcessId()) {
            $query->processId($this->getProcessId());
        }
        // Filter by user
        if ($this->getUserId()) {
            $query->userId($this->getUserId());
        }
        // Filter by task
        if ($this->getTaskId()) {
            $query->task($this->getTaskId());
        }
        // Filter one or more priorities like ['VL', 'L', 'N']
        if (!empty($this->getPriorities())) {
            $query->priorities($this->getPriorities());
        }
        // Filter by delegate from
        if (!empty($this->getDelegateFrom())) {
            $query->delegateDateFrom($this->getDelegateFrom());
        }
        // Filter by delegate to
        if (!empty($this->getDelegateTo())) {
            $query->delegateDateTo($this->getDelegateTo());
        }
        // Filter by due from
        if (!empty($this->getDueFrom())) {
            $query->dueFrom($this->getDueFrom());
        }
        // Filter by due to
        if (!empty($this->getDueTo())) {
            $query->dueTo($this->getDueTo());
        }
        /** This filter define the UNION */

        // Filter related to the case status like ['DRAFT', 'TO_DO']
        if (!empty($this->getCaseStatuses())) {
            $statuses = $this->getCaseStatuses();
            $casesOpen = [];
            $casesClosed = [];
            foreach ($statuses as $row) {
                if ($row === Application::STATUS_DRAFT or $row === Application::STATUS_TODO) {
                    $casesOpen[] = $row;
                } else {
                    $casesClosed[] = $row;
                }
            }
            if (!empty($casesOpen) && !empty($casesClosed)) {
                // Only in this case need to clone the same query for the union
                $cloneQuery = clone $query;
                // Get the open threads
                $query->casesInProgress($casesOpen);
                // Get the last thread
                $cloneQuery->casesDone($casesClosed);
                // Union
                $query->union($cloneQuery);
            } else {
                if (!empty($casesOpen)) {
                    // Get the open thread
                    $query->casesInProgress($casesOpen);
                }
                if (!empty($casesClosed)) {
                    // Get the last thread
                    $query->casesDone($casesClosed);
                }
            }
        }

        return $query;
    }

    /**
     * Get the data corresponding to advanced search
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
        // Join with users
        $query->joinUser();
        // Join with application
        $query->joinApplication();
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
     * Get the number of rows corresponding to the advanced search
     *
     * @return int
     */
    public function getCounter()
    {
        $query = Delegation::query()->select();

        // Return the number of rows
        return $query->count();
    }
}