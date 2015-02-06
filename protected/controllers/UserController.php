<?php

include 'RequestController.php';

class UserController extends Controller
{
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
	// 			'actions'=>array('index','view'),
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
	// 	return array(array('index','view','create','update','admin','delete'));
	// }	

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{

		// $inheritedmachines=Yii::app()->db->createCommand()
		// 										  ->selectDistinct('pnm.node_name,pnm.machine_name')
		// 										  ->from('project_node_machine_table pnm')												  
		// 										  ->leftJoin('project_user_table pu', 'pu.project_id=pnm.project_id')
		// 										  ->where('pu.user_id = :user', array(':user'=>Yii::app()->user->id))
		// 										  ->queryAll();		

		// $inheritedmachines= new CArrayDataProvider($inheritedmachines,array('keyField'=>'machine_name'));
		$model=$this->loadModel($id);

		$inheritedmachines= new CArrayDataProvider($model->findAssociatedVmByProject(),array('keyField'=>'machine_name'));

		$this->render('view',array(
			'model'=>$model,
			'inherited'=>$inheritedmachines,
		));
	}

	public function actionDeleteRole($userid,$rolename){
		if (!Yii::app()->user->checkAccess('User.DeleteRole'))
			throw new CHttpException(403, "You are not authorized to perform this action.");
		
		if (!empty($rolename) && !empty($userid))
		{
			$authorizer = Yii::app()->getModule("rights")->getAuthorizer();
			$authorizer->authManager->revoke($rolename, $userid);
		}
	}

	public function actionAddRole($userid,$rolename){
		if (!Yii::app()->user->checkAccess('User.AddRole'))
		 	throw new CHttpException(403, "You are not authorized to perform this action.");

		if (!empty($rolename) && !empty($userid))
		{
			$authorizer = Yii::app()->getModule("rights")->getAuthorizer();
			$authorizer->authManager->assign($rolename, $userid);
		}
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new User;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			if($model->save()){
				//Añado el rol de usuario autenticado
				$authorizer = Yii::app()->getModule("rights")->getAuthorizer();
				$authorizer->authManager->assign('AuthenticatedRole', $model->id);
				//$this->redirect(array('admin'));

				
				$this->redirect(array('update','id'=>$model->id,'node'=>null));
				
			}
		}

		$inheritedmachines= new CArrayDataProvider(array());

