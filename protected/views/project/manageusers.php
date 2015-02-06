<?php


Yii::app()->clientScript->registerScript('sel_status', "
        $('#projectlist').change(function() {
            // alert(this.value);
            // $.fn.yiiGridView.update('assigned-users', {
            //         data: $(this).val()
            // });            
            return false;
        });
    ");

$this->breadcrumbs=array(
	'Group Users',
);

$this->menu=array(
	array('label'=>'List Groups', 'url'=>array('index')),	

);




$cproject="";
if (isset($_GET["project"]))
	$cproject=$_GET["project"];



?>


<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" id="aprousers">
	<h3 style="margin-bottom:0;margin-top:15px">Assigned Users:</h3>
	<?php
		$this->widget('booster.widgets.TbGridView', array(
		'id'=>'assigned-users',
		'selectableRows' => 0, 		
		'htmlOptions'=>array('class' =>'grid-view table-curved'),
		'dataProvider'=> User::model()->searchprojects($cproject),	
		'afterAjaxUpdate'=>'function(id, data){ $.fn.yiiGridView.update("available-users",{data: "project="+$("select#project option:selected").val()}); }',            	
		'filter' => $filtersForm,
		'columns' => array(
				array(
                    'header'=>'User Name',
                    
                    'value'=>'CHtml::encode($data->username)',                                      
                    'filter' => CHtml::dropDownList('project','',$project_list,
                    								array('prompt'=>'Please select a group',
                    										'options'=>array($cproject=>array('selected'=>true)),
                										 )
            										),
				),	
				array(
					'class'=>'booster.widgets.TbButtonColumn',
					'template'=>'{delete}',
					'buttons'=>array
                	(
                        'delete' => array
                        (
                      
                        	'url'=>'Yii::app()->createUrl("project/deleteuser",array("project"=>'.$cproject.',
                        														  "user"=>$data->id))',
                        ),
                	),

				),										
	            
	        ),
		));
	?>
</div>

<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" id="avprousers">
	<h3 style="margin-bottom:0;margin-top:15px">Available Users:</h3>
	<?php
		$this->widget('booster.widgets.TbGridView', array(
		'id'=>'available-users',
		'selectableRows' => 0, 	
		'htmlOptions'=>array('class' =>'grid-view table-curved'),	
		'dataProvider'=> User::model()->searchfree($cproject),				
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
                        	'url'=>'Yii::app()->createUrl("project/adduser",array("project"=>'.$cproject.',
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