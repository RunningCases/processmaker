<?php 


abstract class BasicEnum {
    private static $constCacheArray = NULL;

    private static function getConstants() {
        if (self::$constCacheArray == NULL) { 
            self::$constCacheArray = array();
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }

    public static function isValidName($name, $strict = false) {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }

    public static function isValidValue($value) {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict = true);
    }
}


abstract class ReportingPeriodicityEnum extends BasicEnum {
	//100s space to easy add more periods if in the future new periods are needed
    const NONE = 0;
    const MONTH = 100;
    const QUARTER = 200;
    const SEMESTER = 300;
    const YEAR = 400;

	public static function fromValue($value) {
		if ($value == ReportingPeriodicityEnum::NONE) return ReportingPeriodicityEnum::NONE;
		if ($value == ReportingPeriodicityEnum::MONTH) return ReportingPeriodicityEnum::MONTH;
		if ($value == ReportingPeriodicityEnum::QUARTER) return ReportingPeriodicityEnum::QUARTER;
		if ($value == ReportingPeriodicityEnum::SEMESTER) return ReportingPeriodicityEnum::SEMESTER;
		if ($value == ReportingPeriodicityEnum::YEAR) return ReportingPeriodicityEnum::YEAR;
		return ReportingPeriodicityEnum::MONTH;
	}

	public static function labelFromValue($value) {
		if ($value == ReportingPeriodicityEnum::MONTH) return "ID_MONTH" ;
		if ($value == ReportingPeriodicityEnum::QUARTER) return "ID_QUARTER";
		if ($value == ReportingPeriodicityEnum::SEMESTER) return "ID_SEMESTER";
		if ($value == ReportingPeriodicityEnum::YEAR) return "ID_YEAR";
		return "ID_MONTH";
	}
}

abstract class IndicatorDataSourcesEnum extends BasicEnum {
	//100s space to easy add more periods if in the future new periods are needed
	const USER = 0;
	const PROCESS = 100;
	const PROCESS_CATEGORY = 200;
	const USER_GROUP = 300;
}

class indicatorsCalculator
{
	private static $connectionName = 'workflow';

	private $userReportingMetadata = array("tableName" => "USR_REPORTING", "keyField" => "USR_UID");
	private $processReportingMetadata = array("tableName" => "PRO_REPORTING", "keyField" => "PRO_UID");
	private $userGroupReportingMetadata = array("tableName" => "USR_REPORTING", "keyField" => "USR_UID");
	private $processCategoryReportingMetadata = array("tableName" => "PRO_REPORTING", "keyField" => "PRO_UID");

	private $peiCostFormula = "SUM(TOTAL_CASES_OUT * CONFIGURED_TASK_TIME - TOTAL_TIME_BY_TASK * USER_HOUR_COST)";
	private $peiFormula = "SUM(TOTAL_CASES_OUT*CONFIGURED_TASK_TIME) / SUM(SDV_TIME * TOTAL_CASES_OUT + TOTAL_TIME_BY_TASK)";

	private $ueiCostFormula = "SUM(TOTAL_CASES_OUT * CONFIGURED_TASK_TIME - TOTAL_TIME_BY_TASK * USER_HOUR_COST)";
	private $ueiFormula = "SUM(TOTAL_CASES_OUT * CONFIGURED_TASK_TIME) / SUM(TOTAL_TIME_BY_TASK * USER_HOUR_COST)";

//
//	public function processEfficiencyIndex($processList, $initDate, $endDate)
//	{
//		$resultList = $this->processEfficiencyIndexList($processList, $initDate, $endDate, ReportingPeriodicityEnum::NONE);
//		return ($resultList[0]['PEI']);
//	}

	public function peiHistoric($processId, $initDate, $endDate, $periodicity) {
		if (!is_a($initDate, 'DateTime')) throw new InvalidArgumentException ('initDate parameter must be a DateTime object.', 0);
		if (!is_a($endDate, 'DateTime')) throw new InvalidArgumentException ('endDate parameter must be a DateTime object.', 0);

		$sqlString = $this->indicatorsBasicQueryBuilder(IndicatorDataSourcesEnum::USER
			, $processId, $periodicity, $initDate, $endDate
			, $this->peiFormula);
	
		$returnValue = $this->propelExecutor($sqlString);
		return $returnValue;
	}

