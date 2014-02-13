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
);

echo $this->renderPartial('_snapdialogs');


?>

<!--Esto hace que el grid se cargue cuando la página se cargue del todo-->
<script type="text/javascript">	
	onload = function() { 
			lazyLoadGridView();			
		}
</script>


<h1>List of Nodes:</h1>



<?php 	
	Yii::app()->clientScript->registerScript(
   'hideFlashEffect',
   //'$("#flash-messages").animate({opacity: 1.0}, 3000).fadeOut("slow");',
   '$("#flash-messages").fadeIn().delay(3000).fadeOut("slow")',
   
   CClientScript::POS_READY
	);?>


<div id="flash-messages">	
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

<?php	
	

	// $this->widget('ext.groupgridview.GroupGridView', array(
	// 'id'=>'node-model-grid',
	// //'dataProvider'=>$model->search(),
	// 'dataProvider'=>$dataProvider,
	// 'filter'=>$dataProvider->model,
	// //'cssFile' => Yii::app()->baseUrl . '/css/gridViewStyle/gridView.css',
    // 'summaryText' => 'Displaying {start} - {end} of {count} nodes',    
    // //'pager' => array('cssFile' => Yii::app()->baseUrl . '/css/customize.css'),
	// //'htmlOptions' => array('class' => 'grid-view'),
	// 'rowHtmlOptionsExpression' =>'array("id"=>"$data->onlinestr")',
	// 'mergeColumns' => array('node_port'),  
	// 'columns'=>array(		
		// 'name' => 'node_name',
		// array(
			// 'header' =>'Boxes',
			// 'class'=>'CLinkColumn',
			// 'label' => 'Manage Boxes',
		// ),
		// array(
			// 'header' =>'Virtual Machines',
			// 'class'=>'CLinkColumn',
			// 'label' => 'Manage Virtual Machines',
			// //'url'=>'Yii::app()->createUrl("vm/list",array("id"=>$data["node_name"]))',
			// // 'options'=>array(
                                                    // // 'ajax'=>array(
                                                            // // 'type'=>'POST',
                                                            // // 'url'=>"js:$(this).attr('href')",                                                               
                                                    // // ),
                                            // // ),
		// ),
// 		
// 		
		// array(
			 // 'header' =>'Status',
			 // 'class'=>'CDataColumn',
			 // 'type' => 'text',
			 // 'value' => '$data->onlinestr',			 
			 // 'htmlOptions' => array('id' => 'node-status'),			 		 
		// ),	
// 		
		// array(
			// 'class'=>'CButtonColumn',
		// ),
	// ),
// )); 





$dashboard=$this->widget('ext.groupgridview.GroupGridView', array(
			'id'=>'node-model-grid',
            'dataProvider'=>$dataProvider,            
            'filter'=>$filtersForm,
            'ajaxVar' => 'ajaxFiltering',            
            'mergeColumns' => array('node_name'),
            'template'=> Yii::app()->request->getIsAjaxRequest()? '{summary}{items}{pager}': '{summary}{pager}',
            'beforeAjaxUpdate'=>'function(id, data){ $("#node-status-loading-dialog").dialog("open"); }',                        
            'afterAjaxUpdate'=>'function(id, data){ $("#node-status-loading-dialog").dialog("close"); }',            
            //'ajaxUpdateError'=>'function(id, data){ $("#node-status-loading-dialog").dialog("close");   }',
            'ajaxUpdate'=>'node-model-grid',
            //Añadiendo clase extra a las de even y odd
            'rowCssClassExpression' => '( $row%2 ? $this->rowCssClass[1] : $this->rowCssClass[0] ) .
								        ( $data["node_status"] ? null : " offline" )
								    ',			
            'selectionChanged' => 'function(row){ rowSelected(row);   }',
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
                    //'value'=> function ($data,$row){
                    	//$this->attachEventHandler(function($event){});
                    	//return $data["status"];
                    //},                   
                    'cssClassExpression' => '$data["node_status"]? "vm_status" : "offline"',
                ),                
                array(
				    'class'=>'CButtonColumn',
				    'template'=>'{run}{pause}{stop}',
				    'cssClassExpression' => '$data["node_status"]? "" : "offline"',
				    'header'=>'Actions',
				    'buttons'=> array
    				(
	    				'run' => array
				        (				        
				            'label'=>'Run Virtual Machine',
				            'imageUrl'=>Yii::app()->request->baseUrl.'/images/play.png',
				            'url'=>'Yii::app()->createUrl("vm/command", array("id"=>$data["vm_name"],
				            												  "node"=>$data["node_name"],
				            												  "command"=>"run"))',
				           	'options' => array('id'=>'run'),
				            'visible' => '$data["node_status"]',

				        ),
				        'pause' => array
				        (				        	
				            'label'=>'Pause Virtual Machine',
				            'imageUrl'=>Yii::app()->request->baseUrl.'/images/pause.png',
				            'url'=>'Yii::app()->createUrl("vm/command", array("id"=>$data["vm_name"],
				            												  "node"=>$data["node_name"],
				            												  "command"=>"pause"))',
						    'options' => array('id'=>'pause'),						    
				            'visible' => '$data["node_status"]',
				        ),
				        'stop' => array
				        (
				            'label'=>'Stop Virtual Machine',
				            'imageUrl'=>Yii::app()->request->baseUrl.'/images/stop.png',
				            'url'=>'Yii::app()->createUrl("vm/command", array("id"=>$data["vm_name"],
				            												  "node"=>$data["node_name"],				            												  
				            												  "command"=>"halt"))',
						    'options' => array('id'=>'stop'),
				            'visible' => '$data["node_status"]',
				        ),
    				),
				),
				array(
				    'class'=>'CButtonColumn',
				    'template'=>'{snapshot}{snapshot_list}{backup}',
				    'cssClassExpression' => '$data["node_status"]? "" : "offline"',
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
				            'visible' => '$data["node_status"]',
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
            		'disabled' =>  '!$data["node_status"]',         		
				),                                                   
				
            ),
        )); 


		
