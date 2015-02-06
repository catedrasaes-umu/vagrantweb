<?php

class ECSVExport
{
    
    public $includeColumnHeaders = true;
    
    
    public $stripNewLines = true;
    
    
    public $exportFull = true;
    
    
    public $convertActiveDataProvider = true;
    
    
    protected $_outputFile;
    
    
    protected $_filePointer;
    
    
    protected $_dataProvider;
    
    
    protected $_callback;
    
    
    protected $_headers = array();
    
    
    protected $_exclude = array();
    
    
    protected $_delimiter = ",";
    
    
    protected $_enclosure = '"';
    
    
    protected $_appendCsv = false;
    
    
    protected $_modelRelations = array();
    
    
    public function __construct($dataProvider, $exportFull=true, $includeColumnHeaders=true, $delimiter=null, $enclosure=null) 
    {
        $this->_dataProvider = $dataProvider;
        $this->exportFull = (bool) $exportFull;
        $this->includeColumnHeaders = (bool) $includeColumnHeaders;
        if($delimiter) $this->_delimiter = $delimiter;
        if($enclosure) $this->_enclosure = $enclosure;
    }
    
    
    public function getDataProvider()
    {
        return $this->_dataProvider;
    }
    
    
    public function dontConvertProvider()
    {
        $this->convertActiveDataProvider = false;
        return $this;
    }
    
    
    public function setToAppend()
    {
        $this->_appendCsv = true;
        return $this;
    }
    
    
    public function setDelimiter($delimiter)
    {
        $this->_delimiter = $delimiter;
        return $this;
    }
    
    
    public function getDelimiter()
    {
        return $this->_delimiter;
    }
    
    
    public function setEnclosure($enclosure)
    {
        $this->_enclosure = $enclosure;
        return $this;
    }
    
    
    public function getEnclosure()
    {
        return $this->_enclosure;
    }
    
    
    public function setOutputFile($filename)
    {
        $this->_outputFile = $filename;
        return $this;
    }
    
    
    public function getOutputFile()
    {
        return $this->_outputFile;
    }
       
    
    public function setCallback($callback)
    {
        if(is_callable($callback)) {
            $this->_callback = $callback;
            return $this;
        } else {
            throw new Exception('Callback must be callable. Duh.');
        }
    }
    
    
    public function getCallback()
    {
        return $this->_callback;
    }
    
    
    public function setHeaders(array $headers)
    {
        $this->_headers = $headers;
        return $this;
    }
    
    
    public function getHeaders()
    {
        return $this->_headers;
    }
    
    
    public function setHeader($key, $value)
    {
        $this->_headers[$key] = $value;
        return $this;
    }
    
    
    public function setExclude($noshow)
    {
        if(is_array($noshow)) {
            $this->_exclude = $noshow;
            return $this;
        } else {
            $this->_exclude[] = (string) $noshow;
        }
    }
    
    
    public function getExclude()
    {
        return $this->_exclude;
    }
    
    
    public function getModelRelations()
    {
        return $this->_modelRelations;
    }
    
    
    public function setModelRelations(array $relations)
    {
        $this->_modelRelations = $relations;
    }
    
    
    public function exportCurrentPageOnly()
    {
        $this->exportFull = false;
        return $this;
    }
    
    
    public function toCSV($outputFile=null, $delimiter=null, $enclosure=null, $includeHeaders=true)
    {
        // check that data provider is something useful
        $isGood = false;
        
        if($this->_dataProvider instanceof CActiveDataProvider) {
            $isGood = true;
        }
        
        if($this->_dataProvider instanceof CSqlDataProvider) {
            $isGood = true;
        }
        
        if($this->_dataProvider instanceof CDbCommand) {
            $isGood = true;
        }
        
        if(is_array($this->_dataProvider)) {
            $isGood = true;
        }
        
        if(!$isGood) {
            throw new Exception('Bad data provider given as source to '.__CLASS__);
        }
        
        if($outputFile !== null) {
            $this->setOutputFile($outputFile);
        }
        
        if(!$includeHeaders) {
            $this->includeColumnHeaders = false;
        }
        
        if($delimiter !== null) {
            $this->_delimiter = $delimiter;
        }
        
        if($enclosure !== null) {
            $this->_enclosure = $enclosure;
        }
        
        // create file pointer
        $this->_filePointer =  fopen("php://temp", 'w');
        $this->_writeData();        
        rewind($this->_filePointer);
        
        // make sure you can write to file!
        if($this->_outputFile !== null) {
            // write stream to file
            return $this->_appendCsv ? file_put_contents($this->_outputFile, $this->_filePointer, FILE_APPEND | LOCK_EX) 
                                     : file_put_contents($this->_outputFile, $this->_filePointer, LOCK_EX);
            
        } else {
            return stream_get_contents($this->_filePointer);    
        }
    }
    
    
    protected function _writeData()
    {        
        $firstTimeThrough = true;        
        if($this->_dataProvider instanceof CActiveDataProvider) { 
            if($this->exportFull) {
                // set pagination to off
                $this->_dataProvider->setPagination(false);
            }
            if($this->convertActiveDataProvider) {
                $criteria = $this->_dataProvider->getCriteria();
                $model = $this->_dataProvider->model;
                $criteria = $model->getCommandBuilder()
                                    ->createCriteria($criteria,array());
                $this->_dataProvider = $model->getCommandBuilder()
                                             ->createFindCommand($model->getTableSchema(), 
                                                                 $criteria);                                
                unset($model, $criteria);
            } else {
                // suggested implementation from marcovtwout	
                $models = $this->_dataProvider->getData();
                $dataReader = array();
                $attributes = $this->_dataProvider->model->getMetaData()->columns;
                
                
                // since we are already looping through results, don't bother
                // passing results to _loopRow, just write it here.
                foreach ($models as &$model) {
                    $row = array();
                    
                    foreach ($attributes as $attribute => $col) {
                        $row[$attribute] = $model->{$attribute};
                    }
                    
                    // check model relations
                    if(count($this->_modelRelations)) {
                        foreach($this->_modelRelations as $relation=>$value) {
                            if(is_array($value)) {
                                foreach($value as $subvalue) {
                                    if(isset($model->$relation->$subvalue) && $model->$relation->$subvalue)
                                        $row[$relation.'['.$subvalue.']'] = $model->$relation->$subvalue;
                                }
                            } else {
                                if(isset($model->$relation->$value) && $model->$relation->$value)
                                    $row[$relation.'['.$value.']'] = $model->$relation->$value;
                            }
                        }
                    }
                    
                    if($firstTimeThrough) {
                        $this->_writeHeaders($row);
                        $firstTimeThrough = false;                    
                    }
                    $this->_writeRow($row);
                }
                unset($models, $attributes);
                return;
            }            
        }
        
        if($this->_dataProvider instanceof CSqlDataProvider) {            
            if($this->exportFull) {
                $this->_dataProvider->setId('csvexport');
                $this->_dataProvider->getPagination()->setItemCount($this->_dataProvider->getTotalItemCount());                
                $pageVar = $this->_dataProvider->getPagination()->pageVar;
                $_GET[$pageVar] = 0;                
                $totalPages = $this->_dataProvider->getPagination()->getPageCount();                
                $this->setToAppend();
                for($i=1; $i<=$totalPages; $i++) {                    
                    $_GET[$pageVar] = $i;
                    $this->_dataProvider->getPagination()->setCurrentPage($i); 
                    $_getData = $this->_dataProvider->getData(true);
                    $this->_loopRows($_getData);                    
                    $this->includeColumnHeaders = !(bool) $i;
                }                
            } else {
                $this->_loopRows($this->_dataProvider->getData());
            }
                        
            return;
        }
        
        if($this->_dataProvider instanceof CDbCommand) {
            $dataReader = $this->_dataProvider->query();  
            $this->_loopRows($dataReader);            
            return;
        }
        
        if(is_array($this->_dataProvider)) {
            $this->_loopRows($this->_dataProvider);
            return;
        } 
        
        // if program made it this far something happened
        throw new Exception('Data source failed to retrieve data, are you sure you passed something useable?');
    }
    
    
    public function _loopRows(&$dp)
    {
        $firstTimeThrough = true;
        if($dp instanceof CDbDataReader) {
            while(($row = $dp->read()) !== false) {
                if($firstTimeThrough) {
                    $this->_writeHeaders($row);
                    $firstTimeThrough = false;                    
                }
                $this->_writeRow($row);
            }
        } else {
            $total = count($dp);
            for($i=0; $i<$total; $i++) {
                if($firstTimeThrough) {
                    $this->_writeHeaders($dp[$i]);
                    $firstTimeThrough = false;                    
                }
                $this->_writeRow($dp[$i]);
            }
        }
    }
    
    
    protected function _writeHeaders($row)
    {
        if(!$this->includeColumnHeaders) {
            return;
        }
        
        if($row instanceof CActiveRecord) {
            $headers = array_keys($row->getAttributes());
        } else {
            $headers = array_keys($row);
        }
                
        // remove excluded
        if(count($this->_exclude) > 0) {
            foreach($this->_exclude as $e) { 
                $key = array_search($e, $headers);
                if($key !== false) {                    
                    unset($headers[$key]);                        
                }
            }
        }            
        
        if(count($this->_headers) > 0) {
            foreach($headers as &$header) {
                if(array_key_exists($header, $this->_headers)) {
                    $header = $this->_headers[$header];             
                }
            }
        }                
        
        fputcsv($this->_filePointer, $headers, $this->_delimiter, $this->_enclosure);
    }
    
    
    public function _writeRow($row)
    {
        if($row instanceof CActiveRecord) {
            $row = $row->getAttributes();
        }
        // remove excluded
        if(count($this->_exclude) > 0) {
            foreach($this->_exclude as $e) { 
                if(array_key_exists($e, $row)) {
                    unset($row[$e]);
                }
            }
        }
        
        if($this->stripNewLines) {            
            array_walk($row, array('ECSVExport','lambdaFail'));
        }
        
        array_walk($row, array('ECSVExport','stripSlashes'));
               
        if(isset($this->_callback) && $this->_callback) {
            fputcsv($this->_filePointer, call_user_func($this->_callback, $row), $this->_delimiter, $this->_enclosure);                       
        } else {
            fputcsv($this->_filePointer, $row, $this->_delimiter, $this->_enclosure);
        }
        unset($row);
    }
	
	public static function lambdaFail(&$value, $key)
	{
		$value = str_replace("\r\n"," ", $value);
	}
    
    public static function stripSlashes(&$value, $key)
    {
        $value = stripslashes($value);
        $value = str_replace('\"', '"', $value);
    }
}
