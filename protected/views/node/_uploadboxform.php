<?php 

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'box-upload-dialog',
    'options'=>array(
        'title'=>'Box Uploading Options',
        'width'=>'auto',
        'height'=>'auto',         
        'autoOpen'=>false,
    ),
));



 $form=$this->beginWidget('CActiveForm', array(
	'id'=>'upload-box-form',
	'action'=> Yii::app()->createUrl("box/add", array("node"=> $node)),
	'enableAjaxValidation'=>false,
	

)); ?>

<div class="row">
	<?php echo CHtml::label('Box Name','box-name');?></br>
	<?php echo CHtml::textField('box-name','',array('size'=>60,'maxlength'=>50,'style'=>"margin-top:10px;margin-bottom:10px")); ?>
</div>

<div class="upload-box-method" style="margin-top:10px;">
	<div class="row">	
	<?php	echo CHtml::radioButton('upload-option', 
									true, 
									array('value'=>'1',
										'id'=>'remote-http',
										'uncheckValue'=>null,
										'onclick'=>'remotehttpOption()',
										'style'=>'vertical-align: middle'));
			echo CHtml::label('HTTP URL', 'remote-http',array('style'=>'vertical-align: top;padding-left:7px')); ?>			
	</div></br>
	<div class="row">
			<?php echo CHtml::textField('remote-url-tf','',array('size'=>60,'maxlength'=>128)); ?>
	</div></br>	
	<div class="row">	
	<?php	echo CHtml::radioButton('upload-option', 
									false, 
									array('value'=>'2',
										'id'=>'other',
										'uncheckValue'=>null,
										'onclick'=>'otherOption()',
										'style'=>'vertical-align: middle'));
			echo CHtml::label('Shared Folder', 'other',array('style'=>'vertical-align: top;padding-left:7px')); ?>			
	</div></br>
	<div class="row">
			<?php echo CHtml::textField('othertf','',array('size'=>60,'maxlength'=>128,'disabled'=>'true')); ?>
	</div></br>
	
	
	<div class="row">
			<?php
				$this->widget('booster.widgets.TbButton',
				array('buttonType' => 'link', 
					'label' => 'Upload',
					// 'url' => Yii::app()->createUrl("box/add", array("node"=> $node)),
					'url' => Yii::app()->createUrl("box/add",array("node"=> $node)),
					'htmlOptions' => array('id' => 'upload-button')));

				
			?>
	</div>
		
</div>

<?php 
	$this->endWidget();
	$this->endWidget('zii.widgets.jui.CJuiDialog'); 
	
?>


	
<script>

// $(function() {  
                  
    
    
//       box_dialog_options = { width: "auto",
//                               height:"auto",
//                               modal : true,
//                               open: function(event, ui)
//                               {
                                  

//                                   // $('input#box_name').val('');
//                                   // $('input#remote-url-tf').val('');
                                  
                                  
//                               }
//       };

      

//     });

 $('#box-upload-dialog').bind('dialogopen', function(event) {
     $('input#box-name').val('');
     $('input#remote-url-tf').val('');
 });

	$('#upload-button').on('click',function(){
		
		

		
		$.ajax({
    		type: "POST",    		
    		data: $('form#upload-box-form').serialize(),    		
    		url: $(this).attr("href"),								        		        		
    		success:function(data) {						

    			$("#flash-box-messages").addClass("flash-success").html("Uploading Box").fadeIn().delay(5000).fadeOut("slow");								                	
    				               
            },
            error:function(x, t, m) {								                			
            		$("#flash-box-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(5000).fadeOut("slow");								                	
            }
        });
		

		$("#box-upload-dialog").dialog("close");
		return false;
	});
	//TODO FALTA VER EL TEMA DEL COMPONENTE UPLOAD YA QUE HACE UNA COMPORTAMIENTO RARO
	//CUANDO ELIMINAS UN FICHERO SELECCIONADO Y SU OPCION N OESTA ACTIVA
	function remotehttpOption()
	{
	
		$("#remote-url-tf").attr('disabled',false);
		$("#othertf").attr('disabled',true);
		$("#upload-box-browser").attr('disabled',true);
		$("#upload-box-browser_wrap_list a").attr('disabled',true);
		
	}
	
	function otherOption()
	{
	
		$("#remote-url-tf").attr('disabled',true);		
		$("#othertf").attr('disabled',false);
		$("#upload-box-browser").attr('disabled',true);
		$("#upload-box-browser_wrap_list a").attr('disabled',true);
	}
	
	function other1Option()
	{
	
		$("#remote-url-tf").attr('disabled',true);
		$("#othertf").attr('disabled',true);
		$("#upload-box-browser").attr('disabled',false);
		$("#upload-box-browser_wrap_list a").attr('disabled',false);
		
	}
	
</script>
	


