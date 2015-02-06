<?php

//include_once('RequestController');

include_once 'RequestController.php';

// include('../models/ProjectPendingOperations');

class LauncherController extends CController
{
	var $project_id=-1;
	var $next_operations = null;
	var $active_operations = null;

	// public function actionCheckidle()
	// {

	// 	//Si está ejecutando acciones indicamos que no está idle
	// 	if (!empty($this->active_operations))
	// 		return false;

	// 	return true;
	// }



	public function actionExecute()
	{
		$conn=Yii::app()->db;
		$nextcriteria= new CDbCriteria();
		$ppom = ProjectPendingOperations::model();
		$projects = ProjectModel::model()->findAll();
		foreach ($projects as $project) {

			//Comprobando si hay operaciones en curso
			$command=$conn->createCommand("SELECT ppo.id,ppo.pnm_id,ppo.project_id,ppo.operation_id,status,status_msg,command,
											pnm.node_name,pnm.machine_name,pnm.priority
										 FROM project_pending_operations_table ppo
									   LEFT JOIN project_node_machine_table pnm ON ppo.pnm_id=pnm.id
									   WHERE ppo.project_id=".$project->id." AND ppo.status=".OperationController::OPERATION_IN_PROGRESS." order by pnm.priority");

			$auxcom=$command->queryAll();
			if (!empty($auxcom))
				continue;

			$command=$conn->createCommand("SELECT ppo.id,ppo.pnm_id,ppo.project_id,status,status_msg,command,
									pnm.node_name,pnm.machine_name,pnm.priority
								 FROM project_pending_operations_table ppo
							   LEFT JOIN project_node_machine_table pnm ON ppo.pnm_id=pnm.id
							   WHERE ppo.project_id=".$project->id." AND ppo.status=".OperationController::OPERATION_WAITING." order by pnm.priority");

			$this->next_operations=$command->queryAll();
			//var_dump($this->next_operations);
			if (!empty($this->next_operations))
			{
				$priority=-1;
				//echo "HOLA EN LAUNCHER mi id es ".$this->project_id;
				foreach ($this->next_operations as $key => $value) {
					if ($priority==-1)
						$priority=$value["priority"];
					else if ($priority!=$value["priority"]){ 
						//Si no tiene la misma prioridad que el anterior salimos del bucle
						break;
					}
					//Ejecutar las operaciones					

					$result=RequestController::do_vm_command(NodeModel::model()->findByPk($value["node_name"]),
															$value["machine_name"],
															$value["command"]);		

					

					
					


					if ($result[0]==RequestController::LOCATION_CODE){
						//Actualizamos el estado de las "pending operations en la bbdd"
						$conn=Yii::app()->db;
						$command=$conn->createCommand("UPDATE project_pending_operations_table SET status=".OperationController::OPERATION_IN_PROGRESS.",operation_id=".preg_replace('/[^0-9]/','',$result[1])." WHERE id=".$value["id"]);
						$command->execute();
					}
				}
				return true;
			}
		}

		

		
		//Si hay operaciones a ejecutar
		
		return false;
	}


	function __construct(){ 

		// $conn=Yii::app()->db;
		// $command=$conn->createCommand("SELECT ppo.id,ppo.pnm_id,ppo.project_id,ppo.operation_id,status,status_msg,command,
		// 									pnm.node_name,pnm.machine_name,pnm.priority
		// 								 FROM project_pending_operations_table ppo
		// 							   LEFT JOIN project_node_machine_table pnm ON ppo.pnm_id=pnm.id
		// 							   WHERE ppo.status=".OperationController::OPERATION_IN_PROGRESS." order by pnm.priority");

		// $this->active_operations=$command->queryAll();
		// debug($this->active_operations);
		
		/*
		
		$acriteria = new CDbCriteria();
		$acriteria->addInCondition("status", array(0),"AND");	    
		$ppom = ProjectPendingOperations::model();
		// $this->active_operations = $ppom -> getCommandBuilder() -> 
		// 								createFindCommand($ppom -> tableSchema, $acriteria) -> queryAll();

		$conn=Yii::app()->db;
		$command=$conn->createCommand("SELECT ppo.id,ppo.pnm_id,ppo.project_id,ppo.operation_id,status,status_msg,command,
											pnm.node_name,pnm.machine_name,pnm.priority
										 FROM project_pending_operations_table ppo
									   LEFT JOIN project_node_machine_table pnm ON ppo.pnm_id=pnm.id
									   WHERE ppo.status=".OperationController::OPERATION_PENDING." order by pnm.priority");

		$this->active_operations=$command->queryAll();
		debug($this->active_operations);

		unset($acriteria);

		//En active_operations están las operaciones en curso. Cada vez que se crea el launcher
		//se comprueba si las operaciones acticas han finalizado y se actualiza la tabla de 
		//project_pending_operations_table
		foreach ($this->active_operations as $key => $value) {
			
			$result=RequestController::get_operation(NodeModel::model()->findByPk($value["node_name"]),
														$value["operation_id"]);		
			$result=json_decode($result);
			
			// var_dump($result);
			// var_dump($value);


			$op_code=$result[0];
			
			if ($op_code!=OperationController::OPERATION_IN_PROGRESS)
			{

				$statmsg="";
				//Error
				if ($op_code!=OperationController::OPERATION_SUCCESS)
				{	
					$statmsg=$result[1];
				}
				
			    
				//Actualizamos la información de la bbdd
				$command=$conn->createCommand("UPDATE project_pending_operations_table SET status=$op_code,status_msg='".substr($statmsg,0,200)."'".
			    "WHERE operation_id=".$value['operation_id']);
				$command->execute();
				

				//Independientemenete de si es success o error eliminamos la operación como activa
				unset($this->active_operations[$key]);

			}
			
			
			
			
		}
		*/

		/*

		//Si no se están realizando acciones procedemos a la carga de nuevas operaciones		
		if (empty($this->active_operations))
		{	
			
			$nextcriteria= new CDbCriteria();
			$nextcriteria->addInCondition("status", array(OperationController::OPERATION_WAITING),"AND");	    
			$this->next_operations = $ppom -> getCommandBuilder() -> 
										 createFindCommand($ppom -> tableSchema, $nextcriteria) -> queryAll();			

			//$conn=Yii::app()->db;
			$command=$conn->createCommand("SELECT ppo.id,ppo.pnm_id,ppo.project_id,status,status_msg,command,
												pnm.node_name,pnm.machine_name,pnm.priority
											 FROM project_pending_operations_table ppo
										   LEFT JOIN project_node_machine_table pnm ON ppo.pnm_id=pnm.id
										   WHERE ppo.project_id=".$project_id." AND ppo.status=".OperationController::OPERATION_WAITING." order by pnm.priority");
			$this->next_operations=$command->queryAll();
			//var_dump($this->next_operations);
		}

		*/
	}
	
}