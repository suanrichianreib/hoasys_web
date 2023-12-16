<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once(dirname(__FILE__) . "/General.php");
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Content-Type");


class Login extends General
{
    protected $title = 'Login';

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['title'] = $this->title;

        if ($this->session->userdata("id_admin")) {
            $this->check_login_access();
        } else {
            $this->load->view('templates/login/login', $data);
        }
    }

    public function login_authentication()
    {
        $email = $this->input->post('email');
        $password = sha1(md5($this->input->post('password')));
        // $password = $this->input->post('password');
        $result = $this->general_model->fetch_specific_val("*", "username = '$email' AND password = '$password'", "tbl_admin");
        // var_dump($result);
        // echo $email+" "+$password;
        if ($result != null) {
            $status = 'Success';
            $role = '';
            if ($result->role === 'admin') {
                $role = 'Admin';
            } elseif ($result->role === 'regular') {
                $role = 'Regular';
            } elseif ($result->role === 'vct') {
                $role = 'VCT';
            } 
            $set_session = array(
                'fullname'      => $result->fname . ' ' . $result->mname . ' ' . $result->lname,
                'id_admin'   => $result->id_admin,
                'username'      => $result->username,
                'role'          => $role
            );
            $this->session->set_userdata($set_session);
        } else {
            $status = 'Failed';
        }
        echo json_encode($status);
    }

    public function check_login_access()
    {
        // if ($this->session->userdata("role") == "Admin") {
        //     redirect(base_url('admin'));
        // } else {
        //     redirect(base_url('homeowners'));
        // }
        redirect(base_url('dashboard'));
    }

    public function logout()
    {
        if ($this->session->has_userdata('id_admin')) {
            $array_items = array('fullname', 'id_admin', 'username', 'role');
            $this->session->unset_userdata($array_items);
        }
        redirect('login');
    }
}