<?php
/* @var $this ProjectController */
/* @var $model ProjectModel */

$this->breadcrumbs=array(
	'Groups'=>array('index'),
	$model->project_name=>array('view','id'=>$model->id),
	'Update',
);


?>

<h1 class="page-header">Update Group <?php echo $model->project_name; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model,
												'nodes'=>$nodes,
												'node_list'=>$node_list,
												'selectednode'=>$selectednode,
												'filtersForm' => $filtersForm,)); ?>




<script type="text/javascript" charset="utf-8">
	jQuery(	function($) {
		jQuery('#add-button').on('click',function(event) {
			var machines = [];
			
			var command =  $(this).attr("href");			
			
			
			$('div#node-model-grid tr.selected').each(function() {				
				machines.push(new Array($(this).children('td.node').text(),$(this).children('td.machine').text()));	
			});
			
			if (machines.length==0)
			{
				alert("Please select a virtual machine to add");						                		
			}else{
			
				$.ajax({
		        		type: 'POST',	        		
		        		//timeout:0, //No timeout
		        		url: command,	        		
		        		data:{project:<?php echo $model->id;?>,vms:machines},
		        		complete:function(data) {	        			
		        			
		        				$.fn.yiiGridView.update('assigned-machines',{data: "id=<?php echo $model->id;?>",});
		        				$.fn.yiiGridView.update('node-model-grid',{data: "id=<?php echo $model->id;?>",});
		        			},        		
		        		
	            });
			}
            event.preventDefault();
		});
		
		
		
		jQuery('#remove-button').on('click',function(event) {
			
			var machines = [];
			
			var command =  $(this).attr("href");			
			
			
			$('div#assigned-machines tr.selected').each(function() {				
				machines.push(new Array($(this).children('td.node').text(),$(this).children('td.machine').text()));	
			});
			
			
			
			if (machines.length==0)
			{
				alert("Please select a virtual machine to remove");
						                		
			}else{

				$.ajax({
		        		type: 'POST',	        		
		        		//timeout:0, //No timeout
		        		url: command,	        		
		        		data:{project:<?php echo $model->id;?>,vms:machines},	        		
		        		complete:function(data) {	        			
		        			
		        				$.fn.yiiGridView.update('assigned-machines',{data: "id=<?php echo $model->id;?>",});
		        				$.fn.yiiGridView.update('node-model-grid',{data: "id=<?php echo $model->id;?>",});
		        		},        		
		        		
	            });
			}
            event.preventDefault();
		});
		
		
		$(document).on("focusout", "input#priority", function(){		
			
			var priority = $(this).val();
			
			
			var vm_machine=$(this).parent().siblings("td.machine").text();
			var node=$(this).parent().siblings("td.node").text();
			
			
			var obinput = $(this);
			
			$.ajax({
	        		type: 'POST',	        		
	        		//timeout:0, //No timeout
	        		url: '<?php echo Yii::app()->createUrl("project/updatepriority");?>',	        		
	        		data:{priority:priority,vm_machine:vm_machine,node:node,project:<?php echo $model->id;?>},
	        		success:function(data) {
	        			
	        			$.fn.yiiGridView.update('assigned-machines',{data: "id=<?php echo $model->id;?>"});
	        		},
	        		error:function(x, t, m) {
	        			alert(x.responseText);
	        			obinput.val("0");
	        			obinput.focus();
	        		},
	        		
	        		
            });
			
			
		});

	
		
});	
</script>