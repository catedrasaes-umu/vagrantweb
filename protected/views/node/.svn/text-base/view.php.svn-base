<?php
/* @var $this NodeController */
/* @var $model NodeModel */

$this->breadcrumbs=array(
	'Nodes'=>array('index'),
	$model->node_name,
);

$this->menu=array(
	array('label'=>'List Nodes', 'url'=>array('index')),
	//array('label'=>'Create Node', 'url'=>array('create')),
	array('label'=>'Modify Node', 'url'=>array('update', 'id'=>$model->node_name)),
	array('label'=>'Delete Node', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->node_name),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Edit Configuration', 'url'=>array('editconfig','id'=>$model->node_name)),	
);
?>



<?php 	
	Yii::app()->clientScript->registerScript(
   'hideFlashEffect',
   //'$("#flash-messages").animate({opacity: 1.0}, 3000).fadeOut("slow");',
   '$("#flash-node-messages").fadeIn().delay(3000).fadeOut("slow")',
   
   CClientScript::POS_READY
	);
?>


<div id="flash-node-messages">
	<?php
		$flashMessages = Yii::app()->user->getFlashes();
		
		if ($flashMessages) {		
		    foreach($flashMessages as $key => $message) {
		    	debug($message.time());
		        echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
		    }
			
		    
		}
	?>
</div>

<h1>Node: <?php echo $model->node_name; ?></h1>
	
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'node_name',
		'node_address',
		'node_port',
		'node_password',
	),
)); 

echo CHtml::button('Change Password', array('submit'=>Yii::app()->createUrl("node/passwordchange",array("node" => $model->node_name)),
											'id' => 'changepassword-button',
											'style' => 'margin: 20px 0 30px 0;'));
											
											


echo CHtml::button('Vagrant Config', array('href'=>Yii::app()->createUrl("node/showconfig"),
											'onClick'=>"loadConfig('$model->node_name');",
											'id' => 'showconfig-button',
											'style' => 'margin: 20px 0 30px 0;'));
											

											

?>

<div id="highlighted-config" style="display: none;">
	
</div>

<?php

echo $this->renderPartial('_snapdialogs');
echo $this->renderPartial('_uploadboxform',array('node'=>$model->node_name));
echo $this->renderPartial('_addvmdialog',array('boxes'=>$boxesdp,'node'=>$model->node_name));


// $this->renderPartial('_viewconfig',array('model'=>$model,)); 
?>

<div id="flash-action-messages">
<?php	

// $flashMessages = Yii::app()->user->getFlashes();
// if ($flashMessages) {
    // echo '<ul class="flashes">';
    // foreach($flashMessages as $key => $message) {
        // echo '<li><div class="flash-' . $key . '">' . $message . "</div></li>\n";
    // }
    // echo '</ul>';
// }
?>
</div>

