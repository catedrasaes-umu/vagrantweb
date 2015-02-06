<?php
/* @var $this NodeController */
/* @var $dataProvider CActiveDataProvider */

//set_time_limit(300);
//echo ini_get('max_execution_time');



$this->breadcrumbs=array(
	'List of Nodes',
);

$this->menu=array(
	array('label'=>'Create Node', 'url'=>array('create')),
	array('label'=>'Manage Nodes', 'url'=>array('admin')),
	array('label'=>'Manage Groups', 'url'=>array('project/index')),
	
);

echo $this->renderPartial('_snapdialogs');


?>

<!--Esto hace que el grid se cargue cuando la página se cargue del todo-->
<script type="text/javascript">	
	onload = function() { 
			//lazyLoadGridView();			
			reload_page();
		}


	//Setting global variable
	var selectedrows = undefined;

</script>


<h1 class="page-header">List of Nodes:</h1>



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
	    	// debug($message.time());
	        echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
	    }
		
	    
	}
?>

</div>

<div class="col-lg-8 col-md-12 col-sm-12 col-xs-12" id="node-header" style="padding-left:0">

<div class="panel panel-primary">
        <div class="panel-heading">
            <i class="fa fa-table fa-fw"></i> Global View
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">



<?php
$dashboard=$this->widget('ext.groupgridview.GroupGridView', array(
			'id'=>'node-model-grid',
            'dataProvider'=>$dataProvider,            
            'filter'=>$filtersForm,
            'ajaxVar' => 'ajaxFiltering',            
            'mergeColumns' => array('node_name'),
            'template'=> Yii::app()->request->getIsAjaxRequest()? '{summary}{items}{pager}': '{summary}{pager}',
            'beforeAjaxUpdate'=>'function(id, data){
            	$("#node-status-loading-dialog").dialog("close");             	
            	selectedrows=getSelectedRows();
            }',                        
            'afterAjaxUpdate'=>'function(id, data){ 
            	$("#node-status-loading-dialog").dialog("close"); 
    
             	$.each(selectedrows, function( i, value ){
             		aux=value["node"]+"_"+value["id"];
					$("td.checkbox-column > input[value=\'"+aux+"\']").click();
				});
				
            }',            
            //'ajaxUpdateError'=>'function(id, data){ $("#node-status-loading-dialog").dialog("close");   }',
            'ajaxUpdate'=>'node-model-grid',
            //Añadiendo clase extra a las de even y odd
            'rowCssClassExpression' => '( $row%2 ? $this->rowCssClass[1] : $this->rowCssClass[0] ) .
            							( ($data["busy"]==true) ? " busy" : null ) .
            							( $data["vm_name"] ? null : " default" ) .
								        ( $data["node_status"] ? null : " offline" ) 								        
								    ',			
            'selectionChanged' => 'function(row){  rowSelected(row);   }',
            'selectableRows' => 2,
            'summaryText' => 'Displaying {start} - {end} of {count} nodes',                        
            'columns'=>array(
            	
                array(
                    'header'=>'Node Name',
                    'name'=>'node_name',
                    'type'	=> 'raw',
                    'value'=>'CHtml::link($data["node_name"], array("node/view","id"=>$data["node_name"]))',                    
                    'cssClassExpression' => '$data["node_status"]? "online" : "offline"',                    
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
				    'class'=>'CButtonColumn',
				    'template'=>'{run}{pause}{stop}',
				    'cssClassExpression' => '($data["busy"]==true)?"busy":(($data["node_status"])? "" : "offline")',
				    'header'=>'Actions',
				    'buttons'=> array
    				(
	    				'run' => array
				        (				        
				            'label'=>'Run Virtual Machine',
				            'imageUrl'=>Yii::app()->request->baseUrl.'/images/play.png',	
				            'visible' => '$data["node_status"] && $data["vm_name"]!=""',			            
				            'url'=>'Yii::app()->createUrl("vm/command", array("id"=>$data["vm_name"],
				            												  "node"=>$data["node_name"],
				            												  "command"=>"run"))',
				           	'options' => array('id'=>'run'),
				            //'visible' => '$data["node_status"] && $data["vm_name"]!=""',

				        ),				        
				        'pause' => array
				        (				        	
				            'label'=>'Pause Virtual Machine',
				            'visible' => '$data["node_status"] && $data["vm_name"]!=""',
				            'imageUrl'=>Yii::app()->request->baseUrl.'/images/pause.png',
				            'url'=>'Yii::app()->createUrl("vm/command", array("id"=>$data["vm_name"],
				            												  "node"=>$data["node_name"],
				            												  "command"=>"pause"))',
						    'options' => array('id'=>'pause'),						    
				            //'visible' => '$data["node_status"] && $data["vm_name"]!=""',
				        ),
				        'stop' => array
				        (
				            'label'=>'Stop Virtual Machine',
				            'imageUrl'=>Yii::app()->request->baseUrl.'/images/stop.png',
				            'visible' => '$data["node_status"] && $data["vm_name"]!=""',
				            'url'=>'Yii::app()->createUrl("vm/command", array("id"=>$data["vm_name"],
				            												  "node"=>$data["node_name"],				            												  
				            												  "command"=>"halt"))',
						    'options' => array('id'=>'stop'),
				            //'visible' => '$data["node_status"] && $data["vm_name"]!=""',
				            //'visible' => array($this,'isActionable'),
				        ),
    				),
				),
				array(
				    'class'=>'CButtonColumn',
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
				            'visible' => '$data["node_status"] && $data["vm_name"]!=""',
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
				            'url'=>'Yii::app()->createUrl("vm/snapshotlist", array("id"=>$data["vm_name"],
				            												  "node"=>$data["node_name"],
				            												  ))',
						    'options' => array('id'=>'snapshot_list'),						    
				            'visible' => '$data["node_status"] && $data["vm_name"]!=""',
				            'click'=>'js:function(){				            							            			
				            			$.ajax({
								        		type: "GET",								        		
								        		url: $(this).attr("href"),								        		        		
								        		success:function(data) {													   												   
													   $("#snapshot-list-dialog div.snapshot-list").html(data);    						
								                       $("#snapshot-list-dialog").dialog("open");
								                },
								                error:function(x, t, m) {								                			
								                		$("#flash-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(3000).fadeOut("slow");								                	
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
				            'visible' => '$data["node_status"] && $data["vm_name"]!=""',
				        ),
    				),
				),
                array(
            		'class'=>'CCheckBoxColumn',
            		'headerTemplate' => '{item}',
            		'cssClassExpression' => '$data["node_status"]? "" : "offline"',
            		//'selectableRows' => 2,          
            		'id' => 'node-check',  		
            		//'cssClassExpression'=> '$data["node_status"]? "" : "hidden"',
            		'disabled' =>  '$data["busy"] || !$data["node_status"] || $data["vm_name"]==""',
            		'value' => '$data["node_name"]."_".$data["vm_name"]',
            		


				),                                                   
				
            ),
        )); 

