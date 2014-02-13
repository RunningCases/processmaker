<?php
namespace Services\Api\ProcessMaker;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Pmtable Api Controller
 *
 * @protected
 */
class Pmtable extends Api
{
    /**
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET
     */
    public function doGetPmTables()
    {
        try {
            $oPmTable = new \BusinessModel\Table();
            $response = $oPmTable->getTables();
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $pmt_uid {@min 1} {@max 32}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /:pmt_uid
     */
    public function doGetPmTable($pmt_uid)
    {
        try {
            $oPmTable = new \BusinessModel\Table();
            $response = $oPmTable->getTable($pmt_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $pmt_uid {@min 1} {@max 32}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /:pmt_uid/data
     */
    public function doGetPmTableData($pmt_uid)
    {
        try {
            $oPmTable = new \BusinessModel\Table();
            $response = $oPmTable->getTableData($pmt_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param array $request_data
     * @param string $pmt_tab_name {@from body}
     * @param string $pmt_tab_dsc {@from body}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url POST
     * @status 201
     */
    public function doPostPmTable(
        $request_data,
        $pmt_tab_name,
        $pmt_tab_dsc = ''
    ) {
        try {
            $oReportTable = new \BusinessModel\Table();
            $response = $oReportTable->saveTable($request_data);
            if (isset($response['pro_uid'])) {
                unset($response['pro_uid']);
            }
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $pmt_uid {@min 1} {@max 32}
     *
     * @param array $request_data
     * @param string $pmt_tab_dsc {@from body}
     * @return void
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url PUT /:pmt_uid
     */
    public function doPutPmTable(
        $pmt_uid,
        $request_data,
        $pmt_tab_dsc = ''
    ) {
        try {
            $request_data['pmt_uid'] = $pmt_uid;
            $oReportTable = new \BusinessModel\Table();
            $response = $oReportTable->updateTable($request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $pmt_uid {@min 1} {@max 32}
     *
     * @return void
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url DELETE /:pmt_uid
     */
    public function doDeletePmTable($pmt_uid)
    {
        try {
            $oReportTable = new \BusinessModel\Table();
            $response = $oReportTable->deleteTable($pmt_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

