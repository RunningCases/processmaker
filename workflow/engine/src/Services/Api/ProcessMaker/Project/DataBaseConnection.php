<?php
namespace Services\Api\ProcessMaker\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\DataBaseConnection Api Controller
 *
 * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 *
 * @protected
 */
class DataBaseConnection extends Api
{
    /**
     * @param string $projectUid {@min 1} {@max 32}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     * @return array
     *
     * @url GET /:projectUid/database-connections
     */
    public function doGetDataBaseConnections($projectUid)
    {
        try {
            $oDBConnection = new \BusinessModel\DataBaseConnection();
            $response = $oDBConnection->getDataBaseConnections($projectUid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $projectUid {@min 1} {@max 32}
     * @param string $dbConnecionUid {@min 1} {@max 32}
     * @return array
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /:projectUid/database-connection/:dbConnecionUid
     */
    public function doGetDataBaseConnection($projectUid, $dbConnecionUid)
    {
        try {
            $oDBConnection = new \BusinessModel\DataBaseConnection();
            $response = $oDBConnection->getDataBaseConnection($projectUid, $dbConnecionUid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $projectUid {@min 1} {@max 32}
     * @param array $request_data
     *
     * @param string $dbs_type {@from body}
     * @param string $dbs_server {@from body}
     * @param string $dbs_database_name {@from body}
     * @param string $dbs_username {@from body}
     * @param string $dbs_port {@from body}
     * @param string $dbs_encode {@from body}
     * @param string $dbs_password {@from body}
     * @param string $dbs_description {@from body}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url POST /:projectUid/database-connection
     * @status 201
     */
    public function doPostDataBaseConnection($projectUid, $request_data, $dbs_type, $dbs_server,
        $dbs_database_name, $dbs_username, $dbs_port, $dbs_encode, $dbs_password = '', $dbs_description = '')
    {
        try {
            $oDBConnection = new \BusinessModel\DataBaseConnection();
            $response = $oDBConnection->saveDataBaseConnection($projectUid, $request_data, true);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $projectUid {@min 1} {@max 32}
     * @param string $dbConnecionUid {@min 1} {@max 32}
     * @param array $request_data
     *
     * @param string $dbs_type {@from body}
     * @param string $dbs_server {@from body}
     * @param string $dbs_database_name {@from body}
     * @param string $dbs_username {@from body}
     * @param string $dbs_port {@from body}
     * @param string $dbs_encode {@from body}
     * @param string $dbs_password {@from body}
     * @param string $dbs_description {@from body}
     * @return void
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url PUT /:projectUid/database-connection/:dbConnecionUid
     */
    public function doPutDataBaseConnection($projectUid, $dbConnecionUid, $request_data, $dbs_type, $dbs_server,
        $dbs_database_name, $dbs_username, $dbs_port, $dbs_encode, $dbs_password = '', $dbs_description = '')
    {
        try {
            $request_data['dbs_uid'] = $dbConnecionUid;
            $oDBConnection = new \BusinessModel\DataBaseConnection();
            $response = $oDBConnection->saveDataBaseConnection($projectUid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $projectUid {@min 1} {@max 32}
     * @param string $dbConnecionUid {@min 1} {@max 32}
     * @return void
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url DELETE /:projectUid/database-connection/:dbConnecionUid
     */
    public function doDeleteDataBaseConnection($projectUid, $dbConnecionUid)
    {
        try {
            $oDBConnection = new \BusinessModel\DataBaseConnection();
            $response = $oDBConnection->deleteDataBaseConnection($projectUid, $dbConnecionUid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}


