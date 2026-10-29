<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Iclock extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        // Load Biometric model if needed, or query directly for speed
        $this->load->model("Biometric_model");
                $this->load->model("Attendance_model");

    }

    /**
     * Handshake / Init
     * Devices often call /iclock/cdata or /iclock/getrequest to handshake
     * We need to handle parameters like SN (Serial Number)
     */
    public function index()
    {
        echo "OK";
    }

    /**
     * cdata: Receives Data (Attendance Logs, etc.)
     * URL: /iclock/cdata?SN=...&table=ATTLOG&...
     * Device sends data via POST body (often tab-separated content) or GET parameters depending on firmware
     */
    /**
     * cdata: Receives Data (Attendance Logs, etc.)
     * URL: /iclock/cdata?SN=...&table=ATTLOG&c=log
     */
    /**
     * cdata: Receives Data (Attendance Logs, etc.) AND Handles Config/Time Sync
     * URL: /iclock/cdata?SN=...&table=ATTLOG&c=log (POST)
     * URL: /iclock/cdata?SN=...&type=time (GET)
     * URL: /iclock/cdata?SN=...&options=all (GET)
     */
    public function cdata()
    {
        // Force plain text header for all ADMS responses
        $this->output->set_content_type('text/plain');

        $sn = $this->input->get('SN');
        $table = $this->input->get('table');
        $c = $this->input->get('c');
        $type = $this->input->get('type');
        $options = $this->input->get('options');

        $this->_update_device_status($sn);

        // Handle GET requests (Configuration & Time)
        if ($this->input->method() == 'get') {
            if ($type == 'time') {
                // Device expects simpler format often, or just DateTime
                // Reference: Time=YYYY-MM-DDTHH:mm:ss
                // Using Dhaka time (UTC+6) as requested in reference
                $date = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                echo "Time=" . $date->format('Y-m-d\TH:i:s');
                return;
            }

            if ($options) {
                $date = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                $serverTime = $date->format('Y-m-d H:i:s');

                $config = [
                    "GET OPTION FROM: $sn",
                    "Stamp=9999",
                    "OpStamp=9999",
                    "PhotoStamp=9999",
                    "ErrorDelay=60",
                    "Delay=30",
                    "TransTimes=00:00;23:59",
                    "TransInterval=1",
                    "TransFlag=1111000000",
                    "Realtime=1",
                    "Encrypt=0",
                    "PushProtVer=2.4.1",
                    "ServerTime=$serverTime",
                    "TimeZone=6"
                ];
                echo implode("\n", $config);
                return;
            }
        }

        // Handle POST requests (Data Sync)
        // === DEBUG LOGGING START ===
        $logFile = FCPATH . 'iclock_debug.log';
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => $this->input->method(),
            'ip' => $this->input->ip_address(),
            'sn' => $sn,
            'table' => $table,
            'c' => $c,
            'type' => $type,
            'options' => $options,
        ];
        file_put_contents($logFile, "\n=== REQUEST ===" . json_encode($logData, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);
        // === DEBUG LOGGING END ===

        if ($table == 'ATTLOG' || $table == 'attlog') {
            $content = file_get_contents('php://input');

            // === DEBUG: Log raw POST data ===

            if (!empty($content)) {
                $rows = explode("\n", $content);

                $insertCount = 0;
                $skipCount = 0;

                foreach ($rows as $idx => $row) {
                    if (trim($row) == "")
                        continue;

                    // === DEBUG: Log each raw row ===

                    // Parse tab-separated format: EmployeeID	DateTime	Status	Verify	...
                    // Example: FE-0018	2026-02-08 18:02:21	0	15	0	0	0	0	0	0	12458
                    $parts = explode("\t", trim($row));


                    // Check if we have at least employee ID and datetime
                    if (count($parts) >= 2) {
                        $user_id = trim($parts[0]);      // Employee ID (e.g., FE-0018)
                        $check_time = trim($parts[1]);   // DateTime (e.g., 2026-02-08 18:02:21)

                        $data_row = [
                            'PIN' => $user_id,
                            'DateTime' => $check_time
                        ];
                    } else {
                        $data_row = [];
                    }


                    if (isset($data_row['PIN']) && isset($data_row['DateTime'])) {
                        $user_id = $data_row['PIN'];
                        $user_id = str_replace('#', '', $user_id);

                        $check_time = $data_row['DateTime'];

                        $data = array(
                            'proxi_id' => $user_id,
                            'date_time' => $check_time,
                        );

                        $exist = $this->db->where('proxi_id', $user_id)
                            ->where('date_time', $check_time)
                            ->count_all_results('xin_att_machine');

                        if ($exist == 0) {
                            $result = $this->db->insert('xin_att_machine', $data);
                            $employee = $this->db->where('proxi_id', $user_id)->get('xin_proxi')->row();
                            if (!empty($employee) && !empty($employee->emp_id)) {
                                $employeeIds = [$employee->emp_id];
                                $this->Attendance_model->attn_process(date('Y-m-d', strtotime($check_time)), $employeeIds);
                            }
                        } else {
                            $skipCount++;
                        }
                    } else {
                        file_put_contents($logFile, "SKIPPED[$idx]: Missing PIN or DateTime\n", FILE_APPEND);
                    }
                }

                // === DEBUG: Summary ===
                file_put_contents($logFile, "SUMMARY: Inserted=$insertCount, Skipped=$skipCount\n\n", FILE_APPEND);

                echo "OK:" . count($rows); // Return count sometimes helps
            } else {
                file_put_contents($logFile, "EMPTY POST BODY\n\n", FILE_APPEND);
                echo "OK";
            }
        } else {
            // Other tables like USER, OPERLOG
            echo "OK";
        }
    }

    /**
     * getrequest: Device asks for commands
     * URL: /iclock/getrequest?SN=...
     */
    public function getrequest()
    {
        $this->output->set_content_type('text/plain');
        $sn = $this->input->get('SN');
        $this->_update_device_status($sn);
        $logFile = FCPATH . 'iclock_debug.log';

        file_put_contents($logFile, "getting request from device $sn");


        $device = $this->db->where('serial_number', $sn)->get('xin_biometric_devices')->row();

        if ($device) {
            $commands = $this->db->where('device_id', $device->id)
                ->where('status', 0)
                ->order_by('created_at', 'ASC')
                ->limit(1)
                ->get('xin_biometric_commands')
                ->result();

            if (!empty($commands)) {
                foreach ($commands as $cmd) {
                    // Standard ADMS often sends: "C:ID:COMMAND_STRING"
                    echo "C:" . $cmd->id . ":" . $cmd->command_data;

                    $this->db->where('id', $cmd->id)->update('xin_biometric_commands', ['status' => 1]);
                    return;
                }
            }
        }
        echo "OK";
    }

    /**
     * devicecmd: Device reports command execution result
     * URL: /iclock/devicecmd?SN=...&ID=...&Return=...
     */
    public function devicecmd()
    {
        $this->output->set_content_type('text/plain');
        $sn = $this->input->get('SN');
        $id = $this->input->get('ID');
        $ret = $this->input->get('Return');

        $this->_update_device_status($sn);

        if ($id) {
            $status = ($ret >= 0) ? 2 : 3;
            $this->db->where('id', $id)->update('xin_biometric_commands', ['status' => $status, 'executed_at' => date('Y-m-d H:i:s')]);
        }
        echo "OK";
    }

    // Helper to update Last Seen
    private function _update_device_status($sn)
    {
        if ($sn) {
            $device = $this->db->where('serial_number', $sn)->get('xin_biometric_devices')->row();
            if ($device) {
                $this->db->where('id', $device->id)->update('xin_biometric_devices', [
                    'last_seen' => date('Y-m-d H:i:s'),
                    'status' => 1,
                    'ip_address' => $this->input->ip_address() // Update IP if dynamic
                ]);
            } else {
                // Auto-register device? Optional.
                // For now, let's just log or ignore.
                // Ideally we should create it if we want "Plug and Play"
            }
        }
    }
}
