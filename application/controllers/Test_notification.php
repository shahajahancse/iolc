<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_notification extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('general');
    }

    public function index() {
        echo "<h1>FCM Debugger</h1>";

        // 1. Check Service Account
        $key_file_path = APPPATH . 'config/service-account.json';
        if (!file_exists($key_file_path)) {
            die("Error: service-account.json MISSING at $key_file_path");
        } else {
            echo "1. Service Account File: FOUND<br>";
        }

        // 2. Fetch Tokens
        $employees = $this->db->select('user_id, first_name, device_id')
                              ->where('is_active', 1)
                              ->where('device_id !=', '')
                              ->where('device_id IS NOT NULL')
                              ->get('xin_employees')
                              ->result();

        echo "2. Database Query: Found " . count($employees) . " employees with tokens.<br>";
        
        $tokens = [];
        foreach($employees as $emp) {
            echo " - User: " . $emp->first_name . " (ID: " . $emp->user_id . ")<br>";
            // Truncate token for display
            echo "   Token: " . substr($emp->device_id, 0, 20) . "...<br>";
            $tokens[] = $emp->device_id;
        }

        if(empty($tokens)) {
            die("NO TOKENS FOUND. Stopping.");
        }

        // 3. Send Notification
        echo "3. Attempting to send...<br>";
        $title = "Debug Notification";
        $body = "This is a test from the debugger script at " . date('Y-m-d H:i:s');
        
        // Call helper
        $responses = send_firebase_notification($tokens, $title, $body);

        echo "4. Raw Responses:<br><pre>";
        print_r($responses);
        echo "</pre>";
    }
}
