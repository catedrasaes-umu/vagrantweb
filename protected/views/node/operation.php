

<?php

if (isset($node) && $node!=null){
    echo "<div class='breadcrumbframe'>";
    $this->widget('booster.widgets.TbBreadcrumbs', array(         
            'links'=>array($node=>Yii::app()->createUrl("node/view", array("id"=>$node))),
    )); 

    echo "</div>";

}


 
?>


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="notificationarea">
    <div class="panel panel-primary" id="alertas">
        <div class="panel-heading">
            <i class="fa fa-bell fa-fw"></i> Operation Notification Panel
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">
            <div class="list-group">
            	
            </div>
            <!-- /.list-group -->
            <a href="#" onclick="clearOperations()" class="btn btn-default btn-block">Clear Operations</a>
        </div>
        <!-- /.panel-body -->
    </div>

</div>

<script>
    function clearOperations()
    {
        $.ajax({
            type: "GET",                                                
            url: "/vagrantweb/index.php?r=operation/clear",                                                              
            success:function(data) {             
                $("div#alertas div.list-group").children().remove();
            },
            error:function(x, t, m) {                
                    $("#flash-messages").addClass("flash-error").html("Error retrieving clearing operations");                                                 
            }
        }); 
    }


    function loadNotifications()
    {
        var url= "/vagrantweb/index.php?r=operation/last&limit=50";
        

        <?php if (isset($node)){ ?>                
                url=url+"&node="+<?php echo json_encode($node);?>;
        <?php } ?>
        

        $.ajax({
            type: "GET",                                                
            url: url,                                                              
            success:function(data) {             
                var todas=$.parseHTML(data);

                
                
                var indices = [];

                $("div#alertas div.list-group a.list-group-item div.notificacionmsg:visible").each(function( index ) {                
                  indices.push($(this).parent().attr('id'))
                });

    
                $("div#alertas div.list-group").empty();

                var arrayLength = todas.length;
                for (var i = 0; i < arrayLength; i++) {
                    var id = $(todas[i]).attr('id');                    

                    if ($.inArray(id,indices)!=-1){             
                        $(todas[i]).children("div.notificacionmsg").show();
                    }
                    
                    $("div#alertas div.list-group").append($(todas[i]));
                }

                //$("div#alertas div.list-group").html(data);
            },
            error:function(x, t, m) {                
                    $("#flash-messages").addClass("flash-error").html("Error retrieving loading operations");                                                 
            }
        }); 
    }

    $(document).on("click", "div#alertas div.list-group a.list-group-item", function(event){        
    
        $(this).children("div.notificacionmsg").toggle("600");
        return false;
    });

    $( document ).ready(function() {
    
        loadNotifications();    
    

        $(document).on("operationPolling",function(event,params){
            
            loadNotifications();
            
            
        });
     
    });

</script>