	public function indicatorData($indicatorId)
	{
		$sqlString = "select * from DASHBOARD_INDICATOR  where DAS_IND_UID= '$indicatorId'";
		$retval = $this->propelExecutor($sqlString);
		return $retval;
	}

	public function peiProcesses($indicatorId, $initDate, $endDate, $language)
	{
		if (!is_a($initDate, 'DateTime')) throw new InvalidArgumentException ('initDate parameter must be a DateTime object.', 0);
		if (!is_a($endDate, 'DateTime')) throw new InvalidArgumentException ('endDate parameter must be a DateTime object.', 0);

		$initYear = $initDate->format("Y");
		$initMonth = $initDate->format("m");
		$initDay = $endDay = 1;
		$endYear = $endDate->format("Y");
		$endMonth = $endDate->format("m");

		$sqlString = "
					select
                        i.PRO_UID as uid,
                        tp.CON_VALUE as name,
                        efficiencyIndex,
                        inefficiencyCost
                    from
                    (	select
                            PRO_UID,
                            $this->peiFormula as efficiencyIndex,
                            $this->peiCostFormula as inefficiencyCost
                        from  USR_REPORTING
                            WHERE
							(
								PRO_UID = (select DAS_UID_PROCESS from  DASHBOARD_INDICATOR where DAS_IND_UID = '$indicatorId')
								or
								(select DAS_UID_PROCESS from  DASHBOARD_INDICATOR where DAS_IND_UID = '$indicatorId')= '0'
							)
							AND
                            IF (`YEAR` = $initYear, `MONTH`, `YEAR`) >= IF (`YEAR` = $initYear, $initMonth, $initYear)
                            AND
                            IF(`YEAR` = $endYear, `MONTH`, `YEAR`) <= IF (`YEAR` = $endYear, $endMonth, $endYear)
                        group by PRO_UID
                    ) i
                    left join (select *
                                        from CONTENT
                                        where CON_CATEGORY = 'PRO_TITLE'
                                                and CON_LANG = '$language'
                                ) tp on i.PRO_UID = tp.CON_ID";
		$retval = $this->propelExecutor($sqlString);
		return $retval;
	}

	public function ueiUserGroups($indicatorId, $initDate, $endDate, $language)
	{
		//for the moment all the indicator summarizes ALL users, so indicatorId is not used in this function.
		if (!is_a($initDate, 'DateTime')) throw new InvalidArgumentException ('initDate parameter must be a DateTime object.', 0);
		if (!is_a($endDate, 'DateTime')) throw new InvalidArgumentException ('endDate parameter must be a DateTime object.', 0);

		$initYear = $initDate->format("Y");
		$initMonth = $initDate->format("m");
		$initDay = $endDay = 1;
		$endYear = $endDate->format("Y");
		$endMonth = $endDate->format("m");

		//TODO ADD to USR_REPORTING the user's Group to speed up the query.
		$sqlString = "
				select
						 IFNULL(i.GRP_UID, '0') as uid,
						 IFNULL(tp.CON_VALUE, 'No Group') as name,
						 efficiencyIndex,
						 inefficiencyCost,
						 averageTime,
						 deviationTime
				from
				(	select
					   gu.GRP_UID,
					   $this->ueiFormula as efficiencyIndex,
					   $this->ueiCostFormula as inefficiencyCost,
					   AVG(AVG_TIME) as averageTime,
					   AVG(SDV_TIME) as deviationTime 
				   from  USR_REPORTING ur
				   left join
				   GROUP_USER gu on gu.USR_UID = ur.USR_UID
				   WHERE
					IF (`YEAR` = $initYear, `MONTH`, `YEAR`) >= IF (`YEAR` = $initYear, $initMonth, $initYear)
					AND
					IF(`YEAR` = $endYear, `MONTH`, `YEAR`) <= IF (`YEAR` = $endYear, $endMonth, $endYear)
				   group by gu.GRP_UID
				) i
				left join (select *
								from CONTENT
							where CON_CATEGORY = 'GRP_TITLE'
									and CON_LANG = 'en'
						   ) tp on i.GRP_UID = tp.CON_ID";

		$retval = $this->propelExecutor($sqlString);
		return $retval;
	}

