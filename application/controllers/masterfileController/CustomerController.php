<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Customercontroller extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('upload');
        $this->load->library('session');
        $this->load->library('form_validation');
        date_default_timezone_set('Asia/Manila');


        $this->load->model('masterfile_model');
    }

    function sanitize($string)
    {
        $string = htmlentities($string, ENT_QUOTES, 'UTF-8');
        $string = trim($string);
        return $string;
    }

    public function addCustomer()
    {

        $fetch_data = $this->input->post(NULL, TRUE);
        $insert_customer = $this->masterfile_model->addCustomer($fetch_data['customer'], $fetch_data['acroname']);

        if ($insert_customer) {
            die("success");
        } else {
            die("error");
        }
    }

    public function updateCustomer()
    {

        $fetch_data = $this->input->post(NULL, TRUE);
        $updateCustomer = $this->masterfile_model->updateCustomer($fetch_data['ccode'], $fetch_data['cname'], $fetch_data['lacroname']);
        if ($updateCustomer) {
            die("success");
        } else {
            die("error");
        }
    }

    public function deactivateCustomer()
    {
        $fetch_data = $this->input->post(NULL, TRUE);
        $deactivate = $this->masterfile_model->deactivateCustomer($fetch_data['ccode']);
        if ($deactivate) {
            die('success');
        } else {
            die('error');
        }
    }

    public function fetchCustomers()
    {
        $table = 'customers';
        $result = $this->masterfile_model->getTableData($table);
        return JSONResponse($result);
    }
}
