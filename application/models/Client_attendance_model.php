<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Client_attendance_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_client_attendance($from_date = null, $to_date = null, $emp_id = null) {
        $this->db->select("ca.*, xe.first_name, xe.last_name, xe.employee_id");
        $this->db->from("client_attendance ca");
        $this->db->join('xin_employees xe', 'ca.emp_id = xe.user_id', 'left');
        
        if ($from_date && $to_date) {
            $this->db->where("ca.date BETWEEN '$from_date' AND '$to_date'");
        }
        
        if ($emp_id) {
            $this->db->where("ca.emp_id", $emp_id);
        }
        
        $this->db->order_by('ca.id', 'desc');
        return $this->db->get()->result();
    }
}