?>

<div class="buttons">
	

<?php

$this->widget('booster.widgets.TbButton',
				array('buttonType' => 'link', 
					'label' => 'Run',
					'url' => Yii::app()->createUrl("vm/command", array("command"=>"run")),
					'htmlOptions' => array('id' => 'run-button','disabled'=>true,'style'=>'margin-right:20px')));

$this->widget('booster.widgets.TbButton',
				array('buttonType' => 'link', 
					'label' => 'Pause',
					'url' => Yii::app()->createUrl("vm/command", array("command"=>"pause")),
					'htmlOptions' => array('id' => 'pause-button','disabled'=>true,'style'=>'margin-right:20px')));

$this->widget('booster.widgets.TbButton',
				array('buttonType' => 'link', 
					'label' => 'Stop',
					'url' => Yii::app()->createUrl("vm/command", array("command"=>"halt")),
					'htmlOptions' => array('id' => 'stop-button','disabled'=>true,)));

?>
</div>
</div>
</div>
</div>

 <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12" id="notificationarea">
    <div class="panel panel-primary" id="alertas">
        <div class="panel-heading">
            <i class="fa fa-bell fa-fw"></i> Operation Notification Panel
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">
            <div class="list-group">            	
            </div>
            <!-- /.list-group -->
            <a href="<?=Yii::app()->createUrl("node/operations")?>" class="btn btn-default btn-block">View All Operations</a>
        </div>
        <!-- /.panel-body -->
    </div>

