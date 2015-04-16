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
    private $newFeatures = array(
            0 => array(
                "description" => "Enables de Actions By Email feature",
                "enabled" => false,
                "id" => "actionsByEmail",
                "latest_version" => "",
                "log" => null,
                "name" => "actionsByEmail",
                "nick" => "actionsByEmail",
                "progress" => 0,
                "publisher" => "Colosa",
                "release_type" => "localRegistry",
                "status" => "ready",
                "store" => "00000000000000000000000000010004",
                "type" => "features",
                "url" => "",
                "version" => ""
            ),
            1 => array(
                "description" => "Enables de Batch Routing feature",
                "enabled" => false,
                "id" => "pmConsolidatedCL",
                "latest_version" => "",
                "log" => null,
                "name" => "pmConsolidatedCL",
                "nick" => "pmConsolidatedCL",
                "progress" => 0,
                "publisher" => "Colosa",
                "release_type" => "localRegistry",
                "status" => "ready",
                "store" => "00000000000000000000000000010005",
                "type" => "features",
                "url" => "",
                "version" => ""
            ),
            2 => array(
                "description" => "Dashboard with improved charting graphics and optimized to show strategic information like Process Efficiency and User Efficiency indicators.",
                "enabled" => false,
                "id" => "strategicDashboards",
                "latest_version" => "",
                "log" => null,
                "name" => "strategicDashboards",
                "nick" => "Strategic Dashboards",
                "progress" => 0,
                "publisher" => "Colosa",
                "release_type" => "localRegistry",
                "status" => "ready",
                "store" => "00000000000000000000000000010006",
                "type" => "features",
                "url" => "",
                "version" => ""
            )
        );

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
    /*----------------------------------********---------------------------------*/
    public function verifyfeature ($featureName)
    {
        if (!class_exists("pmLicenseManager")) {
            require_once ("classes" . PATH_SEP . "class.pmLicenseManager.php");
        }

        $licenseManager = pmLicenseManager::getSingleton(false);

        $_SESSION['__sw__'] = true;
        $padl = new padl();
        $value = $padl->_decrypt($featureName);

        if (is_array($value)) {
            $value = $value[0];
        }
        $trueValue = $value;
        $enable = in_array($trueValue, $licenseManager->licensedfeatures);

        if (!isset($this->featuresDetails[$value[0]]) || !is_object($this->featuresDetails[$value[0]])) {
            $this->featuresDetails[$value[0]] = new stdclass();
        }
        $this->featuresDetails[$value[0]]->enabled = $enable;
        return $enable;
    }
    
    public function addNewFeatures ($data)
    {
        $newFeaturesList = $this->newFeatures;
        $newData = array();
        $newFeaturesIds = array();
        foreach($newFeaturesList as $val) {
            $newFeaturesIds[] = $val['id'];
        }
        $criteria = new Criteria();
        $criteria->addSelectColumn(AddonsManagerPeer::ADDON_ID);
        $criteria->add(AddonsManagerPeer::ADDON_ID, $newFeaturesIds, Criteria::IN);
        $criteria->add(AddonsManagerPeer::ADDON_TYPE, 'features');
        $rs = AddonsManagerPeer::doSelectRS($criteria);
        $rs->next();
        $row = $rs->getRow();
        if(sizeof($row)) {  
            while (is_array($row)) {
                $ids[] = $row[0];
                $rs->next();
                $row = $rs->getRow();
            } 
            $toUpdate = array_diff($newFeaturesIds,$ids);
            
            if(sizeof($toUpdate)){
                $newFeaturesListAux = array();
                foreach($toUpdate as $index => $v) {
                    $newFeaturesListAux[] = $newFeaturesList[$index];
                }
                unset($newFeaturesList);
                $newFeaturesList = array_values($newFeaturesListAux);
            } else {
                return $data;    
            }
        }
        
        foreach($newFeaturesList as $k => $newFeature){
            $newData[] = array (
                    'db' => 'wf',
                    'table' => 'ADDONS_MANAGER',
                    'keys' =>
                        array (
                        0 => 'ADDON_ID',
                        ),
                    'data' =>
                        array (
                        0 =>
                        array (
                        'field' => 'ADDON_DESCRIPTION',
                        'type' => 'text',
                        'value' => $newFeature['description'],
                        ),
                        1 =>
                        array (
                        'field' => 'ADDON_ID',
                        'type' => 'text',
                        'value' => $newFeature['id'],
                        ),
                        2 =>
                        array (
                        'field' => 'ADDON_NAME',
                        'type' => 'text',
                        'value' => $newFeature['name'],
                        ),
                        3 =>
                        array (
                        'field' => 'ADDON_NICK',
                        'type' => 'text',
                        'value' => $newFeature['nick'],
                        ),
                        4 =>
                        array (
                        'field' => 'ADDON_PUBLISHER',
                        'type' => 'text',
                        'value' => $newFeature['publisher'],
                        ),
                        5 =>
                        array (
                        'field' => 'ADDON_RELEASE_TYPE',
                        'type' => 'text',
                        'value' => $newFeature['release_type'],
                        ),
                        6 =>
                        array (
                        'field' => 'ADDON_STATUS',
                        'type' => 'text',
                        'value' => $newFeature['status'],
                        ),
                        7 =>
                        array (
                        'field' => 'STORE_ID',
                        'type' => 'text',
                        'value' => $newFeature['store'],
                        ),
                        8 =>
                        array (
                        'field' => 'ADDON_TYPE',
                        'type' => 'text',
                        'value' => $newFeature['type'],
                        ),
                        9 =>
                        array (
                        'field' => 'ADDON_DOWNLOAD_URL',
                        'type' => 'text',
                        'value' => $newFeature['url'],
                        ),
                        10 =>
                        array (
                        'field' => 'ADDON_VERSION',
                        'type' => 'text',
                        'value' => $newFeature['version'],
                        ),
                        11 =>
                        array (
                        'field' => 'ADDON_DOWNLOAD_PROGRESS',
                        'type' => 'text',
                        'value' => $newFeature['progress'],
                        )
                        ),
                    'action' => 1,
                    );
               
            $i++;       
        }
        return array_merge($data, $newData);
    }
    /*----------------------------------********---------------------------------*/
}

