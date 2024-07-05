 <?php
defined('BASEPATH') or exit('No direct script access allowed');

class SOPController extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('Pdf');
        $this->load->library('evalmath');
        $this->load->helper(array('form', 'url'));
        date_default_timezone_set('Asia/Manila');

        if (!$this->session->userdata('cwo_logged_in')) {

            redirect('home');
        }

        $this->load->model('sop_model');
        //Disable Cache
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        ('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }   

    // private function printSOP($sopId)
    // {
    //     $supplier      = $this->sop_model->getData('SELECT s.supplier_code, s.supplier_name FROM sop_head sop INNER JOIN suppliers s ON s.supplier_id = sop.supplier_id WHERE sop.sop_id = ' . $sopId);
    //     $customer      = $this->sop_model->getData('SELECT c.customer_code, c.customer_name FROM sop_head sop INNER JOIN customers c ON c.customer_code = sop.customer_code WHERE sop.sop_id = ' . $sopId);
    //     $headData      = $this->sop_model->getHeadData($sopId);
    //     $invoiceData   = $this->sop_model->getInvoiceData($sopId);
    //     $deductionData = $this->sop_model->getDeductionData($sopId);
    //     $chargesData   = $this->sop_model->getChargesData($sopId);
    //     $vat           = $this->sop_model->getVATData()['value'];
    //     $invclerk      = "";
    //     $pricing       = "";

    //     $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array(215, 279.4), true, 'UTF-8', false); //215.9 by 279.4 mm

    //     // set document information
    //     $pdf->SetCreator(PDF_CREATOR);
    //     $pdf->SetAuthor('Mariel Taray');
    //     $pdf->SetTitle('CWO-SOP');
    //     $pdf->SetSubject('CWO-SOP');
    //     $pdf->SetKeywords('CWO, SOP');

    //     // remove default header/footer
    //     $pdf->setPrintHeader(false);
    //     $pdf->setPrintFooter(false);

    //     // set default header data
    //     $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 004', PDF_HEADER_STRING);

    //     // set header and footer fonts
    //     $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    //     $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    //     // set default monospaced font
    //     $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    //     // set margins
    //     $pdf->SetMargins(17, 15, 15);
    //     $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    //     $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    //     // set auto page breaks
    //     $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    //     // set image scale factor
    //     $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    //     // set some language-dependent strings (optional)
    //     if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    //         require_once(dirname(__FILE__) . '/lang/eng.php');
    //         $pdf->setLanguageArray($l);
    //     }

    //     $pdf->AddPage();

    //     $pdf->SetFont('helvetica', 'B', 12);
    //     $pdf->Cell(0, 5, 'ALTURAS GROUP OF COMPANIES', 0, 0, 'C');
    //     $pdf->Ln(5);
    //     $pdf->SetFont('helvetica', 'B', 10);
    //     $pdf->Cell(0, 5, 'B. INTING ST., TAGBILARAN CITY', 0, 0, 'C');
    //     $pdf->Ln(10);
    //     $pdf->Cell(0, 5, 'SUMMARY OF PAYMENTS', 0, 0, 'C');
    //     $pdf->Ln(15);

    //     $pdf->SetFont('helvetica', '', 9);
    //     $pdf->Cell(20, 5, 'SUPPLIER :', 0, 0, 'L');
    //     $pdf->Cell(110, 5, $supplier['supplier_code'] . ' - ' . $supplier['supplier_name'], 0, 0, 'L');
    //     $pdf->Cell(18, 5, 'NUMBER : ', 0, 0, 'L');
    //     $pdf->Cell(35, 5, $headData['sop_no'], 0, 0, 'R');
    //     $pdf->Ln(5);
    //     $pdf->Cell(20, 5, 'SECTION :', 0, 0, 'L');
    //     $pdf->Cell(110, 5, $customer['customer_code'] . ' - ' . $customer['customer_name'], 0, 0, 'L');
    //     $pdf->Cell(18, 5, 'DATE : ', 0, 0, 'L');
    //     $pdf->Cell(35, 5, date("m/d/Y", strtotime($headData['date_created'])), 0, 0, 'R');
    //     $pdf->Ln(5);
    //     $pdf->Cell(0, 0, '', 'B', '', '', false);
    //     $pdf->Ln(5);

    //     $pdf->SetFont('helvetica', 'B', 10);

    //     $pdf->Cell(40, 8, 'PO NO.', 0, 0, 'L');
    //     $pdf->Cell(30, 8, 'PO DATE', 0, 0, 'L');

    //     $pdf->Cell(40, 4, 'PROFORMA', 0, 0, 'L');
    //     $pdf->Ln();
    //     $pdf->setX(87);
    //     $pdf->Cell(40, 4, 'INVOICE NO.', 0, 0, 'L');

    //     $pdf->Ln(-4.7);
    //     $pdf->setX(127);
    //     $pdf->Cell(32, 4, 'PROFORMA', 0, 0, 'L');
    //     $pdf->Ln();
    //     $pdf->setX(127);
    //     $pdf->Cell(32, 4, 'INVOICE DATE', 0, 0, 'L');

    //     $pdf->Ln(-4);
    //     $pdf->setX(159);
    //     $pdf->Cell(40, 8, 'AMOUNT', 0, 0, 'R');
    //     $pdf->Ln(4);
    //     $pdf->Cell(0,0,'','B','','',false);
    //     $pdf->Ln(5);


    //     $pdf->SetFont('helvetica', '', 9);

    //     $invTotal    = 0;
    //     $chargeTotal = 0;
    //     $dedTotal    = 0;
    //     $net         = 0;
    //     foreach( $invoiceData as $inv)
    //     {
    //         $pdf->Cell(40, 0, $inv['po_no'], 0, 0, 'L');
    //         $pdf->Cell(30, 0, date("m-d-Y", strtotime($inv['po_date'])), 0, 0, 'L');
    //         $pdf->Cell(40, 0, $inv['so_no'], 0, 0, 'L');
    //         $pdf->Cell(32, 0, $inv['order_date'] != "" ? date("m-d-Y", strtotime($inv['order_date'])) : "", 0, 0, 'L');
    //         $pdf->Cell(40, 0, number_format($inv['invoice_amount'] ,2), 0, 0, 'R');

    //         $invTotal      += $inv['invoice_amount'];
    //         $invclerk      = $inv['invclerk'];
    //         $pricing       = $inv['pricing'];
    //         $pdf->Ln();
    //     }

    //     $pdf->Ln(2);
    //     $pdf->Cell(0,0,'------------------------------',0,0,'R');
    //     $pdf->Ln(3);
    //     $pdf->Cell(40, 0, '', 0, 0, 'L');
    //     $pdf->Cell(40, 0, 'Pro-forma Sales Invoice Total', 0, 0, 'L');
    //     $pdf->Cell(103, 0,'P '. number_format($invTotal ,2), 0, 0, 'R');
    //     $pdf->Ln(5);
    //     $pdf->SetFont('helvetica', 'I', 7);
    //     $pdf->Cell(43, 0, '', 0, 0, 'L');
    //     $pdf->Cell(30, 0, 'PSI (Net of VAT)', 0, 0, 'L');
    //     $pdf->Cell(30, 0, 'P '. number_format($invTotal / $vat  ,2), 0, 0, 'R');
    //     $pdf->Ln(4);
    //     $pdf->Cell(43, 0, '', 0, 0, 'L');
    //     $pdf->Cell(30, 0, 'VAT', 0, 0, 'L');
    //     $pdf->Cell(30, 0, 'P '. number_format( $invTotal - ( $invTotal / $vat  ) ,2), 0, 0, 'R');
    //     $pdf->Ln(5);

    //     $pdf->SetFont('helvetica', '', 9);
    //     if(  !empty($chargesData) ){
    //         $pdf->Cell(40, 0, '', 0, 0, 'L');
    //         $pdf->Cell(40, 0, 'Add : Charges', 0, 0, 'L');
    //         $pdf->Ln();
    //         foreach( $chargesData as $charge)
    //         {
    //             $pdf->Cell(45, 0, '', 0, 0, 'L');
    //             $pdf->Cell(80,5, $charge['description'],0,0,'L');
    //             $pdf->Cell(30,5, number_format($charge['charge_amount'],2),0,0,'R');

    //             $chargeTotal += $charge['charge_amount'] ;
    //             $pdf->Ln();
    //         }

    //         $pdf->Cell(183, 0,'P '. number_format($chargeTotal ,2), 0, 0, 'R');
    //         $pdf->Ln(2);
    //         $pdf->Cell(0,0,'------------------------------',0,0,'R');
    //         $pdf->Ln(2);
    //     }

    //     if( !empty($deductionData) ){
    //         $pdf->Cell(40, 0, '', 0, 0, 'L');
    //         $pdf->Cell(40, 0, 'Less : Deductions', 0, 0, 'L');
    //         $pdf->Ln();
    //         foreach( $deductionData as $ded)
    //         {
    //             $pdf->Cell(45, 0, '', 0, 0, 'L');
    //             $pdf->Cell(80,5, $ded['description'],0,0,'L');
    //             $pdf->Cell(30,5, number_format($ded['deduction_amount'],2),0,0,'R');

    //             $dedTotal += $ded['deduction_amount'] ;
    //             $pdf->Ln();
    //         }
    //         $pdf->Cell(183, 0,'P '. number_format($dedTotal ,2), 0, 0, 'R');
    //         $pdf->Ln(2);
    //         $pdf->Cell(0,0,'------------------------------',0,0,'R');
    //         $pdf->Ln(5);
    //     }

    //     $net = $invTotal + $chargeTotal + $dedTotal;

    //     $pdf->SetFont('helvetica', 'B', 10);
    //     $pdf->Cell(42, 0, '', 0, 0, 'L');
    //     $pdf->Cell(40, 0, 'NET PAYABLE AMOUNT', 0, 0, 'R');
    //     $pdf->Cell(102, 0, 'P ' . number_format($net, 2), 0, 0, 'R');

    //     $pdf->Ln(10);
    //     $pdf->SetFont('helvetica', '', 7);
    //     $pdf->Cell(10, 0, 'Legend:', 0, 0, 'L');
    //     $pdf->Cell(30, 0, 'PSI - Pro-forma Sales Invoice', 0, 0, 'L');
    //     $pdf->Ln(1);
    //     $pdf->Cell(0,0,'','B','','',false);
    //     $pdf->Ln(10);
    //     $pdf->SetFont('helvetica', '', 9);
    //     $pdf->Cell(20, 0, 'Prepared by :', 0, 0, 'L');
    //     $pdf->Cell(50, 0, $this->session->userdata('name'), 'B', 0, 'C');
    //     $pdf->Cell(38, 0, '', 0, 0, 'L');
    //     $pdf->Cell(20, 0, 'Audited by :', 0, 0, 'L');
    //     $pdf->Cell(50, 0, '', 'B', 0, 'L');
    //     $pdf->Ln();
    //     $pdf->Cell(20, 0, '', 0, 0, 'L');
    //     $pdf->Cell(50, 0, '(Accounts Payable Clerk)', 0, 0, 'C');

    //     $pdf->Ln(15);
    //     $pdf->Cell(28, 0, 'Pricing Incharge :', 0, 0, 'L');
    //     $pdf->Cell(30, 0, $pricing, 'B', 0, 'C');
    //     $pdf->Cell(28, 0, 'Inv. Clerk :', 0, 0, 'R');
    //     $pdf->Cell(30, 0, $invclerk, 'B', 0, 'C');
    //     $pdf->Cell(32, 0, 'Checked by :', 0, 0, 'R');
    //     $pdf->Cell(30, 0, '', 'B', 0, 'L');

    //     $pdf->Ln(15);
    //     $pdf->SetX(70);
    //     $pdf->Cell(20, 0, 'Approved by :', 0, 0, 'L');
    //     $pdf->Cell(50, 0, '', 'B', 0, 'L');
    //     $pdf->Ln();
    //     $pdf->SetX(90);
    //     $pdf->Cell(50, 0, '(Section/Department Head)', 0, 0, 'C');


    //     //sop vat
    //     $this->db->insert('sop_vat', array('sop_id' => $sopId, 'vat_amount' => $invTotal - ( $invTotal / $vat  )));
    //     //sop vat
    //     $fileName = "CWO-" . $headData['sop_no'] . time() . '.pdf';
    //     $pdf->Output(getcwd() . '/files/Reports/SOP/' . $fileName, 'F');

    //     return $fileName;
    // }
    private function printSOP($sopId)
    {
        $supplier      = $this->sop_model->getData('SELECT s.supplier_code, s.supplier_name FROM sop_head sop INNER JOIN suppliers s ON s.supplier_id = sop.supplier_id WHERE sop.sop_id = ' . $sopId);
        $customer      = $this->sop_model->getData('SELECT c.customer_code, c.customer_name FROM sop_head sop INNER JOIN customers c ON c.customer_code = sop.customer_code WHERE sop.sop_id = ' . $sopId);
        $headData      = $this->sop_model->getHeadData($sopId);
        $invoiceData   = $this->sop_model->getInvoiceData($sopId);
        $deductionData = $this->sop_model->getDeductionData($sopId);
        $chargesData   = $this->sop_model->getChargesData($sopId);
        $vat           = $this->sop_model->getVATData()['value'];
        $invclerk      = "";
        $pricing       = "";

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array(215, 279.4), true, 'UTF-8', false); //215.9 by 279.4 mm

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Mariel Taray');
        $pdf->SetTitle('CWO-SOP');
        $pdf->SetSubject('CWO-SOP');
        $pdf->SetKeywords('CWO, SOP');

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 004', PDF_HEADER_STRING);

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(17, 15, 15);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 5, 'ALTURAS GROUP OF COMPANIES', 0, 0, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 5, 'B. INTING ST., TAGBILARAN CITY', 0, 0, 'C');
        $pdf->Ln(10);
        $pdf->Cell(0, 5, 'SUMMARY OF PAYMENTS', 0, 0, 'C');
        $pdf->Ln(15);

        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(20, 5, 'SUPPLIER :', 0, 0, 'L');
        $pdf->Cell(110, 5, $supplier['supplier_code'] . ' - ' . $supplier['supplier_name'], 0, 0, 'L');
        $pdf->Cell(18, 5, 'NUMBER : ', 0, 0, 'L');
        $pdf->Cell(35, 5, $headData['sop_no'], 0, 0, 'R');
        $pdf->Ln(5);
        $pdf->Cell(20, 5, 'SECTION :', 0, 0, 'L');
        $pdf->Cell(110, 5, $customer['customer_code'] . ' - ' . $customer['customer_name'], 0, 0, 'L');
        $pdf->Cell(18, 5, 'DATE : ', 0, 0, 'L');
        $pdf->Cell(35, 5, date("m/d/Y", strtotime($headData['date_created'])), 0, 0, 'R');
        $pdf->Ln(5);
        $pdf->Cell(0, 0, '', 'B', '', '', false);
        $pdf->Ln(5);

        $pdf->SetFont('helvetica', 'B', 10);

        $pdf->Cell(40, 8, 'PO NO.', 0, 0, 'L');
        $pdf->Cell(30, 8, 'PO DATE', 0, 0, 'L');

        $pdf->Cell(40, 4, 'PROFORMA', 0, 0, 'L');
        $pdf->Ln();
        $pdf->setX(87);
        $pdf->Cell(40, 4, 'INVOICE NO.', 0, 0, 'L');

        $pdf->Ln(-4.7);
        $pdf->setX(127);
        $pdf->Cell(32, 4, 'PROFORMA', 0, 0, 'L');
        $pdf->Ln();
        $pdf->setX(127);
        $pdf->Cell(32, 4, 'INVOICE DATE', 0, 0, 'L');

        $pdf->Ln(-4);
        $pdf->setX(159);
        $pdf->Cell(40, 8, 'AMOUNT', 0, 0, 'R');
        $pdf->Ln(4);
        $pdf->Cell(0,0,'','B','','',false);
        $pdf->Ln(5);


        $pdf->SetFont('helvetica', '', 9);

        $invTotal    = 0;
        $chargeTotal = 0;
        $dedTotal    = 0;
        $net         = 0;
        foreach( $invoiceData as $inv)
        {
            $pdf->Cell(40, 0, $inv['po_no'], 0, 0, 'L');
            $pdf->Cell(30, 0, date("m-d-Y", strtotime($inv['po_date'])), 0, 0, 'L');
            $pdf->Cell(40, 0, $inv['so_no'], 0, 0, 'L');
            $pdf->Cell(32, 0, $inv['order_date'] != "" ? date("m-d-Y", strtotime($inv['order_date'])) : "", 0, 0, 'L');
            $pdf->Cell(40, 0, number_format($inv['invoice_amount'] ,2), 0, 0, 'R');

            $invTotal      += $inv['invoice_amount'];
            $invclerk      = $inv['invclerk'];
            $pricing       = $inv['pricing'];
            $pdf->Ln();
        }

        $pdf->Ln(2);
        $pdf->Cell(0,0,'------------------------------',0,0,'R');
        $pdf->Ln(3);
        $pdf->Cell(20, 0, '', 0, 0, 'L');
        $pdf->Cell(40, 0, 'Pro-forma Sales Invoice Total', 0, 0, 'L');
        $pdf->Cell(103, 0,'P '. number_format($invTotal ,2), 0, 0, 'R');
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'I', 7);
        $pdf->Cell(23, 0, '', 0, 0, 'L');
        $pdf->Cell(30, 0, 'PSI (Net of VAT)', 0, 0, 'L');
        $pdf->Cell(30, 0, 'P '. number_format($invTotal / $vat  ,2), 0, 0, 'R');
        $pdf->Ln(4);
        $pdf->Cell(23, 0, '', 0, 0, 'L');
        $pdf->Cell(30, 0, 'VAT', 0, 0, 'L');
        $pdf->Cell(30, 0, 'P '. number_format( $invTotal - ( $invTotal / $vat  ) ,2), 0, 0, 'R');
        $pdf->Ln(5);

       

        $pdf->SetFont('helvetica', '', 9);
        if(  !empty($chargesData) ){
            $pdf->Cell(20, 0, '', 0, 0, 'L');
            $pdf->Cell(40, 0, 'Add : Charges', 0, 0, 'L');
            $pdf->Ln();
            foreach( $chargesData as $charge)
            {
                $pdf->Cell(23, 0, '', 0, 0, 'L');
                $pdf->Cell(112,5, $charge['description'],0,0,'L');
                $pdf->Cell(30,5, number_format($charge['charge_amount'],2),0,0,'R');

                $chargeTotal += $charge['charge_amount'] ;
                $pdf->Ln();
            }

            $pdf->Cell(183, 0,'P '. number_format($chargeTotal ,2), 0, 0, 'R');
            $pdf->Ln(2);
            $pdf->Cell(0,0,'------------------------------',0,0,'R');
            $pdf->Ln(2);
        }

        if( !empty($deductionData) ){
            $pdf->Cell(20, 0, '', 0, 0, 'L');
            $pdf->Cell(40, 0, 'Less : Deductions', 0, 0, 'L');
            $pdf->Ln();
            foreach( $deductionData as $ded)
            {
                $pdf->Cell(23, 0, '', 0, 0, 'L');
                $pdf->Cell(112,5, $ded['description'],0,0,'L');
                $pdf->Cell(30,5, number_format($ded['deduction_amount'],2),0,0,'R');

                $dedTotal += $ded['deduction_amount'] ;
                $pdf->Ln();
            }
            $pdf->Cell(183, 0,'P '. number_format($dedTotal ,2), 0, 0, 'R');
            $pdf->Ln(2);
            $pdf->Cell(0,0,'------------------------------',0,0,'R');
            $pdf->Ln(5);
        }

        $net = $invTotal + $chargeTotal + $dedTotal;

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(23, 0, '', 0, 0, 'L');
        $pdf->Cell(51, 0, 'NET PAYABLE AMOUNT', 0, 0, 'R');
        $pdf->Cell(108, 0, 'P ' . number_format($net, 2), 0, 0, 'R');

        $pdf->Ln(10);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(10, 0, 'Legend:', 0, 0, 'L');
        $pdf->Cell(30, 0, 'PSI - Pro-forma Sales Invoice', 0, 0, 'L');
        $pdf->Ln(1);
        $pdf->Cell(0,0,'','B','','',false);
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(20, 0, 'Prepared by :', 0, 0, 'L');
        $pdf->Cell(50, 0, $this->session->userdata('name'), 'B', 0, 'C');
        $pdf->Cell(38, 0, '', 0, 0, 'L');
        $pdf->Cell(20, 0, 'Audited by :', 0, 0, 'L');
        $pdf->Cell(50, 0, '', 'B', 0, 'L');
        $pdf->Ln();
        $pdf->Cell(20, 0, '', 0, 0, 'L');
        $pdf->Cell(50, 0, '(Accounts Payable Clerk)', 0, 0, 'C');

        $pdf->Ln(15);
        $pdf->Cell(28, 0, 'Pricing Incharge :', 0, 0, 'L');
        $pdf->Cell(30, 0, $pricing, 'B', 0, 'C');
        $pdf->Cell(28, 0, 'Inv. Clerk :', 0, 0, 'R');
        $pdf->Cell(30, 0, $invclerk, 'B', 0, 'C');
        $pdf->Cell(32, 0, 'Checked by :', 0, 0, 'R');
        $pdf->Cell(30, 0, '', 'B', 0, 'L');

        $pdf->Ln(15);
        $pdf->SetX(70);
        $pdf->Cell(20, 0, 'Approved by :', 0, 0, 'L');
        $pdf->Cell(50, 0, '', 'B', 0, 'L');
        $pdf->Ln();
        $pdf->SetX(90);
        $pdf->Cell(50, 0, '(Section/Department Head)', 0, 0, 'C');


        //sop vat
        $this->db->insert('sop_vat', array('sop_id' => $sopId, 'vat_amount' => $invTotal - ( $invTotal / $vat  )));
        //sop vat
        $fileName = "CWO-" . $headData['sop_no'] . time() . '.pdf';
        $pdf->Output(getcwd() . '/files/Reports/SOP/' . $fileName, 'F');

        return $fileName;
    }
 
    public function getSuppliersSop()
    {
        $result = $this->sop_model->getSuppliers();
        JSONResponse($result);
    }

    public function getCustomersSop()
    {
        $result = $this->sop_model->getCustomers();
        JSONResponse($result);
    }

    public function loadVendorsDeal()
    {
        $fetch_data   = json_decode($this->input->raw_input_stream, TRUE);
        $getDeals     = $this->sop_model->getDeals($fetch_data['supId']);
        JSONResponse($getDeals);
    }

    
    public function loadSONos()
    {
        $fetch_data               = json_decode($this->input->raw_input_stream, TRUE);
        $getSOs                   = $this->sop_model->loadSONos($fetch_data['supId']);
        $getNoOfDiscount          = $this->sop_model->getData2('vendors_deal_header', 'vendor_deal_head_id', $fetch_data['dealId'])['no_of_discount'];
        $sopgross                 = $this->sop_model->getData2('vendors_deal_header', 'vendor_deal_head_id', $fetch_data['dealId'])['sop_gross'];
        $getHasDeal               = $this->sop_model->getSupData('has_deal', $fetch_data['supId'], 'supplier_id');
        $vat                      = $this->sop_model->getVATData()['value'];
        $getProformaItemAmount    = $this->sop_model->getProformaItemAmount($fetch_data['supId']);
        $getVendorsDeal           = $this->sop_model->getVendorsDealLine($fetch_data['dealId']);
        $getDiscType              = $this->sop_model->getSupData('disc_type', $fetch_data['supId'], 'supplier_id');
        $getDiscounting           = $this->sop_model->getSupData('discounting', $fetch_data['supId'], 'supplier_id');
        $getAmountUsed            = $this->sop_model->getSupData('amounting', $fetch_data['supId'], 'supplier_id');

        $final           = array();
        $proformaItems   = array();
        $itemCodeToFind  = "";        
        $disc1           = 0.00;
        $disc2           = 0.00;
        $disc3           = 0.00;
        $disc4           = 0.00;
        $disc5           = 0.00;
        $disc6           = 0.00;
        $disc_amt1       = 0.00;
        $evaluatedAmount = 0;
        $profAmount      = 0;
        $formula         = [];
        $formula_disc    = "";      
        
        foreach($getSOs as $so)
        {
            $proformaAmount  = 0;
            if($getAmountUsed->amounting != 'GROSSofVAT&Disc'){
                if($getDiscounting->discounting == "Per Item") {
                    foreach($getProformaItemAmount as $prof)//foreach($getProformaItemAmount as &$prof)
                    {         
                        if($so['proforma_header_id'] == $prof['proforma_header_id'])
                        {                          
                            if($getHasDeal->has_deal == "1"){          
                                foreach($getVendorsDeal as $deal)
                                {                    
                                    if($deal['type'] == "Item Department"){
                                        $itemCodeToFind = $prof['item_department_code'] ;
                                    } else if($deal['type'] == "Item") {
                                        $itemCodeToFind = $prof['itemcode_loc'] ;
                                    } else if($deal['type'] == "Item Group"){
                                        $itemCodeToFind = $prof['item_group_code'] ;
                                    }     
                                        if($deal['number'] == $itemCodeToFind){
                                            $keys              = array_keys($deal);
                                            $trimmed           = trim(ltrim($sopgross, '/'));
                                            $explode_sopgross  = explode("/", $trimmed);
                                            $formula           = [];

                                            if($getDiscType->disc_type == 'PERCENTAGE'){
                                                $disc1   = $deal['disc_1'] == 0.00 ? 0 : 1.00 - $deal['disc_1'] * 0.01 ;
                                                $disc2   = $deal['disc_2'] == 0.00 ? 0 : 1.00 - $deal['disc_2'] * 0.01 ;
                                                $disc3   = $deal['disc_3'] == 0.00 ? 0 : 1.00 - $deal['disc_3'] * 0.01 ;
                                                $disc4   = $deal['disc_4'] == 0.00 ? 0 : 1.00 - $deal['disc_4'] * 0.01 ;
                                                $disc5   = $deal['disc_5'] == 0.00 ? 0 : 1.00 - $deal['disc_5'] * 0.01 ;
                                                $disc6   = $deal['disc_6'] == 0.00 ? 0 : 1.00 - $deal['disc_6'] * 0.01 ;     
                                               
                                                for ($i=0; $i < count($explode_sopgross) ; $i++) {
                                                    if($keys[7] == trim($explode_sopgross[$i])){
                                                        $formula[] = '/' .$disc1 ;
                                                    } else if($keys[8] == trim($explode_sopgross[$i])){
                                                        $formula[] = '/' .$disc2 ;
                                                    } else if($keys[9] == trim($explode_sopgross[$i])){
                                                        $formula[] = '/' .$disc3 ;
                                                    } else if($keys[10] == trim($explode_sopgross[$i])){
                                                        $formula[] = '/' .$disc4 ;
                                                    } else if($keys[11] == trim($explode_sopgross[$i])){
                                                        $formula[] = '/' .$disc5 ;
                                                    } else if($keys[12] == trim($explode_sopgross[$i])){
                                                        $formula[] = '/' .$disc6 ;
                                                    }
                                                    $formula_disc = implode(' ', $formula);
                                                }
                                                $evaluatedAmount = backToGrossSop($fetch_data['supId'],$prof['amount'], $formula_disc,$vat);

                                            } else if($getDiscType->disc_type == 'AMOUNT'){   
                                                $disc1   = $deal['disc_1'] == 0.00 ? 0 : $deal['disc_1'] ;
                                                $disc2   = $deal['disc_2'] == 0.00 ? 0 : $deal['disc_2'] ;
                                                $disc3   = $deal['disc_3'] == 0.00 ? 0 : $deal['disc_3'] ;
                                                $disc4   = $deal['disc_4'] == 0.00 ? 0 : $deal['disc_4'] ;
                                                $disc5   = $deal['disc_5'] == 0.00 ? 0 : $deal['disc_5'] ;
                                                $disc6   = $deal['disc_6'] == 0.00 ? 0 : $deal['disc_6'] ;  
                                                for ($i=0; $i < count($explode_sopgross) ; $i++) {
                                                    if($keys[7] == trim($explode_sopgross[$i])){
                                                        $formula[] = '/' .$disc1 ;
                                                    } else if($keys[8] == trim($explode_sopgross[$i])){
                                                        $formula[] = '/' .$disc2 ;
                                                    } else if($keys[9] == trim($explode_sopgross[$i])){
                                                        $formula[] = '/' .$disc3 ;
                                                    } else if($keys[10] == trim($explode_sopgross[$i])){
                                                        $formula[] = '/' .$disc4 ;
                                                    } else if($keys[11] == trim($explode_sopgross[$i])){
                                                        $formula[] = '/' .$disc5 ;
                                                    } else if($keys[12] == trim($explode_sopgross[$i])){
                                                        $formula[] = '/' .$disc6 ;
                                                    }
                                                    $formula_disc = implode(' ', $formula);
                                                }
                                                $evaluatedAmount = backToGrossSop($fetch_data['supId'],$prof['amount'], $formula_disc,$vat);
                                            } else if($getDiscType->disc_type == 'NONE'){
                                                $evaluatedAmount = backToGrossSop($fetch_data['supId'],$prof['amount'], $formula_disc,$vat);
                                            }
                                        }
                                                        
                                } 
    
                            } else if($getHasDeal->has_deal == "0"){
                                $evaluatedAmount = backToGrossSop($fetch_data['supId'],$prof['amount'], $formula_disc,$vat);
                            }

                        }
                        
                        if($so['proforma_header_id'] == $prof['proforma_header_id'])
                        {    
                            $proformaAmount += $evaluatedAmount; 

                        }              
                    }
                
                } else if($getDiscounting->discounting == "All Items"){
                    
                    if($getHasDeal->has_deal == "1"){   
                        foreach($getVendorsDeal as $del)
                        {  
                            $keys              = array_keys($del);
                            $trimmed           = trim(ltrim($sopgross, '/'));
                            $explode_sopgross  = explode("/", $trimmed);
                            $formula           = [];
                   
                            if($getDiscType->disc_type == 'PERCENTAGE'){
                                $disc1   = $del['disc_1'] == 0.00 ? 0 : 1.00 - $del['disc_1'] * 0.01 ;
                                $disc2   = $del['disc_2'] == 0.00 ? 0 : 1.00 - $del['disc_2'] * 0.01 ;
                                $disc3   = $del['disc_3'] == 0.00 ? 0 : 1.00 - $del['disc_3'] * 0.01 ;
                                $disc4   = $del['disc_4'] == 0.00 ? 0 : 1.00 - $del['disc_4'] * 0.01 ;
                                $disc5   = $del['disc_5'] == 0.00 ? 0 : 1.00 - $del['disc_5'] * 0.01 ;
                                $disc6   = $del['disc_6'] == 0.00 ? 0 : 1.00 - $del['disc_6'] * 0.01 ;

                                for ($i=0; $i < count($explode_sopgross) ; $i++) {
                                    if($keys[7] == trim($explode_sopgross[$i])){
                                        $formula[] = '/' .$disc1 ;
                                    } else if($keys[8] == trim($explode_sopgross[$i])){
                                        $formula[] = '/' .$disc2 ;
                                    } else if($keys[9] == trim($explode_sopgross[$i])){
                                        $formula[] = '/' .$disc3 ;
                                    } else if($keys[10] == trim($explode_sopgross[$i])){
                                        $formula[] = '/' .$disc4 ;
                                    } else if($keys[11] == trim($explode_sopgross[$i])){
                                        $formula[] = '/' .$disc5 ;
                                    } else if($keys[12] == trim($explode_sopgross[$i])){
                                        $formula[] = '/' .$disc6 ;
                                    }
                                    $formula_disc = implode(' ', $formula);                               
                                }
                               
                            } else if($getDiscType->disc_type == 'AMOUNT'){   
                                $disc1   = $del['disc_1'] == 0.00 ? 0 : $del['disc_1'] ;
                                $disc2   = $del['disc_2'] == 0.00 ? 0 : $del['disc_2'] ;
                                $disc3   = $del['disc_3'] == 0.00 ? 0 : $del['disc_3'] ;
                                $disc4   = $del['disc_4'] == 0.00 ? 0 : $del['disc_4'] ;
                                $disc5   = $del['disc_5'] == 0.00 ? 0 : $del['disc_5'] ;
                                $disc6   = $del['disc_6'] == 0.00 ? 0 : $del['disc_6'] ; 
                                for ($i=0; $i < count($explode_sopgross) ; $i++) {
                                    if($keys[7] == trim($explode_sopgross[$i])){
                                        $formula[] = '/' .$disc1 ;
                                    } else if($keys[8] == trim($explode_sopgross[$i])){
                                        $formula[] = '/' .$disc2 ;
                                    } else if($keys[9] == trim($explode_sopgross[$i])){
                                        $formula[] = '/' .$disc3 ;
                                    } else if($keys[10] == trim($explode_sopgross[$i])){
                                        $formula[] = '/' .$disc4 ;
                                    } else if($keys[11] == trim($explode_sopgross[$i])){
                                        $formula[] = '/' .$disc5 ;
                                    } else if($keys[12] == trim($explode_sopgross[$i])){
                                        $formula[] = '/' .$disc6 ;
                                    }
                                    $formula_disc = implode(' ', $formula);                               
                                }
                            }                           
                            break ;                            
                        }
                        $proformaAmount = backToGrossSop($fetch_data['supId'],$so['amount'], $formula_disc,$vat);
                    }
                } else if( $getDiscounting->discounting == "None"){
                    $proformaAmount = backToGrossSop($fetch_data['supId'],$so['amount'], $formula_disc,$vat);
                }
            } else {
                $proformaAmount = backToGrossSop($fetch_data['supId'],$so['amount'], $formula_disc,$vat);
            }
            $final[] = ['proforma_header_id' =>$so['proforma_header_id'], 'so_no'  => $so['so_no'], 'order_date' =>$so['delivery_date'],
                        'po_no' =>$so['po_no'],'poDate' => $so['poDate'], 'amount' => $proformaAmount];   

        }

        $returnArr = [ 'SONOs' => $final];
        JSONResponse($returnArr);
    }

    public function loadDeductionType()
    {
        $getDeductionType = $this->sop_model->loadDeductionType();
        JSONResponse($getDeductionType);
    }

    public function getDeductionOrder()
    {
        $fetch_data   = json_decode($this->input->raw_input_stream, TRUE);
        $getOrder     = $this->sop_model->getDeductionOrder($fetch_data['typeId']);
        JSONResponse($getOrder->order);
    }

    public function loadDeduction()
    {
        $fetch_data   = json_decode($this->input->raw_input_stream, TRUE);
        $getNames     = $this->sop_model->getDeductionNames($fetch_data['typeId'], $fetch_data['supId']);
        JSONResponse($getNames);
    }

    public function calcAmountToBeDeductedForRegDisc()
    {
        $fetch_data       = json_decode($this->input->raw_input_stream, TRUE);
        $invoiceData      = $fetch_data['invoice'];
        $getDeductionData = $this->sop_model->getData('SELECT * FROM deduction WHERE deduction_id= '. $fetch_data['dedId']);
        $sopgross         = $this->sop_model->getData2('vendors_deal_header', 'vendor_deal_head_id', $fetch_data['dealId'])['sop_gross'];
        $vat              = $this->sop_model->getVATData()['value'];
        $getDeals         = $this->sop_model->getVendorsDealLine($fetch_data['dealId']);
        $getDiscType      = $this->sop_model->getSupData('disc_type', $fetch_data['supId'], 'supplier_id');
        $line             = array();
        $profAmount       = 0;
        $disc1            = 0.00;
        $disc2            = 0.00;
        $disc3            = 0.00;
        $disc4            = 0.00;
        $disc5            = 0.00;
        $disc6            = 0.00;
        $disc_amt1        = 0.00;
        $formula          = [];
        $formula_disc     = "";


       
        foreach($invoiceData as $inv)
        {
            if(!empty($inv))
            {
                $line[] = $this->sop_model->getProformaLine($inv['profId']);
            }            
        }

        // $flatLine = array_merge([],...$line); #not compatible in 5.4
        $flatLine = call_user_func_array('array_merge', $line);
   
        foreach($flatLine as $l)
        {
            $itemCodeToFind = "";
            foreach($getDeals as $deals)
            {
                
                if($deals['type'] == "Item Department"){
                    $itemCodeToFind = $l['item_department_code'];
                } else if($deals['type'] == "Item"){
                    $itemCodeToFind = $l['itemcode_loc'];
                } else if($deals['type'] == "Item Group"){
                    $itemCodeToFind = $l['item_group_code'];
                }
                if($deals['number'] == $itemCodeToFind){   
                    
                    $keys              = array_keys($deals);
                    $trimmed           = trim(ltrim($sopgross, '/'));
                    $explode_sopgross  = explode("/", $trimmed);
                    $formula           = [];
                   
                    if($getDiscType->disc_type == "PERCENTAGE"){
                        $disc1   = $deals['disc_1'] == 0.00 ? 0 : 1.00 - $deals['disc_1'] * 0.01 ;
                        $disc2   = $deals['disc_2'] == 0.00 ? 0 : 1.00 - $deals['disc_2'] * 0.01 ;
                        $disc3   = $deals['disc_3'] == 0.00 ? 0 : 1.00 - $deals['disc_3'] * 0.01 ;
                        $disc4   = $deals['disc_4'] == 0.00 ? 0 : 1.00 - $deals['disc_4'] * 0.01 ;
                        $disc5   = $deals['disc_5'] == 0.00 ? 0 : 1.00 - $deals['disc_5'] * 0.01 ;
                        $disc6   = $deals['disc_6'] == 0.00 ? 0 : 1.00 - $deals['disc_6'] * 0.01 ;  
                        for ($i=0; $i < count($explode_sopgross) ; $i++) {
                            if($keys[7] == $explode_sopgross[$i]){
                                $formula[] = '/' .$disc1 ;
                            } else if($keys[8] == $explode_sopgross[$i]){
                                $formula[] = '/' .$disc2 ;
                            } else if($keys[9] == $explode_sopgross[$i]){
                                $formula[] = '/' .$disc3 ;
                            } else if($keys[10] == $explode_sopgross[$i]){
                                $formula[] = '/' .$disc4 ;
                            } else if($keys[11] == $explode_sopgross[$i]){
                                $formula[] = '/' .$disc5 ;
                            } else if($keys[12] == $explode_sopgross[$i]){
                                $formula[] = '/' .$disc6 ;
                            }
                            $formula_disc = implode(' ', $formula);
                        }
                        
                        if($deals['number'] == $itemCodeToFind && $deals['disc_1'] == $getDeductionData['value_in_vd'] ){
                            $profAmount += backToGrossSop($fetch_data['supId'],$l['amount'], $formula_disc,$vat);
                        }
                    } else if($getDiscType->disc_type == "AMOUNT"){
                        $disc1   = $deals['disc_1'] == 0.00 ? 0 : $deals['disc_1'] ;
                        $disc2   = $deals['disc_2'] == 0.00 ? 0 : $deals['disc_2'] ;
                        $disc3   = $deals['disc_3'] == 0.00 ? 0 : $deals['disc_3'] ;
                        $disc4   = $deals['disc_4'] == 0.00 ? 0 : $deals['disc_4'] ;
                        $disc5   = $deals['disc_5'] == 0.00 ? 0 : $deals['disc_5'] ;
                        $disc6   = $deals['disc_6'] == 0.00 ? 0 : $deals['disc_6'] ; 

                        for ($i=0; $i < count($explode_sopgross) ; $i++) {
                            if($keys[7] == $explode_sopgross[$i]){
                                $formula[] = '/' .$disc1 ;
                            } else if($keys[8] == $explode_sopgross[$i]){
                                $formula[] = '/' .$disc2 ;
                            } else if($keys[9] == $explode_sopgross[$i]){
                                $formula[] = '/' .$disc3 ;
                            } else if($keys[10] == $explode_sopgross[$i]){
                                $formula[] = '/' .$disc4 ;
                            } else if($keys[11] == $explode_sopgross[$i]){
                                $formula[] = '/' .$disc5 ;
                            } else if($keys[12] == $explode_sopgross[$i]){
                                $formula[] = '/' .$disc6 ;
                            }
                            $formula_disc = implode(' ', $formula);
                        }

                        if($deals['number'] == $itemCodeToFind){
                            $profAmount += backToGrossSop($fetch_data['supId'],$l['amount'], $formula_disc,$vat);
                        }
                    }
                }
                
            }
        }

        return JSONResponse($profAmount);        
        
    }

    public function calculateDeduction()
    {
        $fetch_data      = json_decode($this->input->raw_input_stream, TRUE);
        $invoiceData     = $fetch_data['invoice'];
        $getDiscType     = $this->sop_model->getSupData('disc_type', $fetch_data['supId'], 'supplier_id');
        $lineQty         = 0;
        $eval            = new EvalMath();
        $getFormula      = $this->sop_model->getDeductionFormula($fetch_data['discountId']);

        // if($getDiscType->disc_type == "PERCENTAGE"){
           
        //     $toEval          = $fetch_data['amount'] . ' ' . $getFormula->formula;
           
        // } else if($getDiscType->disc_type == "AMOUNT"){

            
        //     $toEval = $fetch_data['amount'] . ' ' . $getFormula->formula;      
        // }        
        $toEval = $fetch_data['amount'] . ' ' . $getFormula->formula; 
        
        
        $deductionAmount = round($eval->evaluate($toEval), 2);

        JSONResponse($deductionAmount);
    }

    public function loadChargesType()
    {
        $getChargesType = $this->sop_model->loadChargesType();
        JSONResponse($getChargesType);
    }

    public function submitSOP()
    {
        $fetch_data   = $this->input->post(NULL, TRUE);
        $msg          = array();
        $hasInvoice   = false;
        $hasDeduction = false;
        $hasCharges   = false;
        $hasDocNo     = false;
        $saveInvoice  = 0;
        $saveDeduction= 0;
        $saveCharges  = 0;
        $sopNo        = "";
        $status       = "";

        $this->db->trans_start();

        if($this->session->userdata('province') == "1"){ //bohol
            $bohol = array('1','3', '10', '12', '4', '8', '11', '14','15' ); ///udc/cdc/warehouse,  cebu stores/warehouse, if taga bohol ang ni SOP bohol series ang gamiton
            $store = array('2','5','6','7','9'); // bohol stores
            if( in_array($fetch_data['cusId'], $bohol) ){
                $sopNo    = $this->sop_model->getDocNo('CWO-BOHSOP',true);
                $hasDocNo = TRUE;
            } else if( in_array($fetch_data['cusId'], $store )){
                $sopNo    = $this->sop_model->getDocNo('CWO-STRSOP',true);
                $hasDocNo = TRUE;
            } 
        } else if($this->session->userdata('province') == "2"){ //cebu
            $sopNo        = $this->sop_model->getDocNo('CWO-CEBSOP',true);
            $hasDocNo     = TRUE;
        }     

        if ($hasDocNo){
             $headData     = [ 'sop_no'              => $sopNo,
                               'supplier_id'         => $fetch_data['supId'],
                               'customer_code'       => $fetch_data['cusId'],
                               'invoice_amount'      => $fetch_data['invoiceAmount'],
                               'charges_amount'      => $fetch_data['chargesAmount'],
                               'deduction_amount'    => $fetch_data['dedAmount'],
                               'net_amount'          => $fetch_data['netAmount'],
                               'date_created'        => $fetch_data['sopdate'],//date("Y-m-d"),
                               'datetime_created'    => date("Y-m-d H:i:s"),
                               'status'              => 0,
                               'user_id'             => $this->session->userdata('user_id') ];
            $headId       = $this->sop_model->insertToTable('sop_head', $headData);

            if ($headId) {

                if (!empty($fetch_data['invoice'])) {
                    $hasInvoice = true;
                    foreach ($fetch_data['invoice'] as $inv) {
                        $invData = [ 'sop_id'  => $headId, 'proforma_header_id' => $inv['profId'], 'invoice_amount' => $inv['invoiceAmount'], 'vendor_deal_head_id' => $inv['dealId'] ];
                        $invoice = $this->sop_model->insertToTable('sop_invoice', $invData);
                        if ($invoice) {
                            $saveInvoice++;
                        }
                    }
                }

                if (!empty($fetch_data['deduction'])) {
                    $hasDeduction = true;
                    foreach ($fetch_data['deduction'] as $ded) {
                        $deductionData = [ 'sop_id'           => $headId, 
                                           'deduction_id'     => $ded['dedId'], 
                                           'sop_invoice_id'   => $ded['sopInvId'],
                                           'variance_id'      => $ded['varId'], 
                                           'description'      => $ded['dedName'], 
                                           'deduction_amount' => $ded['dedAmount'] ];
                        $deduction = $this->sop_model->insertToTable('sop_deduction', $deductionData);
                        if ($deduction) {
                            $saveDeduction++;
                        }
                        if($ded['varId'] != '0'){
                            $credit = 0;
                            $credit = $this->sop_model->getData2('variance_ledger', 'variance_id', $ded['varId'])['credit'];
                            $ledger = ['credit'      => $credit +  abs($ded['dedAmount']) ];
                            $this->sop_model->updateToTable('variance_ledger', $ledger, 'variance_id',  $ded['varId'] );
                        }
                    }
                }

                if (!empty($fetch_data['charges'])) {
                    $hasCharges = true;
                    foreach ($fetch_data['charges'] as $charge) {
                        $chargesData = [ 'sop_id' => $headId, 'charges_id' => $charge['chargeId'], 'description' => $charge['description'], 'charge_amount' => $charge['chargeAmount'] ];
                        $charges = $this->sop_model->insertToTable('sop_charges', $chargesData);
                        if ($charges) {
                            $saveCharges++;
                        }
                    }
                }
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $error = array('action' => 'Saving CWO SOP', 'error_msg' => $this->db->error()); //Log error message to `error_log` table
            $this->db->insert('error_log', $error);
            die("incomplete");
        } else {

            if ($hasDocNo){
                $file = $this->printSOP($headId);

                // for history
                $transaction = ['tr_no'         => $sopNo,
                                'tr_date'       => date("F d, Y - h:i:s A"),
                                'supplier_id'   => $fetch_data['supId'],
                                'customer_code' => $fetch_data['cusId'],
                                'filename'      => $file,
                                'user_id'       => $this->session->userdata('user_id') ];
                $history = $this->sop_model->insertToTable('sop_transaction', $transaction);
                // for history

                if ($history) {
                    $status = 'including History';
                } else {
                    $status = 'excluding History';
                } 
                if ($hasInvoice && $hasDeduction && $hasCharges && $headId) {
                    if ($saveInvoice != 0 && $saveDeduction != 0 && $saveCharges != 0) { //invoice & charges & deduction
                        $msg = ['info' => 'success', 'message' => 'SOP (Invoice, Deduction & Charges ' . $status . ') saved successfully!', 'file' => $file];
                    }
                } else if ($hasInvoice && !$hasDeduction && $hasCharges && $headId) { //invoice & charges
                    if ($saveInvoice != 0  && $saveCharges != 0) {
                        $msg = ['info' => 'success', 'message' => 'SOP (Invoice & Charges ' . $status . ') saved successfully!', 'file' => $file];
                    }
                } else if ($hasInvoice && $hasDeduction && !$hasCharges && $headId) { //invoice & deduction
                    if ($saveInvoice != 0  && $saveDeduction != 0) {
                        $msg = ['info' => 'success', 'message' => 'SOP (Invoice & Deduction ' . $status . ') saved successfully!', 'file' => $file];
                    }
                } else if( $hasInvoice && !$hasDeduction && !$hasCharges && $headId ){
                    if ($saveInvoice != 0 ) {
                        $msg = ['info' => 'success', 'message' => 'SOP (Invoice ' . $status . ') saved successfully!', 'file' => $file];
                    }
                }

            } else{
                $msg = ['info' => 'error', 'message' => 'Unable to generate document number!'];
            }

           
            JSONResponse($msg);
        }
    }

    public function loadCwoSop()
    {
        $fetch_data    = json_decode($this->input->raw_input_stream, TRUE);
        $getCwoSopData = $this->sop_model->loadCwoSop($fetch_data['supId'], $fetch_data['cusId'], $fetch_data['from'], $fetch_data['to']);
        JSONResponse($getCwoSopData);
    }

    public function loadSopDetails()
    {
        $fetch_data   = json_decode($this->input->raw_input_stream, TRUE);
        $getInvoice   = $this->sop_model->getInvoiceData($fetch_data['sopId']);
        $getDeduction = $this->sop_model->getDeductionData($fetch_data['sopId']);
        $getCharges   = $this->sop_model->getChargesData($fetch_data['sopId']);
        $data         = ['invoice' => $getInvoice, 'deduction' => $getDeduction, 'charges' => $getCharges];

        JSONResponse($data);
    }

    public function generateSopHistory()
    {
        $fetch_data   = $this->input->post(NULL, TRUE);
        $history      = array();
        $document1    = array();

        if (!empty($fetch_data)) {

            $history = $this->sop_model->getTransactionHistory($fetch_data['transactionType'], $fetch_data['supplierSelect'], $fetch_data['locationSelect']);
        }

        JSONResponse($history);
    }

    public function tagAsAudited()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $tag        = $this->sop_model->tagAsAudited($fetch_data['sopId']);
        if($tag){
            $msg = ['info' => 'Success', 'message' => 'Successfully tagged as AUDITED!'];
        } else {
            $msg = ['info' => 'Error', 'message' => 'Failed to tag as AUDITED!'];
        }
        JSONResponse($msg);
    }

    public function searchSOP()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $sop        = $this->sop_model->getUnMentionSOPInv($fetch_data['supId'],$fetch_data['str']);
        JSONResponse($sop);
    }

    public function searchVar()
    {
        $fetch_data = json_decode($this->input->raw_input_stream, TRUE);
        $crf        = $this->sop_model->searchCRFVar($fetch_data['str'], $fetch_data['supId']);
        JSONResponse($crf);
    }
    
    public function sopStatistics()
    {
        $statistics = $this->sop_model->getSopStatistics();
        JSONResponse($statistics);
    }
}
