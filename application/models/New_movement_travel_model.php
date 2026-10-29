<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class New_movement_travel_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function start_travel($data) {
        $this->db->insert('new_movement_travels', $data);
        return $this->db->insert_id();
    }

    public function get_active_travel($movement_id) {
        $this->db->where('movement_id', $movement_id);
        $this->db->where('status', 'running');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('new_movement_travels');
        return $query->row();
    }
    
    public function get_travels_by_movement($movement_id) {
        $this->db->where('movement_id', $movement_id);
        $query = $this->db->get('new_movement_travels');
        return $query->result();
    }

    public function end_travel($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('new_movement_travels', $data);
    }
}