<?php

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'vms-grid',
	'dataProvider'=>$vmsdp,
	//'afterAjaxUpdate'=>'function(id, data){ pepe();   }',
	'columns'=>array(
		array(
	                'header'=>'Virtual Machine',                    
	                'type' => 'text',
	                'name'=>'name',
	         
        ),
		array(
	                'header'=>'Provider',                    
	                'type' => 'text',
	                'name'=>'provider',
	         
        ),
        array(
	                'header'=>'Status',                    
	                'type' => 'text',
	                'name'=>'status',
	         
        ),
        array(
				    'class'=>'CButtonColumn',
				    'template'=>'{run}{pause}{stop}',				    
				    'header'=>'Actions',
				    'buttons'=> array
    				(
	    				'run' => array
				        (				        
				            'label'=>'Run Virtual Machine',
				            'imageUrl'=>Yii::app()->request->baseUrl.'/images/play.png',
				            'url'=>'Yii::app()->createUrl("vm/command", array("id"=>$data["name"],
				            												  "node"=>"'.$model->node_name.'",
				            												  "command"=>"run"))',
				           	'options' => array('id'=>'run'),
				            

				        ),
				        'pause' => array
				        (				        	
				            'label'=>'Pause Virtual Machine',
				            'imageUrl'=>Yii::app()->request->baseUrl.'/images/pause.png',
				            'url'=>'Yii::app()->createUrl("vm/command", array("id"=>$data["name"],
				            												  "node"=>"'.$model->node_name.'",
				            												  "command"=>"pause"))',
						    'options' => array('id'=>'pause'),						    
				            
				        ),
				        'stop' => array
				        (
				            'label'=>'Stop Virtual Machine',
				            'imageUrl'=>Yii::app()->request->baseUrl.'/images/stop.png',
				            'url'=>'Yii::app()->createUrl("vm/command", array("id"=>$data["name"],
				            												  "node"=>"'.$model->node_name.'",				            												  
				            												  "command"=>"halt"))',
						    'options' => array('id'=>'stop'),
				            
				        ),
    				),
			),
	    array(
				    'class'=>'CButtonColumn',
				    'template'=>'{snapshot}{snapshot_list}{backup}',				    
				    'header'=>'Backup',
				    'buttons'=> array
    				(
    					'snapshot' => array
				        (				        
				            'label'=>'Take Snapshot',
				            'imageUrl'=>Yii::app()->request->baseUrl.'/images/snapshot.png',
				            'url'=>'Yii::app()->createUrl("vm/takesnapshot", array("id"=>$data["name"],
				            												  "node"=>"'.$model->node_name.'"))',
				           	'options' => array('id'=>'snapshot'),				            
			            	'click'	  =>'js:function(){			            					
			            					$.ajax({
								        		type: "GET",								        		
								        		url: $(this).attr("href"),								        		        		
								        		success:function(data) {													   												   
													   $("#snapshot-take-dialog div.snapshot-data").html(data);    						
								                       $("#snapshot-take-dialog").dialog("open");
								                },
								                error:function(x, t, m) {								                			
								                		$("#flash-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(5000).fadeOut("slow");								                	
								                }
								            });
				            				return false;
			            				}',

				        ),
				        'snapshot_list' => array
				        (				        	
				            'label'=>'Show Snapshot List',
				            'imageUrl'=>Yii::app()->request->baseUrl.'/images/backup_manager.png',
				            'url'=>'Yii::app()->createUrl("vm/snapshotlist", array("id"=>$data["name"],
				            												  "node"=>"'.$model->node_name.'",
				            												  ))',
						    'options' => array('id'=>'snapshot_list'),
				            'click'=>'js:function(){				            							            			
				            			$.ajax({
								        		type: "GET",								        		
								        		url: $(this).attr("href"),								        		        		
								        		success:function(data) {													   												   
													   $("#snapshot-list-dialog div.snapshot-list").html(data);    						
								                       $("#snapshot-list-dialog").dialog("open");
													   
								                },
								                error:function(x, t, m) {								                			
								                		$("#flash-node-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(3000).fadeOut("slow");																					                	
								                }
								            });
				            			return false;
									  }',
				        ),
				        'backup' => array
				        (
				            'label'=>'Take Backup',
				            'imageUrl'=>Yii::app()->request->baseUrl.'/images/backups.png',
				            'url'=>'Yii::app()->createUrl("vm/command", array("id"=>$data["name"],
				            												  "node"=>"'.$model->node_name.'",				            												  
				            												  "command"=>"halt"))',
						    'options' => array('id'=>'backup'),				            
				        ),
					)
		),
		array(
				    'class'=>'CButtonColumn',
				    'header' => 'Options',
				    // 'template' => '{view}{destroy}{delete}',
				    'template' => '{destroy}{delete}',
				  	'buttons'=> array
    				(
    					'destroy' => array
				        (				        
				            'label'=>'Destroy current machine state',
				            'imageUrl'=>Yii::app()->request->baseUrl.'/images/RecycleBin.png',
				            'url'=>'Yii::app()->createUrl("vm/destroy", array("id" => $data["name"],"node"=>"'.$model->node_name.'"))',
				            'confirm'=>'Are you sure you want to delete this item?',
				            'click'=>'js:function(){				            						
				            			if(!confirm("Are you sure you want to destroy the current machine state?")) return false;	            							            			
				            			$.ajax({				            					
								        		type: "GET",								        		
								        		url: $(this).attr("href"),								        		        		
								        		success:function(data) {													   												   
													   $.fn.yiiGridView.update("vms-grid",{data: "ajaxUpdateRequest=true",});
								                },
								                error:function(x, t, m) {								                			
								                		$("#flash-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(3000).fadeOut("slow");								                	
								                }
								            });
				            			return false;
									  }',
							  				           	

				        ),
				        // 'aaa' => array
				        // (				        
				            // 'label'=>'Delete vm from node',
				            // // 'imageUrl'=>Yii::app()->request->baseUrl.'/images/recicler.png',
				            // 'url'=>'Yii::app()->createUrl("vm/delete", array("id" => $data["name"],"node"=>"'.$model->node_name.'"))',				           	
							// 'click'=>'js:function(){				            						
				            			// if(!confirm("Are you sure you want to destroy the currentaa machine state?")) return false;	            							            			
				            			// $.ajax({				            					
								        		// type: "GET",								        		
								        		// url: $(this).attr("href"),								        		        		
								        		// success:function(data) {													   												   
													   // $.fn.yiiGridView.update("vms-grid",{data: "ajaxUpdateRequest=true",});
								                // },
								                // error:function(x, t, m) {								                			
								                		// $("#flash-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(3000).fadeOut("slow");								                	
								                // }
								            // });
				            			// return false;
									  // }',
				        // ),
				        
					),
    				  
				    // 'viewButtonUrl'=>'Yii::app()->createUrl("vm/view", array("id" => $data["name"],"node"=>"'.$model->node_name.'"))',
			    	'deleteButtonUrl'=>'Yii::app()->createUrl("vm/delete", array("id" => $data["name"],"node"=>"'.$model->node_name.'"))',
			    	'deleteConfirmation' => 'Are you sure you want to remove the virtual machine from the node and all its data?',				    					
					//'updateButtonUrl'=>'Yii::app()->createUrl("/controllername/update", array("id" => $data->[\'model_id\']))',
	    ),
	    
	),
));

