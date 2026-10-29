<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Room_management_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // --- Rooms CRUD ---
    public function get_all_rooms() {
        return $this->db->get('meeting_rooms')->result();
    }

    public function get_active_rooms() {
        $this->db->where('status', 'active');
        return $this->db->get('meeting_rooms')->result();
    }

    public function get_room($id) {
        $this->db->where('id', $id);
        return $this->db->get('meeting_rooms')->row();
    }

    public function add_room($data) {
        return $this->db->insert('meeting_rooms', $data);
    }

    public function update_room($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('meeting_rooms', $data);
    }

    public function delete_room($id) {
        $this->db->where('id', $id);
        return $this->db->delete('meeting_rooms');
    }

    // --- Bookings ---
    public function add_booking($data) {
        return $this->db->insert('meeting_room_bookings', $data);
    }

    public function get_all_bookings() {
        $from_date = date('Y-m-d', strtotime('-15 days'));
        $this->db->select('meeting_room_bookings.*, meeting_rooms.name as room_name, xin_employees.first_name, xin_employees.last_name');
        $this->db->from('meeting_room_bookings');
        $this->db->join('meeting_rooms', 'meeting_rooms.id = meeting_room_bookings.room_id');
        $this->db->join('xin_employees', 'xin_employees.user_id = meeting_room_bookings.employee_id');
        $this->db->where('meeting_room_bookings.meeting_date >=', $from_date);
        $this->db->order_by('meeting_room_bookings.id', 'DESC');
        return $this->db->get()->result();
    }

    public function get_employee_bookings($employee_id) {
        $this->db->select('meeting_room_bookings.*, meeting_rooms.name as room_name');
        $this->db->from('meeting_room_bookings');
        $this->db->join('meeting_rooms', 'meeting_rooms.id = meeting_room_bookings.room_id');

        $this->db->group_start();
        $this->db->where('meeting_room_bookings.employee_id', $employee_id);
        $this->db->or_where("FIND_IN_SET('$employee_id', meeting_room_bookings.attendees) !=", 0);
        $this->db->group_end();

        $this->db->order_by('meeting_room_bookings.id', 'DESC');
        return $this->db->get()->result();
    }

    public function get_booking($id) {
        return $this->db->get_where('meeting_room_bookings', ['id' => $id])->row();
    }

    public function update_booking($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('meeting_room_bookings', $data);
    }

    public function get_room_bookings_by_date($room_id, $date) {
        $this->db->select('start_time, end_time, meeting_room_bookings.status, meeting_room_bookings.employee_id, xin_employees.first_name, xin_employees.last_name');
        $this->db->from('meeting_room_bookings');
        $this->db->join('xin_employees', 'xin_employees.user_id = meeting_room_bookings.employee_id');
        $this->db->where('room_id', $room_id);
        $this->db->where('meeting_date', $date);
        $this->db->where_in('meeting_room_bookings.status', ['approved', 'pending']);
        $this->db->order_by('start_time', 'ASC');
        return $this->db->get()->result();
    }
    public function check_availability($room_id, $date, $start_time, $end_time, $exclude_booking_id = null) {
        // Business Rule: Only 'approved' bookings block time.
        // Pending bookings do NOT block time (as per strict rule).
        // However, usually systems block pending too to avoid double request.
        // User Requirement: "System checks approved bookings" -> implies pending might not block?
        // Let's re-read: "Approved booking = lock". "Pending bookings do not block".

        $this->db->where('room_id', $room_id);
        $this->db->where('meeting_date', $date);
        $this->db->where('status', 'approved'); // Only approved blocks

        if ($exclude_booking_id) {
            $this->db->where('id !=', $exclude_booking_id);
        }

        // Overlap Formula: (StartA < EndB) and (EndA > StartB)
        $this->db->group_start();
            $this->db->where("start_time <", $end_time);
            $this->db->where("end_time >", $start_time);
        $this->db->group_end();

        $count = $this->db->count_all_results('meeting_room_bookings');

        return $count === 0; // True if Available (0 overlaps)
    }
    public function count_pending_bookings() {
        $this->db->where('status', 'pending');
        return $this->db->count_all_results('meeting_room_bookings');
    }
}
?>
