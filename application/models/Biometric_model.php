<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Biometric_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // Get all devices
    public function get_devices()
    {
        return $this->db->get('xin_biometric_devices')->result();
    }

    // Get single device
    public function read_device_information($id)
    {
        return $this->db->where('id', $id)->get('xin_biometric_devices')->row();
    }

    // Add device
    public function add_device($data)
    {
        $this->db->insert('xin_biometric_devices', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Update device
    public function update_device($data, $id)
    {
        $this->db->where('id', $id)->update('xin_biometric_devices', $data);
        return true;
    }

    // Delete device
    public function delete_device($id)
    {
        $this->db->where('id', $id)->delete('xin_biometric_devices');
        return true;
    }

    // Get all commands
    public function get_commands()
    {
        $this->db->select('xin_biometric_commands.*, xin_biometric_devices.device_name');
        $this->db->from('xin_biometric_commands');
        $this->db->join('xin_biometric_devices', 'xin_biometric_commands.device_id = xin_biometric_devices.id', 'left');
        $this->db->order_by('xin_biometric_commands.created_at', 'DESC');
        return $this->db->get()->result();
    }

    // Add command
    public function add_command($data)
    {
        $this->db->insert('xin_biometric_commands', $data);
        return $this->db->insert_id();
    }

    // Get attendance logs (from xin_att_machine)
    public function get_attendance_logs()
    {
        $this->db->select('xin_att_machine.*, xin_biometric_devices.device_name as dev_name, xin_employees.first_name, xin_employees.last_name');
        $this->db->from('xin_att_machine');
        // Assuming we might map device_id in xin_att_machine to our new table later, 
        // but for now, let's just fetch everything. 
        // Note: xin_att_machine might optionally have device_id already.
        $this->db->join('xin_biometric_devices', 'xin_att_machine.device_id = xin_biometric_devices.id', 'left');
        $this->db->join('xin_proxi', 'xin_att_machine.proxi_id = xin_proxi.proxi_id', 'left');
        $this->db->join('xin_employees', 'xin_proxi.emp_id = xin_employees.user_id', 'left');
        $this->db->order_by('date_time', 'DESC');
        $this->db->limit(1000); // Limit to recent 1000 for performance
        return $this->db->get()->result();
    }

    // Get employees for mapping
    public function get_employees_mapping()
    {
        // Fetch employees and their current mapping (xin_proxi)
        // Note: xin_proxi relation: xin_employees.user_id = xin_proxi.emp_id
        $this->db->select('xin_employees.user_id, xin_employees.first_name, xin_employees.last_name, xin_employees.employee_id, xin_proxi.proxi_id, xin_proxi.id as mapping_id');
        $this->db->from('xin_employees');
        $this->db->join('xin_proxi', 'xin_employees.user_id = xin_proxi.emp_id', 'left');
        $this->db->where('xin_employees.is_active', 1);
        return $this->db->get()->result();
    }

    // Update Proxi ID (Mapping)
    public function update_employee_mapping($user_id, $proxi_id)
    {
        // specific check if proxi already exists for another user to avoid duplicates? 
        // For now, simple insert/update
        $existing = $this->db->where('emp_id', $user_id)->get('xin_proxi')->row();

        if ($existing) {
            $this->db->where('emp_id', $user_id)->update('xin_proxi', ['proxi_id' => $proxi_id]);
        } else {
            $this->db->insert('xin_proxi', ['emp_id' => $user_id, 'proxi_id' => $proxi_id]);
        }
        return true;
    }
}
