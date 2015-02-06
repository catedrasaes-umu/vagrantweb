<?php
//include 'RequestController.php';
//include '../models/CachedData.php';
include 'CacheController.php';
class NodeController extends Controller {

	const EXPIRATION_TIME = 150;
	const CSVFILENAME = "nodes.csv";
	//Seconds
	// const ONLINE_STR = "ONLINE";
	// const OFFLINE_STR = "OFFLINE";

	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout = '//layouts/column2';

	/**
	 * @return array action filters
	 */
	// public function filters() {
	// 	return array('accessControl', // perform access control for CRUD operations
	// 	'postOnly + delete', // we only allow deletion via POST request
	// 	);
	// }


	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	// public function accessRules() {
	// 	return array( array('allow', // allow all users to perform 'index' and 'view' actions
	// 	'actions' => array('index', 'view', 'editconfig', 
	// 						'showconfig', 'uploadconfig','passwordchange'), 'users' => array('@'), ), array('allow', // allow authenticated user to perform 'create' and 'update' actions
	// 	'actions' => array('create', 'update'), 'users' => array('@'), ), array('allow', // allow admin user to perform 'admin' and 'delete' actions
	// 	'actions' => array('admin', 'delete'), 'users' => array('admin'), ), array('deny', // deny all users
	// 	'users' => array('*'), ), );
	// }

	// public function accessRules() {
	// 	return array( array('index', 'view', 'editconfig', 'showconfig', 'uploadconfig','passwordchange',
	// 						'create', 'update','admin', 'delete'));
	// }

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionDownloadsinfo($node)
	{
		$model = $this -> loadModel($node);

		try
		{
			$downloads = RequestController::get_node_boxes_downloads($model);
			
			if (is_null($downloads) || empty($downloads))
			{
				$downloadProvider=new CArrayDataProvider(array());
			}else{
				$downloadProvider=new CArrayDataProvider($downloads, array(
				    'keyField' => 'box_name',
				    'sort'=>array(
				        'attributes'=>array(
				             'box_name', 'box_url', 'box_progress','box_remaining','download_status',
				        ),
				    ),
				    'pagination'=>array(
				        'pageSize'=>10,
				    ),
				));	
			}
			

			

			$this -> renderPartial('_downloadsinfo', array('model'=>$model,'downloadsdp' => $downloadProvider ));
		} catch(CHttpException $e) {			
			$downloadProvider=new CArrayDataProvider(array());
			$this -> renderPartial('_downloadsinfo', array('model'=>$model,'downloadsdp' => $downloadProvider ));
		}

		
		//echo CJSON::encode($downloads);



	}

	public function actionDeleteboxdownloads($node)
	{
		$model = $this -> loadModel($node);
		$result = RequestController::delete_box_downloads($model);
	}

