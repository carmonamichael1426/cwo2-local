<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Povsproformacontroller extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('upload');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->library('PHPExcel');
        $this->load->library('fpdf');
        $this->load->model('povsproforma_model');
        date_default_timezone_set('Asia/Manila');

        //Disable Cache
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        ('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    function sanitize($string)
    {
        $string = htmlentities($string, ENT_QUOTES, 'UTF-8');
        $string = trim($string);
        return $string;
    }

    public function getSuppliers()
    {
        $result = $this->povsproforma_model->getDataSupplier();
        return JSONResponse($result);
    }

    public function getCustomers()
    {
        $result = $this->povsproforma_model->getDataCustomer();
        return JSONResponse($result);
    }

    public function getPurchaseOrder()
    {
        $supplierid = $this->uri->segment(4);
        $customerID = $this->uri->segment(5);

        $result = $this->povsproforma_model->getPOHeader('many', $supplierid, $customerID, '');
        return JSONResponse($result);
    }

    public function uploadProforma()
    {
        $data           = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $checkDuplicate = array();
        $proformaLine   = array();
        $msg            = array();
        $ad             = array();
        $proforma_code  = '';
        $itemDontExistCount = 0;
        $itemNotFound   = array();

        if (!empty($data)) {
            $this->db->trans_start();

            for ($i = 0; $i < count($_FILES['proforma']['name']); $i++) {

                $acroname       = $this->povsproforma_model->getAcroname($data['supplierSelect']);
                $proforma_code  = $_FILES['proforma']['name'][$i];
                $proformaName   = pathinfo($proforma_code, PATHINFO_FILENAME);
                $target         = getcwd() . DIRECTORY_SEPARATOR . 'files/POVSPROFORMA/' . $proforma_code;
                $checkDuplicate = $this->povsproforma_model->checkDuplicate($proformaName);

                if (!empty($checkDuplicate)) {

                    $msg = ['message' => 'Proforma already exists.', 'info' => 'Duplicate'];
                    JSONResponse($msg);
                }

                $file_name     = str_replace(['"', '[', ']'], '', json_encode($proforma_code));
                $file_tmp_name = str_replace(['"', '[', ']'], '', json_encode($_FILES['proforma']['tmp_name'][$i]));

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
                        $orderEntryDate      = trim($objWorksheet->getCellByColumnAndRow(1, 1)->getValue());
                        $salesOrderNumber    = trim($objWorksheet->getCellByColumnAndRow(1, 2)->getValue());
                        $customerPoNumber    = trim($objWorksheet->getCellByColumnAndRow(1, 3)->getValue());
                        $requestDeliveryDate = trim($objWorksheet->getCellByColumnAndRow(1, 4)->getValue());
                        $salesInvoiceNumber  = trim($objWorksheet->getCellByColumnAndRow(1, 5)->getValue());

                        // =============== ADDITIONALS AND DEDUCTIONSs =============== //


                        // check item existence => MARIEL TARAY //      
                        for ($t = 8 + 1; $t <= $highestRow; $t++) {
                            $navItemCode = trim($objWorksheet->getCellByColumnAndRow(1, $t)->getValue());
                            if( !empty($navItemCode)){
                                $itemInMast  = $this->povsproforma_model->checkItemInSetup($navItemCode, $data['supplierSelect']);
                                if( is_null($itemInMast)){
                                    $itemNotFound[] = $navItemCode;
                                    $itemDontExistCount ++ ;
                                }
                            }                           
                        }   
                        if ($itemDontExistCount > 0) {
                            $msg = ['message' => 'Item(s) not found in masterfile!', 'info' => 'Item', 'item' => $itemNotFound];
                            JSONResponse($msg);
                            exit();
                        }
                        // check item existence => MARIEL TARAY //

                        $proformaHeader =
                            [
                                'order_date'    => $orderEntryDate,
                                'delivery_date' => $requestDeliveryDate,
                                'so_no'         => $salesOrderNumber,
                                'order_no'      => $customerPoNumber,
                                'sales_invoice_no' => $salesInvoiceNumber,
                                'proforma_code' => $proformaName,
                                'supplier_id'   => $data['supplierSelect'],
                                'customer_code' => $data['customerSelect'],
                                'po_header_id'  => $data['poSelect'],
                                'status'        => 'PENDING',
                                'user_id'       => $this->session->userdata('user_id'),
                                'date_uploaded' => date('Y-m-d'),
                                'entry_type'    => 'UPLOADED'
                            ];

                        $this->db->insert('proforma_header', $proformaHeader);
                        $proformaHeaderID = $this->db->insert_id();

                        // =============== PROFORMA LINE =============== //
                        for ($x = 8 + 1; $x <= $highestRow; $x++) {

                            $item_code          = trim($objWorksheet->getCellByColumnAndRow(0, $x)->getValue());
                            $customer_item_code = trim($objWorksheet->getCellByColumnAndRow(1, $x)->getValue());
                            $description        = trim($objWorksheet->getCellByColumnAndRow(2, $x)->getValue());
                            $quantity           = trim($objWorksheet->getCellByColumnAndRow(3, $x)->getValue());
                            $uom                = trim($objWorksheet->getCellByColumnAndRow(4, $x)->getValue());
                            $price              = trim($objWorksheet->getCellByColumnAndRow(5, $x)->getValue());
                            $amount             = trim($objWorksheet->getCellByColumnAndRow(6, $x)->getValue());
                            $batch_code         = '';
                            $discount           = '';

                            
                            if (empty($item_code) && empty($customer_item_code) && empty($description) && empty($quantity) && empty($price) && empty($amount)) {
                                break;
                            } else if (empty($item_code) && !empty($description) && !empty($quantity) && !empty($price) && !empty($amount)) {
                                $msg = ['message' => 'Item Code is empty, please review.', 'info' => 'Error'];
                                JSONResponse($msg);
                                exit();
                            } 
                            else if(!empty($item_code) && empty($customer_item_code) && !empty($description) && !empty($quantity) && !empty($price) && !empty($amount))
                            {
                                $msg = ['message' => 'Customer Material is empty, please review.', 'info' => 'Error'];
                                JSONResponse($msg);
                                exit();
                            }
                            else if (!empty($item_code) && !empty($customer_item_code) && empty($description) && !empty($quantity) && !empty($price) && !empty($amount)) {
                                $msg = ['message' => 'Description is empty, please review.', 'info' => 'Error'];
                                JSONResponse($msg);
                                exit();
                            } else if (!empty($item_code) && !empty($customer_item_code) && !empty($description) && empty($quantity) && !empty($price) && !empty($amount)) {
                                $msg = ['message' => 'Quantity is empty, please review.', 'info' => 'Error'];
                                JSONResponse($msg);
                                exit();
                            } else if (!empty($item_code) && !empty($customer_item_code) && !empty($description) && !empty($quantity) && empty($price) && !empty($amount)) {
                                $msg = ['message' => 'Price is empty, please review.', 'info' => 'Error'];
                                JSONResponse($msg);
                                exit();
                            } else if (!empty($item_code) && !empty($customer_item_code) && !empty($description) && !empty($quantity) && !empty($price) && empty($amount)) {
                                $msg = ['message' => 'Amount is empty, please review.', 'info' => 'Error'];
                                JSONResponse($msg);
                                exit();
                            } else {

                                if ($price == 'FREE' || $price == 'free') {
                                    $proformaLine =
                                        [
                                            'item_code'          => $item_code,
                                            'itemcode_loc'       => $customer_item_code,
                                            'description'        => $description,
                                            'qty'                => $quantity,
                                            'uom'                => $uom,
                                            'price'              => str_replace(',', '', '0.00'),
                                            'amount'             => str_replace(',', '', '0.00'),
                                            'supplier_id'        => $data['supplierSelect'],
                                            'customer_code'      => $data['customerSelect'],
                                            'proforma_header_id' => $proformaHeaderID,
                                            'free'               => '1'
                                        ];
                                } else {

                                    $proformaLine =
                                        [
                                            'item_code'          => $item_code,
                                            'itemcode_loc'       => $customer_item_code,
                                            'description'        => $description,
                                            'qty'                => $quantity,
                                            'uom'                => $uom,
                                            'price'              => str_replace(',', '', $price),
                                            'amount'             => str_replace(',', '', $amount),
                                            'supplier_id'        => $data['supplierSelect'],
                                            'customer_code'      => $data['customerSelect'],
                                            'proforma_header_id' => $proformaHeaderID
                                        ];
                                }
                            }


                            $this->db->insert('proforma_line', $proformaLine);
                        }

                        for ($z = 8 + 1; $z <= $highestRow; $z++) {
                            $description = trim($objWorksheet->getCellByColumnAndRow(7, $z)->getValue());
                            $amount      = trim($objWorksheet->getCellByColumnAndRow(8, $z)->getValue());

                            if (empty($description)) {
                                break;
                            } else {
                                $ad =
                                    [
                                        'discount'           => $description,
                                        'total_discount'     => $amount,
                                        'proforma_header_id' => $proformaHeaderID,
                                        'supplier_id'        => $data['supplierSelect'],
                                        'user_id'            => $this->session->userdata('user_id')
                                    ];
                            }

                            $this->db->insert('discountvat', $ad);
                        }
                    } else {
                        $msg = ['message' => 'File Format not supported.', 'info' => 'Invalid Format'];
                        JSONResponse($msg);
                        exit();
                    }

                    $report_status =
                        [
                            'po_header_id'       => $data['poSelect'],
                            'proforma_header_id' => $proformaHeaderID,
                            'pi_head_id'         => 0,
                            'crf_id'             => 0,
                            'proforma_stat'      => 0,
                            'pi_stat'            => 0,
                            'crf_stat'           => 0,
                            'supplier_id'        => $data['supplierSelect'],
                            'customer_code'      => $data['customerSelect'],
                            'user_id'            => $this->session->userdata('user_id')
                        ];

                    $this->db->insert('report_status', $report_status);
                    // move_uploaded_file($_FILES['proforma']['tmp_name'], $target);

                    /**** ADDED BY MARIEL TARAY ***/
                    $saveDocument = ['document_name'  => $file_name,
                                     'document_type'  => 'Proforma Sales Invoice',
                                     'document_path'  => "files/POVSPROFORMA/".$file_name,
                                     'uploaded_on'    => date("Y-m-d H:i:s"),
                                     'supplier_id'    => $data['supplierSelect'],
                                     'customer_code'  => $data['customerSelect'],
                                     'user_id'        => $this->session->userdata('user_id')] ;                                                    
                    $this->db->insert('uploaded_transaction_documents', $saveDocument);
                    move_uploaded_file($_FILES['proforma']['tmp_name'][$i],"files/POVSPROFORMA/".$file_name);
                    /*** ADDED BY MARIEL TARAY **/
                } else {
                    $msg = ['message' => 'No File Uploaded.', 'info' => 'No Data'];
                    JSONResponse($msg);
                    exit();
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $error = array('action' => 'Uploading Proforma', 'error_msg' => $this->db->error()); //Log error message to `error_log` table
                $this->db->insert('error_log', $error);
                $msg = ['message' => 'Error: Failed Uploading Proforma', 'info' => 'Error'];
            } else {
                $msg = ['message' => 'Proforma Upload Complete.', 'info' => 'Uploaded'];
            }
        } else {
            $msg = ['message' => 'Data seems to be empty.', 'info' => 'No Data'];
        }

        JSONResponse($msg);
    }

    #*** KING ARTHUR ADDITIONALS ***#
    public function additionals()
    {
        $data           = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $checkDuplicate = array();
        $proformaLine   = array();
        $msg            = array();
        $ad             = array();
        $proforma_code  = '';

        if (!empty($data)) {
            $this->db->trans_start();

            for ($i = 0; $i < count($_FILES['proforma2']['name']); $i++) {

                $acroname       = $this->povsproforma_model->getAcroname($data['supplierID']);
                $proforma_code  = $_FILES['proforma2']['name'][$i];
                $proformaName   = pathinfo($proforma_code, PATHINFO_FILENAME);
                $target         = getcwd() . DIRECTORY_SEPARATOR . 'files/POVSPROFORMA/' . $proforma_code;
                $checkDuplicate = $this->povsproforma_model->checkDuplicate($proformaName);

                if (!empty($checkDuplicate)) {
                    $msg = ['message' => 'Proforma already exists.', 'info' => 'Duplicate'];
                    JSONResponse($msg);
                }

                $file_name     = str_replace(['"', '[', ']'], '', json_encode($proforma_code));
                $file_tmp_name = str_replace(['"', '[', ']'], '', json_encode($_FILES['proforma2']['tmp_name'][$i]));

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
                        $orderEntryDate      = trim($objWorksheet->getCellByColumnAndRow(1, 1)->getValue());
                        $salesOrderNumber    = trim($objWorksheet->getCellByColumnAndRow(1, 2)->getValue());
                        $customerPoNumber    = trim($objWorksheet->getCellByColumnAndRow(1, 3)->getValue());
                        $requestDeliveryDate = trim($objWorksheet->getCellByColumnAndRow(1, 4)->getValue());
                        $salesInvoiceNumber  = trim($objWorksheet->getCellByColumnAndRow(1, 5)->getValue());

                        // =============== ADDITIONALS AND DEDUCTIONSs =============== //

                        $proformaHeader =
                            [
                                'order_date'    => $orderEntryDate,
                                'delivery_date' => $requestDeliveryDate,
                                'so_no'         => $salesOrderNumber,
                                'order_no'      => $customerPoNumber,
                                'sales_invoice_no' => $salesInvoiceNumber,
                                'proforma_code' => $proformaName,
                                'supplier_id'   => $data['supplierID'],
                                'customer_code' => $data['locationID'],
                                'po_header_id'  => $data['po_header_id'],
                                'status'        => 'PENDING',
                                'user_id'       => $this->session->userdata('user_id'),
                                'date_uploaded' => date('Y-m-d'),
                                'entry_type'    => 'UPLOADED'
                            ];

                        $this->db->insert('proforma_header', $proformaHeader);
                        $proformaHeaderID = $this->db->insert_id();

                        // =============== PROFORMA LINE =============== //
                        for ($x = 8 + 1; $x <= $highestRow; $x++) {

                            $item_code          = trim($objWorksheet->getCellByColumnAndRow(0, $x)->getValue());
                            $customer_item_code = trim($objWorksheet->getCellByColumnAndRow(1, $x)->getValue());
                            $description        = trim($objWorksheet->getCellByColumnAndRow(2, $x)->getValue());
                            $quantity           = trim($objWorksheet->getCellByColumnAndRow(3, $x)->getValue());
                            $uom                = trim($objWorksheet->getCellByColumnAndRow(4, $x)->getValue());
                            $price              = trim($objWorksheet->getCellByColumnAndRow(5, $x)->getValue());
                            $amount             = trim($objWorksheet->getCellByColumnAndRow(6, $x)->getValue());
                            $batch_code         = '';
                            $discount           = '';

                            if (empty($item_code) && empty($customer_item_code) && empty($description) && empty($quantity) && empty($price) && empty($amount)) {
                                break;
                            } else if (empty($item_code) && !empty($description) && !empty($quantity) && !empty($price) && !empty($amount)) {
                                $msg = ['message' => 'Item Code is empty, please review.', 'info' => 'Error'];
                                JSONResponse($msg);
                                exit();
                            } 
                            else if(!empty($item_code) && empty($customer_item_code) && !empty($description) && !empty($quantity) && !empty($price) && !empty($amount))
                            {
                                $msg = ['message' => 'Customer Material is empty, please review.', 'info' => 'Error'];
                                JSONResponse($msg);
                                exit();
                            }
                            else if (!empty($item_code) && !empty($customer_item_code) && empty($description) && !empty($quantity) && !empty($price) && !empty($amount)) {
                                $msg = ['message' => 'Description is empty, please review.', 'info' => 'Error'];
                                JSONResponse($msg);
                                exit();
                            } else if (!empty($item_code) && !empty($customer_item_code) && !empty($description) && empty($quantity) && !empty($price) && !empty($amount)) {
                                $msg = ['message' => 'Quantity is empty, please review.', 'info' => 'Error'];
                                JSONResponse($msg);
                                exit();
                            } else if (!empty($item_code) && !empty($customer_item_code) && !empty($description) && !empty($quantity) && empty($price) && !empty($amount)) {
                                $msg = ['message' => 'Price is empty, please review.', 'info' => 'Error'];
                                JSONResponse($msg);
                                exit();
                            } else if (!empty($item_code) && !empty($customer_item_code) && !empty($description) && !empty($quantity) && !empty($price) && empty($amount)) {
                                $msg = ['message' => 'Amount is empty, please review.', 'info' => 'Error'];
                                JSONResponse($msg);
                                exit();
                            } else {

                                if ($price == 'FREE' || $price == 'free') {
                                    $proformaLine =
                                        [
                                            'item_code'          => $item_code,
                                            'itemcode_loc'       => $customer_item_code,
                                            'description'        => $description,
                                            'qty'                => $quantity,
                                            'uom'                => $uom,
                                            'price'              => str_replace(',', '', '0.00'),
                                            'amount'             => str_replace(',', '', '0.00'),
                                            'supplier_id'        => $data['supplierID'],
                                            'customer_code'      => $data['locationID'],
                                            'proforma_header_id' => $proformaHeaderID,
                                            'free'               => '1',
                                            'additional'        => 'Yes'
                                        ];
                                } else {

                                    $proformaLine =
                                        [
                                            'item_code'          => $item_code,
                                            'itemcode_loc'       => $customer_item_code,
                                            'description'        => $description,
                                            'qty'                => $quantity,
                                            'uom'                => $uom,
                                            'price'              => str_replace(',', '', $price),
                                            'amount'             => str_replace(',', '', $amount),
                                            'supplier_id'        => $data['supplierID'],
                                            'customer_code'      => $data['locationID'],
                                            'proforma_header_id' => $proformaHeaderID,
                                            'additional'        => 'Yes'
                                        ];
                                }
                            }


                            $this->db->insert('proforma_line', $proformaLine);
                        }

                        for ($z = 8 + 1; $z <= $highestRow; $z++) {
                            $description = trim($objWorksheet->getCellByColumnAndRow(7, $z)->getValue());
                            $amount      = trim($objWorksheet->getCellByColumnAndRow(8, $z)->getValue());

                            if (empty($description)) {
                                break;
                            } else {
                                $ad =
                                    [
                                        'discount'           => $description,
                                        'total_discount'     => $amount,
                                        'proforma_header_id' => $proformaHeaderID,
                                        'supplier_id'        => $data['supplierSelect'],
                                        'user_id'            => $this->session->userdata('user_id')
                                    ];
                            }

                            $this->db->insert('discountvat', $ad);
                        }
                    } else {
                        $msg = ['message' => 'File Format not supported.', 'info' => 'Invalid Format'];
                        JSONResponse($msg);
                        exit();
                    }

                    $report_status =
                        [
                            'po_header_id'       => $data['po_header_id'],
                            'proforma_header_id' => $proformaHeaderID,
                            'pi_head_id'         => 0,
                            'crf_id'             => 0,
                            'proforma_stat'      => 0,
                            'pi_stat'            => 0,
                            'crf_stat'           => 0,
                            'supplier_id'        => $data['supplierID'],
                            'customer_code'      => $data['locationID'],
                            'user_id'            => $this->session->userdata('user_id')
                        ];

                    $this->db->insert('report_status', $report_status);

                    /**** ADDED BY MARIEL TARAY ***/
                    $saveDocument = ['document_name'  => $file_name,
                                     'document_type'  => 'Proforma Sales Invoice',
                                     'document_path'  => "files/POVSPROFORMA/".$file_name,
                                     'uploaded_on'    => date("Y-m-d H:i:s"),
                                     'supplier_id'    => $data['supplierID'],
                                     'customer_code'  => $data['locationID'],
                                     'user_id'        => $this->session->userdata('user_id')] ;                                                    
                    $this->db->insert('uploaded_transaction_documents', $saveDocument);
                    move_uploaded_file($_FILES['proforma2']['tmp_name'][$i],"files/POVSPROFORMA/".$file_name);
                    /*** ADDED BY MARIEL TARAY **/
                } else {
                    $msg = ['message' => 'No File Uploaded.', 'info' => 'No Data'];
                    JSONResponse($msg);
                    exit();
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $error = array('action' => 'Uploading Proforma', 'error_msg' => $this->db->error()); //Log error message to `error_log` table
                $this->db->insert('error_log', $error);
                $msg = ['message' => 'Error: Failed Uploading Proforma', 'info' => 'Error'];
            } else {
                $msg = ['message' => 'Proforma Upload Complete.', 'info' => 'Uploaded'];
            }
        } else {
            $msg = ['message' => 'Data seems to be empty.', 'info' => 'No Data'];
        }

        JSONResponse($msg);
    }
    #*** KING ARTHUR ADDITIONALS ***#

    public function getPendingMatches()
    {
        $data     = $this->input->post(NULL, FILTER_SANITIZE_STRING);

        if (!empty($data)) {

            $this->db->trans_start();
            $result   = $this->povsproforma_model->getPendingMatches($data['supplier_id'], $data['customer_code'], $data['from'], $data['to']);
            // dump($result);
            // die();
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                JSONResponse($this->db->log_message());
            } else {
                return JSONResponse($result);
            }
        }
    }

    public function matchPOandProforma()
    {
        $data             = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $po_header        = $this->povsproforma_model->matchPOHeaders($data['container2']['po_header_id']);
        $prof_header      = $this->povsproforma_model->matchProfHeaders($data['container2']['proforma_header_id']);
        $doc_no           = $this->povsproforma_model->getDocNo(true);
        $transaction_id   = array();

        $msg              = array();
        $forTransaction   = array();
        $no_match1        = array();
        $no_match2        = array();
        $length           = 0;
        $status           = 0;
        $matchStatus      = '';
        $file             = '';
        $PONOMATCH        = array();
        $PROFNOMATCH      = array();

        $po_Served          = array();
        $proforma_Served    = array();
        $po_NotServed       = array();
        $proforma_NotServed = array();
        $proforma_OverServe = array();
        // var_dump($data);
        // die();

        if ($po_header['po_header_id'] == $prof_header['po_header_id']) {
            if (!empty($data['container1'])) {

                $this->db->trans_start();

                foreach ($data['container1'] as $value) {

                    if ($value['po_item'] != '' && $value['pr_item'] != '') { // IF PO ARE SERVED

                        $po_Served[] = $value['po_item'];
                        $proforma_Served[] = $value['pr_item'];
                    } else if ($value['po_item'] == '' &&  $value['pr_item'] == '') { // IF NO ITEM FOUND - ALL NOT SERVED

                        $po_NotServed[] = $value['po_item'];
                        $proforma_NotServed[] = $value['pr_item'];
                    } else if ($value['po_item'] != '' &&  $value['pr_item'] == '') { // IF PO NOT SERVED

                        $po_NotServed[] = $value['po_item'];
                    } else if ($value['po_item'] == '' &&  $value['pr_item'] != '') { // IF PROFORMA OVER SERVED

                        $proforma_OverServe[] = $value['pr_item'];
                    }
                }

                if (!empty($po_Served)) {
                    if (empty($po_NotServed) && empty($proforma_OverServe)) {
                        $status      = 1;
                        $matchStatus = 'Matched';
                    } else if (!empty($po_NotServed) && !empty($proforma_OverServe)) {
                        $status      = 2;
                        $matchStatus = 'Served, Not served , and Overserved';
                    } else if (empty($po_NotServed) && !empty($proforma_OverServe)) {
                        $status      = 2;
                        $matchStatus = 'Served and Overserved';
                    } else if (!empty($po_NotServed) && empty($proforma_OverServe)) {
                        $status      = 2;
                        $matchStatus = 'Served and Not served';
                    }
                } else {
                    $status      = 3;
                    $matchStatus = 'Matching Failed';
                }

                if ($status == 1) {
                    //Proforma Header Status Update
                    $POHASMATCH   = $this->povsproforma_model->reArray($po_Served);
                    $PROFHASMATCH = $this->povsproforma_model->reArray($proforma_Served);
                    $finalStatus  = ['status' => 'MATCHED'];
                    $statusUpdate = ['proforma_stat' => $status];

                    for ($length; $length < count($po_Served); $length++) {
                        $tData = $this->povsproforma_model->transactionData($POHASMATCH[$length], $PROFHASMATCH[$length], $data['container2']['po_header_id']);

                        $forTransaction[] =
                            [
                                'po_itemcode'    => $tData->po_itemcode,
                                'po_qty'         => $tData->po_qty,
                                'po_price'       => $tData->po_price,
                                'po_amount'      => $tData->po_amount,
                                'pf_itemcode'    => $tData->pf_itemcode,
                                'pf_description' => $tData->pf_description,
                                'pf_qty'         => $tData->pf_qty,
                                'pf_price'       => $tData->pf_price,
                                'pf_amount'      => $tData->pf_amount,
                            ];

                        // var_dump($tData);
                    }

                    $transaction =
                        [
                            'tr_no'              => $doc_no,
                            'tr_date'            => date("F d, Y - h:i:s A"),
                            'po_header_id'       => $data['container2']['po_header_id'],
                            'proforma_header_id' => $data['container2']['proforma_header_id'],
                            'supplier_id'        => $po_header['supplier_id'],
                            'customer_code'      => $po_header['customer_code'],
                            'user_id'            => $this->session->userdata('user_id'),
                            'status'             => reset($finalStatus)
                        ];

                    // $this->povsproforma_model->update($data['container2']['proforma_code'], 'proforma_code', 'proforma_header', $finalStatus);
                    // $this->povsproforma_model->update($data['container2']['rep_stat_id'], 'rep_stat_id', 'report_status', $statusUpdate);
                    // $this->povsproforma_model->update($po_header['po_reference'], 'po_reference', 'po_header', $finalStatus);

                    // $this->db->insert('povprof_transaction', $transaction);
                    $transaction_id = $this->db->insert_id();
                } else if ($status == 2) {
                    $POHASMATCH   = $this->povsproforma_model->reArray($po_Served);
                    $PROFHASMATCH = $this->povsproforma_model->reArray($proforma_Served);
                    $PONOMATCH    = $this->povsproforma_model->reArray($po_NotServed);
                    $PROFNOMATCH  = $this->povsproforma_model->reArray($proforma_OverServe);
                    $finalStatus  = ['status' => 'MATCHED-VARIANCE'];
                    $statusUpdate = ['proforma_stat' => $status];


                    if (!empty($proforma_OverServe)) {
                        for ($length; $length < count($proforma_OverServe); $length++) {
                            $noMatch1[] = $this->povsproforma_model->getItems1($PROFNOMATCH[$length], $data['container2']['po_header_id']);
                        }

                        foreach ($noMatch1 as $key => $value) {

                            $no_match1[] =
                                [
                                    'item_code'   => $value['item_code'],
                                    'description' => $value['description'],
                                    'qty'         => $value['qty'],
                                    'price'       => $value['price'],
                                    'amount'      => $value['amount']
                                ];
                        }

                        $length = 0;
                    }

                    if (!empty($po_NotServed)) {
                        for ($length; $length < count($po_NotServed); $length++) {
                            $noMatch2[] = $this->povsproforma_model->getData4($PONOMATCH[$length], 'item_code', 'po_line');
                        }

                        foreach ($noMatch2 as $key => $value) {
                            $no_match2[] =
                                [
                                    'item_code' => $value['item_code'],
                                    'qty'       => $value['qty'],
                                    'price'     => $value['direct_unit_cost']
                                ];
                        }

                        $length = 0;
                    }

                    for ($length; $length < count($po_Served); $length++) {
                        $tData = $this->povsproforma_model->transactionData($POHASMATCH[$length], $PROFHASMATCH[$length], $data['container2']['po_header_id']);

                        // var_dump($tData);
                        $forTransaction[] =
                            [
                                'po_itemcode'    => $tData->po_itemcode,
                                'po_qty'         => $tData->po_qty,
                                'po_price'       => $tData->po_price,
                                'po_amount'      => $tData->po_amount,
                                'pf_itemcode'    => $tData->pf_itemcode,
                                'pf_description' => $tData->pf_description,
                                'pf_qty'         => $tData->pf_qty,
                                'pf_price'       => $tData->pf_price,
                                'pf_amount'      => $tData->pf_amount
                            ];
                    }

                    // FOR PO VS PROFORMA TRANSACTION TABLE
                    $transaction =
                        [
                            'tr_no'              => $doc_no,
                            'tr_date'            => date("F d, Y - h:i:s A"),
                            'po_header_id'       => $data['container2']['po_header_id'],
                            'proforma_header_id' => $data['container2']['proforma_header_id'],
                            'supplier_id'        => $po_header['supplier_id'],
                            'customer_code'      => $po_header['customer_code'],
                            'user_id'            => $this->session->userdata('user_id'),
                            'status'             => reset($finalStatus)
                        ];

                    // $this->povsproforma_model->update($data['container2']['proforma_code'], 'proforma_code', 'proforma_header', $finalStatus);
                    // $this->povsproforma_model->update($data['container2']['rep_stat_id'], 'rep_stat_id', 'report_status', $statusUpdate);
                    // $this->povsproforma_model->update($po_header['po_reference'], 'po_reference', 'po_header', $finalStatus);
                    // $this->db->insert('povprof_transaction', $transaction);
                    $transaction_id = $this->db->insert_id();
                } else if ($status == 3) {
                    $statusUpdate = ['proforma_stat' => $status];
                    // $this->povsproforma_model->update($data['container2']['rep_stat_id'], 'rep_stat_id', 'report_status', $statusUpdate);
                }

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $error = array('action' => 'Matching and Saving Data', 'error_msg' => $this->db->error()); //Log error message to `error_log` table
                    $this->db->insert('error_log', $error);
                    $msg = ['message' => 'Error: Failed saving some data.', 'info' => 'Error'];
                } else if ($matchStatus == 'Matching Failed') {
                    $msg =
                        [
                            'message' => "Matching Failed, PO and Proforma do not match. Item code was not set up yet or has no match.",
                            'info'    => $matchStatus
                        ];
                } else if ($matchStatus == 'Served and Not served') {
                    $file = $this->generatePOvsProformaReport($transaction_id, $po_header, $prof_header, $forTransaction, $data['container2']['supplier_code'], $data['container2']['customer_code'], $no_match1, $no_match2);
                    $msg =
                        [
                            'message' => "Matched Successfully, but there are items in PO that has not served.",
                            'info'    => $matchStatus,
                            'file'    => $file
                        ];

                    $msg['item_codes'] = $PONOMATCH;
                } else if ($matchStatus == 'Served and Overserved') {
                    $file = $this->generatePOvsProformaReport($transaction_id, $po_header, $prof_header, $forTransaction, $data['container2']['supplier_code'], $data['container2']['customer_code'], $no_match1, $no_match2);
                    $msg =
                        [
                            'message' => "Matched Successfully, but there are items in Proforma that are overserved.",
                            'info'    => $matchStatus,
                            'file'    => $file
                        ];

                    $msg['item_codes'] = $PROFNOMATCH;
                } else if ($matchStatus == 'Served, Not served , and Overserved') {
                    $file = $this->generatePOvsProformaReport($transaction_id, $po_header, $prof_header, $forTransaction, $data['container2']['supplier_code'], $data['container2']['customer_code'], $no_match1, $no_match2);
                    $msg =
                        [
                            'message' => "Matched Successfully, but there are items in PO that has not served, and Items in Proforma that are overserved.",
                            'info'    => $matchStatus,
                            'file'    => $file
                        ];

                    $msg['po_items'] = $PONOMATCH;
                    $msg['pr_items'] = $PROFNOMATCH;
                } else if ($matchStatus == 'Matched') {
                    $file = $this->generatePOvsProformaReport($transaction_id, $po_header, $prof_header, $forTransaction, $data['container2']['supplier_code'], $data['container2']['customer_code'], $no_match1, $no_match2);
                    $msg = ['message' => 'PO VS Proforma Matched', 'info' => $matchStatus, 'file' => $file];
                }
            } else {
                $msg = ['message' => 'PO Line and Proforma Line not found.', 'info' => 'Error retrieving lines'];
            }
        } else {
            $msg = ['message' => 'Failed Matching Headers, PO Header and Proforma Header do not Match.', 'info' => 'Error matching headers'];
        }

        echo json_encode($msg);
    }

    public function generatePOvsProformaReport($transaction_id, $poHeader, $profHeader, $lines, $supplier, $customer, $nomatch1, $nomatch2)
    {
        $PO_Total       = 0;
        $Prof_Total     = 0;
        $VATAmount      = 0;
        $variance1      = 0;
        $variance2      = 0;
        $discount_total = 0;
        $poQty          = 0;
        $proformaQty    = 0;

        $supp     = $this->povsproforma_model->getData2($supplier, 'supplier_code', 'suppliers');
        $cust     = $this->povsproforma_model->getData2($customer, 'customer_code', 'customers');
        // $discount = $this->povsproforma_model->getData3($profHeader['proforma_header_id'], 'proforma_header_id', 'discountvat');

        $proforma  = $this->povsproforma_model->getProformaUploadedToPO($poHeader['po_header_id']);
        $proformas = array();

        foreach ($proforma as $key => $value) {
            $proformas[] = $value['proforma_code'];
        }

        $pdf = new FPDF('L', 'mm', 'Legal');
        $pdf->AddPage();
        $pdf->setDisplayMode('fullpage');

        // ========== HEADER START ========== //
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(40, 0, $supp->supplier_name . ' TO ' . $cust->customer_name, 0, 0, 'L');

        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(40, 0, 'PO vs PROFORMA SUPPLIER INVOICE - VARRIANCE REPORT', 0, 0, 'L');
        $pdf->Ln(5);
        $pdf->Cell(40, 0, 'SO Date : ' . date("Y-m-d", strtotime($profHeader['order_date'])), 0, 0, 'L');
        $pdf->Ln(5);
        $pdf->Cell(40, 0, 'SO No : ' . $profHeader['so_no'], 0, 0, 'L');
        $pdf->Ln(5);
        $pdf->Cell(40, 0, 'PO No : ' . $profHeader['order_no'] . '/' . $poHeader['po_no'] . '/' . $poHeader['po_reference'], 0, 0, 'L');
        $pdf->Ln(5);
        $pdf->Cell(40, 0, 'Proforma No : ' . implode(", ", $proformas), 0, 0, 'L');
        // ========== HEADER END ========== //

        $pdf->Ln(10);

        // ==============  PO LINES AND PROFORMA LINES MATCHED START ============== //
        $pdf->setFont('Arial', 'B', 12);
        $pdf->Cell(150, 5, 'Purchase Order Items', 0, 0, 'L');
        $pdf->cell(3, 6, " ", 0, 0, 'L');
        $pdf->Cell(160, 5, 'Proforma Items', 0, 0, 'L');
        $pdf->ln();

        $pdf->SetTextColor(201, 201, 201);
        $pdf->SetFillColor(35, 35, 35);
        $pdf->setFont('Arial', '', 9);
        $pdf->cell(20, 6, "PO Item No.", 1, 0, 'C', TRUE);
        $pdf->cell(70, 6, "Description", 1, 0, 'C', TRUE);
        $pdf->cell(15, 6, "Qty", 1, 0, 'C', TRUE);
        $pdf->cell(20, 6, "Price", 1, 0, 'C', TRUE);
        $pdf->cell(25, 6, "Amount", 1, 0, 'C', TRUE);

        $pdf->cell(3, 6, " ", 0, 0, 'L');

        $pdf->cell(20, 6, "PSI Item No.", 1, 0, 'C', TRUE);
        $pdf->cell(60, 6, "Description", 1, 0, 'C', TRUE);
        $pdf->cell(15, 6, "Qty", 1, 0, 'C', TRUE);
        $pdf->cell(20, 6, "Price", 1, 0, 'C', TRUE);
        $pdf->cell(20, 6, "VAT", 1, 0, 'C', TRUE);
        $pdf->cell(25, 6, "Amount", 1, 0, 'C', TRUE);
        // $pdf->cell(25, 6, "Disc Amt", 1, 0, 'C', TRUE);
        $pdf->setFont('Arial', 'B', 8);
        $pdf->cell(25, 6, "Total VAT Per Item", 1, 0, 'C', TRUE);

        foreach ($lines as $value) {

            $po_desc  = $this->povsproforma_model->getData2($value['po_itemcode'], 'itemcode_loc', 'items');

            $pdf->SetTextColor(0, 0, 0);
            $pdf->ln();

            $pdf->setFont('Arial', '', 9);
            $pdf->cell(20, 5, $value['po_itemcode'], 1, 0, 'C');
            $pdf->cell(70, 5, $po_desc->description, 1, 0, 'L');
            $pdf->cell(15, 5, $value['po_qty'], 1, 0, 'C');
            $pdf->cell(20, 5, "P " . number_format($value['po_price'], 2), 1, 0, 'R');
            $pdf->cell(25, 5, "P " . number_format($value['po_amount'], 2), 1, 0, 'R');

            $pdf->cell(3, 6, " ", 0, 0, 'L');

            $pdf->cell(20, 5, $value['pf_itemcode'], 1, 0, 'C');
            $pdf->cell(60, 5, $value['pf_description'], 1, 0, 'L');
            $pdf->cell(15, 5, $value['pf_qty'], 1, 0, 'C');
            $pdf->cell(20, 5, "P " . number_format($value['pf_price'], 2), 1, 0, 'R');
            $pdf->cell(20, 5, "P " . number_format($value['po_price'] - $value['pf_price'], 2), 1, 0, 'R');
            $pdf->cell(25, 5, "P " . number_format($value['pf_amount'], 2), 1, 0, 'R');
            // $pdf->cell(25, 5, "P 0.00", 1, 0, 'R');
            $pdf->cell(25, 5, "P " . (number_format($value['po_amount'] - $value['pf_amount'], 2)), 1, 0, 'R');

            $PO_Total   += $value['po_amount'];
            $Prof_Total += $value['pf_amount'];
            $VATAmount  += $value['po_amount'] - $value['pf_amount'];
            $poQty      += $value['po_qty'];
            $proformaQty += $value['pf_qty'];
        }

        $pdf->setFont('Arial', 'B', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->ln();

        $pdf->cell(150, 5, "TOTAL:", 1, 0, 'L');
        $pdf->cell(0.5, 5, "P " . number_format($PO_Total, 2), 0, 0, 'R');

        $pdf->cell(2.5, 6, " ", 0, 0, 'L');

        $pdf->cell(160, 5, "TOTAL:", 1, 0, 'L');
        $pdf->cell(0.1, 5, "P " . number_format($Prof_Total, 2), 0, 0, 'R');
        // $pdf->cell(25, 5, "P 0.00", 1, 0, 'R');
        // $pdf->cell(25, 5, "", "B", 0, 'R');
        $pdf->cell(25, 5, "P " . number_format($VATAmount, 2), 1, 0, 'R');

        $pdf->ln();
        $pdf->ln();

        $pdf->setFont('Arial', 'B', 10);
        $pdf->cell(40, 5, "Total Match Count : " . count($lines), 0, 0, 'L');
        $pdf->cell(47, 5, "Total Match PO QTY : " . $poQty, 0, 0, 'L');
        $pdf->cell(20, 5, "Total Match Proforma QTY : " . $proformaQty, 0, 0, 'L');

        $pdf->ln();
        $pdf->ln();
        // ==============  PO LINES AND PROFORMA LINES MATCHED END ============== //
        // ==============  PO LINES AND PROFORMA LINES VARIANCES START ============== //
        if (!empty($nomatch2) && !empty($nomatch1)) :
            $pdf->__currentY = $pdf->GetY();

            $pdf->setFont('Arial', 'B', 12);
            $pdf->Cell(150, 5, 'PO Unserved Items', 0, 0, 'L');
            $pdf->ln();
            $pdf->SetTextColor(201, 201, 201);
            $pdf->SetFillColor(35, 35, 35);
            $pdf->setFont('Arial', '', 9);
            $pdf->cell(20, 6, "PO Item No.", 1, 0, 'C', TRUE);
            $pdf->cell(70, 6, "PO Description", 1, 0, 'C', TRUE);
            $pdf->cell(15, 6, "Qty", 1, 0, 'C', TRUE);
            $pdf->cell(20, 6, "Price", 1, 0, 'C', TRUE);
            $pdf->cell(25, 6, "Amount", 1, 0, 'C', TRUE);

            foreach ($nomatch2 as $value) {
                $po_desc  = $this->povsproforma_model->getData2($value['item_code'], 'itemcode_loc', 'items');
                $pdf->SetTextColor(0, 0, 0);
                $pdf->ln();

                $pdf->setFont('Arial', '', 9);
                $pdf->cell(20, 5, $value['item_code'], 1, 0, 'C');
                $pdf->cell(70, 5, $po_desc->description, 1, 0, 'L');
                $pdf->cell(15, 5, $value['qty'], 1, 0, 'C');
                $pdf->cell(20, 5, "P " . number_format($value['price'], 2), 1, 0, 'R');
                $pdf->cell(25, 5, "P " . number_format($value['price'] * $value['qty'], 2), 1, 0, 'R');

                $variance1 += $value['price'];
            }

            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->ln();

            $pdf->cell(150, 5, "TOTAL:", 1, 0, 'L');
            $pdf->cell(0.5, 5, "P " . number_format($variance1, 2), 0, 0, 'R');

            $pdf->SetXY($pdf->GetX() + 4.15, $pdf->__currentY);

            $pdf->setFont('Arial', 'B', 12);
            $pdf->Cell(150, 5, 'PROFORMA Overserved Items', 0, 0, 'L');
            $pdf->ln();
            $pdf->SetTextColor(201, 201, 201);
            $pdf->SetFillColor(35, 35, 35);
            $pdf->setFont('Arial', '', 9);
            $pdf->cell(153, 6, "", 0, 0, 'C');
            $pdf->cell(20, 6, "PSI Item No.", 1, 0, 'C', TRUE);
            $pdf->cell(70, 6, "PSI Description", 1, 0, 'C', TRUE);
            $pdf->cell(15, 6, "Qty", 1, 0, 'C', TRUE);
            $pdf->cell(20, 6, "Price", 1, 0, 'C', TRUE);
            $pdf->cell(25, 6, "Amount", 1, 0, 'C', TRUE);

            foreach ($nomatch1 as $value) {
                $pdf->SetTextColor(0, 0, 0);
                $pdf->ln();

                $pdf->cell(153, 6, "", 0, 0, 'C');
                $pdf->cell(20, 5, $value['item_code'], 1, 0, 'C');
                $pdf->cell(70, 5, $value['description'], 1, 0, 'L');
                $pdf->cell(15, 5, $value['qty'], 1, 0, 'C');
                $pdf->cell(20, 5, "P " . number_format($value['price'], 2), 1, 0, 'R');
                $pdf->cell(25, 5, "P " . number_format($value['amount'], 2), 1, 0, 'R');

                $variance2 += $value['amount'];
            }

            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->ln();

            $pdf->cell(153, 6, "", 0, 0, 'C');
            $pdf->cell(150, 5, "TOTAL:", 1, 0, 'L');
            $pdf->cell(0.1, 5, "P " . number_format($variance2, 2), 0, 0, 'R');
        elseif (!empty($nomatch2)) :
            $pdf->setFont('Arial', 'B', 12);
            $pdf->Cell(40, 5, 'PO Unserved Items', 0, 0, 'L');
            $pdf->ln();
            $pdf->SetTextColor(201, 201, 201);
            $pdf->SetFillColor(35, 35, 35);
            $pdf->setFont('Arial', '', 9);
            $pdf->cell(20, 6, "PO Item No.", 1, 0, 'C', TRUE);
            $pdf->cell(70, 6, "PO Description", 1, 0, 'C', TRUE);
            $pdf->cell(15, 6, "Qty", 1, 0, 'C', TRUE);
            $pdf->cell(20, 6, "Price", 1, 0, 'C', TRUE);
            $pdf->cell(25, 6, "Amount", 1, 0, 'C', TRUE);

            foreach ($nomatch2 as $value) {
                $po_desc  = $this->povsproforma_model->getData2($value['item_code'], 'itemcode_loc', 'items');
                $pdf->SetTextColor(0, 0, 0);
                $pdf->ln();

                $pdf->setFont('times', '', 9);
                $pdf->cell(20, 5, $value['item_code'], 1, 0, 'C');
                $pdf->cell(70, 5, $po_desc->description, 1, 0, 'L');
                $pdf->cell(15, 5, $value['qty'], 1, 0, 'C');
                $pdf->cell(20, 5, "P " . number_format($value['price'], 2), 1, 0, 'R');
                $pdf->cell(25, 5, "P " . number_format($value['price'] * $value['qty'], 2), 1, 0, 'R');

                $variance1 += $value['price'];
            }

            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->ln();

            $pdf->cell(150, 5, "TOTAL:", 1, 0, 'L');
            $pdf->cell(0.5, 5, "P " . number_format($variance1, 2), 0, 0, 'R');
        elseif (!empty($nomatch1)) :

            $pdf->setFont('Arial', 'B', 12);
            $pdf->Cell(40, 5, 'PROFORMA Overserved Items', 0, 0, 'L');
            $pdf->ln();
            $pdf->SetTextColor(201, 201, 201);
            $pdf->SetFillColor(35, 35, 35);
            $pdf->setFont('Arial', '', 9);
            $pdf->cell(20, 6, "PSI Item No.", 1, 0, 'C', TRUE);
            $pdf->cell(70, 6, "PSI Description", 1, 0, 'C', TRUE);
            $pdf->cell(15, 6, "Qty", 1, 0, 'C', TRUE);
            $pdf->cell(20, 6, "Price", 1, 0, 'C', TRUE);
            $pdf->cell(25, 6, "Amount", 1, 0, 'C', TRUE);

            foreach ($nomatch1 as $value) {
                $pdf->SetTextColor(0, 0, 0);
                $pdf->ln();

                $pdf->cell(20, 5, $value['item_code'], 1, 0, 'C');
                $pdf->cell(70, 5, $value['description'], 1, 0, 'L');
                $pdf->cell(15, 5, $value['qty'], 1, 0, 'C');
                $pdf->cell(20, 5, "P " . number_format($value['price'], 2), 1, 0, 'R');
                $pdf->cell(25, 5, "P " . number_format($value['amount'], 2), 1, 0, 'R');

                $variance2 += $value['amount'];
            }

            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->ln();

            $pdf->cell(150, 5, "TOTAL:", 1, 0, 'L');
            $pdf->cell(0.1, 5, "P " . number_format($variance2, 2), 0, 0, 'R');
            $pdf->ln();
        endif;
        // ==============  PO LINES AND PROFORMA LINES VARIANCES START ============== //

        $pdf->ln(5);
        $pdf->ln(5);
        $pdf->ln(5);

        // ============= FOOTER START ============= //

        $vat = 0;
        $p   = '';

        foreach ($proformas as $key => $value) {
            $discount[]  = $this->povsproforma_model->getAdditionalsAndDiscounts($value);
        }

        if (!empty($discount)) {
            foreach ($discount as $value) {
                for ($d = 0; $d < count($value); $d++) {

                    if ($value[$d]['proforma_code'] != $p) {
                        $pdf->ln();

                        $pdf->cell(100, 5, '', 0, 0, 'L');
                        $pdf->cell(0.5, 5, '', 0, 0, 'R');

                        $pdf->cell(150, 6, " ", 0, 0, 'L');

                        $pdf->cell(50, 5, $value[$d]['proforma_code'], 0, 0, 'L');
                    }

                    $pdf->ln();

                    $pdf->cell(100, 5, '', 0, 0, 'L');
                    $pdf->cell(0.5, 5, '', 0, 0, 'R');

                    $pdf->cell(150, 6, " ", 0, 0, 'L');

                    $pdf->cell(50, 5, $value[$d]['discount'] . " :", 'T,L,B,B', 0, 'R');
                    $pdf->cell(25, 5, number_format($value[$d]['total_discount'], 2), 'T,R,B,B', 0, 'R');

                    if ($value[$d]['discount'] == 'VAT') {
                        $vat += $value[$d]['total_discount'];
                    } else {
                        $discount_total += $value[$d]['total_discount'];
                    }

                    $p = $value[$d]['proforma_code'];
                }
                $pdf->ln();
            }

            // exit();
            $pdf->ln();

            $pdf->cell(100, 5, '', 0, 0, 'L');
            $pdf->cell(0.5, 5, '', 0, 0, 'R');

            $pdf->cell(150, 6, " ", 0, 0, 'L');

            $pdf->cell(50, 5, "Amount Due :", 1, 0, 'R');
            $pdf->cell(25, 5, number_format(($Prof_Total - $discount_total * -1) + $vat, 2), 1, 0, 'R');
        }
        // ============= FOOTER END ============= //

        $file_name = $supp->acroname . '-' . $cust->l_acroname . '-' . time() . '.pdf';

        // $this->db->insert('povprof_files', [
        //     'tr_id'        => $transaction_id,
        //     'filename'     => $file_name
        // ]);

        $pdf->Output('files/Reports/POvsProforma/' . $file_name, 'F');

        return $file_name;
    }

    public function getProforma()
    {
        $acroname           = $this->uri->segment(4);
        $po_header_id       = $this->uri->segment(5);
        $proforma_header_id = $this->uri->segment(6);
        $proforma_line      = $this->povsproforma_model->getProformaLine($proforma_header_id);
        return JSONResponse($proforma_line);
    }

    public function updateProformaLine()
    {
        $data        = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $updateData  = array();
        $getId       = array();
        $historyData = array();
        $msg         = array();

        if (!empty($data['proforma_line'])) {
            $this->db->trans_start();

            foreach ($data['proforma_line'] as $value) {
                $updateData =
                    [
                        'qty'    => $value['qty'],
                        'price'  => $value['price'],
                        'amount' => $value['price'] * $value['qty']
                    ];

                $forHistory = $this->povsproforma_model->getData2($value['id'], 'proforma_line_id', 'proforma_line');

                $historyData =
                    [
                        'proforma_header_id' => $forHistory->proforma_header_id,
                        'item_code'          => $forHistory->item_code,
                        'description'        => $forHistory->description,
                        'qty'                => $forHistory->qty,
                        'uom'                => $forHistory->uom,
                        'price'              => $forHistory->price,
                        'amount'             => $forHistory->amount,
                        'date_edited'        => date("m-d-Y"),
                        'user_id'            => $this->session->userdata('user_id'),
                        'approved_by'        => $this->session->userdata('authorize_id')
                    ];

                $this->db->insert('proforma_line_history', $historyData);
                $this->povsproforma_model->update($value['id'], 'proforma_line_id', 'proforma_line', $updateData);
            }
            $updateHead   = [  'pricing_userid'   => $this->session->userdata('user_id'),
                               'pricing_status'   => 'PRICE CHECKED',
                               'price_checked_on' => date("Y-m-d H:i:s")];    
            $this->povsproforma_model->update($forHistory->proforma_header_id, 'proforma_header_id', 'proforma_header', $updateHead);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $error = array('action' => 'Updating Proforma Line', 'error_msg' => $this->db->error()); //Log error message to `error_log` table
                $this->db->insert('error_log', $error);
                $msg = ['message' => 'Error: Failed Updating Proforma Line', 'info' => 'Error'];
            } else {
                $this->session->unset_userdata('authorize_id');
                $msg = ['message' => 'Proforma Line Successfully Updated.', 'info' => 'Updated'];
            }
        } else {
            $msg = ['message' => 'Error: Empty Data', 'info' => 'Empty'];
        }

        JSONResponse($msg);
    }

    public function getHistory()
    {
        $header_id = $this->uri->segment(4);
        $result    = $this->povsproforma_model->getHistory($header_id);
        return JSONResponse($result);
    }

    public function replaceProforma()
    {
        $data = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $ad   = array();

        if (!empty($_FILES['new_proforma']['name'])) {
            $this->db->trans_start();

            // DELETE OLD PROFORMA HEAD AND LINE
            $proforma_code = $_FILES['new_proforma']['name'];
            $proformaName  = pathinfo($proforma_code, PATHINFO_FILENAME);
            $msg           = array();
            $prof          = $this->povsproforma_model->getData2($data['proforma_header_id'], 'proforma_header_id', 'proforma_header');

            $this->povsproforma_model->delete('proforma_line', 'proforma_header_id', $data['proforma_header_id']);
            $this->povsproforma_model->delete('proforma_header', 'proforma_header_id', $data['proforma_header_id']);
            $this->povsproforma_model->delete('discountvat', 'proforma_header_id', $data['proforma_header_id']);

            $checkDuplicate = $this->povsproforma_model->checkDuplicate($proformaName);

            // GET PROFORMA DATAS
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
                    $orderEntryDate      = trim($objWorksheet->getCellByColumnAndRow(1, 1)->getValue());
                    $salesOrderNumber    = trim($objWorksheet->getCellByColumnAndRow(1, 2)->getValue());
                    $customerPoNumber    = trim($objWorksheet->getCellByColumnAndRow(1, 3)->getValue());
                    $requestDeliveryDate = trim($objWorksheet->getCellByColumnAndRow(1, 4)->getValue());

                    $proformaHeader =
                        [
                            'order_date'    => $orderEntryDate,
                            'delivery_date' => $requestDeliveryDate,
                            'so_no'         => $salesOrderNumber,
                            'order_no'      => $customerPoNumber,
                            'proforma_code' => $proformaName,
                            'supplier_id'   => $prof->supplier_id,
                            'customer_code' => $prof->customer_code,
                            'po_header_id'  => $prof->po_header_id,
                            'status'        => 'PENDING',
                            'entry_type'    => 'UPLOADED',
                            'user_id'       => $this->session->userdata('user_id')
                        ];

                    // SAVE PROFORMA DATAS
                    if (empty($checkDuplicate)) :

                        $this->db->insert('proforma_header', $proformaHeader);
                        $proformaHeaderID = $this->db->insert_id();
                    elseif ($checkDuplicate->proforma_header_id == $data['proforma_header_id']) :
                        $this->db->insert('proforma_header', $proformaHeader);
                        $proformaHeaderID = $this->db->insert_id();
                    else :
                        $msg = [
                            'message' => 'Proforma already exists.', 'info' => 'Duplicate'
                        ];
                        JSONResponse($msg);
                    endif;


                    // =============== PROFORMA LINE =============== //
                    for ($i = 6 + 1; $i <= $highestRow; $i++) {

                        $item_code          = trim($objWorksheet->getCellByColumnAndRow(0, $i)->getValue());
                        $customer_item_code = trim($objWorksheet->getCellByColumnAndRow(1, $i)->getValue());
                        $description        = trim($objWorksheet->getCellByColumnAndRow(2, $i)->getValue());
                        $quantity           = trim($objWorksheet->getCellByColumnAndRow(3, $i)->getValue());
                        $uom                = trim($objWorksheet->getCellByColumnAndRow(4, $i)->getValue());
                        $price              = trim($objWorksheet->getCellByColumnAndRow(5, $i)->getValue());
                        $amount             = trim($objWorksheet->getCellByColumnAndRow(6, $i)->getValue());


                        if (empty($item_code)) break;

                        $proformaLine =
                            [
                                'item_code'          => $item_code,
                                'description'        => $description,
                                'qty'                => $quantity,
                                'uom'                => $uom,
                                'price'              => str_replace(',', '', $price),
                                'amount'             => str_replace(',', '', $amount),
                                'supplier_id'        => $prof->supplier_id,
                                'customer_code'      => $prof->customer_code,
                                'proforma_header_id' => $proformaHeaderID
                            ];

                        $this->db->insert('proforma_line', $proformaLine);
                    }

                    for ($i = 6 + 1; $i <= $highestRow; $i++) {
                        $description = trim($objWorksheet->getCellByColumnAndRow(7, $i)->getValue());
                        $amount      = trim($objWorksheet->getCellByColumnAndRow(8, $i)->getValue());

                        if (empty($description)) {
                            break;
                        } else {
                            $ad =
                                [
                                    'discount'           => $description,
                                    'total_discount'     => $amount,
                                    'proforma_header_id' => $proformaHeaderID,
                                    'supplier_id'        => $prof->supplier_id,
                                    'user_id'            => $this->session->userdata('user_id')
                                ];
                        }

                        $this->db->insert('discountvat', $ad);
                    }
                } else {
                    $msg = ['message' => 'File Format not supported.', 'info' => 'Invalid Format'];
                    JSONResponse($msg);
                    exit();
                }

                $report_status_update =
                    [
                        'proforma_header_id'   => $proformaHeaderID
                    ];

                $this->povsproforma_model->update($data['proforma_header_id'], 'proforma_header_id', 'report_status', $report_status_update);

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $error = array('action' => 'Replacing Proforma Line', 'error_msg' => $this->db->error()); //Log error message to `error_log` table
                    $this->db->insert('error_log', $error);
                    $msg = ['message' => 'Error: Failed Replaceing Proforma', 'info' => 'Error'];
                } else {
                    $msg = ['message' => 'New Proforma Uploaded.', 'info' => 'Replaced'];
                }
            } else {
                $msg = ['message' => 'No File Submitted, please check again.', 'info' => 'No File'];
            }
        } else {
            $msg = ['message' => 'No File Submitted, please check again.', 'info' => 'No File'];
        }

        JSONResponse($msg);
    }

    public function getMatchItems()
    {
        $data = $this->input->post(NULL, FILTER_SANITIZE_STRING);

        $result = $this->povsproforma_model->getItems($data['po_header_id'], $data['proforma_header_id']);
        return JSONResponse($result);
    }

    public function addDiscount()
    {
        $data       = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $supplierID = array();
        $discount   = array();
        $msg        = array();

        if (!empty($data['discountData'])) {
            $this->db->trans_start();

            $supplierID = $this->povsproforma_model->getData2($data['proforma_header_id'], 'proforma_header_id', 'proforma_header');

            foreach ($data['discountData'] as $value) {
                if ($value['discount'] === 'VAT') {

                    $amount = $value['amount'];
                } else {

                    $amount = $value['amount'];
                }

                $discount[] =
                    [
                        'discount'           => $value['discount'],
                        'total_discount'     => $amount,
                        'proforma_header_id' => $data['proforma_header_id'],
                        'supplier_id'        => $supplierID->supplier_id,
                        'user_id'            => $this->session->userdata('user_id'),
                        'approved_by'        => $this->session->userdata('authorize_id')
                    ];
            }

            foreach ($discount as $data) {
                $this->db->insert('discountvat', $data);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $error = array('action' => 'Saving Discount/VAT', 'error_msg' => $this->db->error()); //Log error message to `error_log` table
                $this->db->insert('error_log', $error);
                $msg = ['message' => 'Error: Failed saving some data.', 'info' => 'Error'];
            } else {
                $this->session->unset_userdata('authorize_id');
                $msg = ['message' => 'Discount/VAT Added.', 'info' => 'Added'];
            }
        } else {
            $msg = ['message' => 'No inputted data found.', 'info' => 'No Data'];
        }

        JSONResponse($msg);
    }

    public function getDiscount()
    {
        $headerID  = $this->uri->segment(4);
        $result = $this->povsproforma_model->getData3($headerID, 'proforma_header_id', 'discountvat');
        echo JSONResponse($result);
    }

    public function getPos()
    {
        $data   = json_decode($this->input->raw_input_stream, true);
        $po     = $data['po'];
        $poData = $this->povsproforma_model->getDataLike($po);

        JSONResponse($poData);
    }

    public function priceCheck()
    {
        $fetch_data   = json_decode($this->input->raw_input_stream, true);
        $data         = ['pricing_userid'   => $this->session->userdata('user_id'),
                         'pricing_status'   => 'PRICE CHECKED',
                         'price_checked_on' => date("Y-m-d H:i:s")];        
        $update       = $this->povsproforma_model->update($fetch_data['profId'], 'proforma_header_id', 'proforma_header', $data);
        if($update){
            $msg = ['info' => 'success', 'message' => 'Price checked!'];
        } else {
            $msg = ['info' => 'error', 'message' => 'Failed to price check!'];
        }

        JSONResponse($msg);
    }

    //========== CREATE PROFORMA ==========//
    public function searchPos()
    {
        $fetch_data   = json_decode($this->input->raw_input_stream, true);
        $po     = $fetch_data['po'];
        $supId  = $fetch_data['supplier'];
        $cusId  = $fetch_data['customer'];
        $poData = $this->povsproforma_model->searchPo($po,$supId,$cusId);

        JSONResponse($poData);
    }

    public function getPisDetails()
    {
        $data          = json_decode($this->input->raw_input_stream, true);
        $po_id         = $data['po_id'];
        $pisDetails    = $this->povsproforma_model->getData2($po_id, 'po_header_id', 'proforma_header');
        $location      = $this->povsproforma_model->getData2($pisDetails->customer_code, 'customer_code', 'customers');
        $supplier      = $this->povsproforma_model->getData2($pisDetails->supplier_id, 'supplier_id', 'suppliers');
        $result        = array();
        $result['loc'] = ['locationID' => $location->customer_code, 'customer_name' => $location->customer_name];
        $result['sup'] = ['supplierID' => $supplier->supplier_id, 'supplier_name' => $supplier->supplier_name];

        JSONResponse($result);
    }

    public function PoDetails()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $query      = $this->db->select('l.*, i.description')
                               ->from('po_line l')
                               ->join('items i','i.itemcode_loc = l.item_code','inner')
                               ->where('l.po_header_id',$fetch_data['poId'])
                               ->where('i.supplier_id', $fetch_data['supId'])
                               ->get()
                               ->result_array();

        JSONResponse($query);
    }

    public function saveProforma()
    {
        $fetch_data   = $this->input->post(NULL,TRUE);
        $invoiceData  = [];
        $discountData = [];
        $msg          = [];

        // dump($fetch_data);
        // die();

        $this->db->trans_start();

        if( isset($fetch_data['inv']) ){
            $invoiceData = $fetch_data['inv'];
        }
        if( isset($fetch_data['disc']) ){
            $discountData = $fetch_data['disc'];
        }

        if(!empty($invoiceData)){
            $proformano = $fetch_data['si'] != 'NA' ? $fetch_data['si'] : $fetch_data['so'];
            $checkDupes = $this->povsproforma_model->checkDuplicateProf($fetch_data['supId'],$proformano);
            
            if($checkDupes == 0){

            $headData = ['proforma_code'    => $fetch_data['si'] != 'NA' ? $fetch_data['si'] : $fetch_data['so'],
                         'order_date'       => '',
                         'delivery_date'    => date('m/d/Y',strtotime($fetch_data['date'])),
                         'so_no'            => $fetch_data['so'],
                         'order_no'         => '',
                         'po_header_id'     => $fetch_data['poId'],
                         'sales_invoice_no' => $fetch_data['si'],
                         'date_uploaded'    => date('Y-m-d'),
                         'status'           => 'PENDING',
                         'supplier_id'      => $fetch_data['supId'],
                         'customer_code'    => $fetch_data['cusid'],
                         'entry_type'       => 'CREATED',
                         'user_id'          => $this->session->userdata('user_id')];   
            $this->db->insert('proforma_header', $headData);
            $profId = $this->db->insert_id();

            $reportStatus = ['po_header_id'         => $fetch_data['poId'],
                             'proforma_header_id'   => $profId,
                             'pi_head_id'           => 0,
                             'crf_id'               => 0,
                             'proforma_stat'        => 0,
                             'supplier_id'          => $fetch_data['supId'],
                             'customer_code'        => $fetch_data['cusid'],
                             'user_id'              => $this->session->userdata('user_id') ];
            $this->db->insert('report_status', $reportStatus);
            
            foreach($invoiceData as $inv){
                $free = 0;
                if( isset($inv['selected'])){
                    if( $inv['selected'] == 'false' ){
                        $free = 0;
                    } else {
                        $free = 1;
                    }
                } else {
                    $free = 0;
                }
                $line = ['item_code'          => $inv['materialcode'],
                         'itemcode_loc'       => $inv['customercode'],
                         'description'        => $inv['description'],
                         'qty'                => $inv['qty'],
                         'uom'                => $inv['uom'],
                         'price'              => $inv['unitcost'],
                         'amount'             => $inv['qty'] * $inv['unitcost'] ,
                         'free'               => $free,
                         'supplier_id'        => $fetch_data['supId'],
                         'customer_code'      => $fetch_data['cusid'],
                         'proforma_header_id' => $profId];
                
                $this->db->insert('proforma_line', $line);
            }
            if(!empty($discountData)){
                foreach($discountData as $disc){
                    $discLine = ['discount'           => $disc['discname'],
                                 'total_discount'     => $disc['amount'],
                                 'proforma_header_id' => $profId,
                                 'supplier_id'        => $fetch_data['supId'],
                                 'user_id'            => $this->session->userdata('user_id')];
                    $this->db->insert('discountvat', $discLine);
                }
            }            
            
            $msg = ['info' => 'Success', 'message' => 'Pro-forma created successfully!'];
            } else {
                $msg = ['info' => 'Error', 'message' => 'Sales Invoice/Order no. '.$proformano.' already exist!'];
            }
        } else {
            $msg = ['info' => 'Error', 'message' => 'Failed to create Pro-forma!'];
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $error = array('action' => 'Saving CWO SOP', 'error_msg' => $this->db->error()); //Log error message to `error_log` table
            $this->db->insert('error_log', $error);
            $msg = ['info' => 'Error', 'message' => 'Error saving Proforma Invoice!'];
        }
        JSONResponse($msg);
    }

    public function getPricing()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $pricing    = $this->povsproforma_model->getData4($fetch_data['supId'], 'supplier_id', 'suppliers')['pricing'];
        JSONResponse($pricing);
    }
    //========== CREATE PROFORMA ==========//


    public function getProfPriceCheckStatistics()
    {
        $statistics = $this->povsproforma_model->getProfPriceCheckedStatistics();
        JSONResponse($statistics);
    }
}
