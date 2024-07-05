<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UploadedTransactionController extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('upload');
        $this->load->model('uploadedtransactionmodel');
        $this->load->library('session');
        $this->load->library('form_validation');
        date_default_timezone_set('Asia/Manila');

        if (!$this->session->userdata('cwo_logged_in')) {

            redirect('home');
        }

        //Disable Cache
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        ('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

   
    public function generateUploadedTransaction()
    {
        $fetch_data = $this->input->post(NULL,TRUE);

        if(!empty($fetch_data)){

            $data = $this->uploadedtransactionmodel->getUploadedTransactionsDocs($fetch_data['type'],$fetch_data['supplierSelect'],$fetch_data['locationSelect']);

            JSONResponse($data);
        }
    }

   
}
