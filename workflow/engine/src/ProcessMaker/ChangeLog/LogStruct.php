<?php

namespace ProcessMaker\ChangeLog;

class LogStruct
{
    /**
     * Name of the variable.
     * 
     * @var string 
     */
    private $field = '';

    /**
     * Previous value of the variable.
     * 
     * @var string 
     */
    private $previousValue = '';

    /**
     * Current value.
     * 
     * @var string 
     */
    private $currentValue = '';

    /**
     * Previous type of variable.
     * 
     * @var string 
     */
    private $previousValueType = '';

    /**
     * Type of the variable.
     * 
     * @var string 
     */
    private $currentValueType = '';

    /**
     * Group of changes.
     * 
     * @var string 
     */
    private $record = '';

    /**
     * Set the field.
     * 
     * @param string $field
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * Set the previousValue.
     * 
     * @param string $previousValue
     * @return $this
     */
    public function setPreviousValue($previousValue)
    {
        $this->previousValue = $previousValue;
        return $this;
    }

    /**
     * Set the currentValue.
     * 
     * @param string $currentValue
     * @return $this
     */
    public function setCurrentValue($currentValue)
    {
        $this->currentValue = $currentValue;
        return $this;
    }

    /**
     * Set the previousValueType.
     * 
     * @param string $previousValueType
     * @return $this
     */
    public function setPreviousValueType($previousValueType)
    {
        $this->previousValueType = $previousValueType;
        return $this;
    }

    /**
     * Set the currentValueType.
     * 
     * @param string $currentValueType
     * @return $this
     */
    public function setCurrentValueType($currentValueType)
    {
        $this->currentValueType = $currentValueType;
        return $this;
    }

    /**
     * Set the record.
     * 
     * @param string $record
     * @return $this
     */
    public function setRecord($record)
    {
        $this->record = $record;
        return $this;
    }

    /**
     * Get values.
     * 
     * @return array
     */
    public function getValues()
    {
        return get_object_vars($this);
    }
}
