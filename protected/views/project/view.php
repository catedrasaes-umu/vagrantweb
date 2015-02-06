<style>
	/* Ocultado el gif de loading para que no se haga muy pesado */
	.grid-view-loading {
		background:none !important;
	}
</style>

<?php	


$this->breadcrumbs=array(
	'Groups'=>array('index'),
	$model->project_name,
);


?>

<script>
	//Setting global variable
	//window.globalVar = undefined;
	var selectedrows = undefined;
</script>



<h1 class="page-header">Group <?php echo $model->project_name; ?></h1>


<?php 	
	Yii::app()->clientScript->registerScript(
   'hideFlashEffect',   
   '$("#flash-messages").fadeIn().delay(3000).fadeOut("slow")',
   
   CClientScript::POS_READY
	);?>


<div id="flash-messages">	
<?php
	
	$flashMessages = Yii::app()->user->getFlashes();
	
	if ($flashMessages) {		
	    foreach($flashMessages as $key => $message) {
	        echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
	    }
		
	    
	}
?>

</div>

<?php 


	



$this->widget('booster.widgets.TbGridView', array(
			'id'=>'node-model-grid',
            'dataProvider'=>$dataProvider,
            'filter'=>$filtersForm,
            'ajaxVar' => 'ajaxFiltering',     
            'htmlOptions'=>array('class' =>'grid-view table-curved'),       
            'beforeAjaxUpdate' => 'js:function(id) {             							
            							selectedrows = $.fn.yiiGridView.getSelection("node-model-grid");
            						}',
     		'afterAjaxUpdate' => 'js:function(id, data) {      									
										
										var arrayLength = selectedrows.length;
										for (var i = 0; i < arrayLength; i++) {										
										    $("input#node-check_"+selectedrows[i]).click();
										}

     									
     								}',
            
            'selectionChanged' => 'function(row){ rowSelected(row)}',
            'ajaxUpdate'=>'pending-operations',
            //Añadiendo clase extra a las de even y odd
            'rowCssClassExpression' => '( $row%2 ? $this->rowCssClass[1] : $this->rowCssClass[0] ) .
            							( ($data["busy"]==true) ? " busy" : null ) .
								        ( $data["node_status"] ? null : " offline" )
								    ',		
            
            'selectableRows' => 2,
            'summaryText' => 'Displaying {start} - {end} of {count} machines',              
         
            'columns'=>array(
            	
                array(
                    'header'=>'Node Name',
                    'name'=>'node_name',
                    'type'	=> 'raw',
                    'value'=>'CHtml::link($data["node_name"], array("node/view","id"=>$data["node_name"]))',                    
                    'cssClassExpression' => '$data["node_status"]? "online node" : "offline node"',
                    
					),
                array(
                    'header'=>'Virtual Machine',
                    'type'	=> 'raw',
                    'value'=>'CHtml::link($data["vm_name"], array("vm/view","id"=>$data["vm_name"],"node"=>$data["node_name"]))',
                    'name' => 'vm_name',
                    'cssClassExpression' => '$data["node_status"]? "vm_name" : "offline"',                    
                    ),
                array(
	                'header'=>'Provider',                    
	                'type' => 'raw',
	                'name'=>'provider',	                 
	                'cssClassExpression' => '$data["node_status"]? "vm_provider" : "offline"',
            	),                   
                array(
                    'header'=>'Status',
                    'name'=>'status',                                       
                    'cssClassExpression' => '$data["node_status"]? "vm_status" : "offline"',
                ),
                array(
                    'header'=>'Priority',
                    'name'=>'spriority',                                       
                    'cssClassExpression' => '$data["node_status"]? "vm_priority" : "offline"',
                ),                 
    
				array(
				    'class'=>'booster.widgets.TbButtonColumn',
				    'template'=>'{snapshot}{snapshot_list}{backup}',				    
				    'cssClassExpression' => '($data["busy"]==true)?"busy":(($data["node_status"])? "" : "offline")',
				    'header'=>'Backup',
				    'buttons'=> array
    				(
	    				'snapshot' => array
				        (				        
				            'label'=>'Take Snapshot',
				            'imageUrl'=>Yii::app()->request->baseUrl.'/images/snapshot.png',
				            'url'=>'Yii::app()->createUrl("vm/takesnapshot", array("id"=>$data["vm_name"],
				            												  "node"=>$data["node_name"]))',
				           	'options' => array('id'=>'snapshot'),
				            'visible' => '$data["node_status"]',
			            	'click'	  =>'js:function(){			            					
			            					$.ajax({
								        		type: "GET",								        		
								        		url: $(this).attr("href"),								        		        		
								        		success:function(data) {													   												   
													   $("#snapshot-take-dialog div.snapshot-data").html(data);    						
								                       $("#snapshot-take-dialog").dialog("open");
								                },
								                error:function(x, t, m) {								                			
								                		$("#flash-messages").addClass("flash-error").html(x.responseText);								                	
								                }
								            });
				            				return false;
			            				}',

				        ),
				        'snapshot_list' => array
				        (				        	
				            'label'=>'Show Snapshot List',
				            'imageUrl'=>Yii::app()->request->baseUrl.'/images/backup_manager.png',
				            'url'=>'Yii::app()->createUrl("vm/snapshotlist", array("id"=>$data["vm_name"],
				            												  "node"=>$data["node_name"],
				            												  ))',
						    'options' => array('id'=>'snapshot_list'),						    
				            'visible' => '$data["node_status"]',
				            'click'=>'js:function(){				            							            			
				            			$.ajax({
								        		type: "GET",								        		
								        		url: $(this).attr("href"),								        		        		
								        		success:function(data) {													   												   
													   $("#snapshot-list-dialog div.snapshot-list").html(data);    						
								                       $("#snapshot-list-dialog").dialog("open");
								                },
								                error:function(x, t, m) {								                			
								                		$("#flash-messages").addClass("flash-error").html(x.responseText);								                	
								                }
								            });
				            			return false;
									  }',
				        ),
				        'backup' => array
				        (
				            'label'=>'Take Backup',
				            'imageUrl'=>Yii::app()->request->baseUrl.'/images/backups.png',
				            'url'=>'Yii::app()->createUrl("vm/command", array("id"=>$data["vm_name"],
				            												  "node"=>$data["node_name"],				            												  
				            												  "command"=>"halt"))',
						    'options' => array('id'=>'backup'),
				            'visible' => '$data["node_status"]',
				        ),
    				),
				),
				
                array(
            		'class'=>'CCheckBoxColumn',
            		'headerTemplate' => '{item}',
            		'cssClassExpression' => '$data["node_status"]? "" : "offline"',            		
            		'id' => 'node-check',  		            		            		
            		'disabled' =>  '$data["busy"] || !$data["node_status"]',             		
            		
				),                                                   
				
            ),
        )); 
	
	
	
	
	
	
