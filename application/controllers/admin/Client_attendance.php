<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Client_attendance extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("Client_attendance_model");
        $this->load->model("Xin_model"); // For getting employee list
    }

    public function index() {
        $data['title'] = $this->Xin_model->site_title();
        $data['breadcrumbs'] = 'Client Attendance';
        $data['path_url'] = 'client_attendance';
        
        $data['all_employees'] = $this->Xin_model->all_employees();
        
        // Default filter: Current Month
        $data['from_date'] = date('Y-m-01');
        $data['to_date'] = date('Y-m-t');
        
        $data['subview'] = $this->load->view('admin/client_attendance/attendance_list', $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data);
    }
    
    public function get_list() {
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $emp_id = $this->input->post('employee_id');
        
        // Validation or default
        if(!$from_date) $from_date = date('Y-m-01');
        if(!$to_date) $to_date = date('Y-m-t');

        $data['attendance_list'] = $this->Client_attendance_model->get_client_attendance($from_date, $to_date, $emp_id);
        
        $this->load->view('admin/client_attendance/attendance_table', $data);
    }

    public function export() {
         $from_date = $this->input->get('from_date');
         $to_date = $this->input->get('to_date');
         $emp_id = $this->input->get('employee_id');

         $attendance_list = $this->Client_attendance_model->get_client_attendance($from_date, $to_date, $emp_id);

         // CSV Export logic
         $filename = 'client_attendance_' . date('Ymd') . '.csv';
         header("Content-Description: File Transfer");
         header("Content-Disposition: attachment; filename=$filename");
         header("Content-Type: application/csv; "); 
         
         $file = fopen('php://output', 'w');
         
         $header = array("Date", "Employee Name", "Client Name", "Type", "Representative", "Mobile", "Location (Lat/Lng)", "Address", "Remarks"); 
         fputcsv($file, $header);
         
         foreach ($attendance_list as $row){ 
             $line = array(
                 $row->date,
                 $row->first_name . ' ' . $row->last_name,
                 $row->client_name,
                 $row->type,
                 $row->representative_name,
                 $row->mobile,
                 $row->location_latitude . ', ' . $row->location_longitude,
                 $row->address,
                 $row->remarks
             );
             fputcsv($file, $line);
         }
         
         fclose($file); 
         exit; 
    }
}
