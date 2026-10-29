<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Task_break_downs_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // get all task breakdowns
    public function get_task_break_downs($user_id, $role_id) {
        if ($role_id == 3) {
            $this->db->where("created_by", $user_id);
        }
        $this->db->order_by("date", 'DESC');
        return $this->db->get("task_breack_downs");
    }

    public function get_assign_task_break($user_id) {
        $this->db->where("emp_id", $user_id);
        $this->db->order_by("date", 'DESC');
        return $this->db->get("task_breack_downs");
    }

    // Function to add record in table
    public function add($data){
        $this->db->insert('task_breack_downs', $data);
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    // Function to read record from table
    public function read_task_break_down_information($id) {
        $sql = 'SELECT * FROM task_breack_downs WHERE id = ? ORDER BY date DESC';
        $binds = array($id);
        $query = $this->db->query($sql, $binds);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    // Function to update record in table
    public function update_record($data, $id){
        $this->db->where('id', $id);
        if( $this->db->update('task_breack_downs',$data)) {
            return true;
        } else {
            return false;
        }
    }

    // Function to Delete selected record from table
    public function delete_record($id){
        $this->db->where('id', $id);
        $this->db->delete('task_breack_downs');
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function add_batch($data){
        return $this->db->insert_batch('task_breack_downs', $data);
    }

    // Get assigned tasks for a user
    public function get_assigned_tasks($user_id) {
        $this->db->where('emp_id', $user_id);
        $this->db->order_by("date", 'DESC');
        return $this->db->get("task_breack_downs");
    }

    // Get tasks created by a user
    public function get_created_tasks($user_id) {
        $this->db->where('created_by', $user_id);
        $this->db->order_by("date", 'DESC');
        return $this->db->get("task_breack_downs");
    }

}
?>