?>

<div style="margin-top:20px;margin-bottom:20px">

<?php

$this->widget('booster.widgets.TbButton',
				array('buttonType' => 'link', 
					'label' => 'Run',
					'url' => Yii::app()->createUrl("project/batchrun", array("command"=>"run")),
					'htmlOptions' => array('id' => 'run-button','disabled'=>true,'style'=>'margin-right:20px')));

$this->widget('booster.widgets.TbButton',
				array('buttonType' => 'link', 
					'label' => 'Pause',
					'url' => Yii::app()->createUrl("project/batchpause", array("command"=>"pause")),
					'htmlOptions' => array('id' => 'pause-button','disabled'=>true,'style'=>'margin-right:20px')));

$this->widget('booster.widgets.TbButton',
				array('buttonType' => 'link', 
					'label' => 'Stop',
					'url' => Yii::app()->createUrl("project/batchstop", array("command"=>"halt")),
					'htmlOptions' => array('id' => 'stop-button','disabled'=>true,)));




?>

</div>



<?php $this->widget('booster.widgets.TbGridView', array(
	'id'=>'pending-operations',
	'dataProvider'=>$pending_operations,
	'htmlOptions'=>array('class' =>'grid-view table-curved grid-primary'),       		
	'rowCssClassExpression' => '($data["status"]==-1)? "oppending" : (($data["status"]==100)?"opcurrent": (($data["status"]==200)?"opok": "operror"))',
	'columns'=>array(
		// 'id',
		// 'pnm_id',		
		array(
			'header'=> 'User',
			'name'=>'username',
		),	
		array(
			'header'=> 'Timestamp',
			'name'=>'operation_timestamp',
		),
		array(
			'name'=> 'Node',
			'value'=>array($this,'getRelatedNode'), 
		),
		array(
			'name'=> 'Virtual Machine',
			'value'=>array($this,'getRelatedMachine'), 	
		),		
		array(
			'name'=> 'Priority',
			'value'=>array($this,'getRelatedMachinePriority'), 	
		),
		array(
	                'header'=> 'Command',
		        'name'=>'command',
		),		
		array(
			'header'=>'Status',
			'name'=> 'status',
			'value'=>array($this,'getStatusString'), 	
		),
		array(
	                'header'=> 'Result',
		        'name'=>'status_msg',
		),			

		array(
			'class'=>'booster.widgets.TbButtonColumn',			
		    'template'=>'{delete}',
		    'buttons'=> array
			(
				'delete' => array
		        (
					'url'=>'Yii::app()->createUrl("project/deleteoperation", array("id"=>$data["id"],))',
					'visible' => '$data["status"]!=100',
				)
			)
		),
	),
)); ?>

