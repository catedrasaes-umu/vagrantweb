<?php
//Dialog para mostrar el árbol de snapshots
//el contenido será rellenado mediante ajax por el controlador 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'snapshot-list-dialog',
    'options'=>array(
        'title'=>'Snapshot List',
        'width' => 'auto',
        'min-width' => '500',
        'height'=> 'auto',         
        'autoOpen'=>false,        
    ),
));
?>
<div class="snapshot-list"></div>
<?php	
$this->endWidget('zii.widgets.jui.CJuiDialog');	

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'snapshot-take-dialog',
    'options'=>array(
        'title'=>'Snapshot Information',
        'width' => 'auto',
        'height'=> 'auto',         
        'autoOpen'=>false,        
    ),
));
?>
<div class="snapshot-data"></div>
<?php	
$this->endWidget('zii.widgets.jui.CJuiDialog');



$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'node-status-loading-dialog',
    'options'=>array(
        'title'=>'Loading Node Information',
        'width'=>'auto',
        'height'=>'auto',         
        'autoOpen'=>false,
    ),
));

?>
<div id="dialog-message">
	<p style="text-align: left">This operation could take some time</p>
</div>
<?php



//you must have a animated gif
Yii::app()->clientScript->registerCss('progress-animated',
        '#node-status-loading .ui-progressbar-value{
                background-image:url(images/bar-ani.gif)
                }
        '); 

$this->widget('zii.widgets.jui.CJuiProgressBar', array(
        'id' => 'node-status-loading',         
        'value'=>100, //value in percent
        'htmlOptions'=>array(
                'style'=>'height:20px',
         ),
));

$this->endWidget('zii.widgets.jui.CJuiDialog');


