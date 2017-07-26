<?php

require_once 'classes/model/om/BasePluginsRegistry.php';


/**
 * Skeleton subclass for representing a row from the 'PLUGINS_REGISTRY' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class PluginsRegistry extends BasePluginsRegistry
{
    /**
     * @return array
     * @throws Exception
     */
    public static function loadPlugins()
    {
        $oCriteria = new Criteria();
        $oDataset = PluginsRegistryPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rows = array();
        while ($oDataset->next()) {
            $rows[] = $oDataset->getRow();
        }
        return $rows;
    }

    /**
     * @param $prUid
     * @return array
     * @throws Exception
     */
    public static function load($prUid)
    {
        $oPluginsRegistry = PluginsRegistryPeer::retrieveByPK($prUid);
        if ($oPluginsRegistry) {
            /** @var array $aFields */
            $aFields = $oPluginsRegistry->toArray(BasePeer::TYPE_FIELDNAME);
            return $aFields;
        } else {
            throw new Exception("Plugin with $prUid does not exist!");
        }
    }

    /**
     * @param $prUid
     * @return mixed|bool
     */
    public static function exists($prUid)
    {
        $oPluginsRegistry = PluginsRegistryPeer::retrieveByPk($prUid);
        if ($oPluginsRegistry) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $prUid
     * @param array $pluginData
     * @return mixed|array|bool
     */
    public static function loadOrCreateIfNotExists($prUid, $pluginData = array())
    {
        if (!self::exists($prUid)) {
            $pluginData['PR_UID'] = $prUid;
            self::create($pluginData);
        } else {
            $fields = self::load($prUid);
            $pluginData = array_merge($fields, $pluginData);
        }
        return $pluginData;
    }

    public static function create($aData)
    {
        $oConnection = Propel::getConnection(PluginsRegistryPeer::DATABASE_NAME);
        try {
            $oPluginsRegistry = new PluginsRegistry();
            $oPluginsRegistry->fromArray($aData, BasePeer::TYPE_FIELDNAME);
            if ($oPluginsRegistry->validate()) {
                $oConnection->begin();
                $oPluginsRegistry->save();
                $oConnection->commit();
                return true;
            } else {
                $sMessage = '';
                $aValidationFailures = $oPluginsRegistry->getValidationFailures();
                /** @var ValidationFailed $oValidationFailure */
                foreach ($aValidationFailures as $oValidationFailure) {
                    $sMessage .= $oValidationFailure->getMessage() . '<br />';
                }
                throw (new Exception('The registry cannot be created!<br />' . $sMessage));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    public static function update($aData)
    {
        $oConnection = Propel::getConnection(PluginsRegistryPeer::DATABASE_NAME);
        try {
            $oPluginsRegistry = PluginsRegistryPeer::retrieveByPK($aData['PR_UID']);
            if ($oPluginsRegistry) {
                $oPluginsRegistry->fromArray($aData, BasePeer::TYPE_FIELDNAME);
                if ($oPluginsRegistry->validate()) {
                    $oConnection->begin();
                    $iResult = $oPluginsRegistry->save();
                    $oConnection->commit();
                    return $iResult;
                } else {
                    $sMessage = '';
                    $aValidationFailures = $oPluginsRegistry->getValidationFailures();
                    /** @var ValidationFailed $oValidationFailure */
                    foreach ($aValidationFailures as $oValidationFailure) {
                        $sMessage .= $oValidationFailure->getMessage() . '<br />';
                    }
                    throw (new Exception('The registry cannot be updated!<br />' . $sMessage));
                }
            } else {
                throw (new Exception('This row doesn\'t exist!'));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }
}
