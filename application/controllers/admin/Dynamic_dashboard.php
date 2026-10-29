<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dynamic_dashboard extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("New_movement_model");
        $this->load->model('Timesheet_model');
        $this->load->model('Xin_model'); // Fix: Load Xin_model for site_title()
        // Check session or auth if not handled by MY_Controller usually
        // Assuming MY_Controller handles basic auth check

        // $session = $this->session->userdata('username');
        // if (empty($session)) {
        //     redirect('admin/');
        // }
    }

    public function index()
    {
        $data['title'] = $this->Xin_model->site_title();
        $data['breadcrumbs'] = 'Dynamic Dashboard';
        $data['path_url'] = 'dynamic_dashboard';

        $role_id = $this->session->userdata('username')['role_id'];
        if (!in_array($role_id, [1, 2, 4])) { // Admin, HR, Manager
            redirect('admin/dashboard');
        }

        $date = $this->input->get('date') ? $this->input->get('date') : date('Y-m-d');

        // 1. Attendance Data
        $present_list = $this->Timesheet_model->get_today_present(0, ['Present', 'HalfDay'], $date);
        $late_list = $this->Timesheet_model->get_today_present(1, ['Present', 'HalfDay'], $date);
        $absent_list = $this->Timesheet_model->get_today_present(0, ['Absent'], $date);

        $data['present_count'] = is_array($present_list) ? count($present_list) : 0;
        $data['late_count'] = is_array($late_list) ? count($late_list) : 0;
        $data['absent_count'] = is_array($absent_list) ? count($absent_list) : 0;

        $data['present_list'] = $present_list;
        $data['late_list'] = $late_list;
        $data['absent_list'] = $absent_list;

        // 2. Movement Data
        $active_movements = $this->New_movement_model->get_all_active_movements();
        $on_meeting = [];
        $on_travel = [];
        $active_user_ids = [];

        foreach ($active_movements as $m) {
            $status_detail = $this->New_movement_model->get_detailed_status($m->id);
            $m->current_status_detail = $status_detail;
            $active_user_ids[] = $m->employee_id;

            if ($status_detail['status'] == 'Meeting') {
                $on_meeting[] = $m;
            } else {
                $on_travel[] = $m;
            }
        }

        $data['on_meeting'] = $on_meeting;
        $data['on_travel'] = $on_travel; // Movement/Field

        // 3. In Office
        $all_present_users = [];
        if (is_array($present_list)) {
            foreach ($present_list as $p) {
                $all_present_users[] = $p;
            }
        }
        $in_office = [];
        foreach ($all_present_users as $p) {
            if (!in_array($p->emp_id, $active_user_ids)) {
                $in_office[] = $p;
            }
        }
        $data['in_office'] = $in_office;

        // 4. Events
        $data['todays_meetings'] = $this->New_movement_model->get_todays_movement_meetings();
        $data['todays_birthdays'] = $this->New_movement_model->get_todays_birthdays();

        $data['selected_date'] = $date;

        $data['subview'] = $this->load->view("admin/dashboard/dynamic_dashboard", $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data); // Assuming standard layout
    }
}
