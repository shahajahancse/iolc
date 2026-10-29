
<style>
    .using {
        display: inline-flex;
        padding: 4.5px 14.3px 5.5px 9px;
        align-items: center;
        gap: 9px;
        border-radius: 50px;
        border: 1px solid #CCC;
        background: #FFF;
    }
</style>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success">
        <?= $this->session->flashdata('success'); ?>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger">
        <?= $this->session->flashdata('error'); ?>
    </div>
<?php endif; ?>

<script>
    setTimeout(function () {
        $('.alert').fadeOut('slow');
    }, 3000);
</script>

<div class="card">
    <div class="card-body">
        <h4>Allowance Deduct Types Add</h4>
        <?php $attributes = array('id' => 'unit_insert', 'autocomplete' => 'off', 'class' => 'add form-hrm ');?>
        <?php echo form_open_multipart('admin/payroll/allowance_deduct_types_add', $attributes);?>
        <div class="row">
            <div class="form-group col-md-3">
                <label for="types">Types <span class="text-danger">*</span> </label>
                <select class="form-control" id="ad_type" name="type" required>
                    <option value="">Select Types</option>
                    <option value="1">Allowance</option>
                    <option value="2">Deduct</option>
                    <option value="3">Both</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="name">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter name" required>
            </div>
            <div class="form-group col-md-3">
                <label for="types">Status <span class="text-danger">*</span></label>
                <select class="form-control" id="ad_status" name="status" required>
                    <option value="">Select Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-12">
                <label for="remarkssss">Remarks</label>
                <textarea class="form-control" id="remarkssss" name="remarks" placeholder="Enter remarks"></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mb-2" style="float:right" name="btn_advanced">Save</button>
        <?php echo form_close(); ?>




        <br><br>
        <p class="h4">List</p>
        <table class="table table-striped table-bordered" id="table_id">
            <thead>
                <tr>
                    <th scope="col" class="text-center">Sl.</th>
                    <th scope="col" class="text-center">Type</th>
                    <th scope="col" class="text-center">Name</th>
                    <th scope="col" class="text-center">Remarks </th>
                    <th scope="col" class="text-center">Status</th>
                    <th scope="col" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody style="height: 60vh; overflow-y: auto;">
                <?php $i=1; foreach($results as $row){ ?>
                <tr class="text-center">
                    <td><?php echo $i++?></td>
                    <td><?= $row->type == 1 ? "Allowance" : ($row->type == 2 ? "Deduct" : "Both");?></td>
                    <td><?= $row->name?></td>
                    <td><?= $row->remarks?></td>
                    <td><?= $row->status == 1 ? "Active" : "Inactive";?></td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown"> Action <span class="caret"></span> </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" class="edit-data-btn" data-type="<?=  $row->type ?>" data-id="<?=  $row->id ?>" data-name="<?= $row->name?>" data-status="<?=$row->status?>" data-remarks="<?=$row->remarks?>" data-toggle="modal" data-target="#typeModal">Edit</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <?php }?>
            </tbody>
        </table>
    </div>
</div>

    <!-- Modal -->
    <div class="modal fade" id="typeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Edit Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php $attributes = array('id' => 'unit_insert', 'autocomplete' => 'off', 'class' => 'add form-hrm');?>
                <?php $hidden = array('user_id' => $session['user_id']);?>
                <?php //echo form_open_multipart('', $attributes, $hidden);?>
                <div class="modal-body">
                    <input type="hidden" id="row_id" name="row_id">
                    <label>Type:</label>
                    <select class="form-control" id="etype" name="type">
                        <option value="1">Allowance</option>
                        <option value="2">Deduct</option>
                        <option value="3">Both</option>
                    </select>

                    <label>Name:</label>
                    <input type="text" id="ename" name="name" class="form-control">

                    <label>Status:</label>
                    <select class="form-control" id="estatus" name="status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>

                    <label>Remarks:</label>
                    <textarea id="eremarks" name="remarks" class="form-control"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success btn-sm" id="save_data">Save</button>
                </div>
                <?php //echo form_close(); ?>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function() {
        $('#table_id').DataTable();
        $('#select_data').select2();
    });

    $('.edit-data-btn').on('click', function() {
        const type    = $(this).data('type');     // 1,2,3
        const id      = $(this).data('id');
        const name    = $(this).data('name');
        const status  = $(this).data('status');   // 1 or 0
        const remarks = $(this).data('remarks');

        $('#row_id').val(id);
        $('#ename').val(name);
        $('#eremarks').val(remarks);

        // SELECT fields (important)
        $('#etype').val(String(type)).trigger('change');
        $('#estatus').val(String(status)).trigger('change');
    });

    $('#typeModal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        $('#row_id').val('');
    });
    $('#save_data').on('click', function() {
        var rowId = $('#row_id').val();
        var type = $('#etype').val();
        var name = $('#ename').val();
        var status = $('#estatus').val();
        var remarks = $('#eremarks').val();
        var dataToSend = {
            rowId: rowId,
            type: type,
            name: name,
            status: status,
            remarks: remarks
        };

        var url = "<?php echo base_url('admin/payroll/update_allowance_deduct_types/')?>";
        // Send the AJAX request to update the data on the server.
        $.ajax({
            type: "POST",
            url: url,
            data: dataToSend,
            success: function(response) {
                showSuccessAlert("Update Successfully");
                $('#typeModal').modal('hide');
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", error);
            }
        });
    });

</script>
