<?php
/* @var $this MigrationController */

$this->breadcrumbs=array(
	'Migration Tools',
);

$this->menu=array(
	array('label'=>'Export Data', 'url'=>array('export')),
	array('label'=>'Import Data', 'url'=>array('import')),	
);
?>
<h1>Export Data</h1>

<p>Please select the Components that you want to export:</p>

<h1>Import Data</h1>

<div class="form">

<?php 
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'importform',
	'enableAjaxValidation'=>false,
	'action'=>Yii::app()->createUrl('/migration/import'),
	)); 
?>

	<input name="fileimport" type="file"/>
	
	

<?php $this->endWidget(); ?>

</div><!-- form -->

