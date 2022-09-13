<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Services\Api;

use Faker\Factory;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use ProcessMaker\Model\ListUnassigned;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\TaskUser;
use ProcessMaker\Model\User;
use ProcessMaker\Util\DateTime;
use Tests\TestCase;

/**
 * To do: This only works if the test database is the same where ProcessMaker is 
 * installed, improvements must be made so that the method "Installer::create_site()" 
 * can create the connection file (/processmaker/shared/sites/{workspace}/db.php) 
 * to different instances of MySql.
 */
class LightTest extends TestCase
{
    private $clientId;
    private $clientSecret;
    private $authorization;
    private $optionsForConvertDatetime;

    /**
     * This is using instead of DatabaseTransactions
     * @todo DatabaseTransactions is having conflicts with propel
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->truncateNonInitialModels();
        $this->workspace = env("DB_DATABASE", "test");
        $this->clientId = config("oauthClients.pm.clientId");
        $this->clientSecret = config("oauthClients.pm.clientSecret");
        $this->user = "admin";
        $this->password = "admin";
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
     * Return a simulated http client.
     * @param string $body
     * @return Client
     */
    private function getHttp($body = "")
    {
        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Cache-Control' => 'no-cache, must-revalidate',
            'Connection' => 'keep-alive',
            'Content-Type' => 'application/json; charset=utf-8',
        ];
        $mock = new MockHandler([
            new Response(200, $headers, $body)
        ]);
        $handler = HandlerStack::create($mock);
        $http = new Client([
            'handler' => $handler
        ]);
        return $http;
    }

    /**
     * Get authorization values.
     */
    private function getAuthorization()
    {
        $body = [
            "access_token" => "",
            "expires_in" => "",
            "refresh_token" => "",
            "scope" => "*",
            "token_type" => "",
        ];
        $http = $this->getHttp(json_encode($body));
        $request = $http->request("POST", "{$this->workspace}/oauth2/token", [
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
        $faker = $faker = Factory::create();

        //Create process
        $process = Process::factory()->create();

        //Tasks created in the factory process are cleaned because it does not meet the test rules
        Task::where('PRO_UID', $process->PRO_UID)->delete();

        //Get user
        $user = User::select()
                ->where('USR_USERNAME', '=', $this->user)
                ->first();

        //Create a task self service
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);

        //Assign a user in the task
        TaskUser::factory(1)->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);

        //truncate previous elements for create 15 registers
        ListUnassigned::truncate();

        //Create a record in list unassigned
        $listUnassigned = ListUnassigned::factory(15)->create([
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

        $body = clone $listUnassigned;
        $http = $this->getHttp(json_encode($this->normalizeData($body)));
        $this->getAuthorization();
        $request = $http->request("GET", "api/1.0/{$this->workspace}/light/unassigned", [
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

        $body = clone $result;
        $http = $this->getHttp(json_encode($this->normalizeData($body)));
        $this->getAuthorization();
        $request = $http->request("GET", "api/1.0/{$this->workspace}/light/unassigned?start={$start}&limit={$limit}", [
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
