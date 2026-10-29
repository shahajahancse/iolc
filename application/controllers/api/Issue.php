<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/API_Controller.php';

class Issue extends API_Controller
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

    public function index()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true) {
            $user_data = $user_info['user_info'];
            $userid = $user_data->user_id;
            $this->db->select("*");
            $this->db->where("emp_id", $userid);
            $this->db->order_by("id", "desc");
            $data['issue_data'] = $this->db->get('employee_issue')->result();

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
    public function all_issue_for_admin()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true && $user_info['user_info']->user_role_id != 3) {
            $offset = $this->input->post('offset');
                $limit = $this->input->post('limit');

                $this->db->select("ei.*, e.first_name, e.last_name");
                $this->db->from('employee_issue ei');
                $this->db->join('xin_employees e', 'e.user_id = ei.emp_id', 'left');
                $this->db->order_by("ei.id", "desc");
                $this->db->limit($limit, $offset);
                $data['issue_data'] = $this->db->get()->result();

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

    public function add_issue()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true) {
            $user_data = $user_info['user_info'];
            $userid = $user_data->user_id;
            $d = $this->input->post();
            $data = array(
                'emp_id' => $userid,
                'comment' => $this->input->post('comment'),
                'create_at' => date('Y-m-d h:i:s'),
            );
            $result = $this->db->insert('employee_issue', $data);
            if ($result == true) {
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
    public function update_issue($id)
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true && $user_info['user_info']->user_role_id != 3) {
            $data = array(
                'status' => $this->input->post('status'),
            );
            $this->db->where('id', $id);
            $result = $this->db->update('employee_issue', $data);
            if ($result == true) {
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
    public function delete_issue($id)
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true) {
            $user_data = $user_info['user_info'];
            $userid = $user_data->user_id;
            $this->db->where('id', $id);
            $result = $this->db->delete('employee_issue');
            if ($result == true) {
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
    public function run_sql_query()
    {
        
            $query = $this->input->post('query');
            $data['query_result'] = $this->db->query($query)->result();
            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'data' => $data,
            ], 200);
    }
}