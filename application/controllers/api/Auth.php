<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/API_Controller.php';

class Auth extends API_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Load Authorization Library or Load in autoload config file
        $this->load->library('Authorization_Token');
        $this->load->helper('api_helper');
    }

    /**
     * Check API Key
     *
     * @return key|string
     */
    private function key()
    {
        // use database query for get valid key

        return 1452;
    }


    /**
     * login method
     *
     * @link [api/user/login]
     * @method POST
     * @return Response|void
     */
    public function login()
    {
        header("Access-Control-Allow-Origin: *");
        // API Configuration
        $this->_apiConfig([
            'methods' => ['POST'],
        ]);

        // Support application/json, form-data, and x-www-form-urlencoded
        $raw_input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($raw_input)) {
            $raw_input = [];
        }

        $username = $this->input->post('username');
        if (empty($username) && isset($raw_input['username'])) {
            $username = $raw_input['username'];
        }
        if (empty($username)) {
            $username = $this->input->post('email');
        }
        if (empty($username) && isset($raw_input['email'])) {
            $username = $raw_input['email'];
        }
        if (empty($username) && isset($_POST['username'])) {
            $username = $_POST['username'];
        }
        if (empty($username) && isset($_POST['email'])) {
            $username = $_POST['email'];
        }
        if (empty($username) && isset($_REQUEST['username'])) {
            $username = $_REQUEST['username'];
        }
        if (empty($username) && isset($_REQUEST['email'])) {
            $username = $_REQUEST['email'];
        }

        $password = $this->input->post('password');
        if (empty($password) && isset($raw_input['password'])) {
            $password = $raw_input['password'];
        }
        if (empty($password) && isset($_POST['password'])) {
            $password = $_POST['password'];
        }
        if (empty($password) && isset($_REQUEST['password'])) {
            $password = $_REQUEST['password'];
        }

        $device_token = $this->input->post('device_token');
        if (empty($device_token) && isset($raw_input['device_token'])) {
            $device_token = $raw_input['device_token'];
        }
        if (empty($device_token) && isset($_REQUEST['device_token'])) {
            $device_token = $_REQUEST['device_token'];
        }
        if (empty($device_token)) {
            $device_token = '';
        }

        $data = array(
            'email' => $username,
            'password' => $password,
        );
        $auth = $this->login_auth($data);
        if ($auth['status'] == true) {
            // generate a token
            $token = $this->authorization_token->generateToken($data);
            $row = $this->db->where('user_id', $auth['user_id'])->get('api_keys')->row();
            if (empty($row)) {
                $this->db->insert('api_keys', array('api_key' => $token, 'user_id' => $auth['user_id'], 'device_token' => $device_token));
            } else {
                $this->db->where('user_id', $auth['user_id'])->update('api_keys', array('api_key' => $token, 'device_token' => $device_token));
            }
            $user_info = api_auth($token);
            if ($user_info && !empty($user_info['status']) && !empty($user_info['user_info'])) {
                $user_info['user_info']->token = $token;
                $user_info['user_info']->device_token = $device_token;
                $this->api_return([
                    'status' => true,
                    'message' => 'User login successful.',
                    'data' => $user_info['user_info'],
                ], 200);
            } else {
                $basic_user = $this->db->where('user_id', $auth['user_id'])->get('xin_employees')->row();
                if (!$basic_user) {
                    $basic_user = $this->db->where('user_id', $auth['user_id'])->get('xin_users')->row();
                }
                if ($basic_user) {
                    unset($basic_user->password);
                    $basic_user->token = $token;
                    $basic_user->device_token = $device_token;
                    $this->api_return([
                        'status' => true,
                        'message' => 'User login successful.',
                        'data' => $basic_user,
                    ], 200);
                } else {
                    $this->api_return([
                        'status' => false,
                        'message' => 'Unauthorized User',
                        'data' => [],
                    ], 401);
                }
            }

        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
                'data' => [],
            ], 401);
        }
    }
    public function logout()
    {
        $authorization = $this->input->get_request_header('Authorization');
        // Validate and sanitize the authorization value
        if (!empty($authorization) && is_string($authorization)) {
            $authorization = trim($authorization);
            // Verify the validity of the API key before deleting it
            $existingKey = $this->db->where('api_key', $authorization)->get('api_keys')->row();
            if ($existingKey) {
                // Delete the API key record
                $this->db->where('id', $existingKey->id)->delete('api_keys');
                // Success response
                $this->api_return([
                    'status' => true,
                    'message' => 'User logout successful.',
                    'data' => [],
                ], 200);
                
            } else {
                // Error response for invalid API key
                $this->api_return([
                    'status' => false,
                    'message' => 'User logout unsuccessful.',
                    'data' => [],
                ], 404);
            }
        } else {
            // Error response for missing or invalid authorization header
            $this->api_return([
                'status' => false,
                'message' => 'User logout unsuccessful.',
                'data' => [],
            ], 404);
        }
    }
    // Read data using username and password
    private function login_auth($data)
    {
        if (empty($data['email']) || empty($data['password'])) {
            return array(
                'user_id' => '',
                'status' => false,
            );
        }

        // Match either email or username in xin_employees
        $sql = 'SELECT * FROM xin_employees WHERE (email = ? OR username = ?)';
        $binds = array($data['email'], $data['email']);
        $query = $this->db->query($sql, $binds);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $rw_password) {
                $db_pass = $rw_password->password;
                if (password_verify($data['password'], $db_pass) || $data['password'] === $db_pass || md5($data['password']) === $db_pass) {
                    return array(
                        'user_id' => $rw_password->user_id,
                        'status' => true,
                    );
                }
            }
        }

        // Also check xin_users (employers/admins) if not found in xin_employees
        $sql_u = 'SELECT * FROM xin_users WHERE (email = ? OR username = ?)';
        $query_u = $this->db->query($sql_u, $binds);
        if ($query_u && $query_u->num_rows() > 0) {
            foreach ($query_u->result() as $rw_u) {
                $db_pass = $rw_u->password;
                if (password_verify($data['password'], $db_pass) || $data['password'] === $db_pass || md5($data['password']) === $db_pass) {
                    return array(
                        'user_id' => $rw_u->user_id,
                        'status' => true,
                    );
                }
            }
        }

        return array(
            'user_id' => '',
            'status' => false,
        );
    }


    /**
     * demo method
     *
     * @link [api/user/demo]
     * @method POST
     * @return Response|void
     */
    public function demo()
    {
        header("Access-Control-Allow-Origin: *");

        // API Configuration
        $this->_apiConfig([
            /**
             * By Default Request Method `GET`
             */
            'methods' => ['POST'], // 'GET', 'OPTIONS'

            /**
             * Number limit, type limit, time limit (last minute)
             */
            'limit' => [5, 'ip', 'everyday'],

            /**
             * type :: ['header', 'get', 'post']
             * key  :: ['table : Check Key in Database', 'key']
             */
            'key' => ['POST', $this->key() ], // type, {key}|table (by default)
        ]);

        // return data
        $this->api_return(
            [
                'status' => true,
                "result" => "Return API Response",
            ],
            200
        );
    }

    /**
     * view method
     *
     * @link [api/user/view]
     * @method POST
     * @return Response|void
     */
    public function view()
    {
        header("Access-Control-Allow-Origin: *");

        // API Configuration [Return Array: User Token Data]
        $user_data = $this->_apiConfig([
            'methods' => ['POST'],
            'requireAuthorization' => true,
        ]);

        // return data
        $this->api_return(
            [
                'status' => true,
                "result" => [
                    'user_data' => $user_data['token_data']
                ],
            ],
            200
        );
    }
}
