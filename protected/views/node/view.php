<?php



$this->breadcrumbs=array(
	'Nodes'=>array('index'),
	$model->node_name,
);


?>

<script type="text/javascript">	
	onload = function() { 	
			 reload_page();
		}
</script>

<?php 	
	Yii::app()->clientScript->registerScript(
   'updatedownloads',
   
   '$("#flash-node-messages").fadeIn().delay(3000).fadeOut("slow")',
   
   CClientScript::POS_READY
	);


	

?>


<div id="flash-node-messages">
	<?php
		$flashMessages = Yii::app()->user->getFlashes();
		
		if ($flashMessages) {		
		    foreach($flashMessages as $key => $message) {
		    	
		        echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
		    }
			
		    
		}
	?>
</div>

<h1 class="page-header">Node: <?php echo $model->node_name; ?></h1>
	
<?php $this->widget('booster.widgets.TbDetailView', array(
	'data'=>$model,
	'htmlOptions'=>array('class' =>'detailview-curved'),
	'attributes'=>array(
		'node_name',
		'node_address',
		'node_port',
		
	),
)); 


?>

<div id="hwtable">
<?php if (!is_null($nodeinfo)){ ?>
<table class="detailview-curved table table-striped table-condensed" id="yw1" style="margin-top:20px">
<tr class="odd"><th>Operating System</th><td><span><?php echo $nodeinfo["lsbdistdescription"]; ?></span></td></tr>
<tr class="even"><th>Processor Count</th><td><span><?php echo $nodeinfo["physicalprocessorcount"]; ?></span></td></tr>
<tr class="odd"><th>Core Count</th><td><span><?php echo $nodeinfo["processorcount"]; ?></span></td></tr>
<tr class="even"><th>Architecture</th><td><span><?php echo $nodeinfo["architecture"]; ?></span></td></tr>
<tr class="odd"><th>CPU Average</th><td><span><?php echo $nodeinfo["cpuaverage"]; ?></span></td></tr>
<tr class="even"><th>Interfaces</th>
	<td>
	<?php 
		echo "<table class='nopadtop'>";
		
		foreach ($nodeinfo["interfaces"] as $key => $value) {			
			echo "<tr>";			
			 echo("<td><span>".$value["name"].":</span></td><td><span>".$value["ipaddress"]."</span></td>");			
			echo "</tr>";
		}		
		
		echo "</table>";
	?>
	</td>
</tr>
<tr class="odd"><th>Memory</th><td><span><?php echo $nodeinfo["memorysize"]."&nbspGB&nbsp;&nbsp;&nbsp;(".$nodeinfo["memoryfree"]."&nbspGB)"; ?></span></td></tr>
<tr class="even"><th>Disk Usage</th>
	<td>
	<?php 
		
		echo "<table class='nopadtop'>";
		
		foreach ($nodeinfo["diskusage"] as $key => $value) {						
			echo "<tr>";			
			 echo("<td><span>".$value["partition"].":</span></td>");
			 echo("<td><span>Free: ".$value["free"]."B</span></td>");			
			 echo("<td><span>Total: ".$value["total"]."B</span></td>");			
			 echo("<td><span>(Used: ".$value["freepercent"].")</span></td>");			
			echo "</tr>";
		}		

		
		echo "</table>";
	?>
	</td>
</tr>
<tr class="odd"><th>Vagrant Version</th><td><?php echo $nodeinfo["vagrant_version"];?></td></tr>

</table>

<?php } ?>
</div>


<?php



$this->widget(
			    'booster.widgets.TbButton',
			    array('buttonType' => 'submit', 
			    	'label' => 'Change Password',
			    	'htmlOptions' => array('submit'=>Yii::app()->createUrl("node/passwordchange",array("node" => $model->node_name)),
			    							'id' => 'changepassword-button',
			    							'style'=>'margin: 20px 20px 30px 0;'))
			);

$this->widget(
			    'booster.widgets.TbButton',
			    array('buttonType' => 'submit', 
			    	'label' => 'Vagrant Config',
			    	'htmlOptions' => array('submit'=>Yii::app()->createUrl("node/showconfig",array("node" => $model->node_name)),
			    						//'onClick'=>"loadConfig('$model->node_name');",
			    						'id' => 'showconfig-button','style'=>'margin: 20px 0 30px 0;'))
					);




											

?>

<div id="highlighted-config" style="display: none;">
	
</div>

<?php

