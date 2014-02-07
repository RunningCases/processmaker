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
     * @param string $prj_uid {@min 1} {@max 32}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     * @return array
     *
     * @url GET /:prj_uid/report-tables
     */
    public function doGetReportTables($prj_uid)
    {
        try {
            $oReportTable = new \BusinessModel\ReportTable();
            $response = $oReportTable->getReportTables($prj_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $prj_uid {@min 1} {@max 32}
     * @param string $rep_uid {@min 1} {@max 32}
     * @return array
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /:prj_uid/report-table/:rep_uid
     */
    public function doGetReportTable($prj_uid, $rep_uid)
    {
        try {
            $oReportTable = new \BusinessModel\ReportTable();
            $response = $oReportTable->getReportTable($prj_uid, $rep_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $prj_uid {@min 1} {@max 32}
     * @param string $rep_uid {@min 1} {@max 32}
     * @return array
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /:prj_uid/report-table/:rep_uid/populate
     */
    public function doGetPopulateReportTable($prj_uid, $rep_uid)
    {
        try {
            $oReportTable = new \BusinessModel\ReportTable();
            $response = $oReportTable->generateDataReport($prj_uid, $rep_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $prj_uid {@min 1} {@max 32}
     * @param string $rep_uid {@min 1} {@max 32}
     * @return array
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /:prj_uid/report-table/:rep_uid/data
     */
    public function doGetReportTableData($prj_uid, $rep_uid)
    {
        try {
            $oReportTable = new \BusinessModel\ReportTable();
            $response = $oReportTable->getDataReportTableData($prj_uid, $rep_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $prj_uid {@min 1} {@max 32}
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
     * @url POST /:prj_uid/report-table
     * @status 201
     */
    public function doPostReportTable(
        $prj_uid,
        $request_data,
        $rep_tab_name,
        $rep_tab_dsc,
        $rep_tab_connection,
        $rep_tab_type,
        $rep_tab_grid = ''
    ) {
        try {
            $oReportTable = new \BusinessModel\ReportTable();
            $response = $oReportTable->saveReportTable($prj_uid, $request_data);
            if (isset($response['pro_uid'])) {
                unset($response['pro_uid']);
            }
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $prj_uid {@min 1} {@max 32}
     * @param string $rep_uid {@min 1} {@max 32}
     * @param array $request_data
     *
     * @param string $rep_tab_dsc {@from body}
     * @return void
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url PUT /:prj_uid/report-table/:rep_uid
     */
    public function doPutReportTable(
        $prj_uid,
        $rep_uid,
        $request_data,
        $rep_tab_dsc = ''
    ) {
        try {
            $request_data['rep_uid'] = $rep_uid;
            $oReportTable = new \BusinessModel\ReportTable();
            $response = $oReportTable->updateReportTable($prj_uid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $prj_uid {@min 1} {@max 32}
     * @param string $rep_uid {@min 1} {@max 32}
     * @return void
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url DELETE /:prj_uid/report-table/:rep_uid
     */
    public function doDeleteReportTable($prj_uid, $rep_uid)
    {
        try {
            $oReportTable = new \BusinessModel\ReportTable();
            $response = $oReportTable->deleteReportTable($prj_uid, $rep_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}
