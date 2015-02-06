
<?php
/* @var $this ProjectController */
/* @var $model ProjectModel */





?>

<!-- Page-Level Demo Scripts - Dashboard - Use for reference -->
    <script src="recursos/js/plugins/morris/raphael-2.1.0.min.js"></script>
    <script src="recursos/js/plugins/morris/morris.js"></script>
    <script src="recursos/js/demo/dashboard-demo.js"></script>
    

<?php   
    Yii::app()->clientScript->registerScript(
   'hideFlashEffect',   
   '$("#flash-messages").fadeIn().delay(3000).fadeOut("slow")',
   
   CClientScript::POS_READY
    );?>


<div id="flash-messages">   
<?php
    
    $flashMessages = Yii::app()->user->getFlashes();
    
    if ($flashMessages) {       
        foreach($flashMessages as $key => $message) {
           // debug($message.time());
            echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
        }
        
        
    }
?>
</div>

<div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Dashboard</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Area Chart Example
                            <div class="pull-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                        Actions
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li><a href="#">Action</a>
                                        </li>
                                        <li><a href="#">Another action</a>
                                        </li>
                                        <li><a href="#">Something else here</a>
                                        </li>
                                        <li class="divider"></li>
                                        <li><a href="#">Separated link</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div id="morris-area-chart"></div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    
                    
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Node Performance
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="flot-chart">
                                <div class="flot-chart-content" id="morris-line-chart"></div>
                            </div>
                        </div>
                        <!-- /.panel-body -->
                    </div>



                   
                    
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Virtual Machines Overwiew
                            
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="row">
                                
                                <!-- /.col-lg-4 (nested) -->
                                
                                    <div id="morris-bar-chart"></div>
                                
                                <!-- /.col-lg-8 (nested) -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    
                    
                </div>
                <!-- /.col-lg-8 -->
                <div class="col-lg-4">
                    
                    <!-- /.panel -->
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Node Status
                        </div>
                        <div class="panel-body">
                            <div id="node-status-chart"></div>
                            <a href="<?=Yii::app()->createUrl('node/index')?>" class="btn btn-default btn-block">View Details</a>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <div class="panel panel-primary" id="alertas">
                        <div class="panel-heading">
                            <i class="fa fa-bell fa-fw"></i> Notifications Panel
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="list-group">
                                
                            </div>
                            <!-- /.list-group -->
                            <a href="<?=Yii::app()->createUrl("node/operations")?>" class="btn btn-default btn-block">View All Alerts</a>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    
                </div>
                <!-- /.col-lg-4 -->
            </div>

<script type="text/javascript">
var donut;

function setNodeStatusGraph(online,offline)
{    
    donut=Morris.Donut({
                    element: 'node-status-chart',
                    data: [{
                        label: "Online",
                        value: online
                    }, {
                        label: "Offline",
                        value: offline
                    }, ],
                    colors: ["#6BD380","#E56767"],
                    resize: true
                });

}

function getNodeStatus()
{
    $.ajax({
            type: "GET",                                                
            url: "/vagrantweb/index.php?r=node/ping",                                                              
            success:function(data) {           

                var status=$.parseJSON(data);

                if (donut==undefined){
                    setNodeStatusGraph(status["online"],status["offline"]);
                }else{

                    donut.setData([{
                        label: "Online",
                        value: status["online"]
                    }, {
                        label: "Offline",
                        value: status["offline"]
                    }, ]);
                }
                
            },
            error:function(x, t, m) {                             
                    $("#flash-messages").addClass("flash-error").html("Error retrieving node status");                                                 
            }
        });

    setTimeout( getNodeStatus, 20000 ); 

}

function loadNotifications()
{
    
        $.ajax({
            type: "GET",                                                
            url: "/vagrantweb/index.php?r=operation/last&limit=7",                                                              
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

            
                




            },
            error:function(x, t, m) {                
                    $("#flash-messages").addClass("flash-error").html("Error retrieving node status");                                                 
            }
        }); 
}






var morrisline = Morris.Line({
  element: 'morris-line-chart',
  data: [],
  xkey: 'y',
  ykeys: ['cpu','memory'],
  labels: ['CPU','Memory'],
  parseTime: false,
  hideHover: true,
  ymin: 0,
  ymax: 130,
  resize: true
});

function data(offset) {
 // var ret = [];

    var data1 = morrisline.data;  


    var datanew = [];

    var comienzo = 0;
    if (data1.length>8)
    {        
        comienzo =1;
    }

    
    for (var i = comienzo; i < data1.length; i++) {       
        datanew.push({y:data1[i].label,
                    cpu: data1[i].y[0],
                    memory: data1[i].y[1] });


    }

    
    var date = new Date;
    //date.setTime(result_from_Date_getTime);
    var minutes = date.getMinutes();
    var hour = date.getHours();
    var seconds = date.getSeconds();
    var hora = hour+":"+minutes+":"+seconds;

    datanew.push({y:hora,
                cpu:30 + Math.floor(Math.random() * 100),
                memory:30 + Math.floor(Math.random() * 100)});

    
  
  return datanew;
}

// update the chart with the new data.
function updatePerformance() {  
  
  morrisline.setData(data(5));
  
}



