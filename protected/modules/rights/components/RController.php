<?php
/**
* Rights base controller class file.
*
* @author Christoffer Niska <cniska@live.com>
* @copyright Copyright &copy; 2010 Christoffer Niska
* @since 0.6
*/
class RController extends CController
{
	/**
	* @property string the default layout for the controller view. Defaults to '//layouts/column1',
	* meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	*/
	public $layout='//layouts/column1';
	/**
	* @property array context menu items. This property will be assigned to {@link CMenu::items}.
	*/
	public $menu=array();
	/**
	* @property array the breadcrumbs of the current page. The value of this property will
	* be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	* for more details on how to specify this property.
	*/
	public $breadcrumbs=array();

	/**
	* The filter method for 'rights' access filter.
	* This filter is a wrapper of {@link CAccessControlFilter}.
	* @param CFilterChain $filterChain the filter chain that the filter is on.
	*/
	public function filterRights($filterChain)
	{		

		$filter = new RightsFilter;
		$filter->allowedActions = $this->allowedActions();
		$filter->filter($filterChain);
	}


	private function getTopMenu() {
		 return "<ul class='nav navbar-top-links navbar-right'>".
            		"<li class='dropdown'>".
                		"<a class='dropdown-toggle' data-toggle='dropdown' href='#'>".
                    		"<i class='fa fa-user fa-fw'></i>  <i class='fa fa-caret-down'></i>".
                		"</a>".
                		"<ul class='dropdown-menu dropdown-user'>".                    	
                    	"<li><a href='".Yii::app()->createUrl("site/logout")."'><i class='fa fa-sign-out fa-fw'></i> Logout</a>".
                		"</li>".
            			"</ul>".
            		"</li>".
        		"</ul>";
	}

	private function getDashBoardMenu() {	

		$menu="";

		if (Yii::app()->user->checkAccess('Site.Controlpanel'))
		{

			$menu="<li>
	                    <a href='".Yii::app()->createUrl("site/controlpanel")."'><i class='fa fa-dashboard fa-fw'></i> Dashboard</a>
	               </li>";
        }

        return $menu;
	}

	private function getProjectMenu() {
		
		$projectindex=Yii::app()->user->checkAccess('Project.Index');
		// $projectcreate=Yii::app()->user->checkAccess('Project.Create');
		$projectusers=Yii::app()->user->checkAccess('Project.Manageusers');
                     
		$menu="";

		if ($projectindex || $projectcreate || $projectusers)
		{

	        if ($this->getUniqueId()=="project")
	            $menu="<li class='active'>";
	        else
	            $menu="<li>";

	                    
	            $menu.="<a href='#'><i class='fa fa-bar-chart-o fa-fw'></i> Groups<span class='fa arrow'></span></a>
	            		<ul class='nav nav-second-level'>";

	            if ($projectindex){
	            	$menu.="<li>	
	                    		<a href='".Yii::app()->createUrl("project/index")."'>List Groups</a>
	                		</li>";	                		
            	}
            	// if ($projectcreate){
            		// $menu.="<li>
	             //        		<a href='".Yii::app()->createUrl("project/create")."'>Create Group</a>
	             //    		</li>";
        		// }
        		if ($projectusers){
	            	$menu.="<li>
	                    		<a href='".Yii::app()->createUrl("project/manageusers")."'>Manage Group Users</a>
	                		</li>";
        		}

				$menu.="</ul>                        
	        		</li>";
		}

	    return $menu;
	}

