<?php 
include 'db_connect.php';
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT t.*,concat(e.lastname,', ',e.firstname,' ',e.middlename) as name FROM task_list t inner join employee_list e on e.id = t.employee_id  where t.id = ".$_GET['id'])->fetch_array();
	foreach($qry as $k => $v){
		$$k = $v;
	}
}
?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row">
			<div class="col-md-6">
				<dl>
					<dt><b class="border-bottom border-primary">Task</b></dt>
					<dd><?php echo ucwords($task) ?></dd>
				</dl>
				<dl>
					<dt><b class="border-bottom border-primary">Assign To</b></dt>
					<dd><?php echo ucwords($name) ?></dd>
				</dl>
			</div>
			<div class="col-md-6">
				<dl>
					<dt><b class="border-bottom border-primary">Due Date</b></dt>
					<dd><?php echo date("m d,Y",strtotime($due_date)) ?></dd>
				</dl>
				<dl>
					<dt><b class="border-bottom border-primary">Status</b></dt>
					<dd>
						<?php 
			        	if($status == 0){
					  		echo "<span class='badge badge-info'>Pending</span>";
			        	}elseif($status == 1){
					  		echo "<span class='badge badge-primary'>On-Progress</span>";
			        	}elseif($status == 2){
					  		echo "<span class='badge badge-success'>Complete</span>";
			        	}
			        	if(strtotime($due_date) < strtotime(date('Y-m-d'))){
					  		echo "<span class='badge badge-danger mx-1'>Over Due</span>";
			        	}
			        	?>
					</dd>
				</dl>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<dl>
				<dt><b class="border-bottom border-primary">Description</b></dt>
				<dd><?php echo html_entity_decode($description) ?></dd>
			</dl>
			</div>
		</div>
	</div>
</div>
<style>
	#uni_modal .modal-footer{
		display: none
	}
	#uni_modal .modal-footer.display{
		display: flex
	}
	#post-field{
		max-height: 70vh;
		overflow: auto;
	}
</style>
<div class="modal-footer display p-0 m-0">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>