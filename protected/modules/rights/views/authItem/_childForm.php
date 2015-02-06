<div class="form">

<?php $form=$this->beginWidget('booster.widgets.TbActiveForm', array(		
	'htmlOptions' => array('class' => 'well'), // for inset effect
)); ?>
	
	<div class="row">
		<?php echo $form->dropDownList($model, 'itemname', $itemnameSelectOptions); ?>
		<?php echo $form->error($model, 'itemname'); ?>
		<?php
		$this->widget(
			    'booster.widgets.TbButton',
			    array('buttonType' => 'submit', 'label' => 'Add','htmlOptions' => array('style'=>'margin-left:20px'))
			);
		?>
		
		
	</div>
	
	

<?php $this->endWidget(); ?>

</div>