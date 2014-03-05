<?php
namespace BusinessModel;

class Calendar
{
    private $arrayFieldDefinition = array(
        "CAL_UID"         => array("fieldName" => "CALENDAR_UID",         "type" => "string",   "required" => false, "empty" => false, "defaultValues" => array(),                     "fieldNameAux" => "calendarUid"),

        "CAL_NAME"        => array("fieldName" => "CALENDAR_NAME",        "type" => "string",   "required" => true,  "empty" => false, "defaultValues" => array(),                     "fieldNameAux" => "calendarName"),
        "CAL_DESCRIPTION" => array("fieldName" => "CALENDAR_DESCRIPTION", "type" => "string",   "required" => false, "empty" => true,  "defaultValues" => array(),                     "fieldNameAux" => "calendarDescription"),
        "CAL_CREATE_DATE" => array("fieldName" => "CALENDAR_CREATE_DATE", "type" => "datetime", "required" => false, "empty" => false, "defaultValues" => array(),                     "fieldNameAux" => "calendarCreateDate"),
        "CAL_UPDATE_DATE" => array("fieldName" => "CALENDAR_UPDATE_DATE", "type" => "datetime", "required" => false, "empty" => false, "defaultValues" => array(),                     "fieldNameAux" => "calendarUpdateDate"),
        "CAL_WORK_DAYS"   => array("fieldName" => "CALENDAR_WORK_DAYS",   "type" => "string",   "required" => false, "empty" => false, "defaultValues" => array(),                     "fieldNameAux" => "calendarWorkDays"),
        "CAL_STATUS"      => array("fieldName" => "CALENDAR_STATUS",      "type" => "string",   "required" => true,  "empty" => false, "defaultValues" => array("ACTIVE", "INACTIVE"), "fieldNameAux" => "calendarStatus")
    );

    private $formatFieldNameInUppercase = true;

    private $arrayFieldNameForException = array(
        "filter" => "FILTER",
        "start"  => "START",
        "limit"  => "LIMIT"
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
     * Get criteria for Calendar
     *
     * return object
     */
    public function getCalendarCriteria()
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\CalendarDefinitionPeer::CALENDAR_UID);
            $criteria->addSelectColumn(\CalendarDefinitionPeer::CALENDAR_NAME);
            $criteria->addSelectColumn(\CalendarDefinitionPeer::CALENDAR_DESCRIPTION);
            $criteria->addSelectColumn(\CalendarDefinitionPeer::CALENDAR_CREATE_DATE);
            $criteria->addSelectColumn(\CalendarDefinitionPeer::CALENDAR_UPDATE_DATE);
            $criteria->addSelectColumn(\CalendarDefinitionPeer::CALENDAR_WORK_DAYS);
            $criteria->addSelectColumn(\CalendarDefinitionPeer::CALENDAR_STATUS);
            $criteria->add(\CalendarDefinitionPeer::CALENDAR_STATUS, "DELETED", \Criteria::NOT_EQUAL);

