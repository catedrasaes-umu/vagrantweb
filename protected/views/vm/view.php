<?php

$this->breadcrumbs=array(
	'Nodes'=>array('node/index'),
	$node=>array('node/view','id'=>$node),		
	$vm,
);
?>

<div id="hwtable">
<?php if (!is_null($vminfo)){ ?>
<table class="detailview-curved table table-striped table-condensed" id="yw1" style="margin-top:20px;margin-bottom:30px">
<tr class="odd"><th>Operating System</th><td><span><?php echo $vminfo["ostype"]; ?></span></td></tr>
<tr class="even"><th>Num. Cpus</th><td><span><?php echo $vminfo["cpus"]; ?></span></td></tr>
<tr class="odd"><th>CPU Limit</th><td><span><?php echo $vminfo["cpuexecutioncap"]; ?></span></td></tr>
<tr class="even"><th>Memory</th><td><span><?php echo $vminfo["memory"]; ?>MB</span></td></tr>
<tr class="odd"><th>PAE</th><td><span><?php echo $vminfo["pae"]; ?></span></td></tr>
<tr class="even"><th>3D Acceleration</th><td><span><?php echo $vminfo["accelerate3d"]; ?></span></td></tr>
<tr class="odd"><th>2D Acceleration</th><td><span><?php echo $vminfo["accelerate2dvideo"]; ?></span></td></tr>
<tr class="even"><th>Clipboard</th><td><span><?php echo $vminfo["clipboard"]; ?></span></td></tr>
<tr class="odd"><th>Remote Desktop</th><td><span><?php echo $vminfo["vrde"]; ?></span></td></tr>
<tr class="even"><th>USB</th><td><span><?php echo $vminfo["usb"]; ?></span></td></tr>

<tr class="odd"><th>Boot Order</th><td>
	<?php 
		echo "<table class='nopadtop'>";
		
		
		echo "<tr>";			
		echo("<td><span>".$vminfo["boot1"]."</span></td>");			
		echo "</tr>";

		echo "<tr>";			
		echo("<td><span>".$vminfo["boot2"]."</span></td>");			
		echo "</tr>";

		echo "<tr>";			
		echo("<td><span>".$vminfo["boot3"]."</span></td>");			
		echo "</tr>";

		echo "<tr>";			
		echo("<td><span>".$vminfo["boot4"]."</span></td>");			
		echo "</tr>";
				
		
		echo "</table>";
	?>
</td></tr>

<tr class="even"><th>Interfaces</th><td>
	<?php 
		echo "<table class='nopadtop'>";
		
		foreach (range(1, 8) as $n) {
			if (array_key_exists('nic'.$n, $vminfo) && $vminfo['nic'.$n]!="none") {
				echo "<tr><td></td></tr>";
				echo "<tr>";			
				echo("<td><span><strong>Network Interface:</strong> ".'nic'.$n."</span></td>");			
				echo "</tr>";
				echo "<tr>";			
				echo("<td><span><strong>Network Type:</strong> ".$vminfo['nic'.$n]."</span></td>");			
				echo "</tr>";
				echo "<tr>";			
				echo("<td><span><strong>MAC Address:</strong> ".$vminfo['macaddress'.$n]."</span></td>");			
				echo "</tr>";
				echo "<tr>";				
				echo("<td><span><strong>Cable connected:</strong> ".$vminfo['cableconnected'.$n]."</span></td>");			
				echo "</tr>";
								
			}
		}

		// foreach ($vminfo["interfaces"] as $key => $value) {			
		// 	echo "<tr>";			
		// 	 echo("<td><span>".$value["name"].":</span></td><td><span>".$value["ipaddress"]."</span></td>");			
		// 	echo "</tr>";
		// }		
		
		echo "</table>";
	?>
</td>
</tr>

</table>

<?php } ?>
</div>


