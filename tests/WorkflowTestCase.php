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
}
