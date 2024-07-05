<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Masterfile extends CI_Controller
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

        $customername = $fetch_data['customer'];

        $insert_customer = $this->masterfile_model->addCustomer($customername);

        if ($insert_customer) {
            die("success");
        } else {
            die("error");
        }
    }

    public function updateCustomer()
    {

        $fetch_data = $this->input->post(NULL, TRUE);

        $code = $fetch_data['ccode'];
        $name = $fetch_data['cname'];
        $updateCustomer = $this->masterfile_model->updateCustomer($code, $name);

        if ($updateCustomer) {
            die("success");
        } else {
            die("error");
        }
    }

    public function deactivateCustomer()
    {
        $fetch_data = $this->input->post(NULL, TRUE);
        $code = $fetch_data['ccode'];

        $deactivate = $this->masterfile_model->deactivateCustomer($code);
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
