<?php

include_once 'RouteController.php';
include_once 'OperationController.php';


class RequestController extends Controller
{
	
	const COOKIE= "COOKIE_TOKEN";
	const TOKEN= "MD5_TOKEN";
	const GET_VERB = "get";
	const POST_VERB = "post";
	const PUT_VERB = "put";
	const DELETE_VERB = "delete";
	const LOCATION_CODE = 202;
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
	
	
	// public static function get_node_vm_status($node)
	// {
// 		
		// $noderow=NodeModel::model()->findByPk($node);
		// if (is_null($noderow))
			// return null;
// 		
// 		
		// return RequestController::get_vm_status($noderow->node_address,$noderow->node_port);
	// }
	
	public static function get_node_boxes($node)
	{		
		$boxes = RequestController::execute_request($node,self::GET_VERB, RouteController::box_list_route());
		
		return json_decode($boxes[1],true);
		
	}
	
	
	
	/**
	 * Retrieve virtual machines from node
	 * @param integer $id the ID of the model to be displayed
	 */
	//public static function get_vm_status($node_address,$node_port,$vm_name=null)
	public static function get_vm_status($node,$vm_name=null)
	{

		if (is_null($vm_name))
		{
			$vms = RequestController::execute_request($node,
													self::GET_VERB, 
													RouteController::vm_status_all_route());			
		}else{
			//TODO
		}
		return ($vms===false)?null:json_decode($vms[1],true);
	}
	
	public static function node_listening($address,$port)
	{
		
		$fp = @fsockopen($address,
						$port,
						$errno,
						$errstr,
						1);
			
		
						
		if($fp)
		{						
			fclose($fp);						
			return true;
		}
		
		return false;
	}
	
	
	public static function get_vm_snapshot_list($node,$id)
	{
		
		$result = RequestController::execute_request($node,
													self::GET_VERB, 
													RouteController::vm_snapshots_route($id));	
			
		
		return json_decode($result[1],true);
	}
	
	public static function do_vm_command($node,$id,$command)
	{	
		$result="";
		
		$timeout=180;
		$route = RouteController::vm_up_route();
		$params = array('vmname' => $id);
		
		
		
		
		
		switch($command)
		{
			case "run":
				$route =  RouteController::vm_up_route();
        		break;
			case "pause":
				$route = RouteController::vm_suspend_route();
	    		break;
			case "halt":
				$params['force']=true;
				$route = RouteController::vm_halt_route();
        		break;				
		}	
				
		
		$result = RequestController::execute_request($node,
											self::POST_VERB, 
											$route,
											$params,
											$timeout);		
		
		
		return $result;
		//return json_decode($result,true);	
			
		
	}

	public static function destroy_vm($node,$vm)
	{
		
		$result = RequestController::execute_request($node,
													self::POST_VERB, 
													RouteController::vm_destroy_route(),
													array('vmname' => $vm));
		
		
			
		return json_decode($result[1],true);
		
	}
	
	public static function add_vm($node,$cfg)
	{
		$result = RequestController::execute_request($node,
													 self::PUT_VERB, 
													 RouteController::vm_add_route(),													 
													 array('file' => $cfg, 'rename' => true ));
													 
	 	return $result[1];
	}
	
	public static function delete_vm($node,$vm)
	{
		
		
		$result = RequestController::execute_request($node,
													 self::DELETE_VERB, 
													 RouteController::vm_delete_route($vm),
													 // array('vm' => $vm));
													 array('vm' => $vm, 'remove' => true ));
		
		
			
		return $result[1];
		
	}
	
	
	public static function add_box($node,$boxname,$url)
	{	
			
		
		$result = RequestController::execute_request($node,
													self::POST_VERB,
													RouteController::box_add_route(),
													array('box'=>$boxname,'url'=>$url));
		
			
		//return json_decode($result[1],true);
		return $result;
		
	}
	
	public static function delete_box($node,$box,$provider)
	{
			
		
		$result = RequestController::execute_request($node,
													self::DELETE_VERB,
													RouteController::box_delete_route($box, $provider));
		
			
		return json_decode($result[1],true);
		
	}
	
	
	public static function do_restore_snapshot($node,$id,$uuid)
	{												
									
													
		$result = RequestController::execute_request($node,
											self::POST_VERB,
											RouteController::vm_snapshot_restore_route($id),											
											array('vmname' => $id,'snapid'=>$uuid),
											120);
		
			
		// return json_decode($result[1],true);
		return $result[1];
	}
	
