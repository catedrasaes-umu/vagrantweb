<?php
/* @var $this NodeController */
/* @var $model NodeModel */

$this->breadcrumbs=array(
	'Nodes'=>array('index'),
	$node => array('view','id'=>$node),
);

$this->menu=array(
	array('label'=>'List Nodes', 'url'=>array('index')),	
	array('label'=>'Modify Node', 'url'=>array('update', 'id'=>$node)),
	array('label'=>'Delete Node', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$node),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Edit Configuration', 'url'=>array('editconfig','id'=>$node)),
	
);
?>


<h1 class="page-header">Change remote node password</h1>

<div class="form">
	<p class="note">This action will change the remote and local node password associated to the node</p>
<?php 
	
	$form=$this->beginWidget('booster.widgets.TbActiveForm', array(
	'id'=>'upload-box-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('autocomplete'=>'off','class' => 'well'), // for inset effect
	)); ?>

	<div class="row">
		<?php 
      		echo CHtml::label('New Password:', 'password_field');
      		echo CHtml::passwordField('password_field','', array('size' => 25, 'maxlength' => 20)); 
  		?>
	</div>
	<div class="row">
		<?php 
      		echo CHtml::label('Confirm New Password:', 'cpassword_field');
      		echo CHtml::passwordField('cpassword_field','', array('size' => 25, 'maxlength' => 20)); 
  		?>
	</div>
	<div class="row buttons" style="margin-top:15px">
		


		<?php
		$this->widget(
			    'booster.widgets.TbButton',
			    array('buttonType' => 'submit', 
			    	'label' => 'Save',
			    	'htmlOptions' => array('onClick'=>'if ($("#password_field").val()!=$("#cpassword_field").val())
																{
																		alert("Passwords does not match!!")
																		return false;
																}')
			    	)
			);
		?>
		
		
		<?php
		$this->widget(
			    'booster.widgets.TbButton',
			    array('buttonType' => 'submit', 
			    	'label' => 'Cancel',
			    	'htmlOptions' => array('submit'=>Yii::app()->createUrl("node/view",
			    															array('id'=>$node)))
			));
		?>


	</div>
		
<?php
	$this->endWidget();
?>
</div>
