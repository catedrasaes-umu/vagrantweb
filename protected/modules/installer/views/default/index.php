<?php
$this->breadcrumbs=array(
	$this->module->id,
);
?>

<style>
	form {
		padding-top:25px !important;
		padding-bottom:25px !important;
	}
	.form-horizontal .control-label{
		padding-top:3px;
		margin-left:0;
		text-align:left;
	}

	.form-group {
		margin-bottom:42px;
		margin-left:0;
		padding-left:0;
	}

	.form-horizontal .form-group {
		margin-left:0;
	}

	div#icontainer {
		padding-left:0;
	}	

	.form-group input {
		width:60%;
	}

	form button {
		margin-top:15px;
	}

	div.well {
		font-weight: bold;
		/*text-transform: uppercase;*/
		letter-spacing: 2px;
		font-size: 18px;
		cursor:default;
	}
	.well.default {
		color:#6D6D6D;
	}

	.well.success {
		color: #3C763D;
		background-color: #DFF0D8;
		border-color: #D6E9C6;
	}

	.well.error {
		color: #A94442;
		background-color: #F2DEDE;
		border-color: #EBCCD1;

	}

	.form-control {
    	height: 32px;
	}

	.well-sm {
		padding:0;
	}

	span.glyphicon {
		float: right;
		padding-right:15px;
		font-size: 25px;
	}

	.panel-body {
		background-color:white;
		font-weight: normal;
		font-size: 15px;
		color:black;
		letter-spacing: 0;
	}

	div#result {
		text-align: center;
		display:table;
		margin-left:auto;
		margin-right:auto;
		padding-left:30px;
		padding-right:30px;
		margin-top:45px;
		margin-bottom:20px;
		border-width: 5px;
	}


</style>

<h1>Vagrant Web Installation page</h1>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-left:0;padding-right:0;margin-top:20px">
	<form  class="well" id="installf" role="form" autocomplete="off">
		
		<div class="form-group">
		    <label for="bbddpuser" class="control-label">MySql Privilege User:</label>
		    <div class="col-lg-12 col-md-5 col-sm-12 col-xs-12" id="icontainer">
		      <input type="text" class="form-control" id="bbddpuser" placeholder="Insert database privilege user">
		    </div>
	  	</div>
	  	
	  	<div class="form-group">
		    <label for="bbddppassword" class="control-label">MySql Privilege User Password:</label>
		    <div class="col-lg-12 col-md-5 col-sm-12 col-xs-12" id="icontainer">
		      <input type="password" class="form-control" id="bbddppassword" placeholder="Insert database privilege user password">
		    </div>
	  	</div>

		<div class="form-group">
		    <label for="bbddname" class="control-label">Database Name:</label>
		    <div class="col-lg-12 col-md-5 col-sm-12 col-xs-12" id="icontainer">
		      <input type="text" class="form-control" id="bbddname" placeholder="Insert database name to create">
		    </div>
	  	</div>

	  	<div class="form-group">
		    <label for="bbdduser" class="control-label">Database User: (leave blank to use the above user)</label>
		    <div class="col-lg-12 col-md-5 col-sm-12 col-xs-12" id="icontainer">
		      <input type="text" class="form-control" id="bbdduser" placeholder="Insert database user">
		    </div>
	  	</div>
	  	
	  	<div class="form-group">
		    <label for="bbddpassword" class="control-label">Database User Password: (leave blank to use the above password)</label>
		    <div class="col-lg-12 col-md-5 col-sm-12 col-xs-12" id="icontainer">
		      <input type="password" class="form-control" id="bbddpassword" placeholder="Insert database user password">
		    </div>
	  	</div>
	  	
	  	
	  	<button type="submit" disabled="disabled" class="btn btn-default">Submit</button>
		
	</form>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="steps" style="display:none;padding-left:0;padding-right:0;margin-top:20px">	

	<div class="panel well default well-sm" id="fpermissions">
		<div class="panel-heading">Checking folder permissions</div>
	</div>

	<div class="panel well default well-sm" id="dbms">
		<div class="panel-heading">Checking if MySQL is installed</div>
	</div>

	<div class="panel well default well-sm" id="connection">
		<div class="panel-heading">Testing connection to MySql</div>
	</div>

	<div class="panel well default well-sm" id="cdb">
		<div class="panel-heading">Creating database</div>	
	</div>

	<div class="panel well default well-sm" id="structure">
		<div class="panel-heading">Creating table structure</div>
	</div>

	<div class="panel well default well-sm" id="newuser" style="display:none">
        <div class="panel-heading">Creating new user</div>
    </div>

    <div class="panel well default well-sm" id="newusertest">
            <div class="panel-heading">Testing connection with new user</div>
    </div>

	<div class="panel well default well-sm" id="result" style="	"></div>
	
	<div id="redirect" style="display:none;text-align:center;margin-bottom:60px;">
		You will be redirected to the home page in a few seconds...
	</div>

