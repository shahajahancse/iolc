<?php
	$session = $this->session->userdata('username');
	$user_info = $this->Xin_model->read_user_info($session['user_id']);
	$theme = $this->Xin_model->read_theme_info(1);
?>
<?php
	if(in_array($user_info[0]->user_role_id, array(1,2,4,5))):
		$dd='dashboard_3';
		if($dd == 'dashboard_1') {
			// dd('a');
			$this->load->view('admin/dashboard/administrator_dashboard_1');
		} else if($dd == 'dashboard_2') {
			// dd('b');
			$this->load->view('admin/dashboard/administrator_dashboard_2');
		} else if($dd == 'dashboard_3') {
			// dd('c');
			$this->load->view('admin/dashboard/administrator_dashboard_3');
		} else if($dd == 'dashboard_4') {
			// dd('d');
			$this->load->view('admin/dashboard/administrator_dashboard_4');
		} else {
			// dd('e');
			$this->load->view('admin/dashboard/administrator_dashboard_1');
		}
	elseif (in_array($user_info[0]->user_role_id, array(6))):
		// dd('f');
		$this->load->view('admin/dashboard/document_dashboard');
	else:
		// dd('g');
		$this->load->view('admin/dashboard/employee_dashboard');
	endif;
?>











