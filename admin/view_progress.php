<?php
session_start();
include 'db_connect.php';
$id = $_GET['id'];
?>
<div class="container-fluid">
	<div id="post-field">
	<?php 
	$progress = $conn->query("SELECT p.*,concat(u.firstname,' ',u.lastname) as uname,u.avatar FROM task_progress p inner join task_list t on t.id = p.task_id inner join employee_list u on u.id = t.employee_id where p.task_id = $id order by unix_timestamp(p.date_created) desc ");
	if($progress->num_rows > 0):
	while($row = $progress->fetch_assoc()):
	?>
		<div class="post">
              <div class="user-block">
              	<?php if($_SESSION['login_type'] == 0): ?>
              	<span class="btn-group dropleft float-right">
				  <span class="btndropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="cursor: pointer;">
				    <i class="fa fa-ellipsis-v"></i>
				  </span>
				  <div class="dropdown-menu">
				  	<a class="dropdown-item manage_progress" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"  data-task="<?php echo $row['task'] ?>">Edit</a>
                  	<div class="dropdown-divider"></div>
                     <a class="dropdown-item delete_progress" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
				  </div>
				</span>
				<?php endif; ?>
                <img class="img-circle img-bordered-sm" src="assets/uploads/<?php echo $row['avatar'] ?>" alt="user image">
                <span class="username">
                  <a href="#"><?php echo ucwords($row['uname']) ?></a>
                </span>
                <span class="description">
                	<span class="fa fa-calendar-day"></span>
                	<span><b><?php echo date('M d, Y',strtotime($row['date_created'])) ?></b></span>
            	</span>
              </div>
              <div>
               <?php echo html_entity_decode($row['progress']) ?>
              </div>

              <p>
              </p>
        </div>
    <?php endwhile; ?>
    <?php else: ?>
    	<div class="mb-2">
    	<center><i>No Progress Yet</i></center>
    		
    	</div>
    <?php endif; ?>
    </div>
</div>
<style>
	.users-list>li img {
	    border-radius: 50%;
	    height: 67px;
	    width: 67px;
	    object-fit: cover;
	}
	.users-list>li {
		width: 33.33% !important
	}
	.truncate {
		-webkit-line-clamp:1 !important;
	}
</style>
<div class="modal-footer display p-0 m-0">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
<script>
	$('.manage_progress').click(function(){
		uni_modal("<i class='fa fa-edit'></i> Edit Progress","manage_progress.php?tid=<?php echo $id ?>&id="+$(this).attr('data-id'),'large')
	})
	$('.delete_progress').click(function(){
	_conf("Are you sure to delete this progress?","delete_progress",[$(this).attr('data-id')])
	})
	function delete_progress($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_progress',
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
