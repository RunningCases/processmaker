<?php
namespace Maveriks\Util;

class ClassLoader
{
    private static $includePath = array();
    private static $includePathNs = array();
    protected static $instance;

    /**
     * Creates a new <tt>SplClassLoader</tt> that loads classes of the
     * specified namespace.
     *
     * @param string $ns The namespace to use.
     */
    public function __construct()
    {
        defined("DS") || define("DS", DIRECTORY_SEPARATOR);
        defined("NS") || define("NS", "\\");

        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * @return \Maveriks\Util\ClassLoader
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Gets the base include path for all class files in the namespace of this class loader.
     *
     * @return string $includePath
     */
    public function getIncludePaths()
    {
        return self::$includePath;
    }

    /**
     * Uninstalls this class loader from the SPL autoloader stack.
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    public function add($sourceDir, $namespace = "")
    {
        if (empty($namespace)) {
            self::$includePath[] = $sourceDir . (substr($sourceDir, -1) == DS ? "" : DS);
        } else {
            self::$includePathNs[$namespace] = $sourceDir . (substr($sourceDir, -1) == DS ? "" : DS);
        }
    }

    function loadClass($className)
    {
        $classPath  = str_replace(NS, DS, $className);

        if (false !== strpos($className, NS)) {// has namespace?
            $lastPos = strpos($className, NS);
            $mainNs = substr($className, 0, $lastPos);

            if (isset(self::$includePathNs[$mainNs])) {
                if (file_exists(self::$includePathNs[$mainNs] . $classPath . ".php")) {
                    require self::$includePathNs[$mainNs] . $classPath . ".php";
                    return true;
                } else {
                    return false;
                }
            }
        }

        foreach (self::$includePath as $path) {
            $filename = $path . $classPath . ".php";
            //var_dump($filename);

            if (file_exists($filename)) {
                require $filename;
                return true;
            }
        }

        return false;
    }

    /**
     * Loads the given class or interface.
     *
     * @param string $className The name of the class to load.
     * @return void
     */
    public function loadClass2($className)
    {
        if (null === $this->_namespace || $this->_namespace.$this->_namespaceSeparator === substr($className, 0, strlen($this->_namespace.$this->_namespaceSeparator))) {
            $fileName = '';
            $namespace = '';

            if (false !== ($lastNsPos = strripos($className, $this->_namespaceSeparator))) {
                $namespace = substr($className, 0, $lastNsPos);
                $className = substr($className, $lastNsPos + 1);
                $fileName = str_replace($this->_namespaceSeparator, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }

            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . $this->_fileExtension;

            require ($this->_includePath !== null ? $this->_includePath . DIRECTORY_SEPARATOR : '') . $fileName;
        }
    }
}