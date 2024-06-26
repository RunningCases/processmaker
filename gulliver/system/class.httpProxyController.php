<?php

use ProcessMaker\Exception\RBACException;

/**
 * HttpProxyController
 *
 * @package gulliver.system
 * @access private
 */
class HttpProxyController
{

    /**
     *
     * @var array - private array to store proxy data
     */
    private $__data__ = array();

    /**
     *
     * @var object - private object to store the http request data
     */
    private $__request__;

    public $jsonResponse = true;

    private $sendResponse = true;

    public function __construct()
    {
        $this->__request__ = new stdclass();
    }

    /**
     * Magic setter method
     *
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value)
    {
        $this->__data__[$name] = $value;
    }

    /**
     * Magic getter method
     *
     * @param string $name
     * @return string or NULL if the internal var doesn't exist
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->__data__)) {
            return $this->__data__[$name];
        }
    }

    /**
     * Magic isset method
     *
     * @param string $name
     */
    public function __isset($name)
    {
        return isset($this->__data__[$name]);
    }

    /**
     * Magic unset method
     *
     * @param string $name
     */
    public function __unset($name)
    {
        //echo "Unsetting '$name'\n";
        unset($this->__data__[$name]);
    }

    /**
     * call to execute a internal proxy method and handle its exceptions
     *
     * @param string $name
     */
    public function call($name)
    {
        $result = new stdClass();
        try {
            $result = $this->$name($this->__request__);

            if (! $this->jsonResponse) {
                return null;
            }

            if (! $result) {
                $result = $this->__data__;
            }
        } catch (RBACException $e) {
            // If is a RBAC exception bubble up...
            throw $e;
        } catch (Exception $e) {
            $result->success = false;
            $result->message = $result->msg = $e->getMessage();
            switch (get_class($e)) {
                case 'Exception':
                    $error = "SYSTEM ERROR";
                    break;
                case 'PMException':
                    $error = "PROCESSMAKER ERROR";
                    break;
                case 'PropelException':
                    $error = "DATABASE ERROR";
                    break;
                case 'UserException':
                    $error = "USER ERROR";
                    break;
            }
            $result->error = $e->getMessage();
            $result->exception = new stdClass();
            $result->exception->class = get_class($e);
            $result->exception->code = $e->getCode();
            $result->exception->trace = $e->getTraceAsString();
        }

        if ($this->sendResponse) {
            print G::json_encode($result);
        }
    }

    /**
     * Set the http request data
     *
     * @param array $data
     */
    public function setHttpRequestData($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $this->__request__->{$key} = $value;
            }
        } else {
            $this->__request__ = $data;
        }
    }

    public function setJsonResponse($bool)
    {
        $this->jsonResponse = $bool;
    }

    /**
     * Send response to client
     *
     * @param boolean $val
     */
    public function setSendResponse($val)
    {
        $this->sendResponse = $val;
    }
}
