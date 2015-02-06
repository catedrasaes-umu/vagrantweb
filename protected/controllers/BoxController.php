<?php
include 'RequestController.php';


class BoxController extends Controller
{
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
	// 			'actions'=>array('delete','add'),
	// 			'users'=>array('admin'),
	// 		),		
	// 		array('deny',  // deny all users
	// 			'users'=>array('*'),
	// 		),
	// 	);
	// }

	// public function accessRules()
	// {
	// 	return array(array('delete','add'));
	// }
	
	
	
	public function actionDelete($id,$provider,$node)
	{		
		//$result=RequestController::delete_box($node,$id,$provider);
		// try
		// {
			$result=RequestController::delete_box(NodeModel::model()->findByPk($node),$id,$provider);
		// 	Yii::app()->user->setFlash('success', "Box Removed");
		// }catch(CHttpException $e) {	
		// 	Yii::app()->user->setFlash('error', $e->getMessage());
		// }
	}
	
	public function actionAdd($node)
	{
		
		 if (Yii::app() -> request -> isPostRequest) {
			
			
			//var_dump($_POST);exit;
			
			if ($_POST["upload-option"]=="3")
			{
				// FIXME FALTA ESTSTA OPCION INCLUSIVE EN LOS PLUGINS DE VAGRANT
				//var_dump("TERCERA OPCION");exit;
				
			}else{			
				$url = "";
				switch ($_POST["upload-option"]) {
					case '1':
						$url = $_POST["remote-url-tf"];
						break;
					case '2':
						$url = $_POST["othertf"];
						break;
					
					default:
						
						break;
				}				
				
				$result=RequestController::add_box(NodeModel::model()->findByPk($node),$_POST["box-name"],$url);
				
			}
			
			
			
			if ($result[0]==RequestController::LOCATION_CODE){			
				Yii::app()->user->setFlash('success', "Operation Queued");
				$this -> redirect(Yii::app()->createUrl("node/view", array("id"=> $node)));
			}else
				throw new CHttpException(500,'Error performing the command');
		 }else
				throw new CHttpException(500,'Error performing the command');
		
	}
	
}