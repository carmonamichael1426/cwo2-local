<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Po extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('upload');
        $this->load->library('session');
        $this->load->library('form_validation');
        date_default_timezone_set('Asia/Manila');
       
        
        $this->load->model('po_model');
    }

  
    public function loadSupplier()
    {
        $result = $this->po_model->loadSupplier();
        return JSONResponse($result);
    }

    public function loadCustomer()
    {
        $result = $this->po_model->loadCustomer();
        return JSONResponse($result);
    }

    public function loadPO()
    {
        $result = $this->po_model->loadPo();
        return JSONResponse($result);
    }

    public function uploadPo()
    {
        
        $fetch_data = $this->input->post(NULL, TRUE);
        $supplier = $fetch_data['sup'];
        $customer = $fetch_data['cus'];
        $poFile   = $_FILES['pofile']['name'];
        $poExt = pathinfo($poFile,PATHINFO_EXTENSION);
       
          if($poExt == "txt" || $poExt == "TXT" || $poExt == "CENT-DC" || $poExt == "CENT-DC-PST" || $poExt == "ICM-SA-PST") //validate file extension
        {            
            $poContent = file_get_contents($_FILES['pofile']['tmp_name']);

            $line = explode("\n",$poContent);
            $totalLine = count($line);
            $totalItem = $totalLine - 3;
            $item = 0;

            // var_dump($line);
            for ($i=0; $i < $totalLine; $i++) 
            { 
                if ($line[$i] != NULL) 
                {
                    $blits = str_replace('"', "", $line[$i]);
                    $refined = explode("|", $blits);
                    $countRefined = count($refined);
                   

                    if($countRefined == 7)
                    {
                        if($supplier == trim($refined[5])) //validate if same ang sa selected supplier sa supplier  naa sa texfile
                        {
                            
                        $poNo        = trim($refined[0]);
                        $orderDate   = trim($refined[1]);
                        $postingDate = trim($refined[2]);
                        $reference   = trim($refined[4]);
                        $vendor      = trim($refined[5]);

                        $insertHeader = $this->po_model->uploadHeader($poNo,$orderDate,$postingDate,$reference,$vendor,$customer); 
                        }else
                        {
                            die("invalid supplier");
                        }
                      
                    } 
                    if($countRefined == 11)
                    {                        
                        
                        $barcode     = trim($refined[0]);
                        $itemCode    = trim($refined[1]);
                        $qty         = trim($refined[2]);
                        $unitCost    = trim($refined[3]);
                        $uom         = trim($refined[4]);

                        $this->po_model->uploadLine($barcode,$itemCode,$qty,$unitCost,$uom,$supplier,$customer,$poNo,$reference);                       

                    }                    
                }
            }

            die("success");

        }else
        {
            die("invalid");
        }
    }

    

}