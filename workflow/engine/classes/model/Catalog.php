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
    private $records = array();

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
    private function dataCatalog ()
    {
        $this->records[] = array('10','ID_BARS','GRAPHIC','','','2015-03-04','2015-03-04');
        $this->records[] = array('20','ID_LINES','GRAPHIC','','','2015-03-04','2015-03-04');
        $this->records[] = array('100','ID_MONTH','PERIODICITY','','','2015-03-04','2015-03-04');
        $this->records[] = array('200','ID_QUARTER','PERIODICITY','','','2015-03-04','2015-03-04');
        $this->records[] = array('300','ID_SEMESTER','PERIODICITY','','','2015-03-04','2015-03-04');
        $this->records[] = array('400','ID_YEAR','PERIODICITY','','','2015-03-04','2015-03-04');
        $this->records[] = array('1010','ID_PROCESS_EFFICIENCE','INDICATOR','','','2015-03-04','2015-03-04');
        $this->records[] = array('1030','ID_EMPLYEE_EFFICIENCIE','INDICATOR','','','2015-03-04','2015-03-04');
        $this->records[] = array('1050','ID_OVER_DUE','INDICATOR','%','Unit for displaying','2015-03-04','2015-03-04');
    }
    public function registerRows($data)
    {
        $this->dataCatalog();
        $newData = array();

        $criteria = new Criteria();
        $criteria->clearSelectColumns();
        $criteria->addSelectColumn(CatalogPeer::CAT_UID);
        $criteria->addSelectColumn(CatalogPeer::CAT_TYPE);
        $rs = CatalogPeer::doSelectRS($criteria);
        $dataCatalog  = array();
        while ($rs->next()) {
            $row = $rs->getRow();
            $dataCatalog[] = $row;
        }

        foreach($this->records as $k => $record) {
            $flag = false;

            foreach ($dataCatalog as $key => $catalog) {
                if ($record[0] == $catalog[0] && $record[2] == $catalog[1]) {
                    $flag = true;
                    break;
                }
            }
            if ($flag) {
                continue;
            }
            $newData[] = array (
                'db' => 'wf',
                'table' => 'CATALOG',
                'keys' =>
                    array (
                        0 => 'CAT_UID',
                        1 => 'CAT_TYPE'
                    ),
                'data' =>
                    array (
                        0 =>
                            array (
                                'field' => 'CAT_UID',
                                'type' => 'text',
                                'value' => $record[0],
                            ),
                        1 =>
                            array (
                                'field' => 'CAT_LABEL_ID',
                                'type' => 'text',
                                'value' => $record[1],
                            ),
                        2 =>
                            array (
                                'field' => 'CAT_TYPE',
                                'type' => 'text',
                                'value' => $record[2],
                            ),
                        3 =>
                            array (
                                'field' => 'CAT_FLAG',
                                'type' => 'text',
                                'value' => $record[3],
                            ),
                        4 =>
                            array (
                                'field' => 'CAT_OBSERVATION',
                                'type' => 'text',
                                'value' => $record[4],
                            ),
                        5 =>
                            array (
                                'field' => 'CAT_CREATE_DATE',
                                'type' => 'text',
                                'value' => $record[5],
                            ),
                        6 =>
                            array (
                                'field' => 'CAT_UPDATE_DATE',
                                'type' => 'text',
                                'value' => $record[6],
                            )
                    ),
                'action' => 1,
            );

        }
        return array_merge($data, $newData);

    }
}

