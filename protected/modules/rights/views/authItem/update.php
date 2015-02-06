<?php $this->breadcrumbs = array(
	'Rights'=>Rights::getBaseUrl(),
	Rights::getAuthItemTypeNamePlural($model->type)=>Rights::getAuthItemRoute($model->type),
	$model->name,
); ?>



<div id="updatedAuthItem">

	<h2><?php echo Rights::t('core', 'Update :name', array(
		':name'=>$model->name,
		':type'=>Rights::getAuthItemTypeName($model->type),
	)); ?></h2>

	<?php $this->renderPartial('_form', array('model'=>$formModel)); ?>

	<?php if ($model->type==CAuthItem::TYPE_ROLE){ ?>	

		<div class="relations">

			<h3><?php echo Rights::t('core', 'Relations'); ?></h3>

			<?php if( $model->name!==Rights::module()->superuserName ): ?>

				<div class="parents col-lg-6 col-md-6 col-sm-12 col-xs-12" style="padding-left:0">

					<h4><?php echo Rights::t('core', 'Parents'); ?></h4>

					<?php $this->widget('booster.widgets.TbGridView', array(
						'dataProvider'=>$parentDataProvider,
						'template'=>'{items}',
						'hideHeader'=>true,
						'emptyText'=>Rights::t('core', 'This item has no parents.'),
						'htmlOptions'=>array('class'=>'grid-view  table-curved'),
						'columns'=>array(
	    					array(
	    						'name'=>'name',
	    						'header'=>Rights::t('core', 'Name'),
	    						'type'=>'raw',
	    						'htmlOptions'=>array('class'=>'name-column'),
	    						'value'=>'$data->getNameLink()',
	    					),
	    					// array(
	    					// 	'name'=>'type',
	    					// 	'header'=>Rights::t('core', 'Type'),
	    					// 	'type'=>'raw',
	    					// 	'htmlOptions'=>array('class'=>'type-column'),
	    					// 	'value'=>'$data->getTypeText()',
	    					// ),
	    					array(
	    						'header'=>'&nbsp;',
	    						'type'=>'raw',
	    						'htmlOptions'=>array('class'=>'actions-column'),
	    						'value'=>'$data->getRemoveChildLink()',
	    					),
						)
					)); ?>

				</div>

				<div class="children col-lg-6 col-md-6 col-sm-12 col-xs-12">

					<h4><?php echo Rights::t('core', 'Children'); ?></h4>

					<?php $this->widget('booster.widgets.TbGridView', array(
						'dataProvider'=>$childDataProvider,
						'template'=>'{items}',
						'hideHeader'=>true,
						'emptyText'=>Rights::t('core', 'This item has no children.'),
						'htmlOptions'=>array('class'=>'grid-view  table-curved'),
						'columns'=>array(
	    					array(
	    						'name'=>'name',
	    						'header'=>Rights::t('core', 'Name'),
	    						'type'=>'raw',
	    						'htmlOptions'=>array('class'=>'name-column'),
	    						'value'=>'$data->getNameLink()',
	    					),
	    					// array(
	    					// 	'name'=>'type',
	    					// 	'header'=>Rights::t('core', 'Type'),
	    					// 	'type'=>'raw',
	    					// 	'htmlOptions'=>array('class'=>'type-column'),
	    					// 	'value'=>'$data->getTypeText()',
	    					// ),
	    					array(
	    						'header'=>'&nbsp;',
	    						'type'=>'raw',
	    						'htmlOptions'=>array('class'=>'actions-column'),
	    						'value'=>'$data->getRemoveParentLink2()',
	    					),
						)
					)); ?>

				</div>

				<div class="addChild col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-left:0;margin-top:20px">

					<div class="addChild col-lg-6 col-md-6 col-sm-6 col-xs-6" style="padding-left:0;padding-right:0">
						<h4><?php echo Rights::t('core', 'Add Parent Role'); ?></h4>

						<?php if( $childFormModel!==null ): ?>

							<?php $this->renderPartial('_parentForm', array(
								'model'=>$parentFormModel,
								'itemnameSelectOptions'=>$parentSelectOptions,
							)); ?>

						<?php else: ?>

							<p class="info"><?php echo Rights::t('core', 'No parent available to be added to this item.'); ?>

						<?php endif; ?>
					</div>

					<div class="addChild col-lg-6 col-md-6 col-sm-6 col-xs-6" style="padding-left:25px;padding-right:0">
						<h4><?php echo Rights::t('core', 'Add Child Role'); ?></h4>

						<?php if( $childFormModel!==null ): ?>

							<?php $this->renderPartial('_childForm', array(
								'model'=>$childFormModel,
								'itemnameSelectOptions'=>$childSelectOptions,
							)); ?>

						<?php else: ?>

							<p class="info"><?php echo Rights::t('core', 'No children available to be added to this item.'); ?>

						<?php endif; ?>
					</div>

				</div>

			<?php else: ?>

				<p class="info">
					<?php echo Rights::t('core', 'No relations need to be set for the superuser role.'); ?><br />
					<?php echo Rights::t('core', 'Super users are always granted access implicitly.'); ?>
				</p>

			<?php endif; ?>

		</div>
	<?php } ?>


