<?php include'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary new_academic" href="javascript:void(0)"><i class="fa fa-plus"></i> Add New</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="35%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Academic Year</th>
						<th>Semester</th>
						<th>Questions</th>
						<th>Answered</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$qry = $conn->query("SELECT * FROM academic_list order by abs(year) desc,abs(semester) desc ");
					while($row= $qry->fetch_assoc()):
						$questions = $conn->query("SELECT * FROM question_list where academic_id ={$row['id']} ")->num_rows;
						$answers = $conn->query("SELECT * FROM evaluation_list where academic_id ={$row['id']} ")->num_rows;
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo $row['year'] ?></b></td>
						<td><b><?php echo $row['semester'] ?></b></td>
						<td class="text-center"><b><?php echo number_format($questions) ?></b></td>
						<td class="text-center"><b><?php echo number_format($answers) ?></b></td>
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" style="">
		                      <a class="dropdown-item manage_questionnaire" href="index.php?page=manage_questionnaire&id=<?php echo $row['id'] ?>">Manage</a>
		                    </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.new_academic').click(function(){
			uni_modal("New academic","<?php echo $_SESSION['login_view_folder'] ?>manage_academic.php")
		})
		$('.manage_academic').click(function(){
			uni_modal("Manage academic","<?php echo $_SESSION['login_view_folder'] ?>manage_academic.php?id="+$(this).attr('data-id'))
		})
		$('.delete_academic').click(function(){
		_conf("Are you sure to delete this academic?","delete_academic",[$(this).attr('data-id')])
		})
		$('.make_default').click(function(){
		_conf("Are you sure to make this academic year as the system default?","make_default",[$(this).attr('data-id')])
		})
		$('#list').dataTable()
	})
	function delete_academic($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_academic',
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
	function make_default($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=make_default',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Dafaut Academic Year Updated",'success')
					setTimeout(function(){
						location.reload()
					},1500)
				}
			}
		})
	}
</script>