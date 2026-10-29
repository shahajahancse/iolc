<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/API_Controller.php';

class New_movement_admin extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('api_helper');
        $this->load->model("New_movement_model");
        $this->load->model("New_movement_travel_model");
        $this->load->model("New_movement_meeting_model");
        $this->load->model('Xin_model');
        $this->load->model("Employees_model");
    }

    // GET: Admin Dashboard (Stats + Active Movements)
    public function dashboard()
    {
        $this->_APIConfig(['methods' => ['GET']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        // Allow Admin (1), HR (4), or potentially Managers (2)
        $role_id = $user_info['user_info']->user_role_id;
        if ($user_info['status'] == true && in_array($role_id, [1, 2, 4])) {

            $start_date = $this->input->post('start_date') ? date('Y-m-d', strtotime($this->input->post('start_date'))) : date('Y-m-d');
            $end_date = $this->input->post('end_date') ? date('Y-m-d', strtotime($this->input->post('end_date'))) : date('Y-m-d');

            // 1. Global Stats
            $stats = $this->New_movement_model->get_admin_dashboard_stats($start_date, $end_date);

            // 2. Active Movements List (Live Tracking)
            $active_movements = $this->New_movement_model->get_all_active_movements();

           // Enrich active movements with current status detail
            foreach ($active_movements as &$m) {
                // Get detailed status (Traveling/Meeting/Idle) using existing logic
                $m->current_status = $this->New_movement_model->get_detailed_status($m->id);
            }

            $this->api_return([
                'status' => true,
                'data' => [
                    'stats' => $stats,
                    'active_movements' => $active_movements
                ]
            ], 200);

        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized User'], 401);
        }
    }

    public function ta_list()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true ) {
            $data = $this->New_movement_model->get_ta_list();
            $this->api_return([
                'status' => true,
                'message' => 'Success',
                'data' => [
                    'Results' => $data
                ]
            ], 200);

        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized User'], 401);
        }
    }

    public function ta_summary()
    {
        // $this->_APIConfig(['methods' => ['POST']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true ) {
            $start_date = $this->input->post('start_date') ? date('Y-m-d 23:59:59', strtotime($this->input->post('start_date'))) : date('Y-m-d 23:59:59');
            $end_date = $this->input->post('end_date')? date('Y-m-d 00:00:00', strtotime($this->input->post('end_date')))
            : date('Y-m-d 00:00:00', strtotime('-7 days'));

            $ta_status = $this->input->post('ta_status') ? $this->input->post('ta_status') : null;
            $employee_id = $this->input->post('employee_id') ? $this->input->post('employee_id') : null;

            // Admins (1, 2, 4): Show All movements, No Active Flow Redirect
            $data['movements'] = $this->New_movement_model->get_ta_summary($start_date, $end_date, $ta_status, $employee_id);

            $this->api_return([
                'status' => true,
                'message' => 'Success',
                'data' => [
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'ta_status' => $ta_status,
                    'employee_id' => $employee_id,
                    'Results' => $data
                ]
            ], 200);
        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized User'], 401);
        }
    }

    public function ta_summary_details()
    {
        // $this->_APIConfig(['methods' => ['POST']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true ) {
            $start_date = $this->input->post('start_date');
            $end_date = $this->input->post('end_date');
            $ta_status = $this->input->post('ta_status');
            $employee_id = $this->input->post('employee_id');

            // Admins (1, 2, 4): Show All movements, No Active Flow Redirect
            $data['employee'] = $this->Xin_model->read_user_info($employee_id);
            $data['movements'] = $this->New_movement_model->get_ta_summary_details_by_user($start_date, $end_date, $ta_status, $employee_id);

            $this->api_return([
                'status' => true,
                'message' => 'Success',
                'data' => [
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'ta_status' => $ta_status,
                    'employee_id' => $employee_id,
                    'Results' => $data
                ]
            ], 200);
        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized User'], 401);
        }
    }

    public function get_travel_with_expense()
    {
        // $this->_APIConfig(['methods' => ['POST']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true ) {
            $movement_id = $this->input->post('movement_id');

            // Admins (1, 2, 4): Show All movements, No Active Flow Redirect
            $data['expenses'] = $this->New_movement_model->get_travel_with_expense($movement_id);

            $this->api_return([
                'status' => true,
                'message' => 'Success',
                'data' => [
                    'movement_id' => $movement_id,
                    'Results' => $data
                ]
            ], 200);
        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized User'], 401);
        }
    }

    public function ta_summary_update()
    {
        // $this->_APIConfig(['methods' => ['POST']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {

            $start_date = $this->input->post('start_date');
            $end_date = $this->input->post('end_date');
            $ta_status = $this->input->post('ta_status');
            $employee_id = $this->input->post('employee_id');
            if (is_string($employee_id)) {
                $employee_id = explode(',', $employee_id);
            }
            if (!is_array($employee_id)) {
                $employee_id = array($employee_id);
            }

            if (!empty($employee_id) && $start_date && $end_date && $ta_status) {

                $this->db->where_in('employee_id', $employee_id);
                $this->db->where('ta_status', 'approved');
                $this->db->where('end_time <=', $start_date);
                $this->db->where('end_time >=', $end_date);

                $this->db->update(
                    'new_movement_movements',
                    array('ta_status' => $ta_status)
                );
            }

            return $this->api_return(
                array('status' => true, 'message' => 'Success'),
                200
            );

        } else {
            return $this->api_return(
                array('status' => false, 'message' => 'Unauthorized User'),
                401
            );
        }

    }

    // GET: Consolidated Report
    public function report()
    {
        $this->_APIConfig(['methods' => ['GET']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $role_id = $user_info['user_info']->user_role_id;
            if (!in_array($role_id, [1, 2, 4])) {
                $this->api_return(['status' => false, 'message' => 'Unauthorized Access'], 403);
                return;
            }

            $start_date = $this->input->get('start_date');
            $end_date = $this->input->get('end_date');
            $employee_ids = $this->input->get('employee_ids'); // can be array or string

            // Handle optional filter
            $emp_id_list = [];
            if ($employee_ids) {
                if (is_array($employee_ids)) {
                    $emp_id_list = $employee_ids;
                } else if (is_string($employee_ids)) {
                    // Check if comma separated
                    if (strpos($employee_ids, ',') !== false) {
                        $emp_id_list = explode(',', $employee_ids);
                    } else {
                        $emp_id_list = [$employee_ids];
                    }
                }
            } else {
                // Compatibility for old user_id param
                $user_id = $this->input->get('user_id');
                if ($user_id) {
                    $emp_id_list = [$user_id];
                }
            }

            if (!$start_date || !$end_date) {
                $this->api_return(['status' => false, 'message' => 'Start Date and End Date are required'], 400);
                return;
            }

            // Use V2 which supports array filter and extra stats
            $report_data = $this->New_movement_model->get_report_data_v2($start_date, $end_date, $emp_id_list);

            $this->api_return(['status' => true, 'data' => $report_data], 200);

        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized'], 401);
        }
    }

    // GET: Movement Details (Drill down)
    public function details($id)
    {
        $this->_APIConfig(['methods' => ['GET']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $role_id = $user_info['user_info']->user_role_id;
            if (!in_array($role_id, [1, 2, 4])) {
                $this->api_return(['status' => false, 'message' => 'Unauthorized Access'], 403);
                return;
            }

            $movement = $this->New_movement_model->get_movement_by_id($id);
            if (!$movement) {
                $this->api_return(['status' => false, 'message' => 'Movement Not Found'], 404);
                return;
            }

            $travels = $this->New_movement_travel_model->get_travels_by_movement($id);
            $meetings = $this->New_movement_meeting_model->get_meetings_by_movement($id);
            $expenses = $this->New_movement_model->get_expenses_by_movement($id);

            $this->api_return([
                'status' => true,
                'data' => [
                    'movement' => $movement,
                    'travels' => $travels,
                    'meetings' => $meetings,
                    'expenses' => $expenses
                ]
            ], 200);

        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized'], 401);
        }
    }

    // POST: Update TA Amount (Edit Expenses)
    // Allows admin to override/edit expenses for a movement
    public function update_ta()
    {
        $this->_APIConfig(array('methods' => array('POST')));

        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if (empty($user_info) || $user_info['status'] != true) {
            return $this->api_return(array('status' => false, 'message' => 'Unauthorized'), 401);
        }

        // Read RAW JSON input
        $input = json_decode($this->input->raw_input_stream, true);

        if (!is_array($input)) {
            return $this->api_return(array('status' => false, 'message' => 'Invalid JSON payload'), 400);
        }

        $movement_id = isset($input['movement_id']) ? (int)$input['movement_id'] : 0;
        $status = $input['status'];
        $expenses    = isset($input['expenses']) ? $input['expenses'] : array();

        // Validation
        if ($movement_id <= 0) {
            return $this->api_return(array('status' => false, 'message' => 'Movement ID Required'), 400);
        }

        if (!is_array($expenses) || count($expenses) == 0) {
            return $this->api_return(array('status' => false, 'message' => 'Expenses Required'), 400);
        }

        $this->db->trans_begin();

        $modified_amt = 0;

        foreach ($expenses as $i => $data) {

            // Payload key is "travel_id" but we treat it as expense row primary key (id)
            $expense_id = isset($data['expense_id']) ? (int)$data['expense_id'] : 0;

            if ($expense_id <= 0) {
                $this->db->trans_rollback();
                return $this->api_return(array(
                    'status' => false,
                    'message' => 'Invalid expense_id at index ' . $i
                ), 400);
            }

            // ✅ 1) Check row exists first (prevents false 404 on second update)
            $exists = $this->db->select('id')
                ->from('new_movement_travel_expenses')
                ->where('id', $expense_id)   // change this if your PK isn't "id"
                ->limit(1)
                ->get()
                ->num_rows();

            if ($exists == 0) {
                $this->db->trans_rollback();
                return $this->api_return(array(
                    'status' => false,
                    'message' => 'No expense row found for id: ' . $expense_id
                ), 404);
            }

            $transport_type = isset($data['transport_type']) ? $data['transport_type'] : null;
            $amount         = isset($data['amount']) ? $data['amount'] : 0;
            $ta_app_amt     = isset($data['ta_app_amt']) ? $data['ta_app_amt'] : 0;
            $note           = isset($data['note']) ? $data['note'] : '';

            $update_data = array(
                'transport_type' => $transport_type,
                'amount'         => $amount,
                'approve_amount' => $ta_app_amt,
                'note'           => $note,
            );

            // ✅ 2) Update row
            $this->db->where('id', $expense_id); // change if needed
            $this->db->update('new_movement_travel_expenses', $update_data);

            // ✅ 3) If any DB error happens, rollback
            $dbErr = $this->db->error();
            if (!empty($dbErr['message'])) {
                $this->db->trans_rollback();
                return $this->api_return(array(
                    'status' => false,
                    'message' => 'DB Error: ' . $dbErr['message']
                ), 500);
            }

            // NOTE: we DO NOT use affected_rows() to decide "not found"
            // because second time same data => affected_rows() can be 0.
            $modified_amt += (float)$ta_app_amt;
        }

        // Update movement total
        $move_array = array(
            'ta_app_amt' => $modified_amt,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $user_info['user_info']->user_id
        );
        if (!empty($status)) {
            $move_array['ta_status'] = $status;
        }
        $this->db->where('id', $movement_id);
        $this->db->update('new_movement_movements', $move_array);

        $dbErr2 = $this->db->error();
        if (!empty($dbErr2['message'])) {
            $this->db->trans_rollback();
            return $this->api_return(array(
                'status' => false,
                'message' => 'DB Error: ' . $dbErr2['message']
            ), 500);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return $this->api_return(array('status' => false, 'message' => 'Transaction failed'), 500);
        }

        $this->db->trans_commit();

        return $this->api_return(array(
            'status' => true,
            'message' => 'Updated Successfully',
            'ta_app_amt' => $modified_amt
        ), 200);
    }

    public function update_tassss()
    {
        $this->_APIConfig(['methods' => ['POST']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $role_id = $user_info['user_info']->user_role_id;
            // Only Admin(1) and HR(2) can probably edit money
            // if (!in_array($role_id, [1, 2, 4])) {
            //     $this->api_return(['status' => false, 'message' => 'Unauthorized Access'], 403);
            //     return;
            // }

            $movement_id = $this->input->post('movement_id');
            $expenses = $this->input->post('expenses'); // JSON array or array structure

            if (!$movement_id) {
                $this->api_return(['status' => false, 'message' => 'Movement ID Required'], 400);
                return;
            }

            // Note: If sending JSON in post body, might need json_decode if it comes as string
            // Assuming form-data array or json body handled by client
            if (is_string($expenses)) {
                $expenses = json_decode($expenses, true);
            }

            if (!is_array($expenses)) {
                $this->api_return(['status' => false, 'message' => 'Invalid Expenses Format'], 400);
                return;
            }

            $user_id = $user_info['user_info']->user_id;
            if (!empty($expenses)) {
                $modifyed_amt = 0;
                foreach ($expenses as $data) {
                    $update_data = [
                        'transport_type' => $data['transport_type'],
                        'amount' => $data['amount'],
                        'approve_amount' => $data['ta_app_amt'],
                        'note' => $data['note']
                    ];
                    $this->db->where('id', $data['travel_id']);
                    $this->db->update('new_movement_travel_expenses', $update_data);
                    $modifyed_amt = $modifyed_amt + $data['ta_app_amt'];
                }
                $array = array(
                    'ta_app_amt' => $modifyed_amt,
                    'ta_admin_note' => $this->input->post('ta_admin_note'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $user_id
                );
                $this->db->where('id', $movement_id);
                $this->db->update('new_movement_movements', $array);
            }

            $this->api_return(['status' => true, 'message' => 'TA Expenses Updated'], 200);

        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized'], 401);
        }
    }

    // POST: Approve/Reject TA Status
    public function approve_ta()
    {
        $this->_APIConfig(['methods' => ['POST']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $role_id = $user_info['user_info']->user_role_id;
            $movement_id = $this->input->post('movement_id');
            $status = $this->input->post('status'); // approved, rejected, hr_approved, handed_over

            if (!$movement_id || !$status) {
                $this->api_return(['status' => false, 'message' => 'Missing ID or Status'], 400);
                return;
            }

            // Permission Logic (Mirroring Web Controller)
            $allowed = false;

            // Managers (Role 4) can approve to 'hr_approved' or 'handed_over' (Partial approval)
            // Assuming logic from Web `update_ta_status`:
            // if ($role == 4 && in_array($status, ['hr_approved', 'handed_over'])) allowed
            // if (($role == 1 || $role == 2) && $status == 'approved') allowed

            if ($role_id == 4 && in_array($status, ['hr_approved', 'handed_over', 'rejected'])) {
                $allowed = true;
            } elseif (in_array($role_id, [1, 2])) {
                // Admin/HR can do anything basically, including final approval
                $allowed = true; // Admin Power
            }

            if ($allowed) {
                $this->db->where('id', $movement_id);
                $this->db->update('new_movement_movements', ['ta_status' => $status]);
                $this->api_return(['status' => true, 'message' => 'Status Updated to ' . $status], 200);
            } else {
                $this->api_return(['status' => false, 'message' => 'Permission Denied for this Status Change'], 403);
            }

        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized'], 401);
        }
    }
}
