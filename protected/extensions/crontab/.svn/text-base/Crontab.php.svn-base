<?php 
/**
 * Copyright (c) 2010 David Soyez, http://code.google.com/p/yii-crontab/
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *  
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *  
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 * Crontab helps to add system cron jobs
 *
 * @author David Soyez <david.soyez@yiiframework.fr>
 * @link http://code.google.com/p/yii-crontab/
 * @copyright Copyright &copy; 2009-2010 yiiframework.fr
 * @license http://www.opensource.org/licenses/mit-license.php
 * @version 0.3
 * @package crontab
 * @since 0.1
 */
class Crontab extends CApplicationComponent{
	
	protected $jobs			= array();
	protected $directory	= NULL;
	protected $filename		= "crons";
	protected $crontabPath	= NULL;
	protected $handle		= NULL;
	
	/**
	 *	Constructor. Attempts to create directory for
	 *	holding cron jobs
	 *
	 *	@param	string	$dir		 Directory to hold cron job files (slash terminated)
	 *	@param	string	$filename	 Filename to write to
	 *	@param	string	$crontabPath Path to cron program
	 *	@access	public
	 */
	function Crontab($filename=NULL, $dir=NULL, $crontabPath=NULL){
		$result				=(!$dir) ? $this->setDirectory(Yii::getPathOfAlias('application.extensions.crontab.crontabs').'/') : $this->setDirectory($dir);
		if(!$result)
			exit('Directory error');
		$result				=(!$filename) ? $this->createCronFile("crons") : $this->createCronFile($filename);
		if(!$result)
			exit('File error');
		$this->crontabPath=($crontabPath) ? NULL : $crontabPath;
		
		$this->loadJobs();
	}
	
	

	/**
	 *	Add a job
	 *
	 *	If any parameters are left NULL then they default to *
	 *
	 *	A hyphen (-) between integers specifies a range of integers. For
	 *	example, 1-4 means the integers 1, 2, 3, and 4.
	 *
	 *	A list of values separated by commas (,) specifies a list. For
	 *	example, 3, 4, 6, 8 indicates those four specific integers.
	 *
	 *	The forward slash (/) can be used to specify step values. The value
	 *	of an integer can be skipped within a range by following the range
	 *	with /<integer>. For example, 0-59/2 can be used to define every other
	 *	minute in the minute field. Step values can also be used with an asterisk.
	 *	For instance, the value * /3 (no space) can be used in the month field to run the
	 *	task every third month...
	 *
	 *	@param	string	$command	Command
	 *	@param	mixed	$min		Minute(s)... 0 to 59
	 *	@param	mixed	$hour		Hour(s)... 0 to 23
	 *	@param	mixed	$day		Day(s)... 1 to 31
	 *	@param	mixed	$month		Month(s)... 1 to 12 or short name
	 *	@param	mixed	$dayofweek	Day(s) of week... 0 to 7 or short name. 0 and 7 = sunday
	 *  @return CCrontab return this
	 */
	function addJob($command, $min=NULL, $hour=NULL, $day=NULL, $month=NULL, $dayofweek=NULL)
	{
		$this->jobs[] = new Cronjob($command, $min, $hour, $day, $month, $dayofweek);
		
		return $this;
		
	}

	
	/**
	 *	Add an application job
	 */
	function addApplicationJob($entryScript, $commandName, $parameters = array(), $min=NULL, $hour=NULL, $day=NULL, $month=NULL, $dayofweek=NULL)
	{
		$this->jobs[] = new CronApplicationJob($entryScript, $commandName, $parameters, $min, $hour, $day, $month, $dayofweek);

		return $this;
	}
	
	/**
	 * Add job object
	 * @param mixed $job CronApplicationJob or Cronjob
	 * @return CCrontab
	 */
	public function add($job)
	{
		if($job instanceof CronApplicationJob OR $job instanceof Cronjob)
			$this->jobs[] = $job;
				
		return $this;
	}
	
	
	
	/**
	 *	Write cron command to file. Make sure you used createCronFile
	 *	before using this function of it will return false
	 *  @return CCrontab return this or false
	 */
	function saveCronFile(){
		$this->emptyCrontabFile();
		foreach ($this->jobs as $job)
		{
			if(!fwrite($this->handle, $job->getJobCommand()))
				return false;				
		}
		
		return $this;
	}
	
	
	/**
	 *	Save cron in system
	 *	@return boolean this if successful else false
	 */
	function saveToCrontab(){
		
		if(!$this->filename)
			exit('No name specified for cron file');
					
		if(exec($this->crontabPath."crontab ".$this->directory.$this->filename))
			return $this;
		else
			return false;
	}
	

