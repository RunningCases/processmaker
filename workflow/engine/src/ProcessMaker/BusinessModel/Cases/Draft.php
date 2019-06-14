<?php

namespace ProcessMaker\BusinessModel\Cases;

use ProcessMaker\Model\Delegation;

class Draft extends AbstractCases
{
    /**
     * Get data self-services cases by user
     *
     * @return array
     */
    public function getData()
    {
        $query = Delegation::query()->select();
        // Add the initial scope for draft cases
        $query->draft($this->getUserId());
        // Add join with task
        $query->specificTaskTypes(['NORMAL', 'ADHOC']);
        // Add join for process, but only for certain scenarios such as category or process
        if ($this->getCategoryUid() || $this->getProcessUid() || $this->getOrderByColumn() === 'PRO_TITLE') {
            $query->categoryProcess($this->getCategoryUid());
        }
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