	public function groupEmployeesData($groupId, $initDate, $endDate, $language)
	{
		//TODO what if we are analizing empty user group (users without group)
		//for the moment all the indicator summarizes ALL users, so indicatorId is not used in this function.
		if (!is_a($initDate, 'DateTime')) throw new InvalidArgumentException ('initDate parameter must be a DateTime object.', 0);
		if (!is_a($endDate, 'DateTime')) throw new InvalidArgumentException ('endDate parameter must be a DateTime object.', 0);

		$initYear = $initDate->format("Y");
		$initMonth = $initDate->format("m");
		$initDay = $endDay = 1;
		$endYear = $endDate->format("Y");
		$endMonth = $endDate->format("m");

		$sqlString = " select
						   i.USR_UID as uid,
						   i.name,
						   efficiencyIndex,
						   inefficiencyCost,
						   averageTime,
						   deviationTime
						from
						(	select
							   u.USR_UID,
							   concat(u.USR_FIRSTNAME, ' ', u.USR_LASTNAME) as name,
							   $this->ueiFormula as efficiencyIndex,
							   $this->ueiCostFormula as inefficiencyCost,
							   AVG(AVG_TIME) as averageTime,
							   AVG(SDV_TIME) as deviationTime 
						   from  USR_REPORTING ur
						   left join
							   GROUP_USER gu on gu.USR_UID = ur.USR_UID
						   LEFT JOIN USERS u on u.USR_UID = ur.USR_UID
						   where (gu.GRP_UID = '$groupId' or ('$groupId' = '0' && gu.GRP_UID is null ))
							   AND
							   IF (`YEAR` = $initYear, `MONTH`, `YEAR`) >= IF (`YEAR` = $initYear, $initMonth, $initYear)
								AND
								IF(`YEAR` = $endYear, `MONTH`, `YEAR`) <= IF (`YEAR` = $endYear, $endMonth, $endYear)
						   group by ur.USR_UID
						) i";
		$returnValue = $this->propelExecutor($sqlString);
		return $returnValue;
	}
//
//	public function employeeEfficiencyIndex($employeeList, $initDate, $endDate)
//	{
//		$resultList = $this->employeeEfficiencyIndexList($employeeList, $initDate, $endDate, ReportingPeriodicityEnum::NONE);
//		return current(reset($resultList));
//	}

	public function ueiHistoric($employeeId, $initDate, $endDate, $periodicity)
	{
		if (!is_a($initDate, 'DateTime')) throw new InvalidArgumentException ('initDate parameter must be a DateTime object.', 0);
		if (!is_a($endDate, 'DateTime')) throw new InvalidArgumentException ('endDate parameter must be a DateTime object.', 0);

		$sqlString = $this->indicatorsBasicQueryBuilder(IndicatorDataSourcesEnum::USER
			, $employeeId, $periodicity, $initDate, $endDate
			, $this->ueiFormula);
		$returnValue = $this->propelExecutor($sqlString);
		return $returnValue;
	}

//	public function employeeEfficiencyCost($employeeList, $initDate, $endDate)
//	{
//		$resultList = $this->employeeEfficiencyCostList($employeeList, $initDate, $endDate, ReportingPeriodicityEnum::NONE);
//		return current(reset($resultList));
//	}
//
//	public function userCostByGroupHistoric($groupId, $initDate, $endDate, $periodicity) {
//		if (!is_a($initDate, 'DateTime')) throw new InvalidArgumentException ('initDate parameter must be a DateTime object.', 0);
//		if (!is_a($endDate, 'DateTime')) throw new InvalidArgumentException ('endDate parameter must be a DateTime object.', 0);
//
//		$periodicitySelectFields = $this->periodicityFieldsForSelect($periodicity);
//		$periodicityGroup = $this->periodicityFieldsForGrouping($periodicity);
//		$initYear = $initDate->format("Y");
//		$initMonth = $initDate->format("m");
//		$initDay = $endDay = 1;
//		$endYear = $endDate->format("Y");
//		$endMonth = $endDate->format("m");
//
//		$filterCondition = "";
//		if ($groupId != null && $groupId > 0) {
//			$filterCondition = " AND GRP_UID = '$groupId'";
//		}
//
//		$sqlString = "SELECT  (SUM(CONFIGURED_TASK_TIME) - SUM(TOTAL_TIME_BY_TASK)) * USER_HOUR_COST as EEC
//						FROM  USR_REPORTING ur
//							LEFT JOIN GROUP_USER gu on gu.USR_UID =ur.USR_UID
//							LEFT JOIN GROUP_USER gu on gu.USR_UID =ur.USR_UID
//						WHERE
//						IF (`YEAR` = $initYear, `MONTH`, `YEAR`) >= IF (`YEAR` = $initYear, $initMonth, $initYear)
//						AND
//						IF(`YEAR` = $endYear, `MONTH`, `YEAR`) <= IF (`YEAR` = $endYear, $endMonth, $endYear)"
//			. $filterCondition
//			. $periodicityGroup;
//		$returnValue = $this->propelExecutor($sqlString);
//		return $returnValue;
//	}

//	public function processEfficiencyCost($processList, $initDate, $endDate)
//	{
//		$resultList = $this->processEfficiencyCostList($processList, $initDate, $endDate, ReportingPeriodicityEnum::NONE);
//		return current(reset($resultList));
//	}

