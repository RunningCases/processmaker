<?php

namespace ProcessMaker\BusinessModel\Cases;

use ProcessMaker\Model\Delegation;

class Paused extends AbstractCases
{
    /**
     * Gets the data for the paused cases list
     * 
     * @return array
     */
    public function getData()
    {
        $query = Delegation::query()->select();
        $query->paused($this->getUserId(), $this->getCategoryUid(), $this->getTaskId(), $this->getCaseNumber());
        $query->joinPreviousIndex();
        $query->joinPreviousUser();
        $query->orderBy($this->getOrderByColumn(), $this->getOrderDirection());
        $query->offset($this->getOffset())->limit($this->getLimit());

        $result = $query->get()->values()->toArray();

        return $result;
    }

    /**
     * Get the total for the paused cases list
     * 
     * @return int
     */
    public function getCounter()
    {
        $total = $this->getData();

        return count($total);
    }
}
