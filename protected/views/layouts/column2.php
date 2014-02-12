<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>
<div class="span-5">
	 <div id="sidebar">
	 <?php
		// $this->beginWidget('zii.widgets.CPortlet', array(
			// 'title'=>'Configuration',
		// ));
		// $this->widget('zii.widgets.CMenu', array(
			// 'items'=>array(
				// array('label'=>'TODO', 'url'=>array('TODO')),
				// array('label'=>'TODO', 'url'=>array('TODO')),
				// array('label'=>'TODO', 'url'=>array('TODO')),
			// ),
			// 'htmlOptions'=>array('class'=>'operations'),
		// ));
		// $this->endWidget();
	?>
	
	<?php
		$this->beginWidget('zii.widgets.CPortlet', array(
			'title'=>'Operations',
		));
		$this->widget('zii.widgets.CMenu', array(
			'items'=>$this->menu,
			'htmlOptions'=>array('class'=>'operations'),
		));
		$this->endWidget();
	?>
	</div>
 </div>

<div class="span-18">	
	<div id="content">		
		<?php echo $content; ?>
	</div><!-- content -->
</div>
<!-- <div class="span-5 last">
	 <div id="sidebar">
	 <?php
		$this->beginWidget('zii.widgets.CPortlet', array(
			'title'=>'Operations',
		));
		$this->widget('zii.widgets.CMenu', array(
			'items'=>$this->menu,
			'htmlOptions'=>array('class'=>'operations'),
		));
		$this->endWidget();
	?>
	</div>
</div>-->
<?php $this->endContent(); ?>