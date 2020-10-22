<?php

namespace ProcessMaker\BusinessModel\Cases;

use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\ProcessUser;

class Supervising extends AbstractCases
{
    /**
     * Gets the data for the Cases list Review
     * 
     * @return array
     */
    public function getData()
    {
        //Get the list of processes of the supervisor 
        $processes = ProcessUser::getProcessesOfSupervisor($this->getUserUid());

        // Start tthe query for get the cases related to the user
        $query = Delegation::query()->select();

        $query->inbox($this->getUserId());

        $query->categoryProcess($this->getCategoryUid());

        $query->processInList($processes);

        $query->joinPreviousIndex();

        $query->joinPreviousUser();

        if (!empty($this->getCaseNumber())) {
            $query->case($this->getCaseNumber());
        }

        if (!empty($this->getProcessId())) {
            $query->processId($this->getProcessId());
        }

        //The order by clause
        $query->orderBy($this->getOrderByColumn(), $this->getOrderDirection());

        //The limit clause
        $query->offset($this->getOffset())->limit($this->getLimit());

        return $query->get()->values()->toArray();
    }

    /**
     * Gets the total of Review cases
     * 
     * @return int
     */
    public function getCounter()
    {
        $quantity = $this->getData();
        return count($quantity);
    }
}
