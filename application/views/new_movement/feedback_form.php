<style>
    .feedback-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        padding: 50px;
        border: none;
        max-width: 800px;
        margin: 0 auto;
        text-align: center;
    }

    /* .form-control-custom {
        border-radius: 12px;
        border: 1px solid #e9ecef;
        background-color: #f8f9fe;
        padding: 15px;
        font-size: 15px;
        transition: all 0.3s;
        color: #32325d;
    }
    .form-control-custom:focus {
        background-color: #fff;
        border-color: #5e72e4;
        box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11);
    } */
    .btn-submit-feedback {
        background: linear-gradient(87deg, #11cdef 0, #1171ef 100%);
        border-radius: 30px;
        padding: 12px 40px;
        font-weight: 600;
        border: none;
        box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11);
        color: white;
        transition: all 0.3s;
        font-size: 16px;
    }

    .btn-submit-feedback:hover {
        transform: translateY(-2px);
        box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1);
        color: white;
    }
</style>

<div class="feedback-card">
    <h2 style="font-weight: 800; color: #32325d; margin-bottom: 30px;">📝 Meeting Feedback</h2>
    <p class="text-muted mb-4">How did the meeting go? Please provide a brief summary.</p>

    <form action="<?php echo site_url('new_movement/submit_feedback'); ?>" method="post">
        <div class="form-group">
            <textarea name="feedback" class="form-control form-control-custom" rows="5" required
                placeholder="Enter meeting outcomes, next steps, or general notes..."></textarea>
        </div>

        <div class="row text-left mb-4">
            <div class="col-12">
                <h6 class="text-uppercase text-muted font-weight-bold" style="font-size: 0.8rem;">Edit Client Details
                    (Optional)</h6>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-2">
                    <label class="small text-muted mb-1">Client/Company Name</label>
                    <input type="text" name="client_name" class="form-control form-control-sm"
                        placeholder="Update Name...">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-2">
                    <label class="small text-muted mb-1">Contact Person</label>
                    <input type="text" name="contact_person" class="form-control form-control-sm"
                        placeholder="Update Person...">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-2">
                    <label class="small text-muted mb-1">Job Title</label>
                    <input type="text" name="contact_job_title" class="form-control form-control-sm"
                        placeholder="Title">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-2">
                    <label class="small text-muted mb-1">Email</label>
                    <input type="email" name="contact_email" class="form-control form-control-sm" placeholder="Email">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-2">
                    <label class="small text-muted mb-1">Phone</label>
                    <input type="text" name="contact_phone" class="form-control form-control-sm" placeholder="Phone">
                </div>
            </div>
        </div>

        <?php if (isset($is_crm_linked) && $is_crm_linked): ?>
            <div class="form-group text-left mt-3">
                <label class="font-weight-bold text-uppercase small text-muted" style="letter-spacing:1px;">Update CRM Lead
                    Status (Optional)</label>
                <select name="crm_status" class="form-control form-control-custom">
                    <option value="">-- Keep Current Status --</option>
                    <?php if (isset($statuses) && !empty($statuses)): ?>
                        <?php foreach ($statuses as $st): ?>
                            <option value="<?php echo $st->id; ?>"><?php echo $st->title; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <button type="submit" class="btn btn-submit-feedback">
                <i class="fa fa-paper-plane"></i> Submit Feedback
            </button>
        </div>
    </form>
</div>