	public function peiCostHistoric($processId, $initDate, $endDate, $periodicity)
	{
		if (!is_a($initDate, 'DateTime')) throw new InvalidArgumentException ('initDate parameter must be a DateTime object.', 0);
		if (!is_a($endDate, 'DateTime')) throw new InvalidArgumentException ('endDate parameter must be a DateTime object.', 0);

		$periodicitySelectFields = $this->periodicityFieldsForSelect($periodicity);
		$periodicityGroup = $this->periodicityFieldsForGrouping($periodicity);
		$initYear = $initDate->format("Y");
		$initMonth = $initDate->format("m");
		$initDay = $endDay = 1;
		$endYear = $endDate->format("Y");
		$endMonth = $endDate->format("m");

		$filterCondition = "";
		if ($processId != null && $processId > 0) {
			$filterCondition = " AND PRO_UID =  '$processId'";
		}

		$sqlString = "SELECT $periodicitySelectFields " . $this->peiCostFormula . " as PEC
						FROM  USR_REPORTING
						WHERE
						IF (`YEAR` = $initYear, `MONTH`, `YEAR`) >= IF (`YEAR` = $initYear, $initMonth, $initYear)
						AND
						IF(`YEAR` = $endYear, `MONTH`, `YEAR`) <= IF (`YEAR` = $endYear, $endMonth, $endYear)"
			. $filterCondition
			. $periodicityGroup;

		$retval = $this->propelExecutor($sqlString);
		return $retval;
	}

