<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProformavCrfHistoryController extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->model('ProformavCrfHistoryModel');
        $this->load->library('session');
        date_default_timezone_set('Asia/Manila');


        //Disable Cache
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        ('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    public function generateProfvCrfHistory()
    {
        $fetch_data   = $this->input->post(NULL, TRUE);
        $history      = array();

        if (!empty($fetch_data)) {

            $history = $this->ProformavCrfHistoryModel->getTransactionHistory($fetch_data['transactionType'], $fetch_data['supplierSelect'], $fetch_data['locationSelect']);
           
        }       

        return JSONResponse($history);
    }

    public function deleteprofvscrf()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $file = getcwd()."/files/Reports/ProformaVsCrf/".$fetch_data['filename'];   

        if(file_exists($file)){
            if(unlink($file)){
                $this->db->delete('profvcrf_transaction', array('tr_id' => $fetch_data['id']));
                $msg = ['info' => "success", "message" => "File has been deleted!"];

            } else {
                $msg = ['info' => "error", "message" => "Unable to delete file!"];
            }
            
        } else {
            $msg = ['info' => "error", "message" => "Directory is not found!"];
        }
        JSONResponse($msg);
    }
}
