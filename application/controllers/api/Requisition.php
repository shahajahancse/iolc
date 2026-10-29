<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'libraries/API_Controller.php';
class Requisition extends API_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->helper('api_helper');
        $this->load->model("Accessories_model");
		$this->load->model("Xin_model");
		$this->load->model("Inventory_model");
        $this->load->helper('form');
    }

    public function test(){
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == TRUE) {
            echo json_encode($user_info);
            exit();
        } else {
            echo json_encode($user_info);
            exit();
        }
    }

    // ========= store requisition section ===========
    // store product list
    public function get_item_list(){
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if($user_info['status']==TRUE){
             $this->db->select('
                p.cat_id,
                p.sub_cate_id,
		   		p.id as product_id,
		   		pc.category_name,
		   		psc.sub_cate_name,
		   		p.product_name,
		   		p.quantity,
		   		pu.unit_name
	   		');
            $this->db->from('products as p');
            $this->db->join('products_categories as pc', 'pc.id = p.cat_id');
            $this->db->join('products_sub_categories as psc', 'psc.id = p.sub_cate_id');
            $this->db->join('product_unit as pu', 'pu.id = p.unit_id');
            $this->db->order_by('p.id','ASC');
            $data = $this->db->get()->result();
            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'data' => $data,
            ], 200);
        }else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
            ], 401);
        }
    }

    // get product by id
    public function get_item_by_id($id = null){
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if($user_info['status']==TRUE){
            if (!empty($this->input->get('product_id'))) {
                $id = $this->input->get('product_id');
            }
            $this->db->select('
                p.cat_id,
                p.sub_cate_id,
		   		p.id as product_id,
		   		pc.category_name,
		   		psc.sub_cate_name,
		   		p.product_name,
		   		p.quantity,
		   		pu.unit_name
	   		');
            $this->db->from('products as p');
            $this->db->join('products_categories as pc', 'pc.id = p.cat_id');
            $this->db->join('products_sub_categories as psc', 'psc.id = p.sub_cate_id');
            $this->db->join('product_unit as pu', 'pu.id = p.unit_id');
            $data = $this->db->where('p.id', $id)->get()->row();
            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'data' => $data,
            ], 200);
        }else{
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
            ], 401);
        }
    }

    // create requisition
    public function create_requisition() {
        $authorization = $this->input->get_request_header('Authorization');
        $auth = api_auth($authorization);

        if ($auth['status'] == TRUE) {
            $user_info = (array) $auth['user_info'];
            $cat_id = $this->input->post('cat_id');
            $sub_cate_id = $this->input->post('sub_cate_id');
            $product_id = $this->input->post('product_id');
            $quantity = $this->input->post('quantity');
            $note = $this->input->post('note');
            $priority = $this->input->post('priority');
            // Build and execute your SQL query
            $insert_data = array();
            foreach ($product_id as $k => $product) {
                $insert_data[$k] = array(
                    'user_id' => $user_info['user_id'],
                    'cat_id' => $cat_id[$k],
                    'sub_cate_id' => $sub_cate_id[$k],
                    'product_id' => $product,
                    'quantity' => $quantity[$k],
                    'note' => $note[$k],
                    'priority' => $priority[$k],
                    'requisition_date' => date('Y-m-d'),
                    'created_at' => date('Y-m-d'),
                );
            }
            if ($this->db->insert_batch('products_requisition_details', $insert_data)) {
                $this->api_return([
                    'status' => true,
                    'message' => 'Successfully inserted data',
                ], 200);
            } else {
                $this->api_return([
                    'status' => false,
                    'message' => 'Failed to insert data',
                ], 200);
            }
        }else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
            ], 401);
        }
    }

    // get requisition list by status
    public function get_requisition_list() {
        $authorization = $this->input->get_request_header('Authorization');
        $auth = api_auth($authorization);

        if ($auth['status'] == TRUE) {
            $user_info = (array) $auth['user_info'];
            $user_role = $user_info['user_role_id'];

            $status = $this->input->get('status');
            $limit = $this->input->get('limit');
            $offset = $this->input->get('offset');
            if ($status == 2) {
                $astatus = array(2); // approved
            } elseif ($status == 3) {
                $astatus = array(3);  // delivered
            } elseif ($status == 4) {
                $astatus = array(4); // rejected
            } elseif ($status == 1) {
                $astatus = array(1, 5, 6); // pending
            } else {
                $astatus = array(1, 2, 3, 4, 5, 6); // all
            }

            $this->db->select("
                xin_employees.first_name,
                xin_employees.last_name,
                products.product_name,
                prd.*
            ");
            $this->db->from("products_requisition_details as prd");
            $this->db->join('xin_employees','prd.user_id = xin_employees.user_id','left');
            $this->db->join('products','prd.product_id = products.id','left');
            $this->db->where_in('prd.status',$astatus);
            if ($user_role == 3) {
                $this->db->where_in('prd.user_id', $user_info['user_id']);
            }
            $this->db->group_by('prd.id')->order_by('prd.id', 'desc');
            $data = $this->db->limit($limit, $offset)->get()->result();
            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'data' => $data,
            ], 200);
        }else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
            ], 401);
        }
    }

    // get requisition details
    public function get_requisition_details($id = null){
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if($user_info['status']==TRUE){
            $user_info = (array) $user_info['user_info'];
            if (!empty($this->input->get('requisition_id'))) {
                $id = $this->input->get('requisition_id');
            }
            $data['requisition'] = $this->db->select('
                        pc.category_name,
                        products.id as product_id,
                        products.product_name,
                        prd.*,
                        prd.id as req_id,
                        xin_employees.first_name,
                        xin_employees.last_name
                    ')
                    ->from('products_requisition_details prd')
                    ->join('products_categories pc', 'pc.id = prd.cat_id','LEFT')
                    ->join('products', 'products.id = prd.product_id','LEFT')
                    ->join('xin_employees', 'xin_employees.user_id = prd.user_id','LEFT')
                    ->where('prd.id',$id)->get()->row();
            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'data' => $data,
            ], 200);
        }else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
            ], 401);
        }
    }

    // update requisition
    public function update_requisition(){
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true) {
            $user_info = (array) $user_info['user_info'];
            $id = $this->input->post('requisition_id');

            $data = array(
                'cat_id' => $this->input->post('cat_id'),
                'sub_cate_id' => $this->input->post('sub_cate_id'),
                'product_id' => $this->input->post('product_id'),
                'quantity' => $this->input->post('quantity'),
                'note' => $this->input->post('note'),
                'priority' => $this->input->post('priority'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $user_info['user_id'],
            );

            if ($this->db->where('id',$id)->update('products_requisition_details', $data)) {
                $this->api_return([
                'status' => true,
                'message' => 'Successfully update',
                ], 200);
            }else {
                $this->api_return([
                'status' => false,
                'message' => 'Failed to update data',
                ], 200);
            }
        }else {
            $this->api_return([
            'status' => false,
            'message' => 'Unauthorized User',
            ], 401);
        }
    }

    // approve requisition
    public function approve_requisition(){
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true) {
            $user_info = (array) $user_info['user_info'];
            $user_role = $user_info['user_role_id'];

            $id = $this->input->post('requisition_id');
            $approved_qty = $this->input->post('approved_qty');
            $status = $this->input->post('status');

            if ($user_role == 2 && $status == 2) {
                $rr = $this->db->where('id', $id)->get('products_requisition_details')->row();
                $row = $this->db->where('id', $rr->product_id)->get('products')->row();
                if ($row->p_permission == '0') {
                    $status = 2;
                } else {
                    $status = 6;
                }
            }

            $data = array(
                'approved_qty' => $approved_qty,
                'status' 	   => $status,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $user_info['user_id'],
            );

            if ($this->db->where('id',$id)->update('products_requisition_details', $data)) {
                $this->api_return([
                'status' => true,
                'message' => 'Successfully update',
                ], 200);
            }else {
                $this->api_return([
                'status' => false,
                'message' => 'Failed to update data',
                ], 200);
            }
        }else {
            $this->api_return([
            'status' => false,
            'message' => 'Unauthorized User',
            ], 401);
        }
    }

    // delete requisition
    public function delete_requisition($id = null){
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if($user_info['status']==TRUE){
            $id = $this->input->post('requisition_id');
            if($this->db->where('id',$id)->delete('products_requisition_details') ){
                $this->api_return([
                    'status' => true,
                    'message' => 'Delete Successful'
                ], 200);
            }else{
                $this->api_return([
                    'status' => true,
                    'message' => 'Error'
                 ], 200);
            }
        }else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
            ], 401);
        }
	}
    // ========= store requisition section end ===========

    // ========= store purchase requisition section ===========
    // get purchase list by status
    public function get_purchase_list() {
        $authorization = $this->input->get_request_header('Authorization');
        $auth = api_auth($authorization);

        if ($auth['status'] == TRUE) {
            $user_info = (array) $auth['user_info'];
            $status = $this->input->get('status');
            $limit = $this->input->get('limit');
            $offset = $this->input->get('offset');

            $this->db->select('
                p.*,
                emp.first_name,
                emp.last_name,
                products.product_name
            ')->from('products_purches_details as p')
            ->join('xin_employees as emp', 'emp.user_id = p.user_id', 'left')
            ->join('products', 'p.product_id = products.id', 'left');
            if (!empty($status)) {
                $this->db->where('p.status', $status);
            }
            $this->db->order_by('p.id', 'desc');
            $data = $this->db->limit($limit, $offset)->get()->result();

            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'data' => $data,
            ], 200);
        }else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
            ], 401);
        }
    }

    // approve purchase requisition
    public function approve_purchase(){
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true) {
            $user_info = (array) $user_info['user_info'];
            $id = $this->input->post('purchase_id');
            $approved_qty = $this->input->post('approved_qty');
            $status = $this->input->post('status');
            $data = array(
                'ap_quantity' => $approved_qty,
                'status' 	   => $status,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $user_info['user_id'],
            );

            if ($this->db->where('id',$id)->update('products_purches_details', $data)) {
                $this->api_return([
                'status' => true,
                'message' => 'Successfully update',
                ], 200);
            }else {
                $this->api_return([
                'status' => false,
                'message' => 'Failed to update data',
                ], 200);
            }
        }else {
            $this->api_return([
            'status' => false,
            'message' => 'Unauthorized User',
            ], 401);
        }
    }
    // ========= store purchase requisition end ===========


    // ========= mobile bill section ===========
    // mobile bill requisition
    // view list
    public function view_moile_bill_req_list(){
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if($user_info['status']==TRUE){
            $user_info = $user_info['user_info'];
            $data = $this->db->select('*')->where('user_id',$user_info->user_id)->get('mobile_bill_requisition')->result();
            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'mobile_bill_req_list' => $data,
            ], 200);
        }else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
            ], 401);
        }
    }

    public function get_number(){
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if($user_info['status']==TRUE){
            $user_info = $user_info['user_info'];
            $get_user_number = $this->db->select('mobile_numbers.number')
                        ->from('mobile_numbers')
                        ->join('product_accessories','product_accessories.number = mobile_numbers.id')
                        ->join('employee_using_device','product_accessories.id = employee_using_device.device_id')
                        ->where('employee_using_device.user_id',$user_info->user_id)
                        ->get()->row();
            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'user_number' => $get_user_number,
            ], 200);
        }else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
            ], 401);
        }
    }

    public function add_mobile_bill(){
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if($user_info['status']==TRUE){
            $raw_data = $this->input->raw_input_stream;
            $data = json_decode($raw_data);
            if($data !=null){
                $insert = array(
                            'phone_number'  => $data[0]->phone_number,
                            'amount'        => $data[0]->amount,
                            'user_id'       => $user_info['user_info']->user_id,
                            'created_at'     =>  date('Y-m-d')
                        );
                $this->db->insert('mobile_bill_requisition',$insert);
                $this->api_return([
                'status' => true,
                'message' => 'successful',
            ], 200);
            }else{
                $this->api_return([
                'status' => true,
                'message' => 'error',
            ], 200);
            }


        }else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
            ], 401);
        }
    }

    public function edit_bill($id){
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if($user_info['status']==TRUE){
            $user_info = (array) $user_info['user_info'];
            $data['mobile_bill']= $this->db->where('id',$id)->get('mobile_bill_requisition')->row();
            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'data' => $data,
            ], 200);
        }else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
            ], 401);
        }
    }

    public function update_bill(){
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        $user_info = (array) $user_info['user_info'];
        if ($user_info['status'] == true) {
            $raw_data = $this->input->raw_input_stream;
            $data = json_decode($raw_data);
            // dd($data);
            if ($data !== null) {
               $update  = $this->db->where('id',$data[0]->h_id)->update('mobile_bill_requisition',['amount'=>$data[0]->amount]);
                if ($update) {
                    $this->api_return([
                    'status' => true,
                    'message' => 'Successfully update',
                    ], 200);
                }else {
                    $this->api_return([
                    'status' => false,
                    'message' => 'Failed to update data',
                    ], 200);
                }
            }else {
                $this->api_return([
                'status' => false,
                'message' => 'Invalid JSON data',
                ], 400);
            }
        }else {
            $this->api_return([
            'status' => false,
            'message' => 'Unauthorized User',
            ], 401);
        }
    }

    public function delete_number($id){
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        $user_info = (array) $user_info['user_info'];
        if($user_info['status']==TRUE){
            $delete = $this->db->where('id',$id)->delete('mobile_bill_requisition');
            if($delete ){
                $this->api_return([
                    'status' => true,
                    'message' => 'Delete Successful'
                ], 200);
            }else{
                $this->api_return([
                    'status' => true,
                    'message' => 'Error'
                ], 200);
            }
        }else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
            ], 401);
        }
    }

    public function check_user(){
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if($user_info['status']==TRUE){
            $user_info = $user_info['user_info'];
            $check = $this->db->select('use_number,number')
                              ->where('number !=','')
                              ->where('user_id',$user_info->user_id)
                              ->get('product_accessories')
                              ->row();
            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'user_number' => $check,
            ], 200);
        }else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
            ], 401);
        }
    }
    // ========= store purchase requisition end ===========






    public function index(){
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if($user_info['status']==TRUE){
            $user_info = $user_info['user_info'];
            $data['using_items'] = $this->db->select('COUNT(user_id) as using_items')
            ->from('product_accessories')
            ->join('employee_using_device','product_accessories.id = employee_using_device.device_id')
            ->where('employee_using_device.user_id',$user_info->user_id)
            ->where('status',1)
            ->get()
            ->row()
            ->using_items;
            $data['requisition_list'] = $this->db->select('COUNT(user_id) as total_requisition_request, COUNT(status) as total_requisition_pending')->where('user_id',$user_info->user_id)->get('products_requisition_details')->row();
            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'data' => $data,
            ], 200);
        }else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
            ], 401);
        }

    }
    public function using_list(){
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if($user_info['status']==TRUE){
           $user_info =(array) $user_info['user_info'];
           $user_info['role_id'] = $user_info['user_role_id'];
           unset($user_info['user_role_id']);
           $data['equipments'] = $this->Inventory_model->equipment_list($user_info);
            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'data' => $data,
            ], 200);
        }else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
            ], 401);
        }

    }

    public function requisition_request_list(){
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if($user_info['status']==TRUE){
            $user_info =(array) $user_info['user_info'];
            $user_info['role_id'] = $user_info['user_role_id'];
            unset($user_info['user_role_id']);
            $data['requisition_list'] 	= $this->Inventory_model->requisition_list($user_info);
            $this->api_return([
                'status' => true,
                'message' => 'successful',
                'data' => $data,
            ], 200);
        }else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
            ], 401);
        }

    }

}
