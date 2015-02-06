<?php
/* @var $this ProjectController */
/* @var $model ProjectModel */
/* @var $form CActiveForm */
?>


<div class="form">



<?php $form=$this->beginWidget('booster.widgets.TbActiveForm', array(
	'id'=>'project-model-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('autocomplete'=>'off','class' => 'well'), // for inset effect
)); ?>


	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'project_name'); ?>
		<?php echo $form->textField($model,'project_name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'project_name'); ?>
	</div>

	<div class="clear"></div>
	<div class=" buttons">

		<?php
			$this->widget('booster.widgets.TbButton',
				array('buttonType' => 'submit', 
					'label' => $model->isNewRecord ? 'Create' : 'Save',					
					'htmlOptions' => array('style'=>'margin-right:20px')));



			$this->widget(
			    'booster.widgets.TbButton',
			    array('buttonType' => 'submit', 'label' => 'Cancel','htmlOptions' => array('submit'=>Yii::app()->createUrl("project/index")))
			);
		?>

		<?php //echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
		<?php //echo CHtml::Button('Cancel',array('submit'=>Yii::app()->createUrl("project/index"))); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->

<?php if (!$model->isNewRecord){?>
	
		
		<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 user" id="assignedvm">
			<h4 style="margin-bottom:0;margin-top:20px">Assigned Machines:</h4>
			<?php
				$this->widget('booster.widgets.TbGridView', array(
				'id'=>'assigned-machines',
				'selectableRows' => 2, 
				'dataProvider'=> new CArrayDataProvider($model->machines, array('keyField'=>'id')),
				'htmlOptions'=>array('class' =>'grid-view table-curved'),
				'ajaxUpdate'=>'node-model-grid',
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
		                    'header'=>'Priority',
		                    'name'=>'priority',
		                    'type'=>'raw',
		                    'value' => 'CHtml::textField("priority",$data["priority"],
		                    								array(
		                    									"size"=>1,
		                    									"maxlength"=>2,
		                    									"style"=>"text-align:center"
		                    									)
																)',
		                    
			                ), 
		             	array(
							'class'=>'booster.widgets.TbButtonColumn',
							'template'=>'{delete}',
							'buttons'=>array
                        	(
                                'delete' => array
                                (                              
                                	'url'=>'Yii::app()->createUrl("project/removemachine",array("id"=>'.$model->id.',		                        													
		                        															"node"=>$data["node_name"],
		                        													 		"vm"=>$data["machine_name"]))',
                                ),
                        	),

						),	
			            
			        ),
				));
			?>
		</div>
	
		
	
		<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 user" id="availablevm" style="margin-bottom:50px">
			<h4 style="margin-bottom:0;margin-top:20px">Available Machines:</h4>
			
			<?php 		
		
			
			if (is_null($selectednode))
				$selectednode='';				
			

			$this->widget('booster.widgets.TbGridView', array(
				'id'=>'node-model-grid',
				'selectableRows' => 0, 		
				'htmlOptions'=>array('class' =>'grid-view table-curved'),				
				'dataProvider'=>  $nodes,
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
		                      
		                        	'url'=>'Yii::app()->createUrl("project/addmachine",array("id"=>'.$model->id.',		                        													
		                        															"node"=>"'.$selectednode.'",
		                        													 		"vm"=>$data["name"]))',
		                        	'options'=>array('title'=>''),
		                        	'click'=>'js:function(){			            					
			            					$.ajax({
								        		type: "GET",								        		
								        		url: $(this).attr("href"),											        		
								        		success:function(data) {													   												   
								        			   
													   $.fn.yiiGridView.update("assigned-machines",{data: "id='.$model->id.'&node="+$("div#node-model-grid select#node option:selected").val()});
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
				?>
		</div>

	
		<?php

			$cproject="";
			if (isset($_GET["project"]))
				$cproject=$_GET["project"];

		?>
		

		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" id="aprousersp" >
			<h4 style="margin-bottom:0;margin-top:15px">Assigned Users:</h4>
			<?php
				$this->widget('booster.widgets.TbGridView', array(
				'id'=>'assigned-users',
				'selectableRows' => 0, 		
				'htmlOptions'=>array('class' =>'grid-view table-curved'),
				'dataProvider'=> User::model()->searchprojects($model->id),	
				'afterAjaxUpdate'=>'function(id, data){ $.fn.yiiGridView.update("available-users",{data: "project='.$model->id.'"}); }',            					


				'columns' => array(	
						array(
		                    'header'=>'User Name',		                    
		                    'value'=>'CHtml::encode($data->username)',                                      		                    
						),						
						array(
							'class'=>'booster.widgets.TbButtonColumn',
							'template'=>'{delete}',
							'buttons'=>array
		                	(
		                        'delete' => array
		                        (
		                      
		                        	'url'=>'Yii::app()->createUrl("project/deleteuser",array("project"=>'.$model->id.',
		                        														  "user"=>$data->id))',
		                        ),
		                	),

						),										
			            
			        ),
				));
			?>
		</div>

		

		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" id="avprousersp">
			<h4 style="margin-bottom:0;margin-top:15px">Available Users:</h4>
			<?php
				$this->widget('booster.widgets.TbGridView', array(
				'id'=>'available-users',
				'selectableRows' => 0, 	
				'htmlOptions'=>array('class' =>'grid-view table-curved'),	
				'dataProvider'=> User::model()->searchfree($model->id),				
				'columns' => array(
						array(
		                    'header'=>'User Name',
		                    
		                    'value'=>'$data->username',
		                	
						),	
						array(
							'class'=>'CButtonColumn',
							'template'=>'{add}',
							'buttons'=>array
		                	(
		                        'add' => array
		                        (
		                        	'label'=>'Add User to Project',
		                        	'url'=>'Yii::app()->createUrl("project/adduser",array("project"=>'.$model->id.',
		                        														  "user"=>$data->id))',
		                        	'imageUrl'=>Yii::app()->request->baseUrl.'/images/adduser.png',
		                        	'click'	  =>'js:function(){			            					
					            					$.ajax({
										        		type: "GET",								        		
										        		url: $(this).attr("href"),								        		        		
										        		success:function(data) {													   												   
															   $.fn.yiiGridView.update("assigned-users",{data: "project="+$("select#project option:selected").val()});
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
			?>
		</div>












<?php } ?>
       
		
	
	
	
	

	




