<?php
/* @var $this NodeController */
/* @var $model NodeModel */

$this->breadcrumbs=array(
	'Node Models'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Nodes', 'url'=>array('index')),
	array('label'=>'Create Node', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#node-model-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Nodes</h1>

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

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'node-model-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,	
    'summaryText' => 'Displaying {start} - {end} of {count} nodes',
    //'rowHtmlOptionsExpression' =>'array("id"=>"$data->onlinestr")',
	'columns'=>array(
		'node_name',
		// 'node_address',
		// 'node_port',
		array(
			'header' =>'Boxes',
			'class'=>'CLinkColumn',
			'label' => 'boxes',
		),
		array(
			'header' =>'Virtual Machines',
			'class'=>'CLinkColumn',
			'label' => 'virtual machines',
			//'url'=>'Yii::app()->createUrl("vm/list",array("id"=>$data["node_name"]))',
			// 'options'=>array(
                                                    // 'ajax'=>array(
                                                            // 'type'=>'POST',
                                                            // 'url'=>"js:$(this).attr('href')",                                                               
                                                    // ),
                                            // ),
		),
		// array(
			 // 'header' =>'Status',
			 // 'class'=>'CDataColumn',
			 // 'type' => 'text',
			 // 'value' => '$data->onlinestr',			 
			 // 'htmlOptions' => array('id' => 'node-status')		 
		// ),		
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
