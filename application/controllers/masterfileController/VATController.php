<?php
defined('BASEPATH') or exit('No direct script access allowed');

class VATController extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('session');
        $this->load->library('form_validation');
        date_default_timezone_set('Asia/Manila');


        $this->load->model('masterfile_model');
    }

    public function addVAT()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $query = $this->db->get_where('vat', array('description' => $fetch_data['desc']));
        if($query->num_rows() > 0){
            $msg = ['info' => 'Error', 'message' => 'VAT already exists!'];
        } else {
            $data = ['description' => $fetch_data['desc'], 'value' => $fetch_data['value'], 'status' => 1];
            $this->db->insert('vat', $data);
            $i    = $this->db->insert_id();            
            if($i){
                $u   = $this->db->query('UPDATE vat SET status = 0 WHERE vat_id <> '.$i); 
                $msg = ['info' => 'Success', 'message' => 'VAT successfully added and set as default!'];
            } else {
                $msg = ['info' => 'Error', 'message' => 'Failed to save VAT!'];
            }
        }
        
        return JSONResponse($msg);
            
    }

    public function loadVAT()
    {
        $result = $this->masterfile_model->loadVAT();
        return JSONResponse($result);
    }

    public function updateVAT()
    {
        $fetch_data   = json_decode($this->input->raw_input_stream, TRUE);
        $oldData      = $this->masterfile_model->getOldData('vat','vat_id',$fetch_data['id']);
        $edit         = false;

        if(strtolower($fetch_data['desc']) == strtolower($oldData['description']) && $fetch_data['value'] == $oldData['value'] ){
            $edit     = false;
        } else {
            $edit     = true;
        }

        if($edit){
            $updateData = [ 'description' => $fetch_data['desc'], 'value' => $fetch_data['value'] ];
            $u          = $this->masterfile_model->update($fetch_data['id'],'vat_id','vat',$updateData);
            if($u){
                $msg = ['info' => 'Success', 'message' => 'VAT: '.strtoupper($fetch_data['desc']). ' has been updated!'];
            } else {
                $msg = ['info' => 'Error', 'message' => 'Failed to update VAT: '.strtoupper($fetch_data['desc'])];
            }
        } else {
            $msg = ['info' => 'Info', 'message' => 'No changes has been detected for VAT: '. strtoupper($fetch_data['desc'])];
        }
        return JSONResponse($msg);  
    }  
  
    public function deactivateVAT()
    {
        $fetch_data   = json_decode($this->input->raw_input_stream, TRUE);
        $update       = array('status' => 0);
        $deac         = $this->masterfile_model->update($fetch_data['id'],'vat_id','vat',$update);

        if($deac){
            $msg = ['info' => 'Success', 'message' => 'VAT is deactivated!'];
        } else {
            $msg = ['info' => 'Error', 'message' => 'Failed to deactivate VAT!'];
        }
        return JSONResponse($msg);
    }

}
