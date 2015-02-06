<?php
/* @var $this NodeController */
/* @var $model NodeModel */
/* @var $form CActiveForm */
?>
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
	    	// debug($message.time());
	        echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
	    }
		
	    
	}
?>

</div>


<div class="form">

<?php 

/*$form=$this->beginWidget('CActiveForm', array(
	'id'=>'node-model-form',
	'enableAjaxValidation'=>false,	
)); */

$form=$this->beginWidget('booster.widgets.TbActiveForm', array(
	'id'=>'node-model-form',
	'enableAjaxValidation'=>false,	
	'htmlOptions' => array('autocomplete'=>'off','class' => 'well'), // for inset effect
)); 




?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'node_name'); ?>
		<?php echo $form->textField($model,'node_name',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'node_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'node_address'); ?>
		<?php echo $form->textField($model,'node_address',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'node_address'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'node_port',array('label' => 'Node Port (default 3333):')); ?> 
		<?php echo $form->textField($model,'node_port'); ?>
		<?php echo $form->error($model,'node_port'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'node_password'); ?>
		<?php echo $form->passwordField($model,'node_password',array('size'=>60,'maxlength'=>64,'value'=>'')); ?>
		<?php echo $form->error($model,'node_password'); ?>
	</div>
	<div class="row buttons">
		<?php //echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
		<?php

			

			$this->widget('booster.widgets.TbButton',
				array('buttonType' => 'submit', 
					'label' => $model->isNewRecord ? 'Create' : 'Save',					
					'htmlOptions' => array('style'=>'margin-right:20px')));



			$this->widget(
			    'booster.widgets.TbButton',
			    array('buttonType' => 'submit', 'label' => 'Cancel','htmlOptions' => array('submit'=>Yii::app()->createUrl("node/admin")))
			);

		?>

	</div>

	

<?php $this->endWidget(); ?>

</div><!-- form -->
