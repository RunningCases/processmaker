<?php
if (! class_exists("Propel")) {
    include_once __DIR__ . "/../bootstrap.php";
}

use \BpmnActivity;

class BpmnActivityTest extends PHPUnit_Framework_TestCase
{
    protected static $prjUid = "00000000000000000000000000000001";
    protected static $diaUid = "18171550f1198ddc8642045664020352";
    protected static $proUid = "155064020352f1198ddc864204561817";

    protected static $data1;
    protected static $data2;

    public static function setUpBeforeClass()
    {
        $project = new \BpmnProject();
        $project->setPrjUid(self::$prjUid);
        $project->setPrjName("Dummy Project");
        $project->save();

        $process = new \BpmnDiagram();
        $process->setDiaUid(self::$diaUid);
        $process->setPrjUid(self::$prjUid);
        $process->save();

        $process = new \BpmnProcess();
        $process->setProUid(self::$proUid);
        $process->setPrjUid(self::$prjUid);
        $process->setDiaUid(self::$diaUid);
        $process->save();

        self::$data1 = array(
            "ACT_UID" => "864215906402045618170352f1198ddc",
            "PRJ_UID" => self::$prjUid,
            "PRO_UID" => self::$proUid,
            "ACT_NAME" => "Activity #1",
            "BOU_X" => "51",
            "BOU_Y" => "52"
        );

        self::$data2 = array(
            "ACT_UID" => "70352f1198ddc8642159064020456181",
            "PRJ_UID" => self::$prjUid,
            "PRO_UID" => self::$proUid,
            "ACT_NAME" => "Activity #2",
            "BOU_X" => "53",
            "BOU_Y" => "54"
        );
    }

    public static function tearDownAfterClass()
    {
        $activities = BpmnActivity::findAllBy(BpmnActivityPeer::PRJ_UID, self::$prjUid);
        foreach ($activities as $activity) {
            $activity->delete();
        }

        $bounds = BpmnBound::findAllBy(BpmnBoundPeer::PRJ_UID, self::$prjUid);
        foreach ($bounds as $bound) {
            $bound->delete();
        }

        $process = BpmnProcessPeer::retrieveByPK(self::$proUid);
        $process->delete();

        $diagram = BpmnDiagramPeer::retrieveByPK(self::$diaUid);
        $diagram->delete();

        $project = BpmnProjectPeer::retrieveByPK(self::$prjUid);
        $project->delete();
    }

    public function testNew()
    {
        $activity = new BpmnActivity();
        $activity->setActUid(self::$data1["ACT_UID"]);
        $activity->setPrjUid(self::$data1["PRJ_UID"]);
        $activity->setProUid(self::$data1["PRO_UID"]);
        $activity->setActName(self::$data1["ACT_NAME"]);
        $activity->getBound()->setBouX(self::$data1["BOU_X"]);
        $activity->getBound()->setBouY(self::$data1["BOU_Y"]);
        $activity->save();

        $activity2 = BpmnActivityPeer::retrieveByPK($activity->getActUid());

        $this->assertNotNull($activity2);

        return $activity;
    }

    public function testNewUsingFromArray()
    {
        $activity = new BpmnActivity();
        $activity->fromArray(self::$data2);

        $activity->save();

        $activity2 = BpmnActivityPeer::retrieveByPK($activity->getActUid());

        $this->assertNotNull($activity2);

        return $activity;
    }

    /**
     * @depends testNew
     */
    public function testToArrayFromTestNew($activity)
    {
        $expected = array(
            "ACT_UID" => self::$data1["ACT_UID"],
            "PRJ_UID" => self::$data1["PRJ_UID"],
            "PRO_UID" => self::$data1["PRO_UID"],
            "ACT_NAME" => self::$data1["ACT_NAME"],
            "ACT_TYPE" => "TASK",
            "ACT_IS_FOR_COMPENSATION" => "0",
            "ACT_START_QUANTITY" => "1",
            "ACT_COMPLETION_QUANTITY" => "1",
            "ACT_TASK_TYPE" => "EMPTY",
            "ACT_IMPLEMENTATION" => "",
            "ACT_INSTANTIATE" => "0",
            "ACT_SCRIPT_TYPE" => "",
            "ACT_SCRIPT" => "",
            "ACT_LOOP_TYPE" => "NONE",
            "ACT_TEST_BEFORE" => "0",
            "ACT_LOOP_MAXIMUM" => "0",
            "ACT_LOOP_CONDITION" => "",
            "ACT_LOOP_CARDINALITY" => "0",
            "ACT_LOOP_BEHAVIOR" => "NONE",
            "ACT_IS_ADHOC" => "0",
            "ACT_IS_COLLAPSED" => "1",
            "ACT_COMPLETION_CONDITION" => "",
            "ACT_ORDERING" => "PARALLEL",
            "ACT_CANCEL_REMAINING_INSTANCES" => "1",
            "ACT_PROTOCOL" => "",
            "ACT_METHOD" => "",
            "ACT_IS_GLOBAL" => "0",
            "ACT_REFERER" => "",
            "ACT_DEFAULT_FLOW" => "",
            "ACT_MASTER_DIAGRAM" => "",
            "DIA_UID" => "18171550f1198ddc8642045664020352",
            "ELEMENT_UID" => self::$data1["ACT_UID"],
            "BOU_ELEMENT" => "pm_canvas",
            "BOU_ELEMENT_TYPE" => "bpmnActivity",
            "BOU_X" => self::$data1["BOU_X"],
            "BOU_Y" => self::$data1["BOU_Y"],
            "BOU_WIDTH" => "0",
            "BOU_HEIGHT" => "0",
            "BOU_REL_POSITION" => "0",
            "BOU_SIZE_IDENTICAL" => "0",
            "BOU_CONTAINER" => "bpmnDiagram"
        );

        $result = $activity->toArray();
        $bouUid = $result["BOU_UID"];

        $this->assertNotEmpty($bouUid);
        $this->assertEquals(32, strlen($bouUid));

        unset($result["BOU_UID"]);

        $this->assertEquals($expected, $result);
    }

