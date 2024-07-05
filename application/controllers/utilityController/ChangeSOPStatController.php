<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ChangeSOPStatController extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->model('sop_model');
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

   
    public function getSOPs()
    {
        $fetch_data = $this->input->post(NULL,TRUE);

        if(!empty($fetch_data)){
           
            $data = $this->sop_model->getSops($fetch_data['type'],$fetch_data['supplierSelect'],$fetch_data['locationSelect']);
            
            JSONResponse($data);
        }
    }

    public function changeSOPStatus()
    {
        $fetch_data  = $this->input->post(NULL,TRUE);
        $stat = 0;
        if( $fetch_data['status'] == 'PENDING' ){
            $stat = 0;
        } else if( $fetch_data['status'] == 'AUDITED' ){
            $stat = 1;
        } else if( $fetch_data['status'] == 'CANCELLED' ){
            $stat = 2;
        }
        $msg = [];
        $getInvoices = $this->sop_model->getInvoices($fetch_data['sopId']);
        $count1 = count($getInvoices);
        $updateCount = 0;
        foreach($getInvoices as $get){
            $data = [ 'status' =>  $stat ];
            $u = $this->sop_model->updateToTable('sop_invoice', $data, 'id', $get['id']);
            if($u){
                $updateCount ++;
            }
        }
        if($updateCount == $count1){
            $head = [ 'status' =>  $stat ];
            $hu   = $this->sop_model->updateToTable('sop_head', $head, 'sop_id', $fetch_data['sopId']);
            if($hu){
                $msg = ['info' => 'Success', 'message' => 'Successfully changed!'];
            } else {
                $msg = ['info' => 'Error', 'message' => 'Failed to change status!'];
            }
        }
       
        JSONResponse($msg);       
    }

   
}
