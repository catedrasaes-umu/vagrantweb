<?php /* @var $this NodeController */
/* @var $model NodeModel */

$this -> breadcrumbs = array('Nodes' => array('index'), $id => array('view', 'id' => $id), 'Edit Node Configuration', );

$this -> menu = array( array('label' => 'List Nodes', 'url' => array('index')),
//array('label'=>'Create Node', 'url'=>array('create')),
array('label' => 'Modify Node', 'url' => array('update', 'id' => $id)), array('label' => 'Delete Node', 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $id), 'confirm' => 'Are you sure you want to delete this item?')),
//array('label'=>'Manage Nodes', 'url'=>array('admin')),
);
?>

<h1>Node: <?php echo $id; ?></h1>

<div class="form">

<?php $form = $this -> beginWidget('CActiveForm', array('id' => 'editconfig-form', 'enableAjaxValidation' => false, ));

echo CHtml::textArea('edit-node-config', $cfile, array('rows' => 55, 'cols' => 85));
?>

<div id="buttons">
<?php 



$this->widget(
                'booster.widgets.TbButton',
                array('buttonType' => 'submit', 
                    'label' => 'Save',
                    'htmlOptions' => array('submit'=>Yii::app()->createUrl("node/uploadconfig",array('node' => $id)),
                                            'style'=>'margin-right:20px;margin-bottom:30px',
                                            'params' => array('cfile' => 'tio'),)
            ));


$this->widget(
                'booster.widgets.TbButton',
                array('buttonType' => 'submit', 
                    'label' => 'Cancel',
                    'htmlOptions' => array('submit'=>Yii::app()->createUrl("node/view",array('id'=>$id)),
                    						'style'=>'	margin-bottom:30px',)
            ));

		
//echo CHtml::button('Save', array('submit' => Yii::app() -> createUrl("node/uploadconfig", array('node' => $id)), 'params' => array('cfile' => 'tio'), ));
//echo CHtml::button('Cancel', array('submit' => array('view', 'id' => $id)));
?>
</div>

<?php $this->endWidget(); ?>

</div> <!-- form -->


<script>
	$("#edit-node-config").keydown(function(e) {

		if (e.which === 9 && e.shiftKey) {
			
			// prevent the focus lose
			e.preventDefault();

		} else if (e.keyCode === 9) {// tab was pressed
			// get caret position/selection
			var start = this.selectionStart;
			var end = this.selectionEnd;

			var $this = $(this);
			var value = $this.val();

			// set textarea value to: text before caret + tab + text after caret
			$this.val(value.substring(0, start) + "\t" + value.substring(end));

			// put caret at right position again (add one for the tab)
			this.selectionStart = this.selectionEnd = start + 1;

			// prevent the focus lose
			e.preventDefault();
		}
	});

</script>