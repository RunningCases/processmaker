<?php

namespace tests\unit\workflow\engine\src\ProcessMaker\Model;

use ProcessMaker\Model\CaseList;
use Tests\TestCase;

class CaseListTest extends TestCase
{

    /**
     * setUp method.
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * teardown method.
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * This tests the getColumnNameFromAlias method.
     * @test
     * @covers  \ProcessMaker\Model\CaseList::createSetting()
     */
    public function it_should_test_getColumnNameFromAlias()
    {
        $data = [
            'id' => 1,
            'type' => 'inbox',
            'name' => 'test1',
            'description' => 'my description',
            'tableUid' => '',
            'columns' => [],
            'userId' => 1,
            'iconList' => 'deafult.png',
            'iconColor' => 'red',
            'iconColorScreen' => 'blue',
            'createDate' => date('Y-m-d H:m:s'),
            'updateDate' => date('Y-m-d H:m:s')
        ];
        $array = CaseList::getColumnNameFromAlias($data);

        //asserts
        $this->assertArrayHasKey("CAL_ID", $array);
        $this->assertArrayHasKey("CAL_TYPE", $array);
        $this->assertArrayHasKey("CAL_NAME", $array);
        $this->assertArrayHasKey("CAL_DESCRIPTION", $array);
        $this->assertArrayHasKey("ADD_TAB_UID", $array);
        $this->assertArrayHasKey("CAL_COLUMNS", $array);
        $this->assertArrayHasKey("USR_ID", $array);
        $this->assertArrayHasKey("CAL_ICON_LIST", $array);
        $this->assertArrayHasKey("CAL_ICON_COLOR", $array);
        $this->assertArrayHasKey("CAL_ICON_COLOR_SCREEN", $array);
        $this->assertArrayHasKey("CAL_CREATE_DATE", $array);
        $this->assertArrayHasKey("CAL_UPDATE_DATE", $array);
    }

    /**
     * This tests the getAliasFromColumnName method.
     * @test
     * @covers  \ProcessMaker\Model\CaseList::createSetting()
     */
    public function it_should_test_getAliasFromColumnName()
    {
        $data = [
            'CAL_ID' => 1,
            'CAL_TYPE' => 'inbox',
            'CAL_NAME' => 'test1',
            'CAL_DESCRIPTION' => 'my description',
            'ADD_TAB_UID' => 'my description',
            'CAL_COLUMNS' => [],
            'USR_ID' => 1,
            'CAL_ICON_LIST' => 'deafult.png',
            'CAL_ICON_COLOR' => 'red',
            'CAL_ICON_COLOR_SCREEN' => 'blue',
            'CAL_CREATE_DATE' => date('Y-m-d H:m:s'),
            'CAL_UPDATE_DATE' => date('Y-m-d H:m:s')
        ];
        $array = CaseList::getAliasFromColumnName($data);

        //asserts
        $this->assertArrayHasKey("id", $array);
        $this->assertArrayHasKey("type", $array);
        $this->assertArrayHasKey("name", $array);
        $this->assertArrayHasKey("description", $array);
        $this->assertArrayHasKey("tableUid", $array);
        $this->assertArrayHasKey("columns", $array);
        $this->assertArrayHasKey("userId", $array);
        $this->assertArrayHasKey("iconList", $array);
        $this->assertArrayHasKey("iconColor", $array);
        $this->assertArrayHasKey("iconColorScreen", $array);
        $this->assertArrayHasKey("createDate", $array);
        $this->assertArrayHasKey("updateDate", $array);
    }

