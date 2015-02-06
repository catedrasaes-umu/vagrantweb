<?php
/* @var $this NodeController */
/* @var $model NodeModel */

$this->breadcrumbs=array(
	'Nodes'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Nodes', 'url'=>array('index')),
	array('label'=>'Manage Nodes', 'url'=>array('admin')),
);
?>

<h1 class="page-header">Create Node</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>