</div>


<script>
	
	function redirect()
	{
		window.location='<?php echo Yii::app()->homeUrl; ?>';
	}

	function setSuccess(ob,msg) {
		ob.parent().removeClass("default").addClass("success");
		ob.append('<span class="glyphicon glyphicon-ok"></span>');		
		
		
		if (typeof msg!=='undefined'){
			ob.parent().append('<div class="panel-body">'+msg+'</div>');
		}
	}

	function setError(ob,msg){
		ob.removeClass("default").addClass("error");
		ob.find("div.panel-heading").append('<span class="glyphicon glyphicon-remove"></span>');
		ob.append('<div class="panel-body">'+msg+'</div>');

		$("#result").show();	
		$("#result").removeClass("default").addClass("error");
		$("#result").append('<div class="panel-heading">Installation Failed</div>');
	}

	function finishInstall()
	{
		$.ajax({
        		type: 'GET',
        		timeout:0, //No timeout
        		url: '<?php echo Yii::app()->createUrl("installer/default/finishinstall"); ?>',        		        		
        });
	}

	function createStructure()
	{
		url= '<?php echo Yii::app()->createUrl("installer/default/createstructure"); ?>';
		
		url=url+"&bbdd="+$("input#bbddname").val()+"&puser="+$("input#bbddpuser").val()+"&ppass="+$("input#bbddppassword").val();
			
			
			$.ajax({
        		type: 'GET',
        		timeout:0, //No timeout
        		url: url,        		
        		success:function(data) {

        			if(data.status == 'success'){
						setSuccess($("#structure div.panel-heading"));

						if ($("input#bbdduser").val().length>0 && $("input#bbddpassword").val().length>0)
						{
							setTimeout(createNewUser, 2000);        
						}else{ 
							finishInstall();
							$("#result").show();    
							$("#result").removeClass("default").addClass("success");
							$("#result").append('<div class="panel-heading">Installation Successful</div>');
							$("#redirect").show();    
							setTimeout(redirect, 5000);        

						}

				    }else if(data.status == 'error'){				    	
				        setError($("#structure"),data.msg);
				    } 
        			  
                       
                },
                error:function(x, t, m) {  

                	setError($("#structure"),x.responseText);
                	
                }
            });
	}

	function checkConnectionUser(){
		$.ajax({
        		type: 'GET',
        		timeout:0, //No timeout
        		url: '<?php echo Yii::app()->createUrl("installer/default/checkConnectionUser"); ?>',        		
        		success:function(data) {


        			if(data.status == 'success'){
						setSuccess($("#newusertest div.panel-heading"));

						finishInstall();						
						$("#result").show();    
						$("#result").removeClass("default").addClass("success");
						$("#result").append('<div class="panel-heading">Installation Successful</div>');
						$("#redirect").show();    
						setTimeout(redirect, 5000);        
					}else if(data.status == 'error'){				    	
				        setError($("#newusertest"),data.msg);
				    }        			 
				    
                       
                },
                error:function(x, t, m) {  

                	setError($("#newusertest"),x.responseText);
                	
                }
            });

	}
	function createNewUser()
	{
		url= '<?php echo Yii::app()->createUrl("installer/default/createuser"); ?>';
		
		url=url+"&newuser="+$("input#bbdduser").val()+"&newpass="+$("input#bbddpassword").val();
		$.ajax({
        		type: 'GET',
        		timeout:0, //No timeout
        		url: url,        		
        		success:function(data) {
					
					 if(data.status == 'success'){
				        setSuccess($("#newuser div.panel-heading"),data.msg);        			  
				        setTimeout(checkConnectionUser, 2000); 
				    }else if(data.status == 'error'){				    	
				        setError($("#newuser"),data.msg);
				    } 
					
					
					
                       
                },
                error:function(x, t, m) {  

                	setError($("#newuser"),x.responseText);
                	
                }
            });
	}

	function createDatabase() {
		url= '<?php echo Yii::app()->createUrl("installer/default/createdb"); ?>';
		
		url=url+"&bbdd="+$("input#bbddname").val()+"&puser="+$("input#bbddpuser").val()+"&ppass="+$("input#bbddppassword").val()
			+"&user="+$("input#bbdduser").val()+"&pass="+$("input#bbddpassword").val();
		$.ajax({
        		type: 'GET',
        		timeout:0, //No timeout
        		url: url,
        		success:function(data) {

        			if(data.status == 'success'){
				        setSuccess($("#cdb div.panel-heading"));
        			  
						
						setTimeout(createStructure, 2000); 	
						

				    }else if(data.status == 'error'){				    	
				        setError($("#cdb"),data.msg);
				    } 
        			  
        			  
        			  
                       
                },
                error:function(x, t, m) {  

                	setError($("#cdb"),x.responseText);
                	
                }
            });
	}

	function checkConnection() {
		url= '<?php echo Yii::app()->createUrl("installer/default/checkConnection"); ?>';
		url=url+"&user="+$("input#bbddpuser").val()+"&pass="+$("input#bbddppassword").val();
		$.ajax({
        		type: 'GET',
        		timeout:0, //No timeout
        		url: url,
        		success:function(data) {

        			 if(data.status == 'success'){
				        setSuccess($("#connection div.panel-heading"));
				        setTimeout(createDatabase, 2000); 
				    }else if(data.status == 'error'){				    	
				        setError($("#connection"),data.msg);
				    } 
        			  
                       
                },
                error:function(x, t, m) {  

                	setError($("#connection"),x.responseText);
                	
                }
            });
	}

	function checkDBMS() {
		$.ajax({
        		type: 'GET',
        		timeout:0, //No timeout
        		url: '<?php echo Yii::app()->createUrl("installer/default/checkdbms"); ?>',        		
        		success:function(data) {

        			 if(data.status == 'success'){
				        setSuccess($("#dbms div.panel-heading"));
				        setTimeout(checkConnection, 2000); 
				    }else if(data.status == 'error'){				    	
				        setError($("#dbms"),data.msg);
				    } 

        			  
        			  
        			  
                       
                },
                error:function(x, t, m) {  

                	setError($("#dbms"),x.responseText);
                	
                }
            });
	}

	function checkFolderPermissions() {
		 $.ajax({
        		type: 'GET',
        		timeout:0, //No timeout
        		url: '<?php echo Yii::app()->createUrl("installer/default/checkpermissions"); ?>',        		
        		success:function(data) {
        			
        			if(data.status == 'success'){
				        setSuccess($("#fpermissions div.panel-heading"));
				        setTimeout(checkDBMS, 2000); 
				    }else if(data.status == 'error'){				    	
				        setError($("#fpermissions"),data.msg);
				    } 
                       
                },
                error:function(x, t, m) {  

                	setError($("#fpermissions"),x.responseText);
                	
                }
            });
	}

	$("input").on( "focusout", function() {
		if ($("input#bbddpuser").val().length > 0 && 
			$("input#bbddppassword").val().length > 0 &&
			$("input#bbddname").val().length > 0)
		{			
			$(".well button").prop('disabled', false);
		}else{
			$(".well button").prop('disabled', true);
		}
	  
	});

	$( "#installf" ).submit(function( event ) {

		$("#steps").show();
		$("#result").hide();
		$("#result div.panel-heading").remove();
		

		if ($("input#bbdduser").val().length==0){
			$("#newuser").hide();	
			$("#newusertest").hide();	
			
		}else{
			$("#newuser").show();
			$("#newusertest").show();	
		}

		$("#steps div.panel div.panel-body").remove();
		$("#steps div.well span").remove();
		$("#steps div.well").removeClass("success").removeClass("error").addClass("default");

		checkFolderPermissions();
		event.preventDefault();
	});

</script>