<?php if (!is_null($users)) { ?>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" id="aprousers">
	<h4 style="margin-bottom:0;margin-top:15px">Assigned Users:</h4>
	<?php
		$this->widget('booster.widgets.TbGridView', array(
		'id'=>'assigned-users',
		'selectableRows' => 0, 		
		'htmlOptions'=>array('class' =>'grid-view table-curved'),
		'dataProvider'=> $users,	
		//'afterAjaxUpdate'=>'function(id, data){ $.fn.yiiGridView.update("available-users",{data: "project="+$("select#project option:selected").val()}); }',            	
		//'filter' => $filtersForm,
		'ajaxUpdate'=>'available-users',
		'columns' => array(
				array(
                    'header'=>'User Name',                    
                    'value'=>'CHtml::encode($data["username"])',                    
				),	
				array(
					'class'=>'booster.widgets.TbButtonColumn',
					'template'=>'{delete}',
					'buttons'=>array
                	(
                        'delete' => array
                        (                      		            	
                        	'url'=>'Yii::app()->createUrl("user/removevm",array("user"=>$data["id"],
                        														"node"=>"'.$node.'",
                        													 	"vm"=>"'.$vm.'"))',
                        ),
                	),

				),										
	            
	        ),
		));
	?>
</div>

<?php } ?>


<?php if (!is_null($availableusers)) { ?>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" id="avprousers">
	<h4 style="margin-bottom:0;margin-top:15px">Available Users:</h4>
	<?php
		$this->widget('booster.widgets.TbGridView', array(
		'id'=>'available-users',
		'selectableRows' => 0, 	
		'htmlOptions'=>array('class' =>'grid-view table-curved'),	
		'dataProvider'=> $availableusers,
		'ajaxUpdate'=>'assigned-users',
		'columns' => array(
				array(
                    'header'=>'User Name',                    
                    'name'=>'username',                	
				),	
				array(
					'class'=>'CButtonColumn',
					'template'=>'{add}',
					'buttons'=>array
                	(
                        'add' => array
                        (
                        	'label'=>'Add User to Group',
                        	'url'=>'Yii::app()->createUrl("user/addvm",array("user"=>$data->id,
                        													"node"=>"'.$node.'",
                        													 "vm"=>"'.$vm.'"))',

                        	'imageUrl'=>Yii::app()->request->baseUrl.'/images/adduser.png',
                        	'click'	  =>'js:function(){			            					
			            					$.ajax({
								        		type: "GET",								        		
								        		url: $(this).attr("href"),								        		        		
								        		success:function(data) {													   												   
													   //$.fn.yiiGridView.update("assigned-users",{data: "id="+$("select#project option:selected").val()});
								        			$.fn.yiiGridView.update("assigned-users");
								                },
								                error:function(x, t, m) {								                			
								                		$("#flash-messages").addClass("flash-error").html(x.responseText).fadeIn().delay(5000).fadeOut("slow");								                	
								                }
								            });
				            				return false;
			            				}',
                        ),
                	),

				),	
	            
	        ),
		));
	?>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="vmpgroups" style="margin-top:40px">
	<h4 style="margin-bottom:0;margin-top:15px">Groups Assigned:</h4>
	<?php
		$this->widget('booster.widgets.TbGridView', array(
		'id'=>'assigned-groups',
		'selectableRows' => 0, 	
		'htmlOptions'=>array('class' =>'grid-view table-curved'),	
		'dataProvider'=> $projects,		
		'columns' => array(
				array(
                    'header'=>'Group Name',                    
                    'name'=>'groups',       
                    'type'	=> 'raw',         	
                    'value'=>'CHtml::link($data["project_name"], array("project/view","id"=>$data["id"]))',
				),				
	            
	        ),
		));
	?>
</div>


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="vmprojectusers" style="margin-top:40px">
	<h4 style="margin-bottom:0;margin-top:15px">Users Assigned By Groups:</h4>
	<?php
		$this->widget('booster.widgets.TbGridView', array(
		'id'=>'assigned-groupusers',
		'selectableRows' => 0, 	
		'htmlOptions'=>array('class' =>'grid-view table-curved'),	
		'dataProvider'=> $projectusers,		
		'columns' => array(
				array(
                    'header'=>'Users',                    
                    'name'=>'username',                           
                    'type'	=> 'raw',         	
                    'value'=>'	CHtml::link($data["username"], array("user/view","id"=>$data["uid"]))',
				),	
				array(
                    'header'=>'Group Name',                    
                    'name'=>'groups',       
                    'type'	=> 'raw',         	
                    'value'=>'	CHtml::link($data["project_name"], array("project/view","id"=>$data["id"]))',
				),				
	            
	        ),
		));
	?>
</div>

<?php } ?>



