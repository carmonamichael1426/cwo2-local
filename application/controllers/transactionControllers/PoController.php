<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PoController extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('upload');
        $this->load->library('session');
        $this->load->library('form_validation');
        date_default_timezone_set('Asia/Manila');

        if (!$this->session->userdata('cwo_logged_in')) {

            redirect('home');
        }

        $this->load->model('po_model');

        //Disable Cache
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        ('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    public function getSuppliersForPO()
    {
        $result = $this->po_model->loadSupplier();
        JSONResponse($result);
    }

    public function getCustomersForPO()
    {
        $result = $this->po_model->loadCustomer();
        JSONResponse($result);
    }

    public function getPOs()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $result     = $this->po_model->loadPo($fetch_data['supId'], $fetch_data['cusId'], $fetch_data['from'], $fetch_data['to']);
        JSONResponse($result);
    }

    public function uploadPo()
    {
        $fetch_data   = $this->input->post(NULL, TRUE);
        $supId        = $fetch_data['selectSupplier'];
        $cusId        = $fetch_data['selectCustomer'];
        $cusPoExt     = $this->po_model->getCustomerPoExtension($fetch_data['selectCustomer']);        
        $invalidExt   = array();
        $msg          = array();
        $itemNotFound = array();
        $hasExtSetup  = FALSE ;
        $extFound     = FALSE ;
        $poInserted   = 0;

        $this->db->trans_start();

        if(!empty($cusPoExt)){
            $hasExtSetup = TRUE ;
            for ($i = 0; $i < count($_FILES['pofile']['name']); $i++) 
            {
                $getExt =  pathinfo($_FILES['pofile']['name'][$i], PATHINFO_EXTENSION); 
                $checkExt   = array_search($getExt, array_column($cusPoExt, 'po_extension'));               
                if($checkExt === FALSE){
                    $extFound     = FALSE ;
                    $invalidExt[] = $getExt;
                } else {
                    $extFound = TRUE ;
                    $poContent  = file_get_contents($_FILES['pofile']['tmp_name'][$i]);
                    $line       = explode("\n", $poContent);
                    $totalLine  = count($line);

                    for ($n = 0; $n < $totalLine; $n++) {
                        if ($line[$n] != NULL) {
                            $blits = str_replace('"', "", $line[$n]);
                            $refined = explode("|", $blits);
                            $countRefined = count($refined);
            
                            if ($countRefined == 11) {
                                $checkItem = $this->po_model->checkItem(trim($refined[1]));
                                if (!$checkItem) {
                                    $itemNotFound[$n] =  trim($refined[1]);
                                }
                            }
                        }
                    }
                }
            }

        } else {
            $hasExtSetup  = FALSE ;
        }

        if( !empty($invalidExt) && $hasExtSetup ){
            $msg = [ 'info' => 'Error-ext', 'message' => 'Invalid PO for this location!', 'ext' => array_unique($invalidExt,SORT_STRING ) ];
        }
        if( !empty($itemNotFound) && $hasExtSetup){
            $msg = [ 'info' => 'Error-item', 'message' => 'Item not found in the masterfile!', 'item' => $itemNotFound  ];
        }     
        if( !$hasExtSetup){
            $msg = [ 'info' => 'ExtNotFound', 'message' => 'This location has no setup for valid PO extension!' ];
        }

        if( empty($invalidExt) && empty($itemNotFound) && $hasExtSetup ){
            for ($p = 0; $p < count($_FILES['pofile']['name']); $p++) 
            {
                $fileName   = $_FILES['pofile']['name'][$p];
                $poTemp     = $_FILES['pofile']['tmp_name'][$p];
                $poContent  = file_get_contents($_FILES['pofile']['tmp_name'][$p]);
                $line       = explode("\n", $poContent);
                $totalLine  = count($line);                
               
                for ($i = 0; $i < $totalLine; $i++) 
                {
                    if ($line[$i] != NULL) {
                        $blits = str_replace('"', "", $line[$i]);
                        $refined = explode("|", $blits);
                        $countRefined = count($refined);
    
                        if ($countRefined == 7) {
                            $getSupCode = $this->po_model->getSupplierData($supId, 'supplier_code', 'supplier_id');                            
                            if ($getSupCode->supplier_code == trim($refined[5])) //validate if same ang sa selected supplier sa supplier  naa sa texfile
                            {    
                                $poNo        = trim($refined[0]);
                                $orderDate   = trim($refined[1]);
                                $postingDate = trim($refined[2]);
                                $reference   = trim($refined[4]);
                                $vendor      = trim($refined[5]);
    
                                $poId = $this->po_model->uploadHeader($poNo, $orderDate, $postingDate, $reference, $supId, $cusId);
                                if($poId){
                                    $poInserted ++ ;
                                    $saveDocument = ['document_name'  => $fileName,
                                                     'document_type'  => 'Purchase Order',
                                                     'document_path'  => "files/PO/".$fileName,
                                                     'uploaded_on'    => date("Y-m-d H:i:s"),
                                                     'supplier_id'    => $fetch_data['selectSupplier'],
                                                     'customer_code'  => $fetch_data['selectCustomer'],
                                                     'user_id'        => $this->session->userdata('user_id')] ;                                                    
                                    $this->db->insert('uploaded_transaction_documents', $saveDocument);
                                    move_uploaded_file($poTemp,"files/PO/".$fileName);
                                }
                            } else {
                                $msg = [ 'info' => 'Error', 'message' => 'Invalid supplier!'];
                                break;
                            }
                        }
    
                        if ($countRefined == 11) {
                            $barcode     = trim($refined[0]);
                            $itemCode    = trim($refined[1]);
                            $qty         = trim($refined[2]);
                            $unitCost    = trim($refined[3]);
                            $uom         = trim($refined[4]);    
                            $insertLine = $this->po_model->uploadLine($barcode, $itemCode, $qty, $unitCost, $uom, $poId);
                        }
                    }
                }
            }

            if( count($_FILES['pofile']['name']) == $poInserted ){
                $msg = [ 'info' => 'Success', 'message' => 'PO uploaded successfully!'];
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $error = array('action' => 'Uploading Purchase Order', 'error_msg' => $this->db->error()); //Log error message to `error_log` table
            $this->db->insert('error_log', $error);
            $msg = [ 'info' => 'Error', 'message' => 'Error uploading PO!'];
        } 

        JSONResponse($msg);       
    }

    public function getPoDetails()
    {
        $poId      = $this->uri->segment(4);
        $supId     = $this->uri->segment(5);
        $poDetails = $this->po_model->poDetails($poId, $supId);
        JSONResponse($poDetails);
    }

    public function loadItems()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $items      = $this->po_model->getItems($fetch_data['supId']);
        
        JSONResponse($items);
    }

    public function createPo()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $items      = $fetch_data['items'];
        $msg        = [];
        $countwQtyPrice = 0;

        $this->db->trans_start();

        $checkDupes = $this->po_model->checkPono($fetch_data['pono']);
        foreach($items as $it){
            if($it['qty'] > 0 && $it['cost'] > 0){
                $countwQtyPrice ++;
            }
        }

        if($checkDupes == 0){
            if($countwQtyPrice > 0 ){
                $poheader   = [ 'po_no'         => strtoupper($fetch_data['pono']),
                                'order_date'    => $fetch_data['podate'],
                                'posting_date'  => $fetch_data['podate'],
                                'po_reference'  => strtoupper($fetch_data['poref']),
                                'supplier_id'   => $fetch_data['supid'],
                                'customer_code' => 7,
                                'user_id'       => $this->session->userdata('user_id'),
                                'date_uploaded' => date("Y-m-d H:i:s"),
                                'status'        => 'PENDING'
                            ]; 

                $poId = $this->po_model->insertData('po_header', $poheader);
                if($poId){    
                    
                    foreach($items as $i){
                        if($i['qty'] > 0 && $i['cost'] > 0){
                            $poline =  [    'barcode'           => '',
                                            'item_code'         => $i['item_code'],
                                            'qty'               => $i['qty'],
                                            'direct_unit_cost'  => $i['cost'],
                                            'uom'               => $i['uom'],
                                            'po_header_id'      => $poId
                                        ];
                            $this->po_model->insertData('po_line', $poline);
                        }
                    }

                    $msg = ['info' => 'Success', 'message' => 'PO created successfully!'];
                } else {
                    $msg = ['info' => 'Error', 'message' => 'Failed to create PO!'];
                } 
            } else {
                $msg = ['info' => 'Error', 'message' => 'Item quantity or unit price is zero!'];
            }
        } else {
            $msg = ['info' => 'Error', 'message' => 'PO number already exist!'];
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $error = array('action' => 'Creating Purchase Order', 'error_msg' => $this->db->error()); //Log error message to `error_log` table
            $this->db->insert('error_log', $error);
            $msg = [ 'info' => 'Error', 'message' => 'Error creating PO!'];
        } 
        
        JSONResponse($msg);
    }
}