    /**
     * This tests the createSetting method.
     * @test
     * @covers  \ProcessMaker\Model\CaseList::createSetting()
     */
    public function it_should_test_createSetting()
    {
        $data = [
            'type' => 'inbox',
            'name' => 'test1',
            'description' => 'my description',
            'tableUid' => '',
            'columns' => [],
            'userId' => 1,
            'iconList' => 'deafult.png',
            'iconColor' => 'red',
            'iconColorScreen' => 'blue',
            'createDate' => date('Y-m-d H:m:s'),
            'updateDate' => date('Y-m-d H:m:s')
        ];
        $caseList = CaseList::createSetting($data, $data['userId']);

        //asserts
        $this->assertEquals($data['type'], $caseList->CAL_TYPE);
        $this->assertEquals($data['name'], $caseList->CAL_NAME);
        $this->assertEquals($data['description'], $caseList->CAL_DESCRIPTION);
        $this->assertEquals($data['tableUid'], $caseList->ADD_TAB_UID);
        $this->assertEquals($data['columns'], $caseList->CAL_COLUMNS);
        $this->assertEquals($data['userId'], $caseList->USR_ID);
        $this->assertEquals($data['iconList'], $caseList->CAL_ICON_LIST);
        $this->assertEquals($data['iconColor'], $caseList->CAL_ICON_COLOR);
        $this->assertEquals($data['iconColorScreen'], $caseList->CAL_ICON_COLOR_SCREEN);
    }

    /**
     * This tests the updateSetting method.
     * @test
     * @covers  \ProcessMaker\Model\CaseList::updateSetting()
     */
    public function it_should_test_updateSetting()
    {
        $data = [
            'type' => 'inbox',
            'name' => 'test1',
            'description' => 'my description',
            'tableUid' => '',
            'columns' => [],
            'userId' => 1,
            'iconList' => 'deafult.png',
            'iconColor' => 'red',
            'iconColorScreen' => 'blue',
            'createDate' => date('Y-m-d H:m:s'),
            'updateDate' => date('Y-m-d H:m:s')
        ];
        $model = CaseList::createSetting($data, $data['userId']);

        $id = $model->CAL_ID;
        $data2 = [
            'type' => 'todo',
            'name' => 'new name',
            'description' => 'new deescription',
        ];
        $caseList = CaseList::updateSetting($id, $data2, $data['userId']);

        //asserts
        $this->assertEquals($data2['type'], $caseList->CAL_TYPE);
        $this->assertEquals($data2['name'], $caseList->CAL_NAME);
        $this->assertEquals($data2['description'], $caseList->CAL_DESCRIPTION);
        $this->assertEquals($data['tableUid'], $caseList->ADD_TAB_UID);
        $this->assertEquals($data['columns'], $caseList->CAL_COLUMNS);
        $this->assertEquals($data['userId'], $caseList->USR_ID);
        $this->assertEquals($data['iconList'], $caseList->CAL_ICON_LIST);
        $this->assertEquals($data['iconColor'], $caseList->CAL_ICON_COLOR);
        $this->assertEquals($data['iconColorScreen'], $caseList->CAL_ICON_COLOR_SCREEN);
    }

    /**
     * This tests the deleteSetting method.
     * @test
     * @covers  \ProcessMaker\Model\CaseList::deleteSetting()
     */
    public function it_should_test_deleteSetting()
    {
        $data = [
            'type' => 'inbox',
            'name' => 'test1',
            'description' => 'my description',
            'tableUid' => '',
            'columns' => [],
            'userId' => 1,
            'iconList' => 'deafult.png',
            'iconColor' => 'red',
            'iconColorScreen' => 'blue',
            'createDate' => date('Y-m-d H:m:s'),
            'updateDate' => date('Y-m-d H:m:s')
        ];
        $model = CaseList::createSetting($data, $data['userId']);

        $id = $model->CAL_ID;
        $caseList = CaseList::deleteSetting($id);

        //asserts
        $this->assertEquals($data['type'], $caseList->CAL_TYPE);
        $this->assertEquals($data['name'], $caseList->CAL_NAME);
        $this->assertEquals($data['description'], $caseList->CAL_DESCRIPTION);
        $this->assertEquals($data['tableUid'], $caseList->ADD_TAB_UID);
        $this->assertEquals($data['columns'], $caseList->CAL_COLUMNS);
        $this->assertEquals($data['userId'], $caseList->USR_ID);
        $this->assertEquals($data['iconList'], $caseList->CAL_ICON_LIST);
        $this->assertEquals($data['iconColor'], $caseList->CAL_ICON_COLOR);
        $this->assertEquals($data['iconColorScreen'], $caseList->CAL_ICON_COLOR_SCREEN);
    }

