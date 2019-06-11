<?php

namespace ProcessMaker\BusinessModel\Cases;

use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Task;

class Inbox extends AbstractCases
{

    /**
     * Get the data corresponding to List Inbox
     *
     * @return array
     */
    public function getData()
    {
        // Start the query for get the cases related to the user
        $query = Delegation::query()->select();

        // Scope that sets the queries for List Inbox
        $query->inbox($this->getUserId());

        // Scope that joins with the process and/or for an specific category in the process
        $query->categoryProcess($this->getCategoryUid());

        switch ($this->getRiskStatus()) {
            case 'ON_TIME':
                // Scope that search for the ON_TIME cases
                $query->onTime();
                break;
            case 'AT_RISK':
                // Scope that search for the AT_RISK cases
                $query->atRisk();
                break;
            case 'OVERDUE':
                // Scope that search for the OVERDUE cases
                $query->overdue();
                break;
        }

        if ($this->getProcessId() != '') {
            // Scope to search for an specific process
            $query->processId($this->getProcessId());
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
