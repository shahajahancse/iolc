<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Todo_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_user_todos($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->order_by('status', 'ASC'); // Pending first
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('xin_todo_list')->result();
    }

    public function get_todo_by_id($id, $user_id) {
        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id); // Security check
        return $this->db->get('xin_todo_list')->row();
    }

    public function add_todo($data) {
        return $this->db->insert('xin_todo_list', $data);
    }

    public function update_status($id, $user_id, $status) {
        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id); // Security check
        return $this->db->update('xin_todo_list', array('status' => $status));
    }

    public function update_todo($id, $user_id, $data) {
        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id); // Security check
        return $this->db->update('xin_todo_list', $data);
    }

    public function delete_todo($id, $user_id) {
        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id); // Security check
        return $this->db->delete('xin_todo_list');
    }
}
