<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	 <meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
    
    <script src="recursos/js/jquery-1.10.2.js"></script>
    

	<!-- <link rel="stylesheet" type="text/css" href="recursos/css/bootstrap.css"> -->

	<!-- Core CSS - Include with every page -->    
	<link rel="stylesheet" type="text/css" media="screen, print, projection" href="recursos/css/bootstrap.min.css">
    <link href="recursos/font-awesome/css/font-awesome.css" media="screen, print, projection" rel="stylesheet">

    <!-- Page-Level Plugin CSS - Dashboard -->
    <link href="recursos/css/plugins/morris/morris-0.4.3.min.css" media="screen, print, projection" rel="stylesheet">
    <link href="recursos/css/plugins/timeline/timeline.css" media="screen, print, projection" rel="stylesheet">

    <!-- SB Admin CSS - Include with every page -->
    <link href="recursos/css/sb-admin.css" rel="stylesheet">
	
	<link rel="stylesheet" type="text/css" href="css/customize.css" /> 

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<?php 
    date_default_timezone_set("Europe/Madrid");

	if( ! ini_get('date.timezone') )
	{
    date_default_timezone_set('GMT');
	}
?>
<body>


<div id="wrapper">

        <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0">
            
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?=Yii::app()->createUrl("site/controlpanel")?>">VagrantWeb</a>
            </div>
            
            <!-- /.navbar-header -->

            <?php if (!Yii::app()->user->isGuest) { ?>

                <ul class="nav navbar-top-links navbar-right">                
                    <!-- /.dropdown -->
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            
                            <li><a href="<?=Yii::app()->createUrl("site/logout")?>"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                            </li>
                        </ul>
                        <!-- /.dropdown-user -->
                    </li>
                    <!-- /.dropdown -->
                </ul>
                <!-- /.navbar-top-links -->

                <div class="navbar-default navbar-static-side" role="navigation">
                    <div class="sidebar-collapse">
                        <ul class="nav" id="side-menu">
                           
                            <?php echo $this->getSideMenu(); ?>
                            
                        </ul>
                    </div>                
                </div>
            <?php } ?>
            
        </nav>
		
		<?php 

			$clase="guest";

			if (!Yii::app()->user->isGuest) 
				$clase="";
		?>
		
		
		<div id="page-wrapper" class="<?=$clase?> ">
		<div class="container-fluid">
		<div class="col-lg-12 col-md-12 col-sm-12 ">
		<div class="breadcrumbframe">
		<?php if(isset($this->breadcrumbs)): ?>
		<?php $this->widget('booster.widgets.TbBreadcrumbs', array(			
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
		<?php endif ?>
		</div>
        
		<?php echo $content; ?>
		
        </div>
        </div>
		</div>
        <div class="clear"></div>

		<div id="footer">
			Created by <?php //echo date('Y'); ?> by CatedraSAES-UMU<br/>
			<!--All Rights Reserved.<br/>-->
			<?php //echo Yii::powered(); ?>
		</div>




</div> <!-- wrapper -->






<!-- Core Scripts - Include with every page -->
    
    <script src="recursos/js/bootstrap.min.js"></script>
    <script src="recursos/js/plugins/metisMenu/jquery.metisMenu.js"></script>

    <!-- Page-Level Plugin Scripts - Dashboard -->
    <!--<script src="recursos/js/plugins/morris/raphael-2.1.0.min.js"></script>
    <script src="recursos/js/plugins/morris/morris.js"></script>-->

    <!-- SB Admin Scripts - Include with every page -->
    <script src="recursos/js/sb-admin.js"></script>

    
<script type="text/javascript">
function queryPendingOperations()
{   
    $.ajax({
        type: 'GET',
        timeout:0, //No timeout
        url: '<?php echo Yii::app()->createUrl("operation/queryoperations")?>',             
        success:function(data) {
              
              var result=jQuery.parseJSON(data);
              if (result.length!==0)
              {
                
                var op_code=result[0]["operation_result"];
                
                if (op_code==200)
                {                
                 
                 //Únicamente cuando la operación acaba satisfactoriamente entonces lanzamos el evento
                 $.event.trigger("updateAsync",result);                    

                }          
                
                
              }         
                                            
               
               
        },
        error:function(x, t, m) {                       

            if (t=="timeout")
            {                       
                $("#flash-messages").addClass("flash-error").html("Request Timeout Error").fadeIn().delay(3000).fadeOut("slow");
            }else{
                if (x.responseText.length!=0)
                {                    
                    $("#flash-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(5000).fadeOut("slow");
                }
            }
        }
    })

    $.event.trigger("operationPolling");   

    
    setTimeout( queryPendingOperations, 10000 );    
}
function activateOperationPolling()
{
    setTimeout(queryPendingOperations,10000);
}

$( document ).ready(function() {

 
    <?php if( Yii::app()->getUser()->isGuest===false || $this->getUniqueId()!="installer/default") { ?>
        activateOperationPolling();
    <?php } ?>
    
    
}); 

</script>

</body>
</html>

