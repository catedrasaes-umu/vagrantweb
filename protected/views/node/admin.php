<?php
/* @var $this NodeController */
/* @var $model NodeModel */

$this->breadcrumbs=array(
	'Nodes'=>array('index'),
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

<h1 class="page-header">Manage Nodes</h1>

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
	'id'=>'node-admin-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'selectableRows'=>0,
	'htmlOptions'=>array('class' =>'grid-view table-curved'),
    'summaryText' => 'Displaying {start} - {end} of {count} nodes',
    //'rowHtmlOptionsExpression' =>'array("id"=>"$data->onlinestr")',
	'columns'=>array(

		array(
            'header'=>'Node Name',
            'name'=>'node_name',
            'type'	=> 'raw',
            'value'=>'CHtml::link($data["node_name"], array("node/view","id"=>$data["node_name"]))',                              
		),
		/*array(
			'header' =>'Boxes',
			'class'=>'CLinkColumn',
			'label' => 'boxes',
			'urlExpression'=>'Yii::app()->createUrl("/node/view",array("id"=>$data["node_name"]),"#"=>"boxes")',                    

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
		),*/
		// array(
			 // 'header' =>'Status',
			 // 'class'=>'CDataColumn',
			 // 'type' => 'text',
			 // 'value' => '$data->onlinestr',			 
			 // 'htmlOptions' => array('id' => 'node-status')		 
		// ),		
		array(
			'htmlOptions' => array('nowrap'=>'nowrap'),
			'class'=>'booster.widgets.TbButtonColumn',			
		),
	),
)); 

		
		if (Yii::app()->user->checkAccess('Node.Create')){	             

			$this->widget(
			    'booster.widgets.TbButton',
			    array('buttonType' => 'submit', 
			    	'label' => 'Create Node',
			    	'htmlOptions' => array('submit'=>Yii::app()->createUrl("node/create"),			    							
			    							'style'=>'margin: 20px 20px 30px 0;'))
			);
		}

		if (Yii::app()->user->checkAccess('Node.Export')){	    
			$this->widget(
			    'booster.widgets.TbButton',
			    array('buttonType' => 'submit', 
			    	'label' => 'Export Nodes',
			    	'htmlOptions' => array('submit'=>Yii::app()->createUrl("node/export"),			    							
			    							'style'=>'margin: 20px 20px 30px 0;'))
			);
		}

?>