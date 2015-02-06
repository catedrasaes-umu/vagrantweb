<?php 
 //    $this->breadcrumbs = array(
	// 'Rights'=>Rights::getBaseUrl(),
	// Rights::t('core', 'Roles'),

    $this->breadcrumbs = array(
    'Manage Users'=>array('/user/admin'),    
    Rights::t('core', 'Manage Roles'),); 

?>

<div id="roles">

	<h1 class="page-header"><?php echo Rights::t('core', 'Roles'); ?></h1>

	<p>
		<?php //echo Rights::t('core', 'A role is group of permissions to perform a variety of tasks and operations, for example the authenticated user.'); ?><br />
		<?php //echo Rights::t('core', 'Roles exist at the top of the authorization hierarchy and can therefore inherit from other roles, tasks and/or operations.'); ?>
	</p>

	<p><?php //echo CHtml::link(Rights::t('core', 'Create a new role'), array('authItem/create', 'type'=>CAuthItem::TYPE_ROLE), array(
	   	//'class'=>'add-role-link',
	//)); ?></p>

	<?php $this->widget('booster.widgets.TbGridView', array(
	    'dataProvider'=>$dataProvider,
	    'template'=>'{items}',
        'selectableRows'=>0,
	    'emptyText'=>Rights::t('core', 'No roles found.'),
	    'htmlOptions'=>array('class'=>'grid-view table-curved'),
	    'columns'=>array(
    		array(
    			'name'=>'name',
    			'header'=>Rights::t('core', 'Name'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'name-column'),
    			'value'=>'($data->name=="AdminRole")?"AdminRole":$data->getGridNameLinkSimple()',
                //'value'=>'$data->getGridNameLink()',
    		),
    		array(
    			'name'=>'description',
    			'header'=>Rights::t('core', 'Description'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'description-column'),
    		),
    		// array(
    		// 	'name'=>'bizRule',
    		// 	'header'=>Rights::t('core', 'Business rule'),
    		// 	'type'=>'raw',
    		// 	'htmlOptions'=>array('class'=>'bizrule-column'),
    		// 	'visible'=>Rights::module()->enableBizRule===true,
    		// ),
    		/*array(
    			'name'=>'data',
    			'header'=>Rights::t('core', 'Data'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'data-column'),
    			'visible'=>Rights::module()->enableBizRuleData===true,
    		),*/
    		/*array(
    			'header'=>'&nbsp;',
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'actions-column','style'=>'text-align:center'),
    			'value'=>'$data->getDeleteRoleLink()',
    		),*/
            array(

                'class'=>'booster.widgets.TbButtonColumn',
                'header' => 'Options',
                // 'template' => '{view}{destroy}{delete}',
                'template' => '{delete}',
                'buttons'=> array
                (
                    'delete' => array(
                        'visible' => '($data->name=="AdminRole")?false:true',
                    ),
                ),
                'deleteButtonUrl'=>'Yii::app()->createUrl("/rights/authItem/delete", array("name" => $data->name))',
                
  
            ),
	    )
	)); ?>

	<!--<p class="info" style="margin-top:20px"><?php //echo Rights::t('core', 'Values within square brackets tell how many children each item has.'); ?></p>-->
    <?php
    $this->widget(
                'booster.widgets.TbButton',
                array('buttonType' => 'submit', 
                    'label' => 'Create Role',
                    'htmlOptions' => array('submit'=>Yii::app()->createUrl('rights/authItem/create',array('type'=>CAuthItem::TYPE_ROLE)),                                          
                                            'style'=>'margin: 20px 20px 30px 0;'))
            );
    ?>
</div>