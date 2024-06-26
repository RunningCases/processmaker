<?php
use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Exception\PendingException;
/**
 * Rest context.
 *
 * @category   Framework
 * @package    restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0
 */

global $config;

class RestContext extends BehatContext
{
    private $_startTime = null;
    private $_restObject = null;
    private $_headers = array();
    private $_restObjectType = null;
    private $_restObjectMethod = 'get';
    private $_client = null;
    private $_response = null;
    private $_request = null;
    private $_requestBody = null;
    private $_requestUrl = null;
    private $_type = null;
    private $_charset = null;
    private $_language = null;
    private $_contentType = null;
    private $_data = null;

    private $access_token = null;

    private $_parameters = array();

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here

        $this->_restObject = new stdClass();
        $this->_parameters = $parameters;
        $this->_client = new Guzzle\Service\Client();

        //suppress few errors
        $this->_client
            ->getEventDispatcher()
            ->addListener('request.error',
                function (\Guzzle\Common\Event $event) {
                    switch ($event['response']->getStatusCode()) {
                        case 400:
                        case 401:
                        case 404:
                        case 405:
                        case 406:
                            $event->stopPropagation();
                    }
                });
        $timezone = ini_get('date.timezone');
        if (empty($timezone)) {
            date_default_timezone_set('UTC');
        }
    }

    public function getParameter($name)
    {
        if (count($this->_parameters) === 0) {
            throw new \Exception('Parameters not loaded!');
        } else {
            $parameters = $this->_parameters;

            if(($name=="uploadFilesFolder")&&(!isset($parameters[$name]) ) ){
                $defaultUploadPath = __DIR__ . "/../resources/uploadfiles/";
                $parameters[$name] = $defaultUploadPath;
            }
            $this->printDebug("Parameter: $name = ".$parameters[$name]);
            return (isset($parameters[$name])) ? $parameters[$name] : null;
        }
    }

    /**
     * @BeforeScenario @MysqlDbConnection
     */
    public function verifyAllRequiredDataToConnectMysqlDB()
    {
        $db_parameters = array(
            'mys_db_type',
            'mys_db_server',
            'mys_db_name',
            'mys_db_username',
            'mys_db_password',
            'mys_db_port',
            'mys_db_encode',
            'mys_db_description');

        foreach ($db_parameters as $value) {
            $param = $this->getParameter($value);
            if (!isset($param)){
                throw new PendingException("Parameter ".$value." is not defined or is empty, please review behat.yml file!");
            }
        }
    }

    /**
     * @BeforeScenario @SqlServerDbConnection
     */
    public function verifyAllRequiredDataToConnectSqlServerDB()
    {
        $db_parameters = array(
            'sqlsrv_db_type',
            'sqlsrv_db_server',
            'sqlsrv_db_name',
            'sqlsrv_db_username',
            'sqlsrv_db_password',
            'sqlsrv_db_port',
            'sqlsrv_db_encode',
            'sqlsrv_db_description');

        foreach ($db_parameters as $value) {
            $param = $this->getParameter($value);
            if (!isset($param)){
                throw new PendingException("Parameter ".$value." is not defined or is empty, please review behat.yml file!");
            }
        }
    }

    /**
     * ============ json array ===================
     * @Given /^that I send (\[[^]]*\])$/
     *
     * ============ json object ==================
     * @Given /^that I send (\{(?>[^\{\}]+|(?1))*\})$/
     *
     * ============ json string ==================
     * @Given /^that I send ("[^"]*")$/
     *
     * ============ json int =====================
     * @Given /^that I send ([-+]?[0-9]*\.?[0-9]+)$/
     *
     * ============ json null or boolean =========
     * @Given /^that I send (null|true|false)$/
     */
    public function thatISend($data)
    {
        $this->_restObject = json_decode($data);
        $this->_restObjectMethod = 'post';
    }

    /**
     * @Given /^that I send:/
     * @param PyStringNode $data
     */
    public function thatISendPyString(PyStringNode $data) {
        $this->thatISend($data);
    }

    /**
     * ============ json array ===================
     * @Given /^the response equals (\[[^]]*\])$/
     *
     * ============ json object ==================
     * @Given /^the response equals (\{(?>[^\{\}]+|(?1))*\})$/
     *
     * ============ json string ==================
     * @Given /^the response equals ("[^"]*")$/
     *
     * ============ json int =====================
     * @Given /^the response equals ([-+]?[0-9]*\.?[0-9]+)$/
     *
     * ============ json null or boolean =========
     * @Given /^the response equals (null|true|false)$/
     */
    public function theResponseEquals($response)
    {
        $data = json_encode($this->_data);
        if ($data !== $response)
            throw new Exception("Response value does not match '$response'\n\n" );
    }

    /**
     * @Given /^the response equals:/
     * @param PyStringNode $data
     */
    public function theResponseEqualsPyString(PyStringNode $response)
    {
        $this->theResponseEquals($response);
    }
    /**
     * @Given /^that I want to make a new "([^"]*)"$/
     */
    public function thatIWantToMakeANew($objectType)
    {
        $this->_restObjectType = ucwords(strtolower($objectType));
        $this->_restObjectMethod = 'post';
    }

    /**
     * @Given /^that I want to update "([^"]*)"$/
     * @Given /^that I want to update an "([^"]*)"$/
     * @Given /^that I want to update a "([^"]*)"$/
     */
    public function thatIWantToUpdate($objectType)
    {
        $this->_restObjectType = ucwords(strtolower($objectType));
        $this->_restObjectMethod = 'put';
    }


    /**
     * @Given /^that I want to find a "([^"]*)"$/
     */
    public function thatIWantToFindA($objectType)
    {
        $this->_restObjectType = ucwords(strtolower($objectType));
        $this->_restObjectMethod = 'get';
    }

    /**
     * @Given /^that I want to delete a "([^"]*)"$/
     * @Given /^that I want to delete an "([^"]*)"$/
     * @Given /^that I want to delete "([^"]*)"$/
     */
    public function thatIWantToDeleteA($objectType)
    {
        $this->_restObjectType = ucwords(strtolower($objectType));
        $this->_restObjectMethod = 'delete';
    }

    /**
     * @Given /^that I want to delete a resource with the key "([^"]*)" stored in session array$/
     */
    public function thatIWantToDeleteAResourceWithTheKeyStoredInSessionArray($varName)
    {
        if (file_exists("session.data")) {
            $sessionData = json_decode(file_get_contents("session.data"));
        } else {
            $sessionData = array();
        }
        if (!isset($sessionData->$varName) ) {
            $varValue = '';
        } else {
            $varValue = $sessionData->$varName;
        }

        $this->_restDeleteQueryStringSuffix = "/" . $varValue;
        $this->_restObjectMethod = 'delete';
    }

    /**
     * @Given /^that I want to update a resource with the key "([^"]*)" stored in session array$/
     */
    public function thatIWantToUpdateAResourceWithTheKeyStoredInSessionArray($varName)
    {
        if (file_exists("session.data")) {
            $sessionData = json_decode(file_get_contents("session.data"));
        } else {
            $sessionData = array();
        }
        if (!isset($sessionData->$varName) ) {
            $varValue = '';
        } else {
            $varValue = $sessionData->$varName;
        }

        $this->_restUpdateQueryStringSuffix = "/" . $varValue;
        $this->_restObjectMethod = 'put';
    }

    /**
     * @Given /^that I want to get a resource with the key "([^"]*)" stored in session array$/
     */
    public function thatIWantToGetAResourceWithTheKeyStoredInSessionArray($varName)
    {
        if (file_exists("session.data")) {
            $sessionData = json_decode(file_get_contents("session.data"));
        } else {
            $sessionData = array();
        }
        if (!isset($sessionData->$varName) ) {
            $varValue = '';
        } else {
            $varValue = $sessionData->$varName;
        }

        $this->_restGetQueryStringSuffix = "/" . $varValue;
        $this->_restObjectMethod = 'get';
    }

    /**
     * @Given /^that "([^"]*)" header is set to "([^"]*)"$/
     * @Given /^that "([^"]*)" header is set to (\d+)$/
     */
    public function thatHeaderIsSetTo($header, $value)
    {
        $this->_headers[$header] = $value;
    }


    /**
     * @Given /^that its "([^"]*)" is "([^"]*)"$/
     * @Given /^that his "([^"]*)" is "([^"]*)"$/
     * @Given /^that her "([^"]*)" is "([^"]*)"$/
     * @Given /^its "([^"]*)" is "([^"]*)"$/
     * @Given /^his "([^"]*)" is "([^"]*)"$/
     * @Given /^her "([^"]*)" is "([^"]*)"$/
     * @Given /^that "([^"]*)" is set to "([^"]*)"$/
     * @Given /^"([^"]*)" is set to "([^"]*)"$/
     */
    public function thatItsStringPropertyIs($propertyName, $propertyValue)
    {
        $this->_restObject->$propertyName = $propertyValue;
    }

    /**
     * @Given /^that its "([^"]*)" is (\d+)$/
     * @Given /^that his "([^"]*)" is (\d+)$/
     * @Given /^that her "([^"]*)" is (\d+)$/
     * @Given /^its "([^"]*)" is (\d+)$/
     * @Given /^his "([^"]*)" is (\d+)$/
     * @Given /^her "([^"]*)" is (\d+)$/
     * @Given /^that "([^"]*)" is set to (\d+)$/
     * @Given /^"([^"]*)" is set to (\d+)$/
     */
    public function thatItsNumericPropertyIs($propertyName, $propertyValue)
    {
        $this->_restObject->$propertyName = is_float($propertyValue)
            ? floatval($propertyValue)
            : intval($propertyValue);
    }

    /**
     * @Given /^that its "([^"]*)" is (true|false)$/
     * @Given /^that his "([^"]*)" is (true|false)$/
     * @Given /^that her "([^"]*)" is (true|false)$/
     * @Given /^its "([^"]*)" is (true|false)$/
     * @Given /^his "([^"]*)" is (true|false)$/
     * @Given /^her "([^"]*)" is (true|false)$/
     * @Given /^that "([^"]*)" is set to (true|false)$/
     * @Given /^"([^"]*)" is set to (true|false)$/
     */
    public function thatItsBooleanPropertyIs($propertyName, $propertyValue)
    {
        $this->_restObject->$propertyName = $propertyValue == 'true';
    }

    /**
     * @Given /^the request is sent as JSON$/
     * @Given /^the request is sent as Json$/
     */
    public function theRequestIsSentAsJson()
    {
        $this->_headers['Content-Type'] = 'application/json; charset=utf-8';
        $this->_requestBody = json_encode(
            is_object($this->_restObject)
                ? (array)$this->_restObject
                : $this->_restObject
        );
    }

    // BACKGROUND STEPS
    /**
     * @Given /^that I have a valid access_token$/
     */
    public function thatIHaveAValidAccessToken()
    {
        $access_token = $this->getParameter('access_token');
        if (strlen($access_token)<= 10) {
            throw new PendingException();
            throw new Exception ("Access token is not valid, please review behat.yml\n\n" );
        }
        $this->access_token = $access_token;
    }

    /**
     * @When /^I request "([^"]*)"$/
     */
    public function iRequest($pageUrl, $urlType="",$customHeader=array())
    {
        $this->_startTime = microtime(true);
        $baseUrl = $this->getParameter('base_url');
        if ($this->access_token != null) {
            $this->_headers['Authorization'] = 'Bearer ' . $this->access_token;
        }elseif(!empty($customHeader)){
            foreach($customHeader as $headerKey => $headerValue){
                $this->_headers[$headerKey] = $headerValue;
            }
        }




        if($urlType=="absolute"){
            $this->_requestUrl = $pageUrl;
        }else{
            $this->_requestUrl = $baseUrl . $pageUrl;
        }
        $url = false !== strpos($pageUrl, '{')
            ? array($this->_requestUrl, (array)$this->_restObject)
            : $this->_requestUrl;

        switch (strtoupper($this->_restObjectMethod)) {
            case 'HEAD':
                $this->_request = $this->_client
                    ->head($url, $this->_headers);
                $this->_response = $this->_request->send();
                break;
            case 'GET':
                if (isset($this->_restGetQueryStringSuffix) &&
                    $this->_restGetQueryStringSuffix != '') {
                    $url .= $this->_restGetQueryStringSuffix;
                }
                $this->_request = $this->_client
                    ->get($url, $this->_headers);
                $this->_response = $this->_request->send();
                break;
            case 'POST':
                $postFields = is_object($this->_restObject)
                    ? (array)$this->_restObject
                    : $this->_restObject;
                $this->_request = $this->_client
                    ->post($url, $this->_headers,
                        (empty($this->_requestBody) ? $postFields :
                            $this->_requestBody));
                $this->_response = $this->_request->send();
                break;
            case 'PUT' :
                if (isset($this->_restUpdateQueryStringSuffix) &&
                    $this->_restUpdateQueryStringSuffix != '') {
                    $url .= $this->_restUpdateQueryStringSuffix;
                }
                $putFields = is_object($this->_restObject)
                    ? (array)$this->_restObject
                    : $this->_restObject;
                $this->printDebug("URL F: $url\n");
                $this->_request = $this->_client
                    ->put($url, $this->_headers,
                        (empty($this->_requestBody) ? $putFields :
                            $this->_requestBody));
                $this->_response = $this->_request->send();
                break;
            case 'PATCH' :
                $putFields = is_object($this->_restObject)
                    ? (array)$this->_restObject
                    : $this->_restObject;
                $this->_request = $this->_client
                    ->patch($url, $this->_headers,
                        (empty($this->_requestBody) ? $putFields :
                            $this->_requestBody));
                $this->_response = $this->_request->send();
                break;
            case 'DELETE':
                if (isset($this->_restDeleteQueryStringSuffix) &&
                    $this->_restDeleteQueryStringSuffix != '') {
                    $url .= $this->_restDeleteQueryStringSuffix;
                }
                $this->_request = $this->_client
                    ->delete($url, $this->_headers);
                $this->_response = $this->_request->send();
                break;
        }
        //detect type, extract data
        $this->_language = $this->_response->getHeader('Content-Language');

        $cType = explode('; ', $this->_response->getHeader('Content-type'));
        if (count($cType) > 1) {
            $charset = $cType[1];
            $this->_charset = substr($charset, strpos($charset, '=') + 1);
        }
        $this->_contentType = $cType[0];
        switch ($this->_contentType) {
            case 'text/html':
                $this->_data = $this->_response->getBody(true);
                return;
                break;
            case 'application/json':
                $this->_type = 'json';
                $this->_data = json_decode($this->_response->getBody(true));
                switch (json_last_error()) {
                    case JSON_ERROR_NONE :
                        return;
                    case JSON_ERROR_DEPTH :
                        $message = 'maximum stack depth exceeded';
                        break;
                    case JSON_ERROR_STATE_MISMATCH :
                        $message = 'underflow or the modes mismatch';
                        break;
                    case JSON_ERROR_CTRL_CHAR :
                        $message = 'unexpected control character found';
                        break;
                    case JSON_ERROR_SYNTAX :
                        $message = "malformed JSON:: \n\n ------\n".$this->_response->getBody(true)."\n ------";
                        break;
                    case JSON_ERROR_UTF8 :
                        $message = 'malformed UTF-8 characters, possibly ' .
                            'incorrectly encoded';
                        break;
                    default :
                        $message = 'unknown error';
                        break;
                }
                throw new Exception ("Error parsing JSON, $message \n\n" );
                break;
            case 'application/xml':
                $this->_type = 'xml';
                libxml_use_internal_errors(true);
                $this->_data = @simplexml_load_string(
                    $this->_response->getBody(true));
                if (!$this->_data) {
                    $message = '';
                    foreach (libxml_get_errors() as $error) {
                        $message .= $error->message . PHP_EOL;
                    }
                    throw new Exception ('Error parsing XML, ' . $message);
                }
                break;
            default:
                $this->_data = $this->_response->getBody(true);
        }
    }

    /**
     * @Then /^the response is JSON$/
     * @Then /^the response should be JSON$/
     */
    public function theResponseIsJson()
    {
        if ($this->_type != 'json') {
            throw new Exception("Response was not JSON\n" . $this->_response->getBody(true) );
        }
    }

    /**
     * @Then /^the response is XML$/
     * @Then /^the response should be XML$/
     */
    public function theResponseIsXml()
    {
        if ($this->_type != 'xml') {
            throw new Exception("Response was not XML\n\n" );
        }
    }

    /**
     * @Then /^the content type is "([^"]*)"$/
     */
    public function theResponseContentTypeIs($contentType)
    {

        if ($this->_contentType != $contentType) {
            throw new Exception("Response Content Type was not $contentType\n\n".$this->_response->getBody(true));
        }
    }

    /**
     * @Then /^the response charset is "([^"]*)"$/
     */
    public function theResponseCharsetIs($charset)
    {
        if (strtoupper($this->_charset) != strtoupper($charset)) {
            throw new Exception("Response charset was not $charset\n\n" );
        }
    }

    /**
     * @Then /^the response language is "([^"]*)"$/
     */
    public function theResponseLanguageIs($language)
    {
        if ($this->_language != $language) {
            throw new Exception("Response Language was not $language\n\n" );
        }
    }

    /**
     * @Then /^the response "([^"]*)" header should be "([^"]*)"$/
     */
    public function theResponseHeaderShouldBe($header, $value)
    {
        if (!$this->_response->hasHeader($header)) {
            throw new Exception("Response header $header was not found\n\n" );
        }
        if ((string)$this->_response->getHeader($header) !== $value) {
            throw new Exception("Response header $header ("
                . (string)$this->_response->getHeader($header)
                . ") does not match '$value'\n\n"
            );
        }
    }

    /**
     * @Then /^the response "Expires" header should be Date\+(\d+) seconds$/
     */
    public function theResponseExpiresHeaderShouldBeDatePlusGivenSeconds($seconds)
    {
        $server_time = strtotime($this->_response->getHeader('Date')) + $seconds;
        $expires_time = strtotime($this->_response->getHeader('Expires'));
        if ($expires_time === $server_time || $expires_time === $server_time + 1)
            return;
        return $this->theResponseHeaderShouldBe(
            'Expires',
            gmdate('D, d M Y H:i:s \G\M\T', $server_time)
        );
    }

    /**
     * @Then /^the response time should at least be (\d+) milliseconds$/
     */
    public function theResponseTimeShouldAtLeastBeMilliseconds($milliSeconds)
    {
        usleep(1);
        $diff = 1000 * (microtime(true) - $this->_startTime);
        if ($diff < $milliSeconds) {
            throw new Exception("Response time $diff is "
                . "quicker than $milliSeconds\n\n"
            );
        }
    }

    /**
     * @Given /^the json data is an empty array$/
     */
    public function theJsonDataIsAnEmptyArray()
    {
        $data = $this->_data;
        if (is_array($data) && count($data) == 0) {
            return;
        }
        throw new Exception("Response is not an empty array\n\n" );
    }

    /**
     * @Given /^the type is "([^"]*)"$/
     */
    public function theTypeIs($type)
    {
        $data = $this->_data;
        switch ($type) {
            case 'string':
                if (is_string($data)) return;
            case 'int':
                if (is_int($data)) return;
            case 'float':
                if (is_float($data)) return;
            case 'array' :
                if (is_array($data)) return;
            case 'object' :
                if (is_object($data)) return;
            case 'null' :
                if (is_null($data)) return;
        }

        throw new Exception("Response is not of type '$type'\n\n" );
    }

    /**
     * @Given /^the "([^"]*)" property type is "([^"]*)"$/
     */
    public function thePropertyTypeIs($property, $type)
    {
        $data = $this->_data;
        if (isset($this->{$property}) ) {
            throw new Exception("The property $property is not defined in the Response\n\n" );
        }
        $theProperty = $data->{$property};
        switch ($type) {
            case 'string':
                if (is_string($theProperty)) return;
            case 'int':
                if (is_int($theProperty)) return;
            case 'float':
                if (is_float($theProperty)) return;
            case 'array' :
                if (is_array($theProperty)) return;
            case 'object' :
                if (is_object($theProperty)) return;
            case 'null' :
                if (is_null($theProperty)) return;
        }

        throw new Exception("The property $property in Response is not of type '$type'\n\n");
    }

    /**
     * @Given /^the value equals "([^"]*)"$/
     */
    public function theValueEquals($sample)
    {
        $data = $this->_data;
        if ($data !== $sample) {
            throw new Exception("Response value does not match '$sample'\n\n" );
        }
    }

    /**
     * @Given /^the value equals (\d+)$/
     */
    public function theNumericValueEquals($sample)
    {
        $sample = is_float($sample) ? floatval($sample) : intval($sample);
        return $this->theValueEquals($sample);
    }

    /**
     * @Given /^the value equals (true|false)$/
     */
    public function theBooleanValueEquals($sample)
    {
        $sample = $sample == 'true';
        return $this->theValueEquals($sample);
    }

    /**
     * @Then /^the response is JSON "([^"]*)"$/
     */
    public function theResponseIsJsonWithType($type)
    {
        if ($this->_type != 'json') {
            throw new Exception("Response was not JSON\n" . $this->_response->getBody(true) );
        }

        $data = $this->_data;

        switch ($type) {
            case 'string':
                if (is_string($data)) return;
            case 'int':
                if (is_int($data)) return;
            case 'float':
                if (is_float($data)) return;
            case 'array' :
                if (is_array($data)) return;
            case 'object' :
                if (is_object($data)) return;
            case 'null' :
                if (is_null($data)) return;
        }

        throw new Exception("Response was JSON\n but not of type '$type'\n\n" );
    }


    /**
     * @Given /^the response has a "([^"]*)" property$/
     * @Given /^the response has an "([^"]*)" property$/
     * @Given /^the response has a property called "([^"]*)"$/
     * @Given /^the response has an property called "([^"]*)"$/
     */
    public function theResponseHasAProperty($propertyName)
    {
        $data = $this->_data;

        if (!empty($data)) {
            if (!isset($data->$propertyName)) {
                throw new Exception("Property '$propertyName' is not set!\n\n");
            }
        }
    }

    /**
     * @Given /^the response has not a "([^"]*)" property$/
     * @Given /^the response has not an "([^"]*)" property$/
     * @Given /^the response has not a property called "([^"]*)"$/
     * @Given /^the response has not an property called "([^"]*)"$/
     */
    public function theResponseHasNotAProperty($propertyName)
    {
        $data = $this->_data;

        if (!empty($data)) {
            if (isset($data->$propertyName)) {
                throw new Exception("Property '$propertyName' is set!\n\n");
            }
        }
    }

    /**
     * @Then /^the "([^"]*)" property equals "([^"]*)"$/
     */
    public function thePropertyEquals($propertyName, $propertyValue)
    {
        $data = $this->_data;

        if (!empty($data)) {
            if (!isset($data->$propertyName)) {
                throw new Exception("Property '$propertyName' is not set!\n\n" );
            }
            if ($data->$propertyName != $propertyValue) {
                throw new \Exception('Property value mismatch! (given: '
                    . $propertyValue . ', match: '
                    . $data->$propertyName . ")\n\n"
                );
            }
        } else {
            throw new Exception("Response was not JSON\n\n"
                . $this->_response->getBody(true));
        }
    }

    /**
     * @Then /^the "([^"]*)" property equals (\d+)$/
     */
    public function thePropertyEqualsNumber($propertyName, $propertyValue)
    {
        $propertyValue = is_float($propertyValue)
            ? floatval($propertyValue) : intval($propertyValue);
        return $this->thePropertyEquals($propertyName, $propertyValue);
    }

    /**
     * @Then /^the "([^"]*)" property in row (\d+) equals "([^"]*)"$/
     */
    public function thePropertyInRowEquals($propertyName, $row, $propertyValue)
    {
        $data = $this->_data;
        if (!empty($data)) {
            if (!is_array($data)) {
                throw new Exception("the Response data is not an array!\n\n" );
            }
            if (is_array($data) && !isset($data[$row])) {
                throw new Exception("the Response data is an array, but the row '$row' does not exists!\n\n" );
            }
            if (!isset($data[$row]->$propertyName)) {
                throw new Exception("Property '"
                    . $propertyName . "' is not set!\n\n"
                );
            }
            if ($data[$row]->$propertyName != $propertyValue) {
                throw new \Exception('Property value mismatch! (given: '
                    . $propertyValue . ', match: '
                    . $data[$row]->$propertyName . ")\n\n"
                );
            }
        } else {
            throw new Exception("Response was not JSON\n\n"
                . $this->_response->getBody(true));
        }
    }

    /**
     * @Then /^the "([^"]*)" property in row (\d+) equals (\d+)$/
     */
    public function thePropertyInRowEqualsNumber($propertyName, $row, $propertyValue)
    {
        $propertyValue = is_float($propertyValue)
            ? floatval($propertyValue) : intval($propertyValue);
        return $this->thePropertyInRowEquals($propertyName, $row, $propertyValue);
    }

    /**
     * @Then /^the "([^"]*)" property equals (true|false)$/
     */
    public function thePropertyEqualsBoolean($propertyName, $propertyValue)
    {
        return $this->thePropertyEquals($propertyName, $propertyValue == 'true');
    }

    /**
     * @Given /^the "([^"]*)" property in row (\d+) of property "([^"]*)" equals "([^"]*)"$/
     */
    public function thePropertyInRowOfPropertyEquals($propertyName, $row, $propertyParent, $propertyValue)
    {
        $data = $this->_data;
        if (empty($data)) {
            throw new Exception("Response is empty or was not JSON\n\n"
                . $this->_response->getBody(true));
            return;
        }

        if (!isset($data->$propertyParent)) {
            throw new Exception("Response has not the property '$propertyParent'\n\n"
                . $this->_response->getBody(true));
            return;
        }

        $data = $data->$propertyParent;

        if (!empty($data)) {
            if (!is_array($data)) {
                throw new Exception("the $propertyParent in Response data is not an array!\n\n" );
            }
            if (is_array($data) && !isset($data[$row])) {
                throw new Exception("the Response data is an array, but the row '$row' does not exists!\n\n" );
            }
            if (!isset($data[$row]->$propertyName)) {
                throw new Exception("Property '"
                    . $propertyName . "' is not set!\n\n"
                );
            }
            if (is_array($data[$row]->$propertyName)) {
                throw new Exception("$propertyName is an array and we expected a value\n\n"
                    . $this->_response->getBody(true));
            }
            if ($data[$row]->$propertyName != $propertyValue) {
                throw new \Exception('Property value mismatch! (given: '
                    . $propertyValue . ', match: '
                    . $data[$row]->$propertyName . ")\n\n"
                );
            }
        } else {
            throw new Exception("Response was not JSON\n\n"
                . $this->_response->getBody(true));
        }
    }

    /**
     * @Given /^the "([^"]*)" property in row (\d+) of property "([^"]*)" is "([^"]*)"$/
     */
    public function thePropertyInRowOfPropertyIs($propertyName, $row, $propertyParent, $propertyType)
    {
        $data = $this->_data;
        if (empty($data)) {
            throw new Exception("Response is empty or was not JSON\n\n"
                . $this->_response->getBody(true));
            return;
        }

        if (!isset($data->$propertyParent)) {
            throw new Exception("Response has not the property '$propertyParent'\n\n"
                . $this->_response->getBody(true));
            return;
        }

        $data = $data->$propertyParent;

        if (!empty($data)) {
            if (!is_array($data)) {
                throw new Exception("the property $propertyParent in Response data is not an array!\n\n" );
            }
            if (is_array($data) && !isset($data[$row])) {
                throw new Exception("the Response data is an array, but the row '$row' does not exists!\n\n" );
            }
            if (!isset($data[$row]->$propertyName)) {
                throw new Exception("Property '$propertyName' is not set in $propertyParent!\n\n");
            }
            if ($propertyType == 'array' && is_array($data[$row]->$propertyName)) {
                return true;
            }

            if ($propertyType == 'object' && is_object($data[$row]->$propertyName)) {
                return true;
            } else {
                throw new Exception("$propertyName is not an $propertyType\n\n"
                    . $this->_response->getBody(true));
            }
        } else {
            throw new Exception("Response was not JSON\n\n"
                . $this->_response->getBody(true));
        }
    }

    /**
     * @Given /^the type of the "([^"]*)" property is ([^"]*)$/
     */
    public function theTypeOfThePropertyIs($propertyName, $typeString)
    {
        $data = $this->_data;

        if (!empty($data)) {
            if (!isset($data->$propertyName)) {
                throw new Exception("Property '"
                    . $propertyName . "' is not set!\n\n");
            }
            // check our type
            switch (strtolower($typeString)) {
                case 'numeric':
                    if (!is_numeric($data->$propertyName)) {
                        throw new Exception("Property '"
                            . $propertyName . "' is not of the correct type: "
                            . $typeString . "!\n\n");
                    }
                    break;
            }

        } else {
            throw new Exception("Response was not JSON\n"
                . $this->_response->getBody(true));
        }
    }

    /**
     * @Then /^the response status code should be (\d+)$/
     */
    public function theResponseStatusCodeShouldBe($httpStatus)
    {
        if(!(isset($this->_response))){
            throw new \Exception('HTTP code does not match ' . $httpStatus .
                ' (actual: No response defined)'
            );
        }
        if ((string)$this->_response->getStatusCode() !== $httpStatus) {
            $message="";
            if($bodyResponse=json_decode($this->_response->getBody(true))){
                if(isset($bodyResponse->error->message)){
                    $message = $bodyResponse->error->message;
                }

            }

            throw new \Exception('HTTP code does not match ' . $httpStatus .
                ' (actual: ' . $this->_response->getStatusCode() . ") - $message\n\n"
            );
        }
    }

    /**
     * @Then /^the response is equivalent to this json file "([^"]*)"$/
     */
    public function theResponseIsEquivalentToThisJsonFile($jsonFile)
    {
        //$this->_data;
        $fileData = file_get_contents(__DIR__ . "/../json/" . $jsonFile);
        $fileJson = json_decode($fileData);
        if ($this->_data != $fileJson) {
            throw new \Exception("JSON Response does not match json file: $jsonFile\n\n" );
        }
    }

    /**
     * @Given /^that I want to make a new "([^"]*)" with:$/
     */
    public function thatIWantToMakeANewWith($object, TableNode $table)
    {
        $rows = array();
        foreach ($table->getHash() as $rowHash) {
            printf ("%s %s \n", $rowHash['name'], $rowHash['followers'] );
            //$user = new User();
            //$user->setUsername($userHash['name']);
            //$user->setFollowersCount($userHash['followers']);
            //$users[] = $user;
        }
    }

    /**
     * @Given /^POST this data:$/
     */
    public function postThisData(PyStringNode $string)
    {
        /*
         * Overwrite the $this->_requestBody = $string; line in order to replace line by line with test data.
         * */
        $linesValues = array();
        foreach ($string->getLines() as $line) {
            foreach ($this->_parameters as $param => $value) {
                $line = str_replace('<'.$param.'>', $value, $line);
            }
            $linesValues[] = $line;
        }
        $string->setLines($linesValues);


        $this->_restObjectMethod = 'post';
        $this->_headers['Content-Type'] = 'application/json; charset=UTF-8';
        $this->_requestBody = $string;
    }

    /**
     * @Given /^PUT this data:$/
     */
    public function putThisData(PyStringNode $string)
    {
        /*
         * Overwrite the $this->_requestBody = $string; line in order to replace line by line with test data.
         * */
        $linesValues = array();
        foreach ($string->getLines() as $line) {
            foreach ($this->_parameters as $param => $value) {
                $line = str_replace('<'.$param.'>', $value, $line);
            }
            $linesValues[] = $line;
        }
        $string->setLines($linesValues);


        $this->_restObjectMethod = 'put';
        $this->_headers['Content-Type'] = 'application/json; charset=UTF-8';
        $this->_requestBody = $string;
    }




    /**
     * @Given /^I want to Insert a new "([^"]*)" with:$/
     */
    public function iWantToInsertANewWith($url, PyStringNode $string)
    {
        //$this->_restObject = json_decode($string);
        $this->_restObjectMethod = 'post';
        $this->_headers['Content-Type'] = 'application/json; charset=utf-8';
        $this->_requestBody = $string;
        $this->iRequest($url);

        //$row = json_decode($string);
        //print_r($row);
        //print "************$string ***********";
    }

    /**
     * @Given /^store "([^"]*)" in session array$/
     */
    public function storeIn($varName)
    {
        if (!isset($this->_data->$varName)) {
            throw new \Exception("JSON Response does not have '$varName' property\n\n" );
        }

        $varValue = $this->_data->$varName;
        if (file_exists("session.data")) {
            $sessionData = json_decode(file_get_contents("session.data"));
        } else {
            $sessionData = new StdClass();
        }
        $sessionData->$varName = $varValue;
        file_put_contents("session.data", json_encode($sessionData));
    }

    /**
     * @Given /^store "([^"]*)" in session array as variable "([^"]*)"$/
     */
    public function storeInAsVariable($varName, $sessionVarName)
    {

        if (!isset($this->_data->$varName)) {
            throw new \Exception("JSON Response does not have '$varName' property\n\n" );
        }


        $varValue = $this->_data->$varName;
        if (file_exists("session.data")) {
            $sessionData = json_decode(file_get_contents("session.data"));
        } else {
            $sessionData = new StdClass();
        }
        $sessionData->$sessionVarName = $varValue;
        file_put_contents("session.data", json_encode($sessionData));
    }

    /**
     * @Then /^echo last response$/
     */
    public function echoLastResponse()
    {
        $this->printDebug("$this->_request\n$this->_response");
    }


    //*********** WEN

    /**
     * @Given /^POST data from file "([^"]*)"$/
     */
    public function postDataFromFile($jsonFile)
    {
        $filePath = __DIR__ . "/../json/" . $jsonFile;

        if(file_exists($filePath))
        {
            $fileData = file_get_contents($filePath);
            $this->postThisData(new PyStringNode($fileData));
        }
        else
        {
            throw new \Exception("JSON File: $filePath not found\n\n" );
        }
        // throw new PendingException();
    }

    /**
     * @Given /^PUT data from file "([^"]*)"$/
     */
    public function putDataFromFile($jsonFile)
    {
        $filePath = __DIR__ . "/../json/" . $jsonFile;

        if(file_exists($filePath))
        {
            $fileData = file_get_contents($filePath);
            $this->putThisData(new PyStringNode($fileData));
        }
        else
        {
            throw new \Exception("JSON File: $filePath not found\n\n" );
        }
        // throw new PendingException();
    }
    /**
     * @Given /^This scenario is not implemented yet$/
     * @Given /^this scenario is not implemented yet$/
     */
    public function thisScenarioIsNotImplementedYet()
    {
        throw new PendingException();
    }

    /**
     * @Then /^the response has (\d+) records$/
     * @Then /^the response has (\d+) record$/
     * @Then /^the response has (\d+) records in property "([^"]*)"$/
     * @Then /^the response has (\d+) record in property "([^"]*)"$/
     */
    public function theResponseHasRecords($quantityOfRecords, $responseProperty="")
    {
        if($responseProperty!=""){
            if(!isset($this->_data->$responseProperty)){
                throw new Exception("the Response data doesn't have a property named: $responseProperty\n\n" );
            }
            $data = $this->_data->$responseProperty;
        }else{
            $data = $this->_data;
        }

        if (!is_array($data)) {
            if ($quantityOfRecords == 0) {
                //if we expect 0 records and the response in fact is not an array, just return as a valid test
                return;
            } else {
                throw new Exception("the Response data is not an array!\n\n" );
            }
        }
        $currentRecordsCount=count($data);
        if($currentRecordsCount!=$quantityOfRecords){
            throw new Exception('Records quantity not match ' . $quantityOfRecords .               ' (actual: ' . $currentRecordsCount . ")\n\n");
        }
    }

    /**
     * @Given /^that I want to update a resource with the key "([^"]*)" stored in session array as variable "([^"]*)"$/
     * @Given /^that I want to update a resource with the key "([^"]*)" stored in session array as variable "([^"]*)" in position (\d+)$/
     */
    public function thatIWantToUpdateAResourceWithTheKeyStoredInSessionArrayAsVariable($varName, $sessionVarName, $position=null)
    {
        if (file_exists("session.data")) {
            $sessionData = json_decode(file_get_contents("session.data"));
        } else {
            $sessionData = array();
        }
        if (!isset($sessionData->$sessionVarName) ) {
            $varValue = '';
        }elseif(!is_null($position)){
            foreach ($sessionData->$sessionVarName as $key => $value) {
                if($key == $position){
                    $varValue = $value;
                }
            }
        } else {
            $varValue = $sessionData->$sessionVarName;
        }

        $this->_restUpdateQueryStringSuffix = "/" . $varValue;
        $this->_restObjectMethod = 'put';
    }

    /**
     * @Given /^that I want to get a resource with the key "([^"]*)" stored in session array as variable "([^"]*)"$/
     * @Given /^that I want to get a resource with the key "([^"]*)" stored in session array as variable "([^"]*)" in position (\d+)$/
     */
    public function thatIWantToGetAResourceWithTheKeyStoredInSessionArrayAsVariable($varName, $sessionVarName, $position=null)
    {
        if (file_exists("session.data")) {
            $sessionData = json_decode(file_get_contents("session.data"));
        } else {
            $sessionData = array();
        }
        if (!isset($sessionData->$sessionVarName) ) {
            $varValue = '';
        }elseif(!is_null($position)){
            foreach ($sessionData->$sessionVarName as $key => $value) {
                if($key == $position){
                    $varValue = $value;
                }
            }
        } else {
            $varValue = $sessionData->$sessionVarName;
        }

        $this->_restGetQueryStringSuffix = "/" . $varValue;
        $this->_restObjectMethod = 'get';
    }

    /**
     * @Given /^that I want to delete a resource with the key "([^"]*)" stored in session array as variable "([^"]*)"$/
     * @Given /^that I want to delete a resource with the key "([^"]*)" stored in session array as variable "([^"]*)" in position (\d+)$/
     */
    public function thatIWantToDeleteAResourceWithTheKeyStoredInSessionArrayAsVariable($varName, $sessionVarName, $position=null)
    {
        if (file_exists("session.data")) {
            $sessionData = json_decode(file_get_contents("session.data"));
        } else {
            $sessionData = array();
        }
        if (!isset($sessionData->$sessionVarName) ) {
            $varValue = '';
        }elseif(!is_null($position)){
            foreach ($sessionData->$sessionVarName as $key => $value) {
                if($key == $position){
                    $varValue = $value;
                }
            }
        } else {
            $varValue = $sessionData->$sessionVarName;
        }

        $this->_restDeleteQueryStringSuffix = "/" . $varValue;

        $this->printDebug("$varName = $varValue\nsessionVarName = $sessionVarName\n");

        $this->_restObjectMethod = 'delete';
    }

    /**
     * @Given /^the response status message should have the following text "([^"]*)"$/
     */
    public function theResponseStatusMessageShouldHaveTheFollowingText($arg1)
    {

        if( $arg1!=""){
            $message="";
            if($bodyResponse=json_decode($this->_response->getBody(true))){
                if(isset($bodyResponse->error->message)){
                    $message = $bodyResponse->error->message;
                    if (strpos($message,$arg1) === false) {
                        throw new \Exception("Error message text does not have: '" . $arg1 ."' (actual: '$message')\n\n");
                    }
                }elseif(is_array($bodyResponse)){
                    $error_found=false;
                    $messages = array();
                    foreach($bodyResponse as $resp){
                        if(isset($resp->error)){
                            $messages[]=$resp->error;
                            if (strpos($resp->error,$arg1) !== false){
                                $error_found=true;
                            }
                        }

                    }
                    if(!$error_found){
                        $message=implode("\n- ",$messages);
                        throw new \Exception("Error message text does not have: '" . $arg1 ."' \nCurrent messages: \n- $message\n\n");
                    }
                }else{
                    throw new \Exception('This is not a valid error response');
                }

            }else{
                throw new \Exception('This is not a valid response');

            }
        }



    }

    /**
     * @Given /^I request "([^"]*)"  with the key "([^"]*)" stored in session array as variable "([^"]*)"$/
     * @Given /^I request "([^"]*)"  with the key "([^"]*)" stored in session array as variable "([^"]*)" and url is "([^"]*)"$/
     * @Given /^I request "([^"]*)" with the key "([^"]*)" stored in session array as variable "([^"]*)"$/
     * @Given /^I request "([^"]*)" with the key "([^"]*)" stored in session array as variable "([^"]*)" and url is "([^"]*)"$/
     */
    public function iRequestWithTheKeyStoredInSessionArrayAsVariable($pageUrl, $varName, $sessionVarName, $urlType="")
    {
        if (file_exists("session.data")) {
            $sessionData = json_decode(file_get_contents("session.data"));
        } else {
            $sessionData = array();
        }
        if (!isset($sessionData->$sessionVarName) ) {
            $varValue = '';
        } else {
            $varValue = $sessionData->$sessionVarName;
        }

        $pageUrl = str_replace($varName, $varValue, $pageUrl);


        $this->printDebug("URL: $pageUrl\n$varName = $varValue\nsessionVarName = $sessionVarName\n");


        $this->iRequest($pageUrl, $urlType);



    }

    /**
     * @Given /^I request "([^"]*)" with the key "([^"]*)" stored in session array as variable "([^"]*)" in position (\d+)$/
     */
    public function iRequestWithTheKeyStoredInSessionArrayAsVariableInPosition($pageUrl, $varName, $sessionVarName, $position)
    {
        if (file_exists("session.data")) {
            $sessionData = json_decode(file_get_contents("session.data"));
        } else {
            $sessionData = array();
        }
        if (!isset($sessionData->$sessionVarName) ) {
            $varValue = '';
        } else {
            foreach ($sessionData->$sessionVarName as $key => $value) {
                if($key == $position){
                    $varValue = $value;
                }
            }
        }

        $pageUrl = str_replace($varName, $varValue, $pageUrl);


        $this->printDebug("URL: $pageUrl\n$varName = $varValue\nsessionVarName = $sessionVarName\n");


        $this->iRequest($pageUrl);
    }


    /**
     * @Given /^the property "([^"]*)" of "([^"]*)" is set to "([^"]*)"$/
     */
    public function thePropertyOfIsSetTo($propertyName, $objName, $propertyValue)
    {
        $data = $this->_data;
        if (!empty($data)) {
            if (!isset($data->$objName)) {
                throw new Exception("Object '$objName' is not set!\n\n" );
            }
            if (!isset($data->$objName->$propertyName)) {
                throw new Exception("Property '$propertyName' is not set in object '$objName'!\n\n" );
            }
            if ($data->$objName->$propertyName != $propertyValue) {
                throw new \Exception('Property value mismatch! (given: "'
                    . $propertyValue . '", match: "'
                    . $data->$objName->$propertyName . '")\n\n'
                );
            }
        } else {
            throw new Exception("Response was not JSON\n\n"
                . $this->_response->getBody(true));
        }
    }

    /**
     * @When /^I request "([^"]*)" with the keys? "([^"]*)" stored in session array$/
     */
    public function iRequestWithTheKeysStoredInSessionArray($url, $sessionVarName)
    {
        if (file_exists("session.data")) {
            $sessionData = json_decode(file_get_contents("session.data"));
        } else {
            $sessionData = array();
        }

        $arraySessionVarName = explode(",", $sessionVarName);

        foreach ($arraySessionVarName as $value) {
            $varName = trim($value);

            $varValue = (isset($sessionData->$varName))? $sessionData->$varName : "";

            $url = str_replace($varName, $varValue, $url);
        }

        $this->iRequest($url);
    }

    //UPLOAD FILE MANAGER
    /**
     * @Given /^POST I want to upload the file "([^"]*)" to path "([^"]*)". Url "([^"]*)"$/
     */
    public function postIWantToUploadTheFileToPathPublicUrl($prfFile, $prfPath, $url)
    {
        $prfFile = $this->getParameter('uploadFilesFolder') . $prfFile;
        $accesstoken = $this->getParameter('access_token');
        $headr = array();
        $headr[] = 'Authorization: Bearer '.$accesstoken;
        $path = rtrim($prfPath, '/') . '/';
        $sfile = end(explode("/",$prfFile));

        $postFields = array('prf_filename'=>$sfile, "prf_path" => $path);

        $this->_restObjectMethod = 'post';
        $this->_restObject = $postFields;
        $this->iRequest($url);

        $postResult = json_decode($this->_response->getBody(true));


        if(!isset($postResult->error)){


            $prfUid = $postResult->prf_uid;
            $url = $this->getParameter('base_url').$url.'/'.$prfUid."/upload";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array('prf_file'=>'@'.$prfFile));
            curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $postResult = curl_exec($ch);
            curl_close($ch);
        }

    }

    //UPLOAD IMAGE
    /**
     * @Given /^POST I want to upload the image "([^"]*)" to user "([^"]*)". Url "([^"]*)"$/
     */
    public function postIWantToUploadTheImageToUser($imageFile, $usrUid, $url)
    {
        $imageFile = $this->getParameter('uploadFilesFolder') . $imageFile;
        $baseUrl = $this->getParameter('base_url');
        $url = $baseUrl.$url.$usrUid."/image-upload";

        $accesstoken = $this->getParameter('access_token');
        $headr = array();
        $headr[] = 'Authorization: Bearer '.$accesstoken;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('USR_PHOTO'=>'@'.$imageFile));
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $postResult = curl_exec($ch);

        if(  $postResult === false)
        {
            //trigger_error(curl_error($ch));
            throw new Exception("Image upload failed ($imageFile):\n\n"
                . curl_error($ch));
        }
        curl_close($ch);
        echo $postResult;
    }

    /**
     * @Given /^POST I want to upload the image "([^"]*)" to user with the key "([^"]*)" stored in session array as variable "([^"]*)"\. Url "([^"]*)"$/
     */
    public function postIWantToUploadTheImageToUserWithTheKeyStoredInSessionArrayAsVariableUsrUidUrl($imageFile, $varName, $sessionVarName, $url)
    {
        if (file_exists("session.data")) {
            $sessionData = json_decode(file_get_contents("session.data"));
        } else {
            $sessionData = array();
        }
        if (!isset($sessionData->$sessionVarName) ) {
            $varValue = '';
        } else {
            $varValue = $sessionData->$sessionVarName;
        }

        $usrUid = $varValue;
        $imageFile = $imageFile;

        $this->postIWantToUploadTheImageToUser($imageFile, $usrUid, $url);
    }

    /**
     * @Given /^that I want to delete the folder$/
     */
    public function thatIWantToDeleteTheFolder()
    {
        $this->_restObjectMethod = 'delete';
    }

    /**
     * @Given /^store response count in session variable as "([^"]*)"$/
     */
    public function storeResponseCountInSessionVariableAs($varName)
    {
        $data = $this->_data;
        $currentRecordsCount=count($data);
        if (file_exists("session.data")) {
            $sessionData = json_decode(file_get_contents("session.data"));
        } else {
            $sessionData = new StdClass();
        }
        $sessionData->$varName = $currentRecordsCount;
        file_put_contents("session.data", json_encode($sessionData));
    }


    /**
     * @Given /^the response has (\d+) records more than "([^"]*)"$/
     */
    public function theResponseHasRecordsMoreThan($records, $base)
    {
        if (file_exists("session.data")) {
            $sessionData = json_decode(file_get_contents("session.data"));
        } else {
            $sessionData = array();
        }
        if (!isset($sessionData->$base) ) {
            $varValue = '';
        } else {
            $varValue = $sessionData->$base;
        }

        $totalRecords=$varValue + $records;

        $this->theResponseHasRecords($totalRecords);
    }


    /**
     * @Given /^POST upload an input document "([^"]*)" to "([^"]*)"$/
     */
    public function postUploadAnInputDocumentTo($file, $url, PyStringNode $string)
    {
        $file = $this->getParameter('uploadFilesFolder') . $file;
        $postFields = json_decode($string);
        $postFields->form ='@'.$file;

        $this->_restObjectMethod = 'post';
        $this->_restObject = $postFields;
        $this->iRequest($url);


    }

    /**
     * @Given /^POST upload a project file "([^"]*)" to "([^"]*)"$/
     */
    public function postUploadAProjectFile($file, $url)
    {
        $file = $this->getParameter('uploadFilesFolder') . $file;
        $postFields = new StdClass();
        $postFields->project_file ='@'.$file;

        $this->_restObjectMethod = 'post';
        $this->_restObject = $postFields;
        $this->iRequest($url);

    }


    /**
     * @Given /^the "([^"]*)" property in object (\d+) of property "([^"]*)" equals "([^"]*)"$/
     */
    public function thePropertyInObjectOfPropertyEquals($propertyName, $row, $propertyParent, $propertyValue)
    {
        $data = $this->_data;
        if (empty($data)) {
            throw new Exception("Response is empty or was not JSON\n\n"
                . $this->_response->getBody(true));
            return;
        }

        if (!isset($data->$propertyParent)) {
            throw new Exception("Response has not the property '$propertyParent'\n\n"
                . $this->_response->getBody(true));
            return;
        }

        $data = $data->$propertyParent;

        if (!empty($data)) {
            if (!is_object($data)) {
                throw new Exception("the $propertyParent in Response data is not an array!\n\n" );
            }
            if (is_object($data) && !isset($data->$row)) {
                throw new Exception("the Response data is an array, but the row '$row' does not exists!\n\n" );
            }
            if (!isset($data->$row->$propertyName)) {
                throw new Exception("Property '"
                    . $propertyName . "' is not set!\n\n"
                );
            }
            if (is_array($data->$row->$propertyName)) {
                throw new Exception("$propertyName is an array and we expected a value\n\n"
                    . $this->_response->getBody(true));
            }
            if ($data->$row->$propertyName != $propertyValue) {
                throw new \Exception('Property value mismatch! (given: '
                    . $propertyValue . ', match: '
                    . $data->$row->$propertyName . ")\n\n"
                );
            }
        } else {
            throw new Exception("Response was not JSON\n\n"
                . $this->_response->getBody(true));
        }
    }

    /**
     * @Given /^store "([^"]*)" in session array as variable "([^"]*)" where an object has "([^"]*)" equal to "([^"]*)"$/
     */
    public function storeInSessionArrayAsVariableWhereAnObjectHasEqualsTo($varName, $sessionVarName, $objectProperty, $objectValue)
    {

        $swFound=false;
        if (file_exists("session.data")) {
            $sessionData = json_decode(file_get_contents("session.data"));
        } else {
            $sessionData = new StdClass();
        }

        $sessionData->$sessionVarName = array();


        foreach($this->_data as $obj){
            if((isset($obj->$objectProperty))&&($obj->$objectProperty == $objectValue)){
                $swFound=true;
                $varValue = $obj->$varName;

                //$sessionData->$sessionVarName = $varValue;
                $sessionData->{$sessionVarName}[] = $varValue;
                file_put_contents("session.data", json_encode($sessionData));
            }
        }
        if (!$swFound) {
            //print_r($this->_data);
            $this->printDebug("JSON Response does not have '$sessionVarName' property\n\n");
            //throw new \Exception("JSON Response does not have '$sessionVarName' property\n\n" );
        }
    }

    /**
     * @Given /^that "([^"]*)" property in object "([^"]*)" equals "([^"]*)"$/
     */
    public function thatPropertyInObjectEquals($propertyName, $propertyParent, $propertyValue)
    {
        $data = $this->_data;
        if (empty($data)) {
            throw new Exception("Response is empty or was not JSON\n\n"
                . $this->_response->getBody(true));
            return;
        }

        if (!isset($data->$propertyParent)) {
            throw new Exception("Response has not the property '$propertyParent'\n\n"
                . $this->_response->getBody(true));
            return;
        }

        $data = $data->$propertyParent;

        if (!empty($data)) {
            if (!is_object($data)) {
                throw new Exception("the $propertyParent in Response data is not an object!\n\n" );
            }
            if (!isset($data->$propertyName)) {
                throw new Exception("Property '"
                    . $propertyName . "' is not set!\n\n"
                );
            }
            if (is_array($data->$propertyName)) {
                throw new Exception("$propertyName is an array and we expected a value\n\n"
                    . $this->_response->getBody(true));
            }
            if ($data->$propertyName != $propertyValue) {
                throw new \Exception('Property value mismatch! (given: '
                    . $propertyValue . ', match: '
                    . $data->$propertyName . ")\n\n"
                );
            }
        } else {
            throw new Exception("Response was not JSON\n\n"
                . $this->_response->getBody(true));
        }


    }
    /**
     * @Given /^save exported process to "([^"]*)"$/
     * @Given /^save exported process to "([^"]*)" as "([^"]*)"$/
     */
    public function saveExportedProcessTo($destinationFolder, $exportedProcessFileName="")
    {

        if($exportedProcessFileName == ""){//Obtain name from XML
            $exportedProcessFileName=$this->_data->xpath('//metadata/meta[@key="name"]');
            $exportedProcessFileName = $exportedProcessFileName[0];
            $exportedProcessFileName = "ExpBehat ".$exportedProcessFileName;


        }
        $destinationFolder = $this->getParameter('uploadFilesFolder') . $destinationFolder;
        $exportedProcessFileName = $destinationFolder.str_replace(" ","_",$exportedProcessFileName).".pmx";

        $this->printDebug("Exporting process to: $exportedProcessFileName");

        file_put_contents($exportedProcessFileName, $this->_response->getBody(true));
        chmod($exportedProcessFileName, 0777);


    }


    /**
     * @Given /^POST a dynaform:$/
     */
    public function postADynaform(PyStringNode $string)
    {
        $postFields = json_decode($string);

        if ((isset($postFields->dyn_content))&&(file_exists($this->getParameter('uploadFilesFolder') . $postFields->dyn_content))) {
            $postFields->dyn_content = $this->getParameter('uploadFilesFolder') . $postFields->dyn_content;
            $this->printDebug("Extracting dyanform content from: ".$postFields->dyn_content."\n");
            $postFields->dyn_content = file_get_contents($postFields->dyn_content);

            $string = json_encode($postFields);
        }



        $this->_restObjectMethod = 'post';
        $this->_headers['Content-Type'] = 'application/json; charset=UTF-8';
        $this->_requestBody = $string;
    }

    /**
     * @Given /^PUT a dynaform:$/
     */
    public function putADynaform(PyStringNode $string)
    {
        $postFields = json_decode($string);

        if ((isset($postFields->dyn_content))&&(file_exists($this->getParameter('uploadFilesFolder') . $postFields->dyn_content))) {
            $postFields->dyn_content = $this->getParameter('uploadFilesFolder') . $postFields->dyn_content;
            $this->printDebug("Extracting dyanform content from: ".$postFields->dyn_content."\n");
            $postFields->dyn_content = file_get_contents($postFields->dyn_content);

            $string = json_encode($postFields);
        }


        $this->_restObjectMethod = 'put';
        $this->_headers['Content-Type'] = 'application/json; charset=UTF-8';
        $this->_requestBody = $string;
    }

    /**
     * @overrides
     */
    public function printDebug($string)
    {
        //echo "\n\033[36m|  " . strtr($string, array("\n" => "\n|  ")) . "\033[0m\n\n";

        $fp = fopen(sys_get_temp_dir() . "/behat.log", "a+");
        fwrite($fp, $string . PHP_EOL);
    }
    /**
     * @Then /^if database-connection with id "([^"]*)" is active$/
     */
    public function ifDatabaseConnectionWithIdIsActive($dbConnectionId)
    {
        if(!(isset($this->_response))){
            throw new \Exception('Empty result ' );
        }
        $message="";
        $sw_error=false;
        if($bodyResponse=json_decode($this->_response->getBody(true))){
            //print_r($bodyResponse);
            foreach($bodyResponse as $testDetail){
                $message.=$testDetail->test;
                if(isset($testDetail->error)){
                    $sw_error=true;
                    $message .= " -> ".$testDetail->error;
                }else{
                    $message.=" -> [OK]";
                }
                $message.=" | ";
            }

        }else{
            throw new \Exception('Empty result ' );
        }

        if (file_exists("session.data")) {
            $sessionData = json_decode(file_get_contents("session.data"));
        } else {
            $sessionData = new StdClass();
        }
        if(!isset($sessionData->dbconnectionStatus)){
            $sessionData->dbconnectionStatus = new StdClass();
        }
        $sessionData->dbconnectionStatus->$dbConnectionId = !$sw_error;
        file_put_contents("session.data", json_encode($sessionData));
        if($sw_error){
            throw new PendingException($message);
        }
    }
    /**
     * @Given /^database-connection with id "([^"]*)" is active$/
     */
    public function databaseConnectionWithIdIsActive($dbConnectionId)
    {
        if (file_exists("session.data")) {
            $sessionData = json_decode(file_get_contents("session.data"));
        } else {
            $sessionData = new StdClass();
        }

        if(!$sessionData->dbconnectionStatus->$dbConnectionId){
            throw new PendingException("Skip inactive dbconnection: $dbConnectionId");
        }
    }
    /**
     * @Given /^OAUTH register an application$/
     */
    public function oauthRegisterAnApplication(PyStringNode $data)
    {
        $this->printDebug("Register Application...");
        $baseUrl            = $this->getParameter('base_url');
        $login_url          = $this->getParameter('login_url');
        $authentication_url = $this->getParameter('authentication_url');
        $oauth_app_url      = $this->getParameter('oauth_app_url');
        $oauth_authorization_url      = $this->getParameter('oauth_authorization_url');

        $user_name          = $this->getParameter('user_name');
        $user_password      = $this->getParameter('user_password');
        $cookie_file        = sys_get_temp_dir()."pmcookie";



        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $authentication_url);
        curl_setopt($ch, CURLOPT_REFERER, $login_url);
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "form[USR_USERNAME]=$user_name&form[USR_PASSWORD]=$user_password&form[USER_LANG]=en&form[URL]");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $answer = curl_exec($ch);
        $newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);


        if (strpos($newurl, "/login/login") !== false) {
            throw new Exception('Bad credentials');
        }


        //print "<textarea>$answer</textarea>";
        if (curl_error($ch)) {
            throw new Exception(curl_error($ch));
        }


        // Read the session saved in the cookie file

        if(!file_exists($cookie_file)){
            throw new Exception('Invalid Cookie/Session: '.$cookie_file);
        }



        //another request preserving the session

        $data = json_decode((string) $data);

        $name=$data->name;
        $description=$data->description;
        $webSite = $data->webSite;
        $redirectUri=$data->redirectUri;
        $applicationNumber=$data->applicationNumber;

        //1. Register application
        curl_setopt($ch, CURLOPT_URL, $oauth_app_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "option=INS&name=$name&description=$description&webSite=$webSite&redirectUri=$redirectUri");
        $answer = curl_exec($ch);
        if (curl_error($ch)) {
            throw new Exception(curl_error($ch));
        }
        $newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        if (strpos($newurl, "/login/login") !== false) {
            throw new Exception('Not authenticated');
        }
        // json_decode(json)
        $response=json_decode($answer);
        $this->printDebug("Register application:\n".$answer."\n");
        $this->_restObjectMethod = 'post';
        $this->_headers['Content-Type'] = 'application/json; charset=UTF-8';
        $this->_response = json_decode($answer);


        if (file_exists("session.data")) {
            $sessionData = json_decode(file_get_contents("session.data"));
        } else {
            $sessionData = new StdClass();
        }
        foreach($response->data as $key => $varValue){
            $sessionVarName=$key."_".$applicationNumber;
            $sessionData->$sessionVarName = $varValue;
            $this->printDebug("Save $sessionVarName = $varValue");
        }
        //print_r($sessionData);

        $clientId = $response->data->CLIENT_ID;
        $clientSecret = $response->data->CLIENT_SECRET;

        //2. Request Authorization
        curl_setopt($ch, CURLOPT_URL, $oauth_authorization_url."?"."response_type=code&client_id=$clientId&scope=*");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "allow=Accept&transaction_id=");
        //print "response_type=code&client_id=$clientId&scope=*";
        $answer = curl_exec($ch);
        if (curl_error($ch)) {
            throw new Exception(curl_error($ch));
        }
        $newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $codeA = explode("code=",$newurl);

        $code = $codeA[1];
        $this->printDebug("Authorization code:\n".$code."\n");

        //3. Request Token
        $headr = array();
        $headr[] = 'Content-Type: application/json';
        $headr[] = 'Authorization: Basic '.base64_encode("$clientId:$clientSecret");

        curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
        //curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $baseUrl."oauth2/token");
        //curl_setopt($ch, CURLOPT_USERPWD, "$clientId:$clientSecret");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array("grant_type"=>"authorization_code","code"=>$code)));

        $answer = curl_exec($ch);
        if (curl_error($ch)) {
            throw new Exception(curl_error($ch));
        }
        $newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $this->printDebug("Request token:\n".$answer."\n");
        //print_r("Request token:\n".$newurl."\n");
        foreach(json_decode($answer) as $key => $varValue){
            $sessionVarName=$key."_".$applicationNumber;
            $sessionData->$sessionVarName = $varValue;
            $this->printDebug("Save $sessionVarName = $varValue");
        }
        file_put_contents("session.data", json_encode($sessionData));

    }

    /**
     * @Given /^I request a owner password credential grant$/
     */
    public function iRequestAOwnerPasswordCredentialGrant()
    {
        $baseUrl            = $this->getParameter('base_url');
        $clientId            = $this->getParameter('client_id');
        $clientSecret            = $this->getParameter('client_secret');

        $this->printDebug("Password credentials");

        $headr = array();
        $headr['Authorization'] = 'Basic '.base64_encode("$clientId:$clientSecret");

        $this->iRequest($baseUrl."oauth2/token", "absolute",$headr);
        //print_r($this->_data);
        if(isset($this->_data->error)){
            throw new Exception($this->_data->error." : ".$this->_data->error_description);
        }
    }
    /**
     * @Given /^I request a client credential grant$/
     */
    public function iRequestAClientCredentialGrant()
    {
        $baseUrl            = $this->getParameter('base_url');
        $clientId            = $this->getParameter('client_id');
        $clientSecret            = $this->getParameter('client_secret');

        $this->printDebug("Client credentials");

        $headr = array();
        $headr['Authorization'] = 'Basic '.base64_encode("$clientId:$clientSecret");

        $this->iRequest($baseUrl."oauth2/token", "absolute",$headr);
        //print_r($this->_data);
        if(isset($this->_data->error)){
            throw new Exception($this->_data->error." : ".$this->_data->error_description);
        }
    }
    /**
     * @Given /^I request a refresh token for "([^"]*)"$/
     */
    public function iRequestARefreshToken($refreshTokenSession)
    {
        $refArray=explode("_",$refreshTokenSession);
        $varNumber = $refArray[2];
        $baseUrl            = $this->getParameter('base_url');
        $clientId            = $this->getParameter('client_id');
        $clientSecret            = $this->getParameter('client_secret');
        $this->printDebug("Refresh token");

        $headr = array();

        $request=array();
        $request['grant_type']="refresh_token";
        if (file_exists("session.data")) {
            $sessionData = json_decode(file_get_contents("session.data"));
        } else {
            $sessionData = array();
        }
        if (!isset($sessionData->$refreshTokenSession) ) {
            $varValue = '';
        } else {
            $varValue = $sessionData->$refreshTokenSession;
            $clientIdName="CLIENT_ID_$varNumber";
            $clientSecretName="CLIENT_SECRET_$varNumber";
            $clientId            = $sessionData->$clientIdName;
            $clientSecret            = $sessionData->$clientSecretName;
        }
        $headr['Authorization'] = 'Basic '.base64_encode("$clientId:$clientSecret");
        $request['refresh_token']=$varValue;
        $this->_requestBody=json_encode($request);
        print_r($this->_requestBody);
        $this->iRequest($baseUrl."oauth2/token", "absolute", $headr);
        print_r($this->_data);
        if(isset($this->_data->error)){
            throw new Exception($this->_data->error." : ".$this->_data->error_description);
        }
    }

    /**
     * @Given /^OAUTH request implicit grant$/
     */
    public function oauthRequestImplicitGrant(PyStringNode $data)
    {
        $this->printDebug("Implicit Grant");
        $baseUrl            = $this->getParameter('base_url');
        $login_url          = $this->getParameter('login_url');
        $authentication_url = $this->getParameter('authentication_url');
        $oauth_app_url      = $this->getParameter('oauth_app_url');
        $oauth_authorization_url      = $this->getParameter('oauth_authorization_url');

        $user_name          = $this->getParameter('user_name');
        $user_password      = $this->getParameter('user_password');
        $cookie_file        = sys_get_temp_dir()."pmcookie";



        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $authentication_url);
        curl_setopt($ch, CURLOPT_REFERER, $login_url);
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "form[USR_USERNAME]=$user_name&form[USR_PASSWORD]=$user_password&form[USER_LANG]=en&form[URL]");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $answer = curl_exec($ch);
        $newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        //print_r($newurl);
        if (strpos($newurl, "/login/login") !== false) {
            throw new Exception('Bad credentials');
        }

        //print "<textarea>$answer</textarea>";
        if (curl_error($ch)) {
            throw new Exception(curl_error($ch));
        }

        // Read the session saved in the cookie file

        if(!file_exists($cookie_file)){
            throw new Exception('Invalid Cookie/Session: '.$cookie_file);
        }

        //another request preserving the session

        $data = json_decode((string) $data);

        $response_type=$data->response_type;
        $client_id=$data->client_id;
        $scope = $data->scope;
        $implicit_grant_number = $data->implicit_grant_number;


        //1. Register application
        curl_setopt($ch, CURLOPT_URL, $oauth_authorization_url."?response_type=$response_type&client_id=$client_id&scope=$scope");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "transaction_id=");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $answer = curl_exec($ch);
        if (curl_error($ch)) {
            throw new Exception(curl_error($ch));
        }
        $newurl = urldecode(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));


        if (strpos($newurl, "/login/login") !== false) {
            throw new Exception('Not authenticated');
        }
        $parts = parse_url($newurl);

        if (file_exists("session.data")) {
            $sessionData = json_decode(file_get_contents("session.data"));
        } else {
            $sessionData = new StdClass();
        }

        //print_r($fragment);
        // json_decode(json)
        //$response=json_decode($answer);
        parse_str($parts['fragment'], $fragment);
        foreach($fragment as $key => $varValue){
            $sessionVarName=$key."_".$implicit_grant_number;
            $sessionData->$sessionVarName = $varValue;
        }

        //print_r($sessionData);
        file_put_contents("session.data", json_encode($sessionData));
        //print_r("\nRegister application:\n".$answer."\n$oauth_authorization_url?response_type=$response_type&client_id=$client_id&scope=$scope\n");
        //print_r($newurl);
        $this->_restObjectMethod = 'post';
        $this->_headers['Content-Type'] = 'application/json; charset=UTF-8';
        $this->_response = json_decode($answer);


    }

    /**
     * @Given /^that I assign an access token from session variable "([^"]*)"$/
     */
    public function thatIAssignAnAccessTokenFromSessionVariable($varName)
    {
        if (file_exists("session.data")) {
            $sessionData = json_decode(file_get_contents("session.data"));
        } else {
            $sessionData = array();
        }
        if (!isset($sessionData->$varName) ) {
            $varValue = '';
        } else {
            $varValue = $sessionData->$varName;
        }
        $access_token = $varValue;
        if (strlen($access_token)<= 10) {

            throw new Exception ("Access token is not valid\n\n" );
        }
        $this->printDebug("Access token set to: $access_token");
        $this->access_token = $access_token;
    }




}