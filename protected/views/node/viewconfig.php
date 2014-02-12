<?php


$this -> breadcrumbs = array('Nodes' => array('index'), $id => array('view', 'id' => $id), 'Edit Node Configuration', );

$this -> menu = array( array('label' => 'List Nodes', 'url' => array('index')),
//array('label'=>'Create Node', 'url'=>array('create')),
array('label' => 'Modify Node', 'url' => array('update', 'id' => $id)), array('label' => 'Delete Node', 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $id), 'confirm' => 'Are you sure you want to delete this item?')),
//array('label'=>'Manage Nodes', 'url'=>array('admin')),
);

?>

<h1>Node: <?php echo $id; ?></h1>

<?php

$highlighter = Yii::createComponent(array(
    'class' => 'application.components.geshi.GeSHiHighlighter',
    'language' => 'ruby',
    'showLineNumbers' => true,
    'containerOptions' => array('style'=>'border: 1px solid #C6C6C6;background-color:#F4F4F4;','id'=>'highlighted-config'),    
));
 
//FIXME TODO aqui se parsearia el contenido de la configuracion
echo $highlighter->highlight($cfile);

echo CHtml::button('Edit Config',array('submit'=>array('node/editconfig'),'params'=>array('id'=>$id,'cfile'=>$cfile)));

	
	