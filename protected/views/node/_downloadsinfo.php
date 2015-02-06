<?php   

  // if (sizeof($downloadsdp->getData())>0) {   
    Yii::app()->clientScript->registerScript(
       'test',       
       'updateDownloads()',      
       CClientScript::POS_LOAD
    );
  // }

?>

<?php

// if (!is_null($downloadsdp))
// {

  $this->widget('booster.widgets.TbGridView', array(
    'id'=>'boxes-download-grid',
    'dataProvider'=>$downloadsdp,
    'selectableRows'=>0,
    'htmlOptions'=>array('class' =>'grid-view table-curved'),
    //'afterAjaxUpdate'=>'function(id, data){ pepe();   }',
    'ajaxUrl'=>Yii::app()->createUrl( 'node/downloadsinfo',array("node" => $model->node_name) ),
    'columns'=>array(
      array(
                    'header'=>'Box Name',                    
                    'type' => 'text',
                    'name'=>'box_name',
                    
             
          ),
      array(
                    'header'=>'Source Url',                    
                    'type' => 'text',
                    'name'=>'box_url',
             
          ),         
          array(
                    'header'=>'Upload Progress',                    
                    'type' => 'text',
                    'name'=>'box_progress',
             
          ),         
          array(  
                    'header'=>'Time Remaining',                    
                    'type' => 'text',
                    'name'=>'box_remaining',
             
          ),         
      
    ),
  ));





  $this->widget('booster.widgets.TbButton',
          array('buttonType' => 'link', 
            'label' => 'Clear',
            'url' => Yii::app()->createUrl("node/deleteboxdownloads", array("node" => $model->node_name)),
            'htmlOptions' => array('id' => 'clearcompleted','style'=>'margin: 20px 0 50px 0;')));


  ?>

  <style>
    
    #boxes-download-grid.grid-view-loading {
      background:none !important;
    }
  </style>


  <script type="text/javascript">



    function updateDownloads()
  {


    /*
    $.ajax({
      type: 'GET',
      
      timeout:0,
      url: "index.php?r=node/downloadsinfo&node="+'<?=$model->node_name?>',
          success:function(data){
            
            console.log(data);        
            

          }
      });*/
    
    

    $.fn.yiiGridView.update('boxes-download-grid'); 
    


    setTimeout(updateDownloads,5000);
    
  }

  </script>

<?php //} ?>