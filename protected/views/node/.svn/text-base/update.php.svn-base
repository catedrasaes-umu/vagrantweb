<?php
/* @var $this NodeController */
/* @var $model NodeModel */

$this->breadcrumbs=array(
	'List Nodes'=>array('index'),
	$model->node_name=>array('view','id'=>$model->node_name),
	'Update',
);

$this->menu=array(
	array('label'=>'List NodeModel', 'url'=>array('index')),
	array('label'=>'Create NodeModel', 'url'=>array('create')),
	array('label'=>'View NodeModel', 'url'=>array('view', 'id'=>$model->node_name)),
	array('label'=>'Manage NodeModel', 'url'=>array('admin')),
);
?>

<h1>Update Nodes <?php echo $model->node_name; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>