<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Services\Api;

use G;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ProcessMaker\Core\Installer;
use ProcessMaker\Core\System;
use ProcessMaker\Model\ListUnassigned;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\TaskUser;
use ProcessMaker\Model\User;
use ProcessMaker\Util\DateTime;
use Tests\TestCase;

class LightTest extends TestCase
{
    private $http;
    private $baseUri;
    private $workspace;
    private $clientId;
    private $clientSecret;
    private $user;
    private $password;
    private $authorization;
    private $optionsForConvertDatetime;
    private $timezone;

    /**
     * This is using instead of DatabaseTransactions
     * @todo DatabaseTransactions is having conflicts with propel
     */
    protected function setUp()
    {
        $this->timezone = config('app.timezone');
        $_SESSION['USR_TIME_ZONE'] = $this->timezone;
        $this->baseUri = $this->getBaseUri();
        $this->workspace = env("DB_DATABASE", "test");
        $this->clientId = config("oauthClients.pm.clientId");
        $this->clientSecret = config("oauthClients.pm.clientSecret");
        $this->user = "admin";
        $this->password = "admin";
        $this->createTestSite();
        $this->http = new Client([
            "base_uri" => $this->baseUri
        ]);
        $this->optionsForConvertDatetime = [
            'newerThan',
            'oldestthan',
            'date',
            'delegateDate',
            'dueDate',
            'delRiskDate'
        ];
    }

    /**
     * Get base uri for rest applications.
     * @return string
     */
    private function getBaseUri()
    {
        $_SERVER = $this->getServerInformation();
        $baseUri = System::getServerProtocolHost();

        return $baseUri;
    }

    /**
     * Get server information.
     * @return object
     */
    private function getServerInformation()
    {
        $pathData = PATH_DATA . "sites" . PATH_SEP . config("system.workspace") . PATH_SEP . ".server_info";
        $content = file_get_contents($pathData);
        $serverInfo = unserialize($content);

        return $serverInfo;
    }

    /**
     * This method creates a test workspace so that the endpoints can be functional, 
     * it is necessary to change the permissions of the directory so that other 
     * users can access and write to the directory, these users can be for 
     * example: apache2, www-data, httpd, etc... 
     * This method finds the license file of the active site and uses it to register 
     * this license in the LICENSE_MANAGER table. If there is no license file in 
     * the active workspace, an asersion failure will be notified.
     */
    private function createTestSite()
    {
        //We copy the license, otherwise you will not be able to lift the site
        $pathTest = PATH_DATA . "sites" . PATH_SEP . $this->workspace;
        File::copyDirectory(PATH_DATA . "sites" . PATH_SEP . config("system.workspace"), $pathTest);

        //Write permission for other users for example: apache2, www-data, httpd.
        passthru('chmod 777 -R ' . $pathTest . ' >> .log 2>&1');

        $installer = new Installer();
        $options = [
            'isset' => true,
            'name' => $this->workspace,
            'admin' => [
                'username' => $this->user,
                'password' => $this->password
            ],
            'advanced' => [
                'ao_db_drop' => true,
                'ao_db_wf' => $this->workspace,
                'ao_db_rb' => $this->workspace,
                'ao_db_rp' => $this->workspace
            ]
        ];
        //The false option creates a connection to the database, necessary to create a site.
        $installer->create_site($options, false);
        //Now create site
        $installer->create_site($options, true);

        //Important so that the dates are stored in the same timezone
        file_put_contents($pathTest . "/env.ini", "time_zone ='{$this->timezone}'", FILE_APPEND);

        $matchingFiles = File::glob("{$pathTest}/*.dat");
        $this->assertNotEmpty($matchingFiles);

        //set license
        $licensePath = array_pop($matchingFiles);
        DB::Table("LICENSE_MANAGER")->insert([
            "LICENSE_UID" => G::generateUniqueID(),
            "LICENSE_USER" => "ProcessMaker Inc",
            "LICENSE_START" => "1490932800",
            "LICENSE_END" => 0,
            "LICENSE_SPAN" => 0,
            "LICENSE_STATUS" => "ACTIVE",
            "LICENSE_DATA" => file_get_contents($licensePath),
            "LICENSE_PATH" => $licensePath,
            "LICENSE_WORKSPACE" => $this->workspace,
            "LICENSE_TYPE" => ""
        ]);
    }

    /**
     * Get authorization values.
     */
    private function getAuthorization()
    {
        $request = $this->http->request("POST", "{$this->workspace}/oauth2/token", [
            "form_params" => [
                "grant_type" => "password",
                "scope" => "*",
                "client_id" => $this->clientId,
                "client_secret" => $this->clientSecret,
                "username" => $this->user,
                "password" => $this->password
            ]
        ]);

        //Here is to verify if the connection to the endpoint was satisfactory, 
        //so the connection status should be 200.
        $statusCode = $request->getStatusCode();
        $this->assertEquals(200, $statusCode);

        //If the endpoint has responded we can obtain the data and verify if it 
        //is what we expected.
        $contents = $request->getBody()->getContents();
        $credentials = json_decode($contents);

        $this->assertNotNull($credentials);
        $this->assertObjectHasAttribute('access_token', $credentials);
        $this->assertObjectHasAttribute('expires_in', $credentials);
        $this->assertObjectHasAttribute('refresh_token', $credentials);
        $this->assertObjectHasAttribute('scope', $credentials);
        $this->assertObjectHasAttribute('token_type', $credentials);

        $this->authorization = ucwords($credentials->token_type) . " {$credentials->access_token}";
    }

