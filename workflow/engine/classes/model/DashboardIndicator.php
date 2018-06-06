<?php

require_once 'classes/model/om/BaseDashboardIndicator.php';


/**
 * Skeleton subclass for representing a row from the 'DASHBOARD_INDICATOR' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class DashboardIndicator extends BaseDashboardIndicator
{
    public function load($dasIndUid)
    {
        try {
            $dashboardIndicator = DashboardIndicatorPeer::retrieveByPK($dasIndUid);
            $fields = $dashboardIndicator->toArray(BasePeer::TYPE_FIELDNAME);
            $dashboardIndicator->fromArray($fields, BasePeer::TYPE_FIELDNAME);
            return $fields;
        } catch (Exception $error) {
            throw $error;
        }
    }

    public function loadbyDasUid($dasUid, $vcompareDate, $vmeasureDate, $userUid)
    {
        $calculator = new \IndicatorsCalculator();

        try {
            $connection = Propel::getConnection('workflow');
            $qryString = "select * from CONFIGURATION where CFG_UID = 'DASHBOARDS_SETTINGS' and USR_UID = '$userUid'";
            $qry = $connection->PrepareStatement($qryString);
            $dataSet = $qry->executeQuery();
            $dashConfig = array();
            while ($dataSet->next()) {
                $row = $dataSet->getRow();
                $dashConfig = unserialize($row['CFG_VALUE']);
            }

            $criteria = new Criteria('workflow');
            $criteria->clearSelectColumns()->clearOrderByColumns();

            $criteria->add(DashboardIndicatorPeer::DAS_UID, $dasUid, criteria::EQUAL);

            $rs = DashboardIndicatorPeer::doSelectRS($criteria);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $dashboardIndicator = array();

            while ($rs->next()) {
                $row = $rs->getRow();

                $measureDate = new DateTime($vmeasureDate);
                $compareDate = new DateTime($vcompareDate);
                $uid = ($row['DAS_UID_PROCESS'] == '0' ? null : $row['DAS_UID_PROCESS']);
                switch ($row['DAS_IND_TYPE']) {
                    case '1010':
                        $value = $calculator->peiHistoric($uid, $measureDate, $measureDate, \ReportingPeriodicityEnum::NONE);
                        $value = reset($value);
                        $value = current($value);
                        $oldValue = $calculator->peiHistoric($uid, $compareDate, $compareDate, \ReportingPeriodicityEnum::NONE);
                        $oldValue = reset($oldValue);
                        $oldValue = current($oldValue);
                        $row['DAS_IND_VARIATION'] = $value - $oldValue;
                        $row['DAS_IND_OLD_VALUE'] = $oldValue;
                        $row['DAS_IND_PERCENT_VARIATION'] = $oldValue != 0
                            ? round(($value - $oldValue) * 100 / $oldValue)
                            : "--";
                        break;
                    case '1030':
                        $value = $calculator->ueiHistoric(null, $measureDate, $measureDate, \ReportingPeriodicityEnum::NONE);
                        $value = reset($value);
                        $value = current($value);
                        $oldValue = $calculator->ueiHistoric($uid, $compareDate, $compareDate, \ReportingPeriodicityEnum::NONE);
                        $oldValue = reset($oldValue);
                        $oldValue = current($oldValue);
                        $row['DAS_IND_VARIATION'] = $value - $oldValue;
                        $row['DAS_IND_OLD_VALUE'] = $oldValue;
                        $row['DAS_IND_PERCENT_VARIATION'] = $oldValue != 0
                            ? round(($value - $oldValue) * 100 / $oldValue)
                            : "--";
                        break;
                    case '1050':
                        $value = $calculator->statusIndicatorGeneral($userUid);
                        $row['OVERDUE'] = 0;
                        $row['ON_TIME'] = 0;
                        $row['AT_RISK'] = 0;
                        $row['PERCENTAGE_OVERDUE'] = 0;
                        $row['PERCENTAGE_AT_RISK'] = 0;
                        $row['PERCENTAGE_ON_TIME'] = 0;

                        if (is_array($value) && isset($value[0])) {
                            $row['OVERDUE'] = $value[0]['OVERDUE'];
                            $row['ON_TIME'] = $value[0]['ONTIME'];
                            $row['AT_RISK'] = $value[0]['ATRISK'];

                            $total = $row['OVERDUE'] + $row['AT_RISK'] + $row['ON_TIME'];
                            if ($total != 0) {
                                $row['PERCENTAGE_OVERDUE'] = ($row['OVERDUE'] * 100) / $total;
                                $row['PERCENTAGE_AT_RISK'] = ($row['AT_RISK'] * 100) / $total;
                                $row['PERCENTAGE_ON_TIME'] = ($row['ON_TIME'] * 100) / $total;
                            }
                        }
                        break;
                    default:
                        $arrResult = $calculator->generalIndicatorData($row['DAS_IND_UID'], $measureDate, $measureDate, \ReportingPeriodicityEnum::NONE);
                        $value = $arrResult[0]['value'];
                        $row['DAS_IND_VARIATION'] = $row['DAS_IND_GOAL'];
                        break;
                }
                $row['DAS_IND_VALUE'] = $value;

                $indId = $row['DAS_IND_UID'];
                $row['DAS_IND_X'] = 0;
                $row['DAS_IND_Y'] = 0;
                $row['DAS_IND_WIDTH'] = 0;
                $row['DAS_IND_HEIGHT'] = 0;
                $row['DAS_IND_FAVORITE'] = 0;

                foreach ($dashConfig as $dashId => $oneDash) {
                    if ($dashId == $dasUid && is_array($oneDash['dashData'])) {
                        foreach ($oneDash['dashData'] as $graphConfig) {
                            if ($graphConfig['indicatorId'] == $indId) {
                                $row['DAS_IND_X'] = $graphConfig['x'];
                                $row['DAS_IND_Y'] = $graphConfig['y'];
                                $row['DAS_IND_WIDTH'] = $graphConfig['width'];
                                $row['DAS_IND_HEIGHT'] = $graphConfig['height'];
                            }
                        }
                    }
                }

                $dashboardIndicator[] = $row;
            }
            return $dashboardIndicator;
        } catch (Exception $error) {
            throw $error;
        }
    }


    public function createOrUpdate($data)
    {
        $connection = Propel::getConnection(DashboardIndicatorPeer::DATABASE_NAME);
        try {
            if (!isset($data['DAS_IND_UID'])) {
                $data['DAS_IND_UID'] = G::generateUniqueID();
                $data['DAS_IND_CREATE_DATE'] = date('Y-m-d H:i:s');
                $dashboardIndicator = new DashboardIndicator();
                $msg = 'Create';
            } else {
                $msg = 'Update';
                $dashboardIndicator = DashboardIndicatorPeer::retrieveByPK($data['DAS_IND_UID']);
            }
            $data['DAS_IND_UPDATE_DATE'] = date('Y-m-d H:i:s');
            $dashboardIndicator->fromArray($data, BasePeer::TYPE_FIELDNAME);
            if ($dashboardIndicator->validate()) {
                $connection->begin();
                $result = $dashboardIndicator->save();
                $connection->commit();

                if ((!isset($_SESSION['USER_LOGGED']) || $_SESSION['USER_LOGGED'] == '') && isset($data['USR_UID']) && $data['USR_UID'] != '') {
                    $this->setUser($data['USR_UID']);
                }
                G::auditLog($msg, "Dashboard Indicator Name: " . $dashboardIndicator->getDasIndTitle() . " Dashboard indicator ID: (" . $dashboardIndicator->getDasIndUid() . ") ");
                return $dashboardIndicator->getDasIndUid();
            } else {
                $message = '';
                $validationFailures = $dashboardIndicator->getValidationFailures();
                foreach ($validationFailures as $validationFailure) {
                    $message .= $validationFailure->getMessage() . '. ';
                }
                throw(new Exception(G::LoadTranslation("ID_RECORD_CANNOT_BE_CREATED", SYS_LANG) . ' ' . $message));
            }
        } catch (Exception $error) {
            $connection->rollback();
            throw $error;
        }
    }

    public function remove($dasIndUid, $userLogged = '')
    {
        $connection = Propel::getConnection(DashboardIndicatorPeer::DATABASE_NAME);
        try {
            $dashboardIndicator = DashboardIndicatorPeer::retrieveByPK($dasIndUid);
            if (!is_null($dashboardIndicator)) {
                $connection->begin();
                $dashboardIndicatorData = $this->load($dasIndUid);
                $result = $dashboardIndicator->delete();
                $connection->commit();

                if ((!isset($_SESSION['USER_LOGGED']) || $_SESSION['USER_LOGGED'] == '') && $userLogged != '') {
                    $this->setUser($userLogged);
                }
                G::auditLog("Delete", "Dashboard Indicator Name: " . $dashboardIndicatorData['DAS_IND_TITLE'] . " Dashboard Instance ID: (" . $dasIndUid . ") ");
                return $result;
            } else {
                throw new Exception('Error trying to delete: The row "' . $dasIndUid . '" does not exist.');
            }
        } catch (Exception $error) {
            $connection->rollback();
            throw $error;
        }
    }

    public function setUser($usrId)
    {
        $user = new Users();
        $user = $user->loadDetails($usrId);
        $_SESSION['USER_LOGGED'] = $user['USR_UID'];
        $_SESSION['USR_FULLNAME'] = $user['USR_FULLNAME'];
    }
}
