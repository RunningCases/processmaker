<?php
if (! class_exists("Propel")) {
    include_once __DIR__ . "/../bootstrap.php";
}

use \BpmnEvent;

/**
 * Class BpmnEventTest
 *
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
class BpmnEventTest extends PHPUnit_Framework_TestCase
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
            "EVN_UID" => "864215906402045618170352f1198ddc",
            "PRJ_UID" => self::$prjUid,
            "PRO_UID" => self::$proUid,
            "EVN_NAME" => "Event #1",
            "EVN_TYPE" => "START",
            "BOU_X" => 51,
            "BOU_Y" => 52
        );

        self::$data2 = array(
            "EVN_UID" => "70352f1198ddc8642159064020456181",
            "PRJ_UID" => self::$prjUid,
            "PRO_UID" => self::$proUid,
            "EVN_NAME" => "Event #2",
            "EVN_TYPE" => "END",
            "BOU_X" => 53,
            "BOU_Y" => 54
        );
    }

    public static function tearDownAfterClass()
    {
        $events = BpmnEvent::findAllBy(BpmnEventPeer::PRJ_UID, self::$prjUid);
        foreach ($events as $event) {
            $event->delete();
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
        $event = new BpmnEvent();
        $event->setEvnUid(self::$data1["EVN_UID"]);
        $event->setPrjUid(self::$data1["PRJ_UID"]);
        $event->setProUid(self::$data1["PRO_UID"]);
        $event->setEvnName(self::$data1["EVN_NAME"]);
        $event->setEvnType(self::$data1["EVN_TYPE"]);
        $event->getBound()->setBouX(self::$data1["BOU_X"]);
        $event->getBound()->setBouY(self::$data1["BOU_Y"]);
        $event->save();
         
        $event2 = BpmnEventPeer::retrieveByPK($event->getEvnUid());

        $this->assertNotNull($event2);

        return $event;
    }

    public function testNewUsingFromArray()
    {
        $event = new BpmnEvent();
        $event->fromArray(self::$data2);
        $event->save();

        $event2 = BpmnEventPeer::retrieveByPK($event->getEvnUid());

        $this->assertNotNull($event2);

        return $event;
    }

    /**
     * @depends testNew
     * @param $event \BpmnEvent
     */
    public function testToArrayFromTestNew($event)
    {
        $expected = array(
            "EVN_UID" => self::$data1["EVN_UID"],
            "PRJ_UID" => self::$data1["PRJ_UID"],
            "PRO_UID" => self::$data1["PRO_UID"],
            "EVN_NAME" => self::$data1["EVN_NAME"],
            "EVN_TYPE" => self::$data1["EVN_TYPE"],
            "EVN_MARKER" => "EMPTY",
            "EVN_IS_INTERRUPTING" => 1,
            "EVN_ATTACHED_TO" => "",
            "EVN_CANCEL_ACTIVITY" => 0,
            "EVN_ACTIVITY_REF" => "",
            "EVN_WAIT_FOR_COMPLETION" => 1,
            "EVN_ERROR_NAME" => null,
            "EVN_ERROR_CODE" => null,
            "EVN_ESCALATION_NAME" => null,
            "EVN_ESCALATION_CODE" => null,
            "EVN_CONDITION" => null,
            "EVN_MESSAGE" => null,
            "EVN_OPERATION_NAME" => null,
            "EVN_OPERATION_IMPLEMENTATION_REF" => null,
            "EVN_TIME_DATE" => null,
            "EVN_TIME_CYCLE" => null,
            "EVN_TIME_DURATION" => null,
            "EVN_BEHAVIOR" => "CATCH",
            "DIA_UID" => self::$diaUid,
            "ELEMENT_UID" => self::$data1["EVN_UID"],
            "BOU_ELEMENT" => "pm_canvas",
            "BOU_ELEMENT_TYPE" => "bpmnEvent",
            "BOU_X" => self::$data1["BOU_X"],
            "BOU_Y" => self::$data1["BOU_Y"],
            "BOU_WIDTH" => 0,
            "BOU_HEIGHT" => 0,
            "BOU_REL_POSITION" => 0,
            "BOU_SIZE_IDENTICAL" => 0,
            "BOU_CONTAINER" => "bpmnDiagram"
        );

        $result = $event->toArray();
        $bouUid = $result["BOU_UID"];

        $this->assertNotEmpty($bouUid);
        $this->assertEquals(32, strlen($bouUid));

        unset($result["BOU_UID"]);

        $this->assertEquals($expected, $result);
    }

    /**
     * @depends testNewUsingFromArray
     * @param $event \BpmnEvent
     */
    public function testToArrayFromTestNewUsingFromArray($event)
    {
        $expected = array(
            "EVN_UID" => self::$data2["EVN_UID"],
            "PRJ_UID" => self::$data2["PRJ_UID"],
            "PRO_UID" => self::$data2["PRO_UID"],
            "EVN_NAME" => self::$data2["EVN_NAME"],
            "EVN_TYPE" => self::$data2["EVN_TYPE"],
            "EVN_MARKER" => "EMPTY",
            "EVN_IS_INTERRUPTING" => 1,
            "EVN_ATTACHED_TO" => "",
            "EVN_CANCEL_ACTIVITY" => 0,
            "EVN_ACTIVITY_REF" => "",
            "EVN_WAIT_FOR_COMPLETION" => 1,
            "EVN_ERROR_NAME" => null,
            "EVN_ERROR_CODE" => null,
            "EVN_ESCALATION_NAME" => null,
            "EVN_ESCALATION_CODE" => null,
            "EVN_CONDITION" => null,
            "EVN_MESSAGE" => null,
            "EVN_OPERATION_NAME" => null,
            "EVN_OPERATION_IMPLEMENTATION_REF" => null,
            "EVN_TIME_DATE" => null,
            "EVN_TIME_CYCLE" => null,
            "EVN_TIME_DURATION" => null,
            "EVN_BEHAVIOR" => "CATCH",
            "DIA_UID" => self::$diaUid,
            "ELEMENT_UID" => self::$data2["EVN_UID"],
            "BOU_ELEMENT" => "pm_canvas",
            "BOU_ELEMENT_TYPE" => "bpmnEvent",
            "BOU_X" => self::$data2["BOU_X"],
            "BOU_Y" => self::$data2["BOU_Y"],
            "BOU_WIDTH" => 0,
            "BOU_HEIGHT" => 0,
            "BOU_REL_POSITION" => 0,
            "BOU_SIZE_IDENTICAL" => 0,
            "BOU_CONTAINER" => "bpmnDiagram"
        );

        $result = $event->toArray();
        $bouUid = $result["BOU_UID"];

        $this->assertNotEmpty($bouUid);
        $this->assertEquals(32, strlen($bouUid));

        unset($result["BOU_UID"]);

        $this->assertEquals($expected, $result);
    }

    public function testToArray()
    {
        $event = BpmnEventPeer::retrieveByPK(self::$data1["EVN_UID"]);

        $expected = array(
            "EVN_UID" => self::$data1["EVN_UID"],
            "PRJ_UID" => self::$data1["PRJ_UID"],
            "PRO_UID" => self::$data1["PRO_UID"],
            "EVN_NAME" => self::$data1["EVN_NAME"],
            "EVN_TYPE" => self::$data1["EVN_TYPE"],
            "EVN_MARKER" => "EMPTY",
            "EVN_IS_INTERRUPTING" => 1,
            "EVN_ATTACHED_TO" => "",
            "EVN_CANCEL_ACTIVITY" => 0,
            "EVN_ACTIVITY_REF" => "",
            "EVN_WAIT_FOR_COMPLETION" => 1,
            "EVN_ERROR_NAME" => null,
            "EVN_ERROR_CODE" => null,
            "EVN_ESCALATION_NAME" => null,
            "EVN_ESCALATION_CODE" => null,
            "EVN_CONDITION" => null,
            "EVN_MESSAGE" => null,
            "EVN_OPERATION_NAME" => null,
            "EVN_OPERATION_IMPLEMENTATION_REF" => null,
            "EVN_TIME_DATE" => null,
            "EVN_TIME_CYCLE" => null,
            "EVN_TIME_DURATION" => null,
            "EVN_BEHAVIOR" => "CATCH",
            "DIA_UID" => self::$diaUid,
            "ELEMENT_UID" => self::$data1["EVN_UID"],
            "BOU_ELEMENT" => "pm_canvas",
            "BOU_ELEMENT_TYPE" => "bpmnEvent",
            "BOU_X" => self::$data1["BOU_X"],
            "BOU_Y" => self::$data1["BOU_Y"],
            "BOU_WIDTH" => 0,
            "BOU_HEIGHT" => 0,
            "BOU_REL_POSITION" => 0,
            "BOU_SIZE_IDENTICAL" => 0,
            "BOU_CONTAINER" => "bpmnDiagram"
        );

        $result = $event->toArray();

        unset($result["BOU_UID"]);

        $this->assertEquals($expected, $result);
    }

    /**
     * @depends testNew
     * @depends testNewUsingFromArray
     * @param $event1 \BpmnEvent
     * @param $event2 \BpmnEvent
     */
    public function testDelete($event1, $event2)
    {
        $gatUid = $event1->getEvnUid();
        $event = BpmnEventPeer::retrieveByPK($gatUid);
        $event->delete();

        $this->assertNull(BpmnEventPeer::retrieveByPK($gatUid));
        // the previous call must delete the bound object related to activity too.
        $this->assertNull(BpmnBound::findByElement("Event", $gatUid));


        $gatUid = $event2->getEvnUid();
        $event = BpmnEventPeer::retrieveByPK($gatUid);
        $event->delete();

        $this->assertNull(BpmnEventPeer::retrieveByPK($gatUid));
        // the previous call must delete the bound object related to activity too.
        $this->assertNull(BpmnBound::findByElement("Event", $gatUid));
    }

}