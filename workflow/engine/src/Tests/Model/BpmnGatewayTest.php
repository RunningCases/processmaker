<?php
if (! class_exists("Propel")) {
    include_once __DIR__ . "/../bootstrap.php";
}

use \BpmnGateway;


/**
 * Class BpmnGatewayTest
 *
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
class BpmnGatewayTest extends PHPUnit_Framework_TestCase
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
            "GAT_UID" => "864215906402045618170352f1198ddc",
            "PRJ_UID" => self::$prjUid,
            "PRO_UID" => self::$proUid,
            "GAT_NAME" => "Gateway #1",
            "GAT_TYPE" => "SELECTION",
            "BOU_X" => "51",
            "BOU_Y" => "52"
        );

        self::$data2 = array(
            "GAT_UID" => "70352f1198ddc8642159064020456181",
            "PRJ_UID" => self::$prjUid,
            "PRO_UID" => self::$proUid,
            "GAT_NAME" => "Gateway #2",
            "GAT_TYPE" => "EVALUATION",
            "BOU_X" => "53",
            "BOU_Y" => "54"
        );
    }

    public static function tearDownAfterClass()
    {
        $gateways = BpmnGateway::findAllBy(BpmnGatewayPeer::PRJ_UID, self::$prjUid);
        foreach ($gateways as $gateway) {
            $gateway->delete();
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
        $gateway = new BpmnGateway();
        $gateway->setGatUid(self::$data1["GAT_UID"]);
        $gateway->setPrjUid(self::$data1["PRJ_UID"]);
        $gateway->setProUid(self::$data1["PRO_UID"]);
        $gateway->setGatName(self::$data1["GAT_NAME"]);
        $gateway->setGatType(self::$data1["GAT_TYPE"]);
        $gateway->getBound()->setBouX(self::$data1["BOU_X"]);
        $gateway->getBound()->setBouY(self::$data1["BOU_Y"]);
        $gateway->save();
         
        $gateway2 = BpmnGatewayPeer::retrieveByPK($gateway->getGatUid());

        $this->assertNotNull($gateway2);

        return $gateway;
    }

    public function testNewUsingFromArray()
    {
        $gateway = new BpmnGateway();
        $gateway->fromArray(self::$data2);
        $gateway->save();

        $gateway2 = BpmnGatewayPeer::retrieveByPK($gateway->getGatUid());

        $this->assertNotNull($gateway2);

        return $gateway;
    }

    /**
     * @depends testNew
     * @param $gateway \BpmnGateway
     */
    public function testToArrayFromTestNew($gateway)
    {
        $expected = array(
            "GAT_UID" => self::$data1["GAT_UID"],
            "PRJ_UID" => self::$data1["PRJ_UID"],
            "PRO_UID" => self::$data1["PRO_UID"],
            "GAT_NAME" => self::$data1["GAT_NAME"],
            "GAT_TYPE" => self::$data1["GAT_TYPE"],
            "GAT_DIRECTION" => "UNSPECIFIED",
            "GAT_INSTANTIATE" => 0,
            "GAT_EVENT_GATEWAT_TYPE" => 'NONE',
            "GAT_ACTIVATION_COUNT" => 0,
            "GAT_WAITING_FOR_START" => 1,
            "GAT_DEFAULT_FLOW" => "",
            "DIA_UID" => self::$diaUid,
            "ELEMENT_UID" => self::$data1["GAT_UID"],
            "BOU_ELEMENT" => "pm_canvas",
            "BOU_ELEMENT_TYPE" => "bpmnGateway",
            "BOU_X" => self::$data1["BOU_X"],
            "BOU_Y" => self::$data1["BOU_Y"],
            "BOU_WIDTH" => 0,
            "BOU_HEIGHT" => 0,
            "BOU_REL_POSITION" => 0,
            "BOU_SIZE_IDENTICAL" => 0,
            "BOU_CONTAINER" => "bpmnDiagram"
        );

        $result = $gateway->toArray();
        $bouUid = $result["BOU_UID"];

        $this->assertNotEmpty($bouUid);
        $this->assertEquals(32, strlen($bouUid));

        unset($result["BOU_UID"]);

        $this->assertEquals($expected, $result);
    }

    /**
     * @depends testNewUsingFromArray
     * @param $gateway \BpmnGateway
     */
    public function testToArrayFromTestNewUsingFromArray($gateway)
    {
        $expected = array(
            "GAT_UID" =>  self::$data2["GAT_UID"],
            "PRJ_UID" =>  self::$data2["PRJ_UID"],
            "PRO_UID" =>  self::$data2["PRO_UID"],
            "GAT_NAME" => self::$data2["GAT_NAME"],
            "GAT_TYPE" => self::$data2["GAT_TYPE"],
            "GAT_DIRECTION" => "UNSPECIFIED",
            "GAT_INSTANTIATE" => 0,
            "GAT_EVENT_GATEWAT_TYPE" => 'NONE',
            "GAT_ACTIVATION_COUNT" => 0,
            "GAT_WAITING_FOR_START" => 1,
            "GAT_DEFAULT_FLOW" => "",
            "DIA_UID" => self::$diaUid,
            "ELEMENT_UID" => self::$data2["GAT_UID"],
            "BOU_ELEMENT" => "pm_canvas",
            "BOU_ELEMENT_TYPE" => "bpmnGateway",
            "BOU_X" => self::$data2["BOU_X"],
            "BOU_Y" => self::$data2["BOU_Y"],
            "BOU_WIDTH" => 0,
            "BOU_HEIGHT" => 0,
            "BOU_REL_POSITION" => 0,
            "BOU_SIZE_IDENTICAL" => 0,
            "BOU_CONTAINER" => "bpmnDiagram"
        );

        $result = $gateway->toArray();
        $bouUid = $result["BOU_UID"];

        $this->assertNotEmpty($bouUid);
        $this->assertEquals(32, strlen($bouUid));

        unset($result["BOU_UID"]);

        $this->assertEquals($expected, $result);
    }

    public function testToArray()
    {
        $gateway = BpmnGatewayPeer::retrieveByPK(self::$data1["GAT_UID"]);

        $expected = array(
            "GAT_UID" => self::$data1["GAT_UID"],
            "PRJ_UID" => self::$data1["PRJ_UID"],
            "PRO_UID" => self::$data1["PRO_UID"],
            "GAT_NAME" => self::$data1["GAT_NAME"],
            "GAT_TYPE" => self::$data1["GAT_TYPE"],
            "GAT_DIRECTION" => "UNSPECIFIED",
            "GAT_INSTANTIATE" => 0,
            "GAT_EVENT_GATEWAT_TYPE" => 'NONE',
            "GAT_ACTIVATION_COUNT" => 0,
            "GAT_WAITING_FOR_START" => 1,
            "GAT_DEFAULT_FLOW" => "",
            "DIA_UID" => self::$diaUid,
            "ELEMENT_UID" => self::$data1["GAT_UID"],
            "BOU_ELEMENT" => "pm_canvas",
            "BOU_ELEMENT_TYPE" => "bpmnGateway",
            "BOU_X" => self::$data1["BOU_X"],
            "BOU_Y" => self::$data1["BOU_Y"],
            "BOU_WIDTH" => 0,
            "BOU_HEIGHT" => 0,
            "BOU_REL_POSITION" => 0,
            "BOU_SIZE_IDENTICAL" => 0,
            "BOU_CONTAINER" => "bpmnDiagram"
        );

        $result = $gateway->toArray();

        unset($result["BOU_UID"]);

        $this->assertEquals($expected, $result);
    }

    /**
     * @depends testNew
     * @depends testNewUsingFromArray
     * @param $gateway1 \BpmnGateway
     * @param $gateway2 \BpmnGateway
     */
    public function testDelete($gateway1, $gateway2)
    {
        $gatUid = $gateway1->getGatUid();
        $gateway = BpmnGatewayPeer::retrieveByPK($gatUid);
        $gateway->delete();

        $this->assertNull(BpmnGatewayPeer::retrieveByPK($gatUid));
        // the previous call must delete the bound object related to activity too.
        $this->assertNull(BpmnBound::findByElement("Gateway", $gatUid));


        $gatUid = $gateway2->getGatUid();
        $gateway = BpmnGatewayPeer::retrieveByPK($gatUid);
        $gateway->delete();

        $this->assertNull(BpmnGatewayPeer::retrieveByPK($gatUid));
        // the previous call must delete the bound object related to activity too.
        $this->assertNull(BpmnBound::findByElement("Gateway", $gatUid));
    }

}