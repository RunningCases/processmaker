<?php
namespace ProcessMaker\Services\Api;

use Luracast\Restler\RestException;
use ProcessMaker\Services\Api;
use \ProcessMaker\Project\Adapter;
use \ProcessMaker\Util;

/**
 * Class Project
 *
 * @package Services\Api\ProcessMaker
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 *
 * @protected
 */
class Project extends Api
{
    /**
     * @url GET
     */
    public function doGetProjects()
    {
        try {
            $start = null;
            $limit = null;
            $filter = "";

            $projects = Adapter\BpmnWorkflow::getList($start, $limit, $filter, CASE_LOWER);

            return $projects;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetProject($prj_uid)
    {
        try {
            return Adapter\BpmnWorkflow::getStruct($prj_uid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @status 201
     */
    public function post($request_data)
    {
        try {
            //TODO
        } catch (\Exception $e) {
            // TODO in case that $process->createProcess($userUid, $data); fails maybe the BPMN project was created successfully
            //      so, we need remove it or change the creation order.

            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    public function put($prjUid, $request_data)
    {
        try {
            return Adapter\BpmnWorkflow::updateFromStruct($prjUid, $request_data);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    public function delete($prjUid)
    {
        try {
           // TODO
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid/export
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function export($prj_uid)
    {
        $exporter = new \ProcessMaker\Exporter\XmlExporter($prj_uid);

        $outputDir = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "files" . PATH_SEP . "output" . PATH_SEP;
        $version = \ProcessMaker\Util\Common::getLastVersion($outputDir . $exporter->getProjectName() . "-*.pmx") + 1;
        $outputFilename = $outputDir . sprintf("%s-%s.%s", $exporter->getProjectName(), $version, "pmx");

        $exporter->saveExport($outputFilename);

        $httpStream = new \ProcessMaker\Util\IO\HttpStream();
        $fileExtension = pathinfo($outputFilename, PATHINFO_EXTENSION);

        $httpStream->loadFromFile($outputFilename);
        $httpStream->setHeader("Content-Type", "application/$fileExtension");
        $httpStream->send();
    }

    /**
     * @url GET /:prj_uid/process
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetProcess($prj_uid)
    {
        try {
            $process = new \ProcessMaker\BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = $process->getProcess($prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:prj_uid/process
     *
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param array  $request_data
     */
    public function doPutProcess($prj_uid, $request_data)
    {
        try {
            $process = new \ProcessMaker\BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $arrayData = $process->update($prj_uid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/dynaforms
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetDynaForms($prj_uid)
    {
        try {
            $process = new \ProcessMaker\BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = $process->getDynaForms($prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/input-documents
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetInputDocuments($prj_uid)
    {
        try {
            $process = new \ProcessMaker\BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = $process->getInputDocuments($prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/variables
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetVariables($prj_uid)
    {
        try {
            $process = new \ProcessMaker\BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = $process->getVariables("ALL", $prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/grid/variables
     * @url GET /:prj_uid/grid/:grid_uid/variables
     *
     * @param string $prj_uid  {@min 32}{@max 32}
     * @param string $grid_uid
     */
    public function doGetGridVariables($prj_uid, $grid_uid = "")
    {
        try {
            $process = new \ProcessMaker\BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = ($grid_uid == "")? $process->getVariables("GRID", $prj_uid) : $process->getVariables("GRIDVARS", $prj_uid, $grid_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/trigger-wizards
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetTriggerWizards($prj_uid)
    {
        try {
            $process = new \ProcessMaker\BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid", "libraryName" => "lib_name", "methodName" => "fn_name"));

            $response = $process->getLibraries($prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

