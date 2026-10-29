<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Task_break_downs extends MY_Controller {

    public function __construct() {
        parent::__construct();
        //load the models
        $this->load->model("Task_break_downs_model");
        $this->load->model("Xin_model");
        $this->load->model("Employees_model");
        $this->load->model("Clients_model");
    }

    /*Function to set JSON output*/
    public function output($Return=array()){
        /*Set response header*/
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        /*Final JSON response*/
        exit(json_encode($Return));
    }

    public function index() {
        $session = $this->session->userdata('username');
        if(empty($session)){
            redirect('admin/');
        }
        $data['title'] = 'Task Breakdowns | '.$this->Xin_model->site_title();
        $data['all_employees'] = $this->Xin_model->get_task_employees();
        $data['all_clients'] = $this->Clients_model->get_clients();
        // $data['all_countries'] = $this->Xin_model->get_countries();
        $data['breadcrumbs'] = 'Task Breakdowns';
        $data['path_url'] = 'task_break_downs';
        $role_resources_ids = $this->Xin_model->user_role_resource();

        $data['subview'] = $this->load->view("admin/task_break_downs/task_break_downs_list", $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data); //page load
    }

    public function task_break_downs_list(){

        // Datatables Variables
        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));

        $data = array();

        $session = $this->session->userdata('username');
        $task_break_downs = $this->Task_break_downs_model->get_task_break_downs($session['user_id'], $session['role_id']);

        foreach($task_break_downs->result() as $r) {

            $employee = $this->Xin_model->read_user_info($r->emp_id);
            $client = $this->Clients_model->read_client_info($r->client_id);
            $client_name = !empty($client) ? $client[0]->name : '';
            if (!empty($employee)) {
                $ename = $employee[0]->first_name . ' ' . $employee[0]->last_name;
            } else {
                $ename = '';
            }

            if($r->status == 'pending') {
                $delete_button = '<span data-toggle="tooltip" data-placement="top" title="Delete"><button type="button" class="btn btn-danger btn-sm waves-effect waves-light delete" data-toggle="modal" data-target=".delete-task-modal" data-record-id="'. $r->id . '"><i class="fa fa-trash"></i></button></span>';
            } else {
                $delete_button = '';
            }
            $edit = '<span data-toggle="tooltip" data-placement="top" title="Edit"><button type="button" class="btn btn-secondary btn-sm m-b-0-0 waves-effect waves-light"  data-toggle="modal" data-target=".edit-modal-data"  data-id="'. $r->id . '"><i class="fa fa-pencil"></i></button></span>';

            $data[] = array(
                $r->id,
                $ename,
                $client_name,
                $r->date,
                $r->status,
                $r->remarks,
                $r->emp_remark,
                $edit .' '. $delete_button,
            );
        }
        // dd($data);

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $task_break_downs->num_rows(),
            "recordsFiltered" => $task_break_downs->num_rows(),
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function add_task_break_down() {

        if($this->input->post('add_type')=='task_break_down') {
            $Return = array('result'=>'', 'error'=>'');

            if($this->input->post('emp_id')==='') {
                $Return['error'] = "The employee field is required.";
            } else if($this->input->post('client_id')==='') {
                $Return['error'] = "The client field is required.";
            } else if($this->input->post('date')==='') {
                $Return['error'] = "The date field is required.";
            }

            if($Return['error']!=''){
                $this->output($Return);
            }

            $data = array(
                'emp_id' => $this->input->post('emp_id'),
                'client_id' => $this->input->post('client_id'),
                'date' => $this->input->post('date'),
                'remarks' => $this->input->post('remarks'),
                'emp_remark' => $this->input->post('emp_remark'),
                'status' => $this->input->post('status'),
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $this->session->userdata('username')['user_id'],
            );
            $result = $this->Task_break_downs_model->add($data);
            if ($result == TRUE) {
                $Return['result'] = 'Task Breakdown added.';
            } else {
                $Return['error'] = 'Bug Something wrong, please try again.';
            }
            $this->output($Return);
            exit;
        }
    }

    public function add_task_break_down_batch() {
        if($this->input->post('add_type')=='task_break_down_batch') {
            $Return = array('result'=>'', 'error'=>'');

            $emp_id = $this->input->post('emp_id');
            $date = $this->input->post('date');
            $clients = $this->input->post('client_id');
            $statuses = $this->input->post('status');
            $remarks = $this->input->post('remarks');
            $emp_remarks = $this->input->post('emp_remark');

            if(empty($emp_id)) {
                $Return['error'] = "The employee field is required.";
            } else if(empty($date)) {
                $Return['error'] = "The date field is required.";
            } else if(empty($clients)) {
                $Return['error'] = "At least one row is required.";
            }

            if($Return['error']!=''){
                $this->output($Return);
            }

            $data = array();
            for($i = 0; $i < count($clients); $i++) {
                $data[] = array(
                    'emp_id' => $emp_id,
                    'date' => $date,
                    'client_id' => $clients[$i],
                    'status' => $statuses[$i],
                    'remarks' => $remarks[$i],
                    'emp_remark' => $emp_remarks[$i],
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $this->session->userdata('username')['user_id'],
                );
            }

            $result = $this->Task_break_downs_model->add_batch($data);

            if ($result == TRUE) {
                $Return['result'] = 'Task Breakdowns added.';
            } else {
                $Return['error'] = 'Bug Something wrong, please try again.';
            }
            $this->output($Return);
            exit;
        }
    }

    public function read() {
        $data['title'] = $this->Xin_model->site_title();
        $id = $this->input->get('id');
        $result = $this->Task_break_downs_model->read_task_break_down_information($id);
        $data = array(
            'id' => $result[0]->id,
            'emp_id' => $result[0]->emp_id,
            'client_id' => $result[0]->client_id,
            'date' => $result[0]->date,
            'remarks' => $result[0]->remarks,
            'emp_remark' => $result[0]->emp_remark,
            'status' => $result[0]->status,
            'all_employees' => $this->Xin_model->get_task_employees(),
            'all_clients' => $this->Clients_model->get_clients(),
        );
        $session = $this->session->userdata('username');
        if(!empty($session)){
            $this->load->view('admin/task_break_downs/dialog_task_break_down', $data);
        } else {
            redirect('admin/');
        }
    }

    public function update() {
        if($this->input->post('edit_type')=='task_break_down') {
            $id = $this->uri->segment(4);
            $Return = array('result'=>'', 'error'=>'');

            if($this->input->post('emp_id')==='') {
                $Return['error'] = "The employee field is required.";
            } else if($this->input->post('client_id')==='') {
                $Return['error'] = "The client field is required.";
            } else if($this->input->post('date')==='') {
                $Return['error'] = "The date field is required.";
            }

            if($Return['error']!=''){
                $this->output($Return);
            }

            $data = array(
                'emp_id' => $this->input->post('emp_id'),
                'client_id' => $this->input->post('client_id'),
                'date' => $this->input->post('date'),
                'remarks' => $this->input->post('remarks'),
                'emp_remark' => $this->input->post('emp_remark'),
                'status' => $this->input->post('status'),
                'updated_at' => date('Y-m-d H:i:s'),
                'update_by' => $this->session->userdata('username')['user_id'],
            );

            $result = $this->Task_break_downs_model->update_record($data, $id);

            if ($result == TRUE) {
                $Return['result'] = 'Task Breakdown updated.';
            } else {
                $Return['error'] = 'Bug Something wrong, please try again.';
            }
            $this->output($Return);
            exit;
        }
    }

    public function delete() {
        $session = $this->session->userdata('username');
        if(empty($session)){
            redirect('admin/');
        }
        $user_info = $this->Xin_model->read_user_info($session['user_id']);
        if($user_info[0]->user_role_id != 1) {
            $Return['error'] = 'You do not have permission to delete.';
            $this->output($Return);
            return;
        }
        $Return = array('result'=>'', 'error'=>'');
        $id = $this->input->post('_token');
        $result = $this->Task_break_downs_model->delete_record($id);
        if ($result == TRUE) {
            $Return['result'] = 'Task Breakdown deleted.';
        } else {
            $Return['error'] = 'Bug Something wrong, please try again.';
        }
        $this->output($Return);
    }

    public function assigned() {
        $session = $this->session->userdata('username');
        if(empty($session)){
            redirect('admin/');
        }
        $data['title'] = 'Task assigned list';
        $data['all_employees'] = $this->Xin_model->get_task_employees();
        $data['all_clients'] = $this->Clients_model->get_clients();

        $data['breadcrumbs'] = 'Task assigned list';
        $data['path_url'] = 'task_break_downs';
        $role_resources_ids = $this->Xin_model->user_role_resource();

        if(!empty($session)){
            $data['subview'] = $this->load->view("admin/task_break_downs/assigned", $data, TRUE);
            $this->load->view('admin/layout/layout_main', $data); //page load
        } else {
            redirect('admin/');
        }
    }

    public function task_assigned_list(){

        // Datatables Variables
        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));

        $data = array();

        $session = $this->session->userdata('username');
        $task_break_downs = $this->Task_break_downs_model->get_assign_task_break($session['user_id']);

        foreach($task_break_downs->result() as $r) {

            $employee = $this->Xin_model->read_user_info($r->emp_id);
            $client = $this->Clients_model->read_client_info($r->client_id);

            $edit = '<span data-toggle="tooltip" data-placement="top" title="Edit"><button type="button" class="btn btn-secondary btn-sm m-b-0-0 waves-effect waves-light"  data-toggle="modal" data-target=".edit-modal-data"  data-id="'. $r->id . '"><i class="fa fa-pencil"></i></button></span>';

            $data[] = array(
                $r->id,
                $employee[0]->first_name . ' ' . $employee[0]->last_name,
                $client[0]->name,
                $r->date,
                $r->status,
                $r->remarks,
                $r->emp_remark,
                $edit,
            );
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $task_break_downs->num_rows(),
            "recordsFiltered" => $task_break_downs->num_rows(),
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function task_break_report(){
        $data['title'] = 'Task Breakdown Report';
        $data['breadcrumbs'] = 'Breakdown Report';
        $data['path_url'] = '';
        $data['subview'] = $this->load->view("admin/task_break_downs/task_break_report", $data, true);
        $this->load->view('admin/layout/layout_main', $data); //page load
    }

    public function get_employee_ajax_request()
    {
        $status = $this->input->get('status');
        $salary_month= date("Y-m-01", strtotime($this->input->get('salary_month')));

        $this->db->select('user_id as emp_id, first_name, last_name');
        if ($status == 1) {
            $this->db->where_in('status', array(1,4,5));
        }
        if ($status == 3){
            $this->db->where_in('status', array(2,3));
        }
        $this->db->where('user_role_id', 3);
        $this->db->order_by('user_id', 'asc');
        $result = $this->db->get('xin_employees')->result();

        $data["employees"] = $result;
        echo json_encode($data);
    }

    public function break_report(){
        $first_date = date("Y-m-d", strtotime($this->input->post('first_date')));
        $second_date = date("Y-m-d", strtotime($this->input->post('second_date')));
        $sql = $this->input->post('sql');
        $emp_id = explode(',', trim($sql));

        $this->db->select('tb.*, emp.first_name, emp.last_name, cli.name as client_name');
        $this->db->from('task_breack_downs tb');
        $this->db->join('xin_employees emp', 'emp.user_id = tb.emp_id', 'left');
        $this->db->join('xin_clients cli', 'cli.client_id = tb.client_id', 'left');
        if (!empty($sql)) {
            $this->db->where_in('tb.emp_id', $emp_id);
        }
        $this->db->where('tb.date >=', $first_date);
        $this->db->where('tb.date <=', $second_date);
        $this->db->order_by("tb.date", 'DESC');
        $data['results'] = $this->db->get()->result();

        $this->load->view("admin/task_break_downs/break_report", $data);
    }

}
?>
