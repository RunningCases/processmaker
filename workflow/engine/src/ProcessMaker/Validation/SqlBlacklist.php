<?php

namespace ProcessMaker\Validation;

use Exception;
use G;
use PhpMyAdmin\SqlParser\Parser;

class SqlBlacklist extends Parser
{

    /**
     * Define the statements to block, this is case sensitive.
     * @var array
     */
    private $statementsToBeBlocked = [
        'SELECT',
        'EXECUTE',
        'EXEC',
        'SHOW',
        'DESCRIBE',
        'EXPLAIN',
        'BEGIN',
        'INSERT',
        'UPDATE',
        'DELETE',
        'REPLACE'
    ];

    /**
     * Constructor of class.
     * @param string $list
     * @param boolean $strict
     */
    public function __construct($list = null, $strict = false)
    {
        parent::__construct($list, $strict);
    }

    /**
     * Get information about the statements permitted and tables that can be modified.
     * @return array
     */
    public function getConfigValues(): array
    {
        $tables = [];
        $statements = [];
        $pmtables = [];

        $path = PATH_CONFIG . 'system-tables.ini';
        if (file_exists($path)) {
            $values = @parse_ini_file($path);

            $string = isset($values['tables']) ? $values['tables'] : '';
            $tables = explode('|', $string);
            $tables = array_filter($tables, function ($v) {
                return !empty($v);
            });
        }

        $path = PATH_CONFIG . 'execute-query-blacklist.ini';
        if (file_exists($path)) {
            $values = @parse_ini_file($path);

            $string = isset($values['pmtables']) ? $values['pmtables'] : '';
            $pmtables = explode('|', $string);
            $pmtables = array_filter($pmtables, function ($v) {
                return !empty($v);
            });

            $string = isset($values['queries']) ? $values['queries'] : '';
            $string = strtoupper($string);
            $statements = explode('|', $string);
            //get only statements allowed for lock
            $statements = array_filter($statements, function ($v) {
                $toUpper = strtoupper($v);
                return !empty($v) && in_array($toUpper, $this->statementsToBeBlocked);
            });
        }

        return [
            'tables' => $tables,
            'statements' => $statements,
            'pmtables' => $pmtables
        ];
    }

    /**
     * Parse a sql string and check the blacklist, an exception is thrown if it contains a restricted item.
     * @return void
     * @throws Exception
     */
    public function validate(): void
    {
        $config = $this->getConfigValues();

        //verify statements
        foreach ($this->statements as $statement) {
            $signed = get_class($statement);
            foreach (Parser::$STATEMENT_PARSERS as $key => $value) {
                if ($signed === $value && in_array(strtoupper($key), $config['statements'])) {
                    throw new Exception(G::loadTranslation('ID_INVALID_QUERY'));
                }
            }
        }

        //verify tables
        //tokens are formed multidimensionally, it is necessary to recursively traverse the multidimensional object.
        $listTables = array_merge($config['tables'], $config['pmtables']);
        $fn = function ($object) use (&$fn, $listTables) {
            foreach ($object as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $fn($value);
                }
                if ($key === 'table' && is_string($value)) {
                    if (in_array($value, $listTables)) {
                        throw new Exception(G::loadTranslation('ID_NOT_EXECUTE_QUERY', [$value]));
                    }
                }
            }
        };
        $fn($this->statements);
    }
}
