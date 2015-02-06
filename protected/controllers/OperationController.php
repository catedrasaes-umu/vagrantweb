<?php
//include_once 'RequestController.php';
include_once 'LauncherController.php';
include_once 'CacheController.php';
class OperationController extends CController
{
	
	const OPERATION_WAITING = -1;
	const OPERATION_IN_PROGRESS = 100;
	const OPERATION_COMPLETED = 1;
	const OPERATION_SUCCESS = 200;
 	const OPERATION_ERROR = 500;
 	const OPERATION_NOT_FOUND = 404;

 	const VM_UP = "VM_UP";
 	const VM_HALT = "VM_HALT";
 	const VM_SUSPEND = "VM_PAUSE";
 	const VM_RESUME = "VM_RESUME";
 	const VM_DESTROY = "VM_DELETE";
 	const VM_STATUS = "VM_STATUS";
 	const VM_ADD = "VM_ADD";
 	const BOX_ADD = "BOX_ADD";
 	const BOX_STATUS = "BOX_STATUS";

 	const BUSY_KEYWORD = "BUSY";
			
	private static $operation_id=1;
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */		
	// public $layout='//layouts/column2';
	// public function filters()
	// {
	// 	return array(
	// 		'accessControl', // perform access control for CRUD operations
	// 		'postOnly + delete', // we only allow deletion via POST request
	// 	);
	// }
	
	// public function accessRules()
	// {
	// 	return array(
	// 		array('allow',  // allow all users to perform 'index' and 'view' actions
	// 			'actions'=>array('queryoperations'),'users'=>array('admin'),
	// 		),		
	// 		array('deny',  // deny all users
	// 			'users'=>array('*'),
	// 		),
	// 	);
	// }

	// public function accessRules()
	// {
	// 	return array(array('queryoperations'));
	// }

	private static function detailedToString($op)
	{	
		switch($op)
		{
			case RouteController::VM_UP_ROUTE:
				return OperationController::VM_UP;
				break;
			case RouteController::VM_HALT_ROUTE:
				return OperationController::VM_HALT;
				break;
			case RouteController::VM_SUSPEND_ROUTE:
				return OperationController::VM_SUSPEND;
				break;				
			case RouteController::VM_RESUME_ROUTE:
				return OperationController::VM_RESUME;
				break;
			break;
			case RouteController::VM_DESTROY_ROUTE:
				return OperationController::VM_DESTROY;
				break;
			case RouteController::VM_ADD_ROUTE:			
				return OperationController::VM_ADD;
				break;
			break;
			case RouteController::BOX_ADD_ROUTE:
				return OperationController::BOX_ADD;				
			break;
			
		}

	}

	public static function addBackgroundOperation($operation_id,$operation_command,$detailedop,$node,$params)
	{
		// $cron = new Crontab("prueba");
		// $job = new CronApplicationJob('yiicmd', $operation_id, array("'datetime"), '0', '0'); // run every day
		// $job->setParams(array("'date'"));
		// $cron->add($job);
		// $cron->saveCronFile();
		date_default_timezone_set("Europe/Madrid");

		$commanduser=Yii::app()->db->createCommand("SELECT username from Users WHERE id=".Yii::app()->user->id);
		$resarray=$commanduser->queryAll();
		

		$operation = new OperationModel();
		$operation->operation_id=$operation_id;
		$operation->operation_command=$operation_command;
		$operation->username=$resarray[0]['username'];

		$aux=OperationController::detailedToString($detailedop);
		
		$operation->operation_specific=$aux;		
		
		$params['status'] ='Operation queued';
		//var_dump($params);
		//$operation->operation_status="100";
		$aux1="[".json_encode($params)."]";
		$operation->operation_result=$aux1;


		


		$operation->node_name=$node;
		$current_timestamp = new DateTime('NOW');
		$operation->operation_timestamp = $current_timestamp->format('Y-m-d H:i:s');
		$operation->setIsNewRecord(true);

		$operation->save();	
	
	
	}