</div>

<?php if ($model->type==CAuthItem::TYPE_ROLE){ ?>	

<?php if (!empty($parentOperationDataProvider->getData())){ ?>

<div class="relations col-lg-6 col-md-6 col-sm-6 col-xs-12" style="padding-left:0">

	<h3><?php echo Rights::t('core', 'Operations Inherited by Parent Roles'); ?></h3>

	<?php $this->widget('booster.widgets.TbGridView', array(
					'dataProvider'=>$parentOperationDataProvider,
					'template'=>'{items}',
					'hideHeader'=>true,
					'selectableRows'=>0,					
					'emptyText'=>Rights::t('core', 'This item has no operations inherited.'),
					'htmlOptions'=>array('class'=>'grid-view  table-curved'),
					'columns'=>array(
    					array(
    						'name'=>'id',
    						'header'=>Rights::t('core', 'Name'),
    						'type'=>'raw',
    						'htmlOptions'=>array('class'=>'name-column'),    						    						
    						'value'=>'CHtml::link($data["desc"], array("authItem/update","name"=>$data["id"]))'

    					),    					
					)
				)); ?>


</div>

<div class="relations col-lg-6 col-md-6 col-sm-6 col-xs-12" style="padding-left:0">
<?php }else{ ?>
<div class="relations col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-left:0">
<?php } ?>




	<h3><?php echo Rights::t('core', 'Operations Assigned'); ?></h3>

	<?php $this->widget('booster.widgets.TbGridView', array(
					'dataProvider'=>$operationDataProvider,
					'template'=>'{items}',
					'hideHeader'=>true,
					'emptyText'=>Rights::t('core', 'This item has no children.'),
					'htmlOptions'=>array('class'=>'grid-view  table-curved'),
					'columns'=>array(
    					array(
    						'name'=>'name',
    						'header'=>Rights::t('core', 'Name'),
    						'type'=>'raw',
    						'htmlOptions'=>array('class'=>'name-column'),
    						'value'=>'$data->getDescriptionLink()',
    						//'value'=>'$data->getNameLink()',
    					),
    					// array(
    					// 	'name'=>'type',
    					// 	'header'=>Rights::t('core', 'Type'),
    					// 	'type'=>'raw',
    					// 	'htmlOptions'=>array('class'=>'type-column'),
    					// 	'value'=>'$data->getTypeText()',
    					// ),
    					array(
    						'header'=>'&nbsp;',
    						'type'=>'raw',
    						'htmlOptions'=>array('class'=>'actions-column'),
    						'value'=>'$data->getRemoveChildLink()',
    					),
					)
				)); ?>


</div>


<div class="addChild col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-left:0;margin-top:20px">


	<h4><?php echo Rights::t('core', 'Add Operation'); ?></h4>

	<?php if( $childFormModel!==null ): ?>

		<?php $this->renderPartial('_childForm', array(
			'model'=>$parentFormModel,
			'itemnameSelectOptions'=>$operationSelectOptions,
		)); ?>

	<?php else: ?>

		<p class="info"><?php echo Rights::t('core', 'No operation available to be added to this item.'); ?>

	<?php endif; ?>
	

</div>

<?php } ?>