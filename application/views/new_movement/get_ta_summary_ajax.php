    <thead style="background: #c1e4ef; color: #fff;">
        <tr>
            <th>SL</th>
            <th>Employee</th>
            <th>Meetings</th>
            <th>Claims</th>
            <th>M.Amount</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <?php $apply_amt = 0; $approve_amt = 0; ?>
    <tbody style="height: 60vh !important; overflow-y: auto;">
        <?php if(!empty($movements)): ?>
            <?php foreach($movements as $key => $mov): ?>
                <?php
                    $apply_amt += $mov->applyed_amount;
                    $approve_amt += $mov->approved_amount;
                ?>
                <tr>
                    <td><?php echo ++$key; ?></td>
                    <td><?php echo $mov->first_name . ' ' . $mov->last_name; ?></td>
                    <td><?php echo $mov->total_movements; ?></td>
                    <td><?php echo $mov->applyed_amount; ?></td>
                    <td><?php echo $mov->approved_amount; ?></td>
                    <td>
                        <span class="label label-success">
                            <?php echo ucfirst($ta_status); ?>
                        </span>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?php echo $this->lang->line('xin_action');?>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" role="menu">
                                <li>
                                    <a href="javascript:void(0)" style="border-radius: 6px;" onclick="ta_summary_details('<?= $start_date ?>','<?= $end_date ?>', '<?= $ta_status ?>', '<?= $mov->employee_id ?>')" > <i class="fa fa-eye"></i> View Details</a>
                                </li>
                                <li class="divider"></li>
                                <?php if($ta_status == 'approved') { ?>
                                <li>
                                    <a onclick="ta_summary_approve('<?= $start_date ?>','<?= $end_date ?>', 'paid', '<?= $mov->employee_id ?>')" style="border-radius: 6px;" href="javascript:void(0)" > <i class="fa fa-check"></i> Paid </a>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="text-center" style="color: #8898aa;">No movements found. Start one above!</td>
            </tr>
        <?php endif; ?>
    </tbody>
    <tfoot style="font-weight: bold; background: #c1e4ef;">
        <tr>
            <th colspan="3" class="text-right">Total : </th>
            <th class=""><?= number_format($apply_amt, 2) ?></th>
            <th class=""><?= number_format($approve_amt, 2) ?></th>
            <?php if($ta_status == 'approved') { ?>
                <th></th>
                <th class="">
                    <a class="btn btn-primary btn-sm" onclick="ta_summary_approve('<?= $start_date ?>','<?= $end_date ?>', 'paid')" >Paid All</a>
                </th>
            <?php } else { ?>
            <th colspan="2"></th>
            <?php } ?>
        </tr>
    </tfoot>
