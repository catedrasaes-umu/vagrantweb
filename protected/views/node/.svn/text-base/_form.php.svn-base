<?php
/* @var $this NodeController */
/* @var $model NodeModel */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'node-model-form',
	'enableAjaxValidation'=>false,
)); ?>

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
		<?php echo $form->labelEx($model,'node_port'); ?>
		<?php echo $form->textField($model,'node_port'); ?>
		<?php echo $form->error($model,'node_port'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'node_password'); ?>
		<?php echo $form->passwordField($model,'node_password',array('size'=>60,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'node_password'); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->