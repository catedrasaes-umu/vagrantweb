<?php
/* @var $this ProjectController */
/* @var $model ProjectModel */

$this->breadcrumbs=array(
	'Groups'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Groups', 'url'=>array('index')),
	array('label'=>'Manage Groups', 'url'=>array('admin')),
);
?>

<h1 class="page-header">Create Group</h1>

<?php //echo $this->renderPartial('_form', array('model'=>$model)); ?>
<?php //echo $this->renderPartial('_form', array('model'=>$model,'nodes'=>$nodes,'filtersForm' => $filtersForm,)); ?>
<?php echo $this->renderPartial('_form', array('model'=>$model,'nodes'=>null,'filtersForm' => null)); ?>