<?php

class featuresDetail
{
    public $featureName;
    public $description = null;
    public $enabled = false;
    public $workspaces = null;

    /**
     * This function is the constructor of the featuresDetail class
     *
     * @param string $featureName
     * @param string $name
     * @param string $description
     * @return void
     */
    public function __construct ($featureName, $description = '')
    {
        $this->featureName = $featureName;
        $this->description = $description;
    }
}


class PMLicensedFeatures
{
    private $featuresDetails = array ();
    private $features = array ();

    private static $instancefeature = null;

    /**
     * This function is the constructor of the PMLicensedFeatures class
     * param
     *
     * @return void
     */
    public function __construct ()
    {
        $criteria = new Criteria();
        $criteria->addAscendingOrderByColumn(AddonsManagerPeer::ADDON_ID);
        $criteria->add(AddonsManagerPeer::ADDON_TYPE, 'feature', Criteria::EQUAL);
        $addons = AddonsManagerPeer::doSelect($criteria);
        foreach ($addons as $addon) {
            $this->features[] = $addon->getAddonId();
            $detail = new featuresDetail($addon->getAddonNick(), $addon->getAddonDescription());
            $this->featuresDetails[$addon->getAddonId()] = $detail;
        }
    }

    /**
     * This function is instancing to this class
     * param
     *
     * @return object
     */
    public static function getSingleton ()
    {
        if (self::$instancefeature == null) {
            self::$instancefeature = new PMLicensedFeatures();
        }
        return self::$instancefeature;
    }

    public function verifyfeature ($featureName)
    {
        $licenseManager = &pmLicenseManager::getSingleton();
        $_SESSION['__sw__'] = true;
        $padl = new padl();

        $enable = in_array($padl->_decrypt($featureName), $licenseManager->features);

        $this->featuresDetails[$padl->_decrypt($featureName)]->enabled = $enable;
        return $enable;
    }

    public static function loadSingleton($file)
    {
        self::$instancefeature = unserialize(file_get_contents($file));

        if (! is_object(self::$instancefeature) || get_class(self::$instancefeature) != "PMLicensedFeatures") {
            throw new Exception("Can't load main PMLicensedFeatures object.");
        }

        return self::$instancefeature;
    }
}

