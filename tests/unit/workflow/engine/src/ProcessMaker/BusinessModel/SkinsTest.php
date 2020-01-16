<?php

namespace ProcessMaker\BusinessModel;

use G;
use Tests\TestCase;

/**
 * Skins Tests
 */
class SkinsTest extends TestCase
{
    /**
     * @var Skins
     */
    protected $object;

    /**
     * Sets up the unit test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new Skins();
    }

    /**
     * Tears down the unit test.
     */
    protected function tearDown()
    {
        parent::tearDown();
        G::rm_dir(PATH_DATA . 'skins');
        mkdir(PATH_DATA . 'skins');
    }

    /**
     * Get default skins and one custom global skin.
     *
     * @covers ProcessMaker\BusinessModel\Skins::getSkins
     * @covers ProcessMaker\BusinessModel\Skins::createSkin
     * @category HOR-3208:1
     */
    public function testGetSkins()
    {
        $skins = $this->object->getSkins();
        $this->assertCount(2, $skins);
        $this->assertEquals($skins[0]['SKIN_FOLDER_ID'], 'classic');
        $this->assertEquals($skins[1]['SKIN_FOLDER_ID'], 'neoclassic');
        $this->object->createSkin('test', 'test');
        $skins2 = $this->object->getSkins();
        $this->assertCount(3, $skins2);
        $this->assertEquals($skins2[2]['SKIN_FOLDER_ID'], 'test');
    }

    /**
     * Get default skins, one custom global and one custom current workspace skin.
     *
     * @covers ProcessMaker\BusinessModel\Skins::getSkins
     * @covers ProcessMaker\BusinessModel\Skins::createSkin
     * @category HOR-3208:2
     */
    public function testGetSkinsCurrentWorkspace()
    {
        $this->object->createSkin('test', 'test');
        $this->object->createSkin('test2', 'test2', 'Second skin', 'ProcessMaker Team', 'current', 'neoclassic');
        $skins = $this->object->getSkins();
        $skins = collect($skins)
                ->sortBy('SKIN_FOLDER_ID')
                ->values()
                ->toArray();
        $this->assertCount(4, $skins);
        $this->assertEquals($skins[2]['SKIN_FOLDER_ID'], 'test');
        $this->assertEquals($skins[3]['SKIN_FOLDER_ID'], 'test2');
        $this->assertEquals($skins[3]['SKIN_WORKSPACE'], config("system.workspace"));
    }
}
