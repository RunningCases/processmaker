<?php
namespace ProcessMaker\Core;

class ClassLoader
{
    private static $includePath;
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
     * @return \ProcessMaker\Core\ClassLoader
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

    public function add($namespace, $sourceDir)
    {
        if (empty($namespace)) {
            self::$includePath[] = $sourceDir . (substr($sourceDir, -1) == DS ? "" : DS);
        } else {
            self::$includePath[$namespace] = $sourceDir . (substr($sourceDir, -1) == DS ? "" : DS);
        }
    }

    function loadClass($className)
    {
        var_dump(self::$includePath); die;
        $className = ltrim($className, NS);

        foreach (self::$includePath as $path) {
            if ($lastPos = strrpos($className, NS)) {
                $namespace = substr($className, 0, $lastPos);
                var_dump($namespace);
                $className = substr($className, $lastPos + 1);
                $subpath  = str_replace(NS, DS, $namespace) . DS;
            }


            $filename = $path . $subpath . $className . ".php";
            var_dump($filename);

            if (file_exists($filename)) {
                require $filename . ".php";
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