</div>


<div class="clear"></div>

<script type="text/javascript">

    function lazyLoadGridView(){   
    	 
    	 
    	 var nodes = <?php echo json_encode($nodes); ?>;
    	 
    	 
    	 
    	 
    	 //Filling the grid dynamicly    	 
    	 $.each( nodes, function( key, value ) {    	 	  			    	 	
  			$.fn.yiiGridView.update('node-model-grid',{
       		data: "node="+value,
       		});       		
    	 	
		 });
    	 
               
	}

function commandButtonStatus(value)
{
	$("#run-button").attr('disabled',value);
	$("#pause-button").attr('disabled',value);
	$("#stop-button").attr('disabled',value);
	$("#snapshot-button").attr('disabled',value);
}

function rowSelected(row) {
	   
	   //Little hack to avoid selection in offline or busy Nodes
	   $("tr.offline").removeClass("selected");
	   $("tr.offline .select-on-check").prop('checked',false);

	   $("tr.busy").removeClass("selected");
	   $("tr.busy .select-on-check").prop('checked',false);

	   $("tr.default").removeClass("selected");
	   $("tr.default .select-on-check").prop('checked',false);
	      
	   
	   
	   //Enable or disable batch buttons       
       var value=($.fn.yiiGridView.getChecked("node-model-grid","node-check").length>0) ? false:true;
       
       commandButtonStatus(value);
       
}

function loadNotifications()
{		

		$.ajax({
            type: "GET",                                                
            url: "/vagrantweb/index.php?r=operation/last&last=7",                                                              
            success:function(data) { 
            	
            	var todas=$.parseHTML(data);

        		
            	
            	var indices = [];

            	$("div#alertas div.list-group a.list-group-item div.notificacionmsg:visible").each(function( index ) {				  
				  indices.push($(this).parent().attr('id'))
				});

    
            	$("div#alertas div.list-group").empty();

				var arrayLength = todas.length;
				for (var i = 0; i < arrayLength; i++) {
					var id = $(todas[i]).attr('id');					

					if ($.inArray(id,indices)!=-1){				
						$(todas[i]).children("div.notificacionmsg").show();
					}
					
					$("div#alertas div.list-group").append($(todas[i]));
				}


				

 //               $("div#alertas div.list-group").html(data);
            },
            error:function(x, t, m) {                            		
                    $("#flash-messages").addClass("flash-error").html("Error retrieving operations");                                                 
            }
        });	
}



function addNotification()
{
	
	$.ajax({
            type: "GET",                                                
            url: "/vagrantweb/index.php?r=operation/last&limit=1",                                                              
            success:function(data) { 
            	
    			var max_alertas=7;
				var cantidad = $("div#alertas div.list-group a.list-group-item").length;

				if (cantidad>=max_alertas){
					$("div#alertas div.list-group a.list-group-item").last().remove();
				}



				$("div#alertas div.list-group").prepend(data);
				// $("div#alertas div.list-group").append(data);
				//Añadir la última notificación

            },
            error:function(x, t, m) {                
                    $("#flash-messages").addClass("flash-error").html("Error retrieving notifications");                                                 
            }
        });	


}

$(document).on("click", "div#alertas div.list-group a.list-group-item", function(event){		
	
	$(this).children("div.notificacionmsg").toggle("600");
	return false;
});

function getSelectedRows(){

	var vms = [];
	//var checkboxCount=$("#node-model-grid").yiiGridView("getChecked","vm_name");
	var checkboxCount = $.fn.yiiGridView.getChecked("node-model-grid","node-check");
	
	$.each(checkboxCount, function(key,value) {		
		var row = $("#node-model-grid").yiiGridView("getRow",value);
		
		$.each(row,function(){		
				
			if ($(this).hasClass("vm_name"))
			{
				var ref=$(this).children().attr("href");
				//var searchString = ref
			    var params = ref.split("&")
			    , hash = {};
				
				//Saving the row of the machine for future interactions
				hash["row"] = value;
				
				for (var i = 1; i < params.length; i++) {
				    var val = params[i].split("=");				
				    hash[unescape(val[0])] = unescape(val[1]);
			    }
			    
				vms.push(hash);
			}			
		});		
		
	});
	return vms;
}

