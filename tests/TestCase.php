<?php

namespace Tests;

use App\Factories\Factory;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use mysqli;

abstract class TestCase extends BaseTestCase
{
    /**
     *
     * @var object 
     */
    protected $currentConfig;

    /**
     *
     * @var string 
     */
    protected $currentArgv;

    /**
     * The array of the initial tables to be dropped.
     * @var array
     */
    public static $truncateInitialTables = '';

    /**
     * Create application
     */
    use CreatesApplication;

    /**
     * Constructs a test case with the given name.
     *
     * @param string $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        /**
         * Method Tests\CreatesApplication::createApplication() restarts the application 
         * and the values loaded in bootstrap.php have been lost, for this reason 
         * it is necessary to save the following values.
         */
        $this->currentConfig = app('config');
        $this->currentArgv = $_SERVER['argv'];
        parent::__construct($name, $data, $dataName);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        /**
         * Lost argv are restored.
         */
        if (empty($_SERVER['argv'])) {
            $_SERVER['argv'] = $this->currentArgv;
        }
        parent::setUp();
        /**
         * Lost config are restored.
         */
        app()->instance('config', $this->currentConfig);
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * truncate non-initial Models.
     * @return void
     */
    public static function truncateNonInitialModels(): void
    {
        if (empty(static::$truncateInitialTables)) {
            $initialTables = [
                'RBAC_PERMISSIONS',
                'RBAC_ROLES',
                'RBAC_ROLES_PERMISSIONS',
                'RBAC_SYSTEMS',
                'RBAC_USERS',
                'RBAC_USERS_ROLES',
                'USERS',
                'CONTENT',
                'LANGUAGE',
                'ISO_COUNTRY',
                'ISO_SUBDIVISION',
                'ISO_LOCATION',
                'TRANSLATION',
                'DASHLET',
                'DASHLET_INSTANCE',
                'CONFIGURATION',
                'CATALOG',
                'ADDONS_MANAGER',
                'APP_SEQUENCE',
                'OAUTH_CLIENTS',
                'OAUTH_ACCESS_TOKENS'
            ];
            $directory = Factory::$customDirectoryForModels;
            if (file_exists($directory)) {
                $files = scandir($directory);
                $files = array_diff($files, ['.', '..']);
                $tables = [];
                foreach ($files as $filename) {
                    $filepath = $directory . $filename;
                    $ext = pathinfo($filepath, PATHINFO_EXTENSION);
                    if (strtolower($ext) !== 'php') {
                        continue;
                    }
                    $modelName = pathinfo($filepath, PATHINFO_FILENAME);
                    $model = Factory::$customNameSpaceForModels . $modelName;
                    $tableName = (new $model())->getTable();
                    $tables[] = $tableName;
                }
                $result = array_diff($tables, $initialTables);
                $result = array_values($result);
                $truncates = [];
                foreach ($result as $value) {
                    $truncates[] = 'TRUNCATE TABLE ' . $value;
                }
                static::$truncateInitialTables = implode(';', $truncates);
            }
        }
        $mysqli = new mysqli(env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE'));
        $mysqli->multi_query(
            "set global max_connections = 500;" .
            "SET FOREIGN_KEY_CHECKS = 0;" .
            static::$truncateInitialTables .
            ";SET FOREIGN_KEY_CHECKS = 1;"
        );
        // flush multi_queries
        while ($mysqli->next_result()) {;}
    }
}