	public static function do_delete_snapshot($node,$id,$uuid)
	{												
									
		
		$result = RequestController::execute_request($node,
											self::DELETE_VERB,
											RouteController::vm_snapshot_delete_route($id,$uuid),
											150);
											
		return $result[1];	
															
		
			
		
	}

	public static function do_take_snapshot($node,$id,$name,$desc)
	{	
		$result = RequestController::execute_request($node,
											self::POST_VERB,
											RouteController::vm_snapshot_take_route($id),											
											array('vmname' => $id,'name' => $name,'desc'=>$desc),											
											120);
			
		return json_decode($result[1],true);
	}


	public static function get_config_file($node)
	{		
				
		$result = RequestController::execute_request($node,
													self::GET_VERB,
													RouteController::config_show_route());		
	
		return $result[1];	
	}
	
	public static function do_update_config($node,$cfile)
	{
		
		$result = RequestController::execute_request($node,
												  self::POST_VERB,
												  RouteController::config_show_upload_route(),
												  array('file' => $cfile));
												  
		return $result[1];	
	}
	
	
	public static function get_operation($node,$operation_id)
	{
		$result = RequestController::execute_request($node,
												  self::GET_VERB,
												  RouteController::node_operation_route($operation_id)
												  );
												  
		return $result[1];												  
												  
												  
												  
	}
	
	public static function node_password_change($node,$password)
	{
		$result = RequestController::execute_request($node,
												  self::POST_VERB,
												  RouteController::node_password_change_route(),
												  array('password' => md5($password)));
	  	return $result[1];	
	}
	
	
	private function calc_token($token,$node_password)
	{		
		return md5($token.$node_password);		
	}
	
	private function login($node,$rest_client)
	{
		
		//We want the response headers
		$rest_client->option('HEADER', TRUE);
		// $rest_client->option('VERBOSE', TRUE);
		// $rest_client->option('RETURNTRANSFER', TRUE);
		
		$response = $rest_client->get(RouteController::login_route());
		
		$response_headers = $rest_client->response_headers();
		
		//Searching for Set-Cookie and Content_md5 headers		
		$result[self::COOKIE]=isset($response_headers['Set-Cookie'])? $response_headers['Set-Cookie']: null;
		$result[self::TOKEN]=isset($response_headers['Content_md5'])? 
							RequestController::calc_token($response_headers['Content_md5'],$node->node_password): null;		
		
		$rest_client->option('HEADER', FALSE);
		
							 
		return $result;
		
	}
	
	private function execute_request($node,$http_verb,$path, $params = NULL,$timeout=0)
	{		
		
		//$noderow=NodeModel::model()->findByPk($node_id);
		
		
		// if (is_null($noderow))
			// return null;
			
		if (is_null($node))
			return null;
		
		if (!RequestController::node_listening($node->node_address,$node->node_port))
			return null;	
	
		
		$rest = new RESTClient();
		$server="http://".$node->node_address.":".$node->node_port;
		$rest->initialize(array('server' => $server));
		
		
		$client_cred = RequestController::login($node,$rest);
		

		//debug($client_cred);
		
		//Setting credential headers
		if (isset($client_cred[self::COOKIE]))		
			$rest->option('COOKIE', $client_cred[self::COOKIE]);
		
		if (isset($client_cred[self::TOKEN]))		
			$rest->set_header('CONTENT_MD5', $client_cred[self::TOKEN]);
		
		
		$rest->option('timeout', $timeout);
		
		 
		 
		
		
		$result = $rest->$http_verb($path,$params);
		
		
		
		
		
// 		
		
		if ($rest->status()==202)
		{						
			OperationController::addBackgroundOperation(trim(str_replace("Location:","",$result)),RouteController::getOperationID($path),$node->node_name);				
		}
		elseif ($rest->status()>=400)
		{	
			throw new CHttpException($rest->status(),$result);
		}
		
		
		
		
		return array($rest->status(),$result);
		
	}

}

