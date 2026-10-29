<?php
// Calculate total logic in JS or PHP? easier in JS for live update
?>
<div class="modal-header">
    <h5 class="modal-title" style="float: left" id="editTAModalLabel">Edit TA Claim - Movement #<?php echo $movement->id; ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php //echo site_url('new_movement/update_expenses'); ?>
<form action="" method="post" id="form_edit_expenses_data">
    <div class="modal-body" style="top: -15px; margin-bottom: -35px">
        <input type="hidden" name="movement_id" value="<?php echo $movement->id; ?>">

        <div class="alert alert-info py-2" style="font-size: 13px;">
            <strong>Employee:</strong> <?php echo $movement->first_name . ' ' . $movement->last_name; ?> <br>
            <strong>Purpose:</strong> <?php echo $movement->purpose; ?>
        </div>

        <table class="table table-bordered table-sm">
            <thead class="bg-light">
                <tr>
                    <th width="20%">Travel Segment</th>
                    <th width="20%">Transport</th>
                    <th width="15%">Amount</th>
                    <th width="15%">Modify</th>
                    <th width="30%">Note</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $grand_total = 0;
                $modify_total = 0;
                // dd($expenses);
                if(!empty($expenses)):
                    foreach($expenses as $exp):
                        $exp->approve_amount = @$exp->approve_amount == 0 ? $exp->amount : @$exp->approve_amount;
                        $grand_total += $exp->amount;
                        $modify_total += @$exp->approve_amount;
                ?>
                <tr>
                    <td>
                        <small class="text-muted">Travel ID: <?php echo $exp->travel_id; ?></small>
                         <input type="hidden" name="expenses[<?php echo $exp->id; ?>][id]" value="<?php echo $exp->id; ?>">
                    </td>
                    <td>
                        <select name="expenses[<?php echo $exp->id; ?>][transport_type]" class="form-control form-control-sm">
                            <option value="Bus" <?php echo ($exp->transport_type == 'Bus') ? 'selected' : ''; ?>>Bus</option>
                            <option value="Rickshaw" <?php echo ($exp->transport_type == 'Rickshaw') ? 'selected' : ''; ?>>Rickshaw</option>
                            <option value="CNG" <?php echo ($exp->transport_type == 'CNG') ? 'selected' : ''; ?>>CNG</option>
                            <option value="Uber/RideShare" <?php echo ($exp->transport_type == 'Uber/RideShare') ? 'selected' : ''; ?>>Uber/RideShare</option>
                            <option value="Train" <?php echo ($exp->transport_type == 'Train') ? 'selected' : ''; ?>>Train</option>
                            <option value="Personal" <?php echo ($exp->transport_type == 'Personal') ? 'selected' : ''; ?>>Personal</option>
                            <option value="Other" <?php echo ($exp->transport_type == 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control form-control-sm text-right expense-amount" name="expenses[<?php echo $exp->id; ?>][amount]" value="<?php echo $exp->amount; ?>" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control form-control-sm text-right expense-modified-amt" name="expenses[<?php echo $exp->id; ?>][modified_amt]" value="<?php echo $exp->approve_amount; ?>" required>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm" name="expenses[<?php echo $exp->id; ?>][note]" value="<?php echo $exp->note; ?>">
                    </td>
                </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="4" class="text-center text-danger">No expenses found to edit.</td></tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-right font-weight-bold">Total Claim:</td>
                    <td class="text-right font-weight-bold" id="total_claim_display"><?php echo number_format($grand_total, 2); ?></td>
                    <td class="text-right font-weight-bold" id="total_claim_modify"><?= number_format($modify_total, 2); ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="form-group">
             <label for="admin_note">Admin/HR Note (Optional)</label>
             <textarea class="form-control" name="ta_admin_note" rows="2" placeholder="Reason for modification..."><?=$movement->ta_admin_note?></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <a class="btn btn-primary btn-update-expense">Update</a>
        <a class="btn btn-success btn-update-approve">Update & Approve</a>
    </div>
</form>

<script>
    $('.expense-amount').on('input', function() {
        var total = 0;
        $('.expense-amount').each(function() {
            var val = parseFloat($(this).val());
            if(!isNaN(val)) {
                total += val;
            }
        });
        $('#total_claim_display').text(total.toFixed(2));
    });

    $('.expense-modified-amt').on('input', function() {
        var total = 0;
        $('.expense-modified-amt').each(function() {
            var val = parseFloat($(this).val());
            if(!isNaN(val)) {
                total += val;
            }
        });
        $('#total_claim_modify').text(total.toFixed(2));
    });
</script>
