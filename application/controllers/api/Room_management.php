<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/API_Controller.php';

class Room_management extends API_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('api_helper');
        $this->load->model('Room_management_model');
        $this->load->model('Xin_model');
    }

    // =========================================================================
    // PUBLIC / EMPLOYEE ENDPOINTS
    // =========================================================================

    /**
     * Get List of Meeting Rooms
     * Endpoint: GET api/room_management/rooms
     */
    public function rooms() {
        $this->_APIConfig(['methods' => ['GET']]);
        
        // Authorization Check
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        
        if($user_info['status'] == false) {
             $this->api_return(['status' => false, 'message' => $user_info['message']], 401); return;
        }

        // Return active rooms for employees, all for admins (optional logic)
        // For simplicity, returning active rooms which is what matters for booking
        $rooms = $this->Room_management_model->get_active_rooms();

        $this->api_return([
            'status' => true,
            'data' => $rooms
        ], 200);
    }

    public function employees() {
        $this->_APIConfig(['methods' => ['GET']]);
        
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        
        if($user_info['status'] == false) {
             $this->api_return(['status' => false, 'message' => $user_info['message']], 401); return;
        }

        $this->db->select('user_id, first_name, last_name, email');
        $this->db->from('xin_employees');
        $this->db->where('is_active', 1);
        // exclude self potentially, or just list everyone
        $employees = $this->db->get()->result();
        
        $data = [];
        foreach($employees as $emp) {
            $data[] = [
                'id' => $emp->user_id,
                'name' => $emp->first_name . ' ' . $emp->last_name . ' (' . $emp->email . ')'
            ];
        }

        $this->api_return([
            'status' => true,
            'data' => $data
        ], 200);
    }

    /**
     * Check Room Schedule (Availability)
     * Endpoint: GET api/room_management/schedule?room_id=X&date=YYYY-MM-DD
     */
    public function schedule() {
         $this->_APIConfig(['methods' => ['GET']]);
        
        // Authorization Check
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if($user_info['status'] == false) {
             $this->api_return(['status' => false, 'message' => $user_info['message']], 401); return;
        }

        $room_id = $this->input->get('room_id');
        $date = $this->input->get('date');

        if(!$room_id || !$date) {
            $this->api_return(['status' => false, 'message' => 'room_id and date are required'], 400); return;
        }

        $bookings = $this->Room_management_model->get_room_bookings_by_date($room_id, $date);
        
        // Format for easy consumption
        $schedule = [];
        foreach($bookings as $b) {
            $schedule[] = [
                'start_time' => $b->start_time,
                'end_time' => $b->end_time,
                'formatted_time' => date('h:i A', strtotime($b->start_time)) . ' - ' . date('h:i A', strtotime($b->end_time)),
                'status' => ucfirst($b->status),
                'organizer' => $b->first_name . ' ' . $b->last_name
            ];
        }

        $this->api_return([
            'status' => true,
            'data' => $schedule
        ], 200);
    }

    /**
     * Check Specific Slot Availability
     * Endpoint: GET api/room_management/check_availability?room_id=X&date=YYYY-MM-DD&start_time=HH:MM&end_time=HH:MM
     */
    public function check_availability() {
        $this->_APIConfig(['methods' => ['GET']]);
        
        // Authorization Check
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if($user_info['status'] == false) {
             $this->api_return(['status' => false, 'message' => $user_info['message']], 401); return;
        }

        $room_id = $this->input->get('room_id');
        $date = $this->input->get('date');
        $start_time = $this->input->get('start_time');
        $end_time = $this->input->get('end_time');

        if(!$room_id || !$date || !$start_time || !$end_time) {
            $this->api_return(['status' => false, 'message' => 'room_id, date, start_time, and end_time are required'], 400); return;
        }

        $is_available = $this->Room_management_model->check_availability($room_id, $date, $start_time, $end_time);

        if($is_available) {
            $this->api_return([
                'status' => true,
                'message' => 'Room is available',
                'data' => ['available' => true]
            ], 200);
        } else {
            $this->api_return([
                'status' => true, 
                'message' => 'Room is NOT available',
                'data' => ['available' => false]
            ], 200);
        }
    }

    /**
     * Create New Booking
     * Endpoint: POST api/room_management/book
     * params: room_id, meeting_date, start_time, end_time, purpose, attendees (comma separated IDs)
     */
    public function book() {
         $this->_APIConfig(['methods' => ['POST']]);
        
        // Authorization Check
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if($user_info['status'] == false) {
             $this->api_return(['status' => false, 'message' => $user_info['message']], 401); return;
        }
        $user_id = $user_info['user_info']->user_id;

        // Validation
        $room_id = $this->input->post('room_id');
        $date = $this->input->post('meeting_date');
        $start = $this->input->post('start_time');
        $end = $this->input->post('end_time');
        $purpose = $this->input->post('purpose');
        $attendees = $this->input->post('attendees'); // Expecting "1,2,3"
        $message = $this->input->post('message'); // Expecting "1,2,3"

        if(!$room_id || !$date || !$start || !$end || !$purpose) {
            $this->api_return(['status' => false, 'message' => 'Missing required fields'], 400); return;
        }

        // Validate Room Exists
        $room = $this->Room_management_model->get_room($room_id);
        if(!$room) {
            $this->api_return(['status' => false, 'message' => 'Invalid Room ID'], 404); return;
        }

        // Availability Check
        $available = $this->Room_management_model->check_availability($room_id, $date, $start, $end);
        if(!$available) {
            $this->api_return(['status' => false, 'message' => 'Room is not available for this time slot'], 409); return;
        }

        $data = [
            'room_id' => $room_id,
            'employee_id' => $user_id,
            'attendees' => $attendees,
            'meeting_date' => $date,
            'start_time' => $start,
            'end_time' => $end,
            'purpose' => $purpose,
            'status' => 'pending', // Default pending
            'message' => $message // Default pending
        ];

        $this->Room_management_model->add_booking($data);

        // Send Push Notification
        if(!empty($attendees)) {
            $ids = explode(',', $attendees);
             

             $this->db->select('device_id');
             $this->db->from('xin_employees');
             $this->db->where_in('employee_id', $ids); // Changed from user_id to employee_id
             $this->db->where('device_id !=', '');
             $this->db->where('device_id IS NOT NULL');
             $tokens_query = $this->db->get();
             
             $tokens = [];
             foreach ($tokens_query->result() as $row) {
                 $tokens[] = $row->device_id;
             }
             
             if(!empty($tokens)) {
                 $this->load->helper('general'); 
                 
                 $title = "Meeting Invitation";
                 $body = "You have been invited to a meeting on " . $date . " (" . $start . " - " . $end . "). Purpose: " . $purpose;
                 
                 $res = send_firebase_notification($tokens, $title, $body);
                 
             }
        }

        $this->api_return([
            'status' => true,
            'message' => 'Booking request submitted successfully'
        ], 201);
    }

    /**
     * Get My Bookings
     * Endpoint: GET api/room_management/my_bookings
     */
    public function my_bookings() {
         $this->_APIConfig(['methods' => ['GET']]);
        
        // Authorization Check
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if($user_info['status'] == false) {
             $this->api_return(['status' => false, 'message' => $user_info['message']], 401); return;
        }
        $user_id = $user_info['user_info']->user_id;

        $bookings = $this->Room_management_model->get_employee_bookings($user_id);
        
        // Enrich with Attendee Names
        foreach($bookings as $key => $booking) {
            $attendent = [];
            if(!empty($booking->attendees)) {
                $ids = explode(',', $booking->attendees);
                if(!empty($ids)) {
                    $this->db->select('user_id,first_name, last_name');
                    $this->db->where_in('user_id', $ids);
                    $employees = $this->db->get('xin_employees')->result();
                    
                    foreach($employees as $emp) {
                        $attendent[] = ['id' => $emp->user_id, 'name' => $emp->first_name . ' ' . $emp->last_name];
                    }
                }
            }
            $bookings[$key]->attendent = $attendent;
        }
        
        $this->api_return([
            'status' => true,
            'data' => $bookings
        ], 200);
    }

    // =========================================================================
    // ADMIN ENDPOINTS
    // =========================================================================

    /**
     * Admin: Get All Bookings (Approvals List)
     * Endpoint: GET api/room_management/all_bookings
     */
    public function all_bookings() {
         $this->_APIConfig(['methods' => ['GET']]);
        
        // Authorization Check
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if($user_info['status'] == false) {
             $this->api_return(['status' => false, 'message' => $user_info['message']], 401); return;
        }
        
        // Role Check (Admin/HR only)
        $role_id = $user_info['user_info']->user_role_id;
        if(!in_array($role_id, [1, 2, 4])) { // 1=SupAdmin, 2=Admin, 4=HR
             $this->api_return(['status' => false, 'message' => 'Permission Denied'], 403); return;
        }

        $bookings = $this->Room_management_model->get_all_bookings();

        $this->api_return([
            'status' => true,
            'data' => $bookings
        ], 200);
    }

    /**
     * Admin: Update Booking Status
     * Endpoint: POST api/room_management/update_status
     * params: booking_id, status (approved/rejected)
     */
    public function update_status() {
         $this->_APIConfig(['methods' => ['POST']]);
        
        // Authorization Check
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if($user_info['status'] == false) {
             $this->api_return(['status' => false, 'message' => $user_info['message']], 401); return;
        }

        // Role Check
        $role_id = $user_info['user_info']->user_role_id;
        if(!in_array($role_id, [1, 2, 4])) {
             $this->api_return(['status' => false, 'message' => 'Permission Denied'], 403); return;
        }

        $booking_id = $this->input->post('booking_id');
        $status = $this->input->post('status');

        if(!$booking_id || !in_array($status, ['approved', 'rejected'])) {
            $this->api_return(['status' => false, 'message' => 'Invalid parameters'], 400); return;
        }

        // Conflict Verification for Approval
        if($status == 'approved') {
            $booking = $this->Room_management_model->get_booking($booking_id);
            if(!$booking) {
                $this->api_return(['status' => false, 'message' => 'Booking not found'], 404); return;
            }
            $available = $this->Room_management_model->check_availability($booking->room_id, $booking->meeting_date, $booking->start_time, $booking->end_time, $booking_id);
            if(!$available) {
                $this->api_return(['status' => false, 'message' => 'Conflict detected! Room is already booked for this slot.'], 409); return;
            }
        }

        $data = [
            'status' => $status,
            'approved_by' => $user_info['user_info']->user_id,
            'approved_at' => date('Y-m-d H:i:s')
        ];

        $this->Room_management_model->update_booking($booking_id, $data);

        $this->api_return([
            'status' => true,
            'message' => 'Booking status updated to ' . $status
        ], 200);
    }

    /**
     * Admin: Manage Room (Add/Edit)
     * Endpoint: POST api/room_management/manage_room
     * params: room_id (optional for update), name, capacity, location, description, status
     */
    public function manage_room() {
         $this->_APIConfig(['methods' => ['GET']]);
        
        // Authorization Check
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if($user_info['status'] == false) {
             $this->api_return(['status' => false, 'message' => $user_info['message']], 401); return;
        }
        
        // Role Check
        $role_id = $user_info['user_info']->user_role_id;
        if(!in_array($role_id, [1, 2, 4])) {
             $this->api_return(['status' => false, 'message' => 'Permission Denied'], 403); return;
        }

        if(!$this->input->post('name')) {
             $this->api_return(['status' => false, 'message' => 'Room Name is required'], 400); return;
        }

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
            $message = 'Room Updated Successfully';
        } else {
            $this->Room_management_model->add_room($data);
            $message = 'Room Added Successfully';
        }

        $this->api_return([
            'status' => true,
            'message' => $message
        ], 200);
    }
}
