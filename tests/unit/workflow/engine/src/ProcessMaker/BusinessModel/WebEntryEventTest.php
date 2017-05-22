<?php

namespace ProcessMaker\BusinessModel;

/**
 * WebEntryEventTest test
 */
class WebEntryEventTest extends \WorkflowTestCase
{
    const SKIP_VALUE = '&SKIP_VALUE%';

    /**
     * @var WebEntryEvent $object
     */
    protected $object;
    private $processUid;
    private $processUid2;
    private $adminUid = '00000000000000000000000000000001';
    private $customTitle = 'CUSTOM TITLE';
    private $domain = 'http://domain.localhost';

    /**
     * Sets up the unit test.
     */
    protected function setUp()
    {
        $this->setupDB();
        $this->processUid = $this->import(__DIR__.'/WebEntryEventTest.pmx');
        $this->processUid2 = $this->import(__DIR__.'/WebEntryEventTest2.pmx');
        $this->object = new WebEntryEvent;
        $this->setTranslation('ID_INVALID_VALUE_CAN_NOT_BE_EMPTY',
                              'ID_INVALID_VALUE_CAN_NOT_BE_EMPTY({0})');
        $this->setTranslation('ID_UNDEFINED_VALUE_IS_REQUIRED',
                              'ID_UNDEFINED_VALUE_IS_REQUIRED({0})');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        //$this->dropDB();
        $this->clearTranslations();
    }

    /**
     * @covers ProcessMaker\BusinessModel\WebEntryEvent::getWebEntryEvents
     * @category HOR-3207:5
     */
    public function testGetWebEntryEvents()
    {
        $entryEvents = $this->object->getWebEntryEvents($this->processUid);
        $this->assertCount(2, $entryEvents);
        $this->assertNotNull($entryEvents[0]['TAS_UID']);
        $this->assertNull($entryEvents[0]['WE_CUSTOM_TITLE']);
        $this->assertEquals($entryEvents[0]['WE_AUTHENTICATION'], 'ANONYMOUS');
        $this->assertEquals($entryEvents[0]['WE_HIDE_INFORMATION_BAR'], '0');
        $this->assertEquals($entryEvents[0]['WE_CALLBACK'], 'PROCESS_MAKER');
        $this->assertNull($entryEvents[0]['WE_CALLBACK_URL']);
        $this->assertEquals($entryEvents[0]['WE_LINK_GENERATION'], 'DEFAULT');
        $this->assertNull($entryEvents[0]['WE_LINK_SKIN']);
        $this->assertNull($entryEvents[0]['WE_LINK_LANGUAGE']);
        $this->assertNull($entryEvents[0]['WE_LINK_DOMAIN']);
    }

    /**
     * @covers ProcessMaker\BusinessModel\WebEntryEvent::getAllWebEntryEvents
     */
    public function testGetAllWebEntryEvents()
    {
        $entryEvents = $this->object->getAllWebEntryEvents();
        $this->assertCount(3, $entryEvents);
        $this->assertNull($entryEvents[0]['WE_CUSTOM_TITLE']);
        $this->assertEquals($entryEvents[0]['WE_AUTHENTICATION'], 'ANONYMOUS');
        $this->assertEquals($entryEvents[0]['WE_HIDE_INFORMATION_BAR'], '0');
        $this->assertEquals($entryEvents[0]['WE_CALLBACK'], 'PROCESS_MAKER');
        $this->assertNull($entryEvents[0]['WE_CALLBACK_URL']);
        $this->assertEquals($entryEvents[0]['WE_LINK_GENERATION'], 'DEFAULT');
        $this->assertNull($entryEvents[0]['WE_LINK_SKIN']);
        $this->assertNull($entryEvents[0]['WE_LINK_LANGUAGE']);
        $this->assertNull($entryEvents[0]['WE_LINK_DOMAIN']);
    }

