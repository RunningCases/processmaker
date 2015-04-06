<?php

require_once 'classes/model/om/BaseDashboardDasInd.php';


/**
 * Skeleton subclass for representing a row from the 'DASHBOARD_DAS_IND' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class DashboardDasInd extends BaseDashboardDasInd
{
    public function loadByDashboards ($dasUid)
    {
        try {

            $criteria = new Criteria('workflow');
            $criteria->add(DashboardDasIndPeer::DAS_UID, $dasUid);

            $dataset = DashboardDasIndPeer::doSelectRS($criteria);
            $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $fields = array();

            while ($dataset->next()) {
                $auxField = $dataset->getRow();
                $fields[] = $auxField;
            }

            return $fields;
        } catch (Exception $error) {
            throw $error;
        }
    }

    public function loadByOwner ($ownerUid)
    {
        try {

            $criteria = new Criteria('workflow');
            $criteria->add(DashboardDasIndPeer::OWNER_UID, $ownerUid);

            $dataset = DashboardDasIndPeer::doSelectRS($criteria);
            $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $fields = array();

            while ($dataset->next()) {
                $auxField = $dataset->getRow();
                $fields[] = $auxField;
            }

            return $fields;
        } catch (Exception $error) {
            throw $error;
        }
    }

    public function create($data)
    {
        $connection = Propel::getConnection(DashboardDasIndPeer::DATABASE_NAME);
        try {
            $dashboardDasInd = new DashboardDasInd();
            $dashboardDasInd->fromArray($data, BasePeer::TYPE_FIELDNAME);
            if ($dashboardDasInd->validate()) {
                $connection->begin();
                $result = $dashboardDasInd->save();
                $connection->commit();

                G::auditLog("Create", "Dashboard Owner: ". $data['OWNER_UID']."  Dashboard ID: (".$dashboardDasInd->getDasUid().") ");
                return $dashboardDasInd;
            } else {
                $message = '';
                $validationFailures = $dashboardDasInd->getValidationFailures();
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

    public function remove($dasUid, $owner)
    {
        $connection = Propel::getConnection(DashboardDasIndPeer::DATABASE_NAME);
        try {
            $dashboardDasInd = DashboardDasIndPeer::retrieveByPK($dasUid, $owner);
            if (!is_null($dashboardDasInd)) {
                $connection->begin();
                $result = $dashboardDasInd->delete();
                $connection->commit();

                G::auditLog("DeletedashboardIndicator", "Dashboard ID: ". $dasUid ." Dashboard owner ID: (".$owner.") ");
                return $result;
            } else {
                throw new Exception('Error trying to delete: The row "' .  $dasUid. '" does not exist.');
            }
        } catch (Exception $error) {
            $connection->rollback();
            throw $error;
        }
    }

    public function getOwnerByDashboard ($dasUid)
    {
        
    }

}

