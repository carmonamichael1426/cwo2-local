<?php
defined('BASEPATH') or exit('No direct script access allowed');

class itemCodeController extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('upload');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->model('itemcode_model');
        $this->load->library('PHPExcel');
        date_default_timezone_set('Asia/Manila');
    }

    function sanitize($string)
    {
        $string = htmlentities($string, ENT_QUOTES, 'UTF-8');
        $string = trim($string);
        return $string;
    }

    public function getNoMapItems()
    {
        $data     = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $result   = array();

        if (!empty($data)) {

            $result = $this->itemcode_model->getNoMapItems($data['supplierID'], $data['customerID']);
        }

        JSONResponse($result);
    }

    public function generateItems()
    {
        $data     = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $result   = $this->itemcode_model->getItems($data['supplierID'], $data['customerID']);
        return JSONResponse($result);
    }

    public function uploadItems()
    {
        $data         = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $supplier     = $this->itemcode_model->getData($data['supplierNameUpload']);
        $itemCodeData = array();
        $duplicates   = array();
        $m            = array();
        $status       = '';
        $document     = array();
        $checkDuplicate = array();

        foreach ($_FILES as $key => $file) {
            $file_name = str_replace(['"', '[', ']'], '', json_encode($file['name']));
        }

        if ($file_name != '') {

            $document = $this->itemcode_model->checkDocument($file_name);

            if (empty($document)) {
                $itemCodes = file_get_contents($_FILES['itemCodes']['tmp_name']);
                $line      = explode("\n", $itemCodes);
                $totalLine = count($line);
                $end       = strrpos($file_name, ".");
                $length    = strlen($file_name);
                $ext       = substr($file_name, $end + 1, $length);

                if ($ext == 'txt') {

                    $this->db->trans_start();

                    for ($i = 0; $i < $totalLine; $i++) {
                        if ($line[$i] != NULL || $line[$i] != '') {

                            $blits        = str_replace('"', "", $line[$i]);
                            $refined      = explode("|", $blits);
                            $countRefined = count($refined);

                            if ($countRefined == 7) {
                                if ($supplier->supplier_code == trim($refined[2])) {

                                    $checkDuplicate = $this->itemcode_model->checkDuplicateUpload(trim($refined[0]), $data['supplierNameUpload'], $data['locationNameUpload']);

                                    if (empty($checkDuplicate)) {
                                        $itemCodeData[] =
                                            [
                                                'itemcode_loc'            => trim($refined[0]),
                                                'itemcode_sup'            => 'NO SET UP',
                                                'description'             => trim($refined[1]),
                                                'supplier_id'             => $data['supplierNameUpload'],
                                                'customer_code'           => $data['locationNameUpload'],
                                                'item_division'           => trim($refined[3]),
                                                'item_department_code'    => trim($refined[4]),
                                                'item_group_code'         => trim($refined[5]),
                                                'inventory_posting_group' => trim($refined[6])

                                            ];
                                    } else {
                                        $duplicates[] =
                                            [
                                                'itemcode_loc'  => trim($refined[0]),
                                                'description'   => trim($refined[1])
                                            ];
                                    }
                                } else {
                                    $m = ['message' => 'Supplier Does not Match: ' . trim($refined[2]), 'info' => 'Incorrect Supplier'];
                                    JSONResponse($m);
                                    exit();
                                }
                            }
                        }
                    }

                    $saveDocument =
                        [
                            'document_name' => $file_name,
                            'document_type' => 'Item Masterfile',
                            'uploaded_on'   => date('Y-m-d'),
                            'user_id'       => $this->session->userdata('user_id')
                        ];

                    $this->db->insert('uploaded_documents', $saveDocument);

                    if (!empty($itemCodeData) && empty($duplicates)) {
                        foreach ($itemCodeData as $items) {

                            $this->db->insert('items', $items);
                        }
                        $status = 'Items Uploaded';
                    } else if (!empty($itemCodeData) && !empty($duplicates)) {
                        foreach ($itemCodeData as $items) {

                            $this->db->insert('items', $items);
                        }
                        $status = 'Items Uploaded with Duplicate Items';
                    } else if (empty($itemCodeData) && !empty($duplicates)) {
                        $status = 'Duplicate Items';
                    } else if (empty($itemCodeData) && empty($duplicates)) {
                        $status = 'Error Uploading';
                    }
                    $this->db->trans_complete();

                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $error = array('action' => 'Saving Items', 'error_msg' => $this->db->_error_message()); //Log error message to `error_log` table
                        $this->db->insert('error_log', $error);
                        $m = ['message' => 'Error: Failed saving item.', 'info' => 'Error-Saving'];
                    } else {

                        if ($status == 'Items Uploaded') {
                            $m = ['message' => $status, 'info' => 'Success'];
                        } else if ($status == 'Items Uploaded with Duplicate Items') {
                            $m = ['message' => $status, 'info' => 'Added-Duplicate'];
                        } else if ($status == 'Duplicate Items') {
                            $m = ['message' => 'All Items Already Existed.', 'info' => 'Duplicate'];
                        } else if ($status == 'Error Uploading') {
                            $m = ['message' => $status, 'info' => 'Error Saving'];
                        }
                    }
                } else {
                    $m = ['message' => 'File Format Not Supported.', 'info' => 'Format'];
                }
            } else {
                $m = ['message' => 'Text File Already Uploaded', 'info' => 'Duplicate'];
            }
        } else {
            $m = ['message' => 'Uploading Error: No File Uploaded.', 'info' => 'Failed'];
        }

        JSONResponse($m);
    }

    public function updateItemCodes()
    {
        $data           = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $supplier       = $this->itemcode_model->getData($data['supplierNameUpload']);
        $hasData        = array();
        $noFound        = array();
        $m              = array();
        $checkDuplicate = array();

        foreach ($_FILES as $key => $file) {
            $file_name = str_replace(['"', '[', ']'], '', json_encode($file['name']));
        }

        if ($file_name != '') {
            $itemCodes = file_get_contents($_FILES['itemCodes']['tmp_name']);
            $line      = explode("\n", $itemCodes);
            $totalLine = count($line);
            $end       = strrpos($file_name, ".");
            $length    = strlen($file_name);
            $ext       = substr($file_name, $end + 1, $length);

            if ($ext == 'txt') {

                $this->db->trans_start();

                for ($i = 0; $i < $totalLine; $i++) {
                    if ($line[$i] != NULL) {

                        $blits        = str_replace('"', "", $line[$i]);
                        $refined      = explode("|", $blits);
                        $countRefined = count($refined);

                        if ($countRefined == 7) {
                            if ($supplier->supplier_code == trim($refined[2])) {

                                $checkDuplicate = $this->itemcode_model->checkDuplicateUploadUpdate(trim($refined[1]), $data['supplierNameUpload'], $data['locationNameUpload']);

                                if (!empty($checkDuplicate)) {
                                    $hasData[] =
                                        [
                                            'itemcode_loc'            => trim($refined[0]),
                                            'description'             => trim($refined[1]),
                                            'supplier_id'             => $data['supplierNameUpload'],
                                            'customer_code'           => $data['locationNameUpload']
                                        ];

                                    $this->db->where('id', $checkDuplicate->id);
                                    $this->db->update('items', [
                                        'itemcode_loc'            => trim($refined[0]),
                                        'description'             => trim($refined[1]),
                                        'item_division'           => trim($refined[3]),
                                        'item_department_code'    => trim($refined[4]),
                                        'item_group_code'         => trim($refined[5]),
                                        'inventory_posting_group' => trim($refined[6])
                                    ]);
                                } else {
                                    $noFound =
                                        [
                                            'itemcode_loc'            => trim($refined[0]),
                                            'itemcode_sup'            => 'NO SET UP',
                                            'description'             => trim($refined[1]),
                                            'supplier_id'             => $data['supplierNameUpload'],
                                            'customer_code'           => $data['locationNameUpload'],
                                            'item_division'           => trim($refined[3]),
                                            'item_department_code'    => trim($refined[4]),
                                            'item_group_code'         => trim($refined[5]),
                                            'inventory_posting_group' => trim($refined[6])
                                        ];

                                        $this->db->insert('items', $noFound);
                                }
                            } else {
                                $m = ['message' => 'Supplier Does not Match: ' . trim($refined[2]), 'info' => 'Incorrect Supplier'];
                                JSONResponse($m);
                                exit();
                            }
                        }
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $error = array('action' => 'Updating Items', 'error_msg' => $this->db->_error_message()); //Log error message to `error_log` table
                    $this->db->insert('error_log', $error);
                    $m = ['message' => 'Error: Failed Updating items.', 'info' => 'Error-Saving'];
                } else {

                    if (!empty($hasData) && empty($noFound)) {
                        $m = ['message' => 'Items Updated Successfully.', 'info' => 'Success'];
                    } else if (!empty($hasData) && !empty($noFound)) {
                        $m = ['message' => 'Items updated successfully but there are items in the file that are not found in the system.', 'info' => 'Success'];
                    } else if (empty($hasData) && !empty($noFound)) {
                        $m = ['message' => 'No items found to be updated.', 'info' => 'Failed'];
                    } else if (empty($hasData) && empty($noFound)) {
                        $m = ['message' => 'Error: Failed Updating items, please check file uploaded and upload again.', 'info' => 'Error-Saving'];
                    }
                }
            } else {
                $m = ['message' => 'File Format Not Supported.', 'info' => 'Format'];
            }
        } else {
            $m = ['message' => 'Uploading Error: No File Uploaded.', 'info' => 'Failed'];
        }

        JSONResponse($m);
    }
    public function uploadMapping()
    {
        $data = $this->input->post(NULL, FILTER_SANITIZE_STRING);

        $location = array();
        $supplier = array();
        $msg      = array();
        $ad       = array();

        if (!empty($data)) {
            $this->db->trans_start();

            foreach ($_FILES as $key => $file) {
                $file_name     = str_replace(['"', '[', ']'], '', json_encode($file['name']));
                $file_tmp_name = str_replace(['"', '[', ']'], '', json_encode($file['tmp_name']));
            }

            if ($file_name != '') {
                $end       = strrpos($file_name, ".");
                $length    = strlen($file_name);
                $ext       = substr($file_name, $end + 1, $length);

                if ($ext == 'xlsx' || $ext == 'XLSX') {
                    if ($ext == 'xls' || $ext == 'XLS') {
                        $excel22 = 'Excel5';
                    } else if ($ext == 'xlsx' || $ext == 'XLSX') {
                        $excel22 = 'Excel2007';
                    }

                    $objReader = PHPExcel_IOFactory::createReader($excel22);
                    //THIS ONES FINE EVEN THO ITS RED BUT ITS UGLY
                    $objReader->setReadDataOnly(true);

                    $objPHPExcel  = $objReader->load($file_tmp_name);
                    $objWorksheet = $objPHPExcel->getActiveSheet();
                    $highestRow   = $objWorksheet->getHighestRow();

                    // =============== PROFORMA HEADER =============== //
                    // $locationItemCode    = trim($objWorksheet->getCellByColumnAndRow(1, 2)->getValue());
                    // $supplierItemCode    = trim($objWorksheet->getCellByColumnAndRow(2, 2)->getValue());

                    // $items = array();

                    for ($i = 2; $i <= $highestRow; $i++) {

                        $locationItemCode    = trim($objWorksheet->getCellByColumnAndRow(1, $i)->getValue());
                        $supplierItemCode    = trim($objWorksheet->getCellByColumnAndRow(2, $i)->getValue());

                        if (empty($locationItemCode)) {
                            break;
                        } else {
                            $this->db->where('itemcode_loc', $locationItemCode);
                            $this->db->update('items', ['itemcode_sup' => $supplierItemCode]);

                            // echo $this->db->last_query();

                            // $items[] =
                            //     [
                            //         'Nav ITem' => $locationItemCode,
                            //         'Sup Item' => $supplierItemCode
                            //     ];
                        }
                    }

                    // var_dump($items);
                    // exit();

                    $this->db->trans_complete();

                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $error = array('action' => 'Mapping Items', 'error_msg' => $this->db->error()); //Log error message to `error_log` table
                        $this->db->insert('error_log', $error);
                        $msg = ['message' => 'Error: Failed Mapping Items', 'info' => 'Error'];
                    } else {
                        // move_uploaded_file($_FILES['proforma']['tmp_name'], $target);
                        $msg = ['message' => 'Mapping Items Complete.', 'info' => 'Mapped'];
                    }
                } else {
                    $msg = ['message' => 'No File Uploaded.', 'info' => 'No Data'];
                }
            } else {
                $msg = ['message' => 'Data seems to be empty.', 'info' => 'No Data'];
            }
        }

        JSONResponse($msg);
    }

    public function saveMapItems()
    {
        $data      = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $itemInfo  = array();
        $duplicate = array();
        $existing  = array();
        $dontExist = array();
        $m         = array();

        if (!empty($data)) {
            if (!empty($data['itemCodeMappingData'])) {

                $this->db->trans_start();

                foreach ($data['itemCodeMappingData'] as $key => $value) {
                    $itemInfo  = $this->itemcode_model->getItemData($value['locationItemCode'], $data['supplierSelectMapping'], $data['locationSelectMapping']);
                    $duplicate = $this->itemcode_model->getItemDataDuplicate($value['supplierItemCode'], $data['supplierSelectMapping'], $data['locationSelectMapping']);

                    if (empty($duplicate)) {
                        $dontExist[] = $value['supplierItemCode'];
                        $this->db->where('id', $itemInfo->id);
                        $this->db->update('items', ['itemcode_sup'  => $value['supplierItemCode']]);
                    } else {
                        $existing[] = $value['supplierItemCode'];
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {

                    $this->db->trans_rollback();
                    $error = array('action' => 'Mapping Items', 'error_msg' => $this->db->_error_message()); //Log error message to `error_log` table
                    $this->db->insert('error_log', $error);
                    $m = ['message' => 'Error: Failed Mapping Items.', 'info' => 'Error-Mapping'];
                } else {
                    if (!empty($dontExist) && empty($existing)) {
                        $m = ['message' => 'Item/s Mapped Succesfully', 'info' => 'Items-Mapped'];
                    } else if (empty($dontExist) && !empty($existing)) {
                        $m = ['message' => 'Supplier Item Code/s Already Existed.', 'info' => 'Duplicate-Item', 'items' => $existing];
                    } else if (!empty($dontExist) && !empty($existing)) {
                        $m = ['message' => 'Item/s Mapped Succesfully But There Are Item/s who Exist Already.', 'info' => 'Mapped-Duplicate', 'items' => $existing];
                    } else if (empty($dontExist) && empty($existing)) {
                        $m = ['message' => 'Error: Failed Mapping Item/s.', 'info' => 'Error-Mapping'];
                    }
                }
            } else {
                $m = ['message' => 'Failed Mapping: No Data Found To Map.', 'info' => 'No-Data'];
            }
        } else {
            $m = ['message' => 'Failed Mapping: No Data Found To Map.', 'info' => 'No-Data'];
        }

        JSONResponse($m);
    }

    public function updateNewItems()
    {
        $data           = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $itemdata       = array();
        $m              = array();
        $checkDuplicate = array();
        $supplier       = $this->itemcode_model->getData($data['supplier_id']);

        if (!empty($data)) {
            $this->db->trans_start();

            $itemdata =
                [
                    'itemcode_sup'  => $data['itemcode_supplier_edit'],
                    'itemcode_loc'  => $data['itemcode_location_edit']
                ];

            $checkDuplicate = $this->itemcode_model->checkDuplicateUpdate(
                [
                    'itemcode_sup'  => $data['itemcode_supplier_edit'],
                    'supplier_id'   => $data['supplier_id'],
                    'customer_code' => $data['customer_code']
                ]
            );

            if (empty($checkDuplicate)) :

                $this->db->where('id', $data['ID']);
                $this->db->update('items', $itemdata);

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {

                    $this->db->trans_rollback();
                    $error = array('action' => 'Saving Items', 'error_msg' => $this->db->_error_message()); //Log error message to `error_log` table
                    $this->db->insert('error_log', $error);
                    $m = ['message' => 'Error: Failed updating info.', 'info' => 'Error Saving'];
                } else {
                    $m = ['message' => 'Successfully Updated.', 'info' => 'Updated'];
                }

            elseif ($data['ID'] == $checkDuplicate->id) :

                $this->db->where('id', $data['ID']);
                $this->db->update('items', $itemdata);

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {

                    $this->db->trans_rollback();
                    $error = array('action' => 'Saving Items', 'error_msg' => $this->db->_error_message()); //Log error message to `error_log` table
                    $this->db->insert('error_log', $error);
                    $m = ['message' => 'Error: Failed updating info.', 'info' => 'Error Saving'];
                } else {
                    $m = ['message' => 'Successfully Updated.', 'info' => 'Updated'];
                }

            else :

                $m = ['message' => 'Item codes ' . $data['itemcode_supplier_edit'] . ' and ' . $data['itemcode_location_edit'] . ' already exist.', 'info' => 'Duplicate'];

            endif;
        } else {
            $m = ['message' => 'Failed saving item, no data to be saved.', 'info' => 'No Data'];
        }

        JSONResponse($m);
    }

    public function deleteItem()
    {
        $data     = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $m        = array();
        $acroname = $this->itemcode_model->getData($data['supplier_id']);

        if (!empty($data)) {
            $this->db->where('id', $data['id']);
            $this->db->delete('items');

            $m = ['message' => 'Item Deleted.', 'info' => 'Deleted'];
        } else {

            $m = ['message' => 'Failed Deleting Item.', 'info' => 'Error'];
        }

        JSONResponse($m);
    }
}
