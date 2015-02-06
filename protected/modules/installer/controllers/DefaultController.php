<?php

class DefaultController extends Controller
{	
	private $puser=null;
	private $ppass=null;
	private $conn=null;
	private $config=null;
	private $iniconfig=null;
	private $iniconfigtmp=null;

	public function init(){		

		if ($this->isInstalled())			
			$this->redirect(Yii::app()->homeUrl);

		$this->iniconfigtmp=getcwd().'/protected/config/config.tmp';
		$this->iniconfig=getcwd().'/protected/config/config.ini';

		if (file_exists($this->iniconfigtmp))
			$this->config=parse_ini_file($this->iniconfigtmp);

	}	

	public function filters()
	{		
	        return array(
	                'rights - index,checkpermissions,checkdbms,checkconnection,checkconnectionuser,createdb,createuser,createstructure,finishinstall',
	        );
	}


	public function actionFinishInstall()
	{
		rename($this->iniconfigtmp,$this->iniconfig);		
	}

	public function actionIndex()
	{		
		$this->render('index');
	}

	public function actionCheckpermissions() {
		
		$basepath=getcwd();		
		$response_array=array();

		if (!is_writable($basepath.DIRECTORY_SEPARATOR.'assets'))
		{

			header('Content-type: application/json');
			$response_array['status'] = 'error'; 
			$response_array['msg'] = 'The directory \''.$basepath.DIRECTORY_SEPARATOR.'assets'.' is not writable.'; 
			echo json_encode($response_array);
			return;
			

			//throw new CHttpException(500, 'The directory \''.$basepath.DIRECTORY_SEPARATOR.'assets'.' is not writable.');
		}

		if (!is_writable($basepath.DIRECTORY_SEPARATOR.'protected/runtime'))
		{
			header('Content-type: application/json');
			$response_array['status'] = 'error'; 
			$response_array['msg'] = 'The directory \''.$basepath.DIRECTORY_SEPARATOR.'protected/runtime'.' is not writable.'; 
			echo json_encode($response_array);
			return;
			
			//throw new CHttpException(500, 'The directory \''.$basepath.DIRECTORY_SEPARATOR.'protected/runtime'.' is not writable.');
		}
		header('Content-type: application/json');
		$response_array['status'] = 'success'; 		
		echo json_encode($response_array);
	}

	public function actionCheckdbms() {				
		$response_array=array();
		if (shell_exec('mysql -V') == ''){
			header('Content-type: application/json');
			$response_array['status'] = 'error'; 
			$response_array['msg'] = 'Mysql is not installed'; 
			echo json_encode($response_array);
			return;
			//throw new CHttpException(500, 'Mysql is not installed');
		}
		header('Content-type: application/json');
		$response_array['status'] = 'success'; 		
		echo json_encode($response_array);
	}

	public function actionCheckconnection($user,$pass) {
		$response_array=array();
		
		try
		{
			if (!$link = @mysql_connect('localhost', $user, $pass))
			{

				header('Content-type: application/json');
				$response_array['status'] = 'error'; 
				$response_array['msg'] = 'Can\'t connect to Mysql with privelege credentials';
				echo json_encode($response_array);
				return;
			}

			mysql_close($link);
			

		}catch(CDbException $e)
		{

			echo $e->getMessage();
			header('Content-type: application/json');
			$response_array['status'] = 'error'; 
			$response_array['msg'] = 'Can\'t connect to Mysql with privelege credentials';
			echo json_encode($response_array);
			return;
		}		
		
		
		
		header('Content-type: application/json');
		$response_array['status'] = 'success'; 		
		echo json_encode($response_array);
		
	}

	public function actionCheckconnectionUser() {
		
		$response_array=array();	
		$bbdd=$this->config["dbname"];
		$user=$this->config["user"];
		$pass=$this->config["password"];
	
		
		
		if (!$link = @mysql_connect('localhost', $user, $pass)) 		    		
		{
			header('Content-type: application/json');
			$response_array['status'] = 'error'; 
			$response_array['msg'] = 'Can\'t connect to Mysql with the assigned user';
			echo json_encode($response_array);
			return;
		   //throw new CHttpException(500, 'Can\'t connect to Mysql with the assigned user');
		}
		

		$sql="SHOW DATABASES LIKE '".$bbdd."'";
		$result=mysql_query($sql,$link);
		
		if (mysql_num_rows($result)==0){
			header('Content-type: application/json');
			$response_array['status'] = 'error'; 
			$response_array['msg'] = 'The created user does not have access to the database';
			echo json_encode($response_array);
			return;
			//throw new CHttpException(500, 'The created user does not have access to the database');
		}

		
		mysql_close($link);
		header('Content-type: application/json');
		$response_array['status'] = 'success'; 		
		echo json_encode($response_array);
		
	}

