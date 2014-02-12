<?php
include 'RequestController.php';
//include '../models/CachedData.php';

class NodeController extends Controller {

	const EXPIRATION_TIME = 150;
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
	public function filters() {
		return array('accessControl', // perform access control for CRUD operations
		'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules() {
		return array( array('allow', // allow all users to perform 'index' and 'view' actions
		'actions' => array('index', 'view', 'editconfig', 
							'showconfig', 'uploadconfig','passwordchange'), 'users' => array('@'), ), array('allow', // allow authenticated user to perform 'create' and 'update' actions
		'actions' => array('create', 'update'), 'users' => array('@'), ), array('allow', // allow admin user to perform 'admin' and 'delete' actions
		'actions' => array('admin', 'delete'), 'users' => array('admin'), ), array('deny', // deny all users
		'users' => array('*'), ), );
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id) {
		//debug($this->loadModel($id));
		$model = $this -> loadModel($id);

		//$vms= RequestController::get_vm_status($model->node_address,$model->node_port);
		$vms = RequestController::get_vm_status($model);
		$boxes = RequestController::get_node_boxes($model);

		if (is_null($vms))
			$vms = array();

		if (is_null($boxes))
			$boxes = array();

		$vmsdp = new CArrayDataProvider($vms, array('keyField' => 'name'));

		$boxesdp = new CArrayDataProvider($boxes, array('keyField' => 'name'));

		$this -> render('view', array('model' => $model, 'vmsdp' => $vmsdp, 'boxesdp' => $boxesdp, ));
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
			if ($model -> save())
				$this -> redirect(array('view', 'id' => $model -> node_name));
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
		
			
			$model -> attributes = $_POST['NodeModel'];

			if ($model -> save()) {
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

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if (!isset($_GET['ajax']))
			$this -> redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex($node='') {

		
		if (empty($node))
			$dataProvider = new CActiveDataProvider('NodeModel', array('sort' => array('defaultOrder' => 'NODE_NAME ASC', ), ));
		else{
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
						
						//if ($record->node_name == $node)
						//{
							//CachedData::model()->deleteAllByAttributes(array('node_name'=>$record->node_name));
							$time = time() + NodeController::EXPIRATION_TIME;
							$vms = RequestController::get_vm_status($record);
							if (!is_null($vms)) {
								foreach ($vms as $vm) {									
									
									$criteria1 = new CDbCriteria();
									$criteria1->condition="node_name=:nodename AND vm_name=:vmname";
									$criteria1->params = array(
														':nodename' => $record->node_name,
														':vmname' => $vm["name"],
														);
									
									//$rawData1= $model1 -> getCommandBuilder() -> createFindCommand($model1 -> tableSchema, $criteria) -> query();
									$cdata=$cache_model->find($criteria1);
									if (!$cdata){
										$cdata = new CachedData;
										$cdata -> node_name = $record -> node_name;
										$cdata -> node_status = true;
										$cdata -> vm_name = $vm["name"];
										$cdata -> status = $vm["status"];
										$cdata -> provider = $vm["provider"];
										$cdata -> expiration = $time;
									}else{
										$cdata -> status = $vm["status"];
										$cdata -> provider = $vm["provider"];
										$cdata -> expiration = $time;
									}
									//var_dump($rawData1);
									//debug($rawData1);
									// $ = array('node_name' => $record -> node_name,							
									// 'node_status' => true, 'vm_name' => $vm["name"], 'status' => $vm["status"], 'provider' => $vm["provider"], );
									
									//Pushing data in cached data model
									
									$cdata -> save();	
									//exit;						
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
								
								//$rawData1= $model1 -> getCommandBuilder() -> createFindCommand($model1 -> tableSchema, $criteria) -> query();
								$cdata=$cache_model->find($criteria1);
								if (!$cdata){
									$cdata = new CachedData;
									$cdata -> node_name = $record -> node_name;
									$cdata -> node_status = false;
									$cdata -> vm_name = '';
									$cdata -> status = '';
									$cdata -> provider = '';
									$cdata -> expiration = $time;
								}else{
									$cdata -> node_status = false;
									$cdata -> vm_name = '';
									$cdata -> status = '';
									$cdata -> provider = '';
									$cdata -> expiration = $time;
								}
								
								
								
								$cdata -> save();					
							}
						//}						
					}
					
					$rawData = $cache_model -> getCommandBuilder() -> createFindCommand($cache_model -> tableSchema, $cache_model -> dbCriteria) -> queryAll();
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
		$this -> render('index', array('dataProvider' => $arrayProvider, 'filtersForm' => $filtersForm, 'nodes' => $nodenames,'refresh_time' => 300 ));
		

	}

	

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
		$cs -> scriptMap = array('jquery.js' => false, // prevent produce jquery.js in additional javascript data
		);
		//////////////////////////////////////////////////////////

		$cfile = RequestController::get_config_file(NodeModel::model() -> findByPk($node));
		//$this->renderPartial('_viewconfig',array('node'=>$node,'cfile'=>cfile),false,true);
		$this -> renderPartial('_viewconfig', array('node' => $node, 'cfile' => $cfile), false, true);
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
				$this -> render('viewconfig', array('id' => $node, 'cfile' => $_POST['edit-node-config']));

			} catch(CHttpException $e) {				
				Yii::app() -> user -> setFlash('error', $e->getMessage());
				$this -> redirect(array('view','id' => $node));
			}

		}else
		{
			//GET actions gets the current remote config file 			
			$cfile = RequestController::get_config_file($nodemodel);
			$this -> render('viewconfig', array('id' => $node, 'cfile' => $cfile));
		}

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
			throw new CHttpException(404, 'The requested page does not exist.');
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

}
