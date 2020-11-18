<?php

namespace ProcessMaker\BusinessModel\Cases;

class MyCases extends AbstractCases
{
    /**
     * Gets the data for the Cases list My Cases
     *
     * @return array
     */
    public function getData()
    {
        $filter = $this->getParticipatedStatus();
        $result = [];
        if (!empty($filter)) {
            switch ($filter) {
                case 'STARTED':
                case 'IN_PROGRESS':
                case 'COMPLETED':
                    $list = new Participated();
                    $result = $list->getData();
                    break;
                case 'SUPERVISING':
                    // Scope that search for the SUPERVISING cases by specific user
                    $list = new Supervising();
                    $result = $list->getData();
                    break;
            }
        }

        return $result;
    }

    /**
     * Gets the total of My Cases
     *
     * @return int
     */
    public function getCounter()
    {
        $filter = $this->getParticipatedStatus();
        $count = 0;
        if (!empty($filter)) {
            switch ($filter) {
                case 'STARTED':
                case 'IN_PROGRESS':
                case 'COMPLETED':
                    $list = new Participated();
                    $count = $list->getCounter();
                    break;
                case 'SUPERVISING':
                    // Scope that search for the SUPERVISING cases by specific user
                    $list = new Supervising();
                    $count = $list->getCounter();
                    break;
            }
        }

        return $count;
    }
}