	public function actionCreatedb($bbdd,$puser,$ppass,$user='',$pass=''){		
		$response_array=array();

		
		if (!$link = @mysql_connect('localhost', $puser, $ppass)) {
			header('Content-type: application/json');
			$response_array['status'] = 'error'; 
			$response_array['msg'] = 'Can\'t connect to Mysql with privelege credentials';
			echo json_encode($response_array);
			return;
			//throw new CHttpException(500, 'Can\'t connect to Mysql with privelege credentials');
		}else{			
			if ((empty($user) && !empty($pass))|| (!empty($user) && empty($pass))){
				header('Content-type: application/json');
				$response_array['status'] = 'error'; 
				$response_array['msg'] = 'Incorrect database user information provided';
				echo json_encode($response_array);
				return;
				//throw new CHttpException(500, 'Incorrect database user information provided');
			}

			if (empty($puser) || empty ($ppass)){
				header('Content-type: application/json');
				$response_array['status'] = 'error'; 
				$response_array['msg'] = 'Incorrect privilege database user information provided';
				echo json_encode($response_array);
				return;
				//throw new CHttpException(500, 'Incorrect privilege database user information provided');
			}
			

			
			$sql="SHOW DATABASES LIKE '".$bbdd."'";
			$result=mysql_query($sql,$link);
			
			if (mysql_num_rows($result)>0){
				header('Content-type: application/json');
				$response_array['status'] = 'error'; 
				$response_array['msg'] = 'The database already exists. Delete it first manually or choose antoher database name';
				echo json_encode($response_array);
				return;
				//throw new CHttpException(500, 'The database already exists. Delete it first manually or choose antoher database name');
			}

			$sql="CREATE DATABASE ".$bbdd;
			$result=mysql_query($sql,$link);
			
			if (!$result){
				header('Content-type: application/json');
				$response_array['status'] = 'error'; 
				$response_array['msg'] = 'Error: The database could not be created';
				echo json_encode($response_array);
				return;
				//throw new CHttpException(500, 'Error: The database could not be created');
			}		

			

			/*$iniconfig=getcwd().'/protected/config/config.ini';
			// $suser=(empty($user)?$puser:$user);
			// $spassword=(empty($pass)?$ppass:$pass);

			$this->config = array(
                'db' => array(
                	'dbname'=>$bbdd,
                    'user' => $puser,
                    'password' => $ppass,                    
                ),             
        	);

			
			$this->write_ini_file($this->config,$iniconfig,true);

			//Setting file rights
			chmod($iniconfig, 0640);*/

			mysql_close($link);
		}
		header('Content-type: application/json');
		$response_array['status'] = 'success'; 		
		echo json_encode($response_array);
	}

	public function actionCreateuser($newuser,$newpass){		
		$response_array=array();
		$bbdd=$this->config["dbname"];
		$user=$this->config["user"];
		$pass=$this->config["password"];
	

		

		if (!$link = @mysql_connect('localhost', $user, $pass))
		{
			header('Content-type: application/json');
			$response_array['status'] = 'error'; 
			$response_array['msg'] = 'Can\'t connect to Mysql with privelege credentials';
			echo json_encode($response_array);
			return;
		}

		//$sql="CREATE USER '".$newuser."'@'localhost' IDENTIFIED BY '".$newpass."'";

		$sql="SELECT User FROM mysql.user where User='".$newuser."'";
		
		
		$result=mysql_query($sql,$link);
		

		
		//No elimino la base de datos, porque puede ser que el usuario ya exista
		//y se produzca un error
		if (mysql_num_rows($result)==0)
		{
			$sql="CREATE USER '".$newuser."'@'localhost' IDENTIFIED BY '".$newpass."'";			
			$result=mysql_query($sql,$link);
			if (!$result)
			{
				$sql="DROP DATABASE ".$bbdd;
				mysql_query($sql,$link);		
				header('Content-type: application/json');
				$response_array['status'] = 'error'; 
				$response_array['msg'] = 'Error creating new user';
				echo json_encode($response_array);
				return;
			}
		}else{
			$response_array['msg'] = 'User already exists';
		}

				
		$sql="GRANT ALL PRIVILEGES ON ".$bbdd.".* TO '".$newuser."'@'localhost'";		
		
		$result=mysql_query($sql,$link);
		if (!$result)
		{		
			header('Content-type: application/json');
			$response_array['status'] = 'error'; 
			$response_array['msg'] = 'Error granting permissions to new user';
			echo json_encode($response_array);
			return;
		}


		$sql="FLUSH PRIVILEGES";
		mysql_query($sql,$link);

		mysql_close($link);


		//$iniconfig=getcwd().'/protected/config/config.tmp';

		$this->config = array(
	        'db' => array(
	        	'dbname'=>$bbdd,
	            'user' => $newuser,
	            'password' => $newpass,                    
	        ),             
		);

		
		$this->write_ini_file($this->config,$this->iniconfigtmp,true);


		header('Content-type: application/json');
		$response_array['status'] = 'success'; 		
		echo json_encode($response_array);
	}


