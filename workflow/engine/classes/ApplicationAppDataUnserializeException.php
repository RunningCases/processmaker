<?php
/**
 * Invalid search text for Solr exception
 *
 * @author Herbert Saal Gutierrez
 *        
 */

/**
 * Application APP_DATA could not be unserialized exception
 *
 * @category Colosa
 * @copyright Copyright (c) 2005-2012 Colosa Inc. (http://www.colosa.com)
 */
class ApplicationAppDataUnserializeException extends Exception
{
  // Redefine the exception so message isn't optional
  public function __construct($message, $code = 0)
  {
    // some code
    // make sure everything is assigned properly
    parent::__construct ($message, $code);
  }

  // custom string representation of object
  public function __toString()
  {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }
}
