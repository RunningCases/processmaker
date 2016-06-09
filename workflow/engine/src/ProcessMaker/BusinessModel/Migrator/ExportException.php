<?php

namespace ProcessMaker\BusinessModel\Migrator;


class ExportException extends \Exception
{
    protected $nameException;

    /**
     * @return mixed
     */
    public function getNameException()
    {
        return $this->nameException;
    }

    /**
     * @param mixed $nameException
     */
    public function setNameException($nameException)
    {
        $this->nameException = $nameException;
    }
}