	public function generalIndicatorData($indicatorId, $initDate, $endDate, $periodicity) {
		if (!is_a($initDate, 'DateTime')) throw new InvalidArgumentException ('initDate parameter must be a DateTime object.', 0);
		if (!is_a($endDate, 'DateTime')) throw new InvalidArgumentException ('endDate parameter must be a DateTime object.', 0);

		$arrayT = $this->indicatorData($indicatorId);
		if (sizeof($arrayT) == 0 ) {
			return array();
		}

		$indicator = $arrayT[0];
		$indicatorProcessId = $indicator["DAS_UID_PROCESS"];
		$indicatorType = $indicator["DAS_IND_TYPE"];
		if ($indicatorProcessId == "0" || strlen($indicatorProcessId) ==0) {
			$indicatorProcessId = null;
		}
		//$indicatorJson = unserialize($indicator['DAS_IND_PROPERTIES']);
		//$indicatorConfig = json_decode($indicatorJson);
        
        /*$graph1 = $indicatorConfig->{'IND_FIRST_FIGURE'};
        $freq1 = $indicatorConfig->{'IND_FIRST_FREQUENCY'};
        $graph2 = $indicatorConfig->{'IND_SECOND_FIGURE'};
        $freq2 = $indicatorConfig->{'IND_SECOND_FREQUENCY'};
		*/

        $graph1 = $indicator['DAS_IND_FIRST_FIGURE'];
        $freq1 = $indicator['DAS_IND_FIRST_FREQUENCY'];
        $graph2 = $indicator['DAS_IND_SECOND_FIGURE'];
        $freq2 = $indicator['DAS_IND_SECOND_FREQUENCY'];

		$graph1XLabel = G::loadTranslation(ReportingPeriodicityEnum::labelFromValue($freq1));
		$graph1YLabel = "Value";

		$graph2XLabel = G::loadTranslation(ReportingPeriodicityEnum::labelFromValue($freq2));
		$graph2YLabel = "Value";

		$graphConfigurationString = "'$graph1XLabel' as graph1XLabel,  
										'$graph1YLabel' as graph1YLabel, 
										'$graph2XLabel' as graph2XLabel,  
										'$graph2YLabel' as graph2YLabel, 
										'$graph1' as graph1Type, 
										'$freq1' as frequency1Type, 
										'$graph2' as graph2Type, 
										'$freq2' as frequency2Type,";

        switch ($indicatorType) {
			//overdue
			case "1050":
				$calcField = "$graphConfigurationString 100 * SUM(TOTAL_CASES_OVERDUE) / SUM(TOTAL_CASES_ON_TIME + TOTAL_CASES_OVERDUE) as value";
				break;
			//new cases
			case "1060":
				$calcField = "$graphConfigurationString 100 * SUM(TOTAL_CASES_IN) / SUM(TOTAL_CASES_ON_TIME + TOTAL_CASES_OVERDUE) as value";
				break;
			//completed
			case "1070":
				$calcField = "$graphConfigurationString 100 * SUM(TOTAL_CASES_OUT) / SUM(TOTAL_CASES_ON_TIME + TOTAL_CASES_OVERDUE) as value";
				break;
			default:
				throw new Exception(" The indicator id '$indicatorId' with type $indicatorType hasn't an associated operation.");
		}

		$sqlString = $this->indicatorsBasicQueryBuilder(IndicatorDataSourcesEnum::PROCESS
                            , $indicatorProcessId, $periodicity
							, $initDate, $endDate
                            , $calcField);
		$returnValue = $this->propelExecutor($sqlString);
		return $returnValue;
	}
//	/***** Indicators for overdue, new, completed ******/
//	public function totalOverdueCasesByProcess($processList, $initDate, $endDate)
//	{
//		$returnList = $this->totalOverdueCasesByProcessList($processList, $initDate, $endDate, ReportingPeriodicityEnum::NONE);
//		return current(reset($returnList));
//	}
//
//	public function totalOverdueCasesByProcessList($processList, $initDate, $endDate, $periodicity)
//	{
//		return $this->sumCasesListByProcess($processList, $initDate, $endDate, $periodicity, "SUM(TOTAL_CASES_OVERDUE)", "");
//	}
//
//	public function totalOverdueCasesByUser($userList, $initDate, $endDate)
//	{
//		$returnList = $this->totalOverdueCasesByUserList($userList, $initDate, $endDate, ReportingPeriodicityEnum::NONE);
//		return current(reset($returnList));
//	}
//
//	public function totalOverdueCasesByUserList($userList, $initDate, $endDate, $periodicity)
//	{
//		return $this->sumCasesListByUser($userList, $initDate, $endDate, $periodicity, "SUM(TOTAL_CASES_OVERDUE)", "");
//	}
//
//	//percent
//	public function percentOverdueCasesByProcess($processList, $initDate, $endDate)
//	{
//		$returnList = $this->percentOverdueCasesByProcessList($processList, $initDate, $endDate, ReportingPeriodicityEnum::NONE);
//		return current(reset($returnList));
//	}
//

//	public function percentOverdueCasesByProcessList($processList, $initDate, $endDate, $periodicity)
//	{
//		return $this->sumCasesListByProcess($processList, $initDate, $endDate, $periodicity, "100 * SUM(TOTAL_CASES_OVERDUE)", "SUM(TOTAL_CASES_ON_TIME + TOTAL_CASES_OVERDUE)");
//	}
//
//	public function percentOverdueCasesByUser($userList, $initDate, $endDate)
//	{
//		$returnList = $this->percentOverdueCasesByUserList($userList, $initDate, $endDate, ReportingPeriodicityEnum::NONE);
//		return current(reset($returnList));
//	}
//
//	public function percentOverdueCasesByUserList($userList, $initDate, $endDate, $periodicity)
//	{
//		return $this->sumCasesListByUser($userList, $initDate, $endDate, $periodicity, "100 * SUM(TOTAL_CASES_OVERDUE)", "SUM(TOTAL_CASES_ON_TIME + TOTAL_CASES_OVERDUE)");
//	}
//
//	//new cases
//	public function totalNewCasesByProcess($processList, $initDate, $endDate)
//	{
//		$returnList = $this->totalNewCasesByProcessList($processList, $initDate, $endDate, ReportingPeriodicityEnum::NONE);
//		return current(reset($returnList));
//	}
//
//	public function totalNewCasesByProcessList($processList, $initDate, $endDate, $periodicity)
//	{
//		return $this->sumCasesListByProcess($processList, $initDate, $endDate, $periodicity, "SUM(TOTAL_CASES_IN)", "");
//	}
//
//	public function totalNewCasesByUser($userList, $initDate, $endDate)
//	{
//		$returnList = $this->totalNewCasesByUserList($userList, $initDate, $endDate, ReportingPeriodicityEnum::NONE);
//		return current(reset($returnList));
//	}
//
//	public function totalNewCasesByUserList($userList, $initDate, $endDate, $periodicity)
//	{
//		return $this->sumCasesListByUser($userList, $initDate, $endDate, $periodicity, "SUM(TOTAL_CASES_IN)", "");
//	}
//
//
//	//completed cases
//	public function totalCompletedCasesByProcess($processList, $initDate, $endDate)
//	{
//		$returnList = $this->totalCompletedCasesByProcessList($processList, $initDate, $endDate, ReportingPeriodicityEnum::NONE);
//		return current(reset($returnList));
//	}
//
//	public function totalCompletedCasesByProcessList($processList, $initDate, $endDate, $periodicity)
//	{
//		return $this->sumCasesListByProcess($processList, $initDate, $endDate, $periodicity, "SUM(TOTAL_CASES_OUT)", "");
//	}
//
//	public function totalCompletedCasesByUser($userList, $initDate, $endDate)
//	{
//		$returnList = $this->totalCompletedCasesByUserList($userList, $initDate, $endDate, ReportingPeriodicityEnum::NONE);
//		return current(reset($returnList));
//	}
//
//	public function totalCompletedCasesByUserList($userList, $initDate, $endDate, $periodicity)
//	{
//		return $this->sumCasesListByUser($userList, $initDate, $endDate, $periodicity, "SUM(TOTAL_CASES_OUT)", "");
//	}
//
//	public function sumCasesListByProcess($processList, $initDate, $endDate, $periodicity, $fieldToProcess, $fieldToCompare)
//	{
//		if ($processList != null && !is_array($processList)) throw new InvalidArgumentException ('employeeList parameter must be an Array or null value.', 0);
//		if (!is_a($initDate, 'DateTime')) throw new InvalidArgumentException ('initDate parameter must be a DateTime object.', 0);
//		if (!is_a($endDate, 'DateTime')) throw new InvalidArgumentException ('endDate parameter must be a DateTime object.', 0);
//
//		$periodicitySelectFields = $this->periodicityFieldsForSelect($periodicity);
//		$periodicityGroup = $this->periodicityFieldsForGrouping($periodicity);
//		$initYear = $initDate->format("Y");
//		$initMonth = $initDate->format("m");
//		$initDay = $endDay = 1;
//		$endYear = $endDate->format("Y");
//		$endMonth = $endDate->format("m");
//
//
//		$userCondition = "";
//		if ($processList != null && sizeof($processList) > 0) {
//			$userCondition = " AND PRO_UID IN " . "('" . implode("','", $processList) . "')";
//		}
//		$comparationOperation = "";
//		if (strlen($fieldToCompare) > 0) {
//			$comparationOperation = "/$fieldToCompare";
//		}
//
//		$sqlString = "SELECT $periodicitySelectFields $fieldToProcess$comparationOperation as Indicator
//						FROM  PRO_REPORTING
//						WHERE
//						IF (`YEAR` = $initYear, `MONTH`, `YEAR`) >= IF (`YEAR` = $initYear, $initMonth, $initYear)
//						AND
//						IF(`YEAR` = $endYear, `MONTH`, `YEAR`) <= IF (`YEAR` = $endYear, $endMonth, $endYear)"
//			. $userCondition
//			. $periodicityGroup;
//
//		$retval = $this->propelExecutor($sqlString);
//		return $retval;
//	}
//
//	public function sumCasesListByUser($processList, $initDate, $endDate, $periodicity, $fieldToProcess, $fieldToCompare)
//	{
//		if ($processList != null && !is_array($processList)) throw new InvalidArgumentException ('employeeList parameter must be an Array or null value.', 0);
//		if (!is_a($initDate, 'DateTime')) throw new InvalidArgumentException ('initDate parameter must be a DateTime object.', 0);
//		if (!is_a($endDate, 'DateTime')) throw new InvalidArgumentException ('endDate parameter must be a DateTime object.', 0);
//
//		$periodicitySelectFields = $this->periodicityFieldsForSelect($periodicity);
//		$periodicityGroup = $this->periodicityFieldsForGrouping($periodicity);
//		$initYear = $initDate->format("Y");
//		$initMonth = $initDate->format("m");
//		$initDay = $endDay = 1;
//		$endYear = $endDate->format("Y");
//		$endMonth = $endDate->format("m");
//
//		$userCondition = "";
//		if ($processList != null && sizeof($processList) > 0) {
//			$userCondition = " AND USR_UID IN " . "('" . implode("','", $processList) . "')";
//		}
//
//		$comparationOperation = "";
//		if (strlen($fieldToCompare) > 0) {
//			$comparationOperation = "/" . $fieldToCompare;
//		}
//
//		$sqlString = "SELECT $periodicitySelectFields $fieldToProcess$comparationOperation as Indicator
//						FROM  USR_REPORTING
//						WHERE
//						IF (`YEAR` = $initYear, `MONTH`, `YEAR`) >= IF (`YEAR` = $initYear, $initMonth, $initYear)
//						AND
//						IF(`YEAR` = $endYear, `MONTH`, `YEAR`) <= IF (`YEAR` = $endYear, $endMonth, $endYear)"
//			. $userCondition
//			. $periodicityGroup;
//
//		$retval = $this->propelExecutor($sqlString);
//		return $retval;
//	}