	private function statusToClass($command,$status)
	{
		$icon="fa fa-tasks fa-fw";
		
		if ($status==4)
		{
			$icon = "fa fa-bolt fa-fw";
		}else{
			switch ($command) {
		    case OperationController::VM_UP:
		        //RUN
		        $icon = "fa fa-upload fa-fw";
		        break;
		    case OperationController::VM_SUSPEND:
		    	//Pause
		        $icon = "fa fa-pause fa-fw";
		        break;
		    case OperationController::VM_HALT:
		    	//Halt
		        $icon = "fa fa-download fa-fw";
		        break;		    
		   default:
		    	//other
		        $icon = "fa fa-tasks fa-fw";
		        break;
			}	
		}

		

		return $icon; 

	}
	

	private function statusClass($command,$status)	
	{
		$result=5;

		switch ($status) {
			case OperationController::OPERATION_COMPLETED:
				$result=5;
				break;
			case OperationController::OPERATION_IN_PROGRESS:
				$result=5;
				break;
			case OperationController::OPERATION_SUCCESS:
				$result=0;
				break;
			case OperationController::OPERATION_ERROR:
				$result=4;
				break;			
			case OperationController::OPERATION_NOT_FOUND:
				$result=4;
				break;			
		}

		return $this->statusToClass($command,$result);
			
	 
	}

	private function statusMsg($status)	
	{
		$result="Operation Queued";
		switch ($status) {
			case OperationController::OPERATION_COMPLETED:
				$result="Operation Finished";
				break;
			case OperationController::OPERATION_IN_PROGRESS:
				$result="Operation Queued";
				break;
			case OperationController::OPERATION_SUCCESS:
				$result="Operation Succeed";
				break;
			case OperationController::OPERATION_ERROR:
			case OperationController::OPERATION_NOT_FOUND:
				$result="Operation Failed";
				break;			
		}
		return $result;

	}


	public function actionClear()
	{		
		OperationModel::model()->deleteAll();
	}
	

	public function actionLast($limit=7,$node=null)
	{

		
		
		$model=OperationModel::model() -> findAllBySql("SELECT * from operation_table ORDER BY ID DESC LIMIT ".$limit);	
		
		
		//$result = array();
		$res="";

		date_default_timezone_set("Europe/Madrid");
		$current_timestamp = new DateTime('NOW');



		foreach($model as $operation)
		{
			
			/*array_push($result,array("operation"=>$operation->operation_command,
									 "operation_id" => $operation->operation_id,
									 "node" => $operation->node_name,
									 "operation_result"=>$operation->operation_status,
									 "operation_msg" => $operation->operation_result));
			*/
			


			if (($operation->operation_command!=OperationController::BOX_STATUS) && (($node==null) || ($node!=null && $node==$operation->node_name)))
			{
				

				$rarray=json_decode($operation->operation_result,true);			
				
				

				$statusC=$this->statusClass($operation->operation_specific,$operation->operation_status);
				$statusM=$this->statusMsg($operation->operation_status);

				$timestamp = new DateTime($operation->operation_timestamp);


				$diff = $current_timestamp->diff($timestamp);

				$diffdias = $diff->format('%a');
				$diffhoras = $diff->format('%h');
				$diffmin = $diff->format('%i');

				$diffmain = "";
				if ($diffdias != 0)
				{
					$diffmain = $timestamp->format('d/m/Y');	
				}else if ($diffhoras != 0){
					$diffmain = $timestamp->format('H:i');
				}else{
					$diffmain = $diffmin." minutes ago";
				}
				

				
				if (isset($rarray[0]) && isset($rarray[0]["vmname"]))
				{
				
					$res.="<a href='#' id='".$operation->operation_id."' class='list-group-item'>".
						  "<i class='".$statusC."'></i><span class='notheader'>&nbsp;".$statusM.'</span>'.
						"<span class='pull-right text-muted small' id='elapsed'><em>".$diffmain."</em>".
						"</span>".
						"<div class='notificacionmsg' style='display:none'>".
						"<span><strong>Node:</strong>&nbsp;".$operation->node_name."</span><br/>".
						"<span><strong>VM:</strong>&nbsp;".$rarray[0]["vmname"]."</span><br/>".
						"<span><strong>Command:</strong>&nbsp;".$operation->operation_specific."</span><br/>".
						"<span><strong>User:</strong>&nbsp;".$operation->username."</span><br/>";
						if ($operation->operation_status==OperationController::OPERATION_ERROR)
							$res.="<span><strong>Error:</strong>&nbsp;".$rarray[0]["status"]."</span>";

						$res.="</div></a>";
				}else if ($operation->operation_status==OperationController::OPERATION_NOT_FOUND) {
					$res.="<a href='#' id='".$operation->operation_id."' class='list-group-item'>".
						  "<i class='".$statusC."'></i><span class='notheader'>&nbsp;".$statusM.'</span>'.
						"<span class='pull-right text-muted small' id='elapsed'><em>".$diffmain."</em>".
						"</span>".
						"<div class='notificacionmsg' style='display:none'>".
						"<span><strong>Node:</strong>&nbsp;".$operation->node_name."</span><br/>".						
						"<span><strong>Command:</strong>&nbsp;".$operation->operation_specific."</span><br/>".
						"<span><strong>User:</strong>&nbsp;".$operation->username."</span><br/>".						
						"<span><strong>Error:</strong>&nbsp;".$rarray[0]["status"]."</span>".
						"</div></a>";
				}else{					
					//La entrada está mal formada así que la eliminamos de la lista
					$operation->delete();
				}

			}else{
				//TODO AÑADIR ENTRADA PARA CUANDO SE TRATA DE OPERACIOES CON BOX
			}
			
		}
		
		
		echo $res;

	}
	
