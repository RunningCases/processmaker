<?php
namespace Services\Api\ProcessMaker;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;


/**
 * Departament Api Controller
 *
 * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 *
 * @protected
 */
class Departament extends Api
{
    /**
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url GET
     */
    public function doGetDepartaments()
    {
        try {
            $oDepartament = new \BusinessModel\Departament();
            $response = $oDepartament->getDepartaments();
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $dep_uid {@min 1}{@max 32}
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url GET /:dep_uid
     */
    public function doGetDepartament($dep_uid)
    {
        try {
            $oDepartament = new \BusinessModel\Departament();
            $response = $oDepartament->getDepartament($dep_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param array $request_data
     * @param string $dep_title {@from body} {@min 1}
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url POST
     * @status 201
     */
    public function doPost($request_data, $dep_title)
    {
        try {
            $oDepartament = new \BusinessModel\Departament();
            $response = $oDepartament->saveDepartament($request_data);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $dep_uid {@min 1}{@max 32}
     *
     * @param array $request_data
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url PUT /:dep_uid
     */
    public function doPut($dep_uid, $request_data)
    {
        try {
            $request_data['dep_uid'] = $dep_uid;
            $oDepartament = new \BusinessModel\Departament();
            $response = $oDepartament->saveDepartament($request_data, false);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $dep_uid {@min 1}{@max 32}
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url DELETE /:dep_uid
     */
    public function doDelete($dep_uid)
    {
        try {
            $oDepartament = new \BusinessModel\Departament();
            $oDepartament->deleteDepartament($dep_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

