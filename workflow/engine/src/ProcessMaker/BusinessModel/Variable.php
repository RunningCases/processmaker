<?php
namespace ProcessMaker\BusinessModel;

class Variable
{
    private $arrayFieldDefinition = array(
        "VAR_NAME"            => array("type" => "string", "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "varName"),
        "VAR_FIELD_TYPE"      => array("type" => "string", "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "varFieldType"),
        "VAR_FIELD_SIZE"      => array("type" => "int",    "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "varFieldSize"),
        "VAR_LABEL"           => array("type" => "string", "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "varLabel"),
        "VAR_DBCONNECTION"    => array("type" => "string", "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "varDbconnection"),
        "VAR_SQL"             => array("type" => "string", "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "varSql"),
        "VAR_NULL"            => array("type" => "int",    "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "varNull"),
        "VAR_DEFAULT"         => array("type" => "string", "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "varDefault"),
        "VAR_ACCEPTED_VALUES" => array("type" => "string", "required" => false, "empty" => true, "defaultValues" => array(), "fieldNameAux" => "varAcceptedValues")
    );

    private $arrayFieldNameForException = array(
        "processUid" => "PRO_UID"
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
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create Variable for a Process
     *
     * @param string $processUid Unique id of Process
     * @param array  $arrayData  Data
     *
     * return array Return data of the new Variable created
     */
    public function create($processUid, array $arrayData)
    {
        try {
            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();
            $process->throwExceptionIfNotExistsProcess($processUid, $this->arrayFieldNameForException["processUid"]);

            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            $this->existsName($processUid, $arrayData["VAR_NAME"]);

            //Create
            $cnn = \Propel::getConnection("workflow");
            try {
                $variable = new \ProcessVariables();

                $sPkProcessVariables = \ProcessMaker\Util\Common::generateUID();

                $variable->setVarUid($sPkProcessVariables);
                $variable->setPrjUid($processUid);

                if ($variable->validate()) {
                    $cnn->begin();

                    $variable->setVarName($arrayData["VAR_NAME"]);
                    $variable->setVarFieldType($arrayData["VAR_FIELD_TYPE"]);
                    $variable->setVarFieldSize($arrayData["VAR_FIELD_SIZE"]);
                    $variable->setVarLabel($arrayData["VAR_LABEL"]);
                    $variable->setVarDbconnection($arrayData["VAR_DBCONNECTION"]);
                    $variable->setVarSql($arrayData["VAR_SQL"]);
                    $variable->setVarNull($arrayData["VAR_NULL"]);
                    $variable->setVarDefault($arrayData["VAR_DEFAULT"]);
                    $variable->setVarAcceptedValues($arrayData["VAR_ACCEPTED_VALUES"]);

                    $variable->save();
                    $cnn->commit();
                } else {

                    $msg = "";

                    foreach ($variable->getValidationFailures() as $validationFailure) {
                        $msg = $msg . (($msg != "")? "\n" : "") . $validationFailure->getMessage();
                    }

                    throw new \Exception(\G::LoadTranslation("ID_RECORD_CANNOT_BE_CREATED") . "\n" . $msg);
                }

            } catch (\Exception $e) {
                $cnn->rollback();

                throw $e;
            }

            //Return
            unset($arrayData["PRJ_UID"]);

            $arrayData = array_merge(array("VAR_UID" => $sPkProcessVariables), $arrayData);
            $arrayData = array_change_key_case($arrayData, CASE_LOWER);

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update Variable
     *
     * @param string $processUid Unique id of Process
     * @param string $variableUid Unique id of Variable
     * @param array  $arrayData   Data
     *
     * return array Return data of the Variable updated
     */
    public function update($processUid, $variableUid, $arrayData)
    {
        try {
            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();
            $process->throwExceptionIfNotExistsProcess($processUid, $this->arrayFieldNameForException["processUid"]);

            $this->throwExceptionIfNotExistsVariable($variableUid);

            $arrayData = array_change_key_case($arrayData, CASE_UPPER);
            $this->existsName($processUid, $arrayData["VAR_NAME"]);
            //Update
            $cnn = \Propel::getConnection("workflow");
            try {

                $variable = \ProcessVariablesPeer::retrieveByPK($variableUid);
                $variable->fromArray($arrayData, \BasePeer::TYPE_FIELDNAME);

                if ($variable->validate()) {
                    $cnn->begin();

                    $variable->setVarName($arrayData["VAR_NAME"]);
                    $variable->setVarFieldType($arrayData["VAR_FIELD_TYPE"]);
                    $variable->setVarFieldSize($arrayData["VAR_FIELD_SIZE"]);
                    $variable->setVarLabel($arrayData["VAR_LABEL"]);
                    $variable->setVarDbconnection($arrayData["VAR_DBCONNECTION"]);
                    $variable->setVarSql($arrayData["VAR_SQL"]);
                    $variable->setVarNull($arrayData["VAR_NULL"]);
                    $variable->setVarDefault($arrayData["VAR_DEFAULT"]);
                    $variable->setVarAcceptedValues($arrayData["VAR_ACCEPTED_VALUES"]);

                    $variable->save();
                    $cnn->commit();
                } else {

                    $msg = "";

                    foreach ($variable->getValidationFailures() as $validationFailure) {
                        $msg = $msg . (($msg != "")? "\n" : "") . $validationFailure->getMessage();
                    }

                    throw new \Exception(\G::LoadTranslation("ID_RECORD_CANNOT_BE_CREATED") . "\n" . $msg);
                }

            } catch (\Exception $e) {
                $cnn->rollback();

                throw $e;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete Variable
     *
     * @param string $processUid Unique id of Process
     * @param string $variableUid Unique id of Variable
     *
     * return void
     */
    public function delete($processUid, $variableUid)
    {
        try {
            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfNotExistsProcess($processUid, $this->arrayFieldNameForException["processUid"]);

            $this->throwExceptionIfNotExistsVariable($variableUid);

            //Delete
            $criteria = new \Criteria("workflow");

            $criteria->add(\ProcessVariablesPeer::VAR_UID, $variableUid);

            \ProcessVariablesPeer::doDelete($criteria);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Variable
     * @param string $processUid Unique id of Process
     * @param string $variableUid Unique id of Variable
     *
     * return array Return an array with data of a Variable
     */
    public function getVariable($processUid, $variableUid)
    {
        try {

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfNotExistsProcess($processUid, $this->arrayFieldNameForException["processUid"]);

            $this->throwExceptionIfNotExistsVariable($variableUid);

            //Get data

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_UID);
            $criteria->addSelectColumn(\ProcessVariablesPeer::PRJ_UID);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_NAME);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_FIELD_TYPE);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_FIELD_SIZE);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_LABEL);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_DBCONNECTION);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_SQL);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_NULL);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_DEFAULT);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_ACCEPTED_VALUES);

            $criteria->add(\ProcessVariablesPeer::PRJ_UID, $processUid, \Criteria::EQUAL);
            $criteria->add(\ProcessVariablesPeer::VAR_UID, $variableUid, \Criteria::EQUAL);

            $rsCriteria = \ProcessVariablesPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();
            $arrayVariables = array();

            while ($aRow = $rsCriteria->getRow()) {
                $arrayVariables = array('var_uid' => $aRow['VAR_UID'],
                    'prj_uid' => $aRow['PRJ_UID'],
                    'var_name' => $aRow['VAR_NAME'],
                    'var_field_type' => $aRow['VAR_FIELD_TYPE'],
                    'var_field_size' => $aRow['VAR_FIELD_SIZE'],
                    'var_label' => $aRow['VAR_LABEL'],
                    'var_dbconnection' => $aRow['VAR_DBCONNECTION'],
                    'var_sql' => $aRow['VAR_SQL'],
                    'var_null' => $aRow['VAR_NULL'],
                    'var_default' => $aRow['VAR_DEFAULT'],
                    'var_accepted_values' => $aRow['VAR_ACCEPTED_VALUES']);
                $rsCriteria->next();
            }
            //Return
            return $arrayVariables;

        } catch (\Exception $e) {
             throw $e;
        }
    }


    /**
     * Get data of Variables
     *
     * @param string $processUid Unique id of Process
     *
     * return array Return an array with data of a DynaForm
     */
    public function getVariables($processUid)
    {
        try {
            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfNotExistsProcess($processUid, $this->arrayFieldNameForException["processUid"]);

            //Get data

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_UID);
            $criteria->addSelectColumn(\ProcessVariablesPeer::PRJ_UID);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_NAME);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_FIELD_TYPE);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_FIELD_SIZE);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_LABEL);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_DBCONNECTION);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_SQL);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_NULL);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_DEFAULT);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_ACCEPTED_VALUES);

            $criteria->add(\ProcessVariablesPeer::PRJ_UID, $processUid, \Criteria::EQUAL);

            $rsCriteria = \ProcessVariablesPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();
            $arrayVariables = array();

            while ($aRow = $rsCriteria->getRow()) {
                $arrayVariables[] = array('var_uid' => $aRow['VAR_UID'],
                    'prj_uid' => $aRow['PRJ_UID'],
                    'var_name' => $aRow['VAR_NAME'],
                    'var_field_type' => $aRow['VAR_FIELD_TYPE'],
                    'var_field_size' => $aRow['VAR_FIELD_SIZE'],
                    'var_label' => $aRow['VAR_LABEL'],
                    'var_dbconnection' => $aRow['VAR_DBCONNECTION'],
                    'var_sql' => $aRow['VAR_SQL'],
                    'var_null' => $aRow['VAR_NULL'],
                    'var_default' => $aRow['VAR_DEFAULT'],
                    'var_accepted_values' => $aRow['VAR_ACCEPTED_VALUES']);
                $rsCriteria->next();
            }
            //Return
            return $arrayVariables;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if does not exist the variable in table PROCESS_VARIABLES
     *
     * @param string $variableUid           Unique id of variable
     *
     * return void Throw exception if does not exist the variable in table PROCESS_VARIABLES
     */
    public function throwExceptionIfNotExistsVariable($variableUid)
    {
        try {
            $obj = \ProcessVariablesPeer::retrieveByPK($variableUid);

            if (is_null($obj)) {
                throw new \Exception('var_uid: '.$variableUid. ' '.\G::LoadTranslation("ID_DOES_NOT_EXIST"));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the name of a variable
     *
     * @param string $processUid         Unique id of Process
     * @param string $variableName       Name
     * @param string $variableUidExclude Unique id of Variable to exclude
     *
     */
    public function existsName($processUid, $variableName)
    {
        try {
            $criteria = new \Criteria("workflow");
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_UID);
            $criteria->add(\ProcessVariablesPeer::VAR_NAME, $variableName, \Criteria::EQUAL);
            $criteria->add(\ProcessVariablesPeer::PRJ_UID, $processUid, \Criteria::EQUAL);
            $rsCriteria = \ProcessVariablesPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rsCriteria->next();
            if ($rsCriteria->getRow()) {
                throw new \Exception(\G::LoadTranslation("DYNAFIELD_ALREADY_EXIST"));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }


}

