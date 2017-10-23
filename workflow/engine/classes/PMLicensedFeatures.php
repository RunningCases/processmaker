<?php
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class PMLicensedFeatures
{

    private $featuresDetails = array ();
    private $features = array ();
    private $newFeatures = array(
            0 => array(
                "description" => "Actions By Email allows users to enter information and route cases via email.",
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
                "description" => "Batch Routing allows a user to route multiple cases at a time onto the next task in the process. ",
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
            ),
            3 => array(
                "description" => "Enables the configuration of a second database connection in order to divide the database requests in read and write operations. This features is used with database clusters to improve the application performance.",
                "enabled" => false,
                "id" => "secondDatabaseConnection",
                "latest_version" => "",
                "log" => null,
                "name" => "secondDatabaseConnection",
                "nick" => "secondDatabaseConnection",
                "progress" => 0,
                "publisher" => "Colosa",
                "release_type" => "localRegistry",
                "status" => "ready",
                "store" => "00000000000000000000000000010000",
                "type" => "features",
                "url" => "",
                "version" => ""
            ),
            4 => array(
                "description" => "Registers every administrator action in a log. Any change in the settings under the Admin menu is registered in the log.",
                "enabled" => false,
                "id" => "auditLog",
                "latest_version" => "",
                "log" => null,
                "name" => "auditLog",
                "nick" => "auditLog",
                "progress" => 0,
                "publisher" => "Colosa",
                "release_type" => "localRegistry",
                "status" => "ready",
                "store" => "00000000000000000000000000010001",
                "type" => "features",
                "url" => "",
                "version" => ""
            ),
            5 => array(
                "description" => "A more secure option to store user passwords in ProcessMaker. The modern algorithm SHA-2 is used to store the passwords.",
                "enabled" => false,
                "id" => "secureUserPasswordHash",
                "latest_version" => "",
                "log" => null,
                "name" => "secureUserPasswordHash",
                "nick" => "secureUserPasswordHash",
                "progress" => 0,
                "publisher" => "Colosa",
                "release_type" => "localRegistry",
                "status" => "ready",
                "store" => "00000000000000000000000000010002",
                "type" => "features",
                "url" => "",
                "version" => ""
            ),
            6 => array(
                "description" => "This features allows ProcessMaker to send out notifications using different email accounts and email servers.",
                "enabled" => false,
                "id" => "sendEmailFromDifferentEmailServers",
                "latest_version" => "",
                "log" => null,
                "name" => "sendEmailFromDifferentEmailServers",
                "nick" => "sendEmailFromDifferentEmailServers",
                "progress" => 0,
                "publisher" => "Colosa",
                "release_type" => "localRegistry",
                "status" => "ready",
                "store" => "00000000000000000000000000010003",
                "type" => "features",
                "url" => "",
                "version" => ""
            ),
            7 => array(
                "description"    => "Enables the code scanner feature.",
                "enabled"        => false,
                "id"             => "codeScanner",
                "latest_version" => "",
                "log"            => null,
                "name"           => "codeScanner",
                "nick"           => "codeScanner",
                "progress"       => 0,
                "publisher"      => "Colosa",
                "release_type"   => "localRegistry",
                "status"         => "ready",
                "store"          => "00000000000000000000000000010007",
                "type"           => "features",
                "url"            => "",
                "version"        => ""
            ),
            8 => array(
                "description"    => "Enables the multiple email configuration feature.",
                "enabled"        => false,
                "id"             => "multipleEmailServers",
                "latest_version" => "",
                "log"            => null,
                "name"           => "multipleEmailServers",
                "nick"           => "multipleEmailServers",
                "progress"       => 0,
                "publisher"      => "Colosa",
                "release_type"   => "localRegistry",
                "status"         => "ready",
                "store"          => "00000000000000000000000000010009",
                "type"           => "features",
                "url"            => "",
                "version"        => ""
            ),
            9 => array(
                "description"    => "Enables the mobile fields.",
                "enabled"        => false,
                "id"             => "mobileFields",
                "latest_version" => "",
                "log"            => null,
                "name"           => "mobileFields",
                "nick"           => "mobileFields",
                "progress"       => 0,
                "publisher"      => "Colosa",
                "release_type"   => "localRegistry",
                "status"         => "ready",
                "store"          => "00000000000000000000000000010008",
                "type"           => "features",
                "url"            => "",
                "version"        => ""
            ),
            10 => array(
                "description"    => "This plugin will synchronize ProcessMaker with an LDAP or Active Directory server for user authentication.",
                "enabled"        => false,
                "id"             => "ldapAdvanced",
                "latest_version" => "",
                "log"            => null,
                "name"           => "ldapAdvanced",
                "nick"           => "ldapAdvanced",
                "progress"       => 0,
                "publisher"      => "Colosa",
                "release_type"   => "localRegistry",
                "status"         => "ready",
                "store"          => "00000000000000000000000000010010",
                "type"           => "features",
                "url"            => "",
                "version"        => ""
            ),
            11 => array(
                "description"    => "SSO with an LDAP provider.",
                "enabled"        => false,
                "id"             => "windowsSSO",
                "latest_version" => "",
                "log"            => null,
                "name"           => "windowsSSO",
                "nick"           => "windowsSSO",
                "progress"       => 0,
                "publisher"      => "Colosa",
                "release_type"   => "localRegistry",
                "status"         => "ready",
                "store"          => "00000000000000000000000000010011",
                "type"           => "features",
                "url"            => "",
                "version"        => ""
            ),
            12 => array(
                "description"    => "Integration with Gmail.",
                "enabled"        => false,
                "id"             => "pmGmail",
                "latest_version" => "",
                "log"            => null,
                "name"           => "pmGmail",
                "nick"           => "pmGmail",
                "progress"       => 0,
                "publisher"      => "Colosa",
                "release_type"   => "localRegistry",
                "status"         => "ready",
                "store"          => "00000000000000000000000000010012",
                "type"           => "features",
                "url"            => "",
                "version"        => ""
            ),
            13 => array(
                "description"    => "User-based Language Management.",
                "enabled"        => false,
                "id"             => "userBasedLanguage",
                "latest_version" => "",
                "log"            => null,
                "name"           => "userBasedLanguage",
                "nick"           => "userBasedLanguage",
                "progress"       => 0,
                "publisher"      => "Colosa",
                "release_type"   => "localRegistry",
                "status"         => "ready",
                "store"          => "00000000000000000000000000010013",
                "type"           => "features",
                "url"            => "",
                "version"        => ""
            ),
            14 => array(
                "description"    => "User-based Time Zone Management.",
                "enabled"        => false,
                "id"             => "userBasedTimeZone",
                "latest_version" => "",
                "log"            => null,
                "name"           => "userBasedTimeZone",
                "nick"           => "userBasedTimeZone",
                "progress"       => 0,
                "publisher"      => "Colosa",
                "release_type"   => "localRegistry",
                "status"         => "ready",
                "store"          => "00000000000000000000000000010014",
                "type"           => "features",
                "url"            => "",
                "version"        => ""
            ),
            15 => array(
                "description" => "This Feature will allow to store all input, output and attached documents generated
                 in your processes in Google Drive.",
                "enabled" => false,
                "id" => "pmGoogleDrive",
                "latest_version" => "",
                "log" => null,
                "name" => "pmGoogleDrive",
                "nick" => "pmGoogleDrive",
                "progress" => 0,
                "publisher" => "Colosa",
                "release_type" => "localRegistry",
                "status" => "ready",
                "store" => "00000000000000000000000000010015",
                "type" => "features",
                "url" => "",
                "version" => ""
            ),
            16 => array(
                "description" => "Promotion Manager",
                "enabled" => false,
                "id" => "selectiveImportExport",
                "latest_version" => "",
                "log" => null,
                "name" => "selectiveImportExport",
                "nick" => "selectiveImportExport",
                "progress" => 0,
                "publisher" => "Colosa",
                "release_type" => "localRegistry",
                "status" => "ready",
                "store" => "00000000000000000000000000010016",
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
            $detail = new FeaturesDetail($addon->getAddonNick(), $addon->getAddonDescription());
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
        $cached = Cache::get(PmLicenseManager::CACHE_KEY . '.' . config("system.workspace"), []);
        if (isset($cached[$featureName])) {
            return $cached[$featureName];
        }

        $licenseManager = PmLicenseManager::getSingleton(false);

        $_SESSION['__sw__'] = true;
        $padl = new Padl();
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

        $cached[$featureName] = $enable;
        Cache::put(PmLicenseManager::CACHE_KEY . '.' . config("system.workspace"), $cached, Carbon::now()->addDay(1));

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
        $ids = array();
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

        $i = 0;
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
