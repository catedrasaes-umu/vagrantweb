<?php
	
include_once 'CacheController.php';

class ProjectController extends Controller
{
	
	const OPERATION_PENDING = "PENDING";
	const OPERATION_IN_PROCESS = "IN PROGRESS";
	const OPERATION_SUCCESS = "SUCCESS";
	const OPERATION_ERROR = "ERROR";
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	// public function filters()
	// {
	// 	return array(
	// 		'accessControl', // perform access control for CRUD operations
	// 		'postOnly + delete', // we only allow deletion via POST request
	// 	);
	// }

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	// public function accessRules()
	// {
	// 	return array(
	// 		array('allow',  // allow all users to perform 'index' and 'view' actions
	// 			'actions'=>array('index','view','addmachine','removemachine','updatepriority',
	// 							'batchrun','batchpause','batchstop','deleteoperation','deleteallpending','deletecompleted'),
	// 			'users'=>array('*'),
	// 		),
	// 		array('allow', // allow authenticated user to perform 'create' and 'update' actions
	// 			'actions'=>array('create','update'),
	// 			'users'=>array('@'),
	// 		),
	// 		array('allow', // allow admin user to perform 'admin' and 'delete' actions
	// 			'actions'=>array('admin','delete'),
	// 			'users'=>array('admin'),
	// 		),
	// 		array('deny',  // deny all users
	// 			'users'=>array('*'),
	// 		),
	// 	);
	// }

	// public function accessRules()
	// {
	// 	return array(array('index','view','addmachine','removemachine','updatepriority',
	// 						'batchrun','batchpause','batchstop','deleteoperation',
	// 						'deleteallpending','deletecompleted','create','update','admin','delete'));
	// }

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */

	private function isAllowed($project)
	{
		if (Yii::app()->user->isSuperUser)	
			return true;

		$model=$this->loadModel($project);
		if ($model)
		{			
			$conn=Yii::app()->db;
			$command=$conn->createCommand("SELECT count(*) cuenta FROM project_user_table WHERE project_id=$project AND user_id=".Yii::app()->user->id);
			$result=$command->queryRow();
			if ($result["cuenta"]=="1")
				return true;			
		}		
		throw new CHttpException(403, "You are not authorized to perform this action.");
			
	}

