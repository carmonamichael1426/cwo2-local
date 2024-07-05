<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Deductioncontroller extends CI_Controller
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

    public function addType()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $msg        = array();

        if( !empty($fetch_data)){
            $insert = $this->masterfile_model->addNewType(strtoupper($fetch_data['type']));
            if($insert){
                $msg = ['info' => 'Success', 'message' => 'Deduction type: '. strtoupper($fetch_data['type']) . ' is added successfully!'];
            } else {
                $msg = ['info' => 'Error', 'message' => 'Failed to add deduction type: '. strtoupper($fetch_data['type']) .' !'];
            }
        } 
        
        return JSONResponse($msg);       
    }

    public function loadDeductionType()
    {
        $result = $this->masterfile_model->getType();
        return JSONResponse($result);
    }

    public function saveDeduction()
    {
        $fetch_data   = json_decode($this->input->raw_input_stream, TRUE);        
        $msg          = array();
        if( !empty($fetch_data)){
            $new = ['name'                  => strtoupper($fetch_data['name']),
                    'name_used_for_display' => strtoupper($fetch_data['acronym']),
                    'deduction_type_id'     => $fetch_data['type'],
                    'formula'               => $fetch_data['formula'],
                    'inputted'              => $fetch_data['inputted'] == "Yes" ? 1 : 0 ,
                    'repeat'                => $fetch_data['repeat'] == "Yes" ? 1 : 0 ,
                    'supplier_id'           => $fetch_data['supId'] == 'Applicable to other Suppliers' ? 'All' : $fetch_data['supId'],
                    'status'                => 1
                ];

            $i = $this->masterfile_model->saveNewDeduction($fetch_data['type'],strtoupper($fetch_data['name']),$new,$fetch_data['supId']);
            if($i){
                $msg = ['info' => 'Success', 'message' => strtoupper($fetch_data['name']). ' is added successfully!'];
            } else {
                $msg = ['info' => 'Error', 'message' => 'Failed to save '. strtoupper($fetch_data['name']). ' !'];
            }            
        } else {
            $msg = ['info' => 'Info', 'message' => 'No Data to Save!'];
        }

        return JSONResponse($msg);
    }

    public function loadDeductions()
    {
        $result = $this->masterfile_model->loadDeductions();
        return JSONResponse($result);
    }

    public function editDeduction()
    {
        $fetch_data   = json_decode($this->input->raw_input_stream, TRUE);
        $supId        = $fetch_data['supId'];
        $id           = $fetch_data['id'];
        $name         = $fetch_data['name'];
        $typeId       = $fetch_data['type'];
        $forDisplay   = $fetch_data['acronym'];
        $formula      = $fetch_data['formula']; 
        $repeat       = $fetch_data['repeat'] == "No"? '0' : '1';
        $inputted     = $fetch_data['inputted'] == "No"? '0' : '1';
        $edit         = false;
        $msg          = array();

        $oldData      = $this->masterfile_model->getOldData('deduction','deduction_id',$id);
        if( $oldData['deduction_id'] == $id && strtolower($oldData['name']) == strtolower($name) &&  $oldData['supplier_id'] == $supId &&
            $oldData['deduction_type_id'] == $typeId && strtolower($oldData['name_used_for_display']) == strtolower($forDisplay) &&
            $oldData['formula'] == $formula && $oldData['inputted'] == $inputted && $oldData['repeat'] == $repeat  ){

            $edit     = false;
        } else {
            $edit     = true;
        }
        
        if($edit){
            $updateData = array('name' => strtoupper($name), 
                                'deduction_type_id' => $typeId, 
                                'name_used_for_display' => strtoupper($forDisplay),
                                'formula' => $formula, 
                                'inputted' => $inputted, 
                                'repeat' => $repeat,
                                'supplier_id' => $supId);
            $u = $this->masterfile_model->update($id,'deduction_id','deduction',$updateData);

            if($u){
                $msg = ['info' => 'Success', 'message' => 'Deduction: '.strtoupper($name). ' has been updated!'];
            } else {
                $msg = ['info' => 'Error', 'message' => 'Failed to update deduction: '.strtoupper($name)];
            }

        } else {
            $msg = ['info' => 'Info', 'message' => 'No changes has been detected for deduction: '. strtoupper($name)];
        }
        return JSONResponse($msg);        
    }

    public function deactivateDeduction()
    {
        $fetch_data   = json_decode($this->input->raw_input_stream, TRUE);
        $msg          = array();
        $update       = array('status' => 0);
        $deac         = $this->masterfile_model->update($fetch_data['id'],'deduction_id','deduction',$update);

        if($deac){
            $msg = ['info' => 'Success', 'message' => 'Deduction is deactivated!'];
        } else {
            $msg = ['info' => 'Error', 'message' => 'Failed to deactivate deduction!'];
        }
        return JSONResponse($msg);
    }

}
