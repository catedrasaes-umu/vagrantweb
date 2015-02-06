

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

      <div class="row">
        <?php echo CHtml::label('OS Family:', 'box_family');          ?>
        <select id="osfamily">           
           <option value="linux">Unix/Linux</option>
           <option value="win">Windows</option>
        </select>
      </div>
  		
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

      		echo CHtml::dropDownList('add_vm_network_type', "public_network",array('public_network' => 'public_network'/*, 'private_network' => 'private_network'*/)); 
          //FIXME Si la red es privada, el segundo campo debería ser la ip a establecer

          if (!is_null($nodeinfo)){
            $iname = [];

            foreach ($nodeinfo["interfaces"] as $key => $value) {
              $iname[$value['name']]=$value['name'];            
            }
            
            echo CHtml::dropDownList('add_vm_network_interface','', $iname,array('style'=>'margin-left:20px')); 
          }
    		?> 
  		   
  	  </div>

      <div class="row">
        
        <?php echo CHtml::label('Gui:', 'add_vm_gui',array('style'=>'display:inline;margin-right:10px')); ?>
        
        <?php echo CHtml::CheckBox('add_vm_gui', true,array ('value'=>'on','style'=>'margin-top:5px'));?> 
        
      </div>

      <div class="row">
        
        <?php echo CHtml::label('SSH Connect:', 'add_vm_ssh',array('style'=>'display:inline;margin-right:10px')); ?>
        
        <?php echo CHtml::CheckBox('add_vm_ssh', true,array ('value'=>'on','style'=>'margin-top:5px'));?> 
        
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
              								modal : true,
                              open: function(event, ui)
                              {
                                  var boxes = [];

                                  $('input#add_vm_name').val('');
                                  $('input#add_vm_hostname').val('');
                                  var boxselect = $('select#add_vm_box_name');
                                  boxselect.empty();
                                  $('div#boxes-grid > table > tbody > tr > td:first-child').each( function( key, item ) {
                                    
                                    var boxname = $(this).text();                                    

                                    boxselect.append($('<option>', { 
                                        value: key,
                                        text : boxname
                                    }));

                                  });
                                  
                              }
                              };

    addvm_inline_dialog_options = { width: "auto",
                                    height:"auto",
                                    modal : true,
                                    
                                    };
		
		
		
								  
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

                      //currentDialog.dialog('destroy').remove();
	                 }
	                 
	             });
			
		});
		
        /* next button click */
        $('.next-tab').click(function () {
              var nextDialog= $(this).parent().parent().data("id") + 1;  
              var currentDialog = $(this).parent().parent(); 
              currentDialog.dialog("destroy");
              
              /*Obtener los valores de los datos del formulario y obtener el código de configuración generado*/
              $.ajax({				            					
	        		 type: "POST",								        		
	        		 url: <?php echo json_encode(Yii::app()->createUrl("vm/genconfig")); ?>,
	        		 data: {vm_name:$('#add_vm_name').val(),
	        		 		box_name:$('#add_vm_box_name option:selected').text(),	        		 		
	        		 		host_name:$('#add_vm_hostname').val(),
	        		 		network_type:$('#add_vm_network_type').val(),	        		 		
                  gui:$('#add_vm_gui').is(":checked"),
                  ssh:$('#add_vm_ssh').is(":checked"),
                  network_interface:$('#add_vm_network_interface').val(),                 
                  vagrant_version:'<?php echo $nodeinfo["vagrant_version"]?>',
	        		 		},					        		        		
	        		 success:function(data) {	        		 		        	
	        		 	var msg = jQuery.parseJSON(data)	        		 				   												   						
						    $('#addvm_gen_config').val(msg.cfg);
						   
	                 },
	                 error:function(x, t, m) {								                			
	                		$("#flash-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(3000).fadeOut("slow");								                	
	                 },	                 
	                 
	               });
	                           
                $("#addvm-dialog"+ nextDialog).dialog(addvm_inline_dialog_options);  
                          
        });

        /* previous button click */
        $('.prev-tab').click(function () {
              var prevDialog = $(this).parent().parent().data("id") - 1;  
              
              var currentDialog = $(this).parent().parent(); 
              currentDialog.dialog("close");
              
              $("#addvm-dialog"+ prevDialog).dialog(addvm_inline_dialog_options);              
        });


        $("#osfamily").change(function() {
            
           var ostype=$( "#osfamily option:selected" ).attr("value");

           if (ostype=="win"){            
             if (!$('#add_vm_gui').is(":checked")){           
                $( "#add_vm_gui" ).prop('checked', 'checked');                
             }

             $( "#add_vm_ssh" ).prop('checked', false);                

             $( "#add_vm_ssh" ).prop('disabled', true);
             $('#add_vm_gui').prop('disabled', true);

           }else{
              $( "#add_vm_ssh" ).prop('checked', 'checked');                
              $( "#add_vm_ssh" ).prop('disabled', false);
              $('#add_vm_gui').prop('disabled', false);
           }
          
        });
        
  });

</script>
