<?php
namespace BusinessModel;

class TriggerWizard
{
    private $arrayFieldDefinition = array(
        "TRI_UID"         => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(),         "fieldNameAux" => "triggerUid"),

        "TRI_TITLE"       => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(),         "fieldNameAux" => "triggerTitle"),
        "TRI_DESCRIPTION" => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),         "fieldNameAux" => "triggerDescription"),
        "TRI_TYPE"        => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array("SCRIPT"), "fieldNameAux" => "triggerType"),
        "TRI_WEBBOT"      => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),         "fieldNameAux" => "triggerWebbot"),
        "TRI_PARAM"       => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),         "fieldNameAux" => "triggerParam")
    );

    private $formatFieldNameInUppercase = true;

    private $arrayFieldNameForException = array(
        "processUid"  => "PRO_UID",
        "libraryName" => "LIB_NAME",
        "methodName"  => "MTH_NAME"
    );

    private $library;

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

            //Library
            \G::LoadClass("triggerLibrary");

            $this->library = \triggerLibrary::getSingleton();
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
     * Verify if doesn't exists the name of the library
     *
     * @param string $libraryName                  Library name
     * @param string $libraryFieldNameForException Field name for the exception
     *
     * return void Throw exception if doesn't exists the name of the library
     */
    public function throwExceptionIfNotExistsLibrary($libraryName, $libraryFieldNameForException)
    {
        try {
            $arrayLibrary = $this->library->getRegisteredClasses();

            if (!isset($arrayLibrary[$this->libraryGetLibraryName($libraryName)])) {
                $msg = str_replace(array("{0}", "{1}"), array($libraryFieldNameForException, $libraryName), "The library with {0}: \"{1}\", does not exist");

                throw (new \Exception($msg));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if doesn't exists the method in the library
     *
     * @param string $libraryName                  Library name
     * @param string $methodName                   Method name
     * @param string $libraryFieldNameForException Field name for the exception
     * @param string $methodFieldNameForException  Field name for the exception
     *
     * return void Throw exception if doesn't exists the method in the library
     */
    public function throwExceptionIfNotExistsMethodInLibrary($libraryName, $methodName, $libraryFieldNameForException, $methodFieldNameForException)
    {
        try {
            $this->throwExceptionIfNotExistsLibrary($libraryName, $libraryFieldNameForException);

            $library = $this->library->getLibraryDefinition($this->libraryGetLibraryName($libraryName));

            if (!isset($library->methods[$methodName])) {
                $msg = str_replace(array("{0}", "{1}"), array($methodFieldNameForException, $methodName), "The method with {0}: \"{1}\", does not exist in library");

                throw (new \Exception($msg));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     *
     */
    public function throwExceptionIfLibraryAndMethodIsInvalidForTrigger($libraryName, $methodName, $triggerUid, $libraryFieldNameForException, $methodFieldNameForException, $triggerUidFieldNameForException)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsMethodInLibrary($libraryName, $methodName, $libraryFieldNameForException, $methodFieldNameForException);

            $trigger = new \BusinessModel\Trigger();

            $trigger->throwExceptionIfNotExistsTrigger($triggerUid, "", $triggerUidFieldNameForException);

            //Get data
            $trigger = new \Triggers();

            $arrayTriggerData = $trigger->load($triggerUid);

            $triggerParam = unserialize($arrayTriggerData["TRI_PARAM"]);

            if ($arrayTriggerData["TRI_PARAM"] == "" || !isset($triggerParam["hash"])) {
                $msg = str_replace(array("{0}", "{1}"), array($triggerUidFieldNameForException, $triggerUid), "The trigger with {0}: {1}, does not been created with the wizard");

                throw (new \Exception($msg));
            }

            $arrayTriggerData["TRI_PARAM"] = $triggerParam;

            if (md5($arrayTriggerData["TRI_WEBBOT"]) != $arrayTriggerData["TRI_PARAM"]["hash"]) {
                $msg = str_replace(array("{0}", "{1}"), array($triggerUidFieldNameForException, $triggerUid), "The trigger with {0}: {1}, has been modified manually, is invalid for the wizard");

                throw (new \Exception($msg));
            }

            //PMFUNTION_NAME //createZimbraContacts
            //LIBRARY_CLASS  //class.pmZimbra.pmFunctions.php

            //VALIDATION - El wizard xxx con el metodo yyy, es invalido para el trigger ttttt

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the filename of the library
     *
     * @param string $libraryName Library name
     *
     * return string Return the filename of the library
     */
    public function libraryGetLibraryName($libraryName)
    {
        try {
            if (!preg_match("/\.pmFunctions\.php$/", $libraryName)) {
                $libraryName = ($libraryName != "pmFunctions")? $libraryName . ".pmFunctions" : $libraryName;
                $libraryName = "class." . $libraryName . ".php";
            }

            return $libraryName;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all parameters of a method
     *
     * @param string $libraryName Library name
     * @param string $methodName  Method name
     *
     * return array Return an array with all parameters of a method
     */
    public function methodGetParams($libraryName, $methodName)
    {
        try {
            $arrayParam = array();

            //Verify data
            $this->throwExceptionIfNotExistsMethodInLibrary($libraryName, $methodName, $this->arrayFieldNameForException["libraryName"], $this->arrayFieldNameForException["methodName"]);

            //Get data
            $library = $this->library->getLibraryDefinition($this->libraryGetLibraryName($libraryName));
            $method = $library->methods[$methodName];

            $arrayParameter = array_keys($method->params);

            foreach ($arrayParameter as $key => $value) {
                $strParam = $value;

                if ($strParam != "") {
                    $arrayp = explode("|", $strParam);

                    //Get param
                    $arrayTypeAndMaxLength = array();

                    if (preg_match("/^\s*(.+)\s*[\{\[\(]\s*(\d+)\s*[\)\]\}].*$/", $arrayp[0], $arrayMatch)) {
                        $arrayTypeAndMaxLength = array("type" => $arrayMatch[1], "maxLength" => (int)($arrayMatch[2]));
                    } else {
                        $arrayTypeAndMaxLength = array("type" => trim($arrayp[0]));
                    }

                    $arrayNameAndDefaultValue = array();

                    $arrayNameAndDefaultValue["name"] = "";

                    if (preg_match("/^\s*\\\$(\w+)(.*)$/", $arrayp[1], $arrayMatch)) {
                        $arrayNameAndDefaultValue["name"] = $arrayMatch[1];

                        $arrayp[1] = $arrayMatch[2];
                    }

                    if (preg_match("/^\s*=\s*(.*)$/", $arrayp[1], $arrayMatch)) {
                        $arrayNameAndDefaultValue["defaultValue"] = trim(trim($arrayMatch[1]), "\"'");
                    }

                    //Set param
                    $arrayData = array(
                        "name"        => $arrayNameAndDefaultValue["name"],
                        "type"        => $arrayTypeAndMaxLength["type"],
                        "label"       => (isset($arrayp[2]))? trim($arrayp[2]) : $arrayNameAndDefaultValue["name"],
                        "description" => (isset($arrayp[3]))? trim($arrayp[3]) : "",
                        "required"    => !isset($arrayNameAndDefaultValue["defaultValue"])
                    );

                    if (isset($arrayNameAndDefaultValue["defaultValue"])) {
                        $arrayData["default_value"] = $arrayNameAndDefaultValue["defaultValue"];
                    }

                    if (isset($arrayTypeAndMaxLength["maxLength"])) {
                        $arrayData["max_length"] = $arrayTypeAndMaxLength["maxLength"];
                    }

                    $arrayParam[] = $arrayData;
                }
            }

            //Return
            return $arrayParam;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all parameters return of a method
     *
     * @param string $libraryName Library name
     * @param string $methodName  Method name
     *
     * return array Return an array with all parameters return of a method
     */
    public function methodGetParamsReturn($libraryName, $methodName)
    {
        try {
            $arrayParam = array();

            //Verify data
            $this->throwExceptionIfNotExistsMethodInLibrary($libraryName, $methodName, $this->arrayFieldNameForException["libraryName"], $this->arrayFieldNameForException["methodName"]);

            //Get data
            $library = $this->library->getLibraryDefinition($this->libraryGetLibraryName($libraryName));
            $method = $library->methods[$methodName];

            if (isset($method->info["return"]) && $method->info["return"] != "") {
                $strParam = $method->info["return"];

                $arrayp = explode("|", $strParam);

                if (isset($arrayp[0]) && isset($arrayp[1]) && trim(strtoupper($arrayp[0])) != strtoupper(\G::LoadTranslation("ID_NONE"))) {
                    $description = "";

                    if (isset($arrayp[3])) {
                        $description = (trim(strtoupper($arrayp[3])) == strtoupper(\G::LoadTranslation("ID_NONE")))? \G::LoadTranslation("ID_NOT_REQUIRED") : trim($arrayp[3]);
                    } else {
                        $description = $strParam;
                    }

                    //Set param
                    $arrayParam[] = array(
                        "name"        => "tri_answer",
                        "type"        => trim($arrayp[0]),
                        "label"       => \G::LoadTranslation("ID_TRIGGER_RETURN_LABEL"),
                        "description" => $description,
                        "required"    => isset($arrayp[1]) //(trim($arrayp[1]) != "")? true : false
                    );
                }
            }

            //Return
            return $arrayParam;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    ///**
    // *
    // */
    //public function create()
    //{
    //    try {
    //        //
    //    } catch (\Exception $e) {
    //        throw $e;
    //    }
    //}

    ///**
    // *
    // */
    //public function update()
    //{
    //    try {
    //        //
    //    } catch (\Exception $e) {
    //        throw $e;
    //    }
    //}

    /**
     * Get Method of the Library
     *
     * @param string $libraryName Library name
     * @param string $methodName  Method name
     *
     * return array Return an array with the Method of the Library
     */
    public function getMethod($libraryName, $methodName)
    {
        try {
            $arrayMethod = array();

            //Verify data
            $arrayMethodParam = $this->methodGetParams($libraryName, $methodName);
            $arrayMethodParamReturn = $this->methodGetParamsReturn($libraryName, $methodName);

            //Get data
            $library = $this->library->getLibraryDefinition($this->libraryGetLibraryName($libraryName));
            $method = $library->methods[$methodName];

            $arrayMethod[$this->getFieldNameByFormatFieldName("FN_NAME")] = $method->info["name"];
            $arrayMethod[$this->getFieldNameByFormatFieldName("FN_DESCRIPTION")] = trim(str_replace("*", "", implode("", $method->info["description"])));
            $arrayMethod[$this->getFieldNameByFormatFieldName("FN_LABEL")] = $method->info["label"];
            $arrayMethod[$this->getFieldNameByFormatFieldName("FN_LINK")] = (isset($method->info["link"]) && ($method->info["link"] != ""))? $method->info["link"] : "";

            if ($this->formatFieldNameInUppercase) {
                $arrayMethodParam = \G::array_change_key_case2($arrayMethodParam, CASE_UPPER);
                $arrayMethodParamReturn = \G::array_change_key_case2($arrayMethodParamReturn, CASE_UPPER);
            }

            $arrayMethod[$this->getFieldNameByFormatFieldName("FN_PARAMS")][$this->getFieldNameByFormatFieldName("INPUT")] = $arrayMethodParam;
            $arrayMethod[$this->getFieldNameByFormatFieldName("FN_PARAMS")][$this->getFieldNameByFormatFieldName("OUTPUT")] = $arrayMethodParamReturn;

            //Return
            return $arrayMethod;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get Library
     *
     * @param string $libraryName Library name
     *
     * return array Return an array with the Library
     */
    public function getLibrary($libraryName)
    {
        try {
            $arrayLibrary = array();

            //Verify data
            $this->throwExceptionIfNotExistsLibrary($libraryName, $this->arrayFieldNameForException["libraryName"]);

            //Get data
            $library = $this->library->getLibraryDefinition($this->libraryGetLibraryName($libraryName));

            $arrayLibrary[$this->getFieldNameByFormatFieldName("LIB_NAME")] = $libraryName;
            $arrayLibrary[$this->getFieldNameByFormatFieldName("LIB_TITLE")] = trim($library->info["name"]);
            $arrayLibrary[$this->getFieldNameByFormatFieldName("LIB_DESCRIPTION")] = trim(str_replace("*", "", implode("", $library->info["description"])));
            $arrayLibrary[$this->getFieldNameByFormatFieldName("LIB_ICON")] = (isset($library->info["icon"]) && trim($library->info["icon"]) != "")? trim($library->info["icon"]) : "/images/browse.gif";
            $arrayLibrary[$this->getFieldNameByFormatFieldName("LIB_CLASS_NAME")] = trim($library->info["className"]);

            $arrayMethod = array();

            if (count($library->methods) > 0) {
                ksort($library->methods, SORT_STRING);

                foreach ($library->methods as $key => $value) {
                    $methodName = $key;

                    $arrayMethod[] = $this->getMethod($libraryName, $methodName);
                }
            }

            $arrayLibrary[$this->getFieldNameByFormatFieldName("LIB_FUNCTIONS")] = $arrayMethod;

            //Return
            return $arrayLibrary;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Trigger
     *
     * @param string $libraryName Library name
     * @param string $methodName  Method name
     * @param string $triggerUid  Unique id of Trigger
     *
     * return array Return an array with data of a Trigger
     */
    public function getTrigger($libraryName, $methodName, $triggerUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfLibraryAndMethodIsInvalidForTrigger($libraryName, $methodName, $triggerUid, $this->arrayFieldNameForException["libraryName"], $this->arrayFieldNameForException["methodName"], $this->arrayFieldNameForException["triggerUid"]);

            //Get data
            $trigger = new \Triggers();

            $arrayTriggerData = $trigger->load($triggerUid);

            $arrayTriggerData["TRI_PARAM"] = unserialize($arrayTriggerData["TRI_PARAM"]);

            //PMFUNTION_NAME //createZimbraContacts
            //LIBRARY_CLASS  //class.pmZimbra.pmFunctions.php

            $arrayMethodParam = $this->methodGetParams($libraryName, $methodName);
            $arrayMethodParamReturn = $this->methodGetParamsReturn($libraryName, $methodName);

            //////////////////
            //create atributtes!!!!!!
            //////////////////

            //Return
            unset($arrayTriggerData["PRO_UID"]);
            unset($arrayTriggerData["TRI_WEBBOT"]);
            unset($arrayTriggerData["TRI_PARAM"]);

            if (!$this->formatFieldNameInUppercase) {
                $arrayTriggerData = array_change_key_case($arrayTriggerData, CASE_LOWER);
            }

            return $arrayTriggerData;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

