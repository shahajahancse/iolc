<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class New_movement_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function create_movement($data)
    {
        $this->db->insert('new_movement_movements', $data);
        return $this->db->insert_id();
    }

    public function get_active_movement($employee_id)
    {
        $this->db->where('employee_id', $employee_id);
        $this->db->where('status', 'active');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('new_movement_movements');
        return $query->row();
    }

    public function get_movement_by_id($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('new_movement_movements');
        return $query->row();
    }

    public function get_today_movements($employee_id)
    {
        $today = date('Y-m-d');
        $this->db->where('employee_id', $employee_id);
        $this->db->like('created_at', $today);
        $query = $this->db->get('new_movement_movements');
        return $query->result();
    }

    public function get_all_movements($employee_id = null, $start_date = null, $end_date = null)
    {
        $this->db->select('new_movement_movements.*, xin_employees.first_name, xin_employees.last_name');
        $this->db->join('xin_employees', 'xin_employees.user_id = new_movement_movements.employee_id', 'left');

        if ($employee_id) {
            $this->db->where('new_movement_movements.employee_id', $employee_id);
        }

        if ($start_date && $end_date) {
            $this->db->where('DATE(new_movement_movements.created_at) >=', $start_date);
            $this->db->where('DATE(new_movement_movements.created_at) <=', $end_date);
        }

        $this->db->order_by('new_movement_movements.id', 'DESC');
        $query = $this->db->get('new_movement_movements');
        return $query->result();
    }

    public function get_ta_list($start_date = null, $end_date = null, $ta_status = null, $employee_id = null)
    {
        $this->db->select('new_movement_movements.*, xin_employees.first_name, xin_employees.last_name');
        $this->db->join('xin_employees', 'xin_employees.user_id = new_movement_movements.employee_id', 'left');
        $this->db->where('new_movement_movements.ta_status !=', 'not_applied');

        if (!empty($ta_status)) {
            $this->db->where('new_movement_movements.ta_status', $ta_status);
        }
        if (!empty($employee_id)) {
            $this->db->where('new_movement_movements.employee_id', $employee_id);
        }
        if ($start_date && $end_date) {
            $this->db->where('DATE(new_movement_movements.created_at) >=', $start_date);
            $this->db->where('DATE(new_movement_movements.created_at) <=', $end_date);
        }

        // if ($start_date && $end_date) {
        //     $this->db->where('new_movement_movements.created_at >=', $start_date);
        //     $this->db->where('new_movement_movements.created_at <=', $end_date);
        // }

        $this->db->order_by('new_movement_movements.id', 'DESC');
        $query = $this->db->get('new_movement_movements');
        return $query->result();
    }

    public function get_ta_listssss($start_date = null, $end_date = null, $ta_status = null, $employee_id = null)
    {
        $this->db->select('new_movement_movements.*, xin_employees.first_name, xin_employees.last_name');
        $this->db->from('new_movement_movements');
        $this->db->join(
            'xin_employees',
            'xin_employees.user_id = new_movement_movements.employee_id',
            'left'
        );

        $this->db->where('new_movement_movements.ta_status !=', 'not_applied');
        if (!empty($ta_status)) {
            $this->db->where('new_movement_movements.ta_status', $ta_status);
        }
        if (!empty($employee_id)) {
            $this->db->where('new_movement_movements.employee_id', $employee_id);
        }
        if ($start_date && $end_date) {
            $this->db->where('new_movement_movements.created_at >=', $start_date);
            $this->db->where('new_movement_movements.created_at <=', $end_date);
        }

        $this->db->order_by('new_movement_movements.id', 'DESC');
        return $this->db->get()->result();
    }

    public function get_ta_summary($start_date = null, $end_date = null, $ta_status = null, $user_id = null)
    {
        $this->db->select('
            mv.employee_id,
            COUNT(mv.id) AS total_movements,
            SUM(mv.ta_amount) AS applyed_amount, SUM(mv.ta_app_amt) AS approved_amount,
            em.first_name, em.last_name
        ', false);

        $this->db->from('new_movement_movements mv');
        $this->db->join('xin_employees em', 'em.user_id = mv.employee_id', 'left');

        // Date filter (index friendly)
        $this->db->where('mv.end_time <=', $start_date);
        $this->db->where('mv.end_time >=', $end_date);

        // ✅ TA status filter
        if (!empty($ta_status)) {
            $this->db->where('mv.ta_status', $ta_status);
        }

        // ✅ Employee filter
        if (!empty($user_id)) {
            $this->db->where('mv.employee_id', $user_id);
        }

        // ✅ Correct date range
        // if (!empty($start_date) && !empty($end_date)) {
        //     $this->db->where('mv.end_time >=', $start_date);
        //     $this->db->where('mv.end_time <=', $end_date);
        // }

        $this->db->group_by('mv.employee_id');
        $this->db->order_by('mv.employee_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_ta_summary_ssss($start_date = null, $end_date = null, $ta_status = null, $user_id = null)
    {
        $this->db->select('
            mv.employee_id,
            COUNT(mv.id) AS total_movements,
            SUM(mv.ta_amount) AS applyed_amount,
            SUM(mv.ta_app_amt) AS approved_amount,
            em.first_name,
            em.last_name
        ', false);

        $this->db->from('new_movement_movements mv');
        $this->db->join('xin_employees em', 'em.user_id = mv.employee_id', 'left');

        // ✅ TA status filter
        if (!empty($ta_status)) {
            $this->db->where('mv.ta_status', $ta_status);
        }

        // ✅ Employee filter
        if (!empty($user_id)) {
            $this->db->where('mv.employee_id', $user_id);
        }

        // ✅ Correct date range
        if (!empty($start_date) && !empty($end_date)) {
            $this->db->where('mv.end_time >=', $start_date);
            $this->db->where('mv.end_time <=', $end_date);
        }

        $this->db->group_by('mv.employee_id');
        $this->db->order_by('mv.employee_id', 'DESC');

        $query = $this->db->get();
        return $query->result();
    }

    public function get_ta_summary_details_by_user($start_date = null, $end_date = null, $ta_status = null, $user_id = null)
    {
        $this->db->select('mv.*', false);
        $this->db->from('new_movement_movements mv');
        $this->db->where('mv.ta_status', $ta_status);

        if (!empty($user_id)) {
            $this->db->where('mv.employee_id', $user_id);
        }
        // Date filter (index friendly)
        $this->db->where('mv.end_time <=', $start_date);
        $this->db->where('mv.end_time >=', $end_date);
        $this->db->order_by('mv.id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_travel_with_expense($movement_id)
    {
        $this->db->select('tex.id, tex.travel_id, t.from_location, t.to_location, tex.transport_type, tex.amount, tex.approve_amount', false);
        $this->db->from('new_movement_travel_expenses tex');
        $this->db->join('new_movement_travels t', 't.id = tex.travel_id', 'left');
        $this->db->where('tex.movement_id', $movement_id);
        $this->db->order_by('tex.id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_ta_stats($employee_id = null, $start_date = null, $end_date = null)
    {
        $stats = [
            'total_ta' => 0,
            'pending_ta' => 0,
            'approved_ta' => 0,
            'handed_over_ta' => 0
        ];

        // Helper to query sum
        $this->db->select('SUM(amount) as total');
        $this->db->from('new_movement_travel_expenses');
        $this->db->join('new_movement_movements', 'new_movement_movements.id = new_movement_travel_expenses.movement_id');
        if ($employee_id)
            $this->db->where('new_movement_movements.employee_id', $employee_id);
        if ($start_date && $end_date) {
            $this->db->where('DATE(new_movement_movements.created_at) >=', $start_date);
            $this->db->where('DATE(new_movement_movements.created_at) <=', $end_date);
        }
        $total = $this->db->get()->row()->total;
        $stats['total_ta'] = $total ? $total : 0;

        // Pending (pending OR hr_approved)
        $this->db->select('SUM(amount) as total');
        $this->db->from('new_movement_travel_expenses');
        $this->db->join('new_movement_movements', 'new_movement_movements.id = new_movement_travel_expenses.movement_id');
        $this->db->where_in('new_movement_movements.ta_status', ['pending', 'hr_approved']);
        if ($employee_id)
            $this->db->where('new_movement_movements.employee_id', $employee_id);
        if ($start_date && $end_date) {
            $this->db->where('DATE(new_movement_movements.created_at) >=', $start_date);
            $this->db->where('DATE(new_movement_movements.created_at) <=', $end_date);
        }
        $total = $this->db->get()->row()->total;
        $stats['pending_ta'] = $total ? $total : 0;

        // Approved (approved - Final/Super Admin Only)
        $this->db->select('SUM(amount) as total');
        $this->db->from('new_movement_travel_expenses');
        $this->db->join('new_movement_movements', 'new_movement_movements.id = new_movement_travel_expenses.movement_id');
        $this->db->where('new_movement_movements.ta_status', 'approved');
        if ($employee_id)
            $this->db->where('new_movement_movements.employee_id', $employee_id);
        if ($start_date && $end_date) {
            $this->db->where('DATE(new_movement_movements.created_at) >=', $start_date);
            $this->db->where('DATE(new_movement_movements.created_at) <=', $end_date);
        }
        $total = $this->db->get()->row()->total;
        $stats['approved_ta'] = $total ? $total : 0;

        // Handed Over
        $this->db->select('SUM(amount) as total');
        $this->db->from('new_movement_travel_expenses');
        $this->db->join('new_movement_movements', 'new_movement_movements.id = new_movement_travel_expenses.movement_id');
        $this->db->where('new_movement_movements.ta_status', 'handed_over');
        if ($employee_id)
            $this->db->where('new_movement_movements.employee_id', $employee_id);
        if ($start_date && $end_date) {
            $this->db->where('DATE(new_movement_movements.created_at) >=', $start_date);
            $this->db->where('DATE(new_movement_movements.created_at) <=', $end_date);
        }
        $total = $this->db->get()->row()->total;
        $stats['handed_over_ta'] = $total ? $total : 0;

        return $stats;
    }

    public function end_movement($id, $end_time)
    {
        $this->db->where('id', $id);
        return $this->db->update('new_movement_movements', [
            'end_time' => $end_time,
            'status' => 'completed'
        ]);
    }
    public function get_expenses_by_movement($movement_id)
    {
        $this->db->where('movement_id', $movement_id);
        $this->db->order_by('id', 'ASC');
        return $this->db->get('new_movement_travel_expenses')->result();
    }

    public function get_detailed_status($movement_id)
    {
        // Check for active travel
        $this->db->where('movement_id', $movement_id);
        $this->db->where('status', 'running');
        $travel = $this->db->get('new_movement_travels')->row();
        if ($travel) {
            return [
                'status' => 'Traveling',
                'location' => $travel->from_location . ' ➔ ' . ($travel->to_location ? $travel->to_location : 'Destination'),
                'icon' => 'fa-car',
                'lat' => $travel->start_lat,
                'lng' => $travel->start_lng
            ];
        }

        // Check for active meeting
        $this->db->where('movement_id', $movement_id);
        $this->db->where('end_time', NULL);
        $meeting = $this->db->get('new_movement_meetings')->row();
        if ($meeting) {
            return [
                'status' => 'Meeting',
                'location' => 'With ' . $meeting->client_name . ' at ' . $meeting->location,
                'icon' => 'fa-users',
                'lat' => $meeting->latitude,
                'lng' => $meeting->longitude
            ];
        }

        // Default active but idle
        return [
            'status' => 'Active',
            'location' => 'In Transit / Decision',
            'icon' => 'fa-clock-o',
        ];
    }

    public function get_report_data($start_date, $end_date, $user_id = null)
    {
        $this->db->select('
            m.*,
            e.first_name, e.last_name,
            (SELECT COUNT(*) FROM new_movement_meetings WHERE movement_id = m.id) as meeting_count,
            (SELECT COUNT(*) FROM new_movement_travels WHERE movement_id = m.id) as travel_count
        ');
        $this->db->from('new_movement_movements m');
        $this->db->join('xin_employees e', 'e.user_id = m.employee_id', 'left');

        // Date Filter
        if ($start_date && $end_date) {
            $this->db->where('DATE(m.created_at) >=', $start_date);
            $this->db->where('DATE(m.created_at) <=', $end_date);
        }

        // User Filter
        if ($user_id) {
            $this->db->where('m.employee_id', $user_id);
        }

        $this->db->order_by('m.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get_report_data_v2($start_date, $end_date, $emp_ids = null, $ta_status = null)
    {
        $this->db->select('
            m.*,
            e.first_name, e.last_name, e.employee_id,
            (SELECT COUNT(*) FROM new_movement_meetings WHERE movement_id = m.id) as meeting_count,
            (SELECT COUNT(*) FROM new_movement_travels WHERE movement_id = m.id) as travel_count,
            (SELECT SUM(amount) FROM new_movement_travel_expenses WHERE movement_id = m.id) as total_expense
        ');
        $this->db->from('new_movement_movements m');
        $this->db->join('xin_employees e', 'e.user_id = m.employee_id', 'left');

        // Date Filter
        if ($start_date && $end_date) {
            $this->db->where('DATE(m.created_at) >=', $start_date);
            $this->db->where('DATE(m.created_at) <=', $end_date);
        }

        // User Filter (Array)
        if ($emp_ids && is_array($emp_ids) && count($emp_ids) > 0) {
            $this->db->where_in('m.employee_id', $emp_ids);
        }

        $this->db->order_by('m.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get_report_ta($start_date, $end_date, $emp_ids = null, $ta_status = null)
    {
        $this->db->select('
            m.*,
            e.first_name, e.last_name, e.employee_id,
        ');
        $this->db->from('new_movement_movements m');
        $this->db->join('xin_employees e', 'e.user_id = m.employee_id', 'left');

        // Date Filter
        if ($start_date && $end_date) {
            $this->db->where('DATE(m.created_at) >=', $start_date);
            $this->db->where('DATE(m.created_at) <=', $end_date);
        }

        // User Filter (Array)
        if (!empty($emp_ids) && is_array($emp_ids)) {
            $this->db->where_in('m.employee_id', $emp_ids);
        }

        // TA Status Filter
        if (!empty($ta_status)) {
            $this->db->where('m.ta_status', $ta_status);
        } else {
            $this->db->where('m.ta_status !=', 'not_applied');
        }

        $this->db->order_by('m.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function ta_summary_report($start_date, $end_date, $ta_status = null, $emp_ids = null)
    {
        $this->db->select('
            mv.employee_id,
            COUNT(mv.id) AS total_movements,
            SUM(mv.ta_amount) AS applyed_amount,
            SUM(mv.ta_app_amt) AS approved_amount,
            em.first_name, em.last_name
        ', false);

        $this->db->from('new_movement_movements mv');
        $this->db->join('xin_employees em', 'em.user_id = mv.employee_id', 'left');

        // Date filter (index friendly)
        if ($start_date && $end_date) {
            $this->db->where('DATE(mv.created_at) >=', $start_date);
            $this->db->where('DATE(mv.created_at) <=', $end_date);
        }

        // ✅ TA status filter
        if (!empty($ta_status)) {
            $this->db->where('mv.ta_status', $ta_status);
        } else {
            $this->db->where('mv.ta_status !=', 'not_applied');
        }

        // ✅ Employee filter
        if (!empty($emp_ids) && is_array($emp_ids)) {
            $this->db->where_in('mv.employee_id', $emp_ids);
        }

        $this->db->group_by('mv.employee_id');
        $this->db->order_by('mv.employee_id', 'DESC');
        return $this->db->get()->result();
    }

    public function get_all_active_movements()
    {
        $this->db->select('m.*, e.first_name, e.last_name, e.profile_picture');
        $this->db->from('new_movement_movements m');
        $this->db->join('xin_employees e', 'e.user_id = m.employee_id', 'left');
        $this->db->where('m.status', 'active');
        $movements = $this->db->get()->result();

        // Enrich with detailed status
        foreach ($movements as &$move) {
            $details = $this->get_detailed_status($move->id);
            $move->current_status = $details['status'];
            $move->current_location = $details['location'];
            $move->icon = $details['icon'];
            $move->lat = isset($details['lat']) ? $details['lat'] : $move->start_latitude;
            $move->lng = isset($details['lng']) ? $details['lng'] : $move->start_longitude;
        }
        return $movements;
    }

    public function get_admin_dashboard_stats($start_date = null, $end_date = null)
    {
        $this->db->select("
            COUNT(*) AS total_movements,
            SUM(status != 'completed') AS total_users_active,
            SUM(status = 'completed') AS completed_movements,
            SUM(ta_amount) AS total_ta_claimed,
            SUM(ta_status IN ('pending','hr_approved')) AS pending_ta_approval
        ", false);
        $this->db->from('new_movement_movements');
        // Date filter (index friendly)
        if ($start_date && $end_date) {
            $this->db->where('created_at >=', $start_date . ' 00:00:00');
            $this->db->where('created_at <=', $end_date . ' 23:59:59');
        }
        $row = $this->db->get()->row();

        return [
            'total_movements'       => (int) $row->total_movements,
            'total_users_active'    => (int) $row->total_users_active,
            'completed_movements'   => (int) $row->completed_movements,
            'total_ta_claimed'      => (float) ($row->total_ta_claimed ? $row->total_ta_claimed : 0),
            'pending_ta_approval'   => (int) $row->pending_ta_approval
        ];
    }

    public function get_todays_birthdays()
    {
        $date = date('d-m');
        $this->db->select('user_id, first_name, last_name, profile_picture, date_of_birth, designation_id');
        $this->db->from('xin_employees');
        $this->db->where('is_active', 1);
        $this->db->like('date_of_birth', '-' . $date);
        return $this->db->get()->result();
    }

    public function get_todays_movement_meetings()
    {
        $today = date('Y-m-d');
        $this->db->select('m.*, mov.employee_id, e.first_name, e.last_name, e.profile_picture');
        $this->db->from('new_movement_meetings m');
        $this->db->join('new_movement_movements mov', 'mov.id = m.movement_id');
        $this->db->join('xin_employees e', 'e.user_id = mov.employee_id', 'left');
        $this->db->where('DATE(m.created_at)', $today);
        $this->db->order_by('m.id', 'DESC');
        return $this->db->get()->result();
    }
}
