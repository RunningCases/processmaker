<?php
if (!defined("T_ML_COMMENT")) {
    define("T_ML_COMMENT", T_COMMENT);
} else {
    if (!defined("T_DOC_COMMENT")) {
        define("T_DOC_COMMENT", T_ML_COMMENT);
    }
}

class CodeScanner
{
    private $arrayDisabledCode = array();

    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct($option)
    {
        try {
            switch ($option) {
                case "DISABLED_CODE":
                    $this->setArrayDisabledCode();
                    break;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Set disabled code
     *
     * return void
     */
    public function setArrayDisabledCode()
    {
        try {
            //Disabled functions (PHP)
            $disableFunctions = ini_get("disable_functions");

            if ($disableFunctions != "") {
                $this->arrayDisabledCode = array_filter(array_map("trim", explode(",", $disableFunctions)));
            }

            //Disabled code (blacklist)
            $fileDisabledCode = PATH_CONFIG . "blacklist.ini";

            if (file_exists($fileDisabledCode)) {
                $arrayAux = array_filter(array_map("trim", file($fileDisabledCode, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)));
                $arrayAux = array_filter($arrayAux, create_function("\$line", "return !preg_match(\"/^;.*\$/\", \$line);"));

                $this->arrayDisabledCode = array_unique(array_merge($this->arrayDisabledCode, $arrayAux));
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get disabled code
     *
     * return array Return array with disabled code
     */
    public function getArrayDisabledCode()
    {
        try {
            return $this->arrayDisabledCode;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists disabled code
     *
     * return bool Return true if exists disabled code, false otherwise
     */
    public function existsDisabledCode()
    {
        try {
            return count($this->arrayDisabledCode) > 0;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Check disabled code in Source
     *
     * @param string $source Source
     *
     * return array Return array with disabled code found, array empty otherwise
     */
    public function checkDisabledCodeInSource($source)
    {
        try {
            if (!$this->existsDisabledCode()) {
                //Return
                return array();
            }

            if (trim($source) == "") {
                //Return
                return array();
            }

            //Search code
            $arrayFoundCode = array();

            $arrayDisabledTokenAux = array(
                T_COMMENT,                  //// or #, and /* */ //Comments
                T_ML_COMMENT,
                T_DOC_COMMENT,              ///** */             //PHPDoc style comments
                T_VARIABLE,                 //$foo               //Variables
                T_CONSTANT_ENCAPSED_STRING, //"foo" or 'bar'     //String syntax
                T_DOUBLE_ARROW,             //=>                 //Array syntax
                T_OBJECT_OPERATOR           //->                 //Classes and objects
            );

            $arrayToken = token_get_all("<?php\n" . $source);

            foreach ($arrayToken as $value) {
                $token = $value;

                if (is_array($token)) {
                    list($id, $text, $lineNumber) = $token;

                    if (!in_array($id, $arrayDisabledTokenAux)) {
                        foreach ($this->arrayDisabledCode as $value2) {
                            $code = $value2;

                            if (preg_match("/^" . $code . "$/i", trim($text))) {
                                $arrayFoundCode[$code][$lineNumber - 1] = $lineNumber - 1;
                                break;
                            }
                        }
                    }
                }
            }

            ksort($arrayFoundCode);

            //Return
            return $arrayFoundCode;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Check disabled code
     *
     * @param string $option Option (SOURCE, PATH, FILE)
     * @param string $data   Data
     *
     * return array Return array with disabled code found, array empty otherwise
     */
    public function checkDisabledCode($option, $data)
    {
        try {
            if (!$this->existsDisabledCode()) {
                //Return
                return array();
            }

            //Search code
            $arrayFoundCode = array();

            switch ($option) {
                case "SOURCE":
                    $source = $data;

                    $arrayAux = $this->checkDisabledCodeInSource($source);

                    if (count($arrayAux) > 0) {
                        $arrayFoundCode["source"] = $arrayAux;
                    }
                    break;
                case "PATH":
                case "FILE":
                    $path = $data;

                    if (is_dir($path)) {
                        if ($dirh = opendir($path)) {
                            while (($file = readdir($dirh)) !== false) {
                                if ($file != "" && $file != "." && $file != "..") {
                                    $f = $path . PATH_SEP . $file;

                                    if (is_dir($f) || (is_file($f) && preg_match("/\.php$/", $f))) {
                                        $arrayFoundCode = array_merge($arrayFoundCode, $this->checkDisabledCode((is_dir($f))? "PATH" : "FILE", $f));
                                    }
                                }
                            }

                            closedir($dirh);
                        }
                    } else {
                        if (is_file($path) && preg_match("/\.php$/", $path)) {
                            $source = file_get_contents($path);

                            $arrayAux = $this->checkDisabledCodeInSource($source);

                            if (count($arrayAux) > 0) {
                                $arrayFoundCode[$path] = $arrayAux;
                            }
                        }
                    }
                    break;
            }

            //Return
            return $arrayFoundCode;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
