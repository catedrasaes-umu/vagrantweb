<div class="flash-success" id="snapshot-success-flash" style="display: none;"></div>
<div class="flash-error" id="snapshot-error-flash" style="display: none;"></div>

<div class="view" id="snapshot-tree">
	
<?php


$this->widget('CTreeView',array(
    'id'=>'snapshot-treeview',
    'data'=>$data,
    'animated'=>'fast',
    'collapsed'=>false,
    
));


?>

</div>

<div class="view" id="snapshot-details">

	<b><?php echo CHtml::encode("Name"); ?>:</b>
	<span id="snap_name_tag"><?php echo CHtml::encode(""); ?></span>
	<br />
	<b><?php echo CHtml::encode("UUID"); ?>:</b>
	<span id="snap_uuid_tag"><?php echo CHtml::encode(""); ?></span>
	<br />	
	<b><?php echo CHtml::encode("TimeStamp"); ?>:</b>
	<span id="snap_timestamp_tag"><?php echo CHtml::encode(""); ?></span>
	<br />
	<b><?php echo CHtml::encode("Current State"); ?>:</b>
	<span id="snap_current_state_tag"><?php echo CHtml::encode(""); ?></span>
	<br />
	<b><?php echo CHtml::encode("Description"); ?>:</b>
	<span id="snap_description_tag"><?php echo CHtml::encode(""); ?></span>
	<br />

	


</div>
<div id="command-buttons">
<?php
echo CHtml::button('Restore Snapshot',array('id'=>'restoresnap-button','href'=>Yii::app()->createUrl("vm/restoresnapshot",array('id'=>$vm,'node'=>$node)),'onClick'=>'restore_snapshot($("div#snapshot-details.view span#snap_uuid_tag").text());'));
echo CHtml::button('Delete Snapshot',array('id'=>'deletesnap-button','href'=>Yii::app()->createUrl("vm/deletesnapshot",array('id'=>$vm,'node'=>$node)),'onClick'=>'delete_snapshot($("div#snapshot-details.view span#snap_uuid_tag").text());'));
										
										
echo CHtml::button('Close',array('onClick'=>'js:$("#snapshot-list-dialog").dialog("close");','style'=>'float: right;'));

	
?>
</div>
<script>

	function remove_node(node_id)
	{
		var element = $('a#'+node_id);
		var liparent = element.parent();			
		var ulparent = liparent.parent();
		var liulparent=ulparent.parent();
		
		var nextfocus = liulparent.children('a');
	 	if (liparent.hasClass('last'))
	 	{
	 		//If the snapshot to remove is a leaf	 		
	 		ulparent.remove();
	 		liulparent.attr("class","hasChildren last");
	 		
	 	}else{
	 		//If the snapshot to remove is a node
	 		var ulchildren = liparent.children('ul');
	 		
	 		liulparent.append(ulchildren);
	 		
	 		ulparent.remove();	 		
	 	}
	 	
	 	selectSnapshot(nextfocus);

	}
	
	function delete_snapshot(data)
	{
		if (data.length==0)
			alert("Please select a snapshot to delete");
		else{
			$("#node-status-loading-dialog").dialog({title: "Deleting Snapshot"});			 
			 
        	$("#node-status-loading-dialog").dialog("open");
        	
        	$.ajax({
        		type: 'GET',
        		timeout:0,
        		url: $("#deletesnap-button").attr("href"),
        		data: {uuid:data},        		    		
        		success:function(response) {        				
        				
        				remove_node(data);
        				
        				$('#snapshot-success-flash').html(response).fadeIn().delay(3000).fadeOut("slow");
        				
                },
                error:function(x, t, m) {  
                		              		
                		$('#snapshot-error-flash').html(x.responseText).fadeIn().delay(3000).fadeOut("slow");
                },
                complete: function() {	$("#node-status-loading-dialog").dialog("close"); },
            })
		}
		return false;
	}
	
	function restore_snapshot(data)
	{
		
		if (data.length==0)
			alert("Please select a snapshot to restore");
		else{
			 
			$.ajax({
        		type: 'GET',
        		timeout:0,
        		async: false,
        		url: $("#restoresnap-button").attr("href"),
        		data: {uuid:data},        		    		
        		success:function(response) {        				
        				
        				//changeCurrentState(data);
        				
        				//selectSnapshot($("div#snapshot-tree.view a#"+data));        				
        				
        				// $('#snapshot-success-flash').html(response).fadeIn().delay(3000).fadeOut("slow");
        				msg='<div class="flash-success">'+response+'</div>\n';
        				$('#flash-node-messages').html(msg).fadeIn().delay(3000).fadeOut("slow");
        				
                },
                error:function(x, t, m) {                		
                		msg='<div class="flash-error">'+x.responseText+'</div>\n';
                		$('#flash-node-messages').html(msg).fadeIn().delay(3000).fadeOut("slow");
                },
                complete: function() {	
                	//$("#snapshot-list-dialog").dialog("close");
                	 
                	
                },
            })
            
            
            
		}
		return false;		
	}
	function selectSnapshot(data){		
		
		$('#snapshot-treeview a').removeClass("selected");
		
				
		data.toggleClass("selected");
		
		var divdetails=$('div#snapshot-details.view span');
		var id=data.attr("id");
		
		var divtree=$('div#snapshot-tree a#'+id);
		
		
		divdetails.siblings('#snap_name_tag').text(divtree.siblings('input#'+id+'_name').attr('value'));
		divdetails.siblings('#snap_uuid_tag').text(id);
		divdetails.siblings('#snap_description_tag').text(divtree.siblings('input#'+id+'_description').attr('value'));
		divdetails.siblings('#snap_timestamp_tag').text(divtree.siblings('input#'+id+'_timestamp').attr('value'));
		var state=divtree.siblings('input#'+id+'_current_state').attr('value');
		divdetails.siblings('#snap_current_state_tag').text(((state.length==0)?"NO":"YES"));
		
		
		
		
	}
	function changeCurrentState(uuid)
	{	
		
		//Modificar el valor del input current_state de la antigua snapshot
		var olduuid=$('#snapshot-treeview li#current_state > a').attr("id");		
		$('#snapshot-treeview li#current_state input#'+olduuid+"_current_state").removeAttr('value');
		//Eliminar la etiqueta de estado actual
		$('#snapshot-treeview li#current_state').removeAttr("id");
		
		
		//Localizar la li del uuid correspondiente		
		var parent=$('#snapshot-treeview a#'+uuid).parent("li");
		parent.attr('id','current_state');		
		
		//Modificar el atributo del inputo
		parent.children("input#"+uuid+"_current_state").attr('value','1');
	}
	
$( document ).ready(function() {
	
	
	$(document).on("updateAsync",function(event,params){
			
		if (params.operation_result==200)
		{
			
				
			changeCurrentState(jQuery.parseJSON(params.operation_msg)["web1"][0]);
	    	selectSnapshot($("div#snapshot-tree.view a#"+jQuery.parseJSON(params.operation_msg)["web1"][0]));
	    }		
		
	});
	
		
	
	
	
	 
});
	
	
	
	
	
</script>
