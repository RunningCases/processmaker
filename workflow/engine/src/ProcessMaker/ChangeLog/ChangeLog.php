<?php

namespace ProcessMaker\ChangeLog;

use AppDataChangeLog;
use DynaformPeer;
use InputDocumentPeer;
use OutputDocumentPeer;
use ProcessPeer;
use TaskPeer;
use UsersPeer;

class ChangeLog
{
    /**
     * Identifier for the unknow application.
     */
    const FromUnknow = 0;

    /**
     * Identifier for the web application.
     */
    const FromWeb = 1;

    /**
     * Identifier for the ABE application.
     */
    const FromABE = 2;

    /**
     * Identifier for the mobile application.
     */
    const FromMobile = 3;

    /**
     * Types applications.
     * 
     * @var array 
     */
    protected static $applications = ['Unknow', 'Web Application', 'Action by Email', 'Mobile Application'];

    /**
     * Identifier for unknow object.
     */
    const UNKNOW_OBJECT = 0;

    /**
     * Identifier for the dynaform.
     */
    const DYNAFORM = 1;

    /**
     * Identifier for the input document.
     */
    const INPUT_DOCUMENT = 2;

    /**
     * Identifier for the output document.
     */
    const OUTPUT_DOCUMENT = 3;

    /**
     * Identifier for the trigger.
     */
    const TRIGGER = 4;

    /**
     * Identifier for the external step.
     */
    const EXTERNAL_STEP = 5;

    /**
     * Types objects.
     * 
     * @var array 
     */
    protected static $objects = [
        'UNKNOW_OBJECT',
        'DYNAFORM',
        'INPUT_DOCUMENT',
        'OUTPUT_DOCUMENT',
        'TRIGGER',
        'EXTERNAL_STEP'
    ];

    /**
     * Identifier for unknow step.
     */
    const UNKNOW_STEP = 0;

    /**
     * Identifier for before object step.
     */
    const BEFORE = 1;

    /**
     * Identifier for after object step.
     */
    const AFTER = 2;

    /**
     * Identifier for before assignment object step.
     */
    const BEFORE_ASSIGNMENT = 3;

    /**
     * Identifier for before routing object step.
     */
    const BEFORE_ROUTING = 4;

    /**
     * Identifier for after routing object step.
     */
    const AFTER_ROUTING = 5;

    /**
     * Types steps.
     * 
     * @var array 
     */
    protected static $stepTypes = [
        'UNKNOW_STEP',
        'BEFORE',
        'AFTER',
        'BEFORE_ASSIGNMENT',
        'BEFORE_ROUTING',
        'AFTER_ROUTING'
    ];

    /**
     * Single object instance to be used in the entire environment.
     * 
     * @var object 
     */
    private static $changeLog = null;

    /**
     * The value for the date field.
     * 
     * @var int
     */
    protected $date;

    /**
     * The value for the appNumber field.
     * 
     * @var int
     */
    protected $appNumber = 0;

    /**
     * The value for the delIndex field.
     * 
     * @var int
     */
    protected $delIndex = 0;

    /**
     * The value for the proId field.
     * 
     * @var int
     */
    protected $proId = 0;

    /**
     * The value for the tasId field.
     * 
     * @var int
     */
    protected $tasId = 0;

    /**
     * The value for the usrId field.
     * 
     * @var int
     */
    protected $usrId = 0;

    /**
     * The value for the objectType field.
     * 
     * @var int
     */
    protected $objectType = 0;

    /**
     * The value for the objectId field.
     * 
     * @var int
     */
    protected $objectId = 0;

    /**
     * The value for the objectUid field.
     * 
     * @var string 
     */
    protected $objectUid = '0';

    /**
     * The value for the executedAt field.
     * 
     * @var int
     */
    protected $executedAt = 0;

    /**
     * The value for the sourceId field.
     * 
     * @var string
     */
    protected $sourceId = 0;

    /**
     * The value for the data field.
     * 
     * @var string
     */
    protected $data = '';

    /**
     * The value for the skin field.
     * 
     * @var string
     */
    protected $skin = '';

    /**
     * The value for the language field.
     * 
     * @var string
     */
    protected $language = '';

    /**
     * Constructor of de class.
     */
    function __construct()
    {
        
    }

    /**
     * Get date.
     * 
     * @return datetime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get appNumber.
     * 
     * @return int
     */
    public function getAppNumber()
    {
        return $this->appNumber;
    }

    /**
     * Get delIndex.
     * 
     * @return int
     */
    public function getDelIndex()
    {
        return $this->delIndex;
    }

    /**
     * Get proId.
     * 
     * @return int
     */
    public function getProId()
    {
        return $this->proId;
    }

    /**
     * Get tasId.
     * 
     * @return int
     */
    public function getTasId()
    {
        return $this->tasId;
    }

    /**
     * Get usrId.
     * 
     * @return int
     */
    public function getUsrId()
    {
        return $this->usrId;
    }

