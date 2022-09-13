<?php

namespace Tests\unit\workflow\engine\methods\cases;

use G;
use ProcessMaker\Model\Documents;
use RBAC;
use Tests\TestCase;

class CasesShowDocumentTest extends TestCase
{

    /**
     * Setup method.
     */
    public function setUp(): void
    {
        parent::setUp();
        if (!defined('PATH_DOCUMENT')) {
            define('PATH_DOCUMENT', PATH_DB . config('system.workspace') . PATH_SEP . 'files' . PATH_SEP);
        }
    }

    /**
     * This test verifies the download link of the uploaded file content.
     * @test
     */
    public function it_should_test_link_cases_show_document()
    {
        global $RBAC;
        $RBAC = RBAC::getSingleton();
        $RBAC->initRBAC();

        $appDocument = Documents::factory()->create([
            'APP_DOC_FILENAME' => 'text.txt'
        ]);

        $_GET['a'] = $appDocument->APP_DOC_UID;
        $_GET['v'] = '1';

        $path = G::getPathFromUID($appDocument->APP_UID);
        $file = G::getPathFromFileUID($appDocument->APP_UID, $appDocument->APP_DOC_UID);
        $realPath = PATH_DOCUMENT . $path . '/' . $file[0] . $file[1] . '_' . 1 . '.txt';
        $dirs = explode('/', $realPath);
        $filename = array_pop($dirs);
        $path = '';
        foreach ($dirs as $value) {
            if (empty($value)) {
                continue;
            }
            $path = $path . PATH_SEP . $value;
            if (!file_exists($path)) {
                mkdir($path);
            }
        }
        $expected = 'test';
        file_put_contents($realPath, $expected);
        $_SERVER['HTTP_USER_AGENT'] = '';

        //assert file content
        ob_start();
        $fileName = PATH_METHODS . 'cases/cases_ShowDocument.php';
        require_once $fileName;
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($expected, $content);
    }
}