function loadNodePerformance(){
    

    var day_data = [
  {"elapsed": "8:00", "cpu": 34, "memory": 20},
  {"elapsed": "8:15", "cpu": 24, "memory": 50},
  {"elapsed": "8:30", "cpu": 3, "memory": 60},
  {"elapsed": "8:45", "cpu": 12, "memory": 51},
  {"elapsed": "9:00", "cpu": 13, "memory": 80},
  {"elapsed": "9:15", "cpu": 22, "memory": 82},
  {"elapsed": "9:30", "cpu": 5, "memory": 85},
  {"elapsed": "9:45", "cpu": 26, "memory": 79},
  {"elapsed": "10:00", "cpu": 12, "memory": 70},
  {"elapsed": "10:15", "cpu": 19, "memory": 70},
  {"elapsed": "10:30", "cpu": 30, "memory": 70},
  {"elapsed": "10:45", "cpu": 35, "memory": 70},
  {"elapsed": "11:00", "cpu": 40, "memory": 70},
  {"elapsed": "11:15", "cpu": 55, "memory": 70}
];
// Morris.Line({
//   element: 'morris-line-chart',
//   data: day_data,
//   xkey: 'elapsed',
//   ykeys: ['cpu','memory'],
//   labels: ['CPU','Memory'],
//   parseTime: false,
//   hideHover: 'auto',
//   resize: true
// });

setInterval(updatePerformance, 3000);
}

function printBarChart()
{
    var object = Morris.Bar({
    element: 'morris-bar-chart',
    data: [],
    xkey: 'y',
    ykeys: ['a', 'b','c'],
    labels: ['Ubuntu.12.04', 'Ubuntu.12.10','Centos.5'],
    hideHover: 'auto',
    resize: true
    });
   

    return object;
}

/*
var nReloads = 0;
function data(offset) {
  var ret = [];
  for (var x = 0; x <= 360; x += 10) {
    var v = (offset + x) % 360;
    ret.push({
      x: x,
      y: Math.sin(Math.PI * v / 180).toFixed(4),
      z: Math.cos(Math.PI * v / 180).toFixed(4)
    });
  }
  return ret;
}
// create the morris chart. 
var graph = Morris.Line({
    element: 'morris-bar-chart',
    data: data(0),
    xkey: 'x',
    ykeys: ['y', 'z'],
    labels: ['sin()', 'cos()'],
    parseTime: false,
    ymin: -1.0,
    ymax: 1.0,
    hideHover: true
});
// update the chart with the new data.
function update() {
  nReloads++;
  // called on the returned Morris.Line object.
  graph.setData(data(5 * nReloads));
  $('#reloadStatus').text(nReloads + ' reloads');
}
setInterval(update, 100);
*/

/*
data1= [
      { y: '2010', a: 50,  b: 40 },
    { y: '2012', a: 100, b: 90 }
  ];

 data2= [
    { y: '2006', a: 100, b: 90 },
    { y: '2007', a: 75,  b: 65 },
    { y: '2008', a: 50,  b: 40 },
    { y: '2009', a: 75,  b: 65 },
    { y: '2010', a: 50,  b: 40 },
    { y: '2011', a: 75,  b: 65 },
    { y: '2012', a: 100, b: 90 }
  ];

bar_chart = Morris.Bar({
  element: 'morris-bar-chart',
  data: data1,
  xkey: 'y',
  ykeys: ['a', 'b'],
  labels: ['Series A', 'Series B']
});

$("#morris-bar-chart").click(function(){
  
  bar_chart.setData(data2);
});*/

function updateBarChart(barchart,newdata){
    var data1 = barchart.data;
    
    //console.log(data1);
    //data.push(newdata);
    /*var data1= [{
        y: '2006',
        a: 100,
        b: 90
    }, {
        y: '2007',
        a: 75,
        b: 65
    }, {
        y: '2008',
        a: 50,
        b: 40
    }, {
        y: '2009',
        a: 75,
        b: 65
    }, {
        y: '2010',
        a: 50,
        b: 40
    }, {
        y: '2011',
        a: 75,
        b: 65
    }, {
        y: '2012',
        a: 100,
        b: 90
    }];*/

    var datanew = [];
    for (var i = 0; i < data1.length; i++) {
        //console.log(data1[i]);
        // console.log(data1[i].label);
        // console.log(data1[i].y);
        datanew.push({y:data1[i].label,a:data1[i].y[0],b:data1[i].y[1],c:data1[i].y[2]});
    //Do something
    }

    datanew.push(newdata);

    //console.log(datanew);

    
    barchart.setData(datanew);
    
    
}

function loadNodeVMDistribution()
{

    var nodes = <?php echo json_encode($nodes);?>;
         
         
         
    var barchart = printBarChart();
    
    
    //updateBarChart(barchart,{y: "juan",a: 50,b: 29});
     
    $.each( nodes, function( key, value ) {                                    

        
        $.ajax({
            type: "GET",                                                
            url: "/vagrantweb/index.php?r=node/distribution&node="+value,                                                              
            success:function(data) {                
                var number = 1 + Math.floor(Math.random() * 6);
                var number1 = 1 + Math.floor(Math.random() * 6);
                var number2 = 1 + Math.floor(Math.random() * 6);
                updateBarChart(barchart,{y: value,a: number,b: number1,c:number2});
                
            },
            error:function(x, t, m) {                
                $("#flash-messages").addClass("flash-error").html("Error retrieving node virtual machine distribution");                                                 
            }
        }); 
    });

    
}



$(document).ready(function() {
        getNodeStatus();
        loadNotifications();
        loadNodePerformance();
        loadNodeVMDistribution();
        //loadProjectResume();
        $(document).on("operationPolling",function(event,params){            
            loadNotifications();
        });
});

$(document).on("click", "div#alertas div.list-group a.list-group-item", function(event){        
    
    $(this).children("div.notificacionmsg").toggle("600");
    return false;
});


</script>

