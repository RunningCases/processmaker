<?php
namespace Services\Api\ProcessMaker\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\ReportTable Api Controller
 *
 * @author Brayan Pereyra <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 *
 * @protected
 */
class ReportTable extends Api
{
    /**
     * @param string $projectUid {@min 1} {@max 32}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     * @return array
     *
     * @url GET /:projectUid/report-tables
     */
    public function doGetReportTables($projectUid)
    {
        try {
            $oReportTable = new \BusinessModel\ReportTable();
            $response = $oReportTable->getReportTables($projectUid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $projectUid {@min 1} {@max 32}
     * @param string $rp_uid {@min 1} {@max 32}
     * @return array
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /:projectUid/report-table/:rp_uid
     */
    public function doGetReportTable($projectUid, $rp_uid)
    {
        try {
            $oReportTable = new \BusinessModel\ReportTable();
            $response = $oReportTable->getReportTable($projectUid, $rp_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $projectUid {@min 1} {@max 32}
     * @param array $request_data
     *
     * @param string $rep_tab_name {@from body}
     * @param string $rep_tab_dsc {@from body}
     * @param string $rep_tab_connection {@from body}
     * @param string $rep_tab_type {@from body}
     * @param string $rep_tab_grid {@from body}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url POST /:projectUid/report-table
     * @status 201
     */
    public function doPostReportTable(
        $projectUid,
        $request_data,
        $rep_tab_name,
        $rep_tab_dsc,
        $rep_tab_connection,
        $rep_tab_type,
        $rep_tab_grid = ''
    ) {
        try {
            $oReportTable = new \BusinessModel\ReportTable();
            $response = $oReportTable->createReportTable($projectUid, $request_data);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $projectUid {@min 1} {@max 32}
     * @param string $rp_uid {@min 1} {@max 32}
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
     * @url PUT /:projectUid/report-table/:rp_uid
     */
    public function doPutReportTable(
        $projectUid,
        $rp_uid,
        $request_data,
        $dbs_type,
        $dbs_server,
        $dbs_database_name,
        $dbs_username,
        $dbs_port,
        $dbs_encode,
        $dbs_password = '',
        $dbs_description = ''
    ) {
        try {
            $request_data['dbs_uid'] = $rp_uid;
            $oReportTable = new \BusinessModel\ReportTable();
            $response = $oReportTable->saveReportTable($projectUid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $projectUid {@min 1} {@max 32}
     * @param string $rp_uid {@min 1} {@max 32}
     * @return void
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url DELETE /:projectUid/report-table/:rp_uid
     */
    public function doDeleteReportTable($projectUid, $rp_uid)
    {
        try {
            $oReportTable = new \BusinessModel\ReportTable();
            $response = $oReportTable->deleteReportTable($projectUid, $rp_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}