	private function getNodesMenu() {
		$menu="";
		$nodeindex=Yii::app()->user->checkAccess('Node.Index');
		$nodecreate=Yii::app()->user->checkAccess('Node.Create');
		$nodeadmin=Yii::app()->user->checkAccess('Node.Admin');

		

		if ($nodeindex || $nodecreate || $nodeadmin){
			if ($this->getUniqueId()=="node")
	            $menu="<li class='active'>";
	        else
	            $menu="<li>";

	                    
	            $menu.="<a href='#'><i class='fa fa-table fa-fw'></i> Nodes<span class='fa arrow'></span></a>
	            		<ul class='nav nav-second-level'>";
	            if ($nodeindex){
	            	$menu.="<li>
	                            <a href='".Yii::app()->createUrl("node/index")."'>List Nodes</a>
	                        </li>";
                }

                //if ($nodecreate){
	                // $menu.="<li>
	                //             <a href='".Yii::app()->createUrl("node/create")."'>Create Node</a>
	                //         </li>";
                //}
                if ($nodeadmin){                                
	            	$menu.="<li>
	                            <a href='".Yii::app()->createUrl("node/admin")."'>Manage Nodes</a>
	                        </li>";
                }

                $menu.="</ul>                        
	        			</li>";
    	}

	    return $menu;
	}

	private function getUsersMenu(){
		$menu="";
		

		if (Yii::app()->user->isSuperUser) { 
            
            
            if (($this->getUniqueId()=="user") || ($this->getUniqueId()=="rights/authItem"))
                $menu="<li class='active'>";
            else
                $menu="<li>";
        

        
            $menu.="<a href='#'><i class='fa fa-user fa-fw'></i> Users<span class='fa arrow'></span></a>
        			<ul class='nav nav-second-level'>
        				<li>
        					<a href='".Yii::app()->createUrl("user/admin")."'>Manage Users</a>
    					</li>
    					<li>
                			<a href='".Yii::app()->createUrl("rights/authItem/roles")."'>Manage Roles</a>
            			</li>
						<li>
        					<a href='".Yii::app()->createUrl("rights/authItem/permissions")."'>Manage Rights</a>
    					</li>
					                               
        			</ul>            
				</li>";
        
        } 
        return $menu;
	}

	public function getSideMenu() {
		$menu=$this->getDashBoardMenu();
		$menu.=$this->getProjectMenu();
		$menu.=$this->getNodesMenu();
		$menu.=$this->getUsersMenu();
		return $menu;
	}

	public function getMenu() {
		$menu="<nav class='navbar navbar-default navbar-fixed-top' role='navigation' style='margin-bottom: 0'>".        	
            "<div class='navbar-header'>".
                "<button type='button' class='navbar-toggle' data-toggle='collapse' data-target='.sidebar-collapse'>".
                    "<span class='sr-only'>Toggle navigation</span>".
                    "<span class='icon-bar'></span>".
                    "<span class='icon-bar'></span>".
                    "<span class='icon-bar'></span>".
                "</button>".
                "<a class='navbar-brand' href='".Yii::app()->createUrl("site/controlpanel")."'>".CHtml::encode($this->pageTitle)."</a>".
            "</div>";

            if (!Yii::app()->user->isGuest) { 

				$menu.=$this->getTopMenu();

				$menu.="<div class='navbar-default navbar-static-side' role='navigation'>".
            	$menu.="<div class='sidebar-collapse'>";
            	$menu.="<ul class='nav' id='side-menu'>";
            	

                $menu.=$this->getDashBoardMenu();
                        
                        
                        

                        
				$menu.="</ul>";
                $menu.="</div>";                
            	$menu.="</div>";

        	}

        $menu.="</nav>";

        return $menu;
	}


	/**
	* @return string the actions that are always allowed separated by commas.
	*/
	public function allowedActions()
	{
		
		return '';
	}


	

	/**
	* Denies the access of the user.
	* @param string $message the message to display to the user.
	* This method may be invoked when access check fails.
	* @throws CHttpException when called unless login is required.
	*/
	public function accessDenied($message=null)
	{
	
		// if (!$this->isInstalled()){			
		// 	$this->redirect(Yii::app()->createUrl("installer"));
		// }else{


			if( $message===null )
				$message = Rights::t('core', 'You are not authorized to perform this action.');

			$user = Yii::app()->getUser();
			if( $user->isGuest===true )
				$user->loginRequired();
			else
				throw new CHttpException(403, $message);
		//}
	}
}
