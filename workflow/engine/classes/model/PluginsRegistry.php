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
     * @param $PR_UID
     * @return array
     * @throws Exception
     */
    public static function load($PR_UID)
    {
        $oPluginsRegistry = PluginsRegistryPeer::retrieveByPK($PR_UID);
        if ($oPluginsRegistry) {
            /** @var array $aFields */
            $aFields = $oPluginsRegistry->toArray(BasePeer::TYPE_FIELDNAME);
            return $aFields;
        } else {
            throw new Exception("User with $PR_UID does not exist!");
        }
    }

    /**
     * @param $PR_UID
     * @return mixed|bool
     */
    public static function exists($PR_UID)
    {
        $oPluginsRegistry = PluginsRegistryPeer::retrieveByPk($PR_UID);
        if ($oPluginsRegistry) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $PR_UID
     * @param array $pluginData
     * @return mixed|array|bool
     */
    public static function loadOrCreateIfNotExists($PR_UID, $pluginData = array())
    {
        if (!self::exists($PR_UID)) {
            $pluginData['PR_UID'] = $PR_UID;
            self::create($pluginData);
        } else {
            $fields = self::load($PR_UID);
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
        $oConnection = Propel::getConnection(UsersPropertiesPeer::DATABASE_NAME);
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
