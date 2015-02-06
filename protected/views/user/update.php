<?php
/* @var $this UserController */
/* @var $model User */

$this->breadcrumbs=array(
	'Users'=>array('admin'),
	$model->username=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List User', 'url'=>array('index')),
	array('label'=>'Create User', 'url'=>array('create')),
	array('label'=>'View User', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage User', 'url'=>array('admin')),
);
?>

<h1 class="page-header">Update User <?php echo $model->username; ?></h1>


<?php echo $this->renderPartial('_form', array('model'=>$model,
												'nodes'=>$nodes,
												'node_list'=>$node_list,
												'selectednode'=>$selectednode,
												'vms'=>$vms,
												'inherited'=>$inherited,
												'inherited2'=>$inherited2,
												'showvmassignments'=>$showvmassignments,
												'showroleassignments'=>$showroleassignments,
												'filtersForm' => $filtersForm,)); ?>