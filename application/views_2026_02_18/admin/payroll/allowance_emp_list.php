
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

<!-- Basic multiple Column Form section start -->
<div class="card">
    <div class="card-body">
        <h4> Allowance Add</h4>
        <?php $attributes = array('id' => 'unit_insert', 'autocomplete' => 'off', 'class' => 'add form-hrm ');?>
        <?php echo form_open_multipart('admin/payroll/allowance_emp_add', $attributes);?>
        <div class="row">
            <div class="form-group mb-2 col-md-4">
                <label for="select_data" class="mr-2">Select Employee</label>
                <select class="form-control" id="select_data" name="emp_id" required>
                    <option value="">Select Employee</option>
                    <?php
                    $users = $this->db->select('user_id,first_name,last_name')->where_in('status',[1,4,5])->get('xin_employees')->result();
                    foreach($users as $user) {
                    ?>
                    <option value="<?php echo $user->user_id ?>"><?php echo $user->first_name.' '.$user->last_name; ?>
                    </option>
                    <?php } ?>
                </select>
            </div>

            <?php
            $ads = $this->db->where('status',1)->where_in('type',[1,3])->get('xin_allowance_deduction_types')->result();
            ?>
            <div class="form-group col-md-2">
                <label for="deductType">Type</label>
                <select name="type" id="deductType" class="form-control" required>
                    <option value="">Select Type</option>
                    <?php foreach($ads as $ad){ ?>
                    <option value="<?php echo $ad->id ?>"><?php echo $ad->name ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group col-md-2">
                <label for="pay_amount">Amount</label>
                <input type="number" class="form-control form-control-sm" id="pay_amount" name="pay_amount" placeholder="Set Amount" required>
            </div>

            <div class="form-group col-md-2">
                <label>Pay Month</label>
                <input type="month" class="form-control" id="effective_month" name="effective_month" required  >
            </div>

            <div class="form-group col-md-2">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="">Select Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-12">
                <label for="formGroupExampleInput2">Remarks</label>
                <textarea class="form-control" id="remarks" name="remarks" placeholder="Enter reason" required></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mb-2" style="float:right" name="btn_advanced">Save</button>
        <?php echo form_close(); ?>




        <br><br>
        <p class="h4">Allowance List</p>
        <table class="table table-striped table-bordered" id="table_id">
            <thead>
                <tr>
                    <th scope="col" class="text-center">Sl.</th>
                    <th scope="col" class="text-center">Employee Name</th>
                    <th scope="col" class="text-center">Pay Type</th>
                    <th scope="col" class="text-center">Amount</th>
                    <th scope="col" class="text-center">Effective_month</th>
                    <th scope="col" class="text-center">Remarks</th>
                    <th scope="col" class="text-center">Status</th>
                    <th scope="col" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody style="height: 60vh; overflow-y: auto;" >
                <?php $i=1; foreach($results as $row){ ?>
                <tr class="text-center">
                    <td><?php echo $i++?></td>
                    <td><?= $row->first_name.' '.$row->last_name?></td>
                    <td><?= $row->pay_type?></td>
                    <td><?= $row->pay_amount?></td>
                    <td><?= date("M Y", strtotime($row->effective_month))?></td>
                    <td><?= $row->remarks?></td>
                    <td>
                        <?php if($row->status == 1){ ?>
                        <span style="background-color: green;" class="badge badge-success">Active</span>
                        <?php } else { ?>
                        <span class="badge badge-danger">Inactive</span>
                        <?php } ?>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-info dropdown-toggle" type="button" data-toggle="dropdown"> Action <span class="caret"></span>
                            </button>

                            <ul class="dropdown-menu">
                                <?php if ($row->status != 2) { ?>
                                    <li>
                                        <a href="javascript:void(0)"  class="edit-data-btn" data-id="<?= $row->id ?>"  data-pay_amount="<?= $row->pay_amount ?>" data-effective_month="<?= $row->effective_month ?>" data-type="<?= $row->type ?>" data-remarks="<?= $row->remarks ?>" data-status="<?= $row->status ?>" data-toggle="modal" data-target="#allowanceModal"> Edit </a>
                                    </li>
                                <?php } ?>
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
<div class="modal fade" id="allowanceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Edit Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="row_id" class="form-control" value="">

                <?php
                $ads = $this->db->where('status',1)->where_in('type',[1,3])->get('xin_allowance_deduction_types')->result();
                ?>

                <label>Type:</label>
                <select class="form-control" id="etype" name="type">
                    <option value="">Select</option>
                    <?php foreach ($ads as $row) {?>
                    <option value="<?php echo $row->id ?>"><?php echo $row->name ?></option>
                    <?php } ?>
                </select>

                <label for="payAmount">Pay Amount:</label>
                <input type="text" id="payAmount" class="form-control" value="">

                <label for="effectiveMonth">Effective Month:</label>
                <input type="month" id="effectiveMonth" class="form-control" value="">

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
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#table_id').DataTable();
        $('#select_data').select2();
    });

    $('.edit-data-btn').on('click', function() {
        var rowId = $(this).data('id');
        var pay_amount = $(this).data('pay_amount');
        var effective_month = $(this).data('effective_month').substr(0,7); // convert year and month
        var remarks = $(this).data('remarks');
        var type = $(this).data('type');
        var status = $(this).data('status');

        $('#row_id').val(rowId);
        $('#payAmount').val(pay_amount);
        $('#effectiveMonth').val(effective_month);
        $('#eremarks').val(remarks);

        $('#etype').val(String(type)).trigger('change');
        $('#estatus').val(String(status)).trigger('change');
    });

    $('#save_data').on('click', function() {
        var rowId = $('#row_id').val();
        var pay_amount = $('#payAmount').val();
        var effective_month = $('#effectiveMonth').val();
        var type = $('#etype').val();
        var status = $('#estatus').val();
        var remarks = $('#eremarks').val();
        var dataToSend = {
            rowId: rowId,
            pay_amount: pay_amount,
            effective_month: effective_month,
            type: type,
            status: status,
            remarks: remarks
        };
        $('#allowanceModal').modal('hide');
        // console.log(dataToSend);return false;
        var url = "<?php echo base_url('admin/payroll/update_allowance_emp')?>";
        // Send the AJAX request to update the data on the server.
        $.ajax({
            type: "POST",
            url: url,
            data: dataToSend,
            success: function(response) {
                showSuccessAlert("Update Successfully");
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", error);
            }
        });
    });
</script>

