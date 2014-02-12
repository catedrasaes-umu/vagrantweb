<?php
    class ArrayFilterForm extends CFormModel
	{
	    public $filters = array();
	 
	    /**
	     * Override magic getter for filters
	     */
	    public function __get($name)
	    {
	        if(!array_key_exists($name, $this->filters))
	            $this->filters[$name] = null;
	        return $this->filters[$name];
	    }
	 
	    /**
	     * Filter input array by key value pairs
	     * @param array $data rawData
	     * @return array filtered data array
	     */
	    public function filter(array $data)
	    {
	        foreach($data AS $rowIndex => $row) {
	            foreach($this->filters AS $key => $value) {
	                // unset if filter is set, but doesn't match
	                if(array_key_exists($key, $row) AND !empty($value)) {
	                    if(stripos($row[$key], $value) === false)
	                        unset($data[$rowIndex]);
	                }
	            }
	        }
	        return $data;
	    }
	}
?>