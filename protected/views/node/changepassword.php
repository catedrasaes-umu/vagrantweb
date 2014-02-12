<?php
/* @var $this NodeController */
/* @var $model NodeModel */

$this->breadcrumbs=array(
	'Nodes'=>array('index'),
	$node => array('view','id'=>$node),
);

$this->menu=array(
	array('label'=>'List Nodes', 'url'=>array('index')),
	//array('label'=>'Create Node', 'url'=>array('create')),
	array('label'=>'Modify Node', 'url'=>array('update', 'id'=>$node)),
	array('label'=>'Delete Node', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$node),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Edit Configuration', 'url'=>array('editconfig','id'=>$node)),
	//array('label'=>'Manage Nodes', 'url'=>array('admin')),
);
?>


<h1>Change remote node password</h1>

<div class="form">
	<p class="note">This action will change the remote node passord and the local password associated to the node</p>
<?php 
	
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'upload-box-form',
	'enableAjaxValidation'=>false,
	)); ?>

	<div class="row">
		<?php 
      		echo CHtml::label('Password:', 'password_field');
      		echo CHtml::passwordField('password_field','', array('size' => 25, 'maxlength' => 20)); 
  		?>
	</div>
	<div class="row">
		<?php 
      		echo CHtml::label('Confirm Password:', 'cpassword_field');
      		echo CHtml::passwordField('cpassword_field','', array('size' => 25, 'maxlength' => 20)); 
  		?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Save',array('onClick'=>'if ($("#password_field").val()!=$("#cpassword_field").val())
																{
																		alert("Passwords does not match!!")
																		return false;
																}'
												)); ?>
	</div>
		
<?php
	$this->endWidget();
?>
</div>