echo $this->renderPartial('_snapdialogs');
echo $this->renderPartial('_uploadboxform',array('node'=>$model->node_name));
echo $this->renderPartial('_addvmdialog',array('boxes'=>$boxesdp,'node'=>$model->node_name,'nodeinfo'=>$nodeinfo));



?>

<!-- Gráfica funcional, ocultando por ahora. Descomentar para que funcione -->
<!--<div class="panel panel-primary">
    <div class="panel-heading">
        <i class="fa fa-bar-chart-o fa-fw"></i> Node Performance
    </div>
    <div class="panel-body">
        <div class="flot-chart">
            <div class="flot-chart-content" id="morris-line-chart"></div>
        </div>
    </div>    
</div>-->


<div id="flash-action-messages"></div>


 

<a href="#virtualmachines"></a>
<?php

$this->widget('booster.widgets.TbGridView', array(
	'id'=>'vms-grid',
	'selectableRows'=>0,
	'dataProvider'=>$vmsdp,
	'htmlOptions'=>array('class' =>'grid-view table-curved'),
	'ajaxUpdate'=>'boxes-grid,boxes-download-grid,hwtable',
	'afterAjaxUpdate'=>'function(id, data){  enableAdding(); }',	
	'columns'=>array(
		
		array(
                    'header'=>'Virtual Machine',
                    'type'	=> 'raw',
                    'value'=>'CHtml::link($data["name"], array("vm/view","id"=>$data["name"],"node"=>"'.$model->node_name.'"))',
                    'name' => 'name',                    
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
				    'class'=>'booster.widgets.TbButtonColumn',
				    'template'=>'{run}{pause}{stop}',
				    'cssClassExpression' => '($data["busy"]==true)?"busy": "" ',				    
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
				    'class'=>'booster.widgets.TbButtonColumn',
				    'template'=>'{snapshot}{snapshot_list}{backup}',				    
				    'cssClassExpression' => '($data["busy"]==true)?"busy": "" ',				    
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
								                		$("#flash-action-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(5000).fadeOut("slow");								                	
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
								                		$("#flash-action-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(3000).fadeOut("slow");																					                	
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
				    'class'=>'booster.widgets.TbButtonColumn',
				    'header' => 'Options',
				    // 'template' => '{view}{destroy}{delete}',
				    'template' => '{destroy}{delete}',
				    'cssClassExpression' => '($data["busy"]==true)?"busy": "" ',				    
				  	'buttons'=> array
    				(
    					'destroy' => array
				        (				        
				            'label'=>'Destroy current machine state',
				            'imageUrl'=>Yii::app()->request->baseUrl.'/images/RecycleBin.png',
				            'url'=>'Yii::app()->createUrl("vm/destroy", array("id" => $data["name"],"node"=>"'.$model->node_name.'","ajax"=>true))',
				            'confirm'=>'Are you sure you want to delete this item?',
				            'click'=>'js:function(){				            						
				            			if(!confirm("Are you sure you want to destroy the current machine state?")) return false;	            							            			
				            			$.ajax({				            					
								        		type: "GET",								        		
								        		url: $(this).attr("href"),								        		        		
								        		success:function(data) {													   												   
													   $.fn.yiiGridView.update("vms-grid",{data: "ajaxUpdateRequest=true",});

													   $("#flash-action-messages").html("Virtual Machine Removed").fadeIn().delay(3000).fadeOut("slow");								                	
								                },
								                error:function(x, t, m) {								                			
								                		$("#flash-action-messages").html(x.responseText).fadeIn().delay(3000).fadeOut("slow");								                	
								                }
								            });
				            			return false;
									  }',
							  				           	

				        ),						
				        
				        
					),
    				  
				    
			    	'deleteButtonUrl'=>'Yii::app()->createUrl("vm/delete", array("id" => $data["name"],"node"=>"'.$model->node_name.'"))',
			    	'deleteConfirmation' => 'Are you sure you want to remove the virtual machine from the node and all its data?',				    					
			    	'afterDelete'=>'function(link,success,data){ $("#flash-action-messages").html(data).fadeIn().delay(3000).fadeOut("slow"); }',
					
	    ),
	    
	),
));


?>


<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" style="padding-left:0">
<?php

	$this->widget(
				    'booster.widgets.TbButton',
				    array('buttonType' => 'submit', 
				    	'label' => 'Add Virtual Machine',
				    	'htmlOptions' => array('id' => 'addvm-btn',
				    							'onClick'=>'js:$("#addvm-dialog1").dialog(addvm_dialog_options);',
				    							'style'=>'margin-top: 20px;'))
					);
?>
	</div>
	<div class="col-lg-10 col-md-10 col-sm-8 col-xs-8" style="padding-top:25px">
	
		<span id="addlabel" style='color:red;font-weight:bold'>
			Please, upload a box to be able to add new virtual machines
		</span>	
	</div>


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-left:0;padding-right:0;margin-top:40px;margin-bottom:40px">
	 <div class="panel panel-primary panel-grid" id="alertas">
        <div class="panel-heading">
            <i class="fa fa-bell fa-fw"></i> Operation Notification Panel
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">
            <div class="list-group">            	
            </div>
            <!-- /.list-group -->
            <a href="<?=Yii::app()->createUrl("node/operations",array("node"=>"$model->node_name"))?>" class="btn btn-default btn-block">View All Operations</a>
        </div>
        <!-- /.panel-body -->
    </div>
</div>



<?php

//echo "<div style='height:100px'></div>";

?>


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-left:0;padding-right:0">
<a href="#boxes"></a>

<div id="flash-box-messages"></div>




<?php




$this->widget('booster.widgets.TbGridView', array(
	'id'=>'boxes-grid',
	'dataProvider'=>$boxesdp,
	'selectableRows'=>0,
	'htmlOptions'=>array('class' =>'grid-view table-curved'),
	

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
				    'class'=>'booster.widgets.TbButtonColumn',
				    'template' => '{delete}',
				    'header' => 'Actions',				    
				    'buttons'=> array
    				(
    					'delete' => array
				        (				        
				            'label'=>'Delete Box',
				            // 'imageUrl'=>Yii::app()->request->baseUrl.'/images/RecycleBin.png',
				            'url'=>'Yii::app()->createUrl("box/delete", array("id" => $data["name"],"provider"=>$data["provider"],"node"=>"'.$model->node_name.'"))',
				            'confirm'=>'Are you sure you want to delete this box?',
				            'click'=>'js:function(){				            						
				            			if(!confirm("Are you sure you want to delete this box?")) return false;	            							            			
				            			$.ajax({				            					
								        		type: "GET",								        		
								        		url: $(this).attr("href"),								        		        		
								        		success:function(data) {													   												   
													   $.fn.yiiGridView.update("boxes-grid",{data: "ajaxUpdateRequest=true",});

													   $("#flash-box-messages").addClass("flash-success").html("Box has been removed").fadeIn().delay(3000).fadeOut("slow");								                	
								                },
								                error:function(x, t, m) {									                								                			;
								                		$("#flash-box-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(3000).fadeOut("slow");								                	
								                }
								            });
				            			return false;
									  }',
							  				           	

				        ),						
				        
				        
					),
			    	//'deleteButtonUrl'=>'Yii::app()->createUrl("box/delete", array("id" => $data["name"],"provider"=>$data["provider"],"node"=>"'.$model->node_name.'"))',				    					
			    	
			    	
					
	    ),
	),
));


