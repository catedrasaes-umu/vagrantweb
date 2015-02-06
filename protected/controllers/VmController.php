<?php
include_once 'CacheController.php';


class VmController extends Controller
{
	//private $cached_snapshot_list=array();
	
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
	// 			'actions'=>array('command','destroy','create','delete',
	// 							'batchrun','batchpause','batchstop','batchsnapshot','genconfig',
	// 							'snapshotlist','restoresnapshot','takesnapshot','deletesnapshot'),
	// 			'users'=>array('admin'),
	// 		),		
	// 		array('deny',  // deny all users
	// 			'users'=>array('*'),
	// 		),
	// 	);
	// }

	// public function accessRules()
	// {
	// 	return array(array('command','destroy','create','delete','batchrun',
	// 						'batchpause','batchstop','batchsnapshot','genconfig',
	// 						'snapshotlist','restoresnapshot','takesnapshot','deletesnapshot'));
	// }
	
	public function actionCommand($id,$node,$command,$async=false)
	{	
		
		
		$result=RequestController::do_vm_command(NodeModel::model()->findByPk($node),$id,$command);		
		
		// if (!$async)
		// {
		// 	//Sync Calls
		// 	// debug(RequestController::LOCATION_CODE);
		// 	// debug($result);
		// 	// debug("DESPUES");
			
		// 	///////////////////////////
		// 	if (!empty($result))
		// 	{
		// 		$vm=$result[0];
				
		// 		$vmrow=CachedData::model()->findByAttributes(array('vm_name'=>$id,
	 //    													'node_name'=>$node));					
				
			
				
																				 
		// 		if (!is_null($vmrow))
		// 		{	
		// 			$vmrow->status=$vm["status"];
		// 			$vmrow->update(array('status'));
		// 			echo CJSON::encode(array(                         
		//                         'status'=>$vm["status"]
		//                         ));
		// 		}
			
		// 	}else
		// 		throw new CHttpException(500,'Error performing the command');
		// }else{
			//Assync Calls
			if ($result[0]==RequestController::LOCATION_CODE){					
				//Updating the cache
				//CacheController::updateVirtualMachineStatus($node,$id,null,OperationController::BUSY_KEYWORD);
				//echo CJSON::encode(array('status'=>'Operation Queued'));

				echo CJSON::encode(array('status'=>OperationController::BUSY_KEYWORD,'statusmsg'=>'Operation Queued'));
			}else
				throw new CHttpException(500,'Error performing the command');
		
		//}
	}
	
