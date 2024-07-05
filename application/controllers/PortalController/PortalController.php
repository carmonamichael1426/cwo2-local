<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PortalController extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->model('app_model');
        $this->load->library('upload');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->library('fpdf');
        $this->load->helper('file');
        date_default_timezone_set('Asia/Manila');
        $this->load->library('PHPExcel');

        //Disable Cache
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        ('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    public function portal_login()
    {
        if (!file_exists(APPPATH . 'views/portal/portal_login.php')) {
            show_404();
        }
        $this->load->view('portal/portal_login');
    }

    public function checkCredentials()
    {
        $data  = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $users = $this->app_model->getUsers($data['username'], MD5($data['password']));

        if (!empty($users)) {
            $this->session->set_userdata([
                'admin_user_id'       => $users->user_id,
                'admin_username'      => $users->username,
                'admin_position'      => $users->position,
                'admin_name'          => $users->name,
                'admin_userType'      => $users->userType,
                'admin_logged_in' => TRUE
            ]);

            $userdata = $this->session->userdata('admin_userType');

            if ($userdata == 'Admin') {
                $message = ['info' => 'Granted', 'message' => 'Access Granted.'];
            } else {
                $message = ['info' => 'Denied', 'message' => 'Access Denied.'];
            }
        } else {
            $message = ['info' => 'Error', 'message' => ' Wrong username or password.'];
        }

        JSONResponse($message);
    }

    public function admin_home()
    {
        if ($this->session->userdata('admin_logged_in')) {
            if (!file_exists(APPPATH . 'views/admin/admin_home.php')) {
                show_404();
            }

            $data['title'] = 'Home';

            $this->load->view('admin/admin_header', $data);
            $this->load->view('admin/admin_home');
            $this->load->view('admin/admin_footer');
        } else {
            die('Oops! : You are not allowed to access this page. Please Log in <a href="' . base_url() . '"> here.</a>');
        }
    }
}
