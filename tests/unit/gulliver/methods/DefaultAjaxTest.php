<?php

namespace Tests\unit\gulliver\methods;

use Tests\TestCase;

class DefaultAjaxTest extends TestCase
{
    /**
     * Set up method.
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Issues with php 8');
    }

    /**
     * This gets data from a json file.
     * @param string $pathData
     * @return array
     */
    private function getDataFromFile(string $pathData): array
    {
        $pathData = PATH_TRUNK . "tests/resources/{$pathData}";
        $data = file_get_contents($pathData);
        $result = json_decode($data, JSON_OBJECT_AS_ARRAY);
        return $result;
    }

    /**
     * This should get the data for control suggest in classic process.
     * @test
     */
    public function this_should_get_the_data_for_control_suggest_in_classic_process()
    {
        $_POST = $this->getDataFromFile("simpleClassicPostData.json");
        $_SESSION = $this->getDataFromFile("simpleClassicSessionData.json");
        $_SESSION["CURRENT_PAGE_INITILIZATION"] = "";

        $pathName = PATH_XMLFORM . "2859218665d41d7c2920598058137861";
        $pathFileName = "{$pathName}/3411353005d41d9a730ede8060385476_tmp0.xml";
        if (!is_dir($pathName)) {
            mkdir($pathName);
        }
        $data = file_get_contents(PATH_TRUNK . "tests/resources/simpleClassicXmlFormData.xml");
        file_put_contents($pathFileName, $data);

        require_once PATH_TRUNK . 'gulliver/methods/defaultAjax.php';
        $this->expectOutputString('[]');

        unlink($pathFileName);
        rmdir($pathName);
    }
}
