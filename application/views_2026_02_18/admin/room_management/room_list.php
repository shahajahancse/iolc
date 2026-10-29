<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Meeting Rooms</h3>
                <div class="box-tools pull-right">
                    <?php if($is_admin): ?>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addRoomModal">
                            <i class="fa fa-plus"></i> Create New Room
                        </button>
                        <a href="<?php echo site_url('admin/room_management/approvals'); ?>" class="btn btn-warning">
                             <i class="fa fa-list"></i> Booking List
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo site_url('admin/room_management/book_room'); ?>" class="btn btn-success">
                        <i class="fa fa-calendar-plus-o"></i> Book Room
                    </a>
                </div>
            </div>
            <div class="box-body">
                 <div class="row">
                    <?php foreach($rooms as $room): ?>
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="info-box bg-gray-light" style="min-height: 140px; margin-bottom: 20px; box-shadow: 0 1px 1px rgba(0,0,0,0.1); border: 1px solid #d2d6de;">
                                <span class="info-box-icon bg-aqua" style="height: 140px; line-height: 140px;">
                                    <i class="fa fa-users"></i>
                                </span>

                                <div class="info-box-content">
                                    <span class="info-box-text" style="text-transform: none; font-size: 18px; font-weight: bold;"><?php echo $room->name; ?></span>
                                    <span class="info-box-number" style="font-weight: normal; font-size: 14px; margin-bottom: 5px;">
                                        <i class="fa fa-users text-muted"></i> Capacity: <?php echo $room->capacity; ?><br>
                                        <i class="fa fa-map-marker text-muted"></i> <?php echo $room->location; ?>
                                    </span>

                                     <div class="progress-group" style="padding-top: 10px;">
                                        <span class="progress-text text-muted" style="font-size: 12px;"><?php echo $room->description; ?></span>
                                     </div>
                                </div>

                                <?php if($is_admin): ?>
                                    <div class="box-footer" style="background: none; border-top: 1px solid #ddd; padding: 10px;">
                                         <button class="btn btn-primary btn-xs edit-room-btn"
                                            data-id="<?php echo $room->id; ?>"
                                            data-name="<?php echo $room->name; ?>"
                                            data-capacity="<?php echo $room->capacity; ?>"
                                            data-location="<?php echo $room->location; ?>"
                                            data-description="<?php echo $room->description; ?>"
                                            data-status="<?php echo $room->status; ?>"
                                            data-toggle="modal" data-target="#editRoomModal">
                                            Edit
                                        </button>
                                        <a href="<?php echo site_url('admin/room_management/delete_room/'.$room->id); ?>"
                                           class="btn btn-danger btn-xs pull-right"
                                           onclick="return confirm('Are you sure?')">
                                            Delete
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                 </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Room Modal -->
<div class="modal fade" id="addRoomModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="<?php echo site_url('admin/room_management/manage_room'); ?>" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title">Add New Room</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Room Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Capacity</label>
                                <input type="number" name="capacity" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Location</label>
                                <input type="text" name="location" class="form-control" placeholder="e.g. 1st Floor">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Room</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Room Modal -->
<div class="modal fade" id="editRoomModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="<?php echo site_url('admin/room_management/manage_room'); ?>" method="post">
            <input type="hidden" name="room_id" id="edit_room_id">
            <div class="modal-content">
                 <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title">Edit Room</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Room Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Capacity</label>
                                <input type="number" name="capacity" id="edit_capacity" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-group">
                                <label>Location</label>
                                <input type="text" name="location" id="edit_location" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" id="edit_description" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit_status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                 <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Room</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.edit-room-btn').click(function() {
        $('#edit_room_id').val($(this).data('id'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_capacity').val($(this).data('capacity'));
        $('#edit_location').val($(this).data('location'));
        $('#edit_description').val($(this).data('description'));
        $('#edit_status').val($(this).data('status'));
    });
});
</script>
