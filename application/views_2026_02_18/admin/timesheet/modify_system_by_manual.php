
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success">
            <?php echo $this->session->flashdata('success'); ?>
            <script>
                setTimeout(function() {
                    $(".alert-success").hide(5000);
                }, 3000);
            </script>
        </div>
    <?php endif; ?>

    <div class="box-body">
        <?php $attributes = array('name' => 'add_leave', 'autocomplete' => 'off');?>
        <?php echo form_open('admin/timesheet/modify_system_by_manual', $attributes, '');?>
        <div class="bg-white">
            <div class="box-block">
                <div class="form-group">
                    <textarea class="form-control" name="reason" rows="3" required></textarea>
                </div>
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to submit this request?')"> <i class="fa fa-check-square-o"></i> Submit </button>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>



