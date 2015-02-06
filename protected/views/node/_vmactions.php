<script>
	
	
	jQuery(function($) {
jQuery('#run, #pause, #stop').on('click',function() {
        $("#node-status-loading-dialog").dialog({title: "Performing Operation"});
        $("#node-status-loading-dialog").dialog("open");
        $.ajax({
        		type: 'GET',
        		timeout:0, //No timeout
        		url: $(this).attr("href"),        		
        		success:function() {
					           						
                       $.fn.yiiGridView.update('node-model-grid',{
                       		data: "ajaxUpdateRequest=true",
                       });
                       
                },
                error:function(x, t, m) {    	
                	$("#node-status-loading-dialog").dialog("close");                	
                	if (t=="timeout")
                	{                		
                		$("#flash-messages").addClass("flash-error").html("Request Timeout Error").fadeIn().delay(3000).fadeOut("slow");
                	}else{
                		
                		$("#flash-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(5000).fadeOut("slow");
                	}
                }
            })
        
        return false;
});
});
</script>