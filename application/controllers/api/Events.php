<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/API_Controller.php';

class Events extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('api_helper');
        $this->load->model("Events_model");
    }
    /**
     * demo method
     *
     * @link [api/user/demo]
     * @method POST
     * @return Response|void
     */
    public function test()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true) {
            echo json_encode($user_info);
            exit();
        } else {
            echo json_encode($user_info);
            exit();
        }
    }

    public function index()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        $user_data = $user_info['user_info'];
        if ($user_info['status'] == true) {
            $date = date('Y-m-d', strtotime('-12 months'));
            if ($user_data->user_role_id == 3) {
                $this->db->where('created_by', $user_data->user_id);
            }
            $this->db->where('start_event_date >=', $date);
            $result = $this->db->order_by("start_event_date", "desc")->get('xin_events')->result();
            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'data' => $result,
            ], 200);
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized',
                'data' => [],
            ], 404);
        }
    }

    public function get_events_by_type()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        $user_data = $user_info['user_info'];
        if ($user_info['status'] == true) {
            $today = date('Y-m-d');
            $three_months_ago = date('Y-m-d', strtotime('-3 months'));
            $type = $this->input->get('type');

            $this->db->select('e.*');
            $this->db->from('xin_event_participants p');
            $this->db->join('xin_events e', 'e.event_id = p.event_id', 'inner');
            $this->db->where('p.user_id', $user_data->user_id);
            switch ($type) {
                case 1: // Upcoming
                    $this->db->where('e.start_event_date >', $today);
                    break;
                case 2: // Today
                    $this->db->where('e.start_event_date', $today);
                    break;
                case 3: // Last 3 months (past)
                    $this->db->where('e.start_event_date <', $today)
                        ->where('e.start_event_date >=', $three_months_ago);
                    break;
            }

            $this->db->order_by("e.start_event_date", "desc");
            $result = $this->db->get()->result();

            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'data' => $result,
            ], 200);
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized',
                'data' => [],
            ], 404);
        }
    }

    public function ongoing_events()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        $user_data = $user_info['user_info'];
        if ($user_info['status'] == true) {
            $type = $this->input->get('type');
            $today = date('Y-m-d');
            $three_months_ago = date('Y-m-d', strtotime('-3 months'));

            $this->db->select('xin_events.*');
            $this->db->from('xin_event_participants');
            $this->db->join('xin_events', 'xin_events.event_id = xin_event_participants.event_id', 'left');
            $this->db->where('xin_event_participants.user_id', $user_data->user_id);
            switch ($type) {
                case 1: // Upcoming
                    $this->db->where('e.start_event_date >', $today);
                    break;
                case 2: // Today
                    $this->db->where('e.start_event_date', $today);
                    break;
                case 3: // Last 3 months (past)
                    $this->db->where('e.start_event_date <', $today)
                        ->where('e.start_event_date >=', $three_months_ago);
                    break;
            }

            $this->db->order_by("start_event_date", "desc");
            $result = $this->db->get()->result();

            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'data' => $result,
            ], 200);
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized',
                'data' => [],
            ], 404);
        }
    }

    public function add_event()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        $user_data = $user_info['user_info'];
        if ($user_info['status'] == true) {
            $emp_id = $user_data->user_id;
            $event_note = $this->input->post('event_note');
            $event_remark = $this->input->post('event_remark');
            $qt_event_note = htmlspecialchars(addslashes($event_note), ENT_QUOTES);
            $qt_event_remark = htmlspecialchars(addslashes($event_remark), ENT_QUOTES);

            $event_date = $this->input->post('start_date');
            $end_date = $this->input->post('end_date');
            $event_time = $this->input->post('start_time');
            $end_time = $this->input->post('end_time');

            $startdate = date('Y-m-d H:i:s', strtotime($event_date . ' ' . $event_time));
            $enddate = date('Y-m-d H:i:s', strtotime($end_date . ' ' . $end_time));
            $dateTime1 = new DateTime($startdate);
            $dateTime2 = new DateTime($enddate);

            // Calculate the difference
            $interval = $dateTime1->diff($dateTime2);
            // Format the output
            $days = $interval->format('%a');
            $hours = $interval->format('%h');
            $minutes = $interval->format('%i');

            $output = "";
            if ($days > 0) {
                $output .= $days . " day ";
            }
            if ($hours > 0) {
                $output .= $hours . ":" . $minutes . " h";
            } else {
                $output .= $minutes . " min";
            }
            $parts = $this->input->post('participants');
            $data = array(
                'company_id' => 1,
                'event_title' => $this->input->post('event_title'),
                'location' => $this->input->post('location'),
                'start_event_date' => $event_date,
                'start_event_time' => $event_time,
                'end_event_date' => $end_date,
                'end_event_time' => $end_time,
                'event_duration' => $output,
                'event_note' => $qt_event_note,
                'event_remark' => $qt_event_remark,
                'notification' => $this->input->post('notification'),
                'is_public' => $this->input->post('is_public'),
                'participant_type' => $this->input->post('participant_type'),
                'participants' => !empty($parts) ? json_encode($parts) : '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_by' => $emp_id,
            );
            if ($this->db->insert('xin_events', $data)) {
                $insert_id = $this->db->insert_id();
                if ($this->input->post('participant_type') == 1) {
                    $parts = $this->db->where_not_in('status', [0, 2, 3])->get('xin_employees')->result_array();
                    $participants = array_column($parts, 'user_id'); // one-liner
                } elseif ($this->input->post('participant_type') == 2) {
                    $pts = $this->input->post('participants');
                    $parts = $this->db->where_not_in('status', [0, 2, 3])->where_in('department_id', $pts)->get('xin_employees')->result_array();
                    $participants = array_column($parts, 'user_id');
                } else {
                    $participants = $this->input->post('participants');
                }
                if (!empty($participants)) {
                    $arrays = array();
                    foreach ($participants as $key => $value) {
                        $arrays[] = array(
                            'event_id' => $insert_id,
                            'user_id' => $value,
                            'event_date' => date('Y-m-d', strtotime($event_date)),
                        );
                    }
                    $this->db->insert_batch('xin_event_participants', $arrays);



                    $this->db->select('device_id');
                    $this->db->from('xin_employees');
                    $this->db->where_in('user_id', $participants); // Changed from user_id to employee_id
                    $this->db->where('device_id !=', '');
                    $this->db->where('device_id IS NOT NULL');
                    $tokens_query = $this->db->get();

                    $tokens = [];
                    foreach ($tokens_query->result() as $row) {
                        $tokens[] = $row->device_id;
                    }

                    if (!empty($tokens)) {
                        $this->load->helper('general');

                        $title = "Event Invitation";
                        $body = "Event Invitation for " . $this->input->post('event_title') . " on " . $event_date;

                        $res = send_firebase_notification($tokens, $title, $body);

                    }
                }
            }
            $this->api_return([
                'status' => true,
                'message' => 'Successfully Inserted Event',
                'data' => [],
            ], 200);
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized',
                'data' => [],
            ], 404);
        }
    }

    public function edit_event()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        $user_data = $user_info['user_info'];
        if ($user_info['status'] == true) {
            $event_id = $this->input->get('event_id');
            $event = $this->db->where('event_id', $event_id)->get('xin_events')->row();
            $event->participants = $event->participants ? json_decode($event->participants) : [];
            $this->api_return([
                'status' => true,
                'message' => 'Success',
                'data' => $event,
            ], 200);
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized',
                'data' => [],
            ], 404);
        }
    }

    public function update_event()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        $user_data = $user_info['user_info'];
        if ($user_info['status'] == true) {
            $emp_id = $user_data->user_id;
            $event_id = $this->input->post('event_id');
            $event_note = $this->input->post('event_note');
            $event_remark = $this->input->post('event_remark');
            $qt_event_note = htmlspecialchars(addslashes($event_note), ENT_QUOTES);
            $qt_event_remark = htmlspecialchars(addslashes($event_remark), ENT_QUOTES);

            $event_date = $this->input->post('start_date');
            $end_date = $this->input->post('end_date');
            $event_time = $this->input->post('start_time');
            $end_time = $this->input->post('end_time');

            $startdate = date('Y-m-d H:i:s', strtotime($event_date . ' ' . $event_time));
            $enddate = date('Y-m-d H:i:s', strtotime($end_date . ' ' . $end_time));
            $dateTime1 = new DateTime($startdate);
            $dateTime2 = new DateTime($enddate);

            // Calculate the difference
            $interval = $dateTime1->diff($dateTime2);
            // Format the output
            $days = $interval->format('%a');
            $hours = $interval->format('%h');
            $minutes = $interval->format('%i');

            $output = "";
            if ($days > 0) {
                $output .= $days . " day ";
            }
            if ($hours > 0) {
                $output .= $hours . ":" . $minutes . " h";
            } else {
                $output .= $minutes . " min";
            }
            $participants = $this->input->post('participants');
            $data = array(
                'company_id' => 1,
                'event_title' => $this->input->post('event_title'),
                'location' => $this->input->post('location'),
                'start_event_date' => $this->input->post('start_event_date'),
                'start_event_time' => $this->input->post('start_event_time'),
                'end_event_date' => $this->input->post('end_event_date'),
                'end_event_time' => $this->input->post('end_event_time'),
                'event_duration' => $output,
                'event_note' => $qt_event_note,
                'event_remark' => $qt_event_remark,
                'notification' => $this->input->post('notification'),
                'is_public' => 1,
                'participant_type' => $this->input->post('participant_type'),
                'participants' => !empty($participants) ? json_encode($participants) : '',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $this->input->post('user_id'),
            );

            $this->db->where('event_id', $event_id);
            if ($this->db->update('xin_events', $data)) {
                if ($this->input->post('participant_type') == 1) {
                    $parts = $this->db->where_not_in('status', [0, 2, 3])->get('xin_employees')->result_array();
                    $participants = array_column($parts, 'user_id'); // one-liner
                } elseif ($this->input->post('participant_type') == 2) {
                    $pts = $this->input->post('participants');
                    $parts = $this->db->where_not_in('status', [0, 2, 3])->where_in('department_id', $pts)->get('xin_employees')->result_array();
                    $participants = array_column($parts, 'user_id');
                } else {
                    $participants = $this->input->post('participants');
                }
                if (!empty($participants)) {
                    foreach ($participants as $key => $value) {
                        $arrays = array(
                            'event_id' => $event_id,
                            'user_id' => $value,
                            'event_date' => date('Y-m-d', strtotime($event_date)),
                        );
                        $this->db->where('event_id', $event_id)->where('user_id', $value);
                        $check_exist = $this->db->get('xin_event_participants')->num_rows();
                        if ($check_exist == 0) {
                            $this->db->insert('xin_event_participants', $arrays);
                        }
                    }
                    $this->db->where('event_id', $event_id);
                    $this->db->where_not_in('user_id', $participants);
                    $this->db->delete('xin_event_participants');
                }
            }
            $this->api_return([
                'status' => true,
                'message' => 'Successfully Updated Event',
                'data' => [],
            ], 200);
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized',
                'data' => [],
            ], 404);
        }
    }

    public function event_details()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        $user_data = $user_info['user_info'];
        if ($user_info['status'] == true) {
            $event_id = $this->input->get('event_id');
            $event = $this->db->where('event_id', $event_id)->get('xin_events')->row();

            $this->db->select('xin_employees.user_id, xin_employees.first_name, xin_employees.last_name');
            $this->db->from('xin_event_participants');
            $this->db->from('xin_employees');
            $this->db->where('xin_event_participants.event_id', $event_id);
            $this->db->where('xin_employees.user_id = xin_event_participants.user_id');
            $event->participants = $this->db->get()->result();

            $this->api_return([
                'status' => true,
                'message' => 'Success',
                'data' => $event,
            ], 200);
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized',
                'data' => [],
            ], 404);
        }
    }

    public function get_department()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        $user_data = $user_info['user_info'];
        if ($user_info['status'] == true) {
            $result = $this->db->where('status', 1)->get('xin_departments')->result();
            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'data' => $result,
            ], 200);
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized',
                'data' => [],
            ], 404);
        }
    }

    public function get_participants()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        $user_data = $user_info['user_info'];
        if ($user_info['status'] == true) {
            $result = $this->db->select('user_id, first_name, last_name')->where_not_in('status', [0, 2, 3])->get('xin_employees')->result();
            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'data' => $result,
            ], 200);
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized',
                'data' => [],
            ], 404);
        }
    }

    public function notice_list()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        $user_data = $user_info['user_info'];
        if ($user_info['status'] == true) {
            $result = $this->db->get('xin_office_notice')->result();
            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'data' => $result,
            ], 200);
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized',
                'data' => [],
            ], 404);
        }
    }

}
