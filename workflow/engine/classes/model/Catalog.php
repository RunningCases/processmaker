<?php

require_once 'classes/model/om/BaseCatalog.php';


/**
 * Skeleton subclass for representing a row from the 'CATALOG' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class Catalog extends BaseCatalog 
{
	public function load ($catUid, $catType)
    {
        try {
            $catalog = CatalogPeer::retrieveByPK($catUid, $catType);
            $fields = $catalog->toArray(BasePeer::TYPE_FIELDNAME);
            $catalog->fromArray( $fields, BasePeer::TYPE_FIELDNAME );
            return $fields;
        } catch (Exception $error) {
            throw $error;
        }
    }

    public function createOrUpdate($data)
    {
        $connection = Propel::getConnection(CatalogPeer::DATABASE_NAME);
        try {
            if (!isset($data['CAT_UID'])) {
                $data['CAT_CREATE_DATE'] = date('Y-m-d H:i:s');
                $msg = "Create Catalog";
                $catalog = new catalog();
            } else {
                $msg = "Update Catalog";
                $catalog = CatalogPeer::retrieveByPK($data['CAT_UID']);
            }
            $data['CAT_UPDATE_DATE'] = date('Y-m-d H:i:s');
            $catalog->fromArray($data, BasePeer::TYPE_FIELDNAME);
            if ($catalog->validate()) {
                $connection->begin();
                $result = $catalog->save();
                $connection->commit();

                G::auditLog($msg, "Catalog ID Label: ".$catalog->getCatLabelId()." Catalog  type: (".$catalog->getCatType().") ");
                return $catalog->getCatLabelId();
            } else {
                $message = '';
                $validationFailures = $catalog->getValidationFailures();
                foreach ($validationFailures as $validationFailure) {
                    $message .= $validationFailure->getMessage() . '. ';
                }
                throw(new Exception(G::LoadTranslation("ID_RECORD_CANNOT_BE_CREATED", SYS_LANG) . ' ' . $message));
            }
        } catch (Exception $error) {
            $connection->rollback();
            throw $error;
        }
    }

    public function remove($catUid, $catType)
    {
        $connection = Propel::getConnection(CatalogPeer::DATABASE_NAME);
        try {
            $catalog = CatalogPeer::retrieveByPK($catUid, $catType);
            if (!is_null($catalog)) {
                $connection->begin();
                $catalogData = $this->load($dasUid);
                $result = $catalog->delete();
                $connection->commit();

                G::auditLog("Deletecatalog", "Catalog Id Label: ". $catalogData['CAT_UID']." Catalog Type: (". $catalogData['CAT_TYPE'] .") ");
                return $result;
            } else {
                throw new Exception('Error trying to delete: The row "' .  $catalogData['CAT_UID']. '" does not exist.');
            }
        } catch (Exception $error) {
            $connection->rollback();
            throw $error;
        }
    }

    public function loadByType ($catType)
    {
        try {
            $criteria = new Criteria();
            $criteria->clearSelectColumns();
            $criteria->add(CatalogPeer::CAT_TYPE, strtoupper($catType), Criteria::EQUAL);

            $rs = CatalogPeer::doSelectRS($criteria);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $catalog = array();

            while ($rs->next()) {
                $row = $rs->getRow();
                $row['CAT_LABEL_ID'] = G::loadTranslation($row['CAT_LABEL_ID']);
                $catalog[] = $row;
            }

            return $catalog;
        } catch (Exception $error) {
            throw $error;
        }
    }    
}

