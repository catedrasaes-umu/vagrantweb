
<div class="flash-error" id="take-snapshot-error-flash" style="display: none;"></div>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'snapshot-take-form',
	'enableAjaxValidation'=>false,	
)); 
?>
	

	<div class="row">
		<?php echo CHtml::label('Snapshot Name:', 'snapshot-name',array('style'=>'vertical-align: middle')); 
		 	  echo CHtml::textField('snapshot-name','',array('size'=>51,'maxlength'=>128)); ?>
	</div>	
	
	<div class="row">
		<?php echo CHtml::label('Snapshot Description:', 'snapshot-desc',array('style'=>'vertical-align: middle'));
		 	  echo CHtml::textArea('snapshot-desc','',array('rows'=>10,'cols'=>50));  ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::button("Take Snapshot",
								array('id'=>'takesnap-button',
									  'href'=>Yii::app()->createUrl("vm/takesnapshot",
									  								array('id'=>$vm,'node'=>$node)),	  								  
		  							  'onClick'=>'take_snapshot($("#snapshot-name").val(),$("#snapshot-desc").val())'));
		  							  
		  ?>
	</div>

<?php 

		//$form->attachEventHandler('keyenter',array($this,'puti'));
		
		$this->endWidget(); 
		
		?>
</div><!-- form -->

<script>
	
	
 	function take_snapshot(name,desc)
	{
		
		if (name.length==0)
		{
			$('#take-snapshot-error-flash').html("Snapshot name can't be empty").fadeIn().delay(3000).fadeOut("slow");		
			
		}else{
            
            
            $("#node-status-loading-dialog").dialog({title: "Creating Snapshot"});			 
			 
        	 $("#node-status-loading-dialog").dialog("open");
            $.ajax({
        		type: 'POST',   
        		     		     		
        		url: $("#takesnap-button").attr("href"),
        		data: {snapshot_name:name,snapshot_desc:desc},        		    		
        		success:function(response) {        				
         				
 						$("#flash-messages").html('<div class="flash-success">Snapshot created successfully!</div>').fadeIn().delay(3000).fadeOut("slow");
         				
                },
                error:function(x, t, m) {
                		$("#flash-messages").html('<div class="flash-error">'+x.responseText+'</div>').fadeIn().delay(3000).fadeOut("slow");
                },
                complete: function() {	
                						$("#node-status-loading-dialog").dialog("close");
                						$("#snapshot-take-dialog").dialog("close");
                					 },
            })
            
            
            
		}
		return false;		
	}
	
	//Implemented to handle key enter press and form submission	
	jQuery(function($) {
	jQuery('#snapshot-take-form').live('submit',function() {
		return take_snapshot($("#snapshot-name").val(),$("#snapshot-desc").val());
	});
});

	
	

</script>

	
	