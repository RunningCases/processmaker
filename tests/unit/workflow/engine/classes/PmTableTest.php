<?php

use Tests\TestCase;

class PmTablesTest extends TestCase
{
    /**
     * Check if the "removePmtPropelFolder" is working correctly
     *
     * @covers PmTable::removePmtPropelFolder()
     *
     * @test
     */
    public function it_should_check_remove_pmt_propel_folder()
    {
        // Define the folder path
        $pmtPropelFolderPath = PATH_DB . config('system.workspace') . PATH_SEP . 'pmt-propel';

        // Create the folder
        G::mk_dir($pmtPropelFolderPath);

        // Remove the "pmt-propel" folder
        PmTable::removePmtPropelFolder();

        // Assert that the folder was deleted correctly
        $this->assertFalse(is_dir($pmtPropelFolderPath));
    }
}
