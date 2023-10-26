<?php include'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-success">
		<div class="card-header">
			<?php if($_SESSION['login_type'] == 2): ?>
			<div class="card-tools">
				<button class="btn btn-block btn-sm btn-default btn-flat border-primary" id="new_task"><i class="fa fa-plus"></i> Add New Task</button>
			</div>
			<?php endif; ?>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-condensed" id="list">
			
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th width="30%">Task</th>
						<th>Due Date</th>
						<?php if($_SESSION['login_type'] != 0): ?>
						<th>Assigned To</th>
						<?php endif; ?>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$where = "";
					if($_SESSION['login_type'] == 0)
						$where = " where t.employee_id = '{$_SESSION['login_id']}' ";
					elseif($_SESSION['login_type'] == 1)
						$where = " where e.evaluator_id = {$_SESSION['login_id']} ";
					
					
					$qry = $conn->query("SELECT t.*,concat(e.lastname,', ',e.firstname,' ',e.middlename) as name FROM task_list t inner join employee_list e on e.id = t.employee_id $where order by unix_timestamp(t.date_created) asc");
					while($row= $qry->fetch_assoc()):
						$trans = get_html_translation_table(HTML_ENTITIES,ENT_QUOTES);
						unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
						$desc = strtr(html_entity_decode($row['description']),$trans);
						$desc=str_replace(array("<li>","</li>"), array("",", "), $desc);
						

					?>
					<tr>
						<td class="text-center"><?php echo $i++ ?></td>
						<td>
							<p><b><?php echo ucwords($row['task']) ?></b></p>
							<p class="truncate"><?php echo strip_tags($desc) ?></p>
						</td>
						<td><b><?php echo date("M d, Y",strtotime($row['due_date'])) ?></b></td>
						<?php if($_SESSION['login_type'] != 0): ?>
						<td><p><b><?php echo ucwords($row['name']) ?></b></p></td>
						<?php endif; ?>
						<td>
                        	<?php 
                        	if($row['status'] == 0){
						  		echo "<span class='badge badge-info'>Pending</span>";
                        	}elseif($row['status'] == 1){
						  		echo "<span class='badge badge-primary'>On-Progress</span>";
                        	}elseif($row['status'] == 2){
						  		echo "<span class='badge badge-success'>Complete</span>";
                        	}
                        	if(strtotime($row['due_date']) < strtotime(date('Y-m-d'))){
						  		echo "<span class='badge badge-danger mx-1'>Over Due</span>";
                        	}
                        	?>
                        </td>
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
			                    <div class="dropdown-menu" style="">
		                    	<a class="dropdown-item view_task" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">View Task</a>
		                    	 <div class="dropdown-divider"></div>
			                    <?php if($_SESSION['login_type'] == 2): ?>
		                    	<a class="dropdown-item manage_task" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Edit</a>
		                    	 <div class="dropdown-divider"></div>
		                     	<a class="dropdown-item delete_task" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
		                    	 <div class="dropdown-divider"></div>
		                    	<?php endif; ?>
			                    <?php if($_SESSION['login_type'] == 0): ?>
			                    <?php if($row['status'] != 2): ?>
			                    <a class="dropdown-item new_progress" data-pid = '<?php echo $row['pid'] ?>' data-tid = '<?php echo $row['id'] ?>'  data-task = '<?php echo ucwords($row['task']) ?>'  href="javascript:void(0)">Add Progress</a>
		                    	 <div class="dropdown-divider"></div>
		                    	<?php endif; ?>
		                    	<?php endif; ?>
			                    <a class="dropdown-item view_progress" data-pid = '<?php echo $row['pid'] ?>' data-tid = '<?php echo $row['id'] ?>'  data-task = '<?php echo ucwords($row['task']) ?>'  href="javascript:void(0)">View Progress</a>
								</div>
						</td>
					</tr>	
				<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<style>
	table p{
		margin: unset !important;
	}
	table td{
		vertical-align: middle !important
	}
</style>
<script>
	$(document).ready(function(){
		$('#list').dataTable()
	$('#new_task').click(function(){
		uni_modal("<i class='fa fa-plus'></i> New Task","manage_task.php",'mid-large')
	})
	$('.view_task').click(function(){
		uni_modal("View Task","view_task.php?id="+$(this).attr('data-id'),'mid-large')
	})
	$('.manage_task').click(function(){
		uni_modal("<i class='fa fa-edit'></i> Edit Task","manage_task.php?id="+$(this).attr('data-id'),'mid-large')
	})
	$('.new_progress').click(function(){
		uni_modal("<i class='fa fa-plus'></i> New Progress for: "+$(this).attr('data-task'),"manage_progress.php?tid="+$(this).attr('data-tid'),'mid-large')
	})
	$('.view_progress').click(function(){
		uni_modal("Progress for: "+$(this).attr('data-task'),"view_progress.php?id="+$(this).attr('data-tid'),'mid-large')
	})
	$('.delete_task').click(function(){
	_conf("Are you sure to delete this task?","delete_employee",[$(this).attr('data-id')])
	})
	})
	function delete_task($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_task',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>