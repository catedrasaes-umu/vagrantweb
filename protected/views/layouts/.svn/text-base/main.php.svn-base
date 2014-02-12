<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
	
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/customize.css" />
	
	

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="page">

	<div id="header">
		<div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
	</div><!-- header -->

	<div id="mainmenu">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Home', 'url'=>array('/node/index')),
				array('label'=>'About', 'url'=>array('/site/page', 'view'=>'about')),
				array('label'=>'Contact', 'url'=>array('/site/contact')),
				array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest)
			),
		)); ?>
	</div><!-- mainmenu -->
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>
	
	
	<?php echo $content; ?>
	<div class="clear"></div>
	<div class="span-18 last append-1 prepend-5">
	<? echo CHtml::textArea('operation_log', "",array('style' => 'display:none;width:100%;height:300px;margin-top:15px;resize:none;cursor:default','readonly'=>'true'));?>
	</div>
	
	<div class="clear"></div>

	<div id="footer">
		Copyright &copy; <?php echo date('Y'); ?> by My Company.<br/>
		All Rights Reserved.<br/>
		<?php echo Yii::powered(); ?>
	</div><!-- footer -->

</div><!-- page -->

</body>

 
<script type="text/javascript">
function queryPendingOperations()
{	
	$.ajax({
		type: 'GET',
		timeout:0, //No timeout
		url: '<?=Yii::app()->createUrl("operation/queryoperations")?>',        		
		success:function(data) {
			  
			  var result=jQuery.parseJSON(data);
			  if (result.length!==0)
			  {
			  	
			  	var op_code=result[0]["operation_result"];
			  	var textinicio=($('#operation_log').text().length==0)?"":$('#operation_log').text()+"\n";
			  	if (op_code==200)
			  	{
			  	 
			  	 $('#operation_log').text(textinicio+"La operación \""+result[0]["operation_id"]+"\" ha terminado con EXITO");
			  	 //Únicamente cuando la operación acaba satisfactoriamente entonces lanzamos el evento
			  	 $.event.trigger("updateAsync",result);		
			  	}else{			  	
		  		 $('#operation_log').text(textinicio+"La operación \""+result[0]["operation_id"]+"\" ha fallado, error: \""+result[0]["operation_msg"]+"\"");
			  	}		  	
			  	
			  	
			  }		    
			         						
               
               
        },
        error:function(x, t, m) {    	
        	    console.log(x.responseText);            	
        	if (t=="timeout")
        	{                		
        		$("#flash-messages").addClass("flash-error").html("Request Timeout Error").fadeIn().delay(3000).fadeOut("slow");
        	}else{
        		if (x.responseText.length!=0)
        		{
        			//alert("FIXME: ERROR "+x.responseText);
        			$("#flash-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(5000).fadeOut("slow");
        		}
        	}
        }
    })

	
	setTimeout( queryPendingOperations, 10000 );	
}
function activateOperationPolling()
{
	setTimeout(queryPendingOperations,10000);
}
//Funciones para gestionar el log
$( document ).ready(function() {
	activateOperationPolling();
	// $(document).on("updateAsync",function(event,params){
		// // alert("TRIGGEADO");	
		// console.log("TOY EN MAIN");
	// });
	//10001alert("EN MAIN");
});	

</script>
</html>
