<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate_notice extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->dbforge();
    }

    public function index() {
        echo "Starting Migration...\n";

        // 1. Add 'participant_type' to 'xin_office_notice'
        if (!$this->db->field_exists('participant_type', 'xin_office_notice')) {
            $fields = array(
                'participant_type' => array(
                    'type' => 'INT',
                    'constraint' => 11, // 1: All, 2: Specific
                    'default' => 1,
                    'after' => 'description' 
                )
            );
            $this->dbforge->add_column('xin_office_notice', $fields);
            echo "Added 'participant_type' column to 'xin_office_notice'.\n";
        } else {
            echo "'participant_type' column already exists.\n";
        }

        // 2. Create 'xin_notice_participants' table
        if (!$this->db->table_exists('xin_notice_participants')) {
            $this->dbforge->add_field(array(
                'notice_participant_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ),
                'notice_id' => array(
                    'type' => 'INT',
                    'constraint' => 11
                ),
                'employee_id' => array(
                    'type' => 'INT',
                    'constraint' => 11
                )
            ));
            $this->dbforge->add_key('notice_participant_id', TRUE);
            $this->dbforge->create_table('xin_notice_participants');
            echo "Created 'xin_notice_participants' table.\n";
        } else {
            echo "'xin_notice_participants' table already exists.\n";
        }

        echo "Migration Completed.\n";
    }
}
