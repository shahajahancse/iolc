<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/API_Controller.php';

class Admin_dashboard extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('api_helper');
        $this->load->model("New_movement_model");
        $this->load->model('Timesheet_model');
    }

    // GET: Admin Dynamic Dashboard (All-in-One Realtime Data)
    public function index()
    {
        $this->_APIConfig(['methods' => ['GET']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $role_id = $user_info['user_info']->user_role_id;
            // Allow Admin (1), HR (2), or Managers (4)
            if (!in_array($role_id, [1, 2, 4])) {
                $this->api_return(['status' => false, 'message' => 'Unauthorized Access'], 403);
                return;
            }

            $date = $this->input->get('date') ? $this->input->get('date') : date('Y-m-d');

            // 1. Attendance Data (Present, Absent, Late)
            // Using Timesheet_model->get_today_present logic
            // Late Status: 1=Late, 0=OnTime (or any if logic differs)
            // Status: Present, Absent

            $present_list = $this->Timesheet_model->get_today_present(0, ['Present', 'HalfDay'], $date);
            $late_list = $this->Timesheet_model->get_today_present(1, ['Present', 'HalfDay'], $date);
            $absent_list = $this->Timesheet_model->get_today_present(0, ['Absent'], $date);

            $present_count = is_array($present_list) ? count($present_list) : 0;
            $late_count = is_array($late_list) ? count($late_list) : 0;
            $absent_count = is_array($absent_list) ? count($absent_list) : 0;

            // 2. Movement Data (On Meeting, On Travel)
            // Active movements
            $active_movements = $this->New_movement_model->get_all_active_movements();

            $on_meeting = [];
            $on_travel = [];
            $active_user_ids = [];

            foreach ($active_movements as $m) {
                // Enrich with detailed status (Location, specific status)
                $status_detail = $this->New_movement_model->get_detailed_status($m->id);
                $m->current_status_detail = $status_detail;

                // Use the detailed status string if available, or fallback to object property
                $current_status_str = isset($status_detail['status']) ? $status_detail['status'] : (isset($m->current_status) ? $m->current_status : 'Unknown');
                $m->current_status = $current_status_str;

                $active_user_ids[] = $m->employee_id;

                if ($current_status_str == 'Meeting') {
                    $on_meeting[] = $m;
                } else {
                    $on_travel[] = $m;
                }
            }

            // 3. In Office (Today)
            // Logic: Present in Attendance AND NOT in Active Movement
            // We need a list of ALL present User IDs first.
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

            // 4. Today's Events
            $todays_meetings = $this->New_movement_model->get_todays_movement_meetings();
            $todays_birthdays = $this->New_movement_model->get_todays_birthdays();

            $data = [
                'attendance' => [
                    'present_count' => $present_count, // Total present
                    'late_count' => $late_count,
                    'absent_count' => $absent_count,
                    'present_list' => $present_list, // Detail data
                    'late_list' => $late_list,       // Detail data
                    'absent_list' => $absent_list    // Detail data
                ],
                'movement_status' => [
                    'on_meeting_count' => count($on_meeting),
                    'on_meeting_list' => $on_meeting,
                    'on_travel_count' => count($on_travel), // Traveling/Field
                    'on_travel_list' => $on_travel, // Traveling/Field
                    'in_office_count' => count($in_office),
                    'in_office_list' => $in_office // Available in office
                ],
                'events' => [
                    'todays_meetings_count' => count($todays_meetings),
                    'todays_meetings_list' => $todays_meetings,
                    'todays_birthdays_count' => count($todays_birthdays),
                    'todays_birthdays_list' => $todays_birthdays
                ]
            ];

            $data['app_versions']= array(
                "app_platform" => "Android",
                "app_version_code" => "1.1.3",
                "android_app_version" => 13,
                "android_app_version_msg" => "1.1.3 is live now",
                "android_app_link" => "http://173.212.223.213/smarthr/uploads/apk/Sheba_ERP_live_app.apk",
                "is_enable" => 1,
                "is_hard_update" => 1,
            );

            $this->api_return(['status' => true, 'data' => $data], 200);

        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized'], 401);
        }
    }
}
