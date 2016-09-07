<?php

namespace ProcessMaker\BusinessModel\Migrator;

use ProcessMaker\Util;

class PMXPublisher
{
    public function publish($filename, $data)
    {
        $parentDir = dirname($filename);

        if (!is_dir($parentDir)) {
            Util\Common::mk_dir($parentDir, 0775);
        }

        $outputFile = $this->truncateName($filename);

        file_put_contents($outputFile, $data);
        @chmod($outputFile, 0755);

        $currentLocale = setlocale(LC_CTYPE, 0);
        setlocale(LC_CTYPE, 'en_US.UTF-8');
        $filename = basename($outputFile);
        setlocale(LC_CTYPE, $currentLocale);

        return $filename;
    }

    /**
     * @param $outputFile
     * @param bool $dirName
     * @return mixed|string
     */
    public function truncateName($outputFile, $dirName = true)
    {
        $limit = 200;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $limit = 150;
        }
        if ($dirName) {
            $currentLocale = setlocale(LC_CTYPE, 0);
            setlocale(LC_CTYPE, 'en_US.UTF-8');
            $filename = basename($outputFile);
            if (strlen($filename) >= $limit) {
                $lastPos = strrpos($filename, '.');
                $fileName = substr($filename, 0, $lastPos);
                $newFileName = \G::inflect($fileName);
                $newFileName = $this->truncateFilename($newFileName, $limit);
                $newOutputFile = str_replace($fileName, $newFileName, $outputFile);
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $newOutputFile = str_replace("/", DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, $newOutputFile);
                }
                $outputFile = $newOutputFile;
            }
            setlocale(LC_CTYPE, $currentLocale);
        } else {
            $outputFile = \G::inflect($outputFile);
            $outputFile = $this->truncateFilename($outputFile, $limit);
        }
        return $outputFile;
    }

    /**
     * @param $outputFile
     * @param $limit
     * @return string
     */
    private function truncateFilename($outputFile, $limit)
    {
        $limitFile = $limit;
        if (mb_strlen($outputFile) != strlen($outputFile)) {
            if (strlen($outputFile) >= $limitFile) {
                do {
                    $newFileName = mb_strimwidth($outputFile, 0, $limit);
                    --$limit;
                } while (strlen($newFileName) > $limitFile);
                $outputFile = $newFileName;
            }
        } else {
            if (strlen($outputFile) >= $limitFile) {
                $excess = strlen($outputFile) - $limitFile;
                $newFileName = substr($outputFile, 0, strlen($outputFile) - $excess);
                $outputFile = $newFileName;
            }
        }
        return $outputFile;
    }
}