	/**
	 * Get jobs
	 * @return array jobs
	 */
	public function getJobs()
	{
		return $this->jobs;
	}
	
	/**
	 * Remove a job with given offset
	 * @return CCrontab
	 */
	public function removeJob($offset = NULL)
	{
		if($offset !== NULL)
			unset($this->jobs[$offset]);
		
		return $this;
	}
	
	/**
	 * remove all jobs
	 * @return CCrontab
	 */
	public function eraseJobs()
	{
		$this->jobs = array();
		
		return $this;
	}	
	
	
	
	
	/*********************************/
	/********* Protected *************/
	/*********************************/
	
	/**
	 *	Set the directory path. Will check it if it exists then
	 *	try to open it. Also if it doesn't exist then it will try to
	 *	create it, makes it with mode 0700
	 *
	 *	@param	string	$directory	Directory, relative or full path
	 *	@access	public
	 *  @return CCrontab return this
	 */
	protected function setDirectory($directory){
		if(!$directory) return false;
		
		if(is_dir($directory)){
			if($dh=opendir($directory)){
				$this->directory=$directory;
				return $this;
			}else
				return false;
		}else{
			if(mkdir($directory, 0700)){
				$this->directory=$directory;
				return $this;
			}
		}
		return false;
	}
	
	
	/**
	 *	Create cron file
	 *
	 *	This will create a cron job file for you and set the filename
	 *	of this class to use it. Make sure you have already set the directory
	 *	path variable with the consructor. If the file exists and we can write
	 *	it then return true esle false. Also sets $handle with the resource handle
	 *	to the file
	 *
	 *	@param	string	$filename	Name of file you want to create
	 *	@access	public
	 *  @return CCrontab return this or false
	 */
	protected function createCronFile($filename=NULL){
		if(!$filename)
			return false;
		
		if(file_exists($this->directory.$filename)){
			if($this->openFile($handle,$filename, 'a+')){
				$this->handle=&$handle;
				$this->filename=$filename;
				return $this;
			}else
				return false;
		}
		
		if(!$this->openFile($handle,$filename, 'a+'))
			return false;
		else{
			$this->handle=&$handle;
			$this->filename=$filename;
			return $this;
		}
	}
			
	
	/**
	 * Load jobs from crontab file
	 */
	protected function loadJobs()
	{
		fseek($this->handle, 0);
	    while (! feof ($this->handle)) 
	    {
	        $line= fgets ($this->handle);
	        $line = trim(trim($line), "\t");
	        if(!empty($line))
	        {
		        if(CronApplicationJob::isApplicationJob($line))
		        {
		        	$obj = CronApplicationJob::parseFromCommand($line);
		        	if($obj !== FALSE)
		        		$this->jobs[] = $obj;
		        }
		        else
		        {
					$obj = Cronjob::parseFromCommand($line);
					if($obj !== FALSE)
	        			$this->jobs[] = $obj;
		        }
	        }
    	}		
	}
	
	
	/**
	 * Empty crontab file
	 * @return CCrontab
	 */
	protected function emptyCrontabFile()
	{
		$this->closeFile();
		$this->openFile($this->handle,$this->filename, 'w');
		$this->closeFile();
		$this->openFile($this->handle,$this->filename, 'a');
		
		return $this;
	}
	
	
	/**
	 * Close crontab file
	 */
	protected function closeFile()
	{
		fclose($this->handle);
	}
	
	/**
	 * Open crontab file
	 * @param ressource $handle
	 * @param string $filename
	 * @param string $accessType
	 */	
	protected function openFile(& $handle,$filename, $accessType = 'a+')
	{
		 return $handle = fopen($this->directory.$filename, $accessType);
		
	}	
	
}


class Cronjob 
{
	protected $minute		= NULL;
	protected $hour			= NULL;
	protected $day			= NULL;
	protected $month		= NULL;
	protected $dayofweek	= NULL;
	protected $command		= NULL;
	
	
	function Cronjob($command, $min=NULL, $hour=NULL, $day=NULL, $month=NULL, $dayofweek=NULL)
	{
		$this->setMinute($min);
		$this->setHour($hour);
		$this->setDay($day);
		$this->setMonth($month);
		$this->setDayofweek($dayofweek);
		$this->command = $command;	
	
		return $this;
	}
	
	/**
	 * Return the system command for the object
	 */
	public function getJobCommand()
	{
		return $this->minute." ".$this->hour." ".$this->day." ".$this->month." ".$this->dayofweek." ".$this->getCommand()."\n";
	}
	
	/**
	 * Return the command
	 */
	public function getCommand()
	{
		return $this->command;
	}	
	
