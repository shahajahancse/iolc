<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Room_management extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Room_management_model');
        $this->load->model('Xin_model');
        $this->load->library('session');
    }

    // --- VIEW: Room List (Accessible by All) ---
    public function index() {
        $session = $this->session->userdata('username');
        if(empty($session)){ redirect('admin/'); }

        $role_id = $session['role_id'];
        $is_admin = $this->is_admin_or_hr($role_id);

        // Redirect Employees to My Bookings
        if(!$is_admin) {
            redirect('admin/room_management/my_bookings');
        }

        $data['title'] = 'Meeting Rooms';
        $data['breadcrumbs'] = 'Meeting Rooms';
        $data['path_url'] = 'room_management';
        $data['is_admin'] = $is_admin;
        $data['rooms'] = $role_id == 1
                        ? $this->Room_management_model->get_all_rooms()
                        : $this->Room_management_model->get_active_rooms();

        $data['subview'] = $this->load->view("admin/room_management/room_list", $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data);
    }

    // --- HR ACTION: Add/Edit Room ---
    // --- HR ACTION: Add/Edit Room ---
    public function manage_room() {
        $session = $this->session->userdata('username');
        if(empty($session)){ redirect('admin/'); }

        // HR/Admin Check (Role 1 or 2 or 4) - Adjust as needed
        $role_id = $session['role_id'];
        if(!$this->is_admin_or_hr($role_id)) {
            redirect('admin/room_management');
        }

        if($this->input->post('name')) {
            $data = [
                'name' => $this->input->post('name'),
                'capacity' => $this->input->post('capacity'),
                'location' => $this->input->post('location'),
                'description' => $this->input->post('description'),
                'status' => $this->input->post('status') ? $this->input->post('status') : 'active'
            ];

            $id = $this->input->post('room_id');
            if($id) {
                $this->Room_management_model->update_room($id, $data);
                $this->session->set_flashdata('success', 'Room Updated Successfully');
            } else {
                $this->Room_management_model->add_room($data);
                $this->session->set_flashdata('success', 'Room Added Successfully');
            }
        }
        redirect('admin/room_management');
    }

    public function delete_room($id) {
        $session = $this->session->userdata('username');
        if($this->is_admin_or_hr($session['role_id'])) {
            $this->Room_management_model->delete_room($id);
            $this->session->set_flashdata('success', 'Room Deleted');
        }
        redirect('admin/room_management');
    }

    // --- BOOKING FLOW ---
    public function book_room() {
        $session = $this->session->userdata('username');
        if(empty($session)){ redirect('admin/'); }

        $data['title'] = 'Book Meeting Room';
        $data['breadcrumbs'] = 'Book Room';
        $data['rooms'] = $this->Room_management_model->get_active_rooms();
        $data['all_employees'] = $this->Xin_model->all_employees();

        $data['subview'] = $this->load->view("admin/room_management/book_room", $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data);
    }

    // AJAX: Realtime Availability Check
    public function check_availability() {
        $room_id = $this->input->post('room_id');
        $date = $this->input->post('date');
        $start = $this->input->post('start_time');
        $end = $this->input->post('end_time');

        if(!$room_id || !$date || !$start || !$end) {
            echo json_encode(['status' => false, 'message' => 'Missing fields']);
            return;
        }

        if(strtotime($start) >= strtotime($end)) {
            echo json_encode(['status' => false, 'message' => 'End time must be after start time']);
            return;
        }

        $available = $this->Room_management_model->check_availability($room_id, $date, $start, $end);

        if($available) {
            echo json_encode(['status' => true, 'message' => 'Room is Available']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Room is ALREADY BOOKED for this slot']);
        }
    }

    // AJAX: Get Booked Slots for a Date
    public function get_room_schedule() {
        $room_id = $this->input->post('room_id');
        $date = $this->input->post('date');

        if(!$room_id || !$date) { echo json_encode([]); return; }

        $bookings = $this->Room_management_model->get_room_bookings_by_date($room_id, $date);

        $data = [];
        foreach($bookings as $b) {
            $data[] = [
                'start' => date('h:i A', strtotime($b->start_time)),
                'end' => date('h:i A', strtotime($b->end_time)),
                'org' => $b->first_name . ' ' . $b->last_name,
                'status' => ucfirst($b->status)
            ];
        }
        echo json_encode($data);
    }

    public function submit_booking() {
        $session = $this->session->userdata('username');
        $user_id = $session['user_id'];

        $room_id = $this->input->post('room_id');
        $date = $this->input->post('meeting_date');
        $start = $this->input->post('start_time');
        $end = $this->input->post('end_time');

        // Handle Attendees
        $attendees = $this->input->post('attendees');
        $attendees_str = '';
        if(!empty($attendees)) {
            $attendees_str = implode(',', $attendees);
        }

        // Safety Re-check
        $available = $this->Room_management_model->check_availability($room_id, $date, $start, $end);

        if(!$available) {
            $this->session->set_flashdata('error', 'Booking Failed: Slot was taken just now.');
            redirect('admin/room_management/book_room');
        }

        $data = [
            'room_id' => $room_id,
            'employee_id' => $user_id,
            'attendees' => $attendees_str,
            'meeting_date' => $date,
            'start_time' => $start,
            'end_time' => $end,
            'end_time' => $end,
            'purpose' => $this->input->post('purpose'),
            'message' => $this->input->post('message'),
            'status' => 'pending'
        ];

        $this->Room_management_model->add_booking($data);

        // Send Push Notification to Attendees
        if(!empty($attendees)) {
             $debug_log = FCPATH . 'fcm_room_debug.log';
             file_put_contents($debug_log, "--- New Room Booking ---\n", FILE_APPEND);
             file_put_contents($debug_log, "Attendees: " . print_r($attendees, true) . "\n", FILE_APPEND);

             $this->db->select('device_id');
             $this->db->from('xin_employees');
             $this->db->where_in('user_id', $attendees);
             $this->db->where('device_id !=', '');
             $this->db->where('device_id IS NOT NULL');
             $tokens_query = $this->db->get();

             $tokens = [];
             foreach ($tokens_query->result() as $row) {
                 $tokens[] = $row->device_id;
             }

             file_put_contents($debug_log, "Tokens Found: " . count($tokens) . "\n", FILE_APPEND);

             if(!empty($tokens)) {
                 $this->load->helper('general');
                 $title = "Meeting Invitation";
                 $body = "You have been invited to a meeting on " . $date . " (" . $start . " - " . $end . "). Purpose: " . $this->input->post('purpose');
                 $res = send_firebase_notification($tokens, $title, $body);
                 file_put_contents($debug_log, "FCM Response: " . print_r($res, true) . "\n", FILE_APPEND);
             }
        }

        $this->session->set_flashdata('success', 'Booking Request Submitted. Waiting for Approval.');
        redirect('admin/room_management/my_bookings');
    }

    public function my_bookings() {
        $session = $this->session->userdata('username');
        $data['title'] = 'My Bookings';
        $data['breadcrumbs'] = 'My Bookings';
        $data['user_id'] = $session['user_id'];
        $data['bookings'] = $this->Room_management_model->get_employee_bookings($session['user_id']);

        $data['subview'] = $this->load->view("admin/room_management/my_bookings", $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data);
    }

    // --- HR ACTION: Approval Panel ---
    public function approvals() {
        $session = $this->session->userdata('username');
        $is_admin = $this->is_admin_or_hr($session['role_id']);
        if(!$is_admin) { redirect('admin/room_management'); }

        $data['title'] = 'Booking Approvals';
        $data['breadcrumbs'] = 'Approvals';
        $data['is_admin'] = $is_admin;
        $data['bookings'] = $this->Room_management_model->get_all_bookings();

        // Fetch Employee Map for Attendees display
        $employees = $this->Xin_model->all_employees();
        $data['employee_map'] = [];
        foreach($employees as $e) {
            $data['employee_map'][$e->user_id] = $e->first_name . ' ' . $e->last_name;
        }

        $data['subview'] = $this->load->view("admin/room_management/approvals", $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data);
    }

    public function update_status($id, $status) {
        $session = $this->session->userdata('username');
        if(!$this->is_admin_or_hr($session['role_id'])) { redirect('admin/room_management'); }

        // If status is approved, double check overlap again to be 100% sure
        if($status == 'approved') {
            $booking = $this->Room_management_model->get_booking($id);
            $available = $this->Room_management_model->check_availability($booking->room_id, $booking->meeting_date, $booking->start_time, $booking->end_time, $id);
            if(!$available) {
                $this->session->set_flashdata('error', 'Cannot Approve: Conflict detected with another approved booking.');
                redirect('admin/room_management/approvals');
            }
        }

        $data = [
            'status' => $status,
            'approved_by' => $session['user_id'],
            'approved_at' => date('Y-m-d H:i:s')
        ];

        $this->Room_management_model->update_booking($id, $data);
        $this->session->set_flashdata('success', 'Booking ' . ucfirst($status));
        redirect('admin/room_management/approvals');
    }

    // HR Edit Pending Time
    public function edit_booking_time() {
        $session = $this->session->userdata('username');
        if(!$this->is_admin_or_hr($session['role_id'])) { return; }

        $id = $this->input->post('booking_id');
        $start = $this->input->post('start_time');
        $end = $this->input->post('end_time');

        // Verify Status is pending
        $booking = $this->Room_management_model->get_booking($id);
        if($booking->status != 'pending') {
            $this->session->set_flashdata('error', 'Can only edit pending bookings');
            redirect('admin/room_management/approvals');
        }

        $this->Room_management_model->update_booking($id, ['start_time' => $start, 'end_time' => $end]);
        $this->session->set_flashdata('success', 'Time Updated');
        redirect('admin/room_management/approvals');
    }

    private function is_admin_or_hr($role_id) {
        return in_array($role_id, [1, 2, 4]); // Admin, HR roles
    }
}
?>
