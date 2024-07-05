<?php
defined('BASEPATH') or exit('No direct script access allowed');

class VendorsDealController extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('upload');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->model('VendorsDealModel');
        $this->load->library('PHPExcel');
        date_default_timezone_set('Asia/Manila');
    }

    function sanitize($string)
    {
        $string = htmlentities($string, ENT_QUOTES, 'UTF-8');
        $string = trim($string);
        return $string;
    }

    public function getDeals()
    {
        $data = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $result = $this->VendorsDealModel->getDataWhere('vendors_deal_header',  "supplier_id = '" . $data['supplierID'] . "' ");
        JSONResponse($result);
    }

    public function uploadDeals()
    {
        $data           = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $supplier       = $this->VendorsDealModel->getSupplier($data['supplierSelect']);
        $vendorsDealID  = '';
        $header         = array();
        $lines          = array();
        $m              = array();
        $checkDuplicate = array();
        $file_name      = '';

        foreach ($_FILES as $key => $file) {
            $file_name = str_replace(['"', '[', ']'], '', json_encode($file['name']));
        }

        if ($file_name != '') {
            $itemCodes = file_get_contents($_FILES['vendorsDeal']['tmp_name']);


            $line      = explode("\n", $itemCodes);
            $totalLine = count($line);
            $end       = strrpos($file_name, ".");
            $length    = strlen($file_name);
            $ext       = substr($file_name, $end + 1, $length);

            if ($ext == 'txt') {

                $this->db->trans_start();

                for ($i = 0; $i < $totalLine; $i++) {
                    if ($line[$i] != NULL) {

                        $blits        = str_replace(array('"', '<', '>'), "", $line[$i]);
                        $refined      = explode("|", $blits);
                        $countRefined = count($refined);

                        if ($countRefined == 9) {
                            if ($supplier->supplier_code == trim($refined[1])) {
                                $checkDuplicate = $this->VendorsDealModel->getDuplicate(trim($refined[0]));

                                if (empty($checkDuplicate)) {

                                    $header =
                                        [
                                            'vendor_deal_code'    => trim($refined[0]),
                                            'supplier_id'         => $data['supplierSelect'],
                                            'classification_code' => trim($refined[3]),
                                            'level'               => trim($refined[4]),
                                            'description'         => trim($refined[5]),
                                            'period_from'         => trim($refined[6]),
                                            'period_to'           => trim($refined[7]),
                                            'promo_mechanics'     => trim($refined[8]),
                                        ];


                                    $this->db->insert('vendors_deal_header', $header);
                                    $vendorsDealID = $this->db->insert_id();
                                } else {
                                    $m = ['message' => 'Vendor Deal Code Already Exist.', 'info' => 'Duplicate'];
                                    JSONResponse($m);
                                    exit();
                                }
                            } else {
                                $m = ['message' => 'Supplier Does not Match: ' . trim($refined[2]), 'info' => 'Incorrect-Supplier'];
                                JSONResponse($m);
                                exit();
                            }
                        }

                        if ($countRefined == 12) {
                            $lines =
                                [
                                    'vendor_deal_head_id' => $vendorsDealID,
                                    'line_no'             => trim($refined[1]),
                                    'type'                => trim($refined[2]),
                                    'number'              => trim($refined[3]),
                                    'description'         => trim($refined[4]),
                                    'disc_1'              => trim($refined[5]),
                                    'disc_2'              => trim($refined[6]),
                                    'disc_3'              => trim($refined[7]),
                                    'disc_4'              => trim($refined[8]),
                                    'disc_5'              => trim($refined[9]),
                                    'diminishing'         => trim($refined[10]),
                                    'discount_amount'     => trim($refined[11])
                                ];

                            $this->db->insert('vendors_deal_line', $lines);
                        }
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $error = array('action' => 'Uploading Vendors Deals', 'error_msg' => $this->db->_error_message()); //Log error message to `error_log` table
                    $this->db->insert('error_log', $error);
                    $m = ['message' => 'Error: Failed Uploading Vendors Deal.', 'info' => 'Error-Uploading'];
                } else {

                    $m = ['message' => 'Uploaded Successfully.', 'info' => 'Uploaded'];
                }
            } else {
                $m = ['message' => 'File Format Not Supported.', 'info' => 'Format'];
            }
        } else {
            $m = ['message' => 'Uploading Error: No File Uploaded.', 'info' => 'Failed'];
        }

        JSONResponse($m);
    }

    public function updateDeals()
    {
        $data           = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $supplier       = $this->VendorsDealModel->getSupplier($data['supplierSelect']);
        $vendorsDealID  = '';
        $header         = array();
        $lines          = array();
        $m              = array();
        $checkDuplicate = array();
        $file_name      = '';

        foreach ($_FILES as $key => $file) {
            $file_name = str_replace(['"', '[', ']'], '', json_encode($file['name']));
        }

        if ($file_name != '') {
            $itemCodes = file_get_contents($_FILES['vendorsDeal']['tmp_name']);
            $line      = explode("\n", $itemCodes);
            $totalLine = count($line);
            $end       = strrpos($file_name, ".");
            $length    = strlen($file_name);
            $ext       = substr($file_name, $end + 1, $length);

            if ($ext == 'txt') {

                $this->db->trans_start();

                for ($i = 0; $i < $totalLine; $i++) {
                    if ($line[$i] != NULL) {

                        $blits        = str_replace(array('"', '<', '>'), "", $line[$i]);
                        $refined      = explode("|", $blits);
                        $countRefined = count($refined);

                        if ($countRefined == 9) {
                            if ($supplier->supplier_code == trim($refined[1])) {
                                $checkDuplicate = $this->VendorsDealModel->getDuplicate(trim($refined[0]));

                                if (empty($checkDuplicate)) {

                                    $header =
                                        [
                                            'vendor_deal_code'    => trim($refined[0]),
                                            'supplier_id'         => $data['supplierSelect'],
                                            'classification_code' => trim($refined[3]),
                                            'level'               => trim($refined[4]),
                                            'description'         => trim($refined[5]),
                                            'period_from'         => trim($refined[6]),
                                            'period_to'           => trim($refined[7]),
                                            'promo_mechanics'     => trim($refined[8]),
                                        ];


                                    $this->db->insert('vendors_deal_header', $header);
                                    $vendorsDealID = $this->db->insert_id();
                                } else {
                                    $m = ['message' => 'Vendor Deal Code Already Exist.', 'info' => 'Duplicate'];
                                    JSONResponse($m);
                                    exit();
                                }
                            } else {
                                $m = ['message' => 'Supplier Does not Match: ' . trim($refined[2]), 'info' => 'Incorrect-Supplier'];
                                JSONResponse($m);
                                exit();
                            }
                        }

                        if ($countRefined == 12) {
                            $lines =
                                [
                                    'vendor_deal_head_id' => $vendorsDealID,
                                    'line_no'             => trim($refined[1]),
                                    'type'                => trim($refined[2]),
                                    'number'              => trim($refined[3]),
                                    'description'         => trim($refined[4]),
                                    'disc_1'              => trim($refined[5]),
                                    'disc_2'              => trim($refined[6]),
                                    'disc_3'              => trim($refined[7]),
                                    'disc_4'              => trim($refined[8]),
                                    'disc_5'              => trim($refined[9]),
                                    'diminishing'         => trim($refined[10]),
                                    'discount_amount'         => trim($refined[11])
                                ];

                            $this->db->insert('vendors_deal_line', $lines);
                        }
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $error = array('action' => 'Uploading Vendors Deals', 'error_msg' => $this->db->_error_message()); //Log error message to `error_log` table
                    $this->db->insert('error_log', $error);
                    $m = ['message' => 'Error: Failed Uploading Vendors Deal.', 'info' => 'Error-Uploading'];
                } else {

                    $m = ['message' => 'Uploaded Successfully.', 'info' => 'Uploaded'];
                }
            } else {
                $m = ['message' => 'File Format Not Supported.', 'info' => 'Format'];
            }
        } else {
            $m = ['message' => 'Uploading Error: No File Uploaded.', 'info' => 'Failed'];
        }

        JSONResponse($m);
    }

    public function loadItemDeptCode()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $result     = $this->VendorsDealModel->getItemDeptCode($fetch_data['supId']);
        JSONResponse($result);
    }

    public function submitManualSetup()
    {
        $fetch_data = $this->input->post(NULL, TRUE);
        $lineCount  = 0;
        $insertedLine = 0;
        if (!empty($fetch_data['discount'])) {
            $lineCount = count($fetch_data['discount']);

            $m =      array(
                'vendor_deal_code'    => 'MANUAL SETUP',
                'supplier_id'         => $fetch_data['supId'],
                'classification_code' => '',
                'level'               => '',
                'description'         => '',
                'period_from'         => $fetch_data['from'],
                'period_to'           => $fetch_data['to'],
                'promo_mechanics'     => ''
            );
            $this->db->insert('vendors_deal_header', $m);
            $dealId = $this->db->insert_id();

            if ($dealId) {
                foreach ($fetch_data['discount'] as $disc) {
                    $l = array(
                        'vendor_deal_head_id'    => $dealId,
                        'line_no'                => 0,
                        'type'                   => 'Item Department',
                        'number'                 => $disc['itemcode'],
                        'description'            => $disc['desc'],
                        'disc_1'                 => $disc['disc1'],
                        'disc_2'                 => isset($disc['disc2']) ? $disc['disc2'] : 0,
                        'disc_3'                 => isset($disc['disc3']) ? $disc['disc3'] : 0,
                        'disc_4'                 => isset($disc['disc4']) ? $disc['disc4'] : 0,
                        'disc_5'                 => isset($disc['disc5']) ? $disc['disc5'] : 0,
                        'diminishing'            => ""
                    );
                    $i = $this->db->insert('vendors_deal_line', $l);
                    if ($i) {
                        $insertedLine++;
                    }
                }
            }
        }

        if ($dealId && $lineCount == $insertedLine) {
            $msg = ['info' => 'Success', 'message' => 'Discount/s successfully added!'];
        } else {
            $msg = ['info' => 'Error', 'message' => 'Failed to save discount/s!'];
        }
        JSONResponse($msg);
    }

    public function getDealsDetail()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $details    = $this->VendorsDealModel->getDataWhere('vendors_deal_line',  "vendor_deal_head_id = '" . $fetch_data['dealId'] . "' ");        
        JSONResponse($details);
    }

    public function addVDLine()
    {
        $fetch_data = $this->input->post(NULL,TRUE);
        $checkDupes = $this->VendorsDealModel->vdlinecodedupes($fetch_data['dealId'], $fetch_data['addcode']);

        if( $checkDupes == 0){
            $line = [ 'vendor_deal_head_id' => $fetch_data['dealId'],
                      'line_no'             => '',
                      'type'                => $fetch_data['addtype'],
                      'number'              => $fetch_data['addcode'],
                      'description'         => strtoupper($fetch_data['adddesc']),
                      'disc_1'              => $fetch_data['adddisc1'],
                      'disc_2'              => $fetch_data['adddisc2'],
                      'disc_3'              => $fetch_data['adddisc3'],
                      'disc_4'              => $fetch_data['adddisc4'],
                      'disc_5'              => $fetch_data['adddisc5'],
                      'disc_6'              => $fetch_data['adddisc6'],
                      'diminishing'         => '',
                      'discount_amount'     => 0                      
                    ];
            $this->db->insert('vendors_deal_line', $line);
            $l = $this->db->insert_id();
            if($l)
            {
                $msg = ['info' => 'Success', 'message' => 'VD Line added successfully!'];
            }
        } else {
            $msg = ['info' => 'Error', 'message' => 'Duplicate code detected!'];
        }
        
        JSONResponse($msg);
    }

    public function setDiscount()
    {
        $fetch_data = $this->input->post(NULL,TRUE);
        $sopgross   = [];
        $pvcrfgross = [];
        $pvcrfnet   = [];
        $pvcrfdisc  = [];
        $pvpigross  = [];
        $pvpinet    = [];
        $pvpidisc   = [];
        $formula_sopgross   = '';
        $formula_pvcrfgross = '';
        $formula_pvcrfnet   = '';
        $formula_pvcrfdisc  = '';
        $formula_pvpigross  = '';
        $formula_pvpinet    = '';
        $formula_pvpidisc   = '';

        for ($i=0; $i < 6 ; $i++) { 
            $s = $i + 1;
            if( isset($fetch_data['sopdisc_'.$s]) ){
                $sopgross[] = '/ ' .'disc_'.$s ; 
            } 
            $formula_sopgross = implode(' ', $sopgross);
            // PROFORMA VS CRF
            if( isset($fetch_data['pvcrfdisc_'.$s]) ){ 
                $pvcrfgross[] = '/ ' .'disc_'.$s ; 
            } 
            $formula_pvcrfgross = implode(' ', $pvcrfgross);

            if( isset($fetch_data['pvcrfnetdisc_'.$s]) ){
                $pvcrfnet[] = '/ ' .'disc_'.$s ;
            } 
            $formula_pvcrfnet = implode(' ', $pvcrfnet);

            if( isset($fetch_data['pvcrfdiscounteddisc_'.$s]) ){
                $pvcrfdisc[] = '/ ' .'disc_'.$s ;
            } 
            $formula_pvcrfdisc = implode(' ', $pvcrfdisc);
            // PROFORMA VS CRF
            // PROFORMA VS PI
            if( isset($fetch_data['pvpigrossdisc_'.$s]) ){ 
                $pvpigross[] = '/ ' .'disc_'.$s ; 
            } 
            $formula_pvpigross = implode(' ', $pvpigross);

            if( isset($fetch_data['pvpinetdisc_'.$s]) ){
                $pvpinet[] = '/ ' .'disc_'.$s ;
            } 
            $formula_pvpinet = implode(' ', $pvpinet);

            if( isset($fetch_data['pvpidiscounteddisc_'.$s]) ){
                $pvpidisc[] = '/ ' .'disc_'.$s ;
            } 
            $formula_pvpidisc = implode(' ', $pvpidisc);
            // PROFORMA VS PI
        }

        $data = [ 'sop_gross'           => $formula_sopgross,
                  'profvscrf_gross'     => $formula_pvcrfgross,
                  'profvscrf_net'       => $formula_pvcrfnet,
                  'profvscrf_disc'      => $formula_pvcrfdisc,
                  'profvspi_gross'      => $formula_pvpigross,
                  'profvspi_net'        => $formula_pvpinet,
                  'profvspi_disc'       => $formula_pvpidisc ];
        
        $update = $this->VendorsDealModel->update('vendors_deal_header', $data, "vendor_deal_head_id = '" . $fetch_data['dealId'] . "' ");
        if($update > 0){
            $msg = ['info' => 'Success', 'message' => 'Formula is updated sucessfully!'];
        } else {
            $msg = ['info' => 'Error', 'message' => 'Failed to update formula for this deal!'];
        }
        
        JSONResponse($msg);
    }

    public function getDiscountsUsed()
    {
        $fetch_data         = json_decode($this->input->raw_input_stream, TRUE);
        $sopgross   = [];
        $pvcrfgross = [];
        $pvcrfnet   = [];
        $pvcrfdisc  = [];
        $pvpigross  = [];
        $pvpinet    = [];
        $pvpidisc   = [];
        $discounts  = [];
        $get                = $this->VendorsDealModel->getDataWhereRow('vendors_deal_header',  "vendor_deal_head_id = '" . $fetch_data['dealId'] . "' "); 
        $formula_sopgross   = $get->sop_gross;
        $formula_pvcrfgross = $get->profvscrf_gross;
        $formula_pvcrfnet   = $get->profvscrf_net;
        $formula_pvcrfdisc  = $get->profvscrf_disc;
        $formula_pvpigross  = $get->profvspi_gross;
        $formula_pvpinet    = $get->profvspi_net;
        $formula_pvpidisc   = $get->profvspi_disc;

        $trimmedformula_sopgross   = trim(ltrim($formula_sopgross, '/')); 
        $trimmedformula_pvcrfgross = trim(ltrim($formula_pvcrfgross, '/'));
        $trimmedformula_pvcrfnet   = trim(ltrim($formula_pvcrfnet, '/'));
        $trimmedformula_pvcrfdisc  = trim(ltrim($formula_pvcrfdisc, '/'));
        $trimmedformula_pvpigross  = trim(ltrim($formula_pvpigross, '/'));
        $trimmedformula_pvpinet    = trim(ltrim($formula_pvpinet, '/'));
        $trimmedformula_pvpidisc   = trim(ltrim($formula_pvpidisc, '/'));

        $explode_sopgross   = explode("/", $trimmedformula_sopgross);
        $explode_pvcrfgross = explode("/", $trimmedformula_pvcrfgross);
        $explode_pvcrfnet   = explode("/", $trimmedformula_pvcrfnet);
        $explode_pvcrfdisc  = explode("/", $trimmedformula_pvcrfdisc);
        $explode_pvpigross  = explode("/", $trimmedformula_pvpigross);
        $explode_pvpinet    = explode("/", $trimmedformula_pvpinet);
        $explode_pvpidisc   = explode("/", $trimmedformula_pvpidisc);

        if ( count(array_filter($explode_sopgross)) > 0){
            $discounts[] = ['sopdisc' => $explode_sopgross];
        } else {
            $discounts[] = ['sopdisc' => 0];
        }

        if ( count(array_filter($explode_pvcrfgross)) > 0){
            $discounts[] = ['pvcrfdisc' => $explode_pvcrfgross];
        } else {
            $discounts[] = ['pvcrfdisc' => 0];
        }

        if ( count(array_filter($explode_pvcrfnet)) > 0){
            $discounts[] = ['pvcrfnetdisc' => $explode_pvcrfnet];
        } else {
            $discounts[] = ['pvcrfnetdisc' => 0];
        }

        if ( count(array_filter($explode_pvcrfdisc)) > 0){
            $discounts[] = ['pvcrfdiscounteddisc' => $explode_pvcrfdisc];
        } else {
            $discounts[] = ['pvcrfdiscounteddisc' => 0];
        }

        if ( count(array_filter($explode_pvpigross)) > 0){
            $discounts[] = ['pvpigrossdisc' => $explode_pvpigross];
        } else {
            $discounts[] = ['pvpigrossdisc' => 0];
        }

        if ( count(array_filter($explode_pvpinet)) > 0){
            $discounts[]=  ['pvpinetdisc' => $explode_pvpinet];
        } else {
            $discounts[] = ['pvpinetdisc' => 0];
        }

        if ( count(array_filter($explode_pvpidisc)) > 0){
            $discounts[]=  ['pvpidiscounteddisc' => $explode_pvpidisc];
        } else {
            $discounts[] = ['pvpidiscounteddisc' => 0];
        }

        $flatDisc = call_user_func_array('array_merge', $discounts);
        JSONResponse($flatDisc);
    }
}