	/**
	 * parse system job command and return an object
	 * Works only for regular entry
	 */
	static function parseFromCommand($command)
	{
		$vars = split("[ \t]",ltrim($command, " \t"), 6);
		
		if(count($vars) < 5)
			return false;
			
		$min 	 = $vars[0];
		$hour 		 = $vars[1];
		$day		 = $vars[2];
		$month		 = $vars[3];
		$dayofweek 	 = $vars[4];
		
		$command 	 = $vars[5];
			
		return new Cronjob($command, $min, $hour, $day, $month, $dayofweek);
	}
	
	/* setter */
	
	public function setMinute($min)
	{
		if($min=="0")
			$this->minute=0;
		elseif($min)
			$this->minute=$min;
		else
			$this->minute="*";
	}
	
	public function setHour($hour)
	{
		if($hour=="0")
			$this->hour=0;
		elseif($hour)
			$this->hour=$hour;
		else
			$this->hour="*";	
	}
	
	public function setDay($day)
	{
		$this->day=($day) ? $day : "*";
	}
	
	public function setMonth($month)
	{
		$this->month=($month) ? $month : "*";
	}
	
	public function setdayofweek($dayofweek)
	{
		$this->dayofweek=($dayofweek) ? $dayofweek : "*";
	}
	
	/* getter */
	
	public function getMinute()
	{
		return $this->minute;
	}
	
	public function getHour()
	{
		return $this->hour;
	}

	public function getDay()
	{
		return $this->day;
	}

	public function getMonth()
	{
		return $this->month;
	}

	public function getDayofweek()
	{
		return $this->dayofweek;
	}	
	
}


class CronApplicationJob extends Cronjob
{
	protected $entryScript = NULL;
	protected $commandName = NULL;
	protected $parameters  = array();
	
	function CronApplicationJob($entryScript, $commandName, $parameters = array(), $min=NULL, $hour=NULL, $day=NULL, $month=NULL, $dayofweek=NULL)
	{
		$this->entryScript = $entryScript;
		$this->commandName = $commandName;
		$this->parameters = $parameters;
		
		$command = $this->getCommand();
			
		parent::Cronjob($command, $min, $hour, $day, $month, $dayofweek);
		
		return $this;	
	
	}
	
	/**
	 * Return the system command
	 */
	public function getJobCommand()
	{
		$command =  $this->minute." ".$this->hour." ".$this->day." ".$this->month." ".$this->dayofweek." ".$this->getCommand()."\n";

		return $command;
	}	
	
	/**
	 * Return the Application command
	 */
	public function getCommand()
	{
		$command = 'php '.Yii::getPathOfAlias('webroot').'/'.$this->entryScript . '.php ' . $this->commandName;
		
		foreach($this->parameters as $parameter)
			$command .= ' ' . $parameter;	
			
		return $command;
	}
	
	/**
	 * parse system job command and return an object
	 */
	static function parseFromCommand($command)
	{
		$vars = split("[ \t]",ltrim($command, " \t"), 6);
		
		if(count($vars) < 5)
			return false;
			
		$min 	 = $vars[0];
		$hour 		 = $vars[1];
		$day		 = $vars[2];
		$month		 = $vars[3];
		$dayofweek 	 = $vars[4];
		
		$command 	 = $vars[5];
	
		if(preg_match('|^php ([^\\\]*.php) ([^\\\]*)|', $command, $matches) > 0)
		{
			$entryScript = basename($matches[1], ".php");
			$params = explode(' ',$matches[2]);
			$commandName = $params[0];
			array_shift($params);
			$parameters = $params;	
		}
		else
			return false;
		
		return new CronApplicationJob($entryScript, $commandName, $parameters, $min, $hour, $day, $month, $dayofweek);
	}
	
	/**
	 * Check if the given command would be an ApplicationJob
	 */
	static function isApplicationJob($line)
	{
		$vars = split("[ \t]",ltrim(ltrim($line), "\t"), 6);
		
		if(count($vars) < 5)
			return false;

		return (bool)preg_match("|^php ([^\\\]*.php) ([^\\\]*)|", $vars[5]);
	}
	
	
	/* setter */
	
	public function setParams($params)
	{
		$this->parameters = $params;
	}

	public function setEntryScript($entryScript)
	{
		$this->entryScript = $entryScript;
	}	

	public function setCommandName($commandName)
	{
		$this->commandName = $commandName;
	}		
	
	
	/* getter */
	
	public function getParams()
	{
		return $this->parameters;
	}	
	
	public function getEntryScript()
	{
		return $this->entryScript;
	}

	public function getCommandName()
	{
		return $this->commandName;
	}		
	
}