	public function actionView($id) {

		//debug($this->loadModel($id));
		$model = $this -> loadModel($id);


		
		// try
		// {
				
			$vms = RequestController::get_vm_status($model);
		// } catch(CHttpException $e) {
		// 	$vms=null;
		// }
		
		
		
		//First of all, updating the cache
		CacheController::updateCacheFromStatusArray($id,$vms);


		if (!Rights::getAuthorizer()->isSuperuser(Yii::app()->user->id)){		



			$entries=AssignedVM::model()->findAllByAttributes(array('node_name'=>$id,																	
																	'user_id'=>Yii::app()->user->id),array('group'=>'user_id'));
			
			$usermodel=User::model()->findByPk(Yii::app()->user->id);
			$inheritedData=$usermodel->findAssociatedVmByProjectDistinct();

			

			foreach ($vms as $keyvm => $valuevm) {	
				

				$delete=true;
				foreach ($entries as $keyen => $entryen) {					
					if ($entryen->machine_name==$valuevm["name"]){						
						$delete=false;
						//Found the machine, releasing this machine to avoid interating
						unset($entries[$keyen]);
					}
				}

				if ($delete){
					foreach($inheritedData as $keyen => $entryen){
						if (($entryen["machine_name"]==$valuevm["name"]) && ($entryen["node_name"]==$id)){
							$delete=false;
							unset($inheritedData[$keyen]);
						}		
					}
				}

				if ($delete){				
					unset($vms[$keyvm]);				
				}

			}


			

		}
		
		if (!is_null($vms))
		{
			//Get if Virtual Machine is busy		
			foreach ($vms as $keyvm => $valuevm) {
				$auxData=Yii::app()->db->createCommand("
										SELECT count(*) as num from operation_table o
										WHERE o.operation_status=".OperationController::OPERATION_IN_PROGRESS." 
										AND o.node_name=\"".$id."\" AND 
										o.operation_result LIKE '%\"vmname\":\"".$valuevm["name"]."\"%'
										")->queryRow();
				
				
				
				$valuevm["busy"] = (intval($auxData["num"])>0)?true:false;
				if ($valuevm["busy"])
					$valuevm["status"] = OperationController::BUSY_KEYWORD;
				
				$vms[$keyvm]=$valuevm;
			}
		}
						
					
		


		$boxes = RequestController::get_node_boxes($model);

		$downloads=null;
		if (Yii::app()->user->checkAccess('Node.Downloadsinfo'))
			$downloads = RequestController::get_node_boxes_downloads($model);

		$nodeinfo = RequestController::get_node_info($model);

		
		

		if (is_null($vms))
			$vms = array();

		if (is_null($boxes))
			$boxes = array();

		if (is_null($downloads))
		{
			$downloads = array();
		
		}
		

		//HACK DELETE ME
		//$boxes = array();
			
		$downloadProvider=new CArrayDataProvider($downloads, array(
		    'keyField' => 'box_name',
		    'sort'=>array(
		        'attributes'=>array(
		             'box_name', 'box_url', 'box_progress','box_remaining','download_status',
		        ),
		    ),
		    'pagination'=>array(
		        'pageSize'=>10,
		    ),
		));

		
		
		if (!is_null($nodeinfo)){
			$iaux = explode(",", $nodeinfo["interfaces"]);
			$interfaces = [];
			foreach ($iaux as $key => $value) {			
				if (isset($nodeinfo["ipaddress_".$value]))
				{				
					array_push($interfaces, array('name'=>$value,'ipaddress'=>$nodeinfo["ipaddress_".$value]));
				}		
			}

			$nodeinfo["interfaces"]=$interfaces;
			

			$nodeinfo["cpuaverage"][0] = $nodeinfo["cpuaverage"][0]."%  (1 Minute)";
			$nodeinfo["cpuaverage"][1] = $nodeinfo["cpuaverage"][1]."%  (5 Minutes)";
			$nodeinfo["cpuaverage"][2] = $nodeinfo["cpuaverage"][2]."%  (15 Minutes)";

			$nodeinfo["cpuaverage"] = implode("&nbsp;&nbsp;&nbsp;", $nodeinfo["cpuaverage"]);
		}

		


		$vmsdp = new CArrayDataProvider($vms, array('keyField' => 'name'));

		$boxesdp = new CArrayDataProvider($boxes, array('keyField' => 'name'));
		

		$this -> render('view', array('model' => $model, 
									'nodeinfo' => $nodeinfo, 
									'vmsdp' => $vmsdp, 
									'boxesdp' => $boxesdp,
									'showdownloadinfo'=>Yii::app()->user->checkAccess('Node.Downloadsinfo'),
									'downloadsdp' => $downloadProvider ));

		
	}

	public function actionPing()
	{
		$dataProvider = new CActiveDataProvider('NodeModel', array('sort' => array('defaultOrder' => 'NODE_NAME ASC', ), ));		

		$online=0;
		$offline=0;

		foreach ($dataProvider->getData() as $record) {
			if (RequestController::ping_node($record))
				$online++;
			else
				$offline++;
		}
		
		echo CJSON::encode(array('online'=>$online,'offline'=>$offline));
	}

	public function actionOperations($node=null)
	{

		$params= array();
		if ($node!=null)
		{
			$params=array('node' => $node);
		}

		$this -> render('operation',$params);	
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate() {
		$model = new NodeModel;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['NodeModel'])) {
			$model -> attributes = $_POST['NodeModel'];

			//Checking if another node with the same name exists
			$modelaux=null;
			try
			{
				$modelaux=$this->loadModel($_POST['NodeModel']['node_name']);
			}catch(CHttpException $e)
			{
				if ($model -> save())
				$this -> redirect(array('view', 'id' => $model -> node_name));
			}
			Yii::app() -> user -> setFlash('error', "There another node with the same name. Plase change it");			

			
		}

		$this -> render('create', array('model' => $model, ));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id) {
		$model = $this -> loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['NodeModel'])) {
			//Borro información almacenada
			CachedData::model() -> deleteAll();
			
			$lastname=$model->node_name;

			$model->node_name=$_POST['NodeModel']['node_name'];
			$model->node_address=$_POST['NodeModel']['node_address'];
			$model->node_port=$_POST['NodeModel']['node_port'];
			if (isset($_POST['NodeModel']['node_password']) && !empty($_POST['NodeModel']['node_password']))
			{
				$model->password=$model->hashPassword($_POST['NodeModel']['node_password']);
			}

			//$model -> attributes = $_POST['NodeModel'];			

			if ($model -> save()) {

				//Actualizamos la tabla de proyectos por si hubiera que updatear
				//el nombre del nodo
				$conn=Yii::app()->db;
				$command=$conn->createCommand("UPDATE project_node_machine_table SET 
											node_name='".$model->node_name."' WHERE node_name='".$lastname."'");
				$command->execute();

				$this -> redirect(array('view', 'id' => $model -> node_name));
			}
		}

		$this -> render('update', array('model' => $model, ));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id) {
		$this -> loadModel($id) -> delete();

		//Eliminando el nodeo de la tabla de proyectos
		$conn=Yii::app()->db;		
		$command=$conn->createCommand("DELETE FROM project_node_machine_table WHERE node_name='".$id."'");
		$command->execute();

		
		CacheController::deleteEntry($id);

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if (!isset($_GET['ajax']))
			$this -> redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}



	


	/**
	 * Lists all models.
	 */

	public function actionIndex($node='') {
				

		if (empty($node)){			
			$dataProvider = new CActiveDataProvider('NodeModel', 
													array('sort' => array('defaultOrder' => 'node_name ASC', ), ));
		}else{
			
			$nodecriteria = new CDbCriteria();
			$nodecriteria->condition="node_name=:nodename";
			$nodecriteria->params = array(
								':nodename' => $node,							
								);
			$nodecriteria->order = 'node_name ASC'; 
			// $dataProvider = new CActiveDataProvider('NodeModel', 
			// 										array('criteria'=>$nodecriteria,
			// 									  		  'sort' => array('defaultOrder' => 'node_name ASC', ), ));

			$dataProvider = new CActiveDataProvider('NodeModel', 
													array('criteria'=>$nodecriteria,));
		}
	 	
		
		
	 	$rawData = array();
		
		$nodenames = array();
		

		
		if (empty($node) && !isset($_GET['ArrayFilterForm']))
		{			
			//CachedData::model() -> deleteAll();

			foreach ($dataProvider->getData() as $record) {
				array_push($nodenames,$record -> node_name);
			}
		}

		
		
		
		//if (Yii::app() -> request -> isAjaxRequest) {
			
			if (isset($_GET['ArrayFilterForm']) || isset($_GET['ajaxUpdateRequest']))
			{		
				
				// self::fillCache($node);
				//CacheController::fillCache($node);

				$model = CachedData::model();
				$rawData = $model -> getCommandBuilder() -> createFindCommand($model -> tableSchema, $model -> dbCriteria) -> queryAll();
			}else{				
				

				if (!empty($node))
				{
					
					$cache_model = CachedData::model();					
					

					// self::fillCache($node);
					
					try
					{
						CacheController::fillCache($node);
					}catch(CHttpException $e){
						
						if ($e->statusCode==503)
						{
							throw new CHttpException($e->statusCode, $node.": ".$e->getMessage());
						}else{
							throw $e;
						}
					}



					if (Rights::getAuthorizer()->isSuperuser(Yii::app()->user->id)){
						
						$scriteria = new CDbCriteria();
						$scriteria -> order = "node_name ASC";
						

						//Consulta que obtiene los valores de la cache y además obtiene si la máquina 
						//está ocupada realizando una operación
						// select c.*,o.operation_result from cached_data_table c 
						// LEFT JOIN operation_table o ON o.node_name=c.node_name 
						// WHERE o.operation_status=100 AND 
						// 	  c.node_name=o.node_name AND 
						// 	  operation_result LIKE '%"vmname":"'||c.vm_name||'"%';


						$rawData = $cache_model -> getCommandBuilder() 
												-> createFindCommand($cache_model -> tableSchema, 
																	$scriteria) -> queryAll();

						
						

					}else{

						//Getting direct assigned virtual machines to the user
						//union inherited virtual machines linked to projects associated with the user						
						$rawData=Yii::app()->db->createCommand("select distinct c.* from cached_data_table c INNER JOIN 
																	(SELECT node_name,machine_name 
																	FROM project_node_machine_table pnm 
																	LEFT JOIN project_user_table p ON pnm.project_id=p.project_id 
																	WHERE p.user_id=".Yii::app()->user->id." ORDER BY node_name ASC,machine_name ASC) derived 
																	ON c.node_name=derived.node_name AND c.vm_name=derived.machine_name
																	UNION
																	select cc.* 
																	FROM cached_data_table cc 
																	LEFT JOIN user_virtual_machine_table u ON u.node_name=cc.node_name AND u.machine_name=cc.vm_name 
																	WHERE u.user_id=".Yii::app()->user->id." ORDER BY node_name ASC,vm_name ASC")																	
																	->queryAll();




						//Esta llamada obtiene las máquinas asignadas directamente
						// $rawData=Yii::app()->db->createCommand()->select('c.*')
						// 						  ->from('cached_data_table c')												  
						// 						  ->leftJoin('user_virtual_machine_table u', 'u.node_name=c.node_name AND u.machine_name=c.vm_name')
						// 						  ->where('u.user_id = :user', array(':user'=>Yii::app()->user->id))
						// 						  ->queryAll();
					}
					
					
				}
				
			}
			

		//}

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
		

		//Este cambio del keyfield a false es para que el id del array sea la row en la que se encuentra
		//en el grid. Lo he necesitado por la CCheckBoxColumn
		$arrayProvider = new CArrayDataProvider($filteredData, array('keyField' => false, 'pagination' => array('pageSize' => 20, ), ));

		//FIXME establecer el valor de refresco mediante algún parámetro configurable de la bbdd
		$this -> render('index', array('dataProvider' => $arrayProvider, 'filtersForm' => $filtersForm, 'nodes' => $nodenames,'refresh_time' => 60 ));
		

	}





	/*
	public function actionIndex($node='') {

		

		if (empty($node)){			
			$dataProvider = new CActiveDataProvider('NodeModel', array('sort' => array('defaultOrder' => 'NODE_NAME ASC', ), ));
		}else{
			
			$nodecriteria = new CDbCriteria();
			$nodecriteria->condition="node_name=:nodename";
			$nodecriteria->params = array(
								':nodename' => $node,							
								); 
			$dataProvider = new CActiveDataProvider('NodeModel', array('criteria'=>$nodecriteria,
																'sort' => array('defaultOrder' => 'NODE_NAME ASC', ), ));
		}
	 	
		
		
	 	$rawData = array();
		
		$nodenames = array();
		

		
		if (empty($node) && !isset($_GET['ArrayFilterForm']))
		{			
			CachedData::model() -> deleteAll();
			foreach ($dataProvider->getData() as $record) {
				array_push($nodenames,$record -> node_name);
			}
		}

		
		
		
		//if (Yii::app() -> request -> isAjaxRequest) {
			
			if (isset($_GET['ArrayFilterForm']) || isset($_GET['ajaxUpdateRequest']))
			{		
				
				$model = CachedData::model();
				$rawData = $model -> getCommandBuilder() -> createFindCommand($model -> tableSchema, $model -> dbCriteria) -> queryAll();
			}else{				
				

				if (!empty($node))
				{
					
					$cache_model = CachedData::model();					
					foreach ($dataProvider->getData() as $record) {
						
						if ($record->node_name == $node)
						{
							
							//CachedData::model()->deleteAllByAttributes(array('node_name'=>$record->node_name));
							$time = time() + NodeController::EXPIRATION_TIME;
							$vms = RequestController::get_vm_status($record);
						

							if (!is_null($vms)) {

								if (sizeof($vms)==0){									
									$criteria1 = new CDbCriteria();
									$criteria1->condition="node_name=:nodename";
									$criteria1->params = array(
														':nodename' => $record->node_name,														
														);

									$cdata=$cache_model->find($criteria1);
									if (!$cdata){
										$cdata = new CachedData;
										$cdata -> node_name = $record -> node_name;
										$cdata -> node_status = true;
										$cdata -> vm_name = "";
										$cdata -> status = "";
										$cdata -> provider = "";
										$cdata -> expiration = $time;
									}else{
										$cdata -> status = "";
										$cdata -> provider = "";
										$cdata -> expiration = $time;										
									}

									$cdata -> save();

								}else{
								
									$criterianode = new CDbCriteria();
									$criterianode->condition="node_name=:nodename";
									$criterianode->params = array(
														':nodename' => $record->node_name,													
														);

									//Primero eliminamos todos las entradas de ese nodo por si hubiera alguna
									$cache_model->deleteAll($criterianode);

									foreach ($vms as $vm) {									
										
										// if (!$cdata){
											$cdata = new CachedData;
											$cdata -> node_name = $record -> node_name;
											$cdata -> node_status = true;
											$cdata -> vm_name = $vm["name"];
											$cdata -> status = $vm["status"];
											$cdata -> provider = $vm["provider"];
											$cdata -> expiration = $time;
										// }else{
										// 	$cdata -> status = $vm["status"];
										// 	$cdata -> provider = $vm["provider"];
										// 	$cdata -> expiration = $time;
										// 	$cdata -> node_status = true;
										// }

										
										
										//Pushing data in cached data model										
										$cdata -> save();	
										
									}	
								}


							}else{	
								
								
								//Node is offline
								// $entry = array('node_name' => $record -> node_name,								
								// 'node_status' => false, 'vm_name' => '', 'status' => '', 'provider' => '', );
								// array_push($rawData, $entry);
								//Pushing data in cached data model
								//$model1 = CachedData::model();
								$criteria1 = new CDbCriteria();
								$criteria1->condition="node_name=:nodename";
								$criteria1->params = array(
													':nodename' => $record->node_name,													
													);
								
								
								//$cdata=$cache_model->find($criteria1);
								

								//Primero eliminamos todos las entradas de ese nodo por si hubiera alguna
								$cache_model->deleteAll($criteria1);

								//Después añadimos la entrada
								//if (!$cdata){
									$cdata = new CachedData;
									$cdata -> node_name = $record -> node_name;
									$cdata -> node_status = false;
									$cdata -> vm_name = '';
									$cdata -> status = '';
									$cdata -> provider = '';
									$cdata -> expiration = $time;
								// }else{
								// 	$cdata -> node_status = false;
								// 	$cdata -> vm_name = '';
								// 	$cdata -> status = '';
								// 	$cdata -> provider = '';
								// 	$cdata -> expiration = $time;
								// }
								
								
								
								$cdata -> save();					
							}
						}						
					}

					if (Rights::getAuthorizer()->isSuperuser(Yii::app()->user->id)){
						// if (array_key_exists('AdminRole', Rights::getAssignedRoles(Yii::app()->user->Id))) {
						$rawData = $cache_model -> getCommandBuilder() 
												-> createFindCommand($cache_model -> tableSchema, 
																	$cache_model -> dbCriteria) -> queryAll();
					}else{

						//Getting direct assigned virtual machines to the user
						//union inherited virtual machines linked to projects associated with the user						
						$rawData=Yii::app()->db->createCommand("select distinct c.* from cached_data_table c INNER JOIN 
																	(SELECT node_name,machine_name 
																	FROM project_node_machine_table pnm 
																	LEFT JOIN project_user_table p ON pnm.project_id=p.project_id 
																	WHERE p.user_id=".Yii::app()->user->id.") derived 
																	ON c.node_name=derived.node_name AND c.vm_name=derived.machine_name
																	UNION
																	select cc.* 
																	FROM cached_data_table cc 
																	LEFT JOIN user_virtual_machine_table u ON u.node_name=cc.node_name AND u.machine_name=cc.vm_name 
																	WHERE u.user_id=".Yii::app()->user->id)
																	->order('vm_name ASC')
																	->queryAll();




						//Esta llamada obtiene las máquinas asignadas directamente
						// $rawData=Yii::app()->db->createCommand()->select('c.*')
						// 						  ->from('cached_data_table c')												  
						// 						  ->leftJoin('user_virtual_machine_table u', 'u.node_name=c.node_name AND u.machine_name=c.vm_name')
						// 						  ->where('u.user_id = :user', array(':user'=>Yii::app()->user->id))
						// 						  ->queryAll();
					}
					
					
				}
				
			//}



			//If we are filtering and records has not expired yet, then used cached data
			// if ((isset($_GET['ArrayFilterForm']) || isset($_GET['ajaxUpdateRequest'])) && !$this -> has_expired()) {
// 
				// $model = CachedData::model();
				
				// $rawData = $model -> getCommandBuilder() -> createFindCommand($model -> tableSchema, $model -> dbCriteria) -> queryAll();
// 
			// } else {
				
				

				// //Deleting all records from cached data model
				// CachedData::model() -> deleteAll();
				// $time = time() + NodeController::EXPIRATION_TIME;
// 
				// foreach ($dataProvider->getData() as $record) {
// 
					// //$vms=RequestController::get_vm_status($record->node_address,$record->node_port);
					// $vms = RequestController::get_vm_status($record);
// 
					// if (!is_null($vms)) {
// 
						// foreach ($vms as $vm) {
							// $entry = array('node_name' => $record -> node_name,
							// //'node_status' => NodeController::ONLINE_STR,
							// 'node_status' => true, 'vm_name' => $vm["name"], 'status' => $vm["status"], 'provider' => $vm["provider"], );
// 
							// array_push($rawData, $entry);
							// //Pushing data in cached data model
							// $cdata = new CachedData;
							// $cdata -> node_name = $record -> node_name;
							// $cdata -> node_status = true;
							// $cdata -> vm_name = $vm["name"];
							// $cdata -> status = $vm["status"];
							// $cdata -> provider = $vm["provider"];
							// $cdata -> expiration = $time;
							// $cdata -> save();
// 							
						// }
					// } else {
						// //Node is offline
						// $entry = array('node_name' => $record -> node_name,
						// //'node_status' => NodeController::OFFLINE_STR,
						// 'node_status' => false, 'vm_name' => '', 'status' => '', 'provider' => '', );
						// array_push($rawData, $entry);
						// //Pushing data in cached data model
						// $cdata = new CachedData;
						// $cdata -> node_name = $record -> node_name;
						// $cdata -> node_status = false;
						// $cdata -> vm_name = '';
						// $cdata -> status = '';
						// $cdata -> provider = '';
						// $cdata -> expiration = $time;
						// $cdata -> save();
					// }
// 					
							// // $filtersForm = new ArrayFilterForm;
							// // if (isset($_GET['ArrayFilterForm']))
								// // $filtersForm -> filters = $_GET['ArrayFilterForm'];
// // 					
							// // $filteredData = $filtersForm -> filter($rawData);
							// // $arrayProvider = new CArrayDataProvider($filteredData, array('keyField' => false, 'pagination' => array('pageSize' => 20, ), ));
							// // $this -> renderPartial('index', array('dataProvider' => $arrayProvider, 'filtersForm' => $filtersForm, ));
							// // Yii::app()->end();				
// 								
				 // }
// 				
			// // }
// 			
// 
		}

		$filtersForm = new ArrayFilterForm;
		if (isset($_GET['ArrayFilterForm']))
			$filtersForm -> filters = $_GET['ArrayFilterForm'];

		$filteredData = $filtersForm -> filter($rawData);
		
		// $arrayProvider=new CArrayDataProvider($filteredData,array('keyField'=>'node_name',
		// 'pagination'=>array(
		// 'pageSize'=>20,
		// ),
		// ));

		//Este cambio del keyfield a false es para que el id del array sea la row en la que se encuentra
		//en el grid. Lo he necesitado por la CCheckBoxColumn
		$arrayProvider = new CArrayDataProvider($filteredData, array('keyField' => false, 'pagination' => array('pageSize' => 20, ), ));

		//FIXME establecer el valor de refresco mediante algún parámetro configurable de la bbdd
		$this -> render('index', array('dataProvider' => $arrayProvider, 'filtersForm' => $filtersForm, 'nodes' => $nodenames,'refresh_time' => 60 ));
		

	}*/

	

	//Function that checks if records stored has expired
	private function has_expired() {
		$model = CachedData::model();
		$row = $model -> getCommandBuilder() -> createFindCommand($model -> tableSchema, $model::model() -> dbCriteria) -> queryRow();

		return (($row['expiration'] > time()) ? false : true);
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin() {
		$model = new NodeModel('search');
		$model -> unsetAttributes();
		// clear any default values
		if (isset($_GET['NodeModel']))
			$model -> attributes = $_GET['NodeModel'];

		$this -> render('admin', array('model' => $model, ));
	}

	public function actionShowconfig($node) {
		//When rendering the new page the $.fn.yiiGridView gets undefined
		//i think is due to the fact of adding new jquery. To avoid that
		//the folling code prevents from adding differente jquery.js
		$cs = Yii::app() -> clientScript;
		$cs -> reset();
		//$cs -> scriptMap = array('jquery.js' => false, 'jquery-1.10.2.js' => false // prevent produce jquery.js in additional javascript data

		//);
		//////////////////////////////////////////////////////////

		$cfile = RequestController::get_config_file(NodeModel::model() -> findByPk($node));
		//$this->renderPartial('_viewconfig',array('node'=>$node,'cfile'=>cfile),false,true);
		$this -> render('_viewconfig', array('node' => $node, 'cfile' => $cfile), false, false);
		//$this->renderText($cfile);


	}

	public function actionEditconfig() {

		if (Yii::app() -> request -> isPostRequest) {
			if (isset($_POST['id'])) {
				//TODO METER FLASH MESSAGES
				if (!isset($_POST['cfile'])) {
					//TODO si el parámetro no está presente, entonces
					//se hace la petición para descargarla
					$cfile = RequestController::get_config_file(NodeModel::model() -> findByPk($_POST['id']));
				} else
					$cfile = $_POST['cfile'];

				$this -> render('editconfig', array('id' => $_POST['id'], 'cfile' => $cfile, ));
				return;
			}
		} else {
			if (isset($_GET['id'])) {
				$cfile = RequestController::get_config_file(NodeModel::model() -> findByPk($_GET['id']));
				//FIXME OBTENER LA CONFIGURACION
				$this -> render('editconfig', array('id' => $_GET['id'], 'cfile' => $cfile, ));
				return;
			}
		}

		Yii::app() -> user -> setFlash('error', "Node Parameter not found in request!");
		$this -> redirect(array('index'));

	}

	public function actionPasswordchange($node) {		
			
		if (!isset($_POST['password_field']) || !isset($_POST['cpassword_field']))
		{
			$this -> render('changepassword', array('node' => $node));
		}else{
			if ($_POST['password_field']!=$_POST['cpassword_field'])
			{
				Yii::app() -> user -> setFlash('error', "Passwords doest not match!");
			}else{				
				$node_model=NodeModel::model() -> findByPk($node);				
				RequestController::node_password_change($node_model,$_POST['password_field']);
				$node_model->node_password = $_POST['password_field'];
				$node_model->save();
				Yii::app() -> user -> setFlash('success', "Password successfully changed");
				
			}
			$this -> redirect(array('view', 'id' => $node));
		}
	}
	

	public function actionUploadconfig($node) {
		$nodemodel = NodeModel::model() -> findByPk($node);
		
		if (is_null($nodemodel))
		{
			Yii::app() -> user -> setFlash('error', "Node '".$node."' not found");
			$this -> redirect(array('index'));
		}
		
		if (Yii::app() -> request -> isPostRequest) {
			

			try {
				RequestController::do_update_config($nodemodel, $_POST['edit-node-config']);
				// $this->renderPartial('_viewconfig',array('node'=>$node,'cfile'=>$_POST['edit-node-config']),false,true);
				$this -> render('_viewconfig', array('node' => $node, 'cfile' => $_POST['edit-node-config']));

			} catch(CHttpException $e) {				
				Yii::app() -> user -> setFlash('error', $e->getMessage());
				$this -> redirect(array('view','id' => $node));
			}

		}else
		{
			//GET actions gets the current remote config file 			
			$cfile = RequestController::get_config_file($nodemodel);
			$this -> render('_viewconfig', array('node' => $node, 'cfile' => $cfile));
		}

	}


	

	public function actionDistribution($node)	
	{
		
		$res = array("aa","bb");
		return json_encode("pepe");
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return NodeModel the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id) {
		$model = NodeModel::model() -> findByPk($id);
		if ($model === null)
			throw new CHttpException(404, 'The requested node does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param NodeModel $model the model to be validated
	 */
	protected function performAjaxValidation($model) {
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'node-model-form') {
			echo CActiveForm::validate($model);
			Yii::app() -> end();
		}
	}


	//FIXME DELETE
	public function actionTest($node=null,$vm=null) {
		$cache_model = CachedData::model();	
		$scriteria = new CDbCriteria();
		$scriteria -> order = "node_name ASC";
		$rawData = $cache_model -> getCommandBuilder() 
												-> createFindCommand($cache_model -> tableSchema, 
																	$scriteria) -> queryAll();
		foreach ($rawData as $key => $value) {
			//o.operation_result LIKE \'%"vmname":"\'||'.$value["vm_name"].'||\'"%\'
			// $auxData=Yii::app()->db->createCommand("
			// 										SELECT * from operation_table o
			// 										WHERE o.node_name=\"".$value["node_name"]."\" AND 
			// 										o.operation_result LIKE '%\"vmname\":\"web\"%'
			// 										");


			$auxData=Yii::app()->db->createCommand("
													SELECT count(*) as num from operation_table o
													WHERE o.operation_status=".OperationController::OPERATION_IN_PROGRESS." AND o.operation_status=100 AND o.node_name=\"".$value["node_name"]."\" AND 
													o.operation_result LIKE '%\"vmname\":\"".$value["vm_name"]."\"%'
													")->queryRow();



			//echo "SELECT * from operation_table o WHERE o.node_name=\"".$value["node_name"]."\" AND o.operation_result LIKE '%\"vmname\":\"web\"%'";
			//echo($value["vm_name"]);
			var_dump($auxData);
			exit;	
		}

	}


	public function actionExport(){
		Yii::import('ext.ECSVExport');

		$provider=new CActiveDataProvider('NodeModel');
		$csv = new ECSVExport($provider);
		
		$csv->setHeader('node_name','Node Name');
		$csv->setHeader('node_password','Node Password');
		$csv->setHeader('node_address','Node Address');
		$csv->setHeader('node_port','Node Port');
		
		$content = $csv->toCSV();  
		$filename=NodeController::CSVFILENAME;                 
		
		
		Yii::app()->getRequest()->sendFile($filename, $content, "text/csv", false);


	}

}
