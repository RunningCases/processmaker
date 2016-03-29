<?php

namespace ProcessMaker\BusinessModel\Migrator;


class ImportException extends \Exception
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