    /**
     * @depends testNewUsingFromArray
     */
    public function testToArrayFromTestNewUsingFromArray($activity)
    {
        $expected = array(
            "ACT_UID" => self::$data2["ACT_UID"],
            "PRJ_UID" => self::$data2["PRJ_UID"],
            "PRO_UID" => self::$data2["PRO_UID"],
            "ACT_NAME" => self::$data2["ACT_NAME"],
            "ACT_TYPE" => "TASK",
            "ACT_IS_FOR_COMPENSATION" => "0",
            "ACT_START_QUANTITY" => "1",
            "ACT_COMPLETION_QUANTITY" => "1",
            "ACT_TASK_TYPE" => "EMPTY",
            "ACT_IMPLEMENTATION" => "",
            "ACT_INSTANTIATE" => "0",
            "ACT_SCRIPT_TYPE" => "",
            "ACT_SCRIPT" => "",
            "ACT_LOOP_TYPE" => "NONE",
            "ACT_TEST_BEFORE" => "0",
            "ACT_LOOP_MAXIMUM" => "0",
            "ACT_LOOP_CONDITION" => "",
            "ACT_LOOP_CARDINALITY" => "0",
            "ACT_LOOP_BEHAVIOR" => "NONE",
            "ACT_IS_ADHOC" => "0",
            "ACT_IS_COLLAPSED" => "1",
            "ACT_COMPLETION_CONDITION" => "",
            "ACT_ORDERING" => "PARALLEL",
            "ACT_CANCEL_REMAINING_INSTANCES" => "1",
            "ACT_PROTOCOL" => "",
            "ACT_METHOD" => "",
            "ACT_IS_GLOBAL" => "0",
            "ACT_REFERER" => "",
            "ACT_DEFAULT_FLOW" => "",
            "ACT_MASTER_DIAGRAM" => "",
            "DIA_UID" => "18171550f1198ddc8642045664020352",
            "ELEMENT_UID" => self::$data2["ACT_UID"],
            "BOU_ELEMENT" => "pm_canvas",
            "BOU_ELEMENT_TYPE" => "bpmnActivity",
            "BOU_X" => self::$data2["BOU_X"],
            "BOU_Y" => self::$data2["BOU_Y"],
            "BOU_WIDTH" => "0",
            "BOU_HEIGHT" => "0",
            "BOU_REL_POSITION" => "0",
            "BOU_SIZE_IDENTICAL" => "0",
            "BOU_CONTAINER" => "bpmnDiagram"
        );

        $result = $activity->toArray();
        $bouUid = $result["BOU_UID"];

        $this->assertNotEmpty($bouUid);
        $this->assertEquals(32, strlen($bouUid));

        unset($result["BOU_UID"]);

        $this->assertEquals($expected, $result);
    }

    /**
     * @depends testNew
     * @depends testNewUsingFromArray
     * @param $activity1 \BpmnActivity
     * @param $activity2 \BpmnActivity
     */
    public function testDelete($activity1, $activity2)
    {
        $actUid = $activity1->getActUid();
        $activity = BpmnActivityPeer::retrieveByPK($actUid);
        $activity->delete();

        $this->assertNull(BpmnActivityPeer::retrieveByPK($actUid));
        // the previous call must delete the bound object related to activity too.
        $this->assertNull(BpmnBound::findByElement("Activity", $actUid));


        $actUid = $activity2->getActUid();
        $activity = BpmnActivityPeer::retrieveByPK($actUid);
        $activity->delete();

        $this->assertNull(BpmnActivityPeer::retrieveByPK($actUid));
        // the previous call must delete the bound object related to activity too.
        $this->assertNull(BpmnBound::findByElement("Activity", $actUid));
    }

}