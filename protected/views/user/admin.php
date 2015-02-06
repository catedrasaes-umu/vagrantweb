<?php
/* @var $this UserController */
/* @var $model User */

$this->breadcrumbs=array(
	'Users'=>array('index'),
	'Manage',
);

$this->menu=array(
	// array('label'=>'List User', 'url'=>array('index')),
	array('label'=>'Create User', 'url'=>array('create')),
	array('label'=>'Manage Roles', 'url'=>array('rights/authItem/roles')),
	array('label'=>'Manage Permissions', 'url'=>array('rights/authItem/permissions')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#user-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1 class="page-header">Manage Users</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('booster.widgets.TbGridView', array(
	'id'=>'user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'selectableRows' => 0,
	'htmlOptions'=>array('class' =>'grid-view table-curved'),
	'columns'=>array(		
		//'username',	
		 array(
	        'header'=>'Username',
	        'name'=>'username',
	        'type'	=> 'raw',
	        'value'=>'CHtml::link($data->username, array("user/view","id"=>$data->id))',                    	        
		),	
		'email',
		array(
			'class'=>'booster.widgets.TbButtonColumn',	
		),
	),
)); 


	if (Yii::app()->user->checkAccess('User.Create')){	             

			$this->widget(
			    'booster.widgets.TbButton',
			    array('buttonType' => 'submit', 
			    	'label' => 'Create User',
			    	'htmlOptions' => array('submit'=>Yii::app()->createUrl("user/create"),			    							
			    							'style'=>'margin: 20px 20px 30px 0;'))
			);
	}
?>


<style>
	th#user-grid_c2 {
		width:12%;
	}
</style>