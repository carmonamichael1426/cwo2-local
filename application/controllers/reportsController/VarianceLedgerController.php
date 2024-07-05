<?php
defined('BASEPATH') or exit('No direct script access allowed');

class VarianceLedgerController extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->model('varianceledgermodel');
        $this->load->library('session');
        $this->load->library('form_validation');
        date_default_timezone_set('Asia/Manila');


        //Disable Cache
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        ('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    public function generateVarianceLedger()
    {
        $fetch_data   = json_decode($this->input->raw_input_stream, true);
        $fromLedger   = $this->varianceledgermodel->getVarianceLedger($fetch_data['supId']);
        JSONResponse($fromLedger);
    }

    public function getCrfDetails()
    {
        $fetch_data          = $this->input->post(NULL,TRUE);
        $mentionsTotal       = 0;
        $adjustmentTotal     = 0;
        $data['crf']         = $this->varianceledgermodel->getDataRowArray('crf', $fetch_data['crf_id'], 'crf_id');
        $data['mentions']    = $this->varianceledgermodel->getMentions($fetch_data['variance_id']);
        $data['adjustments'] = $this->varianceledgermodel->getAdjustments($fetch_data['variance_id']);

        if( !empty($data['mentions']) ){
            foreach( $data['mentions'] as $men){
                $mentionsTotal += $men->deduction_amount;
            }
        }
        if( !empty($data['adjustments'])){
            foreach( $data['adjustments'] as $adj){
                $adjustmentTotal += $adj->amount;
            }
        }
        $data['mentionsTotal']   = $mentionsTotal;
        $data['adjustmentTotal'] = $adjustmentTotal;
      
        JSONResponse($data);
    }

}