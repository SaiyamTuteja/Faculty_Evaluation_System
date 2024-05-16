
<?php include 'db_connect.php'; ?>
<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-header">
            <div class="card-tools">
                <a class="btn btn-block btn-sm btn-default btn-flat border-primary new_class" href="javascript:void(0)"><i class="fa fa-plus"></i> Add New</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table tabe-hover table-bordered" id="list">
                <colgroup>
                    <col width="5%">
                    <col width="60%">
                </colgroup>
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Class</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    // Prepared the SQL statement
                    $stmt = $conn->prepare("SELECT *, CONCAT(curriculum, ' ', level, '-', section) AS class FROM class_list ORDER BY class ASC");
                    $stmt->execute(); // Execute the prepared statement
                    $result = $stmt->get_result(); // Get the result set

                    // Fetch data from the result set
                    while ($row = $result->fetch_assoc()):
                    ?>
                        <tr>
                            <th class="text-center"><?php echo $i++ ?></th>
                            <td><b><?php echo htmlspecialchars($row['class']) ?></b></td> <!-- Use htmlspecialchars to escape HTML entities -->
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="javascript:void(0)" data-id='<?php echo $row['id'] ?>' class="btn btn-primary btn-flat manage_class">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-flat delete_class" data-id="<?php echo $row['id'] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
    $(document).ready(function () {
        $('#list').dataTable();
        $('.new_class').click(function () {
            uni_modal("New class", "<?php echo $_SESSION['login_view_folder'] ?>manage_class.php");
        });
        $('.manage_class').click(function () {
            uni_modal("Manage class", "<?php echo $_SESSION['login_view_folder'] ?>manage_class.php?id=" + $(this).attr('data-id'));
        });
        $('.delete_class').click(function () {
            _conf("Are you sure to delete this class?", "delete_class", [$(this).attr('data-id')]);
        });
    });

    function delete_class(id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_class',
            method: 'POST',
            data: { id: id },
            success: function (resp) {
                if (resp == 1) {
                    alert_toast("Data successfully deleted", 'success');
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                }
            }
        });
    }
</script>