    /**
     * @covers ProcessMaker\BusinessModel\WebEntryEvent::getWebEntryEvent
     * @category HOR-3207:6
     */
    public function testGetWebEntryEvent()
    {
        $entryEvents = $this->object->getWebEntryEvents($this->processUid);
        $entry = $this->object->getWebEntryEvent($entryEvents[0]['WEE_UID']);
        $this->assertEquals($entryEvents[0], $entry);
    }

    /**
     * Duplicated web entry
     * @cover ProcessMaker\BusinessModel\WebEntryEvent::create
     * @category HOR-3207:7,HOR-3207:2
     */
    public function testCreateSingleNonAuthAlreadyRegistered()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('**ID_WEB_ENTRY_EVENT_ALREADY_REGISTERED**');
        $entryEvents = $this->object->getWebEntryEvents($this->processUid);
        $dynaform = $this->getADynaform();
        $this->object->create(
            $this->processUid, $this->adminUid,
            [
            'EVN_UID'    => $entryEvents[0]['EVN_UID'],
            'ACT_UID'    => $entryEvents[0]['ACT_UID'],
            'DYN_UID'    => $dynaform->getDynUid(),
            'WEE_STATUS' => 'ENABLED',
            'USR_UID'    => $this->adminUid,
            'WEE_TITLE'  => $entryEvents[0]['EVN_UID'],
            ]
        );
        $this->assertEquals(
            $this->getSimpleWebEntryUrl($webEntry), $entryEvent['WEE_URL'],
                                        'Wrong single web entry url (backward compativility)'
        );
    }

    /**
     * Create a new empty single non auth WE
     * @cover ProcessMaker\BusinessModel\WebEntryEvent::create
     * @category HOR-3207:7
     */
    public function testCreateSingleNonAuth()
    {
        $processUid = $this->processUid2;
        $entryEvents = $this->object->getWebEntryEvents($processUid);
        list($webEntry, $entryEvent) = $this->createWebEntryEvent(
            $processUid, $entryEvents,
            [
            'DYN_UID' => $entryEvents[0]['DYN_UID'],
            ]
        );
        $this->assertEquals(
            $this->getSimpleWebEntryUrl($webEntry), $entryEvent['WEE_URL'],
                                        'Wrong single web entry url (backward compativility)'
        );
    }

    /**
     * Create a new empty multiple non auth WE
     * @cover ProcessMaker\BusinessModel\WebEntryEvent::create
     * @category HOR-3207:7
     */
    public function testCreateNewMultipleNonAuth()
    {
        $processUid = $this->processUid2;
        $entryEvents = $this->object->getWebEntryEvents($processUid);
        $this->createWebEntryEvent(
            $processUid, $entryEvents,
            [
            'WEE_URL'                 => $this->domain."/sys".SYS_SYS."/".SYS_LANG."/".SYS_SKIN."/".$processUid."/custom.php",
            'WE_TYPE'                 => "MULTIPLE",
            'WE_CUSTOM_TITLE'         => $this->customTitle,
            'WE_AUTHENTICATION'       => 'ANONYMOUS',
            'WE_HIDE_INFORMATION_BAR' => "0",
            'WE_CALLBACK'             => "PROCESS_MAKER",
            'WE_CALLBACK_URL'         => "http://domain.localhost/callback",
            'WE_LINK_GENERATION'      => "ADVANCED",
            'WE_LINK_SKIN'            => SYS_SKIN,
            'WE_LINK_LANGUAGE'        => SYS_LANG,
            'WE_LINK_DOMAIN'          => $this->domain,
            ]
        );
    }

    /**
     * Delete a webentry
     * @cover ProcessMaker\BusinessModel\WebEntryEvent::delete
     * @category HOR-3207:9
     */
    public function testDelete()
    {
        $processUid = $this->processUid;
        $criteria = new \Criteria;
        $criteria->add(\WebEntryPeer::PRO_UID, $processUid);
        $entryEvents = $this->object->getWebEntryEvents($processUid);
        $fistWebEntryUid = $entryEvents[0]['WEE_UID'];
        $this->assertCount(2, $entryEvents);
        $this->assertCount(2, \WebEntryPeer::doSelect($criteria));
        $this->object->delete($entryEvents[0]['WEE_UID']);
        $entryEvents = $this->object->getWebEntryEvents($processUid);
        $this->assertCount(1, $entryEvents);
        $this->assertCount(1, \WebEntryPeer::doSelect($criteria));
        $this->object->delete($entryEvents[0]['WEE_UID']);
        $entryEvents = $this->object->getWebEntryEvents($processUid);
        $this->assertCount(0, $entryEvents);
        $this->assertCount(0, \WebEntryPeer::doSelect($criteria));
        $this->expectException(\Exception::class);
        $this->object->delete($fistWebEntryUid);
    }

    /**
     * Create different combinations of WE
     * @cover ProcessMaker\BusinessModel\WebEntryEvent::create
     * @category HOR-3207:7
     */
    public function testCreate()
    {
        /* @var $webEntry \WebEntry */
        $processUid = $this->processUid2;
        $entryEvents = $this->object->getWebEntryEvents($processUid);
        $this->assertCount(1, $entryEvents);
        $rows = $this->getCombinationsFor([
            'WE_LINK_GENERATION' => ['DEFAULT', 'ADVANCED'],
            'WEE_URL'            => [
                $this->domain."/sys".SYS_SYS."/".SYS_LANG."/".SYS_SKIN."/".$processUid."/custom.php",
                null
            ],
            'WEE_STATUS'         => ['ENABLED', null],
            'WE_TYPE'            => ['MULTIPLE'],
            'WE_LINK_SKIN'       => [SYS_SKIN, null],
            'WE_LINK_LANGUAGE'   => [SYS_LANG, null],
        ]);
        $criteria = new \Criteria();
        $criteria->add(\BpmnEventPeer::PRJ_UID, $processUid);
        $criteria->add(\BpmnEventPeer::EVN_NAME, 'simple start');
        $event = \BpmnEventPeer::doSelectOne($criteria);
        foreach ($rows as $row) {
            try {
                $data = [
                    'EVN_UID'   => $event->getEvnUid(),
                    'ACT_UID'   => $entryEvents[0]['ACT_UID'],
                    'USR_UID'   => $this->adminUid,
                    'WEE_TITLE' => $event->getEvnUid(),
                ];
                foreach ($row as $key => $value) {
                    if (isset($value)) {
                        $data[$key] = $value;
                    }
                }
                $this->object->create($processUid, $this->adminUid, $data);
                $entryEvents2 = $this->object->getWebEntryEvents($processUid);
                foreach ($entryEvents2 as $entryEvent) {
                    if ($entryEvent['EVN_UID'] === $event->getEvnUid()) {
                        break;
                    }
                }
                $webEntry = $this->getWebEntry($entryEvent);
                $this->assertCount(2, $entryEvents2,
                                   'Expected 2 events after create');
                $this->object->delete($entryEvent['WEE_UID']);
                foreach ($data as $key => $value) {
                    $this->assertEquals($value, $entryEvent[$key], ">$key<");
                }
            } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
                if (
                    $row['WE_LINK_GENERATION'] === 'DEFAULT' &&
                    preg_match('/>WEE_URL</', $e->getMessage())
                ) {
                    $this->assertEquals(
                        $this->getSimpleWebEntryUrl($webEntry),
                                                    $entryEvent['WEE_URL'],
                                                    'Wrong single web entry url (backward compativility)'
                    );
                } else {
                    throw $e;
                }
            }
        }
    }

    /**
     * Create a WE with invalid parameters
     * @cover ProcessMaker\BusinessModel\WebEntryEvent::create
     * @category HOR-3207:7,HOR-3207:2
     */
    public function testInvalidCreate()
    {
        $processUid = $this->processUid2;
        $entryEvents = $this->object->getWebEntryEvents($processUid);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageRegExp('/(Please enter a valid value for (WE_TYPE|WE_AUTHENTICATION|WE_CALLBACK|WE_LINK_GENERATION)\s*){4,4}/');
        $this->createWebEntryEvent(
            $processUid, $entryEvents,
            [
            'WEE_URL'                 => $this->domain."/sys".SYS_SYS."/".SYS_LANG."/".SYS_SKIN."/".$processUid."/custom.php",
            'WE_TYPE'                 => "NOT-VALID-SINGLE",
            'WE_CUSTOM_TITLE'         => $this->customTitle,
            'WE_AUTHENTICATION'       => 'NOT-VALID-ANONYMOUS',
            'WE_HIDE_INFORMATION_BAR' => "0",
            'WE_CALLBACK'             => "NOT-VALID-PROCESS_MAKER",
            'WE_CALLBACK_URL'         => "http://domain.localhost/callback",
            'WE_LINK_GENERATION'      => "NOT-VALID-ADVANCED",
            'WE_LINK_SKIN'            => SYS_SKIN,
            'WE_LINK_LANGUAGE'        => SYS_LANG,
            'WE_LINK_DOMAIN'          => $this->domain,
            ]
        );
    }

    /**
     * Update different combinations of web entries
     * @throws \PHPUnit_Framework_ExpectationFailedException
     * @cover ProcessMaker\BusinessModel\WebEntryEvent::update
     * @category HOR-3207:8
     */
    public function testUpdate()
    {
        $processUid = $this->processUid;
        $entryEvents = $this->object->getWebEntryEvents($processUid);
        $entryEvent = $entryEvents[0];
        $webEntryEventUid = $entryEvent['WEE_UID'];
        $userUidUpdater = $this->adminUid;

        $criteria = new \Criteria;
        $criteria->add(\DynaformPeer::PRO_UID, $processUid);
        $dynaforms = \DynaformPeer::doSelect($criteria);
        $dynaformIds = [null];
        foreach ($dynaforms as $dyn) {
            $dynaformIds[] = $dyn->getDynUid();
        }

        $rows = $this->getCombinationsFor([
            'WE_LINK_GENERATION' => ['DEFAULT', 'ADVANCED'],
            'WEE_URL'            => [
                $this->domain."/sys".SYS_SYS."/".SYS_LANG."/".SYS_SKIN."/".$processUid."/custom.php",
                null
            ],
            'DYN_UID'            => $dynaformIds,
            'USR_UID'            => [null, $this->adminUid, static::SKIP_VALUE],
        ]);
        foreach ($rows as $row) {
            try {
                $this->object->update($webEntryEventUid, $userUidUpdater, $row);
                $entryEvent = $this->object->getWebEntryEvent($webEntryEventUid);
                $webEntry = $this->getWebEntry($entryEvent);
                foreach ($row as $key => $value) {
                    $this->assertEquals($value, $entryEvent[$key], ">$key<");
                }
            } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
                if (
                    $row['WE_LINK_GENERATION'] === 'DEFAULT' &&
                    preg_match('/>WEE_URL</', $e->getMessage())
                ) {
                    $this->assertEquals(
                        $this->getSimpleWebEntryUrl($webEntry),
                                                    $entryEvent['WEE_URL'],
                                                    'Wrong single web entry url (backward compativility)'
                    );
                } else {
                    throw $e;
                }
            }
        }
    }

    /**
     * Update WE with invalid parameters
     * @cover ProcessMaker\BusinessModel\WebEntryEvent::update
     * @category HOR-3207:8,HOR-3207:2
     */
    public function testInvalidUpdate()
    {
        $processUid = $this->processUid;
        $entryEvents = $this->object->getWebEntryEvents($processUid);
        $entryEvent = $entryEvents[0];
        $webEntryEventUid = $entryEvent['WEE_UID'];
        $userUidUpdater = $this->adminUid;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageRegExp('/(Please enter a valid value for (WE_TYPE|WE_AUTHENTICATION|WE_CALLBACK|WE_LINK_GENERATION)\s*){4,4}/');
        $this->object->update($webEntryEventUid, $userUidUpdater,
                              [
            'WEE_URL'                 => $this->domain."/sys".SYS_SYS."/".SYS_LANG."/".SYS_SKIN."/".$processUid."/custom.php",
            'WE_TYPE'                 => "NOT-VALID-SINGLE",
            'WE_CUSTOM_TITLE'         => $this->customTitle,
            'WE_AUTHENTICATION'       => 'NOT-VALID-ANONYMOUS',
            'WE_HIDE_INFORMATION_BAR' => "0",
            'WE_CALLBACK'             => "NOT-VALID-PROCESS_MAKER",
            'WE_CALLBACK_URL'         => "http://domain.localhost/callback",
            'WE_LINK_GENERATION'      => "NOT-VALID-ADVANCED",
            'WE_LINK_SKIN'            => SYS_SKIN,
            'WE_LINK_LANGUAGE'        => SYS_LANG,
            'WE_LINK_DOMAIN'          => $this->domain,
            ]
        );
    }

    /**
     * Required USR_UID
     * @cover ProcessMaker\BusinessModel\WebEntryEvent::update
     * @category HOR-3207:2
     */
    public function testUsrUidNotRequiredIfLoginRequired()
    {
        $processUid = $this->processUid2;
        $entryEvents = $this->object->getWebEntryEvents($processUid);
        list($webEntry, $entryEvent) = $this->createWebEntryEvent(
            $processUid, $entryEvents,
            [
            'WE_AUTHENTICATION' => 'LOGIN_REQUIRED',
            'DYN_UID'           => $entryEvents[0]['DYN_UID'],
            'USR_UID'           => null,
            ]
        );
    }

    /**
     * Required fields
     * @cover ProcessMaker\BusinessModel\WebEntryEvent::create
     * @category HOR-3207:2
     */
    public function testRequiredFields()
    {
        $processUid = $this->processUid2;
        $entryEvents = $this->object->getWebEntryEvents($processUid);
        $dynaform = $this->getADynaform($processUid);
        $criteria = new \Criteria();
        $criteria->add(\BpmnEventPeer::PRJ_UID, $processUid);
        $criteria->add(\BpmnEventPeer::EVN_NAME, 'simple start');
        $event = \BpmnEventPeer::doSelectOne($criteria);
        //EVN_UID
        try {
            $data = [
            ];
            $this->object->create($processUid, $this->adminUid, $data);
        } catch (\Exception $e) {
            $this->assertEquals('ID_INVALID_VALUE_CAN_NOT_BE_EMPTY($arrayData)',
                                $e->getMessage());
        }
        //EVN_UID
        try {
            $data = [
                'WE_CUSTOM_TITLE' => $this->customTitle,
            ];
            $this->object->create($processUid, $this->adminUid, $data);
        } catch (\Exception $e) {
            $this->assertEquals('ID_UNDEFINED_VALUE_IS_REQUIRED(EVN_UID)',
                                $e->getMessage());
        }
        //ACT_UID
        try {
            $data = [
                'EVN_UID' => $event->getEvnUid(),
            ];
            $this->object->create($processUid, $this->adminUid, $data);
        } catch (\Exception $e) {
            $this->assertEquals('ID_UNDEFINED_VALUE_IS_REQUIRED(ACT_UID)',
                                $e->getMessage());
        }
        //DYN_UID
        try {
            $data = [
                'EVN_UID' => $event->getEvnUid(),
                'ACT_UID' => $entryEvents[0]['ACT_UID'],
            ];
            $this->object->create($processUid, $this->adminUid, $data);
        } catch (\Exception $e) {
            $this->assertEquals('ID_UNDEFINED_VALUE_IS_REQUIRED(DYN_UID)',
                                $e->getMessage());
        }
        //USR_UID (WE_AUTHENTICATION=ANONYMOUS)
        try {
            $data = [
                'EVN_UID' => $event->getEvnUid(),
                'ACT_UID' => $entryEvents[0]['ACT_UID'],
                'DYN_UID' => $dynaform->getDynUid(),
            ];
            $this->object->create($processUid, $this->adminUid, $data);
        } catch (\Exception $e) {
            $this->assertEquals('ID_UNDEFINED_VALUE_IS_REQUIRED(USR_UID)',
                                $e->getMessage());
        }
        //WEE_TITLE
        try {
            $data = [
                'EVN_UID' => $event->getEvnUid(),
                'ACT_UID' => $entryEvents[0]['ACT_UID'],
                'DYN_UID' => $dynaform->getDynUid(),
                'USR_UID' => $this->adminUid,
            ];
            $this->object->create($processUid, $this->adminUid, $data);
        } catch (\Exception $e) {
            $this->assertEquals('ID_INVALID_VALUE_CAN_NOT_BE_EMPTY(WEE_TITLE)',
                                $e->getMessage());
        }
    }

    /**
     * get a dynaform
     * @return type
     */
    private function getADynaform($processUid = null)
    {
        $criteria = new \Criteria;
        $criteria->add(\DynaformPeer::PRO_UID,
                       $processUid ? $processUid : $this->processUid);
        return \DynaformPeer::doSelectOne($criteria);
    }

    /**
     * Get a WebEntry from a WebEntryEvent array
     * @param type $webEntryEvent
     * @return \WebEntry
     */
    private function getWebEntry($webEntryEvent)
    {
        $wee = \WebEntryEventPeer::retrieveByPK($webEntryEvent['WEE_UID']);
        return \WebEntryPeer::retrieveByPK($wee->getWeeWeUid());
    }

    /**
     * The default generated WebEntryUrl.
     *
     * @param \WebEntry $we
     * @return type
     */
    private function getSimpleWebEntryUrl(\WebEntry $we)
    {
        return (\G::is_https() ? "https://" : "http://").
            $_SERVER["HTTP_HOST"]."/sys".SYS_SYS."/".
            SYS_LANG."/".SYS_SKIN."/".$we->getProUid()."/".$we->getWeData();
    }

    /**
     * Create a WebEntryEvent using some default properties.
     *
     * @param type $processUid
     * @param type $entryEvents
     * @param type $config
     * @return type
     */
    private function createWebEntryEvent($processUid, $entryEvents, $config)
    {
        $this->assertCount(1, $entryEvents,
                           'Expected 1 event with web entry in process WebEntry2');
        $criteria = new \Criteria();
        $criteria->add(\BpmnEventPeer::PRJ_UID, $processUid);
        $criteria->add(\BpmnEventPeer::EVN_NAME, 'simple start');
        $event = \BpmnEventPeer::doSelectOne($criteria);
        $data = [
            'EVN_UID'    => $event->getEvnUid(),
            'ACT_UID'    => $entryEvents[0]['ACT_UID'],
            'WEE_STATUS' => 'ENABLED',
            'USR_UID'    => $this->adminUid,
            'WEE_TITLE'  => $event->getEvnUid(),
        ];
        foreach ($config as $key => $value) {
            $data[$key] = $value;
        }
        $this->object->create($processUid, $this->adminUid, $data);
        $entryEvents2 = $this->object->getWebEntryEvents($processUid);
        foreach ($entryEvents2 as $entryEvent) {
            if ($entryEvent['EVN_UID'] === $event->getEvnUid()) {
                break;
            }
        }
        $webEntry = $this->getWebEntry($entryEvent);
        $this->assertCount(2, $entryEvents2, 'Expected 2 events after create');
        foreach ($data as $key => $value) {
            $this->assertEquals($value, $entryEvent[$key], "> $key");
        }
        return [$webEntry, $entryEvent];
    }

    /**
     * Create combination rows
     * @param type $combinations
     * @return array
     */
    private function getCombinationsFor($combinations = [])
    {
        $j = 1;
        foreach ($combinations as $key => $values) {
            $j*=count($values);
        }
        $rows = [];
        for ($i = 0; $i < $j; $i++) {
            $row = [];
            $ii = $i;
            foreach ($combinations as $key => $values) {
                $c = count($values);
                $value = $values[$ii % $c];
                if (static::SKIP_VALUE !== $value) {
                    $row[$key] = $value;
                }
                $ii = floor($ii / $c);
            }
            $rows[] = $row;
        }
        return $rows;
    }
}
