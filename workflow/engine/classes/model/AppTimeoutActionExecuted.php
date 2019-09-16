<?php

require_once 'classes/model/om/BaseAppTimeoutActionExecuted.php';


/**
 * Skeleton subclass for representing a row from the 'APP_TIMEOUT_ACTION_EXECUTED' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class AppTimeoutActionExecuted extends BaseAppTimeoutActionExecuted
{

    public function create($aData)
    {
        $con = Propel::getConnection(AppTimeoutActionExecutedPeer::DATABASE_NAME);
        try {
            $this->fromArray($aData, BasePeer::TYPE_FIELDNAME);
            if ($this->validate()) {
                $result = $this->save();
            } else {
                $e = new Exception("Failed Validation in class " . get_class($this) . ".");
                $e->aValidationFailures = $this->getValidationFailures();
                throw ($e);
            }
            $con->commit();
            return $result;
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }
} // AppTimeoutActionExecuted