    /**
     * Get current collection list unassigned.
     * @return collection
     */
    private function getCollectionListUnassigned()
    {
        //Create process
        $process = factory(Process::class)->create();

        //Get user
        $user = User::select()
                ->where('USR_USERNAME', '=', $this->user)
                ->first();

        //Create a task self service
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);

        //Assign a user in the task
        factory(TaskUser::class, 1)->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);

        //Create a record in list unassigned
        $listUnassigned = factory(ListUnassigned::class, 15)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_PREVIOUS_USR_UID' => $user->USR_UID
        ]);

        $result = $listUnassigned->sortByDesc('DEL_DELEGATE_DATE');

        return $result;
    }

    /**
     * Changes the data to the format returned by REST API.
     * @param array $collection
     * @return array
     */
    private function normalizeData($collection)
    {
        $result = [];
        $collection->transform(function ($item, $key) use (&$result) {
            $value = [
                'caseId' => $item->APP_UID,
                //The current EndPoint returns this value as a string, an Eloquent 
                //collection takes into account the string and numeric types.
                'delIndex' => (string) $item->DEL_INDEX,
                'task' => [
                    'taskId' => $item->TAS_UID,
                    'name' => $item->APP_TAS_TITLE
                ],
                'process' => [
                    'processId' => $item->PRO_UID,
                    'name' => $item->APP_PRO_TITLE
                ],
                //The current EndPoint returns this value as a string, an Eloquent 
                //collection takes into account the string and numeric types.
                'caseNumber' => (string) $item->APP_NUMBER,
                'caseTitle' => $item->APP_TITLE,
                'date' => $item->APP_UPDATE_DATE->format('Y-m-d H:i:s'),
                'delegateDate' => $item->DEL_DELEGATE_DATE->format('Y-m-d H:i:s'),
                'prevUser' => [
                    'userId' => $item->DEL_PREVIOUS_USR_UID,
                    'userName' => $item->DEL_PREVIOUS_USR_USERNAME,
                    'firstName' => $item->DEL_PREVIOUS_USR_FIRSTNAME,
                    'lastName' => $item->DEL_PREVIOUS_USR_LASTNAME
                ],
                'dueDate' => $item->DEL_DUE_DATE->format('Y-m-d H:i:s'),
            ];
            $result[] = $value;
        });

        $converted = DateTime::convertUtcToIso8601($result, $this->optionsForConvertDatetime);

        //Convert the elements to an object
        $object = json_decode(json_encode($converted));

        return $object;
    }

    /**
     * This returns an array of arrays to test the $start and $limit parameters.
     * The values correspond to the following structure:
     * [
     *     [$page, $size, $start, $limit],
     *     [$page, $size, $start, $limit],
     *     [$page, $size, $start, $limit],
     * ]
     * $page and $size are necessary to test the pages we expect to have from the 
     * model collection.
     * @return array
     */
    public function pagesProvider()
    {
        return [
            [1, 5, 0, 5],
            [2, 5, 5, 5],
            [3, 5, 10, 5],
            [4, 5, 15, 5],
            [5, 5, 20, 5],
            [6, 5, 25, 5]
        ];
    }

    /**
     * This check if the endpoint {workspace}/light/unassigned, is returning all data.
     * @test
     * @covers ProcessMaker\Services\Api\Light::doGetCasesListUnassigned
     */
    public function it_should_get_all_data_without_start_and_limit_values()
    {
        $listUnassigned = $this->getCollectionListUnassigned();

        $this->getAuthorization();
        $request = $this->http->request("GET", "api/1.0/{$this->workspace}/light/unassigned", [
            "headers" => [
                "Authorization" => $this->authorization
            ]
        ]);

        //Here is to verify if the connection to the endpoint was satisfactory, 
        //so the connection status should be 200.
        $statusCode = $request->getStatusCode();
        $this->assertEquals(200, $statusCode);

        //If the endpoint has responded we can obtain the data and verify if it 
        //is what we expected.
        $expected = $this->normalizeData($listUnassigned);
        $contents = $request->getBody()->getContents();
        $content = json_decode($contents);

        $this->assertEquals($expected, $content);
    }

    /**
     * This check if the endpoint {workspace}/light/unassigned, is returning the 
     * requested data set according to the start and limit parameters. The $start 
     * and $limit test values are obtained from the data provider pagesProvider().
     * @test
     * @covers ProcessMaker\Services\Api\Light::doGetCasesListUnassigned
     * @dataProvider pagesProvider
     */
    public function it_should_get_data_with_start_and_limit($page, $size, $start, $limit)
    {
        $listUnassigned = $this->getCollectionListUnassigned();
        $result = $listUnassigned->forPage($page, $size);

        $this->getAuthorization();
        $request = $this->http->request("GET", "api/1.0/{$this->workspace}/light/unassigned?start={$start}&limit={$limit}", [
            "headers" => [
                "Authorization" => $this->authorization
            ]
        ]);

        //Here is to verify if the connection to the endpoint was satisfactory, 
        //so the connection status should be 200.
        $statusCode = $request->getStatusCode();
        $this->assertEquals(200, $statusCode);

        //If the endpoint has responded we can obtain the data and verify if it 
        //is what we expected.
        $expected = $this->normalizeData($result);
        $contents = $request->getBody()->getContents();
        $content = json_decode($contents);

        $this->assertEquals($expected, $content);
    }
}
