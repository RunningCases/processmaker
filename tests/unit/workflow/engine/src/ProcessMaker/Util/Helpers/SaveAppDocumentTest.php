<?php

namespace Tests\unit\workflow\src\ProcessMaker\Util\Helpers;

use G;
use Tests\TestCase;

class SaveAppDocumentTest extends TestCase
{
    /**
     * It test if the file reference was uploaded
     *
     * @test
     */
    public function it_should_copy_file_same_name()
    {
        $files = [
            'name' => PATH_TRUNK . 'tests' . PATH_SEP . 'resources' . PATH_SEP . 'images' . PATH_SEP . 'activate.png',
            'tmp_name' => PATH_TRUNK . 'tests' . PATH_SEP . 'resources' . PATH_SEP . 'images' . PATH_SEP . 'activate.png',
        ];
        $appUid  = G::generateUniqueID();
        $appDocUid  = G::generateUniqueID();
        $pathCase = PATH_DB . config('system.workspace') . PATH_SEP . 'files' . PATH_SEP . G::getPathFromUID($appUid) . PATH_SEP;
        saveAppDocument($files, $appUid, $appDocUid, 1, false);
        $this->assertFileExists($pathCase . $appDocUid . '_1.png');
        G::rm_dir($pathCase);
    }

    /**
     * It test if the file reference was uploaded
     *
     * @test
     */
    public function it_should_copy_file_different_name()
    {
        $files = [
            'name' => 'activityRename.gif',
            'tmp_name' => PATH_TRUNK . 'tests' . PATH_SEP . 'resources' . PATH_SEP . 'images' . PATH_SEP . 'activity.gif',
        ];
        $appUid  = G::generateUniqueID();
        $appDocUid  = G::generateUniqueID();
        $pathCase = PATH_DB . config('system.workspace') . PATH_SEP . 'files' . PATH_SEP . G::getPathFromUID($appUid) . PATH_SEP;
        saveAppDocument($files, $appUid, $appDocUid, 1, false);
        $this->assertFileExists($pathCase . $appDocUid . '_1.gif');
        G::rm_dir($pathCase);
    }
}