	//Deprecated. Ya no la utilizo
	private function addFinishedOperation($id,$node,$command,$scommand,$result,$status)
	{		
		date_default_timezone_set("Europe/Madrid");
		
		/*

		$operation = new OperationModel();
		$operation->operation_id=$id;
		$operation->operation_command=$command;
		$operation->operation_status=$status;
		$operation->operation_result=substr($result,0,200);
		$operation->operation_specific = $scommand;
		$operation->node_name=$node;*/
		
		$current_timestamp = new DateTime('NOW');
		// $operation->operation_timestamp = $current_timestamp->format('Y-m-d H:i:s');
		// $operation->setIsNewRecord(false);
		
		//$operation->save();

		
		$conn=Yii::app()->db;
		$command=$conn->createCommand("UPDATE operation_table SET operation_status=".$status.",operation_result='".mysql_escape_string($result)."',
									 operation_timestamp='".$current_timestamp->format('Y-m-d H:i:s')."' WHERE operation_id=".$id);

		$command->execute();

	}
	
	

	public function actionQueryOperations()
	{
		date_default_timezone_set("Europe/Madrid");
		//if ((Yii::app() -> request -> isAjaxRequest) && Yii::app() -> request -> isGetRequest)) {
		

		$model=OperationModel::model() -> findAllBySql("SELECT * from operation_table where operation_status=".OperationController::OPERATION_IN_PROGRESS." ORDER BY ID ASC");
		

		//Lo que hay que hacer es:
		//-- Recorrer el array de operaciones:
		//-- Acceder al servidor y comprobar si la id de la operación está activa (ESTO PUEDE SER LENTO, VER SI HACERLO DE OTRA MANERA)
		//-- Comrobar el código de la operación, para ver si ha acabado y cómo ha acabado
		//-- Si ha acabado, se elimina de la lista de operaciones pendientes
		//-- Al final del bucle se devuelve un array con las operaciones que han terminado
		//Al ser una llamada por ajax, el "caller" de esta función podrá operar en función del resultado
		$result=array();
		
		$conn=Yii::app()->db;

		foreach($model as $operation)
		{			

			
			
			$noderow=NodeModel::model()->findByPk($operation->node_name);
			
			if (empty($noderow))
				continue;				
			
			$operation_result=null;
			try
			{
				$operation_result=json_decode(RequestController::get_operation($noderow,$operation->operation_id));				
			}catch(CHttpException $e){
				//Se si genera una excepción y se debe a que no encuentra una operación
				//La eliminamos para que no quede residente
				if ($e->statusCode==OperationController::OPERATION_NOT_FOUND){
					$operation->delete();
				}else
					throw $e;				
			}

			
			//if $operation_result is null it means that the remote node server is not responding
			//That also means that all pending operations in the remote node won't update 
			if (is_null($operation_result))
				continue;

			
			
			if ($operation_result[0]!=OperationController::OPERATION_IN_PROGRESS)
			{				
				


				array_push($result,array("operation"=>$operation->operation_command,
										 "operation_id" => $operation->operation_id,
										 "node" => $operation->node_name,
										 "operation_result"=>$operation_result[0],
										 "operation_msg" => $operation_result[1]));
				
				

				/*$this->addFinishedOperation($operation->operation_id,
									$operation->node_name,
									$operation->operation_command,
									$operation->operation_specific,
									$operation_result[1],
									$operation_result[0]);				*/


				//var_dump($operation);
				$current_timestamp = new DateTime('NOW');
				$operation->operation_timestamp=$current_timestamp->format('Y-m-d H:i:s');
				// $operation->operation_result=substr($operation_result[1],0,200);
				$operation->operation_result=$operation_result[1];
				$operation->operation_status=$operation_result[0];

				$operation->save();
				
				
				//Se ha terminado la operación, actualizamos la información de las operaciones
				//de proyectos y llamamos a su launcher
				
				$statmsg="OK";
				if (($operation_result[0]==OperationController::OPERATION_ERROR) || ($operation_result[0]==OperationController::OPERATION_NOT_FOUND))
					$statmsg=substr($operation_result[1],0,200);

				
				//COMPROBAMOS SI EXISTE LA OPERACIÓN DENTRO DE LOS PROYECTOS
				//$conn=Yii::app()->db;
				$command=$conn->createCommand("SELECT * from project_pending_operations_table WHERE operation_id=".$operation->operation_id);
				$resarray=$command->queryAll();

				
				if (sizeof($resarray)==1)
				{					

					
					$command=$conn->createCommand("UPDATE project_pending_operations_table SET status=".$operation_result[0].",
												 status_msg='".mysql_escape_string($statmsg)."' WHERE operation_id=".$operation->operation_id);

					

					$command->execute();

					$launcher = new LauncherController();
					$launcher->actionExecute();
					
				}
				unset($resarray);
				unset($command);

				if (($operation->operation_command==OperationController::VM_STATUS) && 
					($operation_result[0]==OperationController::OPERATION_SUCCESS))
				{

					$res=json_decode($operation_result[1],true);
					
					
					if (!isset($res[0]["vmname"]) || !isset($res[0]["status"])){
						$operation->delete();
					}else{

						CacheController::updateVirtualMachineStatus($operation->node_name,
																	$res[0]["vmname"],
																	$res[0]["provider"], //en este punto de la información no tenemos el provider
																	$res[0]["status"]);
					}

						
					
				}






				// $operation->operation_status=OperationController::OPERATION_COMPLETED;
				// $operation->save();
				
				//Delete from pending operations
				//$operation->delete();				
			}
		}
		unset($conn);
		echo json_encode($result);
		
		
		//}
				
	}
	
	
	
	public function loadModel($id) {
		$model = OperationModel::model() -> findByPk($id);
		if ($model === null)
			throw new CHttpException(404, 'The requested page does not exist.');
		return $model;
	}
	
}

//call_user_func("BackgroundOperationController::checkBackgroundOperation");

