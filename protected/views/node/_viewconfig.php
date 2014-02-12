<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'vagrant-config-dialog',
    'options'=>array(
        'title'=>'Vagrant Config',
        'width' => 'auto',
        'height'=> 'auto',         
        'autoOpen'=>false,
    ),
));

$highlighter = Yii::createComponent(array(
    'class' => 'application.components.geshi.GeSHiHighlighter',
    'language' => 'ruby',
    'showLineNumbers' => true,
    'containerOptions' => array('style'=>'border: 1px solid #C6C6C6;background-color:#F4F4F4;','id'=>'highlighted-config'),    
));
 
//FIXME TODO aqui se parsearia el contenido de la configuracion
echo $highlighter->highlight($cfile);

echo CHtml::button('Edit Config',array('submit'=>array('node/editconfig'),'params'=>array('id'=>$node,'cfile'=>$cfile)));

// echo $this->renderPartial('_viewconfig', array('node'=>$node,'cfile'=>$cfile)); 

	
$this->endWidget('zii.widgets.jui.CJuiDialog');	