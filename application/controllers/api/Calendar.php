<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/API_Controller.php';

class Calendar extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('api_helper');
        $this->load->model('New_movement_model');
        $this->load->model('Client_attendance_model');
        $this->load->model('Todo_model');
    }

    public function get_calendar_data()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        
        if ($user_info['status'] == true) {
            $user_data = $user_info['user_info'];
            $user_id = $user_data->user_id;
            
            // Get Month and Year from Input, default to current
            $month = $this->input->get('month') ? $this->input->get('month') : date('m');
            $year = $this->input->get('year') ? $this->input->get('year') : date('Y');
            
            // Calculate start and end date for querying
            $start_date = "$year-$month-01";
            $end_date = date("Y-m-t", strtotime($start_date));
            
            $final_data = [];
            
            // 1. Employee Events
            $this->db->select('e.*');
            $this->db->from('xin_event_participants p');
            $this->db->join('xin_events e', 'e.event_id = p.event_id', 'inner');
            $this->db->where('p.user_id', $user_id);
            $this->db->where("e.start_event_date BETWEEN '$start_date' AND '$end_date'");
            $events = $this->db->get()->result();
            
            foreach($events as $event) {
                $time_str = date('h:i A', strtotime($event->start_event_time)) . ' - ' . date('h:i A', strtotime($event->end_event_time));
                $final_data[] = [
                    'id' => $event->event_id,
                    'date' => $event->start_event_date,
                    'time' => $time_str,
                    'type' => 'event',
                    'title' => $event->event_title,
                    'note' => strip_tags($event->event_note)
                ];
            }

            // 2. Client Attendance
            $this->db->select('*');
            $this->db->from('client_attendance');
            $this->db->where('emp_id', $user_id);
            $this->db->where("date BETWEEN '$start_date' AND '$end_date'");
            $client_attendances = $this->db->get()->result();
            
            foreach($client_attendances as $ca) {
                $final_data[] = [
                    'id' => $ca->id,
                    'date' => $ca->date,
                    'time' => null, 
                    'type' => 'client_visit',
                    'title' => $ca->client_name . ' (' . $ca->type . ')',
                    'note' => $ca->remarks
                ];
            }
            
            // 3. Movement Meetings (External)
             $this->db->select('m.*');
            $this->db->from('new_movement_meetings m');
            $this->db->join('new_movement_movements mv', 'mv.id = m.movement_id');
            $this->db->where('mv.employee_id', $user_id);
            $this->db->where("m.start_time BETWEEN '$start_date' AND '$end_date'"); // Fixed: Removed HH:MM:SS logic to be consistent or just rely on date parsing in loop? 
            // Wait, previous code used :00:00. Let's stick to simple string match if datetime.
            // Actually the replace block must match exact content.
             $this->db->where("m.start_time BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'");
             $meetings = $this->db->get()->result();
             
             foreach($meetings as $meeting) {
                $m_date = date('Y-m-d', strtotime($meeting->start_time));
                $end_time = (isset($meeting->end_time) && $meeting->end_time) ? $meeting->end_time : $meeting->start_time;
                $time_str = date('h:i A', strtotime($meeting->start_time)) . ' - ' . date('h:i A', strtotime($end_time));
                
                $title = isset($meeting->client_name) ? "Meeting with " . $meeting->client_name : "Movement Meeting";

                $final_data[] = [
                    'id' => $meeting->id,
                    'date' => $m_date,
                    'time' => $time_str,
                    'type' => 'movement_meeting',
                    'title' => $title,
                    'note' => isset($meeting->remarks) ? $meeting->remarks : ''
                ];
             }

             // 4. Meeting Room Bookings (Internal)
             $this->db->select('b.*, r.name as room_name');
             $this->db->from('meeting_room_bookings b');
             $this->db->join('meeting_rooms r', 'r.id = b.room_id', 'left');
             $this->db->group_start();
                $this->db->where('b.employee_id', $user_id);
                $this->db->or_where("FIND_IN_SET('$user_id', b.attendees) !=", 0);
             $this->db->group_end();
             $this->db->where("b.meeting_date BETWEEN '$start_date' AND '$end_date'");
             $room_bookings = $this->db->get()->result();

             foreach($room_bookings as $booking) {
                $time_str = date('h:i A', strtotime($booking->start_time)) . ' - ' . date('h:i A', strtotime($booking->end_time));
                $final_data[] = [
                    'id' => $booking->id,
                    'date' => $booking->meeting_date,
                    'time' => $time_str,
                    'type' => 'meeting_room',
                    'title' => 'Meeting in ' . $booking->room_name,
                    'note' => isset($booking->message) ? $booking->message : ''
                ];
             }

             // 4. Birthdays
             $this->db->select('user_id, first_name, last_name, date_of_birth, profile_picture');
             $this->db->from('xin_employees');
             $this->db->where("MONTH(date_of_birth) = '$month'");
             $this->db->where_in('status', [1, 4, 5]);
             $birthdays = $this->db->get()->result();
             
             foreach($birthdays as $bday) {
                 $bday_day = date('d', strtotime($bday->date_of_birth));
                 $current_year_bday = "$year-$month-$bday_day";
                 
                 $final_data[] = [
                     'id' => $bday->user_id,
                     'date' => $current_year_bday,
                     'time' => null,
                     'type' => 'birthday',
                     'title' => 'Birthday of ' . $bday->first_name . ' ' . $bday->last_name,
                     'note' => 'Wish them a happy birthday!'
                 ];
             }
             
             // 5. To-Dos
             $this->db->select('*');
             $this->db->from('xin_todo_list');
             $this->db->where('user_id', $user_id);
             $this->db->where("DATE_FORMAT(created_at, '%Y-%m') = '$year-$month'");
             $todos = $this->db->get()->result();
             
             foreach($todos as $todo) {
                 $t_date = date('Y-m-d', strtotime($todo->created_at));
                 $final_data[] = [
                     'id' => $todo->id,
                     'date' => $t_date,
                     'time' => null,
                     'type' => 'to_do',
                     'title' => $todo->title,
                     'note' => $todo->status == 1 ? 'Completed' : 'Pending'
                 ];
             }
             
            // Sort by Date
            usort($final_data, function($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });

            $this->api_return([
                'status_code'    =>  "200", 
                'message'    =>  'calender List Fetched Successfully',
                'data'       =>  $final_data,
            ], 200);

        } else {
            $this->api_return([
                'status'  =>   false,
                'message'  =>   'Unauthorized User',
                'data'     =>   [],
            ], 401);
        }
    }
    public function add_todo()
    {
        $this->_APIConfig(['methods' => ['POST']]);
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            $user_id = $user_info['user_info']->user_id;
            
            $title = $this->input->post('title');
            
            if(empty($title)) {
                $this->api_return(['status' => false, 'message' => 'Title is required', 'data' => null], 400);
                return;
            }

            $current_date = date('Y-m-d H:i:s', strtotime($this->input->post('date')));

            $data = array(
                'user_id' => $user_id,
                'title' => $title,
                'status' => 0, // Pending
                'created_at' => $current_date
            );

            $result = $this->Todo_model->add_todo($data);

            if ($result) {
                 $this->api_return([
                     'status' => true,
                     'message' => 'ToDo Added Successfully',
                     'data' => [
                         'id' => $this->db->insert_id(),
                         'title' => $title,
                         'date' => date('Y-m-d'),
                         'status' => 0
                     ]
                 ], 200);
            } else {
                 $this->api_return(['status' => false, 'message' => 'Failed to add ToDo', 'data' => null], 500);
            }

        } else {
            $this->api_return(['status' => false, 'message' => 'Unauthorized User', 'data' => null], 401);
        }
    }
}
