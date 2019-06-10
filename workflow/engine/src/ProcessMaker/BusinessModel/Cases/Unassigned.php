<?php

namespace ProcessMaker\BusinessModel\Cases;

use ProcessMaker\Model\AppAssignSelfServiceValue;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\TaskUser;


class Unassigned extends AbstractCases
{
    /**
     * Get data self-services cases by user
     *
     * @return array
     */
    public function getData()
    {
        $query = Delegation::query()->select();
        // Add the initial scope for unassigned cases
        $query->selfService($this->getUserUid());
        // Add join for application, for get the case title when the case status is TO_DO
        $query->appStatusId(Application::STATUS_TODO);
        // Add join for process, but only for certain scenarios such as category or process
        if ($this->getCategoryUid() || $this->getProcessUid() || $this->getOrderByColumn() === 'PRO_TITLE') {
            $query->categoryProcess($this->getCategoryUid());
        }
        // Specific process
        if ($this->getProcessId()) {
            $query->processId($this->getProcessId());
        }
        // Date range filter, this is used from mobile GET /light/unassigned
        if ($this->getNewestThan()) {
            $query->delegateDateFrom($this->getNewestThan());
        }
        if ($this->getOldestThan()) {
            $query->delegateDateTo($this->getOldestThan());
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
        $query->selfService($this->getUserUid());

        return $query->count();
    }
}
