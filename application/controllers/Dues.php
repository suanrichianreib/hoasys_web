<?php

use Mpdf\Tag\P;

defined('BASEPATH') or exit('No direct script access allowed');
require_once(dirname(__FILE__) . "/General.php");
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

class Dues extends General
{
    protected $title = 'Dues';
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        if ($this->session->userdata("id_admin")) {
            $date_time = $this->get_current_date_time();
            $data["date_time"] = $date_time["dateTime"];
            $role = $this->session->userdata("role");
            $data['title'] = $this->title;
            $this->load_template_view('templates/dues/dues', $data);
        } else {
            redirect(base_url('Login'));
        }
    }
    public function get_dues()
    {
        $datatable = $this->input->post('datatable');
        $query['search']['append'] = "";
        $query['search']['total'] = "";
        $where_name = "";
        $order = " lname ";
        $month_where = "";
        $year_where = "";
        $stat_where = "";
        $month = $datatable['query']['month'];
        $year = $datatable['query']['year'];
        $status = $datatable['query']['stat'];
        // var_dump($month);

        if (!empty($month) && trim($month) !== 'All') {
            $month_where = " AND r.month_record = '".$month."'";
        }
        if (!empty($year) && trim($year) !== 'All') {
            $year_where =  " AND r.year_record = '".$year."'";
        }
        if (!empty($status) && trim($status) !== 'All') {
            $stat_where = " AND r.status_record = '".$status."'";
        }
        

        $query['query'] = "SELECT h.*,r.*, CONCAT(h.fname,' ', h.mname, ' ', h.lname) as fullname FROM tbl_records r, tbl_homeowner h WHERE h.id_ho = r.id_ho ".$month_where."".$year_where."".$stat_where;
        if ($datatable['query']['searchField'] != '') {
            $keyword = $datatable['query']['searchField'];
        $where = "(lname LIKE '%" . $keyword . "%' OR fname LIKE '%" . $keyword . "%')";
            $query['search']['append'] = " AND ($where)";
            $query['search']['total'] = " AND ($where)";
        }
        $page = $datatable['pagination']['page'];
        $pages = $datatable['pagination']['page'] * $datatable['pagination']['perpage'];
        $perpage = $datatable['pagination']['perpage'];
        $sort = (isset($datatable['sort']['sort'])) ? $datatable['sort']['sort'] : '';
        $field = (isset($datatable['sort']['field'])) ? $datatable['sort']['field'] : '';
        if (isset($query['search']['append'])) {
            $query['query'] .= $query['search']['append'];
            $search = $query['query'] . $query['search']['total'];
            $total = count($this->general_model->custom_query($search));
            $pages = ceil($total / $perpage);
            $page = ($page > $pages) ? 1 : $page;
        } else {
            $total = count($this->general_model->custom_query($query['query']));
        }
        if (isset($datatable['pagination'])) {
            $offset = $page * $perpage - $perpage;
            $limit = ' LIMIT ' . $offset . ' ,' . $perpage;
            // $order = $field ? " ORDER BY  " . $field : '';
            $order = $field ? " ORDER BY  " . $order : '';
            if ($perpage < 0) {
                $limit = ' LIMIT 0';
            }
            $query['query'] .= $order . ' ' . $sort . $limit;
        }
        $data = $this->general_model->custom_query($query['query']);
        $meta = [
            "page" => intval($page),
            "pages" => intval($pages),
            "perpage" => intval($perpage),
            "total" => $total,
            "sort" => $sort,
            "field" => $field,
        ];
        echo json_encode(['meta' => $meta, 'data' => $data]);
    }
    public function create_billing(){
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $type = $this->input->post('type');
        $id = $this->input->post('id');
        $can_Add = 0;
        $homeid_arr = array();
        $loop = 0;

        if($type == 1){
              // check if existing ? 
        $check = $this->general_model->custom_query('SELECT * FROM `tbl_billing` WHERE month = "'.$month.'" AND year = '.$year);
        if(count($check) > 0){
            $can_Add = 0;
        }else{
            $billing['month'] =   $month;
            $billing['year'] =  $year;
            $homeowner_IDs = $this->general_model->custom_query('SELECT h.homeownerID, p.paymentID,p.payment FROM tbl_homeowners h, tbl_payment p WHERE h.status = "active" AND h.homeownerID = p.homeownerID ');
            
            if(count($homeowner_IDs) > 0){
                $can_Add = 1;
                $billing_id = $this->general_model->insert_vals_last_inserted_id($billing, "tbl_billing");
                foreach ($homeowner_IDs as $bil) {
                    $homeid_arr[$loop] = [
                        'homeownerID' => $bil->homeownerID,
                        'billingID' => $billing_id,
                        'payment' => $bil->payment,
                        'status' => "unpaid"
                    ];
                    $loop++;
                }
                $this->general_model->batch_insert($homeid_arr, 'tbl_billing_homeowner');
            }else{
                // cannot add billing since theres no homeowners active 
                $can_Add = 2;
            }
        }
        }else{
            $check = $this->general_model->custom_query('SELECT * FROM `tbl_billing` WHERE month = "'.$month.'" AND year = '.$year);
            if(count($check) > 0){

                // Check if existing in this homeowner
                $bD = $check[0]->billingID;
                $checkH = $this->general_model->custom_query('SELECT * FROM `tbl_billing_homeowner` WHERE billingID = '.$bD.' AND homeownerID = '.$id);
                if(count($checkH) > 0){
                    $can_Add = 0;
                }else{
                    $can_Add = 1;
                    $payment = $this->general_model->custom_query('SELECT * FROM `tbl_payment` WHERE homeownerID = '.$id);
                    $save['homeownerID'] = $id;
                    $save['billingID'] = $bD;
                    $save['payment'] = $payment[0]->payment;
                    $save['status'] = "unpaid";
                    $this->general_model->insert_vals($save, "tbl_billing_homeowner");
                }
            }else{
                $can_Add = 0;
            }
        }
        echo json_encode($can_Add);
    }
    public function delete_billing(){
        $id = $this->input->post('id');
        $where_del = 'bhomeID = '.$id;
        $this->general_model->delete_vals($where_del, 'tbl_billing_homeowner');
    }
    public function confirm_billing(){
        $id = $this->input->post('id');
        $home['status'] =  "paid";
        $this->general_model->update_vals($home, "bhomeID = $id", "tbl_billing_homeowner");
    }
    public function email_sending_reminder()
    {
        $em = $this->input->post('email');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $payment = $this->input->post('payment');
        $this->load->library('email');
        $ser = 'http://' . $_SERVER['SERVER_NAME'];
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.gmail.com',
            'smtp_timeout' => 30,
            'smtp_port' => 465,
            'smtp_user' => 'ggn1cdo@gmail.com',
            'smtp_pass' => 'asklaymjpayxhkyi',
            'charset' => 'utf-8',
            'mailtype' => 'html',
            'newline' => '\r\n'
        );

        $message = "Hi! This is a reminder that you still have to pay your dues for ".$month." ".$year.". Your payment is: ".$payment;
        $this->email->initialize($config);
        $this->email->set_newline("\r\n");
        $this->email->set_crlf("\r\n");
        $this->email->from("ggn1cdo@gmail.com");
        $this->email->to($em);
        $this->email->subject("GGN1 Account Verification");
        $this->email->message($message);
        if ($this->email->send()) {
            echo "Mail successful";
        } else {
            echo "Sorry";
            print_r($this->email->print_debugger());
        }
    }
    public function create_billing_all()
    {
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $homeowner['created'] = 1; 
        // check billing if existing
        $res = $this->general_model->custom_query("Select billingID FROM tbl_billing_month_year  WHERE month = '$month' AND year = '$year'");

        if (count($res) > 0) {
            // if existing 
            // Get All homeowners that has billing 
            $homeowner['billing_id'] = $res[0]->billingID; 
            $id_records_has_billing = $this->general_model->custom_query("SELECT id_ho FROM `tbl_records` WHERE year_record = '$year' AND month_record = '$month' ");
            if (count($id_records_has_billing) > 0) {
                $excludedIds = array_map(function ($item) {
                    return $item->id_ho;
                }, $id_records_has_billing);

                $sql_ex = "SELECT id_ho FROM tbl_homeowner WHERE status = 'active'";

                // Checking if there are ids to exclude
                if (!empty($excludedIds)) {
                    // Adding NOT IN clause to exclude ids from $array1
                    $sql_ex .= " AND id_ho NOT IN (" . implode(',', $excludedIds) . ")";
                }

                // fetch sa active homeowners nga walay in ani nga billing
                $tobe_added_homeowner = $this->general_model->custom_query($sql_ex);
                // Get otherdetails

                if (count($tobe_added_homeowner) > 0) {
                    // Naa pay mga active homeowners nga need pa butangan ani nga billing
                    $id_ho_values = array_map(function ($obj) {
                        return $obj->id_ho;
                    }, $tobe_added_homeowner);

                    // Get all the homeowners ID that needs to be inserted with tbl_record
                    // Convert the array values to a comma-separated string
                    $id_ho_in_clause = implode(',', $id_ho_values);

                    // Modify your query with the generated IN clause
                    $query_details = "SELECT h.id_ho, hm.id_ho_monthly, hm.monthly, hm.duedate
              FROM tbl_homeowner h, tbl_homeowner_monthly hm
              WHERE h.status = 'active' AND h.id_ho = hm.id_ho AND h.id_ho IN ($id_ho_in_clause)";
                    $result_ho_details = $this->general_model->custom_query($query_details);

                    // Add records 
                    $data_records = array();
                    foreach ($result_ho_details as $item) {
                        $record_ho = array(
                            'year_record' => $year, 
                            'month_record' => $month, 
                            'status_record' => "pending",
                            'id_ho' => $item->id_ho,
                            'id_admin' => $this->session->userdata("id_admin"),
                            'duedate_record' => $item->duedate,
                            'paid_amount' => $item->monthly,
                            'id_ho_monthly' => $item->id_ho_monthly,
                            'billing_id' => $res[0]->billingID
                        );

                        $data_records[] = $record_ho;
                    }
                    $this->general_model->batch_insert($data_records, "tbl_records");
                    $can_Add = 2;
                }else{
                    // Wala nay pwede ma addan ani nga billing 
                    $can_Add = 3;
                }
            } else {
                // Should create billing to all homeowners
                $this->insert_all_homeowners($res[0]->billingID, $month, $year);
                $can_Add = 4;
            }
        } else {
            // ADDS BILLING RECORD
            $data = array(
                'month'     => $month,
                'year'     =>  $year,
            );

            $billing_id = $this->general_model->insert_vals_last_inserted_id($data, "tbl_billing_month_year");
            $this->insert_all_homeowners($billing_id, $month, $year);
            $can_Add = 1;
        }
        echo json_encode($can_Add);
    }
    public function insert_all_homeowners($billing_id, $month, $year)
    {
        // GET ALL HOMEOWNERS ID 
        $homeowners = $this->general_model->custom_query("SELECT h.id_ho,hm.id_ho_monthly,hm.monthly,hm.duedate  FROM tbl_homeowner h, tbl_homeowner_monthly hm WHERE h.status = 'active' AND h.id_ho = hm.id_ho");
        $data = array();
        foreach ($homeowners as $item) {
            $record = array(
                'year_record' => $year, 
                'month_record' => $month,
                'status_record' => "pending",
                'id_ho' => $item->id_ho,
                'id_admin' => $this->session->userdata("id_admin"),
                'duedate_record' => $item->duedate,
                'paid_amount' => $item->monthly,
                'id_ho_monthly' => $item->id_ho_monthly,
                'billing_id' => $billing_id
            );

            $data[] = $record;
        }
        $this->general_model->batch_insert($data, "tbl_records");
    }
    public function fetch_homeowners_options(){
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $homeowner['return'] = 1;
        // check billing if existing
        $res = $this->general_model->custom_query("Select billingID FROM tbl_billing_month_year  WHERE month = '$month' AND year = '$year'");
        if (count($res) > 0) {
            // Na create na nga billing
            // Identify ang mga homeowners nga naa nay in ani nga billing
            $homeowner['bid'] = $res[0]->billingID;
            $homeowner['created'] = 1;
            $id_records_has_billing = $this->general_model->custom_query("SELECT id_ho FROM `tbl_records` WHERE year_record = '$year' AND month_record = '$month' ");
            if (count($id_records_has_billing) > 0) {
                // Naay employees nga naa ani nga billing 
                $excludedIds = array_map(function ($item) {
                    return $item->id_ho;
                }, $id_records_has_billing);

                $sql_ex = "SELECT id_ho as id,CONCAT(lname,', ',fname,' ',mname) as text  FROM tbl_homeowner WHERE status = 'active'";

                // Checking if there are ids to exclude
                if (!empty($excludedIds)) {
                    // Adding NOT IN clause to exclude ids from $array1
                    $sql_ex .= " AND id_ho NOT IN (" . implode(',', $excludedIds) . ")";
                }
                // fetch sa active homeowners nga walay in ani nga billing
                $tobe_added_homeowner = $this->general_model->custom_query($sql_ex);

                if (count($tobe_added_homeowner) > 0) {
                    // Naay walay in ani nga billing
                    $homeowner['opt'] = $tobe_added_homeowner;
                }else{
                    // Na addan na tana employee so dapat wala na syay i return nga options 
                    $homeowner['return'] = 0;
                }
                // Get otherdetails
            }else{
            //walay employees nga naay record ani na billing
        $homeowner['opt'] = $this->general_model->custom_query("SELECT id_ho as id,CONCAT(lname,', ',fname,' ',mname) as text FROM `tbl_homeowner` WHERE status = 'active'");    
            }

        }else{
        // Get all homeowners since this billing is not yet created 
        $homeowner['opt'] = $this->general_model->custom_query("SELECT id_ho as id,CONCAT(lname,', ',fname,' ',mname) as text FROM `tbl_homeowner` WHERE status = 'active'");    
        $homeowner['created'] = 0;
    }   
        echo json_encode($homeowner);
    }
    public function create_billing_ho(){
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $homeowners = $this->input->post('homeowners');
        $ho_str = implode(',', $homeowners);

        $res = $this->general_model->custom_query("Select billingID FROM tbl_billing_month_year  WHERE month = '$month' AND year = '$year'");
        if (count($res) > 0) {
            // Naay billing month and year
            $bil_id = $res[0]->billingID;
        }else{
            $data = array(
                'month'     => $month,
                'year'     =>  $year,
            );
            $bil_id = $this->general_model->insert_vals_last_inserted_id($data, "tbl_billing_month_year"); 
        }

        // $id_ho_values = array_map(function ($obj) {
        //     return $obj->id_ho;
        // }, $homeowners);

        // Get all the homeowners ID that needs to be inserted with tbl_record
        // Convert the array values to a comma-separated string
        // $id_ho_in_clause = implode(',', $id_ho_values);

        // Modify your query with the generated IN clause
        $query_details = "SELECT h.id_ho, hm.id_ho_monthly, hm.monthly, hm.duedate
  FROM tbl_homeowner h, tbl_homeowner_monthly hm
  WHERE h.status = 'active' AND h.id_ho = hm.id_ho AND h.id_ho IN ($ho_str)";
        $result_ho_details = $this->general_model->custom_query($query_details);

        // Add records 
        $data_records = array();
        foreach ($result_ho_details as $item) {
            $record_ho = array(
                'year_record' => $year, 
                'month_record' => $month, 
                'status_record' => "pending",
                'id_ho' => $item->id_ho,
                'id_admin' => $this->session->userdata("id_admin"),
                'duedate_record' => $item->duedate,
                'paid_amount' => $item->monthly,
                'id_ho_monthly' => $item->id_ho_monthly,
                'billing_id' => $bil_id
            );

            $data_records[] = $record_ho;
        }
        $this->general_model->batch_insert($data_records, "tbl_records");
    }
    public function get_details_dues_per_homeowner(){
        $id = $this->input->post('id');
        $id_rec = $this->input->post('record_id');
        $details = $this->general_model->custom_query("SELECT h.*,r.*, CONCAT(h.fname,' ', h.mname, ' ', h.lname) as fullname FROM tbl_records r, tbl_homeowner h WHERE h.id_ho = r.id_ho AND h.id_ho = $id AND r.id_record = $id_rec");
        echo json_encode($details);
    }
    public function update_record_status()
    {
        $date_time = $this->get_current_date_time();
        $id_rec = $this->input->post('record_id');
        $stat = $this->input->post('status');
        $records['status_record'] =  $stat;
        $records['date_updated'] =  $date_time["dateTime"];
        $this->general_model->update_vals($records, "id_record = $id_rec", "tbl_records");

        $admin_name = $this->session->userdata("fullname");
        $ho_records = $this->get_records_for_logs($id_rec);
        $message = $admin_name . ' changed dues status of ' . $ho_records[0]->fullname . ' to ' . $stat . ' for ' . $ho_records[0]->month_record . ' ' . $ho_records[0]->year_record . ' dues.';
        $this->activity_log('dues', $id_rec, $message);
    }
    public function update_penalty()
    {
        $id_rec = $this->input->post('record_id');
        $penalty = $this->input->post('penalty');
        if ($penalty == "0" || $penalty == "") {
            $penalty == null;
        }
        $records['penalty'] =  $penalty;
        $this->general_model->update_vals($records, "id_record = $id_rec", "tbl_records");

        $admin_name = $this->session->userdata("fullname");
        $penalty_records = $this->get_records_for_logs($id_rec);
        $message = $admin_name . ' added penalty of ' . $penalty . ' to homeowner' . $penalty_records[0]->fullname . ' for ' . $penalty_records[0]->month_record . ' ' . $penalty_records[0]->year_record . ' dues.';
        $this->activity_log('dues', $id_rec, $message);
    }
    public function delete_record()
    {
        $id_rec = $this->input->post('record_id');
        $admin_name = $this->session->userdata("fullname");
        $penalty_records = $this->get_records_for_logs($id_rec);
        $message = $admin_name . ' removed/deleted  due record of ' . $penalty_records[0]->fullname . ' for ' . $penalty_records[0]->month_record . ' ' . $penalty_records[0]->year_record . '.';
        $this->activity_log('dues', $id_rec, $message);
        $this->general_model->delete_vals("id_record = $id_rec", "tbl_records");
    }
    public function send_billing_dues()
    {
        $record_id = $this->input->post('record_id');
        $ho_id = $this->input->post('ho_id');
        $billing_info = $this->general_model->custom_query("SELECT h.*,r.*, CONCAT(h.fname,' ', h.mname, ' ', h.lname) as fullname, h.email_add FROM tbl_records r, tbl_homeowner h WHERE h.id_ho = r.id_ho AND r.id_record =" . $record_id . " AND r.id_ho = " . $ho_id . " ");
        $subject = $billing_info[0]->fullname . " | Billing for " . $billing_info[0]->month_record . " " . $billing_info[0]->year_record . ".";
        $month_year = $billing_info[0]->month_record . " " . $billing_info[0]->year_record;
        $fullname = $billing_info[0]->fullname;
        $paid_amount = $billing_info[0]->paid_amount;
        $penalty = 0;
        $total = $paid_amount;
        if ($billing_info[0]->penalty != null) {
            $penalty = $billing_info[0]->penalty;
            $total = $paid_amount + $penalty;
        }
        $email = "Hi $fullname,<br><br>

        As part of our ongoing commitment to transparency and effective communication, we are pleased to provide you with your monthly billing statement for $month_year.<br><br>

        Below, you will find a detailed breakdown of your charges: <br><br>

        MONTHLY PAYMENT = $paid_amount<br>
        PENALTY =  $penalty <br>
        TOTAL PAYMENT = $total <br><br>

        If you have any questions or need assistance, feel free to contact the homeowners' association by sending concern through the website or by replying this email.<br><br>

        Best regards,<br>
        Hoasys Admin
    ";


        $this->email_sending_billing($billing_info[0]->email_add, $subject, $email);
    }
    public function download_dues_report()
    {
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $status = $this->input->post('status');
        $status_where = "";

        if ($status != "All" && $status != " " && $status != null) {
            $status_where = ' AND r.status_record = "' . $status . '" ';
        }

        $dues = $this->general_model->custom_query("
        SELECT 
            h.*, 
            r.*, 
            CONCAT(h.fname, ' ', h.mname, ' ', h.lname) as fullname,
            COALESCE(r.penalty, 0) + r.paid_amount as total_amount
        FROM 
            tbl_records r
            JOIN tbl_homeowner h ON h.id_ho = r.id_ho 
        WHERE 
            r.status_record != 'archived' 
            AND r.month_record = '" . $month . "' 
            AND r.year_record = '" . $year . "'" . $status_where);


        $all_paid = $this->general_model->custom_query('
    SELECT COUNT(r.id_record) as paid
    FROM tbl_records r, tbl_homeowner h
    WHERE r.status_record != "archived" AND h.id_ho = r.id_ho AND r.year_record = ' . $year . ' AND r.month_record = "' . $month . '" AND r.status_record = "paid" ');
        $all_unpaid = $this->general_model->custom_query('
    SELECT COUNT(r.id_record) as unpaid
    FROM tbl_records r, tbl_homeowner h
    WHERE r.status_record != "archived" AND h.id_ho = r.id_ho AND r.year_record = ' . $year . ' AND r.month_record = "' . $month . '" AND r.status_record = "pending" ');

        $all_dues = $this->general_model->custom_query('
    SELECT COUNT(r.id_record) as paid_unpaid
    FROM tbl_records r, tbl_homeowner h
    WHERE r.status_record != "archived" AND h.id_ho = r.id_ho AND r.year_record = ' . $year . ' AND r.month_record = "' . $month . '"');

        $this->load->library('PHPExcel', null, 'excel');
        // for ($sheet_loop = 0; $sheet_loop < 1; $sheet_loop++) {
        // $this->excel->createSheet(1);
        // }
        // ----------------------------------- Dues
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Homeowners');
        $this->excel->getActiveSheet()->setShowGridlines(false);
        $header_condition = [
            ['col' => 'A', 'id' => 'A7', 'title' => 'LAST NAME', 'width' => 20, 'data_id' => 'lname', 'position' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],
            ['col' => 'B', 'id' => 'B7', 'title' => 'FIRST NAME', 'width' => 20, 'data_id' => 'fname', 'position' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],
            ['col' => 'C', 'id' => 'C7', 'title' => 'MIDDLE NAME', 'width' => 20, 'data_id' => 'mname', 'position' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
            ['col' => 'D', 'id' => 'D7', 'title' => 'BLOCK', 'width' => 10, 'data_id' => 'block', 'position' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
            ['col' => 'E', 'id' => 'E7', 'title' => 'LOT', 'width' => 10, 'data_id' => 'lot', 'position' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],
            ['col' => 'F', 'id' => 'F7', 'title' => 'VILLAGE', 'width' => 15, 'data_id' => 'village', 'position' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],
            ['col' => 'G', 'id' => 'G7', 'title' => 'MONTH DUE', 'width' => 20, 'data_id' => 'month_record', 'position' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],
            ['col' => 'H', 'id' => 'H7', 'title' => 'YEAR DUE', 'width' => 10, 'data_id' => 'year_record', 'position' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],
            ['col' => 'I', 'id' => 'I7', 'title' => 'PAYMENT', 'width' => 15, 'data_id' => 'paid_amount', 'position' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],
            ['col' => 'J', 'id' => 'J7', 'title' => 'PENALTY', 'width' => 15, 'data_id' => 'penalty', 'position' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],
            ['col' => 'K', 'id' => 'K7', 'title' => 'TOTAL PAYMENT', 'width' => 20, 'data_id' => 'total_amount', 'position' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],
            ['col' => 'L', 'id' => 'L7', 'title' => 'STATUS', 'width' => 10, 'data_id' => 'status_record', 'position' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],

        ];
        $this->excel->getActiveSheet()->setCellValue('B2', "HOASYS HOMEOWNER DUES");
        $this->excel->getActiveSheet()->setCellValue('B3', "TOTAL HOMEOWNERS DUES: " . $all_dues[0]->paid_unpaid);
        $this->excel->getActiveSheet()->setCellValue('B4', "TOTAL HOMEOWNERS PAID: " . $all_paid[0]->paid);
        $this->excel->getActiveSheet()->setCellValue('B5', "TOTAL HOMEOWNERS UNPAID: " . $all_unpaid[0]->unpaid);
        $this->excel->getActiveSheet()->getStyle('B2:B2')->getFont()->setSize(20);
        // var_dump($header_condition);
        // exit();
        for ($excel_data_header_loop = 0; $excel_data_header_loop < count($header_condition); $excel_data_header_loop++) {
            $this->excel->getActiveSheet()->setCellValue($header_condition[$excel_data_header_loop]['id'], $header_condition[$excel_data_header_loop]['title']);
            $this->excel->getActiveSheet()->getStyle($header_condition[$excel_data_header_loop]['id'])->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle($header_condition[$excel_data_header_loop]['id'])->getFont()->getColor()->setRGB('FFFFFF');
            $this->excel->getActiveSheet()->getStyle($header_condition[$excel_data_header_loop]['id'])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            if ($header_condition[$excel_data_header_loop]['id'] != "D2") {
                $this->excel->getActiveSheet()->getStyle($header_condition[$excel_data_header_loop]['id'])->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('404040');
            }
            if ($header_condition[$excel_data_header_loop]['width'] > 0) {
                $this->excel->getActiveSheet()->getColumnDimension($header_condition[$excel_data_header_loop]['col'])->setWidth($header_condition[$excel_data_header_loop]['width']);
            }
        }
        $this->excel->getActiveSheet()->getStyle('D2:D2')->getFont()->setSize(20);
        $this->excel->getActiveSheet()->getStyle('D2:D3')->getFont()->getColor()->setRGB('000000');
        // $this->excel->getActiveSheet()->freezePane('E6');
        $rowNum = 8;
        $last_row = (count($dues) + $rowNum) - 1;
        if (count($dues) > 0) {
            foreach ($dues as $condition_rows) {
                for ($loop_header = 0; $loop_header < count($header_condition); $loop_header++) {
                    $this->excel->getActiveSheet()->setCellValue($header_condition[$loop_header]['col'] . $rowNum, $condition_rows->{$header_condition[$loop_header]['data_id']});
                    $this->excel->getActiveSheet()->getStyle($header_condition[$loop_header]['col'] . $rowNum . ":" . $header_condition[$loop_header]['col'] . $rowNum)->getAlignment()->setHorizontal($header_condition[$loop_header]['position']);
                }
                $rowNum++;
            }
        }
        $this->excel->getActiveSheet()->getStyle('A7:L7' . $this->excel->getActiveSheet()->getHighestRow())->applyFromArray(
            array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => 'DDDDDD'),
                    ),
                ),
            )
        );

        $this->excel->getActiveSheet()->getProtection()->setPassword('password hoa_sys');
        $this->excel->getActiveSheet()->getProtection()->setSheet(true);
        $dateTime = $this->get_current_date_time();
        $filename = 'HOASYS_REPORT' . $dateTime['dateTime'] . '.xlsx'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        // header('Set-Cookie: fileDownload=true; path=/');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        ob_end_clean();
        $objWriter->save('php://output');
        // Yes export 
    }
    public function email_sending_billing($email_to, $subject, $email)
    {
        $this->load->library('email');
        $ser = 'http://' . $_SERVER['SERVER_NAME'];
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.gmail.com',
            'smtp_timeout' => 30,
            'smtp_port' => 465,
            'smtp_user' => 'ggn1cdo@gmail.com',
            'smtp_pass' => 'asklaymjpayxhkyi',
            'charset' => 'utf-8',
            'mailtype' => 'html',
            'newline' => '\r\n'
        );
        $message = $email;
        $this->email->initialize($config);
        $this->email->set_newline("\r\n");
        $this->email->set_crlf("\r\n");
        $this->email->from("ggn1cdo@gmail.com");
        $this->email->to($email_to);
        $this->email->subject($subject);
        $this->email->message($message);
        if ($this->email->send()) {
            echo "Mail successful";
        } else {
            echo "Sorry";
            print_r($this->email->print_debugger());
        }
    }
}