	public function peiTasks($processList, $initDate, $endDate, $language)
	{
		$processCondition = "";
		if ($processList != null && sizeof($processList) > 0) {
			$processCondition = " WHERE PRO_UID IN " . "('" . implode("','", $processList) . "')";
		}
		//TODO add dates condition in query
		$sqlString = " select
                            i.TAS_UID as uid,
                            t.CON_VALUE as name,
                            i.efficienceIndex,
                            i.averageTime,
                            i.deviationTime,
                            i.configuredTime
                         FROM
                        (	select
                                TAS_UID,
                                $this->peiFormula as efficienceIndex,
                                AVG(AVG_TIME) as averageTime,
                                AVG(SDV_TIME) as deviationTime,
                                CONFIGURED_TASK_TIME as configuredTime
                            from USR_REPORTING
                            $processCondition
                            group by TAS_UID
                        ) i
                        left join (select *
                                            from CONTENT
                                            where CON_CATEGORY = 'TAS_TITLE'
                                                    and CON_LANG = '$language'
                                    ) t on i.TAS_UID = t.CON_ID";
		$retval = $this->propelExecutor($sqlString);
		return $retval;
	}

//	public function employeeTasksInfoList($userList, $initDate, $endDate, $language)
//	{
//		$userCondition = "";
//		if ($userList != null && sizeof($userList) > 0) {
//			$userCondition = " WHERE USR_UID IN " . "('" . implode("','", $userList) . "')";
//		}
//		//TODO add dates contidion to query
//		$sqlString = " select
//                            i.PRO_UID as ProcessId,
//                            tp.CON_VALUE as ProcessTitle,
//                            i.TAS_UID as TaskId,
//                            tt.CON_VALUE as TaskTitle,
//                            i.EfficienceIndex,
//                            i.TimeAverage,
//                            i.TimeSdv,
//                            i.CONFIGURED_TASK_TIME as ConfiguredTime
//                         FROM
//                        (	select
//                                PRO_UID,
//                                TAS_UID,
//                                (AVG(CONFIGURED_TASK_TIME) + AVG(SDV_TIME))/ AVG(AVG_TIME) as EfficienceIndex,
//                                AVG(AVG_TIME) as TimeAverage,
//                                AVG(SDV_TIME) as TimeSdv,
//                                CONFIGURED_TASK_TIME
//                            from USR_REPORTING
//                            $userCondition
//                            group by PRO_UID, TAS_UID
//                        ) i
//                        left join (select *
//                                            from CONTENT
//                                            where CON_CATEGORY = 'TAS_TITLE'
//                                                    and CON_LANG = '$language'
//                                    ) tt on i.TAS_UID = tt.CON_ID
//                        left join (select *
//                                            from CONTENT
//                                            where CON_CATEGORY = 'PRO_TITLE'
//                                                    and CON_LANG = '$language'
//                                    ) tp on i.PRO_UID = tp.CON_ID";
//		$retval = $this->propelExecutor($sqlString);
//		return $retval;
//	}

