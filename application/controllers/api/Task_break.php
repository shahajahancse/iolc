<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/API_Controller.php';

class Task_break extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('api_helper');
    }
    /**
     * demo method
     *
     * @link [api/user/demo]
     * @method POST
     * @return Response|void
     */

    public function assigned_list()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $user_data = $user_info['user_info'];
            $user_id = $user_data->user_id;

            $from_date = date('Y-m-d', strtotime($this->input->post('from_date')));
            $to_date = date('Y-m-d', strtotime($this->input->post('to_date')));
            $emp_id = $this->input->post('emp_id');

            $this->db->select('tb.*, emp.first_name, emp.last_name, cli.name as client_name');
            $this->db->from('task_breack_downs tb');
            $this->db->join('xin_employees emp', 'emp.user_id = tb.emp_id', 'left');
            $this->db->join('xin_clients cli', 'cli.client_id = tb.client_id', 'left');
            $this->db->where("tb.emp_id", $user_id);
            if (!empty($from_date) && !empty($to_date)) {
                $this->db->where('tb.date >=', $from_date);
                $this->db->where('tb.date <=', $to_date);
            }
            if (!empty($emp_id)) {
                $this->db->where("created_by", $emp_id);
            }

            $this->db->order_by("tb.date", 'DESC');
            $data = $this->db->get()->result();

            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'data' => $data,
            ], 200);

        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
                'data' => [],
            ], 401);
        }
    }

    public function my_task_list()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $user_data = $user_info['user_info'];
            $user_id = $user_data->user_id;

            $from_date = date('Y-m-d', strtotime($this->input->post('from_date')));
            $to_date = date('Y-m-d', strtotime($this->input->post('to_date')));
            $emp_id = $this->input->post('emp_id');

            $this->db->select('tb.*, emp.first_name, emp.last_name, cli.name as client_name');
            $this->db->from('task_breack_downs tb');
            $this->db->join('xin_employees emp', 'emp.user_id = tb.emp_id', 'left');
            $this->db->join('xin_clients cli', 'cli.client_id = tb.client_id', 'left');
            $this->db->where("created_by", $user_id);
            if (!empty($from_date) && !empty($to_date)) {
                $this->db->where('tb.date >=', $from_date);
                $this->db->where('tb.date <=', $to_date);
            }
            if (!empty($emp_id)) {
                $this->db->where('tb.emp_id', $emp_id);
            }

            $this->db->order_by("tb.date", 'DESC');
            $data = $this->db->get()->result();

            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'data' => $data,
            ], 200);

        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
                'data' => [],
            ], 401);
        }
    }

    public function task_add()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        $json = $this->input->post();
        if (empty($json)) {
            $json = json_decode(file_get_contents('php://input'), true);
        }
        if ($user_info['status'] == true) {
            $user_data = $user_info['user_info'];
            $user_id = $user_data->user_id;

            $emp_id    = $json['emp_id'];
            $date      = date('Y-m-d', strtotime($json['date']));
            $task_data = $json['task_data'];
            $data = array();
            foreach ($task_data as $key => $value) {
                $data[] = array(
                    'emp_id' => $emp_id,
                    'date' => $date,
                    'client_id' => $value['company_id'],
                    'status' => 'pending',
                    'remarks' => $value['remark'],
                    'emp_remark' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $user_id,
                );
            }

            if ($this->db->insert_batch('task_breack_downs', $data)) {
                $this->api_return([
                    'status' => true,
                    'message' => 'successful',
                    'data' => [],
                ], 200);
            } else {
                $this->api_return([
                    'status' => false,
                    'message' => 'Unsuccessful',
                    'data' => [],
                ], 404);
            }
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
                'data' => [],
            ], 401);
        }
    }

    public function update_task()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $user_data = $user_info['user_info'];
            $user_id = $user_data->user_id;

            $task_id = $this->input->post('task_id');
            $data = array(
                'status' => $this->input->post('status'),
                'emp_remark' => $this->input->post('remark'),
                'updated_at' => date('Y-m-d H:i:s'),
                'update_by' => $user_id,
            );

            $this->db->where('id', $task_id);
            if ($this->db->update('task_breack_downs', $data)) {
                $this->api_return([
                    'status' => true,
                    'message' => 'successful',
                    'data' => [],
                ], 200);
            } else {
                $this->api_return([
                    'status' => false,
                    'message' => 'Unsuccessful',
                    'data' => [],
                ], 404);
            }
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
                'data' => [],
            ], 401);
        }
    }

    public function client_list()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true) {
            $this->db->select("client_id, name, email, contact_number, address_1, address_2, lat, long");
            $data = $this->db->get('xin_clients')->result();

            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'data' => $data,
            ], 200);

        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
                'data' => [],
            ], 401);
        }
    }

    public function client_add()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $name = $this->input->post('name');
            $address = $this->input->post('address');
            $email = $this->input->post('email');
            $contact_number = $this->input->post('contact_number');
            $lat = $this->input->post('Lat');
            $long = $this->input->post('long');

            if (!empty($name) && !empty($address)) {
                $data = array(
                    'name' => $name,
                    'address_1' => $address,
                    'email' => empty($email) ? null : $email,
                    'contact_number' => empty($contact_number) ? null : $contact_number,
                    'lat' => empty($lat) ? null : $lat,
                    'long' => empty($long) ? null : $long,
                );

                if ($this->db->insert('xin_clients', $data)) {
                    $last_id = $this->db->insert_id();
                    $data = $this->db->where('client_id', $last_id)->get('xin_clients')->row();
                    $this->api_return([
                        'status' => true,
                        'message' => 'successful',
                        'data' => $data,
                    ], 200);
                } else {
                    $this->api_return([
                        'status' => false,
                        'message' => 'Unsuccessful',
                        'data' => [],
                    ], 404);
                }
            } else {
                $this->api_return([
                    'status' => false,
                    'message' => 'Please enter name and address',
                    'data' => [],
                ], 401);
            }
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
                'data' => [],
            ], 401);
        }
    }

    public function delete_task($id)
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true) {
            $this->db->where('id', $id)->where('status', 'pending');
            if ($this->db->delete('task_breack_downs')) {
                $this->api_return([
                    'status' => true,
                    'message' => 'successful',
                    'data' => [],
                ], 200);
            } else {
                $this->api_return([
                    'status' => false,
                    'message' => 'Unsuccessful',
                    'data' => [],
                ], 404);
            }
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
                'data' => [],
            ], 401);
        }
    }

}
