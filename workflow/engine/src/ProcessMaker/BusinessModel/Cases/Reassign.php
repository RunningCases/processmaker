<?php

namespace ProcessMaker\BusinessModel\Cases;

use ProcessMaker\Model\Delegation;

class Reassign extends AbstractCases
{
    /**
     * Get the data corresponding to Reassign
     *
     * @return array
     */
    public function getData()
    {
        // Start the query for get the cases related to the user
        $query = Delegation::query()->select();

        // Scope that sets the queries for reassign
        if (!empty($this->getUserId())) {
            $query->inbox($this->getUserId());
        }

        // Scope to search for an specific process
        if (!empty($this->getProcessId())) {
            $query->processId($this->getProcessId());
        }

        // The order by clause
        $query->orderBy($this->getOrderByColumn(), $this->getOrderDirection());

        // The limit by clause
        $query->offset($this->getOffset())->limit($this->getLimit());

        // Execute the query
        $results = $query->get();

        // Return the values as an array format
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

        // Scope that sets the queries for reassign
        if (!empty($this->getUserId())) {
            $query->inbox($this->getUserId());
        } else {
            // Scope that sets the queries for List Inbox
            $query->inboxWithoutUser();
        }

        // Return the number of rows
        return $query->count();
    }

}