	private function periodicityFieldsForSelect($periodicity) {
		$periodicityFields = $this->periodicityFieldsString($periodicity);
		//add a comma if there are periodicity fields
		return $periodicityFields
		. ((strlen($periodicityFields) > 0)
			? ", "
			: "");
	}

	private function periodicityFieldsForGrouping($periodicity) {
		$periodicityFields = $this->periodicityFieldsString($periodicity);

		return ((strlen($periodicityFields) > 0)
			? " GROUP BY "
			: "") . str_replace(" AS QUARTER", "", str_replace(" AS SEMESTER", "", $periodicityFields));
	}

	private function periodicityFieldsString($periodicity) {
		if (!ReportingPeriodicityEnum::isValidValue($periodicity)) throw new ArgumentException('Not supported periodicity: ', 0, 'periodicity');

		$retval = "";
		switch ($periodicity) {
			case ReportingPeriodicityEnum::MONTH;
				$retval = "`YEAR`, `MONTH` ";
				break;
			case ReportingPeriodicityEnum::SEMESTER;
				$retval = "`YEAR`, IF (`MONTH` <= 6, 1, 2) AS SEMESTER";
				break;
			case ReportingPeriodicityEnum::QUARTER;
				$retval = "`YEAR`,  CASE WHEN `MONTH` BETWEEN 1 AND 3 THEN 1 WHEN `MONTH` BETWEEN 4 AND 6 THEN 2 WHEN `MONTH` BETWEEN 7 AND 9 THEN 3 WHEN `MONTH` BETWEEN 10 AND 12 THEN 4 END AS QUARTER";
				break;
			case ReportingPeriodicityEnum::YEAR;
				$retval = "`YEAR`  ";
				break;
		}
		return $retval;
	}

