<?php

use ProcessMaker\Importer\XmlImporter;
use PHPUnit\Framework\TestCase;

/**
 * Test case that could instance a workspace DB
 *
 */
class WorkflowTestCase extends TestCase
{

    /**
     * Create and install the database.
     */
    protected function setupDB()
    {
        //Install Database
        $pdo0 = new PDO("mysql:host=".DB_HOST, DB_USER, DB_PASS);
        $pdo0->query('DROP DATABASE IF EXISTS '.DB_NAME);
        $pdo0->query('CREATE DATABASE '.DB_NAME);
        $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER,
                       DB_PASS);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);
        $pdo->exec(file_get_contents(PATH_CORE.'data/mysql/schema.sql'));
        $pdo->exec(file_get_contents(PATH_RBAC_CORE.'data/mysql/schema.sql'));
        $pdo->exec(file_get_contents(PATH_CORE.'data/mysql/insert.sql'));
        $pdo->exec(file_get_contents(PATH_RBAC_CORE.'data/mysql/insert.sql'));
        $pdo->exec("INSERT INTO `APP_SEQUENCE` (`ID`) VALUES ('1')");
        $pdo->exec("INSERT INTO `OAUTH_CLIENTS` (`CLIENT_ID`, `CLIENT_SECRET`, `CLIENT_NAME`, `CLIENT_DESCRIPTION`, `CLIENT_WEBSITE`, `REDIRECT_URI`, `USR_UID`) VALUES
('x-pm-local-client',	'179ad45c6ce2cb97cf1029e212046e81',	'PM Web Designer',	'ProcessMaker Web Designer App',	'www.processmaker.com',	'http://".$_SERVER["HTTP_HOST"].":".$_SERVER['SERVER_PORT']."/sys".config("sys_sys")."/en/neoclassic/oauth2/grant',	'00000000000000000000000000000001');");
        $pdo->exec("INSERT INTO `OAUTH_ACCESS_TOKENS` (`ACCESS_TOKEN`, `CLIENT_ID`, `USER_ID`, `EXPIRES`, `SCOPE`) VALUES
('39704d17049f5aef45e884e7b769989269502f83',	'x-pm-local-client',	'00000000000000000000000000000001',	'2017-06-15 17:55:19',	'view_processes edit_processes *');");
    }

    /**
     * Drop the database.
     */
    protected function dropDB()
    {
        //Install Database
        $pdo0 = new PDO("mysql:host=".DB_HOST, DB_USER, DB_PASS);
        $pdo0->query('DROP DATABASE IF EXISTS '.DB_NAME);
    }

    /**
     * Import a process to the database.
     *
     * @param type $filename ProcessMaker file to be imported
     * @return string PRO_UID
     */
    protected function import($filename, $regenerateUids = false)
    {
        $importer = new XmlImporter();
        $importer->setSourceFile($filename);
        return $importer->import(
                $regenerateUids ? XmlImporter::IMPORT_OPTION_KEEP_WITHOUT_CHANGING_AND_CREATE_NEW : XmlImporter::IMPORT_OPTION_CREATE_NEW,
                XmlImporter::GROUP_IMPORT_OPTION_CREATE_NEW, $regenerateUids
        );
    }

    /**
     * Rebuild workflow's schema.sql
     */
    protected function rebuildModel()
    {
        $pwd = getcwd();
        chdir(PATH_CORE);
        exec('../../gulliver/bin/gulliver propel-build-sql mysql');
        exec('../../gulliver/bin/gulliver propel-build-model');
        chdir($pwd);
    }

    /**
     * Clean the shared folder to only have the sites.
     */
    protected function cleanShared()
    {
        $this->rrmdir(PATH_DATA.'skins');
        mkdir(PATH_DATA.'skins');
        clearstatcache();
    }

    /**
     * Set the text of and specific translated message.
     *
     * @global array $translation
     * @param type $msgId
     * @param type $text
     */
    protected function setTranslation($msgId, $text)
    {
        global $translation;
        $translation[$msgId] = $text;
    }

    /**
     * Clear all the translated messages loaded.
     *
     * @global array $translation
     */
    protected function clearTranslations()
    {
        global $translation;
        foreach ($translation as $msgId => $text) {
            unset($translation[$msgId]);
        }
    }

    private function rrmdir($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file") && !is_link($dir)) ? $this->rrmdir("$dir/$file")
                        : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    /**
     * Set specific env.ini configuration.
     *
     * @param type $param
     * @param type $value
     */
    protected function setEnvIni($param, $value)
    {
        $config = file_get_contents(PATH_CONFIG.'env.ini');
        if (substr($config, -1, 1) !== "\n") {
            $config.="\n";
        }
        $regexp = '/^\s*'.preg_quote($param).'\s*=\s*.*\n$/m';
        if (preg_match($regexp, $config."\n")) {
            if ($value === null) {
                $config = preg_replace($regexp, "", $config);
            } else {
                $value1 = is_numeric($value) ? $value : json_encode($value, true);
                $config = preg_replace($regexp, "$param = $value1\n", $config);
            }
        } elseif ($value !== null) {
            $value1 = is_numeric($value) ? $value : json_encode($value, true);
            $config.="$param = $value1\n";
        }
        file_put_contents(PATH_CONFIG.'env.ini', $config);
    }

    /**
     * Unset specific env.ini configuration.
     *
     * @param type $param
     */
    protected function unsetEnvIni($param)
    {
        $this->setEnvIni($param, null);
    }

    /**
     * Installa an licese file.
     *
     * @param type $path
     * @throws \Exception
     */
    protected function installLicense($path)
    {
        $licenseFile = glob($path);
        if (!$licenseFile) {
            throw new \Exception('To continue please put a valid license at features/resources');
        }
        G::LoadClass('pmLicenseManager');
        $licenseManager = new PmLicenseManager();
        $licenseManager->installLicense($licenseFile[0]);
    }

    /**
     * Add a PM configuration.
     *
     * @return \Configurations
     */
    protected function config($config=[]){
        $configGetStarted = new \Configuration;
        $data = array_merge([
            'OBJ_UID' => '',
            'PRO_UID' => '',
            'USR_UID' => '',
            'APP_UID' => '',
        ], $config);
        $configGetStarted->create($data);
    }

    protected function getBaseUrl($url)
    {
        return (\G::is_https() ? "https://" : "http://").
            $GLOBALS["APP_HOST"].':'.$GLOBALS['SERVER_PORT']."/sys".config("sys_sys")."/".
            SYS_LANG."/".SYS_SKIN."/".$url;
    }
}
