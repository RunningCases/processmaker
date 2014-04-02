<?php
namespace BusinessModel;

class ProcessCategory
{
    private $arrayFieldDefinition = array(
        "CAT_UID"    => array("fieldName" => "CATEGORY_UID",    "type" => "string", "required" => false, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "processCategoryUid"),

        "CAT_PARENT" => array("fieldName" => "CATEGORY_PARENT", "type" => "string", "required" => false, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "processCategoryParent"),
        "CAT_NAME"   => array("fieldName" => "CATEGORY_NAME",   "type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(), "fieldNameAux" => "processCategoryName"),
        "CAT_ICON"   => array("fieldName" => "CATEGORY_ICON",   "type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(), "fieldNameAux" => "processCategoryIcon")
    );

    private $formatFieldNameInUppercase = true;

    private $arrayFieldNameForException = array(
        "filter" => "FILTER",
        "start"  => "START",
        "limit"  => "LIMIT"
    );

    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            foreach ($this->arrayFieldDefinition as $key => $value) {
                $this->arrayFieldNameForException[$value["fieldNameAux"]] = $key;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set the format of the fields name (uppercase, lowercase)
     *
     * @param bool $flag Value that set the format
     *
     * return void
     */
    public function setFormatFieldNameInUppercase($flag)
    {
        try {
            $this->formatFieldNameInUppercase = $flag;

            $this->setArrayFieldNameForException($this->arrayFieldNameForException);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set exception messages for fields
     *
     * @param array $arrayData Data with the fields
     *
     * return void
     */
    public function setArrayFieldNameForException($arrayData)
    {
        try {
            foreach ($arrayData as $key => $value) {
                $this->arrayFieldNameForException[$key] = $this->getFieldNameByFormatFieldName($value);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the name of the field according to the format
     *
     * @param string $fieldName Field name
     *
     * return string Return the field name according the format
     */
    public function getFieldNameByFormatFieldName($fieldName)
    {
        try {
            return ($this->formatFieldNameInUppercase)? strtoupper($fieldName) : strtolower($fieldName);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get criteria for Process Category
     *
     * return object
     */
    public function getProcessCategoryCriteria()
    {
        try {
            $criteria = new \Criteria("workflow");
            $criteria->addSelectColumn(\ProcessCategoryPeer::CATEGORY_UID);
            $criteria->addSelectColumn(\ProcessCategoryPeer::CATEGORY_PARENT);
            $criteria->addSelectColumn(\ProcessCategoryPeer::CATEGORY_NAME);
            $criteria->addSelectColumn(\ProcessCategoryPeer::CATEGORY_ICON);
            $criteria->add(\ProcessCategoryPeer::CATEGORY_UID, "", \Criteria::NOT_EQUAL);

            return $criteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Process Category from a record
     *
     * @param array $record Record
     *
     * return array Return an array with data Process Category
     */
    public function getProcessCategoryDataFromRecord($record)
    {
        try {
            return array(
                $this->getFieldNameByFormatFieldName("CAT_UID")             => $record["CATEGORY_UID"],
                $this->getFieldNameByFormatFieldName("CAT_NAME")            => $record["CATEGORY_NAME"],
                $this->getFieldNameByFormatFieldName("CAT_TOTAL_PROCESSES") => (int)($record["CATEGORY_TOTAL_PROCESSES"])
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all Process Categories
     *
     * @param array  $arrayFilterData Data of the filters
     * @param string $sortField       Field name to sort
     * @param string $sortDir         Direction of sorting (ASC, DESC)
     * @param int    $start           Start
     * @param int    $limit           Limit
     *
     * return array Return an array with all Process Categories
     */
    public function getCategories($arrayFilterData = null, $sortField = null, $sortDir = null, $start = null, $limit = null)
    {
        try {
            $arrayProcessCategory = array();

            //Verify data
            $process = new \BusinessModel\Process();

            $process->throwExceptionIfDataNotMetPagerVarDefinition(array("start" => $start, "limit" => $limit), $this->arrayFieldNameForException);

            //Get data
            if (!is_null($limit) && $limit . "" == "0") {
                return $arrayProcessCategory;
            }

            //Set variables
            $process = new \Process();

            $arrayTotalProcessesByCategory = $process->getAllProcessesByCategory();

            //SQL
            $criteria = $this->getProcessCategoryCriteria();

            if (!is_null($arrayFilterData) && is_array($arrayFilterData) && isset($arrayFilterData["filter"]) && trim($arrayFilterData["filter"]) != "") {
                $criteria->add(\ProcessCategoryPeer::CATEGORY_NAME, "%" . $arrayFilterData["filter"] . "%", \Criteria::LIKE);
            }

            //Number records total
            $criteriaCount = clone $criteria;

            $criteriaCount->clearSelectColumns();
            $criteriaCount->addSelectColumn("COUNT(" . \ProcessCategoryPeer::CATEGORY_UID . ") AS NUM_REC");

            $rsCriteriaCount = \ProcessCategoryPeer::doSelectRS($criteriaCount);
            $rsCriteriaCount->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteriaCount->next();
            $row = $rsCriteriaCount->getRow();

            $numRecTotal = $row["NUM_REC"];

            //SQL
            if (!is_null($sortField) && trim($sortField) != "") {
                $sortField = strtoupper($sortField);
                $sortField = (isset($this->arrayFieldDefinition[$sortField]["fieldName"]))? $this->arrayFieldDefinition[$sortField]["fieldName"] : $sortField;

                switch ($sortField) {
                    case "CATEGORY_UID":
                    case "CATEGORY_PARENT":
                    case "CATEGORY_NAME":
                    case "CATEGORY_ICON":
                        $sortField = \ProcessCategoryPeer::TABLE_NAME . "." . $sortField;
                        break;
                    default:
                        $sortField = \ProcessCategoryPeer::CATEGORY_NAME;
                        break;
                }
            } else {
                $sortField = \ProcessCategoryPeer::CATEGORY_NAME;
            }

            if (!is_null($sortDir) && trim($sortDir) != "" && strtoupper($sortDir) == "DESC") {
                $criteria->addDescendingOrderByColumn($sortField);
            } else {
                $criteria->addAscendingOrderByColumn($sortField);
            }

            if (!is_null($start)) {
                $criteria->setOffset((int)($start));
            }

            if (!is_null($limit)) {
                $criteria->setLimit((int)($limit));
            }

            $rsCriteria = \ProcessCategoryPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $row["CATEGORY_TOTAL_PROCESSES"] = (isset($arrayTotalProcessesByCategory[$row["CATEGORY_UID"]]))? $arrayTotalProcessesByCategory[$row["CATEGORY_UID"]] : 0;

                $arrayProcessCategory[] = $this->getProcessCategoryDataFromRecord($row);
            }

            //Return
            return $arrayProcessCategory;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get a Process Category
     *
     * @param string $cat_uid       Category Id
     *
     * return array Return an object with the Process Category
     */
    public function getCategory($cat_uid)
    {
        try {
            $oProcessCategory = '';
            $process = new \Process();
            $oTotalProcessesByCategory = $process->getAllProcessesByCategory();
            $criteria = $this->getAProcessCategoryCriteria($cat_uid);
            $criteriaCount = clone $criteria;
            $criteriaCount->clearSelectColumns();
            $criteriaCount->addSelectColumn("COUNT(" . \ProcessCategoryPeer::CATEGORY_UID . ") AS NUM_REC");
            $rsCriteriaCount = \ProcessCategoryPeer::doSelectRS($criteriaCount);
            $rsCriteriaCount->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rsCriteriaCount->next();
            $rsCriteria = \ProcessCategoryPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();
                $row["CATEGORY_TOTAL_PROCESSES"] = (isset($oTotalProcessesByCategory[$row["CATEGORY_UID"]]))? $oTotalProcessesByCategory[$row["CATEGORY_UID"]] : 0;
                $oProcessCategory = $this->getProcessCategoryDataFromRecord($row);
            }
            //Return
            if ($oProcessCategory != '') {
                return $oProcessCategory;
            } else {
                throw (new \Exception( 'The Category with cat_uid: '.$cat_uid.' doesn\'t exist!'));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Post Process Category
     *
     * @param string $cat_name   Name of Category
     *
     * return array
     */
    public function addCategory($cat_name)
    {
        try {
            require_once 'classes/model/ProcessCategory.php';
            $catName = trim( $cat_name );
            if ($this->existsName( $cat_name )) {
                throw (new \Exception( 'cat_name. Duplicate Process Category name'));
            }
            $catUid = \G::GenerateUniqueID();
            $pcat = new \ProcessCategory();
            $pcat->setNew( true );
            $pcat->setCategoryUid( $catUid );
            $pcat->setCategoryName( $catName );
            $pcat->save();
            $oProcessCategory = array_change_key_case($this->getCategory( $catUid ), CASE_LOWER);
            //Return
            return $oProcessCategory;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Put Process Category
     *
     * @param string $cat_uid    Category id
     * @param string $cat_name   Category Name
     *
     * return array
     */
    public function updateCategory($cat_uid, $cat_name)
    {
        try {
            require_once 'classes/model/ProcessCategory.php';
            $catUID = $cat_uid;
            $catName = trim( $cat_name );
            if ($this->existsName( $cat_name )) {
                throw (new \Exception( 'cat_name. Duplicate Process Category name'));
            }
            $pcat = new \ProcessCategory();
            $pcat->setNew( false );
            $pcat->setCategoryUid( $catUID );
            $pcat->setCategoryName( $catName );
            $pcat->save();
            $oProcessCategory = array_change_key_case($this->getCategory( $cat_uid ), CASE_LOWER);
            //Return
            return $oProcessCategory;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete Process Category
     *
     * @param string $cat_uid    Category id
     *
     * return array
     */
    public function deleteCategory($cat_uid)
    {
        try {
            require_once 'classes/model/ProcessCategory.php';
            $criteria = $this->getAProcessCategoryCriteria($cat_uid);
            $rsCriteria = \ProcessCategoryPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rsCriteria->next();
            $row = $rsCriteria->getRow();
            if ($row) {
                $cat = new \ProcessCategory();
                $cat->setCategoryUid( $cat_uid );
                $cat->delete();
            } else {
                throw (new \Exception( 'The Category with cat_uid: '.$cat_uid.' doesn\'t exist!'));
            }

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get criteria for Process Category
     *
     * return object
     */
    public function getAProcessCategoryCriteria($cat_uid)
    {
        try {
            $criteria = new \Criteria("workflow");
            $criteria->addSelectColumn(\ProcessCategoryPeer::CATEGORY_UID);
            $criteria->addSelectColumn(\ProcessCategoryPeer::CATEGORY_PARENT);
            $criteria->addSelectColumn(\ProcessCategoryPeer::CATEGORY_NAME);
            $criteria->addSelectColumn(\ProcessCategoryPeer::CATEGORY_ICON);
            $criteria->add(\ProcessCategoryPeer::CATEGORY_UID, $cat_uid);
            return $criteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Checks if the name exists
     *
     * @param string $name       Name
     *
     * return bool Return true if the name exists, false otherwise
     */
    public function existsName($name)
    {
        try {
            $criteria = new \Criteria("workflow");
            $criteria->add(\ProcessCategoryPeer::CATEGORY_NAME, $name, \Criteria::EQUAL);
            $rsCriteria = \ProcessCategoryPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rsCriteria->next();
            return $rsCriteria->getRow();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
