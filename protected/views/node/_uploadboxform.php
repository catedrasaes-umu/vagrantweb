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
			echo CHtml::label('Remote http location', 'remote-http',array('style'=>'vertical-align: middle')); ?>			
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
			echo CHtml::label('Shared Folder', 'other',array('style'=>'vertical-align: middle')); ?>			
	</div></br>
	<div class="row">
			<?php echo CHtml::textField('othertf','',array('size'=>60,'maxlength'=>128,'disabled'=>'true')); ?>
	</div></br>
	<!--<div class="row">	
	<?php	echo CHtml::radioButton('upload-option', 
									false, 
									array('value'=>'3',
										'id'=>'other1',
										'uncheckValue'=>null,
										'onclick'=>'other1Option()',
										'style'=>'vertical-align: middle'));
			echo CHtml::label('Local system', 'other1',array('style'=>'vertical-align: middle')); ?>			
	</div></br>-->
	<!--
	<div class="row">
			<?php 
				$this->widget('CMultiFileUpload',array(
					'name'=>'files',
					'accept'=>'zip|tar|gzip',
					'max'=>1,
					'remove'=>Yii::t('ui','Remove'),
					'denied'=>'Incorrect file type',
					'id'=>'upload-box-browser',					
					//'duplicate'=>'', message that is displayed when a file appears twice
					'htmlOptions'=>array('size'=>128,
										'disabled'=>'true'),
					'options'=>array(						
						'onFileRemove'=>'function(e, v, m){ alert("onFileRemove - "+v) }',
						'onFileAppend'=>'function(e, v, m){ alert("onFileAppend - "+v) }',
						
					),
				)); 

			?>
	</div></br>-->
	<div class="row">
			<?php
	
				echo CHtml::submitButton('Upload'); 	
				//echo CHtml::button('Upload', array('onClick'=>'js:alert("TODO");','id' => 'upload-box-btn'));
			?>
	</div>
		
</div>

<?php 
	$this->endWidget();
	$this->endWidget('zii.widgets.jui.CJuiDialog'); 
	
?>
	
<script>
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
	


