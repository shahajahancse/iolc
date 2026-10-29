<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Biometric extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Biometric_model");
        $this->load->model("Xin_model");

        // Ensure only admin access - adjust role check as needed
        $session = $this->session->userdata('username');
        if (empty($session)) {
            redirect('admin/');
        }
    }

    // Default: List Devices
    public function devices()
    {
        $data['title'] = $this->Xin_model->site_title();
        $data['breadcrumbs'] = 'Biometric Devices';
        $data['path_url'] = 'biometric_devices';
        $session = $this->session->userdata('username');
        $data['subview'] = $this->load->view("admin/biometric/devices", $data, true);
        $this->load->view('admin/layout/layout_main', $data);
    }

    // List devices data (for ajax datatable if needed, or just pass to view)
    // For simplicity, we'll fetch in view or controller and pass to view directly as we did in index
    public function device_list()
    {
        $session = $this->session->userdata('username');
        if (empty($session)) {
            redirect('admin/');
        }
        // Datatable handling
        $draw = intval($this->input->get("draw"));
        $devices = $this->Biometric_model->get_devices();
        $data = array();

        foreach ($devices as $r) {
            $status = '<span class="badge bg-red">Offline</span>';
            if ($r->status == 1) {
                $status = '<span class="badge bg-green">Online</span>';
            }

            $functions = '<span data-toggle="tooltip" data-placement="top" title="Ping/Check Device"><button type="button" class="btn btn-info btn-sm m-b-0-0 waves-effect waves-light ping-device" data-device_id="' . $r->id . '"><i class="fa fa-refresh"></i></button></span>';
            $functions .= '<span data-toggle="tooltip" data-placement="top" title="Edit"><button type="button" class="btn btn-secondary btn-sm m-b-0-0 waves-effect waves-light" data-toggle="modal" data-target=".edit-modal-data" data-device_id="' . $r->id . '"><i class="fa fa-pencil-square-o"></i></button></span>';
            $functions .= '<span data-toggle="tooltip" data-placement="top" title="Delete"><button type="button" class="btn btn-danger btn-sm m-b-0-0 waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->id . '"><i class="fa fa-trash-o"></i></button></span>';

            $data[] = array(
                $r->device_name,
                $r->serial_number,
                $r->ip_address,
                $r->last_seen ? date("d F Y h:i A", strtotime($r->last_seen)) : 'Never',
                $status,
                $functions
            );
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => count($devices),
            "recordsFiltered" => count($devices),
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    // Add Device
    public function add_device()
    {
        if ($this->input->post('add_type') == 'device') {
            $data = array(
                'device_name' => $this->input->post('device_name'),
                'serial_number' => $this->input->post('serial_number'),
                'ip_address' => $this->input->post('ip_address'),
                'port' => $this->input->post('port'),
                'status' => 0,
                'created_at' => date('Y-m-d H:i:s')
            );
            $result = $this->Biometric_model->add_device($data);
            if ($result == true) {
                $Return['result'] = 'Device added.';
            } else {
                $Return['error'] = 'Bug. Something went wrong.';
            }
            echo json_encode($Return);
            exit;
        }
    }

    // Delete Device
    public function delete_device()
    {
        if ($this->input->post('type') == 'delete') {
            $id = $this->input->post('id_entry');
            $result = $this->Biometric_model->delete_device($id);
            if ($result == true) {
                $Return['result'] = 'Device deleted.';
            } else {
                $Return['error'] = 'Bug. Something went wrong.';
            }
            echo json_encode($Return);
            exit;
        }
    }

    // Attendance Logs View
    public function attendance_logs()
    {
        $data['title'] = $this->Xin_model->site_title();
        $data['breadcrumbs'] = 'Attendance Logs';
        $data['path_url'] = 'biometric_logs';
        $data['logs'] = $this->Biometric_model->get_attendance_logs(); // Pass directly or use ajax
        $data['subview'] = $this->load->view("admin/biometric/attendance_logs", $data, true);
        $this->load->view('admin/layout/layout_main', $data);
    }

    // Employee Mapping View
    public function employees_mapping()
    {
        $data['title'] = $this->Xin_model->site_title();
        $data['breadcrumbs'] = 'Employees Mapping';
        $data['path_url'] = 'biometric_mapping';
        $data['employees'] = $this->Biometric_model->get_employees_mapping();
        $data['subview'] = $this->load->view("admin/biometric/employees_mapping", $data, true);
        $this->load->view('admin/layout/layout_main', $data);
    }

    // Update Mapping (Ajax)
    public function update_mapping()
    {
        $user_id = $this->input->post('user_id');
        $proxi_id = $this->input->post('proxi_id');

        $this->Biometric_model->update_employee_mapping($user_id, $proxi_id);
        $Return['result'] = 'Mapping updated.';
        echo json_encode($Return);
        exit;
    }

    // Send Command (Ping/Check)
    public function send_command()
    {
        $device_id = $this->input->post('device_id');
        $command = $this->input->post('command_type'); // e.g. 'CHECK', 'REBOOT'

        if (!$device_id || !$command) {
            echo json_encode(['error' => 'Invalid parameters']);
            exit;
        }

        // For now, simple textual commands. 
        // Helper to formatting ADMS commands if needed, but 'CHECK' is standard.
        // If Reboot: 'Reboot', TimeSync: 'INFO' typically triggers info update? 
        // Actually 'CHECK' forces a check config. 
        // 'REBOOT' is 'Reboot'.

        $cmd_string = $command;
        if ($command == 'CHECK') {
            $cmd_string = 'CHECK';
        }

        $data = array(
            'device_id' => $device_id,
            'command_type' => $command,
            'command_data' => $cmd_string,
            'status' => 0, // Pending
            'created_at' => date('Y-m-d H:i:s')
        );

        $this->Biometric_model->add_command($data);

        echo json_encode(['result' => 'Command queued successfully. Device will pick it up shortly.']);
        exit;
    }

    // Commands View
    public function commands()
    {
        $data['title'] = $this->Xin_model->site_title();
        $data['breadcrumbs'] = 'Commands';
        $data['path_url'] = 'biometric_commands';
        $data['commands'] = $this->Biometric_model->get_commands();
        $data['subview'] = $this->load->view("admin/biometric/commands", $data, true);
        $this->load->view('admin/layout/layout_main', $data);
    }
}
