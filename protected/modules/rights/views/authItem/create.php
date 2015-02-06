<?php 

/*	$this->breadcrumbs = array(
	'Rights'=>Rights::getBaseUrl(),
	Rights::t('core', 'Create :type', array(':type'=>Rights::getAuthItemTypeName($_GET['type']))),
);*/

	$this->breadcrumbs = array(
    'Manage Users'=>array('/user/admin'),
    Rights::t('core', 'Manage Roles') => array('/rights/authItem/roles'),      
    Rights::t('core', 'Create Roles'),);  

?>

<div class="createAuthItem">

	<h1 class="page-header"><?php echo Rights::t('core', 'Create :type', array(
		':type'=>Rights::getAuthItemTypeName($_GET['type']),
	)); ?></h1>

	<?php $this->renderPartial('_form', array('model'=>$formModel)); ?>

</div>