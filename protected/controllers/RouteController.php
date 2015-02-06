<?php


class RouteController extends CController
{
	const BOX_LIST_ROUTE =	"/api/box/list";
	//const BOX_DELETE_ROUTE =	"/api/box/:box/:provider/delete";
	const BOX_ADD_ROUTE = "/api/box/add";
	const BOX_DOWNLOAD_ROUTE =	"/api/box/download";

	
	const VM_UP_ROUTE = "/api/vm/up";
	const VM_HALT_ROUTE = "/api/vm/halt";
	const VM_DESTROY_ROUTE ="/api/vm/destroy";
	const VM_SUSPEND_ROUTE = "/api/vm/suspend";
	const VM_RESUME_ROUTE = "/api/vm/resume";
	const VM_PROVISION_ROUTE = "/api/vm/provision";
	const VM_STATUS_ALL_ROUTE = "/api/vm/status";
	// const VM_DELETE_ROUTE = "/api/vm/:vm/delete";
 	const VM_ADD_ROUTE = "/api/vm/add";
	//const VM_STATUS_ROUTE = "/api/vm/:vm/status";			
	//const SSH_CONFIG_ROUTE = "/api/vm/:vm/sshconfig";
	
	const SNAPSHOTS_ALL_ROUTE = "/api/vm/snapshots";
	//const VM_SNAPSHOTS_ROUTE = "/api/vm/:vm/snapshots";
	//const VM_SNAPSHOT_TAKE_ROUTE = "/api/vm/:vm/take";
	//const VM_SNAPSHOT_RESTORE_ROUTE = "/api/vm/:vm/restore";
	
	//const VM_BACKUP_LOG_ROUTE = "/api/vm/:vm/backuplog";
	const NODE_BACKUP_LOG_ROUTE = "/api/backuplog";
	const NODE_OPERATION_ROUTE = "/api/backuplog";
	
	const NODE_CONFIG_SHOW_ROUTE = "/api/config/show";
	const NODE_CONFIG_UPLOAD_ROUTE = "/api/config/upload";
	
	const LOGIN_ROUTE = "/api/login";
	
	const NODE_PASSWORD_CHANGE = "/api/password/change";
	
	const NODE_INFO = "/api/info";

	
	public static function getOperationID($operation)
	{
		
		switch($operation)
		{
			case RouteController::VM_UP_ROUTE:
			case RouteController::VM_HALT_ROUTE:
			case RouteController::VM_SUSPEND_ROUTE:
			case RouteController::VM_RESUME_ROUTE:
				return "VM_STATUS";
			break;
			case RouteController::VM_DESTROY_ROUTE:
			case RouteController::VM_ADD_ROUTE:			
				return "NODE_STATUS";
			break;
			case RouteController::BOX_ADD_ROUTE:
				return "BOX_STATUS";			
			break;
			
		}
		
		if (RouteController::is_snapshot_restore_route($operation))
			return "SNAPSHOT_STATUS";
		
		
		return "";
	}
	
	public static function node_password_change_route()
	{
		return self::NODE_PASSWORD_CHANGE;
	}
	
	public static function node_operation_route($operation_id)
	{
		return "/api/queue/".$operation_id;
		
	}
	
	public static function box_list_route()
	{
		return self::BOX_LIST_ROUTE;
	}

	public static function box_download_route()
	{
		return self::BOX_DOWNLOAD_ROUTE;
	}
		
	public static function box_delete_route($box,$provider)
	{
		return "/api/box/".$box."/".$provider."/delete";
	}
	
	public static function box_add_route()
	{
		return self::BOX_ADD_ROUTE;
	}
	
	public static function vm_up_route()
	{
		return self::VM_UP_ROUTE;
	}
	
	public static function vm_suspend_route()
	{ 
		return self::VM_SUSPEND_ROUTE;
	}
	
	public static function vm_resume_route()
	{
		return self::VM_RESUME_ROUTE;
	}
	
	public static function vm_halt_route()
	{
		return self::VM_HALT_ROUTE;
	}
	
	public static function vm_destroy_route()
	{
		return self::VM_DESTROY_ROUTE;
	}
	
	public static function vm_delete_route($vm)
	{
		return "/api/vm/".$vm."/delete";	
	}	

	public static function vm_info_route($vm)
	{
		return "/api/vm/".$vm."/info";	
	}	
	
	public static function vm_add_route()
	{
		return self::VM_ADD_ROUTE;
	}
	
	public static function vm_status_route($vm)
	{
		return "/api/vm/".$vm."/status";		
	}
	
	public static function vm_provision_route()
	{
		return self::VM_PROVISION_ROUTE;
	}
	
	public static function vm_status_all_route()
	{
		return self::VM_STATUS_ALL_ROUTE;
	}
	
	public static function vm_sshconfig_route($vm)
	{
		return "/api/vm/".$vm."/sshconfig";
	}
	
	public static function snapshots_all_route()
		{
		return self::SNAPSHOTS_ALL_ROUTE;
	}
	
	public static function vm_snapshots_route($vm)
	{
		return "/api/vm/".$vm."/snapshots";		
	}
	
	public static function vm_snapshot_take_route($vm)
	{		
		return "/api/vm/".$vm."/take";
	}
	
	
	public static function is_snapshot_restore_route($route)
	{
		if ((preg_match("/^\/api\/vm\//", $route)) && (preg_match("/restore$/", $route))) 
			return true;
		
		return false;
	}
	
	public static function vm_snapshot_restore_route($vm)
	{
		return "/api/vm/".$vm."/restore";		
	}
	
	public static function vm_snapshot_delete_route($vm,$uuid)
	{
		return "/api/vm/".$vm."/".$uuid."/delete";		
	}
	
	public static function node_backup_log_route()
		{
		return self::NODE_BACKUP_LOG_ROUTE;
	}
	
	public static function vm_backup_log_route($vm)
	{
		return "/api/vm/".$vm."/backuplog";		
	}
		
		
	public static function config_show_route()
      {
		return self::NODE_CONFIG_SHOW_ROUTE;
    }
	
	public static function config_show_upload_route()
    {
		return self::NODE_CONFIG_UPLOAD_ROUTE;
    }	
		
	public static function login_route()
		  {
		return self::LOGIN_ROUTE;
	}

	public static function node_info_route(){
		return self::NODE_INFO;
	}
}