    /**
     * This tests the getSetting method.
     * @test
     * @covers  \ProcessMaker\Model\CaseList::deleteSetting()
     */
    public function it_should_test_getSetting()
    {
        CaseList::truncate();

        $data = [
            'type' => 'inbox',
            'name' => 'test1',
            'description' => 'my description',
            'tableUid' => '',
            'columns' => [],
            'userId' => 1,
            'iconList' => 'deafult.png',
            'iconColor' => 'red',
            'iconColorScreen' => 'blue'
        ];
        $model1 = CaseList::createSetting($data, $data['userId']);
        $model2 = CaseList::createSetting($data, $data['userId']);
        $model3 = CaseList::createSetting($data, $data['userId']);
        $model4 = CaseList::createSetting($data, $data['userId']);

        //assert total
        $result = CaseList::getSetting('inbox', '', 0, 10);
        $this->assertArrayHasKey("total", $result);
        $this->assertArrayHasKey("data", $result);
        $this->assertEquals(4, $result['total']);

        //assert page 1
        $result = CaseList::getSetting('inbox', '', 0, 2);
        $this->assertArrayHasKey("total", $result);
        $this->assertArrayHasKey("data", $result);
        $this->assertEquals(4, $result['total']);
        $this->assertEquals(2, count($result['data']));

        //assert page 2
        $result = CaseList::getSetting('inbox', '', 2, 2);
        $this->assertArrayHasKey("total", $result);
        $this->assertArrayHasKey("data", $result);
        $this->assertEquals(4, $result['total']);
        $this->assertEquals(2, count($result['data']));

        //assert search
        $result = CaseList::getSetting('inbox', 'test1', 0, 10);
        $this->assertArrayHasKey("total", $result);
        $this->assertArrayHasKey("data", $result);
        $this->assertEquals(4, $result['total']);
        $this->assertEquals(4, count($result['data']));

        //assert search no exist result
        $result = CaseList::getSetting('inbox', 'xxxx', 0, 10);
        $this->assertArrayHasKey("total", $result);
        $this->assertArrayHasKey("data", $result);
        $this->assertEquals(0, $result['total']);
        $this->assertEquals(0, count($result['data']));
    }

    /**
     * This tests the import method.
     * @test
     * @covers  \ProcessMaker\Model\CaseList::import()
     */
    public function it_should_test_import()
    {
        $data = [
            'type' => 'inbox',
            'name' => 'test1',
            'description' => 'my description',
            'tableUid' => '',
            'columns' => [],
            'iconList' => 'deafult.png',
            'iconColor' => 'red',
            'iconColorScreen' => 'blue'
        ];
        $json = json_encode($data);
        $tempFile = sys_get_temp_dir() . '/test_' . random_int(10000, 99999);
        file_put_contents($tempFile, $json);
        $_FILES = [
            'file_content' => [
                'tmp_name' => $tempFile,
                'error' => 0
            ]
        ];
        $request_data = [];
        $ownerId = 1;
        $result = CaseList::import($request_data, $ownerId);

        //assert
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('description', $result);

        $this->assertEquals($data['type'], $result['type']);
        $this->assertEquals($data['name'], $result['name']);
        $this->assertEquals($data['description'], $result['description']);
    }

    /**
     * This tests the export method.
     * @test
     * @covers  \ProcessMaker\Model\CaseList::export()
     */
    public function it_should_test_export()
    {
        CaseList::truncate();
        $data = [
            'type' => 'inbox',
            'name' => 'test export',
            'description' => 'my description',
            'tableUid' => '',
            'columns' => [],
            'userId' => 1,
            'iconList' => 'deafult.png',
            'iconColor' => 'red',
            'iconColorScreen' => 'blue'
        ];
        CaseList::createSetting($data, $data['userId']);

        $result = CaseList::export($data['userId']);

        //assert
        $this->assertArrayHasKey('type', $result['data']);
        $this->assertArrayHasKey('name', $result['data']);
        $this->assertArrayHasKey('description', $result['data']);

        $this->assertEquals($data['type'], $result['data']['type']);
        $this->assertEquals($data['name'], $result['data']['name']);
        $this->assertEquals($data['description'], $result['data']['description']);

        //assert file export
        $this->assertFileExists($result['filename']);
    }
}