            return $criteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Calendar from a record
     *
     * @param array $record Record
     *
     * return array Return an array with data Calendar
     */
    public function getCalendarDataFromRecord($record)
    {
        try {
            return array(
                $this->getFieldNameByFormatFieldName("CAL_UID")             => $record["CALENDAR_UID"],
                $this->getFieldNameByFormatFieldName("CAL_NAME")            => $record["CALENDAR_NAME"],
                $this->getFieldNameByFormatFieldName("CAL_DESCRIPTION")     => $record["CALENDAR_DESCRIPTION"] . "",
                $this->getFieldNameByFormatFieldName("CAL_CREATE_DATE")     => $record["CALENDAR_CREATE_DATE"] . "",
                $this->getFieldNameByFormatFieldName("CAL_UPDATE_DATE")     => $record["CALENDAR_UPDATE_DATE"] . "",
                $this->getFieldNameByFormatFieldName("CAL_WORK_DAYS")       => $record["CALENDAR_WORK_DAYS"] . "",
                $this->getFieldNameByFormatFieldName("CAL_STATUS")          => $record["CALENDAR_STATUS"],
                $this->getFieldNameByFormatFieldName("CAL_TOTAL_USERS")     => (int)($record["CALENDAR_TOTAL_USERS"]),
                $this->getFieldNameByFormatFieldName("CAL_TOTAL_PROCESSES") => (int)($record["CALENDAR_TOTAL_PROCESSES"]),
                $this->getFieldNameByFormatFieldName("CAL_TOTAL_TASKS")     => (int)($record["CALENDAR_TOTAL_TASKS"])
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all Calendars
     *
     * @param array  $arrayFilterData Data of the filters
     * @param string $sortField       Field name to sort
     * @param string $sortDir         Direction of sorting (ASC, DESC)
     * @param int    $start           Start
     * @param int    $limit           Limit
     *
     * return array Return an array with all Calendars
     */
    public function getCalendars($arrayFilterData = null, $sortField = null, $sortDir = null, $start = null, $limit = null)
    {
        try {
            $arrayCalendar = array();

            //Verify data
            $process = new \BusinessModel\Process();

            $process->throwExceptionIfDataNotMetPagerVarDefinition(array("start" => $start, "limit" => $limit), $this->arrayFieldNameForException);

            //Get data
            if (!is_null($limit) && $limit . "" == "0") {
                return $arrayCalendar;
            }

            //Set variables
            $calendar = new \CalendarDefinition();

            $arrayTotalUsersByCalendar = $calendar->getAllCounterByCalendar("USER");
            $arrayTotalProcessesByCalendar = $calendar->getAllCounterByCalendar("PROCESS");
            $arrayTotalTasksByCalendar = $calendar->getAllCounterByCalendar("TASK");

            //SQL
            $criteria = $this->getCalendarCriteria();

            if (!is_null($arrayFilterData) && is_array($arrayFilterData) && isset($arrayFilterData["filter"]) && trim($arrayFilterData["filter"]) != "") {
                $criteria->add(\CalendarDefinitionPeer::CALENDAR_NAME, "%" . $arrayFilterData["filter"] . "%", \Criteria::LIKE);
            }

            //Number records total
            $criteriaCount = clone $criteria;

            $criteriaCount->clearSelectColumns();
            $criteriaCount->addSelectColumn("COUNT(" . \CalendarDefinitionPeer::CALENDAR_UID . ") AS NUM_REC");

            $rsCriteriaCount = \CalendarDefinitionPeer::doSelectRS($criteriaCount);
            $rsCriteriaCount->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteriaCount->next();
            $row = $rsCriteriaCount->getRow();

            $numRecTotal = $row["NUM_REC"];

            //SQL
            if (!is_null($sortField) && trim($sortField) != "") {
                $sortField = strtoupper($sortField);
                $sortField = (isset($this->arrayFieldDefinition[$sortField]["fieldName"]))? $this->arrayFieldDefinition[$sortField]["fieldName"] : $sortField;

                switch ($sortField) {
                    case "CALENDAR_UID":
                    case "CALENDAR_NAME":
                    case "CALENDAR_DESCRIPTION":
                    case "CALENDAR_CREATE_DATE":
                    case "CALENDAR_UPDATE_DATE":
                    case "CALENDAR_WORK_DAYS":
                    case "CALENDAR_STATUS":
                        $sortField = \CalendarDefinitionPeer::TABLE_NAME . "." . $sortField;
                        break;
                    default:
                        $sortField = \CalendarDefinitionPeer::CALENDAR_NAME;
                        break;
                }
            } else {
                $sortField = \CalendarDefinitionPeer::CALENDAR_NAME;
            }

            if (!is_null($sortDir) && trim($sortDir) != "" && strtoupper($sortDir) == "DESC") {
                $criteria->addDescendingOrderByColumn($sortField);
            } else {
                $criteria->addAscendingOrderByColumn($sortField);
            }

            if (!is_null($start)) {
                $criteria->setOffset((int)($start));
            }

            if (!is_null($limit)) {
                $criteria->setLimit((int)($limit));
            }

            $rsCriteria = \CalendarDefinitionPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $row["CALENDAR_TOTAL_USERS"] = (isset($arrayTotalUsersByCalendar[$row["CALENDAR_UID"]]))? $arrayTotalUsersByCalendar[$row["CALENDAR_UID"]] : 0;
                $row["CALENDAR_TOTAL_PROCESSES"] = (isset($arrayTotalProcessesByCalendar[$row["CALENDAR_UID"]]))? $arrayTotalProcessesByCalendar[$row["CALENDAR_UID"]] : 0;
                $row["CALENDAR_TOTAL_TASKS"] = (isset($arrayTotalTasksByCalendar[$row["CALENDAR_UID"]]))? $arrayTotalTasksByCalendar[$row["CALENDAR_UID"]] : 0;

                $arrayCalendar[] = $this->getCalendarDataFromRecord($row);
            }

            //Return
            return $arrayCalendar;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