		$this->render('create',array(
			'model'=>$model,
			'inherited'=>$inheritedmachines,
			'showroleassignments'=>false,
			'showvmassignments'=>false,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id,$node=null)
	{
		$model=$this->loadModel($id);

		
		//Consulta para obtener todas las máquinas asignadas a usuario por proyecto, filtra para que 
		//aparezca la visibilidad de forma global y no por proyecto
		// select distinct pnm.node_name,pnm.machine_name 
		// from project_node_machine_table pnm 
		// LEFT JOIN project_user_table pu ON pu.project_id=pnm.project_id WHERE pu.user_id=Yii::app()->user->id;

		// $inheritedmachines=Yii::app()->db->createCommand()
		// 										  ->selectDistinct('pnm.node_name,pnm.machine_name')
		// 										  ->from('project_node_machine_table pnm')												  
		// 										  ->leftJoin('project_user_table pu', 'pu.project_id=pnm.project_id')
		// 										  ->where('pu.user_id = :user', array(':user'=>Yii::app()->user->id))
		// 										  ->queryAll();		

		// $inheritedmachines= new CArrayDataProvider($inheritedmachines,array('keyField'=>'machine_name'));


		$inheritedmachines= new CArrayDataProvider($model->findAssociatedVmByProject(),array('keyField'=>'machine_name'));
	
		
		// select pnm.project_id,pnm.node_name,pnm.machine_name 
		// from project_node_machine_table pnm 
		// LEFT JOIN project_user_table pu ON pu.project_id=pnm.project_id 
		// WHERE pu.user_id=1 order by pnm.project_id,pnm.machine_name;

		// $inheritedmachines2=Yii::app()->db->createCommand()
		// 										  ->select('p.project_name,pnm.project_id,pnm.node_name,pnm.machine_name')
		// 										  ->from('project_node_machine_table pnm')												  
		// 										  ->leftJoin('project_user_table pu', 'pu.project_id=pnm.project_id')
		// 										  ->leftJoin('project_table p', 'p.id=pnm.project_id')
		// 										  ->where('pu.user_id = :user', array(':user'=>Yii::app()->user->id))
		// 										  ->order('pnm.project_id ASC,pnm.machine_name DESC')
		// 										  ->queryAll();		

	 //    $inheritedmachines2= new CArrayDataProvider($inheritedmachines2,array('keyField'=>'machine_name'));

	    //$inheritedmachines2= new CArrayDataProvider($model->findAssociatedVmByProjectDistinct(),array('keyField'=>'machine_name'));
	    $inheritedmachines2= new CArrayDataProvider($model->findAssociatedVmByProject(),array('keyField'=>'machine_name'));
	    

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->username=$_POST['User']['username'];
			$model->email=$_POST['User']['email'];
			if (isset($_POST['User']['password']) && !empty($_POST['User']['password']))
			{
				$model->password=$model->hashPassword($_POST['User']['password']);
			}

			
			// $model->attributes=$_POST['User'];
			if($model->save())
				$this->redirect(array('admin'));
		}

		$nodes=NodeModel::model()->findAll();
		
		$vms=array();

		//Removing offline nodes from array
		foreach ($nodes as $key => $value) {		 			 	
		 	
			if (!RequestController::ping_node($value)){
				unset($nodes[$key]);
			}
	
			if (!is_null($node) && $node!='' && $value->node_name==$node){
				$vmsaux = RequestController::get_vm_status($value,null,false);
				if (!is_null($vmsaux)){

					//Una vez encontradas las virtual machines del nodo sacamos del 
					//array aquellas que el usuario ya tenga asignadas. Disponible en $mode->machines					
					$vms=$vmsaux;
					foreach ($model->machines as $keym => $valuem) {
						foreach ($vms as $keyar => $valuear) {
							if ($valuem->machine_name==$valuear["name"]){
								unset($vms[$keyar]);
							}
						}
					}					
				}
					
			}

		}




		$vms= new CArrayDataProvider($vms,array('keyField'=>'name'));
		
		$node_list=CHtml::listData($nodes, 'node_name', 'node_name');

		$filtersForm = new ArrayFilterForm;		
			

		$this->render('update',array(
			'model'=>$model,
			'node_list'=>$node_list,
			'filtersForm'=>$filtersForm,
			'nodes'=>$nodes,
			'selectednode'=>$node,
			'vms'=>$vms,
			'inherited'=>$inheritedmachines,
			'inherited2'=>$inheritedmachines2,
			'showroleassignments'=>(Yii::app()->user->checkAccess('User.AddRole') || Yii::app()->user->checkAccess('User.DeleteRole')),
			'showvmassignments'=>(Yii::app()->user->checkAccess('User.Addvm') || Yii::app()->user->checkAccess('User.Removevm')),
		));
	}


	public function actionAddvm($user,$node,$vm){

		$model=$this->loadModel($user);
		if (!is_null($node)&& $node!='' && !is_null($vm) && $vm!='' && !is_null($model)){
			$avm=new AssignedVM;
			$avm->node_name=$node;
			$avm->machine_name=$vm;			
			$avm->user_id=$user;			
			$avm->save();			
		}

	}

	public function actionRemovevm($user,$node,$vm){

		$model=$this->loadModel($user);

		if ($model)
		{
			$avm=AssignedVM::model()->findByAttributes(array('user_id'=>$user,'node_name'=>$node,'machine_name'=>$vm));
			
			if (!is_null($avm))
				$avm->delete();
			else{
				throw new CHttpException(404,'The requested user or virtual machine doesn\'t exist');
			}
		}
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{

		$this->loadModel($id)->delete();

		//Delete also the role assigned		
		Rights::revokeAllRoles($id);

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('User');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new User('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['User']))
			$model->attributes=$_GET['User'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return User the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=User::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}





	/**
	 * Performs the AJAX validation.
	 * @param User $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	
}
