<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/API_Controller.php';

class Lunch extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('api_helper');
        $this->load->model("Lunch_model");

    }
    /**
     * demo method
     *
     * @link [api/user/demo]
     * @method POST
     * @return Response|void
     */
    public function test()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true) {
            echo json_encode($user_info);
            exit();




        } else {
            echo json_encode($user_info);
            exit();
        }
    }
    public function index()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true) {
            $user_data = $user_info['user_info'];
            $lunch_data = $this->db->order_by('id', 'desc')->where('lunch_payment.emp_id', $user_data->user_id)->get('lunch_payment', 1)->result();

            // $lunch_data = $this->db
            //     ->select('lunch_payment.*')
            //     ->from('lunch_payment')
            //     ->where('lunch_payment.end_date', $result->end_date)
            //     ->where('lunch_payment.emp_id', $user_data->user_id)
            //     ->order_by('lunch_payment.id', 'desc')
            //     ->get()
            //     ->result();
            $data['Lunch_data_table'] = $this->db
                ->select('*')
                ->from('lunch_payment')
                ->where('lunch_payment.emp_id', $user_data->user_id)
                ->order_by('lunch_payment.id', 'desc')
                ->get()
                ->result();

            if (empty($lunch_data)) {
                $this->api_return([
                    'status' => false,
                    'message' => 'No lunch data found',
                    'data' => [],
                ], 200);
            } else {
                $data1 = $lunch_data[0];
                $first_date = date('Y-m-d', strtotime($data1->end_date . ' +1 day'));
                $second_date = date('Y-m-d', strtotime($data1->next_date . ' +1 day'));
                $data['first_date'] = $first_date;
                $data['second_date'] = $second_date;



                $current_month_lonch = $this->Lunch_model->get_data_date_wise($first_date, $second_date, $data1->emp_id);
                $taken_lunch = 0;
                foreach ($current_month_lonch['emp_data'] as $r) {
                    $taken_lunch += $r->meal_amount;

                }

                $total_lunch = $data1->probable_meal;
                $total_payment = $data1->pay_amount;
                $saved_lunch = $total_lunch - $taken_lunch;
                $data['total_lunch'] = $total_lunch;
                $data['total_payment'] = $total_payment;
                $data['saved_lunch'] = $saved_lunch;
                $data['taken_lunch'] = $taken_lunch;






                $this->api_return([
                    'status' => true,
                    'message' => 'successful',
                    'data' => $data,
                ], 200);
            }

        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
                'data' => [],
            ], 401);
        }
    }
    public function lunch_search()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $First_date = date('Y-m-d', strtotime($this->input->post('First_date')));
        $Last_date = date('Y-m-d', strtotime($this->input->post('Last_date')));
        $user_info = api_auth($authorization);
        $user_data = $user_info['user_info'];
        if ($user_info['status'] == true) {
            $data['Lunch_data_table'] = $this->db
                ->select('*')
                ->from('lunch_payment')
                ->where('lunch_payment.emp_id', $user_data->user_id)
                ->where('lunch_payment.end_date >=', $First_date)
                ->where('lunch_payment.end_date<=', $Last_date)
                ->order_by('lunch_payment.id', 'desc')
                ->get()
                ->result();
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
    }

    public function today_lunch_off_list()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        $user_data = $user_info['user_info'];
        if ($user_info['status'] == true) {
            $emp_id    = $user_data->user_id;
            $result = $this->db->where('emp_id', $emp_id)->order_by('id', 'desc')->get('lunch_today_off_request')->result();
            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'data' => $result,
            ], 200);
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unsuccessful',
                'data' => [],
            ], 404);
        }
    }

    public function today_lunch_offOn()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        $user_data = $user_info['user_info'];
        if ($user_info['status'] == true) {
            $emp_id    = $user_data->user_id;
            $date      = date('Y-m-d', strtotime($this->input->post('date')));
            $row = $this->db->where('date', $date)->get('lunch')->row();
            if (!empty($row)) {
                $this->api_return([
                    'status' => false,
                    'message' => 'Sorry, A vendor lunch request has already been made on this date.',
                    'data' => [],
                ], 200);
            } else {
                $lunch_status = $this->input->post('status');
                $extra_check = $this->input->post('extra');
                $extra_quantity = $this->input->post('extra_qty');
                $data = array(
                    'emp_id'     => $emp_id,
                    'off_day'    => $date,
                    'status'     => $lunch_status, // 1 lunch off and 2 lunch on
                    'extra'      => $lunch_status == 1 ? 0 : $extra_check, // 1 extra lunch on and 0 for normal
                    'extra_qty'  => $lunch_status == 1 ? 0 : $extra_quantity,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                );
                $check = $this->db->where('emp_id', $emp_id)->where('off_day', $date)->get('lunch_today_off_request')->row();
                if (empty($check)) {
                    $this->db->insert('lunch_today_off_request', $data);
                } else {
                    $data = array(
                        'status'     => $lunch_status, // 1 lunch off and 2 lunch on
                        'extra'      => $lunch_status == 1 ? 0 : $extra_check, // 1 extra lunch on and 0 for normal
                        'extra_qty'  => $lunch_status == 1 ? 0 : $extra_quantity,
                        'updated_at' => date('Y-m-d H:i:s'),
                    );
                    $this->db->where('emp_id', $emp_id)->where('off_day', $date)->update('lunch_today_off_request', $data);
                }
                $this->api_return([
                    'status' => true,
                    'message' => 'Successfully applied for lunch off',
                    'data' => [],
                ], 200);
            }
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unsuccessful',
                'data' => [],
            ], 404);
        }
    }

    public function today_lunch_off_edit()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        $user_data = $user_info['user_info'];
        if ($user_info['status'] == true) {
            $id        = $this->input->post('id');
            $date      = date('Y-m-d', strtotime($this->input->post('date')));
            $row = $this->db->where('date', $date)->get('lunch')->row();
            if (!empty($row)) {
                $this->api_return([
                    'status' => false,
                    'message' => 'Lunch is off or already lunch applied on this date',
                    'data' => [],
                ], 200);
            } else {
                $data = array(
                    'off_day' => $date,
                    'updated_at' => date('Y-m-d H:i:s'),
                );
                $this->db->where('id', $id)->update('lunch_today_off_request', $data);
                $this->api_return([
                    'status' => true,
                    'message' => 'Successfully applied for lunch off',
                    'data' => [],
                ], 200);
            }
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unsuccessful',
                'data' => [],
            ], 404);
        }
    }

    public function today_lunch_off_delete()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        $user_data = $user_info['user_info'];
        if ($user_info['status'] == true) {
            $id  = $this->input->post('id');
            if (empty($id)) {
                $this->api_return([
                    'status' => false,
                    'message' => 'Trying to get property of non-object',
                    'data' => [],
                ], 200);
                return;
            }
            $get = $this->db->where('id', $id)->get('lunch_today_off_request')->row();
            if (empty($get)) {
                $this->api_return([
                    'status' => false,
                    'message' => 'Trying to get property of non-object',
                    'data' => [],
                ], 200);
                return;
            }
            $row = $this->db->where('date', $get->off_day)->get('lunch')->row();
            if (!empty($row)) {
                $this->api_return([
                    'status' => false,
                    'message' => 'Sorry, A vendor lunch request has already been made on this date.',
                    'data' => [],
                ], 200);
            } else {
                $this->db->where('id', $id)->delete('lunch_today_off_request');
                $this->api_return([
                    'status' => true,
                    'message' => 'Successfully applied for lunch off deleted',
                    'data' => [],
                ], 200);
            }
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unsuccessful',
                'data' => [],
            ], 404);
        }
    }
}
