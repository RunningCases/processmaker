<?php
/**
 * Description of ActionsByEmailFeature
 *
 */

if (!class_exists('enterprisePlugin')) {
    return;
}

// Load dependences
G::LoadClass('feature');

class ActionsByEmailFeature extends PMFeature
{
    protected $triggers;
    protected $classInstance;

    public function __construct($namespace, $filename = null)
    {
        $result               = parent::__construct($namespace, $filename);
        $this->sFriendlyName  = 'Actions By Email';
        $this->sDescription   = 'Actions by Email using variables as multiple choice actions delivered by email';
        $this->sFeatureFolder = 'ActionsByEmail';
        $this->classInstance  = array('filename' => 'class.actionsByEmail.php', 'classname' => 'actionsByEmailClass');
        $this->sSetupPage     = '';
        $this->iVersion       = self::getFeatureVersion($namespace);
        $this->aWorkspaces    = null;
        $this->aDependences   = array(array('sClassName' => 'enterprise'), array('sClassName' => 'pmLicenseManager'));
        $this->triggers       = array();
//        $this->bPrivate       = parent::registerEE($this->sFeatureFolder, $this->iVersion);

        return $result;
    }

    public function setup()
    {
        try {
            if (!defined('PM_CREATE_NEW_DELEGATION')) {
                throw new Exception('It might be using a version of ProcessMaker which is not totally compatible with this plugin, the minimun required version is 2.0.37');
            }
            $this->registerTrigger(PM_CREATE_NEW_DELEGATION, 'sendActionsByEmail');
        } catch (Exception $error) {
            
        }
    }

    public function executeTriggers($triggerId, $data)
    {
        if (PMLicensedFeatures
            ::getSingleton()
            ->verifyfeature('zLhSk5TeEQrNFI2RXFEVktyUGpnczV1WEJNWVp6cjYxbTU3R29mVXVZNWhZQT0=')) {
                $method = $this->triggers[$triggerId];
                require_once PATH_FEATURES. $this->sFeatureFolder . DS .$this->classInstance['filename'];
                $actionsByEmail = new $this->classInstance['classname']();
                $actionsByEmail->$method($data);
        }
    }
    
    public function registerTrigger($triggerId, $method)
    {
        $this->triggers[$triggerId] = $method;
    }
    
    
    public function install()
    {
        $this->checkTables();
    }

    public function enable()
    {
        $this->checkTables();
    }

    public function disable()
    {
        // Nothing to do for now
    }

    /**
     * This method get the version of this plugin, when the plugin is packaged in the tar.gz
     * the file "version" in the plugin folder has this information for development purposes,
     * we calculate the version using git commands, because the repository is in GIT
     *
     * @param String $namespace The namespace of the plugin
     * @return String $version
     */
    private static function getFeatureVersion($namespace)
    {
        return "2.0.20";
    }

    public function checkTables()
    {
        $con = Propel::getConnection('workflow');
        $stmt = $con->createStatement();
        // setting the path of the sql schema files
        $filenameSql = PATH_PLUGINS . 'actionsByEmail/data/schema.sql';

        // checking the existence of the schema file
        if (!file_exists($filenameSql)) {
            throw new Exception("File data/schema.sql doesn't exists");
        }

        // exploding the sql query in an array
        $sql = explode(';', file_get_contents($filenameSql));

        $stmt->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');

        // executing each query stored in the array
        foreach ($sql as $sentence) {
            if (trim($sentence) != '') {
                $stmt->executeQuery($sentence);
            }
        }
    }
}
