<?php

namespace ProcessMaker\BusinessModel\Migrator;

use ProcessMaker\BusinessModel;
/**
 * The assignment rules migrator class.
 * The container class that stores the import and export rules for assignment rules.
 *
 * Class AssignmentRulesMigrator
 * @package ProcessMaker\BusinessModel\Migrator
 */

class AssignmentRulesMigrator implements Importable, Exportable
{
    protected $processes;
    protected $className;

    /**
     * AssignmentRulesMigrator constructor.
     */
    public function __construct()
    {
        $this->processes = new \Processes();
        $this->className = 'Assignment Rules';
    }

    public function beforeImport($data)
    {
        // TODO: Implement beforeImport() method.
    }

    /**
     * @param $data
     * @param $replace
     * @throws ImportException
     */
    public function import($data, $replace)
    {
        try {
            $workflowTaks = array();
            $dummyTaskTypes = BusinessModel\Task::getDummyTypes();
            foreach ($data['tasks'] as $key => $value) {
                $arrayTaskData = $value;
                if (!in_array($arrayTaskData["TAS_TYPE"], $dummyTaskTypes)) {
                    $workflowTaks[] = $arrayTaskData;
                }
            }

            if ($replace) {
                $this->processes->createTaskRows($workflowTaks);
                $this->processes->addNewGroupRow($data['groupwfs']);
                $this->processes->removeTaskUserRows($data['tasks']);
                $this->processes->createTaskUserRows($data['taskusers']);
            } else {
                $this->processes->addNewTaskRows($workflowTaks);
                $this->processes->addNewGroupRow($data['groupwfs']);
                $this->processes->addNewTaskUserRows($data['taskusers']);
            }

        } catch (\Exception $e) {
            $exception = new ImportException($e->getMessage());
            $exception->setNameException($this->className);
            throw($exception);
        }
    }

    public function afterImport($data)
    {
        // TODO: Implement afterImport() method.
    }

    public function beforeExport()
    {
        // TODO: Implement beforeExport() method.
    }

    /**
     * @param $prj_uid
     * @return array
     * @throws ExportException
     */
    public function export($prj_uid)
    {
        try {
            $oAssignRules = new \StdClass();
            $oAssignRules->tasks = $this->processes->getTaskRows($prj_uid);
            $oAssignRules->taskusers = $this->processes->getTaskUserRows($oAssignRules->tasks);
            //groups - task
            $oDataTaskUsers = $this->processes->getTaskUserRows($oAssignRules->tasks);
            $oAssignRules->groupwfs = $this->processes->getGroupwfRows($oDataTaskUsers);

            $result = array(
                'workflow-definition' => (array)$oAssignRules
            );

            return $result;

        } catch (\Exception $e) {
            $exception = new ExportException($e->getMessage());
            $exception->setNameException($this->className);
            throw($exception);
        }
    }

    public function afterExport()
    {
        // TODO: Implement afterExport() method.
    }

}