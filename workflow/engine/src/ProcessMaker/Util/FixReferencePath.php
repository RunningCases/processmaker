<?php

namespace ProcessMaker\Util;

/**
 * This class regenerates the 'Propel' classes that are necessary for the
 * administration of a 'Report Table', this is caused by the import of processes
 * where the data directory of ProcessMaker has different routes.
 */
class FixReferencePath
{
    private $modeDebug = false;
    private $resumeDebug = "";

    /**
     * Get property modeDebug.
     *
     * @return boolean
     */
    public function getModeDebug()
    {
        return $this->modeDebug;
    }

    /**
     * Set property modeDebug.
     *
     * @param boolean $modeDebug
     */
    public function setModeDebug($modeDebug)
    {
        $this->modeDebug = $modeDebug;
    }

    /**
     * Get property resumeDebug.
     *
     * @return string
     */
    public function getResumeDebug()
    {
        return $this->resumeDebug;
    }

    /**
     * Set property resumeDebug.
     *
     * @param string $resumeDebug
     */
    public function setResumeDebug($resumeDebug)
    {
        $this->resumeDebug = $resumeDebug;
    }

    /**
     * Find all PHP type files recursively.
     * The '$pathData' argument is the path to be replaced with the path found
     * as incorrect.
     *
     * @param string $directory
     * @param string $pathData
     * @return void
     */
    public function runProcess($directory, $pathData)
    {
        try {
            //This variable is not defined and does not involve its value in this
            //task, it is removed at the end of the method.
            $_SERVER["REQUEST_URI"] = "";
            if (!defined("SYS_SKIN")) {
                $conf = new \Configurations();
                define("SYS_SKIN", $conf->getConfiguration('SKIN_CRON', ''));
            }

            $criteria = new \Criteria("workflow");
            $criteria->addSelectColumn(\ReportTablePeer::REP_TAB_UID);
            $criteria->addSelectColumn(\CaseConsolidatedCorePeer::TAS_UID);
            $criteria->addSelectColumn(\ReportTablePeer::REP_TAB_NAME);
            $criteria->addJoin(\ReportTablePeer::REP_TAB_UID, \CaseConsolidatedCorePeer::REP_TAB_UID, \Criteria::JOIN);
            $criteria->add(\CaseConsolidatedCorePeer::CON_STATUS, "ACTIVE", \Criteria::EQUAL);
            $doSelect = \ReportTablePeer::doSelectRS($criteria);
            $doSelect->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($doSelect->next()) {
                $row = $doSelect->getRow();
                $fields = $this->getReportTableFields($row["REP_TAB_UID"]);
                $this->regeneratePropelClasses($row["REP_TAB_NAME"], $fields, $row["TAS_UID"]);
                $this->outVerboseln("* Regenerate classes for table: " . $row["REP_TAB_NAME"]);
            }

            unset($_SERVER["REQUEST_URI"]);
        } catch (Exception $e) {
            CLI::logging(CLI::error("Error:" . "Error in updating consolidated files, proceed to regenerate manually: " . $e));
        }
    }

    /**
     * Gets the fields of the 'Report Table'.
     *
     * @param string $repTabUid
     * @return array
     */
    public function getReportTableFields($repTabUid)
    {
        $fields = array();
        $criteria = new \Criteria("workflow");
        $criteria->addSelectColumn(\ReportVarPeer::REP_VAR_NAME);
        $criteria->addSelectColumn(\ReportVarPeer::REP_VAR_TYPE);
        $criteria->add(\ReportVarPeer::REP_TAB_UID, $repTabUid, \Criteria::EQUAL);
        $doSelect = \ReportVarPeer::doSelectRS($criteria);
        $doSelect->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        while ($doSelect->next()) {
            $row = $doSelect->getRow();
            $fields[] = $row['REP_VAR_NAME'] . '-' . $row['REP_VAR_TYPE'];
        }
        return $fields;
    }

    /**
     * Regenerate 'Propel' classes for 'Report Tables'. The name of the 'Report Table',
     * the fields and the related task are required.
     *
     * @param string $repTabName
     * @param array $fields
     * @param string $guid
     * @return void
     */
    public function regeneratePropelClasses($repTabName, $fields, $guid)
    {
        $consolidatedCases = new \ConsolidatedCases();
        list($outFieldsClass, $outFields) = $consolidatedCases->buildReportVariables($fields);

        $className = $repTabName;
        $sourcePath = PATH_DB . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;

        @unlink($sourcePath . $className . '.php');
        @unlink($sourcePath . $className . 'Peer.php');
        @unlink($sourcePath . PATH_SEP . 'map' . PATH_SEP . $className . 'MapBuilder.php');
        @unlink($sourcePath . PATH_SEP . 'om' . PATH_SEP . 'Base' . $className . '.php');
        @unlink($sourcePath . PATH_SEP . 'om' . PATH_SEP . 'Base' . $className . 'Peer.php');

        $additionalTables = new \AdditionalTables();
        $additionalTables->createPropelClasses($repTabName, $className, $outFieldsClass, $guid);
    }

    /**
     * Display the output found, the message is not displayed if the value of the
     * 'modeVerbose' property is false.
     *
     * @param string $message
     * @return void
     */
    private function outVerbose($message)
    {
        $this->resumeDebug = $this->resumeDebug . $message;
        if ($this->modeDebug === true) {
            echo $message;
        }
    }

    /**
     * Shows on the screen the output found with line break.
     *
     * @param string $message
     * @return void
     */
    private function outVerboseln($message)
    {
        $this->outVerbose($message . "\n");
    }
}

