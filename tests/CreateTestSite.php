<?php

namespace Tests;

use G;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ProcessMaker\Core\Installer;
use ProcessMaker\Core\System;

trait CreateTestSite
{
    private $timezone;
    private $baseUri;
    private $user;
    private $password;
    private $workspace;

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
        if (!file_exists($pathData) && method_exists($this, 'markTestSkipped')) {
            $this->markTestSkipped('Please define an active workspace.');
        }
        $content = file_get_contents($pathData);
        $serverInfo = unserialize($content);

        return $serverInfo;
    }

    /**
     * This method creates a test workspace so that the endpoints can be functional, 
     * it is necessary to change the permissions of the directory so that other 
     * users can access and write to the directory, these users can be for 
     * example: apache2, www-data, httpd, etc... 
     * This method finds the license file of the active site and uses it to register 
     * this license in the LICENSE_MANAGER table. If there is no license file in 
     * the active workspace, an asersion failure will be notified.
     */
    private function createTestSite()
    {
        //We copy the license, otherwise you will not be able to lift the site
        $pathTest = PATH_DATA . "sites" . PATH_SEP . $this->workspace;
        File::copyDirectory(PATH_DATA . "sites" . PATH_SEP . config("system.workspace"), $pathTest);

        //Write permission for other users for example: apache2, www-data, httpd.
        passthru('chmod 775 -R ' . $pathTest . ' >> .log 2>&1');

        $installer = new Installer();
        $options = [
            'isset' => true,
            'name' => $this->workspace,
            'admin' => [
                'username' => $this->user,
                'password' => $this->password
            ],
            'advanced' => [
                'ao_db_drop' => true,
                'ao_db_wf' => $this->workspace,
                'ao_db_rb' => $this->workspace,
                'ao_db_rp' => $this->workspace
            ]
        ];
        //The false option creates a connection to the database, necessary to create a site.
        $installer->create_site($options, false);
        //Now create site
        $installer->create_site($options, true);

        //Important so that the dates are stored in the same timezone
        file_put_contents($pathTest . "/env.ini", "time_zone ='{$this->timezone}'", FILE_APPEND);

        $matchingFiles = File::glob("{$pathTest}/*.dat");
        $this->assertNotEmpty($matchingFiles);

        //set license
        $licensePath = array_pop($matchingFiles);
        DB::Table("LICENSE_MANAGER")->insert([
            "LICENSE_UID" => G::generateUniqueID(),
            "LICENSE_USER" => "ProcessMaker Inc",
            "LICENSE_START" => "1490932800",
            "LICENSE_END" => 0,
            "LICENSE_SPAN" => 0,
            "LICENSE_STATUS" => "ACTIVE",
            "LICENSE_DATA" => file_get_contents($licensePath),
            "LICENSE_PATH" => $licensePath,
            "LICENSE_WORKSPACE" => $this->workspace,
            "LICENSE_TYPE" => ""
        ]);
    }
}