<div style="margin-top:20px;margin-bottom:40px">
<?php 
$this->widget('booster.widgets.TbButton',
				array('buttonType' => 'link', 
					'label' => 'Delete All Pending',
					'url' => Yii::app()->createUrl("project/deleteallpending", array("id"=>$model->id)),
					'htmlOptions' => array('id' => 'deleteallpending','style'=>'margin-right:20px')));

$this->widget('booster.widgets.TbButton',
				array('buttonType' => 'link', 
					'label' => 'Clear Completed',
					'url' => Yii::app()->createUrl("project/deletecompleted", array("id"=>$model->id)),
					'htmlOptions' => array('id' => 'deletecompleted','style'=>'margin-right:20px')));

$this->widget('booster.widgets.TbButton',
				array('buttonType' => 'link', 
					'label' => 'Force Delete All',
					'url' => Yii::app()->createUrl("project/deleteall", array("id"=>$model->id)),
					'htmlOptions' => array('id' => 'deleteall','style'=>'margin-right:20px')));


	
?>

</div>


<script type="text/javascript" charset="utf-8">
	
function rowSelected(row) {
	   
	   $("tr.busy").removeClass("selected");
	   $("tr.busy .select-on-check").prop('checked',false);

       var value=$.fn.yiiGridView.getChecked("node-model-grid","node-check").length;
       
       changeButton(value);
       
}


function reload_page(){


	
	setTimeout( reload_page, 30000 );
}

function changeButton(count)
{
	
	if (count==0){
		$('#run-button').html("Run").attr("disabled","disabled");
		$('#pause-button').html("Pause").attr("disabled","disabled");
		$('#stop-button').html("Stop").attr("disabled","disabled");
	}else{

		$('#run-button').removeAttr("disabled");
		$('#pause-button').removeAttr("disabled");
		$('#stop-button').removeAttr("disabled");

		if (count==1)
		{
			$('#run-button').html("Run");
			$('#pause-button').html("Pause");
			$('#stop-button').html("Stop");
			
		}else{
			$('#run-button').html("Run All");
			$('#pause-button').html("Pause All");
			$('#stop-button').html("Stop All");

		}
	}
}


$(document).on("click", "th#node-check.checkbox-column", function(event){
	

	if ($('div#node-model-grid table tbody tr.selected').length==$('div#node-model-grid table tbody tr').length)	
	{
		$('#run-button').html("Run All").removeAttr("disabled");
		$('#pause-button').html("Pause All").removeAttr("disabled");
		$('#stop-button').html("Stop All").removeAttr("disabled");
	}else{
		$('#run-button').html("Run").attr("disabled","disabled");
		$('#pause-button').html("Pause").attr("disabled","disabled");
		$('#stop-button').html("Stop").attr("disabled","disabled");
	}
	
	
});



