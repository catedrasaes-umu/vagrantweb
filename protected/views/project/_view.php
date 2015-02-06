<?php
/* @var $this ProjectController */
/* @var $data ProjectModel */
?>

<div class="view">

	

	<b><?php echo CHtml::encode($data->getAttributeLabel('project_name')); ?>:</b>	
	<?php echo CHtml::link(CHtml::encode($data->project_name), array('view', 'id'=>$data->id)); ?>
	<br />


</div>