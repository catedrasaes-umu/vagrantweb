<?php
/* @var $this ProjectController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Groups',
);

// $this->menu=array(
// 	array('label'=>'Create Group', 'url'=>array('create')),
	
// 	//array('label'=>'Manage Projects', 'url'=>array('admin')),
// );


if (Yii::app()->user->isSuperUser)
{
	array_push($this->menu,array('label'=>'Manage Groups Users', 'url'=>array('project/manageusers')));	
}


?>

<div class="col-lg-12 col-md-12 col-sm-12" style="padding-left:0;padding-right:0">

<h1 class="page-header">Groups</h1>


<?php 	
	// Yii::app()->clientScript->registerScript(
 //   'hideFlashEffect',
 //   //'$("#flash-messages").animate({opacity: 1.0}, 3000).fadeOut("slow");',
 //   '$("#flash-messages").fadeIn().delay(3000).fadeOut("slow")',
   
 //   CClientScript::POS_READY
	// );
	?>

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
	// $this->widget('zii.widgets.CListView', array(
	// 'dataProvider'=>$dataProvider,
	// 'itemView'=>'_view',
//)); 
?>

<div style="padding-top:15px">
	<label style="font-weight:normal">Groups marked with <label style="color:red">*</label> have busy machines. 
	Global commands will be performed only on idle machines</label>
</div>

<?php $this->widget('booster.widgets.TbGridView', array(
	'id'=>'project-model-grid',
	'dataProvider'=>$dataProvider,	
	'selectableRows' => 0,
	'htmlOptions'=>array('class' =>'grid-view table-curved'),
	'columns'=>array(				 
		 array(
		 	'type'	=> 'raw',
            'value'=>'CHtml::link(CHtml::encode($data["project_name"]), array("project/view","id"=>$data["id"]))." ".($data["busy"]?"<strong style=\"color:red;font-size:15px\">*</strong>":"")',                    
            'header'=>'Group Name',
            'name'=>'project_name',  
			'htmlOptions' => array('class' => 'project_list','id'=>'project_name'),                                               
        ), 
        array(
		    'class'=>'CButtonColumn',
		    'template'=>'{run}{pause}{stop}',		    
		    'htmlOptions' => array('class' => 'project_buttons'),                                               
		    'header'=>'Group Actions',
		    'buttons'=> array
			(
				'run' => array
		        (				        
		            'label'=>'Run Virtual Machine',
		            'imageUrl'=>Yii::app()->request->baseUrl.'/images/play.png',
		            'url'=>'Yii::app()->createUrl("project/batchrun",array("project"=>$data["id"]))',		            
		           	'options' => array('id'=>'run'),
		           	
		            

		        ),
		        'pause' => array
		        (				        	
		            'label'=>'Pause Virtual Machine',
		            'imageUrl'=>Yii::app()->request->baseUrl.'/images/pause.png',
		            'url'=>'Yii::app()->createUrl("project/batchpause",array("project"=>$data["id"]))',		            
		            //'url'=>'Yii::app()->createUrl("vm/command", array("id"=>$data["vm_name"],
		            //												  "node"=>$data["node_name"],
		            //												  "command"=>"pause"))',
				    'options' => array('id'=>'pause'),						    
		            //'visible' => '$data["node_status"]',
		        ),
		        'stop' => array
		        (
		            'label'=>'Stop Virtual Machine',
		            'imageUrl'=>Yii::app()->request->baseUrl.'/images/stop.png',
		            'url'=>'Yii::app()->createUrl("project/batchstop",array("project"=>$data["id"]))',		            
				    'options' => array('id'=>'stop'),				 
		            //'visible' => '$data["node_status"]',
		        ),
			),
		),
		array(
			'class'=>'booster.widgets.TbButtonColumn',
			'header'=>'Admin Group',
			'htmlOptions' => array('style' => 'display:block'),
			'template'=>'{update}{delete}',
			'buttons'=> array
			(
				'update' => array
		        (			            
		            'url'=>'Yii::app()->createUrl("project/update",array("id"=>$data["id"]))',		            
		        ),
		        'delete' => array
		        (   
		            'url'=>'Yii::app()->createUrl("project/delete",array("id"=>$data["id"]))',		            		            
		        ),		        
			),
		),
	),
)); ?>


</div>

<div>
<?php

	$projectcreate=Yii::app()->user->checkAccess('Project.Create');

	if ($projectcreate){            	
		$this->widget(
				    'booster.widgets.TbButton',
				    array('buttonType' => 'submit', 
				    	'label' => 'Create Group',
				    	'htmlOptions' => array('submit'=>Yii::app()->createUrl("project/create"),			    							
				    							'style'=>'margin: 20px 20px 30px 0;'))
				);
	}
?>	
</div>

<script type="text/javascript">

$('#run, #pause, #stop').on('click',function() {	
	var vms = [];
	var command =  $(this).attr("href");

	
	var fcommand= command.split("&");
	command=fcommand[0];
	
	fcommand=fcommand[1];	
	
	var project_id=fcommand.split("=");
	project_id=project_id[1];

	
	$.ajax({
		type: 'POST',
		
		timeout:0, //No timeout
		url: command,			
		data:{batch_objects:vms,project:project_id},        		        		
		success:function(data) {					
				var msg = jQuery.parseJSON(data)
				$.fn.yiiGridView.update('project-model-grid',{});
			   $("#flash-messages").addClass("flash-success").html(msg).fadeIn().delay(3000).fadeOut("slow");			   
        },
        error:function(x, t, m) {
        	
        	$("#flash-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(3000).fadeOut("slow");								                		
        },
        
    });

	
	return false;
});

</script>