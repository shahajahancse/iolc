<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class New_movement extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("New_movement_model");
        $this->load->model("New_movement_travel_model");
        $this->load->model("New_movement_meeting_model");
        $this->load->model('Xin_model');
        $this->load->model("Employees_model");

        $this->load->library('session');
    }

    // Dashboard: Entry point
    public function index()
    {
        $data['title'] = 'New Movement Dashboard';
        $session = $this->session->userdata('username');
        $user_id = $session['user_id'];
        $user_role_id = $session['role_id']; // Assuming this is available in session

        // Admins (1, 2, 4): Show All movements, No Active Flow Redirect
        $data['movements'] = $this->New_movement_model->get_all_movements();

        // Enrich with detailed status
        foreach ($data['movements'] as $k => $mov) {
            if ($mov->status == 'active') {
                $details = $this->New_movement_model->get_detailed_status($mov->id);
                $data['movements'][$k]->current_status_text = $details['status'];
                $data['movements'][$k]->current_location = $details['location'];
                $data['movements'][$k]->status_icon = $details['icon'];
                // Pass Coords
                $data['movements'][$k]->current_lat = isset($details['lat']) ? $details['lat'] : 0;
                $data['movements'][$k]->current_lng = isset($details['lng']) ? $details['lng'] : 0;
            } else {
                $data['movements'][$k]->current_status_text = 'Completed';
                $data['movements'][$k]->current_location = $mov->start_location;
                $data['movements'][$k]->status_icon = 'fa-check-circle';
                $data['movements'][$k]->current_lat = 0;
                $data['movements'][$k]->current_lng = 0;
            }
        }
        $data['is_employee'] = false;

        // Dashboard Stats (Calculate TA Amounts)
        $data['stats'] = [
            'total_ta' => 0,
            'pending_ta' => 0,
            'approved_ta' => 0,
            'handed_over_ta' => 0
        ];

        // Fetch expenses to calculate sums based on status
        // Note: This is a heavy operation, ideally should use SUM() query in Model.
        // For now, doing it via PHP as dataset is small or we can add a method in model.
        // Let's add a robust method in Model instead.
        $data['stats'] = $this->New_movement_model->get_ta_stats($user_role_id == 3 ? $user_id : null);

        $data['breadcrumbs'] = 'New Movement Dashboard';
        $data['subview'] = $this->load->view('new_movement/dashboard', $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data);
    }

    // Get TA List Entry point
    public function ta_list()
    {
        $data['title'] = 'TA List';
        $session = $this->session->userdata('username');
        $user_id = $session['user_id'];

        // Admins (1, 2, 4): Show All movements, No Active Flow Redirect
        $data['movements'] = $this->New_movement_model->get_ta_list();
        $data['is_employee'] = false;

        $data['breadcrumbs'] = 'Ta List';
        $data['subview'] = $this->load->view('new_movement/ta_list', $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data);
    }

    // Get TA List Ajax
    public function get_ta_list_ajax()
    {
        $start_date = null;
        $end_date   = null;

        if (!empty($this->input->post('start_date'))) {
            $start_date = date('Y-m-d 00:00:00', strtotime($this->input->post('start_date')));
        }
        if (!empty($this->input->post('end_date'))) {
            $end_date = date('Y-m-d 23:59:59', strtotime($this->input->post('end_date')));
        }

        $ta_status  = $this->input->post('ta_status');
        $employee_id = $this->input->post('employee_id');

        $data['movements'] = $this->New_movement_model->get_ta_list($start_date, $end_date, $ta_status, $employee_id);
        echo $this->load->view('new_movement/get_ta_list_ajax', $data, TRUE);
    }

    // Get TA Summary Entry point
    public function ta_summary()
    {
        $data['title'] = 'TA Summary';
        $session = $this->session->userdata('username');
        $user_id = $session['user_id'];

        $post_start = $this->input->post('start_date');
        $post_end   = $this->input->post('end_date');

        // ❌ Only one date provided
        if ((!empty($post_start) && empty($post_end)) || (empty($post_start) && !empty($post_end))) {
            return $this->api_return(array(
                'status' => false,
                'message' => 'Both start date and end date are required'
            ), 400);
        }

        $start_date = $this->input->post('start_date') ? date('Y-m-d 23:59:59', strtotime($this->input->post('start_date'))) : date('Y-m-d 23:59:59');
        $end_date = $this->input->post('end_date')? date('Y-m-d 00:00:00', strtotime($this->input->post('end_date')))
        : date('Y-m-d 00:00:00', strtotime('-7 days'));

        // ✅ Default last 7 days
        // if (empty($post_start) && empty($post_end)) {
        //     $start_date = date('Y-m-d 00:00:00', strtotime('-7 days'));
        //     $end_date   = date('Y-m-d 23:59:59');
        // } else {
        //     $start_date = date('Y-m-d 00:00:00', strtotime($post_start));
        //     $end_date   = date('Y-m-d 23:59:59', strtotime($post_end));
        // }

        $ta_status   = $this->input->post('ta_status') ? $this->input->post('ta_status') : 'approved';
        $employee_id = $this->input->post('employee_id');

        // Admins (1, 2, 4): Show All movements, No Active Flow Redirect
        $data['movements'] = $this->New_movement_model->get_ta_summary($start_date, $end_date, $ta_status, $employee_id);
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['ta_status'] = $ta_status;
        $data['user_id'] = $user_id;

        $data['breadcrumbs'] = 'Ta Summary';
        $data['subview'] = $this->load->view('new_movement/ta_summary', $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data);
    }

    // Get TA Summary Ajax
    public function get_ta_summary_ajax()
    {
        $start_date = $this->input->post('start_date');
        $end_date   = $this->input->post('end_date');

        // ❌ Only one date provided
        if ((!empty($start_date) && empty($end_date)) || (empty($start_date) && !empty($end_date))) {
            return $this->api_return(array(
                'status' => false,
                'message' => 'Both start date and end date are required'
            ), 400);
        }

        // ✅ Default last 7 days
        if (empty($start_date) && empty($end_date)) {
            $start_date = date('Y-m-d 00:00:00', strtotime('-7 days'));
            $end_date   = date('Y-m-d 23:59:59');
        } else {
            $end_date = date('Y-m-d 23:59:59', strtotime($end_date));
            $start_date = date('Y-m-d 00:00:00', strtotime($start_date));
        }

        $ta_status   = $this->input->post('ta_status');
        $employee_id = $this->input->post('employee_id');

        // Admins (1, 2, 4): Show All movements, No Active Flow Redirect
        $data['movements'] = $this->New_movement_model->get_ta_summary($end_date, $start_date, $ta_status, $employee_id);
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['ta_status'] = $ta_status;
        echo $this->load->view('new_movement/get_ta_summary_ajax', $data, TRUE);
    }

    // Admin Action: View TA Summary Details
    public function ta_summary_details()
    {
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $ta_status = $this->input->get('ta_status');
        $employee_id = $this->input->get('employee_id');
        if (!$employee_id) {
            echo " Employee ID required ";
            return;
        }

        // Fetch employee info
        $data['employee'] = $this->Xin_model->read_user_info($employee_id);
        $data['movements'] = $this->New_movement_model->get_ta_summary_details_by_user($start_date, $end_date, $ta_status, $employee_id);

        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['ta_status'] = $ta_status;
        $this->load->view('new_movement/ta_summary_details', $data);
    }

    // Admin Action: Approve / Paid TA from Summary
    public function ta_summary_approve()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $ta_status = $this->input->post('ta_status');
        $employee_id = $this->input->post('employee_id');
        // if (empty($employee_id)) {
        //     return ['message' => 'Employee ID required'];
        // }

        $this->db->trans_start();
        try {
            if (!empty($employee_id)) {
                $this->db->where('employee_id', $employee_id);
            }
            $this->db->where('ta_status', 'approved');
            $this->db->where('end_time <=', $start_date);
            $this->db->where('end_time >=', $end_date);
            $this->db->update('new_movement_movements', ['ta_status' => $ta_status]);

            $this->db->trans_complete();
            if ($this->db->trans_status() === false) {
                throw new Exception('Error updating TA status');
            } else {
                echo 'TA status updated successfully';
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo 'Error: ' . $e->getMessage();
        }
    }

    // Dashboard: Entry point
    public function emp_dashboard()
    {
        $data['title'] = 'New Movement Dashboard';
        $session = $this->session->userdata('username');
        $user_id = $session['user_id'];
        $user_role_id = $session['role_id']; // Assuming this is available in session

        // Role 3: Employee (Has Movement Flow)

        $active_movement = $this->New_movement_model->get_active_movement($user_id);

        if ($active_movement) {
            $active_travel = $this->New_movement_travel_model->get_active_travel($active_movement->id);
            if ($active_travel) {
                return redirect('new_movement/traveling');
            }

            $active_meeting = $this->New_movement_meeting_model->get_active_meeting($active_movement->id);
            if ($active_meeting) {
                return redirect('new_movement/meeting_running');
            }

            // Check if last meeting ended but no feedback
            $pending_feedback = $this->New_movement_meeting_model->get_pending_feedback_meeting($active_movement->id);
            if ($pending_feedback) {
                return redirect('new_movement/feedback_form');
            }

            return redirect('new_movement/decision');
        }
        // Show only their movements
        $data['movements'] = $this->New_movement_model->get_all_movements($user_id);
        $data['is_employee'] = true; // Flag for View to show "Start Movement" button

        // Dashboard Stats (Calculate TA Amounts)
        $data['stats'] = [
            'total_ta' => 0,
            'pending_ta' => 0,
            'approved_ta' => 0,
            'handed_over_ta' => 0
        ];

        // Fetch expenses to calculate sums based on status
        // Note: This is a heavy operation, ideally should use SUM() query in Model.
        // For now, doing it via PHP as dataset is small or we can add a method in model.
        // Let's add a robust method in Model instead.
        $data['stats'] = $this->New_movement_model->get_ta_stats($user_role_id == 3 ? $user_id : null);

        $data['breadcrumbs'] = 'New Movement Dashboard';
        $data['subview'] = $this->load->view('new_movement/dashboard', $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data);
    }

    // Start Movement Action
    public function start()
    {
        if ($this->input->post('submit')) {
            $session = $this->session->userdata('username');
            $user_id = $session['user_id'];

            $photo_path = null;
            if (!empty($_FILES['start_photo']['name'])) {
                $config['upload_path'] = './uploads/movement_photos/';
                $config['allowed_types'] = 'jpg|jpeg|png';
                $config['file_name'] = 'start_' . $user_id . '_' . time();

                // Create directory if not exists
                if (!is_dir($config['upload_path'])) {
                    mkdir($config['upload_path'], 0777, true);
                }

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('start_photo')) {
                    $upload_data = $this->upload->data();
                    $photo_path = 'uploads/movement_photos/' . $upload_data['file_name'];
                }
            }

            $data = array(
                'employee_id' => $user_id,
                'start_location' => $this->input->post('start_location'),
                'start_latitude' => $this->input->post('latitude'),
                'start_longitude' => $this->input->post('longitude'),
                'purpose' => $this->input->post('purpose'),
                'type' => $this->input->post('type'),
                'start_time' => date('Y-m-d H:i:s'),
                'start_photo' => $photo_path,
                'status' => 'active'
            );

            $movement_id = $this->New_movement_model->create_movement($data);

            $travel_data = array(
                'movement_id' => $movement_id,
                'from_location' => $this->input->post('start_location'),
                'start_lat' => $this->input->post('latitude'),
                'start_lng' => $this->input->post('longitude'),
                'start_time' => date('Y-m-d H:i:s'),
                'status' => 'running'
            );
            $this->New_movement_travel_model->start_travel($travel_data);

            redirect('new_movement/traveling');
        } else {
            $data['title'] = 'Start Movement';
            $data['breadcrumbs'] = 'Start Movement';
            $data['subview'] = $this->load->view('new_movement/start_form', $data, TRUE);
            $this->load->view('admin/layout/layout_main', $data);
        }
    }

    // View: Traveling (Auto Mode)
    public function traveling()
    {
        $session = $this->session->userdata('username');
        $user_id = $session['user_id'];
        $movement = $this->New_movement_model->get_active_movement($user_id);

        if (!$movement)
            redirect('new_movement');

        $travel = $this->New_movement_travel_model->get_active_travel($movement->id);
        if (!$travel)
            redirect('new_movement/decision');

        $data['movement'] = $movement;
        $data['travel'] = $travel;
        $data['title'] = 'Traveling';
        $data['breadcrumbs'] = 'Traveling';
        $data['subview'] = $this->load->view('new_movement/traveling', $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data);
    }

    // Action: Reached Destination
    public function reached_destination()
    {
        $session = $this->session->userdata('username');
        $user_id = $session['user_id'];
        $movement = $this->New_movement_model->get_active_movement($user_id);
        $travel = $this->New_movement_travel_model->get_active_travel($movement->id);

        if ($travel) {
            $end_lat = $this->input->post('latitude');
            $end_lng = $this->input->post('longitude');
            $start_lat = $travel->start_lat;
            $start_lng = $travel->start_lng;
            $dist_km = 0;

            if ($start_lat && $start_lng && $end_lat && $end_lng) {
                // Haversine Formula
                $theta = $start_lng - $end_lng;
                $dist = sin(deg2rad($start_lat)) * sin(deg2rad($end_lat)) + cos(deg2rad($start_lat)) * cos(deg2rad($end_lat)) * cos(deg2rad($theta));

                // Clamp $dist to [-1, 1] to avoid NaN from acos
                if ($dist > 1)
                    $dist = 1;
                if ($dist < -1)
                    $dist = -1;

                $dist = acos($dist);
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $dist_km = $miles * 1.609344;

                if (is_nan($dist_km))
                    $dist_km = 0;
            }

            $update_data = array(
                'end_time' => date('Y-m-d H:i:s'),
                'status' => 'completed',
                'to_location' => $this->input->post('current_location'),
                'end_lat' => $end_lat,
                'end_lng' => $end_lng,
                'distance_km' => round($dist_km, 2),
                'distance' => round($dist_km, 2)
            );
            $this->New_movement_travel_model->end_travel($travel->id, $update_data);
        }

        if ($this->input->post('is_office_return') == 1 || $this->input->post('is_home_return') == 1) {
            return $this->end_movement_process();
        }

        redirect('new_movement/start_meeting');
    }

    // View: Start Meeting Form
    public function start_meeting()
    {
        $data['title'] = 'Start Meeting';
        $data['breadcrumbs'] = 'Start Meeting';

        // Pass user email for API calls
        $session = $this->session->userdata('username');
        $user_info = $this->Employees_model->read_employee_information($session['user_id']);
        $data['user_email'] = $user_info[0]->email;

        $data['subview'] = $this->load->view('new_movement/meeting_form', $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data);
    }

    // Action: Process Start Meeting
    public function process_start_meeting()
    {
        $session = $this->session->userdata('username');
        $user_id = $session['user_id'];
        $movement = $this->New_movement_model->get_active_movement($user_id);

        $location = $this->input->post('location');
        $lat = '';
        $long = '';
        if (!empty($location)) {
            $parts = explode(',', $location);
            if (count($parts) == 2) {
                $lat = trim($parts[0]);
                $long = trim($parts[1]);
            }
        }

        $data = array(
            'movement_id' => $movement->id,
            'entity_type' => $this->input->post('entity_type'),
            'client_name' => $this->input->post('client_name'),
            'contact_person' => $this->input->post('contact_person'),
            'contact_email' => $this->input->post('contact_email'),
            'contact_phone' => $this->input->post('contact_phone'),
            'contact_job_title' => $this->input->post('contact_job_title'),
            'meeting_type' => $this->input->post('meeting_type'),
            'location' => $location,
            'latitude' => $lat,
            'longitude' => $long,
            'remarks' => $this->input->post('remarks'),
            'start_time' => date('Y-m-d H:i:s')
        );
        // CRM Integration
        $meeting_source = $this->input->post('meeting_source');
        $crm_lead_id = $this->input->post('crm_lead_id');

        // Add lead ID to data if exists
        if ($crm_lead_id) {
            $data['crm_lead_id'] = $crm_lead_id;
        }

        $this->New_movement_meeting_model->start_meeting($data);

        // Check for Sales Team and trigger Leads API
        $user_info = $this->Employees_model->read_employee_information($session['user_id']);
        // if (!empty($user_info) && isset($user_info[0]->team_type) && $user_info[0]->team_type == 'sales') {

        //     // Only create new lead if source is 'new'
        //     if ($meeting_source == 'new') {
        //         $leads_url = 'http://demo-crm.mysoftheaven.com/index.php/leads_api/save';
        //         $leads_data = [
        //             'owner_email' => $user_info[0]->email,
        //             'entity_type' => $this->input->post('entity_type'),
        //             'client_name' => $this->input->post('client_name'),
        //             'contact_person' => $this->input->post('contact_person'),
        //             'contact_email' => $this->input->post('contact_email'),
        //             'contact_phone' => $this->input->post('contact_phone'),
        //             'contact_job_title' => $this->input->post('contact_job_title'),
        //             'remarks' => $this->input->post('remarks'),
        //             'feedback' => ''
        //         ];

        //         $ch = curl_init($leads_url);
        //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //         curl_setopt($ch, CURLOPT_POST, true);
        //         curl_setopt($ch, CURLOPT_POSTFIELDS, $leads_data);
        //         curl_exec($ch);
        //         curl_close($ch);
        //     }
        //     // If existing, we just linked usage via crm_lead_id stored in DB.
        // }
        redirect('new_movement/meeting_running');
    }

    // View: Meeting Running
    public function meeting_running()
    {
        $session = $this->session->userdata('username');
        $user_id = $session['user_id'];
        $movement = $this->New_movement_model->get_active_movement($user_id);
        $meeting = $this->New_movement_meeting_model->get_active_meeting($movement->id);

        if (!$meeting)
            redirect('new_movement/decision');

        $data['meeting'] = $meeting;
        $data['title'] = 'Meeting in Progress';
        $data['breadcrumbs'] = 'Meeting Running';
        $data['subview'] = $this->load->view('new_movement/meeting_running', $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data);
    }

    // Action: End Meeting
    public function end_meeting()
    {
        $session = $this->session->userdata('username');
        $user_id = $session['user_id'];
        $movement = $this->New_movement_model->get_active_movement($user_id);
        $meeting = $this->New_movement_meeting_model->get_active_meeting($movement->id);

        if ($meeting) {
            $this->New_movement_meeting_model->end_meeting($meeting->id, date('Y-m-d H:i:s'));

            // Redirect to Feedback Form instead of Decision
            redirect('new_movement/feedback_form');
        } else {
            redirect('new_movement/decision');
        }
    }

    // View: Feedback Form
    public function feedback_form()
    {
        $data['title'] = 'Meeting Feedback';
        $data['breadcrumbs'] = 'Meeting Feedback';

        // Fetch Lead Statuses if this meeting is linked to a lead
        $session = $this->session->userdata('username');
        $movement = $this->New_movement_model->get_active_movement($session['user_id']);
        // Need to fetch last meeting details to see if it has crm_lead_id
        $meeting = $this->New_movement_meeting_model->get_last_meeting($movement->id);

        $data['statuses'] = [];
        $data['is_crm_linked'] = false;

        if ($meeting && !empty($meeting->crm_lead_id)) {
            $data['is_crm_linked'] = true;
            $data['crm_lead_id'] = $meeting->crm_lead_id;

            // Fetch statuses from CRM API? Or just hardcode common ones to avoid latency?
            // Let's try to fetch since we built the API.
            // We can reuse get_daily_schedules which returns statuses, but that's heavy.
            // Ideally we should have a `get_statuses` endpoint.
            // For now, I'll modify the previous plan to just hardcode standard ones OR fetch via Curl.
            // Let's use a quick Curl to get_daily_schedules just to parse statuses (inefficient but works within constraints).

            $user_info = $this->Employees_model->read_employee_information($session['user_id']);
            if (!empty($user_info)) {
                $leads_url = 'http://demo-crm.mysoftheaven.com/index.php/leads_api/get_daily_schedules';
                $post_data = ['owner_email' => $user_info[0]->email];

                $ch = curl_init($leads_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                $response = curl_exec($ch);
                curl_close($ch);

                $result = json_decode($response);
                if ($result && !empty($result->statuses)) {
                    $data['statuses'] = $result->statuses;
                }
            }
        }

        $data['subview'] = $this->load->view('new_movement/feedback_form', $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data);
    }

    // Action: Submit Feedback
    public function submit_feedback()
    {
        $session = $this->session->userdata('username');
        $user_id = $session['user_id'];
        $movement = $this->New_movement_model->get_active_movement($user_id);

        $meeting = $this->New_movement_meeting_model->get_last_meeting($movement->id);

        if ($meeting) {
            $feedback = $this->input->post('feedback');

            // Prepare update data
            $update_data = ['feedback' => $feedback];

            // Add optional client details updates if provided
            $optional_fields = ['client_name', 'contact_person', 'contact_email', 'contact_phone', 'contact_job_title'];
            foreach ($optional_fields as $field) {
                if ($this->input->post($field)) {
                    $update_data[$field] = $this->input->post($field);
                }
            }

            $this->New_movement_meeting_model->update_meeting_feedback($meeting->id, $update_data);

            // Update CRM if linked
            if (!empty($meeting->crm_lead_id)) {
                $status = $this->input->post('crm_status');
                if ($status) {
                    $user_info = $this->Employees_model->read_employee_information($session['user_id']);

                    $leads_url = 'http://demo-crm.mysoftheaven.com/index.php/leads_api/update_lead_status';
                    $leads_data = [
                        'owner_email' => $user_info[0]->email,
                        'lead_id' => $meeting->crm_lead_id,
                        'status' => $status,
                        'feedback' => $feedback
                    ];

                    $ch = curl_init($leads_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $leads_data);
                    curl_exec($ch);
                    curl_close($ch);
                }
            }
        }

        redirect('new_movement/decision');
    }

    // View: Log Visit Form (No Meeting)
    public function log_visit_form()
    {
        $data['title'] = 'Log Client Visit';
        $data['breadcrumbs'] = 'Log Visit';

        $data['subview'] = $this->load->view('new_movement/log_visit_form', $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data);
    }

    // Action: Process Log Visit
    public function process_log_visit()
    {
        $session = $this->session->userdata('username');
        $user_id = $session['user_id'];
        $movement = $this->New_movement_model->get_active_movement($user_id);

        if (!$movement)
            redirect('new_movement');

        $location = $this->input->post('location');
        $lat = '';
        $long = '';
        if (!empty($location)) {
            $parts = explode(',', $location);
            if (count($parts) == 2) {
                $lat = trim($parts[0]);
                $long = trim($parts[1]);
            }
        }

        $data = array(
            'movement_id' => $movement->id,
            'entity_type' => $this->input->post('entity_type'),
            'client_name' => $this->input->post('client_name'),
            'contact_person' => $this->input->post('contact_person'),
            'contact_email' => $this->input->post('contact_email'),
            'contact_phone' => $this->input->post('contact_phone'),
            'contact_job_title' => $this->input->post('contact_job_title'),
            'meeting_type' => 'visit_only',
            'location' => $location,
            'latitude' => $lat,
            'longitude' => $long,
            'remarks' => $this->input->post('remarks'),
            'start_time' => date('Y-m-d H:i:s'),
            'end_time' => date('Y-m-d H:i:s'), // Instant end
            'feedback' => 'Client Visit (Busy/No Meeting)'
        );

        // CRM Integration
        $crm_lead_id = $this->input->post('crm_lead_id');
        if ($crm_lead_id) {
            $data['crm_lead_id'] = $crm_lead_id;
        }

        $this->New_movement_meeting_model->start_meeting($data);

        redirect('new_movement/decision');
    }

    // Ajax Proxy for searching clients
    public function search_clients_proxy()
    {
        $query = $this->input->get('query');
        if (empty($query)) {
            echo json_encode([]);
            return;
        }
        $results = $this->New_movement_meeting_model->search_clients($query);
        echo json_encode($results);
    }

    // View: Decision Screen
    public function decision()
    {
        $data['title'] = 'Decision';
        $data['breadcrumbs'] = 'What Next?';
        $data['subview'] = $this->load->view('new_movement/decision', $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data);
    }

    // Action: Handle Decision
    public function handle_decision()
    {
        $choice = $this->input->post('choice');
        $session = $this->session->userdata('username');
        $user_id = $session['user_id'];
        $movement = $this->New_movement_model->get_active_movement($user_id);

        if ($choice == 'meeting') {
            redirect('new_movement/start_meeting');
        } elseif ($choice == 'office') {
            $travel_data = array(
                'movement_id' => $movement->id,
                'from_location' => $this->input->post('current_location'),
                'start_lat' => $this->input->post('latitude'),
                'start_lng' => $this->input->post('longitude'),
                'to_location' => 'Office',
                'start_time' => date('Y-m-d H:i:s'),
                'status' => 'running',
                'is_office_return' => 1
            );
            $this->New_movement_travel_model->start_travel($travel_data);

            $this->session->set_flashdata('is_office_return', 1);

            redirect('new_movement/traveling');

        } elseif ($choice == 'home') {
            $travel_data = array(
                'movement_id' => $movement->id,
                'from_location' => $this->input->post('current_location'),
                'start_lat' => $this->input->post('latitude'),
                'start_lng' => $this->input->post('longitude'),
                'to_location' => 'Home',
                'start_time' => date('Y-m-d H:i:s'),
                'status' => 'running',
                'is_office_return' => 0 // Not office, but home
            );
            $this->New_movement_travel_model->start_travel($travel_data);

            // Use is_home_return flashdata to signal the next view
            $this->session->set_flashdata('is_home_return', 1);

            redirect('new_movement/traveling');

        } else {
            $travel_data = array(
                'movement_id' => $movement->id,
                'from_location' => $this->input->post('current_location'),
                'start_lat' => $this->input->post('latitude'),
                'start_lng' => $this->input->post('longitude'),
                'start_time' => date('Y-m-d H:i:s'),
                'status' => 'running'
            );
            $this->New_movement_travel_model->start_travel($travel_data);
            redirect('new_movement/traveling');
        }
    }

    // Helper: End Movement Process
    private function end_movement_process()
    {
        $session = $this->session->userdata('username');
        $user_id = $session['user_id'];
        $movement = $this->New_movement_model->get_active_movement($user_id);

        if ($movement) {
            $this->New_movement_model->end_movement($movement->id, date('Y-m-d H:i:s'));
        }

        redirect('new_movement/details/' . $movement->id);
    }

    // View: Movement Details (Map)
    public function details($id, $ftype=null)
    {
        $data['movement'] = $this->New_movement_model->get_movement_by_id($id);
        $data['travels'] = $this->New_movement_travel_model->get_travels_by_movement($id);
        $data['meetings'] = $this->New_movement_meeting_model->get_meetings_by_movement($id);
        $data['expenses'] = $this->New_movement_model->get_expenses_by_movement($id); // NEW

        $data['title'] = 'Movement Details';
        $data['breadcrumbs'] = 'Movement Details';
        $data['ftype'] = $ftype;
        $data['subview'] = $this->load->view('new_movement/details', $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data);
    }

    // Display TA Application Form
    public function apply_ta($movement_id)
    {
        $data['title'] = 'Apply Travel Allowance';
        $data['movement'] = $this->New_movement_model->get_movement_by_id($movement_id);
        $data['travels'] = $this->New_movement_travel_model->get_travels_by_movement($movement_id);

        $data['breadcrumbs'] = 'Apply TA';
        $data['subview'] = $this->load->view('new_movement/apply_ta', $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data);
    }

    // Save TA Claim
    public function save_ta()
    {
        if ($this->input->post()) {
            $movement_id = $this->input->post('movement_id');
            $expenses = $this->input->post('expenses'); // Array of expenses

            // Delete existing expenses for this movement (Simple overwrite logic)
            $this->db->where('movement_id', $movement_id);
            $this->db->delete('new_movement_travel_expenses');

            $amount = 0;
            if (!empty($expenses)) {
                foreach ($expenses as $travel_id => $travel_expenses) {
                    foreach ($travel_expenses as $exp) {
                        if (!empty($exp['amount']) && $exp['amount'] > 0) {
                            $data = array(
                                'movement_id' => $movement_id,
                                'travel_id' => $travel_id,
                                'transport_type' => $exp['type'],
                                'amount' => $exp['amount'],
                                'note' => $exp['note']
                            );
                            $amount = $amount + $exp['amount'];
                            $this->db->insert('new_movement_travel_expenses', $data);
                        }
                    }
                }
            }

            // Update movement TA status to pending
            $array = array(
                'ta_status' => 'pending',
                'ta_amount' => $amount,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $this->session->userdata('username')['user_id']
            );
            $this->db->where('id', $movement_id);
            $this->db->update('new_movement_movements', $array);

            $this->session->set_flashdata('success', 'TA Claim Submitted Successfully!');
            redirect('new_movement/emp_dashboard');
        }
    }

    // Update TA Status (Role Actions)
    public function update_ta_status($movement_id = null, $status = null)
    {
        $allowed = false;
        $role = $this->session->userdata('username')['role_id'];
        if (in_array($role, array(1,2,4))) {
            $allowed = true;
        }
        if (empty($movement_id) && empty($status) && $this->input->post() && $allowed) {
            $movement_id = $this->input->post('movement_id');
            $status = $this->input->post('type');

            $this->db->where('id', $movement_id);
            $this->db->update('new_movement_movements', ['ta_status' => $status]);
            // $this->session->set_flashdata('success', 'Status updated to ' . $status);
            $response = 'Status updated to ' . $status;
        } else {
            // $this->session->set_flashdata('error', 'Permission Denied');
            $response = 'Permission Denied';
        }
        // Redirect back to previous page
        // redirect($this->input->server('HTTP_REFERER'));
        echo $response;
    }

    public function reports()
    {
        $session = $this->session->userdata('username');
        if ($session['role_id'] == 3) {
            redirect('new_movement');
        } // Employees cannot access reports

        $data['title'] = 'Movement Reports';
        $data['breadcrumbs'] = 'Reports';

        $data['subview'] = $this->load->view('admin/new_movement/reports_new', $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data);
    }

    public function generate_custom_report()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $emp_ids_str = $this->input->post('emp_ids');
        $report_type = $this->input->post('report_type');
        $ta_status = $this->input->post('ta_status');

        $emp_ids = !empty($emp_ids_str) ? explode(',', $emp_ids_str) : null;

        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['ta_status'] = $ta_status ? $ta_status : 'all';

        if ($report_type == 'card') {
            if (!empty($emp_ids)) {
                $this->db->where_in('user_id', $emp_ids);
            } else {
                $this->db->where('is_active', 1);
            }
            $this->db->select('xin_employees.*, xin_departments.department_name, xin_designations.designation_name');
            $this->db->join('xin_departments', 'xin_employees.department_id = xin_departments.department_id', 'left');
            $this->db->join('xin_designations', 'xin_employees.designation_id = xin_designations.designation_id', 'left');
            $employees = $this->db->get('xin_employees')->result();

            $data['employees'] = $employees;
            $this->load->view('admin/new_movement/movement_card', $data);

        } elseif ($report_type == 'ta') {
            $data['report'] = $this->New_movement_model->get_report_ta($start_date, $end_date, $emp_ids, $ta_status);
            $this->load->view('admin/new_movement/ta_report_body', $data);
        } else {
            $data['report'] = $this->New_movement_model->get_report_data_v2($start_date, $end_date, $emp_ids);
            $this->load->view('admin/new_movement/report_body', $data);
        }
    }

    public function ta_summary_report()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $ta_status = $this->input->post('ta_status');
        $emp_ids_str = $this->input->post('emp_ids');
        $emp_ids = !empty($emp_ids_str) ? explode(',', $emp_ids_str) : null;

        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['ta_status'] = $ta_status ? $ta_status : 'all';
        $data['report'] = $this->New_movement_model->ta_summary_report($start_date, $end_date, $ta_status, $emp_ids);
        $this->load->view('admin/new_movement/ta_summary_report', $data);

    }

    public function generate_report()
    {
        // Legacy method kept for safety or redirect
        $this->generate_custom_report();
    }
    public function details_text($id)
    {
        $data['movement'] = $this->New_movement_model->get_movement_by_id($id);
        $data['travels'] = $this->New_movement_travel_model->get_travels_by_movement($id);
        $data['meetings'] = $this->New_movement_meeting_model->get_meetings_by_movement($id);
        $data['expenses'] = $this->New_movement_model->get_expenses_by_movement($id);

        $data['title'] = 'Movement Details';
        $data['breadcrumbs'] = 'Movement Details';
        $data['subview'] = $this->load->view('new_movement/details_text', $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data);
    }

    // Admin Action: Load Edit Modal
    public function edit_expenses()
    {
        $movement_id = $this->input->get('movement_id');
        if (!$movement_id) {
            echo "ID required";
            return;
        }

        $data['movement'] = $this->New_movement_model->get_movement_by_id($movement_id);
        // dd($data);
        // Fetch employee info
        $employee = $this->Xin_model->read_user_info($data['movement']->employee_id);
        if ($employee) {
            $data['movement']->first_name = $employee[0]->first_name;
            $data['movement']->last_name = $employee[0]->last_name;
        } else {
            $data['movement']->first_name = 'Unknown';
            $data['movement']->last_name = '';
        } 

        $data['expenses'] = $this->New_movement_model->get_expenses_by_movement($movement_id);
        // dd($data);
        $this->load->view('new_movement/dialog_edit_expenses', $data);
    }

    // Admin Action: Update Expenses
    public function update_expenses()
    {
        if ($this->input->post()) {
            $movement_id = $this->input->post('movement_id');
            $type = $this->input->post('type');
            $expenses = $this->input->post('expenses');

            // $ta_admin_note = $this->input->post('ta_admin_note');

            if (!empty($expenses)) {
                $modifyed_amt = 0;
                foreach ($expenses as $data) {
                    $update_data = [
                        'transport_type' => $data['transport_type'],
                        'amount' => $data['amount'],
                        'approve_amount' => $data['modified_amt'],
                        'note' => $data['note']
                    ];
                    $this->db->where('id', $data['id']);
                    $this->db->update('new_movement_travel_expenses', $update_data);
                    $modifyed_amt = $modifyed_amt + $data['modified_amt'];
                }
                $array = array(
                    'ta_app_amt' => $modifyed_amt,
                    'ta_admin_note' => $this->input->post('ta_admin_note'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $this->session->userdata('username')['user_id']
                );
                if (!empty($type)) {
                    $array['ta_status'] = 'approved';
                    $response = 'TA amount updated & approved successfully';
                } else {
                    $response = 'TA status updated successfully';
                }
                $this->db->where('id', $movement_id);
                $this->db->update('new_movement_movements', $array);
            }
            // $this->session->set_flashdata('success', 'Expenses updated successfully.');
            // redirect($this->input->server('HTTP_REFERER'));
            echo $response;
        }
    }

    // Ajax Proxy for fetching scheduled methods
    public function fetch_schedules_proxy()
    {
        $session = $this->session->userdata('username');
        if (empty($session)) {
            echo json_encode(['success' => false, 'message' => 'Session expired']);
            return;
        }

        $user_info = $this->Employees_model->read_employee_information($session['user_id']);
        if (!empty($user_info)) {
            $leads_url = 'http://demo-crm.mysoftheaven.com/index.php/leads_api/get_daily_schedules';
            $post_data = ['owner_email' => $user_info[0]->email];

            $ch = curl_init($leads_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            $response = curl_exec($ch);
            curl_close($ch);

            echo $response;
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
    }
}
