<?php



$this->breadcrumbs=array(
    'Nodes'=>array('index'),
    $node => array('view','id'=>$node),
//    $node->node_name,
    'Config'
);
?>

<h1 class="page-header" style="text-transform:capitalize"><?php echo strtolower($node); ?>&nbsp;Configuration File</h1>

<?php

// $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
//     'id'=>'vagrant-config-dialog',
//     'options'=>array(
//         'title'=>'Vagrant Config',
//         'width' => 'auto',
//         'height'=> 'auto',         
//         'autoOpen'=>false,
//     ),
// ));

//$form=$this->beginWidget('CActiveForm');

$highlighter = Yii::createComponent(array(
    'class' => 'application.components.geshi.GeSHiHighlighter',
    'language' => 'ruby',
    'showLineNumbers' => true,
    'containerOptions' => array('style'=>'border: 1px solid #C6C6C6;background-color:#F4F4F4;','id'=>'highlighted-config'),    
));
 
//FIXME TODO aqui se parsearia el contenido de la configuracion
echo $highlighter->highlight($cfile);

//echo CHtml::button('Edit Config',array('submit'=>array('node/editconfig'),'params'=>array('id'=>$node,'cfile'=>$cfile)));





$this->widget(
                'booster.widgets.TbButton',
                array('buttonType' => 'submit', 
                    'label' => 'Edit Config',
                    'htmlOptions' => array('submit'=>Yii::app()->createUrl("node/editconfig"),
                                            'style'=>'margin-right:20px',
                                            'params'=>array('id'=>$node,'cfile'=>$cfile))
            ));


$this->widget(
                'booster.widgets.TbButton',
                array('buttonType' => 'submit', 
                    'label' => 'Cancel',
                    'htmlOptions' => array('submit'=>Yii::app()->createUrl("node/view",
                                                                            array('id'=>$node)))
            ));


// echo $this->renderPartial('_viewconfig', array('node'=>$node,'cfile'=>$cfile)); 

	
//$this->endWidget('CActiveForm');	