//Función para ejecutar operaciones batch, recorre la lista de vm checkeadas,
//obtiene ciertos parámetros y los pasa al controlador
jQuery(	function($) {
jQuery('#run-button, #pause-button, #stop-button, #snapshot-button').on('click',function() {
	

	
	var vms=[];

	vms=getSelectedRows();

	if (vms.length === 0)
		return false;
	
	
	var command =  $(this).attr("href");
	
	var boton = $(this);
	
	
	
	$.each( vms, function( key, value ) {
	
		$.ajax({
	        		type: 'GET',
	        		
	        		timeout:0, //No timeout
	        		url: command,
	        		//async:false,
	        		// data: {batch_objects:vms},
	        		data:{node:value["node"],id:value["id"]},
	        		        		
	        		success:function(data) {
	        				var msg = jQuery.parseJSON(data)	        		 				   												   						

        					addNotification();

	        				
	        					
	        				
	                       
	                       
	                },
	                error:function(x, t, m) {
	                	
	                	if (t=="timeout")
	                	{                		
	                		$("#flash-messages").addClass("flash-error").html("Request Timeout Error").fadeIn().delay(3000).fadeOut("slow");
	                	}else{
	                		if (x.responseText.length!=0)
	                		{	                			
	                			
	                			$("#flash-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(5000).fadeOut("slow");
	                		}
	                	}
	                }
	
            })
			
	});

	boton.css("background-color", "white");
	
	return false;
	
});
});	

$(document).on("click", "#run, #pause, #stop", function(){		



     	
     	bobject = $(this);
     	tdsiblings = $(this).siblings();
     	trparent = $(this).parents("tr");
     	tdobject = trparent.find("td.vm_status");
     	otherobject = $(this).parents("td").next("td.button-column").find("a");
     	check = $(this).parents("td").nextAll("td.checkbox-column").find("input")
     	

        $.ajax({
        		type: 'GET',
        		timeout:0, //No timeout
        		url: $(this).attr("href"),        		
        		success:function(data) {
        			  //console.log(data);
					$("#node-status-loading-dialog").dialog("close");
					var msg = jQuery.parseJSON(data)

					trparent.addClass("busy");
					check.prop("disabled",true);
					otherobject.hide();
					bobject.hide();
					tdsiblings.hide();
					tdobject.text(msg.status);

					
					$("#flash-messages").addClass("flash-success").html("Operation Queued").fadeIn().delay(3000).fadeOut("slow");
					       						
					
                       
                },
                error:function(x, t, m) {    	
                	$("#node-status-loading-dialog").dialog("close");                	
                	if (t=="timeout")
                	{                		
                		$("#flash-messages").addClass("flash-error").html("Request Timeout Error").fadeIn().delay(3000).fadeOut("slow");
                	}else{
                		if (x.responseText.length!=0)
                		{                	
                			$("#flash-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(5000).fadeOut("slow");
                		}
                	}
                }
            })
        
        return false;
//});
});

function reload_page(){
	lazyLoadGridView();
	
	// setTimeout( reload_page, <?php echo $refresh_time*1000?> );	
	//FIXME PARA HACER PRUEBAS ESTABLEZCO UN TIEMPO MENOR
	setTimeout( reload_page, 30000 );	
}

$( document ).ready(function() {
	

	loadNotifications();
	
	$(document).on("updateAsync",function(event,params){
		
		if (params.operation=="VM_STATUS")
		{	
			
			$.fn.yiiGridView.update('node-model-grid',{
       		data: "node="+params.node,
       		});
       		loadNotifications();
		}
		
	});

	$(document).on("operationPolling",function(event,params){
		
       	loadNotifications();
		
		
	});
	
		
	
	
	
	 
});




</script>