echo CHtml::button('Run', array('href'=>Yii::app()->createUrl("vm/command", array("command"=>"run")),'id' => 'run-button','disabled'=> true));
echo CHtml::button('Pause', array('href'=>Yii::app()->createUrl("vm/command", array("command"=>"pause")),'id' => 'pause-button','disabled'=> true));
echo CHtml::button('Stop', array('href'=>Yii::app()->createUrl("vm/command", array("command"=>"halt")),'id' => 'stop-button','disabled'=> true));
//echo CHtml::button('Snapshot', array('href'=>Yii::app()->createUrl("vm/batchsnapshot"),'id' => 'snapshot-button','disabled'=> true));


// echo CHtml::textArea('edit-node-config', $cfile, array('rows' => 55, 'cols' => 85));

?>

<div class="clear"></div>



<?php	
// $this->endWidget('zii.widgets.jui.CJuiDialog');	
// 
// 
// //FIXME DELETE EJEMPLOS DE OPCIONES DE DIALOGOS
// // 'draggable'=>true,
                            // // 'resizable'=>true,
                            // // 'closeOnEscape' => true,                                                       
                            // // 'show'=>'fade',
                            // // 'hide'=>'fade',
                            // // 'position'=>'center',
                            // // 'modal'=>true,
// 
// 
// //echo CHtml::button('Vagrant Config', array('onClick'=>'js:$("#snapshot-list-dialog").dialog("open");','id' => 'showconfig-button','style' => 'margin: 20px 0 30px 0;'));
?>


<script type="text/javascript">
    function lazyLoadGridView(){   
    	 
    	 var nodes = <?php echo json_encode($nodes);?>;
    	 
    	 
    	 
    	 //Filling the grid dynamicly    	 
    	 $.each( nodes, function( key, value ) {    	 	  			
  			$.fn.yiiGridView.update('node-model-grid',{
       		data: "node="+value,
       		});
		 });
		 
		 
		 
    	 // console.log(nodes);
        //$.fn.yiiGridView.update('node-model-grid');
        // $.fn.yiiGridView.update('node-model-grid',{
       		// data: "node=Nodopruebas",
       // });
        // $.fn.yiiGridView.update('node-model-grid',{
                       		// data: "node=Nodo2",
                       // });
                       // $.fn.yiiGridView.update('node-model-grid',{
                       		// data: "node=NodoOffline",
                       // });
                       
       
        
        //$('#node-status-loading').progressbar('option', 'value', 75);
               
	}

function commandButtonStatus(value)
{
	$("#run-button").attr('disabled',value);
	$("#pause-button").attr('disabled',value);
	$("#stop-button").attr('disabled',value);
	$("#snapshot-button").attr('disabled',value);
}

function rowSelected(row) {
	   
	   //Little hack to avoid selection in offline Nodes
	   $("tr.offline").removeClass("selected");
	   $("tr.offline .select-on-check").prop('checked',false);
	      
	   
	   
	   //Enable or disable batch buttons       
       var value=($.fn.yiiGridView.getChecked("node-model-grid","node-check").length>0) ? false:true;
       
       commandButtonStatus(value);
       
}