	public function actionCreatestructure($bbdd,$puser,$ppass){
		$response_array=array();
		
		// $result=Yii::app()->db->createCommand("CREATE TABLE IF NOT EXISTS node_table (
		// 									    node_name VARCHAR(128) PRIMARY KEY NOT NULL,
		// 									    node_address VARCHAR(128) NOT NULL,
		// 									    node_port INTEGER NOT NULL,
		// 									    node_password VARCHAR(64) NOT NULL
		// 										)")->execute();


		$sqldata=getcwd().'/protected/data/schema.mysql.sql';

		if (!file_exists($sqldata)){
			header('Content-type: application/json');
			$response_array['status'] = 'error'; 
			$response_array['msg'] = 'Error: The mysql structure file could not be found';
			echo json_encode($response_array);
			return;
			//throw new CHttpException(500, 'Error: The mysql structure file could not be found');
		}

		$sqlinfo = file_get_contents($sqldata);	

		if ($sqlinfo===false){
			header('Content-type: application/json');
			$response_array['status'] = 'error'; 
			$response_array['msg'] = 'Error: The structure content is not valid';
			echo json_encode($response_array);
			return;
			//throw new CHttpException(500, 'Error: The structure content is not valid');
		}

		
		
		

		try
		{
			if (shell_exec('mysql -u '.$puser.' --password='.$ppass.' '.$bbdd." <".$sqldata) != ''){
				header('Content-type: application/json');
				$response_array['status'] = 'error'; 
				$response_array['msg'] = 'Error importing the MySql structure';
				echo json_encode($response_array);
				return;
			}
			//Yii::app()->db->createCommand($sqlinfo)->execute();			
		}catch(CDbException $e)
		{
			header('Content-type: application/json');
			$response_array['status'] = 'error'; 
			$response_array['msg'] = 'Error importing the MySql structure';
			echo json_encode($response_array);
			return;
			//throw new CHttpException(500, 'Error importing the MySql structure');
		}
		
		//$iniconfig=getcwd().'/protected/config/config.tmp';
		// $suser=(empty($user)?$puser:$user);
		// $spassword=(empty($pass)?$ppass:$pass);

		$this->config = array(
			'db' => array(
				'dbname'=>$bbdd,
				'user' => $puser,
				'password' => $ppass,                    
			),             
		);

			
		$this->write_ini_file($this->config,$this->iniconfigtmp,true);

		//Setting file rights
		chmod($this->iniconfigtmp, 0640);

		header('Content-type: application/json');
		$response_array['status'] = 'success'; 		
		echo json_encode($response_array);
		
	}




	//PRIVATE FUNCIONS
	private function write_ini_file($assoc_arr, $path, $has_sections=FALSE) { 
	    $content = ""; 
	    if ($has_sections) { 
	        foreach ($assoc_arr as $key=>$elem) { 
	            $content .= "[".$key."]\n"; 
	            foreach ($elem as $key2=>$elem2) { 
	                if(is_array($elem2)) 
	                { 
	                    for($i=0;$i<count($elem2);$i++) 
	                    { 
	                        $content .= $key2."[] = \"".$elem2[$i]."\"\n"; 
	                    } 
	                } 
	                else if($elem2=="") $content .= $key2." = \n"; 
	                else $content .= $key2." = \"".$elem2."\"\n"; 
	            } 
	        } 
	    } 
	    else { 
	        foreach ($assoc_arr as $key=>$elem) { 
	            if(is_array($elem)) 
	            { 
	                for($i=0;$i<count($elem);$i++) 
	                { 
	                    $content .= $key."[] = \"".$elem[$i]."\"\n"; 
	                } 
	            } 
	            else if($elem=="") $content .= $key." = \n"; 
	            else $content .= $key." = \"".$elem."\"\n"; 
	        } 
	    } 

	    if (!$handle = fopen($path, 'w')) { 
	        return false; 
	    }

	    $success = fwrite($handle, $content);
	    fclose($handle); 

	    return $success; 
	}

}
