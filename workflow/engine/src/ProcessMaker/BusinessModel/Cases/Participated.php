<?php

namespace ProcessMaker\BusinessModel\Cases;

use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;

class Participated extends AbstractCases
{
    /**
     * Get the data corresponding to Participated
     *
     * @return array
     */
    public function getData()
    {
        // Start the query for get the cases related to the user
        $query = Delegation::query()->select();
        // Scope to Participated
        $query->participated($this->getUserId());

        // Scope to search for an specific process
        if (!empty($this->getProcessId())) {
            $query->processId($this->getProcessId());
        }

        // Scope the specific category
        $category = $this->getCategoryUid();
        if (!empty($category)) {
            $query->categoryProcess($category);
        }

        // Scope the specific case status
        $status = $this->getCaseStatus();
        if (array_key_exists($status, Application::$app_status_values)) {
            $statusId = Application::$app_status_values[$status];
            $query->appStatusId($statusId);
        }

        // Add filter
        $filter = $this->getParticipatedStatus();
        if (!empty($filter)) {
            switch ($filter) {
                case 'STARTED':
                    // Scope that search for the STARTED cases by specific user
                    $query->caseStarted();
                    break;
                case 'COMPLETED':
                    // Scope that search for the COMPLETED cases by specific user
                    $query->caseCompleted();
                    break;
            }
        }

        // The order by clause
        $query->orderBy($this->getOrderByColumn(), $this->getOrderDirection());

        // The limit by clause
        $query->offset($this->getOffset())->limit($this->getLimit());

        //Execute the query
        $result = $query->get();

        //Return the values as an array format
        return $result->values()->toArray();
    }
    /**
     * Get the number of rows corresponding to the Participate
     *
     * @return int
     */
    public function getCounter()
    {
        $query = Delegation::query()->select();
        // Scope that sets the queries for Participated
        $query->participated($this->getUserId());
        // Return the number of rows
        return $query->count();
    }
}
