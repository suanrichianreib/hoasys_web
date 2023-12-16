<?php

use Mpdf\Tag\P;

defined('BASEPATH') or exit('No direct script access allowed');
require_once(dirname(__FILE__) . "/General.php");
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

class Announcement extends General
{
    protected $title = 'Announcement';
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
            $this->load_template_view('templates/announcement/announcement', $data);
        } else {
            redirect(base_url('Login'));
        }
    }
    public function get_announcement()
    {
        $datatable = $this->input->post('datatable');
        $query['search']['append'] = "";
        $query['search']['total'] = "";
        $where_name = "";
        $order = "datecreated_anmnt";

        $query['query'] = "SELECT id_anmnt,datecreated_anmnt,title_anmnt,desc_anmnt,status_anmnt FROM tbl_announcement WHERE id_anmnt != 0 " . $where_name;
        if ($datatable['query']['searchField'] != '') {
            $keyword = $datatable['query']['searchField'];
        $where = "(title_anmnt LIKE '%" . $keyword . "%' OR desc_anmnt LIKE '%" . $keyword . "%')";
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
    public function save_announcement()
    {
        $date_time = $this->get_current_date_time();
        $ann["datecreated_anmnt"] = $date_time["dateTime"];
        $ann['title_anmnt']  = $this->input->post('title');
        $ann['desc_anmnt'] = $this->input->post('desc');
        $ann['status_anmnt'] = "unpublished";
        $ann['id_admin'] = $this->session->userdata("id_admin");
        $this->general_model->insert_vals($ann, "tbl_announcement");
    }
    public function get_announcement_details(){
        $id = $this->input->post('id');
        $ann_info = $this->general_model->custom_query('SELECT id_anmnt,datecreated_anmnt,title_anmnt as title,desc_anmnt as description,status_anmnt FROM tbl_announcement WHERE id_anmnt = '. $id);
        echo json_encode($ann_info);
    }
    public function update_announcement(){
        $id = $this->input->post('id');
        $ann['title_anmnt'] = $this->input->post('title');
        $ann['desc_anmnt'] = $this->input->post('desc');
        $this->general_model->update_vals($ann, "id_anmnt = $id", "tbl_announcement");
    }
    public function update_announcement_publish(){
        $id = $this->input->post('id');
        $ann['status_anmnt'] = $this->input->post('pub');
        $this->general_model->update_vals($ann, "id_anmnt = $id", "tbl_announcement");
    }
    public function remove_announcement(){
        $id = $this->input->post('id');
        $where_del = 'id_anmnt = '.$id;
        $this->general_model->delete_vals($where_del, 'tbl_announcement');
    }
}