echo CHtml::button('Add Virtual Machine', array('onClick'=>'js:$("#addvm-dialog1").dialog(addvm_dialog_options);','id' => 'addvm-btn'));

echo "<div style='height:15px'></div>";



$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'boxes-grid',
	'dataProvider'=>$boxesdp,
	//'afterAjaxUpdate'=>'function(id, data){ pepe();   }',
	'columns'=>array(
		array(
	                'header'=>'Box Name',                    
	                'type' => 'text',
	                'name'=>'name',
	                
	         
        ),
		array(
	                'header'=>'Provider',                    
	                'type' => 'text',
	                'name'=>'provider',
	         
        ),         
		array(
				    'class'=>'CButtonColumn',
				    'template' => '{delete}',
				    'header' => 'Actions',
				    //'viewButtonUrl'=>'Yii::app()->createUrl("vm/view", array("id" => $data["name"],"node"=>"'.$model->node_name.'"))',
			    	'deleteButtonUrl'=>'Yii::app()->createUrl("box/delete", array("id" => $data["name"],"provider"=>$data["provider"],"node"=>"'.$model->node_name.'"))',				    					
					//'updateButtonUrl'=>'Yii::app()->createUrl("/controllername/update", array("id" => $data->[\'model_id\']))',
	    ),
	),
));



	
echo CHtml::button('Upload Box', array('onClick'=>'js:$("#box-upload-dialog").dialog("open");','id' => 'box-upload-btn'));




// $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    // 'id'=>'box-upload-dialog',
    // 'options'=>array(
        // 'title'=>'Box Uploading Options',
        // 'width'=>'auto',
        // 'height'=>'auto',         
        // 'autoOpen'=>false,
    // ),
// ));



?>

<?php 
// $form=$this->beginWidget('CActiveForm', array(
	// 'id'=>'upload-box-form',
	// 'enableAjaxValidation'=>false,
// )); 
?>

<!-- <div class="upload-box-method">
	<div class="row">	 -->
	<?php	
			// echo CHtml::radioButton('upload-option', 
									// true, 
									// array('value'=>'1',
										// 'id'=>'remote-http',
										// 'uncheckValue'=>null,
										// 'onclick'=>'remotehttpOption()',
										// 'style'=>'vertical-align: middle'));
			// echo CHtml::label('Remote http location', 'remote-http',array('style'=>'vertical-align: middle')); 
			?>			
	<!-- </div></br>
	<div class="row"> -->
			<?php 
					// echo CHtml::textField('remote-url-tf','',array('size'=>60,'maxlength'=>128)); 
					?>
	<!-- </div></br>	
	<div class="row">	 -->
	<?php	
			// echo CHtml::radioButton('upload-option', 
									// false, 
									// array('value'=>'2',
										// 'id'=>'other',
										// 'uncheckValue'=>null,
										// 'onclick'=>'otherOption()',
										// 'style'=>'vertical-align: middle'));
			// echo CHtml::label('Other', 'other',array('style'=>'vertical-align: middle')); 
			?>			
	<!-- </div></br>
	<div class="row"> -->
			<?php 
					// echo CHtml::textField('othertf','',array('size'=>60,'maxlength'=>128,'disabled'=>'true')); 
					?>
	<!-- </div></br>
	<div class="row">	 -->
	<?php	
			// echo CHtml::radioButton('upload-option', 
									// false, 
									// array('value'=>'3',
										// 'id'=>'other1',
										// 'uncheckValue'=>null,
										// 'onclick'=>'other1Option()',
										// 'style'=>'vertical-align: middle'));
			// echo CHtml::label('Other1', 'other1',array('style'=>'vertical-align: middle')); 
			?>			
	<!-- </div></br>
	<div class="row"> -->
			<?php 
				// $this->widget('CMultiFileUpload',array(
					// 'name'=>'files',
					// 'accept'=>'zip|tar|gzip',
					// 'max'=>1,
					// 'remove'=>Yii::t('ui','Remove'),
					// 'denied'=>'Incorrect file type',
					// 'id'=>'upload-box-browser',					
					// //'duplicate'=>'', message that is displayed when a file appears twice
					// 'htmlOptions'=>array('size'=>128,
										// 'disabled'=>'true'),
					// 'options'=>array(						
						// 'onFileRemove'=>'function(e, v, m){ alert("onFileRemove - "+v) }',
						// 'onFileAppend'=>'function(e, v, m){ alert("onFileAppend - "+v) }',
