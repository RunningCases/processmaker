<?php

namespace ProcessMaker\BusinessModel\Cases;

use Propel;
use StdClass;
use G;
use Cases;
use AppDocument;
use Dynaform;
use Exception;
use Task;

/**
 * Return the ChangeLog of a Dynaform
 * http://localhost:8083/sysworkflow/en/neoclassic/cases/casesHistoryDynaformPage_Ajax?actionAjax=showDynaformListHistory&PRO_UID=23134954556f0727c3fde11063016937&APP_UID=93748078756f16cd8e665b7099829333&TAS_UID=16304814956f0729495da25001454322&DYN_UID=12434578956f07308e54d44056464541
 * http://localhost:8083/sysworkflow/en/neoclassic/cases/ajaxListener?action=changeLogTab&idHistory=23134954556f0727c3fde11063016937_48789444056f0747d7e2e19034608525_16304814956f0729495da25001454322
 */
class ChangeLog
{
    /**
     * List of variables that should not be considered
     * @var string[]
     */
    private $reserved = [
        'TASK',
        'INDEX',
        'DYN_CONTENT_HISTORY'
    ];

    /**
     * Map of variables and its values
     * @var mixed[]
     */
    private $values = [];

    /**
     * List of variables changes
     * @var object[]
     */
    private $tree;

    /**
     * List of assigned permissions
     * @var string[]
     */
    private $permissions = [];

    public function getChangeLog($appUid, $proUid, $tasUid, $start,
                                 $limit)
    {
        $this->loadPermissions($appUid, $proUid, $tasUid);
        $result = $this->getResultSet($appUid);
        $totalCount = $this->readRecords($result, $start, $limit);
        return ['data' => $this->tree, 'totalCount' => $totalCount];
    }

    private function getResultSet($appUid)
    {
        $conn = Propel::getConnection('workflow');
        $stmt = $conn->createStatement();
        $sql = 'SELECT APP_HISTORY.*, USERS.USR_USERNAME FROM APP_HISTORY LEFT JOIN USERS ON(APP_HISTORY.USR_UID=USERS.USR_UID)'
            . ' WHERE APP_UID="'.$appUid.'" ORDER BY HISTORY_DATE ASC';
        if (!$stmt->execute($sql)) {
            throw Exception('Unable to read history');
        }
        return $stmt->getResultSet();
    }

    private function readRecords($result, $start = 0, $limit = 15)
    {
        $index = 0;
        while ($result->next()) {
            $row = $result->getRow();
            $data = unserialize($row['HISTORY_DATA']);
            if ($this->isEmpty($data)) {
                continue;
            }
            if ($index < $start) {
                $index += $this->updateData($data, $row,
                                            $this->hasPermission($row['DYN_UID']),
                                                                 false);
                continue;
            }
            $a = $this->updateData($data, $row,
                                   $this->hasPermission($row['DYN_UID']), true);
            $limit-= $a;
            $index+= $a;
            error_log("+$a = $index / $limit");
            if ($limit < 0) {
                $index+=1;
                break;
            }
        }
        return $index;
    }

    private function isEmpty($data)
    {
        foreach ($data as $key => $value) {
            if (array_search($key, $this->reserved) !== false) {
                continue;
            }
            return false;
        }
        return true;
    }

    private function updateData($data, $row, $hasPermission, $addToTree = false)
    {
        $i = 0;
        foreach ($data as $key => $value) {
            if (array_search($key, $this->reserved) !== false) {
                continue;
            }
            if ($hasPermission && (!isset($this->values[$key]) || $this->values[$key] !== $value)) {
                if ($addToTree) {
                    $node = new StdClass();
                    $node->field = $key;
                    $previousValue = !isset($this->values[$key]) ? null : $this->values[$key];
                    $node->previousValue = (string) $previousValue;
                    $node->currentValue = (string) $value;
                    $node->previousValueType = gettype($previousValue);
                    $node->currentValueType = gettype($value);
                    $node->record = $this->getHistoryTitle($row);
                    $this->tree[] = $node;
                }
//                error_log($key);
                $i++;
            }
            $this->values[$key] = $value;
        }
        return $i;
    }

    private function getHistoryTitle($row)
    {
        return $this->getObjectTitle($row['TAS_UID'], 'TASK')
            .' / '.$this->getObjectTitle($row['DYN_UID'], $row['OBJ_TYPE'])
            .' / '.G::LoadTranslation('ID_LAN_UPDATE_DATE').': '.$row['HISTORY_DATE']
            .' / '.G::LoadTranslation('ID_USER').': '.$row['USR_USERNAME'];
    }

    private function getObjectTitle($uid, $objType)
    {
        switch ($objType) {
            case 'DYNAFORM':
                $obj = new Dynaform();
                $obj->Load($uid);
                $title = $obj->getDynTitle();
                break;
            case 'OUTPUT_DOCUMENT':
            case 'INPUT_DOCUMENT':
                $obj = new AppDocument();
                $obj->load($uid);
                $title = $obj->getDynTitle();
                break;
            case 'TASK':
                $obj = new Task();
                $obj->load($uid);
                $title = $obj->getTasTitle();
                break;
            default:
                $title = $uid;
        }
        return $title;
    }

    private function loadPermissions($APP_UID, $PRO_UID, $TAS_UID)
    {
        G::LoadClass('case');
        $oCase = new Cases();
        $oCase->verifyTable();
        $this->permissions = $oCase->getAllObjects(
            $PRO_UID, $APP_UID, $TAS_UID, $_SESSION['USER_LOGGED']
        );
    }

    private function hasPermission($uid)
    {
        foreach ($this->permissions as $type => $ids) {
            if (array_search($uid, $ids) !== false) {
                return true;
            }
        }
        return false;
    }
}