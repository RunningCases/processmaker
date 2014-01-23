<?php
namespace BusinessModel;

use \G;
use \DbSource;
use \dbConnections;

class DataBaseConnection
{
    /**
     * List of DataBaseConnections in process
     * @var string $sProcessUid. Uid for Process
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function getDataBaseConnections($sProcessUid)
    {
        $oDBSource = new DbSource();
        $oCriteria = $oDBSource->getCriteriaDBSList($sProcessUid);
        
        $rs = \DbSourcePeer::doSelectRS($oCriteria);
        $rs->setFetchmode( \ResultSet::FETCHMODE_ASSOC );
        $rs->next();

        $dbConnecions = array();
        while ($row = $rs->getRow()) {
            $row = array_change_key_case($row, CASE_LOWER);
            $dataDb = $this->getDataBaseConnection($sProcessUid, $row['dbs_uid']);
            $dbConnecions[] = array_change_key_case($dataDb, CASE_LOWER);
            $rs->next();
        }
        return $dbConnecions;
    }

    /**
     * Get data for DataBaseConnection
     * @var string $sProcessUid. Uid for Process
     * @var string $dbConnecionUid. Uid for Data Base Connection
     *
     * return object
     */
    public function getDataBaseConnection($sProcessUid, $dbConnecionUid)
    {
        try {
            G::LoadClass( 'dbConnections' );
            $dbs = new dbConnections($sProcessUid);
            $oDBConnection = new DbSource();
            $aFields = $oDBConnection->load($dbConnecionUid, $sProcessUid);
            if ($aFields['DBS_PORT'] == '0') {
                $aFields['DBS_PORT'] = '';
            }
            $aFields['DBS_PASSWORD'] = $dbs->getPassWithoutEncrypt($aFields);

            $response = array_change_key_case($aFields, CASE_LOWER);
            return $response;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Save Data for DataBaseConnection
     * @var string $sProcessUid. Uid for Process
     * @var string $dataDataBaseConnection. Data for DataBaseConnection
     * @var string $create. Create o Update DataBaseConnection
     * @var string $sDataBaseConnectionUid. Uid for DataBaseConnection
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function saveDataBaseConnection($sProcessUid = '', $dataDBConnection = array(), $create = false)
    {
        G::LoadClass('dbConnections');
        $oDBSource = new DbSource();
        $oContent  = new \Content();
        $dataDBConnection = array_change_key_case($dataDBConnection, CASE_UPPER);

        $dataDBConnection['PRO_UID'] = $sProcessUid;

        if (isset($dataDBConnection['DBS_TYPE'])) {
            $typesExists = array();
            G::LoadClass( 'dbConnections' );
            $dbs = new dbConnections($sProcessUid);;
            $dbServices = $dbs->getDbServicesAvailables();
            foreach ($dbServices as $value) {
                $typesExists[] = $value['id'];
            }
            if (!in_array($dataDBConnection['DBS_TYPE'], $typesExists)) {
                throw (new \Exception("This 'dbs_type' is invalid"));
            }
        }

        if (isset($dataDBConnection['DBS_TYPE'])) {
            $typesExists = array();
            
            $dbs = new dbConnections($sProcessUid);;
            $dbServices = $dbs->getDbServicesAvailables();
            foreach ($dbServices as $value) {
                $typesExists[] = $value['id'];
            }
            if (!in_array($dataDBConnection['DBS_TYPE'], $typesExists)) {
                throw (new \Exception("This 'dbs_type' is invalid"));
            }
        }

        if (isset($dataDBConnection['DBS_ENCODE'])) {
            $encodesExists = array();
            $dbs = new dbConnections();
            $dbEncodes = $dbs->getEncondeList($dataDBConnection['DBS_TYPE']);
            foreach ($dbEncodes as $value) {
                $encodesExists[] = $value['0'];
            }
            if (!in_array($dataDBConnection['DBS_ENCODE'], $encodesExists)) {
                throw (new \Exception( "This 'dbs_encode' is invalid for '" . $dataDBConnection['DBS_TYPE'] . "'" ));
            }
        }

        $passOrigin = '';
        if (isset($dataDBConnection['DBS_PASSWORD'])) {
            $passOrigin = $dataDBConnection['DBS_PASSWORD'];
            if ($dataDBConnection['DBS_PASSWORD'] == 'none') {
                $dataDBConnection['DBS_PASSWORD'] = '';
            } else {
                $pass = G::encrypt( $dataDBConnection['DBS_PASSWORD'], $dataDBConnection['DBS_DATABASE_NAME']) . "_2NnV3ujj3w";
                $dataDBConnection['DBS_PASSWORD'] = $pass;
            }
        }

        if ($create) {
            unset($dataDBConnection['DBS_UID']);
            // TEST CONNECTION
            $dataTest = array_merge($dataDBConnection, array('DBS_PASSWORD' => $passOrigin));
            $resTest = $this->testConnection($dataTest);
            if (!$resTest['resp']) {
                throw (new \Exception($resTest['message']));
            }
            $newDBConnectionUid = $oDBSource->create($dataDBConnection);
            $oContent->addContent('DBS_DESCRIPTION', '', $newDBConnectionUid,
                SYS_LANG, $dataDBConnection['DBS_DESCRIPTION'] );
            $newDataDBConnection = $this->getDataBaseConnection($sProcessUid, $newDBConnectionUid);
            $newDataDBConnection = array_change_key_case($newDataDBConnection, CASE_LOWER);
            return $newDataDBConnection;
        } else {
            // TEST CONNECTION
            $allData = $this->getDataBaseConnection($sProcessUid, $dataDBConnection['DBS_UID']);
            $dataTest = array_merge($allData, $dataDBConnection);
            $resTest = $this->testConnection($dataTest);
            if (!$resTest['resp']) {
                throw (new \Exception($resTest['message']));
            }
            $oDBSource->update($dataDBConnection);
            if (isset($dataDBConnection['DBS_DESCRIPTION'])) {
                $oContent->addContent('DBS_DESCRIPTION', '', $dataDBConnection['DBS_UID'],
                    SYS_LANG, $dataDBConnection['DBS_DESCRIPTION'] );
            }
        }
        return array();
    }

    /**
     * Delete DataBaseConnection
     * @var string $sDataBaseConnectionUID. Uid for DataBaseConnection
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return void
     */
    public function deleteDataBaseConnection($sProcessUid, $dbConnecionUid)
    {
        $oDBSource = new DbSource();
        $oContent  = new \Content();

        $oDBSource->remove($dbConnecionUid, $sProcessUid);
        $oContent->removeContent( 'DBS_DESCRIPTION', "", $dbConnecionUid );
    }


    public function testConnection ($dataCon) {
        $resp = array();
        $resp['resp'] = false;

        G::LoadClass( 'net' );
        $Server = new \NET($dataCon['DBS_SERVER']);

        // STEP 1 : Resolving Host Name
        if ($Server->getErrno() != 0) {
            $resp['message'] = "Error Testting Connection: Resolving Host Name FAILED : " . $Server->error;
            return $resp;
        }

        // STEP 2 : Checking port
        $Server->scannPort($dataCon['DBS_PORT']);
        if ($Server->getErrno() != 0) {
            $resp['message'] = "Error Testting Connection: Checking port FAILED : " . $Server->error;
            return $resp;
        }

        // STEP 3 : Trying to connect to host
        $Server->loginDbServer($dataCon['DBS_USERNAME'], $dataCon['DBS_PASSWORD']);
        $Server->setDataBase($dataCon['DBS_DATABASE_NAME'], $dataCon['DBS_PORT']);
        if ($Server->errno == 0) {
            $response = $Server->tryConnectServer($dataCon['DBS_TYPE']);
            if ($response->status != 'SUCCESS') {
                $resp['message'] = "Error Testting Connection: Trying to connect to host FAILED : " . $Server->error;
                return $resp;
            }
        } else {
            $resp['message'] = "Error Testting Connection: Trying to connect to host FAILED : " . $Server->error;
            return $resp;
        }
                
        // STEP 4 : Trying to open database
        $Server->loginDbServer($dataCon['DBS_USERNAME'], $dataCon['DBS_PASSWORD']);
        $Server->setDataBase($dataCon['DBS_DATABASE_NAME'], $dataCon['DBS_PORT']);
        if ($Server->errno == 0) {
            $response = $Server->tryConnectServer($dataCon['DBS_TYPE']);
            if ($response->status == 'SUCCESS') {
                $response = $Server->tryOpenDataBase($dataCon['DBS_TYPE']);
                if ($response->status != 'SUCCESS') {
                    $resp['message'] = "Error Testting Connection: Trying to open database FAILED : " . $Server->error;
                    return $resp;
                }
            } else {
                $resp['message'] = "Error Testting Connection: Trying to open database FAILED : " . $Server->error;
                return $resp;
            }
        } else {
            $resp['message'] = "Error Testting Connection: Trying to open database FAILED : " . $Server->error;
            return $resp;
        }

        // CORRECT CONNECTION
        $resp['resp'] = true;
        return $resp;
    }
}

