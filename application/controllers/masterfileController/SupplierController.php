<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suppliercontroller extends CI_Controller
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

    public function fetchSuppliers()
    {
        $table  = 'suppliers';
        $result = $this->masterfile_model->getTableData($table);
        return JSONResponse($result);
    }

    public function addSupplier()
    {
        $data          = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $supplier_data = array();
        $duplicate     = array();

        if (!empty($data)) {
            // TRANSACTION STARTS HERE
            $this->db->trans_start();

            $supplier_data =
                [
                    'supplier_code' => $data['vendorsCode'],
                    'supplier_name' => $data['supplierName'],
                    'address'       => $data['supplierAddress'],
                    'contact_no'    => $data['supplierContact'],
                    'acroname'      => $data['supplierAcroname'],
                    'status'        => '1'
                ];

            $duplicate = $this->masterfile_model->checkDuplicate('suppliers', $data['supplierName'], $data['supplierAcroname']);

            if (empty($duplicate)) {

                // $this->masterfile_model->createItemTable(str_replace(' ', '', $data['supplierAcroname']));
                // $this->masterfile_model->createProformaHeader(str_replace(' ', '', $data['supplierAcroname']));
                // $this->masterfile_model->createProformaLine(str_replace(' ', '', $data['supplierAcroname']));

                $this->db->insert('suppliers', $supplier_data);

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $error = array('action' => 'Saving Supplier', 'error_msg' => $this->db->_error_message()); //Log error message to `error_log` table
                    $this->db->insert('error_log', $error);
                    $msg = ['message' => 'Error: Failed saving data.', 'info' => 'Error Saving'];
                } else {
                    $msg = ['message' => 'Supplier Succesfully Saved.', 'info' => 'Success'];
                }
            } else {
                $msg = ['message' => 'Supplier already existed.', 'info' => 'Supplier Exist'];
            }
        } else {
            $msg = ['message' => 'Error: Failed saving data, please check data inputted.', 'info' => 'No Data'];
        }

        JSONResponse($msg);
    }

    public function uploadSupplier()
    {
        $pricing       = $this->input->post('supplierPricing');
        $proforma_code = $_FILES['file']['name'];
        $m             = array();
        $supplierInfo  = file_get_contents($_FILES['file']['tmp_name']);
        $supplierData  = array();

        $line = explode("\n", $supplierInfo);
        $totalLine = count($line);
        $totalItem = $totalLine;
        $item = 0;

        foreach ($_FILES as $key => $file) {
            $file_name     = str_replace(['"', '[', ']'], '', json_encode($file['name']));
            $file_tmp_name = str_replace(['"', '[', ']'], '', json_encode($file['tmp_name']));
        }

        if ($file_name != '') {
            $end       = strrpos($file_name, ".");
            $toCompare = substr($file_name, 0, $end);
            $length    = strlen($file_name);
            $ext       = substr($file_name, $end + 1, $length);

            if ($ext == 'txt') {
                $this->db->trans_start();
                for ($i = 0; $i < $totalLine; $i++) {
                    if ($line[$i] != NULL) {
                        $blits = str_replace('"', "", $line[$i]);
                        $refined = explode("|", $blits);
                        $countRefined = count($refined);

                        if ($countRefined == 12) {
                            $supplierData =
                                [
                                    'supplier_code' => trim($refined[0]),
                                    'supplier_name' => trim($refined[1]),
                                    'acroname'      => strtok(trim($refined[1]), " "),
                                    'address'       => trim($refined[2]) . ', ' . trim($refined[3]),
                                    'status'        => '1',
                                    'pricing'       => $pricing
                                ];
                        }
                    }
                }

                $duplicate = $this->masterfile_model->checkDuplicate('suppliers', $supplierData['supplier_name'], $supplierData['acroname']);

                if (empty($duplicate)) {

                    // $this->masterfile_model->createItemTable(str_replace(' ', '', $supplierData['acroname']));
                    // $this->masterfile_model->createProformaHeader($supplierData['acroname']);
                    // $this->masterfile_model->createProformaLine($supplierData['acroname']);

                    $this->db->insert('suppliers', $supplierData);

                    $this->db->trans_complete();

                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $error = array('action' => 'Saving Supplier', 'error_msg' => $this->db->_error_message()); //Log error message to `error_log` table
                        $this->db->insert('error_log', $error);
                        $msg = ['message' => 'Error: Failed saving data.', 'info' => 'Error Saving'];
                    } else {
                        $msg = ['message' => 'Supplier Succesfully Saved.', 'info' => 'Success'];
                    }
                } else {
                    $m = ['message' => 'Supplier ' . $supplierData['supplier_name'] . ' already exist.', 'info' => 'Duplicate'];
                }
            } else {
                $m  = ['message' => 'File Format Not Supported.', 'info' => 'Format'];
            }
        } else {
            $m = ['message' => 'Failed Uploading no File Detected.', 'info' => 'Failed'];
        }

        JSONResponse($m);
    }

    public function updateSupplier()
    {
        $data          = $this->input->post(NULL,  FILTER_SANITIZE_STRING);
        $PHData        = array();
        $PLData        = array();
        $supplier_data = array();
        $msg           = array();
        $supplier      = array();

        if (!empty($data)) {
            // TRANSACTION STARTS HERE
            $this->db->trans_start();

            $supplier_data =
                [
                    'supplier_code' => $data['vendorsCodeU'],
                    'supplier_name' => $data['supplierNameU'],
                    'address'       => $data['supplierAddressU'],
                    'contact_no'    => $data['supplierContactU'],
                    'acroname'      => $data['supplierAcronameU']
                ];

            $duplicate = $this->masterfile_model->checkDuplicate('suppliers', $data['supplierNameU'], $data['supplierAcronameU']);
            $supplier  = $this->masterfile_model->getData($data['ID']);

            if (empty($duplicate)) :
                if ($supplier->acroname == $data['supplierAcronameU']) {
                    $this->masterfile_model->update($data['ID'], 'supplier_id', 'suppliers', $supplier_data);
                } else {
                    $this->masterfile_model->renameProformaHeader($supplier->acroname, $data['supplierAcronameU']);
                    $this->masterfile_model->renameProformaLine($supplier->acroname, $data['supplierAcronameU']);
                    $this->masterfile_model->update($data['ID'], 'supplier_id', 'suppliers', $supplier_data);
                }
            elseif ($duplicate->supplier_id == $data['ID']) :
                if ($supplier->acroname == $data['supplierAcronameU']) {
                    $this->masterfile_model->update($data['ID'], 'supplier_id', 'suppliers', $supplier_data);
                } else {
                    $this->masterfile_model->renameProformaHeader($supplier->acroname, $data['supplierAcronameU']);
                    $this->masterfile_model->renameProformaLine($supplier->acroname, $data['supplierAcronameU']);
                    $this->masterfile_model->update($data['ID'], 'supplier_id', 'suppliers', $supplier_data);
                }
            else :
                $msg = ['message' => 'Supplier Already Existed.', 'info' => 'Exist'];
                JSONResponse($msg);
            endif;

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $error = array('action' => 'Updating Supplier', 'error_msg' => $this->db->error()); //Log error message to `error_log` table
                $this->db->insert('error_log', $error);
                $msg = ['message' => 'Error: Failed updating data.', 'info' => 'Error Saving'];
            } else {
                $msg = ['message' => 'Supplier Succesfully Updated.', 'info' => 'Success'];
            }
        } else {
            $msg = ['message' => 'Error: Failed updating data, please check data inputted.', 'info' => 'No Data'];
        }

        JSONResponse($msg);
    }

    public function deactivateSupplier()
    {
        $ID = $this->input->post('ID');

        if (!empty($ID)) {
            $this->db->trans_start();

            $status = ['status' => 0];
            $this->masterfile_model->update($ID, 'supplier_code', 'suppliers', $status);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $error = array('action' => 'Deactivating Supplier', 'error_msg' => $this->db->error()); //Log error message to `error_log` table
                $this->db->insert('error_log', $error);
                $msg = ['message' => 'Error: Failed deactivating supplier.', 'info' => 'Error Deactivating'];
            } else {
                $msg = ['message' => 'Supplier Deactivated.', 'info' => 'Success'];
            }
        } else {
            $msg = ['message' => 'No Data found to deactivate.', 'info' => 'Error'];
        }

        JSONResponse($msg);
    }
}
