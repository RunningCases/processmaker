<?php

namespace ProcessMaker\BusinessModel\Migrator;

use ProcessMaker\Util;

class PMXPublisher
{
    public function publish($filename, $data)
    {
        $parentDir = dirname($filename);

        if (! is_dir($parentDir)) {
            Util\Common::mk_dir($parentDir, 0775);
        }

        $outputFile = $this->truncateName($filename);

        file_put_contents($outputFile, $data);
        @chmod($outputFile, 0755);

        return basename($outputFile);
    }

    public function truncateName($outputFile, $dirName = true)
    {
        $limit = 200;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $limit = 150;
        }
        if ($dirName) {
            if (strlen(basename($outputFile)) >= $limit) {
                $lastPos = strrpos(basename($outputFile), '.');
                $fileName = substr(basename($outputFile), 0, $lastPos);
                $newFileName = str_replace(".", "_", $fileName);
                $newFileName = str_replace(" ", "_", $fileName);
                $excess = strlen($newFileName) - $limit;
                $newFileName = substr($newFileName, 0, strlen($newFileName) - $excess);
                $newOutputFile = str_replace($fileName, $newFileName, $outputFile);
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $newOutputFile = str_replace("/", DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, $newOutputFile);
                }
                $outputFile = $newOutputFile;
            }
        } else {
            $outputFile = str_replace(".", "_", $outputFile);
            $outputFile = str_replace(" ", "_", $outputFile);
            if (strlen($outputFile) >= $limit) {
                $excess = strlen($outputFile) - $limit;
                $newFileName = substr($outputFile, 0, strlen($outputFile) - $excess);
                $outputFile = $newFileName;
            }
        }
        return $outputFile;
    }
}