    /**
     * Get objectType.
     * 
     * @return int
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * Get objectId.
     * 
     * @return int
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Get objectUid.
     * 
     * @return type
     */
    public function getObjectUid()
    {
        return $this->objectUid;
    }

    /**
     * Get executedAt.
     * 
     * @return int
     */
    public function getExecutedAt()
    {
        return $this->executedAt;
    }

    /**
     * Get sourceId.
     * 
     * @return string
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     * Get data.
     * 
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get skin.
     * 
     * @return string
     */
    public function getSkin()
    {
        return $this->skin;
    }

    /**
     * Get language.
     * 
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set date.
     * 
     * @param datetime $date
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Set appNumber.
     * 
     * @param int $appNumber
     * @return $this
     */
    public function setAppNumber($appNumber)
    {
        $this->appNumber = $appNumber;
        return $this;
    }

    /**
     * Set delIndex.
     * 
     * @param int $delIndex
     * @return $this
     */
    public function setDelIndex($delIndex)
    {
        $this->delIndex = $delIndex;
        return $this;
    }

    /**
     * Set proId.
     * 
     * @param int $proId
     * @return $this
     */
    public function setProId($proId)
    {
        $this->proId = $proId;
        return $this;
    }

    /**
     * Set tasId.
     * 
     * @param int $tasId
     * @return $this
     */
    public function setTasId($tasId)
    {
        $this->tasId = $tasId;
        return $this;
    }

    /**
     * Set usrId.
     * 
     * @param int $usrId
     * @return $this
     */
    public function setUsrId($usrId)
    {
        $this->usrId = $usrId;
        return $this;
    }

    /**
     * Set objectType.
     * 
     * @param int $objectType
     * @return $this
     */
    public function setObjectType($objectType)
    {
        $this->objectType = $objectType;
        return $this;
    }

    /**
     * Set objectId.
     * 
     * @param int $objectId
     * @return $this
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;
        return $this;
    }

    /**
     * Set objectUid.
     * 
     * @param string $objectUid
     * @return $this
     */
    public function setObjectUid($objectUid)
    {
        $this->objectUid = $objectUid;
        return $this;
    }

    /**
     * Set executedAt.
     * 
     * @param int $executedAt
     * @return $this
     */
    public function setExecutedAt($executedAt)
    {
        $this->executedAt = $executedAt;
        return $this;
    }

    /**
     * Set sourceId.
     * 
     * @param string $sourceId
     * @return $this
     */
    public function setSourceId($sourceId)
    {
        $this->sourceId = $sourceId;
        return $this;
    }

    /**
     * Set data.
     * 
     * @param string $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Set skin.
     * 
     * @param string $skin
     * @return $this
     */
    public function setSkin($skin)
    {
        $this->skin = $skin;
        return $this;
    }

    /**
     * Set language.
     * 
     * @param string $language
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * Registers an entry in the database with all the data established in the 
     * object at that moment.
     * 
     * @return $this
     */
    public function register()
    {
        $appDataChangeLog = new AppDataChangeLog();
        $appDataChangeLog->setDate($this->getDate());
        $appDataChangeLog->setAppNumber($this->getAppNumber());
        $appDataChangeLog->setDelIndex($this->getDelIndex());
        $appDataChangeLog->setProId($this->getProId());
        $appDataChangeLog->setTasId($this->getTasId());
        $appDataChangeLog->setUsrId($this->getUsrId());
        $appDataChangeLog->setObjectType($this->getObjectType());
        $appDataChangeLog->setObjectId($this->getObjectId());
        $appDataChangeLog->setObjectUid($this->getObjectUid());
        $appDataChangeLog->setExecutedAt($this->getExecutedAt());
        $appDataChangeLog->setSourceId($this->getSourceId());
        $appDataChangeLog->setData($this->getData());
        $appDataChangeLog->setSkin($this->getSkin());
        $appDataChangeLog->setLanguage($this->getLanguage());
        $appDataChangeLog->setRowMigration(0);
        $appDataChangeLog->save();
        return $this;
    }

    /**
     * Get 'usrId' for the value 'usrUid', the value is set to cache if the 
     * parameter 'force' is false, if 'force' is 'true' you get it again.
     * 
     * @param string $usrUid
     * @param boolean $force
     * @return $this
     */
    public function getUsrIdByUsrUid($usrUid = '', $force = false)
    {
        $usrId = $this->getUsrId();
        if (!empty($usrId) && $force === false) {
            return $this;
        }
        if (!empty($usrUid)) {
            $usersPeer = UsersPeer::retrieveByPK($usrUid);
            if ($usersPeer === null) {
                $this->setUsrId(0);
            } else {
                $this->setUsrId($usersPeer->getUsrId());
            }
        }
        return $this;
    }

