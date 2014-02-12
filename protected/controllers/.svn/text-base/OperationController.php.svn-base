<?php
include_once 'RequestController.php';
class OperationController extends Controller
{
	
	const OPERATION_IN_PROGRESS = 100;
	const OPERATION_SUCCESS = 200;
 	const OPERATION_ERROR = 500;
			
	private static $operation_id=1;
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */		
	// public $layout='//layouts/column2';
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}
	
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('queryoperations'),'users'=>array('admin'),
			),		
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public static function addBackgroundOperation($operation_id,$operation_command,$node)
	{
		// $cron = new Crontab("prueba");
		// $job = new CronApplicationJob('yiicmd', $operation_id, array("'datetime"), '0', '0'); // run every day
		// $job->setParams(array("'date'"));
		// $cron->add($job);
		// $cron->saveCronFile();
		$operation = new OperationModel();
		$operation->operation_id=$operation_id;
		$operation->operation_command=$operation_command;
		//$operation->operation_status="100";
		//$operation->operation_result="operation result";
		$operation->node_name=$node;
		$operation->save();
		
	}
	
	
	
	public function actionQueryOperations()
	{
		//if ((Yii::app() -> request -> isAjaxRequest) && Yii::app() -> request -> isGetRequest)) {
		$model=OperationModel::model() -> findAll();
		//Lo que hay que hacer es:
		//-- Recorrer el array de operaciones:
		//-- Acceder al servidor y comprobar si la id de la operación está activa (ESTO PUEDE SER LENTO, VER SI HACERLO DE OTRA MANERA)
		//-- Comrobar el código de la operación, para ver si ha acabado y cómo ha acabado
		//-- Si ha acabado, se elimina de la lista de operaciones pendientes
		//-- Al final del bucle se devuelve un array con las operaciones que han terminado
		//Al ser una llamada por ajax, el "caller" de esta función podrá operar en función del resultado
		$result=array();
		foreach($model as $operation)
		{				
			$noderow=NodeModel::model()->findByPk($operation->node_name);
			if (empty($noderow))
				continue;				
			
			$operation_result=json_decode(RequestController::get_operation($noderow,$operation->operation_id));
			
			if ($operation_result[0]!=OperationController::OPERATION_IN_PROGRESS)
			{				
				array_push($result,array("operation"=>$operation->operation_command,
										 "operation_id" => $operation->operation_id,
										 "node" => $operation->node_name,
										 "operation_result"=>$operation_result[0],
										 "operation_msg" => $operation_result[1]));
				//Delete from pending operations
				$operation->delete();				
			}
		}
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

