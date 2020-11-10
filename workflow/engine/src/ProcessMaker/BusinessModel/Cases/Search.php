<?php

namespace ProcessMaker\BusinessModel\Cases;

use ProcessMaker\Model\Delegation;

class Search extends AbstractCases
{
    /**
     * Get the data corresponding to advanced search
     *
     * @return array
     */
    public function getData()
    {
        $query = Delegation::query()->select();

        // Filter by case number
        $query->case($this->getCaseNumber());

        // Filter by case number: from and to
        if ($this->getFromCaseNumber() > 0 && $this->getToCaseNumber() > 0) {
            $query->rangeOfCases($this->getFromCaseNumber(), $this->getToCaseNumber());
        }

        // @todo: Filter by case title, pending from other PRD

        // Filter by category
        $query->categoryProcess($this->getCategoryUid());

        // Filter by priority
        if ($this->getPriority() > 0) {
            $query->priority($this->getPriority());
        }

        // Filter by process
        if (!empty($this->getProcessId())) {
            $query->processId($this->getProcessId());
        }

        // Filter by user
        if (!empty($this->getUserId())) {
            $query->userId($this->getUserId());
        }

        // Filter by task
        if (!empty($this->getTaskId())) {
            $query->task($this->getTaskId());
        }

        // The order by clause
        $query->orderBy($this->getOrderByColumn(), $this->getOrderDirection());

        // The limit by clause
        $query->offset($this->getOffset())->limit($this->getLimit());

        //Execute the query
        $results = $query->get();

        //Return the values as an array format
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