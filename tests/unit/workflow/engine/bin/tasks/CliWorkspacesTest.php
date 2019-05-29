<?php

namespace Tests\unit\workflow\engine\bin\tasks;

use Tests\TestCase;

class CliWorkspacesTest extends TestCase
{
    /**
     * Test that the deprecated files are removed successfully
     *
     * @covers WorkspaceTools::removeDeprecatedFiles
     * @test
     */
    public function it_should_delete_the_deprecated_files()
    {
        include(PATH_TRUNK . PATH_SEP . 'workflow/engine/bin/tasks/cliWorkspaces.php');
        if (!file_exists(PATH_TRUNK . PATH_SEP . 'workflow/engine/methods/users/data_usersList.php')) {
            $filename = PATH_TRUNK . PATH_SEP . 'workflow/engine/methods/users/data_usersList.php';
            $handle = fopen($filename, 'w');
            fclose($handle);
        }

        // This assert the data_usersList.php file do exists before being deleted
        $this->assertTrue(file_exists(PATH_TRUNK . PATH_SEP . 'workflow/engine/methods/users/data_usersList.php'));

        $path = PATH_TRUNK . PATH_SEP . 'workflow/engine/methods/users/';

        if (getmyuid() == fileowner($path)) {
            if (substr($this->getPermissions(PATH_TRUNK . PATH_SEP . 'workflow/engine/methods/users/data_usersList.php'),
                    1, 2) == 'rw' &&
                substr($this->getPermissions(PATH_TRUNK . PATH_SEP . 'workflow/engine/methods/users/'), 2, 1) == 'w' &&
                substr($this->getPermissions(PATH_TRUNK . PATH_SEP . 'workflow/engine/methods/'), 3, 1) == 'x' &&
                substr($this->getPermissions(PATH_TRUNK . PATH_SEP . 'workflow/engine/'), 3, 1) == 'x' &&
                substr($this->getPermissions(PATH_TRUNK . PATH_SEP . 'workflow/'), 3, 1) == 'x'
            ) {
                remove_deprecated_files();
            } else {
                dd("Could not delete the file. Please, make sure the file have write permission for the direct parent directory and 
                execute permission for all parent directories.");
            }
        } else {
            if (getmygid() == filegroup($path)) {
                if (substr($this->getPermissions(PATH_TRUNK . PATH_SEP . 'workflow/engine/methods/users/data_usersList.php'),
                        4, 2) == 'rw' &&
                    substr($this->getPermissions(PATH_TRUNK . PATH_SEP . 'workflow/engine/methods/users/'), 5,
                        1) == 'w' &&
                    substr($this->getPermissions(PATH_TRUNK . PATH_SEP . 'workflow/engine/methods/'), 6, 1) == 'x' &&
                    substr($this->getPermissions(PATH_TRUNK . PATH_SEP . 'workflow/engine/'), 6, 1) == 'x' &&
                    substr($this->getPermissions(PATH_TRUNK . PATH_SEP . 'workflow/'), 6, 1) == 'x'
                ) {
                    remove_deprecated_files();
                } else {
                    dd("Could not delete the file. Please, make sure the file have write permission for the direct parent directory and 
                execute permission for all parent directories.");
                }

            } else {
                if (substr($this->getPermissions(PATH_TRUNK . PATH_SEP . 'workflow/engine/methods/users/data_usersList.php'),
                        7, 2) == 'rw' &&
                    substr($this->getPermissions(PATH_TRUNK . PATH_SEP . 'workflow/engine/methods/users/'), 8,
                        1) == 'w' &&
                    substr($this->getPermissions(PATH_TRUNK . PATH_SEP . 'workflow/engine/methods/'), 9, 1) == 'x' &&
                    substr($this->getPermissions(PATH_TRUNK . PATH_SEP . 'workflow/engine/'), 9, 1) == 'x' &&
                    substr($this->getPermissions(PATH_TRUNK . PATH_SEP . 'workflow/'), 9, 1) == 'x'
                ) {
                    remove_deprecated_files();
                } else {
                    dd("Could not delete the file. Please, make sure the file have write permission for the direct parent directory and 
                execute permission for all parent directories.");
                }
            }
        }

        // This assert the data_usersList.php does not exist anymore
        $this->assertFalse(file_exists(PATH_TRUNK . PATH_SEP . 'workflow/engine/methods/users/data_usersList.php'));
    }

    /**
     * Get the permissions of a file or directory
     *
     * @param string $path
     * @return string
     */
    public function getPermissions($path)
    {
        $per = fileperms($path);
        switch ($per & 0xF000) {
            case 0xC000: // socket
                $permissions = 's';
                break;
            case 0xA000: // symbolic link
                $permissions = 'l';
                break;
            case 0x8000: // regular
                $permissions = '-';
                break;
            case 0x6000: // block special
                $permissions = 'b';
                break;
            case 0x4000: // directory
                $permissions = 'd';
                break;
            case 0x2000: // character special
                $permissions = 'c';
                break;
            case 0x1000: // FIFO pipe
                $permissions = 'p';
                break;
            default: // unknown
                $permissions = 'u';
        }

        // Owner
        $permissions .= (($per & 0x0100) ? 'r' : '-');
        $permissions .= (($per & 0x0080) ? 'w' : '-');
        $permissions .= (($per & 0x0040) ?
            (($per & 0x0800) ? 's' : 'x') :
            (($per & 0x0800) ? 'S' : '-'));

        // Group
        $permissions .= (($per & 0x0020) ? 'r' : '-');
        $permissions .= (($per & 0x0010) ? 'w' : '-');
        $permissions .= (($per & 0x0008) ?
            (($per & 0x0400) ? 's' : 'x') :
            (($per & 0x0400) ? 'S' : '-'));

        // Others
        $permissions .= (($per & 0x0004) ? 'r' : '-');
        $permissions .= (($per & 0x0002) ? 'w' : '-');
        $permissions .= (($per & 0x0001) ?
            (($per & 0x0200) ? 't' : 'x') :
            (($per & 0x0200) ? 'T' : '-'));

        return $permissions;
    }
}