	private function propelExecutor($sqlString) {
		$con = Propel::getConnection(self::$connectionName);
		$qry = $con->PrepareStatement($sqlString);
		try {
			$dataSet = $qry->executeQuery();
		} catch (Exception $e) {
			throw new Exception("Can't execute query " . $sqlString);
		}

		$rows = Array();
		while ($dataSet->next()) {
			$rows[] = $dataSet->getRow();
		}
		return $rows;
	}

	private function indicatorsBasicQueryBuilder($reportingTable, $filterId, $periodicity, $initDate, $endDate, $fields ) {
		if (!is_a($initDate, 'DateTime')) throw new InvalidArgumentException ('initDate parameter must be a DateTime object.', 0);
		if (!is_a($endDate, 'DateTime')) throw new InvalidArgumentException ('endDate parameter must be a DateTime object.', 0);

		$tableMetadata = $this->metadataForTable($reportingTable);
		$periodicitySelectFields = $this->periodicityFieldsForSelect($periodicity);
		$periodicityGroup = $this->periodicityFieldsForGrouping($periodicity);
		$initYear = $initDate->format("Y");
		$initMonth = $initDate->format("m");
		$endYear = $endDate->format("Y");
		$endMonth = $endDate->format("m");

		$filterCondition = "";
		if ($filterId != null && $filterId > 0) {
			$filterCondition = " AND ".$tableMetadata["keyField"]." = '$filterId'";
		}

		$sqlString = "SELECT $periodicitySelectFields $fields
						FROM  ".$tableMetadata["tableName"].
					" WHERE
						IF (`YEAR` = $initYear, `MONTH`, `YEAR`) >= IF (`YEAR` = $initYear, $initMonth, $initYear)
						AND
						IF(`YEAR` = $endYear, `MONTH`, `YEAR`) <= IF (`YEAR` = $endYear, $endMonth, $endYear)"
			. $filterCondition
			. $periodicityGroup;
		return $sqlString;
	}

	private function metadataForTable($table)  {
		$returnVal = null;
		switch (strtolower($table)) {
			case IndicatorDataSourcesEnum::USER:
				$returnVal = $this->userReportingMetadata;
				break;
			case IndicatorDataSourcesEnum::PROCESS:
				$returnVal = $this->processReportingMetadata;
				break;
			case IndicatorDataSourcesEnum::USER_GROUP:
				$returnVal = $this->userGroupReportingMetadata;
				break;
			case IndicatorDataSourcesEnum::PROCESS_CATEGORY:
				$returnVal = $this->processCategoryReportingMetadata;
				break;
		}
		if ($returnVal == null) {
			throw new Exception("'$table' it's not supportes. It has not associated a template.");
		}
		return $returnVal;
	}
}


