<div class="form">

<?php 

	$backurl = Yii::app()->createUrl("/rights/authItem/roles");

	if (isset($_SERVER['HTTP_REFERER'])){

		$array_url=parse_url($_SERVER['HTTP_REFERER']); 
		$query_url=$array_url["query"];
		

		foreach ( explode("&",$query_url) as $gparameter) {					
			$aux = explode("=",$gparameter);
			
			if (($aux[0]=="r") && ($aux[1]=="user/view")){				
				$backurl = $_SERVER['HTTP_REFERER'];
				break;
			}

		 };

	}

	
		

?>

<?php if( $model->scenario==='update' ): ?>

	<h3><?php echo Rights::getAuthItemTypeName($model->type); ?></h3>

<?php endif; ?>
	
<?php $form=$this->beginWidget('booster.widgets.TbActiveForm',array(
		'id'=>'role-form',
		'htmlOptions' => array('autocomplete'=>'off','class' => 'well'), // for inset effect
		)); ?>

	<?php if (isset($_SERVER['HTTP_REFERER'])){ ?>
		<input name="rurl" type="hidden" value="<?=$backurl?>"/>
	<?php } ?>

	<div class="row">		
		<?php echo $form->labelEx($model, 'name'); ?>
		<?php
			//$readonly= (($model->type==CAuthItem::TYPE_ROLE)?false:true);			
			$readonly= ((is_null($model->type))?false:true);
			
			
	 		echo $form->textField($model, 'name', array('maxlength'=>255, 'class'=>'text-field','readonly'=>$readonly)); 
			echo $form->error($model, 'name'); 
		
		?>
		<!--<p class="hint"><?php //echo Rights::t('core', 'Do not change the name unless you know what you are doing.'); ?></p>-->
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'description'); ?>
		<?php echo $form->textField($model, 'description', array('maxlength'=>255, 'class'=>'text-field')); ?>
		<?php echo $form->error($model, 'description'); ?>
		<p class="hint"><?php echo Rights::t('core', 'A descriptive name for this item.'); ?></p>
	</div>

	<?php if( 1==2 && Rights::module()->enableBizRule===true ): ?>

		<div class="row">
			<?php echo $form->labelEx($model, 'bizRule'); ?>
			<?php echo $form->textField($model, 'bizRule', array('maxlength'=>255, 'class'=>'text-field')); ?>
			<?php echo $form->error($model, 'bizRule'); ?>
			<p class="hint"><?php echo Rights::t('core', 'Code that will be executed when performing access checking.'); ?></p>
		</div>

	<?php endif; ?>
			
	<?php if( Rights::module()->enableBizRule===true && Rights::module()->enableBizRuleData ): ?>

		<div class="row">
			<?php echo $form->labelEx($model, 'data'); ?>
			<?php echo $form->textField($model, 'data', array('maxlength'=>255, 'class'=>'text-field')); ?>
			<?php echo $form->error($model, 'data'); ?>
			<p class="hint"><?php echo Rights::t('core', 'Additional data available when executing the business rule.'); ?></p>
		</div>

	<?php endif; ?>

	<div class="row buttons" style="margin-top:20px">

		<?php
		$this->widget(
			    'booster.widgets.TbButton',
			    array('buttonType' => 'submit', 'label' => 'Save')
			);
		?>
		
		<?php //echo CHtml::Button('Cancel',array('submit'=>Yii::app()->createUrl("user/admin"))); ?>

		

		<?php
		$this->widget(
			    'booster.widgets.TbButton',
			    array('buttonType' => 'submit', 'label' => 'Cancel','htmlOptions' => array('submit'=>$backurl))
			);
		?>

		<?php //echo CHtml::submitButton(Rights::t('core', 'Save')); ?> <?php //echo CHtml::link(Rights::t('core', 'Cancel'), Yii::app()->user->rightsReturnUrl); ?>
	</div>

<?php $this->endWidget(); ?>

</div>