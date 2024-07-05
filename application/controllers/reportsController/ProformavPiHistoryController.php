<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProformavPiHistoryController extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->model('ProformavPiHistoryModel');
        $this->load->library('session');
        $this->load->library('form_validation');
        date_default_timezone_set('Asia/Manila');


        //Disable Cache
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        ('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    public function generateProfvPiHistory()
    {
        $fetch_data   = $this->input->post(NULL, TRUE);
        $history      = array();
        $document1    = array();
        $document2    = array();

        if (!empty($fetch_data)) {

            $document1 = $this->ProformavPiHistoryModel->getTransactionHistory($fetch_data['transactionType'], $fetch_data['supplierSelect'], $fetch_data['locationSelect']);
            $document2 = $this->ProformavPiHistoryModel->getTransactionHistory2($fetch_data['transactionType'], $fetch_data['supplierSelect'], $fetch_data['locationSelect']);
        }

        foreach($document1 as $doc1)
        {
            foreach($document2 as $doc2)
            {
                if( $doc1['tr_id'] == $doc2['tr_id'] ){
                    
                    $history[] = array('tr_id'      => $doc1['tr_id'],    'tr_no'      => $doc1['tr_no'],     'tr_date'   => $doc1['tr_date'],
                                       'acroname'   => $doc1['acroname'], 'l_acroname' => $doc1['l_acroname'],'document1' => $doc1['document1'],
                                       'document2'  => $doc2['document2'],'filename'   => $doc1['filename']);
                }
            }
        }

        return JSONResponse($history);
    }

    public function deleteprofvspi()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $file = getcwd()."/files/Reports/ProformaVsPi/".$fetch_data['filename'];   

        if(file_exists($file)){
            if(unlink($file)){
                $this->db->delete('profvpi_transaction', array('tr_id' => $fetch_data['id']));
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
