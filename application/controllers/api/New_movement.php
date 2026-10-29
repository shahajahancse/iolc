<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/API_Controller.php';

class New_movement extends API_Controller
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

    // GET: Dashboard Data (Current Status & History)
    public function dashboard()
    {
        $this->_APIConfig(['methods' => ['GET']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $user_id = $user_info['user_info']->user_id;

            // Check Active Status
            $active_movement = $this->New_movement_model->get_active_movement($user_id);
            $current_state = 'idle';
            $active_data = null;

            if ($active_movement) {
                // Check if traveling
                $active_travel = $this->New_movement_travel_model->get_active_travel($active_movement->id);
                if ($active_travel) {
                    $current_state = 'traveling';
                    $active_data = $active_travel;
                    if (isset($active_data->is_office_return)) {
                        $active_data->is_office_return = ($active_data->to_location == 'Office') ? 1 : 0;
                    }
                    $active_data->is_home_return = ($active_data->to_location == 'Home') ? 1 : 0;
                } else {
                    $active_meeting = $this->New_movement_meeting_model->get_active_meeting($active_movement->id);
                    if ($active_meeting) {
                        $current_state = 'meeting';
                        $active_data = $active_meeting;
                    } else {
                        // Check pending feedback
                        $pending_feedback = $this->New_movement_meeting_model->get_pending_feedback_meeting($active_movement->id);
                        if ($pending_feedback) {
                            $current_state = 'feedback_pending';
                            $active_data = $pending_feedback;
                        } else {
                            // In decision phase
                            $current_state = 'decision';
                        }
                    }
                }
            }

            // History
            $start_date = $this->input->get('start_date');
            $end_date = $this->input->get('end_date');

            $history = $this->New_movement_model->get_all_movements($user_id, $start_date, $end_date);
            $stats = $this->New_movement_model->get_ta_stats($user_id, $start_date, $end_date);

            $this->api_return([
                'status' => true,
                'message' => 'Dashboard Data',
                'data' => [
                    'current_state' => $current_state,
                    'active_movement' => $active_movement,
                    'active_data' => $active_data,
                    'stats' => $stats,
                    'history' => $history
                ]
            ], 200);

        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized User'], 401);
        }
    }

    // POST: Start Movement
    public function start()
    {
        $this->_APIConfig(['methods' => ['POST']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $user_id = $user_info['user_info']->user_id;

            // Validation
            if (!$this->input->post('start_location')) {
                $this->api_return(['status' => false, 'message' => 'Start Location is required'], 400);
                return;
            }

            // Handle Photo Upload (Base64)
            $photo_path = null;
            if ($this->input->post('start_photo')) {
                $base64String = $this->input->post('start_photo');
                // Extract file type from base64 string
                preg_match('/^data:image\/(.*);base64,/', $base64String, $output_array);

                if (isset($output_array[1])) {
                    $fileExtension = $output_array[1];
                    // Remove header
                    $base64String = preg_replace('/^data:image\/(.*);base64,/', '', $base64String);
                    $base64String = str_replace(' ', '+', $base64String);
                    $imageData = base64_decode($base64String);

                    $filename = 'start_' . $user_id . '_' . time() . '.' . $fileExtension;
                    if (!is_dir('./uploads/movement_photos/'))
                        mkdir('./uploads/movement_photos/', 0777, true);

                    $fileLocation = './uploads/movement_photos/' . $filename;
                    file_put_contents($fileLocation, $imageData);

                    // Database stores 'uploads/movement_photos/filename.jpg'
                    $photo_path = 'uploads/movement_photos/' . $filename;
                }
            }

            // Create Movement
            $data = array(
                'employee_id' => $user_id,
                'start_location' => $this->input->post('start_location'),
                'start_latitude' => $this->input->post('latitude'),
                'start_longitude' => $this->input->post('longitude'),
                'purpose' => $this->input->post('purpose'),
                'type' => $this->input->post('type'),
                'start_time' => date('Y-m-d H:i:s'),
                'start_photo' => $photo_path,
                'status' => 'active'
            );
            $movement_id = $this->New_movement_model->create_movement($data);

            // Start First Travel
            $travel_data = array(
                'movement_id' => $movement_id,
                'from_location' => $this->input->post('start_location'),
                'start_lat' => $this->input->post('latitude'),
                'start_lng' => $this->input->post('longitude'),
                'start_time' => date('Y-m-d H:i:s'),
                'status' => 'running'
            );
            $this->New_movement_travel_model->start_travel($travel_data);

            $this->api_return(['status' => true, 'message' => 'Movement Started', 'movement_id' => $movement_id], 200);
        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized User'], 401);
        }
    }

    // POST: Reached Destination
    public function reached_destination()
    {
        $this->_APIConfig(['methods' => ['POST']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $user_id = $user_info['user_info']->user_id;
            $movement = $this->New_movement_model->get_active_movement($user_id);

            if (!$movement) {
                $this->api_return(['status' => false, 'message' => 'No active movement'], 404);
                return;
            }

            $travel = $this->New_movement_travel_model->get_active_travel($movement->id);
            if ($travel) {
                // Calculate Distance
                $end_lat = $this->input->post('latitude');
                $end_lng = $this->input->post('longitude');
                $start_lat = $travel->start_lat;
                $start_lng = $travel->start_lng;
                $dist_km = 0;

                if ($start_lat && $start_lng && $end_lat && $end_lng) {
                    $theta = $start_lng - $end_lng;
                    $dist = sin(deg2rad($start_lat)) * sin(deg2rad($end_lat)) + cos(deg2rad($start_lat)) * cos(deg2rad($end_lat)) * cos(deg2rad($theta));

                    // Clamp $dist to [-1, 1] to avoid NaN from acos
                    if ($dist > 1)
                        $dist = 1;
                    if ($dist < -1)
                        $dist = -1;

                    $dist = acos($dist);
                    $dist = rad2deg($dist);
                    $miles = $dist * 60 * 1.1515;
                    $dist_km = $miles * 1.609344;

                    if (is_nan($dist_km))
                        $dist_km = 0;
                }

                $update_data = array(
                    'end_time' => date('Y-m-d H:i:s'),
                    'status' => 'completed',
                    'to_location' => $this->input->post('current_location'),
                    'end_lat' => $end_lat,
                    'end_lng' => $end_lng,
                    'distance_km' => round($dist_km, 2)
                );
                $this->New_movement_travel_model->end_travel($travel->id, $update_data);
            }

            // Check if returning to office or home
            if ($this->input->post('is_office_return') == 1 || $this->input->post('is_home_return') == 1 || $this->input->post('to_location') == 'Home') {
                $this->New_movement_model->end_movement($movement->id, date('Y-m-d H:i:s'));
                $this->api_return(['status' => true, 'message' => 'Movement Completed'], 200);
            } else {
                $this->api_return(['status' => true, 'message' => 'Reached Destination'], 200);
            }
        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized User'], 401);
        }
    }

    // POST: Start Meeting
    public function start_meeting()
    {
        $this->_APIConfig(['methods' => ['POST']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $user_id = $user_info['user_info']->user_id;
            $movement = $this->New_movement_model->get_active_movement($user_id);

            if (!$movement) {
                $this->api_return(['status' => false, 'message' => 'No active movement'], 404);
                return;
            }

            $data = array(
                'movement_id' => $movement->id,
                'entity_type' => $this->input->post('entity_type'),
                'client_name' => $this->input->post('client_name'),
                'contact_person' => $this->input->post('contact_person'),
                'contact_email' => $this->input->post('contact_email'),
                'contact_phone' => $this->input->post('contact_phone'),
                'contact_job_title' => $this->input->post('contact_job_title'),
                'meeting_type' => $this->input->post('meeting_type'),
                'location' => $this->input->post('location'),
                'latitude' => $this->input->post('latitude'),
                'longitude' => $this->input->post('longitude'),
                'remarks' => $this->input->post('remarks'),
                'start_time' => date('Y-m-d H:i:s')
            );

            // Handle CRM Lead ID
            $crm_lead_id = $this->input->post('crm_lead_id');
            if ($crm_lead_id) {
                $data['crm_lead_id'] = $crm_lead_id;
            }

            $meeting_id = $this->New_movement_meeting_model->start_meeting($data);

            // Check for Sales Team and trigger Leads API
            $full_user_details = $this->Employees_model->read_employee_information($user_id);
            // Check meeting source (default to 'new' if not set for backward compatibility)
            $meeting_source = $this->input->post('meeting_source');
            if (!$meeting_source)
                $meeting_source = 'new';

            if (!empty($full_user_details) && isset($full_user_details[0]->team_type) && $full_user_details[0]->team_type == 'sales') {

                // Only create new lead if source is 'new'
                if ($meeting_source == 'new') {
                    $leads_url = 'http://demo-crm.mysoftheaven.com/index.php/leads_api/save';
                    $leads_data = [
                        'owner_email' => $full_user_details[0]->email,
                        'entity_type' => $this->input->post('entity_type'),
                        'client_name' => $this->input->post('client_name'),
                        'contact_person' => $this->input->post('contact_person'),
                        'contact_email' => $this->input->post('contact_email'),
                        'contact_phone' => $this->input->post('contact_phone'),
                        'contact_job_title' => $this->input->post('contact_job_title'),
                        'remarks' => $this->input->post('remarks'),
                        'feedback' => '' // Initial start has no feedback
                    ];

                    $ch = curl_init($leads_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $leads_data);
                    // Execute implicitly
                    curl_exec($ch);
                    curl_close($ch);
                }
            }

            $this->api_return(['status' => true, 'message' => 'Meeting Started', 'meeting_id' => $meeting_id], 200);
        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized User'], 401);
        }
    }

    // POST: End Meeting
    public function end_meeting()
    {
        $this->_APIConfig(['methods' => ['POST']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $user_id = $user_info['user_info']->user_id;

            // Check for direct IDs from Input
            $post_meeting_id = $this->input->post('meeting_id');
            $post_move_id = $this->input->post('move_id');

            if ($post_meeting_id) {
                // Use provided ID
                $meeting_id = $post_meeting_id;
                // Ideally should verify ownership here but relying on user for now
                $movement_id = $post_move_id ? $post_move_id : 0;

                // End the meeting directly by ID
                $this->New_movement_meeting_model->end_meeting($meeting_id, date('Y-m-d H:i:s'));

                // If feedback is provided immediately
                if ($this->input->post('feedback')) {
                    $feedback = $this->input->post('feedback');
                    $this->New_movement_meeting_model->update_meeting_feedback($meeting_id, $feedback);

                    // Update CRM lead status if linked (need to fetch meeting to check)
                    // Use direct query as model method might be missing
                    $meeting = $this->db->get_where('new_movement_meetings', ['id' => $meeting_id])->row();

                    if ($meeting && !empty($meeting->crm_lead_id) && $this->input->post('crm_status')) {
                        $full_user_details = $this->Employees_model->read_employee_information($user_id);
                        if (!empty($full_user_details)) {
                            $leads_url = 'http://demo-crm.mysoftheaven.com/index.php/leads_api/update_lead_status';
                            $leads_data = [
                                'owner_email' => $full_user_details[0]->email,
                                'lead_id' => $meeting->crm_lead_id,
                                'status' => $this->input->post('crm_status'),
                                'feedback' => $feedback
                            ];

                            $ch = curl_init($leads_url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $leads_data);
                            curl_exec($ch);
                            curl_close($ch);
                        }
                    }
                }

                $this->api_return([
                    'status' => true,
                    'message' => 'Meeting Ended',
                    'meeting_id' => $meeting_id,
                    'move_id' => $movement_id
                ], 200);

            } else {
                // Fallback to Active State Logic
                $movement = $this->New_movement_model->get_active_movement($user_id);
                $meeting = $this->New_movement_meeting_model->get_active_meeting($movement->id);

                if ($meeting) {
                    $this->New_movement_meeting_model->end_meeting($meeting->id, date('Y-m-d H:i:s'));

                    if ($this->input->post('feedback')) {
                        $feedback = $this->input->post('feedback');
                        $this->New_movement_meeting_model->update_meeting_feedback($meeting->id, $feedback);

                        // Update CRM if linked
                        if (!empty($meeting->crm_lead_id) && $this->input->post('crm_status')) {
                            $full_user_details = $this->Employees_model->read_employee_information($user_id);
                            if (!empty($full_user_details)) {
                                $leads_url = 'http://demo-crm.mysoftheaven.com/index.php/leads_api/update_lead_status';
                                $leads_data = [
                                    'owner_email' => $full_user_details[0]->email,
                                    'lead_id' => $meeting->crm_lead_id,
                                    'status' => $this->input->post('crm_status'),
                                    'feedback' => $feedback
                                ];

                                $ch = curl_init($leads_url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_POST, true);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $leads_data);
                                curl_exec($ch);
                                curl_close($ch);
                            }
                        }
                    }

                    $this->api_return(['status' => true, 'message' => 'Meeting Ended'], 200);
                } else {
                    $this->api_return(['status' => false, 'message' => 'No active meeting'], 404);
                }
            }
        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized'], 401);
        }
    }

    // POST: Handle Decision (Next Travel)
    public function decision()
    {
        $this->_APIConfig(['methods' => ['POST']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $user_id = $user_info['user_info']->user_id;
            $movement = $this->New_movement_model->get_active_movement($user_id);
            $choice = $this->input->post('choice'); // 'office' or 'travel'

            $travel_data = array(
                'movement_id' => $movement->id,
                'from_location' => $this->input->post('current_location'),
                'start_lat' => $this->input->post('latitude'),
                'start_lng' => $this->input->post('longitude'),
                'start_time' => date('Y-m-d H:i:s'),
                'status' => 'running'
            );

            if ($choice == 'office') {
                $travel_data['to_location'] = 'Office';
                // Note: The app should handle the flag locally or send a separate end request
            } elseif ($choice == 'home') {
                $travel_data['to_location'] = 'Home';
            }

            $this->New_movement_travel_model->start_travel($travel_data);
            $this->api_return(['status' => true, 'message' => 'Next Travel Started'], 200);
        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized'], 401);
        }
    }

    // GET: Details including TA
    public function details()
    {
        $this->_APIConfig(['methods' => ['GET']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $movement_id = $this->input->get('movement_id');
            if (!$movement_id) {
                $this->api_return(['status' => false, 'message' => 'Movement ID required'], 400);
                return;
            }

            $movement = $this->New_movement_model->get_movement_by_id($movement_id);
            $travels = $this->New_movement_travel_model->get_travels_by_movement($movement_id);
            $meetings = $this->New_movement_meeting_model->get_meetings_by_movement($movement_id);
            $expenses = $this->New_movement_model->get_expenses_by_movement($movement_id);

            // Enrich with Address Names (Reverse Geocoding)
            if ($movement && isset($movement->start_latitude) && isset($movement->start_longitude)) {
                $movement->start_address_name = $this->_get_address($movement->start_latitude, $movement->start_longitude);
            }

            if ($travels) {
                foreach ($travels as $travel) {
                    if (isset($travel->start_lat) && isset($travel->start_lng)) {
                        $travel->start_address_name = $this->_get_address($travel->start_lat, $travel->start_lng);
                    }
                    if (isset($travel->end_lat) && isset($travel->end_lng)) {
                        $travel->end_address_name = $this->_get_address($travel->end_lat, $travel->end_lng);
                    }
                }
            }

            if ($meetings) {
                foreach ($meetings as $meeting) {
                    if (isset($meeting->latitude) && isset($meeting->longitude)) {
                        $meeting->address_name = $this->_get_address($meeting->latitude, $meeting->longitude);
                    }
                }
            }

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

    // POST: Apply TA
    public function apply_ta()
    {
        $this->_APIConfig(['methods' => ['POST']]);

        try {

            // 🔐 Authorization
            $authorization = $this->input->get_request_header('Authorization');
            $user_info = api_auth($authorization);

            if (!$user_info['status']) {
                return $this->api_return([
                    'status'  => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $user_id = $user_info['user_info']->user_id;

            // 📥 Read RAW JSON input
            $input = json_decode($this->input->raw_input_stream, true);

            $movement_id = $input['movement_id'] ? $input['movement_id'] : null;
            $expenses    = $input['expenses'] ? $input['expenses'] : [];

            // ✅ Validation
            if (empty($movement_id)) {
                return $this->api_return([
                    'status'  => false,
                    'message' => 'Movement ID Required'
                ], 400);
            }

            if (!is_array($expenses) || empty($expenses)) {
                return $this->api_return([
                    'status'  => false,
                    'message' => 'Expenses Required'
                ], 400);
            }

            // 🔄 Start Transaction
            $this->db->trans_begin();

            // 🗑️ Delete old expenses
            $this->db->where('movement_id', $movement_id)
                    ->delete('new_movement_travel_expenses');

            $total_amount = 0;

            // 📌 Insert new expenses
            foreach ($expenses as $exp) {

                $exp_amount = isset($exp['amount']) ? (float)$exp['amount'] : 0;
                $total_amount += $exp_amount;

                $data = [
                    'movement_id'     => $movement_id,
                    'travel_id'       => $exp['travel_id'] ? $exp['travel_id'] : null,
                    'transport_type'  => $exp['type'] ? $exp['type'] : null,
                    'amount'          => $exp_amount,
                    'approve_amount'  => $exp_amount,
                    'note'            => $exp['note'] ? $exp['note'] : null,
                    'created_at'      => date('Y-m-d H:i:s'),
                    // 'created_by'      => $user_id
                ];

                $this->db->insert('new_movement_travel_expenses', $data);
            }

            // 📝 Update movement table
            $this->db->where('id', $movement_id)
                    ->update('new_movement_movements', [
                        'ta_status'   => 'pending',
                        'ta_amount'   => $total_amount,
                        'ta_app_amt'  => $total_amount,
                        'updated_at'  => date('Y-m-d H:i:s'),
                        'updated_by'  => $user_id
                    ]);

            // ❌ Transaction failed?
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Database transaction failed');
            }

            // ✅ Commit
            $this->db->trans_commit();

            return $this->api_return([
                'status'  => true,
                'message' => 'TA Applied Successfully',
                'amount'  => $total_amount
            ], 200);

        } catch (Exception $e) {

            // 🔙 Rollback on error
            $this->db->trans_rollback();

            return $this->api_return([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function apply_ta_sss()
    {
        $this->_APIConfig(['methods' => ['POST']]);

        try {
            $authorization = $this->input->get_request_header('Authorization');
            $user_info = api_auth($authorization);

            if (!$user_info['status']) {
                return $this->api_return(['status' => false, 'message' => 'Unauthorized'], 401);
            }

            $user_id     = $user_info['user_info']->user_id;
            $movement_id = $this->input->post('movement_id');
            $expenses    = $this->input->post('expenses');

            if (empty($movement_id)) {
                return $this->api_return(['status' => false, 'message' => 'Movement ID Required'], 400);
            }

            // JSON string হলে decode
            if (is_string($expenses)) {
                $expenses = json_decode($expenses, true);
            }

            if (!is_array($expenses) || empty($expenses)) {
                return $this->api_return(['status' => false, 'message' => 'Expenses Required'], 400);
            }

            // dd($expenses);

            $this->db->trans_begin();

            // Delete old expenses
            $this->db->where('movement_id', $movement_id)
                    ->delete('new_movement_travel_expenses');

            $amount = 0;

            foreach ($expenses as $exp) {

                $exp_amount = isset($exp['amount']) ? (float)$exp['amount'] : 0;
                $amount += $exp_amount;

                $data = [
                    'movement_id'     => $movement_id,
                    'travel_id'       => $exp['travel_id'] ? $exp['travel_id'] : null,
                    'transport_type'  => $exp['type'] ? $exp['type'] : null,
                    'amount'          => $exp_amount,
                    'approve_amount'  => $exp_amount,
                    'note'            => $exp['note'] ? $exp['note'] : null,
                    'created_at'      => date('Y-m-d H:i:s'),
                    'created_by'      => $user_id
                ];

                // ✅ INSERT HERE
                $this->db->insert('new_movement_travel_expenses', $data);
            }

            // Update movement
            $this->db->where('id', $movement_id)
                    ->update('new_movement_movements', [
                        'ta_status'   => 'pending',
                        'ta_amount'   => $amount,
                        'ta_app_amt'  => $amount,
                        'updated_at'  => date('Y-m-d H:i:s'),
                        'updated_by'  => $user_id
                    ]);

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Database transaction failed');
            }

            $this->db->trans_commit();

            return $this->api_return([
                'status'  => true,
                'message' => 'TA Applied Successfully'
            ], 200);

        } catch (Exception $e) {
            $this->db->trans_rollback();

            return $this->api_return([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }



    // POST: Submit Feedback (Standalone)
    public function submit_feedback()
    {
        $this->_APIConfig(['methods' => ['POST']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $user_id = $user_info['user_info']->user_id;
            $movement_id = $this->input->post('movement_id');
            $feedback = $this->input->post('feedback');

            $meeting = null;
            if ($this->input->post('meeting_id')) {
                $this->db->where('id', $this->input->post('meeting_id'));
                $meeting = $this->db->get('new_movement_meetings')->row();
            } else {
                if (!$movement_id) {
                    $active_movement = $this->New_movement_model->get_active_movement($user_id);
                    if ($active_movement)
                        $movement_id = $active_movement->id;
                }

                if ($movement_id) {
                    $meeting = $this->New_movement_meeting_model->get_last_meeting($movement_id);
                }
            }

            if ($meeting) {
                // Prepare update data
                $update_data = ['feedback' => $feedback];

                // Add optional client details updates if provided
                $optional_fields = ['client_name', 'contact_person', 'contact_email', 'contact_phone', 'contact_job_title'];
                foreach ($optional_fields as $field) {
                    if ($this->input->post($field)) {
                        $update_data[$field] = $this->input->post($field);
                    }
                }

                $this->New_movement_meeting_model->update_meeting_feedback($meeting->id, $update_data);

                // Update CRM lead status if linked
                if (!empty($meeting->crm_lead_id) && $this->input->post('crm_status')) {
                    $full_user_details = $this->Employees_model->read_employee_information($user_id);
                    if (!empty($full_user_details)) {
                        $leads_url = 'http://demo-crm.mysoftheaven.com/index.php/leads_api/update_lead_status';
                        $leads_data = [
                            'owner_email' => $full_user_details[0]->email,
                            'lead_id' => $meeting->crm_lead_id,
                            'status' => $this->input->post('crm_status'),
                            'feedback' => $feedback
                        ];

                        $ch = curl_init($leads_url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $leads_data);
                        curl_exec($ch);
                        curl_close($ch);
                    }
                }

                $this->api_return(['status' => true, 'message' => 'Feedback Submitted'], 200);
            } else {
                $this->api_return(['status' => false, 'message' => 'No meeting found'], 404);
            }

        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized'], 401);
        }
    }

    // POST: Log Visit (Client Busy / No Meeting)
    public function log_visit()
    {
        $this->_APIConfig(['methods' => ['POST']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $user_id = $user_info['user_info']->user_id;
            $movement = $this->New_movement_model->get_active_movement($user_id);

            if (!$movement) {
                $this->api_return(['status' => false, 'message' => 'No active movement'], 404);
                return;
            }

            $data = array(
                'movement_id' => $movement->id,
                'entity_type' => $this->input->post('entity_type'),
                'client_name' => $this->input->post('client_name'),
                'contact_person' => $this->input->post('contact_person'),
                'contact_email' => $this->input->post('contact_email'),
                'contact_phone' => $this->input->post('contact_phone'),
                'contact_job_title' => $this->input->post('contact_job_title'),
                'meeting_type' => 'visit_only', // Distinguish from normal meeting
                'location' => $this->input->post('location'),
                'latitude' => $this->input->post('latitude'),
                'longitude' => $this->input->post('longitude'),
                'remarks' => $this->input->post('remarks'),
                'start_time' => date('Y-m-d H:i:s'),
                'end_time' => date('Y-m-d H:i:s'), // Instant end
                'feedback' => 'Client Visit (Busy/No Meeting)'
            );

            // Handle CRM Lead ID
            $crm_lead_id = $this->input->post('crm_lead_id');
            if ($crm_lead_id) {
                $data['crm_lead_id'] = $crm_lead_id;
            }

            $meeting_id = $this->New_movement_meeting_model->start_meeting($data);

            $this->api_return(['status' => true, 'message' => 'Visit Logged Successfully', 'meeting_id' => $meeting_id], 200);
        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized User'], 401);
        }
    }

    // GET: Search Clients
    public function search_clients()
    {
        $this->_APIConfig(['methods' => ['GET']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $query = $this->input->get('query');
            if (empty($query)) {
                $this->api_return(['status' => true, 'data' => []], 200);
                return;
            }

            $results = $this->New_movement_meeting_model->search_clients($query);
            $this->api_return(['status' => true, 'data' => $results], 200);

        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized'], 401);
        }
    }

    // POST: Update TA Status (Admin/HR)
    public function update_ta_status()
    {
        $this->_APIConfig(['methods' => ['POST']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $role_id = $user_info['user_info']->user_role_id;
            $movement_id = $this->input->post('movement_id');
            $status = $this->input->post('status'); // approved, hr_approved, handed_over

            $allowed = false;
            if ($role_id == 4 && in_array($status, ['hr_approved', 'handed_over'])) {
                $allowed = true;
            } elseif (($role_id == 1 || $role_id == 2) && $status == 'approved') {
                $allowed = true;
            }

            if ($allowed) {
                $this->db->where('id', $movement_id);
                $this->db->update('new_movement_movements', ['ta_status' => $status]);
                $this->api_return(['status' => true, 'message' => 'Status updated to ' . $status], 200);
            } else {
                $this->api_return(['status' => false, 'message' => 'Permission Denied'], 403);
            }
        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized'], 401);
        }
    }
    // GET: Admin Live Tracking
    public function live_tracking()
    {
        $this->_APIConfig(['methods' => ['GET']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $role_id = $user_info['user_info']->user_role_id;
            // Allow Admin (1), HR (2), maybe Manager (4)?
            if ($role_id == 1 || $role_id == 2 || $role_id == 4) {
                $data = $this->New_movement_model->get_all_active_movements();
                $this->api_return(['status' => true, 'data' => $data], 200);
            } else {
                $this->api_return(['status' => false, 'message' => 'Available for Admins/Managers only'], 403);
            }
        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized'], 401);
        }
    }

    // Private method for reverse geocoding
    private function _get_address($lat, $lng)
    {

        return null;
    }

    // POST: Get CRM Schedules (Proxy)
    public function get_crm_schedules()
    {
        $this->_APIConfig(['methods' => ['POST']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $user_id = $user_info['user_info']->user_id;
            $full_user_details = $this->Employees_model->read_employee_information($user_id);

            if (!empty($full_user_details)) {
                $email = $full_user_details[0]->email;

                $leads_url = 'http://demo-crm.mysoftheaven.com/index.php/leads_api/get_daily_schedules';
                $post_data = ['owner_email' => $email];

                $ch = curl_init($leads_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                $response = curl_exec($ch);
                curl_close($ch);

                // Return raw response from CRM as JSON
                $this->output
                    ->set_content_type('application/json')
                    ->set_output($response);
            } else {
                $this->api_return(['status' => false, 'message' => 'User email not found'], 404);
            }
        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized'], 401);
        }
    }
}
