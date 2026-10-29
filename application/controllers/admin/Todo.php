<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Todo extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("Todo_model");
        $this->load->model("Xin_model");
    }

    public function index() {
        $session = $this->session->userdata('username');
        if(empty($session)){ 
            redirect('admin/');
        }
        $data['title'] = $this->Xin_model->site_title();
        $data['breadcrumbs'] = 'To-Do List';
        $data['path_url'] = 'todo';
        
        $data['user_id'] = $session['user_id'];
        $data['todos'] = $this->Todo_model->get_user_todos($session['user_id']);
        
        $data['subview'] = $this->load->view('admin/todo/todo_list', $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data);
    }
    
    // Add new task
    public function add_task() {
        $session = $this->session->userdata('username');
        
        if(empty($session)) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        $title = trim($this->input->post('title'));
        
        if(empty($title)) {
            echo json_encode(['success' => false, 'message' => 'Task title is required']);
            return;
        }
        
        $data = array(
            'user_id' => $session['user_id'],
            'title' => $title,
            'status' => 0,
            'created_at' => date('Y-m-d H:i:s')
        );
        
        if($this->Todo_model->add_todo($data)) {
            // Return updated list
            $data['todos'] = $this->Todo_model->get_user_todos($session['user_id']);
            $this->load->view('admin/todo/todo_items', $data);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add task']);
        }
    }

    // Update task status (toggle complete/incomplete)
    public function update_status() {
        $session = $this->session->userdata('username');
        
        if(empty($session)) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        
        if(empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Task ID is required']);
            return;
        }
        
        if($this->Todo_model->update_status($id, $session['user_id'], $status)) {
            // Return updated list
            $data['todos'] = $this->Todo_model->get_user_todos($session['user_id']);
            $this->load->view('admin/todo/todo_items', $data);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update task']);
        }
    }

    // Get single task for editing
    public function get_task() {
        $session = $this->session->userdata('username');
        
        if(empty($session)) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        $id = $this->input->post('id');
        
        if(empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Task ID is required']);
            return;
        }
        
        $task = $this->Todo_model->get_todo_by_id($id, $session['user_id']);
        
        if($task) {
            echo json_encode(['success' => true, 'task' => $task]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Task not found']);
        }
    }

    // Update task title
    public function update_task() {
        $session = $this->session->userdata('username');
        
        if(empty($session)) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        $id = $this->input->post('id');
        $title = trim($this->input->post('title'));
        
        if(empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Task ID is required']);
            return;
        }
        
        if(empty($title)) {
            echo json_encode(['success' => false, 'message' => 'Task title is required']);
            return;
        }
        
        $data = array('title' => $title);
        
        if($this->Todo_model->update_todo($id, $session['user_id'], $data)) {
            // Return updated list
            $data['todos'] = $this->Todo_model->get_user_todos($session['user_id']);
            $this->load->view('admin/todo/todo_items', $data);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update task']);
        }
    }

    // Delete task
    public function delete_task() {
        $session = $this->session->userdata('username');
        
        if(empty($session)) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        $id = $this->input->post('id');
        
        if(empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Task ID is required']);
            return;
        }
        
        if($this->Todo_model->delete_todo($id, $session['user_id'])) {
            // Return updated list
            $data['todos'] = $this->Todo_model->get_user_todos($session['user_id']);
            $this->load->view('admin/todo/todo_items', $data);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete task']);
        }
    }
    
    // Get all tasks as JSON
    public function get_all_tasks() {
        $session = $this->session->userdata('username');
        
        if(empty($session)) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        $todos = $this->Todo_model->get_user_todos($session['user_id']);
        echo json_encode(['success' => true, 'todos' => $todos]);
    }
}
