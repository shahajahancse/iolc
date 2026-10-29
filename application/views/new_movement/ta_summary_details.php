<style>
    .modal-header .close {
        margin-top: -30px !important;
    }
</style>
<div class="modal-header">
    <h5 class="modal-title" style="text-align: center;">TA Summary Details <br>
        <small style="color : #141313">Date : <?php echo date('d-m-Y', strtotime($start_date)); ?> to <?php echo date('d-m-Y', strtotime($end_date)); ?></small>
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body" style="top: -15px; margin-bottom: -35px">
    <div class="alert alert-info py-2" style="font-size: 13px; margin-bottom: 0px;">
        <strong>Employee:</strong> <?php echo $employee[0]->first_name . ' ' . $employee[0]->last_name; ?> <br>
    </div>

    <table class="table table-bordered table-sm">
        <thead class="bg-light">
            <tr>
                <th width="5%">Sl.</th>
                <th width="30%">Meeting/purpose</th>
                <th width="13%">Date</th>
                <th width="30%">Hr Remark</th>
                <th width="10%">Amount</th>
                <th width="10%">Modify</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $grand_total = 0;
            $modify_total = 0;
            if(!empty($movements)):
                foreach($movements as $key => $exp):
                    $grand_total += $exp->ta_amount;
                    $modify_total += $exp->ta_app_amt;
            ?>
            <tr>
                <td><?php echo $key + 1; ?></small></td>
                <td><?php echo $exp->purpose; ?></td>
                <td><?php echo date('d-m-Y', strtotime($exp->end_time)); ?></td>
                <td><?php echo $exp->ta_admin_note; ?></td>
                <td><?php echo number_format($exp->ta_amount, 2); ?></td>
                <td><?php echo number_format($exp->ta_app_amt, 2); ?></td>
            </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="4" class="text-center text-danger">No expenses found to edit.</td></tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right font-weight-bold">Total Claim:</td>
                <td class="font-weight-bold"><?php echo number_format($grand_total, 2); ?></td>
                <td class="font-weight-bold"><?= number_format($modify_total, 2); ?></td>
            </tr>
        </tfoot>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>

