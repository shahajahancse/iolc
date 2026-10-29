<?php
 /**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the HRSALE License
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.hrsale.com/license.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to hrsalesoft@gmail.com so we can send you a copy immediately.
 *
 * @author   HRSALE
 * @author-email  hrsalesoft@gmail.com
 * @copyright  Copyright © hrsale.com. All Rights Reserved
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Documents extends MY_Controller {

	 public function __construct() {
        parent::__construct();
		//load the models
		$this->load->model("Xin_model");
		$session = $this->session->userdata('username');
		if(empty($session)){
			redirect('admin/');
		}
	}

	/*Function to set JSON output*/
	public function output($Return=array()){
		/*Set response header*/
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
		/*Final JSON response*/
		exit(json_encode($Return));
	}

	public function index()
	{

		$data = [
			'title' => 'Documents | '.$this->Xin_model->site_title(),
			'all_countries' => $this->Xin_model->get_countries(),
			'breadcrumbs' => $this->lang->line('xin_documents'),
			'path_url' => 'documents',
			'role_resources_ids' => $this->Xin_model->user_role_resource()
		];
		$data['subview'] = $this->load->view("admin/documents/doc_list", $data, TRUE);
		$this->load->view('admin/layout/layout_main', $data); //page load

	}

	public function doc_report(){
		$data = [
			'title' => 'Document Report | '.$this->Xin_model->site_title(),
			'all_countries' => $this->Xin_model->get_countries(),
			'breadcrumbs' => $this->lang->line('xin_documents'),
			'path_url' => 'documents',
			'role_resources_ids' => $this->Xin_model->user_role_resource()
		];
		$data['subview'] = $this->load->view("admin/documents/doc_report", $data, TRUE);
		$this->load->view('admin/layout/layout_main', $data); //page load
	}
	public function doc_reportt(){
		// dd($_POST);
		$first_date = $this->input->post('first_date');
		$second_date = $this->input->post('second_date');
		$sql = $this->input->post('sql');
		$this->db->where('issue_date >=', $first_date);
		$this->db->where('issue_date <=', $second_date);
		$query = $this->db->get('discipline_documents');
		$data['docs'] = $query->result();
		// dd($data);
		$this->load->view('admin/documents/doc_reporttt', $data);
	}


	public function save_document() {
		$id = $this->input->post('document_id', true);
		$reference = '';

		// Only generate reference for new records
		if (empty($id)) {
			$currentYear = date("Y");

			$this->db->order_by('id', 'DESC');
			$this->db->limit(1);
			$datas = $this->db->get('discipline_documents');

			if ($datas->num_rows() > 0) {
				$data = $datas->row();
				$lastYear = date("Y", strtotime($data->created_at));

				if ($lastYear == $currentYear) {
					$reference = sprintf("%06d", $data->reference + 1);
				} else {
					$reference = "000001";
				}
			} else {
				$reference = "000001";
			}
		}

		// map codes → IDs (since your form posts discipline_code & class_code)
		$discipline_id = $this->db->select('code')->where('code', $this->input->post('discipline_code'))->get('disciplines')->row('code');
		$classification_id = $this->db->select('class_code')->where('class_code', $this->input->post('class_code'))->get('discipline_classification')->row('class_code');

		$data = [
			'discipline_id'     => $discipline_id,
			'classification_id' => $classification_id,
			'title'             => $this->input->post('title', true),
			'description'       => $this->input->post('description', true),
			'user'              => $this->input->post('user', true),
			'issue_date'        => $this->input->post('issue_date', true),
			'status'            => $this->input->post('status', true),
			'updated_at'        => date('Y-m-d H:i:s'),
		];

		if (empty($id)) {
			// Insert new document
			$data['reference']  = $reference;
			$data['rev_number'] = 0;
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['created_by'] = $this->session->userdata('user_id')['user_id'];

			$this->db->insert('discipline_documents', $data);
			$msg = "Document saved successfully.";
		} else {
			// Get current rev_number
			$rev = $this->db->select('rev_number')->where('id', $id)->get('discipline_documents')->row('rev_number');
			$data['rev_number'] = $rev + 1; // increment by 1

			$this->db->where('id', $id);
			$this->db->update('discipline_documents', $data);
			$msg = "Document updated successfully. Revision " . $data['rev_number'];
		}

		if ($this->db->affected_rows() > 0) {
			$this->session->set_flashdata('success', $msg);
		} else {
			$this->session->set_flashdata('error', 'No changes were made.');
		}

		redirect('admin/documents');
	}




	public function discipline_view()
	{
		$data = [
			'title' => $this->lang->line('xin_documents').' | '.$this->Xin_model->site_title(),
			'all_countries' => $this->Xin_model->get_countries(),
			'breadcrumbs' => $this->lang->line('xin_documents'),
			'path_url' => 'documents',
			'role_resources_ids' => $this->Xin_model->user_role_resource()
		];
		$data['subview'] = $this->load->view("admin/documents/dis_view", $data, TRUE);
		$this->load->view('admin/layout/layout_main', $data); //page load

	}

	public function get_discipline(){
		$this->db->where('id', $_POST['id']);
		$query = $this->db->get('disciplines');
		if($query->num_rows() > 0){
			echo json_encode(array('result' => 'success', 'data' => $query->row()));
		} else {
			echo json_encode(array('result' => 'error', 'message' => 'Discipline not found.'));
		}
	}
	public function get_discipline_document() {
		$id = $this->input->post('id');
		$this->db->select('d.id, d.title, d.description, d.user, d.issue_date, d.status,
						dis.code as discipline_code,
						cls.class_code as class_code');
		$this->db->from('discipline_documents d');
		$this->db->join('disciplines dis', 'dis.code = d.discipline_id', 'left');
		$this->db->join('discipline_classification cls', 'cls.class_code = d.classification_id', 'left');
		$this->db->where('d.id', $id);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			echo json_encode(['result' => 'success', 'data' => $query->row()]);
		} else {
			echo json_encode(['result' => 'error', 'message' => 'Document not found.']);
		}
	}



	public function save_discipline() {
		$id = $this->input->post('discipline_id');
		$code = $this->input->post('code', true);
		$this->db->where('code', $code);
		$duplicate = $this->db->get('disciplines')->num_rows();

		$data = [
			'code'        => $this->input->post('code', true),
			'title'       => $this->input->post('title', true),
			'description' => $this->input->post('description', true),
			'status'      => $this->input->post('status', true),
			'updated_at'  => date('Y-m-d H:i:s'),
		];

		if ($id) {
			// Update
			$this->db->where('id', $id);
			$this->db->update('disciplines', $data);
			$this->session->set_flashdata('success', 'Discipline updated successfully.');
		} else {
			// Insert
			$this->db->where('code', $code);
			$duplicate = $this->db->get('disciplines')->num_rows();

			if ($duplicate > 0) {
				$this->session->set_flashdata('error', 'Discipline code already exists.');
				redirect('admin/documents/discipline_view');
			}
			$data['created_at'] = date('Y-m-d H:i:s');
			$this->db->insert('disciplines', $data);
			$this->session->set_flashdata('success', 'Discipline added successfully.');
		}

		redirect('admin/documents/discipline_view');
	}

	public function delete_discipline($id){
		$this->db->where('id', $id);
		$this->db->delete('disciplines');
		if($this->db->affected_rows() > 0){
			$this->session->set_flashdata('success', 'Discipline deleted successfully.');
		} else {
			$this->session->set_flashdata('error', 'Failed to delete discipline.');
		}
		redirect('admin/documents/discipline_view');
	}


	public function classification_view(){
		$data = [
			'title' => $this->lang->line('xin_documents').' | '.$this->Xin_model->site_title(),
			'all_countries' => $this->Xin_model->get_countries(),
			'breadcrumbs' => $this->lang->line('xin_documents'),
			'path_url' => 'documents',
			'role_resources_ids' => $this->Xin_model->user_role_resource()
		];
		$data['subview'] = $this->load->view("admin/documents/class_view", $data, TRUE);
		$this->load->view('admin/layout/layout_main', $data); //page load

	}

	public function get_classification(){
		$this->db->where('id', $_POST['id']);
		$query = $this->db->get('discipline_classification');
		if($query->num_rows() > 0){
			echo json_encode(array('result' => 'success', 'data' => $query->row()));
		} else {
			echo json_encode(array('result' => 'error', 'message' => 'Classification not found.'));
		}
	}
	public function get_classifications() {
		$discipline_code = $this->input->post('id');
		if($discipline_code) {
			$classifications = $this->db->get_where('discipline_classification', ['discipline_code' => $discipline_code])->result();
			echo json_encode($classifications);
		} else {
			echo json_encode([]);
		}
	}

	public function save_classification() {
		$id = $this->input->post('classification_id');
		$code = $this->input->post('class_code', true);

		$data = [
			'discipline_code' => $this->input->post('discipline_code', true),
			'class_code'      => $this->input->post('class_code', true),
			'title'           => $this->input->post('title', true),
			'description'     => $this->input->post('description', true),
			'status'          => $this->input->post('status', true),
			'updated_at'      => date('Y-m-d H:i:s'),
		];

		if ($id) {
			// Update
			$this->db->where('id', $id);
			$this->db->update('discipline_classification', $data);
			$this->session->set_flashdata('success', 'Classification updated successfully.');
		} else {
			// Insert
			$this->db->where('class_code', $code);
			$duplicate = $this->db->get('discipline_classification')->num_rows();

			if ($duplicate > 0) {
				$this->session->set_flashdata('error', 'Classification code already exists.');
				redirect('admin/documents/classification_view');
			}
			$data['created_at'] = date('Y-m-d H:i:s');
			$this->db->insert('discipline_classification', $data);
			$this->session->set_flashdata('success', 'Classification added successfully.');
		}

		redirect('admin/documents/classification_view');
	}

	public function delete_classification($id){
		$this->db->where('id', $id);
		$this->db->delete('discipline_classification');
		if($this->db->affected_rows() > 0){
			$this->session->set_flashdata('success', 'Classification deleted successfully.');
		} else {
			$this->session->set_flashdata('error', 'Failed to delete classification.');
		}
		redirect('admin/documents/classification_view');
	}
	public function delete_document($id){
		$this->db->where('id', $id);
		$this->db->delete('discipline_documents');
		if($this->db->affected_rows() > 0){
			$this->session->set_flashdata('success', 'Document deleted successfully.');
		} else {
			$this->session->set_flashdata('error', 'Failed to delete document.');
		}
		redirect('admin/documents/');
	}

	public function doc_list_ajax()
	{
		$data['title'] = 'Doc List';
		$session = $this->session->userdata('username');
		if (!empty($session)) {
			$this->load->view("admin/documents/doc_list", $data);
		} else {
			redirect('admin/');
		}

		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));

		$role_resources_ids = $this->Xin_model->user_role_resource();
		$user_info = $this->Xin_model->read_user_info($session['user_id']);

		$holidays = $this->db->get("discipline_documents");

		$data = array();

		foreach ($holidays->result() as $key => $r) {

			$action = '<a onclick="edit_document(' . $r->id . ')" class="btn btn-xs btn-primary">Edit</a>';
			$action .= '<a href="' . site_url('admin/documents/delete_document/' . $r->id) . '" class="btn btn-xs btn-danger">Delete</a>';

			if($r->status == 1) {
				$status = '<span class="badge badge-success" style="background-color: #28a745; color: #fff;">Active</span>';
			} else {
				$status = '<span class="badge badge-secondary" style="background-color: #6c757d; color: #fff;">Inactive</span>';
			}
			$code = "MHL-".$r->discipline_id."-".$r->classification_id."-".date("y",strtotime($r->created_at)).'-'.$r->reference."/".$r->rev_number;
			$data[] = array(
				$key + 1,
				$r->discipline_id,
				$r->classification_id,
				$code,
				$r->rev_number,
				$r->description,
				$status,
				$action,
			);
		}

		$output = array(
			"draw" => $draw,
			"recordsTotal" => $holidays->num_rows(),
			"recordsFiltered" => $holidays->num_rows(),
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}

}


