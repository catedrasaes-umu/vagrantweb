<?php
/* @var $this UserController */
/* @var $model User */

$this->breadcrumbs=array(
	'Users'=>array('admin'),
	$model->username,
);

$this->menu=array(
	// array('label'=>'List User', 'url'=>array('index')),
	array('label'=>'Create User', 'url'=>array('create')),
	array('label'=>'Update User', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete User', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Users', 'url'=>array('admin')),
);
?>

<h1 class="page-header">View User <?php echo $model->username; ?></h1>

<?php $this->widget('booster.widgets.TbDetailView', array(
	'data'=>$model,
	'htmlOptions'=>array('class' =>'detailview-curved'),	
	'attributes'=>array(		
		'username',		
		'email',
	),
)); ?>


<?php
		$aroles=Rights::getAssignedRoles($model->id);
		$roles = [];
		foreach ($aroles as $role) {
			array_push($roles, array("nombre"=>$role->name,"description"=>$role->description));
		}
		 

	?>

		<div style="margin-top:40px">
			<h3 style="margin-bottom:0;margin-top:15px">Assigned Roles:</h3>
			<?php
				$this->widget('booster.widgets.TbGridView', array(
				'id'=>'assigned-roles',
				'selectableRows' => 0, 
				'htmlOptions'=>array('class' =>'grid-view table-curved'),    			
    			'dataProvider'=> new RAuthItemDataProvider('roles', array('type'=>2,'userId'=>$model->id)),    			
    			'columns' => array(
    					array(
		                    'header'=>'Role Name',
		                    // 'name'=>'name',
		                    'type'	=> 'raw',
		                    //'value'=>'CHtml::link($data->name, array("rights/authItem/update","name"=>$data->name))',                    
		                    'value'=>'($data->name=="AdminRole")?"AdminRole":CHtml::link($data->name, array("rights/authItem/update","name"=>$data->name))',
		                    // 'cssClassExpression' => '$data["node_status"]? "online" : "offline"',
						),
						array(
		                    'header'=>'Description',
		                    'name'=>'description',
		                    'type'	=> 'raw',
		                    // 'value'=>'CHtml::link($data["node_name"], array("node/view","id"=>$data["node_name"]))',                    
		                    // 'cssClassExpression' => '$data["node_status"]? "online" : "offline"',
						),    							
			            
			        ),
    			));
			?>
		</div>



		<div style="margin-top:50px">
			<h3 style="margin-bottom:0;margin-top:15px">Assigned Virtual Machines:</h3>
			<?php
				
				$this->widget('booster.widgets.TbGridView', array(
				'id'=>'assigned-machines',
				'selectableRows' => 2, 
				'dataProvider'=> new CArrayDataProvider($model->machines, array('keyField'=>'id')),
				'htmlOptions'=>array('class' =>'grid-view table-curved'),
				'ajaxUpdate'=>'available-machines',
				'columns' => array(
			            array(
		                    'header'=>'Node Name',
		                    'name'=>'node_name',
		                    'type'	=> 'raw',	
		                    'cssClassExpression' => '"node"',	                    
							),
						array(
		                    'header'=>'Virtual Machine',
		                    'type'	=> 'raw',		                    
		                    'name' => 'machine_name',
		                    'cssClassExpression' => '"machine"',
		                    ),		                    					
			        ),
				));
			?>
		</div>

		
		<div style="margin-top:50px;">
			<h3 for="type_id" style="margin-bottom:0;margin-top:15px">Virtual machines assigned in Groups:</h3>
			<?php
				
				$this->widget('booster.widgets.TbGridView', array(
				'id'=>'inherit-machines',
				'selectableRows' => 0, 
				'dataProvider'=> $inherited,
				'htmlOptions'=>array('class' =>'grid-view table-curved'),
				//'ajaxUpdate'=>'inherit-machines',
				'columns' => array(
						array(
		                    'header'=>'Group',
		                    'type'	=> 'raw',		                    
		                    'name' => 'project_name',
		                    'cssClassExpression' => '"project"',
		                    ),  
			            array(
		                    'header'=>'Node Name',
		                    'name'=>'node_name',
		                    'type'	=> 'raw',	
		                    'cssClassExpression' => '"node"',	                    
							),
						array(
		                    'header'=>'Virtual Machine',
		                    'type'	=> 'raw',		                    
		                    'name' => 'machine_name',
		                    'cssClassExpression' => '"machine"',
		                    ),		                    		
			        ),
				));
			?>
		
		</div>
