<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AdjustmentController extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('session');
        $this->load->library('form_validation');
        date_default_timezone_set('Asia/Manila');

        $this->load->model('adjustment_model');
    }

    public function searchCRFVar()
    {
        $fetch_data   = json_decode($this->input->raw_input_stream, TRUE);
        $varianceCRF  = $this->adjustment_model->searchCRFVar($fetch_data['str'], $fetch_data['supId']);
        JSONResponse($varianceCRF);    
    }

    public function submitAdjustment()
    {
        $fetch_data = $this->input->post(NULL,TRUE);
        $msg        = [];

        $this->db->trans_start();

        $adjno     = $this->adjustment_model->getDocNo('ADJ', TRUE);
        if( $fetch_data['positive'] == 'false'){
            $adjamt = -$fetch_data['amount'];
        } else {
            $adjamt = $fetch_data['amount'];
        }
        if( !empty($adjno) ){
            $adjData   = [  'adj_no'     => $adjno,
                            'type'       => $fetch_data['positive'] == 'true' ? 1 : 0, //if positive 1, else 0
                            'adj_date'   => date("Y-m-d"),
                            'description'=> strtoupper($fetch_data['desc']),
                            'amount'     => $adjamt,
                            'crf_id'     => $fetch_data['crfId'],
                            'variance_id'=> $fetch_data['varianceId'],
                            'supplier_id'=> $fetch_data['supId'],
                            'user_id'    => $this->session->userdata('user_id') ];
            
            $adj = $this->adjustment_model->insertToTable('variance_adjustment', $adjData);
            if($adj){
                $row = $this->adjustment_model->getDataRowArray('variance_ledger', 'variance_id', $fetch_data['varianceId']);
                $updateData = [ 'debit' => $row['debit'] + $adjamt, 'adjustment' =>  $row['adjustment'] +  $adjamt ];
                $this->adjustment_model->updateToTable('variance_ledger', $updateData, 'variance_id', $fetch_data['varianceId']);
            }
            $msg = [ 'info' => 'Success', 'message' => 'Adjustment is submitted successfully!'];
        } else {
            $msg = [ 'info' => 'Error', 'message' => 'Failed to generate adjustment number!'];
        }
       
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $error = array('action' => 'Saving Variance Adjustment', 'error_msg' => $this->db->error()); //Log error message to `error_log` table
            $this->db->insert('error_log', $error);
            $msg = ['info' => 'Error', 'message' => 'Error submitting adjustment!'];
        } 
        JSONResponse($msg);
    }

    public function getAdjustments()
    {
        $fetch_data   = json_decode($this->input->raw_input_stream, TRUE);
        $getAdjs      = $this->adjustment_model->getAdjustments($fetch_data['supId'], $fetch_data['from'], $fetch_data['to']);
        // dump($getAdjs);
        // die();
        JSONResponse($getAdjs);    
    }
 

}