	public function actionView($id)
	{

		$this->isAllowed($id);

		$project=$this->loadModel($id);
		



		$rawData = array();
		
		$machines = array();
		$nodenames = array();
		
		if (!isset($_GET['ArrayFilterForm']))
		{		

			//CachedData::model() -> deleteAll();
			foreach ($project->machines as $record) {								
				array_push($nodenames,$record->node_name);
				array_push($machines,array('node_name' =>$record -> node_name,'vm_name'=>$record ->machine_name,'priority'=>$record->priority));
			}
		}



//if (Yii::app() -> request -> isAjaxRequest) {
		
		if (isset($_GET['ArrayFilterForm']) || isset($_GET['ajaxUpdateRequest']))
		{	


			// if (isset($_GET['ArrayFilterForm']))
			// 	var_dump($_GET['ArrayFilterForm']);

			$patronvm="";
			$patronnode="";
			$patronprovider="";
			$patronstatus="";
			$patroniority="";


			if (isset($_GET['ArrayFilterForm'])){				
				$patronvm=$_GET['ArrayFilterForm']["vm_name"];
				$patronnode=$_GET['ArrayFilterForm']["node_name"];
				$patronprovider=$_GET['ArrayFilterForm']["provider"];
				$patronstatus=$_GET['ArrayFilterForm']["status"];
				$patronpriority=$_GET['ArrayFilterForm']["spriority"];
			}


			$cmodel = CachedData::model();
			//$rawData = $cmodel -> getCommandBuilder() -> createFindCommand($cmodel -> tableSchema, $cmodel -> dbCriteria) -> queryAll();


			$rawData=Yii::app()->db->createCommand("select distinct c.*,derived.priority as spriority from cached_data_table c INNER JOIN 
																	(SELECT node_name,machine_name,priority 
																	FROM project_node_machine_table pnm 																	
																	WHERE pnm.project_id=".$id.") derived 
																	ON c.node_name=derived.node_name AND c.vm_name=derived.machine_name
																	WHERE c.node_name LIKE '%".$patronnode."%' AND 
																		c.status LIKE '%".$patronstatus."%' AND 
																		c.provider LIKE '%".$patronprovider."%' AND 
																		derived.priority LIKE '%".$patronpriority."%' AND 
																		c.vm_name LIKE '%".$patronvm."%'")
																	->order('vm_name ASC')
																	->queryAll();
		}else{
			
			
			
			$cmodel = CachedData::model();

			foreach (array_unique($nodenames) as $key => $value) {
				CacheController::fillCache($value);					
			}
			
//}		
			$rawData=Yii::app()->db->createCommand("select distinct c.*,derived.priority as spriority from cached_data_table c INNER JOIN 
																	(SELECT node_name,machine_name,priority 
																	FROM project_node_machine_table pnm 																	
																	WHERE pnm.project_id=".$id.") derived 
																	ON c.node_name=derived.node_name AND c.vm_name=derived.machine_name")
																	->order('vm_name ASC')
																	->queryAll();
		
		}
		
		
		
		
		foreach ($rawData as $key => $value) {
			//o.operation_result LIKE \'%"vmname":"\'||'.$value["vm_name"].'||\'"%\'
			$auxData=Yii::app()->db->createCommand("
									SELECT count(*) as num from operation_table o
									WHERE o.operation_status=".OperationController::OPERATION_IN_PROGRESS." AND o.operation_status=100 AND o.node_name=\"".$value["node_name"]."\" AND 
									o.operation_result LIKE '%\"vmname\":\"".$value["vm_name"]."\"%'
									")->queryRow();
			
			
			
			$value["busy"] = (intval($auxData["num"])>0)?true:false;
			if ($value["busy"])
				$value["status"] = OperationController::BUSY_KEYWORD;
			
			$rawData[$key]=$value;
			
		}
		
		
		$filtersForm = new ArrayFilterForm;
		if (isset($_GET['ArrayFilterForm']))
			$filtersForm -> filters = $_GET['ArrayFilterForm'];

		$filteredData = $filtersForm -> filter($rawData);

		
		$sort = new CSort();
		$sort->defaultOrder= "spriority ASC";
		$sort->attributes = array('spriority');

		$arrayProvider = new CArrayDataProvider($filteredData, array('keyField' => false, 'sort'=>$sort, 'pagination' => array('pageSize' => 20, ), ));
		
		unset($sort);		

		// var_dump($arrayProvider->getData());
		// exit;

		// if (empty($project->launcher))		
		// 	$pending_operations=new ProjectPendingOperations;		

		// $pending_operations= new CActiveDataProvider('ProjectPendingOperations', array('sort' => array('defaultOrder' => 'priority ASC', ), ));
		$sort = new CSort();
		$sort->defaultOrder= "spriority ASC";
		$sort->attributes = array('spriority');		

		//$pending_operations= new CActiveDataProvider('ProjectPendingOperations',array('sort'=>$sort,'criteria'=>array('with'=>array('pnm'))));
		$pending_operations= new CArrayDataProvider($project->operations);
		
		
		//var_dump($pending_operations->getData());exit;
		
		
		
		$this->render('view',array(
			'model'=>$project,
			'pending_operations'=>$pending_operations,
			'dataProvider' => $arrayProvider,
			'filtersForm' => $filtersForm,			
		));
		
	}







	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new ProjectModel;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ProjectModel']))
		{
			$model->attributes=$_POST['ProjectModel'];
			if($model->save())
				$this->redirect(array('update','id'=>$model->id));
		}

		$modelc = CachedData::model();
		
		$criteria1 = new CDbCriteria();
		$criteria1->addNotInCondition("node_status", array(false),"AND");
		
		$rawData = $modelc -> getCommandBuilder() -> createFindCommand($modelc -> tableSchema, $criteria1) -> queryAll();

		$filtersForm = new ArrayFilterForm;
		$filteredData = $filtersForm -> filter($rawData);
		$arrayProvider = new CArrayDataProvider($filteredData, array('keyField' => false, 'pagination' => array('pageSize' => 20, ), ));

		$this->render('create',array(
			'model'=>$model,
			'nodes'=>$arrayProvider,
			'filtersForm' => $filtersForm,
		));
	}

	public function actionUsers($id){
		
		$this->isAllowed($id);

		$model=$this->loadModel($id);
		$dataProvider =  new CArrayDataProvider('ProjectUserModel');
		$dataProvider->setData($model->users);

		return $dataProvider;
	}

	public function actionDeleteuser($project,$user){
		
		$this->isAllowed($project);

		$model=$this->loadModel($project);
		if ($model)
		{
			$transaction=ProjectUserModel::model()->dbConnection->beginTransaction();
			try
			{
				$criteria=new CDbCriteria;
                $criteria->condition='user_id=:user_id AND project_id=:project_id';
                $criteria->params=array(':user_id'=>$user,':project_id'=>$project);
				ProjectUserModel::model()->deleteAll($criteria);
				$transaction->commit();				

			}catch(Exception $e)
			{
				$transaction->rollback();
                throw $e;
			}
		}

	}

	public function actionAdduser($project,$user){

		$this->isAllowed($project);

		$model=$this->loadModel($project);
		if ($model)
		{		
			//Evitar añadir usuarios si ya están añadidos
			$userfound=ProjectUserModel::model()->findByAttributes(array('user_id'=>$user,'project_id'=>$project));
			if (!$userfound){
				$transaction=ProjectUserModel::model()->dbConnection->beginTransaction();
				try
				{
					$modelp= new ProjectUserModel;
					$modelp->user_id=$user;
					$modelp->project_id=$project;
					$modelp->save();
					$transaction->commit();				

				}catch(Exception $e)
				{
					$transaction->rollback();
	                throw $e;
				}
			}
			$this -> redirect(array('update', 'id' => $project));
			return;			
		}
		$this -> redirect(array('index'));
	}

	public function actionManageusers($project=null){

		
		if ($project!=null)
		{
			$this->isAllowed($project);
		}else if ($project==null && !Yii::app()->user->isSuperUser){
			throw new CHttpException(403, "You are not authorized to perform this action.");
		}
			
		// $dataProvider=new CActiveDataProvider('ProjectModel');
		// if ($project!=null)
		// 	$projects=ProjectModel::model()->findByPk($project);
		// else
		$projects=ProjectModel::model()->findAll();
		
		$project_list=CHtml::listData($projects, 'id', 'project_name');

		// $data=CHtml::listData($data,'id','project_name');
		// foreach($data as $value=>$name)
	 //    {
	 //        echo CHtml::tag('option',
	 //                   array('value'=>$value),CHtml::encode($name),true);
	 //    }

		
		$filtersForm = new ArrayFilterForm;
		// if (isset($_GET['ArrayFilterForm']))
			// $filtersForm -> filters = $_GET['ArrayFilterForm'];

		// $filteredData = $filtersForm -> filter($rawData);	
		

		// $arrayProvider = new CArrayDataProvider($filteredData, array('keyField' => false, 'sort'=>$sort, 'pagination' => array('pageSize' => 20, ), ));
		
		


	    // $this->render('manageusers',array('dataProvider'=>$dataProvider));
	    $this->render('manageusers',array('project_list'=>$project_list,
	    								'dataProvider'=>$projects,
	    								'filtersForm'=>$filtersForm,
	    								));
	    // $this->render('manageusers');

	}	




	public function actionDeleteallpending($id)
	{
		$this->isAllowed($id);

		if (Yii::app()->request ->isAjaxRequest)
		{
			$conn=Yii::app()->db;
			$command=$conn->createCommand("DELETE FROM project_pending_operations_table WHERE status=".OperationController::OPERATION_WAITING." AND project_id=$id");
			$command->execute();
		}
	}	

	public function actionDeleteall($id)
	{
		$this->isAllowed($id);

		if (Yii::app()->request ->isAjaxRequest)
		{
			$conn=Yii::app()->db;
			$command=$conn->createCommand("DELETE FROM project_pending_operations_table WHERE project_id=$id");
			$command->execute();

			//shell_exec('crontab -r');
		}
	}	


	public function actionDeletecompleted($id)
	{
		$this->isAllowed($id);

		if (Yii::app()->request ->isAjaxRequest)
		{
	
			$conn=Yii::app()->db;
			$command=$conn->createCommand("DELETE FROM project_pending_operations_table WHERE status!=".OperationController::OPERATION_IN_PROGRESS." AND status !=".OperationController::OPERATION_WAITING." AND project_id=$id");
			$command->execute();
		}
	}	

	


	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id,$node=null)
	{

		$this->isAllowed($id);

		$model=$this->loadModel($id);

		//var_dump($model->machines);exit;
		// $total= new CActiveDataProvider('NodeModel', array('sort' => array('defaultOrder' => 'NODE_NAME ASC', ), ));
		// $nodenames=$total;
		
		
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ProjectModel']))
		{
			$model->attributes=$_POST['ProjectModel'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		// $tmpvalues=array();
		// foreach ($model->machines as $record) {			
		// 	array_push($tmpvalues,array('node_name' =>$record['node_name'],'vm_name'=>$record['machine_name']));			
		// }

		
		// $modelc = CachedData::model();
		// $rawData = $modelc -> getCommandBuilder() -> createFindCommand($modelc -> tableSchema, $modelc -> dbCriteria) -> queryAll();
		// if (empty($rawData))
		// {
		// 	NodeController::fillCache();	
		// }




		// $criteria1 = new CDbCriteria();		
		// $criteria1->addNotInCondition("node_name", $this->array_column($tmpvalues,'node_name'),"AND");
	 //    $criteria1->addNotInCondition("vm_name", $this->array_column($tmpvalues,'vm_name'),"OR");			    
		// $criteria1->addNotInCondition("node_status", array(false),"AND");
		// $criteria1->addCondition("vm_name!=\"\"");

		
		// $rawData = $modelc -> getCommandBuilder() -> createFindCommand($modelc -> tableSchema, $criteria1) -> queryAll();
		
		// unset($tmpvalues);
		
		// $filtersForm = new ArrayFilterForm;
		
		// if (isset($_GET['ArrayFilterForm']))
		// 	$filtersForm -> filters = $_GET['ArrayFilterForm'];

		// $filteredData = $filtersForm -> filter($rawData);
		
		// $arrayProvider = new CArrayDataProvider($filteredData, 
		// 										array('keyField' => false, 
		// 											  'pagination' => array('pageSize' => 20, ), ));

		
		$nodesall=NodeModel::model()->findAll();
		$vms=array();
		
		//Removing offline nodes from array
		foreach ($nodesall as $key => $value) {		 			 	
		 	
			if (!RequestController::ping_node($value)){
				unset($nodesall[$key]);
			}
	
			if (!is_null($node) && $node!='' && $value->node_name==$node){

				$vmsaux = RequestController::get_vm_status($value,null,false);

				if (!is_null($vmsaux)){
					
					//Una vez encontradas las virtual machines del nodo sacamos del 
					//array aquellas que el usuario ya tenga asignadas. Disponible en $mode->machines					
					$vms=$vmsaux;
					foreach ($model->machines as $keym => $valuem) {
						foreach ($vms as $keyar => $valuear) {
							if (($valuem->machine_name==$valuear["name"]) && ($valuem->node_name==$node)){
								unset($vms[$keyar]);
							}
						}
					}					
				}
					
			}

		}

		$filtersForm = new ArrayFilterForm;
		$node_list=CHtml::listData($nodesall, 'node_name', 'node_name');
		$vms= new CArrayDataProvider($vms,array('keyField'=>'name'));


		$this->render('update',array(
			'model'=>$model,
			'selectednode'=>$node,			
			'nodes'=>$vms,
			'filtersForm' => $filtersForm,
			'node_list'=>$node_list,
		));

	}







	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{

		$this->isAllowed($id);

		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{	

		// $this->isAllowed($id);
		

		if (Yii::app()->user->isSuperUser)
		{
			$dataProvider=new CActiveDataProvider('ProjectModel');
			
		}else{			
			// User::model() -> findByPk(Yii::app()->user->id);

			// $usercriteria = new CDbCriteria();
			// $usercriteria->condition="user_id=:user_id";
			// $usercriteria->params = array(
			// 					':user_id' => Yii::app()->user->id,							
			// 					); 
			// $dataProvider = new CActiveDataProvider('ProjectUserModel', array('criteria'=>$usercriteria,));

						
			
			$criteria = new CDbCriteria();

			$criteria-> select = "id,project_name ";
			$criteria-> join = " LEFT JOIN project_user_table p ON p.project_id = t.id ";

			
			$criteria->condition=" p.user_id=:user_id ";
			
			$criteria->params = array(
								':user_id' => Yii::app()->user->id,							
								); 

			$dataProvider=new CActiveDataProvider('ProjectModel',array('criteria'=>$criteria,));
		}


		$pdata=$dataProvider->getData();
		
		$newdata = array();


		foreach ($pdata as  $key => $record) {			
			$aux = array();
			$auxData=Yii::app()->db->createCommand("SELECT count(*) as num FROM operation_table o 
													INNER JOIN (select project_id,node_name,machine_name 
																from project_node_machine_table pnm 
																LEFT JOIN project_table p ON p.id=pnm.project_id
																WHERE p.id=".$record->id."	) derived 
													WHERE o.operation_status=".OperationController::OPERATION_IN_PROGRESS." AND 
													o.node_name=derived.node_name AND 
													o.operation_result LIKE CONCAT('%\"vmname\":\"',derived.machine_name,'\"%')")->queryRow();
													//o.operation_result LIKE '%\"vmname\":\"'||derived.machine_name||'\"%'")->queryRow();
			
			$aux["project_name"]=$record->project_name;
			$aux["id"]=$record->id;			
			$aux["busy"]=(intval($auxData["num"])>0)?true:false;			

			$newdata[$key]=$aux;
			
		}

		


		$this->render('index',array(
				//'dataProvider'=>$dataProvider,
				'dataProvider'=> new CArrayDataProvider($newdata,array('keyField'=>'id')),
		));	

		
	}

	/**
	 * Manages all models.
	 */
	// public function actionAdmin()
	// {
	// 	$model=new ProjectModel('search');
		

	// 	$model->unsetAttributes();  // clear any default values
	// 	if(isset($_GET['ProjectModel']))
	// 		$model->attributes=$_GET['ProjectModel'];

	// 	$this->render('admin',array(
	// 		'model'=>$model,
	// 	));
	// }


	public function actionUpdatepriority() {
		if (Yii::app() -> request -> isPostRequest) {
			
			if (isset($_POST['priority']) && 
				is_numeric($_POST['priority']) &&
				isset($_POST['vm_machine']) && 
				isset($_POST['node']) &&
				isset($_POST['project'])){
				

				if ($_POST['priority']<0){
					throw new CHttpException(500, 'Priority can\'t be a negative value.');
				}

				if ($_POST['priority']>99){
					throw new CHttpException(500, 'Priority values must be in the range from 0 to 99');
				}

				$this->isAllowed($_POST['project']);

				$model=$this->loadModel($_POST['project']);
						
				foreach ($model->machines as $machine) {						
					if (($machine->node_name == $_POST['node']) && 
						($machine->machine_name == $_POST['vm_machine'])){
							$machine->priority=$_POST['priority'];
							$machine->save();
					}
				}
				
			}else{
				if ($model === null)
					throw new CHttpException(500, 'The operation could not be performed.');
			}
		}		
	}		



	public function actionAddmachine($id,$node,$vm) {
		if (Yii::app() -> request -> isAjaxRequest) {
			
			
			$this->isAllowed($id);

			$project_id = $id;
			$model=$this->loadModel($project_id);
			 				
			
			// foreach ($vms as $value) {					
			// 	$node=$value[0];
			// 	$vm=$value[1];
				
				
				//FIXME añadir una comprobación consultando la tabla de proyectos
				//y que no añada el registro si ya existe un registro con el mismo nodo
				//y nombre de máquina
				
				//Crear el registro a insertar en la table project_node_machine_table					
				$project_machine=new ProjectNodeMachineModel();
				$project_machine->project_id=$project_id;
				$project_machine->node_name=$node;
				$project_machine->machine_name=$vm;
				$project_machine->priority=0;
				$project_machine->save();					
				
			//}
			
		}
		
	}
	
	public function actionRemovemachine($id,$node,$vm) {			
		if (Yii::app() -> request -> isAjaxRequest) {
			
			
			$this->isAllowed($id);

			$project_id = $id;
			$model=$this->loadModel($project_id);
			 				
			
			// foreach ($vms as $value) {					
			// 	$node=$value[0];
			// 	$vm=$value[1];					
				
				
				foreach ($model->machines as $machine) {
					
					if (($machine->node_name == $node) && ($machine->machine_name == $vm))
					{							
						$machine->delete();	
					}
				}
				
			//}
			
		}
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return ProjectModel the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=ProjectModel::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param ProjectModel $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='project-model-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	
	private function array_column($carray,$key)
	{
		$result=array();
		
		foreach ($carray as $value) {
			if ($value[$key]!="")
				array_push($result,$value[$key]);
			
		}
		return $result;
	}



	private function checkBatchData()
	{
		

		if (!isset($_POST['project']))
		{
			return array();
		}

		if (!isset($_POST['batch_objects']) || empty($_POST['batch_objects']))
		{
			return array();		
		}else{			
			return $_POST['batch_objects'];
		}
	}	


	private function checkLauncherActive()
	{

		$acriteria = new CDbCriteria();
		$acriteria->addInCondition("status", array(0),"AND");	    
		$ppom = ProjectPendingOperations::model();
		$active_operations = $ppom -> getCommandBuilder() -> 
										createFindCommand($ppom -> tableSchema, $acriteria) -> queryAll();
		
		return (!empty($active_operations));			
	}

	private function doBatchCommand($command)
	{

		date_default_timezone_set("Europe/Madrid");

		//Comprobamos las restricciones pertinentes para asegurarnos
		//que el Launcher no está funcionando, ya que si está funcionando
		//no dejamos añadir más operaciones mientras este no termine
		if ($this->checkLauncherActive())
		{
			throw new CHttpException(500,'The project is performing some operations, you have to wait until they end');
		}
		
		$this->isAllowed($_POST['project']);
		

		//El launcher no está funcionando así que añadimos las operaciones enviadas
		$vms=$this->checkBatchData();
		
		
		$project_id=$_POST['project'];

		$model=$this->loadModel($project_id);
		
		
		if (!empty($vms))
		{

	
			$commanduser=Yii::app()->db->createCommand("SELECT username from Users WHERE id=".Yii::app()->user->id);
			$resarray=$commanduser->queryAll();
			

			foreach ($vms as $value) {
				if (empty($value))
					continue;

				foreach ($value as $record) {
					$nodo=$record[0];
					$machine=$record[1];
					
					$auxData=Yii::app()->db->createCommand("
									SELECT count(*) as num from operation_table o
									WHERE o.operation_status=".OperationController::OPERATION_IN_PROGRESS." 
									AND o.node_name=\"".$nodo."\" AND 
									o.operation_result LIKE '%\"vmname\":\"".$machine."\"%'
									")->queryRow();
				
					//If machine is busy don't perform operations on that machine
					if (intval($auxData["num"])>0)
						continue;
					


					
					$pnm_entry = Yii::app()->db->createCommand()
											    ->select('id')
											    ->from('project_node_machine_table')										    
											    ->where('project_id=:project_id AND node_name=:node_name AND machine_name=:machine_name',
											     array(':project_id'=>$project_id,':node_name'=>$nodo,':machine_name'=>$machine))
											    ->queryRow();

					$result=false;
					if ($pnm_entry!=false)
					{
						$pnm_id=$pnm_entry["id"];
						$pendingop = new ProjectPendingOperations();
						$pendingop->pnm_id=$pnm_id;
						$pendingop->project_id=$project_id;
						$pendingop->command=$command;

						$current_timestamp = new DateTime('NOW');	

						$pendingop->operation_timestamp=$current_timestamp->format('Y-m-d H:i:s');
						$pendingop->username = $resarray[0]['username'];
						
						$result=$pendingop->save();


					}


					// if ($result){						
					// 	echo CJSON::encode(array('status'=>'Operation Queued'));
					// }else
					if (!$result)					
						throw new CHttpException(500,'Error adding an operation');
					
					
				}
				
			}
		}else{
			//Si está vacio el array de máquinas es porque se quieren ejecutar acciones
			//sobre todas las máquinas
			$commanduser=Yii::app()->db->createCommand("SELECT username from Users WHERE id=".Yii::app()->user->id);
			$resarray=$commanduser->queryAll();
			
			foreach ($model->machines as $machine) {
				
				$auxData=Yii::app()->db->createCommand("
									SELECT count(*) as num from operation_table o
									WHERE o.operation_status=".OperationController::OPERATION_IN_PROGRESS." 
									AND o.node_name=\"".$machine->node_name."\" AND 
									o.operation_result LIKE '%\"vmname\":\"".$machine->machine_name."\"%'
									")->queryRow();
				
				//If machine is busy don't perform operations on that machine
				if (intval($auxData["num"])>0)
					continue;
				
				$pnm_entry = Yii::app()->db->createCommand()
											    ->select('id')
											    ->from('project_node_machine_table')										    
											    ->where('project_id=:project_id AND node_name=:node_name AND machine_name=:machine_name',
											     array(':project_id'=>$project_id,':node_name'=>$machine->node_name,':machine_name'=>$machine->machine_name))
											    ->queryRow();
				$result=false;
				
				if ($pnm_entry!=false)
				{
					$pnm_id=$pnm_entry["id"];
					$pendingop = new ProjectPendingOperations();
					$pendingop->pnm_id=$pnm_id;
					$pendingop->project_id=$project_id;
					$pendingop->command=$command;

					$current_timestamp = new DateTime('NOW');	
					$pendingop->operation_timestamp=$current_timestamp->format('Y-m-d H:i:s');
					$pendingop->username = $resarray[0]['username'];

					$result=$pendingop->save();


					if (!$result)
					{
						var_dump($pendingop->getErrors());
					}
				}
				if (!$result)
						throw new CHttpException(500,'Error performing the operations');
				

			}
			
		}

		
		
		//Si no está activo el launcher lo lanzamos
		$launcher = new LauncherController();
		$launcher->actionExecute();
		
		
		

		echo CJSON::encode('Performing operations');
		

	}


	public function actionBatchrun()
	{					

		$this->doBatchCommand("run");
		
	}	
	

	public function actionBatchpause()
	{

		$this->doBatchCommand("pause");		
		
	}

	
	public function actionBatchstop()
	{
		
		$this->doBatchCommand("halt");
		
	}

	public function actionDeleteoperation($id)
	{
		$this->isAllowed($id);

		ProjectPendingOperations::model()->findByPk($id)->delete();
	}


	public function getRelatedNode($data,$row){		
		
		$node = ProjectNodeMachineModel::model()->findByPk($data->pnm_id);
		
		return $node->node_name;
	}

	public function getRelatedMachine($data,$row){		
		
		$node = ProjectNodeMachineModel::model()->findByPk($data->pnm_id);
		
		return $node->machine_name;
	}

	public function getRelatedMachinePriority($data,$row){		

		$node = ProjectNodeMachineModel::model()->findByPk($data->pnm_id);
		
		return $node->priority;
	}

	public function getStatusString($data,$row)
	{
		switch ($data->status) {
			case OperationController::OPERATION_IN_PROGRESS:
				return self::OPERATION_IN_PROCESS;
				break;
			case OperationController::OPERATION_SUCCESS:
				return self::OPERATION_SUCCESS;
				break;
			case OperationController::OPERATION_ERROR:
				return self::OPERATION_ERROR;
				break;
			default:
				return self::OPERATION_PENDING;
				break;
		}
			
	}
}
