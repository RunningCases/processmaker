<?php
namespace BusinessModel;

class WebEntry
{
    private $arrayFieldDefinition = array(
        "TAS_UID"               => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(),             "fieldNameAux" => "taskUid"),
        "DYN_UID"               => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(),             "fieldNameAux" => "dynaFormUid"),
        "METHOD"                => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array("WS", "HTML"), "fieldNameAux" => "method"),
        "INPUT_DOCUMENT_ACCESS" => array("type" => "int",    "required" => true,  "empty" => false, "defaultValues" => array(0, 1),         "fieldNameAux" => "inputDocumentAccess")
    );

    private $arrayUserFieldDefinition = array(
        "USR_USERNAME" => array("type" => "string", "required" => true, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "userUsername"),
        "USR_PASSWORD" => array("type" => "string", "required" => true, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "userPassword")
    );

    private $formatFieldNameInUppercase = true;

    private $arrayFieldNameForException = array(
        "processUid"  => "PRO_UID"
    );

    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            foreach ($this->arrayFieldDefinition as $key => $value) {
                $this->arrayFieldNameForException[$value["fieldNameAux"]] = $key;
            }

            foreach ($this->arrayUserFieldDefinition as $key => $value) {
                $this->arrayFieldNameForException[$value["fieldNameAux"]] = $key;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set the format of the fields name (uppercase, lowercase)
     *
     * @param bool $flag Value that set the format
     *
     * return void
     */
    public function setFormatFieldNameInUppercase($flag)
    {
        try {
            $this->formatFieldNameInUppercase = $flag;

            $this->setArrayFieldNameForException($this->arrayFieldNameForException);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set exception messages for fields
     *
     * @param array $arrayData Data with the fields
     *
     * return void
     */
    public function setArrayFieldNameForException($arrayData)
    {
        try {
            foreach ($arrayData as $key => $value) {
                $this->arrayFieldNameForException[$key] = $this->getFieldNameByFormatFieldName($value);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the name of the field according to the format
     *
     * @param string $fieldName Field name
     *
     * return string Return the field name according the format
     */
    public function getFieldNameByFormatFieldName($fieldName)
    {
        try {
            return ($this->formatFieldNameInUppercase)? strtoupper($fieldName) : strtolower($fieldName);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Sanitizes a filename
     *
     * @param string $name Filename
     *
     * return string Return the filename sanitizes
     */
    public function sanitizeFilename($name)
    {
        $name = trim($name);

        $arraySpecialCharSearch = array(
            "ñ", "Ñ",
            "á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú",
            "à", "è", "ì", "ò", "ù", "À", "È", "Ì", "Ò", "Ù",
            "â", "ê", "î", "ô", "û", "Â", "Ê", "Î", "Ô", "Û",
            "ä", "ë", "ï", "ö", "ü", "Ä", "Ë", "Ï", "Ö", "Ü",
            "/", "\\",
            " "
        );
        $arraySpecialCharReplace = array(
            "n", "N",
            "a", "e", "i", "o", "u", "A", "E", "I", "O", "U",
            "a", "e", "i", "o", "u", "A", "E", "I", "O", "U",
            "a", "e", "i", "o", "u", "A", "E", "I", "O", "U",
            "a", "e", "i", "o", "u", "A", "E", "I", "O", "U",
            "_", "_",
            "_"
        );

        $newName = str_replace($arraySpecialCharSearch, $arraySpecialCharReplace, $name);

        $arraySpecialCharSearch  = array("/[^a-zA-Z0-9\_\-\.]/");
        $arraySpecialCharReplace = array("");

        $newName = preg_replace($arraySpecialCharSearch, $arraySpecialCharReplace, $newName);

        return $newName;
    }

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

            //Verify data
            $process = new \BusinessModel\Process();

            $process->throwExceptionIfNotExistsProcess($processUid, $this->arrayFieldNameForException["processUid"]);

            if ($taskUid != "") {
                $process->throwExceptionIfNotExistsTask($processUid, $taskUid, $this->arrayFieldNameForException["taskUid"]);
            }

            if ($dynaFormUid != "") {
                $dynaForm = new \BusinessModel\DynaForm();

                $dynaForm->throwExceptionIfNotExistsDynaForm($dynaFormUid, $processUid, $this->arrayFieldNameForException["dynaFormUid"]);
            }

            //Get data
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

            //Return
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
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            //Verify data
            $process = new \BusinessModel\Process();

            $process->throwExceptionIfNotExistsProcess($processUid, $this->arrayFieldNameForException["processUid"]);

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition, $this->arrayFieldNameForException, true);

            $projectUser = new \BusinessModel\ProjectUser();

            if ($arrayData["METHOD"] == "WS") {
                $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayUserFieldDefinition, $this->arrayFieldNameForException, true);

                $loginData = $projectUser->userLogin($arrayData["USR_USERNAME"], $arrayData["USR_PASSWORD"]);

                if ($loginData->status_code != 0) {
                    throw (new \Exception($loginData->message));
                }
            }

            $process->throwExceptionIfNotExistsTask($processUid, $arrayData["TAS_UID"], $this->arrayFieldNameForException["taskUid"]);

            $dynaForm = new \BusinessModel\DynaForm();

            $dynaForm->throwExceptionIfNotExistsDynaForm($arrayData["DYN_UID"], $processUid, $this->arrayFieldNameForException["dynaFormUid"]);

            $task = new \Task();

            $arrayTaskData = $task->load($arrayData["TAS_UID"]);

            $weEventUid = $task->getStartingEvent($arrayData["TAS_UID"]);

            if ($arrayTaskData["TAS_START"] == "FALSE") {
                throw (new \Exception(str_replace(array("{0}"), array($arrayTaskData["TAS_TITLE"]), "The task \"{0}\" is not initial task")));
            }

            if ($arrayTaskData["TAS_ASSIGN_TYPE"] != "BALANCED") {
                throw (new \Exception(str_replace(array("{0}"), array($arrayTaskData["TAS_TITLE"]), "Web Entry only works with tasks which have \"Cyclical Assignment\", the task \"{0}\" does not have a valid assignment type. Please change the Assignment Rules")));
            }

            if ($arrayData["METHOD"] == "WS") {
                $task = new \Tasks();

                if ($task->assignUsertoTask($arrayData["TAS_UID"]) == 0) {
                    throw (new \Exception(str_replace(array("{0}"), array($arrayTaskData["TAS_TITLE"]), "The task \"{0}\" does not have users")));
                }
            }

            $dynaForm = new \Dynaform();

            $arrayDynaFormData = $dynaForm->Load($arrayData["DYN_UID"]);

            $step = new \BusinessModel\Step();

            if (!$step->existsRecord($arrayData["TAS_UID"], "DYNAFORM", $arrayData["DYN_UID"])) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($arrayDynaFormData["DYN_TITLE"], $arrayTaskData["TAS_TITLE"]), "The DynaForm \"{0}\" isn't assigned to the task \"{1}\"")));
            }

            if ($arrayData["METHOD"] == "WS") {
                //Verify if the Web Entry exist
                $arrayWebEntryData = $this->getData($processUid, "UID", $arrayData["TAS_UID"], $arrayData["DYN_UID"]);

                if (count($arrayWebEntryData) > 0) {
                    throw (new \Exception("The Web Entry exist"));
                }

                //Verify if User is assigned to Task
                $criteria = new \Criteria("workflow");

                $criteria->addSelectColumn(\UsersPeer::USR_UID);
                $criteria->add(\UsersPeer::USR_USERNAME, $arrayData["USR_USERNAME"], \Criteria::EQUAL);

                $rsCriteria = \UsersPeer::doSelectRS($criteria);
                $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                $rsCriteria->next();

                $row = $rsCriteria->getRow();

                if (!$projectUser->userIsAssignedToTask($row["USR_UID"], $arrayData["TAS_UID"])) {
                    throw (new \Exception(str_replace(array("{0}", "{1}"), array($arrayData["USR_USERNAME"], $arrayTaskData["TAS_TITLE"]), "The user \"{0}\" does not have the task \"{1}\" assigned")));
                }
            }

            //Create
            $taskUid     = $arrayData["TAS_UID"];
            $dynaFormUid = $arrayData["DYN_UID"];
            $method      = $arrayData["METHOD"];
            $inputDocumentAccess = $arrayData["INPUT_DOCUMENT_ACCESS"];
            $wsRoundRobin = 0; //0, 1 //0 - Cyclical Assignment

            $pathProcess = PATH_DATA_SITE . "public" . PATH_SEP . $processUid;

            \G::mk_dir($pathProcess, 0777);

            $http = (\G::is_https())? "https://" : "http://";

            $arrayDataAux = array();

            switch ($method) {
                case "WS":
                    $usrUsername = $arrayData["USR_USERNAME"];
                    $usrPassword = $arrayData["USR_PASSWORD"];

                    //Creating sys.info;
                    $site_public_path = "";

                    if (file_exists($site_public_path . "")) {
                    }

                    //Creating the first file
                    $dynTitle = $this->sanitizeFilename($arrayDynaFormData["DYN_TITLE"]);
                    $fileName = $dynTitle;

                    $fileContent = "<?php\n";
                    $fileContent .= "global \$_DBArray;\n";
                    $fileContent .= "if (!isset(\$_DBArray)) {\n";
                    $fileContent .= "  \$_DBArray = array();\n";
                    $fileContent .= "}\n";
                    $fileContent .= "\$_SESSION['PROCESS'] = '" . $processUid . "';\n";
                    $fileContent .= "\$_SESSION['CURRENT_DYN_UID'] = '" . $dynaFormUid . "';\n";
                    $fileContent .= "\$G_PUBLISH = new Publisher;\n";
                    $fileContent .= "\$G_PUBLISH->AddContent('dynaform', 'xmlform', '" . $processUid . '/' . $dynaFormUid . "', '', array(), '" . $fileName . 'Post.php' . "');\n";
                    $fileContent .= "G::RenderPage('publish', 'blank');";

                    file_put_contents($pathProcess . PATH_SEP . $fileName . ".php", $fileContent);

                    //Creating the second file, the  post file who receive the post form.
                    $pluginTpl = PATH_CORE . "templates" . PATH_SEP . "processes" . PATH_SEP . "webentryPost.tpl";

                    $template = new \TemplatePower($pluginTpl);
                    $template->prepare();

                    $template->assign("wsdlUrl", $http . $_SERVER["HTTP_HOST"] . "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/services/wsdl2");
                    $template->assign("wsUploadUrl", $http . $_SERVER["HTTP_HOST"] . "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/services/upload");
                    $template->assign("processUid", $processUid);
                    $template->assign("dynaformUid", $dynaFormUid);
                    $template->assign("taskUid", $taskUid);
                    $template->assign("wsUser", $usrUsername);
                    $template->assign("wsPass", "md5:" . md5($usrPassword));
                    $template->assign("wsRoundRobin", $wsRoundRobin);

                    if ($inputDocumentAccess == 0) {
                        //Restricted to process permissions
                        $template->assign("USR_VAR", "\$cInfo = ws_getCaseInfo(\$caseId);\n\t  \$USR_UID = \$cInfo->currentUsers->userId;");
                    } else {
                        //No Restriction
                        $template->assign("USR_VAR", "\$USR_UID = -1;");
                    }

                    $template->assign("dynaform", $dynTitle);
                    $template->assign("timestamp", date("l jS \of F Y h:i:s A"));
                    $template->assign("ws", SYS_SYS);
                    $template->assign("version", \System::getVersion());

                    $fileName = $pathProcess . PATH_SEP . $dynTitle . "Post.php";

                    file_put_contents($fileName, $template->getOutputContent());

                    //Creating the third file, only if this wsClient.php file doesn't exist.
                    $fileName = $pathProcess . PATH_SEP . "wsClient.php";
                    $pluginTpl = PATH_CORE . "test" . PATH_SEP . "unit" . PATH_SEP . "ws" . PATH_SEP . "wsClient.php";

                    if (file_exists($fileName)) {
                        if (filesize($fileName) != filesize($pluginTpl)) {
                            @copy($fileName, $pathProcess . PATH_SEP . "wsClient.php.bck");
                            @unlink($fileName);

                            $template = new \TemplatePower($pluginTpl);
                            $template->prepare();

                            file_put_contents($fileName, $template->getOutputContent());
                        }
                    } else {
                        $template = new \TemplatePower($pluginTpl);
                        $template->prepare();

                        file_put_contents($fileName, $template->getOutputContent());
                    }

                    //Event
                    if ($weEventUid != "") {
                        $event = new \Event();

                        $arrayEventData = array();

                        $arrayEventData["EVN_UID"] = $weEventUid;
                        $arrayEventData["EVN_RELATED_TO"] = "MULTIPLE";
                        $arrayEventData["EVN_ACTION"] = $dynaFormUid;
                        $arrayEventData["EVN_CONDITIONS"] = $usrUsername;

                        $result = $event->update($arrayEventData);
                    }

                    //Data
                    $url = $http . $_SERVER["HTTP_HOST"] . "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/" . $processUid . "/" . $dynTitle . ".php";

                    $arrayDataAux = array("URL" => $url);
                    break;
                case "HTML":
                    global $G_FORM;

                    if (! class_exists("Smarty")) {
                        $loader = \Maveriks\Util\ClassLoader::getInstance();
                        $loader->addClass("Smarty", PATH_THIRDPARTY . "smarty".PATH_SEP."libs".PATH_SEP."Smarty.class.php");
                    }

                    $G_FORM = new \Form($processUid . "/" . $dynaFormUid, PATH_DYNAFORM, SYS_LANG, false);
                    $G_FORM->action = $http . $_SERVER["HTTP_HOST"] . "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/services/cases_StartExternal.php";

                    $scriptCode = "";
                    $scriptCode = $G_FORM->render(PATH_CORE . "templates/" . "xmlform" . ".html", $scriptCode);
                    $scriptCode = str_replace("/controls/", $http . $_SERVER["HTTP_HOST"] . "/controls/", $scriptCode);
                    $scriptCode = str_replace("/js/maborak/core/images/", $http . $_SERVER["HTTP_HOST"] . "/js/maborak/core/images/", $scriptCode);

                    //Render the template
                    $pluginTpl = PATH_CORE . "templates" . PATH_SEP . "processes" . PATH_SEP . "webentry.tpl";

                    $template = new \TemplatePower($pluginTpl);
                    $template->prepare();

                    $step = new \Step();
                    $sUidGrids = $step->lookingforUidGrids($processUid, $dynaFormUid);

                    $template->assign("URL_MABORAK_JS", \G::browserCacheFilesUrl("/js/maborak/core/maborak.js"));
                    $template->assign("URL_TRANSLATION_ENV_JS", \G::browserCacheFilesUrl("/jscore/labels/" . SYS_LANG . ".js"));
                    $template->assign("siteUrl", $http . $_SERVER["HTTP_HOST"]);
                    $template->assign("sysSys", SYS_SYS);
                    $template->assign("sysLang", SYS_LANG);
                    $template->assign("sysSkin", SYS_SKIN);
                    $template->assign("processUid", $processUid);
                    $template->assign("dynaformUid", $dynaFormUid);
                    $template->assign("taskUid", $taskUid);
                    $template->assign("dynFileName", $processUid . "/" . $dynaFormUid);
                    $template->assign("formId", $G_FORM->id);
                    $template->assign("scriptCode", $scriptCode);

                    if (sizeof($sUidGrids) > 0) {
                        foreach ($sUidGrids as $k => $v) {
                            $template->newBlock("grid_uids");
                            $template->assign("siteUrl", $http . $_SERVER["HTTP_HOST"]);
                            $template->assign("gridFileName", $processUid . "/" . $v);
                        }
                    }

                    //Data
                    $html = str_replace("</body>", "</form></body>", str_replace("</form>", "", $template->getOutputContent()));

                    $arrayDataAux = array("HTML" => $html);
                    break;
            }

            //Return
            $arrayData = array_merge($arrayData, $arrayDataAux);

            if (!$this->formatFieldNameInUppercase) {
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
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
            //Get data
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
                $this->getFieldNameByFormatFieldName("TAS_UID")   => $record["taskUid"],
                $this->getFieldNameByFormatFieldName("TAS_TITLE") => $record["taskTitle"],
                $this->getFieldNameByFormatFieldName("DYN_UID")   => $record["dynaFormUid"],
                $this->getFieldNameByFormatFieldName("DYN_TITLE") => $record["dynaFormTitle"],
                $this->getFieldNameByFormatFieldName("URL")       => $record["url"]
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
            //Get data
            $arrayWebEntryData = $this->getData($processUid, "UID", $taskUid, $dynaFormUid);

            if (count($arrayWebEntryData) == 0) {
                throw (new \Exception("The Web Entry doesn't exist"));
            }

            //Return
            return $this->getWebEntryDataFromRecord($arrayWebEntryData[$taskUid . "/" . $dynaFormUid]);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

