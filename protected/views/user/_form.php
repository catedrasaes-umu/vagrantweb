<?php
/* @var $this UserController */
/* @var $model User */
/* @var $form CActiveForm */
?>

<div class="form">



<?php
	 $form=$this->beginWidget('booster.widgets.TbActiveForm', array(
		'id'=>'user-form',
		'type' => 'horizontal',
		'htmlOptions' => array('class' => 'well'), // for inset effect
		'enableAjaxValidation'=>false,
		'htmlOptions' => array('autocomplete'=>'off','class' => 'well'), // for inset effect
	));

?>

<?php 
	//$form=$this->beginWidget('CActiveForm', array(
	//'id'=>'user-form',
	//'enableAjaxValidation'=>false,
//)); 
?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password',array('size'=>60,'maxlength'=>128,'value'=>'')); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>	


	<div class="row buttons">
		<?php //echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>

		<?php
		$this->widget(
			    'booster.widgets.TbButton',
			    array('buttonType' => 'submit', 'label' => $model->isNewRecord ? 'Create' : 'Save')
			);
		?>
		
		<?php //echo CHtml::Button('Cancel',array('submit'=>Yii::app()->createUrl("user/admin"))); ?>
		<?php
		$this->widget(
			    'booster.widgets.TbButton',
			    array('buttonType' => 'submit', 'label' => 'Cancel','htmlOptions' => array('submit'=>Yii::app()->createUrl("user/admin")))
			);
		
		?>
	</div>

<?php $this->endWidget(); ?>


<div class="row" >
<?php if ($showroleassignments && !$model->isNewRecord){ ?>

	<?php
		$aroles=Rights::getAssignedRoles($model->id);
		$roles = [];
		foreach ($aroles as $role) {
			array_push($roles, array("nombre"=>$role->name,"description"=>$role->description));
		}
		 

	?>

		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 user" id="assignedroles" style="margin-bottom:40px">
			<h4 style="margin-bottom:0;margin-top:15px">Assigned Roles:</h4>
			<?php
				$this->widget('booster.widgets.TbGridView', array(
				'id'=>'assigned-roles',
				'selectableRows' => 0, 
				'htmlOptions'=>array('class' =>'grid-view table-curved'),
    			// 'dataProvider'=> new RAuthItemDataProvider('roles', array('type'=>2,)),
    			'dataProvider'=> new RAuthItemDataProvider('roles', array('type'=>2,'userId'=>$model->id)),
    			//new CArrayDataProvider(Rights::getAssignedRoles(Yii::app()->user->Id), array('keyField'=>'id')),
    			'columns' => array(
    					array(
		                    'header'=>'Role Name',
		                    'name'=>'name',
		                    'type'	=> 'raw',
		                    // 'value'=>'CHtml::link($data["node_name"], array("node/view","id"=>$data["node_name"]))',                    
		                    // 'cssClassExpression' => '$data["node_status"]? "online" : "offline"',
						),
						array(
		                    'header'=>'Description',
		                    'name'=>'description',
		                    'type'	=> 'raw',
		                    // 'value'=>'CHtml::link($data["node_name"], array("node/view","id"=>$data["node_name"]))',                    
		                    // 'cssClassExpression' => '$data["node_status"]? "online" : "offline"',
						),
    					array(
							'class'=>'booster.widgets.TbButtonColumn',
							'template'=>'{delete}',
							'buttons'=>array
                        	(
                                'delete' => array
                                (
                              //           'url'=>'Yii::app()->createUrl("user/deleterole", array("id"=>$model->id,
				            												  // "role"=>$data["name"]))',
                                	'url'=>'Yii::app()->createUrl("user/deleterole",array("userid"=>'.$model->id.',
                                															"rolename"=>$data->name))',
                                ),
                        	),

						),			
			            
			        ),
    			));
			?>
		</div>



	
<?php } ?>



<?php


if ($showroleassignments && !$model->isNewRecord) {		
    $all_roles=new RAuthItemDataProvider('roles', array('type'=>2,));
  	$data=$all_roles->fetchData();
?>	
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 user" id="availableroles" style="margin-bottom:30px;">
	        <h4 for="type_id" style="margin-bottom:20px">Available Roles:</h4>
	    <?php echo CHtml::dropDownList("type",'',CHtml::listData($data,'name','name'));?> 

	<?php

				$this->widget(
			    'booster.widgets.TbButton',
			    array('buttonType' => 'url', 
			    	'label' => 'Assign',			    	
			    	'htmlOptions' => array('id' => 'assign-button',
			    							'url' => Yii::app()->createUrl("user/addrole", array("userid"=>$model->id)),
			    							'style' => 'margin-left:20px',
			    							))
				);

	?>

		<?php //echo CHtml::button('Assign', array('href'=>Yii::app()->createUrl("user/addrole", array("userid"=>$model->id)),'id' => 'assign-button')); ?>
	</div>
<?php
}
?>

</div>

