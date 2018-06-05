<?php

namespace ProcessMaker\ChangeLog;

use Cases;
use G;
use ProcessMaker\ChangeLog\LogStruct;
use Propel;

class ChangeLogResult
{
    /**
     * Reserved steps.
     * 
     * @var array
     */
    private $reservedSteps = [
        -1,
        -2,
    ];

    /**
     * Variables to exclude.
     * 
     * @var array
     */
    private $excludeVariables = [
        'SYS_LANG',
        'SYS_SKIN',
        'SYS_SYS',
        'APPLICATION',
        'PROCESS',
        'TASK',
        'INDEX',
        'USER_LOGGED',
        'USR_USERNAME',
        'APP_NUMBER',
        'PIN',
        'DYN_CONTENT_HISTORY',
        '__VAR_CHANGED__',
    ];

    /**
     * Permissions.
     * 
     * @var array
     */
    private $permissions = [];

    /**
     * Identifier of the application.
     * 
     * @var string
     */
    private $appUid;

    /**
     * Identifier of the process.
     * 
     * @var string
     */
    private $proUid;

    /**
     * Identifier of the task.
     * 
     * @var string
     */
    private $tasUid;

    /**
     * User logged.
     *
     * @var string
     */
    private $userLogged;

    /**
     * Set appUid.
     * 
     * @param string $appUid
     * @return object
     */
    public function setAppUid($appUid)
    {
        $this->appUid = $appUid;
        return $this;
    }

    /**
     * Set proUid.
     * 
     * @param string $proUid
     * @return object
     */
    public function setProUid($proUid)
    {
        $this->proUid = $proUid;
        return $this;
    }

    /**
     * Set tasUid.
     * 
     * @param string $tasUid
     * @return object
     */
    public function setTasUid($tasUid)
    {
        $this->tasUid = $tasUid;
        return $this;
    }

    /**
     * Set userLogged.
     * 
     * @param string $userLogged
     * @return object
     */
    public function setUserLogged($userLogged)
    {
        $this->userLogged = $userLogged;
        return $this;
    }

    /**
     * Get logs.
     * 
     * @return array
     */
    public function getLogs()
    {
        $cases = new Cases();
        $this->permissions = $cases->getAllObjects($this->proUid, $this->appUid, $this->tasUid, $this->userLogged);

        $logs = [];
        $totalCount = 0;
        $values = [];

        $this->getLogsFromDataBase($this->appUid, function($row) use(&$logs, &$totalCount, &$values) {
            $appData = $this->getAppData($row['DATA']);
            $this->removeVariables($appData);

            $hasPermission = $this->hasPermission($row['DYN_UID']);
            if ((int) $row['SOURCE_ID'] === ChangeLog::FromABE) {
                $hasPermission = true;
            }

            $count = 0;
            foreach ($appData as $key => $value) {
                if ($hasPermission && (!isset($values[$key]) || $values[$key] !== $value)) {

                    $previousValue = !isset($values[$key]) ? null : $values[$key];
                    $record = ''
                            . G::LoadTranslation('ID_TASK') . ': ' . $row['TAS_TITLE'] . ' / '
                            . G::LoadTranslation('ID_DYNAFORM') . ': ' . $row['DYN_TITLE'] . ' / '
                            . G::LoadTranslation('ID_LAN_UPDATE_DATE') . ': ' . $row['DATE'] . ' / '
                            . G::LoadTranslation('ID_USER') . ': ' . $row['USR_USERNAME'] . ' / '
                            . G::LoadTranslation('ID_FROM') . ': ' . ChangeLog::getChangeLog()->getApplicationNameById($row['SOURCE_ID']);

                    $struct = new LogStruct();
                    $struct->setField($key)
                            ->setPreviousValue($this->toString($previousValue))
                            ->setCurrentValue($this->toString($value))
                            ->setPreviousValueType(gettype($previousValue))
                            ->setCurrentValueType(gettype($value))
                            ->setRecord($record);

                    $logs[] = $struct->getValues();
                    $count++;
                }
                $values[$key] = $value;
            }
            $totalCount = $totalCount + $count;
        });

        return [
            'data' => $logs,
            'totalCount' => $totalCount
        ];
    }

    /**
     * Get logs from Database.
     * 
     * @param type $appUid
     * @param type $callback
     */
    public function getLogsFromDataBase($appUid, $callback = null)
    {
        $conn = Propel::getConnection('workflow');
        $sql = ""
                . "SELECT "
                . "A.CHANGE_LOG_ID, "
                . "D.TAS_TITLE, "
                . "C.PRO_TITLE,  "
                . "IF(F.DYN_UID IS NULL,'N/A',F.DYN_TITLE) AS 'DYN_TITLE', "
                . "F.DYN_UID, "
                . "A.DATE,  "
                . "E.USR_USERNAME, "
                . "A.APP_NUMBER, "
                . "A.DEL_INDEX, "
                . "A.PRO_ID, "
                . "A.TAS_ID, "
                . "A.USR_ID, "
                . "A.OBJECT_ID, "
                . "A.OBJECT_UID, "
                . "A.EXECUTED_AT, "
                . "A.SOURCE_ID, "
                . "A.DATA, "
                . "A.SKIN, "
                . "A.LANGUAGE "
                . "FROM APP_DATA_CHANGE_LOG AS A "
                . "INNER JOIN APPLICATION AS B ON (B.APP_NUMBER=A.APP_NUMBER AND B.APP_UID=? ) "
                . "LEFT JOIN PROCESS AS C ON (C.PRO_ID=A.PRO_ID) "
                . "LEFT JOIN TASK AS D ON (D.TAS_ID=A.TAS_ID) "
                . "LEFT JOIN USERS AS E ON (E.USR_ID=A.USR_ID) "
                . "LEFT JOIN DYNAFORM AS F ON (F.DYN_ID=A.OBJECT_ID AND A.OBJECT_TYPE=" . ChangeLog::DYNAFORM . ") "
                . "ORDER BY A.DATE ASC ";

        $stmt = $conn->prepareStatement($sql);
        $stmt->set(1, $appUid);
        $stmt->executeQuery();
        $result = $stmt->getResultSet();
        while ($result->next()) {
            $row = $result->getRow();
            if (!empty($callback) && is_callable($callback)) {
                $callback($row);
            }
        }
    }

    /**
     * Get appData from value serialized.
     * 
     * @param string $appDataString
     * @return array
     */
    private function getAppData($appDataString)
    {
        $case = new Cases();
        $appData = $case->unserializeData($appDataString);
        if (!is_array($appData)) {
            $appData = [];
        }
        return $appData;
    }

    /**
     * Remove all values from the array that are in the excludeVariables property.
     * 
     * @param array $appData
     */
    private function removeVariables(&$appData)
    {
        foreach ($this->excludeVariables as $value) {
            unset($appData[$value]);
        }
    }

    /**
     * Has permission.
     * 
     * @param string $uid
     * @return boolean
     */
    private function hasPermission($uid)
    {
        if (array_search($uid, $this->reservedSteps) !== false) {
            return false;
        }
        foreach ($this->permissions as $ids) {
            if (is_array($ids) && array_search($uid, $ids) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the print string from the value.
     * 
     * @param mixed $value
     * @return string
     */
    private function toString($value)
    {
        if (is_array($value)) {
            return nl2br(print_r($value, true));
        } else {
            return (string) $value;
        }
    }
}
