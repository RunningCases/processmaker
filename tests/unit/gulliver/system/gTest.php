<?php

namespace Tests\unit\gulliver\system;

use Faker\Factory;
use G;
use Tests\TestCase;

/**
 * @coversDefaultClass \G
 */
class gTest extends TestCase
{

    /**
     * Set up method.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
    }

    /**
     * It tests that the new words added to the array are present
     *
     * @test
     */
    public function it_should_match_reserved_new_words()
    {
        $res = G::reservedWordsSql();
        $newWords = [
            'GENERATED',
            'GET',
            'IO_AFTER_GTIDS',
            'IO_BEFORE_GTIDS',
            'MASTER_BIND',
            'OPTIMIZER_COSTS',
            'PARTITION',
            'PARSE_GCOL_EXPR',
            'SQL_AFTER_GTIDS',
            'SQL_BEFORE_GTIDS',
            'STORED',
            'VIRTUAL',
            '_FILENAME'
        ];
        foreach ($newWords as $word) {
            //This assert the array contains the new words added
            $this->assertContains($word, $res);
        }
    }

    /**
     * It tests that all the reserved words in MySQL 5.6 and MySQL 5.7 are present
     *
     * @test
     */
    public function it_should_match_all_reserved_words_in_mysql_57()
    {
        $res = G::reservedWordsSql();
        $words = [
            "ACCESSIBLE",
            "ADD",
            "ALL",
            "ALTER",
            "ANALYZE",
            "AND",
            "AS",
            "ASC",
            "ASENSITIVE",
            "AUTHORIZATION",
            "BEFORE",
            "BETWEEN",
            "BIGINT",
            "BINARY",
            "BLOB",
            "BOTH",
            "BREAK",
            "BROWSE",
            "BULK",
            "BY",
            "CALL",
            "CASCADE",
            "CASE",
            "CHANGE",
            "CHAR",
            "CHARACTER",
            "CHECK",
            "CHECKPOINT",
            "CLUSTERED",
            "COLLATE",
            "COLUMN",
            "COMPUTE",
            "CONDITION",
            "CONSTRAINT",
            "CONTAINSTABLE",
            "CONTINUE",
            "CONVERT",
            "CREATE",
            "CROSS",
            "CURRENT_DATE",
            "CURRENT_TIME",
            "CURRENT_TIMESTAMP",
            "CURRENT_USER",
            "CURSOR",
            "DATABASE",
            "DATABASES",
            "DAY_HOUR",
            "DAY_MICROSECOND",
            "DAY_MINUTE",
            "DAY_SECOND",
            "DBCC",
            "DEC",
            "DECIMAL",
            "DECLARE",
            "DEFAULT",
            "DELAYED",
            "DELETE",
            "DENY",
            "DESC",
            "DESCRIBE",
            "DETERMINISTIC",
            "DISTINCT",
            "DISTINCTROW",
            "DISTRIBUTED",
            "DIV",
            "DOUBLE",
            "DROP",
            "DUAL",
            "DUMMY",
            "DUMP",
            "EACH",
            "ELSE",
            "ELSEIF",
            "ENCLOSED",
            "ERRLVL",
            "ESCAPED",
            "EXCEPT",
            "EXEC",
            "EXISTS",
            "EXIT",
            "EXPLAIN",
            "FALSE",
            "FETCH",
            "FILLFACTOR",
            "FLOAT",
            "FLOAT4",
            "FLOAT8",
            "FOR",
            "FORCE",
            "FOREIGN",
            "FREETEXT",
            "FREETEXTTABLE",
            "FROM",
            "FULLTEXT",
            "GENERATED",
            "GET",
            "GOTO",
            "GRANT",
            "GROUP",
            "HAVING",
            "HIGH_PRIORITY",
            "HOLDLOCK",
            "HOUR_MICROSECOND",
            "HOUR_MINUTE",
            "HOUR_SECOND",
            "IDENTITY",
            "IDENTITYCOL",
            "IDENTITY_INSERT",
            "IF",
            "IGNORE",
            "IN",
            "INDEX",
            "INFILE",
            "INNER",
            "INOUT",
            "INSENSITIVE",
            "INSERT",
            "INT",
            "INT1",
            "INT2",
            "INT3",
            "INT4",
            "INT8",
            "INTEGER",
            "INTERSECT",
            "INTERVAL",
            "INTO",
            "IO_AFTER_GTIDS",
            "IO_BEFORE_GTIDS",
            "IS",
            "ITERATE",
            "JOIN",
            "KEY",
            "KEYS",
            "KILL",
            "LEADING",
            "LEAVE",
            "LEFT",
            "LIKE",
            "LIMIT",
            "LINEAR",
            "LINENO",
            "LINES",
            "LOAD",
            "LOCALTIME",
            "LOCALTIMESTAMP",
            "LOCK",
            "LONG",
            "LONGBLOB",
            "LONGTEXT",
            "LOOP",
            "LOW_PRIORITY",
            "MASTER_BIND",
            "MASTER_SSL_VERIFY_SERVER_CERT",
            "MATCH",
            "MAXVALUE",
            "MEDIUMBLOB",
            "MEDIUMINT",
            "MEDIUMTEXT",
            "MIDDLEINT",
            "MINUTE_MICROSECOND",
            "MINUTE_SECOND",
            "MOD",
            "MODIFIES",
            "NATURAL",
            "NOCHECK",
            "NONCLUSTERED",
            "NOT",
            "NO_WRITE_TO_BINLOG",
            "NULL",
            "NULLIF",
            "NUMERIC",
            "OF",
            "OFF",
            "OFFSETS",
            "ON",
            "OPENDATASOURCE",
            "OPENQUERY",
            "OPENROWSET",
            "OPENXML",
            "OPTIMIZE",
            "OPTIMIZER_COSTS",
            "OPTION",
            "OPTIONALLY",
            "OR",
            "ORDER",
            "OUT",
            "OUTER",
            "OUTFILE",
            "OVER",
            "PARTITION",
            "PARSE_GCOL_EXPR",
            "PERCENT",
            "PLAN",
            "PRECISION",
            "PRIMARY",
            "PRINT",
            "PROC",
            "PROCEDURE",
            "PUBLIC",
            "PURGE",
            "RAISERROR",
            "RANGE",
            "READ",
            "READS",
            "READTEXT",
            "READ_WRITE",
            "REAL",
            "RECONFIGURE",
            "REFERENCES",
            "REGEXP",
            "RELEASE",
            "RENAME",
            "REPEAT",
            "REPLACE",
            "REQUIRE",
            "RESIGNAL",
            "RESTRICT",
            "RETURN",
            "REVOKE",
            "RIGHT",
            "RLIKE",
            "ROWCOUNT",
            "ROWGUIDCOL",
            "RULE",
            "SAVE",
            "SCHEMA",
            "SCHEMAS",
            "SECOND_MICROSECOND",
            "SELECT",
            "SENSITIVE",
            "SEPARATOR",
            "SESSION_USER",
            "SET",
            "SETUSER",
            "SHOW",
            "SIGNAL",
            "SMALLINT",
            "SPATIAL",
            "SPECIFIC",
            "SQL",
            "SQLEXCEPTION",
            "SQLSTATE",
            "SQLWARNING",
            "SQL_AFTER_GTIDS",
            "SQL_BEFORE_GTIDS",
            "SQL_BIG_RESULT",
            "SQL_CALC_FOUND_ROWS",
            "SQL_SMALL_RESULT",
            "SSL",
            "STARTING",
            "STATISTICS",
            "STORED",
            "STRAIGHT_JOIN",
            "SYSTEM_USER",
            "TABLE",
            "TERMINATED",
            "TEXTSIZE",
            "THEN",
            "TINYBLOB",
            "TINYINT",
            "TINYTEXT",
            "TO",
            "TOP",
            "TRAILING",
            "TRAN",
            "TRIGGER",
            "TRUE",
            "TSEQUAL",
            "UNDO",
            "UNION",
            "UNIQUE",
            "UNLOCK",
            "UNSIGNED",
            "UPDATE",
            "UPDATETEXT",
            "USAGE",
            "USE",
            "USING",
            "UTC_DATE",
            "UTC_TIME",
            "UTC_TIMESTAMP",
            "VALUES",
            "VARBINARY",
            "VARCHAR",
            "VARCHARACTER",
            "VARYING",
            "VIRTUAL",
            "WAITFOR",
            "WHEN",
            "WHERE",
            "WHILE",
            "WITH",
            "WRITE",
            "WRITETEXT",
            "XOR",
            "YEAR_MONTH",
            "ZEROFILL",
            "_FILENAME"
        ];
        foreach ($words as $word) {
            //This assert the array contains all the reserved words in MySQL 5.6 and MySQL 5.7
            $this->assertContains($word, $res);
        }
    }

    /**
     * It tests if the errors related to the trigger execution was registered
     *
     * @covers ::logTriggerExecution
     * @test
     */
    public function it_check_log_trigger_execution()
    {
        $data = [];
        $error = 'This is some error';
        $_SESSION['_DATA_TRIGGER_']['_TRI_LOG_'] = false;
        G::logTriggerExecution($data, $error, 'FATAL_ERROR', 60);
        $this->assertTrue($_SESSION['_DATA_TRIGGER_']['_TRI_LOG_']);

        $_SESSION['_DATA_TRIGGER_']['_TRI_LOG_'] = false;
        G::logTriggerExecution($data, '', '', 100);
        $this->assertFalse($_SESSION['_DATA_TRIGGER_']['_TRI_LOG_']);
    }

    /**
     * This test the realEscapeString method.
     * @test
     * @covers G::realEscapeString()
     */
    public function it_should_test_realEscapeString_method()
    {
        $string = $this->faker->word;
        $result = G::realEscapeString($string);

        $this->assertNotEmpty($result);
    }
}
