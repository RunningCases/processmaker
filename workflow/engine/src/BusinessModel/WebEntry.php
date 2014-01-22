<?php
namespace BusinessModel;

class WebEntry
{
    /**
     * Get all Web Entries data of a Process
     *
     * @param string $processUid  Unique id of Process
     * @param string $option      Option (ALL, UID, DYN_UID)
     * @param string $taskUid     Unique id of Task
     * @param string $dynaFormUid Unique id of DynaForm
     *
     * return array Return an array with all Web Entries data of a Process
     */
    public function getData($processUid, $option = "ALL", $taskUid = "", $dynaFormUid = "")
    {
        try {
            $arrayData = array();

            $webEntryPath = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "public" . PATH_SEP . $processUid;

            if (is_dir($webEntryPath)) {
                $task = new \Task();
                $dynaForm = new \Dynaform();

                $step = new \BusinessModel\Step();

                $arrayDirFile = scandir($webEntryPath); //Ascending Order

                $nrt     = array("\n",    "\r",    "\t");
                $nrthtml = array("(n /)", "(r /)", "(t /)");

                $http = (\G::is_https())? "https://" : "http://";
                $url = $http . $_SERVER["HTTP_HOST"] . "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/" . $processUid;

                $flagNext = 1;

                for ($i = 0; $i <= count($arrayDirFile) - 1 && $flagNext == 1; $i++) {
                    $file = $arrayDirFile[$i];

                    if ($file != "" && $file != "." && $file != ".." && is_file($webEntryPath . PATH_SEP . $file)) {
                        $one = 0;
                        $two = 0;

                        $one = count(explode("wsClient.php", $file));
                        $two = count(explode("Post.php", $file));

                        if ($one == 1 && $two == 1) {
                            $arrayInfo = pathinfo($file);

                            $weTaskUid = "";
                            $weDynaFormUid = "";
                            $weFileName = $arrayInfo["filename"];

                            $strContent = str_replace($nrt, $nrthtml, file_get_contents($webEntryPath . PATH_SEP . $weFileName . ".php"));

                            if (preg_match("/^.*CURRENT_DYN_UID.*=.*[\"\'](\w{32})[\"\'].*$/", $strContent, $arrayMatch)) {
                                $weDynaFormUid = $arrayMatch[1];
                            }

                            if (file_exists($webEntryPath . PATH_SEP . $weFileName . "Post.php")) {
                                $strContent = str_replace($nrt, $nrthtml, file_get_contents($webEntryPath . PATH_SEP . $weFileName . "Post.php"));

                                if (preg_match("/^.*ws_newCase\s*\(\s*[\"\']" . $processUid . "[\"\']\s*\,\s*[\"\'](\w{32})[\"\'].*\)\s*\;.*$/", $strContent, $arrayMatch)) {
                                    $weTaskUid = $arrayMatch[1];
                                }
                            }

                            if ($weTaskUid != "" && $weDynaFormUid != "") {
                                $flagPush = 0;

                                switch ($option) {
                                    case "ALL":
                                        if ($step->existsRecord($weTaskUid, "DYNAFORM", $weDynaFormUid)) {
                                            $flagPush = 1;
                                        }
                                        break;
                                    case "UID":
                                        if ($taskUid != "" && $dynaFormUid != "" && $weTaskUid == $taskUid && $weDynaFormUid == $dynaFormUid && $step->existsRecord($weTaskUid, "DYNAFORM", $weDynaFormUid)) {
                                            $flagPush = 1;
                                            $flagNext = 0;
                                        }
                                        break;
                                    case "DYN_UID":
                                        if ($dynaFormUid != "" && $weDynaFormUid == $dynaFormUid && $step->existsRecord($weTaskUid, "DYNAFORM", $weDynaFormUid)) {
                                            $flagPush = 1;
                                            $flagNext = 0;
                                        }
                                        break;
                                }

                                if ($flagPush == 1) {
                                    $arrayTaskData = $task->load($weTaskUid);
                                    $arrayDynaFormData = $dynaForm->Load($weDynaFormUid);

                                    $arrayData[$weTaskUid . "/" . $weDynaFormUid] = array(
                                        "processUid"    => $processUid,
                                        "taskUid"       => $weTaskUid,
                                        "taskTitle"     => $arrayTaskData["TAS_TITLE"],
                                        "dynaFormUid"   => $weDynaFormUid,
                                        "dynaFormTitle" => $arrayDynaFormData["DYN_TITLE"],
                                        "fileName"      => $weFileName,
                                        "url"           => $url . "/" . $weFileName . ".php"
                                    );
                                }
                            }
                        }
                    }
                }
            }

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create Web Entry for a Process
     *
     * @param string $processUid Unique id of Process
     * @param array  $arrayData  Data
     *
     * return array Return data of the new Web Entry created
     */
    public function create($processUid, $arrayData)
    {
        try {
            //
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete Web Entry
     *
     * @param string $processUid  Unique id of Process
     * @param string $taskUid     Unique id of Task
     * @param string $dynaFormUid Unique id of DynaForm
     *
     * return void
     */
    public function delete($processUid, $taskUid, $dynaFormUid)
    {
        try {
            //Verify data
            $process = new \Process();

            if (!$process->exists($processUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($processUid, "PROCESS"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            $arrayWebEntryData = $this->getData($processUid, "UID", $taskUid, $dynaFormUid);

            if (count($arrayWebEntryData) == 0) {
                throw (new \Exception("The Web Entry doesn't exist"));
            }

            //Delete
            $webEntryPath = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "public" . PATH_SEP . $processUid;

            unlink($webEntryPath . PATH_SEP . $arrayWebEntryData[$taskUid . "/" . $dynaFormUid]["fileName"] . ".php");
            unlink($webEntryPath . PATH_SEP . $arrayWebEntryData[$taskUid . "/" . $dynaFormUid]["fileName"] . "Post.php");
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Web Entry from a record
     *
     * @param array $record Record
     *
     * return array Return an array with data of a Web Entry
     */
    public function getWebEntryDataFromRecord($record)
    {
        try {
            return array(
                "tas_uid"   => $record["taskUid"],
                "tas_title" => $record["taskTitle"],
                "dyn_uid"   => $record["dynaFormUid"],
                "dyn_title" => $record["dynaFormTitle"],
                "url"       => $record["url"]
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Web Entry
     *
     * @param string $processUid  Unique id of Process
     * @param string $taskUid     Unique id of Task
     * @param string $dynaFormUid Unique id of DynaForm
     *
     * return array Return an array with data of a Web Entry
     */
    public function getWebEntry($processUid, $taskUid, $dynaFormUid)
    {
        try {
            //Verify data
            $process = new \Process();

            if (!$process->exists($processUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($processUid, "PROCESS"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            $arrayWebEntryData = $this->getData($processUid, "UID", $taskUid, $dynaFormUid);

            if (count($arrayWebEntryData) == 0) {
                throw (new \Exception("The Web Entry doesn't exist"));
            }

            //Get data
            //Return
            return $this->getWebEntryDataFromRecord($arrayWebEntryData[$taskUid . "/" . $dynaFormUid]);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

