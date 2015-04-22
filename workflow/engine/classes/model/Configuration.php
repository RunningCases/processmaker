<?php
class Configuration extends BaseConfiguration
{
    public function create($aData)
    {
        $con = Propel::getConnection(ConfigurationPeer::DATABASE_NAME);
        try {
            $con->begin();
            $this->setCfgUid($aData['CFG_UID']);
            $this->setObjUid($aData['OBJ_UID']);
            $this->setCfgValue(isset($aData['CFG_VALUE'])?$aData['CFG_VALUE']:'');
            $this->setProUid($aData['PRO_UID']);
            $this->setUsrUid($aData['USR_UID']);
            $this->setAppUid($aData['APP_UID']);
            if ($this->validate()) {
                $result=$this->save();
                $con->commit();
                return $result;
            } else {
                $con->rollback();
                throw(new Exception("Failed Validation in class ".get_class($this)."."));
            }
        } catch (Exception $e) {
            $con->rollback();
            throw($e);
        }
    }

    public function load($CfgUid, $ObjUid = '', $ProUid = '', $UsrUid = '', $AppUid = '')
    {
        try {
            $oRow = ConfigurationPeer::retrieveByPK( $CfgUid, $ObjUid, $ProUid, $UsrUid, $AppUid );
            if (!is_null($oRow)) {
                $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
                $this->fromArray($aFields,BasePeer::TYPE_FIELDNAME);
                $this->setNew(false);
                return $aFields;
            } else {
                throw(new Exception( "The row '$CfgUid, $ObjUid, $ProUid, $UsrUid, $AppUid' in table Configuration doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw($oError);
        }
    }

    public function update($fields)
    {
        $con = Propel::getConnection(ConfigurationPeer::DATABASE_NAME);
        try {
            $con->begin();
            $this->load($fields['CFG_UID'], $fields['OBJ_UID'], $fields['PRO_UID'], $fields['USR_UID'], $fields['APP_UID']);
            $this->fromArray($fields,BasePeer::TYPE_FIELDNAME);
            if ($this->validate()) {
                $contentResult=0;
                $result=$this->save();
                $result=($result==0)?($contentResult>0?1:0):$result;
                $con->commit();
                return $result;
            } else {
                $con->rollback();
                throw(new Exception("Failed Validation in class ".get_class($this)."."));
            }
        } catch (Exception $e) {
            $con->rollback();
            throw($e);
        }
    }

    public function remove($CfgUid, $ObjUid, $ProUid, $UsrUid, $AppUid)
    {
        $con = Propel::getConnection(ConfigurationPeer::DATABASE_NAME);
        try {
            $con->begin();
            $this->setCfgUid($CfgUid);
            $this->setObjUid($ObjUid);
            $this->setProUid($ProUid);
            $this->setUsrUid($UsrUid);
            $this->setAppUid($AppUid);
            $result=$this->delete();
            $con->commit();
            return $result;
        } catch (Exception $e) {
            $con->rollback();
            throw($e);
        }
    }

    /**
    * To check if the configuration row exists, by using Configuration Uid data
    */
    public function exists($CfgUid, $ObjUid = "", $ProUid = "", $UsrUid = "", $AppUid = "")
    {
        $oRow = ConfigurationPeer::retrieveByPK( $CfgUid, $ObjUid, $ProUid, $UsrUid, $AppUid );
        return (( get_class ($oRow) == 'Configuration' )&&(!is_null($oRow)));
    }

    public function getAll ()
    {
        $oCriteria = new Criteria( 'workflow' );

        $oCriteria->addSelectColumn( ConfigurationPeer::CFG_UID );
        $oCriteria->addSelectColumn( ConfigurationPeer::OBJ_UID );
        $oCriteria->addSelectColumn( ConfigurationPeer::CFG_VALUE );
        $oCriteria->addSelectColumn( ConfigurationPeer::PRO_UID );
        $oCriteria->addSelectColumn( ConfigurationPeer::USR_UID );
        $oCriteria->addSelectColumn( ConfigurationPeer::APP_UID );

        //execute the query
        $oDataset = ConfigurationPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $aRows = array ();
        while ($oDataset->next()) {
            $aRows[] = $oDataset->getRow();
        }
        return $aRows;
    }
}

