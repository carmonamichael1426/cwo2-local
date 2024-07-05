<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Chargescontroller extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('session');
        $this->load->library('form_validation');
        date_default_timezone_set('Asia/Manila');


        $this->load->model('masterfile_model');
    }

    public function fetchChargesType() {
        $table  = 'charges_type';
        $result = $this->masterfile_model->getTableData($table);
        return JSONResponse($result);
    }

    public function addChargesType() {
        $data              = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $charges_type_data = array();
        $msg               = [];


        if(!empty($data)) {
            // TRANSACTION STARTS HERE
            $this->db->trans_start();

            $charges_type_data = [
                'charges_type' => isset($data['charges_type']) ? $data['charges_type'] : NULL,
                'value' => 0
            ];

            $duplicate = $this->db->get_where('charges_type', ['charges_type' => $data['charges_type']])->row();
            if(empty($duplicate)) {
                $this->db->insert('charges_type', $charges_type_data);

                $this->db->trans_complete();

                if($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $msg = ['message' => 'Failed saving data.', 'info' => 'Error'];
                } else {
                    $msg = ['message' => 'Charges type has been successfully saved.', 'info' => 'Success'];
                }

            } else {
                $msg = ['message' => 'The charges type already exists!', 'info' => 'Exist'];
            }

        } else {
            $msg = ['message' => 'Failed saving data, please check data inputted.', 'info' => 'No Data'];
        }

        JSONResponse($msg);
    }

    public function editChargesType() {
        $data              = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $charges_type_data = array();
        $msg               = [];


        if(!empty($data)) {
            // Check if the item_code exists in the database
            $existingChargesType = $this->masterfile_model->getChargesType($data['charges_type']);

            if(!$existingChargesType || $existingChargesType['charges_id'] == $data['ID']) {
                // TRANSACTION STARTS HERE
                $this->db->trans_start();

                $charges_type_data = [
                    'charges_type' => isset($data['charges_type']) ? $data['charges_type'] : NULL,
                    'value' => 0
                ];

                $this->db->where('charges_id', $data['ID']);
                $this->db->update('charges_type', $charges_type_data);

                $this->db->trans_complete();

                if($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $msg = ['message' => 'Failed saving data.', 'info' => 'Error'];
                } else {
                    $msg = ['message' => 'Charges type has been successfully updated.', 'info' => 'Success'];
                }

            } else {
                $msg = ['message' => 'The charges type already exists!', 'info' => 'Exist'];
            }

        } else {
            $msg = ['message' => 'Failed saving data, please check data inputted.', 'info' => 'No Data'];
        }

        JSONResponse($msg);
    }

}