$this->widget('booster.widgets.TbButton',
			    array('buttonType' => 'submit', 
			    	'label' => 'Upload Box',
			    	'htmlOptions' => array('id' => 'box-upload-btn',
			    							'onClick'=>'js:$("#box-upload-dialog").dialog("open");',
			    							//'onClick'=>'js:$("#box-upload-dialog").dialog(box_dialog_options);',
			    							'style'=>'margin: 20px 0 50px 0;'))
				);


	


if ($showdownloadinfo){
	
	echo $this->renderPartial('_downloadsinfo', array('model'=>$model,'downloadsdp' => $downloadsdp ));

}

?>


</div>



<script>




$(document).on("click", "div#alertas div.list-group a.list-group-item", function(event){		
	
	$(this).children("div.notificacionmsg").toggle("600");
	return false;
});


$(document).on("click", "#run, #pause, #stop", function(){		
        
        $.ajax({
        		type: 'GET',
        		timeout:0, //No timeout
        		url: $(this).attr("href"),        		
        		success:function(response) {
					           						
                       
                       
                       $("#flash-action-messages").addClass("flash-success").html(jQuery.parseJSON(response)["statusmsg"]).fadeIn().delay(3000).fadeOut("slow");
                       
                },
                error:function(x, t, m) {    	
                	
                	if (t=="timeout")
                	{                		
                		$("#flash-action-messages").addClass("flash-error").html("Request Timeout Error").fadeIn().delay(3000).fadeOut("slow");
                	}else{                		                		
                		$("#flash-action-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(3000).fadeOut("slow");
                	}
                },
                
            });
        
        return false;
	});





	$('#clearcompleted').on('click',function(event) {

		$.ajax({
			type: 'GET',
			
			timeout:0,
			url: $(this).attr("href"),

	        complete:function(){
	        	
	        	
	        	$.fn.yiiGridView.update('boxes-download-grid');
			        	

	        }
	    });

		event.preventDefault();

	});
	
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
	
	




	function loadNodePerformance(){
		/*var timestamp_data = [
		  {"period": 1349046000000, "cpu": 3407, "memory": 660},
		  {"period": 1313103600000, "cpu": 3351, "memory": 629},
		  {"period": 1299110400000, "cpu": 3269, "memory": 618},
		  {"period": 1281222000000, "cpu": 3246, "memory": 661},
		  {"period": 1273446000000, "cpu": 3257, "memory": 667},
		  {"period": 1268524800000, "cpu": 3248, "memory": 627},
		  {"period": 1263081600000, "cpu": 3171, "memory": 660},
		  {"period": 1260403200000, "cpu": 3171, "memory": 676},
		  {"period": 1254870000000, "cpu": 3201, "memory": 656},
		  {"period": 1253833200000, "cpu": 3215, "memory": 622}
		];
		Morris.Line({
		  element: 'morris-line-chart',
		  data: timestamp_data,
		  xkey: 'period',
		  ykeys: ['cpu', 'memory'],
		  labels: ['CPU', 'Memory'],
		  dateFormat: function (x) { return new Date(x).toDateString(); }
		});*/

		var day_data = [
		{"elapsed": "8:00", "cpu": 34, "memory": 20},
		{"elapsed": "8:15", "cpu": 24, "memory": 50},
		{"elapsed": "8:30", "cpu": 3, "memory": 60},
		{"elapsed": "8:45", "cpu": 12, "memory": 51},
		{"elapsed": "9:00", "cpu": 13, "memory": 80},
		{"elapsed": "9:15", "cpu": 22, "memory": 82},
		{"elapsed": "9:30", "cpu": 5, "memory": 85},
		{"elapsed": "9:45", "cpu": 26, "memory": 79},
		{"elapsed": "10:00", "cpu": 12, "memory": 70},
		{"elapsed": "10:15", "cpu": 19, "memory": 70},
		{"elapsed": "10:30", "cpu": 30, "memory": 70},
		{"elapsed": "10:45", "cpu": 35, "memory": 70},
		{"elapsed": "11:00", "cpu": 40, "memory": 70},
		{"elapsed": "11:15", "cpu": 55, "memory": 70}
		];
		Morris.Line({
		element: 'morris-line-chart',
		data: day_data,
		xkey: 'elapsed',
		ykeys: ['cpu','memory'],
		labels: ['CPU','Memory'],
		parseTime: false,
		hideHover: 'auto',
		resize: true
		});
	}


	function reload_page(){

		var node = <?php echo json_encode($model->node_name);?>;

		
		$.fn.yiiGridView.update('vms-grid',{data: "id="+node,});
		setTimeout( reload_page, 30000 );	
	}


function loadNotifications()
{		

		$.ajax({
            type: "GET",                                                
            url: "/vagrantweb/index.php?r=operation/last&last=7&node="+<?php echo json_encode($model->node_name);?>,                                                              
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
 
            },
            error:function(x, t, m) {                            		
                    $("#flash-messages").addClass("flash-error").html("Error retrieving operations");                                                 
            }
        });	
}



$( document ).ready(function() {	
	

	loadNotifications();

	//Disabling notifications, uncomment to enable
	//loadNodePerformance();

	enableAdding();
	
	$(document).on("updateAsync",function(event,params){
		if (params.operation=="VM_STATUS")
		{		
			$.fn.yiiGridView.update('vms-grid',{
       			data: "node="+params.node,
       		});
       		loadNotifications();
		}else if (params.operation=="BOX_STATUS")
		{	
			$.fn.yiiGridView.update('boxes-grid');
		}
	
	});
	
	$(document).on("operationPolling",function(event,params){		
       	loadNotifications();		
		
	});	
	 
});






	
function enableAdding(){
	
	
	if ($("div#boxes-grid > table > tbody > tr > td").hasClass("empty"))
	{		
		$("#addvm-btn").prop( "disabled", true );
		$("#addlabel").show();
	}else{	
		$("#addvm-btn").prop( "disabled", false );
		$("#addlabel").hide();
	}

	
}
	
</script>


<script src="recursos/js/plugins/morris/raphael-2.1.0.min.js"></script>
<script src="recursos/js/plugins/morris/morris.js"></script>