//Función para ejecutar operaciones batch, recorre la lista de vm checkeadas,
//obtiene ciertos parámetros y los pasa al controlador
jQuery(	function($) {
jQuery('#run-button, #pause-button, #stop-button, #snapshot-button').live('click',function() {
	
	var vms = [];
	//var checkboxCount=$("#node-model-grid").yiiGridView("getChecked","vm_name");
	var checkboxCount = $.fn.yiiGridView.getChecked("node-model-grid","node-check");
	
	$.each(checkboxCount, function(key,value) {		
		var row = $("#node-model-grid").yiiGridView("getRow",value);
		console.log(row);
		return;
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
	
	
	if (vms.length === 0)
		return false;
	
	//console.log($("#node-model-grid").yiiGridView.element);
	// console.log($.fn.yiiGridView.getChecked("node-model-grid","node-check"));
// 	 
	  //console.log($("#node-model-grid").yiiGridView("getKey","1"));
	  // console.log($("#node-model-grid").yiiGridView("getColumn","1"));
	    // console.log($("#node-model-grid").yiiGridView("getSelection"));
	// console.log($("#node-model-grid").yiiGridView("getRow","1"));
	var command =  $(this).attr("href");
	
	
	
	//console.log($("#node-model-grid").yiiGridView("columns"));
	
	
	$.each( vms, function( key, value ) {
		// console.log(key);
		// console.log(value["id"])  			
  			// $.fn.yiiGridView.update('node-model-grid',{
       		// data: {node:value["node"],id:value["id"]},url:command,
       		// });
		 
	
	
		$.ajax({
	        		type: 'GET',
	        		
	        		timeout:0, //No timeout
	        		url: command,
	        		//async:false,
	        		// data: {batch_objects:vms},
	        		data:{node:value["node"],id:value["id"]},
	        		        		
	        		success:function(data) {
	        				var msg = jQuery.parseJSON(data)	        		 				   												   						
							
	        				var row = $("#node-model-grid").yiiGridView("getRow",value["row"]);
	        				
	        				$.each(row,function(){
	        					if ($(this).hasClass("vm_status"))
								{
									$(this).text(msg.status);
								}		
	        				});
	        					
	        				
	                       
	                       
	                },
	                error:function(x, t, m) {
	                	console.log(x);
	                	
	                	//$("#node-status-loading-dialog").dialog("close");                	
	                	if (t=="timeout")
	                	{                		
	                		$("#flash-messages").addClass("flash-error").html("Request Timeout Error").fadeIn().delay(3000).fadeOut("slow");
	                	}else{
	                		if (x.responseText.length!=0)
	                		{
	                			//alert("FIXME: ERROR "+x.responseText);
	                			
	                			$("#flash-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(5000).fadeOut("slow");
	                		}
	                	}
	                }
            })
			
	});
	
	return false;
	
});
});	



jQuery(function($) {
jQuery('#run, #pause, #stop').live('click',function() {
        $("#node-status-loading-dialog").dialog({title: "Performing Operation"});
        $("#node-status-loading-dialog").dialog("open");
        $.ajax({
        		type: 'GET',
        		timeout:0, //No timeout
        		url: $(this).attr("href"),        		
        		success:function(data) {
        			  console.log(data);
        			  $("#node-status-loading-dialog").dialog("close");
        			  var msg = jQuery.parseJSON(data)
        			  
	    				
    				  $("#flash-messages").addClass("flash-success").html(msg.status).fadeIn().delay(3000).fadeOut("slow");
					           						
                       // $.fn.yiiGridView.update('node-model-grid',{
                       		// data: "ajaxUpdateRequest=true",
                       // });
                       
                },
                error:function(x, t, m) {    	
                	$("#node-status-loading-dialog").dialog("close");                	
                	if (t=="timeout")
                	{                		
                		$("#flash-messages").addClass("flash-error").html("Request Timeout Error").fadeIn().delay(3000).fadeOut("slow");
                	}else{
                		if (x.responseText.length!=0)
                		{
                			//alert("FIXME: ERROR "+x.responseText);
                			$("#flash-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(5000).fadeOut("slow");
                		}
                	}
                }
            })
        
        return false;
});
});

function reload_page(){
	lazyLoadGridView();
	//$("#node-model-grid").live("change",function(event,params){alert("TRIGGEADONODEGRID");});
	setTimeout( reload_page, <?php echo $refresh_time*1000?> );
}

$( document ).ready(function() {
	$('#operation_log').show();
	
	setTimeout(reload_page,120000);
	
	
	$(document).on("updateAsync",function(event,params){
		if (params.operation=="VM_STATUS")
		{	
			$.fn.yiiGridView.update('node-model-grid',{
       		data: "node="+params.node,
       		});
		}
		
	});
	
		
	
	
	
	 
});




</script>



