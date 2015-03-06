<?php

require_once 'classes/model/om/BaseListInbox.php';

/**
 * Skeleton subclass for representing a row from the 'LIST_INBOX' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
 
class ListInbox extends BaseListInbox
{
    /**
     * Create List Inbox Table
     *
     * @param type $data
     * @return type
     *
     */
    public function create($data)
    {
        $con = Propel::getConnection( ListInboxPeer::DATABASE_NAME );
        try {
            $this->fromArray( $data, BasePeer::TYPE_FIELDNAME );
            if ($this->validate()) {
                $result = $this->save();
            } else {
                $e = new Exception( "Failed Validation in class " . get_class( $this ) . "." );
                $e->aValidationFailures = $this->getValidationFailures();
                throw ($e);
            }
            $con->commit();

            // create participated history
            $listParticipatedHistory = new ListParticipatedHistory();
            $listParticipatedHistory->remove($data['APP_UID'],$data['DEL_INDEX']);
            $listParticipatedHistory = new ListParticipatedHistory();
            $listParticipatedHistory->create($data);

            // create participated history
            $listMyInbox = new ListMyInbox();
            $listMyInbox->refresh($data);

            // remove and create participated last
            $listParticipatedLast = new ListParticipatedLast();
            $listParticipatedLast->remove($data['APP_UID'], $data['USR_UID'],$data['DEL_INDEX']);
            $listParticipatedLast = new ListParticipatedLast();
            $listParticipatedLast->create($data);        
            $listParticipatedLast = new ListParticipatedLast();
            $listParticipatedLast->refresh($data);

            return $result;
        } catch(Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    /**
     *  Update List Inbox Table
     *
     * @param type $data
     * @return type
     * @throws type
     */
    public function update($data)
    {
        $con = Propel::getConnection( ListInboxPeer::DATABASE_NAME );
        try {
            $con->begin();
            $this->setNew( false );
            $this->fromArray( $data, BasePeer::TYPE_FIELDNAME );
            if ($this->validate()) {
                $result = $this->save();
                $con->commit();

                // update participated history
                $listParticipatedHistory = new ListParticipatedHistory();
                $listParticipatedHistory->update($data);                
                return $result;
            } else {
                $con->rollback();
                throw (new Exception( "Failed Validation in class " . get_class( $this ) . "." ));
            }
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    /**
     * Remove List Inbox
     *
     * @param type $seqName
     * @return type
     * @throws type
     *
     */
    public function remove ($app_uid, $del_index)
    {
        $con = Propel::getConnection( ListInboxPeer::DATABASE_NAME );
        try {
            $this->setAppUid($app_uid);
            $this->setDelIndex($del_index);

            $con->begin();
            $this->delete();
            $con->commit();
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    /**
     * Remove All List Inbox
     *
     * @param type $seqName
     * @return type
     * @throws type
     *
     */
    public function removeAll ($app_uid)
    {
        $con = Propel::getConnection( ListInboxPeer::DATABASE_NAME );
        try {
            $this->setAppUid($app_uid);

            $con->begin();
            $this->delete();
            $con->commit();
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    public function newRow ($data, $delPreviusUsrUid) {

        $data['DEL_PREVIOUS_USR_UID'] = $delPreviusUsrUid;
        if (isset($data['DEL_TASK_DUE_DATE'])) {
            $data['DEL_DUE_DATE'] = $data['DEL_TASK_DUE_DATE'];
        }

        $criteria = new Criteria();
        $criteria->addSelectColumn( ApplicationPeer::APP_NUMBER );
        $criteria->addSelectColumn( ApplicationPeer::APP_UPDATE_DATE );
        $criteria->add( ApplicationPeer::APP_UID, $data['APP_UID'], Criteria::EQUAL );
        $dataset = ApplicationPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        $data = array_merge($data, $aRow);


        $criteria = new Criteria();
        $criteria->addSelectColumn(ContentPeer::CON_VALUE);
        $criteria->add( ContentPeer::CON_ID, $data['APP_UID'], Criteria::EQUAL );
        $criteria->add( ContentPeer::CON_CATEGORY, 'APP_TITLE', Criteria::EQUAL );
        $criteria->add( ContentPeer::CON_LANG, SYS_LANG, Criteria::EQUAL );
        $dataset = ContentPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        $data['APP_TITLE'] = $aRow['CON_VALUE'];


        $criteria = new Criteria();
        $criteria->addSelectColumn(ContentPeer::CON_VALUE);
        $criteria->add( ContentPeer::CON_ID, $data['PRO_UID'], Criteria::EQUAL );
        $criteria->add( ContentPeer::CON_CATEGORY, 'PRO_TITLE', Criteria::EQUAL );
        $criteria->add( ContentPeer::CON_LANG, SYS_LANG, Criteria::EQUAL );
        $dataset = ContentPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        $data['APP_PRO_TITLE'] = $aRow['CON_VALUE'];


        $criteria = new Criteria();
        $criteria->addSelectColumn(ContentPeer::CON_VALUE);
        $criteria->add( ContentPeer::CON_ID, $data['TAS_UID'], Criteria::EQUAL );
        $criteria->add( ContentPeer::CON_CATEGORY, 'TAS_TITLE', Criteria::EQUAL );
        $criteria->add( ContentPeer::CON_LANG, SYS_LANG, Criteria::EQUAL );
        $dataset = ContentPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        $data['APP_TAS_TITLE'] = $aRow['CON_VALUE'];


        $data['APP_PREVIOUS_USER'] = '';
        if ($data['DEL_PREVIOUS_USR_UID'] != '') {
            $criteria = new Criteria();
            $criteria->addSelectColumn(UsersPeer::USR_USERNAME);
            $criteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
            $criteria->addSelectColumn(UsersPeer::USR_LASTNAME);
            $criteria->add( UsersPeer::USR_UID, $data['DEL_PREVIOUS_USR_UID'], Criteria::EQUAL );
            $dataset = UsersPeer::doSelectRS($criteria);
            $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $dataset->next();
            $aRow = $dataset->getRow();
            $data['DEL_PREVIOUS_USR_USERNAME']  = $aRow['USR_USERNAME'];
            $data['DEL_PREVIOUS_USR_FIRSTNAME'] = $aRow['USR_FIRSTNAME'];
            $data['DEL_PREVIOUS_USR_LASTNAME']  = $aRow['USR_LASTNAME'];
        }
        
        if(!isset($data['APP_STATUS']) && $data['DEL_INDEX']>1){
          $data['APP_STATUS'] = 'TO_DO';
        }

        self::create($data);
    }

    public function loadFilters (&$criteria, $filters)
    {
        $filter = isset($filters['filter']) ? $filters['filter'] : "";
        $search = isset($filters['search']) ? $filters['search'] : "";
        $process = isset($filters['process']) ? $filters['process'] : "";
        $category = isset($filters['category']) ? $filters['category'] : "";
        $dateFrom = isset($filters['dateFrom']) ? $filters['dateFrom'] : "";
        $dateTo = isset($filters['dateTo']) ? $filters['dateTo'] : "";

        if ($filter != '') {
            switch ($filter) {
                case 'read':
                    $criteria->add( ListInboxPeer::DEL_INIT_DATE, null, Criteria::ISNOTNULL );
                    break;
                case 'unread':
                    $criteria->add( ListInboxPeer::DEL_INIT_DATE, null, Criteria::ISNULL );
                    break;
            }
        }

        if ($search != '') {
            $criteria->add(
                $criteria->getNewCriterion( ListInboxPeer::APP_TITLE, '%' . $search . '%', Criteria::LIKE )->
                    addOr( $criteria->getNewCriterion( ListInboxPeer::APP_TAS_TITLE, '%' . $search . '%', Criteria::LIKE )->
                        addOr( $criteria->getNewCriterion( ListInboxPeer::APP_NUMBER, $search, Criteria::LIKE ) ) ) );
        }

        if ($process != '') {
            $criteria->add( ListInboxPeer::PRO_UID, $process, Criteria::EQUAL);
        }

        if ($category != '') {
            // INNER JOIN FOR TAS_TITLE
            $criteria->addSelectColumn(ProcessPeer::PRO_CATEGORY);
            $aConditions   = array();
            $aConditions[] = array(ListInboxPeer::PRO_UID, ProcessPeer::PRO_UID);
            $aConditions[] = array(ProcessPeer::PRO_CATEGORY, "'" . $category . "'");
            $criteria->addJoinMC($aConditions, Criteria::INNER_JOIN);
        }

        if ($dateFrom != "") {
            if ($dateTo != "") {
                if ($dateFrom == $dateTo) {
                    $dateSame = $dateFrom;
                    $dateFrom = $dateSame . " 00:00:00";
                    $dateTo = $dateSame . " 23:59:59";
                } else {
                    $dateFrom = $dateFrom . " 00:00:00";
                    $dateTo = $dateTo . " 23:59:59";
                }

                $criteria->add( $criteria->getNewCriterion( ListInboxPeer::DEL_DELEGATE_DATE, $dateFrom, Criteria::GREATER_EQUAL )->
                    addAnd( $criteria->getNewCriterion( ListInboxPeer::DEL_DELEGATE_DATE, $dateTo, Criteria::LESS_EQUAL ) ) );
            } else {
                $dateFrom = $dateFrom . " 00:00:00";

                $criteria->add( ListInboxPeer::DEL_DELEGATE_DATE, $dateFrom, Criteria::GREATER_EQUAL );
            }
        } elseif ($dateTo != "") {
            $dateTo = $dateTo . " 23:59:59";

            $criteria->add( ListInboxPeer::DEL_DELEGATE_DATE, $dateTo, Criteria::LESS_EQUAL );
        }
    }

    public function countTotal ($usr_uid, $filters = array())
    {
        $criteria = new Criteria();
        $criteria->add( ListInboxPeer::USR_UID, $usr_uid, Criteria::EQUAL );
        self::loadFilters($criteria, $filters);
        $total = ListInboxPeer::doCount( $criteria );
        return (int)$total;
    }

    public function loadList ($usr_uid, $filters = array())
    {
        $criteria = new Criteria();

        $criteria->addSelectColumn(ListInboxPeer::APP_UID);
        $criteria->addSelectColumn(ListInboxPeer::DEL_INDEX);
        $criteria->addSelectColumn(ListInboxPeer::USR_UID);
        $criteria->addSelectColumn(ListInboxPeer::TAS_UID);
        $criteria->addSelectColumn(ListInboxPeer::PRO_UID);
        $criteria->addSelectColumn(ListInboxPeer::APP_NUMBER);
        $criteria->addSelectColumn(ListInboxPeer::APP_STATUS);
        $criteria->addSelectColumn(ListInboxPeer::APP_TITLE);
        $criteria->addSelectColumn(ListInboxPeer::APP_PRO_TITLE);
        $criteria->addSelectColumn(ListInboxPeer::APP_TAS_TITLE);
        $criteria->addSelectColumn(ListInboxPeer::APP_UPDATE_DATE);
        $criteria->addSelectColumn(ListInboxPeer::DEL_PREVIOUS_USR_UID);
        $criteria->addSelectColumn(ListInboxPeer::DEL_PREVIOUS_USR_USERNAME);
        $criteria->addSelectColumn(ListInboxPeer::DEL_PREVIOUS_USR_FIRSTNAME);
        $criteria->addSelectColumn(ListInboxPeer::DEL_PREVIOUS_USR_LASTNAME);
        $criteria->addSelectColumn(ListInboxPeer::DEL_DELEGATE_DATE);
        $criteria->addSelectColumn(ListInboxPeer::DEL_INIT_DATE);
        $criteria->addSelectColumn(ListInboxPeer::DEL_DUE_DATE);
        $criteria->addSelectColumn(ListInboxPeer::DEL_PRIORITY);

        $arrayTaskTypeToExclude = array("WEBENTRYEVENT", "END-MESSAGE-EVENT", "START-MESSAGE-EVENT", "INTERMEDIATE-THROW-MESSAGE-EVENT", "INTERMEDIATE-CATCH-MESSAGE-EVENT");

        $criteria->addJoin(ListInboxPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN);
        $criteria->add(TaskPeer::TAS_TYPE, $arrayTaskTypeToExclude, Criteria::NOT_IN);

        $criteria->add( ListInboxPeer::USR_UID, $usr_uid, Criteria::EQUAL );
        self::loadFilters($criteria, $filters);

        $sort  = (!empty($filters['sort'])) ? $filters['sort'] : "LIST_INBOX.APP_UPDATE_DATE";
        $dir   = isset($filters['dir']) ? $filters['dir'] : "ASC";
        $start = isset($filters['start']) ? $filters['start'] : "0";
        $limit = isset($filters['limit']) ? $filters['limit'] : "25";
        $paged = isset($filters['paged']) ? $filters['paged'] : 1;

        if ($filters['action'] == 'draft') {
            $criteria->add( ListInboxPeer::APP_STATUS, 'DRAFT', Criteria::EQUAL );
        } else {            
            $criteria->add( ListInboxPeer::APP_STATUS, 'TO_DO', Criteria::EQUAL );
        }

        if ($dir == "DESC") {
            $criteria->addDescendingOrderByColumn($sort);
        } else {
            $criteria->addAscendingOrderByColumn($sort);
        }

        if ($paged == 1) {
            $criteria->setLimit( $limit );
            $criteria->setOffset( $start );
        }

        $dataset = ListInboxPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $data = array();
        $aPriorities = array ('1' => 'VL','2' => 'L','3' => 'N','4' => 'H','5' => 'VH');
        while ($dataset->next()) {
            $aRow = $dataset->getRow();
            $aRow['DEL_PRIORITY'] = G::LoadTranslation( "ID_PRIORITY_{$aPriorities[$aRow['DEL_PRIORITY']]}" );
            $data[] = $aRow;
        }
        return $data;
    }
}

