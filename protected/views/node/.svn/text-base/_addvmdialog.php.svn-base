<div class="addvm-dialog-div" style="display:none">
    <div id="addvm-dialog1" class="dialogs" data-id="1" title="Virtual Machine information">
      <p class="note">Please, complete the Virtual Machine basic configuration:</p>
      <div class="form">
      <div class="row">
      	<?php
      		echo CHtml::label('Name:', 'add_vm_name');
      	 	echo CHtml::textField('add_vm_name', '', array('size' => 35, 'maxlength' => 50)); 
  	 	?>
  	 	
  	 	
      </div>
      
      <div class="row"> 
      <?php 
      		echo CHtml::label('Box name:', 'add_vm_box_name');      		
			echo CHtml::dropDownList('add_vm_box_name', "",$boxes->keys);
			
  		?>
  		
      </div> 
      
      <div class="row">
      	<?php 
      		echo CHtml::label('Hostname:', 'add_vm_hostname');
      		echo CHtml::textField('add_vm_hostname', '', array('size' => 35, 'maxlength' => 50)); 
  		?> 
  		   
  	  </div>
  	  
  	  <div class="row">
      	<?php 
      		echo CHtml::label('Network Type:', 'add_vm_network_type');
      		//echo CHtml::textField('add_vm_network_type', '', array('size' => 35, 'maxlength' => 50));
      		echo CHtml::dropDownList('add_vm_network_type', "public_network",array('public_network' => 'public_network', 'private_network' => 'private_network')); 
  		?> 
  		   
  	  </div>
      	
    </div>
   </div>
    <div id="addvm-dialog2" class="dialogs" data-id="2" title="Virtual Machine generated config">
      <p>This is the generated config for the Virtual Machine. You can add as many options as you want:</p>
      <?php echo CHtml::textArea('addvm_gen_config', "", array('rows' => 15, 'cols' => 50,'style'=>'display:block;margin-right:auto;margin-left:auto')); ?>
    </div>    
</div>


<script>
$(function() {  
		// addvm_dialog_options = { width: 500,
								// height:500,
								// modal : true};
// 								
		
		
		addvm_dialog_options = { width: "auto",
								height:"auto",
								modal : true};
		
		
		
								
        /* construct prev/next button */
        $(".addvm-dialog-div div.dialogs").each(function (i) {
            var totalSize = $(".addvm-dialog-div div.dialogs").size() - 1;           
            
            if (i != 0) {            	
                prev = i - 1;
                //$(this).append("<div class='prev_button'><a href='#tabs' class='prev-tab mover' rel='" + prev + "'>Previous</a></div>");
                $(this).append("<div class='prev_button'><button type='button' style='float:left' href='#tabs' class='prev-tab mover' rel='" + prev + "' >Previous</button></div>");
                
            }

            if (i != totalSize) {
                next = i + 1;
                //$(this).append("<div class='next_button'><a href='#tabs' class='next-tab mover' rel='" + next + "'>Next</a></div>");
                $(this).append("<div class='prev_button'><button type='button' style='float:right' href='#tabs' class='next-tab mover' rel='" + next + "' >Next</button></div>");
            }
            
            if (i == totalSize) {                
                //$(this).append("<div class='finish_button'><a href='#tabs' class='finish-tab mover' rel='" + next + "'>Finish</a></div>");
                $(this).append("<div class='finish_button'><button type='button' style='float:right' href='#tabs' class='finish-tab mover'>Finish</button></div>");
            }
        });

		$('.finish-tab').click(function () {			
			  
			 var currentDialog = $(this).parent().parent();
			 $.ajax({				            					
	        		 type: "POST",								        		
	        		 url: <?php echo json_encode(Yii::app()->createUrl("vm/create",array("node"=>$node))); ?>,
	        		 data: {gen_config:$('#addvm_gen_config').val()},								        		        		
	        		 success:function(data) {
	        		 														   												   
						$.fn.yiiGridView.update("vms-grid",{data: "ajaxUpdateRequest=true",});
						   
	                 },
	                 error:function(x, t, m) {								                			
	                		$("#flash-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(3000).fadeOut("slow");								                	
	                 },
	                 complete:function() {	
							currentDialog.dialog("close");							
	                 }
	                 
	             });
			
		});
		
        /* next button click */
        $('.next-tab').click(function () {
              var nextDialog= $(this).parent().parent().data("id") + 1;  
              var currentDialog = $(this).parent().parent(); currentDialog.dialog("close");
              /*Obtener los valores de los datos del formulario y obtener el código de configuración generado*/
             
             
             
              $.ajax({				            					
	        		 type: "POST",								        		
	        		 url: <?php echo json_encode(Yii::app()->createUrl("vm/genconfig")); ?>,
	        		 data: {vm_name:$('#add_vm_name').val(),
	        		 		box_name:$('#add_vm_box_name option:selected').text(),	        		 		
	        		 		host_name:$('#add_vm_hostname').val(),
	        		 		network_type:$('#add_vm_network_type').val(),	        		 		
	        		 		},					        		        		
	        		 success:function(data) {	        		 		        	
	        		 	var msg = jQuery.parseJSON(data)	        		 				   												   						
						$('#addvm_gen_config').val(msg.cfg);
						   
	                 },
	                 error:function(x, t, m) {								                			
	                		$("#flash-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(3000).fadeOut("slow");								                	
	                 },	                 
	                 
	             });
	                           
              $("#addvm-dialog"+ nextDialog).dialog(addvm_dialog_options);  
                          
        });

        /* previous button click */
        $('.prev-tab').click(function () {
              var prevDialog = $(this).parent().parent().data("id") - 1;  
              var currentDialog = $(this).parent().parent(); currentDialog.dialog("close");
              $("#addvm-dialog"+ prevDialog).dialog(addvm_dialog_options);              
        });


		// $("#opener").click(function() {
			// $("#addvm-dialog1").dialog(options);
		// });
        
  });

</script>