<?php if ($showvmassignments && !$model->isNewRecord) {	?>

	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 user" id="assignedvm" style="margin-bottom:30px;">
			<h4 for="type_id" style="margin-bottom:20px">Assigned Virtual Machines:</h4>
			<?php
				
				$this->widget('booster.widgets.TbGridView', array(
				'id'=>'assigned-machines',
				'selectableRows' => 0, 
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
	                    array(
							'class'=>'booster.widgets.TbButtonColumn',
							'template'=>'{delete}',
							'buttons'=>array
		                	(
		                        'delete' => array
		                        (
		                      //           'url'=>'Yii::app()->createUrl("user/deleterole", array("id"=>$model->id,
				            												  // "role"=>$data["name"]))',
		                        	'url'=>'Yii::app()->createUrl("user/removevm",array("user"=>'.$model->id.',
		                        														"node"=>$data->node_name,
		                        													 	"vm"=>$data->machine_name))',
		              //           	'click'=>'js:function(){			            					
			            	// 				$.ajax({
								        // 		type: "GET",								        		
								        // 		url: $(this).attr("href"),								        		        		
								        // 		success:function(data) {													   												   
													   // $.fn.yiiGridView.update("available-machines");
								        //         },
								        //         error:function(x, t, m) {								                			
								        //         		$("#flash-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(5000).fadeOut("slow");								                	
								        //         }
								        //     });
				            				
			            	// 			}',
		                        ),
		                	),

						),							
			        ),
				));
			?>
		</div>

		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 user" id="availablevm" style="margin-bottom:30px;">
			<h4 for="type_id" style="margin-bottom:20px">Available Virtual Machines:</h4>
			<?php
				
				
				if (is_null($selectednode))
					$selectednode='';

				$this->widget('booster.widgets.TbGridView', array(
				'id'=>'available-machines',
				'selectableRows' => 0, 		
				'htmlOptions'=>array('class' =>'grid-view table-curved'),				
				'dataProvider'=>  $vms,
				
				//'dataProvider'=>Node::model()->searchprojects($cproject),	
				//'afterAjaxUpdate'=>'function(id, data){ $.fn.yiiGridView.update("available-users",{data: "project="+$("select#project option:selected").val()}); }',            	
				// 'beforeAjaxUpdate'=>'function(id, data){ $.fn.yiiGridView.update("available-machines",{data: "node="+$("select#node option:selected").val()}); }',
				'filter' => $filtersForm,
				'columns' => array(
						array(
		                    'header'=>'Node',		                    
		                    'value'=>'CHtml::encode($data["name"])',		                      		                    
		                    'filter' => CHtml::dropDownList('node','',$node_list,
		                    								array('prompt'=>'Please select a node',
		                    										'options'=>array($selectednode=>array('selected'=>true)),
		                										 )
		            										),
						),	
						array(
							'class'=>'booster.widgets.TbButtonColumn',
							'template'=>'{add}',
							'buttons'=>array
		                	(
		                        'add' => array
		                        (
		                        	'label'=>'<i class="fa fa-plus"></i>',
		                      //           'url'=>'Yii::app()->createUrl("user/deleterole", array("id"=>$model->id,
				            												  // "role"=>$data["name"]))',
		                        	'url'=>'Yii::app()->createUrl("user/addvm",array("user"=>'.$model->id.',
		                        													"node"=>"'.$selectednode.'",
		                        													 "vm"=>$data["name"]))',
		                        	'options'=>array('title'=>''),
		                        	'click'=>'js:function(){			            					
			            					$.ajax({
								        		type: "GET",								        		
								        		url: $(this).attr("href"),								        		        		
								        		success:function(data) {													   												   
													   $.fn.yiiGridView.update("assigned-machines",{data: "id="+'.$model->id.'});
								                },
								                error:function(x, t, m) {								                			
								                		$("#flash-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(5000).fadeOut("slow");								                	
								                }
								            });
				            				return false;
			            				}',
		                        ),
		                	),

						),										
			            
			        ),
				));




				// $this->widget('booster.widgets.TbGridView', array(
	   //  			'dataProvider'=>$nodes,            
	   //          	'filter'=>$filtersForm,
	   //  			'id'=>'node-model-grid',
	   //  			'htmlOptions'=>array('class' =>'grid-view table-curved'),
	   //  			'selectableRows' => 2, 
	   //  			'columns' => array(
	   //  					array (
				//             'header' => 'Node Name',
				//             'name' =>'node_name',
				//             'type'	=> 'raw',	
				//             'cssClassExpression' => '"node"',		            
				//             ),
				//             array (
				//             'header' => 'Machine Name',
				//             'name' =>'vm_name',
				//             'type'	=> 'raw',	
				//             'cssClassExpression' => '"machine"',		            
				// 			),			            
				//         ),
				// ));
			?>
		</div>
	</div>

<?php } ?>


<?php if (Yii::app()->user->isSuperuser && !$model->isNewRecord) {	?>	
	<!--
	<div class="row">
		<div class="user" id="assignedvm" style="margin-bottom:30px;">
			<h4 for="type_id" style="margin-bottom:0px">Virtual machines assigned in Groups:</h4>
			<?php /*
				
				$this->widget('booster.widgets.TbGridView', array(
				'id'=>'inherit-machines',
				'selectableRows' => 0, 
				'dataProvider'=> $inherited,
				'htmlOptions'=>array('class' =>'grid-view table-curved'),
				//'ajaxUpdate'=>'inherit-machines',
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
				));*/
			?>
		</div>
	</div>-->

	<div class="row">
		<div class="user" id="assignedvm" style="margin-bottom:30px;">
			<h4 for="type_id" style="margin-bottom:0px">Virtual machines assigned in Groups:</h4>
			<?php
				
				$this->widget('booster.widgets.TbGridView', array(
				'id'=>'inherit-machines',
				'selectableRows' => 0, 
				'dataProvider'=> $inherited2,
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
	</div>

<?php } ?>


</div><!-- form -->

<script>

	

	$('#assign-button').on('click',function() {
	
		enlace=$(this).attr("url");
	
		enlace=enlace+"&rolename="+$('select#type option:selected').val();
		$.ajax({
	        		type: 'GET',	        			        		
	        		url: enlace,
	        		success:function(data) {
	        			$.fn.yiiGridView.update('assigned-roles');
	                       
	                },
	        	});
		
				
	});
</script>