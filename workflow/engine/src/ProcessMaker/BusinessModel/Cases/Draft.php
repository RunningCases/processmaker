<?php

namespace ProcessMaker\BusinessModel\Cases;

use G;
use ProcessMaker\Model\Delegation;

class Draft extends AbstractCases
{
    // Columns to see in the cases list
    public $columnsView = [
        // Columns view in the cases list
        'APP_DELEGATION.APP_NUMBER', // Case #
        'APP_DELEGATION.APP_NUMBER AS APP_TITLE', // Case Title @todo: Filter by case title, pending from other PRD
        'PROCESS.PRO_TITLE', // Process
        'TASK.TAS_TITLE',  // Task
        'APP_DELEGATION.DEL_TASK_DUE_DATE',  // Due Date
        'APP_DELEGATION.DEL_DELEGATE_DATE',  // Delegate Date
        'APP_DELEGATION.DEL_PRIORITY',  // Priority
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
        // Specific process
        if ($this->getProcessId()) {
            $query->processId($this->getProcessId());
        }
        // Specific case uid
        if (!empty($this->getCaseUid())) {
            $query->appUid($this->getCaseUid());
        }
        // Specific cases
        if (!empty($this->getCasesUids())) {
            $query->specificCasesByUid($this->getCasesUids());
        }
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
            // Apply the date format defined in environment
            $item['DEL_TASK_DUE_DATE_LABEL'] = applyMaskDateEnvironment($item['DEL_TASK_DUE_DATE']);
            $item['DEL_DELEGATE_DATE_LABEL'] = applyMaskDateEnvironment($item['DEL_DELEGATE_DATE']);

            return $item;
        });

        return $results->values()->toArray();
    }

    /**
     * Count the self-services cases by user
     *
     * @return int
     */
    public function getCounter()
    {
        $query = Delegation::query()->select();
        // Add the initial scope for draft cases
        $query->draft($this->getUserId());

        return $query->count();
    }
}
