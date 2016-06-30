<?php

namespace ProcessMaker\BusinessModel\Migrator;


class MigratorFactory
{
    public function create($classname)
    {
        $class = new NullMigrator();
        switch (strtoupper($classname)) {
            case 'ASSIGNMENTRULES':
                $class = new AssignmentRulesMigrator();
                break;
            case 'FILES':
                $class = new FilesMigrator();
                break;
            case 'DBCONNECTION':
            case 'DBCONNECTIONS':
                $class = new DBConnectionMigrator();
                break;
            case 'DYNAFORMS':
                $class = new DynaformsMigrator();
                break;
            case 'INPUTDOCUMENTS':
                $class = new InputDocumentsMigrator();
                break;
            case 'OUTPUTDOCUMENTS':
                $class = new OutputDocumentsMigrator();
                break;
            case 'PROCESSDEFINITION':
                $class = new ProcessDefinitionMigrator();
                break;
            case 'REPORTTABLES':
                $class = new ReportTablesMigrator();
                break;
            case 'SUPERVISORS':
                $class = new SupervisorsMigrator();
                break;
            case 'SUPERVISORSOBJECTS':
                $class = new SupervisorsObjectsMigrator();
                break;
            case 'TEMPLATES':
                $class = new TemplatesMigrator();
                break;
            case 'TRIGGERS':
                $class = new TriggersMigrator();
                break;
            case 'VARIABLES':
                $class = new VariablesMigrator();
                break;
            case 'PERMISSIONS':
                $class = new PermissionsMigrator();
                break;
            default:
                $class = new PluginMigratorAdapter($classname);
                break;
        }
        return $class;
    }
}