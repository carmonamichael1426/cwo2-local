<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProformaVSPiController extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('upload');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->library('fpdf');
        $this->load->library('PHPExcel');
        date_default_timezone_set('Asia/Manila');

        if (!$this->session->userdata('cwo_logged_in')) {

            redirect('home');
        }

        $this->load->model('proformavspi_model');

        //Disable Cache
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        ('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    //gross amount 
    private function generateProformaVsPi($transactionId, $fetch_data, $getProfHead, $discountVat, $discVatSummary, $profPiMerge, $fullyServed, $partiallyServed,$excess, $sopDeduction, $sopCharges,
                                          $notFoundItemsFromProforma, $notFoundItemsFromPi,$cmHead,$cmLineMerge,$freeItems) 
    {
        $supplier             = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['supplier_name'];
        $getPricing           = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['pricing'];
        $getAmounting         = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['amounting'];
        $getDiscType          = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['disc_type'];
        $vat                  = $this->proformavspi_model->getVATData()['value'];
        $mentionsFromSop      = $this->proformavspi_model->getVarianceMentions($fetch_data['crfId']);
        
        // dump($mentionsFromSop);
        // die();

        $fullyServedItemQty             = 0;
        $fullyServedTotalAmt            = 0;
        $fullyServedGrossAmt            = 0;
        $fullyServedDiscountedAmt       = 0;  
        $fullyReceivedItemQty           = 0;       
        $fullyReceivedNetAmount         = 0;
        $fullyGrossAmount               = 0;
        $fullyReceivedDiscountedAmt     = 0;  
        $fullyVarianceGrossAmnt         = 0;
        $fullyVarianceDiscountedAmt     = 0;
        $fullyServedReceivedVarianceQty = 0; 
        $fullyServedReceivedItemCount   = 0;
        $freeItemQty                    = 0;
        $freeItemCount                  = 0;
        
        $partiallyServedItemQty                  = 0;
        $partiallyServedTotalAmt                 = 0;
        $partiallyServedGrossAmt                 = 0;
        $partiallyServedDiscountedAmt            = 0;
        $partiallyReceivedItemQty                = 0;
        $partiallyReceivedGrossAmt               = 0;
        $partiallyReceivedDiscountedAmt          = 0;
        $partiallyReceivedNetAmt                 = 0;
        $unServedItemQty                         = 0;
        $partiallyServedReceivedItemCount        = 0;
        $partiallyServedReceivedVarianceGrossAmt = 0;
        $partiallyServedReceivedVarianceDiscountedAmt = 0;

        $overServedItemQty                  = 0;
        $overServedTotalAmt                 = 0;
        $overServedGrossAmt                 = 0;
        $overServedDiscountedAmt            = 0;
        $overReceivedItemQty                = 0;
        $overReceivedGrossAmt               = 0;
        $overReceivedDiscountedAmt          = 0;
        $overReceivedNetAmount              = 0;
        $overReceivedItemCount              = 0 ;
        $overServedReceivedVarianceGrossAmt = 0;
        $overServedReceivedVarianceDiscountedAmt = 0;
        $excessItemQty                      = 0;

        $fullyUnservedItemQty          = 0;
        $fullyUnservedTotalAmt         = 0;
        $fullyUnservedGrossAmt         = 0;
        $fullyUnservedDiscountedAmt    = 0;
        $fullyUnservedItemCount        = 0;
        $fullyUnreceivedGrossAmt       = 0;
        $fullyUnreceivedNetAmt         = 0;  
        $fullyUnreceivedDiscountedAmt  = 0;
        $fullyUnservedVarianceGrossAmt = 0;
        $fullyUnservedVarianceDiscountedAmt = 0;
        $fullyUnservedQty              = 0;
        
            
        $fullyOverservedProfGrossAmt      = 0;
        $fullyOverservedProfNetAmt        = 0;
        $fullyOverservedProfDiscountedAmt = 0;
        $fullyOverreceivedItemQty        = 0;
        $fullyOverservedGrossAmt         = 0;
        $fullyOverservedNetAmt           = 0;
        $fullyOverservedDiscountedAmt    = 0;
        $fullyOverservedItemQty          = 0;
        $fullyOverservedVarianceGrossAmt = 0;
        $fullyOverservedVarianceDiscountedAmt = 0;
        $fullyOverservedItemCount        = 0;

        $cmItemCount           = 0;
        $cmItemQty             = 0;
        $cmGrossAmount         = 0;
        $cmDiscountedAmt       = 0;
        $cmNetAmount           = 0;
        $cmVarianceQty         = 0; 
        $cmVarianceDiscAmount  = 0;

        $totalProformaGrossAmt = 0;
        $totalProformaQty      = 0; 
        $totalProformaItemCount= 0;
        $totalProformaGrossAmt = 0;
        $totalProformaDiscountedAmt = 0;
        $totalPiGrossAmount    = 0;
        $totalPiNetAmount      = 0;
        $totalPiQty            = 0; 
        
        $totalVarianceGrossAmt      = 0;
        $totalVarianceDiscountedAmt = 0;
        $proformaAddLessTotal       = 0;  
        $sopTotalDeductionW12       = 0;
        $sopTotalDeductionWo12      = 0;
        $sopTotalWHT                = 0;
        $sopVarianceMentionTotal    = 0;
        $total1PercentDisc          = 0;
        $total2PercentDisc          = 0;
        $totalVat                   = 0;

        $sopTotalCharges            = 0;

        $netPrice   = 0.00;
        $netAmount  = 0.00;
        $discountedPrice = 0.00;
        $discountedAmount = 0.00 ;
        $grossPrice = 0.00;
        $grossAmount= 0.00;

        $netPricePi         = 0.00;
        $discountedPricePi  = 0.00;

        $pdf = new FPDF('L', 'mm', array(594 , 841  )); /*  A1	594 × 841 mm	23.4 × 33.1 in   */
        // $pdf = new FPDF('L', 'mm', array(216  , 356   )); //Legal
        $pdf->AddPage();
        $pdf->setDisplayMode('fullpage');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setX(30);
        $pdf->Cell(0, 0, $supplier, 0, 0, 'L');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 10);
        $pdf->setX(30);
        $pdf->Cell(40, 0, 'PRO-FORMA SALES INVOICE vs PURCHASE INVOICE - VARIANCE REPORT', 0, 0, 'L');
        $pdf->Ln(10);


        // new template
        $pdf->setX(30);
        $pdf->Cell(40, 0, 'PRO-FORMA SALES INVOICE', 0, 0, 'L');
        $pdf->setX(423);
        $pdf->Cell(0, 0, 'PURCHASE INVOICE', 0, 0, 'L');
        $pdf->Ln(5);

        if($fullyServed !== false ){ //fully served/received
            $this->createTableColumnHeader('Fully-Served Item(s) :','Fully-Received Item(s) :','Variance',$pdf,'PI No');        

            foreach ($profPiMerge as $line) 
            {
                if($line['fullyServed']  == 1){                  

                    if($getAmounting == "NETofVAT&Disc"){
                        $netAmount = $line['profAmt'];
                    } else if($getAmounting == "GROSSofVAT&Disc"){
                        if($getDiscType == "PERCENTAGE"){
                            $netAmount = netPrice($fetch_data['supId'],$getPricing,$line['profAmt'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat, $fetch_data['cusId']);
                        } else if($getDiscType == "AMOUNT"){
                            $price     = netPrice($fetch_data['supId'],$getPricing,$line['profPrice'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat, $fetch_data['cusId']);
                            $netAmount = $price * $line['profQty'];
                        }
                       
                    } else if($getAmounting == "NETofVATwDisc"){
                        $netAmount = netPrice($fetch_data['supId'],$getPricing,$line['profAmt'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat, $fetch_data['cusId']);
                    } else if($getAmounting == "NETofDiscwVAT"){
                        $price     = netPrice($fetch_data['supId'],$getPricing,$line['profPrice'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat, $fetch_data['cusId']);
                        $netAmount = $price * $line['profQty'];
                    } else if($getAmounting == "GROSSofDiscwoVAT"){ //like columbia
                        $netAmount = netPrice($fetch_data['supId'],$getPricing,$line['profAmt'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat, $fetch_data['cusId']);
                    }
                    $netPrice          = netPrice($fetch_data['supId'],$getPricing,$line['profPrice'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat, $fetch_data['cusId']);   
                    $netPricePi        = netPricePi($fetch_data['supId'],$line['piPrice'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat);   
                    $discountedPricePi = discountedPricePi($fetch_data['supId'],$line['piPrice'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6']);          
                    if($getPricing == "NETofVATwDisc"){
                        $grossPrice      = backToGross($fetch_data['supId'],$line['profPrice'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat);
                    } else if($getPricing == "GROSSofVAT&Disc"){
                        $grossPrice      = backToGross($fetch_data['supId'],$line['profPrice'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$line['profPrice'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat);
                    } else if($getPricing == "GROSSofDiscwoVAT"){
                        $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat);
                    } else { //NETofVAT&Disc
                        $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat);
                    }

                    $pdf->setX(30);
                    $pdf->cell(80, 6, $line['profCode'], 'LBR', 0, 'C');
                    $pdf->cell(20, 6, $line['profItem'], 'BR', 0, 'C', 0);
                    $pdf->cell(82, 6, $line['profDesc'], 'BR', 0, 'L', 0);
                    $pdf->cell(15, 6, $line['profUom'], 'BR', 0, 'C', 0);
                    $pdf->cell(12, 6, $line['profQty'], 'BR', 0, 'C', 0);
                    $pdf->cell(27, 6, number_format($netPrice, 2), 'BR', 0, 'R', 0); //net price
                    $pdf->cell(30, 6, number_format($discountedPrice, 2), 'BR', 0, 'R', 0); //discounted price
                    $pdf->cell(30, 6, number_format($grossPrice, 2), 'BR', 0, 'R', 0);  //gross price
                    $pdf->cell(32, 6, number_format($netAmount, 2), 'BR', 0, 'R', 0);  //net amount
                    $pdf->cell(32, 6, number_format($discountedPrice * $line['profQty'] ,2), 'BR', 0, 'R', 0);  //discounted amount
                    $pdf->cell(32, 6, number_format($grossPrice * $line['profQty'], 2), 'BR', 0, 'R', 0);  //gross amount
                    
                    $fullyServedTotalAmt += $netAmount;
                    $fullyServedItemQty  += $line['profQty'];
                    $fullyServedGrossAmt += $grossPrice * $line['profQty']  ;
                    $fullyServedDiscountedAmt += $discountedPrice * $line['profQty'] ;

                    $pdf->cell(2, 6, " ", 0, 0, 'L');

                    $pdf->cell(35, 6, $line['piNo'], 'LBR', 0, 'C');
                    $pdf->cell(20, 6, $line['piDate'], 'BR', 0, 'C', 0);
                    $pdf->cell(20, 6, $line['piItem'], 'BR', 0, 'C', 0);
                    $pdf->cell(82, 6, $line['piDesc'], 'BR', 0, 'L', 0);
                    $pdf->cell(15, 6, $line['piUom'], 'BR', 0, 'C', 0);
                    $pdf->cell(12, 6, $line['piQty'], 'BR', 0, 'C', 0);
                    $pdf->cell(18, 6, number_format($line['piPrice'], 2), 'BR', 0, 'R', 0); //gross price
                    $pdf->cell(30, 6, number_format($discountedPricePi,2), 'BR', 0, 'R', 0); //discounted price
                    $pdf->cell(30, 6, number_format($netPricePi,2), 'BR', 0, 'R', 0); //net price
                    $pdf->cell(32, 6, number_format($line['piPrice'] * $line['piQty'], 2), 'BR', 0, 'R', 0); //gross amount
                    $pdf->cell(32, 6, number_format($discountedPricePi * $line['piQty'],2), 'BR', 0, 'R', 0); //discounted amount
                    $pdf->cell(32, 6, number_format($netPricePi * $line['piQty'] ,2), 'BR', 0, 'R', 0); //net amount
                    $pdf->cell(18, 6,  $line['profQty'] -  $line['piQty'] , 'BR', 0, 'C', 0); //variance qty
                    $pdf->cell(32, 6, number_format( ($discountedPrice * $line['profQty']) - ($discountedPricePi * $line['piQty']),2), 'BR', 0, 'R', 0); //variance discounted amount
   
                    $fullyGrossAmount               += round($line['piPrice'] * $line['piQty'] , 2);   
                    $fullyReceivedDiscountedAmt     += $discountedPricePi * $line['piQty'];
                    $fullyReceivedNetAmount         += $netPricePi * $line['piQty'];
                    $fullyVarianceGrossAmnt         += ($grossPrice * $line['profQty'] ) - ($line['piPrice'] * $line['piQty']) ;        
                    $fullyVarianceDiscountedAmt     += ($discountedPrice * $line['profQty']) - ($discountedPricePi * $line['piQty']);     
                    $fullyServedReceivedVarianceQty += $line['profQty'] -  $line['piQty'] ;
                    $fullyReceivedItemQty           += $line['piQty']; 
                    $fullyServedReceivedItemCount ++;
                    $pdf->Ln();
                }
            }

            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->setX(30);
            $pdf->cell(182, 5, "FULLY-SERVED TOTAL :", 'LB', 0, 'L');
            $pdf->cell(27, 5, $fullyServedItemQty, 'B', 0, 'R'); 
            $pdf->cell(119, 5, "P " . number_format($fullyServedTotalAmt, 2), 'B', 0, 'R');
            $pdf->cell(32, 5, "P " . number_format($fullyServedDiscountedAmt,2), 'B', 0, 'R');
            $pdf->cell(32, 5, "P " . number_format($fullyServedGrossAmt, 2), 'BR', 0, 'R');

            $pdf->cell(2, 5, "", 0, 0, 'L');

            $pdf->cell(157, 5, "FULLY-RECEIVED TOTAL :", 'LB', 0, 'L');
            $pdf->cell(27, 5, $fullyReceivedItemQty, 'B', 0, 'R'); 
            $pdf->cell(110, 5, "P ". number_format($fullyGrossAmount, 2), 'B', 0, 'R'); //gross amount
            $pdf->cell(32, 5, "P ". number_format($fullyReceivedDiscountedAmt,2) , 'B', 0, 'R'); //discounted amount
            $pdf->cell(32, 5, "P ". number_format($fullyReceivedNetAmount,2) , 'B', 0, 'R'); //net amount
            $pdf->cell(18, 5, $fullyServedReceivedVarianceQty , 'B', 0, 'C'); //qty variance
            $pdf->cell(32, 5, "P ". number_format($fullyVarianceDiscountedAmt,2) , 'BR', 0, 'R');  //variance discounted amount
 
            $pdf->Ln();
            $pdf->setX(30);
            $pdf->cell(25, 5, "Fully-Served/Received Item Count : " . $fullyServedReceivedItemCount, '', 0, 'L');
            $pdf->Ln(10);
        }   
    
        if ( $partiallyServed !== false  ) { /*    partially served */

            $this->createTableColumnHeader('Underserved Item(s) :','Partially-Received Item(s) :','Underserved',$pdf,'PI No'); 

            foreach ($profPiMerge as $partial) { //partially served
                if ($partial['partiallyServed'] == 1) {

                    if($getAmounting == "NETofVAT&Disc"){
                        $netAmount = $partial['profAmt'];
                    } else if($getAmounting == "GROSSofVAT&Disc"){
                        if($getDiscType == "PERCENTAGE"){
                            $netAmount = netPrice($fetch_data['supId'],$getPricing,$partial['profAmt'],$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat, $fetch_data['cusId']);
                        } else if($getDiscType == "AMOUNT"){
                            $price     = netPrice($fetch_data['supId'],$getPricing,$partial['profPrice'],$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat, $fetch_data['cusId']);
                            $netAmount = $price * $partial['profQty'];
                        }
                    } else if($getAmounting == "NETofVATwDisc"){
                        $netAmount = netPrice($fetch_data['supId'],$getPricing,$partial['profAmt'],$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat, $fetch_data['cusId']);
                    } else if($getAmounting == "NETofDiscwVAT"){
                        $price     = netPrice($fetch_data['supId'],$getPricing,$partial['profPrice'],$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat, $fetch_data['cusId']);
                        $netAmount = $price * $partial['profQty'];
                    } else if($getAmounting == "GROSSofDiscwoVAT"){ //like columbia
                        $netAmount = netPrice($fetch_data['supId'],$getPricing,$line['profAmt'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat, $fetch_data['cusId']);
                    }            
                    $netPrice          = netPrice($fetch_data['supId'],$getPricing,$partial['profPrice'],$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat, $fetch_data['cusId']);
                    $netPricePi        = netPricePi($fetch_data['supId'],$partial['piPrice'],$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat);   
                    $discountedPricePi = discountedPricePi($fetch_data['supId'],$partial['piPrice'],$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6']);  
                    if($getPricing == "NETofVATwDisc"){
                        $grossPrice      = backToGross($fetch_data['supId'],$partial['profPrice'],$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat);
                    } else if($getPricing == "GROSSofVAT&Disc"){
                        $grossPrice      = backToGross($fetch_data['supId'],$partial['profPrice'],$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$partial['profPrice'],$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat);
                    }  else if($getPricing == "GROSSofDiscwoVAT"){
                        $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat);
                    } else {
                        $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'], $partial['disc5'],$partial['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'], $partial['disc5'],$partial['disc6'],$vat);
                    }

                    $pdf->setX(30);
                    $pdf->cell(80, 6, $partial['profCode'], 'LBR', 0, 'C');
                    $pdf->cell(20, 6, $partial['profItem'], 'BR', 0, 'C', 0);
                    $pdf->cell(82, 6, $partial['profDesc'], 'BR', 0, 'L', 0);
                    $pdf->cell(15, 6, $partial['profUom'], 'BR', 0, 'C', 0);
                    $pdf->cell(12, 6, $partial['profQty'], 'BR', 0, 'C', 0);
                    $pdf->cell(27, 6, number_format($netPrice, 2), 'BR', 0, 'R', 0); //net price
                    $pdf->cell(30, 6, number_format($discountedPrice, 2), 'BR', 0, 'R', 0); //discounted price
                    $pdf->cell(30, 6, number_format($grossPrice, 2), 'BR', 0, 'R', 0);  //gross price
                    $pdf->cell(32, 6, number_format($netAmount, 2), 'BR', 0, 'R', 0);  //net amount
                    $pdf->cell(32, 6, number_format($discountedPrice * $partial['profQty'],2), 'BR', 0, 'R', 0);  //discounted amount
                    $pdf->cell(32, 6, number_format($grossPrice * $partial['profQty'], 2), 'BR', 0, 'R', 0);  //gross amount
        
                    $partiallyServedItemQty  += $partial['profQty'] ;
                    $partiallyServedTotalAmt += $netAmount ;
                    $partiallyServedGrossAmt += $grossPrice * $partial['profQty']  ; 
                    $partiallyServedDiscountedAmt += $discountedPrice * $partial['profQty'] ;

                    $pdf->cell(2, 6, " ", 0, 0, 'L');

                    $pdf->cell(35, 6, $partial['piNo'], 'LBR', 0, 'C');
                    $pdf->cell(20, 6, $partial['piDate'], 'BR', 0, 'C', 0);
                    $pdf->cell(20, 6, $partial['piItem'], 'BR', 0, 'C', 0);
                    $pdf->cell(82, 6, $partial['piDesc'], 'BR', 0, 'L', 0);
                    $pdf->cell(15, 6, $partial['piUom'], 'BR', 0, 'C', 0);
                    $pdf->cell(12, 6, $partial['piQty'], 'BR', 0, 'C', 0);
                    $pdf->cell(18, 6, number_format($partial['piPrice'], 2), 'BR', 0, 'R', 0); //gross price
                    $pdf->cell(30, 6, number_format($discountedPricePi,2), 'BR', 0, 'R', 0); //discounted price
                    $pdf->cell(30, 6, number_format($netPricePi,2), 'BR', 0, 'R', 0); //net price
                    $pdf->cell(32, 6, number_format($partial['piPrice'] * $partial['piQty'], 2), 'BR', 0, 'R', 0); //gross amount
                    $pdf->cell(32, 6, number_format($discountedPricePi * $partial['piQty'],2), 'BR', 0, 'R', 0); //discounted amount
                    $pdf->cell(32, 6, number_format($netPricePi * $partial['piQty'] ,2), 'BR', 0, 'R', 0); //net amount
                    $pdf->cell(18, 6, $partial['profQty'] -  $partial['piQty'] , 'BR', 0, 'C', 0); //variance qty
                    $pdf->cell(32, 6, number_format(  ($discountedPrice * $partial['profQty']) - ($discountedPricePi * $partial['piQty']) , 2), 'BR', 0, 'R', 0); //variance discounted amount
   
                    $partiallyReceivedItemQty                += $partial['piQty'] ;
                    $partiallyReceivedGrossAmt               += $partial['piPrice']  * $partial['piQty'] ;
                    $partiallyReceivedDiscountedAmt          += $discountedPricePi * $partial['piQty'] ;
                    $partiallyReceivedNetAmt                 += $netPricePi * $partial['piQty'] ;
                    $partiallyServedReceivedVarianceGrossAmt += ($grossPrice * $partial['profQty']) - ($partial['piPrice'] * $partial['piQty']) ;
                    $partiallyServedReceivedVarianceDiscountedAmt += ($discountedPrice * $partial['profQty']) - ($discountedPricePi * $partial['piQty']);
                    $unServedItemQty                         += $partial['profQty'] - $partial['piQty'] ;
                    $partiallyServedReceivedItemCount ++ ;
                    $pdf->Ln();
                }            
            }

            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->setX(30);
            $pdf->cell(182, 5, "UNDERSERVED TOTAL :", 'LB', 0, 'L');
            $pdf->cell(27, 5, $partiallyServedItemQty, 'B', 0, 'R');       
            $pdf->cell(119, 5, "P " . number_format($partiallyServedTotalAmt, 2), 'B', 0, 'R');
            $pdf->cell(32, 5, "P " . number_format($partiallyServedDiscountedAmt,2), 'B', 0, 'R');
            $pdf->cell(32, 5, "P " . number_format($partiallyServedGrossAmt, 2), 'BR', 0, 'R');

            $pdf->cell(2, 5, "", 0, 0, 'L');

            $pdf->cell(157, 5, "PARTIALLY-RECEIVED TOTAL :", 'LB', 0, 'L');
            $pdf->cell(27, 5, $partiallyReceivedItemQty, 'B', 0, 'R'); 
            $pdf->cell(110, 5, 'P '. number_format($partiallyReceivedGrossAmt, 2), 'B', 0, 'R');   
            $pdf->cell(32, 5, "P ". number_format($partiallyReceivedDiscountedAmt,2) , 'B', 0, 'R'); //discounted amount
            $pdf->cell(32, 5, "P ". number_format($partiallyReceivedNetAmt,2) , 'B', 0, 'R'); //net amount
            $pdf->cell(18, 5, $unServedItemQty, 'B', 0, 'C');   
            $pdf->cell(32, 5, 'P '. number_format($partiallyServedReceivedVarianceDiscountedAmt,2), 'BR', 0, 'R');   

            $pdf->Ln();
            $pdf->setX(30);
            $pdf->cell(25, 5, "Underserved/Partially-Received Item Count : " . $partiallyServedReceivedItemCount, '', 0, 'L');
            $pdf->Ln(10);
        }

        if ( $excess !== false  ) { /*    excess  */

            $this->createTableColumnHeader('Over-Served Item(s) :','Over-Received Item(s) :','Overserved',$pdf,'PI No'); 

            foreach ($profPiMerge as $excessLine) //excess served
            { 
                if ($excessLine['excess'] == 1) {

                    if($getAmounting == "NETofVAT&Disc"){
                        $netAmount = $excessLine['profAmt'];
                    } else if($getAmounting == "GROSSofVAT&Disc"){                        
                        if($getDiscType == "PERCENTAGE"){
                            $netAmount = netPrice($fetch_data['supId'],$getPricing,$excessLine['profAmt'],$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat, $fetch_data['cusId']);
                        } else if($getDiscType == "AMOUNT"){
                            $price     = netPrice($fetch_data['supId'],$getPricing,$excessLine['profPrice'],$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat, $fetch_data['cusId']);
                            $netAmount = $price * $excessLine['profQty'];
                        }
                    } else if($getAmounting == "NETofVATwDisc"){
                        $netAmount = netPrice($fetch_data['supId'],$getPricing,$excessLine['profAmt'],$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat, $fetch_data['cusId']);
                    } else if($getAmounting == "NETofDiscwVAT"){
                        $price     = netPrice($fetch_data['supId'],$getPricing,$excessLine['profPrice'],$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat, $fetch_data['cusId']);
                        $netAmount = $price * $excessLine['profQty'];
                    } else if($getAmounting == "GROSSofDiscwoVAT"){ //like columbia
                        $netAmount = netPrice($fetch_data['supId'],$getPricing,$line['profAmt'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat, $fetch_data['cusId']);
                    }             
                    $netPrice          = netPrice($fetch_data['supId'],$getPricing,$excessLine['profPrice'],$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat, $fetch_data['cusId']);
                    $netPricePi        = netPricePi($fetch_data['supId'],$excessLine['piPrice'],$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat);   
                    $discountedPricePi = discountedPricePi($fetch_data['supId'],$excessLine['piPrice'],$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6']);
                    if($getPricing == "NETofVATwDisc"){
                        $grossPrice      = backToGross($fetch_data['supId'],$excessLine['profPrice'],$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat);
                    } else if($getPricing == "GROSSofVAT&Disc"){
                        $grossPrice = backToGross($fetch_data['supId'],$excessLine['profPrice'],$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat);
                    } else if($getPricing == "GROSSofDiscwoVAT"){
                        $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat);
                    } else {
                        $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat);
                    }

                    $pdf->setX(30);
                    $pdf->cell(80, 6, $excessLine['profCode'], 'LBR', 0, 'C');
                    $pdf->cell(20, 6, $excessLine['profItem'], 'BR', 0, 'C', 0);
                    $pdf->cell(82, 6, $excessLine['profDesc'], 'BR', 0, 'L', 0);
                    $pdf->cell(15, 6, $excessLine['profUom'], 'BR', 0, 'C', 0); 
                    $pdf->cell(12, 6, $excessLine['profQty'], 'BR', 0, 'C', 0);
                    $pdf->cell(27, 6, number_format($netPrice, 2), 'BR', 0, 'R', 0); //net price
                    $pdf->cell(30, 6, number_format($discountedPrice, 2), 'BR', 0, 'R', 0);  //discounted price
                    $pdf->cell(30, 6, number_format($grossPrice, 2), 'BR', 0, 'R', 0);  //gross price
                    $pdf->cell(32, 6, number_format($netAmount, 2), 'BR', 0, 'R', 0);  //net amount
                    $pdf->cell(32, 6, number_format($discountedPrice * $excessLine['profQty'] , 2), 'BR', 0, 'R', 0);  //discounted amount
                    $pdf->cell(32, 6, number_format( $grossPrice * $excessLine['profQty'], 2), 'BR', 0, 'R', 0);  //gross amount     

                    $overServedItemQty  += $excessLine['profQty'] ;
                    $overServedTotalAmt += $netAmount ;
                    $overServedGrossAmt += $grossPrice * $excessLine['profQty']  ; 
                    $overServedDiscountedAmt += $discountedPrice * $excessLine['profQty'] ;

                    $pdf->cell(2, 6, " ", 0, 0, 'L');

                    $pdf->cell(35, 6, $excessLine['piNo'], 'LBR', 0, 'C');
                    $pdf->cell(20, 6, $excessLine['piDate'], 'BR', 0, 'C', 0);
                    $pdf->cell(20, 6, $excessLine['piItem'], 'BR', 0, 'C', 0);
                    $pdf->cell(82, 6, $excessLine['piDesc'], 'BR', 0, 'L', 0);
                    $pdf->cell(15, 6, $excessLine['piUom'], 'BR', 0, 'C', 0);
                    $pdf->cell(12, 6, $excessLine['piQty'], 'BR', 0, 'C', 0);
                    $pdf->cell(18, 6, number_format($excessLine['piPrice'], 2), 'BR', 0, 'R', 0); //gross price
                    $pdf->cell(30, 6, number_format($discountedPricePi,2), 'BR', 0, 'R', 0); //discounted price
                    $pdf->cell(30, 6, number_format($netPricePi,2), 'BR', 0, 'R', 0); //discounted price
                    $pdf->cell(32, 6, number_format($excessLine['piPrice'] * $excessLine['piQty'], 2), 'BR', 0, 'R', 0); //gross amount
                    $pdf->cell(32, 6, number_format($discountedPricePi * $excessLine['piQty'],2), 'BR', 0, 'R', 0); //discounted amount
                    $pdf->cell(32, 6, number_format($netPricePi * $excessLine['piQty'] ,2), 'BR', 0, 'R', 0); //net amount
                    $pdf->cell(18, 6, $excessLine['profQty'] -  $excessLine['piQty'] , 'BR', 0, 'C', 0); //variance qty
                    $pdf->cell(32, 6, number_format(  ($discountedPrice * $excessLine['profQty']) - ($discountedPricePi * $excessLine['piQty']) , 2), 'BR', 0, 'R', 0); //variance gross amount

                    $overReceivedItemQty                += $excessLine['piQty'] ;
                    $overReceivedGrossAmt               += $excessLine['piPrice']   * $excessLine['piQty'] ;
                    $overReceivedDiscountedAmt          += $discountedPricePi * $excessLine['piQty'] ;
                    $overReceivedNetAmount              += $netPricePi * $excessLine['piQty'] ;
                    $overServedReceivedVarianceGrossAmt += ($grossPrice * $excessLine['profQty']) - ($excessLine['piPrice'] * $excessLine['piQty']) ;
                    $overServedReceivedVarianceDiscountedAmt += ($discountedPrice * $excessLine['profQty']) - ($discountedPricePi * $excessLine['piQty']) ;
                    $excessItemQty                      += $excessLine['profQty'] - $excessLine['piQty'] ;
                    $overReceivedItemCount ++ ;
                    $pdf->Ln();
                }
            }

            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->setX(30);
            $pdf->cell(182, 5, "OVER-SERVED TOTAL :", 'LB', 0, 'L');
            $pdf->cell(27, 5, $overServedItemQty, 'B', 0, 'R');       
            $pdf->cell(119, 5, "P " . number_format($overServedTotalAmt, 2), 'B', 0, 'R');
            $pdf->cell(32, 5, "P ". number_format($overServedDiscountedAmt,2), 'B', 0, 'R');
            $pdf->cell(32, 5, "P " . number_format($overServedGrossAmt, 2), 'BR', 0, 'R');

            $pdf->cell(2, 5, "", 0, 0, 'L');

            $pdf->cell(157, 5, "OVER-RECEIVED TOTAL :", 'LB', 0, 'L');
            $pdf->cell(27, 5, $overReceivedItemQty, 'B', 0, 'R'); 
            $pdf->cell(110, 5, "P ". number_format($overReceivedGrossAmt, 2), 'B', 0, 'R'); //gross amount
            $pdf->cell(32, 5, "P ". number_format($overReceivedDiscountedAmt,2) , 'B', 0, 'R'); //discounted amount
            $pdf->cell(32, 5, "P ". number_format($overReceivedNetAmount,2) , 'B', 0, 'R'); //net amount
            $pdf->cell(18, 5, $excessItemQty , 'B', 0, 'C'); //qty variance
            $pdf->cell(32, 5, "P ". number_format($overServedReceivedVarianceDiscountedAmt,2) , 'BR', 0, 'R');  //variance discounted amount
 
            $pdf->Ln();
            $pdf->setX(30);
            $pdf->cell(25, 5, "Over-Served/Over-Received Item Count : " . $overReceivedItemCount, '', 0, 'L');
            $pdf->Ln(10);
        }        

        if ( !empty($notFoundItemsFromProforma) ) {

            $this->createTableColumnHeader('Fully-Unserved Item(s) :','','Unserved',$pdf,'PI No');

            foreach ($notFoundItemsFromProforma as $notFound1) //fully unserved
            { 

                if($getAmounting == "NETofVAT&Disc"){
                    $netAmount = $notFound1['amount'];
                } else if($getAmounting == "GROSSofVAT&Disc"){                    
                    if($getDiscType == "PERCENTAGE"){
                        $netAmount = netPrice($fetch_data['supId'],$getPricing,$notFound1['amount'],$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat, $fetch_data['cusId']);
                    } else if($getDiscType == "AMOUNT"){
                        $price     = netPrice($fetch_data['supId'],$getPricing,$notFound1['price'],$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat, $fetch_data['cusId']);
                        $netAmount = $price * $notFound1['qty'];
                    }
                } else if($getAmounting == "NETofVATwDisc"){
                    $netAmount = netPrice($fetch_data['supId'],$getPricing,$notFound1['amount'],$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat, $fetch_data['cusId']);
                } else if($getAmounting == "NETofDiscwVAT"){
                    $price     = netPrice($fetch_data['supId'],$getPricing,$notFound1['price'],$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat, $fetch_data['cusId']);
                    $netAmount = $price * $notFound1['qty'];
                } else if($getAmounting == "GROSSofDiscwoVAT"){ //like columbia
                    $netAmount = netPrice($fetch_data['supId'],$getPricing,$line['profAmt'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat, $fetch_data['cusId']);
                }  
              
                $netPrice          = netPrice($fetch_data['supId'],$getPricing,$notFound1['price'],$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat, $fetch_data['cusId']);
                if($getPricing == "NETofVATwDisc"){
                    $grossPrice      = backToGross($fetch_data['supId'],$notFound1['price'],$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat);
                    $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat);
                } else if($getPricing == "GROSSofVAT&Disc"){
                    $grossPrice      = backToGross($fetch_data['supId'],$notFound1['price'],$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat);
                    $discountedPrice = discountedPrice($fetch_data['supId'],$notFound1['price'],$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat);
                } else if($getPricing == "GROSSofDiscwoVAT"){
                    $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat);
                    $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat);
                } else {
                    $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat);
                    $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat);
                }

                $pdf->setX(30);
                $pdf->cell(80, 6, $notFound1['profCode'], 'LBR', 0, 'C');
                $pdf->cell(20, 6, $notFound1['item'], 'BR', 0, 'C', 0);
                $pdf->cell(82, 6, $notFound1['idesc'], 'BR', 0, 'L', 0);
                $pdf->cell(15, 6, $notFound1['uom'], 'BR', 0, 'C', 0);
                $pdf->cell(12, 6, $notFound1['qty'], 'BR', 0, 'C', 0);
                $pdf->cell(27, 6, number_format($netPrice, 2), 'BR', 0, 'R', 0); // net price
                $pdf->cell(30, 6, number_format($discountedPrice, 2), 'BR', 0, 'R', 0);  //discounted price
                $pdf->cell(30, 6, number_format($grossPrice, 2), 'BR', 0, 'R', 0);  //gross price
                $pdf->cell(32, 6, number_format($netAmount, 2), 'BR', 0, 'R', 0); //net amount
                $pdf->cell(32, 6, number_format($discountedPrice * $notFound1['qty'] , 2), 'BR', 0, 'R', 0); //discounted amount
                $pdf->cell(32, 6, number_format( $grossPrice * $notFound1['qty'], 2), 'BR', 0, 'R', 0);  //gross amount
              
                $fullyUnservedItemQty  += $notFound1['qty'] ;
                $fullyUnservedTotalAmt += $netAmount ;
                $fullyUnservedGrossAmt += $grossPrice * $notFound1['qty'] ;
                $fullyUnservedDiscountedAmt += $discountedPrice * $notFound1['qty'] ; 
                $fullyUnservedItemCount ++;

                $pdf->cell(2, 6, " ", 0, 0, 'L');

                $pdf->cell(35, 6, '' , 'LB', 0, 'C');
                $pdf->cell(20, 6, '' , 'B', 0, 'C', 0);
                $pdf->cell(20, 6, '' , 'B', 0, 'C', 0);
                $pdf->cell(82, 6, '' , 'B', 0, 'L', 0);
                $pdf->cell(15, 6, '' , 'B', 0, 'C', 0);
                $pdf->cell(12, 6, '' , 'B', 0, 'C', 0);
                $pdf->cell(18, 6, '', 'BR', 0, 'R', 0); //gross price
                $pdf->cell(30, 6, number_format($discountedPrice,2), 'BR', 0, 'R', 0); //discounted price
                $pdf->cell(30, 6, number_format($netPrice,2), 'BR', 0, 'R', 0); //net price
                $pdf->cell(32, 6, number_format($grossPrice * $notFound1['qty'], 2), 'BR', 0, 'R', 0);  //gross amount
                $pdf->cell(32, 6, number_format($discountedPrice * $notFound1['qty'],2), 'BR', 0, 'R', 0); //discounted amount
                $pdf->cell(32, 6, number_format($netPrice * $notFound1['qty'] ,2), 'BR', 0, 'R', 0); //net amount
                $pdf->cell(18, 6, $notFound1['qty'] , 'BR', 0, 'C', 0); //variance qty
                $pdf->cell(32, 6, number_format( $discountedPrice * $notFound1['qty'], 2), 'BR', 0, 'R', 0); //variance discounted amount
                
                $fullyUnreceivedGrossAmt       += $grossPrice * $notFound1['qty'] ;
                $fullyUnreceivedNetAmt         += $netPrice * $notFound1['qty'];
                $fullyUnreceivedDiscountedAmt  += $discountedPrice * $notFound1['qty'] ;
                $fullyUnservedVarianceGrossAmt += $grossPrice * $notFound1['qty'] ;
                $fullyUnservedVarianceDiscountedAmt += $discountedPrice * $notFound1['qty'];
                $fullyUnservedQty              += $notFound1['qty'];
                $pdf->Ln();
            }            

            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->setX(30);
            $pdf->cell(182, 5, "FULLY-UNSERVED TOTAL :", 'LB', 0, 'L');
            $pdf->cell(27, 5, $fullyUnservedItemQty, 'B', 0, 'R');       
            $pdf->cell(119, 5, "P " . number_format($fullyUnservedTotalAmt, 2), 'B', 0, 'R');
            $pdf->cell(32, 5, "P ". number_format($fullyUnservedDiscountedAmt,2), 'B', 0, 'R');
            $pdf->cell(32, 5, "P " . number_format($fullyUnservedGrossAmt, 2), 'BR', 0, 'R');

            $pdf->cell(2, 5, "", 0, 0, 'L');

            $pdf->cell(157, 5, "", 'LB', 0, 'L');
            $pdf->cell(27, 5, "", 'B', 0, 'R'); 
            $pdf->cell(110, 5, "P ". number_format($fullyUnreceivedGrossAmt, 2), 'B', 0, 'R'); //gross amount
            $pdf->cell(32, 5, "P ". number_format($fullyUnreceivedDiscountedAmt,2) , 'B', 0, 'R'); //discounted amount
            $pdf->cell(32, 5, "P ". number_format($fullyUnreceivedNetAmt,2) , 'B', 0, 'R'); //net amount
            $pdf->cell(18, 5, $fullyUnservedQty , 'B', 0, 'C'); //qty variance
            $pdf->cell(32, 5, "P ". number_format($fullyUnservedVarianceDiscountedAmt,2) , 'BR', 0, 'R');  //variance discounted amount

            $pdf->Ln();
            $pdf->setX(30);
            $pdf->cell(25, 5, "Fully-Unserved Item Count : " . $fullyUnservedItemCount, '', 0, 'L');
            $pdf->Ln(10); /** */
        }

        if( !empty($notFoundItemsFromPi)  ){ //un ordered items, items nga naa sa PI pro wala sa proforma

            $this->createTableColumnHeader('','Fully-Overserved Item(s) :','Overserved',$pdf,'PI No');
            
            foreach($notFoundItemsFromPi as $notFound2)
            {  
                $netPricePi        = netPricePi($fetch_data['supId'],$notFound2['direct'],$notFound2['disc1'],$notFound2['disc2'],$notFound2['disc3'],$notFound2['disc4'],$notFound2['disc5'],$notFound2['disc6'],$vat);
                $discountedPricePi = discountedPricePi($fetch_data['supId'],$notFound2['direct'],$notFound2['disc1'],$notFound2['disc2'],$notFound2['disc3'],$notFound2['disc4'],$notFound2['disc5'],$notFound2['disc6']);

                $pdf->setX(30);
                $pdf->cell(80, 6, '', 'LB', 0, 'C');
                $pdf->cell(20, 6, '', 'B', 0, 'C', 0);
                $pdf->cell(82, 6, '', 'B', 0, 'L', 0);
                $pdf->cell(15, 6, '', 'B', 0, 'C', 0);
                $pdf->cell(12, 6, '', 'BR', 0, 'C', 0);
                $pdf->cell(27, 6, number_format($netPricePi,2), 'BR', 0, 'R', 0); //net price
                $pdf->cell(30, 6, number_format($discountedPricePi,2), 'BR', 0, 'R', 0); //discounted price
                $pdf->cell(30, 6, number_format($notFound2['direct'],2), 'BR', 0, 'R', 0); //gross price
                $pdf->cell(32, 6, number_format($netPricePi * $notFound2['qty'],2), 'BR', 0, 'R', 0); //net amount
                $pdf->cell(32, 6, number_format($discountedPricePi * $notFound2['qty'],2), 'BR', 0, 'R', 0); //discounted amount
                $pdf->cell(32, 6, number_format($notFound2['direct'] * $notFound2['qty'], 2), 'BR', 0, 'R', 0); //gross amount  
                
                $fullyOverservedProfGrossAmt += $notFound2['direct'] * $notFound2['qty'];
                $fullyOverservedProfNetAmt   += $netPricePi * $notFound2['qty'] ;
                $fullyOverservedProfDiscountedAmt += $discountedPricePi * $notFound2['qty'] ;
        
                $pdf->cell(2, 6, " ", 0, 0, 'L');

                $pdf->cell(35, 6, $notFound2['piNo'], 'LBR', 0, 'C');
                $pdf->cell(20, 6, $notFound2['piDate'], 'BR', 0, 'C', 0);
                $pdf->cell(20, 6, $notFound2['item'], 'BR', 0, 'C', 0);
                $pdf->cell(82, 6, $notFound2['idesc'], 'BR', 0, 'L', 0);
                $pdf->cell(15, 6, $notFound2['uom'], 'BR', 0, 'C', 0);
                $pdf->cell(12, 6, $notFound2['qty'], 'BR', 0, 'C', 0);
                $pdf->cell(18, 6, number_format($notFound2['direct'], 2), 'BR', 0, 'R', 0); //gross price
                $pdf->cell(30, 6, number_format($discountedPricePi,2), 'BR', 0, 'R', 0); //discounted price
                $pdf->cell(30, 6, number_format($netPricePi,2), 'BR', 0, 'R', 0); //net price
                $pdf->cell(32, 6, number_format($notFound2['direct'] * $notFound2['qty'], 2), 'BR', 0, 'R', 0); //gross amount
                $pdf->cell(32, 6, number_format($discountedPricePi * $notFound2['qty'],2), 'BR', 0, 'R', 0); //discounted amount
                $pdf->cell(32, 6, number_format($netPricePi * $notFound2['qty'],2), 'BR', 0, 'R', 0); //net amount
                $pdf->cell(18, 6, $notFound2['qty'] * -1 , 'BR', 0, 'C', 0); //variance qty
                $pdf->cell(32, 6, number_format(  $discountedPricePi * $notFound2['qty'] * -1 , 2), 'BR', 0, 'R', 0); //discounted gross amount

                $fullyOverreceivedItemQty             += $notFound2['qty']  ;
                $fullyOverservedGrossAmt              += $notFound2['direct']   * $notFound2['qty']   ;
                $fullyOverservedNetAmt                += $netPricePi * $notFound2['qty'];
                $fullyOverservedDiscountedAmt         += $discountedPricePi * $notFound2['qty'];
                $fullyOverservedItemQty               += $notFound2['qty'] * -1 ;
                $fullyOverservedVarianceGrossAmt      += $notFound2['direct'] * $notFound2['qty'] * -1 ;
                $fullyOverservedVarianceDiscountedAmt += $discountedPricePi * $notFound2['qty'] * -1;
                $fullyOverservedItemCount ++ ;
                $pdf->Ln();                 
            }       

            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->setX(30);
            $pdf->cell(182, 5, "", 'LB', 0, 'L');
            $pdf->cell(27, 5, "", 'B', 0, 'R');       
            $pdf->cell(119, 5, "P ". number_format($fullyOverservedProfNetAmt,2), 'B', 0, 'R');
            $pdf->cell(32, 5, "P ". number_format($fullyOverservedProfDiscountedAmt,2), 'B', 0, 'R');
            $pdf->cell(32, 5, "P " . number_format($fullyOverservedProfGrossAmt * -1, 2), 'BR', 0, 'R');

            $pdf->cell(2, 5, "", 0, 0, 'L');

            $pdf->cell(157, 5, 'FULLY-OVERSERVED TOTAL', 'LB', 0, 'L');
            $pdf->cell(27, 5, $fullyOverreceivedItemQty, 'B', 0, 'R'); 
            $pdf->cell(110, 5, "P ". number_format($fullyOverservedGrossAmt, 2), 'B', 0, 'R'); //gross amount
            $pdf->cell(32, 5, 'P '. number_format($fullyOverservedDiscountedAmt, 2), 'B', 0, 'R');  
            $pdf->cell(32, 5, "P ". number_format($fullyOverservedNetAmt,2) , 'B', 0, 'R'); //net amount 
            $pdf->cell(18, 5, $fullyOverservedItemQty, 'B', 0, 'C');   
            $pdf->cell(32, 5, 'P '. number_format($fullyOverservedVarianceDiscountedAmt,2), 'BR', 0, 'R');  

            $pdf->Ln();
            $pdf->setX(423);
            $pdf->cell(0, 5, "Fully-Overserved Item Count : " . $fullyOverservedItemCount, '', 0, 'L');
            $pdf->Ln(10);
            // *
        }

        if( !empty($freeItems)){
            $this->createTableColumnHeader('Free Item(s) :','','',$pdf,'');
            foreach($freeItems as $free)
            { 
                $pdf->setX(30);
                $pdf->cell(80, 6, $free['profCode'], 'LBR', 0, 'C');
                $pdf->cell(20, 6, $free['item'], 'BR', 0, 'C', 0);
                $pdf->cell(82, 6, $free['idesc'], 'BR', 0, 'L', 0);
                $pdf->cell(15, 6, $free['uom'], 'BR', 0, 'C', 0);
                $pdf->cell(12, 6, $free['qty'], 'BR', 0, 'C', 0);
                $pdf->cell(27, 6, '0', 'BR', 0, 'R', 0); //net price
                $pdf->cell(30, 6, '0', 'BR', 0, 'R', 0); //discounted price
                $pdf->cell(30, 6, '0', 'BR', 0, 'R', 0); //gross price
                $pdf->cell(32, 6, '0', 'BR', 0, 'R', 0); //net amount
                $pdf->cell(32, 6, '0', 'BR', 0, 'R', 0); //discounted amount
                $pdf->cell(32, 6, '0', 'BR', 0, 'R', 0); //gross amount  

                $freeItemQty   += $free['qty'];
                $freeItemCount ++ ;

                $pdf->cell(2, 6, " ", 0, 0, 'L');

                $pdf->cell(35, 6, '', 'LBR', 0, 'C');
                $pdf->cell(20, 6, '', 'BR', 0, 'C', 0);
                $pdf->cell(20, 6, '', 'BR', 0, 'C', 0);
                $pdf->cell(82, 6, '', 'BR', 0, 'L', 0);
                $pdf->cell(15, 6, '', 'BR', 0, 'C', 0);
                $pdf->cell(12, 6, '', 'BR', 0, 'C', 0);
                $pdf->cell(18, 6, '', 'BR', 0, 'R', 0); //gross price
                $pdf->cell(30, 6, '', 'BR', 0, 'R', 0); //discounted price
                $pdf->cell(30, 6, '', 'BR', 0, 'R', 0); //net price
                $pdf->cell(32, 6, '', 'BR', 0, 'R', 0); //gross amount
                $pdf->cell(32, 6, '', 'BR', 0, 'R', 0); //discounted amount
                $pdf->cell(32, 6, '', 'BR', 0, 'R', 0); //net amount
                $pdf->cell(18, 6, '' , 'BR', 0, 'C', 0); //variance qty
                $pdf->cell(32, 6, '', 'BR', 0, 'R', 0); //discounted gross amount

                $pdf->Ln();
            }
            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->setX(30);
            $pdf->cell(197, 5, "FREE ITEM(s) TOTAL :", 'LB', 0, 'L');
            $pdf->cell(12, 5, $freeItemQty, 'B', 0, 'C');   
            $pdf->cell(183, 5, '', 'BR', 0, 'R');   

            $pdf->cell(2, 5, "", 0, 0, 'L');

            $pdf->cell(408, 5, "", 'LBR', 0, 'L');
            $pdf->Ln();
            $pdf->setX(30);
            $pdf->cell(25, 5, "Free Item(s) Count : " . $freeItemCount, '', 0, 'L');
            $pdf->Ln(10); 
        }

        $totalProformaGrossAmt  = $fullyServedGrossAmt + $partiallyServedGrossAmt + $overServedGrossAmt + $fullyUnservedGrossAmt  ;
        $totalProformaNetAmt    = $fullyServedTotalAmt + $partiallyServedTotalAmt + $overServedTotalAmt + $fullyUnservedTotalAmt  ;
        $totalProformaDiscountedAmt = $fullyServedDiscountedAmt + $partiallyServedDiscountedAmt + $overServedDiscountedAmt + $fullyUnservedDiscountedAmt ;
        $totalProformaQty       = $freeItemQty + $fullyServedItemQty + $partiallyServedItemQty + $overServedItemQty + $fullyUnservedItemQty ;
        $totalProformaItemCount = $freeItemCount + $fullyServedReceivedItemCount + $partiallyServedReceivedItemCount + $overReceivedItemCount + $fullyUnservedItemCount   ;
        $totalPiItemCount       = $fullyServedReceivedItemCount + $partiallyServedReceivedItemCount + $overReceivedItemCount + $fullyOverservedItemCount ;        
        $totalPiQty             = $fullyReceivedItemQty + $partiallyReceivedItemQty + $overReceivedItemQty + $fullyOverreceivedItemQty ;
        $totalPiGrossAmount     = $fullyGrossAmount + $partiallyReceivedGrossAmt + $overReceivedGrossAmt  + $fullyOverservedGrossAmt ;
        $totalPiDiscountedAmt   = $fullyReceivedDiscountedAmt + $partiallyReceivedDiscountedAmt + $overReceivedDiscountedAmt + $fullyOverservedDiscountedAmt ;
        $totalPiNetAmount       = $fullyReceivedNetAmount + $partiallyReceivedNetAmt + $overReceivedNetAmount + $fullyOverservedNetAmt ;
        $totalPiQtyVariance     = $unServedItemQty + $excessItemQty + $fullyUnservedQty + $fullyOverservedItemQty ;
        $totalVarianceGrossAmt  = $fullyVarianceGrossAmnt + $partiallyServedReceivedVarianceGrossAmt + $overServedReceivedVarianceGrossAmt + $fullyUnservedVarianceGrossAmt + $fullyOverservedVarianceGrossAmt ; //apilon ug total ang wala na receive aron makuha ang total
        $totalVarianceDiscountedAmt = $fullyVarianceDiscountedAmt + $partiallyServedReceivedVarianceDiscountedAmt + $overServedReceivedVarianceDiscountedAmt + $fullyUnservedVarianceDiscountedAmt + $fullyOverservedVarianceDiscountedAmt ;

        /*   TOTAL OF PROFORMA && PURCHASE INVOICE     */
        $pdf->setX(30);
        $pdf->cell(150, 5, "PRO-FORMA SALES INVOICE (PSI) Total Item Count : " . $totalProformaItemCount, 0, 0, 'L');
        $pdf->cell(242, 5, '', '', 0, 'L');
        $pdf->cell(2, 5,  '', '', 0, 'L');
        $pdf->cell(80, 5,  "PURCHASE INVOICE Total Item Count : " . $totalPiItemCount, '', 0, 'L');
        $pdf->Ln(5);

        $pdf->setX(30);
        $pdf->cell(182, 5, "TOTAL PRO-FORMA SALES INVOICE (PSI) : ", 0, 0, 'L');
        $pdf->cell(27, 5,  $totalProformaQty, 0, 0, 'R');
        $pdf->cell(119, 5, "P " .number_format($totalProformaNetAmt,2), 0, 0, 'R');
        $pdf->cell(32, 5, "P ".number_format($totalProformaDiscountedAmt,2), 0, 0, 'R');
        $pdf->cell(32, 5, "P " .number_format($totalProformaGrossAmt,2), 0, 0, 'R');
        $pdf->cell(2, 5,  '', 0, 0, 'L');
        $pdf->cell(157, 5,  "TOTAL PURCHASE INVOICE (PI) : ", 0, 0, 'L');
        $pdf->cell(27, 5, $totalPiQty, 0, 0, 'R'); 
        $pdf->cell(110, 5, "P ". number_format($totalPiGrossAmount,2), 0, 0, 'R'); //gross amount
        $pdf->cell(32, 5, "P ". number_format($totalPiDiscountedAmt,2) , 0, 0, 'R'); //discounted amount
        $pdf->cell(32, 5, "P ". number_format($totalPiNetAmount,2) , 0, 0, 'R');//net amount
        $pdf->cell(18, 5, $totalPiQtyVariance , 0, 0, 'C'); //qty variance
        $pdf->cell(32, 5, "P ". number_format($totalVarianceDiscountedAmt,2) , 0, 0, 'R');  //discounted amount variance
        $pdf->Ln(10);
        /*   TOTAL OF PROFORMA && PURCHASE INVOICE     */

        /*   CREDIT MEMO     */
        if( !empty($cmLineMerge) && !empty($cmHead) ){
            $this->createTableColumnHeader('','CREDIT MEMO','Variance',$pdf,'CM No');
            foreach($cmLineMerge as $cmd)
            {
                $netPricePi        = netPricePi($fetch_data['supId'],$cmd['price'],$cmd['disc1'],$cmd['disc2'],$cmd['disc3'],$cmd['disc4'],$cmd['disc5'],$cmd['disc6'],$vat);
                $discountedPricePi = discountedPricePi($fetch_data['supId'],$cmd['price'],$cmd['disc1'],$cmd['disc2'],$cmd['disc3'],$cmd['disc4'],$cmd['disc5'],$cmd['disc6']);
                $pdf->setX(30);
                $pdf->cell(80, 6, '', 'LB', 0, 'C');
                $pdf->cell(20, 6, '', 'B', 0, 'C', 0);
                $pdf->cell(82, 6, '', 'B', 0, 'L', 0);
                $pdf->cell(15, 6, '', 'B', 0, 'C', 0);
                $pdf->cell(12, 6, '', 'BR', 0, 'C', 0);
                $pdf->cell(27, 6, '', 'BR', 0, 'R', 0); //net price
                $pdf->cell(30, 6, '', 'BR', 0, 'R', 0); //discounted price
                $pdf->cell(30, 6, '', 'BR', 0, 'R', 0); //gross price
                $pdf->cell(32, 6, '', 'BR', 0, 'R', 0); //net amount
                $pdf->cell(32, 6, '', 'BR', 0, 'R', 0); //discounted amount
                $pdf->cell(32, 6, '', 'BR', 0, 'R', 0); //gross amount 

                $pdf->cell(2, 6, " ", 0, 0, 'L');

                $pdf->cell(35, 6, $cmd['cmNo'], 'LBR', 0, 'C');
                $pdf->cell(20, 6, $cmd['date'], 'BR', 0, 'C', 0);
                $pdf->cell(20, 6, $cmd['item'], 'BR', 0, 'C', 0);
                $pdf->cell(82, 6, $cmd['desc'], 'BR', 0, 'L', 0);
                $pdf->cell(15, 6, $cmd['uom'], 'BR', 0, 'C', 0);
                $pdf->cell(12, 6, $cmd['qty'], 'BR', 0, 'C', 0);
                $pdf->cell(18, 6, number_format($cmd['price'], 2), 'BR', 0, 'R', 0); //gross price
                $pdf->cell(30, 6, number_format($discountedPricePi,2), 'BR', 0, 'R', 0); //discounted price
                $pdf->cell(30, 6, number_format($netPricePi,2), 'BR', 0, 'R', 0); //net price
                $pdf->cell(32, 6, number_format($cmd['price'] * $cmd['qty'], 2), 'BR', 0, 'R', 0); //gross amount
                $pdf->cell(32, 6, number_format($discountedPricePi * $cmd['qty'],2), 'BR', 0, 'R', 0); //discounted amount
                $pdf->cell(32, 6, number_format($netPricePi * $cmd['qty'],2), 'BR', 0, 'R', 0); //net amount
                $pdf->cell(18, 6, '0' , 'BR', 0, 'C', 0); //variance qty
                $pdf->cell(32, 6, number_format( $discountedPricePi * $cmd['qty'] * -1 , 2), 'BR', 0, 'R', 0); //discounted gross amount

                $cmItemCount          ++ ;
                $cmItemQty            += $cmd['qty'] * -1 ;
                $cmVarianceQty        += 0;//$cmd['qty'] * -1;
                $cmGrossAmount        += $cmd['price'] * $cmd['qty'] * -1;
                $cmDiscountedAmt      += $discountedPricePi * $cmd['qty'] * -1;
                $cmNetAmount          += $netPricePi * $cmd['qty'] * -1;
                $cmVarianceDiscAmount += $discountedPricePi * $cmd['qty'] * -1;
                $pdf->Ln();
                
            }
            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->setX(30);
            $pdf->cell(182, 5, "", 'LB', 0, 'L');
            $pdf->cell(27, 5, "", 'B', 0, 'R');       
            $pdf->cell(119, 5, "", 'B', 0, 'R');
            $pdf->cell(32, 5, "", 'B', 0, 'R');
            $pdf->cell(32, 5, "", 'BR', 0, 'R');

            $pdf->cell(2, 5, "", 0, 0, 'L');

            $pdf->cell(157, 5, 'CREDIT MEMO TOTAL', 'LB', 0, 'L');
            $pdf->cell(27, 5, $cmItemQty, 'B', 0, 'R'); 
            $pdf->cell(110, 5, "P ". number_format($cmGrossAmount, 2), 'B', 0, 'R'); //gross amount
            $pdf->cell(32, 5, 'P '. number_format($cmDiscountedAmt, 2), 'B', 0, 'R');  
            $pdf->cell(32, 5, "P ". number_format($cmNetAmount,2) , 'B', 0, 'R'); //net amount 
            $pdf->cell(18, 5, $cmVarianceQty, 'B', 0, 'C');   
            $pdf->cell(32, 5, "P ". number_format($cmVarianceDiscAmount,2), 'BR', 0, 'R');  

            $pdf->Ln();
            $pdf->setX(423);
            $pdf->cell(0, 5, "Credit Memo Item Count : " . $cmItemCount, '', 0, 'L');
            $pdf->Ln(10);
        }
        /*   CREDIT MEMO     */

        $pdf->setX(424);
        $pdf->cell(157, 5,  "TOTAL PURCHASE INVOICE(PI) (Net of Credit Memo) : ", 0, 0, 'L');
        $pdf->cell(27, 5, $totalPiQty + $cmItemQty, 0, 0, 'R'); 
        $pdf->cell(110, 5, "P ". number_format($totalPiGrossAmount + $cmGrossAmount ,2), 0, 0, 'R'); //gross amount
        $pdf->cell(32, 5, "P ". number_format($totalPiDiscountedAmt + $cmDiscountedAmt,2) , 0, 0, 'R'); //discounted amount
        $pdf->cell(32, 5, "P ". number_format($totalPiNetAmount + $cmNetAmount,2) , 0, 0, 'R');//net amount
        $pdf->cell(18, 5, $totalPiQtyVariance + $cmVarianceQty , 0, 0, 'C'); //qty variance
        $pdf->cell(32, 5, "P ". number_format($totalVarianceDiscountedAmt + $cmVarianceDiscAmount,2) , 0, 0, 'R');  //discounted amount variance
        $pdf->Ln(10);


        /* PROFORMA ADDITIONAL & DEDUCTION  */
        if( !empty($discountVat) && !empty($discVatSummary)){
            $pdf->SetTextColor(201, 201, 201);
            $pdf->SetFillColor(35, 35, 35);
            $pdf->setFont('Arial', '', 9);
            $pdf->setX(30);
            $pdf->Cell(80, 6, 'Pro-forma', 1, 0, 'C', TRUE);
            $pdf->cell(25, 6, "Delivery Date", 1, 0, 'C', TRUE);
            $pdf->Cell(30, 6, 'SO No', 1, 0, 'C', TRUE);        
            $pdf->cell(20, 6, "Location", 1, 0, 'C', TRUE);
            $pdf->Cell(30, 6, 'PO No', 1, 0, 'C', TRUE);
            $pdf->Cell(45, 6, "Add'l & Deduction", 1, 0, 'C', TRUE);
            $pdf->Cell(30, 6, 'Amount', 1, 0, 'C', TRUE);
            $pdf->Ln();

            $previousProf = "";
            $height       = 5;

            $pdf->SetTextColor(0, 0, 0);
            $pdf->setX(30);
            foreach ($getProfHead as $head) 
            {
                $height1 = $height * $head['numberOfDiscount'];            
                foreach ($discountVat as $dv) 
                {
                    if ($head['profId'] == $dv['profId']) {
                        if ($dv['profId'] != $previousProf) {
                            $pdf->Cell(80, $height1, $dv['profCode'], 'LBR', 0, 'C', 0);
                            $pdf->Cell(25, $height1, $dv['delivery'] != "" ? date("Y-m-d", strtotime($dv['delivery'])) : "", 'BR', 0, 'C', 0);
                            $pdf->Cell(30, $height1, $dv['sono'], 'BR', 0, 'C', 0);
                            $pdf->Cell(20, $height1, $dv['customer'], 'BR', 0, 'C', 0);
                            $pdf->Cell(30, $height1, $dv['pono'], 'BR', 0, 'C', 0);
                            $pdf->Cell(45, 5, $dv['discName'], 'BR', 0, 'L', 0);
                            $pdf->Cell(30, 5, number_format($dv['amount'], 2), 'BR', 0, 'R');
                            $pdf->Cell(30, 5, '', '0', 0, 'R');

                        } else if ($dv['profId'] == $previousProf) {
                            $pdf->Cell(80, 5, '', '', 0, 'C', 0);
                            $pdf->Cell(25, 5, '', '', 0, 'C', 0);
                            $pdf->Cell(30, 5, '', '', 0, 'C', 0);
                            $pdf->Cell(20, 5, '', '', 0, 'C', 0);
                            $pdf->Cell(30, 5, '', '', 0, 'C', 0);
                            $pdf->Cell(45, 5, $dv['discName'], 'BR', 0, 'L', 0);
                            $pdf->Cell(30, 5, number_format($dv['amount'], 2), 'BR', 0, 'R');
                            $pdf->Cell(30, 5, '', '0', 0, 'R');                       
                        }
                        $pdf->Ln();
                        $pdf->setX(30);
                        $previousProf = $dv['profId'];
                    }
                }
            }

            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            foreach($discVatSummary as $sum)
            {
                $pdf->setX(30);
                $pdf->Cell(230, 5, $sum['name'], 'LB', 0, 'L', 0);
                $pdf->Cell(30, 5, 'P '.number_format($sum['amount'],2), 'BR', 0, 'R');
                $proformaAddLessTotal += $sum['amount'];
                $pdf->Ln(5);
            }
            $pdf->setX(30);
            $pdf->Cell(230, 5, "TOTAL PSI - Add'l & Deduction :", 'LB', 0, 'L', 0);
            $pdf->Cell(30, 5, 'P '.number_format($proformaAddLessTotal,2), 'BR', 0, 'R');
            $pdf->Ln(10);
        }
        /* PROFORMA ADDITIONAL & DEDUCTION  */

        /* SOP CHARGES  */
        if(!empty($sopCharges))
        {
            $pdf->SetTextColor(201, 201, 201);
            $pdf->SetFillColor(35, 35, 35);
            $pdf->setX(30);
            $pdf->setFont('Arial', '', 9);
            $pdf->Cell(80, 7, 'SOP No', 1, 0, 'C', TRUE);
            $pdf->cell(25, 7, 'Date', 1, 0, 'C', TRUE);
            $pdf->cell(125, 7, 'Charges', 1, 0, 'C', TRUE);
            $pdf->Cell(30, 7, 'Amount', 1, 0, 'C', TRUE);

            $i = 0;
            $height = count($sopCharges) * 5;
            foreach($sopCharges as $charges)
            {
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Ln();
                $pdf->setX(30);
                if($i == 0){
                    $pdf->cell(80, $height, $charges['sop_no'], 'LBR', 0, 'C');              
                    $pdf->cell(25, $height, date("Y-m-d",strtotime($charges['datetime_created'])), 'BR', 0, 'C');
                    $pdf->cell(125, 5, $charges['description'], 'BR', 0, 'C');
                    $pdf->cell(30, 5, number_format($charges['charge_amount'] ,2), 'BR', 0, 'R');
                } else {
                    $pdf->cell(80, 5, '', '', 0, 'C');              
                    $pdf->cell(25, 5, '', '', 0, 'C');
                    $pdf->cell(125, 5, $charges['description'], 'BR', 0, 'C');
                    $pdf->cell(30, 5, number_format($charges['charge_amount'] ,2), 'BR', 0, 'R');
                }      
                $sopTotalCharges += $charges['charge_amount'] ;    
                $i++;                          
            }

            $pdf->Ln();
            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->setX(30);
            $pdf->cell(230, 5, "TOTAL SOP Charges :", 'LB', 0, 'L', 0);
            $pdf->cell(30, 5, "P " . number_format($sopTotalCharges, 2), 'BR', 0, 'R');
            $pdf->Ln(10);
        }
        /* SOP CHARGES  */


        /* SOP DEDUCTION  */
        if(!empty($sopDeduction))
        {
            $pdf->SetTextColor(201, 201, 201);
            $pdf->SetFillColor(35, 35, 35);
            $pdf->setX(30);
            $pdf->setFont('Arial', '', 9);
            $pdf->Cell(80, 7, 'SOP No', 1, 0, 'C', TRUE);
            $pdf->cell(25, 7, 'Date', 1, 0, 'C', TRUE);
            $pdf->cell(125, 7, 'Deduction', 1, 0, 'C', TRUE);
            $pdf->Cell(30, 7, 'Amount', 1, 0, 'C', TRUE);

            $i = 0;
            $height = count($sopDeduction) * 5;
            foreach($sopDeduction as $sopRow)
            {
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Ln();
                $pdf->setX(30);
                if($i == 0){
                    $pdf->cell(80, $height, $sopRow['sop_no'], 'LBR', 0, 'C');              
                    $pdf->cell(25, $height, date("Y-m-d",strtotime($sopRow['datetime_created'])), 'BR', 0, 'C');
                    $pdf->cell(125, 5, $sopRow['description'], 'BR', 0, 'C');
                    $pdf->cell(30, 5, number_format($sopRow['deduction_amount'] ,2), 'BR', 0, 'R');
                } else {
                    $pdf->cell(80, 5, '', '', 0, 'C');              
                    $pdf->cell(25, 5, '', '', 0, 'C');
                    $pdf->cell(125, 5, $sopRow['description'], 'BR', 0, 'C');
                    $pdf->cell(30, 5, number_format($sopRow['deduction_amount'] ,2), 'BR', 0, 'R');
                }      
                $sopTotalDeductionW12 += $sopRow['deduction_amount'] ;    
                $i++;                          
            }

            $pdf->Ln();
            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->setX(30);
            $pdf->cell(230, 5, "TOTAL SOP Deduction :", 'LB', 0, 'L', 0);
            $pdf->cell(30, 5, "P " . number_format($sopTotalDeductionW12, 2), 'BR', 0, 'R');
            $pdf->Ln(10);
        } /* SOP DEDUCTION  */

        /* VARIANCE MENTION/S FROM OTHER SOP  */
        if(!empty($mentionsFromSop)){
            $pdf->Ln(2);
            $pdf->setX(30);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(40, 0, 'VARIANCE MENTIONS FROM OTHER SOP :', 0, 0, 'L');
            $pdf->Ln(2);
            $pdf->SetTextColor(201, 201, 201);
            $pdf->SetFillColor(35, 35, 35);            
            $pdf->setX(30);
            $pdf->setFont('Arial', '', 9);
            $pdf->Cell(80, 7, 'SOP No', 1, 0, 'C', TRUE);
            $pdf->cell(25, 7, 'Date', 1, 0, 'C', TRUE);
            $pdf->cell(125, 7, 'Deduction', 1, 0, 'C', TRUE);
            $pdf->Cell(30, 7, 'Amount', 1, 0, 'C', TRUE);

            $pdf->SetTextColor(0, 0, 0);
            foreach($mentionsFromSop as $mention)
            {                
                $pdf->Ln();
                $pdf->setX(30);
                $pdf->cell(80, 5, $mention['sop_no'], 'LBR', 0, 'C');              
                $pdf->cell(25, 5, date("Y-m-d",strtotime($mention['datetime_created'])), 'BR', 0, 'C');
                $pdf->cell(125, 5, $mention['description'], 'BR', 0, 'C');
                $pdf->cell(30, 5, number_format($mention['deduction_amount'],2), 'BR', 0, 'R'); 
                $sopVarianceMentionTotal += $mention['deduction_amount']  ;    
            }

            $pdf->Ln();
            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->setX(30);
            $pdf->cell(230, 5, "TOTAL SOP Deduction (Mentions) :", 'LB', 0, 'L', 0);
            $pdf->cell(30, 5, "P " . number_format($sopVarianceMentionTotal, 2), 'BR', 0, 'R');
            $pdf->Ln(15);
        }
        /* VARIANCE MENTION/S FROM OTHER SOP  */
        
        $pdf->setFont('Arial', 'B', 10);
        $pdf->SetTextColor(0, 0, 0);

        /*   GET VARIANCE     */
        $pdf->setX(30);
        $pdf->Cell(105, 0, 'TOTAL PRO-FORMA SALES INVOICE (PSI) (Gross Amount) : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P " . number_format($totalProformaGrossAmt, 2), 0, 0, 'R');
        $pdf->Ln(5); 
        $pdf->setX(30);
        $pdf->Cell(105, 0, 'Add: TOTAL SOP Charges ', 0, 0, 'R');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->cell(30, 0, "P ". number_format( $sopTotalCharges ,2), 0, 0, 'R');
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Ln(5); 
        $pdf->setX(30);
        $pdf->Cell(105, 0, 'Less: TOTAL SOP Deduction ', 0, 0, 'R');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->cell(30, 0, "P ". number_format( $sopTotalDeductionW12 ,2), 0, 0, 'R');
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Ln(5); 
        $pdf->setX(30);
        $pdf->Cell(105, 0, 'TOTAL CRF/CV Amount : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P ". number_format($totalProformaGrossAmt + $sopTotalCharges + $sopTotalDeductionW12,2), 0, 0, 'R');
        $pdf->Ln(10);

        $pdf->setX(30);
        $pdf->Cell(105, 0, 'TOTAL PURCHASE INVOICE (PI) (Gross Amount) : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P ". number_format($totalPiGrossAmount + $cmGrossAmount,2), 0, 0, 'R');
        $pdf->Ln(5);
        $pdf->setX(30);
        $pdf->Cell(105, 0, 'Less: TOTAL SOP Deduction', 0, 0, 'R');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->cell(30, 0, "P ". number_format($sopTotalDeductionW12,2), 0, 0, 'R');
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Ln(5);
        $pdf->setX(30);
        $pdf->Cell(105, 0, 'NET PURCHASE INVOICE (PI) Amount : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P ". number_format( ($totalPiGrossAmount + $cmGrossAmount + $sopTotalDeductionW12) ,2), 0, 0, 'R');
        $pdf->Ln(10);

        $pdf->setX(30);
        $pdf->Cell(105, 0, 'TOTAL CRF/CV Amount : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P ". number_format( $totalProformaGrossAmt + $sopTotalCharges + $sopTotalDeductionW12,2), 0, 0, 'R');
        $pdf->Ln(5);
        $pdf->setX(30);
        $pdf->Cell(105, 0, 'NET PURCHASE INVOICE (PI) Amount : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->cell(30, 0, "P ". number_format( ($totalPiGrossAmount + $cmGrossAmount +  $sopTotalCharges + $sopTotalDeductionW12) * -1,2), 0, 0, 'R');
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Ln(5);
        $pdf->setX(30);
        $pdf->Cell(105, 0, 'Variance (Total) : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P ". number_format( ($totalProformaGrossAmt +  $sopTotalCharges + $sopTotalDeductionW12) + (($totalPiGrossAmount + $cmGrossAmount + $sopTotalDeductionW12) * -1)  ,2), 0, 0, 'R');
        $pdf->Ln(10);

        $pdf->setX(30);
        $pdf->Cell(105, 0, 'Variance (Total) : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P ". number_format( ($totalProformaGrossAmt +  $sopTotalCharges + $sopTotalDeductionW12) + (($totalPiGrossAmount + $cmGrossAmount + $sopTotalDeductionW12) * -1)  ,2), 0, 0, 'R');
        $pdf->Ln(5);
        $pdf->setX(30);
        $pdf->Cell(105, 0, 'Variance (Item) : ', 0, 0, 'L'); 
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->cell(30, 0, "P ". number_format( $totalVarianceGrossAmt * -1,2), 0, 0, 'R');
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Ln(5);        
        $pdf->setX(30);
        $pdf->cell(230, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P ". number_format( ($totalProformaGrossAmt +  $sopTotalCharges + $sopTotalDeductionW12) + (($totalPiGrossAmount + $cmGrossAmount + $sopTotalDeductionW12) * -1) +  ($totalVarianceGrossAmt * -1) ,2), 0, 0, 'R');
        
        $pdf->Ln(10);
        $pdf->setX(30);
        $pdf->Cell(105, 0, 'TOTAL PRO-FORMA SALES INVOICE (PSI) (Discounted Amount) : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P " . number_format($totalProformaDiscountedAmt, 2), 0, 0, 'R');
        $pdf->Ln(5); 
        $pdf->setX(30);
        $pdf->Cell(105, 0, 'TOTAL PURCHASE INVOICE (PI) (Discounted Amount) : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->cell(30, 0, "P ". number_format(($totalPiDiscountedAmt + $cmDiscountedAmt) * -1,2), 0, 0, 'R');
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Ln(5);
        $pdf->setX(30);
        $pdf->Cell(105, 0, 'Variance (Discounted Amount) : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P ". number_format( $totalProformaDiscountedAmt + ($totalPiDiscountedAmt + $cmDiscountedAmt) * -1,2), 0, 0, 'R');

        $pdf->Ln(10);
        $pdf->setX(30);
        $pdf->Cell(105, 0, 'TOTAL PRO-FORMA SALES INVOICE (PSI) (Net Amount) : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P " . number_format($totalProformaNetAmt, 2), 0, 0, 'R');
        $pdf->Ln(5); 
        $pdf->setX(30);
        $pdf->Cell(105, 0, 'TOTAL PURCHASE INVOICE (PI) (Net Amount) : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->SetFont('Arial', 'BU', 10);   
        $pdf->cell(30, 0, "P ". number_format(($totalPiNetAmount + $cmNetAmount) * -1,2), 0, 0, 'R');
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Ln(5);
        $pdf->setX(30);
        $pdf->Cell(105, 0, 'Variance (Net Amount) : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P ". number_format( $totalProformaNetAmt + ($totalPiNetAmount + $cmNetAmount) * -1,2), 0, 0, 'R');  

        $pdf->Ln(10);
        $pdf->setX(30);
        $pdf->Cell(105, 0, 'Variance (Total) : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P ". number_format( ($totalProformaGrossAmt +  $sopTotalCharges + $sopTotalDeductionW12) + (($totalPiGrossAmount + $cmGrossAmount + $sopTotalDeductionW12) * -1)   ,2), 0, 0, 'R');
        $varianceTotal = ($totalProformaGrossAmt +  $sopTotalCharges + $sopTotalDeductionW12) + (($totalPiGrossAmount + $cmGrossAmount + $sopTotalDeductionW12) * -1) ;
        $pdf->Ln(5);
        $pdf->setX(30);
        $pdf->Cell(105, 0, 'Mentions From Other SOP : ', 0, 0, 'L'); 
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->cell(30, 0, "P ". number_format( $sopVarianceMentionTotal,2), 0, 0, 'R');
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Ln(5);        
        $pdf->setX(30);
        $pdf->cell(230, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P ". number_format( $varianceTotal + $sopVarianceMentionTotal ,2), 0, 0, 'R');

        $supAcroname = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['acroname'];
        $cusAcroname = $this->proformavspi_model->getCustomerData($fetch_data['cusId'],'customer_code')['l_acroname'];
        $fileName    = $supAcroname.'-'. $cusAcroname.' - PSIvsPI'  . time() . '.pdf';

        // for history
        $this->db->set('filename', $fileName)
                 ->where('tr_id', $transactionId)
                 ->update('profvpi_transaction');
        // for history

        // for variance
        if($fetch_data['saveVar'] == '1'){
            $varianceToSave = 0;
            if( $fetch_data['supId'] == '9'){
                $varianceToSave = round($totalProformaDiscountedAmt + ($totalPiDiscountedAmt + $cmDiscountedAmt) * -1,2);
            }

            $varianceData = ['crf_id'            => $fetch_data['crfId'],
                             'supplier_id'       => $fetch_data['supId'],
                             'variance_amount'   => $varianceToSave,//($totalProformaGrossAmt + $sopTotalDeductionW12) + (($totalPiGrossAmount + $cmGrossAmount + $sopTotalDeductionW12) * -1),
                             'user_id'           => $this->session->userdata('user_id'),
                             'datetime_created'  => date("Y-m-d H:i:s") ];
            $this->db->insert('variance',$varianceData);
            $varId = $this->db->insert_id();
            $ledger = ['variance_id'  => $varId,
                       'sop_id'       => 0,
                       'debit'        => $varianceToSave,//($totalProformaGrossAmt + $sopTotalDeductionW12) + (($totalPiGrossAmount + $cmGrossAmount + $sopTotalDeductionW12) * -1),
                       'credit'       => 0 ];
            $this->db->insert('variance_ledger',$ledger);
        }
        // for variance

        $pdf->Output('files/Reports/ProformaVsPi/' . $fileName, 'F');

        return $fileName;
    }
    //gross

    private function createTableColumnHeader($one, $two, $three,$pdf,$four)
    {
        $pdf->SetFont('Arial', '', 10);
        $pdf->setX(30);
        $pdf->Cell(40, 0, $one, 0, 0, 'L');
        $pdf->setX(423);
        $pdf->Cell(0, 0, $two, 0, 0, 'L');
        $pdf->Ln(3);

        $pdf->SetFont('Arial','',8);
        $pdf->SetTextColor(201, 201, 201);
        $pdf->SetFillColor(35, 35, 35);

        $pdf->setX(30);
        $pdf->cell(80, 8, "Pro-forma", 1, 0, 'C', TRUE);
        $pdf->cell(20, 8, "Item", 1, 0, 'C', TRUE);
        $pdf->cell(82, 8, "Description", 1, 0, 'C', TRUE);
        $pdf->cell(15, 8, "UOM", 1, 0, 'C', TRUE);
        $pdf->cell(12, 8, "Qty", 1, 0, 'C', TRUE);
        
        $pdf->cell(27, 4, "Net Price", 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(239);
        $pdf->cell(27, 4, "(Net of VAT & Disct.)", 'LBR', 0, 'C', TRUE);

        $pdf->Ln(-4);
        $pdf->setX(266);
        $pdf->cell(30, 4, "Discounted Price", 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(266);
        $pdf->cell(30, 4, "(Net of Disct. incl. VAT)", 'LBR', 0, 'C', TRUE);

        $pdf->Ln(-4);
        $pdf->setX(296);
        $pdf->cell(30, 4, "Gross Price", 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(296);
        $pdf->cell(30, 4, "(Gross of VAT & Disct.)", 'LBR', 0, 'C', TRUE);
        
        $pdf->Ln(-4);
        $pdf->setX(326);
        $pdf->cell(32, 8, "Net Amount", 'LTR', 0, 'C', TRUE);
        $pdf->cell(32, 8, "Discounted Amount", 'LTR', 0, 'C', TRUE);
        $pdf->cell(32, 8, "Gross Amount", 'LTR', 0, 'C', TRUE);
       
        $pdf->setX(422);
        $pdf->cell(2, 8, "", 0, 0, 'L');

        $pdf->SetTextColor(201, 201, 201);
        $pdf->SetFillColor(35, 35, 35);
        $pdf->cell(35, 8, $four, 1, 0, 'C', TRUE);
        $pdf->cell(20, 8, "Date", 1, 0, 'C', TRUE);
        $pdf->cell(20, 8, "Item", 1, 0, 'C', TRUE);
        $pdf->cell(82, 8, "Description", 1, 0, 'C', TRUE);
        $pdf->cell(15, 8, "UOM", 1, 0, 'C', TRUE);
        $pdf->cell(12, 8, "Qty", 1, 0, 'C', TRUE);

        $pdf->cell(18, 4, "Unit Price", 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(608);
        $pdf->cell(18, 4, "(Gross)", 'LBR', 0, 'C', TRUE);

        $pdf->Ln(-4);
        $pdf->setX(626);
        $pdf->cell(30, 4, "Discounted Price", 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(626);
        $pdf->cell(30, 4, "(Net of Disct. incl. VAT)", 'LBR', 0, 'C', TRUE);

        $pdf->Ln(-4);
        $pdf->setX(656);
        $pdf->cell(30, 4, "Net Price", 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(656);
        $pdf->cell(30, 4, "(Net of VAT & Disct.)", 'LBR', 0, 'C', TRUE);

        $pdf->Ln(-4);
        $pdf->setX(686);
        $pdf->cell(32, 8, "Gross Amount", 1, 0, 'C', TRUE);

        $pdf->setX(718);
        $pdf->cell(32, 8, "Discounted Amount", 1, 0, 'C', TRUE);

        $pdf->setX(750);
        $pdf->cell(32, 8, "Net Amount", 1, 0, 'C', TRUE);        

        $pdf->setX(782);
        $pdf->cell(18, 4, $three, 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(782);
        $pdf->cell(18, 4, "(Qty)", 'LBR', 0, 'C', TRUE);

        $pdf->Ln(-4);
        $pdf->setX(800);
        $pdf->cell(32, 4, "Variance", 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(800);
        $pdf->cell(32, 4, "(Discounted Amount)", 'LBR', 0, 'C', TRUE);
        
        $pdf->setFont('times', '', 7);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();
    }

    //excell generateProformaVsPiExcel
    private function generateProformaVsPiExcel($transactionId, $fetch_data, $getProfHead, $discountVat, $discVatSummary, $profPiMerge, $fullyServed, $partiallyServed,$excess, $sopDeduction, $sopCharges,
                                          $notFoundItemsFromProforma, $notFoundItemsFromPi,$cmHead,$cmLineMerge,$freeItems) 
    {
        $supplier             = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['supplier_name'];
        $getPricing           = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['pricing'];
        $getAmounting         = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['amounting'];
        $getDiscType          = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['disc_type'];
        $vat                  = $this->proformavspi_model->getVATData()['value'];

        $fullyServedItemQty             = 0;
        $fullyServedTotalAmt            = 0;
        $fullyServedGrossAmt            = 0;
        $fullyServedDiscountedAmt       = 0;  
        $fullyReceivedItemQty           = 0;       
        $fullyReceivedNetAmount         = 0;
        $fullyGrossAmount               = 0;
        $fullyReceivedDiscountedAmt     = 0;  
        $fullyVarianceGrossAmnt         = 0;
        $fullyVarianceDiscountedAmt     = 0;
        $fullyServedReceivedVarianceQty = 0; 
        $fullyServedReceivedItemCount   = 0;
        $freeItemQty                    = 0;
        $freeItemCount                  = 0;
        
        $partiallyServedItemQty                  = 0;
        $partiallyServedTotalAmt                 = 0;
        $partiallyServedGrossAmt                 = 0;
        $partiallyServedDiscountedAmt            = 0;
        $partiallyReceivedItemQty                = 0;
        $partiallyReceivedGrossAmt               = 0;
        $partiallyReceivedDiscountedAmt          = 0;
        $partiallyReceivedNetAmt                 = 0;
        $unServedItemQty                         = 0;
        $partiallyServedReceivedItemCount        = 0;
        $partiallyServedReceivedVarianceGrossAmt = 0;
        $partiallyServedReceivedVarianceDiscountedAmt = 0;

        $overServedItemQty                  = 0;
        $overServedTotalAmt                 = 0;
        $overServedGrossAmt                 = 0;
        $overServedDiscountedAmt            = 0;
        $overReceivedItemQty                = 0;
        $overReceivedGrossAmt               = 0;
        $overReceivedDiscountedAmt          = 0;
        $overReceivedNetAmount              = 0;
        $overReceivedItemCount              = 0 ;
        $overServedReceivedVarianceGrossAmt = 0;
        $overServedReceivedVarianceDiscountedAmt = 0;
        $excessItemQty                      = 0;

        $fullyUnservedItemQty          = 0;
        $fullyUnservedTotalAmt         = 0;
        $fullyUnservedGrossAmt         = 0;
        $fullyUnservedDiscountedAmt    = 0;
        $fullyUnservedItemCount        = 0;
        $fullyUnreceivedGrossAmt       = 0;
        $fullyUnreceivedNetAmt         = 0;  
        $fullyUnreceivedDiscountedAmt  = 0;
        $fullyUnservedVarianceGrossAmt = 0;
        $fullyUnservedVarianceDiscountedAmt = 0;
        $fullyUnservedQty              = 0;
        
            
        $fullyOverservedProfGrossAmt      = 0;
        $fullyOverservedProfNetAmt        = 0;
        $fullyOverservedProfDiscountedAmt = 0;
        $fullyOverreceivedItemQty        = 0;
        $fullyOverservedGrossAmt         = 0;
        $fullyOverservedNetAmt           = 0;
        $fullyOverservedDiscountedAmt    = 0;
        $fullyOverservedItemQty          = 0;
        $fullyOverservedVarianceGrossAmt = 0;
        $fullyOverservedVarianceDiscountedAmt = 0;
        $fullyOverservedItemCount        = 0;

        $cmItemCount           = 0;
        $cmItemQty             = 0;
        $cmGrossAmount         = 0;
        $cmDiscountedAmt       = 0;
        $cmNetAmount           = 0;
        $cmVarianceQty         = 0; 
        $cmVarianceDiscAmount  = 0;

        $totalProformaGrossAmt = 0;
        $totalProformaQty      = 0; 
        $totalProformaItemCount= 0;
        $totalProformaGrossAmt = 0;
        $totalProformaDiscountedAmt = 0;
        $totalPiGrossAmount    = 0;
        $totalPiNetAmount      = 0;
        $totalPiQty            = 0; 
        
        $totalVarianceGrossAmt      = 0;
        $totalVarianceDiscountedAmt = 0;
        $proformaAddLessTotal       = 0;  
        $sopTotalDeductionW12       = 0;
        $sopTotalDeductionWo12      = 0;
        $sopTotalWHT                = 0;
        $total1PercentDisc          = 0;
        $total2PercentDisc          = 0;
        $totalVat                   = 0;

        $netPrice   = 0.00;
        $netAmount  = 0.00;
        $discountedPrice = 0.00;
        $discountedAmount = 0.00 ;
        $grossPrice = 0.00;
        $grossAmount= 0.00;

        $netPricePi         = 0.00;
        $discountedPricePi  = 0.00;        
        
        $objPHPExcel = new PHPExcel();
        $objDrawing  = new PHPExcel_Worksheet_Drawing();        
        $active_sheet = $objPHPExcel->getActiveSheet();
        /********************* SHEET Styles **********************/
        $systemTitleStyle  = array( 'font'=> array(
                                                'color' => array('rgb' => '000000'),
                                                'name'  =>  'Arial',
                                                'size'  =>  12,
                                                'bold'  =>  TRUE ),
                                    'alignment' => array(
                                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT )  );
        $supplierStyle     = array( 'font'=> array(
                                                'color' => array('rgb' => '000000'),
                                                'name'  =>  'Arial',
                                                'size'  =>  12,
                                                'bold'  =>  TRUE ) );
        $reportHeaderStyle = array( 'font'=> array(
                                                'color' => array('rgb' => '000000'),
                                                'name'  =>  'Arial',
                                                'size'  =>  10 ) );
        $tableHeaderStyle  = array( 'font'=> array(
                                                'color' => array('rgb' => 'ffffff'),
                                                'name'  =>  'Arial',
                                                'size'  =>  9),
                                    'fill' => array(
                                                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                                                'color' => array('rgb' => '000000') ),
                                    'alignment' => array(
                                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)  );
        $allBorderStyle    = array( 'borders' => array(
                                                  'allborders' => array(
                                                        'style' => PHPExcel_Style_Border::BORDER_THIN ) )  );
        $tableFooterStyle  = array( 'font'=> array(
                                                'color' => array('rgb' => '000000'),
                                                'name'  =>  'Arial',
                                                'size'  =>  10,
                                                'bold'  =>  TRUE ),
                                   'borders'=> array(
                                                'outline' => array(
                                                    'style' => PHPExcel_Style_Border::BORDER_THIN ) )   );

        $itemCountStyle    = array( 'font'=> array(
                                                'color' => array('rgb' => '000000'),
                                                'name'  =>  'Arial',
                                                'size'  =>  10,
                                                'bold'  =>  TRUE ) );
        $boldRightStyle    = array( 'font'=> array(
                                                'color' => array('rgb' => '000000'),
                                                'name'  =>  'Arial',
                                                'size'  =>  10,
                                                'bold'  =>  TRUE ),
                                    'alignment' => array(
                                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT ) );  
        $boldRightUnderLinedStyle = array( 'font'=> array(
                                                'color' => array('rgb' => '000000'),
                                                'name'  =>  'Arial',
                                                'size'  =>  10,
                                                'bold'  =>  TRUE,
                                                'underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE),
                                            'alignment' => array(
                                                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT ) );        
        $centerTextStyle   = array( 'alignment' => array(
                                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER ) );
        $mergedCellBorderStyle = array( 'borders' => array(
                                                        'allborders' => array(
                                                            'style' => PHPExcel_Style_Border::BORDER_THIN ) )  );
        /********************* SHEET Styles **********************/
        /********************* SHEET Headings **********************/         
         $logo         =  getcwd().'/assets/img/alturas.png';
         $objDrawing->setPath($logo);
         $objDrawing->setCoordinates('A1');
         $objDrawing->setResizeProportional(false); 
         $objDrawing->setWidth(30); 
         $objDrawing->setHeight(40); 
         $objDrawing->setWorksheet($active_sheet);
         $active_sheet->getRowDimension(1)->setRowHeight(30);
       
        $active_sheet->setCellValue('A1', 'Cash With Order Monitoring Report')
                     ->getStyle('A1')->applyFromArray($systemTitleStyle);
        $active_sheet->setCellValue('M1', 'Prepared By : '.$this->session->userdata('name'))
                     ->setCellValue('P1', date("F d, Y - h:i:s A"))
                     ->getStyle('M1:P1')->applyFromArray($itemCountStyle);
        $active_sheet->setCellValue('A3', $supplier)
                     ->getStyle('A3')->applyFromArray($supplierStyle);
        $active_sheet->setCellValue('A4', 'PRO-FORMA SALES INVOICE vs PURCHASE INVOICE - VARIANCE REPORT')
                     ->setCellValue('A6', 'PRO-FORMA SALES INVOICE')
                     ->setCellValue('M6', 'PURCHASE INVOICE')
                     ->getStyle("A4:M6")->applyFromArray($reportHeaderStyle);     
        /********************* SHEET Headings **********************/       
                                     
        if($fullyServed !== false ){ //fully served/received
            $newRow = $this->getHighestDataRow($active_sheet);  
            $this->writeTableColumnHeader($active_sheet,$newRow,$reportHeaderStyle,$tableHeaderStyle,'Fully-Served Item(s) :','Fully-Received Item(s) :','Variance','PI No');        

            foreach ($profPiMerge as $line) 
            {
                if($line['fullyServed']  == 1){                  
                    
                    if($getAmounting == "NETofVAT&Disc"){
                        $netAmount = $line['profAmt'];
                    } else if($getAmounting == "GROSSofVAT&Disc"){
                        if($getDiscType == "PERCENTAGE"){
                            $netAmount = netPrice($fetch_data['supId'],$getPricing,$line['profAmt'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat, $fetch_data['cusId']);
                        } else if($getDiscType == "AMOUNT"){
                            $price     = netPrice($fetch_data['supId'],$getPricing,$line['profPrice'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat, $fetch_data['cusId']);
                            $netAmount = $price * $line['profQty'];
                        }
                    } else if($getAmounting == "NETofVATwDisc"){
                        $netAmount = netPrice($fetch_data['supId'],$getPricing,$line['profAmt'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat, $fetch_data['cusId']);
                    } else if($getAmounting == "NETofDiscwVAT"){
                        $price     = netPrice($fetch_data['supId'],$getPricing,$line['profPrice'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat, $fetch_data['cusId']);
                        $netAmount = $price * $line['profQty'];
                    }

                    $netPrice          = netPrice($fetch_data['supId'],$getPricing,$line['profPrice'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat, $fetch_data['cusId']);   
                    $netPricePi        = netPricePi($fetch_data['supId'],$line['piPrice'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat);   
                    $discountedPricePi = discountedPricePi($fetch_data['supId'],$line['piPrice'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6']);          
                    if($getPricing == "NETofVATwDisc"){
                        $grossPrice      = backToGross($fetch_data['supId'],$line['profPrice'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat);
                    } else if($getPricing == "GROSSofVAT&Disc"){
                        $grossPrice      = backToGross($fetch_data['supId'],$line['profPrice'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$line['profPrice'],$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat);
                    } else if($getPricing == "GROSSofDiscwoVAT"){
                        $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat);
                    } else { //NETofVAT&Disc
                        $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$line['disc1'],$line['disc2'],$line['disc3'],$line['disc4'],$line['disc5'],$line['disc6'],$vat);
                    }

                    $newRow = $this->getHighestDataRow($active_sheet); 
                    $active_sheet->setCellValue('A'.$newRow, $line['profCode'])
                                 ->setCellValue('B'.$newRow, $line['profItem'])
                                 ->setCellValue('C'.$newRow, $line['profDesc'])
                                 ->setCellValue('D'.$newRow, $line['profUom'])
                                 ->setCellValue('E'.$newRow, $line['profQty'])
                                 ->setCellValue('F'.$newRow, number_format($netPrice, 2))
                                 ->setCellValue('G'.$newRow, number_format($discountedPrice,2))
                                 ->setCellValue('H'.$newRow, number_format($grossPrice, 2))
                                 ->setCellValue('I'.$newRow, number_format($netAmount, 2))
                                 ->setCellValue('J'.$newRow, number_format($discountedPrice * $line['profQty'], 2))
                                 ->setCellValue('K'.$newRow, number_format($grossPrice * $line['profQty'], 2))                                 
                                 ->getStyle('F'.$newRow.':K'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $active_sheet->getStyle('A'.$newRow.':K'.$newRow)->applyFromArray($allBorderStyle);
                    $active_sheet->getStyle('A'.$newRow.':D'.$newRow)->applyFromArray($centerTextStyle);

                    $fullyServedTotalAmt += $netAmount;
                    $fullyServedItemQty  += $line['profQty'];
                    $fullyServedGrossAmt += $grossPrice * $line['profQty']  ;
                    $fullyServedDiscountedAmt += $discountedPrice * $line['profQty'] ;

                    $active_sheet->setCellValue('M'.$newRow, $line['piNo'])
                                 ->setCellValue('N'.$newRow, $line['piDate'])
                                 ->setCellValue('O'.$newRow, $line['piItem'])
                                 ->setCellValue('P'.$newRow, $line['piDesc'])
                                 ->setCellValue('Q'.$newRow, $line['piUom'])
                                 ->setCellValue('R'.$newRow, $line['piQty'])
                                 ->setCellValue('S'.$newRow, number_format($line['piPrice'], 2))
                                 ->setCellValue('T'.$newRow, number_format($discountedPricePi,2))
                                 ->setCellValue('U'.$newRow, number_format($netPricePi,2))
                                 ->setCellValue('V'.$newRow, number_format($line['piPrice'] * $line['piQty'], 2))
                                 ->setCellValue('W'.$newRow, number_format($discountedPricePi * $line['piQty'],2))
                                 ->setCellValue('X'.$newRow, number_format($netPricePi * $line['piQty'] ,2))
                                 ->setCellValue('Y'.$newRow, $line['profQty'] -  $line['piQty'])
                                 ->setCellValue('Z'.$newRow, number_format( ($discountedPrice * $line['profQty']) - ($discountedPricePi * $line['piQty']),2))
                                 ->getStyle('S'.$newRow.':Z'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $active_sheet->getStyle('M'.$newRow.':Z'.$newRow)->applyFromArray($allBorderStyle);
                    $active_sheet->getStyle('M'.$newRow.':Q'.$newRow)->applyFromArray($centerTextStyle);
 
                    $fullyGrossAmount               += round($line['piPrice'] * $line['piQty'] , 2);   
                    $fullyReceivedDiscountedAmt     += $discountedPricePi * $line['piQty'];
                    $fullyReceivedNetAmount         += $netPricePi * $line['piQty'];
                    $fullyVarianceGrossAmnt         += ($grossPrice * $line['profQty'] ) - ($line['piPrice'] * $line['piQty']) ;        
                    $fullyVarianceDiscountedAmt     += ($discountedPrice * $line['profQty']) - ($discountedPricePi * $line['piQty']);     
                    $fullyServedReceivedVarianceQty += $line['profQty'] -  $line['piQty'] ;
                    $fullyReceivedItemQty           += $line['piQty']; 
                    $fullyServedReceivedItemCount ++;
                }
            }

            $newRow = $this->getHighestDataRow($active_sheet); 
            $active_sheet->setCellValue('A'.$newRow, 'FULLY-SERVED TOTAL :')
                         ->setCellValue('E'.$newRow, $fullyServedItemQty)
                         ->setCellValue('I'.$newRow, 'P '.number_format($fullyServedTotalAmt,2))
                         ->setCellValue('J'.$newRow, 'P '.number_format($fullyServedDiscountedAmt,2))
                         ->setCellValue('K'.$newRow, 'P '.number_format($fullyServedGrossAmt,2))
                         ->getStyle('A'.$newRow.':K'.$newRow)->applyFromArray($tableFooterStyle);
            $active_sheet->getStyle('I'.$newRow.':K'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $active_sheet->setCellValue('M'.$newRow, 'FULLY-RECEIVED TOTAL :')
                         ->setCellValue('R'.$newRow, $fullyServedItemQty)
                         ->setCellValue('V'.$newRow, 'P '.number_format($fullyGrossAmount,2))
                         ->setCellValue('W'.$newRow, 'P '.number_format($fullyReceivedDiscountedAmt,2))
                         ->setCellValue('X'.$newRow, 'P '.number_format($fullyReceivedNetAmount,2))
                         ->setCellValue('Y'.$newRow, $fullyServedReceivedVarianceQty)
                         ->setCellValue('Z'.$newRow, 'P '.number_format($fullyVarianceDiscountedAmt,2))
                         ->getStyle('M'.$newRow.':Z'.$newRow)->applyFromArray($tableFooterStyle);
            $active_sheet->getStyle('V'.$newRow.':Z'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $newRow = $this->getHighestDataRow($active_sheet); 
            $active_sheet->setCellValue('A'.$newRow, 'Fully-Served/Received Item Count : '.$fullyServedReceivedItemCount)
                         ->getStyle('A'.$newRow)->applyFromArray($itemCountStyle);
        }  
                                    
        if($partiallyServed !== false ){ //partially served
            $newRow = $this->getHighestDataRow($active_sheet) + 1;   
            $this->writeTableColumnHeader($active_sheet,$newRow,$reportHeaderStyle,$tableHeaderStyle,'Underserved Item(s) :','Partially-Received Item(s) :','Underserved','PI No');        

            foreach ($profPiMerge as $partial) 
            {
                if($partial['partiallyServed']  == 1){                  
                    
                    if($getAmounting == "NETofVAT&Disc"){
                        $netAmount = $partial['profAmt'];
                    } else if($getAmounting == "GROSSofVAT&Disc"){
                        if($getDiscType == "PERCENTAGE"){
                            $netAmount = netPrice($fetch_data['supId'],$getPricing,$partial['profAmt'],$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat, $fetch_data['cusId']);
                        } else if($getDiscType == "AMOUNT"){
                            $price     = netPrice($fetch_data['supId'],$getPricing,$partial['profPrice'],$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat, $fetch_data['cusId']);
                            $netAmount = $price * $partial['profQty'];
                        }
                        
                    } else if($getAmounting == "NETofVATwDisc"){
                        $netAmount = netPrice($fetch_data['supId'],$getPricing,$partial['profAmt'],$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat, $fetch_data['cusId']);
                    } else if($getAmounting == "NETofDiscwVAT"){
                        $price     = netPrice($fetch_data['supId'],$getPricing,$partial['profPrice'],$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat, $fetch_data['cusId']);
                        $netAmount = $price * $partial['profQty'];
                    }

                    $netPrice          = netPrice($fetch_data['supId'],$getPricing,$partial['profPrice'],$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat, $fetch_data['cusId']);   
                    $netPricePi        = netPricePi($fetch_data['supId'],$partial['piPrice'],$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat);   
                    $discountedPricePi = discountedPricePi($fetch_data['supId'],$partial['piPrice'],$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6']);          
                    if($getPricing == "NETofVATwDisc"){
                        $grossPrice      = backToGross($fetch_data['supId'],$partial['profPrice'],$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat);
                    } else if($getPricing == "GROSSofVAT&Disc"){
                        $grossPrice      = backToGross($fetch_data['supId'],$partial['profPrice'],$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$partial['profPrice'],$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat);
                    } else if($getPricing == "GROSSofDiscwoVAT"){
                        $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat);
                    } else { //NETofVAT&Disc
                        $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$partial['disc1'],$partial['disc2'],$partial['disc3'],$partial['disc4'],$partial['disc5'],$partial['disc6'],$vat);
                    }

                    $newRow = $this->getHighestDataRow($active_sheet); 
                    $active_sheet->setCellValue('A'.$newRow, $partial['profCode'])
                                 ->setCellValue('B'.$newRow, $partial['profItem'])
                                 ->setCellValue('C'.$newRow, $partial['profDesc'])
                                 ->setCellValue('D'.$newRow, $partial['profUom'])
                                 ->setCellValue('E'.$newRow, $partial['profQty'])
                                 ->setCellValue('F'.$newRow, number_format($netPrice, 2))
                                 ->setCellValue('G'.$newRow, number_format($discountedPrice,2))
                                 ->setCellValue('H'.$newRow, number_format($grossPrice, 2))
                                 ->setCellValue('I'.$newRow, number_format($netAmount, 2))
                                 ->setCellValue('J'.$newRow, number_format($discountedPrice * $partial['profQty'], 2))
                                 ->setCellValue('K'.$newRow, number_format($grossPrice * $partial['profQty'], 2))                                 
                                 ->getStyle('F'.$newRow.':K'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $active_sheet->getStyle('A'.$newRow.':K'.$newRow)->applyFromArray($allBorderStyle);
                    $active_sheet->getStyle('A'.$newRow.':D'.$newRow)->applyFromArray($centerTextStyle);

                    $partiallyServedItemQty  += $partial['profQty'] ;
                    $partiallyServedTotalAmt += $netAmount ;
                    $partiallyServedGrossAmt += $grossPrice * $partial['profQty']  ; 
                    $partiallyServedDiscountedAmt += $discountedPrice * $partial['profQty'] ;

                    $active_sheet->setCellValue('M'.$newRow, $partial['piNo'])
                                 ->setCellValue('N'.$newRow, $partial['piDate'])
                                 ->setCellValue('O'.$newRow, $partial['piItem'])
                                 ->setCellValue('P'.$newRow, $partial['piDesc'])
                                 ->setCellValue('Q'.$newRow, $partial['piUom'])
                                 ->setCellValue('R'.$newRow, $partial['piQty'])
                                 ->setCellValue('S'.$newRow, number_format($partial['piPrice'], 2))
                                 ->setCellValue('T'.$newRow, number_format($discountedPricePi,2))
                                 ->setCellValue('U'.$newRow, number_format($netPricePi,2))
                                 ->setCellValue('V'.$newRow, number_format($partial['piPrice'] * $partial['piQty'], 2))
                                 ->setCellValue('W'.$newRow, number_format($discountedPricePi * $partial['piQty'],2))
                                 ->setCellValue('X'.$newRow, number_format($netPricePi * $partial['piQty'] ,2))
                                 ->setCellValue('Y'.$newRow, $partial['profQty'] -  $partial['piQty'])
                                 ->setCellValue('Z'.$newRow, number_format( ($discountedPrice * $partial['profQty']) - ($discountedPricePi * $partial['piQty']),2))
                                 ->getStyle('S'.$newRow.':Z'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $active_sheet->getStyle('M'.$newRow.':Z'.$newRow)->applyFromArray($allBorderStyle);
                    $active_sheet->getStyle('M'.$newRow.':Q'.$newRow)->applyFromArray($centerTextStyle);
 
                    $partiallyReceivedItemQty                += $partial['piQty'] ;
                    $partiallyReceivedGrossAmt               += $partial['piPrice']  * $partial['piQty'] ;
                    $partiallyReceivedDiscountedAmt          += $discountedPricePi * $partial['piQty'] ;
                    $partiallyReceivedNetAmt                 += $netPricePi * $partial['piQty'] ;
                    $partiallyServedReceivedVarianceGrossAmt += ($grossPrice * $partial['profQty']) - ($partial['piPrice'] * $partial['piQty']) ;
                    $partiallyServedReceivedVarianceDiscountedAmt += ($discountedPrice * $partial['profQty']) - ($discountedPricePi * $partial['piQty']);
                    $unServedItemQty                         += $partial['profQty'] - $partial['piQty'] ;
                    $partiallyServedReceivedItemCount ++ ;
                }
            }

            $newRow = $this->getHighestDataRow($active_sheet); 
            $active_sheet->setCellValue('A'.$newRow, 'UNDERSERVED TOTAL :')
                         ->setCellValue('E'.$newRow, $partiallyServedItemQty)
                         ->setCellValue('I'.$newRow, 'P '.number_format($partiallyServedTotalAmt,2))
                         ->setCellValue('J'.$newRow, 'P '.number_format($partiallyServedDiscountedAmt,2))
                         ->setCellValue('K'.$newRow, 'P '.number_format($partiallyServedGrossAmt,2))
                         ->getStyle('A'.$newRow.':K'.$newRow)->applyFromArray($tableFooterStyle);
            $active_sheet->getStyle('I'.$newRow.':K'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $active_sheet->setCellValue('M'.$newRow, 'PARTIALLY-RECEIVED TOTAL :')
                         ->setCellValue('R'.$newRow, $partiallyReceivedItemQty)
                         ->setCellValue('V'.$newRow, 'P '.number_format($partiallyReceivedGrossAmt,2))
                         ->setCellValue('W'.$newRow, 'P '.number_format($partiallyReceivedDiscountedAmt,2))
                         ->setCellValue('X'.$newRow, 'P '.number_format($partiallyReceivedNetAmt,2))
                         ->setCellValue('Y'.$newRow, $unServedItemQty)
                         ->setCellValue('Z'.$newRow, 'P '.number_format($partiallyServedReceivedVarianceDiscountedAmt,2))
                         ->getStyle('M'.$newRow.':Z'.$newRow)->applyFromArray($tableFooterStyle);
            $active_sheet->getStyle('V'.$newRow.':Z'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $newRow = $this->getHighestDataRow($active_sheet); 
            $active_sheet->setCellValue('A'.$newRow, 'Underserved/Partially-Received Item Count : '.$partiallyServedReceivedItemCount)
                         ->getStyle('A'.$newRow)->applyFromArray($itemCountStyle);
        }

        if ( $excess !== false  ) { /*    excess  */
            $newRow = $this->getHighestDataRow($active_sheet) + 1;   
            $this->writeTableColumnHeader($active_sheet,$newRow,$reportHeaderStyle,$tableHeaderStyle,'Over-Served Item(s) :','Over-Received Item(s) :','Overserved','PI No'); 

            foreach ($profPiMerge as $excessLine)
            {
                if ($excessLine['excess'] == 1) {
                    if($getAmounting == "NETofVAT&Disc"){
                        $netAmount = $excessLine['profAmt'];
                    } else if($getAmounting == "GROSSofVAT&Disc"){                        
                        if($getDiscType == "PERCENTAGE"){
                            $netAmount = netPrice($fetch_data['supId'],$getPricing,$excessLine['profAmt'],$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat, $fetch_data['cusId']);
                        } else if($getDiscType == "AMOUNT"){
                            $price     = netPrice($fetch_data['supId'],$getPricing,$excessLine['profPrice'],$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat, $fetch_data['cusId']);
                            $netAmount = $price * $excessLine['profQty'];
                        }
                    } else if($getAmounting == "NETofVATwDisc"){
                        $netAmount = netPrice($fetch_data['supId'],$getPricing,$excessLine['profAmt'],$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat, $fetch_data['cusId']);
                    } else if($getAmounting == "NETofDiscwVAT"){
                        $price     = netPrice($fetch_data['supId'],$getPricing,$excessLine['profPrice'],$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat, $fetch_data['cusId']);
                        $netAmount = $price * $excessLine['profQty'];
                    } 
                    $netPrice          = netPrice($fetch_data['supId'],$getPricing,$excessLine['profPrice'],$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat, $fetch_data['cusId']);
                    $netPricePi        = netPricePi($fetch_data['supId'],$excessLine['piPrice'],$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat);   
                    $discountedPricePi = discountedPricePi($fetch_data['supId'],$excessLine['piPrice'],$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6']);
                    if($getPricing == "NETofVATwDisc"){
                        $grossPrice      = backToGross($fetch_data['supId'],$excessLine['profPrice'],$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat);
                    } else if($getPricing == "GROSSofVAT&Disc"){
                        $grossPrice = backToGross($fetch_data['supId'],$excessLine['profPrice'],$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat);
                    } else if($getPricing == "GROSSofDiscwoVAT"){
                        $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat);
                    } else {
                        $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$excessLine['disc1'],$excessLine['disc2'],$excessLine['disc3'],$excessLine['disc4'],$excessLine['disc5'],$excessLine['disc6'],$vat);
                    }

                    $newRow = $this->getHighestDataRow($active_sheet); 
                    $active_sheet->setCellValue('A'.$newRow, $excessLine['profCode'])
                                 ->setCellValue('B'.$newRow, $excessLine['profItem'])
                                 ->setCellValue('C'.$newRow, $excessLine['profDesc'])
                                 ->setCellValue('D'.$newRow, $excessLine['profUom'])
                                 ->setCellValue('E'.$newRow, $excessLine['profQty'])
                                 ->setCellValue('F'.$newRow, number_format($netPrice, 2))
                                 ->setCellValue('G'.$newRow, number_format($discountedPrice,2))
                                 ->setCellValue('H'.$newRow, number_format($grossPrice, 2))
                                 ->setCellValue('I'.$newRow, number_format($netAmount, 2))
                                 ->setCellValue('J'.$newRow, number_format($discountedPrice * $excessLine['profQty'], 2))
                                 ->setCellValue('K'.$newRow, number_format($grossPrice * $excessLine['profQty'], 2))                                 
                                 ->getStyle('F'.$newRow.':K'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $active_sheet->getStyle('A'.$newRow.':K'.$newRow)->applyFromArray($allBorderStyle);
                    $active_sheet->getStyle('A'.$newRow.':D'.$newRow)->applyFromArray($centerTextStyle);

                    $overServedItemQty  += $excessLine['profQty'] ;
                    $overServedTotalAmt += $netAmount ;
                    $overServedGrossAmt += $grossPrice * $excessLine['profQty']  ; 
                    $overServedDiscountedAmt += $discountedPrice * $excessLine['profQty'] ;

                    $active_sheet->setCellValue('M'.$newRow, $excessLine['piNo'])
                                 ->setCellValue('N'.$newRow, $excessLine['piDate'])
                                 ->setCellValue('O'.$newRow, $excessLine['piItem'])
                                 ->setCellValue('P'.$newRow, $excessLine['piDesc'])
                                 ->setCellValue('Q'.$newRow, $excessLine['piUom'])
                                 ->setCellValue('R'.$newRow, $excessLine['piQty'])
                                 ->setCellValue('S'.$newRow, number_format($excessLine['piPrice'], 2))
                                 ->setCellValue('T'.$newRow, number_format($discountedPricePi,2))
                                 ->setCellValue('U'.$newRow, number_format($netPricePi,2))
                                 ->setCellValue('V'.$newRow, number_format($excessLine['piPrice'] * $excessLine['piQty'], 2))
                                 ->setCellValue('W'.$newRow, number_format($discountedPricePi * $excessLine['piQty'],2))
                                 ->setCellValue('X'.$newRow, number_format($netPricePi * $excessLine['piQty'] ,2))
                                 ->setCellValue('Y'.$newRow, $excessLine['profQty'] -  $excessLine['piQty'])
                                 ->setCellValue('Z'.$newRow, number_format( ($discountedPrice * $excessLine['profQty']) - ($discountedPricePi * $excessLine['piQty']),2))
                                 ->getStyle('S'.$newRow.':Z'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $active_sheet->getStyle('M'.$newRow.':Z'.$newRow)->applyFromArray($allBorderStyle);
                    $active_sheet->getStyle('M'.$newRow.':Q'.$newRow)->applyFromArray($centerTextStyle);

                    $overReceivedItemQty                += $excessLine['piQty'] ;
                    $overReceivedGrossAmt               += $excessLine['piPrice']   * $excessLine['piQty'] ;
                    $overReceivedDiscountedAmt          += $discountedPricePi * $excessLine['piQty'] ;
                    $overReceivedNetAmount              += $netPricePi * $excessLine['piQty'] ;
                    $overServedReceivedVarianceGrossAmt += ($grossPrice * $excessLine['profQty']) - ($excessLine['piPrice'] * $excessLine['piQty']) ;
                    $overServedReceivedVarianceDiscountedAmt += ($discountedPrice * $excessLine['profQty']) - ($discountedPricePi * $excessLine['piQty']) ;
                    $excessItemQty                      += $excessLine['profQty'] - $excessLine['piQty'] ;
                    $overReceivedItemCount ++ ;
                }
            }
            $newRow = $this->getHighestDataRow($active_sheet); 
            $active_sheet->setCellValue('A'.$newRow, 'OVER-SERVED TOTAL :')
                         ->setCellValue('E'.$newRow, $overServedItemQty)
                         ->setCellValue('I'.$newRow, 'P '.number_format($overServedTotalAmt,2))
                         ->setCellValue('J'.$newRow, 'P '.number_format($overServedDiscountedAmt,2))
                         ->setCellValue('K'.$newRow, 'P '.number_format($overServedGrossAmt,2))
                         ->getStyle('A'.$newRow.':K'.$newRow)->applyFromArray($tableFooterStyle);
            $active_sheet->getStyle('I'.$newRow.':K'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $active_sheet->setCellValue('M'.$newRow, 'OVER-RECEIVED TOTAL :')
                         ->setCellValue('R'.$newRow, $overReceivedItemQty)
                         ->setCellValue('V'.$newRow, 'P '.number_format($overReceivedGrossAmt,2))
                         ->setCellValue('W'.$newRow, 'P '.number_format($overReceivedDiscountedAmt,2))
                         ->setCellValue('X'.$newRow, 'P '.number_format($overReceivedNetAmount,2))
                         ->setCellValue('Y'.$newRow, $excessItemQty)
                         ->setCellValue('Z'.$newRow, 'P '.number_format($fullyVarianceDiscountedAmt,2))
                         ->getStyle('M'.$newRow.':Z'.$newRow)->applyFromArray($tableFooterStyle);
            $active_sheet->getStyle('V'.$newRow.':Z'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $newRow = $this->getHighestDataRow($active_sheet); 
            $active_sheet->setCellValue('A'.$newRow, 'Over-Served/Over-Received Item Count : '.$overReceivedItemCount)
                         ->getStyle('A'.$newRow)->applyFromArray($itemCountStyle);
        }

        if ( !empty($notFoundItemsFromProforma) ) { 
            $newRow = $this->getHighestDataRow($active_sheet) + 1;   
            $this->writeTableColumnHeader($active_sheet,$newRow,$reportHeaderStyle,$tableHeaderStyle,'Fully-Unserved Item(s) :','','Unserved','PI No'); 

            foreach ($notFoundItemsFromProforma as $notFound1) //fully unserved
            { 
                if($getAmounting == "NETofVAT&Disc"){
                    $netAmount = $notFound1['amount'];
                } else if($getAmounting == "GROSSofVAT&Disc"){
                    if($getDiscType == "PERCENTAGE"){
                        $netAmount = netPrice($fetch_data['supId'],$getPricing,$notFound1['amount'],$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat, $fetch_data['cusId']);
                    } else if($getDiscType == "AMOUNT"){
                        $price     = netPrice($fetch_data['supId'],$getPricing,$notFound1['price'],$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat, $fetch_data['cusId']);
                        $netAmount = $price * $notFound1['qty'];
                    }
                   
                } else if($getAmounting == "NETofVATwDisc"){
                    $netAmount = netPrice($fetch_data['supId'],$getPricing,$notFound1['amount'],$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat, $fetch_data['cusId']);
                } else if($getAmounting == "NETofDiscwVAT"){
                    $price     = netPrice($fetch_data['supId'],$getPricing,$notFound1['price'],$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat, $fetch_data['cusId']);
                    $netAmount = $price * $notFound1['qty'];
                } 

                $netPrice          = netPrice($fetch_data['supId'],$getPricing,$notFound1['price'],$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat, $fetch_data['cusId']);
                if($getPricing == "NETofVATwDisc"){
                    $grossPrice      = backToGross($fetch_data['supId'],$notFound1['price'],$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat);
                    $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat);
                } else if($getPricing == "GROSSofVAT&Disc"){
                    $grossPrice      = backToGross($fetch_data['supId'],$notFound1['price'],$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat);
                    $discountedPrice = discountedPrice($fetch_data['supId'],$notFound1['price'],$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat);
                } else if($getPricing == "GROSSofDiscwoVAT"){
                    $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat);
                    $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat);
                } else {
                    $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat);
                    $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$notFound1['disc1'],$notFound1['disc2'],$notFound1['disc3'],$notFound1['disc4'],$notFound1['disc5'],$notFound1['disc6'],$vat);
                }

                $newRow = $this->getHighestDataRow($active_sheet); 
                $active_sheet->setCellValue('A'.$newRow, $notFound1['profCode'])
                             ->setCellValue('B'.$newRow, $notFound1['item'])
                             ->setCellValue('C'.$newRow, $notFound1['idesc'])
                             ->setCellValue('D'.$newRow, $notFound1['uom'])
                             ->setCellValue('E'.$newRow, $notFound1['qty'])
                             ->setCellValue('F'.$newRow, number_format($netPrice, 2))
                             ->setCellValue('G'.$newRow, number_format($discountedPrice,2))
                             ->setCellValue('H'.$newRow, number_format($grossPrice, 2))
                             ->setCellValue('I'.$newRow, number_format($netAmount, 2))
                             ->setCellValue('J'.$newRow, number_format($discountedPrice * $notFound1['qty'], 2))
                             ->setCellValue('K'.$newRow, number_format($grossPrice * $notFound1['qty'], 2))                                 
                             ->getStyle('F'.$newRow.':K'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $active_sheet->getStyle('A'.$newRow.':K'.$newRow)->applyFromArray($allBorderStyle);
                $active_sheet->getStyle('A'.$newRow.':D'.$newRow)->applyFromArray($centerTextStyle);
                $fullyUnservedItemQty  += $notFound1['qty'] ;
                $fullyUnservedTotalAmt += $netAmount ;
                $fullyUnservedGrossAmt += $grossPrice * $notFound1['qty'] ;
                $fullyUnservedDiscountedAmt += $discountedPrice * $notFound1['qty'] ; 
                $fullyUnservedItemCount ++;

                $active_sheet->setCellValue('M'.$newRow, '')
                             ->setCellValue('N'.$newRow, '')
                             ->setCellValue('O'.$newRow, '')
                             ->setCellValue('P'.$newRow, '')
                             ->setCellValue('Q'.$newRow, '')
                             ->setCellValue('R'.$newRow, '')
                             ->setCellValue('S'.$newRow, number_format($grossPrice, 2))
                             ->setCellValue('T'.$newRow, number_format($discountedPrice,2))
                             ->setCellValue('U'.$newRow, number_format($netPrice,2))
                             ->setCellValue('V'.$newRow, number_format($grossPrice * $notFound1['qty'], 2))
                             ->setCellValue('W'.$newRow, number_format($discountedPrice * $notFound1['qty'],2))
                             ->setCellValue('X'.$newRow, number_format($netPrice * $notFound1['qty'] ,2))
                             ->setCellValue('Y'.$newRow, $notFound1['qty'] )
                             ->setCellValue('Z'.$newRow, number_format( $discountedPrice * $notFound1['qty'],2))
                             ->getStyle('S'.$newRow.':Z'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $active_sheet->getStyle('M'.$newRow.':Z'.$newRow)->applyFromArray($allBorderStyle);
                $active_sheet->getStyle('M'.$newRow.':Q'.$newRow)->applyFromArray($centerTextStyle);
                $fullyUnreceivedGrossAmt       += $grossPrice * $notFound1['qty'] ;
                $fullyUnreceivedNetAmt         += $netPrice * $notFound1['qty'];
                $fullyUnreceivedDiscountedAmt  += $discountedPrice * $notFound1['qty'] ;
                $fullyUnservedVarianceGrossAmt += $grossPrice * $notFound1['qty'] ;
                $fullyUnservedVarianceDiscountedAmt += $discountedPrice * $notFound1['qty'];
                $fullyUnservedQty              += $notFound1['qty'];
            }

            $newRow = $this->getHighestDataRow($active_sheet); 
            $active_sheet->setCellValue('A'.$newRow, 'FULLY-UNSERVED TOTAL :')
                         ->setCellValue('E'.$newRow, $fullyUnservedItemQty)
                         ->setCellValue('I'.$newRow, 'P '.number_format($fullyUnservedTotalAmt,2))
                         ->setCellValue('J'.$newRow, 'P '.number_format($fullyUnservedDiscountedAmt,2))
                         ->setCellValue('K'.$newRow, 'P '.number_format($fullyUnservedGrossAmt,2))
                         ->getStyle('A'.$newRow.':K'.$newRow)->applyFromArray($tableFooterStyle);
            $active_sheet->getStyle('I'.$newRow.':K'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $active_sheet->setCellValue('M'.$newRow, '')
                         ->setCellValue('R'.$newRow, '')
                         ->setCellValue('V'.$newRow, 'P '.number_format($fullyUnreceivedGrossAmt,2))
                         ->setCellValue('W'.$newRow, 'P '.number_format($fullyUnreceivedDiscountedAmt,2))
                         ->setCellValue('X'.$newRow, 'P '.number_format($fullyUnreceivedNetAmt,2))
                         ->setCellValue('Y'.$newRow, $fullyUnservedQty)
                         ->setCellValue('Z'.$newRow, 'P '.number_format($fullyUnservedVarianceDiscountedAmt,2))
                         ->getStyle('M'.$newRow.':Z'.$newRow)->applyFromArray($tableFooterStyle);
            $active_sheet->getStyle('V'.$newRow.':Z'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $newRow = $this->getHighestDataRow($active_sheet); 
            $active_sheet->setCellValue('A'.$newRow, 'Fully-Unserved Item Count : '.$fullyUnservedItemCount)
                         ->getStyle('A'.$newRow)->applyFromArray($itemCountStyle);
        }

        if( !empty($notFoundItemsFromPi)  ){ //un ordered items, items nga naa sa PI pro wala sa proforma
            $newRow = $this->getHighestDataRow($active_sheet) + 1;   
            $this->writeTableColumnHeader($active_sheet,$newRow,$reportHeaderStyle,$tableHeaderStyle,'','Fully-Overserved Item(s) :','Overserved','PI No'); 

            foreach($notFoundItemsFromPi as $notFound2)
            {
                $netPricePi        = netPricePi($fetch_data['supId'],$notFound2['direct'],$notFound2['disc1'],$notFound2['disc2'],$notFound2['disc3'],$notFound2['disc4'],$notFound2['disc5'],$notFound2['disc6'],$vat);
                $discountedPricePi = discountedPricePi($fetch_data['supId'],$notFound2['direct'],$notFound2['disc1'],$notFound2['disc2'],$notFound2['disc3'],$notFound2['disc4'],$notFound2['disc5'],$notFound2['disc6']);

                $newRow = $this->getHighestDataRow($active_sheet); 
                $active_sheet->setCellValue('A'.$newRow, '')
                             ->setCellValue('B'.$newRow, '')
                             ->setCellValue('C'.$newRow, '')
                             ->setCellValue('D'.$newRow, '')
                             ->setCellValue('E'.$newRow, '')
                             ->setCellValue('F'.$newRow, number_format($netPricePi, 2)) //net price
                             ->setCellValue('G'.$newRow, number_format($discountedPricePi,2)) //discounted price
                             ->setCellValue('H'.$newRow, number_format($notFound2['direct'], 2)) //gross price
                             ->setCellValue('I'.$newRow, number_format($netPricePi * $notFound2['qty'], 2)) //net amount
                             ->setCellValue('J'.$newRow, number_format($discountedPricePi * $notFound2['qty'], 2)) //discounted amount
                             ->setCellValue('K'.$newRow, number_format($notFound2['direct'] * $notFound2['qty'], 2))                                 
                             ->getStyle('F'.$newRow.':K'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $active_sheet->getStyle('A'.$newRow.':K'.$newRow)->applyFromArray($allBorderStyle);

                $fullyOverservedProfGrossAmt += $notFound2['direct'] * $notFound2['qty'];
                $fullyOverservedProfNetAmt   += $netPricePi * $notFound2['qty'] ;
                $fullyOverservedProfDiscountedAmt += $discountedPricePi * $notFound2['qty'] ;

                $active_sheet->setCellValue('M'.$newRow, $notFound2['piNo'])
                             ->setCellValue('N'.$newRow, $notFound2['piDate'])
                             ->setCellValue('O'.$newRow, $notFound2['item'])
                             ->setCellValue('P'.$newRow, $notFound2['idesc'])
                             ->setCellValue('Q'.$newRow, $notFound2['uom'])
                             ->setCellValue('R'.$newRow, $notFound2['qty'])
                             ->setCellValue('S'.$newRow, number_format($notFound2['direct'], 2)) //gross price
                             ->setCellValue('T'.$newRow, number_format($discountedPricePi,2)) //discounted price
                             ->setCellValue('U'.$newRow, number_format($netPricePi,2)) //net price
                             ->setCellValue('V'.$newRow, number_format($notFound2['direct'] * $notFound2['qty'], 2)) //gross amount
                             ->setCellValue('W'.$newRow, number_format($discountedPricePi * $notFound2['qty'],2)) //discounted amount
                             ->setCellValue('X'.$newRow, number_format($netPricePi * $notFound1['qty'] ,2)) //net amount
                             ->setCellValue('Y'.$newRow, $notFound2['qty'] * -1 ) //variance qty
                             ->setCellValue('Z'.$newRow, number_format( $discountedPricePi * $notFound2['qty'] * -1,2)) //discounted gross amount
                             ->getStyle('S'.$newRow.':Z'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $active_sheet->getStyle('M'.$newRow.':Z'.$newRow)->applyFromArray($allBorderStyle);
                $active_sheet->getStyle('M'.$newRow.':Q'.$newRow)->applyFromArray($centerTextStyle);

                $fullyOverreceivedItemQty             += $notFound2['qty']  ;
                $fullyOverservedGrossAmt              += $notFound2['direct']   * $notFound2['qty']   ;
                $fullyOverservedNetAmt                += $netPricePi * $notFound2['qty'];
                $fullyOverservedDiscountedAmt         += $discountedPricePi * $notFound2['qty'];
                $fullyOverservedItemQty               += $notFound2['qty'] * -1 ;
                $fullyOverservedVarianceGrossAmt      += $notFound2['direct'] * $notFound2['qty'] * -1 ;
                $fullyOverservedVarianceDiscountedAmt += $discountedPricePi * $notFound2['qty'] * -1;
                $fullyOverservedItemCount ++ ;
            }
            $newRow = $this->getHighestDataRow($active_sheet); 
            $active_sheet->setCellValue('A'.$newRow, '')
                         ->setCellValue('E'.$newRow, '')
                         ->setCellValue('I'.$newRow, 'P '.number_format($fullyOverservedProfNetAmt * -1,2))
                         ->setCellValue('J'.$newRow, 'P '.number_format($fullyOverservedProfDiscountedAmt * -1,2))
                         ->setCellValue('K'.$newRow, 'P '.number_format($fullyOverservedProfGrossAmt * -1,2))
                         ->getStyle('A'.$newRow.':K'.$newRow)->applyFromArray($tableFooterStyle);
            $active_sheet->getStyle('I'.$newRow.':K'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $active_sheet->setCellValue('M'.$newRow, 'FULLY-OVERSERVED TOTAL')
                         ->setCellValue('R'.$newRow, $fullyOverreceivedItemQty)
                         ->setCellValue('V'.$newRow, 'P '.number_format($fullyOverservedGrossAmt,2))
                         ->setCellValue('W'.$newRow, 'P '.number_format($fullyOverservedDiscountedAmt,2))
                         ->setCellValue('X'.$newRow, 'P '.number_format($fullyOverservedNetAmt,2))
                         ->setCellValue('Y'.$newRow, $fullyOverservedItemQty)
                         ->setCellValue('Z'.$newRow, 'P '.number_format($fullyOverservedVarianceDiscountedAmt,2))
                         ->getStyle('M'.$newRow.':Z'.$newRow)->applyFromArray($tableFooterStyle);
            $active_sheet->getStyle('V'.$newRow.':Z'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $newRow = $this->getHighestDataRow($active_sheet); 
            $active_sheet->setCellValue('M'.$newRow, 'Fully-Overserved Item Count : '.$fullyOverservedItemCount)
                         ->getStyle('M'.$newRow)->applyFromArray($itemCountStyle);
        }
        
        if( !empty($freeItems)){ //free items
            $newRow = $this->getHighestDataRow($active_sheet);  
            $this->writeTableColumnHeader($active_sheet,$newRow,$reportHeaderStyle,$tableHeaderStyle,'Free Item(s) :','','','');        
            foreach($freeItems as $free)
            { 
                $newRow = $this->getHighestDataRow($active_sheet); 
                $active_sheet->setCellValue('A'.$newRow, $free['profCode'])
                             ->setCellValue('B'.$newRow, $free['item'])
                             ->setCellValue('C'.$newRow, $free['idesc'])
                             ->setCellValue('D'.$newRow, $free['uom'])
                             ->setCellValue('E'.$newRow, $free['qty'])
                             ->setCellValue('F'.$newRow, 0)
                             ->setCellValue('G'.$newRow, 0)
                             ->setCellValue('H'.$newRow, 0)
                             ->setCellValue('I'.$newRow, 0)
                             ->setCellValue('J'.$newRow, 0)
                             ->setCellValue('K'.$newRow, 0)                                 
                             ->getStyle('F'.$newRow.':K'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $active_sheet->getStyle('A'.$newRow.':K'.$newRow)->applyFromArray($allBorderStyle);
                $active_sheet->getStyle('A'.$newRow.':D'.$newRow)->applyFromArray($centerTextStyle);

                $freeItemQty   += $free['qty'];
                $freeItemCount ++ ;

                $active_sheet->setCellValue('M'.$newRow, '')
                             ->setCellValue('N'.$newRow,'')
                             ->setCellValue('O'.$newRow, '')
                             ->setCellValue('P'.$newRow, '')
                             ->setCellValue('Q'.$newRow, '')
                             ->setCellValue('R'.$newRow, '')
                             ->setCellValue('S'.$newRow, '')
                             ->setCellValue('T'.$newRow, '')
                             ->setCellValue('U'.$newRow, '')
                             ->setCellValue('V'.$newRow, '')
                             ->setCellValue('W'.$newRow, '')
                             ->setCellValue('X'.$newRow, '')
                             ->setCellValue('Y'.$newRow, '')
                             ->setCellValue('Z'.$newRow, '')
                             ->getStyle('S'.$newRow.':Z'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $active_sheet->getStyle('M'.$newRow.':Z'.$newRow)->applyFromArray($allBorderStyle);
                $active_sheet->getStyle('M'.$newRow.':Q'.$newRow)->applyFromArray($centerTextStyle);                
            }
            $newRow = $this->getHighestDataRow($active_sheet); 
            $active_sheet->setCellValue('A'.$newRow, 'FREE ITEM(s) TOTAL :')
                         ->setCellValue('E'.$newRow, $freeItemQty)
                         ->setCellValue('I'.$newRow, '')
                         ->setCellValue('J'.$newRow, '')
                         ->setCellValue('K'.$newRow, '')
                         ->getStyle('A'.$newRow.':K'.$newRow)->applyFromArray($tableFooterStyle);
            $active_sheet->getStyle('I'.$newRow.':K'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $active_sheet->setCellValue('M'.$newRow, '')
                         ->setCellValue('R'.$newRow, '')
                         ->setCellValue('V'.$newRow, '')
                         ->setCellValue('W'.$newRow, '')
                         ->setCellValue('X'.$newRow, '')
                         ->setCellValue('Y'.$newRow, '')
                         ->setCellValue('Z'.$newRow, '')
                         ->getStyle('M'.$newRow.':Z'.$newRow)->applyFromArray($tableFooterStyle);
            $active_sheet->getStyle('V'.$newRow.':Z'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $newRow = $this->getHighestDataRow($active_sheet); 
            $active_sheet->setCellValue('A'.$newRow, 'Free Item(s) Count : '.$freeItemCount)
                         ->getStyle('A'.$newRow)->applyFromArray($itemCountStyle);
            
        }
         
        $totalProformaGrossAmt  = $fullyServedGrossAmt + $partiallyServedGrossAmt + $overServedGrossAmt + $fullyUnservedGrossAmt  ;
        $totalProformaNetAmt    = $fullyServedTotalAmt + $partiallyServedTotalAmt + $overServedTotalAmt + $fullyUnservedTotalAmt  ;
        $totalProformaDiscountedAmt = $fullyServedDiscountedAmt + $partiallyServedDiscountedAmt + $overServedDiscountedAmt + $fullyUnservedDiscountedAmt ;
        $totalProformaQty       = $freeItemQty + $fullyServedItemQty + $partiallyServedItemQty + $overServedItemQty + $fullyUnservedItemQty ;
        $totalProformaItemCount = $freeItemCount + $fullyServedReceivedItemCount + $partiallyServedReceivedItemCount + $overReceivedItemCount + $fullyUnservedItemCount   ;
        $totalPiItemCount       = $fullyServedReceivedItemCount + $partiallyServedReceivedItemCount + $overReceivedItemCount + $fullyOverservedItemCount ;        
        $totalPiQty             = $fullyReceivedItemQty + $partiallyReceivedItemQty + $overReceivedItemQty + $fullyOverreceivedItemQty ;
        $totalPiGrossAmount     = $fullyGrossAmount + $partiallyReceivedGrossAmt + $overReceivedGrossAmt  + $fullyOverservedGrossAmt ;
        $totalPiDiscountedAmt   = $fullyReceivedDiscountedAmt + $partiallyReceivedDiscountedAmt + $overReceivedDiscountedAmt + $fullyOverservedDiscountedAmt ;
        $totalPiNetAmount       = $fullyReceivedNetAmount + $partiallyReceivedNetAmt + $overReceivedNetAmount + $fullyOverservedNetAmt ;
        $totalPiQtyVariance     = $unServedItemQty + $excessItemQty + $fullyUnservedQty + $fullyOverservedItemQty ;
        $totalVarianceGrossAmt  = $fullyVarianceGrossAmnt + $partiallyServedReceivedVarianceGrossAmt + $overServedReceivedVarianceGrossAmt + $fullyUnservedVarianceGrossAmt + $fullyOverservedVarianceGrossAmt ; //apilon ug total ang wala na receive aron makuha ang total
        $totalVarianceDiscountedAmt = $fullyVarianceDiscountedAmt + $partiallyServedReceivedVarianceDiscountedAmt + $overServedReceivedVarianceDiscountedAmt + $fullyUnservedVarianceDiscountedAmt + $fullyOverservedVarianceDiscountedAmt ;

       /*   TOTAL OF PROFORMA && PURCHASE INVOICE     */
        $newRow = $this->getHighestDataRow($active_sheet) + 1;
        $active_sheet->setCellValue('A'.$newRow, 'PRO-FORMA SALES INVOICE (PSI) Total Item Count : ' . $totalProformaItemCount)
                     ->setCellValue('M'.$newRow, 'PURCHASE INVOICE (PI) Total Item Count : ' . $totalPiItemCount)
                     ->getStyle('A'.$newRow .':M'.$newRow)->applyFromArray($itemCountStyle);

        $newRow = $this->getHighestDataRow($active_sheet) ;
        $active_sheet->setCellValue('A'.$newRow, 'PRO-FORMA SALES INVOICE (PSI) TOTAL ')
                     ->setCellValue('E'.$newRow,  $totalProformaQty)
                     ->getStyle('A'.$newRow .':E'.$newRow)->applyFromArray($itemCountStyle);
        $active_sheet->setCellValue('I'.$newRow,  'P ' .number_format($totalProformaNetAmt,2))
                     ->setCellValue('J'.$newRow,  'P ' .number_format($totalProformaDiscountedAmt,2))
                     ->setCellValue('K'.$newRow,  'P ' .number_format($totalProformaGrossAmt,2)) 
                     ->getStyle('I'.$newRow .':K'.$newRow)->applyFromArray($boldRightStyle);
        $active_sheet->setCellValue('M'.$newRow,  'PURCHASE INVOICE (PI) TOTAL ')
                     ->setCellValue('R'.$newRow,  $totalPiQty)
                     ->getStyle('M'.$newRow .':R'.$newRow)->applyFromArray($itemCountStyle);
        $active_sheet->setCellValue('V'.$newRow,  'P ' . number_format($totalPiGrossAmount,2))
                     ->setCellValue('W'.$newRow,  'P ' . number_format($totalPiDiscountedAmt,2))
                     ->setCellValue('X'.$newRow,  'P ' . number_format($totalPiNetAmount,2))
                     ->setCellValue('Y'.$newRow,  $totalPiQtyVariance)
                     ->setCellValue('Z'.$newRow,  'P ' . number_format($totalVarianceDiscountedAmt,2))
                     ->getStyle('V'.$newRow .':Z'.$newRow)->applyFromArray($boldRightStyle);
       /*   TOTAL OF PROFORMA && PURCHASE INVOICE     */
        
       /*   CREDIT MEMO     */
       if( !empty($cmLineMerge) && !empty($cmHead) ){
            $newRow = $this->getHighestDataRow($active_sheet) + 1; 
            $this->writeTableColumnHeader($active_sheet,$newRow,$reportHeaderStyle,$tableHeaderStyle,'','CREDIT MEMO','Variance','CM No'); 

            foreach($cmLineMerge as $cmd)
            {
                $netPricePi        = netPricePi($fetch_data['supId'],$cmd['price'],$cmd['disc1'],$cmd['disc2'],$cmd['disc3'],$vat);
                $discountedPricePi = discountedPricePi($fetch_data['supId'],$cmd['price'],$cmd['disc1'],$cmd['disc2'],$cmd['disc3']);

                $newRow = $this->getHighestDataRow($active_sheet); 
                $active_sheet->setCellValue('A'.$newRow, '')
                             ->setCellValue('B'.$newRow, '')
                             ->setCellValue('C'.$newRow, '')
                             ->setCellValue('D'.$newRow, '')
                             ->setCellValue('E'.$newRow, '')
                             ->setCellValue('F'.$newRow, '') //net price
                             ->setCellValue('G'.$newRow, '') //discounted price
                             ->setCellValue('H'.$newRow, '') //gross price
                             ->setCellValue('I'.$newRow, '') //net amount
                             ->setCellValue('J'.$newRow, '') //discounted amount
                             ->setCellValue('K'.$newRow, '')   
                             ->getStyle('A'.$newRow.':K'.$newRow)->applyFromArray($allBorderStyle);
                $active_sheet->setCellValue('M'.$newRow, $cmd['cmNo'])
                             ->setCellValue('N'.$newRow, $cmd['date'])
                             ->setCellValue('O'.$newRow, $cmd['item'])
                             ->setCellValue('P'.$newRow, $cmd['desc'])
                             ->setCellValue('Q'.$newRow, $cmd['uom'])
                             ->setCellValue('R'.$newRow, $cmd['qty'])
                             ->setCellValue('S'.$newRow, number_format($cmd['price'], 2))
                             ->setCellValue('T'.$newRow, number_format($discountedPricePi,2))
                             ->setCellValue('U'.$newRow, number_format($netPricePi,2))
                             ->setCellValue('V'.$newRow, number_format($cmd['price'] * $cmd['qty'], 2))
                             ->setCellValue('W'.$newRow, number_format($discountedPricePi * $cmd['qty'],2))
                             ->setCellValue('X'.$newRow, number_format($netPricePi * $cmd['qty'],2))
                             ->setCellValue('Y'.$newRow, '0')
                             ->setCellValue('Z'.$newRow, number_format( $discountedPricePi * $cmd['qty'] * -1 , 2))
                             ->getStyle('S'.$newRow.':Z'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $active_sheet->getStyle('M'.$newRow.':Z'.$newRow)->applyFromArray($allBorderStyle);

                $cmItemCount          ++ ;
                $cmItemQty            += $cmd['qty'] * -1 ;
                $cmVarianceQty        += 0;//$cmd['qty'] * -1;
                $cmGrossAmount        += $cmd['price'] * $cmd['qty'] * -1;
                $cmDiscountedAmt      += $discountedPricePi * $cmd['qty'] * -1;
                $cmNetAmount          += $netPricePi * $cmd['qty'] * -1;
                $cmVarianceDiscAmount += $discountedPricePi * $cmd['qty'] * -1;
            }
            $newRow = $this->getHighestDataRow($active_sheet); 
            $active_sheet->setCellValue('A'.$newRow, '')
                         ->setCellValue('E'.$newRow, '')
                         ->setCellValue('I'.$newRow, '')
                         ->setCellValue('J'.$newRow, '')
                         ->setCellValue('K'.$newRow, '')
                         ->getStyle('A'.$newRow.':K'.$newRow)->applyFromArray($tableFooterStyle);

            $active_sheet->setCellValue('M'.$newRow, 'CREDIT MEMO TOTAL')
                         ->setCellValue('R'.$newRow, $cmItemQty)
                         ->setCellValue('V'.$newRow, 'P '.number_format($cmGrossAmount,2))
                         ->setCellValue('W'.$newRow, 'P '.number_format($cmDiscountedAmt,2))
                         ->setCellValue('X'.$newRow, 'P '.number_format($cmNetAmount,2))
                         ->setCellValue('Y'.$newRow, $cmVarianceQty)
                         ->setCellValue('Z'.$newRow, 'P '.number_format($cmVarianceDiscAmount,2))
                         ->getStyle('M'.$newRow.':Z'.$newRow)->applyFromArray($tableFooterStyle);
            $active_sheet->getStyle('V'.$newRow.':Z'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $newRow = $this->getHighestDataRow($active_sheet); 
            $active_sheet->setCellValue('M'.$newRow, 'Credit Memo Item Count : '.$cmItemCount)
                         ->getStyle('M'.$newRow)->applyFromArray($itemCountStyle);
       }
        /*   CREDIT MEMO     */

        $newRow = $this->getHighestDataRow($active_sheet) + 1;
        $active_sheet->setCellValue('M'.$newRow, 'PURCHASE INVOICE (PI) TOTAL  (Net of Credit Memo) : ')
                     ->setCellValue('R'.$newRow,  $totalPiQty + $cmItemQty)
                     ->getStyle('M'.$newRow .':R'.$newRow)->applyFromArray($itemCountStyle);
        $active_sheet->setCellValue('V'.$newRow,  'P ' . number_format($totalPiGrossAmount + $cmGrossAmount ,2))
                     ->setCellValue('W'.$newRow,  'P ' . number_format($totalPiDiscountedAmt + $cmDiscountedAmt,2))
                     ->setCellValue('X'.$newRow,  'P ' . number_format($totalPiNetAmount + $cmNetAmount,2))
                     ->setCellValue('Y'.$newRow,  $totalPiQtyVariance + $cmVarianceQty)
                     ->setCellValue('Z'.$newRow,  'P ' . number_format($totalVarianceDiscountedAmt + $cmVarianceDiscAmount,2))
                     ->getStyle('V'.$newRow .':Z'.$newRow)->applyFromArray($boldRightStyle);

        /* PROFORMA ADDITIONAL & DEDUCTION  */
        if( !empty($discountVat) && !empty($discVatSummary)){
            $newRow = $this->getHighestDataRow($active_sheet) + 1;
            $active_sheet->setCellValue('A'.$newRow, 'Pro-forma')
                         ->setCellValue('B'.$newRow, 'Delivery Date')
                         ->setCellValue('C'.$newRow, 'SO No')
                         ->setCellValue('D'.$newRow, 'Location')
                         ->setCellValue('E'.$newRow, 'PO No')
                         ->setCellValue('F'.$newRow, "Add'l & Deduction")
                         ->setCellValue('G'.$newRow, 'Amount')
                         ->getStyle("A".$newRow.":G".$newRow)->applyFromArray($tableHeaderStyle);

            $previousProf = "";
            foreach($getProfHead as $head)
            {
                $height  = $head['numberOfDiscount'] - 1 ;
                $newRow  = $this->getHighestDataRow($active_sheet) ;
                            
                $height1 = $height + $newRow ;
                $active_sheet->mergeCells('A'.$newRow.':A'.$height1)
                             ->mergeCells('B'.$newRow.':B'.$height1)
                             ->mergeCells('C'.$newRow.':C'.$height1)
                             ->mergeCells('D'.$newRow.':D'.$height1)
                             ->mergeCells('E'.$newRow.':E'.$height1);

                foreach($discountVat as $dv)
                {     
                    if($head['profId'] == $dv['profId']){   
                        if( $dv['profId'] != $previousProf){
                            $active_sheet->setCellValue('A'.$newRow,$head['profCode'])
                                         ->setCellValue('B'.$newRow,date("Y-m-d", strtotime($dv['delivery'])) ) 
                                         ->setCellValue('C'.$newRow,$dv['sono'])
                                         ->setCellValue('D'.$newRow,$dv['customer'])
                                         ->setCellValue('E'.$newRow,$dv['pono'])
                                         ->setCellValue('F'.$newRow,$dv['discName'])
                                         ->setCellValue('G'.$newRow,number_format($dv['amount'], 2))
                                         ->getStyle('A'.$newRow.':G'.$newRow)->applyFromArray($allBorderStyle);
                            $active_sheet->getStyle('A'.$newRow.':E'.$newRow)->applyFromArray($centerTextStyle);
                            $active_sheet->getStyle('G'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        } else if( $dv['profId'] == $previousProf) {
                            $newRow    = $this->getHighestDataRow($active_sheet);
                            $active_sheet->setCellValue('A'.$newRow,'')
                                         ->setCellValue('B'.$newRow,'' ) 
                                         ->setCellValue('C'.$newRow,'')
                                         ->setCellValue('D'.$newRow,'')
                                         ->setCellValue('E'.$newRow,'')
                                         ->setCellValue('F'.$newRow,$dv['discName'])
                                         ->setCellValue('G'.$newRow,number_format($dv['amount'], 2))
                                         ->getStyle('A'.$newRow.':G'.$newRow)->applyFromArray($allBorderStyle);
                            $active_sheet->getStyle('G'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        }
                    }
                    $previousProf = $dv['profId'] ;                        
                }                
            }
            
            foreach($discVatSummary as $sum)
            {
                $newRow = $this->getHighestDataRow($active_sheet); 
                $active_sheet->setCellValue('A'.$newRow, $sum['name'])
                             ->setCellValue('G'.$newRow, 'P '.number_format($sum['amount'],2))
                             ->getStyle('A'.$newRow.':G'.$newRow)->applyFromArray($tableFooterStyle);
                $active_sheet->getStyle('G'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $proformaAddLessTotal += $sum['amount'];
            } 
            $newRow = $this->getHighestDataRow($active_sheet); 
            $active_sheet->setCellValue('A'.$newRow, "TOTAL PSI - Add'l & Deduction :")
                         ->setCellValue('G'.$newRow, 'P '.number_format($proformaAddLessTotal,2))
                         ->getStyle('A'.$newRow.':G'.$newRow)->applyFromArray($tableFooterStyle);
            $active_sheet->getStyle('G'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        }    
        /* PROFORMA ADDITIONAL & DEDUCTION  */

        /* SOP DEDUCTION  */
        if(!empty($sopDeduction))
        {         
            $countSOP = count($sopDeduction);   
            $newRow = $this->getHighestDataRow($active_sheet) + 1;
            $active_sheet->setCellValue('A'.$newRow, 'SOP No')
                         ->setCellValue('B'.$newRow, 'Date')
                         ->setCellValue('C'.$newRow, 'Deduction')
                         ->setCellValue('D'.$newRow, 'Amount')
                         ->getStyle("A".$newRow.":D".$newRow)->applyFromArray($tableHeaderStyle);

            $height  = $countSOP - 1 ;
            $newRow  = $this->getHighestDataRow($active_sheet) ;                            
            $height1 = $height + $newRow ;
            $active_sheet->mergeCells('A'.$newRow.':A'.$height1)
                         ->mergeCells('B'.$newRow.':B'.$height1);
            
            $i = 0;
            foreach($sopDeduction as $sopRow)
            {
                if($i == 0){
                    $active_sheet->setCellValue('A'.$newRow, $sopRow['sop_no'])
                                 ->setCellValue('B'.$newRow, date("Y-m-d",strtotime($sopRow['datetime_created'])))
                                 ->setCellValue('C'.$newRow, $sopRow['description'])
                                 ->setCellValue('D'.$newRow, number_format($sopRow['deduction_amount'] ,2))
                                 ->getStyle('A'.$newRow.':D'.$newRow)->applyFromArray($allBorderStyle);
                    $active_sheet->getStyle('A'.$newRow.':C'.$newRow)->applyFromArray($centerTextStyle);
                    $active_sheet->getStyle('D'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                } else {
                    $newRow  = $this->getHighestDataRow($active_sheet) ;   
                    $active_sheet->setCellValue('A'.$newRow, '')
                                 ->setCellValue('B'.$newRow, '')
                                 ->setCellValue('C'.$newRow, $sopRow['description'])
                                 ->setCellValue('D'.$newRow, number_format($sopRow['deduction_amount'] ,2))
                                 ->getStyle('A'.$newRow.':D'.$newRow)->applyFromArray($allBorderStyle);
                    $active_sheet->getStyle('A'.$newRow.':C'.$newRow)->applyFromArray($centerTextStyle);
                    $active_sheet->getStyle('D'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                }
                $sopTotalDeductionW12 += $sopRow['deduction_amount'] ;
                $i ++;
            }          
            $newRow = $this->getHighestDataRow($active_sheet); 
            $active_sheet->setCellValue('A'.$newRow, 'TOTAL SOP Deduction : ')
                         ->setCellValue('D'.$newRow, 'P '.number_format($sopTotalDeductionW12 * -1,2))
                         ->getStyle('A'.$newRow.':D'.$newRow)->applyFromArray($tableFooterStyle);
            $active_sheet->getStyle('D'.$newRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        }
        /* SOP DEDUCTION  */

        /*   GET VARIANCE     */
        $newRow = $this->getHighestDataRow($active_sheet) + 1;
        $active_sheet->setCellValue('A'.$newRow, 'TOTAL PRO-FORMA SALES INVOICE (PSI) (Gross Amount) :')
                     ->getStyle('A'.$newRow)->applyFromArray($itemCountStyle);
        $active_sheet->setCellValue('D'.$newRow, 'P '.number_format($totalProformaGrossAmt ,2))
                     ->getStyle('D'.$newRow)->applyFromArray($boldRightStyle);
        $newRow = $this->getHighestDataRow($active_sheet) ;
        $active_sheet->setCellValue('A'.$newRow, 'Less: TOTAL SOP Deduction')
                     ->getStyle('A'.$newRow)->applyFromArray($boldRightStyle);
        $active_sheet->setCellValue('D'.$newRow, 'P '.number_format($sopTotalDeductionW12 ,2))
                     ->getStyle('D'.$newRow)->applyFromArray($boldRightUnderLinedStyle);
        $newRow = $this->getHighestDataRow($active_sheet) ;
        $active_sheet->setCellValue('A'.$newRow, 'TOTAL CRF/CV Amount :')
                     ->getStyle('A'.$newRow)->applyFromArray($itemCountStyle);
        $active_sheet->setCellValue('D'.$newRow, 'P '.number_format($totalProformaGrossAmt + $sopTotalDeductionW12 ,2))
                     ->getStyle('D'.$newRow)->applyFromArray($boldRightStyle);

        $newRow = $this->getHighestDataRow($active_sheet) + 1;
        $active_sheet->setCellValue('A'.$newRow, 'TOTAL PURCHASE INVOICE (PI) (Gross Amount) :')
                     ->getStyle('A'.$newRow)->applyFromArray($itemCountStyle);
        $active_sheet->setCellValue('D'.$newRow, 'P '.number_format($totalPiGrossAmount + $cmGrossAmount ,2))
                     ->getStyle('D'.$newRow)->applyFromArray($boldRightStyle);
        $newRow = $this->getHighestDataRow($active_sheet) ;
        $active_sheet->setCellValue('A'.$newRow, 'Less: TOTAL SOP Deduction')
                     ->getStyle('A'.$newRow)->applyFromArray($boldRightStyle);
        $active_sheet->setCellValue('D'.$newRow, 'P '.number_format($sopTotalDeductionW12 ,2))
                     ->getStyle('D'.$newRow)->applyFromArray($boldRightUnderLinedStyle);
        $newRow = $this->getHighestDataRow($active_sheet) ;
        $active_sheet->setCellValue('A'.$newRow, 'NET PURCHASE INVOICE (PI) Amount :')
                     ->getStyle('A'.$newRow)->applyFromArray($itemCountStyle);
        $active_sheet->setCellValue('D'.$newRow, 'P '.number_format($totalPiGrossAmount + $cmGrossAmount + $sopTotalDeductionW12 ,2))
                     ->getStyle('D'.$newRow)->applyFromArray($boldRightStyle);

        $newRow = $this->getHighestDataRow($active_sheet) + 1;
        $active_sheet->setCellValue('A'.$newRow, 'TOTAL CRF/CV Amount :')
                     ->getStyle('A'.$newRow)->applyFromArray($itemCountStyle);
        $active_sheet->setCellValue('D'.$newRow, 'P '.number_format($totalProformaGrossAmt + $sopTotalDeductionW12 ,2))
                     ->getStyle('D'.$newRow)->applyFromArray($boldRightStyle);
        $newRow = $this->getHighestDataRow($active_sheet) ;
        $active_sheet->setCellValue('A'.$newRow, 'NET PURCHASE INVOICE (PI) Amount :')
                     ->getStyle('A'.$newRow)->applyFromArray($itemCountStyle);
        $active_sheet->setCellValue('D'.$newRow, 'P '.number_format( ($totalPiGrossAmount + $cmGrossAmount + $sopTotalDeductionW12) * -1 ,2))
                     ->getStyle('D'.$newRow)->applyFromArray($boldRightUnderLinedStyle);
        $newRow = $this->getHighestDataRow($active_sheet) ;
        $active_sheet->setCellValue('A'.$newRow, 'Variance (Total) :')
                     ->getStyle('A'.$newRow)->applyFromArray($itemCountStyle);
        $active_sheet->setCellValue('D'.$newRow, 'P '.number_format(($totalProformaGrossAmt + $sopTotalDeductionW12) + (($totalPiGrossAmount + $cmGrossAmount + $sopTotalDeductionW12) * -1) ,2))
                     ->getStyle('D'.$newRow)->applyFromArray($boldRightStyle);

        $newRow = $this->getHighestDataRow($active_sheet) + 1;
        $active_sheet->setCellValue('A'.$newRow, 'Variance (Total) :')
                     ->getStyle('A'.$newRow)->applyFromArray($itemCountStyle);
        $active_sheet->setCellValue('D'.$newRow, 'P '.number_format( ($totalProformaGrossAmt + $sopTotalDeductionW12) + (($totalPiGrossAmount + $cmGrossAmount + $sopTotalDeductionW12) * -1) ,2))
                     ->getStyle('D'.$newRow)->applyFromArray($boldRightStyle);
        $newRow = $this->getHighestDataRow($active_sheet) ;
        $active_sheet->setCellValue('A'.$newRow, 'Variance (Total) :')
                     ->getStyle('A'.$newRow)->applyFromArray($itemCountStyle);
        $active_sheet->setCellValue('D'.$newRow, 'P '.number_format( $totalVarianceGrossAmt * -1 ,2))
                     ->getStyle('D'.$newRow)->applyFromArray($boldRightUnderLinedStyle);
        $newRow = $this->getHighestDataRow($active_sheet) ;
        $active_sheet->setCellValue('D'.$newRow, 'P '.number_format( ($totalProformaGrossAmt + $sopTotalDeductionW12) + (($totalPiGrossAmount + $cmGrossAmount + $sopTotalDeductionW12) * -1) +  ($totalVarianceGrossAmt * -1) ,2))
                     ->getStyle('D'.$newRow)->applyFromArray($boldRightStyle);
        
        $newRow = $this->getHighestDataRow($active_sheet) + 1;
        $active_sheet->setCellValue('A'.$newRow, 'TOTAL PRO-FORMA SALES INVOICE (PSI) (Discounted Amount) :')
                    ->getStyle('A'.$newRow)->applyFromArray($itemCountStyle);
        $active_sheet->setCellValue('D'.$newRow, 'P '.number_format( $totalProformaDiscountedAmt ,2))
                    ->getStyle('D'.$newRow)->applyFromArray($boldRightStyle);
        $newRow = $this->getHighestDataRow($active_sheet) ;
        $active_sheet->setCellValue('A'.$newRow, 'TOTAL PURCHASE INVOICE (PI) (Discounted Amount) :')
                    ->getStyle('A'.$newRow)->applyFromArray($itemCountStyle);
        $active_sheet->setCellValue('D'.$newRow, 'P '.number_format( ($totalPiDiscountedAmt + $cmDiscountedAmt) * -1 ,2))
                    ->getStyle('D'.$newRow)->applyFromArray($boldRightUnderLinedStyle);
        $newRow = $this->getHighestDataRow($active_sheet) ;
        $active_sheet->setCellValue('A'.$newRow, 'Variance (Discounted Amount) :')
                     ->getStyle('A'.$newRow)->applyFromArray($itemCountStyle);
        $active_sheet->setCellValue('D'.$newRow, 'P '.number_format( $totalProformaDiscountedAmt + ($totalPiDiscountedAmt + $cmDiscountedAmt) * -1  ,2))
                    ->getStyle('D'.$newRow)->applyFromArray($boldRightStyle);
        //
        $newRow = $this->getHighestDataRow($active_sheet) + 1;
        $active_sheet->setCellValue('A'.$newRow, 'TOTAL PRO-FORMA SALES INVOICE (PSI) (Net Amount) :')
                    ->getStyle('A'.$newRow)->applyFromArray($itemCountStyle);
        $active_sheet->setCellValue('D'.$newRow, 'P '.number_format( $totalProformaNetAmt ,2))
                    ->getStyle('D'.$newRow)->applyFromArray($boldRightStyle);
        $newRow = $this->getHighestDataRow($active_sheet) ;
        $active_sheet->setCellValue('A'.$newRow, 'TOTAL PURCHASE INVOICE (PI) (Net Amount) :')
                    ->getStyle('A'.$newRow)->applyFromArray($itemCountStyle);
        $active_sheet->setCellValue('D'.$newRow, 'P '.number_format( ($totalPiNetAmount + $cmNetAmount) * -1 ,2))
                    ->getStyle('D'.$newRow)->applyFromArray($boldRightUnderLinedStyle);
        $newRow = $this->getHighestDataRow($active_sheet) ;
        $active_sheet->setCellValue('A'.$newRow, 'Variance (Net Amount) :')
                     ->getStyle('A'.$newRow)->applyFromArray($itemCountStyle);
        $active_sheet->setCellValue('D'.$newRow, 'P '.number_format( $totalProformaNetAmt + ($totalPiNetAmount + $cmNetAmount) * -1 ,2))
                    ->getStyle('D'.$newRow)->applyFromArray($boldRightStyle);
        /*   GET VARIANCE     */

                     
        /********************* Autoresize column width depending upon contents **********************/
        foreach(range('A', 'Z') as $columnID) {
            $active_sheet->getColumnDimension($columnID)->setAutoSize(TRUE);
        }
        /********************* Autoresize column width depending upon contents **********************/
        $supAcroname = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['acroname'];
        $cusAcroname = $this->proformavspi_model->getCustomerData($fetch_data['cusId'],'customer_code')['l_acroname'];

        $lastRow   = $active_sheet->getHighestDataRow();
        $sheetName = $supAcroname.'-'. $cusAcroname.' - PSIvsPI';
        $filename  = $sheetName.time().'.xls';        
        $active_sheet->setTitle($sheetName); //give title to sheet
        /********************* SET PASSWORD AND DISABLE FORMATTING IN THE WHOLE SHEET **********************/
        $active_sheet->getProtection()->setPassword($supAcroname.'1'.$lastRow.$cusAcroname)//supplier acroname, 1st row, last row with data, customer acroname
                                      ->setFormatRows(TRUE)                                      
                                      ->setFormatCells(TRUE)
                                      ->setObjects(TRUE)
                                      ->setSort(TRUE)
                                      ->setAutofilter(TRUE)
                                      ->setInsertColumns(TRUE)
                                      ->setInsertRows(TRUE)
                                      ->setDeleteColumns(TRUE)
                                      ->setDeleteRows(TRUE)
                                      ->setSheet(TRUE);
        /********************* SET PASSWORD AND DISABLE FORMATTING IN THE WHOLE SHEET **********************/
        $active_sheet->setSelectedCell('Z1')
                     ->setShowGridlines(FALSE);
        $objPHPExcel->setActiveSheetIndex(0);//set as the active sheet
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;Filename=$filename");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save(str_replace(__FILE__,'files/Reports/ProformaVsPi/'.$filename,__FILE__));
        
         // for history
         $this->db->set('filename', $filename)
                  ->where('tr_id', $transactionId)
                  ->update('profvpi_transaction');
        // for history
        return $filename;
    
    }

    private function writeTableColumnHeader($active_sheet,$newRow,$reportHeaderStyle,$tableHeaderStyle,$one,$two,$three,$four)
    {
        $active_sheet->setCellValue('A'.$newRow, $one)
                     ->setCellValue('M'.$newRow, $two)
                     ->getStyle("A".$newRow.":M".$newRow)->applyFromArray($reportHeaderStyle);

        $newRow = $this->getHighestDataRow($active_sheet);

        //Proforma
        $active_sheet->setCellValue('A'.$newRow, 'Pro-forma')
                     ->setCellValue('B'.$newRow, 'Item')
                     ->setCellValue('C'.$newRow, 'Description')
                     ->setCellValue('D'.$newRow, 'UOM')
                     ->setCellValue('E'.$newRow, 'Qty')
                     ->setCellValue('F'.$newRow, 'Net Price')
                     ->setCellValue('G'.$newRow, 'Discounted Price')
                     ->setCellValue('H'.$newRow, 'Gross Price')
                     ->setCellValue('I'.$newRow, 'Net Amount')
                     ->setCellValue('J'.$newRow, 'Discounted Amount')
                     ->setCellValue('K'.$newRow, 'Gross Amount')
                     ->getStyle("A".$newRow.":K".$newRow)->applyFromArray($tableHeaderStyle);
        //PI
        $active_sheet->setCellValue('M'.$newRow, $four)
                     ->setCellValue('N'.$newRow, 'Date')
                     ->setCellValue('O'.$newRow, 'Item')
                     ->setCellValue('P'.$newRow, 'Description')
                     ->setCellValue('Q'.$newRow, 'UOM')
                     ->setCellValue('R'.$newRow, 'Qty')
                     ->setCellValue('S'.$newRow, 'Unit Price')
                     ->setCellValue('T'.$newRow, 'Discounted Price')
                     ->setCellValue('U'.$newRow, 'Net Price')
                     ->setCellValue('V'.$newRow, 'Gross Amount')
                     ->setCellValue('W'.$newRow, 'Discounted Amount')
                     ->setCellValue('X'.$newRow, 'Net Amount')
                     ->setCellValue('Y'.$newRow, $three)
                     ->setCellValue('Z'.$newRow, 'Variance')
                     ->getStyle("M".$newRow.":Z".$newRow)->applyFromArray($tableHeaderStyle);
        $active_sheet->getRowDimension($newRow)->setRowHeight(15);

        $newRow = $this->getHighestDataRow($active_sheet);
        $active_sheet->setCellValue('A'.$newRow, '')
                     ->setCellValue('B'.$newRow, '')
                     ->setCellValue('C'.$newRow, '')
                     ->setCellValue('D'.$newRow, '')
                     ->setCellValue('E'.$newRow, '')
                     ->setCellValue('F'.$newRow, '(Net of VAT & Disct.)')
                     ->setCellValue('G'.$newRow, '(Net of Disct. incl. VAT)')
                     ->setCellValue('H'.$newRow, '(Gross of VAT & Disct.)')
                     ->setCellValue('I'.$newRow, '')
                     ->setCellValue('J'.$newRow, '')
                     ->setCellValue('K'.$newRow, '')
                     ->getStyle("A".$newRow.":K".$newRow)->applyFromArray($tableHeaderStyle);
        $active_sheet->setCellValue('M'.$newRow, '')
                     ->setCellValue('N'.$newRow, '')
                     ->setCellValue('O'.$newRow, '')
                     ->setCellValue('P'.$newRow, '')
                     ->setCellValue('Q'.$newRow, '')
                     ->setCellValue('R'.$newRow, '')
                     ->setCellValue('S'.$newRow, '(Gross)')
                     ->setCellValue('T'.$newRow, '(Net of Disct. incl. VAT)')
                     ->setCellValue('U'.$newRow, '(Net of VAT & Disct.)')
                     ->setCellValue('V'.$newRow, '')
                     ->setCellValue('W'.$newRow, '')
                     ->setCellValue('X'.$newRow, '')
                     ->setCellValue('Y'.$newRow, '(Qty)')
                     ->setCellValue('Z'.$newRow, '(Discounted Amount)')
                     ->getStyle("M".$newRow.":Z".$newRow)->applyFromArray($tableHeaderStyle);
        $active_sheet->getRowDimension($newRow)->setRowHeight(15);
    }

    private function getHighestDataRow($active_sheet)
    {
        return $active_sheet->getHighestDataRow() + 1;
    }
    
    public function getSuppliersForPI()
    {
        $result = $this->proformavspi_model->getSuppliers();
        return JSONResponse($result);
    }

    public function getCustomersForPI()
    {
        $result = $this->proformavspi_model->getCustomers();
        return JSONResponse($result);
    }
    
    public function uploadPi()
    {
        $fetch_data   = $this->input->post(NULL, TRUE);
        $supId        = $fetch_data['selectSupplier'];
        $cusId        = $fetch_data['selectCustomer'];
        $piLocation   = $this->proformavspi_model->getPiNameLocation($fetch_data['selectCustomer']);
        $itemNotFound = array();
        $msg          = array();
        $invalidExt   = array();
        $insertedPi   = 0 ;
        $piExist      = 0 ;
        $poDontExist  = 0 ;
        $invalidSupp  = 0 ;
        $isHeader     = FALSE;
        $itemsLine    = 0;

       
        $this->db->trans_start();

        for ($i = 0; $i < count($_FILES['piFile']['name']); $i++) 
        {
            
            
            $getExt          =  pathinfo($_FILES['piFile']['name'][$i], PATHINFO_EXTENSION);  
            $getFileName     =  substr(pathinfo($_FILES['piFile']['name'][$i], PATHINFO_FILENAME),0,3); 
            $checkFileName   =  array_search($getFileName, array_column($piLocation, 'pi_docno'));    

            if( $getExt == 'txt' || $getExt == 'TXT' ){
                echo '';
            } else {
                $invalidExt[] = $getExt;
            }   
            
            $piContent = file_get_contents($_FILES['piFile']['tmp_name'][$i]) ;
            $line      = explode("\n", $piContent);
            $totalLine = count($line);

            for ($n = 0; $n < $totalLine; $n++) 
            {                
                if ( $line[$n] !== NULL || $line[$n] !== "" ) {
                    $blits = str_replace('"', "", $line[$n]);                  
                   
                    $refined = explode("|", $blits);
                    $countRefined = count($refined);

                    if( $countRefined == 7 && $n == 0 ){ //header line
                        $isHeader = TRUE;
                    }

                    if ($countRefined == 20 && $n == 2) { //items line
                        $itemsLine ++ ;
                        $checkItem = $this->proformavspi_model->checkItem('itemcode_loc',trim($refined[2]));
                        if(!$checkItem)
                        {
                            $itemNotFound[$n] = trim($refined[2]);                            
                        } 
                    }
                }
            }
        }
        
        if( empty($invalidExt) && empty($itemNotFound) && $checkFileName !== FALSE && $isHeader && $itemsLine > 0 ){
            for ($c = 0; $c < count($_FILES['piFile']['name']); $c++) 
            {   
                $fileName  = $_FILES['piFile']['name'][$c];
                $Temp      = $_FILES['piFile']['tmp_name'][$c];
                $piContent = file_get_contents($_FILES['piFile']['tmp_name'][$c]) ;
                $line      = explode("\n", $piContent);
                $totalLine = count($line);

                for ($l = 0; $l < $totalLine; $l++) 
                {
                    if ($line[$l] != NULL) {
                        $blits = str_replace('"', "", $line[$l]);
                        $refined = explode("|", $blits);
                        $countRefined = count($refined);

                        if ($countRefined == 7) { // header
                            $getContentSupId = $this->proformavspi_model->getSupplierData(trim($refined[6]), 'supplier_code')['supplier_id']; 
                            if ($supId == $getContentSupId) {//validate if same ang sa selected supplier sa supplier nga  naa sa texfile
                                $poId = $this->proformavspi_model->getPoData(trim($refined[5]), 'po_header_id', 'po_no');
                                if ($poId) {
                                    $piNo               = trim($refined[0]);
                                    $vendorInvoiceNo    = trim($refined[1]);
                                    $postingDate        = date("Y-m-d", strtotime(trim($refined[2])));
                                    $amtIncVat          = str_replace(',','',trim($refined[3]));   
    
                                    $piId = $this->proformavspi_model->uploadHeader($piNo, $vendorInvoiceNo, $postingDate, $amtIncVat, $poId->po_header_id, $supId, $cusId);
                                    if(!$piId){
                                        $piExist ++ ;
                                        break;
                                    } else {
                                        $saveDocument = ['document_name'  => $fileName,
                                                         'document_type'  => 'Purchase Invoice',
                                                         'document_path'  => "files/PROFORMAVSPI/".$fileName,
                                                         'uploaded_on'    => date("Y-m-d H:i:s"),
                                                         'supplier_id'    => $fetch_data['selectSupplier'],
                                                         'customer_code'  => $fetch_data['selectCustomer'],
                                                         'user_id'        => $this->session->userdata('user_id')] ;                                                    
                                        $this->db->insert('uploaded_transaction_documents', $saveDocument);
                                        move_uploaded_file($Temp,"files/PROFORMAVSPI/".$fileName);
                                    }
                                   
                                } else {                                   
                                    $poDontExist ++ ;
                                    break;
                                }
                            } else {
                                
                                $invalidSupp ++ ;
                                break;
                            }
                        }// header

                        if ($countRefined == 20 && $refined[6] != 0 ) { // line and qty != 0
                            $piNo               = trim($refined[0]);
                            $itemCode           = trim($refined[2]);
                            $desc               = trim($refined[3]);
                            $uom                = trim($refined[5]);
                            $qty                = str_replace(',', '', trim($refined[6]));
                            $directUnitCost     = str_replace(',', '', trim($refined[7]));
                            $unitCostLcy        = str_replace(',', '', trim($refined[8]));
                            $amt                = str_replace(',', '', trim($refined[9]));
                            $amtIncVat          = str_replace(',', '', trim($refined[10]));
                            $unitCost           = str_replace(',', '', trim($refined[11]));
                            $lineAmt            = str_replace(',', '', trim($refined[12]));
                            $qtyPerUom          = trim($refined[13]);
                            $uomCode            = trim($refined[14]);
                            $lineDiscPercent    = trim($refined[18]) ; //additional
                            $lineDiscAmount     = str_replace(',','', trim($refined[19])) ;
                            $getPiId            = $this->proformavspi_model->getPiHeadData($piNo,$supId)['pi_head_id'];                                 
                            $this->proformavspi_model->uploadLine($itemCode, $desc, $uom, $qty, $directUnitCost, $unitCostLcy, $amt, $amtIncVat, $unitCost, $lineAmt, $qtyPerUom, $uomCode, $getPiId,$lineDiscPercent,$lineDiscAmount);
                        }
                    }
                }
            }
        }    

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $error = array('action' => 'Uploading Purchase Invoice', 'error_msg' => $this->db->error()); //Log error message to `error_log` table
            $this->db->insert('error_log', $error);
            $msg = ['info'=> 'Error','message' => 'Error uploading Purchase Invoice!'];
        } else if($piExist > 0 ){
            // echo '1';
            $msg = ['info'=> 'Error','message' => 'Purchase Invoice(PI) already exists!'];
        } else if($poDontExist > 0 ){
            // echo '2';
            $msg = ['info'=> 'Error','message' => 'PO not existed!'];
        } else if($invalidSupp > 0 ){
            // echo '3';
            $msg = ['info'=> 'Error','message' => 'Supplier is invalid!'];
        } else if($checkFileName === FALSE){
            // echo '4';
            $msg = ['info' => 'PI-Invalid','message' => 'PI file does not match with the selected location!'] ;
        } else if( !empty($invalidExt) && $checkFileName !== FALSE ){
            // echo '5';
            $msg = [ 'info' => 'Error-ext', 'message' => 'Invalid file extension detected!', 'ext' => array_unique($invalidExt,SORT_STRING) ];
        } else if( !empty($itemNotFound) && $checkFileName !== FALSE ){
            // echo '6';
            $msg = ['info' => 'Error-item','message' => 'Item(s) not found in the masterfile!','item' => $itemNotFound] ;
        } else if( !$isHeader ){ 
            // echo '7';
            $msg = ['info'=> 'Error','message' => 'Text file has no header!'];
        } else if( $itemsLine == 0 ){
            // echo '8';
            $msg = ['info'=> 'Error','message' => 'Text file has no line!'];
        } else {
            // echo '9';
            $msg = ['info'=> 'Success','message' => 'Purchase Invoice successfully saved!'];
        }  
    
        JSONResponse($msg);
    }

    public function getPIs()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $supId      = $fetch_data['supId'];
        $cusId      = $fetch_data['cusId'];
        $from       = $fetch_data['from'];
        $to         = $fetch_data['to'];
        $result     = $this->proformavspi_model->loadPiCm($supId, $cusId, $from, $to);
        return JSONResponse($result);
    }

    public function getPiDetails()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $piId       = $fetch_data['pi'];
        $result     = $this->proformavspi_model->getPiDetails($piId);
        return  JSONResponse($result);
    }
    public function updatePrice()
    {
        $fetch_data  = $this->input->post(NULL, TRUE);
        $msg         = array();
        if ($fetch_data['itemQty'] == 0) /* if empty qty, remarks ra ang ma update */ {
            $updateRemarks = $this->proformavspi_model->updateRemarks($fetch_data['piLineId'], $fetch_data['piHeadId'], $fetch_data['remarks']);
            if ($updateRemarks) {
                $msg = ['info' => 'success', 'message' => 'Item is updated!' ,'color' => 'green'];
            } else {
                $msg = ['info' => 'error', 'message' => 'Failed to update item!','color' => 'red'];
            }
        } else {
            $insertLog   = array('pi_line_id'    => $fetch_data['piLineId'],
                                 'pi_head_id'    => $fetch_data['piHeadId'],
                                 'item_code'     => $fetch_data['itemCode'],
                                 'new_price'     => str_replace(',', '', $fetch_data['newPrice']),
                                 'old_price'     => str_replace(',', '', $fetch_data['oldPrice']),
                                 'new_amt'       => str_replace(',', '', $fetch_data['newAmount']),
                                 'old_amt'       => str_replace(',', '', $fetch_data['oldAmount']),
                                 'user_id'       => $this->session->userdata('user_id'),
                                 'changed_date'  => date("Y-m-d"),
                                 'changed_time'  => date("H:i:s") );

            $newPrice       = str_replace(',', '', $fetch_data['newPrice']);
            $newAmt         = str_replace(',', '', $fetch_data['newAmount']);
            $itemCode       = $fetch_data['itemCode'];
            $piLineId       = $fetch_data['piLineId'];
            $piHeadId       = $fetch_data['piHeadId'];
            $remarks        = $fetch_data['remarks'];

            $insert         = $this->db->insert('purchase_invoice_pricelog', $insertLog);
            $update         = $this->proformavspi_model->updatePrice($piLineId, $piHeadId, $newPrice, $newAmt, $itemCode, $remarks);
            if ($insert && $update) {
                $msg = ['info' => 'success', 'message' => 'Item is updated!'];
            } else {
                $msg = ['info' => 'error', 'message' => 'Failed to update item!'];
            }
        }
        return  JSONResponse($msg);
    }

    public function getItemPriceLog()
    {
        $fetch_data   = json_decode($this->input->raw_input_stream, TRUE);
        $itemCode     = $fetch_data['itemCode'];
        $piHeadId     = $fetch_data['piHeadId'];
        $piLineId     = $fetch_data['piLineId'];
        $getLog       = $this->proformavspi_model->getItemPriceLog($piLineId, $piHeadId, $itemCode);
        return JSONResponse($getLog);
    }

    public function getCrfInPI()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $getCrf     = $this->proformavspi_model->loadCrf($fetch_data['supId']);
        JSONResponse($getCrf);
    }

    public function getProfPiInCrf()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $getDiscVat = array();
        $profsDiscV = array();
        $result     = array();
        $getProf    = $this->proformavspi_model->loadProformaInCrf($fetch_data['crfId']);      
        $getPi      = $this->proformavspi_model->loadPiInCrf($fetch_data['crfId']);

        $result    = ['prof' => $getProf, 'pi' => $getPi];
        JSONResponse($result);
    }

    public function applyPiToCrf()
    {
        $fetch_data     =  $this->input->post(NULL, TRUE);
        $msg            = array();
        $getPiDet       =  $this->proformavspi_model->getPiDet($fetch_data['supId'], $fetch_data['pi']);
        $insertCrfLine  =  array('crf_id'               => $fetch_data['crf'],
                                 'proforma_header_id'   => 0,
                                 'pi_head_id'           => $fetch_data['pi'],
                                 'po_header_id'         => $getPiDet['po_header_id'],
                                 'supplier_id'          => $fetch_data['supId'],
                                 'customer_code'        => $getPiDet['customer_code'] );
        $apply          = $this->proformavspi_model->applyPiToCrf($fetch_data['crf'], $fetch_data['pi'], $insertCrfLine);

        if ($apply) {
            $ref    = $this->proformavspi_model->getCrfinLedger($fetch_data['crf'])['reference_no'];
            $ledger = array('reference_no'      => $ref,
                            'posting_date'      => date("Y-m-d", strtotime($getPiDet['posting_date'])),
                            'transaction_date'  => '',
                            'doc_type'          => 'Invoice',
                            'doc_no'            => $getPiDet['pi_no'],
                            'invoice_no'        => $getPiDet['vendor_invoice_no'],
                            'po_reference'      => '',
                            'credit'            => $getPiDet['amount'],
                            'debit'             => 0,
                            'tag'               => '',
                            'supplier_id'       => $fetch_data['supId'],
                            'crf_id'            => 0,
                            'user_id'           => $this->session->userdata('user_id') );
            $i = $this->db->insert('subsidiary_ledger', $ledger);
            if($i){
                $msg = ['info' => 'success', 'message' => 'PI is tagged!' ,'color' => 'green'];   
            } else {
                $msg = ['info' => 'error', 'message' => 'Failed to tag PI!','color' => 'red']; 
            }         
        } else {
            $msg = ['info' => 'info', 'message' => 'PI is already tagged!','color' => 'blue'];
        }

        return JSONResponse($msg);
    }

    public function untagPiFromCrf()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $msg        = array();
        $query = $this->db->get_where('crf_line', array('crf_id' =>$fetch_data['crfId'], 'pi_head_id' =>$fetch_data['piId'], 'proforma_header_id' => 0));
        if($query->num_rows() > 0)
        {
            $untagFromCrfLine = $this->db->delete('crf_line', array('crf_id' =>$fetch_data['crfId'], 'pi_head_id' =>$fetch_data['piId'], 'proforma_header_id' => 0));
            if($untagFromCrfLine)
            {
                $getRef  = $this->proformavspi_model->getCrfinLedger($fetch_data['crfId']);
                $getPiNo = $this->proformavspi_model->getPiNo('pi_head_id',$fetch_data['piId'])['pi_no'];
                $delete  = $this->db->delete('subsidiary_ledger', array('reference_no' =>$getRef->reference_no, 'doc_no' =>$getPiNo ));
                if($delete)
                {
                    $msg = ['info' => 'success', 'message' => 'PI is untagged!','color' => 'green'];
                }else {
                    $msg = ['info' => 'error', 'message' => 'Failed to untag PI!', 'color' => 'red']; 
                }
            }      
        } else {
            $msg = ['info' => 'info', 'message' => 'PI is already untagged!','color' => 'blue'];
        }  
        return JSONResponse($msg);
    }

    public function managersKey()
    {
        $fetch_data = $this->input->post(NULL, TRUE);
        $username   = $fetch_data['user'];
        $password   = md5($fetch_data['pass']);
        $userType   = $this->proformavspi_model->managersKey($username, $password);
        return JSONResponse($userType);
    }

    public function changeStatus()
    {
        $fetch_data = $this->input->post(NULL,TRUE);
        $updatePi   = $this->db->set('status', 'MATCHED')
                               ->where('pi_head_id', $fetch_data['piId'])
                               ->update('purchase_invoice_header');
        $updatePo   = $this->db->set('status', 'MATCHED')
                               ->where('po_header_id', $fetch_data['poId'])
                               ->update('po_header');
        
        if( $updatePi && $updatePo )
        {
            $msg = ['info' => 'success', 'message' => 'PO & PI status have been updated!'] ;

        } else if( !$updatePi && $updatePo){

            $msg = ['info' => 'error', 'message' => 'PO status have been updated but failed to update PI status!'] ;

        } else if( $updatePi && !$updatePo){

            $msg = ['info' => 'error', 'message' => 'PI status have been updated but failed to update PO status!'] ;
        } else if( !$updatePi && !$updatePo){

            $msg = ['info' => 'error', 'message' => 'Failed to update PO & PI status!'] ;
            
        }

        JSONResponse($msg);
    }    

    //original matching//
    public function matchProformaVsPi() 
    {
        $fetch_data         = $this->input->post(NULL, TRUE);       
        $type               = $fetch_data['type'];
        $profPiMerge        = array();
        $profVatDisc        = array();
        $wayParesPoProf     = array();
        $wayParesPoPi       = array();
        $getProfHead        = array();
        $profLineDetails    = array();
        $freeItems          = array();
        $vatDisc            = array();
        $getPiHead          = array();
        $piLineDetails      = array();
        $cmHead             = array();
        $cmLineDetails      = array();
        $poProfNoPairs      = array();
        $poPiNoPairs        = array();
        $flatProfLineDetails= array();
        $flatPiLineDetails  = array();
        $flatVatDisc        = array();
        $flatFreeItems      = array();
        $foundItemsInPiFromProforma = array();
        $notFoundItemsFromProforma  = array();
        $foundItemsInProfFromPi     = array();
        $notFoundItemsFromPi        = array();
        $flatFoundItemsInPiFromProforma = array();
        $flatFoundItemsInProfFromPi     = array();
        $flatCmLineDetails  = array();
        $discountVat        = array();
        $discVatSummary     = array(); 
        $cmLineMerge        = array();       
        $partiallyServed    = false;
        $unServed           = false;
        $excess             = false;
        $documentNo         = "";

        $profLineItemCounter= [];

        $dupeItemsinPi      = array();
        $dupeItemsinProf    = array();
        $itemsNotFoundInPiFromProf  = array();

        $this->db->trans_start();

        $getProf            = $this->proformavspi_model->getProformaInCrf($fetch_data['crfId']);
        $getPi              = $this->proformavspi_model->getPiInCrf($fetch_data['crfId']);
        $getHasDeal         = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['has_deal'];    
        $getDiscType        = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['disc_type'];    
        $documentNo         = $this->proformavspi_model->getDocNo(true);
        
        if($getHasDeal == "1"){
            $getDeals        = $this->proformavspi_model->getDeals($fetch_data['dealId']);
            $getNoOfDisc     = $this->proformavspi_model->getData2('vendors_deal_header','vendor_deal_head_id', $fetch_data['dealId'])['no_of_discount'];
        }
        
        foreach($getProf as $prof)
        {    
            $key = array_search($prof['po_header_id'], array_column($getPi,'po_header_id'));
            if ($key === false) {
                $wayParesPoProf[] = array('proforma_header_id' => $prof['proforma_header_id'], 'po_header_id' => $prof['po_header_id'] );
                $poProfNoPairs[]  = $prof['po_no'];
            }                
        } 
        foreach($getPi as $pi)
        {
            $key = array_search($pi['po_header_id'], array_column($getProf,'po_header_id'));
            if($key === false){
                $wayParesPoPi[] = array('pi_head_id' => $pi['pi_head_id'], 'po_header_id' => $pi['po_header_id'] );
                $poPiNoPairs[]  = $pi['po_no'];
            }
        }           
        
        // dump($getPi);
        // die();
        if( !empty($getProf) && !empty($getPi)  )
        {
            foreach ($getProf as $get1) {
                $getProfHead[]          = $this->proformavspi_model->getProformaHead($get1['proforma_header_id']);
                $profLineDetails[]      = $this->proformavspi_model->getProfLine($get1['proforma_header_id'], $fetch_data['supId']);
                $profLineItemCounter[]  = $this->proformavspi_model->getProfLineItemCounter($get1['proforma_header_id']);
                $freeItems[]            = $this->proformavspi_model->freeItems($get1['proforma_header_id'], $fetch_data['supId']);
                $vatDisc[]              = $this->proformavspi_model->getVatDisc($get1['proforma_header_id']);
            }
           
            // dump($getPi);
            // dump($profLineDetails);
            // die();

            $prevPo = "";
            foreach ($getPi as $get2) {
                $getPiHead[]        = $this->proformavspi_model->getPiHead($get2['pi_head_id']);                            
                $cmHead[]           = $this->proformavspi_model->getCmHead($get2['pi_head_id']);
                // $piLineDetails[]    = $this->proformavspi_model->getPiLine($get2['pi_head_id']);    
                // if($prevPo != $get2['po_header_id'] ){
                    // echo $get2['pi_head_id'];
                    // echo $get2['po_header_id'];
                    $piLineDetails[]    = $this->proformavspi_model->getPiLine($get2['pi_head_id'],$get2['po_header_id'], $fetch_data['supId']);
                // }               
                $prevPo = $get2['po_header_id'];
            }
        } else {
            die('no data');
        }

        // dump($piLineDetails);
        // dump($profLineDetails);
        // die();

        if(!empty($freeItems)){
            $flatFreeItems = call_user_func_array('array_merge', $freeItems);
        }

        if(!empty($cmHead)){            
            foreach($cmHead as $cm)
            {
                if(isset($cm)){
                    $cmLineDetails[] = $this->proformavspi_model->getCmLineDetails($cm['cm_head_id'], $fetch_data['supId']);
                }
            }            
        }               
        #not compatible in 5.4
        // $flatCmLineDetails   = array_merge([],...$cmLineDetails);
        // $flatVatDisc         = array_merge([],...$vatDisc); 
        // $flatProfLineDetails = array_merge([],...$profLineDetails); 
        // $flatPiLineDetails   = array_merge([],...$piLineDetails); 
      
        if( !empty($cmLineDetails)){
            $flatCmLineDetails   = call_user_func_array('array_merge', $cmLineDetails);
        }
        if( !empty($vatDisc)){
            $flatVatDisc         = call_user_func_array('array_merge', $vatDisc);
        }        
        
        $flatProfLineDetails = call_user_func_array('array_merge', $profLineDetails);
        $flatPiLineDetails   = call_user_func_array('array_merge', $piLineDetails);
        
        // dump($flatProfLineDetails);
        // dump($flatPiLineDetails);
        // die();

        $exit = FALSE ;
        $noSetup          = checkNoItemCodes($flatProfLineDetails);          
        $items            = [] ;
        if ( !empty($noSetup) ) { 

            foreach($noSetup as $n){
                $items[] = ['itemcode' => $n['item'], 'desc' => $n['idesc'] ];
            } 
            $exit = TRUE ;

        } else {

           $exit = FALSE ;

            foreach($getProfHead as $head)
            {
                foreach ($flatVatDisc as $dv) 
                {
                    if ($head['profId'] == $dv['proforma_header_id']) {
                        $discountVat[] = ['profId'   => $head['profId'],   'profCode'  => $head['profCode'], 'sono'   => $head['so_no'],
                                          'delivery' => $head['delivery'], 'customer'  => $head['l_acroname'], 'pono' => $head['po_no'],
                                          'discId'   => $dv['discount_id'], 'discName' => $dv['discount'],   'amount' => $dv['total_discount']];
                    }                        
                }
            }    

            $uniqueVatDiscName = unique_multidim_array($discountVat,'discName');
            foreach($uniqueVatDiscName as $unique)
            {   
                $amount = 0;
                foreach($discountVat as $v)
                {
                    if($unique['discName'] == $v['discName']){
                        $amount += $v['amount'] ;
                    }
                }
                $discVatSummary[] = ['name' => 'Total '.$unique['discName'] . ' :', 'amount' => $amount ];
            }
        
            $itemCodeToFind  = "";
            $disc1      = 0.00;
            $disc2      = 0.00;
            $disc3      = 0.00;
            $disc4      = 0.00;
            $disc5      = 0.00;
            $disc6      = 0.00;
            // $discAmt1   = 0.00;
            // $discAmt2   = 0.00;
            // $discAmt3   = 0.00;
            // $discAmt4   = 0.00;
            // $discAmt5   = 0.00;

            foreach ($getPi as $get3) {
                foreach( $flatProfLineDetails as $find1 ) // pangitaon sa PI ang items nga naa sa proforma
                {
                    
                    $query = $this->db->select('i.id,i.itemcode_loc,i.item_division,i.item_department_code,i.item_group_code,ph.pi_head_id,ph.pi_no,ph.posting_date, ph.po_header_id,
                                                pi.item_code,pi.description,pi.uom,sum(pi.qty) as qty,pi.direct_unit_cost,pi.amt_including_vat')
                                    ->from('purchase_invoice_line pi')
                                    ->join('purchase_invoice_header ph','ph.pi_head_id = pi.pi_head_id', 'left')
                                    ->join('items i','i.itemcode_loc = pi.item_code', 'left')
                                    ->where('ph.po_header_id',$find1['poId'])
                                    ->where('pi.item_code',$find1['itemcode_loc'])
                                    ->where('pi.pi_head_id', $get3['pi_head_id'])
                                    ->where('i.supplier_id', $fetch_data['supId'] )
                                    ->group_by('pi.item_code') //(sum qty and group by pi item code for items nga ngbalik2 sa isa ka PI)
                                    ->get();
                        if($query->num_rows() > 0){
                            array_push($foundItemsInPiFromProforma, $query->result_array() ); 
                        
                        }else {
                            if($getHasDeal == "1"){
                                if($getDiscType == "PERCENTAGE" || $getDiscType == "AMOUNT" ){
                                    foreach($getDeals as $deal1)/* vendor deals   */ 
                                    { 
                                        if($deal1['type'] == "Item Department"){
                                            $itemCodeToFind = $find1['item_department_code'];
                                        } else if($deal1['type'] == "Item"){
                                            $itemCodeToFind = $find1['itemcode_loc'];
                                        } else if($deal1['type'] == "Item Group"){
                                            $itemCodeToFind = $find1['item_group_code'];
                                        }
                                        if($deal1['number'] == $itemCodeToFind ){
                                            if($getDiscType == "PERCENTAGE"){
                                                $disc1 = 1.00 - $deal1['disc_1'] * 0.01 ;
                                                $disc2 = 1.00 - $deal1['disc_2'] * 0.01 ;
                                                $disc3 = 1.00 - $deal1['disc_3'] * 0.01 ;
                                                $disc4 = 1.00 - $deal1['disc_4'] * 0.01 ;
                                                $disc5 = 1.00 - $deal1['disc_5'] * 0.01 ;
                                                $disc6 = 1.00 - $deal1['disc_6'] * 0.01 ;
                                            } else if($getDiscType == "AMOUNT"){
                                                $disc1   = $deal1['disc_1'];
                                                $disc2   = $deal1['disc_2'];
                                                $disc3   = $deal1['disc_3'];
                                                $disc4   = $deal1['disc_4'];
                                                $disc5   = $deal1['disc_5'];
                                                $disc6   = $deal1['disc_6'];
                                            }
                                            
                                        }
                                    }
                                } else if( $getDiscType == "NONE" ){
                                    $disc1 = 0 ;
                                    $disc2 = 0 ;
                                    $disc3 = 0 ;
                                    $disc4 = 0 ;
                                    $disc5 = 0 ;
                                    $disc6 = 0 ;
                                }
                            }
                            
                            $notFoundItemsFromProforma[] = ['profId' => $find1['profId'],'profCode' => $find1['profCode'],'id'      => $find1['id'], 
                                                            'poId'   => $find1['poId'],  'item'     => $find1['item'],    'idesc'   => $find1['idesc'],  
                                                            'qty'    => $find1['qty'],   'uom'      => $find1['uom'],     'price'   => $find1['price'],  
                                                            'amount' => $find1['amount'],'disc1'    => $disc1,'disc2' => $disc2, 'disc3' => $disc3, 
                                                            'disc4' => $disc4, 'disc5' => $disc5, 'disc6' => $disc6 ];                
                        }
                }
            }


            // dump($flatPiLineDetails);
            // dump($flatProfLineDetails);
            // die();
            
            foreach ($getProf as $get4) {
                foreach($flatPiLineDetails as $find2) //pangitaon sa proforma ang naa sa PI
                {
                    $query = $this->db->select('i.id,pl.itemcode_loc,i.item_division,i.item_department_code,i.item_group_code,pf.proforma_header_id,pf.proforma_code,pf.po_header_id,
                                                pl.item_code,pl.description,pl.qty,pl.uom,pl.price,pl.amount')
                                    ->from('proforma_line pl')
                                    ->join('proforma_header pf','pf.proforma_header_id = pl.proforma_header_id', 'left')
                                    ->join('items i','i.itemcode_sup = pl.item_code','left')
                                    ->where('pf.po_header_id',$find2['poId'])
                                    ->where('pl.itemcode_loc',$find2['item'])
                                    ->where('pl.proforma_header_id', $get4['proforma_header_id'])
                                    ->where('i.supplier_id', $fetch_data['supId'] )
                                    ->get();

                    if($query->num_rows() > 0){               
                        foreach($flatProfLineDetails as $find3)
                        {
                            if($find2['id'] == $find3['id'] && $find2['poId'] == $find3['poId']){  
                                            
                                $foundItemsInProfFromPi[] = ['id' => $find3['id'],'itemcode_loc' => $find3['itemcode_loc'],'item_division' => $find3['item_division'],
                                                            'item_department_code' => $find3['item_department_code'],'item_group_code' => $find3['item_group_code'],
                                                            'proforma_header_id' => $find3['profId'],'proforma_code' => $find3['profCode'], 'po_header_id' => $find3['poId'],
                                                            'item_code' => $find3['item'], 'description' => $find3['idesc'],'qty' => $find3['qty'],'uom' => $find3['uom'],
                                                            'price' => $find3['price'], 'amount' => $find3['amount'] ];

                            }
                        }       
                        
                    } else {      
                        if($getHasDeal == "1"){
                            if($getDiscType == "PERCENTAGE" || $getDiscType == "AMOUNT"){
                                foreach($getDeals as $deal2)/* vendor deals   */ 
                                { 
                                    if($deal2['type'] == "Item Department"){
                                        $itemCodeToFind = $find2['item_department_code'];
                                    } else if($deal2['type'] == "Item"){
                                        $itemCodeToFind = $find2['itemcode_loc'];
                                    } else if($deal2['type'] == "Item Group"){
                                        $itemCodeToFind = $find2['item_group_code'];
                                    }
                                    if($deal2['number'] == $itemCodeToFind ){
                                        if($getDiscType == "PERCENTAGE"){
                                            $disc1 = 1.00 - $deal2['disc_1'] * 0.01 ;
                                            $disc2 = 1.00 - $deal2['disc_2'] * 0.01 ;
                                            $disc3 = 1.00 - $deal2['disc_3'] * 0.01 ;
                                            $disc4 = 1.00 - $deal2['disc_4'] * 0.01 ;
                                            $disc5 = 1.00 - $deal2['disc_5'] * 0.01 ;
                                            $disc6 = 1.00 - $deal2['disc_6'] * 0.01 ;
                                        } else if($getDiscType == "AMOUNT"){
                                            $disc1   = $deal2['disc_1'];
                                            $disc2   = $deal2['disc_2'];
                                            $disc3   = $deal2['disc_3'];
                                            $disc4   = $deal2['disc_4'];
                                            $disc5   = $deal2['disc_5'];     
                                            $disc6   = $deal2['disc_6'];                       
                                        }
                                    
                                        
                                    }
                                }

                            }else if( $getDiscType == "NONE" ){
                                $disc1 = 0 ;
                                $disc2 = 0 ;
                                $disc3 = 0 ;
                                $disc4 = 0 ;
                                $disc5 = 0 ;
                                $disc6 = 0 ;
                            }
                        }
                        $notFoundItemsFromPi[] = ['piId'   => $find2['piId'],   'piNo'  => $find2['piNo'], 'piDate'  => $find2['piDate'],   
                                                  'poId'   => $find2['poId'],   'id'    => $find2['id'],   'item'    => $find2['item'],
                                                  'idesc'  => $find2['idesc'],  'uom'   => $find2['uom'],  'qty'     => $find2['qty'],
                                                  'direct' => $find2['direct'], 'amount'=> $find2['amount'],
                                                  'disc1'  => $disc1,'disc2' =>$disc2, 'disc3' => $disc3,  'disc4' => $disc4, 'disc5' => $disc5, 'disc6' => $disc6 ]  ;
                        
                    }
                }
            }
            
          

            // dump($foundItemsInProfFromPi);
            // die();

            $flatPiItems        = call_user_func_array('array_merge', $foundItemsInPiFromProforma);

            // dump($foundItemsInProfFromPi);
            // die();

             #region kuhaon sa array ang items nga naa sa notfound array if ang items naa sa flat arrays 
             if(!empty($notFoundItemsFromProforma)){
                foreach($notFoundItemsFromProforma as $key=>$notFound3){
                    foreach($foundItemsInProfFromPi as $flatProf1){
                        if($notFound3['profId'] == $flatProf1['proforma_header_id'] && $notFound3['item'] == $flatProf1['item_code']){
                           unset($notFoundItemsFromProforma[$key]);
                        }
                    }
                }
            }

            $columns = array_column($notFoundItemsFromProforma, "item");
            array_multisort($columns, SORT_ASC, $notFoundItemsFromProforma);
            $newNotFoudFromProforma = [];
            $prevPOId   = '';
            $prevProfId = '';
            $prevItem   = '';
            foreach( $notFoundItemsFromProforma as $ff){
                // if( $prevPOId != $ff['poId'] && $prevProfId != $ff['profId'] && $prevItem == $ff['item']){
                //     array_push($newNotFoudFromProforma, $ff);
                // } else if( $prevPOId != $ff['poId'] && $prevProfId != $ff['profId'] && $prevItem != $ff['item'] ){
                //     array_push($newNotFoudFromProforma, $ff);
                // }   
                if( $prevItem != $ff['item'] ){
                    if( $prevPOId != $ff['poId'] && $prevProfId != $ff['profId'] ){
                        array_push($newNotFoudFromProforma, $ff);
                    } else if( $prevPOId == $ff['poId'] && $prevProfId != $ff['profId'] ){
                        array_push($newNotFoudFromProforma, $ff);
                    } else if( $prevPOId != $ff['poId'] && $prevProfId == $ff['profId'] ){
                        array_push($newNotFoudFromProforma, $ff);
                    }

                } else if( $prevItem == $ff['item'] ){
                    if( $prevPOId != $ff['poId'] && $prevProfId != $ff['profId'] ){
                        array_push($newNotFoudFromProforma, $ff);
                    } else if( $prevPOId == $ff['poId'] && $prevProfId != $ff['profId'] ){
                        array_push($newNotFoudFromProforma, $ff);
                    } else if( $prevPOId != $ff['poId'] && $prevProfId == $ff['profId'] ){
                        array_push($newNotFoudFromProforma, $ff);
                    }
                }
                $prevPOId   = $ff['poId'];
                $prevProfId = $ff['profId'];
                $prevItem   = $ff['item'];
            }
            // dump($newNotFoudFromProforma);
            // die();

            if(!empty($notFoundItemsFromPi)){
                foreach($notFoundItemsFromPi as $key=>$notFound4){
                    foreach($flatPiItems as $flatPi1){
                        if($notFound4['piId'] == $flatPi1['pi_head_id'] && $notFound4['item'] == $flatPi1['item_code']){
                            unset($notFoundItemsFromPi[$key]);
                        }
                    }
                }
            }
            #endregion

            foreach($flatPiItems as $a) // foreach($finalPi as $a)
            {
                foreach($foundItemsInProfFromPi as $b)
                {
                    if( $a['po_header_id'] == $b['po_header_id'] && $a['id'] == $b['id'] ){
                        if($getHasDeal == "1"){
                            if($getDiscType == "PERCENTAGE" || $getDiscType == "AMOUNT"){
                                foreach($getDeals as $deals)/* vendor deals   */ 
                                {
                                    if($deals['type'] == "Item Department"){
                                        $itemCodeToFind = $a['item_department_code'];
                                    } else if($deals['type'] == "Item"){
                                        $itemCodeToFind = $a['itemcode_loc'];
                                    } else if($deals['type'] == "Item Group"){
                                        $itemCodeToFind = $a['item_group_code'];
                                    }
                                    if($deals['number'] == $itemCodeToFind ){
                                        if($getDiscType == "PERCENTAGE"){
                                            $disc1 = 1.00 - $deals['disc_1'] * 0.01 ;
                                            $disc2 = 1.00 - $deals['disc_2'] * 0.01 ;
                                            $disc3 = 1.00 - $deals['disc_3'] * 0.01 ;
                                            $disc4 = 1.00 - $deals['disc_4'] * 0.01 ;
                                            $disc5 = 1.00 - $deals['disc_5'] * 0.01 ;
                                            $disc6 = 1.00 - $deals['disc_6'] * 0.01 ;
                                        } else if($getDiscType == "AMOUNT"){
                                            $disc1   = $deals['disc_1'];
                                            $disc2   = $deals['disc_2'];
                                            $disc3   = $deals['disc_3'];
                                            $disc4   = $deals['disc_4'];
                                            $disc5   = $deals['disc_5'];  
                                            $disc6   = $deals['disc_6'];                          
                                        }                                
                                        
                                    }
                                }
                            } else if($getDiscType == "NONE") { //net amount, no sop deduction like LMB GROCERY
                                $disc1 = 0 ;
                                $disc2 = 0 ;
                                $disc3 = 0 ;
                                $disc4 = 0 ;
                                $disc5 = 0 ;
                                $disc6 = 0 ;
                            }
                        }

                        $profPiMerge[] = [ 'itemId'   =>$a['id'],         'poId'    =>$a['po_header_id'], 'profCode'=>$b['proforma_code'],'profItem' =>$b['item_code'],
                                        'profDesc' =>$b['description'],'profQty' =>$b['qty'],          'profUom' =>$b['uom'],          'profPrice'=>$b['price'],
                                        'profAmt'  =>$b['amount'],     'piId'    =>$a['pi_head_id'],   'piNo'    =>$a['pi_no'],        'piItem'   =>$a['item_code'], 
                                        'piDate'   =>$a['posting_date'],'piDesc' =>$a['description'],  'piQty'   =>$a['qty'],          'piUom'    =>$a['uom'],
                                        'piPrice'  =>$a['direct_unit_cost'], 'piAmt'   =>$a['amt_including_vat'], 'disc1' => $disc1,'disc2' =>$disc2, 'disc3' => $disc3, 
                                        'disc4' => $disc4, 'disc5' => $disc5, 'disc6' => $disc6,
                                        'partiallyServed'   => ($a['qty'] > 0 &&  $a['qty'] < $b['qty']  ) ? 1 : 0,
                                        'excess'            => ($a['qty'] > $b['qty']) ? 1 : 0, 
                                        'fullyServed'       => ( $a['qty'] > 0 && $a['qty'] == $b['qty']) ? 1 : 0 ];
                        // 'partiallyServed'   => ($a['qty'] > 1 &&  $a['qty'] < $b['qty']  ) ? 1 : 0,
                    } 
                }
            }

            
            if(!empty($cmHead) && !empty($flatCmLineDetails)){
                foreach($cmHead as $ch)
                {
                    foreach($flatCmLineDetails as $cml)
                    {      
                        if(isset($ch)){
                            if($ch['cm_head_id'] == $cml['cm_head_id']){
                                if($getHasDeal == "1"){
                                    if($getDiscType == "PERCENTAGE" || $getDiscType == "AMOUNT" ){
                                        foreach($getDeals as $deal3)/* vendor deals   */ 
                                        {
                                            if($deal3['type'] == "Item Department"){
                                                $itemCodeToFind = $cml['item_department_code'];
                                            } else if($deal3['type'] == "Item"){
                                                $itemCodeToFind = $cml['itemcode_loc'];
                                            } else if($deal3['type'] == "Item Group"){
                                                $itemCodeToFind = $cml['item_group_code'];

                                            }
                                            if($deal3['number'] == $itemCodeToFind ){
                                                if($getDiscType == "PERCENTAGE"){
                                                    $disc1 = 1.00 - $deal3['disc_1'] * 0.01 ;
                                                    $disc2 = 1.00 - $deal3['disc_2'] * 0.01 ;
                                                    $disc3 = 1.00 - $deal3['disc_3'] * 0.01 ;
                                                    $disc4 = 1.00 - $deal3['disc_4'] * 0.01 ;
                                                    $disc5 = 1.00 - $deal3['disc_5'] * 0.01 ;
                                                    $disc6 = 1.00 - $deal3['disc_6'] * 0.01 ;
                                                } else if($getDiscType == "AMOUNT"){
                                                    $disc1   = $deal3['disc_1'];
                                                    $disc2   = $deal3['disc_2'];
                                                    $disc3   = $deal3['disc_3'];
                                                    $disc4   = $deal3['disc_4'];
                                                    $disc5   = $deal3['disc_5'];  
                                                    $disc6   = $deal3['disc_6'];                              
                                                }                                        
                                            }
                                        }
                                    } else if($getDiscType == "NONE"){
                                        $disc1      = 0.00;
                                        $disc2      = 0.00;
                                        $disc3      = 0.00;
                                        $disc4      = 0.00;
                                        $disc5      = 0.00;
                                        $disc6      = 0.00;
                                    }
                                }
                                $cmLineMerge[] = ['cmId' => $ch['cm_head_id'], 'cmNo' => $ch['cm_no'], 'date' => $ch['posting_date'], 'item' => $cml['item_code'],
                                                'desc' => $cml['description'],'uom' => $cml['uom'],'qty' => $cml['qty'], 'price' => $cml['direct_unit_cost'],
                                                'disc1'=> $disc1, 'disc2' => $disc2, 'disc3' => $disc3, 'disc4' => $disc4, 'disc5' => $disc5, 'disc6' => $disc6,
                                                'piNo' => $cml['pi_no']  ];
                            }
                        }
                    }
                }
            }

            $fullyServed       = array_search(1, array_column($profPiMerge, 'fullyServed'));
            $partiallyServed   = array_search(1, array_column($profPiMerge, 'partiallyServed'));
            $excess            = array_search(1, array_column($profPiMerge, 'excess'));       
        }

        $sopDeduction      = $this->proformavspi_model->getSopDeduction($fetch_data['crfId']); 
        $sopCharges        = $this->proformavspi_model->getSopCharges($fetch_data['crfId']);
        // dump($sopDeduction);
        // die();

        $this->db->trans_complete();

        // var_dump($profPiMerge);
        // var_dump($newNotFoudFromProforma);
        // var_dump($notFoundItemsFromPi);
        // die();


        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $error = array('action' => 'Matching Pro-forma vs PI', 'error_msg' => $this->db->error()); //Log error message to `error_log` table
            $this->db->insert('error_log', $error);

            $msg = ['info' => 'incomplete', 'message' => 'Matching is incomplete!'];
        } else {

            if( !$exit ){
                // for history
                $transaction = array('tr_no'         => $documentNo,
                                    'tr_date'       => date("F d, Y - h:i:s A"),
                                    'crf_id'        => $fetch_data['crfId'],
                                    'supplier_id'   => $fetch_data['supId'],
                                    'customer_code' => $fetch_data['cusId'],
                                    'user_id'       => $this->session->userdata('user_id'),
                                    'filename'      => '' );
                $this->db->insert('profvpi_transaction', $transaction);
                $transactionId = $this->db->insert_id();
                // for history
                if($type == "pdf"){
                    $generate  = $this->generateProformaVsPi($transactionId,$fetch_data, $getProfHead, $discountVat, $discVatSummary, $profPiMerge, $fullyServed, $partiallyServed, 
                                                                $excess, $sopDeduction, $sopCharges, $newNotFoudFromProforma, $notFoundItemsFromPi,$cmHead,$cmLineMerge,$flatFreeItems);
                } else if($type == "excel"){
                    $generate      = $this->generateProformaVsPiExcel($transactionId,$fetch_data, $getProfHead, $discountVat, $discVatSummary, $profPiMerge, $fullyServed, $partiallyServed, 
                                                                $excess, $sopDeduction, $sopCharges, $newNotFoudFromProforma, $notFoundItemsFromPi,$cmHead,$cmLineMerge,$flatFreeItems);
                }

                $msg = ['info' => 'success', 'message' => 'Matching Pro-forma vs PI is complete!', 'file' => $generate, 'wayParesPoProf' => $poProfNoPairs, 'wayParesPoPi' => $poPiNoPairs ];
           
            } else {

                $msg  = ['info' => 'no-setup', 'message' => 'UNMAPPED ITEM(s) :', 'nosetup' => $items ];
            }

        }

        
        JSONResponse($msg);
    }
    //original matching//

    public function viewMatchedUnmatchedItems()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $getProf    = $this->db->select('proforma_header_id')
                               ->from('crf_line')
                               ->where('crf_id', $fetch_data['crfId'])
                               ->where('pi_head_id', 0)
                               ->get()
                               ->result_array();
        $getPi      = $this->db->select('pi_head_id')
                               ->from('crf_line')
                               ->where('crf_id', $fetch_data['crfId'])
                               ->where('proforma_header_id', 0)
                               ->get()
                               ->result_array();
        foreach($getProf as $prof)
        {            
            $itemsProf[] = $this->db->select('l.proforma_header_id,p.po_header_id,i.id,l.item_code,l.description,i.itemcode_loc,i.description')
                                    ->from('proforma_line l')
                                    ->join('items i', 'i.itemcode_sup = l.item_code', 'left')
                                    ->join('proforma_header h','h.proforma_header_id = l.proforma_header_id', 'left')
                                    ->join('po_header p', 'p.po_header_id = h.po_header_id', 'left')
                                    ->where('l.proforma_header_id', $prof['proforma_header_id'])
                                    ->get()
                                    ->result_array();            
        }

        foreach($getPi as $pi)
        {
            $itemsPi[] = $this->db->select('p.pi_head_id,o.po_header_id, i.id, p.item_code, p.description, i.itemcode_sup, i.description')
                                  ->from('purchase_invoice_line p')
                                  ->join('items i', 'i.itemcode_loc = p.item_code', 'left')
                                  ->join('purchase_invoice_header h','h.pi_head_id = p.pi_head_id', 'left')
                                  ->join('po_header o', 'o.po_header_id = h.po_header_id', 'left')
                                  ->where('p.pi_head_id',$pi['pi_head_id'])
                                  ->get()
                                  ->result_array();
        }
        // $flatPi       = array_merge([],...$itemsPi); 
        // $flatProf     = array_merge([],...$itemsProf);
        $flatPi       = call_user_func_array('array_merge', $itemsPi);
        $flatProf     = call_user_func_array('array_merge', $itemsProf);

        foreach($flatProf as $pf)
        {
            $found   = array_search($pf['id'], array_column($flatPi, 'id'));
            if($found !== false )
            {
                $foundItemsProf[] = array('profId'   => $pf['proforma_header_id'],
                                          'poId'     => $pf['po_header_id'],
                                          'itemId'   => $pf['id'] ,
                                          'itemProf' => $pf['item_code'],
                                          'desc'     => $pf['description']);
            } else {
                $notFoundItemsProf[] = array('profId'   => $pf['proforma_header_id'],
                                             'poId'     => $pf['po_header_id'],
                                             'itemId'   => $pf['id'] ,
                                             'itemProf' => $pf['item_code'],
                                             'desc'     => $pf['description']);
            }
        }

        foreach($flatPi as $p)
        {
            $found = array_search($p['id'], array_column($flatProf,'id'));
            if($found !== false)
            {
                $foundItemsPi[] = array('piId'   => $p['pi_head_id'],
                                        'poId'   => $p['po_header_id'],
                                        'itemId' => $p['id'],
                                        'itemPi' => $p['item_code'],
                                        'desc'   => $p['description']);
            } else {
                $notFoundItemsPi[] = array('piId'   => $p['pi_head_id'],
                                           'poId'   => $p['po_header_id'],
                                           'itemId' => $p['id'],
                                           'itemPi' => $p['item_code'],
                                           'desc'   => $p['description']);
            }
        }

        foreach($foundItemsProf as $found1)
        {
            foreach($foundItemsPi as $found2)
            {
                if($found1['poId'] == $found2['poId'] && $found1['itemId'] == $found2['itemId']){
                    $sameItemSamePo[] = array('profId'   => $found1['profId'],
                                              'piId'     => $found2['piId'],
                                              'itemId'   => $found1['itemId'],
                                              'itemProf' => $found1['itemProf'],
                                              'itemPi'   => $found2['itemPi'],
                                              'desc'     => $found1['desc']);
                }                 
            }
        }
        
        return JSONResponse($sameItemSamePo);
    }

    public function uploadCm()
    {
        $fetch_data = $this->input->post(NULL, TRUE);          
        $supId      = $this->proformavspi_model->getPiHead($fetch_data['piId'])['supplier_id'];
        $cusId      = $this->proformavspi_model->getPiHead($fetch_data['piId'])['customer_code'];
        $supCode    = $this->proformavspi_model->getSupplierData($supId,'supplier_id')['supplier_code'];
        $insert     = 0;
        $insertLine = 0;
        $fileName   = $_FILES['cmFile']['name'];
        $Temp       = $_FILES['cmFile']['tmp_name'];
        $cmContent  = file_get_contents($_FILES['cmFile']['tmp_name']);

        $this->db->trans_start();

        $line      = explode("\n", $cmContent);
        $totalLine = count($line);        
     
        for ($i = 0; $i < $totalLine; $i++) 
        {
            if(isset($line[$i])){
                $blits = str_replace('"', "", $line[$i]);
                $refined = explode("|", $blits);
                $countRefined = count($refined);

                if ($countRefined == 6) {
                    $piInCm = $this->proformavspi_model->getPiData('pi_no', trim($refined[1]))['pi_head_id'];
                    if($piInCm == $fetch_data['piId'] ){
                        if ($supCode == trim($refined[5])) {//validate if same ang sa selected supplier sa supplier  naa sa texfile  
                            $no          = trim($refined[0]);
                            $piId        = $piInCm;
                            $date        = date("Y-m-d",strtotime(trim($refined[2])));
                            $amount      = str_replace(',','',trim($refined[3]));                            

                            $cmId = $this->proformavspi_model->uploadCMHeader($no, $piId, $date, $amount, $supId); 
                            if(is_int($cmId) ){
                                $insert ++ ;
                                $saveDocument = ['document_name'  => $fileName,
                                                 'document_type'  => 'Credit Memo',
                                                 'document_path'  => "files/PROFORMAVSPI/CREDIT MEMO/".$fileName,
                                                 'uploaded_on'    => date("Y-m-d H:i:s"),
                                                 'supplier_id'    => $supId,
                                                 'customer_code'  => $cusId,
                                                 'user_id'        => $this->session->userdata('user_id')] ;                                                    
                                $this->db->insert('uploaded_transaction_documents', $saveDocument);
                                move_uploaded_file($Temp,"files/PROFORMAVSPI/CREDIT MEMO/".$fileName);
                            } else if($cmId === false){
                                $msg = ['info'=> 'Error','message' => 'Credit Memo(CM) already exists!'];
                                break;
                            }
                        } else {
                            $msg = ['info' => 'Error', 'message' => 'Different supplier in PI and in CM!'];
                            break;
                        }
                    } else {
                        $msg = ['info' => 'Error', 'message' => 'Selected PI to be applied by this CM is not the PI in CM textfile!'];
                        break;
                    }
                }

                if ($countRefined == 16) {
                    $item        = $refined[2];                    
                    $desc        = $refined[3];
                    $uom         = $refined[5];
                    $qty         = $refined[6]; 
                    $direct      = str_replace(',', '', $refined[7]);
                    $unitLcy     = str_replace(',', '', $refined[8]);
                    $amt         = str_replace(',', '', $refined[9]);
                    $amtVat      = str_replace(',', '', $refined[10]);
                    $unitCost    = str_replace(',', '', $refined[11]);
                    $lineA        = str_replace(',', '', $refined[12]);   
  
                    $cmLine = $this->proformavspi_model->uploadCMLine($item, $desc, $uom, $qty, $direct, $unitLcy, $amt, $amtVat, $unitCost, $lineA, $cmId);
                    if($cmLine){
                        $insertLine ++ ;
                    }
                }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $error = array('action' => 'Uploading CM', 'error_msg' => $this->db->error()); //Log error message to `error_log` table
            $this->db->insert('error_log', $error);
            $msg = ['info' => 'Error', 'message' => 'Error in uploading Credit Memo(CM)!'];

        } else {
            if($insert > 0 && $insertLine > 0){
                $msg = ['info' => 'Success', 'message' => 'Credit Memo(CM) is uploaded successfully!'];
            } else if( $insert == 0 && $insertLine == 0){
                $msg = ['info' => 'Error', 'message' => 'Error in uploading Credit Memo(CM)!'];
            }
        }       
        
        return JSONResponse($msg);
    }

    public function viewCMDetails()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $cmDetails  = $this->proformavspi_model->getCmDetails($fetch_data['cmId']);
        return JSONResponse($cmDetails);
    }

    public function changeAuditStatus()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $pi = $this->db->set('audited', '1')
                       ->where('pi_head_id', $fetch_data['piId'])
                       ->update('purchase_invoice_header');
        if($pi){
            die('success');
        } else {
            die('failed');
        }
    }

    public function getPo()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $data       = $this->proformavspi_model->getPo($fetch_data['pi']);

        JSONResponse($data);
    }

    // NEW TAGGING AND MATCHING //
    public  function loadCRFS()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $crfs = $this->proformavspi_model->loadCrf($fetch_data['supId']);
        JSONResponse($crfs);
    }

    public function searchProf()
    {
        $supId        = $this->uri->segment(4);
        $crfId        = $this->uri->segment(5);
        $fetch_data   = json_decode($this->input->raw_input_stream, true);
        $str          = $fetch_data['str'];

        $profs = $this->proformavspi_model->getUnAppliedProfs($crfId, $supId, $str);
        JSONResponse($profs);
    }

    public function applyProforma()
    {
        $crfId      = $this->uri->segment(4);
        $fetch_data = json_decode($this->input->raw_input_stream, true);
        $profPO     = $this->db->select('po_header_id')
                               ->get_where('proforma_header', array('proforma_header_id' => $fetch_data['profId']))
                               ->row();
                               
        $piPO       = $this->db->select('po_header_id')
                               ->get_where('purchase_invoice_header', array('pi_head_id' => $fetch_data['piId']))
                               ->row();

        if($profPO->po_header_id == $piPO->po_header_id ){
            $query = $this->db->select('crf_psi_pi_id')
                              ->where('crf_id', $crfId)
                              ->where('proforma_header_id',$fetch_data['profId'])
                              ->where('pi_head_id', $fetch_data['piId'])
                              ->get('crf_psi_pi');
            if ($query->num_rows() == 0) {
                $insert = ['crf_id'              => $crfId,
                           'proforma_header_id'  => $fetch_data['profId'],
                           'pi_head_id'          => $fetch_data['piId'],
                           'po_header_id'        => $profPO->po_header_id ];

                $this->db->insert('crf_psi_pi', $insert);
                die("success");
            } else {
                die("exists");
             }
        } else {
            die("un-equal-po");
        }
       
    }

    public function getAppliedProforma()
    {
        $piId      = $this->uri->segment(4);
        $crfId     = $this->uri->segment(5);

        $loadApplied = $this->proformavspi_model->loadAppliedProf($crfId,$piId);
        JSONResponse($loadApplied);
    }

    public function untagProf()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, true);
        if(!empty($fetch_data['lineId'])){

            $untag = $this->db->delete('crf_psi_pi', array('crf_psi_pi_id' => $fetch_data['lineId']));
            if($untag){
                die("success");
            } else {
                die("failed");
            }
        }
    }

    public function ProfvsPi()
    {
        $fetch_data   = $this->input->post(NULL,TRUE);
        $type         = $fetch_data['type'];
        $getProfHead                = array();
        $profLineDetails            = array();
        $freeItems                  = array();
        $vatDisc                    = array();
        $getPiHead                  = array();
        $piLineDetails              = array();
        $cmHead                     = array();
        $cmLineDetails              = array();
        $foundItemsInPiFromProforma = array();
        $notFoundItemsFromProforma  = array();
        $foundItemsInProfFromPi     = array();
        $flatProfLineDetails        = array();
        $flatPiLineDetails          = array();
        $flatVatDisc                = array();
        $flatFreeItems              = array();
        $foundItemsInPiFromProforma = array();
        $notFoundItemsFromProforma  = array();
        $foundItemsInProfFromPi     = array();
        $notFoundItemsFromPi        = array();
        $flatFoundItemsInPiFromProforma = array();
        $flatFoundItemsInProfFromPi     = array();
        $flatCmLineDetails              = array();
        $discountVat                    = array();
        $discVatSummary                 = array(); 
        $cmLineMerge                    = array();    
        $profPiMerge                    = array();
        $countProfOccurences            = array();   
        $partiallyServed                = false;
        $unServed                       = false;
        $excess                         = false;
        $noData                         = false;
        $documentNo                     = "";

        $this->db->trans_start();
        $getHasDeal         = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['has_deal'];    
        $getDiscType        = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['disc_type'];    
        $documentNo         = $this->proformavspi_model->getDocNo(true);
        $getCRFPROFPI       = $this->proformavspi_model->getCRFPROFPI($fetch_data['crfId']);// $this->db->get_where('crf_psi_pi', array('crf_id' => $fetch_data['crfId']))->order_by('proforma_header_id')->result_array();
        
        if(!empty($getCRFPROFPI)){

            if($getHasDeal == "1"){
                $getDeals       = $this->proformavspi_model->getDeals($fetch_data['dealId']);
                $getNoOfDisc    = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['number_of_discount'];
            }

            $prevProf1 = "";
            $prevPi1   = "";
            $profArray = [];
            $piArray   = [];
            // dump($getCRFPROFPI);
            // die();
            foreach($getCRFPROFPI as $get1){
                if(!in_array($get1['proforma_header_id'], $profArray)){
                    $getProfHead[]          = $this->proformavspi_model->getProformaHead($get1['proforma_header_id']);
                    $profLineDetails[]      = $this->proformavspi_model->getProfLine($get1['proforma_header_id']);
                    $freeItems[]            = $this->proformavspi_model->freeItems($get1['proforma_header_id']);
                    $vatDisc[]              = $this->proformavspi_model->getVatDisc($get1['proforma_header_id']);   
                    $profArray[]            = $get1['proforma_header_id'];
                }

                if(!in_array($get1['pi_head_id'], $piArray)){
                    $getPiHead[]            = $this->proformavspi_model->getPiHead($get1['pi_head_id']);  
                    $piLineDetails[]        = $this->proformavspi_model->getPiLineDetails($get1['pi_head_id']);
                    $cmHead[]               = $this->proformavspi_model->getCmHead($get1['pi_head_id']);
                    $piArray[]              = $get1['pi_head_id'];
                }
            }
            
            // dump($profLineDetails);
            // dump($piLineDetails);
            // die();
            if( !empty($cmLineDetails)){
                $flatCmLineDetails   = call_user_func_array('array_merge', $cmLineDetails);
            }
            if( !empty($vatDisc)){
                $flatVatDisc         = call_user_func_array('array_merge', $vatDisc);
            }   

            if(!empty($freeItems)){
                $flatFreeItems = call_user_func_array('array_merge', $freeItems);
            }

            if(!empty($cmHead)){            
                foreach($cmHead as $cm)
                {
                    if(isset($cm)){
                        $cmLineDetails[] = $this->proformavspi_model->getCmLineDetails($cm['cm_head_id']);
                    }
                }            
            }    

            $flatProfLineDetails = call_user_func_array('array_merge', $profLineDetails);
            $flatPiLineDetails   = call_user_func_array('array_merge', $piLineDetails);
            
            foreach($getProfHead as $head)
            {
                foreach ($flatVatDisc as $dv) 
                {
                    if ($head['profId'] == $dv['proforma_header_id']) {
                        $discountVat[] = ['profId'   => $head['profId'], 'profCode' => $head['profCode'], 'sono' => $head['so_no'],
                                        'delivery' => $head['delivery'], 'customer' => $head['l_acroname'], 'pono' => $head['po_no'],
                                        'discId'   => $dv['discount_id'], 'discName' => $dv['discount'], 'amount' => $dv['total_discount']];
                    }                        
                }
            }    

            $uniqueVatDiscName = unique_multidim_array($discountVat,'discName');
            foreach($uniqueVatDiscName as $unique)
            {   
                $amount = 0;
                foreach($discountVat as $v)
                {
                    if($unique['discName'] == $v['discName']){
                        $amount += $v['amount'] ;
                    }
                }
                $discVatSummary[] = ['name' => 'Total '.$unique['discName'] . ' :', 'amount' => $amount ];
            }

            $itemCodeToFind  = "";
            $disc1      = 0.00;
            $disc2      = 0.00;
            $disc3      = 0.00;
            $disc4      = 0.00;

            foreach($getCRFPROFPI as $get2){
                
                foreach( $flatProfLineDetails as $find1 ) // pangitaon sa PI ang items nga naa sa proforma
                {
                    if($get2['proforma_header_id'] == $find1['profId']){
                    
                        $query = $this->db->select('i.id,i.itemcode_loc,i.item_division,i.item_department_code,i.item_group_code,ph.pi_head_id,ph.pi_no,ph.posting_date, ph.po_header_id,
                                                    pi.item_code,pi.description,pi.uom,pi.qty,pi.direct_unit_cost,pi.amt_including_vat')
                                        ->from('purchase_invoice_line pi')
                                        ->join('purchase_invoice_header ph','ph.pi_head_id = pi.pi_head_id', 'inner')
                                        ->join('crf_psi_pi cpp', 'cpp.pi_head_id = ph.pi_head_id','inner')
                                        ->join('items i','i.itemcode_loc = pi.item_code', 'left')
                                        ->where('ph.pi_head_id',$get2['pi_head_id'])
                                        ->where('pi.item_code',$find1['itemcode_loc'])
                                        ->where('cpp.crf_id', $get2['crf_id'])
                                        ->where('cpp.proforma_header_id', $get2['proforma_header_id'])
                                        ->where('cpp.pi_head_id', $get2['pi_head_id'])                                        
                                        ->order_by('pi.item_code') 
                                        ->get();
                            if($query->num_rows() > 0){
                                array_push($foundItemsInPiFromProforma, $query->result_array() ); 
                            } else {
                                if($getHasDeal == "1"){
                                    foreach($getDeals as $deal1)/* vendor deals   */ 
                                    { 
                                        if($deal1['type'] == "Item Department"){
                                            $itemCodeToFind = $find1['item_department_code'];
                                        } else if($deal1['type'] == "Item"){
                                            $itemCodeToFind = $find1['itemcode_loc'];
                                        } else if($deal1['type'] == "Item Group"){
                                            $itemCodeToFind = $find1['item_group_code'];
                                        }
                                        if($deal1['number'] == $itemCodeToFind ){
                                            if($getDiscType == "PERCENTAGE"){
                                                $disc1 = 1.00 - $deal1['disc_1'] * 0.01 ;
                                            } else if($getDiscType == "AMOUNT"){
                                                $disc1 = $deal1['discount_amount'];
                                            }
                                            $disc2 = 1.00 - $deal1['disc_2'] * 0.01 ;
                                            $disc3 = 1.00 - $deal1['disc_3'] * 0.01 ;
                                            $disc4 = 1.00 - $deal1['disc_4'] * 0.01 ;
                                        }
                                    }
                                }
                                
                                $notFoundItemsFromProforma[] = ['profId' => $find1['profId'],'profCode' => $find1['profCode'], 'piId'  => $get2['pi_head_id'],
                                                                'poId'   => $find1['poId'],  'id'       => $find1['id'],       'item'  => $find1['item'],
                                                                'idesc'  => $find1['idesc'], 'qty'      => $find1['qty'],      'uom'   => $find1['uom'],     
                                                                'price'  => $find1['price'], 'amount'   => $find1['amount'],
                                                                'disc1'  => $disc1,'disc2' => $disc2, 'disc3' => $disc3, 'disc4' => $disc4 ];                
                            }
                    }
                }

                $prevProf2 = $get2['proforma_header_id'];                
            }

            foreach($getCRFPROFPI as $get3){
                foreach($flatPiLineDetails as $find2) //pangitaon sa proforma ang naa sa PI
                {
                    if($get3['pi_head_id'] == $find2['piId']){
                        $query = $this->db->select('i.id,pl.itemcode_loc,i.item_division,i.item_department_code,i.item_group_code,pf.proforma_header_id,pf.proforma_code,pf.po_header_id,
                                                    pl.item_code,pl.description,pl.qty,pl.uom,pl.price,pl.amount')
                                        ->from('proforma_line pl')
                                        ->join('proforma_header pf','pf.proforma_header_id = pl.proforma_header_id', 'left')
                                        ->join('crf_psi_pi cpp', 'cpp.proforma_header_id = pf.proforma_header_id','inner')
                                        ->join('items i','i.itemcode_sup = pl.item_code','left')
                                        ->where('pl.itemcode_loc',$find2['item'])
                                        ->where('cpp.crf_id', $get3['crf_id'])
                                        ->where('cpp.proforma_header_id', $get3['proforma_header_id'])
                                        ->where('cpp.pi_head_id', $get3['pi_head_id'])
                                        ->order_by('pl.itemcode_loc') 
                                        ->get();                                    
                                    

                        if($query->num_rows() > 0){   
                            array_push($foundItemsInProfFromPi, $query->result_array() ); 
                            
                        } else {      
                            if($getHasDeal == "1"){
                                foreach($getDeals as $deal2)/* vendor deals   */ 
                                { 
                                    if($deal2['type'] == "Item Department"){
                                        $itemCodeToFind = $find2['item_department_code'];
                                    } else if($deal2['type'] == "Item"){
                                        $itemCodeToFind = $find2['itemcode_loc'];
                                    } else if($deal2['type'] == "Item Group"){
                                        $itemCodeToFind = $find2['item_group_code'];
                                    }
                                    if($deal2['number'] == $itemCodeToFind ){
                                        if($getDiscType == "PERCENTAGE"){
                                            $disc1 = 1.00 - $deal2['disc_1'] * 0.01 ;
                                            $disc2 = 1.00 - $deal2['disc_2'] * 0.01 ;
                                            $disc3 = 1.00 - $deal2['disc_3'] * 0.01 ;
                                            $disc4 = 1.00 - $deal2['disc_4'] * 0.01 ;
                                        } else if($getDiscType == "AMOUNT"){
                                            $disc1 = $deal2['discount_amount'];                            
                                        }     
                                    }
                                }
                            }
                            $notFoundItemsFromPi[] = ['piId'   => $find2['piId'],  'piNo'   => $find2['piNo'], 'piDate'  => $find2['piDate'],   
                                                    'profId' => $get3['proforma_header_id'],'poId'   => $find2['poId'],   'id'    => $find2['id'],   
                                                    'item'   => $find2['item'],'idesc'    => $find2['idesc'],  'uom'   => $find2['uom'],  
                                                    'qty'    => $find2['qty'],   'direct' => $find2['direct'], 'amount'=> $find2['amount'],
                                                    'disc1'  => $disc1,'disc2' =>$disc2, 'disc3' => $disc3,  'disc4' => $disc4 ]  ;
                            
                        }
                    }
                }

            }

            // dump($foundItemsInProfFromPi);
            // dump($foundItemsInPiFromProforma);
            // die();
    
            // dump($flatPiItems);
            // die();
            if(!empty($foundItemsInPiFromProforma)){
                $flatPiItems        = call_user_func_array('array_merge', $foundItemsInPiFromProforma);
            }
            if(!empty($foundItemsInProfFromPi)){
                $flatProfItems      = call_user_func_array('array_merge', $foundItemsInProfFromPi);
            }
            
          

            #region kuhaon sa array ang items nga naa sa notfound array if ang items naa sa flat arrays 
            if(!empty($notFoundItemsFromProforma)){
                foreach($notFoundItemsFromProforma as $key=>$notFound3){
                    foreach($flatProfItems as $flatProf1){
                        if($notFound3['profId'] == $flatProf1['proforma_header_id'] && $notFound3['item'] == $flatProf1['item_code']){
                           unset($notFoundItemsFromProforma[$key]);
                        }
                    }
                }
            }

            if(!empty($notFoundItemsFromPi)){
                foreach($notFoundItemsFromPi as $key=>$notFound4){
                    foreach($flatPiItems as $flatPi1){
                        if($notFound4['piId'] == $flatPi1['pi_head_id'] && $notFound4['item'] == $flatPi1['item_code']){
                            unset($notFoundItemsFromPi[$key]);
                        }
                    }
                }
            }
            #endregion

            if(!empty($flatPiItems) && !empty($flatProfItems)){
                foreach($getCRFPROFPI as $get4){
                    foreach($flatPiItems as $a) {
                        foreach($flatProfItems as $b){
                            if( $get4['pi_head_id'] == $a['pi_head_id'] && $get4['proforma_header_id'] == $b['proforma_header_id'] && $a['id'] == $b['id']){
                                if($getHasDeal == "1"){
                                    foreach($getDeals as $deals)/* vendor deals   */ 
                                    {
                                        if($deals['type'] == "Item Department"){
                                            $itemCodeToFind = $a['item_department_code'];
                                        } else if($deals['type'] == "Item"){
                                            $itemCodeToFind = $a['itemcode_loc'];
                                        } else if($deals['type'] == "Item Group"){
                                            $itemCodeToFind = $a['item_group_code'];
                                        }
                                        if($deals['number'] == $itemCodeToFind ){
                                            if($getDiscType == "PERCENTAGE"){
                                                $disc1 = 1.00 - $deals['disc_1'] * 0.01 ;
                                            } else if($getDiscType == "AMOUNT"){
                                                $disc1 = $deals['discount_amount'];                            
                                            }                                
                                            $disc2 = 1.00 - $deals['disc_2'] * 0.01 ;
                                            $disc3 = 1.00 - $deals['disc_3'] * 0.01 ;
                                            $disc4 = 1.00 - $deals['disc_4'] * 0.01 ;
                                        }
                                    }
                                }
                                $profPiMerge[] = [ 'itemId'   =>$a['id'],         'poId'    =>$a['po_header_id'], 'profCode'=>$b            ['proforma_code'],'profItem' =>$b['item_code'],
                                                   'profDesc' =>$b['description'],'profQty' =>$b['qty'],          'profUom' =>$b['uom'],          'profPrice'=>$b['price'],
                                                   'profAmt'  =>$b['amount'],     'piId'    =>$a['pi_head_id'],   'piNo'    =>$a['pi_no'],        'piItem'   =>$a['item_code'], 
                                                   'piDate'   =>$a['posting_date'],'piDesc' =>$a['description'],  'piQty'   =>$a['qty'],          'piUom'    =>$a['uom'],
                                                   'piPrice'  =>$a['direct_unit_cost'], 'piAmt'   =>$a['amt_including_vat'], 'disc1' => $disc1,'disc2' =>$disc2, 'disc3' => $disc3, 'disc4' => $disc4,
                                                   'partiallyServed'   => ($a['qty'] > 0 &&  $a['qty'] < $b['qty']  ) ? 1 : 0,
                                                   'excess'            => ($a['qty'] > $b['qty']) ? 1 : 0, 
                                                   'fullyServed'       => ( $a['qty'] > 0 && $a['qty'] == $b['qty']) ? 1 : 0 ];
                            }
                        }
                    }            
                }

                // dump($profPiMerge);
                // die();

                if(!empty($cmHead) && !empty($flatCmLineDetails)){
                    foreach($cmHead as $ch)
                    {
                        foreach($flatCmLineDetails as $cml)
                        {      
                            if(isset($ch)){
                                if($ch['cm_head_id'] == $cml['cm_head_id']){
                                    if($getHasDeal == "1"){
                                        foreach($getDeals as $deal3)/* vendor deals   */ 
                                        {
                                            if($deal3['type'] == "Item Department"){
                                                $itemCodeToFind = $cml['item_department_code'];
                                            } else if($deal3['type'] == "Item"){
                                                $itemCodeToFind = $cml['itemcode_loc'];
                                            } else if($deal3['type'] == "Item Group"){
                                                $itemCodeToFind = $cml['item_group_code'];
                                            }
                                            if($deal3['number'] == $itemCodeToFind ){
                                                if($getDiscType == "PERCENTAGE"){
                                                    $disc1 = 1.00 - $deal3['disc_1'] * 0.01 ;
                                                } else if($getDiscType == "AMOUNT"){
                                                    $disc1 = $deal3['discount_amount'];                            
                                                }                                        
                                                $disc2 = 1.00 - $deal3['disc_2'] * 0.01 ;
                                                $disc3 = 1.00 - $deal3['disc_3'] * 0.01 ;
                                                $disc4 = 1.00 - $deal3['disc_4'] * 0.01 ;
                                            }
                                        }
                                    }
                                    $cmLineMerge[] = ['cmId' => $ch['cm_head_id'], 'cmNo' => $ch['cm_no'], 'date' => $ch['posting_date'], 'item' => $cml['item_code'],
                                                    'desc' => $cml['description'],'uom' => $cml['uom'],'qty' => $cml['qty'], 'price' => $cml['direct_unit_cost'],
                                                    'disc1'=> $disc1, 'disc2' => $disc2, 'disc3' => $disc3, 'disc4' => $disc4, 'piNo' => $cml['pi_no']  ];
                                }
                            }
                        }
                    }
                }

                $fullyServed       = array_search(1, array_column($profPiMerge, 'fullyServed'));
                $partiallyServed   = array_search(1, array_column($profPiMerge, 'partiallyServed'));
                $excess            = array_search(1, array_column($profPiMerge, 'excess')); 

                $sopDeduction      = $this->proformavspi_model->getSopDeduction($fetch_data['crfId']); 
                $noData            = false;
            } else {
                $noData            = true;
            }

        } else {
            $noData            = true;
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $error = array('action' => 'Matching Pro-forma vs PI', 'error_msg' => $this->db->error()); //Log error message to `error_log` table
            $this->db->insert('error_log', $error);

            $msg = ['info' => 'incomplete', 'message' => 'Matching is incomplete!'];
        } else {

            if(!$noData){
                // for history
                $transaction = ['tr_no'         => $documentNo,
                                'tr_date'       => date("F d, Y - h:i:s A"),
                                'crf_id'        => $fetch_data['crfId'],
                                'supplier_id'   => $fetch_data['supId'],
                                'customer_code' => $fetch_data['cusId'],
                                'user_id'       => $this->session->userdata('user_id'),
                                'filename'      => '' ];
                $this->db->insert('profvpi_transaction', $transaction);
                $transactionId = $this->db->insert_id();
                // for history
                if($type == "pdf"){
                    $generate  = $this->generateProformaVsPi($transactionId,$fetch_data, $getProfHead, $discountVat, $discVatSummary, $profPiMerge, $fullyServed, $partiallyServed, 
                                                                $excess, $sopDeduction, $notFoundItemsFromProforma, $notFoundItemsFromPi,$cmHead,$cmLineMerge,$flatFreeItems);
                } else if($type == "excel"){
                    $generate      = $this->generateProformaVsPiExcel($transactionId,$fetch_data, $getProfHead, $discountVat, $discVatSummary, $profPiMerge, $fullyServed, $partiallyServed, 
                                                                $excess, $sopDeduction, $notFoundItemsFromProforma, $notFoundItemsFromPi,$cmHead,$cmLineMerge,$flatFreeItems);
                }

                $msg = ['info' => 'success', 'message' => 'Matching Pro-forma vs PI is complete!', 'file' => $generate];
            } else {
                $msg = ['info' => 'no-data', 'message' => 'No Data To Match! Tag PSI-PI correctly!'];
            }
           

        }

        JSONResponse($msg);
    }

    public function matchPsivsPi()
    {
        $fetch_data         = $this->input->post(NULL, TRUE); 
        $withPairsProfPo    = array();
        $withPairsPiPo      = array();
        $wayParesPoProf     = array();
        $wayParesPoPi       = array();
        $itemsinPsiNotINPi  = array();
        $itemsinPsiInPi     = array();
        $itemsinPINotInPsi  = array();
        $itemsinPIInPsi     = array();
        $profLineDetails    = array();
        $piLineDetails      = array();
        $profPiMerge        = array();
        $flatPiLineDetails  = array();
        $flatProfLineDetails= array();
        $piItemsOfUnpairedPo= array();


        $poProfNoPairs      = array();
        $poPiNoPairs        = array();

        $getProf            = $this->proformavspi_model->getProformaInCrf($fetch_data['crfId']);
        $getPi              = $this->proformavspi_model->getPiInCrf($fetch_data['crfId']);
        $getHasDeal         = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['has_deal']; 
        $getDiscType        = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['disc_type'];  

        if($getHasDeal == "1"){
            $getDeals        = $this->proformavspi_model->getDeals($fetch_data['dealId']);
            $getNoOfDisc     = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['number_of_discount'];
        }
        
        foreach($getProf as $prof)
        {    
            $key = array_search($prof['po_header_id'], array_column($getPi,'po_header_id'));
            if ($key === false) {
                $wayParesPoProf[] = array('proforma_header_id' => $prof['proforma_header_id'], 'po_header_id' => $prof['po_header_id'] );
                $poProfNoPairs[]  = $prof['po_no'];
            }  else {
                $withPairsProfPo[] = array('proforma_header_id' => $prof['proforma_header_id'], 'po_header_id' => $prof['po_header_id'] );
            }        
        } 

        foreach($getPi as $pi)
        {
            $key = array_search($pi['po_header_id'], array_column($getProf,'po_header_id'));
            if($key === false){
                $wayParesPoPi[] = array('pi_head_id' => $pi['pi_head_id'], 'po_header_id' => $pi['po_header_id'] );
                $poPiNoPairs[]  = $pi['po_no'];
            } else {
                $withPairsPiPo[] = array('pi_head_id' => $pi['pi_head_id'], 'po_header_id' => $pi['po_header_id'] );
            }
        } 

        if( !empty($withPairsProfPo) && !empty($withPairsPiPo) ){
            foreach( $withPairsProfPo as $w1){
                foreach( $withPairsPiPo as $w2) {
                    if( $w1['po_header_id'] == $w2['po_header_id']){
                        $itemsinPsiNotINPi[]  = $this->proformavspi_model->getItemsinPsiNotInPi($w1['proforma_header_id'], $w2['pi_head_id']);
                        $itemsinPsiInPi[]     = $this->proformavspi_model->getItemsinPsiInPi($w1['proforma_header_id'], $w2['pi_head_id']);
                        $itemsinPINotInPsi[]  = $this->proformavspi_model->getItemsinPINotInPsi($w1['proforma_header_id'], $w2['pi_head_id']);
                        $itemsinPIInPsi[]     = $this->proformavspi_model->getItemsinPiInPsi($w1['proforma_header_id'], $w2['pi_head_id']);
                    }                      
                }
               
            }
        }
        if( !empty($itemsinPsiInPi)){
            $flatitemsinPsiInPi = call_user_func_array('array_merge', $itemsinPsiInPi);
            foreach( $flatitemsinPsiInPi as $itemsPsi){
                $profLineDetails[]      = $this->proformavspi_model->getProfLineDetails($itemsPsi['proforma_header_id'], $itemsPsi['itemcode_loc']);
            }
        }

        if( !empty($itemsinPIInPsi) ){
            $flatitemsinPIInPsi = call_user_func_array('array_merge', $itemsinPIInPsi);
            foreach( $flatitemsinPIInPsi as $itemsPi){
                $piLineDetails[]    = $this->proformavspi_model->getPiLineDetailss($itemsPi['pi_head_id'],$itemsPi['item_code']);
            }            
        }

        if( !empty($wayParesPoPi) ){
            foreach($wayParesPoPi as $way1){
                $piItemsOfUnpairedPo[] = $this->proformavspi_model->getItemsForUnpairedPoInPi($way1['pi_head_id']);
            }
            $flatpiItemsOfUnpairedPo = call_user_func_array('array_merge', $piItemsOfUnpairedPo);
        }
        // dump($getProf);
        // dump($getPi);
        // dump($withPairsProfPo);
        // dump($withPairsPiPo);
        // dump($piItemsOfUnpairedPo);
        // dump($wayParesPoProf);
        // dump($itemsinPIInPsi);
        // dump($itemsinPsiNotINPi);
        // dump($itemsinPsiInPi);
        // echo '-------------------';
        // dump($itemsinPINotInPsi);
        // dump($itemsinPIInPsi);
        // dump($piItemsOfUnpairedPo);
        // die();
        if( !empty($profLineDetails)){
            $flatProfLineDetails = call_user_func_array('array_merge', $profLineDetails);
        }
        if( !empty($piLineDetails)){
            $flatPiLineDetails = call_user_func_array('array_merge', $piLineDetails);
        }

        $disc1 = 0.00;
        $disc2 = 0.00;
        $disc3 = 0.00;
        $disc4 = 0.00;
       
        foreach( $flatProfLineDetails as $b){
            foreach($flatPiLineDetails as $a){
                if( $b['id'] == $a['id'] && $b['poId'] == $a['poId'] ){
                    if($getHasDeal == "1"){
                        foreach($getDeals as $deals){
                            if($deals['type'] == "Item Department"){
                                $itemCodeToFind = $a['item_department_code'];
                            } else if($deals['type'] == "Item"){
                                $itemCodeToFind = $a['itemcode_loc'];
                            } else if($deals['type'] == "Item Group"){
                                $itemCodeToFind = $a['item_group_code'];
                            }
                            if($deals['number'] == $itemCodeToFind ){
                                if($getDiscType == "PERCENTAGE"){
                                    $disc1 = 1.00 - $deals['disc_1'] * 0.01 ;
                                } else if($getDiscType == "AMOUNT"){
                                    $disc1 = $deals['discount_amount'];                            
                                }       
                                if( $getNoOfDisc == "2"){
                                    $disc2 = 1.00 - $deals['disc_2'] * 0.01 ;                               
                                } else if(  $getNoOfDisc == "3" ){
                                    $disc2 = 1.00 - $deals['disc_2'] * 0.01 ;
                                    $disc3 = 1.00 - $deals['disc_3'] * 0.01 ;
                                } else if(  $getNoOfDisc == "4" ){
                                    $disc2 = 1.00 - $deals['disc_2'] * 0.01 ;
                                    $disc3 = 1.00 - $deals['disc_3'] * 0.01 ;
                                    $disc4 = 1.00 - $deals['disc_4'] * 0.01 ;
                                }   
                                
                                $profPiMerge[] = [ 'itemId'   =>$a['id'],         'poId' =>$a['poId'], 'profCode'=>$b['profCode'],'profItem' =>$b['item'],
                                                   'profDesc' =>$b['idesc'],   'profQty' =>$b['qty'],  'profUom' =>$b['uom'],     'profPrice'=>$b['price'],
                                                   'profAmt'  =>$b['amount'],     'piId' =>$a['piId'],   'piNo'  =>$a['piNo'],    'piItem'   =>$a['item'], 
                                                   'piDate'   =>$a['piDate'],   'piDesc' =>$a['idesc'],'piQty'   =>$a['qty'],     'piUom'    =>$a['uom'],
                                                   'piPrice'  =>$a['direct'],  'piAmt'   =>$a['amount'], 'disc1' =>$disc1,'disc2' =>$disc2, 'disc3' => $disc3, 'disc4' => $disc4,
                                                   'partiallyServed'   => ($a['qty'] > 0 &&  $a['qty'] < $b['qty']  ) ? 1 : 0,
                                                   'excess'            => ($a['qty'] > $b['qty']) ? 1 : 0, 
                                                   'fullyServed'       => ( $a['qty'] > 0 && $a['qty'] == $b['qty']) ? 1 : 0 ];
                            }
                        }
                    }
                }
            }
        }
        
        if( !empty($flatpiItemsOfUnpairedPo) ){
            if($getHasDeal == "1"){
                foreach($flatpiItemsOfUnpairedPo as $flat1){
                    foreach($getDeals as $deals){
                        if($deals['type'] == "Item Department"){
                            $itemCodeToFind = $flat1['item_department_code'];
                        } else if($deals['type'] == "Item"){
                            $itemCodeToFind = $flat1['itemcode_loc'];
                        } else if($deals['type'] == "Item Group"){
                            $itemCodeToFind = $flat1['item_group_code'];
                        }
                        if($deals['number'] == $itemCodeToFind ){
                            if($getDiscType == "PERCENTAGE"){
                                $disc1 = 1.00 - $deals['disc_1'] * 0.01 ;
                            } else if($getDiscType == "AMOUNT"){
                                $disc1 = $deals['discount_amount'];                            
                            }       
                            if( $getNoOfDisc == "2"){
                                $disc2 = 1.00 - $deals['disc_2'] * 0.01 ;                               
                            } else if(  $getNoOfDisc == "3" ){
                                $disc2 = 1.00 - $deals['disc_2'] * 0.01 ;
                                $disc3 = 1.00 - $deals['disc_3'] * 0.01 ;
                            } else if(  $getNoOfDisc == "4" ){
                                $disc2 = 1.00 - $deals['disc_2'] * 0.01 ;
                                $disc3 = 1.00 - $deals['disc_3'] * 0.01 ;
                                $disc4 = 1.00 - $deals['disc_4'] * 0.01 ;
                            }  
                            
                            $piItemsOfUnpairedPoMerge[] = [ 'itemId'   =>$flat1['id'],         'poId' =>$flat1['poId'], 'profCode'=>'',            'profItem' => '',
                                                            'profDesc' =>'',                'profQty' =>'0',            'profUom' => '',           'profPrice'=> '0',
                                                            'profAmt'  =>'0',                  'piId' =>$flat1['piId'],   'piNo'  =>$flat1['piNo'],'piItem'   =>$flat1['item'], 
                                                            'piDate'   =>$flat1['piDate'],   'piDesc' =>$flat1['idesc'],'piQty'   =>$flat1['qty'], 'piUom'    =>$flat1['uom'],
                                                            'piPrice'  =>$flat1['direct'],  'piAmt'   =>$flat1['amount'], 'disc1' =>$disc1,'disc2' =>$disc2, 'disc3' => $disc3, 'disc4' => $disc4,
                                                            'partiallyServed'   =>  0,
                                                            'excess'            =>  0, 
                                                            'fullyServed'       =>  0 ];
                        }
                    }
                }
            }
        }
        // dump($piItemsOfUnpairedPoMerge);
        // die();
        $file = $this->printMatching($profPiMerge, $piItemsOfUnpairedPoMerge, $fetch_data);
        $msg = ['info' => 'success', 'message' => 'Matching Pro-forma vs PI is complete!', 'file' => $file, 'wayParesPoProf' => $poProfNoPairs, 'wayParesPoPi' => $poPiNoPairs ];
        JSONResponse($msg);
  
    }

    private function printMatching($profPiMerge, $piItemsOfUnpairedPoMerge, $fetch_data)
    {
        $supplier             = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['supplier_name'];
        $getPricing           = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['pricing'];
        $getAmounting         = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['amounting'];
        $getDiscType          = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['disc_type'];
        $vat                  = $this->proformavspi_model->getVATData()['value'];

        $fullyServed       = array_search(1, array_column($profPiMerge, 'fullyServed'));
        $partiallyServed   = array_search(1, array_column($profPiMerge, 'partiallyServed'));
        $excess            = array_search(1, array_column($profPiMerge, 'excess'));    

        $fullyServedItemQty             = 0;
        $fullyServedTotalAmt            = 0;
        $fullyServedGrossAmt            = 0;
        $fullyServedDiscountedAmt       = 0;  
        $fullyReceivedItemQty           = 0;       
        $fullyReceivedNetAmount         = 0;
        $fullyGrossAmount               = 0;
        $fullyReceivedDiscountedAmt     = 0;  
        $fullyVarianceGrossAmnt         = 0;
        $fullyVarianceDiscountedAmt     = 0;
        $fullyServedReceivedVarianceQty = 0; 
        $fullyServedReceivedItemCount   = 0;

        // $additionalItemsServedTotalAmt  = 0;
        // $additionalItemsServedItemQty   = 0;
        // $additionalItemsServedGrossAmt  = 0;
        // $additionalItemsServedDiscountedAmt = 0;

        $additionalItemsGrossAmount              = 0;
        $additionalItemsReceivedDiscountedAmt    = 0;
        $additionalItemsReceivedNetAmount        = 0;
        $additionalItemsVarianceGrossAmnt        = 0;
        $additionalItemsVarianceDiscountedAmt    = 0;
        $additionalItemsServedReceivedVarianceQty= 0;
        $additionalItemsReceivedItemQty          = 0;
        $additionalItemsServedReceivedItemCount  = 0;

        $pdf = new FPDF('L', 'mm', array(594 , 841  )); 
        $pdf->AddPage();
        $pdf->setDisplayMode('fullpage');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setX(30);
        $pdf->Cell(0, 0, $supplier, 0, 0, 'L');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 10);
        $pdf->setX(30);
        $pdf->Cell(40, 0, 'PRO-FORMA SALES INVOICE vs PURCHASE INVOICE - VARIANCE REPORT', 0, 0, 'L');
        $pdf->Ln(10);


        // new template
        $pdf->setX(30);
        $pdf->Cell(40, 0, 'PRO-FORMA SALES INVOICE', 0, 0, 'L');
        $pdf->setX(423);
        $pdf->Cell(0, 0, 'PURCHASE INVOICE', 0, 0, 'L');
        $pdf->Ln(5);

        if($fullyServed !== false ){ //fully served/received
            $this->createTableColumnHeader('Fully-Served Item(s) :','Fully-Received Item(s) :','Variance',$pdf,'PI No'); 

            foreach( $profPiMerge as $full){
                if($full['fullyServed']  == 1){ 

                    if($getAmounting == "NETofVAT&Disc"){
                        $netAmount = $full['profAmt'];
                    } else if($getAmounting == "GROSSofVAT&Disc"){
                        if($getDiscType == "PERCENTAGE"){
                            $netAmount = netPrice($fetch_data['supId'],$getPricing,$full['profAmt'],$full['disc1'],$full['disc2'],$full['disc3'],$full['disc4'],$vat, $fetch_data['cusId']);
                        } else if($getDiscType == "AMOUNT"){
                            $price     = netPrice($fetch_data['supId'],$getPricing,$full['profPrice'],$full['disc1'],$full['disc2'],$full['disc3'],$full['disc4'],$vat, $fetch_data['cusId']);
                            $netAmount = $price * $full['profQty'];
                        }
                       
                    } else if($getAmounting == "NETofVATwDisc"){
                        $netAmount = netPrice($fetch_data['supId'],$getPricing,$full['profAmt'],$full['disc1'],$full['disc2'],$full['disc3'],$full['disc4'],$vat, $fetch_data['cusId']);
                    } else if($getAmounting == "NETofDiscwVAT"){
                        $price     = netPrice($fetch_data['supId'],$getPricing,$full['profPrice'],$full['disc1'],$full['disc2'],$full['disc3'],$full['disc4'],$vat,$fetch_data['cusId']);
                        $netAmount = $price * $full['profQty'];
                    }

                    $netPrice          = netPrice($fetch_data['supId'],$getPricing,$full['profPrice'],$full['disc1'],$full['disc2'],$full['disc3'],$full['disc4'],$vat, $fetch_data['cusId']);   
                    $netPricePi        = netPricePi($fetch_data['supId'],$full['piPrice'],$full['disc1'],$full['disc2'],$full['disc3'],$full['disc4'],$vat);   
                    $discountedPricePi = discountedPricePi($fetch_data['supId'],$full['piPrice'],$full['disc1'],$full['disc2'],$full['disc3'],$full['disc4']);          
                    if($getPricing == "NETofVATwDisc"){
                        $grossPrice      = backToGross($fetch_data['supId'],$full['profPrice'],$full['disc1'],$full['disc2'],$full['disc3'],$full['disc4'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$full['disc1'],$full['disc2'],$full['disc3'],$full['disc4'],$vat);
                    } else if($getPricing == "GROSSofVAT&Disc"){
                        $grossPrice      = backToGross($fetch_data['supId'],$full['profPrice'],$full['disc1'],$full['disc2'],$full['disc3'],$full['disc4'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$full['profPrice'],$full['disc1'],$full['disc2'],$full['disc3'],$full['disc4'],$vat);
                    } else if($getPricing == "GROSSofDiscwoVAT"){
                        $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$full['disc1'],$full['disc2'],$full['disc3'],$full['disc4'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$full['disc1'],$full['disc2'],$full['disc3'],$full['disc4'],$vat);
                    } else { //NETofVAT&Disc
                        $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$full['disc1'],$full['disc2'],$full['disc3'],$full['disc4'],$vat);
                        $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$full['disc1'],$full['disc2'],$full['disc3'],$full['disc4'],$vat);
                    }

                    $pdf->setX(30);
                    $pdf->cell(80, 6, $full['profCode'], 'LBR', 0, 'C');
                    $pdf->cell(20, 6, $full['profItem'], 'BR', 0, 'C', 0);
                    $pdf->cell(82, 6, $full['profDesc'], 'BR', 0, 'L', 0);
                    $pdf->cell(15, 6, $full['profUom'], 'BR', 0, 'C', 0);
                    $pdf->cell(12, 6, $full['profQty'], 'BR', 0, 'C', 0);
                    $pdf->cell(27, 6, number_format($netPrice, 2), 'BR', 0, 'R', 0); //net price
                    $pdf->cell(30, 6, number_format($discountedPrice, 2), 'BR', 0, 'R', 0); //discounted price
                    $pdf->cell(30, 6, number_format($grossPrice, 2), 'BR', 0, 'R', 0);  //gross price
                    $pdf->cell(32, 6, number_format($netAmount, 2), 'BR', 0, 'R', 0);  //net amount
                    $pdf->cell(32, 6, number_format($discountedPrice * $full['profQty'] ,2), 'BR', 0, 'R', 0);  //discounted amount
                    $pdf->cell(32, 6, number_format($grossPrice * $full['profQty'], 2), 'BR', 0, 'R', 0);  //gross amount
                    
                    $fullyServedTotalAmt += $netAmount;
                    $fullyServedItemQty  += $full['profQty'];
                    $fullyServedGrossAmt += $grossPrice * $full['profQty']  ;
                    $fullyServedDiscountedAmt += $discountedPrice * $full['profQty'] ;

                    $pdf->cell(2, 6, " ", 0, 0, 'L');

                    $pdf->cell(35, 6, $full['piNo'], 'LBR', 0, 'C');
                    $pdf->cell(20, 6, $full['piDate'], 'BR', 0, 'C', 0);
                    $pdf->cell(20, 6, $full['piItem'], 'BR', 0, 'C', 0);
                    $pdf->cell(82, 6, $full['piDesc'], 'BR', 0, 'L', 0);
                    $pdf->cell(15, 6, $full['piUom'], 'BR', 0, 'C', 0);
                    $pdf->cell(12, 6, $full['piQty'], 'BR', 0, 'C', 0);
                    $pdf->cell(18, 6, number_format($full['piPrice'], 2), 'BR', 0, 'R', 0); //gross price
                    $pdf->cell(30, 6, number_format($discountedPricePi,2), 'BR', 0, 'R', 0); //discounted price
                    $pdf->cell(30, 6, number_format($netPricePi,2), 'BR', 0, 'R', 0); //net price
                    $pdf->cell(32, 6, number_format($full['piPrice'] * $full['piQty'], 2), 'BR', 0, 'R', 0); //gross amount
                    $pdf->cell(32, 6, number_format($discountedPricePi * $full['piQty'],2), 'BR', 0, 'R', 0); //discounted amount
                    $pdf->cell(32, 6, number_format($netPricePi * $full['piQty'] ,2), 'BR', 0, 'R', 0); //net amount
                    $pdf->cell(18, 6,  $full['profQty'] -  $full['piQty'] , 'BR', 0, 'C', 0); //variance qty
                    $pdf->cell(32, 6, number_format( ($discountedPrice * $full['profQty']) - ($discountedPricePi * $full['piQty']),2), 'BR', 0, 'R', 0); //variance discounted amount
   
                    $fullyGrossAmount               += round($full['piPrice'] * $full['piQty'] , 2);   
                    $fullyReceivedDiscountedAmt     += $discountedPricePi * $full['piQty'];
                    $fullyReceivedNetAmount         += $netPricePi * $full['piQty'];
                    $fullyVarianceGrossAmnt         += ($grossPrice * $full['profQty'] ) - ($full['piPrice'] * $full['piQty']) ;        
                    $fullyVarianceDiscountedAmt     += ($discountedPrice * $full['profQty']) - ($discountedPricePi * $full['piQty']);     
                    $fullyServedReceivedVarianceQty += $full['profQty'] -  $full['piQty'] ;
                    $fullyReceivedItemQty           += $full['piQty']; 
                    $fullyServedReceivedItemCount ++;
                    $pdf->Ln();
                }
            }
            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->setX(30);
            $pdf->cell(182, 5, "FULLY-SERVED TOTAL :", 'LB', 0, 'L');
            $pdf->cell(27, 5, $fullyServedItemQty, 'B', 0, 'R'); 
            $pdf->cell(119, 5, "P " . number_format($fullyServedTotalAmt, 2), 'B', 0, 'R');
            $pdf->cell(32, 5, "P " . number_format($fullyServedDiscountedAmt,2), 'B', 0, 'R');
            $pdf->cell(32, 5, "P " . number_format($fullyServedGrossAmt, 2), 'BR', 0, 'R');

            $pdf->cell(2, 5, "", 0, 0, 'L');

            $pdf->cell(157, 5, "FULLY-RECEIVED TOTAL :", 'LB', 0, 'L');
            $pdf->cell(27, 5, $fullyReceivedItemQty, 'B', 0, 'R'); 
            $pdf->cell(110, 5, "P ". number_format($fullyGrossAmount, 2), 'B', 0, 'R'); //gross amount
            $pdf->cell(32, 5, "P ". number_format($fullyReceivedDiscountedAmt,2) , 'B', 0, 'R'); //discounted amount
            $pdf->cell(32, 5, "P ". number_format($fullyReceivedNetAmount,2) , 'B', 0, 'R'); //net amount
            $pdf->cell(18, 5, $fullyServedReceivedVarianceQty , 'B', 0, 'C'); //qty variance
            $pdf->cell(32, 5, "P ". number_format($fullyVarianceDiscountedAmt,2) , 'BR', 0, 'R');  //variance discounted amount
 
            $pdf->Ln();
            $pdf->setX(30);
            $pdf->cell(25, 5, "Fully-Served/Received Item Count : " . $fullyServedReceivedItemCount, '', 0, 'L');
            $pdf->Ln(10);
        }

        if( !empty($piItemsOfUnpairedPoMerge) ){
            $this->createTableColumnHeader('','Item(s) with Different PO :','Variance',$pdf,'PI No');
            foreach( $piItemsOfUnpairedPoMerge as $loop1){
                if($getAmounting == "NETofVAT&Disc"){
                    $netAmount = $loop1['profAmt'];
                } else if($getAmounting == "GROSSofVAT&Disc"){
                    if($getDiscType == "PERCENTAGE"){
                        $netAmount = netPrice($fetch_data['supId'],$getPricing,$loop1['profAmt'],$loop1['disc1'],$loop1['disc2'],$loop1['disc3'],$loop1['disc4'],$vat, $fetch_data['cusId']);
                    } else if($getDiscType == "AMOUNT"){
                        $price     = netPrice($fetch_data['supId'],$getPricing,$loop1['profPrice'],$loop1['disc1'],$loop1['disc2'],$loop1['disc3'],$loop1['disc4'],$vat, $fetch_data['cusId']);
                        $netAmount = $price * $loop1['profQty'];
                    }
                   
                } else if($getAmounting == "NETofVATwDisc"){
                    $netAmount = netPrice($fetch_data['supId'],$getPricing,$loop1['profAmt'],$loop1['disc1'],$loop1['disc2'],$loop1['disc3'],$loop1['disc4'],$vat, $fetch_data['cusId']);
                } else if($getAmounting == "NETofDiscwVAT"){
                    $price     = netPrice($fetch_data['supId'],$getPricing,$loop1['profPrice'],$loop1['disc1'],$loop1['disc2'],$loop1['disc3'],$loop1['disc4'],$vat, $fetch_data['cusId']);
                    $netAmount = $price * $loop1['profQty'];
                }

                $netPrice          = netPrice($fetch_data['supId'],$getPricing,$loop1['profPrice'],$loop1['disc1'],$loop1['disc2'],$loop1['disc3'],$loop1['disc4'],$vat, $fetch_data['cusId']);   
                $netPricePi        = netPricePi($fetch_data['supId'],$loop1['piPrice'],$loop1['disc1'],$loop1['disc2'],$loop1['disc3'],$loop1['disc4'],$vat);   
                $discountedPricePi = discountedPricePi($fetch_data['supId'],$loop1['piPrice'],$loop1['disc1'],$loop1['disc2'],$loop1['disc3'],$loop1['disc4']);          
                if($getPricing == "NETofVATwDisc"){
                    $grossPrice      = backToGross($fetch_data['supId'],$loop1['profPrice'],$loop1['disc1'],$loop1['disc2'],$loop1['disc3'],$loop1['disc4'],$vat);
                    $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$loop1['disc1'],$loop1['disc2'],$loop1['disc3'],$loop1['disc4'],$vat);
                } else if($getPricing == "GROSSofVAT&Disc"){
                    $grossPrice      = backToGross($fetch_data['supId'],$loop1['profPrice'],$loop1['disc1'],$loop1['disc2'],$loop1['disc3'],$loop1['disc4'],$vat);
                    $discountedPrice = discountedPrice($fetch_data['supId'],$loop1['profPrice'],$loop1['disc1'],$loop1['disc2'],$loop1['disc3'],$loop1['disc4'],$vat);
                } else if($getPricing == "GROSSofDiscwoVAT"){
                    $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$loop1['disc1'],$loop1['disc2'],$loop1['disc3'],$loop1['disc4'],$vat);
                    $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$loop1['disc1'],$loop1['disc2'],$loop1['disc3'],$loop1['disc4'],$vat);
                } else { //NETofVAT&Disc
                    $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$loop1['disc1'],$loop1['disc2'],$loop1['disc3'],$loop1['disc4'],$vat);
                    $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$loop1['disc1'],$loop1['disc2'],$loop1['disc3'],$loop1['disc4'],$vat);
                }

                $pdf->setX(30);
                $pdf->cell(80, 6, $loop1['profCode'], 'LBR', 0, 'C');
                $pdf->cell(20, 6, $loop1['profItem'], 'BR', 0, 'C', 0);
                $pdf->cell(82, 6, $loop1['profDesc'], 'BR', 0, 'L', 0);
                $pdf->cell(15, 6, $loop1['profUom'], 'BR', 0, 'C', 0);
                $pdf->cell(12, 6, $loop1['profQty'], 'BR', 0, 'C', 0);
                $pdf->cell(27, 6, number_format($netPrice, 2), 'BR', 0, 'R', 0); //net price
                $pdf->cell(30, 6, number_format($discountedPrice, 2), 'BR', 0, 'R', 0); //discounted price
                $pdf->cell(30, 6, number_format($grossPrice, 2), 'BR', 0, 'R', 0);  //gross price
                $pdf->cell(32, 6, number_format($netAmount, 2), 'BR', 0, 'R', 0);  //net amount
                $pdf->cell(32, 6, number_format($discountedPrice * $loop1['profQty'] ,2), 'BR', 0, 'R', 0);  //discounted amount
                $pdf->cell(32, 6, number_format($grossPrice * $loop1['profQty'], 2), 'BR', 0, 'R', 0);  //gross amount
                
                // $additionalItemsServedTotalAmt += $netAmount;
                // $additionalItemsServedItemQty  += $loop1['profQty'];
                // $additionalItemsServedGrossAmt += $grossPrice * $loop1['profQty']  ;
                // $additionalItemsServedDiscountedAmt += $discountedPrice * $loop1['profQty'] ;

                $pdf->cell(2, 6, " ", 0, 0, 'L');

                $pdf->cell(35, 6, $loop1['piNo'], 'LBR', 0, 'C');
                $pdf->cell(20, 6, $loop1['piDate'], 'BR', 0, 'C', 0);
                $pdf->cell(20, 6, $loop1['piItem'], 'BR', 0, 'C', 0);
                $pdf->cell(82, 6, $loop1['piDesc'], 'BR', 0, 'L', 0);
                $pdf->cell(15, 6, $loop1['piUom'], 'BR', 0, 'C', 0);
                $pdf->cell(12, 6, $loop1['piQty'], 'BR', 0, 'C', 0);
                $pdf->cell(18, 6, number_format($loop1['piPrice'], 2), 'BR', 0, 'R', 0); //gross price
                $pdf->cell(30, 6, number_format($discountedPricePi,2), 'BR', 0, 'R', 0); //discounted price
                $pdf->cell(30, 6, number_format($netPricePi,2), 'BR', 0, 'R', 0); //net price
                $pdf->cell(32, 6, number_format($loop1['piPrice'] * $loop1['piQty'], 2), 'BR', 0, 'R', 0); //gross amount
                $pdf->cell(32, 6, number_format($discountedPricePi * $loop1['piQty'],2), 'BR', 0, 'R', 0); //discounted amount
                $pdf->cell(32, 6, number_format($netPricePi * $loop1['piQty'] ,2), 'BR', 0, 'R', 0); //net amount
                $pdf->cell(18, 6,  $loop1['profQty'] -  $loop1['piQty'] , 'BR', 0, 'C', 0); //variance qty
                $pdf->cell(32, 6, number_format( ($discountedPrice * $loop1['profQty']) - ($discountedPricePi * $loop1['piQty']),2), 'BR', 0, 'R', 0); //variance discounted amount

                $additionalItemsGrossAmount               += round($loop1['piPrice'] * $loop1['piQty'] , 2);   
                $additionalItemsReceivedDiscountedAmt     += $discountedPricePi * $loop1['piQty'];
                $additionalItemsReceivedNetAmount         += $netPricePi * $loop1['piQty'];
                $additionalItemsVarianceGrossAmnt         += ($grossPrice * $loop1['profQty'] ) - ($loop1['piPrice'] * $loop1['piQty']) ;        
                $additionalItemsVarianceDiscountedAmt     += ($discountedPrice * $loop1['profQty']) - ($discountedPricePi * $loop1['piQty']);     
                $additionalItemsServedReceivedVarianceQty += $loop1['profQty'] -  $loop1['piQty'] ;
                $additionalItemsReceivedItemQty           += $loop1['piQty']; 
                $additionalItemsServedReceivedItemCount ++;
                $pdf->Ln();
            }

            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->setX(30);
            $pdf->cell(182, 5, "FULLY-SERVED TOTAL :", 'LB', 0, 'L');
            $pdf->cell(27, 5, 0, 'B', '0', 'R'); 
            $pdf->cell(119, 5, "P " . '0', 'B', 0, 'R');
            $pdf->cell(32, 5, "P " . '0', 'B', 0, 'R');
            $pdf->cell(32, 5, "P " . '0', 'BR', 0, 'R');

            $pdf->cell(2, 5, "", 0, 0, 'L');

            $pdf->cell(157, 5, "ITEM(s) WITH DIFFERENT PO RECEIVED TOTAL :", 'LB', 0, 'L');
            $pdf->cell(27, 5, $additionalItemsReceivedItemQty, 'B', 0, 'R'); 
            $pdf->cell(110, 5, "P ". number_format($additionalItemsGrossAmount, 2), 'B', 0, 'R'); //gross amount
            $pdf->cell(32, 5, "P ". number_format($additionalItemsReceivedDiscountedAmt,2) , 'B', 0, 'R'); //discounted amount
            $pdf->cell(32, 5, "P ". number_format($additionalItemsReceivedNetAmount,2) , 'B', 0, 'R'); //net amount
            $pdf->cell(18, 5, $additionalItemsServedReceivedVarianceQty , 'B', 0, 'C'); //qty variance
            $pdf->cell(32, 5, "P ". number_format($additionalItemsVarianceDiscountedAmt,2) , 'BR', 0, 'R');  //variance discounted amount
 
            $pdf->Ln();
            $pdf->setX(30);
            $pdf->cell(25, 5, "Item(s) with Different PO Received Count : " . $additionalItemsServedReceivedItemCount, '', 0, 'L');
            $pdf->Ln(10);
        }
        $supAcroname = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['acroname'];
        $cusAcroname = $this->proformavspi_model->getCustomerData($fetch_data['cusId'],'customer_code')['l_acroname'];
        $fileName    = $supAcroname.'-'. $cusAcroname.' - PSIvsPI'  . time() . '.pdf';
        $pdf->Output('files/Reports/ProformaVsPi/' . $fileName, 'F');

        return $fileName;
    }

    // MATCHING 2
    public function matching2()
    {
        $fetch_data           = $this->input->post(NULL, TRUE); 
        $type                 = $fetch_data['type'];
        $proformaItems        = array();
        $purchaseInvoiceItems = array();
        $profLineMerge        = array();
        $purchLineMerge       = array();
        $cmLineMerge          = array();
        $cmHead               = array();
        $cmLineDetails        = array();
        $flatCmLineDetails    = array();
        $flatProformaItems    = array();
        $flatPurchaseInvoiceItems = array();
    
        $getProf            = $this->proformavspi_model->getProformaInCrf($fetch_data['crfId']);
        $getPi              = $this->proformavspi_model->getPiInCrf($fetch_data['crfId']);

        $getHasDeal         = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['has_deal']; 
        $getDiscType        = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['disc_type'];  
        $documentNo         = $this->proformavspi_model->getDocNo(true);

        if($getHasDeal == "1"){
            $getDeals       = $this->proformavspi_model->getDeals($fetch_data['dealId']);
            $getNoOfDisc    = $this->proformavspi_model->getData2('vendors_deal_header','vendor_deal_head_id', $fetch_data['dealId'])['no_of_discount'];
        }

        foreach( $getProf as $get1 ){
            $proformaItems[]   = $this->proformavspi_model->getProformaItems($get1['proforma_header_id'], $fetch_data['supId']);
            $flatProformaItems = call_user_func_array('array_merge', $proformaItems);
        }
        foreach( $getPi as $get2 ){
            $cmHead[]                 = $this->proformavspi_model->getCmHead($get2['pi_head_id']);
            $purchaseInvoiceItems[]   = $this->proformavspi_model->getPurchaseInvoiceItems($get2['pi_head_id'], $fetch_data['supId']);
            $flatPurchaseInvoiceItems = call_user_func_array('array_merge', $purchaseInvoiceItems);
        }

        if(!empty($cmHead)){            
            foreach($cmHead as $cm)
            {
                if(isset($cm)){
                    $cmLineDetails[] = $this->proformavspi_model->getCmLineDetails($cm['cm_head_id'], $fetch_data['supId']);
                }
            }            
        }  

        if( !empty($cmLineDetails)){
            $flatCmLineDetails   = call_user_func_array('array_merge', $cmLineDetails);
        }

        // dump($flatCmLineDetails);
        // die();

        $disc1 = 0.00;
        $disc2 = 0.00;
        $disc3 = 0.00;
        $disc4 = 0.00;
        $disc5 = 0.00;
        $disc6 = 0.00;

        foreach( $flatProformaItems as $flat1 ){
            if($getHasDeal == "1"){
                if($getDiscType == "PERCENTAGE" || $getDiscType == "AMOUNT"){
                    foreach($getDeals as $deals){
                        if($deals['type'] == "Item Department"){
                            $itemCodeToFind = $flat1['item_department_code'];
                        } else if($deals['type'] == "Item"){
                            $itemCodeToFind = $flat1['itemcode_loc'];
                        } else if($deals['type'] == "Item Group"){
                            $itemCodeToFind = $flat1['item_group_code'];
                        }
                        if($deals['number'] == $itemCodeToFind ){
                            if($getDiscType == "PERCENTAGE"){
                                $disc1 = 1.00 - $deals['disc_1'] * 0.01 ;
                                if( $getNoOfDisc == "2"){
                                    $disc2 = 1.00 - $deals['disc_2'] * 0.01 ;                               
                                } else if(  $getNoOfDisc == "3" ){
                                    $disc2 = 1.00 - $deals['disc_2'] * 0.01 ;
                                    $disc3 = 1.00 - $deals['disc_3'] * 0.01 ;
                                } else if(  $getNoOfDisc == "4" ){
                                    $disc2 = 1.00 - $deals['disc_2'] * 0.01 ;
                                    $disc3 = 1.00 - $deals['disc_3'] * 0.01 ;
                                    $disc4 = 1.00 - $deals['disc_4'] * 0.01 ;
                                } else if(  $getNoOfDisc == "5" ){
                                    $disc2 = 1.00 - $deals['disc_2'] * 0.01 ;
                                    $disc3 = 1.00 - $deals['disc_3'] * 0.01 ;
                                    $disc4 = 1.00 - $deals['disc_4'] * 0.01 ;
                                    $disc5 = 1.00 - $deals['disc_5'] * 0.01 ;
                                } else if(  $getNoOfDisc == "6" ){
                                    $disc2 = 1.00 - $deals['disc_2'] * 0.01 ;
                                    $disc3 = 1.00 - $deals['disc_3'] * 0.01 ;
                                    $disc4 = 1.00 - $deals['disc_4'] * 0.01 ;
                                    $disc5 = 1.00 - $deals['disc_5'] * 0.01 ;
                                    $disc6 = 1.00 - $deals['disc_6'] * 0.01 ;
                                } 
                            } else if($getDiscType == "AMOUNT"){
                                $disc1 = $deals['disc_1'];    
                                $disc2 = $deals['disc_2'] ;
                                $disc3 = $deals['disc_3'] ;
                                $disc4 = $deals['disc_4'] ;
                                $disc5 = $deals['disc_5'] ;  
                                $disc6 = $deals['disc_6'] ;                        
                            }       
                            
                            
                            $profLineMerge[] = [ 'itemId'   =>$flat1['id'],         'poId' =>$flat1['poId'], 'profCode'=>$flat1['profCode'],'profItem' =>$flat1['item'],
                                                 'profDesc' =>$flat1['idesc'],   'profQty' =>$flat1['qty'],  'profUom' =>$flat1['uom'],     'profPrice'=>$flat1['price'],
                                                 'profAmt'  =>$flat1['amount'],   'disc1'  =>$disc1,           'disc2' =>$disc2,               'disc3' =>$disc3,               
                                                 'disc4'    =>$disc4,           'disc5'    =>$disc5,            'disc6'    =>$disc6, 'pono' =>$flat1['po_no'] ];
                        }
                    }   
                } else if( $getDiscType == "NONE"){
                    $profLineMerge[] = [ 'itemId'   =>$flat1['id'],         'poId' =>$flat1['poId'], 'profCode'=>$flat1['profCode'],'profItem' =>$flat1['item'],
                                         'profDesc' =>$flat1['idesc'],   'profQty' =>$flat1['qty'],  'profUom' =>$flat1['uom'],     'profPrice'=>$flat1['price'],
                                         'profAmt'  =>$flat1['amount'],   'disc1'  =>$disc1,           'disc2' =>$disc2,               'disc3' =>$disc3,               
                                         'disc4'    =>$disc4,           'disc5'    =>$disc5,           'disc6'    =>$disc6, 'pono' =>$flat1['po_no'] ];
                }            
            }
        }

        $disc1 = 0.00;
        $disc2 = 0.00;
        $disc3 = 0.00;
        $disc4 = 0.00;
        $disc5 = 0.00;
        $disc6 = 0.00;

        foreach( $flatPurchaseInvoiceItems as $flat2 ){
            if($getHasDeal == "1"){
                if($getDiscType == "PERCENTAGE" || $getDiscType == "AMOUNT"){
                    foreach($getDeals as $deals){
                        if($deals['type'] == "Item Department"){
                            $itemCodeToFind = $flat2['item_department_code'];
                        } else if($deals['type'] == "Item"){
                            $itemCodeToFind = $flat2['itemcode_loc'];
                        } else if($deals['type'] == "Item Group"){
                            $itemCodeToFind = $flat2['item_group_code'];
                        }
                        if($deals['number'] == $itemCodeToFind ){
                            if($getDiscType == "PERCENTAGE"){
                                $disc1 = 1.00 - $deals['disc_1'] * 0.01 ;
                                if( $getNoOfDisc == "2"){
                                    $disc2 = 1.00 - $deals['disc_2'] * 0.01 ;                               
                                } else if(  $getNoOfDisc == "3" ){
                                    $disc2 = 1.00 - $deals['disc_2'] * 0.01 ;
                                    $disc3 = 1.00 - $deals['disc_3'] * 0.01 ;
                                } else if(  $getNoOfDisc == "4" ){
                                    $disc2 = 1.00 - $deals['disc_2'] * 0.01 ;
                                    $disc3 = 1.00 - $deals['disc_3'] * 0.01 ;
                                    $disc4 = 1.00 - $deals['disc_4'] * 0.01 ;
                                } else if(  $getNoOfDisc == "5" ){
                                    $disc2 = 1.00 - $deals['disc_2'] * 0.01 ;
                                    $disc3 = 1.00 - $deals['disc_3'] * 0.01 ;
                                    $disc4 = 1.00 - $deals['disc_4'] * 0.01 ;
                                    $disc5 = 1.00 - $deals['disc_5'] * 0.01 ;
                                } else if(  $getNoOfDisc == "6" ){
                                    $disc2 = 1.00 - $deals['disc_2'] * 0.01 ;
                                    $disc3 = 1.00 - $deals['disc_3'] * 0.01 ;
                                    $disc4 = 1.00 - $deals['disc_4'] * 0.01 ;
                                    $disc5 = 1.00 - $deals['disc_5'] * 0.01 ;
                                    $disc6 = 1.00 - $deals['disc_6'] * 0.01 ;
                                } 
                            } else if($getDiscType == "AMOUNT"){
                                $disc1 = $deals['disc_1'];    
                                $disc2 = $deals['disc_2'] ;
                                $disc3 = $deals['disc_3'] ;
                                $disc4 = $deals['disc_4'] ;
                                $disc5 = $deals['disc_5'] ; 
                                $disc6 = $deals['disc_6'] ;                             
                            }       
                            
                            
                            $purchLineMerge[] = [ 'itemId'   =>$flat2['id'],         'poId' =>$flat2['poId'],    'piId' =>$flat2['piId'],   'piNo'   =>$flat2['piNo'],  'piItem'   =>$flat2['item'], 
                                                  'piDate'   =>$flat2['piDate'],   'piDesc' =>$flat2['idesc'],'piQty'   =>$flat2['qty'],     'piUom' =>$flat2['uom'],   'piPrice'  =>$flat2['direct'],  
                                                  'piAmt'    =>$flat2['amount'],    'disc1' =>$disc1,           'disc2' =>$disc2,            'disc3' =>$disc3,            'disc4'  =>$disc4 , 
                                                  'disc5'  =>$disc5,  'disc6'  =>$disc6 ,'pono'     =>$flat2['po_no'],     'ref'   =>$flat2['vendor_invoice_no'] ];
                        }
                    } 
                } else if( $getDiscType == "NONE"){// no actual vendor deal like LMB GROCERY
                    $purchLineMerge[] = [ 'itemId'   =>$flat2['id'],         'poId' =>$flat2['poId'],    'piId' =>$flat2['piId'],   'piNo'   =>$flat2['piNo'],  'piItem'   =>$flat2['item'], 
                                          'piDate'   =>$flat2['piDate'],   'piDesc' =>$flat2['idesc'],'piQty'   =>$flat2['qty'],     'piUom' =>$flat2['uom'],   'piPrice'  =>$flat2['direct'],  
                                          'piAmt'    =>$flat2['amount'],    'disc1' =>$disc1,           'disc2' =>$disc2,            'disc3' =>$disc3,            'disc4'  =>$disc4 , 'disc5'  =>$disc5 ,
                                          'disc6'  =>$disc6, 'pono'     =>$flat2['po_no'],     'ref'   =>$flat2['vendor_invoice_no'] ];
                }           
            }
        }

        if(!empty($flatCmLineDetails)){

            foreach($flatCmLineDetails as $cml)
            {      
                
                if($getHasDeal == "1"){
                    if($getDiscType == "PERCENTAGE" || $getDiscType == "AMOUNT" ){
                        foreach($getDeals as $deal3)/* vendor deals   */ 
                        {
                            if($deal3['type'] == "Item Department"){
                                $itemCodeToFind = $cml['item_department_code'];
                            } else if($deal3['type'] == "Item"){
                                $itemCodeToFind = $cml['itemcode_loc'];
                            } else if($deal3['type'] == "Item Group"){
                                $itemCodeToFind = $cml['item_group_code'];

                            }
                            if($deal3['number'] == $itemCodeToFind ){
                                if($getDiscType == "PERCENTAGE"){
                                    $disc1 = 1.00 - $deal3['disc_1'] * 0.01 ;
                                    $disc2 = 1.00 - $deal3['disc_2'] * 0.01 ;
                                    $disc3 = 1.00 - $deal3['disc_3'] * 0.01 ;
                                    $disc4 = 1.00 - $deal3['disc_4'] * 0.01 ;
                                    $disc5 = 1.00 - $deal3['disc_5'] * 0.01 ;
                                } else if($getDiscType == "AMOUNT"){
                                    $disc1   = $deal3['disc_1'];
                                    $disc2   = $deal3['disc_2'];
                                    $disc3   = $deal3['disc_3'];
                                    $disc4   = $deal3['disc_4'];
                                    $disc5   = $deal3['disc_5'];                              
                                }                                        
                            }
                        }
                    } else if($getDiscType == "NONE"){
                        $disc1      = 0.00;
                        $disc2      = 0.00;
                        $disc3      = 0.00;
                        $disc4      = 0.00;
                        $disc5      = 0.00;
                    }
                }
                $cmLineMerge[] = ['cmId' => $cml['cm_head_id'], 'cmNo' => $cml['cm_no'], 'date' => $cml['posting_date'], 'item' => $cml['item_code'],
                                'desc' => $cml['description'],'uom' => $cml['uom'],'qty' => $cml['qty'], 'price' => $cml['direct_unit_cost'],
                                'disc1'=> $disc1, 'disc2' => $disc2, 'disc3' => $disc3, 'disc4' => $disc4, 'disc5' => $disc5, 'piNo' => $cml['pi_no']  ];
                
            }
            
        }

        // for history
        $transaction = array('tr_no'         => $documentNo,
                             'tr_date'       => date("F d, Y - h:i:s A"),
                             'crf_id'        => $fetch_data['crfId'],
                             'supplier_id'   => $fetch_data['supId'],
                             'customer_code' => $fetch_data['cusId'],
                             'user_id'       => $this->session->userdata('user_id'),
                             'filename'      => '' );
        $this->db->insert('profvpi_transaction', $transaction);
        $transactionId = $this->db->insert_id();
        if($type == "pdf"){
            $file = $this->printPDFMatching2($transactionId,$profLineMerge, $purchLineMerge, $cmLineMerge, $fetch_data);
            $msg = ['info' => 'success', 'message' => 'Matching Pro-forma vs PI is complete!', 'file' => $file];
        } else {
            $msg = ['info' => 'error', 'message' => 'Excel file is not yet supported!'];
        }
        
        
        JSONResponse($msg);

    }

    public function printPDFMatching2($transactionId,$profLineMerge, $purchLineMerge, $cmLineMerge, $fetch_data)
    {
        $supplier             = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['supplier_name'];
        $getPricing           = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['pricing'];
        $getAmounting         = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['amounting'];
        $getDiscType          = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['disc_type'];
        $vat                  = $this->proformavspi_model->getVATData()['value'];
        $sopDeduction         = $this->proformavspi_model->getSopDeduction($fetch_data['crfId']); 
        $mentionsFromSop      = $this->proformavspi_model->getVarianceMentions($fetch_data['crfId']);

        $proformaItemCount         = 0;
        $proformaItemTotalQty      = 0;
        $proformaItemTotalNetAmt   = 0;
        $proformaItemTotalDiscAmt  = 0;
        $proformaItemTotalGrossAmt = 0;

        $purchaseInvoiceItemCount         = 0;
        $purchaseInvoiceItemTotalQty      = 0;
        $purchaseInvoiceItemTotalNetAmt   = 0;
        $purchaseInvoiceItemTotalDiscAmt  = 0;
        $purchaseInvoiceItemTotalGrossAmt = 0;

        $sopTotalDeductionW12 = 0;        
        $cmItemCount          = 0;
        $cmItemQty            = 0;
        $cmVarianceQty        = 0;
        $cmGrossAmount        = 0;
        $cmDiscountedAmt      = 0;
        $cmNetAmount          = 0;
        $cmVarianceDiscAmount = 0;

        $sopVarianceMentionTotal = 0;

        $pdf = new FPDF('L', 'mm', array(420,594));  //A2
        $pdf->AddPage();
        $pdf->setDisplayMode('fullpage');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 0, $supplier, 0, 0, 'L');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(40, 0, 'PRO-FORMA SALES INVOICE vs PURCHASE INVOICE - VARIANCE REPORT', 0, 0, 'L');
        $pdf->Ln(10);

        $this->createProfTableHeader($pdf);

        foreach( $profLineMerge as $line1){

            if($getAmounting == "NETofVAT&Disc"){
                $netAmount = $line1['profAmt'];
            } else if($getAmounting == "GROSSofVAT&Disc"){
                if($getDiscType == "PERCENTAGE"){
                    $netAmount = netPrice($fetch_data['supId'],$getPricing,$line1['profAmt'],$line1['disc1'],$line1['disc2'],$line1['disc3'],$line1['disc4'], $line1['disc5'],$line1['disc6'],$vat, $fetch_data['cusId']);
                } else if($getDiscType == "AMOUNT"){
                    $price     = netPrice($fetch_data['supId'],$getPricing,$line1['profPrice'],$line1['disc1'],$line1['disc2'],$line1['disc3'],$line1['disc4'], $line1['disc5'], $line1['disc6'],$vat, $fetch_data['cusId']);
                    $netAmount = $price * $line1['profQty'];
                }
               
            } else if($getAmounting == "NETofVATwDisc"){
                $netAmount = netPrice($fetch_data['supId'],$getPricing,$line1['profAmt'],$line1['disc1'],$line1['disc2'],$line1['disc3'],$line1['disc4'], $line1['disc5'],$line1['disc6'],$vat, $fetch_data['cusId']);
            } else if($getAmounting == "NETofDiscwVAT"){
                $price     = netPrice($fetch_data['supId'],$getPricing,$line1['profPrice'],$line1['disc1'],$line1['disc2'],$line1['disc3'],$line1['disc4'], $line1['disc5'],$line1['disc6'],$vat, $fetch_data['cusId']);
                $netAmount = $price * $line1['profQty'];
            } else if($getAmounting == "GROSSofDiscwoVAT"){ //like columbia
                $netAmount = netPrice($fetch_data['supId'],$getPricing,$line1['profAmt'],$line1['disc1'],$line1['disc2'],$line1['disc3'],$line1['disc4'],$line1['disc5'],$line1['disc6'],$vat, $fetch_data['cusId']);
            } 

            $netPrice          = netPrice($fetch_data['supId'],$getPricing,$line1['profPrice'],$line1['disc1'],$line1['disc2'],$line1['disc3'],$line1['disc4'], $line1['disc5'],$line1['disc6'],$vat, $fetch_data['cusId']);         
            if($getPricing == "NETofVATwDisc"){
                $grossPrice      = backToGross($fetch_data['supId'],$line1['profPrice'],$line1['disc1'],$line1['disc2'],$line1['disc3'],$line1['disc4'], $line1['disc5'],$line1['disc6'],$vat);
                $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$line1['disc1'],$line1['disc2'],$line1['disc3'],$line1['disc4'], $line1['disc5'],$line1['disc6'],$vat);
            } else if($getPricing == "GROSSofVAT&Disc"){
                $grossPrice      = backToGross($fetch_data['supId'],$line1['profPrice'],$line1['disc1'],$line1['disc2'],$line1['disc3'],$line1['disc4'], $line1['disc5'],$line1['disc6'],$vat);
                $discountedPrice = discountedPrice($fetch_data['supId'],$line1['profPrice'],$line1['disc1'],$line1['disc2'],$line1['disc3'],$line1['disc4'], $line1['disc5'],$line1['disc6'],$vat);
            } else if($getPricing == "GROSSofDiscwoVAT"){
                $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$line1['disc1'],$line1['disc2'],$line1['disc3'],$line1['disc4'], $line1['disc5'],$line1['disc6'],$vat);
                $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$line1['disc1'],$line1['disc2'],$line1['disc3'],$line1['disc4'], $line1['disc5'],$line1['disc6'],$vat);
            } else { //NETofVAT&Disc
                $grossPrice      = backToGross($fetch_data['supId'],$netPrice,$line1['disc1'],$line1['disc2'],$line1['disc3'],$line1['disc4'], $line1['disc5'],$line1['disc6'],$vat);
                $discountedPrice = discountedPrice($fetch_data['supId'],$netPrice,$line1['disc1'],$line1['disc2'],$line1['disc3'],$line1['disc4'], $line1['disc5'],$line1['disc6'],$vat);
            }

            $pdf->cell(80, 6, $line1['profCode'], 'LBR', 0, 'C');
            $pdf->cell(35, 6, $line1['pono'], 'BR', 0, 'C', 0);
            $pdf->cell(20, 6, $line1['profItem'], 'BR', 0, 'C', 0);
            $pdf->cell(120,6, $line1['profDesc'], 'BR', 0, 'L', 0);
            $pdf->cell(15, 6, $line1['profUom'], 'BR', 0, 'C', 0);
            $pdf->cell(12, 6, $line1['profQty'], 'BR', 0, 'C', 0);
            $pdf->cell(27, 6, number_format($netPrice, 2), 'BR', 0, 'R', 0); //net price
            $pdf->cell(30, 6, number_format($discountedPrice, 2), 'BR', 0, 'R', 0); //discounted price
            $pdf->cell(30, 6, number_format($grossPrice, 2), 'BR', 0, 'R', 0);  //gross price
            $pdf->cell(32, 6, number_format($netAmount, 2), 'BR', 0, 'R', 0);  //net amount
            $pdf->cell(32, 6,  number_format($discountedPrice * $line1['profQty'] ,2), 'BR', 0, 'R', 0);  //discounted amount
            $pdf->cell(32, 6, number_format($grossPrice * $line1['profQty'], 2), 'BR', 0, 'R', 0);  //gross amount

            $proformaItemCount ++;
            $proformaItemTotalQty      += $line1['profQty'];
            $proformaItemTotalNetAmt   += $netAmount;
            $proformaItemTotalDiscAmt  += $discountedPrice * $line1['profQty'];
            $proformaItemTotalGrossAmt += $grossPrice * $line1['profQty'];

            $pdf->Ln();
        }

        $pdf->setFont('Arial', 'B', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->cell(270, 5, "PRO-FORMA SALES INVOICE ITEM(S) TOTAL :", 'LB', 0, 'L');
        $pdf->cell(12, 5, $proformaItemTotalQty, 'B', 0, 'R'); 
        $pdf->cell(87, 5, "", 'B', 0, 'R');
        $pdf->cell(32, 5, "P " . number_format($proformaItemTotalNetAmt,2), 'B', 0, 'R');
        $pdf->cell(32, 5, "P " . number_format($proformaItemTotalDiscAmt,2), 'B', 0, 'R');
        $pdf->cell(32, 5, "P " . number_format($proformaItemTotalGrossAmt, 2), 'BR', 0, 'R');

        $pdf->Ln();
        $pdf->cell(25, 5, "Pro-forma Sales Invoice Item Count : " . $proformaItemCount, '', 0, 'L');
        $pdf->Ln(10);

        $this->createPiTableHeader($pdf);
        foreach( $purchLineMerge as $line2){
  
            $netPricePi        = netPricePi($fetch_data['supId'],$line2['piPrice'],$line2['disc1'],$line2['disc2'],$line2['disc3'],$line2['disc4'], $line2['disc5'], $line2['disc6'],$vat);   
            $discountedPricePi = discountedPricePi($fetch_data['supId'],$line2['piPrice'],$line2['disc1'],$line2['disc2'],$line2['disc3'],$line2['disc4'], $line2['disc5'], $line2['disc6']);     
            
            $pdf->cell(30, 6, $line2['piNo'], 'LBR', 0, 'C');
            $pdf->cell(20, 6, $line2['piDate'], 'BR', 0, 'C', 0);
            $pdf->cell(29, 6, $line2['ref'], 'BR', 0, 'C', 0);
            $pdf->cell(30, 6, $line2['pono'], 'BR', 0, 'C', 0);
            $pdf->cell(20, 6, $line2['piItem'], 'BR', 0, 'C', 0);
            $pdf->cell(90, 6, $line2['piDesc'], 'BR', 0, 'L', 0);
            $pdf->cell(20, 6, $line2['piUom'], 'BR', 0, 'C', 0);
            $pdf->cell(20, 6, $line2['piQty'], 'BR', 0, 'C', 0);
            $pdf->cell(40, 6, number_format($netPricePi,2), 'BR', 0, 'R', 0); //net price
            $pdf->cell(35, 6, number_format($discountedPricePi, 2), 'BR', 0, 'R', 0);  //discounted price
            $pdf->cell(35, 6, number_format($line2['piPrice'], 2), 'BR', 0, 'R', 0);  //gross price
            $pdf->cell(32, 6, number_format($netPricePi * $line2['piQty'] ,2), 'BR', 0, 'R', 0);  //net amount
            $pdf->cell(32, 6, number_format($discountedPricePi * $line2['piQty'],2), 'BR', 0, 'R', 0);  //discounted amount
            $pdf->cell(32, 6, number_format($line2['piPrice'] * $line2['piQty'], 2), 'BR', 0, 'R', 0);  //gross amount

            $purchaseInvoiceItemCount         ++;
            $purchaseInvoiceItemTotalQty      += $line2['piQty'];
            $purchaseInvoiceItemTotalNetAmt   += $netPricePi * $line2['piQty'];
            $purchaseInvoiceItemTotalDiscAmt  += $discountedPricePi * $line2['piQty'];
            $purchaseInvoiceItemTotalGrossAmt += $line2['piPrice'] * $line2['piQty'];
            $pdf->Ln();
           
        }

        $pdf->setFont('Arial', 'B', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->cell(239, 5, "PURCHASE INVOICE ITEM(S) TOTAL :", 'LB', 0, 'L');
        $pdf->cell(20, 5, $purchaseInvoiceItemTotalQty, 'B', 0, 'R'); 
        $pdf->cell(110, 5, "", 'B', 0, 'R');
        $pdf->cell(32, 5, "P " . number_format($purchaseInvoiceItemTotalNetAmt,2), 'B', 0, 'R');
        $pdf->cell(32, 5, "P " . number_format($purchaseInvoiceItemTotalDiscAmt,2), 'B', 0, 'R');
        $pdf->cell(32, 5, "P " . number_format($purchaseInvoiceItemTotalGrossAmt, 2), 'BR', 0, 'R');

        $pdf->Ln();
        $pdf->cell(25, 5, "Purchase Invoice Item Count : " . $purchaseInvoiceItemCount, '', 0, 'L');
        $pdf->Ln(10);
        
         /*   CREDIT MEMO     */
         if( !empty($cmLineMerge) ){
            $this->createCMTableHeader($pdf);
            foreach($cmLineMerge as $cmd)
            {
                $netPricePi        = netPricePi($fetch_data['supId'],$cmd['price'],$cmd['disc1'],$cmd['disc2'],$cmd['disc3'],$cmd['disc4'],$cmd['disc5'],$cmd['disc6'],$vat);
                $discountedPricePi = discountedPricePi($fetch_data['supId'],$cmd['price'],$cmd['disc1'],$cmd['disc2'],$cmd['disc3'],$cmd['disc4'],$cmd['disc5'],$cmd['disc6']);                

                $pdf->cell(30, 6, $cmd['cmNo'], 'LBR', 0, 'C'); //cm no
                $pdf->cell(20, 6, $cmd['date'], 'BR', 0, 'C', 0); //cm date
                $pdf->cell(29, 6, $cmd['item'], 'BR', 0, 'C', 0); //ref#
                $pdf->cell(30, 6, $cmd['desc'], 'BR', 0, 'L', 0); //po
                $pdf->cell(20, 6, $cmd['item'], 'BR', 0, 'C', 0); //item
                $pdf->cell(90, 6, $cmd['desc'], 'BR', 0, 'C', 0); //desc
                $pdf->cell(20, 6, $cmd['uom'], 'BR', 0, 'C', 0); //uom
                $pdf->cell(20, 6, $cmd['qty'], 'BR', 0, 'C', 0); //qty
                $pdf->cell(40, 6, number_format($cmd['price'], 2), 'BR', 0, 'R', 0); //net price
                $pdf->cell(35, 6, number_format($discountedPricePi,2), 'BR', 0, 'R', 0); //discounted price
                $pdf->cell(35, 6, number_format($netPricePi,2), 'BR', 0, 'R', 0); //unit price
                $pdf->cell(32, 6, number_format($cmd['price'] * $cmd['qty'], 2), 'BR', 0, 'R', 0); //net amount
                $pdf->cell(32, 6, number_format($discountedPricePi * $cmd['qty'],2), 'BR', 0, 'R', 0); //discounted amount
                $pdf->cell(32, 6, number_format($netPricePi * $cmd['qty'],2), 'BR', 0, 'R', 0); //gross amount

                $cmItemCount          ++ ;
                $cmItemQty            += $cmd['qty'] * -1 ;
                $cmVarianceQty        += 0;//$cmd['qty'] * -1;
                $cmGrossAmount        += $cmd['price'] * $cmd['qty'] * -1;
                $cmDiscountedAmt      += $discountedPricePi * $cmd['qty'] * -1;
                $cmNetAmount          += $netPricePi * $cmd['qty'] * -1;
                $cmVarianceDiscAmount += $discountedPricePi * $cmd['qty'] * -1;
                $pdf->Ln();
                
            }
            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->cell(239, 5, "CREDIT MEMO ITEM(S) TOTAL :", 'LB', 0, 'L');
            $pdf->cell(20, 5, $cmItemQty, 'B', 0, 'R'); 
            $pdf->cell(110, 5, "", 'B', 0, 'R');
            $pdf->cell(32, 5, "P " . number_format($cmNetAmount,2), 'B', 0, 'R');
            $pdf->cell(32, 5, "P " . number_format($cmDiscountedAmt,2), 'B', 0, 'R');
            $pdf->cell(32, 5, "P " . number_format($cmGrossAmount, 2), 'BR', 0, 'R');

            $pdf->Ln();
            $pdf->cell(25, 5, "Credit Memo Item Count : " . $cmItemCount, '', 0, 'L');
            $pdf->Ln(10);
        }
        /*   CREDIT MEMO     */

        /* SOP DEDUCTION  */
        if(!empty($sopDeduction))
        {
            $pdf->SetTextColor(201, 201, 201);
            $pdf->SetFillColor(35, 35, 35);
            $pdf->setFont('Arial', '', 9);
            $pdf->Cell(80, 7, 'SOP No', 1, 0, 'C', TRUE);
            $pdf->cell(25, 7, 'Date', 1, 0, 'C', TRUE);
            $pdf->cell(125, 7, 'Deduction', 1, 0, 'C', TRUE);
            $pdf->Cell(30, 7, 'Amount', 1, 0, 'C', TRUE);

            $i = 0;
            $height = count($sopDeduction) * 5;
            foreach($sopDeduction as $sopRow)
            {
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Ln();
                if($i == 0){
                    $pdf->cell(80, $height, $sopRow['sop_no'], 'LBR', 0, 'C');              
                    $pdf->cell(25, $height, date("Y-m-d",strtotime($sopRow['datetime_created'])), 'BR', 0, 'C');
                    $pdf->cell(125, 5, $sopRow['description'], 'BR', 0, 'C');
                    $pdf->cell(30, 5, number_format($sopRow['deduction_amount'] ,2), 'BR', 0, 'R');
                } else {
                    $pdf->cell(80, 5, '', '', 0, 'C');              
                    $pdf->cell(25, 5, '', '', 0, 'C');
                    $pdf->cell(125, 5, $sopRow['description'], 'BR', 0, 'C');
                    $pdf->cell(30, 5, number_format($sopRow['deduction_amount'] ,2), 'BR', 0, 'R');
                }      
                $sopTotalDeductionW12 += $sopRow['deduction_amount'] ;    
                $i++;                          
            }

            $pdf->Ln();
            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->cell(230, 5, "TOTAL SOP Deduction :", 'LB', 0, 'L', 0);
            $pdf->cell(30, 5, "P " . number_format($sopTotalDeductionW12, 2), 'BR', 0, 'R');
            $pdf->Ln(10);
        } /* SOP DEDUCTION  */

        /* VARIANCE MENTION/S FROM OTHER SOP  */
        if(!empty($mentionsFromSop)){
            $pdf->Ln(2);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(40, 0, 'VARIANCE MENTIONS FROM OTHER SOP :', 0, 0, 'L');
            $pdf->Ln(2);
            $pdf->SetTextColor(201, 201, 201);
            $pdf->SetFillColor(35, 35, 35);  
            $pdf->setFont('Arial', '', 9);
            $pdf->Cell(80, 7, 'SOP No', 1, 0, 'C', TRUE);
            $pdf->cell(25, 7, 'Date', 1, 0, 'C', TRUE);
            $pdf->cell(125, 7, 'Deduction', 1, 0, 'C', TRUE);
            $pdf->Cell(30, 7, 'Amount', 1, 0, 'C', TRUE);

            $pdf->SetTextColor(0, 0, 0);
            foreach($mentionsFromSop as $mention)
            {                
                $pdf->Ln();
                $pdf->setX(30);
                $pdf->cell(80, 5, $mention['sop_no'], 'LBR', 0, 'C');              
                $pdf->cell(25, 5, date("Y-m-d",strtotime($mention['datetime_created'])), 'BR', 0, 'C');
                $pdf->cell(125, 5, $mention['description'], 'BR', 0, 'C');
                $pdf->cell(30, 5, number_format($mention['deduction_amount'],2), 'BR', 0, 'R'); 
                $sopVarianceMentionTotal += $mention['deduction_amount']  ;    
            }

            $pdf->Ln();
            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->setX(30);
            $pdf->cell(230, 5, "TOTAL SOP Deduction (Mentions) :", 'LB', 0, 'L', 0);
            $pdf->cell(30, 5, "P " . number_format($sopVarianceMentionTotal, 2), 'BR', 0, 'R');
            $pdf->Ln(15);
        }
        /* VARIANCE MENTION/S FROM OTHER SOP  */


        $pdf->Cell(105, 0, 'TOTAL PRO-FORMA SALES INVOICE (PSI) (Gross Amount) : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P " . number_format($proformaItemTotalGrossAmt, 2), 0, 0, 'R');
        $pdf->Ln(5); 
        $pdf->Cell(105, 0, 'Less: TOTAL SOP Deduction ', 0, 0, 'R');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->cell(30, 0, "P ". number_format( $sopTotalDeductionW12 ,2), 0, 0, 'R');
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Ln(5); 
        $pdf->Cell(105, 0, 'TOTAL CRF/CV Amount : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P ". number_format($proformaItemTotalGrossAmt + $sopTotalDeductionW12,2), 0, 0, 'R');
        $pdf->Ln(10);

        $pdf->Cell(105, 0, 'TOTAL PURCHASE INVOICE (PI) (Gross Amount) : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P ". number_format($purchaseInvoiceItemTotalGrossAmt + $cmGrossAmount,2), 0, 0, 'R');
        $pdf->Ln(5);
        $pdf->Cell(105, 0, 'Less: TOTAL SOP Deduction', 0, 0, 'R');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->cell(30, 0, "P ". number_format($sopTotalDeductionW12,2), 0, 0, 'R');
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Ln(5);
        $pdf->Cell(105, 0, 'NET PURCHASE INVOICE (PI) Amount : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P ". number_format( ($purchaseInvoiceItemTotalGrossAmt + $cmGrossAmount + $sopTotalDeductionW12) ,2), 0, 0, 'R');
        $pdf->Ln(10);

        $pdf->Cell(105, 0, 'TOTAL CRF/CV Amount : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P ". number_format( $proformaItemTotalGrossAmt + $sopTotalDeductionW12,2), 0, 0, 'R');
        $pdf->Ln(5);
        $pdf->Cell(105, 0, 'NET PURCHASE INVOICE (PI) Amount : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->cell(30, 0, "P ". number_format( ($purchaseInvoiceItemTotalGrossAmt + $cmGrossAmount + $sopTotalDeductionW12) * -1,2), 0, 0, 'R');
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Ln(5);
        $pdf->Cell(105, 0, 'Variance (Gross Amount) : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P ". number_format( ($proformaItemTotalGrossAmt + $sopTotalDeductionW12) + (($purchaseInvoiceItemTotalGrossAmt + $cmGrossAmount + $sopTotalDeductionW12) * -1)  ,2), 0, 0, 'R');
        $pdf->Ln(10);

        $pdf->Cell(105, 0, 'TOTAL PRO-FORMA SALES INVOICE (PSI) (Discounted Amount) : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P " . number_format($proformaItemTotalDiscAmt, 2), 0, 0, 'R');
        $pdf->Ln(5); 
        $pdf->Cell(105, 0, 'TOTAL PURCHASE INVOICE (PI) (Discounted Amount) : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->cell(30, 0, "P ". number_format(($purchaseInvoiceItemTotalDiscAmt + $cmDiscountedAmt) * -1,2), 0, 0, 'R');
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Ln(5);
        $pdf->Cell(105, 0, 'Variance (Discounted Amount) : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P ". number_format( $proformaItemTotalDiscAmt + ($purchaseInvoiceItemTotalDiscAmt + $cmDiscountedAmt) * -1,2), 0, 0, 'R');
        $pdf->Ln(10);

        $pdf->Cell(105, 0, 'TOTAL PRO-FORMA SALES INVOICE (PSI) (Net Amount) : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P " . number_format($proformaItemTotalNetAmt, 2), 0, 0, 'R');
        $pdf->Ln(5); 
        $pdf->Cell(105, 0, 'TOTAL PURCHASE INVOICE (PI) (Net Amount) : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->SetFont('Arial', 'BU', 10);   
        $pdf->cell(30, 0, "P ". number_format(($purchaseInvoiceItemTotalNetAmt + $cmNetAmount) * -1,2), 0, 0, 'R');
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Ln(5);
        $pdf->Cell(105, 0, 'Variance (Net Amount) : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P ". number_format( $proformaItemTotalNetAmt + ($purchaseInvoiceItemTotalNetAmt + $cmNetAmount) * -1,2), 0, 0, 'R');  
        $pdf->Ln(10);

        $pdf->Cell(105, 0, 'Pro-forma Sales Invoice Item Count : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, number_format( $proformaItemCount,2), 0, 0, 'R');
        $pdf->Ln(5);
        $pdf->Cell(105, 0, 'Purchase Invoice Item Count : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->cell(30, 0, number_format( $purchaseInvoiceItemCount * -1,2), 0, 0, 'R');
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Ln(5);
        $pdf->Cell(105, 0, 'Variance in Item Count : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, number_format( $proformaItemCount - $purchaseInvoiceItemCount  ,2), 0, 0, 'R');
        $pdf->Ln(10);

        $pdf->Cell(105, 0, 'Pro-forma Sales Invoice Item Quantity Total : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, number_format( $proformaItemTotalQty,2), 0, 0, 'R');
        $pdf->Ln(5);
        $pdf->Cell(105, 0, 'Purchase Invoice Item Quantity Total  : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->cell(30, 0, number_format( $purchaseInvoiceItemTotalQty * -1,2), 0, 0, 'R');
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Ln(5);
        $pdf->Cell(105, 0, 'Variance in Item Quantity : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, number_format( $proformaItemTotalQty - $purchaseInvoiceItemTotalQty  ,2), 0, 0, 'R');
        $pdf->Ln(10);

        $pdf->Cell(105, 0, 'Variance (Discounted Amount)  : ', 0, 0, 'L');
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P ". number_format( $proformaItemTotalDiscAmt + ($purchaseInvoiceItemTotalDiscAmt + $cmDiscountedAmt) * -1,2), 0, 0, 'R');
        $varianceDiscountedTotal = ($proformaItemTotalDiscAmt + ($purchaseInvoiceItemTotalDiscAmt + $cmDiscountedAmt) * -1) ;
        $pdf->Ln(5);
        $pdf->Cell(105, 0, 'Mentions From Other SOP : ', 0, 0, 'L'); 
        $pdf->cell(125, 0, "", 0, 0, 'R');
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->cell(30, 0, "P ". number_format( $sopVarianceMentionTotal,2), 0, 0, 'R');
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Ln(5);        
        $pdf->cell(230, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P ". number_format( $varianceDiscountedTotal + $sopVarianceMentionTotal ,2), 0, 0, 'R');

        $supAcroname = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['acroname'];
        $cusAcroname = $this->proformavspi_model->getCustomerData($fetch_data['cusId'],'customer_code')['l_acroname'];
        $fileName    = $supAcroname.'-'. $cusAcroname.' - PSIvsPI'  . time() . '.pdf';

        // for history
        $this->db->set('filename', $fileName)
                 ->where('tr_id', $transactionId)
                 ->update('profvpi_transaction');
        // for history

        // for variance
        if($fetch_data['saveVar'] == '1'){
            $varianceToSave = 0;
            if( $fetch_data['supId'] == '9'){
                $varianceToSave = round($proformaItemTotalDiscAmt + ($purchaseInvoiceItemTotalDiscAmt + $cmDiscountedAmt) * -1,2);
            }
            $varianceData = ['crf_id'            => $fetch_data['crfId'],
                             'supplier_id'       => $fetch_data['supId'],
                             'variance_amount'   => $varianceToSave,
                             'user_id'           => $this->session->userdata('user_id'),
                             'datetime_created'  => date("Y-m-d H:i:s") ];
            $this->db->insert('variance',$varianceData);
            $varId = $this->db->insert_id();
            $ledger = ['variance_id'  => $varId,
                       'sop_id'       => 0,
                       'debit'        => $varianceToSave,
                       'credit'       => 0 ];
            $this->db->insert('variance_ledger',$ledger);
        }
        // for variance

        $pdf->Output('files/Reports/ProformaVsPi/' . $fileName, 'F');

        return $fileName;
    }

    private function createProfTableHeader($pdf)
    {
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(40, 0, 'Pro-forma Sales Invoice', 0, 0, 'L');
        $pdf->Ln(3);


        $pdf->SetFont('Arial','',8);
        $pdf->SetTextColor(201, 201, 201);
        $pdf->SetFillColor(35, 35, 35);

        $pdf->cell(80, 8, "Pro-forma", 1, 0, 'C', TRUE);
        $pdf->cell(35, 8, "PO No", 1, 0, 'C', TRUE);
        $pdf->cell(20, 8, "Item", 1, 0, 'C', TRUE);
        $pdf->cell(120, 8, "Description", 1, 0, 'C', TRUE);
        $pdf->cell(15, 8, "UOM", 1, 0, 'C', TRUE);
        $pdf->cell(12, 8, "Qty", 1, 0, 'C', TRUE);
        
        $pdf->cell(27, 4, "Net Price", 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(292);
        $pdf->cell(27, 4, "(Net of VAT & Disct.)", 'LBR', 0, 'C', TRUE);

        $pdf->Ln(-4);
        $pdf->setX(319);
        $pdf->cell(30, 4, "Discounted Price", 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(319);
        $pdf->cell(30, 4, "(Net of Disct. incl. VAT)", 'LBR', 0, 'C', TRUE);

        $pdf->Ln(-4);
        $pdf->setX(349);
        $pdf->cell(30, 4, "Gross Price", 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(349);
        $pdf->cell(30, 4, "(Gross of VAT & Disct.)", 'LBR', 0, 'C', TRUE);
        
        $pdf->Ln(-4);
        $pdf->setX(379);
        $pdf->cell(32, 8, "Net Amount", 'LTR', 0, 'C', TRUE);
        $pdf->cell(32, 8, "Discounted Amount", 'LTR', 0, 'C', TRUE);
        $pdf->cell(32, 8, "Gross Amount", 'LTR', 0, 'C', TRUE);
       

        $pdf->setFont('times', '', 7);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();
    }
    

    private function createPiTableHeader($pdf)
    {
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(40, 0, 'Purchase Invoice', 0, 0, 'L');
        $pdf->Ln(3);

        $pdf->SetFont('Arial','',8);
        $pdf->SetTextColor(201, 201, 201);
        $pdf->SetFillColor(35, 35, 35);

        $pdf->cell(30, 8, 'PI No', 1, 0, 'C', TRUE);
        $pdf->cell(20, 8, "Date", 1, 0, 'C', TRUE);
        $pdf->cell(29, 8, 'Reference #', 1, 0, 'C', TRUE);  
        $pdf->cell(30, 8, 'PO No', 1, 0, 'C', TRUE);        
        $pdf->cell(20, 8, "Item", 1, 0, 'C', TRUE);
        $pdf->cell(90, 8, "Description", 1, 0, 'C', TRUE);
        $pdf->cell(20, 8, "UOM", 1, 0, 'C', TRUE);
        $pdf->cell(20, 8, "Qty", 1, 0, 'C', TRUE);

        $pdf->cell(40, 4, "Net Price", 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(269);
        $pdf->cell(40, 4, "(Net of VAT & Disct.)", 'LBR', 0, 'C', TRUE);

        $pdf->Ln(-4);
        $pdf->setX(309);
        $pdf->cell(35, 4, "Discounted Price", 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(309);
        $pdf->cell(35, 4, "(Net of Disct. incl. VAT)", 'LBR', 0, 'C', TRUE);

        $pdf->Ln(-4);
        $pdf->setX(344);
        $pdf->cell(35, 4, "Unit Price", 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(344);
        $pdf->cell(35, 4, "(Gross)", 'LBR', 0, 'C', TRUE);

        $pdf->Ln(-4);
        $pdf->setX(379);
        $pdf->cell(32, 8, "Net Amount", 1, 0, 'C', TRUE);

        $pdf->setX(411);
        $pdf->cell(32, 8, "Discounted Amount", 1, 0, 'C', TRUE);

        $pdf->setX(443);
        $pdf->cell(32, 8, "Gross Amount", 1, 0, 'C', TRUE);
        
        $pdf->setFont('times', '', 7);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();
    }

    private function createCMTableHeader($pdf)
    {
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(40, 0, 'CREDIT MEMO', 0, 0, 'L');
        $pdf->Ln(3);

        $pdf->SetFont('Arial','',8);
        $pdf->SetTextColor(201, 201, 201);
        $pdf->SetFillColor(35, 35, 35);

        $pdf->cell(30, 8, 'CM No', 1, 0, 'C', TRUE);
        $pdf->cell(20, 8, "Date", 1, 0, 'C', TRUE);
        $pdf->cell(29, 8, 'Reference #', 1, 0, 'C', TRUE);  
        $pdf->cell(30, 8, 'PO No', 1, 0, 'C', TRUE);        
        $pdf->cell(20, 8, "Item", 1, 0, 'C', TRUE);
        $pdf->cell(90, 8, "Description", 1, 0, 'C', TRUE);
        $pdf->cell(20, 8, "UOM", 1, 0, 'C', TRUE);
        $pdf->cell(20, 8, "Qty", 1, 0, 'C', TRUE);

        $pdf->cell(40, 4, "Net Price", 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(269);
        $pdf->cell(40, 4, "(Net of VAT & Disct.)", 'LBR', 0, 'C', TRUE);

        $pdf->Ln(-4);
        $pdf->setX(309);
        $pdf->cell(35, 4, "Discounted Price", 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(309);
        $pdf->cell(35, 4, "(Net of Disct. incl. VAT)", 'LBR', 0, 'C', TRUE);

        $pdf->Ln(-4);
        $pdf->setX(344);
        $pdf->cell(35, 4, "Unit Price", 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(344);
        $pdf->cell(35, 4, "(Gross)", 'LBR', 0, 'C', TRUE);

        $pdf->Ln(-4);
        $pdf->setX(379);
        $pdf->cell(32, 8, "Net Amount", 1, 0, 'C', TRUE);

        $pdf->setX(411);
        $pdf->cell(32, 8, "Discounted Amount", 1, 0, 'C', TRUE);

        $pdf->setX(443);
        $pdf->cell(32, 8, "Gross Amount", 1, 0, 'C', TRUE);
        
        $pdf->setFont('times', '', 7);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();
    }

    public function searchCrf()
    {
        $supId        = $this->uri->segment(4);
        $string       = $this->uri->segment(5);
        $crfs         = $this->proformavspi_model->searchCrf($supId, $string);

        JSONResponse($crfs);
    
    }
    // NEW TAGGING AND MATCHING //


    // public function matchProformaVsPi() 
    // {
    //     $fetch_data         = $this->input->post(NULL, TRUE);       
    //     $type               = $fetch_data['type'];
    //     $profPiMerge        = array();
    //     $profVatDisc        = array();
    //     $wayParesPoProf     = array();
    //     $wayParesPoPi       = array();
    //     $getProfHead        = array();
    //     $profLineDetails    = array();
    //     $freeItems          = array();
    //     $vatDisc            = array();
    //     $getPiHead          = array();
    //     $piLineDetails      = array();
    //     $cmHead             = array();
    //     $cmLineDetails      = array();
    //     $poProfNoPairs      = array();
    //     $poPiNoPairs        = array();
    //     $flatProfLineDetails= array();
    //     $flatPiLineDetails  = array();
    //     $flatVatDisc        = array();
    //     $flatFreeItems      = array();
    //     $foundItemsInPiFromProforma = array();
    //     $notFoundItemsFromProforma  = array();
    //     $foundItemsInProfFromPi     = array();
    //     $notFoundItemsFromPi        = array();
    //     $flatFoundItemsInPiFromProforma = array();
    //     $flatFoundItemsInProfFromPi     = array();
    //     $flatCmLineDetails  = array();
    //     $discountVat        = array();
    //     $discVatSummary     = array(); 
    //     $cmLineMerge        = array();       
    //     $partiallyServed    = false;
    //     $unServed           = false;
    //     $excess             = false;
    //     $documentNo         = "";

    //     $profLineItemCounter= [];

    //     $dupeItemsinPi      = array();
    //     $dupeItemsinProf    = array();
    //     $itemsNotFoundInPiFromProf  = array();

    //     $this->db->trans_start();

    //     $getProf            = $this->proformavspi_model->getProformaInCrf($fetch_data['crfId']);
    //     $getPi              = $this->proformavspi_model->getPiInCrf($fetch_data['crfId']);
    //     $getHasDeal         = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['has_deal'];    
    //     $getDiscType        = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['disc_type'];    
    //     $documentNo         = $this->proformavspi_model->getDocNo(true);
        
    //     if($getHasDeal == "1"){
    //         $getDeals        = $this->proformavspi_model->getDeals($fetch_data['dealId']);
    //         $getNoOfDisc     = $this->proformavspi_model->getSupplierData($fetch_data['supId'],'supplier_id')['number_of_discount'];
    //     }
        
    //     foreach($getProf as $prof)
    //     {    
    //         $key = array_search($prof['po_header_id'], array_column($getPi,'po_header_id'));
    //         if ($key === false) {
    //             $wayParesPoProf[] = array('proforma_header_id' => $prof['proforma_header_id'], 'po_header_id' => $prof['po_header_id'] );
    //             $poProfNoPairs[]  = $prof['po_no'];
    //         }                
    //     } 
    //     foreach($getPi as $pi)
    //     {
    //         $key = array_search($pi['po_header_id'], array_column($getProf,'po_header_id'));
    //         if($key === false){
    //             $wayParesPoPi[] = array('pi_head_id' => $pi['pi_head_id'], 'po_header_id' => $pi['po_header_id'] );
    //             $poPiNoPairs[]  = $pi['po_no'];
    //         }
    //     }           
        
    //     if( !empty($getProf) && !empty($getPi)  )
    //     {
    //         foreach ($getProf as $get1) {
    //             $getProfHead[]          = $this->proformavspi_model->getProformaHead($get1['proforma_header_id']);
    //             $profLineDetails[]      = $this->proformavspi_model->getProfLine($get1['proforma_header_id']);
    //             $profLineItemCounter[]  = $this->proformavspi_model->getProfLineItemCounter($get1['proforma_header_id']);
    //             $freeItems[]            = $this->proformavspi_model->freeItems($get1['proforma_header_id']);
    //             $vatDisc[]              = $this->proformavspi_model->getVatDisc($get1['proforma_header_id']);
    //         }
           
    //         $prevPo = "";
    //         // dump($getPi);
    //         // die();
    //         foreach ($getPi as $get2) {
    //             $getPiHead[]        = $this->proformavspi_model->getPiHead($get2['pi_head_id']);                            
    //             $cmHead[]           = $this->proformavspi_model->getCmHead($get2['pi_head_id']);
    //             $piLineDetails[]    = $this->proformavspi_model->getPiLine($get2['pi_head_id']);    
    //             // if($prevPo != $get2['po_header_id'] ){
    //             //     $piLineDetails[]    = $this->proformavspi_model->getPiLine($get2['po_header_id']);
    //             // }               
    //             $prevPo = $get2['po_header_id'];
    //         }
    //     } else {
    //         die('no data');
    //     }

    //     // dump($profLineDetails);
    //     // dump($piLineDetails);
    //     // die();

    //     if(!empty($freeItems)){
    //         $flatFreeItems = call_user_func_array('array_merge', $freeItems);
    //     }

    //     if(!empty($cmHead)){            
    //         foreach($cmHead as $cm)
    //         {
    //             if(isset($cm)){
    //                 $cmLineDetails[] = $this->proformavspi_model->getCmLineDetails($cm['cm_head_id']);
    //             }
    //         }            
    //     }               
    //     #not compatible in 5.4
    //     // $flatCmLineDetails   = array_merge([],...$cmLineDetails);
    //     // $flatVatDisc         = array_merge([],...$vatDisc); 
    //     // $flatProfLineDetails = array_merge([],...$profLineDetails); 
    //     // $flatPiLineDetails   = array_merge([],...$piLineDetails); 
      
    //     if( !empty($cmLineDetails)){
    //         $flatCmLineDetails   = call_user_func_array('array_merge', $cmLineDetails);
    //     }
    //     if( !empty($vatDisc)){
    //         $flatVatDisc         = call_user_func_array('array_merge', $vatDisc);
    //     }        
        
    //     $flatProfLineDetails = call_user_func_array('array_merge', $profLineDetails);
    //     $flatPiLineDetails   = call_user_func_array('array_merge', $piLineDetails);
        
    //     // var_dump($flatProfLineDetails);
    //     $exit = FALSE ;
    //     $noSetup          = checkNoItemCodes($flatProfLineDetails);          
    //     $items            = [] ;
    //     if ( !empty($noSetup) ) { 

    //         foreach($noSetup as $n){
    //             $items[] = ['itemcode' => $n['item'], 'desc' => $n['idesc'] ];
    //         } 
    //         $exit = TRUE ;

    //     } else {

    //        $exit = FALSE ;

    //         foreach($getProfHead as $head)
    //         {
    //             foreach ($flatVatDisc as $dv) 
    //             {
    //                 if ($head['profId'] == $dv['proforma_header_id']) {
    //                     $discountVat[] = ['profId'   => $head['profId'], 'profCode' => $head['profCode'], 'sono' => $head['so_no'],
    //                                     'delivery' => $head['delivery'], 'customer' => $head['l_acroname'], 'pono' => $head['po_no'],
    //                                     'discId'   => $dv['discount_id'], 'discName' => $dv['discount'], 'amount' => $dv['total_discount']];
    //                 }                        
    //             }
    //         }    

    //         $uniqueVatDiscName = unique_multidim_array($discountVat,'discName');
    //         foreach($uniqueVatDiscName as $unique)
    //         {   
    //             $amount = 0;
    //             foreach($discountVat as $v)
    //             {
    //                 if($unique['discName'] == $v['discName']){
    //                     $amount += $v['amount'] ;
    //                 }
    //             }
    //             $discVatSummary[] = ['name' => 'Total '.$unique['discName'] . ' :', 'amount' => $amount ];
    //         }
        
    //         $itemCodeToFind  = "";
    //         $disc1      = 0.00;
    //         $disc2      = 0.00;
    //         $disc3      = 0.00;
    //         $disc4      = 0.00;

    //         // dump($flatProfLineDetails);
    //         // die();
    //         $morethanOneOccurrencesPi   = [];
    //         $profItemsWithManyPiItems   = [];
    //         $onlyOneOccurrencePi        = [];
    //         $morethanOneOccurrencesProf = [];
    //         $onlyOneOccurrenceProf      = [];
            
    //         foreach( $flatProfLineDetails as $count1 ) 
    //         {            
    //             $getCount = $this->proformavspi_model->countItemOccurrencesPi($count1['poId'], $count1['id']);
  
    //             if($getCount->itemCount > 1){
    //                 $morethanOneOccurrencesPi[] = $this->proformavspi_model->getItemsInPi($count1['poId'], $count1['id']);
    //                 $profItemsWithManyPiItems[] = ['profId' => $count1['profId'], 'profCode' => $count1['profCode'], 'poId' => $count1['poId'],
    //                                                'id' => $count1['id'], 'item' => $count1['item'], 'idesc' => $count1['idesc'], 'qty' => $count1['qty'],
    //                                                'uom' => $count1['uom'], 'price'  => $count1['price'], 'amount' => $count1['amount'], 
    //                                                'itemcode_loc' => $count1['itemcode_loc'], 'item_division' => $count1['item_division'],
    //                                                'item_department_code' => $count1['item_department_code'], 'item_group_code' => $count1['item_group_code'],
    //                                                'itemCountinPi' => $getCount->itemCount ];
    //             } else if( $getCount->itemCount == 1 ){
    //                 $onlyOneOccurrencePi[]      = $this->proformavspi_model->getItemsInPi($count1['poId'], $count1['id']);
    //                 $onlyOneOccurrenceProf[]    = $count1;
    //             } else if( $getCount->itemCount == 0 ){
    //                 echo $getCount->itemCount;
    //             }               
                
    //         }

    //         echo '<br>';
    //         dump($morethanOneOccurrencesPi);
    //         echo '<br>';
    //         dump($profItemsWithManyPiItems);
    //         // echo '<br>';
    //         // dump($onlyOneOccurrencePi);
    //         // echo '<br>';
    //         // dump($onlyOneOccurrenceProf);
    //         die();

            
    //         // foreach($flatPiLineDetails as $count2)
    //         // {
    //         //     $getCount2 = $this->proformavspi_model->countItemOccurrencesProf($count2['poId'], $count2['id']);
    //         //     if($getCount->itemCount > 1){
    //         //         $morethanOneOccurrencesProf[] = $this->proformavspi_model->getItemsProf($count1['poId'], $count1['id']);
    //         //     } else if( $getCount->itemCount == 1 ){
    //         //         $onlyOneOccurrenceProf[]      = $this->proformavspi_model->getItemsProf($count1['poId'], $count1['id']);
    //         //     } else if( $getCount->itemCount == 0 ){
    //         //         echo $getCount->itemCount;
    //         //     }  
    //         // }

    //         // echo '<br>';
    //         // dump($morethanOneOccurrencesProf);
    //         // echo '<br>';
    //         // dump($onlyOneOccurrenceProf);
    //         // die();

    //         foreach( $flatProfLineDetails as $find1 ) // pangitaon sa PI ang items nga naa sa proforma
    //         {
                
    //             $query = $this->db->select('i.id,i.itemcode_loc,i.item_division,i.item_department_code,i.item_group_code,ph.pi_head_id,ph.pi_no,ph.posting_date, ph.po_header_id,
    //                                         pi.item_code,pi.description,pi.uom,pi.qty,pi.direct_unit_cost,pi.amt_including_vat')
    //                               ->from('purchase_invoice_line pi')
    //                               ->join('purchase_invoice_header ph','ph.pi_head_id = pi.pi_head_id', 'left')
    //                               ->join('items i','i.itemcode_loc = pi.item_code', 'left')
    //                               ->where('ph.po_header_id',$find1['poId'])
    //                               ->where('pi.item_code',$find1['itemcode_loc'])
    //                               ->group_by('pi.item_code') //(sum qty and group by pi item code for items nga ngbalik2 sa isa ka PI)
    //                               ->get();
    //                 if($query->num_rows() > 0){
    //                     array_push($foundItemsInPiFromProforma, $query->result_array() ); 
                    
    //                 }else {
    //                     if($getHasDeal == "1"){
    //                         foreach($getDeals as $deal1)/* vendor deals   */ 
    //                         { 
    //                             if($deal1['type'] == "Item Department"){
    //                                 $itemCodeToFind = $find1['item_department_code'];
    //                             } else if($deal1['type'] == "Item"){
    //                                 $itemCodeToFind = $find1['itemcode_loc'];
    //                             } else if($deal1['type'] == "Item Group"){
    //                                 $itemCodeToFind = $find1['item_group_code'];
    //                             }
    //                             if($deal1['number'] == $itemCodeToFind ){
    //                                 if($getDiscType == "PERCENTAGE"){
    //                                     $disc1 = 1.00 - $deal1['disc_1'] * 0.01 ;
    //                                 } else if($getDiscType == "AMOUNT"){
    //                                     $disc1 = $deal1['discount_amount'];
    //                                 }
    //                                 $disc2 = 1.00 - $deal1['disc_2'] * 0.01 ;
    //                                 $disc3 = 1.00 - $deal1['disc_3'] * 0.01 ;
    //                                 $disc4 = 1.00 - $deal1['disc_4'] * 0.01 ;
    //                             }
    //                         }
    //                     }
                        
    //                     $notFoundItemsFromProforma[] = ['profId' => $find1['profId'],'profCode' => $find1['profCode'],'id'      => $find1['id'], 
    //                                                     'poId'   => $find1['poId'],  'item'     => $find1['item'],    'idesc'   => $find1['idesc'],  
    //                                                     'qty'    => $find1['qty'],   'uom'      => $find1['uom'],     'price'   => $find1['price'],  
    //                                                     'amount' => $find1['amount'],'disc1'    => $disc1,'disc2' => $disc2, 'disc3' => $disc3, 'disc4' => $disc4 ];                
    //                 }
    //         }


    //         // dump($foundItemsInPiFromProforma);
    //         // die();
        
    //         foreach($flatPiLineDetails as $find2) //pangitaon sa proforma ang naa sa PI
    //         {
    //             $query = $this->db->select('i.id,pl.itemcode_loc,i.item_division,i.item_department_code,i.item_group_code,pf.proforma_header_id,pf.proforma_code,pf.po_header_id,
    //                                         pl.item_code,pl.description,pl.qty,pl.uom,pl.price,pl.amount')
    //                             ->from('proforma_line pl')
    //                             ->join('proforma_header pf','pf.proforma_header_id = pl.proforma_header_id', 'left')
    //                             ->join('items i','i.itemcode_sup = pl.item_code','left')
    //                             ->where('pf.po_header_id',$find2['poId'])
    //                             ->where('pl.itemcode_loc',$find2['item'])
    //                             ->get();

    //             if($query->num_rows() > 0){               
    //                 foreach($flatProfLineDetails as $find3)
    //                 {
    //                     if($find2['id'] == $find3['id'] && $find2['poId'] == $find3['poId']){  
                                        
    //                         $foundItemsInProfFromPi[] = ['id' => $find3['id'],'itemcode_loc' => $find3['itemcode_loc'],'item_division' => $find3['item_division'],
    //                                                     'item_department_code' => $find3['item_department_code'],'item_group_code' => $find3['item_group_code'],
    //                                                     'proforma_header_id' => $find3['profId'],'proforma_code' => $find3['profCode'], 'po_header_id' => $find3['poId'],
    //                                                     'item_code' => $find3['item'], 'description' => $find3['idesc'],'qty' => $find3['qty'],'uom' => $find3['uom'],
    //                                                     'price' => $find3['price'], 'amount' => $find3['amount'] ];

    //                     }
                    
    //                 }       
                    
    //             } else {      
    //                 if($getHasDeal == "1"){
    //                     foreach($getDeals as $deal2)/* vendor deals   */ 
    //                     { 
    //                         if($deal2['type'] == "Item Department"){
    //                             $itemCodeToFind = $find2['item_department_code'];
    //                         } else if($deal2['type'] == "Item"){
    //                             $itemCodeToFind = $find2['itemcode_loc'];
    //                         } else if($deal2['type'] == "Item Group"){
    //                             $itemCodeToFind = $find2['item_group_code'];
    //                         }
    //                         if($deal2['number'] == $itemCodeToFind ){
    //                             if($getDiscType == "PERCENTAGE"){
    //                                 $disc1 = 1.00 - $deal2['disc_1'] * 0.01 ;
    //                             } else if($getDiscType == "AMOUNT"){
    //                                 $disc1 = $deal2['discount_amount'];                            
    //                             }
                            
    //                             $disc2 = 1.00 - $deal2['disc_2'] * 0.01 ;
    //                             $disc3 = 1.00 - $deal2['disc_3'] * 0.01 ;
    //                             $disc4 = 1.00 - $deal2['disc_4'] * 0.01 ;
    //                         }
    //                     }
    //                 }
    //                 $notFoundItemsFromPi[] = ['piId'   => $find2['piId'],   'piNo'  => $find2['piNo'], 'piDate'  => $find2['piDate'],   
    //                                         'poId'   => $find2['poId'],   'id'    => $find2['id'],   'item'    => $find2['item'],
    //                                         'idesc'  => $find2['idesc'],  'uom'   => $find2['uom'],  'qty'     => $find2['qty'],
    //                                         'direct' => $find2['direct'], 'amount'=> $find2['amount'],
    //                                         'disc1'  => $disc1,'disc2' =>$disc2, 'disc3' => $disc3,  'disc4' => $disc4 ]  ;
                    
    //             }
    //         }

    //         // dump($foundItemsInProfFromPi);
    //         // die();

    //         $flatPiItems        = call_user_func_array('array_merge', $foundItemsInPiFromProforma);
            
    //         foreach($flatPiItems as $a) // foreach($finalPi as $a)
    //         {
    //             foreach($foundItemsInProfFromPi as $b)
    //             {
    //                 if( $a['po_header_id'] == $b['po_header_id'] && $a['id'] == $b['id'] ){
    //                     if($getHasDeal == "1"){
    //                         foreach($getDeals as $deals)/* vendor deals   */ 
    //                         {
    //                             if($deals['type'] == "Item Department"){
    //                                 $itemCodeToFind = $a['item_department_code'];
    //                             } else if($deals['type'] == "Item"){
    //                                 $itemCodeToFind = $a['itemcode_loc'];
    //                             } else if($deals['type'] == "Item Group"){
    //                                 $itemCodeToFind = $a['item_group_code'];
    //                             }
    //                             if($deals['number'] == $itemCodeToFind ){
    //                                 if($getDiscType == "PERCENTAGE"){
    //                                     $disc1 = 1.00 - $deals['disc_1'] * 0.01 ;
    //                                 } else if($getDiscType == "AMOUNT"){
    //                                     $disc1 = $deals['discount_amount'];                            
    //                                 }                                
    //                                 $disc2 = 1.00 - $deals['disc_2'] * 0.01 ;
    //                                 $disc3 = 1.00 - $deals['disc_3'] * 0.01 ;
    //                                 $disc4 = 1.00 - $deals['disc_4'] * 0.01 ;
    //                             }
    //                         }
    //                     }

    //                     $profPiMerge[] = [ 'itemId'   =>$a['id'],         'poId'    =>$a['po_header_id'], 'profCode'=>$b['proforma_code'],'profItem' =>$b['item_code'],
    //                                     'profDesc' =>$b['description'],'profQty' =>$b['qty'],          'profUom' =>$b['uom'],          'profPrice'=>$b['price'],
    //                                     'profAmt'  =>$b['amount'],     'piId'    =>$a['pi_head_id'],   'piNo'    =>$a['pi_no'],        'piItem'   =>$a['item_code'], 
    //                                     'piDate'   =>$a['posting_date'],'piDesc' =>$a['description'],  'piQty'   =>$a['qty'],          'piUom'    =>$a['uom'],
    //                                     'piPrice'  =>$a['direct_unit_cost'], 'piAmt'   =>$a['amt_including_vat'], 'disc1' => $disc1,'disc2' =>$disc2, 'disc3' => $disc3, 'disc4' => $disc4,
    //                                     'partiallyServed'   => ($a['qty'] > 0 &&  $a['qty'] < $b['qty']  ) ? 1 : 0,
    //                                     'excess'            => ($a['qty'] > $b['qty']) ? 1 : 0, 
    //                                     'fullyServed'       => ( $a['qty'] > 0 && $a['qty'] == $b['qty']) ? 1 : 0 ];
    //                     // 'partiallyServed'   => ($a['qty'] > 1 &&  $a['qty'] < $b['qty']  ) ? 1 : 0,
    //                 } 
    //             }
    //         }

    //         // dump($profPiMerge);
    //         // die();
    //         if(!empty($cmHead) && !empty($flatCmLineDetails)){
    //             foreach($cmHead as $ch)
    //             {
    //                 foreach($flatCmLineDetails as $cml)
    //                 {      
    //                     if(isset($ch)){
    //                         if($ch['cm_head_id'] == $cml['cm_head_id']){
    //                             if($getHasDeal == "1"){
    //                                 foreach($getDeals as $deal3)/* vendor deals   */ 
    //                                 {
    //                                     if($deal3['type'] == "Item Department"){
    //                                         $itemCodeToFind = $cml['item_department_code'];
    //                                     } else if($deal3['type'] == "Item"){
    //                                         $itemCodeToFind = $cml['itemcode_loc'];
    //                                     } else if($deal3['type'] == "Item Group"){
    //                                         $itemCodeToFind = $cml['item_group_code'];
    //                                     }
    //                                     if($deal3['number'] == $itemCodeToFind ){
    //                                         if($getDiscType == "PERCENTAGE"){
    //                                             $disc1 = 1.00 - $deal3['disc_1'] * 0.01 ;
    //                                         } else if($getDiscType == "AMOUNT"){
    //                                             $disc1 = $deal3['discount_amount'];                            
    //                                         }                                        
    //                                         $disc2 = 1.00 - $deal3['disc_2'] * 0.01 ;
    //                                         $disc3 = 1.00 - $deal3['disc_3'] * 0.01 ;
    //                                         $disc4 = 1.00 - $deal3['disc_4'] * 0.01 ;
    //                                     }
    //                                 }
    //                             }
    //                             $cmLineMerge[] = ['cmId' => $ch['cm_head_id'], 'cmNo' => $ch['cm_no'], 'date' => $ch['posting_date'], 'item' => $cml['item_code'],
    //                                             'desc' => $cml['description'],'uom' => $cml['uom'],'qty' => $cml['qty'], 'price' => $cml['direct_unit_cost'],
    //                                             'disc1'=> $disc1, 'disc2' => $disc2, 'disc3' => $disc3, 'disc4' => $disc4, 'piNo' => $cml['pi_no']  ];
    //                         }
    //                     }
    //                 }
    //             }
    //         }

    //         $fullyServed       = array_search(1, array_column($profPiMerge, 'fullyServed'));
    //         $partiallyServed   = array_search(1, array_column($profPiMerge, 'partiallyServed'));
    //         $excess            = array_search(1, array_column($profPiMerge, 'excess'));       
    //     }

    //     $sopDeduction      = $this->proformavspi_model->getSopDeduction($fetch_data['crfId']); 
    //     // dump($sopDeduction);
    //     // die();

    //     $this->db->trans_complete();

    //     if ($this->db->trans_status() === FALSE) {
    //         $this->db->trans_rollback();
    //         $error = array('action' => 'Matching Pro-forma vs PI', 'error_msg' => $this->db->error()); //Log error message to `error_log` table
    //         $this->db->insert('error_log', $error);

    //         $msg = ['info' => 'incomplete', 'message' => 'Matching is incomplete!'];
    //     } else {

    //         if( !$exit ){
    //             // for history
    //             $transaction = array('tr_no'         => $documentNo,
    //                                 'tr_date'       => date("F d, Y - h:i:s A"),
    //                                 'crf_id'        => $fetch_data['crfId'],
    //                                 'supplier_id'   => $fetch_data['supId'],
    //                                 'customer_code' => $fetch_data['cusId'],
    //                                 'user_id'       => $this->session->userdata('user_id'),
    //                                 'filename'      => '' );
    //             $this->db->insert('profvpi_transaction', $transaction);
    //             $transactionId = $this->db->insert_id();
    //             // for history
    //             if($type == "pdf"){
    //                 $generate  = $this->generateProformaVsPi($transactionId,$fetch_data, $getProfHead, $discountVat, $discVatSummary, $profPiMerge, $fullyServed, $partiallyServed, 
    //                                                             $excess, $sopDeduction, $notFoundItemsFromProforma, $notFoundItemsFromPi,$cmHead,$cmLineMerge,$flatFreeItems);
    //             } else if($type == "excel"){
    //                 $generate      = $this->generateProformaVsPiExcel($transactionId,$fetch_data, $getProfHead, $discountVat, $discVatSummary, $profPiMerge, $fullyServed, $partiallyServed, 
    //                                                             $excess, $sopDeduction, $notFoundItemsFromProforma, $notFoundItemsFromPi,$cmHead,$cmLineMerge,$flatFreeItems);
    //             }

    //             $msg = ['info' => 'success', 'message' => 'Matching Pro-forma vs PI is complete!', 'file' => $generate, 'wayParesPoProf' => $poProfNoPairs, 'wayParesPoPi' => $poPiNoPairs ];
           
    //         } else {

    //             $msg  = ['info' => 'no-setup', 'message' => 'UNMAPPED ITEM(s) :', 'nosetup' => $items ];
    //         }

    //     }

    //     JSONResponse($msg);
    // }
}
