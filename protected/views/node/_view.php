<?php
/* @var $this NodeController */
/* @var $data NodeModel */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('node_name')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->node_name), array('view', 'id'=>$data->node_name)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('node_address')); ?>:</b>
	<?php echo CHtml::encode($data->node_address); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('node_port')); ?>:</b>
	<?php echo CHtml::encode($data->node_port); ?>
	<br />
	<b><?php echo CHtml::encode($data->getAttributeLabel('node_password')); ?>:</b>
	<?php echo CHtml::encode($data->node_password); ?>
	<br />


</div>