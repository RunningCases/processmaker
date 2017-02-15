<?php

require_once 'classes/model/om/BaseDashboard.php';


/**
 * Skeleton subclass for representing a row from the 'DASHBOARD' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class Dashboard extends BaseDashboard
{

    public function load ($dasUid)
    {
        try {
            $dashboard = DashboardPeer::retrieveByPK($dasUid);
            $fields = $dashboard->toArray(BasePeer::TYPE_FIELDNAME);
            $dashboard->fromArray( $fields, BasePeer::TYPE_FIELDNAME );
            return $fields;
        } catch (Exception $error) {
            throw $error;
        }
    }

    public function createOrUpdate($data)
    {
        $connection = Propel::getConnection(DashboardPeer::DATABASE_NAME);
        try {

            if (!isset($data['DAS_UID'])) {

                $dashboard = new Dashboard();
                $data['DAS_UID'] = G::generateUniqueID();
                $data['DAS_CREATE_DATE'] = date('Y-m-d H:i:s');
                $msg = 'Create ';
            } else {
                $msg = 'Update ';
                $dashboard = DashboardPeer::retrieveByPK($data['DAS_UID']);
            }
            if ((!isset($_SESSION['USER_LOGGED']) || $_SESSION['USER_LOGGED'] == '') && isset($data['USR_UID']) &&  $data['USR_UID'] != '') {
                $this->setUser($data['USR_UID']);
            }


            $filter = new InputFilter();
            if (isset($data['DAS_TITLE'])) {
                $data['DAS_TITLE'] = $filter ->validateInput($data['DAS_TITLE'], "string");
            }

            $data['DAS_UPDATE_DATE'] = date('Y-m-d H:i:s');
            $dashboard->fromArray($data, BasePeer::TYPE_FIELDNAME);
            if ($dashboard->validate()) {
                $connection->begin();
                $result = $dashboard->save();
                $connection->commit();

                G::auditLog($msg, "Dashboard  Name: " . $dashboard->getDasTitle() . " Dashboard  ID: (".$dashboard->getDasUid().") ");
                return $dashboard->getDasUid();
            } else {
                $message = '';
                $validationFailures = $dashboard->getValidationFailures();
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

    public function remove($dasUid, $userLogged = '')
    {
        $connection = Propel::getConnection(DashboardPeer::DATABASE_NAME);
        try {

            require_once 'classes/model/DashboardDasInd.php';
            $criteria = new Criteria('workflow');
            $criteria->add(DashboardDasIndPeer::DAS_UID, $dasUid);
            DashboardDasIndPeer::doDelete($criteria);

            require_once 'classes/model/DashboardIndicator.php';
            $criteria = new Criteria('workflow');
            $criteria->add(DashboardIndicatorPeer::DAS_UID, $dasUid);
            DashboardIndicatorPeer::doDelete($criteria);

            if ((!isset($_SESSION['USER_LOGGED']) || $_SESSION['USER_LOGGED'] == '') && $userLogged != '') {
                $this->setUser($userLogged);
            }

            $dashboard = DashboardPeer::retrieveByPK($dasUid);
            if (!is_null($dashboard)) {
                $connection->begin();
                $dashboardData = $this->load($dasUid);
                $result = $dashboard->delete();
                $connection->commit();

                G::auditLog("Delete", "Dashboard Name: ". $dashboardData['DAS_TITLE']." Dashboard ID: (".$dasUid.") ");
                return $result;
            } else {
                throw new Exception('Error trying to delete: The row "' .  $dasUid. '" does not exist.');
            }
        } catch (Exception $error) {
            $connection->rollback();
            throw $error;
        }
    }

    public function setUser($usrId) {
        $user = new Users ();
        $user = $user->loadDetails($usrId);
        $_SESSION['USER_LOGGED'] = $user['USR_UID'];
        $_SESSION['USR_FULLNAME'] = $user['USR_FULLNAME'];
    }
}