// 						
					// ),
				// )); 

			?>
	<!-- </div></br>
	<div class="row"> -->
			<?php
	
				// echo CHtml::submitButton('Upload'); 	
				// //echo CHtml::button('Upload', array('onClick'=>'js:alert("TODO");','id' => 'upload-box-btn'));
			?>
	<!-- </div>
		
</div> -->
<?php 
		// $this->endWidget(); 
		?>
 
<?php 
		// $this->endWidget('zii.widgets.jui.CJuiDialog'); 
		?>



<script>
	// //TODO FALTA VER EL TEMA DEL COMPONENTE UPLOAD YA QUE HACE UNA COMPORTAMIENTO RARO
	// //CUANDO ELIMINAS UN FICHERO SELECCIONADO Y SU OPCION N OESTA ACTIVA
	// function remotehttpOption()
	// {
// 	
		// $("#remote-url-tf").attr('disabled',false);
		// $("#othertf").attr('disabled',true);
		// $("#upload-box-browser").attr('disabled',true);
		// $("#upload-box-browser_wrap_list a").attr('disabled',true);
// 		
	// }
// 	
	// function otherOption()
	// {
// 	
		// $("#remote-url-tf").attr('disabled',true);		
		// $("#othertf").attr('disabled',false);
		// $("#upload-box-browser").attr('disabled',true);
		// $("#upload-box-browser_wrap_list a").attr('disabled',true);
	// }
// 	
	// function other1Option()
	// {
// 	
		// $("#remote-url-tf").attr('disabled',true);
		// $("#othertf").attr('disabled',true);
		// $("#upload-box-browser").attr('disabled',false);
		// $("#upload-box-browser_wrap_list a").attr('disabled',false);
// 		
	// }
	
	function loadConfig(node)
	{
		
		
		
		$.ajax({
    		type: "GET",
    		data: {node: node},								        		
    		url: $("#showconfig-button").attr("href"),								        		        		
    		success:function(data) {						
    			       			   							   												   
				   $("#highlighted-config").html(data);
				   $("#vagrant-config-dialog").dialog("open");    						
                   
            },
            error:function(x, t, m) {								                			
            		$("#flash-node-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(5000).fadeOut("slow");								                	
            }
        });
		return false;
	}
	
	

jQuery(function($) {
jQuery('#run, #pause, #stop').live('click',function() {
		// alert("POR AKI");
        // $("#node-status-loading-dialog").dialog({title: "Performing Operation"});
        // $("#node-status-loading-dialog").dialog("open");
        $.ajax({
        		type: 'GET',
        		timeout:0, //No timeout
        		url: $(this).attr("href"),        		
        		success:function(response) {
					           						
                       // $.fn.yiiGridView.update('vms-grid',{
                       		// data: "ajaxUpdateRequest=true",
                       // });
                       
                       $("#flash-action-messages").addClass("flash-success").html(jQuery.parseJSON(response)["status"]).fadeIn().delay(3000).fadeOut("slow");
                       
                },
                error:function(x, t, m) {    	
                	//$("#node-status-loading-dialog").dialog("close");                	
                	if (t=="timeout")
                	{                		
                		$("#flash-action-messages").addClass("flash-error").html("Request Timeout Error").fadeIn().delay(3000).fadeOut("slow");
                	}else{                		                		
                		$("#flash-action-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(3000).fadeOut("slow");
                	}
                },
                complete: function() { 
                	//$("#node-status-loading-dialog").dialog("close"); 
                },
            })
        
        return false;
});
});


$( document ).ready(function() {
	
	$('#operation_log').show();
	$(document).on("updateAsync",function(event,params){
		if (params.operation=="VM_STATUS")
		{		
			$.fn.yiiGridView.update('vms-grid',{
       		data: "node="+params.node,
       		});
		}else if (params.operation=="BOX_STATUS")
		{
			$.fn.yiiGridView.update('boxes-grid');
		}
	
	});
	
		
	
	
	
	 
});
	
	
</script>






