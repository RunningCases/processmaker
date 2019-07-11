<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel;

use ProcessMaker\BusinessModel\Language;
use System;
use Tests\TestCase;

/**
 * Test the ProcessMaker\BusinessModel\Language class.
 */
class LanguageTest extends TestCase
{
    /**
     * @var Language
     */
    protected $object;
    private $translationEnv;

    /**
     * Sets up the unit tests.
     */
    protected function setUp()
    {
        $this->getBaseUri();
        $this->object = new Language;
        $this->translationEnv = PATH_DATA . "META-INF" . PATH_SEP . "translations.env";
        file_exists($this->translationEnv) ? unlink($this->translationEnv) : false;
    }

    /**
     * Get base uri for rest applications.
     * @return string
     */
    private function getBaseUri()
    {
        $_SERVER = $this->getServerInformation();
        $baseUri = System::getServerProtocolHost();

        return $baseUri;
    }

    /**
     * Get server information.
     * @return object
     */
    private function getServerInformation()
    {
        $pathData = PATH_DATA . "sites" . PATH_SEP . config("system.workspace") . PATH_SEP . ".server_info";
        $content = file_get_contents($pathData);
        $serverInfo = unserialize($content);

        return $serverInfo;
    }

    /**
     * Test default languages
     *
     * @category HOR-3209:1
     * @covers ProcessMaker\BusinessModel\Language::getLanguageList
     */
    public function testGetLanguageList()
    {
        $list = $this->object->getLanguageList();
        $expected = [
            'LANG_ID' => 'en',
            'LANG_NAME' => 'English',
        ];
        $this->assertContains($expected, $list);
    }

    /**
     * Test installed languages
     *
     * @category HOR-3209:2
     * @covers ProcessMaker\BusinessModel\Language::getLanguageList
     */
    public function testGetLanguageListInstalled()
    {
        $this->installLanguage('es', __DIR__ . '/processmaker.es.po');
        $list = $this->object->getLanguageList();
        $english = [
            'LANG_ID' => 'en',
            'LANG_NAME' => 'English',
        ];
        $this->assertContains($english, $list);

        $spanish = [
            'LANG_ID' => 'es-ES',
            'LANG_NAME' => 'Spanish (Spain)',
        ];
        $this->assertContains($spanish, $list);

        $this->uninstallLanguage('es', __DIR__ . '/processmaker.es.po');
        $list2 = $this->object->getLanguageList();
        $this->assertContains($english, $list2);
    }

    /**
     * Install a language to the system.
     *
     * @param type $lanId
     * @param type $filename
     */
    private function installLanguage($lanId, $filename)
    {
        copy($filename, PATH_CORE . 'content/translations/' . basename($filename));
        $language = \LanguagePeer::retrieveByPK($lanId);
        $language->setLanEnabled(1);
        $language->save();
        file_exists($this->translationEnv) ? unlink($this->translationEnv) : false;
    }

    /**
     * Uninstall a language from the system.
     *
     * @param type $lanId
     * @param type $filename
     */
    private function uninstallLanguage($lanId, $filename)
    {
        unlink(PATH_CORE . 'content/translations/' . basename($filename));
        $language = \LanguagePeer::retrieveByPK($lanId);
        $language->setLanEnabled(0);
        $language->save();
        file_exists($this->translationEnv) ? unlink($this->translationEnv) : false;
    }
}
