<?php

namespace ProcessMaker\BusinessModel\Cases;

use G;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\AppNotes;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\User;

class Search extends AbstractCases
{
    // Columns to see in the cases list
    public $columnsView = [
        // Columns view in the cases list
        'APP_DELEGATION.APP_NUMBER', // Case #
        'APP_DELEGATION.DEL_TITLE', // Case Title
        'PROCESS.PRO_TITLE', // Process
        'APPLICATION.APP_STATUS',  // Status
        'APPLICATION.APP_CREATE_DATE',  // Case create date
        'APPLICATION.APP_FINISH_DATE',  // Case finish date
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
            $query->title($this->getCaseTitle());
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
        /** This filter define the UNION */

        // Filter related to the case status like ['DRAFT', 'TO_DO']
        if (!empty($this->getCaseStatuses())) {
            $statuses = $this->getCaseStatuses();
            $casesOpen = [];
            $casesClosed = [];
            foreach ($statuses as $row) {
                if ($row === self::STATUS_DRAFT or $row === self::STATUS_TODO) {
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
        } else {
            $query->isThreadOpen();
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
        $query->selectRaw(
            'CONCAT(
                \'[\',
                    GROUP_CONCAT(
                        CONCAT(
                            \'{"tas_id":\',
                            APP_DELEGATION.TAS_ID,
                            \', "user_id":\',
                            APP_DELEGATION.USR_ID,
                            \', "del_id":\',
                            APP_DELEGATION.DELEGATION_ID,
                            \', "due_date":"\',
                            APP_DELEGATION.DEL_TASK_DUE_DATE,
                            \'"}\'
                        )
                    ),
                \']\'
            ) AS THREADS'
        );
        // Join with process
        $query->joinProcess();
        // Join with application
        $query->joinApplication();
        // Group by AppNumber
        $query->groupBy('APP_NUMBER');
        /** Apply filters */
        $this->filters($query);
        /** Exclude the web entries does not submitted */
        $query->positiveCases($query);
        /** Apply order and pagination */
        // The order by clause
        $query->orderBy($this->getOrderByColumn(), $this->getOrderDirection());
        // The limit by clause
        $query->offset($this->getOffset())->limit($this->getLimit());
        //Execute the query
        $results = $query->get();
        // Prepare the result
        $results->transform(function ($item, $key) {
            // Apply the date format defined in environment
            $item['APP_CREATE_DATE_LABEL'] = !empty($item['APP_CREATE_DATE']) ? applyMaskDateEnvironment($item['APP_CREATE_DATE']): null;
            $item['APP_FINISH_DATE_LABEL'] = !empty($item['APP_FINISH_DATE']) ? applyMaskDateEnvironment($item['APP_FINISH_DATE']): null;
            // Calculate duration
            $startDate = (string)$item['APP_CREATE_DATE'];
            $endDate = !empty($item['APP_FINISH_DATE']) ? $item['APP_FINISH_DATE'] : date("Y-m-d H:i:s");
            $item['DURATION'] = getDiffBetweenDates($startDate, $endDate);
            // Get total case notes
            $item['CASE_NOTES_COUNT'] = AppNotes::total($item['APP_NUMBER']);
            // Get the detail related to the open thread
            if (!empty($item['THREADS'])) {
                $result = $this->prepareTaskPending($item['THREADS'], false);
                $item['THREAD_TASKS'] = !empty($result['THREAD_TASKS']) ? $result['THREAD_TASKS'] : [];
                $item['THREAD_USERS'] = !empty($result['THREAD_USERS']) ? $result['THREAD_USERS'] : [];
                $item['THREAD_TITLES'] = !empty($result['THREAD_TITLES']) ? $result['THREAD_TITLES'] : [];
            }

            return $item;
        });

        return $results->values()->toArray();
    }

    /**
     * Count how many cases the user has in the advanced search, does not apply filters
     *
     * @return int
     */
    public function getCounter()
    {
        // The search does not have a counters
        return 0;
    }

    /**
     * Get the number of rows corresponding to the advanced search, needs to apply filters
     *
     * @return int
     */
    public function getPagingCounters()
    {
        // The search always will enable the pagination
        return 0;
    }
}