    /**
     * Get 'proId' for the value 'proUid', the value is set to cache if the 
     * parameter 'force' is false, if 'force' is 'true' you get it again.
     * 
     * @param string $proUid
     * @param boolean $force
     * @return $this
     */
    public function getProIdByProUid($proUid, $force = false)
    {
        $proId = $this->getProId();
        if (!empty($proId) && $force === false) {
            return $this;
        }
        if (!empty($proUid)) {
            $processPeer = ProcessPeer::retrieveByPK($proUid);
            if ($processPeer === null) {
                $this->setProId(0);
            } else {
                $this->setProId($processPeer->getProId());
            }
        }
        return $this;
    }

    /**
     * Get 'tasId' for the value 'tasUid', the value is set to cache if the 
     * parameter 'force' is false, if 'force' is 'true' you get it again.
     * 
     * @param string $tasUid
     * @param boolean $force
     * @return $this
     */
    public function getTasIdByTasUid($tasUid, $force = false)
    {
        $tasId = $this->getTasId();
        if (!empty($tasId) && $force === false) {
            return $this;
        }
        if (!empty($tasUid)) {
            $taskPeer = TaskPeer::retrieveByPK($tasUid);
            if ($taskPeer === null) {
                $this->setTasId(0);
            } else {
                $this->setTasId($taskPeer->getTasId());
            }
        }
        return $this;
    }

    /**
     * Gets the id of the object, given the object 'uid' and its type.
     * 
     * @param string $uid
     * @param string $objType
     * @return $this
     */
    public function getObjectIdByUidAndObjType($uid, $objType)
    {
        switch ($objType) {
            case 'DYNAFORM':
                $this->setObjectType(self::DYNAFORM);
                $this->setObjectUid($uid);
                if (!empty($uid)) {
                    $object = DynaformPeer::retrieveByPK($uid);
                    if ($object !== null) {
                        $id = $object->getDynId();
                        $this->setObjectId($id);
                    } else {
                        $this->setObjectId(0);
                    }
                }
                break;
            case 'OUTPUT_DOCUMENT':
                $this->setObjectType(self::OUTPUT_DOCUMENT);
                $this->setObjectUid($uid);
                if (!empty($uid)) {
                    $object = OutputDocumentPeer::retrieveByPK($uid);
                    if ($object !== null) {
                        $id = $object->getOutDocId();
                        $this->setObjectId($id);
                    } else {
                        $this->setObjectId(0);
                    }
                }
                break;
            case 'INPUT_DOCUMENT':
                $this->setObjectType(self::INPUT_DOCUMENT);
                $this->setObjectUid($uid);
                if (!empty($uid)) {
                    $object = InputDocumentPeer::retrieveByPK($uid);
                    if ($object !== null) {
                        $id = $object->getInpDocId();
                        $this->setObjectId($id);
                    } else {
                        $this->setObjectId(0);
                    }
                }
                break;
            case 'ASSIGN_TASK':
                $this->setObjectType(self::TRIGGER);
                $this->setObjectUid($uid);
                $this->setObjectId(0);
                break;
            case 'EXTERNAL':
                $this->setObjectType(self::EXTERNAL_STEP);
                $this->setObjectUid($uid);
                $this->setObjectId(0);
                break;
            default :
                $this->setObjectType(self::UNKNOW_OBJECT);
                $this->setObjectUid($uid);
                $this->setObjectId(0);
                break;
        }
        switch ($uid) {
            case "":
                $this->setObjectUid(0);
                break;
            case "-1":
                $this->setObjectUid(0);
                $this->setExecutedAt(self::BEFORE_ASSIGNMENT);
                break;
            case "-2":
                $this->setObjectUid(0);
                if ($this->getExecutedAt() === 1) {
                    $this->setExecutedAt(self::BEFORE_ROUTING);
                }
                if ($this->getExecutedAt() === 2) {
                    $this->setExecutedAt(self::AFTER_ROUTING);
                }
                break;
        }
        return $this;
    }

    /**
     * Get the id of the step, given the type of the trigger in execution.
     * 
     * @param string $triggerType
     * @return $this
     */
    public function getExecutedAtIdByTriggerType($triggerType)
    {
        switch ($triggerType) {
            case 'BEFORE':
                $this->setExecutedAt(self::BEFORE);
                break;
            case 'AFTER':
                $this->setExecutedAt(self::AFTER);
                break;
            default :
                $this->setExecutedAt(self::UNKNOW_STEP);
                break;
        }
        return $this;
    }

    /**
     * Get object name.
     * 
     * @param int $id
     * @return string|null
     */
    public function getObjectNameById($id)
    {
        return isset(self::$objects[$id]) ? self::$objects[$id] : null;
    }

    /**
     * Get application name.
     * 
     * @param int $id
     * @return string|null
     */
    public function getApplicationNameById($id)
    {
        return isset(self::$applications[$id]) ? self::$applications[$id] : null;
    }

    /**
     * It obtains a single object to be used as a record of the whole environment.
     * 
     * @return object
     */
    public static function getChangeLog()
    {
        if (self::$changeLog === null) {
            self::$changeLog = new ChangeLog();
        }
        return self::$changeLog;
    }
}