$('td.checkbox-column').on('click',function(){

	changeButton($('td.checkbox-column > input:checked').length);
	
});


$( document ).ready(function() {
	
	//setTimeout( reload_page, 30000 );	
	 
});

$(document).on("operationPolling",function(event){
		
		
       	$.fn.yiiGridView.update('node-model-grid');
   		$.fn.yiiGridView.update('pending-operations');	
		
		
});
	

$('#deleteallpending').on('click',function(event) {
	

	$.ajax({
		type: 'GET',
		
		timeout:0,
		url: $(this).attr("href"),

        complete:function(){
        	
        	$.fn.yiiGridView.update('pending-operations');
		    $('#deleteallpending').css("background-color", "white");		

        }
    });
	event.preventDefault();

});

$('#deleteall').on('click',function(event) {
	

	if (confirm("Are you sure you want to remove all operations?"))
	{
    	


		$.ajax({
			type: 'GET',
			
			timeout:0,
			url: $(this).attr("href"),

	        complete:function(){
	        	
	        	$.fn.yiiGridView.update('pending-operations');
			    $('#deleteall').css("background-color", "white");	

	        }
	    });
	}
	event.preventDefault();

});


$('#deletecompleted').on('click',function(event) {
	
	
	event.preventDefault();

	$.ajax({
		type: 'GET',
		
		timeout:0,
		url: $(this).attr("href"),

        complete:function(){
        	
        	$.fn.yiiGridView.update('pending-operations');
			$('#deletecompleted').css("background-color", "white");

        }
    });


	

});

	

jQuery
(	function($) {
jQuery('#run-button, #pause-button, #stop-button, #snapshot-button').on('click',function(event) {
	
	var vms = [];
	var nodes = [];
	//var node=$("#checkboxCount-model-grid").yiiGridView("getChecked","vm_name");
	
	var boton = $(this);
	
	event.preventDefault();
	
	
	
	var command =  $(this).attr("href");
	
	
	
	var vms = [];


	
	$('tr.selected').each(function() {
		
		var node=$(this).children('td.node').text();		

		var vm_machine=$(this).children('td.vm_name').text();
		
		var priority=$(this).children('td.vm_priority').text().replace(/\s+/, "");
		
		
		if (priority==undefined || priority.length==0)
		{
			
			$("#flash-messages").addClass("flash-error").html("priority not set in Virtual Machine: "+vm_machine);
			event.preventDefault();
			return false;
		}
		
		
		if (vms[priority]==undefined){
			vms[priority]=[];
		}
		vms[priority][vms[priority].length]=[node,vm_machine];

	});
		


	$.ajax({
			type: 'POST',
			
			timeout:0, //No timeout
			url: command,
			//async:false,
			// data: {batch_objects:vms},
			data:{batch_objects:vms,project:<?php echo $model->id?>},        		        		
			success:function(data) {
					
					var msg = jQuery.parseJSON(data)
					
					

					//setTimeout( reload_page, 30000 );		        		 				   												   						
					// alert(msg["status"]);
					
					// var row = $("#node-model-grid").yiiGridView("getRow",value["row"]);
	//         				
					// $.each(row,function(){
						// if ($(this).hasClass("vm_status"))
						// {
							// $(this).text(msg.status);
						// }		
					// });
					
						
					
	               
	               
	        },
	        error:function(x, t, m) {
	        	// alert(t);
	        	//console.log(x);
	        	
	        	//$("#node-status-loading-dialog").dialog("close");                	
	        	// if (t=="timeout")
	        	// {                		
	        		// $("#flash-messages").addClass("flash-error").html("Request Timeout Error").fadeIn().delay(3000).fadeOut("slow");
	        	// }else{
	        		// if (x.responseText.length!=0)
	        		// {
	        			// //alert("FIXME: ERROR "+x.responseText);
	//                 			
	        			$("#flash-messages").addClass("flash-error").html(x.responseText);
	        		// }
	        	// }
	        },
	        complete:function(){
	        	
	        	$.fn.yiiGridView.update('pending-operations');
	        	boton.css("background-color", "white");

			        	

	        }
	    });
		

	
	//event.preventDefault();
	
});
});	
</script>
