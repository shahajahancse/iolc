<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class New_movement_meeting_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function start_meeting($data)
    {
        $this->db->insert('new_movement_meetings', $data);
        return $this->db->insert_id();
    }

    public function get_active_meeting($movement_id)
    {
        $this->db->where('movement_id', $movement_id);
        $this->db->where('end_time', NULL); // Assuming active meeting has no end_time
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('new_movement_meetings');
        return $query->row();
    }

    public function get_meetings_by_movement($movement_id)
    {
        $this->db->where('movement_id', $movement_id);
        $query = $this->db->get('new_movement_meetings');
        return $query->result();
    }

    public function end_meeting($id, $end_time)
    {
        $this->db->where('id', $id);
        return $this->db->update('new_movement_meetings', ['end_time' => $end_time]);
    }
    public function get_last_meeting($movement_id)
    {
        $this->db->where('movement_id', $movement_id);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('new_movement_meetings');
        return $query->row();
    }

    public function update_meeting_feedback($id, $feedback_or_data)
    {
        $this->db->where('id', $id);

        $data = [];
        if (is_array($feedback_or_data)) {
            $data = $feedback_or_data;
        } else {
            $data = ['feedback' => $feedback_or_data];
        }

        return $this->db->update('new_movement_meetings', $data);
    }

    public function get_pending_feedback_meeting($movement_id)
    {
        $this->db->where('movement_id', $movement_id);
        $this->db->where('end_time !=', NULL);
        $this->db->where('feedback', NULL);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        return $this->db->get('new_movement_meetings')->row();
    }

    public function search_clients($query)
    {
        $this->db->distinct();
        $this->db->select('client_name, contact_person, contact_email, contact_phone, contact_job_title, location, latitude, longitude');
        $this->db->like('client_name', $query);
        $this->db->group_by('client_name'); // Ensure unique client names
        $this->db->limit(10);
        return $this->db->get('new_movement_meetings')->result();
    }

}
