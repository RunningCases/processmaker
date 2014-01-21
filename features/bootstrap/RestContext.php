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
            return (isset($parameters[$name])) ? $parameters[$name] : null;
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
    public function iRequest($pageUrl)
    {
        $this->_startTime = microtime(true);
        $baseUrl = $this->getParameter('base_url');
        if ($this->access_token != null) {
            $this->_headers['Authorization'] = 'Bearer ' . $this->access_token;
        }

        $this->_requestUrl = $baseUrl . $pageUrl;
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
                        $message = 'malformed JSON';
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
            throw new Exception("Response Content Type was not $contentType\n\n");
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
        $this->_restObjectMethod = 'post';
        $this->_headers['Content-Type'] = 'application/json; charset=UTF-8';
        $this->_requestBody = $string;
    }

    /**
     * @Given /^PUT this data:$/
     */
    public function putThisData(PyStringNode $string)
    {
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
     */
    public function theResponseHasRecords($quantityOfRecords)
    {
        $data = $this->_data;
        if (!is_array($data)) {
            throw new Exception("the Response data is not an array!\n\n" );
        }
        $currentRecordsCount=count($data);
        if($currentRecordsCount!=$quantityOfRecords){
            throw new Exception('Records quantity not match ' . $quantityOfRecords .               ' (actual: ' . $currentRecordsCount . ")\n\n");
        }
    }

    /**
     * @Given /^that I want to update a resource with the key "([^"]*)" stored in session array as variable "([^"]*)"$/
     */
    public function thatIWantToUpdateAResourceWithTheKeyStoredInSessionArrayAsVariable($varName, $sessionVarName)
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

        $this->_restUpdateQueryStringSuffix = "/" . $varValue;
        $this->_restObjectMethod = 'put';
    }

     /**
     * @Given /^that I want to get a resource with the key "([^"]*)" stored in session array as variable "([^"]*)"$/
     */
    public function thatIWantToGetAResourceWithTheKeyStoredInSessionArrayAsVariable($varName, $sessionVarName)
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

        $this->_restGetQueryStringSuffix = "/" . $varValue;
        $this->_restObjectMethod = 'get';
    }

    /**
     * @Given /^that I want to delete a resource with the key "([^"]*)" stored in session array as variable "([^"]*)"$/
     */
    public function thatIWantToDeleteAResourceWithTheKeyStoredInSessionArrayAsVariable($varName, $sessionVarName)
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

        $this->_restDeleteQueryStringSuffix = "/" . $varValue;
        $this->_restObjectMethod = 'delete';
    }

     /**
     * @Given /^the response status message should have the following text "([^"]*)"$/
     */
    public function theResponseStatusMessageShouldHaveTheFollowingText($arg1)
    {
        
            $message="";
            if($bodyResponse=json_decode($this->_response->getBody(true))){
                if(isset($bodyResponse->error->message)){
                    $message = $bodyResponse->error->message;
                    if (strpos($message,$arg1) === false) {
                        throw new \Exception("Error message text does not have: '" . $arg1 ."'' (actual: '$message')\n\n");
                    }


                }else{
                    throw new \Exception('This is not a valid error response');

                }

            }else{
                throw new \Exception('This is not a valid response');

            }

            
        
    }



}