	public function actionView($id,$node){

		$entries=AssignedVM::model()->findAllByAttributes(array('node_name'=>$node,'machine_name'=>$id),array('group'=>'user_id'));
		
		$users = array();

		$ids = array();

		//Get assigned users
		foreach ($entries as $key => $value) {			
			$ids[]=$value->user->id;
			array_push($users, array("username" =>$value->user->username,"id"=>$value->user->id));
		}

		//Get assigned projects
		$projects=Yii::app()->db->createCommand("
										select p.id,p.project_name from project_node_machine_table pnm 
										left join project_table p ON pnm.project_id=p.id 
										WHERE pnm.node_name='".$node."' AND pnm.machine_name='".$id."'")->queryAll();

		$projects = new CArrayDataProvider($projects,array('keyField'=>'id'));

		$projectusers=Yii::app()->db->createCommand("
										select p.id,p.project_name,u.id as uid,u.username from project_node_machine_table pnm 
										left join project_table p ON pnm.project_id=p.id 
										LEFT JOIN project_user_table pu ON pu.project_id=p.id 
										left join Users u ON pu.user_id=u.id 
										WHERE pnm.node_name='".$node."' AND pnm.machine_name='".$id."' AND u.id IS NOT NULL")->queryAll();

		

		$fresult=array();
		foreach ($projectusers as $aux) {			
			if (array_key_exists($aux["username"], $fresult))
			{
				$fresult[$aux["username"]]["project_name"]=$fresult[$aux["username"]]["project_name"]."<tr><td>".$aux["project_name"]."</td></tr>";
			}else{
				$fresult[$aux["username"]]=$aux;
				$fresult[$aux["username"]]["project_name"]="<tr><td style='vertical-align:middle'>".$aux["project_name"]."</td></tr>";
			}
		}

		$proaux=array();
		foreach ($fresult as $aux) {
			$aux["project_name"]="<table style='margin-left:auto;margin-right:auto'>".$aux["project_name"]."</table>";			
			array_push($proaux, $aux);
		}

		


		$projectusers = new CArrayDataProvider($proaux,array('keyField'=>'uid'));

		
		

		//Check if user has permissions to manage user assignments
		if ((Yii::app()->user->checkAccess('User.Addvm') || Yii::app()->user->checkAccess('User.Removevm')) || 
			Rights::getAuthorizer()->isSuperuser(Yii::app()->user->id)){
			//Getting available users excluding assigned ones
			$criteria= new CDbCriteria;
			$criteria->addNotInCondition('id', $ids);
			$availableusers = new CActiveDataProvider('User',array('criteria'=>$criteria));
			$users = new CArrayDataProvider($users,array('keyField'=>'username'));
		}else{
			$availableusers = null;
			$users = null;
		}
		
		$nodemodel = NodeModel::model() -> findByPk($node);
		$vminfo = RequestController::get_vm_info($nodemodel,$id);

		if (!is_null($vminfo) && empty($vminfo))
			$vminfo=null;

		$filtersForm = new ArrayFilterForm;	

		$this->render('view',array(
			'vm'=>$id,
			'vminfo'=>$vminfo,
			'node'=>$node,
			'users' => $users,
			'filtersForm'=>$filtersForm,
			'availableusers'=>$availableusers,
			'projects'=>$projects,
			'projectusers'=>$projectusers,
		));
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
		//$result=RequestController::destroy_vm(NodeModel::model()->findByPk($node),$id);
		//Yii::app()->user->setFlash('success', "Virtual Machine status destroyed");
		try
		{
			$result=RequestController::destroy_vm(NodeModel::model()->findByPk($node),$id);	
			
			if(!isset($_GET['ajax']))
		        Yii::app()->user->setFlash('success','Virtual Machine destroyed');
		    else
		        echo "<div class='flash-success'>Virtual Machine destroyed</div>";
			
		}catch(CHttpException $e){			
			if(!isset($_GET['ajax']))
		        Yii::app()->user->setFlash('error',$e->getMessage());
		    else
		        echo "<div class='flash-error'>".$e->getMessage()."</div>";			
		}		

		if(!isset($_GET['ajax']))
    		$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('node/view','id' => $node));
	}

	public function actionDelete($id,$node)
	{

		try
		{
			RequestController::delete_vm(NodeModel::model()->findByPk($node),$id);			
			//Delete virtual machines assigned to user
			AssignedVM::model()->deleteAllByAttributes(array('node_name'=>$node,'machine_name'=>$id));

			//Delete all virtual machines's entries associated to projects
			ProjectNodeMachineModel::model()->deleteAllByAttributes(array('node_name'=>$node,'machine_name'=>$id));


			//Delete entry from cache
			CacheController::deleteEntry($node,$id);

			if(!isset($_GET['ajax']))
		        Yii::app()->user->setFlash('success','Virtual Machine Removed');
		    else
		        echo "<div class='flash-success'>Virtual Machine Removed</div>";
			
		}catch(CHttpException $e){			
			if(!isset($_GET['ajax']))
		        Yii::app()->user->setFlash('error',$e->getMessage());
		    else
		        echo "<div class='flash-error'>".$e->getMessage()."</div>";			
		}		

		if(!isset($_GET['ajax']))
    		$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('node/view','id' => $node));

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
			$network_interface=(isset($_POST['network_interface'])&& !empty($_POST['network_interface']))?$_POST['network_interface']:'TO_BE_FILLED';
			$gui=isset($_POST['gui'])? $_POST['gui']:false;
			$ssh=isset($_POST['ssh'])? $_POST['ssh']:false;
			$vagrant_version=isset($_POST['vagrant_version'])? $_POST['vagrant_version']:"";
			
    
    
    		$config_block=strtolower($vm_name)."_config";
    
    		//"config.vm.synced_folder \".\",\"/vagrant\",disabled: true

			$cfg="Vagrant.configure(\"2\") do |config|\n\n".				 
				 "\tconfig.vm.define(:".$vm_name.") do |".$config_block."|\n".
				 "\t\t".$config_block.".vm.synced_folder \".\",\"/vagrant\",disabled: true\n".
				 "\t\t".$config_block.".vm.box = \"".$box_name."\"\n".
				 "\t\t".$config_block.".vm.hostname = \"".$host_name."\"\n".
				 "\t\t".$config_block.".vm.network(:".$network_type.",:bridge => \"".$network_interface."\")\n";

			if ($ssh!="true"){
				if ($vagrant_version=="1.2.2")
					$cfg.="\t\t".$config_block.".ssh.max_tries = 1\n";	
				else
					$cfg.="\t\t".$config_block.".vm.boot_timeout = 1\n";	
			}

			$cfg.="\t\t".$config_block.".vm.provider :virtualbox do |v| \n".
				 "\t\t\t v.name = \"".$vm_name."\"\n";

			if ($gui=="true"){				 					
				$cfg.="\t\t\t v.gui = true\n";
			}

			

			$cfg.= "\t\tend\n\n".
				 "\tend\n\n".
				 "end\n";

			

			


#				 config.vm.provider :virtualbox do |vb|
  #   # Don't boot with headless mode
  #   vb.gui = true
  #
  #   # Use VBoxManage to customize the VM. For example to change memory:
  #   vb.customize ["modifyvm", :id, "--memory", "1024"]
  # end

			


			if (Yii::app()->request->isAjaxRequest)
				echo CJSON::encode(array(                         
	                        'cfg'=>$cfg
	                        ));
		}
	}



	
}
