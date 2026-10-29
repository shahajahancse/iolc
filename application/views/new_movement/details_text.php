
    <style>
        .timeline-modern { list-style: none; padding: 0; position: relative; }
        .timeline-modern:before {
            content: ''; position: absolute; top: 0; bottom: 0;
            left: 20px; width: 2px; background: #e9ecef;
        }
        .timeline-item { position: relative; padding-left: 50px; margin-bottom: 25px; }
        .timeline-icon {
            position: absolute; left: 0; top: 0; width: 40px; height: 40px;
            border-radius: 50%; text-align: center; line-height: 40px; color: white;
        }
        .timeline-icon-start { background: #0177bc; }
        .timeline-icon-meeting { background: #fff; border: 2px solid #0177bc; color: #0177bc; line-height: 36px; }
        .timeline-icon-end { background: #000; }
        
        .location-text { font-size: 13px; color: #525f7f; margin: 5px 0; font-weight: bold; }
        .timeline-time { font-size: 12px; color: #8898aa; background: #f6f9fc; padding: 2px 8px; border-radius: 4px; }
    </style>

    <div class="row mb-3">
        <div class="col-md-12">
            <button onclick="window.close()" class="btn btn-default btn-sm">Close</button>
            <button onclick="window.print()" class="btn btn-primary btn-sm pull-right"><i class="fa fa-print"></i> Print</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Calculate Total Distance -->
            <?php 
                $total_km = 0;
                if(isset($travels)) {
                    foreach($travels as $t) {
                        $total_km += floatval($t->distance_km);
                    }
                }
            ?>
            <div class="box box-solid">
                <div class="box-body">
                    <h4 style="margin-top:0;">
                        Movement Summary
                        <small>#<?php echo $movement->id; ?> | <?php echo date('d M Y', strtotime($movement->start_time)); ?></small>
                        <span class="pull-right badge bg-aqua">
                            <i class="fa fa-road"></i> <?php echo isset($total_km) ? round($total_km, 2) : 0; ?> km
                        </span>
                    </h4>
                    <hr>
                    <ul class="timeline-modern">
                        <li class="timeline-item">
                            <div class="timeline-icon timeline-icon-start"><i class="fa fa-map-marker"></i></div>
                            <div class="timeline-content">
                                <h5>Start Point</h5>
                                <p class="location-text">
                                    <?php echo !empty($movement->start_location) ? $movement->start_location : 'Location not recorded'; ?>
                                </p>
                                <span class="timeline-time"><?php echo date('h:i A', strtotime($movement->start_time)); ?></span>
                            </div>
                        </li>

                        <?php 
                        $t_index = 0; 
                        foreach($meetings as $meeting): 
                            $travel_info = isset($travels[$t_index]) ? $travels[$t_index] : null;
                            $t_index++;
                        ?>
                        <li class="timeline-item">
                            <div class="timeline-icon timeline-icon-meeting"><i class="fa fa-briefcase"></i></div>
                            <div class="timeline-content">
                                <h5>Meeting: <?php echo $meeting->client_name; ?></h5>
                                <!-- Distance Info -->
                                <?php if($travel_info && floatval($travel_info->distance_km) >= 0): ?>
                                    <small class="d-block text-muted mb-1">
                                        <i class="fa fa-road"></i> Traveled: <strong><?php echo $travel_info->distance_km; ?> km</strong>
                                    </small>
                                <?php endif; ?>
                                
                                <p class="location-text">
                                     <?php echo !empty($meeting->location) ? $meeting->location : 'Location not recorded'; ?>
                                </p>
                                
                                <?php if(!empty($meeting->feedback)): ?>
                                    <div style="margin-top: 5px; padding: 5px; background: #f8f9fa; border-left: 2px solid #ffc107;">
                                        <small><strong>Feedback:</strong> "<?php echo $meeting->feedback; ?>"</small>
                                    </div>
                                <?php endif; ?>

                                <span class="timeline-time"><?php echo date('h:i A', strtotime($meeting->start_time)); ?></span>
                            </div>
                        </li>
                        <?php endforeach; ?>

                        <?php if($movement->end_time): 
                            $last_travel = isset($travels[$t_index]) ? $travels[$t_index] : null;
                        ?>
                        <li class="timeline-item">
                            <div class="timeline-icon timeline-icon-end"><i class="fa fa-home"></i></div>
                            <div class="timeline-content">
                                <h5>Movement Ended</h5>
                                <?php if($last_travel && floatval($last_travel->distance_km) >= 0): ?>
                                    <small class="d-block text-muted mb-1">
                                        <i class="fa fa-road"></i> Traveled: <strong><?php echo $last_travel->distance_km; ?> km</strong>
                                    </small>
                                <?php endif; ?>
                                <p class="location-text">
                                     <?php echo isset($last_travel->to_location) ? $last_travel->to_location : 'End Location'; ?>
                                </p>
                                <span class="timeline-time"><?php echo date('h:i A', strtotime($movement->end_time)); ?></span>
                            </div>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- TA CLAIM TABLE -->
            <?php if(!empty($expenses)): ?>
            <div class="box box-solid">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-money text-success"></i> TA Claim Details</h3>
                    <div class="box-tools pull-right">
                         <span class="badge bg-blue"><?php echo ucfirst(str_replace('_', ' ', $movement->ta_status)); ?></span>
                    </div>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Note</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $total = 0; ?>
                            <?php foreach($expenses as $exp): ?>
                            <tr>
                                <td><?php echo $exp->transport_type; ?></td>
                                <td><?php echo $exp->note; ?></td>
                                <td class="text-right font-weight-bold"><?php echo number_format($exp->amount, 2); ?></td>
                            </tr>
                            <?php $total += $exp->amount; ?>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="2" class="font-weight-bold text-uppercase">Total</td>
                                <td class="text-right font-weight-bold" style="color: #0177bc; font-size: 16px;">
                                    ৳ <?php echo number_format($total, 2); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
