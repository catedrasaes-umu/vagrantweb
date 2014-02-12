<?php
include 'RequestController.php';


class VmController extends Controller
{
	//private $cached_snapshot_list=array();
	
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
				'actions'=>array('command','destroy','create','delete',
								'batchrun','batchpause','batchstop','batchsnapshot','genconfig',
								'snapshotlist','restoresnapshot','takesnapshot','deletesnapshot'),
				'users'=>array('admin'),
			),		
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	public function actionCommand($id,$node,$command)
	{	
		//debug("EN ACTIONCOMMAND");
		
		$result=RequestController::do_vm_command(NodeModel::model()->findByPk($node),$id,$command);		
		
		// debug(RequestController::LOCATION_CODE);
		// debug($result);
		// debug("DESPUES");
		
		///////////////////////////
		// if (!empty($result))
		// {
			// $vm=$result[0];
// 			
			// $vmrow=CachedData::model()->findByAttributes(array('vm_name'=>$id,
    													// 'node_name'=>$node));		
// 			
			// $vmrow=CachedData::model()->findByAttributes(array('vm_name'=>$id,
    													// 'node_name'=>$node));
// 		
// 			
// 																			 
			// if (!is_null($vmrow))
			// {	
				// $vmrow->status=$vm["status"];
				// $vmrow->update(array('status'));
				// echo CJSON::encode(array(                         
	                        // 'status'=>$vm["status"]
	                        // ));
			// }
// 		
		// }else
			// throw new CHttpException(500,'Error performing the command');
			
		if ($result[0]==RequestController::LOCATION_CODE){						
			echo CJSON::encode(array(                         
	                        'status'=>'Operation Queued'));
		}else
			throw new CHttpException(500,'Error performing the command');
		
		
	}
	
	
	
	//public function actionCreate($node)
	public function actionCreate($node)
	{		
		if(!Yii::app()->request->isPostRequest || !isset($_POST['gen_config']))		
			$this->redirect(array('node/view','id' => $node));
		
		$result=RequestController::add_vm(NodeModel::model()->findByPk($node),$_POST['gen_config']);
		Yii::app()->user->setFlash('success', "Virtual Machine(s) successfully added");
		
	}
	
	public function actionDestroy($id,$node)
	{
		$result=RequestController::destroy_vm(NodeModel::model()->findByPk($node),$id);
		Yii::app()->user->setFlash('success', "Virtual Machine status destroyed");
	}

	public function actionDelete($id,$node)
	{
		$result=RequestController::delete_vm(NodeModel::model()->findByPk($node),$id);
		Yii::app()->user->setFlash('success', "Virtual Machine Removed");
	}

	private function checkBatchData()
	{
			
		if (!isset($_POST['batch_objects']) || empty($_POST['batch_objects']))
			return array();		
		else
			return $_POST['batch_objects'];
	}		

	
	public function actionBatchrun()
	{	
				
		$vms=$this->checkBatchData();
		if (empty($vms))
			return;
		
		echo $vms;
		
		
		
		
	}
	
	public function actionBatchpause()
	{
		$vms=$this->checkBatchData();
		if (empty($vms))
			return;
		
		
		
		
		
		
	}
	
	public function actionBatchstop()
	{
		$vms=$this->checkBatchData();
		if (empty($vms))
			return;
		
		
		
	}
	
	public function actionBatchsnapshot()
	{
		$vms=$this->checkBatchData();
		if (empty($vms))
			return;
		
		
		
	}
	
	
	private function transformSnapArrayToTree($snaplist)
	{
			
			
			if (is_null($snaplist))
			{				
				return null;
			}
			
			$treenodes=array();
			foreach($snaplist as &$child)
			{
					
				//Storing cache information
				// $this->cached_snapshot_list[$child["uuid"]]=array($child["name"],
																  // $child["description"],
																  // $child["timestamp"],
																  // $child["current_state"]
																  // );
// 				
				
				////////////////////////////////			
				$leaf=array();
				
				// $nodeText = CHtml::openTag('a', $options);
				// $nodeText.= $item['text'];
				// $nodeText.= CHtml::closeTag('a')."\n";
								
				$leaf["text"]="<a id=".$child["uuid"]." onclick='selectSnapshot($(this))'>".$child["name"]."</a>";				
				$leaf["text"].=CHtml::hiddenField($child["uuid"]."[name]",$child["name"]);
				$leaf["text"].=CHtml::hiddenField($child["uuid"]."[timestamp]",$child["timestamp"]);
				$leaf["text"].=CHtml::hiddenField($child["uuid"]."[description]",$child["description"]);
				$leaf["text"].=CHtml::hiddenField($child["uuid"]."[current_state]",$child["current_state"]);
				$leaf["hasChildren"]=false;
				
				
				if ($child["current_state"])
				{
								
					$leaf["htmlOptions"]=array('id'=>"current_state");
				}
				
				$aux=$this->transformSnapArrayToTree($child["snapshots"]);
				
				if (!is_null($aux))
				{
					$leaf["hasChildren"]=true;
					$leaf["children"]=$aux;
				}
				array_push($treenodes,$leaf);
			}
		
			return $treenodes;	
							
			
			
	}


	

	public function actionTakeSnapshot($id,$node)
	{
		if (Yii::app()->request->isAjaxRequest && !Yii::app()->request->isPostRequest)
		{
			
			// if(!Yii::app()->request->isPostRequest)
			// {
				//When rendering the new page the $.fn.yiiGridView gets undefined
				//i think is due to the fact of adding new jquery. To avoid that
				//the folling code prevents from adding differente jquery.js				
				$cs = Yii::app()->clientScript;
				$cs->reset();
				$cs->scriptMap = array(
				    'jquery.js'  =>  false,   // prevent produce jquery.js in additional javascript data
				);
				//////////////////////////////////////////////////////////
				
				$this->renderPartial('_takesnapshot',array('vm'=>$id,'node'=>$node),false,true);
			//}
		}
		//else{
			
			if(Yii::app()->request->isPostRequest)
			{	
				if (isset($_POST['snapshot_name']))
				{	
					$result=RequestController::do_take_snapshot(NodeModel::model()->findByPk($node),$id,$_POST['snapshot_name'],$_POST['snapshot_desc']);
					// if (isset($result['id']))
					// {
						// //Yii::app()->user->setFlash('success', "Snapshot created successfully!");
						// return "TODO BIEN";			
					// }else{
						// throw new CHttpException(400,'Snapshot could not be created!');
						// //Yii::app()->user->setFlash('error', "Snapshot could not be created!");
						// return "TODO MAl";
					// }
						
					if (!isset($result['id']))
					{						
						throw new CHttpException(400,'Snapshot could not be created!');
						//Yii::app()->user->setFlash('error', "Snapshot could not be created!");					
					}
									
				 }else				
					throw new CHttpException(400,'Snapshot Name can\'t be empty');	
			}
		//}	
		 
	}
	
	public function actionSnapshotList($id,$node)
	{
		if (Yii::app()->request->isAjaxRequest)
		{
			//When rendering the new page the $.fn.yiiGridView gets undefined
			//i think is due to the fact of adding new jquery. To avoid that
			//the folling code prevents from adding differente jquery.js				
			$cs = Yii::app()->clientScript;
			$cs->reset();
			$cs->scriptMap = array(
			    'jquery.js'  =>  false,   // prevent produce jquery.js in additional javascript data
			);
			//////////////////////////////////////////////////////////
			
			$snaplist=RequestController::get_vm_snapshot_list(NodeModel::model()->findByPk($node),$id);
 						
			$treedata=array();
			
			if (is_null($snaplist))
				throw new CHttpException(500,'Error retrieving the snapshot list');
			
			foreach ($snaplist as &$snap) {
				$aux=$this->transformSnapArrayToTree($snap);
				if (!is_null($aux) && !empty($aux))
					array_push($treedata,$aux);
					 			   
			}
			
	 		
			if (!empty($treedata))
			{	
				$this->renderPartial('_snapshotlist',array('data'=>current($treedata),'vm'=>$id,'node'=>$node),false,true);
			}else
				throw new CHttpException(404,'No snapshot available on that Virtual Machine');
				
			
		}	

	}

	public function actionDeletesnapshot($id,$node,$uuid)
	{
		
		$result=RequestController::do_delete_snapshot(NodeModel::model()->findByPk($node),$id,$uuid);
		
		Yii::app()->user->setFlash('success', "Snapshot deleted successfully");
		
		if (!Yii::app()->request->isAjaxRequest)
			$this->redirect(array('node/index'));
		else
			echo Yii::app()->user->getFlash('success');	
		
		
		
	}

	public function actionRestoresnapshot($id,$node,$uuid)
	{						
		$result=RequestController::do_restore_snapshot(NodeModel::model()->findByPk($node),$id,$uuid);
		
		if (!empty($result))		
			Yii::app()->user->setFlash('success', "Operation queued!");						
		else
			throw new CHttpException(500,'Error performing the command');
		
		
		if (!Yii::app()->request->isAjaxRequest)
			$this->redirect(array('node/index'));
		else
			echo Yii::app()->user->getFlash('success');
			

	}
	
	public function actionGenconfig()
	{		
		
		if(Yii::app()->request->isPostRequest)
		{	
				
			$vm_name=(isset($_POST['vm_name'])&& !empty($_POST['vm_name']))?$_POST['vm_name']:'TO_BE_FILLED';
			$box_name=(isset($_POST['box_name'])&& !empty($_POST['box_name']))?$_POST['box_name']:'TO_BE_FILLED';
			$host_name=(isset($_POST['host_name'])&& !empty($_POST['host_name']))?$_POST['host_name']:'TO_BE_FILLED';
			$network_type=(isset($_POST['network_type'])&& !empty($_POST['network_type']))?$_POST['network_type']:'TO_BE_FILLED';
			
			
    
    
    		$config_block=$vm_name."_config";
    
			$cfg="Vagrant.configure(\"2\") do |config|\n\n".
				 "\tconfig.vm.define(:".$vm_name.") do |".$config_block."|\n".
				 "\t\t".$config_block.".vm.box = \"".$box_name."\"\n".
				 "\t\t".$config_block.".vm.hostname = \"".$host_name."\"\n".
				 "\t\t".$config_block.".vm.network(:".$network_type.")\n".
				 "\tend\n\n".
				 "end\n";
			
			if (Yii::app()->request->isAjaxRequest)
				echo CJSON::encode(array(                         
	                        'cfg'=>$cfg
	                        ));
		}
	}
	
}
