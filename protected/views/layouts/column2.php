<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>
<div class="col-lg-2" style="display:none">
	 <div id="sidebar">
	 <?php
		$this->beginWidget('zii.widgets.CPortlet', array(
			'title'=>'Configuration',
		));
		$this->widget('booster.widgets.TbMenu', array(
			'type' => 'list',
			'items'=>array(
				array(
                'label' => 'Configuration',
                'itemOptions' => array('class' => 'nav-header')
            	),
				array('label'=>'Groups', 'url'=>array('/project/index')),
				'',
				array('label'=>'Nodes', 'url'=>array('/node/index')),
				'',
				array('label'=>'Users', 'url'=>array('/user/admin')),				
			),
			'htmlOptions'=>array('class'=>'operations'),
		));
		$this->endWidget();



		$this->widget('booster.widgets.TbMenu', 
			array(
			'type' => 'list',
			'items'=>array(
				array(
                'label' => 'Configuration',
                'itemOptions' => array('class' => 'nav-header')
            	),
				array('label'=>'Groups', 'url'=>array('/project/index')),
				'',
				array('label'=>'Nodes', 'url'=>array('/node/index')),
				'',
				array('label'=>'Users', 'url'=>array('/user/admin')),				
			),			
		));

	?>
	
	<?php
		$this->beginWidget('zii.widgets.CPortlet', array(
			'title'=>'Operations',
		));
		$this->widget('booster.widgets.TbMenu', array(
			'type' => 'list',
			'items'=>$this->menu,
			'htmlOptions'=>array('class'=>'operations'),
		));
		$this->endWidget();
	?>
	</div>
 </div>

<!-- <div class="col-lg-12">	 -->
	<div id="content">		
		<?php echo $content; ?>
	</div><!-- content -->
<!-- </div> -->
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