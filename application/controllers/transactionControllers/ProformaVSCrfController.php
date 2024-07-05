<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProformaVSCrfController extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('upload');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->library('fpdf');
        date_default_timezone_set('Asia/Manila');

        if (!$this->session->userdata('cwo_logged_in')) {

            redirect('home');
        }

        $this->load->model('proformavscrf_model');

        //Disable Cache
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        ('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    private function generateProformaVsCrf($transactionId, $crf, $mergeLine, $discountVat, $discVatSummary, $proformaHead,$freeItems)
    {
        $crfId                 = $crf;         
        $crfData               = $this->proformavscrf_model->getCrf($crfId);
        $supId                 = $crfData['supplier_id'];
        $cusId                 = $crfData['customer_code'];
        $supplier              = $this->proformavscrf_model->getSupplierData('supplier_name', 'supplier_id', $crfData['supplier_id'])['supplier_name'];
        $getPricing            = $this->proformavscrf_model->getSupplierData('pricing', 'supplier_id', $crfData['supplier_id'])['pricing'];
        $getAmounting          = $this->proformavscrf_model->getSupplierData('amounting', 'supplier_id', $crfData['supplier_id'])['amounting'];
        $getDiscountType       = $this->proformavscrf_model->getSupplierData('disc_type', 'supplier_id', $crfData['supplier_id'])['disc_type'];
        $vat                   = $this->proformavscrf_model->getVATData()['value'];
        $sopDeduction          = $this->proformavscrf_model->getSopDeduction($crfId);
        $sopCharges            = $this->proformavscrf_model->getSOpCharges($crfId);
       

        $proformaGrossTotalAmt = 0;
        $proformaNetTotalAmt   = 0;
        $proformaItemTotalAmt  = 0;
        $profItemCount         = 0;
        $addLessTotal          = 0;
        $variance              = 0;
        $sopTotalDeduction     = 0;
        $sopTotalCharges       = 0;
        $totalItemCount        = 0;
        $totalItemQty          = 0;
        $totalFreeItemsQty     = 0;
        $freeItemCount         = 0;

        $pdf = new FPDF('L', 'mm', 'Legal');
        $pdf->AddPage();
        $pdf->setDisplayMode('fullpage');

        $pdf->SetFont('Arial', 'B', 12);
        // $pdf->SetX(30);
        $pdf->Cell(40, 0, $supplier, 0, 0, 'L');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 10);
        // $pdf->SetX(30);
        $pdf->Cell(40, 0, 'PRO-FORMA SALES INVOICE vs CRF - VARIANCE REPORT', 0, 0, 'L');
        $pdf->Ln(10);

        $pdf->SetTextColor(201, 201, 201);
        $pdf->SetFillColor(35, 35, 35);
        // $pdf->SetX(30);
        $pdf->cell(50, 6, "CRF/CV No :", 1, 0, 'C', TRUE);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->cell(62, 6, $crfData['crf_no'], 'TRB', 0, 'C');
        $pdf->SetTextColor(201, 201, 201);
        $pdf->SetFillColor(35, 35, 35);
        $pdf->cell(50, 6, "CRF/CV Date :", 1, 0, 'C', TRUE);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->cell(62, 6, date("Y-m-d", strtotime($crfData['crf_date'])), 'TRB', 0, 'C');
        $pdf->SetTextColor(201, 201, 201);
        $pdf->SetFillColor(35, 35, 35);
        $pdf->cell(50, 6, "CRF/CV Amount :", 1, 0, 'C', TRUE);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->cell(64, 6, 'P ' . number_format($crfData['crf_amt'], 2), 'TRB', 0, 'C');
        $pdf->Ln(10);

        $pdf->setFont('Arial', '', 8);
        // $pdf->SetX(30);
        $pdf->Cell(40, 0, 'PRO-FORMA SALES INVOICE', 0, 0, 'L');
        $pdf->Ln(2);
        $pdf->SetTextColor(201, 201, 201);
        $pdf->SetFillColor(35, 35, 35);
        $pdf->setFont('Arial', '', 7);
        // $pdf->SetX(30);
        $pdf->cell(75, 8, "Pro-forma", 1, 0, 'C', TRUE);
        $pdf->cell(20, 8, "Item", 1, 0, 'C', TRUE);
        $pdf->cell(103, 8, "Description", 1, 0, 'C', TRUE);
        $pdf->cell(13, 8, "UOM", 1, 0, 'C', TRUE);
        $pdf->cell(12, 8, "Quantity", 1, 0, 'C', TRUE);

        $pdf->cell(25, 4, "Net Price", 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(233);
        $pdf->cell(25, 4, "(Net of VAT & Disct.)", 'LBR', 0, 'C', TRUE);

        $pdf->Ln(-4);
        $pdf->setX(258);
        $pdf->cell(28, 4, "Gross Price", 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(258);
        $pdf->cell(28, 4, "(Gross of VAT & Disct.)", 'LBR', 0, 'C', TRUE);

        $pdf->Ln(-4);
        $pdf->setX(286);
        $pdf->cell(28, 4, "Net Amount", 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(286);
        $pdf->cell(28, 4, "(Net of VAT & Disct.)", 'LBR', 0, 'C', TRUE);

        $pdf->Ln(-4);
        $pdf->setX(314);
        $pdf->cell(34, 4, "Gross Amount", 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(314);
        $pdf->cell(34, 4, "(Gross of VAT & Disct.)", 'LBR', 0, 'C', TRUE);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->setFont('times', '', 7);

        $netPrice   = 0.00;
        $netAmount  = 0.00;
        $grossPrice = 0.00;
        $grossAmount = 0.00;
        foreach ($mergeLine as $line) {
            $pdf->Ln();

            if ($getAmounting == "NETofVAT&Disc") {
                $netAmount = $line['amount'];
            } else if ($getAmounting == "GROSSofVAT&Disc") {
                if ($getDiscountType == "PERCENTAGE") {
                    $netAmount = netPrice($supId, $getPricing, $line['amount'], $line['disc1'], $line['disc2'], $line['disc3'], $line['disc4'], $line['disc5'], $line['disc6'], $vat, $cusId);
                } else if ($getDiscountType == "AMOUNT") {
                    $price = netPrice($supId, $getPricing, $line['price'], $line['disc1'], $line['disc2'], $line['disc3'], $line['disc4'], $line['disc5'], $line['disc6'], $vat, $cusId);
                    $netAmount = $line['qty'] * $price;
                }
            } else if ($getAmounting == "NETofVATwDisc") {
                $netAmount = netPrice($supId, $getPricing, $line['amount'], $line['disc1'], $line['disc2'], $line['disc3'], $line['disc4'], $line['disc5'], $line['disc6'], $vat, $cusId);
            } else if ($getAmounting == "NETofDiscwVAT") {
                $price     = netPrice($supId, $getPricing, $line['price'], $line['disc1'], $line['disc2'], $line['disc3'], $line['disc4'], $line['disc5'], $line['disc6'], $vat, $cusId);
                $netAmount = $price * $line['qty'];
            } else if ($getAmounting == "GROSSofDiscwoVAT") {
                $netAmount = netPrice($supId, $getPricing, $line['amount'], $line['disc1'], $line['disc2'], $line['disc3'], $line['disc4'], $line['disc5'],$line['disc6'], $vat, $cusId);
            }

            $netPrice   = netPrice($supId, $getPricing, $line['price'], $line['disc1'], $line['disc2'], $line['disc3'], $line['disc4'], $line['disc5'], $line['disc6'], $vat, $cusId);
            if ($getPricing == "NETofVATwDisc") {
                $grossPrice = backToGross($supId, $line['price'], $line['disc1'], $line['disc2'], $line['disc3'], $line['disc4'], $line['disc5'], $line['disc6'], $vat);
            } else if ($getPricing == "GROSSofVAT&Disc") {
                $grossPrice = backToGross($supId, $line['price'], $line['disc1'], $line['disc2'], $line['disc3'], $line['disc4'], $line['disc5'], $line['disc6'], $vat);
            } else if ($getPricing == "GROSSofDiscwoVAT") {
                $grossPrice      = backToGross($supId, $netPrice, $line['disc1'], $line['disc2'], $line['disc3'], $line['disc4'], $line['disc5'], $line['disc6'], $vat);
            } else if ($getPricing == "NETofDiscwVAT"){
                $grossPrice      = backToGross($supId, $netPrice, $line['disc1'], $line['disc2'], $line['disc3'], $line['disc4'], $line['disc5'], $line['disc6'], $vat);
            } else {
                $grossPrice = backToGross($supId, $netPrice, $line['disc1'], $line['disc2'], $line['disc3'], $line['disc4'],$line['disc5'], $line['disc6'], $vat);
            }

            // $pdf->setX(30);
            $pdf->cell(75, 5, $line['proformaCode'], 'LBR', 0, 'C');
            $pdf->cell(20, 5, $line['item'], 'BR', 0, 'C');
            $pdf->cell(103, 5, $line['desc'], 'BR', 0, 'C');
            $pdf->cell(13, 5, $line['uom'], 'BR', 0, 'C');
            $pdf->cell(12, 5, $line['qty'], 'BR', 0, 'C');
            $pdf->cell(25, 5, number_format($netPrice, 2), 'BR', 0, 'R'); //net price 
            $pdf->cell(28, 5, number_format($grossPrice, 2), 'BR', 0, 'R'); //gross price
            $pdf->cell(28, 5, number_format($netAmount, 2), 'BR', 0, 'R'); //net amount            
            $pdf->cell(34, 5, number_format($grossPrice * $line['qty'], 2), 'BR', 0, 'R'); //gross amount

            $proformaGrossTotalAmt  +=  $grossPrice * $line['qty'];
            $proformaNetTotalAmt    +=  $netAmount;
            $totalItemQty           +=  $line['qty'];
            $profItemCount++;
        }

        $pdf->setFont('Arial', 'B', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();
        // $pdf->setX(30);
        $pdf->cell(211, 5, "PRO-FORMA SALES INVOICE (PSI) GROSS TOTAL :", 'LB', 0, 'L');
        $pdf->cell(12, 5, $totalItemQty, 'B', 0, 'C');
        $pdf->cell(53, 5, "", 'B', 0, 'R');
        $pdf->cell(28, 5, "P " . number_format($proformaNetTotalAmt, 2), 'B', 0, 'R');
        $pdf->cell(34, 5, "P " . number_format($proformaGrossTotalAmt, 2), 'BR', 0, 'R');
        $pdf->Ln();
        // $pdf->setX(30);
        $pdf->cell(25, 5, "PRO-FORMA SALES INVOICE (PSI) Item Count : " . $profItemCount, '', 0, 'L');
        $pdf->Ln(10);
        /** */

        if (!empty($freeItems)) {
            $pdf->Ln(5);
            $pdf->setFont('Arial', '', 8);
            $pdf->Cell(40, 0, 'FREE ITEM(s)', 0, 0, 'L');
            $pdf->Ln(2);
            $pdf->SetTextColor(201, 201, 201);
            $pdf->SetFillColor(35, 35, 35);
            $pdf->setFont('Arial', '', 7);            
            $pdf->cell(75, 8, "Pro-forma", 1, 0, 'C', TRUE);
            $pdf->cell(20, 8, "Item", 1, 0, 'C', TRUE);
            $pdf->cell(103, 8, "Description", 1, 0, 'C', TRUE);
            $pdf->cell(13, 8, "UOM", 1, 0, 'C', TRUE);
            $pdf->cell(12, 8, "Quantity", 1, 0, 'C', TRUE);

            $pdf->cell(25, 4, "Net Price", 'LTR', 0, 'C', TRUE);
            $pdf->Ln();
            $pdf->setX(233);
            $pdf->cell(25, 4, "(Net of VAT & Disct.)", 'LBR', 0, 'C', TRUE);

            $pdf->Ln(-4);
            $pdf->setX(258);
            $pdf->cell(28, 4, "Gross Price", 'LTR', 0, 'C', TRUE);
            $pdf->Ln();
            $pdf->setX(258);
            $pdf->cell(28, 4, "(Gross of VAT & Disct.)", 'LBR', 0, 'C', TRUE);

            $pdf->Ln(-4);
            $pdf->setX(286);
            $pdf->cell(28, 4, "Net Amount", 'LTR', 0, 'C', TRUE);
            $pdf->Ln();
            $pdf->setX(286);
            $pdf->cell(28, 4, "(Net of VAT & Disct.)", 'LBR', 0, 'C', TRUE);

            $pdf->Ln(-4);
            $pdf->setX(314);
            $pdf->cell(34, 4, "Gross Amount", 'LTR', 0, 'C', TRUE);
            $pdf->Ln();
            $pdf->setX(314);
            $pdf->cell(34, 4, "(Gross of VAT & Disct.)", 'LBR', 0, 'C', TRUE);

            $pdf->SetTextColor(0, 0, 0);
            $pdf->setFont('times', '', 7);
            foreach ($freeItems as $free) {
                $pdf->Ln();
                // $pdf->setX(30);
                $pdf->cell(75, 5, $free['proforma_code'], 'LBR', 0, 'C');
                $pdf->cell(20, 5, $free['item_code'], 'BR', 0, 'C');
                $pdf->cell(103, 5, $free['description'], 'BR', 0, 'C');
                $pdf->cell(13, 5, $free['uom'], 'BR', 0, 'C');
                $pdf->cell(12, 5, $free['qty'], 'BR', 0, 'C');
                $pdf->cell(25, 5, 0.00, 'BR', 0, 'R'); //net price 
                $pdf->cell(28, 5, 0.00, 'BR', 0, 'R'); //gross price
                $pdf->cell(28, 5, 0.00, 'BR', 0, 'R'); //net amount            
                $pdf->cell(34, 5, 0.00, 'BR', 0, 'R'); //gross amount

                $totalFreeItemsQty += $free['qty'];
                $freeItemCount++;
            }

            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln();
            // $pdf->setX(30);
            $pdf->cell(211, 5, "FREE ITEM(s) TOTAL :", 'LB', 0, 'L');
            $pdf->cell(12, 5, $totalFreeItemsQty, 'B', 0, 'C');
            $pdf->cell(115, 5, "", 'BR', 0, 'R');
            $pdf->Ln();
            // $pdf->setX(30);
            $pdf->cell(25, 5, "Free Item Count : " . $freeItemCount, '', 0, 'L');
            $pdf->Ln(10);
            /** */
        }

        $totalItemCount = $profItemCount + $freeItemCount;

        $pdf->Ln();
        // $pdf->setX(30);
        $pdf->cell(40, 5, "Total Item Count : ", 0, 0, 'L');
        $pdf->cell(20, 5, $totalItemCount, 0, 0, 'L');
        $pdf->cell(106, 5, "Total Item Quantity : ", 0, 0, 'R');
        $pdf->cell(12, 5,  $totalItemQty + $totalFreeItemsQty, 0, 0, 'C');
        $pdf->Ln(10);

        if (!empty($discountVat)) {
            $pdf->SetTextColor(201, 201, 201);
            $pdf->SetFillColor(35, 35, 35);
            $pdf->setFont('Arial', '', 9);
            // $pdf->setX(30);
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
            foreach ($proformaHead as $value) {
                $height1 = $height * $value['numberOfDiscount'];
                foreach ($discountVat as $dv) {
                    // $pdf->setX(30);
                    if ($value['proforma_header_id'] == $dv['profId']) {
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
                        $previousProf = $dv['profId'];
                    }
                }
            }

            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            foreach ($discVatSummary as $sum) {
                // $pdf->setX(30);
                $pdf->Cell(230, 5, $sum['name'], 'LB', 0, 'L', 0);
                $pdf->Cell(30, 5, 'P ' . number_format($sum['amount'], 2), 'BR', 0, 'R');
                $addLessTotal += $sum['amount'];
                $pdf->Ln(5);
            }
            // $pdf->setX(30);
            $pdf->Cell(230, 5, "TOTAL PSI - Add'l & Deduction :", 'LB', 0, 'L', 0);
            $pdf->Cell(30, 5, 'P ' . number_format($addLessTotal, 2), 'BR', 0, 'R');
            $pdf->Ln(10);
        }

        if (!empty($sopCharges)){
            $pdf->SetTextColor(201, 201, 201);
            $pdf->SetFillColor(35, 35, 35);
            $pdf->setFont('Arial', '', 9);
            // $pdf->setX(30);
            $pdf->Cell(80, 6, 'SOP No ', 1, 0, 'C', TRUE);
            $pdf->Cell(30, 6, 'Date', 1, 0, 'C', TRUE);
            $pdf->Cell(120, 6, 'Charges', 1, 0, 'C', TRUE);
            $pdf->Cell(30, 6, 'Amount', 1, 0, 'C', TRUE);

            $c = 0;
            $height1 = count($sopCharges) * 5;
            foreach ($sopCharges as $charge) {
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Ln();
                // $pdf->setX(30);
                if($c == 0){
                    $pdf->cell(80, $height1, $charge['sop_no'], 'LBR', 0, 'C');
                    $pdf->cell(30, $height1, date("Y-m-d", strtotime($charge['date_created'])), 'BR', 0, 'C');
                    $pdf->cell(120,5, $charge['description'], 'BR', 0, 'C');
                    $pdf->cell(30, 5, number_format($charge['charge_amount'], 2), 'BR', 0, 'R');
                } else {
                    $pdf->cell(80, 5, "", '', 0, 'C');
                    $pdf->cell(30, 5, "", '', 0, 'C');
                    $pdf->cell(120,5, $charge['description'], 'BR', 0, 'C');
                    $pdf->cell(30, 5, number_format($charge['charge_amount'], 2), 'BR', 0, 'R');
                }
                $sopTotalCharges += $charge['charge_amount'];
                $c++ ;   
                
            }
            $pdf->Ln();
            // $pdf->setX(30);
            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->cell(260, 5, "TOTAL SOP Charges :", 'LBR', 0, 'L', 0);
            $pdf->cell(0.1, 5, "P " . number_format($sopTotalCharges, 2), 0, 0, 'R');
            $pdf->Ln(10);
        }

        if (!empty($sopDeduction)) {
            $pdf->SetTextColor(201, 201, 201);
            $pdf->SetFillColor(35, 35, 35);
            $pdf->setFont('Arial', '', 9);
            // $pdf->setX(30);
            $pdf->Cell(80, 6, 'SOP No ', 1, 0, 'C', TRUE);
            $pdf->Cell(30, 6, 'Date', 1, 0, 'C', TRUE);
            $pdf->Cell(120, 6, 'Deduction', 1, 0, 'C', TRUE);
            $pdf->Cell(30, 6, 'Amount', 1, 0, 'C', TRUE);

            $i = 0;
            $height = count($sopDeduction) * 5;
            foreach ($sopDeduction as $ded) {
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Ln();
                // $pdf->setX(30);
                if($i == 0){
                    $pdf->cell(80, $height, $ded['sop_no'], 'LBR', 0, 'C');
                    $pdf->cell(30, $height, date("Y-m-d", strtotime($ded['date_created'])), 'BR', 0, 'C');
                    $pdf->cell(120,5, $ded['description'], 'BR', 0, 'C');
                    $pdf->cell(30, 5, number_format($ded['deduction_amount'], 2), 'BR', 0, 'R');
                } else {
                    $pdf->cell(80, 5, "", '', 0, 'C');
                    $pdf->cell(30, 5, "", '', 0, 'C');
                    $pdf->cell(120,5, $ded['description'], 'BR', 0, 'C');
                    $pdf->cell(30, 5, number_format($ded['deduction_amount'], 2), 'BR', 0, 'R');
                }
                $sopTotalDeduction += $ded['deduction_amount'];
                $i++ ;   
                
            } 
            
            $pdf->Ln();
            // $pdf->setX(30);
            $pdf->setFont('Arial', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->cell(260, 5, "TOTAL SOP Deduction :", 'LBR', 0, 'L', 0);
            $pdf->cell(0.1, 5, "P " . number_format($sopTotalDeduction, 2), 0, 0, 'R');
            $pdf->Ln(10);
        }

        /*   GET VARIANCE     */
        $pdf->Cell(230, 5, 'TOTAL PRO-FORMA SALES INVOICE (PSI) (Gross Amount) : ', 0, 0, 'L');
        $pdf->cell(30, 5, "P " . number_format($proformaGrossTotalAmt, 2), 0, 0, 'R');
        $pdf->Ln(5);
        $pdf->Cell(55, 5, '', 0, 0, 'R');
        $pdf->Cell(48, 5, 'Add: TOTAL SOP Charges ', 0, 0, 'L');
        $pdf->Cell(127, 5, '', 0, 0, 'R');
        $pdf->cell(30, 5, "P " . number_format($sopTotalCharges, 2), 0, 0, 'R');
        $pdf->Ln(5);
        $pdf->Cell(105, 5, 'Less: TOTAL SOP Deduction ', 0, 0, 'R');
        $pdf->Cell(125, 5, '', 0, 0, 'R');
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->cell(30, 5, "P " . number_format($sopTotalDeduction, 2), 0, 0, 'R');
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Ln(5);
        $pdf->Cell(230, 5, '', 0, 0, 'L');
        $pdf->cell(30, 5, "P " . number_format($proformaGrossTotalAmt + $sopTotalCharges + $sopTotalDeduction, 2), 0, 0, 'R');
        $pdf->Ln(5);
        $pdf->Cell(230, 5, 'TOTAL CRF/CV Amount : ', 0, 0, 'L');
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->cell(30, 5, "P " . number_format($crfData['crf_amt'], 2), 0, 0, 'R');
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Ln(5);
        
        $pdf->Cell(230, 5, 'Variance :', 0, 0, 'L');
        $pdf->cell(30, 5, "P " . number_format($proformaGrossTotalAmt + $sopTotalCharges + $sopTotalDeduction - $crfData['crf_amt'], 2), 0, 0, 'R');
        $pdf->Ln(5);


        $supAcroname = $this->proformavscrf_model->getSupplierData('acroname', 'supplier_id', $crfData['supplier_id'])['acroname'];
        $cusAcroname = $this->proformavscrf_model->getCustomerData($crfData['customer_code'], 'l_acroname');
        $fileName    = $supAcroname . '-' . $cusAcroname->l_acroname . '-' . $crfData['crf_no'] . time() . '.pdf';

        // for history
        $this->db->set('filename', $fileName)
                 ->where('tr_id', $transactionId)
                 ->update('profvcrf_transaction');
        // for history

        $pdf->Output('files/Reports/ProformaVsCrf/' . $fileName, 'F');

        return $fileName;
    }

    public function getSuppliersForCRF()
    {
        $result = $this->proformavscrf_model->getSuppliers();
        return JSONResponse($result);
    }

    public function getSop()
    {
        $supId        = $this->uri->segment(4);
        $cusId        = $this->uri->segment(5);
        $result       = $this->proformavscrf_model->getData('SELECT h.sop_id,h.sop_no,h.net_amount FROM sop_head h 
                                                             WHERE NOT EXISTS (SELECT * FROM crf f WHERE f.sop_id = h.sop_id )
                                                             AND status IN (0,1)
                                                             AND  supplier_id = ' . $supId . ' AND customer_code = ' . $cusId);
        return JSONResponse($result);
    }

    public function getCustomersForCRF()
    {
        $result = $this->proformavscrf_model->getCustomers();
        JSONResponse($result);
    }

    public function getUnAppliedProforma()
    {
        $supId        = $this->uri->segment(4);
        $crfId        = $this->uri->segment(5);
        $fetch_data   = json_decode($this->input->raw_input_stream, true);
        $str          = $fetch_data['str'];

        $loadProforma = $this->proformavscrf_model->getUnAppliedProforma($crfId, $supId, $str);
        JSONResponse($loadProforma);
    }

    public function getAppliedProforma()
    {
        $crfId      = $this->uri->segment(4);
        $supId      = $this->uri->segment(5);
        $getDiscVat = array();
        $profsDiscV = array();
        $returnArr  = array();
        $getProfs   = $this->proformavscrf_model->getAppliedProforma($crfId, $supId);

        foreach ($getProfs as $get) {
            $getDiscVat[] = $this->proformavscrf_model->getSumDiscVat($get['proforma_header_id'], $supId);
        }
        $i = 0;

        $findNull = array_search(null, array_column($getDiscVat, 'proforma_header_id'));


        if ($findNull === false) {
            foreach ($getProfs as $get1) {
                foreach ($getDiscVat as $get2) {
                    if ($get1['proforma_header_id'] == $get2['proforma_header_id']) {
                        $profsDiscV[$i]['proforma_header_id'] = $get1['proforma_header_id'];
                        $profsDiscV[$i]['supplier_id']        = $get1['supplier_id'];
                        $profsDiscV[$i]['proforma_code']      = $get1['proforma_code'];
                        $profsDiscV[$i]['delivery_date']      = $get1['delivery_date'] != "" ? date("Y-m-d", strtotime($get1['delivery_date'])) : "";
                        $profsDiscV[$i]['po_no']              = $get1['po_no'];
                        $profsDiscV[$i]['item_total']         = $get1['amount'];
                        $profsDiscV[$i]['add_less']           = $get2['discVat'];
                        $profsDiscV[$i]['total']              = $get1['amount'] + $get2['discVat'];
                        $i++;
                    }
                }
            }
        } else {
            foreach ($getProfs as $get3) {
                $profsDiscV[$i]['proforma_header_id'] = $get3['proforma_header_id'];
                $profsDiscV[$i]['supplier_id']        = $get3['supplier_id'];
                $profsDiscV[$i]['proforma_code']      = $get3['proforma_code'];
                $profsDiscV[$i]['delivery_date']      = $get3['delivery_date'] != ""? date("Y-m-d", strtotime($get3['delivery_date'])) : "";
                $profsDiscV[$i]['po_no']              = $get3['po_no'];
                $profsDiscV[$i]['item_total']         = $get3['amount'];
                $profsDiscV[$i]['add_less']           = 0;
                $profsDiscV[$i]['total']              = $get3['amount'] + 0;
                $i++;
            }
        }
        $getVendorsDeal = $this->proformavscrf_model->getVendorsDealBySupplier($supId);
        $returnArr = ['profs' => $profsDiscV, 'deal' => $getVendorsDeal];

        JSONResponse($returnArr);
    }

    public function applyProforma()
    {
        $crfId      = $this->uri->segment(4);
        $supId      = $this->uri->segment(5);
        $fetch_data = json_decode($this->input->raw_input_stream, true);
        $profId     = $fetch_data['id'];
        $prof       = $this->proformavscrf_model->getProfData($profId);

        $query = $this->db->select('proforma_header_id')
                          ->where('crf_id', $crfId)
                          ->where('proforma_header_id', $profId)
                          ->where('supplier_id', $supId)
                          ->where('customer_code', $prof['customer_code'])
                          ->get('crf_line');

        // var_dump($query);
        // die();
        if ($query->num_rows() == 0) {
            $insert = array(
                'crf_id'              => $crfId,
                'proforma_header_id'  => $profId,
                'pi_head_id'          => 0,
                'po_header_id'        => $prof['po_header_id'],
                'supplier_id'         => $supId,
                'customer_code'       => $prof['customer_code']
            );
            $this->db->insert('crf_line', $insert);
            $this->db->set('crf_id', $crfId)->where('proforma_header_id', $profId)->update('proforma_header');
            $this->db->set('crf_id', $crfId)
                     ->where('proforma_header_id', $profId)
                     ->where('supplier_id', $supId)
                     ->where('customer_code', $prof['customer_code'])
                     ->update('report_status');

            die("success");
        } else {
            die("exists");
        }
    }

    public function untagProforma()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, true);
        $untag      = $this->proformavscrf_model->untagProforma($fetch_data['profId']);
        if ($untag) {
            $this->db->delete('crf_line', array('crf_id' => $fetch_data['crfId'], 'pi_head_id' => 0, 'proforma_header_id' => $fetch_data['profId'], 'supplier_id' => $fetch_data['supId']));
            $this->db->set('crf_id', 0)
                     ->where('supplier_id', $fetch_data['supId'])
                     ->where('proforma_header_id', $fetch_data['profId'])
                     ->update('report_status');
            die("success");
        } else {
            die("failed");
        }
    }

    public function uploadCrf()
    {
        $fetch_data   = $this->input->post(NULL, TRUE);
        $supId        = $fetch_data['selectSupplier'];
        $cusId        = $fetch_data['selectCustomer'];
        $sopId        = $fetch_data['selectSop'];
        $fileName     = $_FILES['crfFile']['name'];
        $Temp         = $_FILES['crfFile']['tmp_name'];
        $crfContent   = file_get_contents($_FILES['crfFile']['tmp_name']);

        $this->db->trans_start();

        $blits        = str_replace('"', "", $crfContent);
        $refined      = explode("|", $blits);
        $countRefined = count($refined);

        $docNo        = $this->proformavscrf_model->getDocNo(true);
        $getSupplier  = $this->proformavscrf_model->getSupplierData('supplier_code', 'supplier_id', $supId)['supplier_code'];

        if ($getSupplier == $refined[6]) {
            $insert     = [ 'crf_no'            => $refined[0],
                            'crf_date'          => date("Y-m-d", strtotime($refined[1])),
                            'crf_status'        => $refined[2],
                            'collector_name'    => $refined[3],
                            'crf_amt'           => str_replace(',', '', $refined[4]),
                            'paid_amt'          => str_replace(',', '', $refined[5]),
                            'date_uploaded'     => date("Y-m-d"),
                            'supplier_id'       => $supId,
                            'remarks'           => $refined[11],
                            'customer_code'     => $cusId,
                            'user_id'           => $this->session->userdata('user_id'),
                            'sop_id'            => $sopId,
                            'status'            => 'PENDING',
                            'audited'           => 0 ];
            $crfId      = $this->proformavscrf_model->uploadCrf($refined[0], $supId, $cusId, $insert);

            if ($crfId) {
                $ledger       = ['reference_no'      => $docNo,
                                 'posting_date'      => date("Y-m-d", strtotime($refined[1])),
                                 'transaction_date'  => '',
                                 'doc_type'          => 'Payment',
                                 'doc_no'            => $refined[0],
                                 'invoice_no'        => '',
                                 'po_reference'      => '',
                                 'credit'            => 0,
                                 'debit'             => str_replace(',', '', $refined[4]),
                                 'tag'               => ($cusId == 1) ? 'CRF' : 'CV',
                                 'supplier_id'       => $supId,
                                 'crf_id'            => $crfId,
                                 'user_id'           => $this->session->userdata('user_id') ];
                $saveDocument = ['document_name'  => $fileName,
                                 'document_type'  => 'Check Request Form',
                                 'document_path'  => "files/PROFORMAPIVSCRF/".$fileName,
                                 'uploaded_on'    => date("Y-m-d H:i:s"),
                                 'supplier_id'    => $fetch_data['selectSupplier'],
                                 'customer_code'  => $fetch_data['selectCustomer'],
                                 'user_id'        => $this->session->userdata('user_id')] ;  
                $l = $this->db->insert('subsidiary_ledger', $ledger);                                                  
                $this->db->insert('uploaded_transaction_documents', $saveDocument);
                move_uploaded_file($Temp,"files/PROFORMAPIVSCRF/".$fileName);
                
                if ($l) {
                    $this->db->trans_complete();

                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $error = array('action' => 'Uploading CRF/CV', 'error_msg' => $this->db->error()); //Log error message to `error_log` table
                        $this->db->insert('error_log', $error);
                        die("incomplete");
                    } else {
                        die("success");
                    }
                }
            }
        }
    }

    public function getCrfs()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, true);
        $result = $this->proformavscrf_model->getCrfs($fetch_data['supId'], $fetch_data['cusId'], $fetch_data['from'], $fetch_data['to']);
        JSONResponse($result);
    }

    public function matchProformaVsCrf()
    {
        $fetch_data      = $this->input->post(NULL, TRUE);
        $appliedProforma = $fetch_data['applied'];
        $crfId           = $fetch_data['crf'];
        $proformaHead    = array();
        $proformaLine    = array();
        $freeItems       = array();
        $proformaDiscVat = array();
        $getDeals        = array();
        $mergeLine       = array();
        $vat             = array();
        $flatVatDisc     = array();
        $flatProformaLine = array();
        $flatFreeItems   = array();
        $discVatSummary  = array();
        $discountVat     = array();
        $sopCharges      = array();
        $exit            = FALSE;

        $documentNo      = "";
        $itemCodeToFind  = "";
        $getNoOfDiscount = 0;

        $this->db->trans_start();

        $documentNo      = $this->proformavscrf_model->getDocNo2(true);
        $crfData         = $this->proformavscrf_model->getCrf($crfId);
        $supId           = $crfData['supplier_id'];
        $cusId           = $crfData['customer_code'];
        $getHasDeal      = $this->proformavscrf_model->getSupplierData('has_deal', 'supplier_id', $supId)['has_deal'];        
        $getDiscountType = $this->proformavscrf_model->getSupplierData('disc_type', 'supplier_id', $supId)['disc_type'];

        foreach ($appliedProforma as $applied) {
            $proformaHead[]     = $this->proformavscrf_model->proformaHead($applied['proforma_header_id']);
            $proformaDiscVat[]  = $this->proformavscrf_model->proformaDiscVat($applied['proforma_header_id']);
        }

        foreach ($proformaHead as $head) {
            $proformaLine[]     = $this->proformavscrf_model->proformaLine($head['proforma_header_id'], $supId );
            $freeItems[]        = $this->proformavscrf_model->freeItems($head['proforma_header_id']);
        }
        
        $flatVatDisc = call_user_func_array('array_merge', $proformaDiscVat);
        if (!empty($freeItems)) {
            $flatFreeItems = call_user_func_array('array_merge', $freeItems);
        }

        foreach ($proformaHead as $head2) {
            foreach ($flatVatDisc as $dv) {
                if ($head2['proforma_header_id'] == $dv['proforma_header_id']) {
                    $discountVat[] = [
                        'profId'   => $head2['proforma_header_id'], 'profCode' => $head2['proforma_code'], 'sono' => $head2['so_no'],
                        'delivery' => $head2['delivery_date'], 'customer' => $head2['l_acroname'], 'pono' => $head2['po_no'],
                        'discId'   => $dv['discount_id'], 'discName' => $dv['discount'], 'amount' => $dv['total_discount']
                    ];
                }
            }
        }

        $uniqueVatDiscName = unique_multidim_array($discountVat, 'discName');
        foreach ($uniqueVatDiscName as $unique) {
            $amount = 0;
            foreach ($discountVat as $v) {
                if ($unique['discName'] == $v['discName']) {
                    $amount += $v['amount'];
                }
            }
            $discVatSummary[] = ['name' => 'Total ' . $unique['discName'] . ' :', 'amount' => $amount];
        }

        // $flatProformaLine = array_merge([], ...$proformaLine); #error in 5.4
        $flatProformaLine = call_user_func_array('array_merge', $proformaLine);
        $noSetup          = checkNoItemCodes($flatProformaLine);      
        $items            = [] ;


        if ( !empty($noSetup) ) {  
            foreach($noSetup as $n){
                $items[] = ['itemcode' => $n['item_code'], 'desc' => $n['description'] ];
            } 
            
            $exit = TRUE ;

        } else {

            $disc1    = 0.00;
            $disc2    = 0.00;
            $disc3    = 0.00;
            $disc4    = 0.00;
            $disc5    = 0.00;
            $disc6    = 0.00;
            $discAmt1 = 0.00;
            $discAmt2 = 0.00;
            $discAmt3 = 0.00;
            $discAmt4 = 0.00;
            $discAmt5 = 0.00;
            $discAmt6 = 0.00;
           
            foreach ($proformaHead as $ph) /*  head  */ {

                foreach ($appliedProforma as $appl) {
                    if($ph['proforma_header_id'] == $appl['proforma_header_id']){
                        $getDeals = [];
                        $getDeals = $this->proformavscrf_model->getDeals($appl['dealId']);
                    }
                }
                foreach ($flatProformaLine as $data) /* line   */ {

                    if ($ph['proforma_header_id'] == $data['proforma_header_id']) {
                        if ($getHasDeal == "1") {
                            if ($getDiscountType == "PERCENTAGE" || $getDiscountType == "AMOUNT" )  {
                                foreach ($getDeals as $deals) { /* vendor deals   */
                                    if ($deals['type'] == "Item Department") {
                                        $itemCodeToFind = $data['item_department_code'];
                                    } else if ($deals['type'] == "Item") {
                                        $itemCodeToFind = $data['itemcode_loc'];
                                    } else if ($deals['type'] == "Item Group") {
                                        $itemCodeToFind = $data['item_group_code'];
                                    }
                                    // echo $itemCodeToFind ;
                                    if ($deals['number'] == $itemCodeToFind) {
                                        if ($getDiscountType == "PERCENTAGE") {
                                            $disc1 = 1.00 - $deals['disc_1'] * 0.01;
                                            $disc2 = 1.00 - $deals['disc_2'] * 0.01;
                                            $disc3 = 1.00 - $deals['disc_3'] * 0.01;
                                            $disc4 = 1.00 - $deals['disc_4'] * 0.01;
                                            $disc5 = 1.00 - $deals['disc_5'] * 0.01;
                                            $disc6 = 1.00 - $deals['disc_6'] * 0.01;
                                            
                                            $mergeLine[] = ['proformaCode' => $ph['proforma_code'], 'delivery' => $ph['delivery_date'], 'customer' => $ph['customer_code'],
                                                            'item'         => $data['item_code'],   'desc'     => $data['description'], 'qty'        => $data['qty'],
                                                            'uom'          => $data['uom'],         'price'    => $data['price'],       'amount'     => $data['amount'],
                                                            'disc1'        => $disc1, 'disc2' => $disc2, 'disc3' => $disc3, 'disc4' => $disc4 , 'disc5' => $disc5, 'disc6' => $disc6 ];
                                        } else if ($getDiscountType == "AMOUNT") {
                                            $discAmt1 = $deals['disc_1'];
                                            $discAmt2 = $deals['disc_2'];
                                            $discAmt3 = $deals['disc_3'];
                                            $discAmt4 = $deals['disc_4'];
                                            $discAmt5 = $deals['disc_5'];
                                            $discAmt6 = $deals['disc_6'];
                                            $mergeLine[] = ['proformaCode' => $ph['proforma_code'], 'delivery' => $ph['delivery_date'], 'customer' => $ph['customer_code'],
                                                            'item'         => $data['item_code'],   'desc'     => $data['description'], 'qty'        => $data['qty'],
                                                            'uom'          => $data['uom'],         'price'    => $data['price'],       'amount'     => $data['amount'],
                                                            'disc1'        => $discAmt1, 'disc2' => $discAmt2, 'disc3' => $discAmt3, 'disc4' => $discAmt4, 'disc5' => $discAmt5, 'disc6' => $discAmt6];
                                        } 
                                    }                                
                                }
                            } else if ( $getDiscountType == "NONE"){
                                $mergeLine[] = ['proformaCode' => $ph['proforma_code'], 'delivery' => $ph['delivery_date'], 'customer'   => $ph['customer_code'],
                                                'item'         => $data['item_code'],   'desc'     => $data['description'], 'qty'        => $data['qty'],
                                                'uom'          => $data['uom'],         'price'    => $data['price'],       'amount'     => $data['amount'],
                                                'disc1'        => 0, 'disc2' => 0, 'disc3' => 0, 'disc4' => 0, 'disc5' => 0, 'disc6' => 0];
                            }
                        } else {

                        }

                    }

                }
                // break;
            }
            
            $exit = FALSE ;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $error = array('action' => 'Matching Pro-forma vs CRF', 'error_msg' => $this->db->error()); //Log error message to `error_log` table
            $this->db->insert('error_log', $error);

            $msg = ['info' => 'incomplete', 'message' => 'Matching is incomplete!'];
        } else {

            if( !$exit ) {
            // for history
                $transaction = array(
                    'tr_no'         => $documentNo,
                    'tr_date'       => date("F d, Y - h:i:s A"),
                    'crf_id'        => $crfId,
                    'supplier_id'   => $supId,
                    'customer_code' => $cusId,
                    'user_id'       => $this->session->userdata('user_id'),
                    'filename'      => ''
                );
                $this->db->insert('profvcrf_transaction', $transaction);
                $transactionId = $this->db->insert_id();
                // for history

                $generate      = $this->generateProformaVsCrf($transactionId, $crfId, $mergeLine, $discountVat, $discVatSummary, $proformaHead, $flatFreeItems);
                $msg = ['info' => 'success', 'message' => 'Matching Pro-forma vs CRF is complete!', 'file' => $generate];

            } else {

                $msg  = ['info' => 'no-setup', 'message' => 'UNMAPPED ITEM(s) :', 'nosetup' => $items ];
            }
        }

        JSONResponse($msg);
    }
    

    public function auditCrf()
    {
        $fetch_data   = $this->input->post(NULL,TRUE);
        $msg          = [];
        $change       = $this->db->set('audited', 1)
                                 ->where('crf_id', $fetch_data['crfId'])
                                 ->update('crf');
        
        if ($change) {
            $msg = ['info' => 'success', 'message' => 'CRF/CV is tagged as AUDITED!'];
        } else {
            $msg = ['info' => 'error', 'message' => 'Failed to AUDIT CRF/CV!'];
        }

        JSONResponse($msg);
    }

    public function tagAsMatchedCrf()
    {
        $fetch_data   = $this->input->post(NULL,TRUE);
        $msg          = [];
        $change       = $this->db->set('status', 'MATCHED')
                                 ->where('crf_id', $fetch_data['crfId'])
                                 ->update('crf');
        
        if ($change) {
            $msg = ['info' => 'success', 'message' => 'CRF/CV is tagged as MATCHED!'];
        } else {
            $msg = ['info' => 'error', 'message' => 'Failed to tag CRF/CV!'];
        }

        JSONResponse($msg);
    }

    public function replaceCRF()
    {
        $fetch_data   = $this->input->post(NULL,TRUE);
        $fileName     = $_FILES['newCrf']['name'];
        $Temp         = $_FILES['newCrf']['tmp_name'];
        $crfContent   = file_get_contents($_FILES['newCrf']['tmp_name']);

        $this->db->trans_start();
        
        $blits        = str_replace('"', "", $crfContent);
        $refined      = explode("|", $blits);
        $countRefined = count($refined);

        $getSupplier  = $this->proformavscrf_model->getSupplierData('supplier_code', 'supplier_id', $fetch_data['supId'])['supplier_code'];
        if ($getSupplier == $refined[6]) {

            $replace = ['crf_no'            => $refined[0],
                        'crf_date'          => date("Y-m-d", strtotime($refined[1])),
                        'crf_status'        => $refined[2],
                        'collector_name'    => $refined[3],
                        'crf_amt'           => str_replace(',', '', $refined[4]),
                        'paid_amt'          => str_replace(',', '', $refined[5]),
                        'date_uploaded'     => date("Y-m-d"),
                        'remarks'           => $refined[11] ];
            
            $c = $this->db->where('crf_id', $fetch_data['crfId'])
                          ->update('crf',$replace);

            if($c){
                $ledger       = ['posting_date'      => date("Y-m-d", strtotime($refined[1])),
                                 'doc_no'            => $refined[0],
                                 'debit'             => str_replace(',', '', $refined[4]),
                                 'tag'               => "" ];//($cusId == 1) ? 'CRF' : 'CV'];
                $this->db->update('subsidiary_ledger', $ledger);
                $msg =['info' => 'success', 'message' => 'CRF/CV successfully replaced!'];
            } else {
                $msg =['info' => 'error', 'message' => 'Failed to replace CRF/CV!'];
            }
        }

        JSONResponse($msg);
    }

    public function tagAsClosedCRF()
    {
        $fetch_data   = $this->input->post(NULL,TRUE);
        $msg          = [];
        $change       = $this->db->set('crfvspi', 1)
                                 ->where('crf_id', $fetch_data['crfId'])
                                 ->update('crf');

        if ($change) {
            $msg = ['info' => 'success', 'message' => 'CRF/CV is CLOSED!'];
        } else {
            $msg = ['info' => 'error', 'message' => 'Failed to CLOSE CRF/CV!'];
        }
    
        JSONResponse($msg);
    }

    public function trackCRF()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, true);
        $prof       = $this->proformavscrf_model->loadProfInCrf($fetch_data['crfId']);
        $pi         = $this->proformavscrf_model->loadPiInCrf($fetch_data['crfId']);

        $result     = ['prof' => $prof, 'pi' => $pi] ; 
        JSONResponse($result);  
    }

    public function crfStatistics()
    {
        $statistics = $this->proformavscrf_model->getCrfStatistics();
        JSONResponse($statistics);
    }
}
