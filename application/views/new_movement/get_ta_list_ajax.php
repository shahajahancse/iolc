    <thead>
        <tr>
            <th>SL</th>
            <th>Date</th>
            <th>Employee</th>
            <th>Purpose</th>
            <th>Amount</th>
            <th>M.Amount</th>
            <th>TA Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody style="height: 60vh !important; overflow-y: auto;">
        <?php if(!empty($movements)): ?>
            <?php foreach($movements as $key => $mov): ?>
                <tr>
                    <td><?php echo ++$key; ?></td>
                    <td><?php echo date('d M, Y', strtotime($mov->created_at)); ?></td>
                    <td><?php echo $mov->first_name . ' ' . $mov->last_name; ?></td>
                    <td><?php echo $mov->purpose; ?></td>
                    <td><?php echo $mov->ta_amount; ?></td>
                    <td><?php echo $mov->ta_app_amt; ?></td>

                    <td>
                        <?php if($mov->ta_status == 'pending'): ?>
                            <span class="label label-warning">
                                <?php echo ucfirst(str_replace('_', ' ', $mov->ta_status)); ?>
                            </span>
                        <?php elseif ($mov->ta_status == 'rejected') : ?>
                            <span class="label label-danger">
                                <?php echo ucfirst(str_replace('_', ' ', $mov->ta_status)); ?>
                            </span>
                        <?php else: ?>
                            <span class="label label-success">
                                <?php echo ucfirst(str_replace('_', ' ', $mov->ta_status)); ?>
                            </span>
                        <?php endif; ?>
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
                                    <a href="<?php echo site_url('new_movement/details/'.$mov->id .'/'. 1); ?>"> <i class="fa fa-eye"></i>View Details</a>
                                </li>
                                <li class="divider"></li>
                                <!-- Accounts / HR Approve -->
                                <?php $my_role = $this->session->userdata('username')['role_id']; ?>
                                <?php if(in_array($my_role, [1,2,4]) && $mov->ta_status == 'pending' ): ?>
                                    <li>
                                        <?php /*
                                        href="<?php echo site_url('new_movement/update_ta_status/'.$mov->id.'/approved'); ?>" onclick="return confirm('Approve this TA?')" */ ?>
                                        <a href="javascript:void(0)" onclick="editTAModal(<?php echo $mov->id; ?>)"><i class="fa fa-check"></i> Approved
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="javascript:void(0)" onclick="if(confirm('Reject this TA?')){update_ta_status(<?php echo $mov->id; ?>, 'rejected')}" ><i class="fa fa-check"></i> Rejected </a>
                                    </li>
                                    <li class="divider"></li>
                                <?php endif; ?>
                                <!-- SUPPER ADMIN ACTIONS to PAY -->
                                <?php if($my_role == 1 && $mov->ta_status == 'approved' ): ?>
                                    <li>
                                        <a href="javascript:void(0)" onclick="if(confirm('Paid this TA?')){update_ta_status(<?php echo $mov->id; ?>, 'paid')}" ><i class="fa fa-check"></i> Paid </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="javascript:void(0)" onclick="if(confirm('Reject this TA?')){update_ta_status(<?php echo $mov->id; ?>, 'rejected')}" ><i class="fa fa-check"></i> Rejected </a>
                                    </li>
                                    <li class="divider"></li>
                                <?php endif; ?>
                                <!-- HR/Accounts ACTIONS to handover -->
                                <?php if(in_array($my_role, [2,4]) && $mov->ta_status == 'paid' ): ?>
                                    <li>
                                        <!-- <a href="<?php echo site_url('new_movement/update_ta_status/'.$mov->id.'/handed_over'); ?>" onclick="return confirm('Approve this TA?')"><i class="fa fa-check"></i> Handover
                                        </a> -->
                                        <a href="javascript:void(0)" onclick="if(confirm('Handover this TA?')){update_ta_status(<?php echo $mov->id; ?>, 'handed_over')}" ><i class="fa fa-check"></i> Handover </a>
                                    </li>
                                    <li class="divider"></li>
                                <?php endif; ?>

                                <!-- EDIT TA AMOUNT (HR/Admin) -->
                                <?php if(in_array($mov->ta_status, ['pending', 'hr_approved', 'approved'])): ?>
                                    <li>
                                        <a href="javascript:void(0)" onclick="editTAModal(<?php echo $mov->id; ?>)"><i class="fa fa-pencil"></i> Edit Amount
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                <?php endif; ?>
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
