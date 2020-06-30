<?php

namespace Tests\unit\workflow\engine\methods\services;

use Carbon\Carbon;
use G;
use Illuminate\Support\Facades\Cache;
use PmLicenseManager;
use Tests\TestCase;

class ActionsByEmailDataFormTest extends TestCase
{

    /**
     * Setup method.
     */
    public function setUp()
    {
        parent::setUp();
        if (!defined('URL_KEY')) {
            define('URL_KEY', 'c0l0s40pt1mu59r1m3');
        }
        $path = PATH_TRUNK . 'shared' . PATH_SEP . 'compiled';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $path = $path . PATH_SEP . 'smarty';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $path = $path . PATH_SEP . 'c';
        if (!file_exists($path)) {
            mkdir($path);
        }
        if (!defined('PATH_GULLIVER_HOME')) {
            define("PATH_GULLIVER_HOME", PATH_TRUNK . "gulliver" . PATH_SEP);
        }
        if (!defined('PATH_TEMPLATE')) {
            define("PATH_TEMPLATE", PATH_GULLIVER_HOME . "templates" . PATH_SEP);
        }
    }

    /**
     * This test verify the form Action By Email build.
     * @test
     */
    public function it_should_test_view_action_by_email_with_time_zone()
    {
        $process = factory(\ProcessMaker\Model\Process::class)->create();

        $pathData = PATH_TRUNK . "tests/resources/dynaform1.json";
        $content = file_get_contents($pathData);

        $dynaform = factory(\ProcessMaker\Model\Dynaform::class)->create([
            'PRO_UID' => $process->PRO_UID,
            'DYN_CONTENT' => $content
        ]);
        $delegation = factory(\ProcessMaker\Model\Delegation::class)->state('closed')->create([
            'PRO_UID' => $process->PRO_UID
        ]);

        global $RBAC;
        $_GET["APP_UID"] = G::encrypt($delegation->APP_UID, URL_KEY);
        $_GET["DEL_INDEX"] = G::encrypt($delegation->DEL_INDEX, URL_KEY);
        $_GET["DYN_UID"] = G::encrypt($dynaform->DYN_UID, URL_KEY);
        $_GET["ABER"] = G::encrypt($delegation->APP_UID, URL_KEY);
        $_GET["BROWSER_TIME_ZONE_OFFSET"] = "-14400";
        $_REQUEST = $_GET;
        $cached = [
            'zLhSk5TeEQrNFI2RXFEVktyUGpnczV1WEJNWVp6cjYxbTU3R29mVXVZNWhZQT0=' => true
        ];
        Cache::put(PmLicenseManager::CACHE_KEY . '.' . config("system.workspace"), $cached, Carbon::now()->addDay(1));

        ob_start();
        $fileName = PATH_METHODS . 'services/ActionsByEmailDataForm.php';
        require_once $fileName;
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertNotEmpty($content);
        $this->assertContains('ID_ABE_FORM_ALREADY_FILLED', $content);
    }
}
