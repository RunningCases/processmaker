<?php
if (! class_exists("Propel")) {
    include_once "../../bootstrap.php";
}

use \ProcessMaker\Project\Adapter\WorkflowProject;


class WorkflowProjectTest extends PHPUnit_Framework_TestCase
{
    protected $workflowProject;

    protected function setUp()
    {
        $this->workflowProject = new WorkflowProject();
    }

    public function testCreate()
    {
        $data = array(
            "PRO_UID" => "b72405854591363786050152eabf59dc",
            "PRO_TITLE" => "Test Project #1",
            "PRO_DESCRIPTION" => "Description for - Test Project #1",
            "PRO_CATEGORY" => "",
            "USR_UID" => "00000000000000000000000000000001",
            "TASKS" => array(
                array(
                    "TAS_UID" => "85459136378bf56050152ea9dcb72405",
                    "TAS_TITLE" => "task_1",
                    "TAS_POSX" => "50",
                    "TAS_POSY" => "50",
                    "TAS_WIDTH" => "100",
                    "TAS_HEIGHT" => "25"
                )
            ),
            "ROUTES" => array()
        );

        $result = $this->workflowProject->create($data);

        var_dump($result);
    }
}