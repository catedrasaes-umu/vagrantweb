<?php

include_once 'RequestController.php';

class CacheController extends CController
{
	const EXPIRATION_TIME = 150;
	const VM_NAME_INVALID = "default";

	private static function shouldUpdate($node){
		//Si no está en caché, updatear
		$cache_model = CachedData::model()->findAllByAttributes(array('node_name'=>$node));	
		if (!$cache_model)
		{			
			return true;
		}


		$update=false;

		//Si estamos consultando esta función significa que el nodo está online
		//así que si en cache está offline, es necesario actualizar
		if ((sizeof($cache_model)==1) && ($cache_model[0]["node_status"]==false))
		{
			$update=true;
		}else{
		
			//Si el tiempo de alguna de sus máquinas ha expirado updatear
			foreach ($cache_model as $entry) {			
				if (intval($entry["expiration"]) < time()){
					$update=true;
				}		 	
			 } 
		}

		 
		if ($update)
			CacheController::deleteEntry($node);

		return $update;
	}
	
	public static function updateCacheFromStatusArray($node,$vms_array){
		if (is_null($vms_array) || sizeof($vms_array)==0)
			return;

		foreach ($vms_array as $keyvm => $valuevm) {	
			CacheController::updateVirtualMachineStatus($node,$valuevm["name"],$valuevm["provider"],$valuevm["status"]);
		}
	}

	public static function fillCache($node=null){
		
		

		if (is_null($node) || empty($node)){			
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




		$time = time() + CacheController::EXPIRATION_TIME;
		$cache_model = CachedData::model();	
	

		foreach ($dataProvider->getData() as $node) {
			
			if (RequestController::ping_node($node)){
				
				if (!CacheController::shouldUpdate($node->node_name))
					continue;

				
				$vms = RequestController::get_vm_status($node);
				
				if (sizeof($vms)==0){									
					$criteria1 = new CDbCriteria();
					$criteria1->condition="node_name=:nodename";
					$criteria1->params = array(
										':nodename' => $node->node_name,														
										);

					$cdata=$cache_model->find($criteria1);
					if (!$cdata){
						$cdata = new CachedData;
						$cdata -> node_name = $node -> node_name;
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
										':nodename' => $node->node_name,													
										);

					//Primero eliminamos todos las entradas de ese nodo por si hubiera alguna
					$cache_model->deleteAll($criterianode);

					foreach ($vms as $vm) {		
						if ($vm["name"]!=CacheController::VM_NAME_INVALID)
						{																																	
							$cdata = new CachedData;
							$cdata -> node_name = $node -> node_name;
							$cdata -> node_status = true;
							$cdata -> vm_name = $vm["name"];
							$cdata -> status = $vm["status"];
							$cdata -> provider = $vm["provider"];
							$cdata -> expiration = $time;										
													
							$cdata -> save();	
						}
					}
				}
			}else{

				$criteria1 = new CDbCriteria();
								$criteria1->condition="node_name=:nodename";
								$criteria1->params = array(
													':nodename' => $node->node_name,													
													);

				//Primero eliminamos todos las entradas de ese nodo por si hubiera alguna
				$cache_model->deleteAll($criteria1);
				
				$cdata = new CachedData;
				$cdata -> node_name = $node -> node_name;
				$cdata -> node_status = false;
				$cdata -> vm_name = '';
				$cdata -> status = '';
				$cdata -> provider = '';
				$cdata -> expiration = $time;

				$cdata -> save();


			}
		}
	}


	public static function updateVirtualMachineStatus($node,$vm,$provider,$status){

		if ($vm==CacheController::VM_NAME_INVALID)
			return;
		
		$cache_model = CachedData::model()->findByAttributes(array('node_name'=>$node,'vm_name'=>$vm));	

		//Si existe en cache, actualizamos el estado
		if ($cache_model){						
			$cache_model -> status = $status;			
			$cache_model -> expiration = time() + CacheController::EXPIRATION_TIME;
			$cache_model -> save();
		}else{
			//Si no existe en cache, añadimos la entrada
			$cdata = new CachedData;
			$cdata -> node_name = $node;
			$cdata -> node_status = true;
			$cdata -> vm_name = $vm;
			$cdata -> status = $status;
			
			if (!is_null($provider))
				$cdata -> provider = $provider;

			$cdata -> expiration = time() + CacheController::EXPIRATION_TIME;
									
			$cdata -> save();

		}
		
	}

	public static function emptyCache() {
		CachedData::model()->deleteAll();
	}

	public static function deleteEntry($node,$vm=null){
		if (is_null($node) || $node=='')
		{			
			CacheController::emptyCache();
		}else{			
			if (is_null($vm))
				CachedData::model()->deleteAllByAttributes(array('node_name'=>$node));
			else					
				CachedData::model()->deleteAllByAttributes(array('node_name'=>$node,'vm_name'=>$vm));
		}
	}
	

}
