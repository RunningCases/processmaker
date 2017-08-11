<?php
/*--------------------------------------------------
 * TAR/GZIP/BZIP2/ZIP ARCHIVE CLASSES 2.1
 * By Devin Doucette
 * Copyright (c) 2005 Devin Doucette
 * Email: darksnoopy@shaw.ca
 *--------------------------------------------------
 * Email bugs/suggestions to darksnoopy@shaw.ca
 *--------------------------------------------------
 * This script has been created and released under
 * the GNU GPL and is free to use and redistribute
 * only if this copyright statement is not removed
 *--------------------------------------------------*/

/**
 *
 * @package workflow.engine.classes
 */

/**
 *
 *
 * This class is derived from the class archive, is employed to use files .bzip
 *
 * @package workflow.engine.classes
 *
 */class bzip_file extends tar_file
{

    /**
     * This function is the constructor of the class bzip_file
     *
     * @param string $name
     * @return void
     */
    public function bzip_file ($name)
    {
        $this->tar_file( $name );
        $this->options['type'] = "bzip";
    }

    /**
     * This function is employed to create files .
     * bzip
     *
     * @return boolean
     */
    public function create_bzip ()
    {
        if ($this->options['inmemory'] == 0) {
            $pwd = getcwd();
            chdir( $this->options['basedir'] );
            if ($fp = bzopen( $this->options['name'], "wb" )) {
                fseek( $this->archive, 0 );
                while ($temp = fread( $this->archive, 1048576 )) {
                    bzwrite( $fp, $temp );
                }
                bzclose( $fp );
                chdir( $pwd );
            } else {
                $this->error[] = "Could not open {$this->options['name']} for writing.";
                chdir( $pwd );
                return 0;
            }
        } else {
            $this->archive = bzcompress( $this->archive, $this->options['level'] );
        }
        return 1;
    }

    /**
     * This function open a archive of the class bzip_file
     *
     * @return void
     */
    public function open_archive ()
    {
        return @bzopen( $this->options['name'], "rb" );
    }
}
