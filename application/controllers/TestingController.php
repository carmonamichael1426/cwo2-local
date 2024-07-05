<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Testingcontroller extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->model('app_model');
        $this->load->library('upload');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->library('fpdf');
        $this->load->helper('file');
        $this->load->library('PHPExcel');
        $this->load->model('proformavspi_model');
        date_default_timezone_set('Asia/Manila');

        //Disable Cache
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        ('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    public function virtualData()
    {
        $array1 = ['data' => 'this', 'adata' => 'that'];
        $array2 = ['data1' => 'this', 'adata1' => 'that'];
        // $data = array(
        //     array('description' => 'CHARGES', 'debit' => '1000', 'credit' => '500'),
        //     array('description' => 'CHARGES', 'debit' => '1000', 'credit' => '500'),
        //     array('description' => 'CHARGES', 'debit' => '12321', 'credit' => '12221'),
        //     array('description' => 'CHARGES', 'debit' => '345345', 'credit' => '345345'),
        //     array('description' => 'CHARGES', 'debit' => '345345', 'credit' => '345'),
        // );

        // return $data;

        $data = array($array1, $array2);
        var_dump($data);
    }


    public function getData()
    {
        $result = $this->db->QUERY("SELECT
                                    s.supplier_name,
                                    l.l_acroname,
                                    sl.reference_no,
                                    c.crf_id,
                                    c.crf_no,
                                    c.crf_date, 
                                    IFNULL(c.crf_amt, 0) as total_crf_amount,
                                    pi.pi_no ,
                                    pi.vendor_invoice_no,
                                    pi.posting_date,
                                    IFNULL(pi.amt_including_vat, 0) as amt_including_vat
                                    FROM crf_line cl
									LEFT JOIN crf c ON cl.crf_id = c.crf_id
									LEFT JOIN purchase_invoice_header pi ON cl.pi_head_id = pi.pi_head_id
									LEFT JOIN suppliers s ON cl.supplier_id = s.supplier_id
									LEFT JOIN customers l ON cl.customer_code = l.customer_code
                                    LEFT JOIN (SELECT s.crf_id, s.reference_no 
												FROM subsidiary_ledger AS s 
                                                LEFT JOIN crf AS cr 
                                                ON cr.crf_id = s.crf_id) as sl
                                    ON sl.crf_id = c.crf_id
                                    WHERE pi.pi_no IS NOT NULL
									AND c.crf_date BETWEEN '2021-09-01' AND '2022-09-31'
                                    GROUP BY pi.pi_no
                                    ORDER BY sl.reference_no")->RESULT_ARRAY();
        return $result;
    }

    public function getDeductions($crf_id)
    {
        $result = $this->db->query("SELECT IFNULL(SUM(sd.amount), 0) AS amount_total 
                                    FROM sop_deduction sd
                                    LEFT JOIN (SELECT * FROM sop_head) sh 
                                    ON sd.sop_id = sh.sop_id
                                    WHERE sd.calculate = '1'
                                    AND sh.crf_id = '$crf_id'")->ROW();
        return $result;
    }

    public function testPDF()
    {
        $data = $this->getData();
        $date1 = '2021-09-01';
        $date2 = '2022-09-31';


        // var_dump($data);
        // exit();

        $dateFrom        = date('F d, Y', strtotime($date1));
        $dateTo          = date('F d, Y', strtotime($date2));
        $previousName    = '';
        $previousCRF     = '';
        $count           = 0;
        $height1         = 5;
        $height2         = 5;
        $suppliers       = [];
        $crfNo           = [];
        $unclosedBalance = 0;
        $crfTotal        = 0;
        $piTotal         = 0;
        $unservedTotal   = 0;
        $totalA          = 0;
        $totalB          = 0;
        $unservedCount   = array();

        $closedTotal     = 0;
        $closeTotalCount  = array();

        // $data = $this->virtualData();

        $pdf = new FPDF('L', 'mm', 'Legal');
        $pdf->AddPage();
        $pdf->setDisplayMode('fullpage');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(340, 0, 'ALTURAS GROUP OF COMPANIES', 0, 0, 'C');
        $pdf->Ln(5);
        $pdf->Cell(340, 0, 'INTERNAL AUDIT DEPARTMENT', 0, 0, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(340, 0, 'AUDIT ON CWO TRANSACTIONS', 0, 0, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(340, 0, 'AS OF ' . date('F d, Y'), 0, 0, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(40, 0, 'Prepared By: ' . $this->session->userdata('name'), 0, 0, 'L');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 0, 'PREVIOUS CWO TRANSACTIONS', 0, 0, 'L');

        $pdf->Ln(5);

        $pdf->SetTextColor(201, 201, 201);
        $pdf->SetFillColor(35, 35, 35);

        $pdf->setFont('Arial', 'B', 7);
        $pdf->cell(50, 6, "SUPPLIER'S NAME", 1, 0, 'C', TRUE);
        $pdf->cell(16, 6, "CHARGED", 1, 0, 'C', TRUE);
        $pdf->cell(20, 6, "CWO #", 1, 0, 'C', TRUE);
        $pdf->cell(20, 6, "CWO DATE", 1, 0, 'C', TRUE);
        $pdf->cell(20, 6, "CRF/CV#", 1, 0, 'C', TRUE);
        $pdf->cell(20, 6, "CRF DATE", 1, 0, 'C', TRUE);
        $pdf->cell(30, 6, "GROSS AMOUNT", 1, 0, 'C', TRUE);
        $pdf->cell(45, 6, "PI #", 1, 0, 'C', TRUE);
        $pdf->cell(20, 6, "PI DATE", 1, 0, 'C', TRUE);
        $pdf->cell(25, 6, "NET AMOUNT", 1, 0, 'C', TRUE);
        $pdf->cell(20, 6, "DISCOUNT", 1, 0, 'C', TRUE);
        $pdf->cell(30, 6, "UNCLOSED BALANCE", 1, 0, 'C', TRUE);
        $pdf->cell(18, 6, "REMARKS", 1, 0, 'C', TRUE);
        $pdf->cell(0, 5, "", 0, 0, 'C');

        foreach ($data as $x) {
            $suppliers[] = $x['supplier_name'];
            $crfNo[]     = $x['crf_no'];
        }

        $v = [];

        foreach (array_unique($crfNo) as $key => $value) {
            $vc = 0;
            $vv = 0;
            foreach ($data as $key2 => $value2) {
                if ($value === $value2['crf_no']) {
                    $vc += (float) $value2['amt_including_vat'];
                    $vv = (float) $value2['total_crf_amount'] - $vc;
                    $v[$value] = $vv;
                }
            }
        }

        $supplierCount = array_count_values($suppliers);
        $crfNoCount    = array_count_values($crfNo);

        $xx = 0;
        $y  = 0;
        $closed   = 0;
        $unclosed = 0;
        $deductions = 0;

        if (!empty($data)) {

            $pdf->SetTextColor(0, 0, 0);
            $pdf->ln();

            $counter  = 0;
            $counter2 = 0;
            foreach ($data as $index => $lines) {

                foreach ($supplierCount as $supplier => $count) {
                    if ($lines['supplier_name'] === $supplier) {
                        $height1  = $height1 * $count;
                        $counter2 = $count;
                    } else {
                        $height1;
                    }
                }

                foreach ($crfNoCount as $crf => $count) {
                    if ($lines['crf_no'] === $crf) {
                        $height2 = $height2 * $count;
                    } else {
                        $height2;
                    }
                }

                $xx += (float) $lines['amt_including_vat'];

                $c = 0;

                foreach ($v as $key => $value) {
                    if ($key === $lines['crf_no']) {
                        $c = $value;
                    }
                }

                if ($lines['supplier_name'] != $previousName) {
                    $deductions = $this->getDeductions($lines['crf_id']);
                    $c = $c + $deductions->amount_total;

                    $y = (float) $lines['total_crf_amount'];

                    if ($lines['crf_no'] != $previousCRF) {
                        $pdf->setFont('Arial', '', 8);
                        $pdf->cell(50, $height1, $lines['supplier_name'], 1, 0, 'C');
                        $pdf->cell(16, $height1, $lines['l_acroname'], 1, 0, 'C');
                        $pdf->cell(20, $height2, $lines['reference_no'], 1, 0, 'C');
                        $pdf->cell(20, $height1, '', 1, 0, 'C');
                        $pdf->cell(20, $height2, $lines['crf_no'], 1, 0, 'C');
                        $pdf->cell(20, $height2, $lines['crf_date'], 1, 0, 'C');
                        $pdf->cell(30, $height2, number_format($lines['total_crf_amount'], 2), 1, 0, 'R');
                        $pdf->cell(45, 5, $lines['pi_no'] . '/' . $lines['vendor_invoice_no'], 1, 0, 'C');
                        $pdf->cell(20, 5, $lines['posting_date'], 1, 0, 'C');
                        $pdf->cell(25, 5, number_format($lines['amt_including_vat'], 2), 1, 0, 'R');

                        $pdf->cell(20, $height2, number_format($deductions->amount_total, 2), 1, 0, 'R');

                        if ($c > 0) {
                            $pdf->SetTextColor(255, 0, 0);
                            $pdf->cell(30, $height2, number_format($c, 2), 1, 0, 'R');
                        } else {
                            $pdf->SetTextColor(0, 0, 0);
                            $pdf->cell(30, $height2, number_format($c, 2), 1, 0, 'R');
                        }


                        if ($c > 0) {
                            $pdf->cell(18, $height2, "Unclosed", 1, 0, 'C');
                            $unclosed++;
                        } else {
                            $pdf->cell(18, $height2, "Closed", 1, 0, 'C');
                            $closed++;
                        }

                        $pdf->cell(0, 5, "", 0, 0, 1);

                        $totalA          += $lines['total_crf_amount'];
                        $totalB          += $lines['amt_including_vat'];
                        $totalB = $totalB - $deductions->amount_total;
                        $crfTotal        += $lines['total_crf_amount'];
                        $piTotal         += $lines['amt_including_vat'];
                        $unclosedBalance += $c;

                        // if ($lines['remarks'] === 'Unserved') {
                        //     $unservedCount[]  = $lines['remarks'];
                        // } else {
                        //     $closeTotalCount[] = $lines['remarks'];
                        // }

                        if ($height1 <= 5) {
                            $counter = 0;
                        }

                        $pdf->SetTextColor(0, 0, 0);
                    }
                } else if ($lines['supplier_name'] == $previousName) {
                    if ($lines['crf_no'] == $previousCRF) {
                        $pdf->setFont('Arial', '', 9);
                        $pdf->cell(50, 5, '', 0, 0, 'C');
                        $pdf->cell(16, 5, '', 0, 0, 'C');
                        $pdf->cell(20, 5, '', 0, 0, 'C');
                        $pdf->cell(20, 5, '', 0, 0, 'C');
                        $pdf->cell(20, 5, '', 0, 0, 'C');
                        $pdf->cell(20, 5, '', 0, 0, 'C');
                        $pdf->cell(30, 5, '', 0, 0, 'R');
                        $pdf->cell(45, 5, $lines['pi_no'] . '/' . $lines['vendor_invoice_no'], 1, 0, 'C');
                        $pdf->cell(20, 5, $lines['posting_date'], 1, 0, 'C');
                        $pdf->cell(25, 5, number_format($lines['amt_including_vat'], 2), 1, 0, 'R');
                        $pdf->cell(20, 5, '', 0, 0, 'R');
                        $pdf->cell(20, 5, '', 0, 0, 'C');
                        $pdf->cell(30, 5, '', 0, 0, 'R');
                        $pdf->cell(18, 5, '', 0, 0, 'C');
                        $pdf->cell(0, 5, '', 0, 0, 'C');

                        $totalB  += $lines['amt_including_vat'];
                        $piTotal += $lines['amt_including_vat'];
                    } else {
                        $deductions = $this->getDeductions($lines['crf_id']);
                        $c = $c + $deductions->amount_total;
                        $pdf->setFont('Arial', '', 9);
                        $pdf->cell(50, 5, '', 0, 0, 'C');
                        $pdf->cell(16, 5, '', 0, 0, 'C');
                        $pdf->cell(20, $height2, $lines['reference_no'], 1, 0, 'C');
                        $pdf->cell(20, 5, '', 0, 0, 'C');
                        $pdf->cell(20, $height2, $lines['crf_no'], 1, 0, 'C');
                        $pdf->cell(20, $height2, $lines['crf_date'], 1, 0, 'C');
                        $pdf->cell(30, $height2, number_format($lines['total_crf_amount'], 2), 1, 0, 'R');
                        $pdf->cell(45, 5, $lines['pi_no'] . '/' . $lines['vendor_invoice_no'], 1, 0, 'C');
                        $pdf->cell(20, 5, $lines['posting_date'], 1, 0, 'C');
                        $pdf->cell(25, 5, number_format($lines['amt_including_vat'], 2), 1, 0, 'R');

                        $pdf->cell(20, $height2, number_format($deductions->amount_total, 2), 1, 0, 'R');

                        if ($c > 0) {
                            $pdf->SetTextColor(255, 0, 0);
                            $pdf->cell(30, $height2, number_format($c, 2), 1, 0, 'R');
                        } else {
                            $pdf->SetTextColor(0, 0, 0);
                            $pdf->cell(30, $height2, number_format($c, 2), 1, 0, 'R');
                        }


                        if ($c > 0) {
                            $pdf->cell(18, $height2, "Unclosed", 1, 0, 'C');
                            $unclosed++;
                        } else {
                            $pdf->cell(18, $height2, "Closed", 1, 0, 'C');
                            $closed++;
                        }

                        $pdf->cell(0, 5, "", 0, 0, 1);
                        $pdf->SetTextColor(0, 0, 0);

                        $totalA          += $lines['total_crf_amount'];
                        $totalB          += $lines['amt_including_vat'];
                        $totalB = $totalB - $deductions->amount_total;
                        $crfTotal        += $lines['total_crf_amount'];
                        $piTotal         += $lines['amt_including_vat'];
                        $unclosedBalance += $c;

                        // if ($lines['remarks'] === 'Unserved') {
                        //     $unservedCount[]  = $lines['remarks'];
                        // } else {
                        //     $closeTotalCount[] = $lines['remarks'];
                        // }
                    }
                }

                $counter++;
                if ($counter >= $counter2) {
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->ln();
                    if ($height1 > 5) {
                        $pdf->setFont('Arial', 'B', 10);
                        $pdf->cell(146, 5, "SUB TOTAL", 'L,B', 0, 'L');
                        $pdf->cell(30, 5, number_format($totalA, 2), 'B,B', 0, 'R');
                        $pdf->cell(90, 5, number_format($totalB, 2), 'B,B', 0, 'R');
                        $pdf->cell(50, 5, number_format($totalA - $totalB, 2), 'B,B', 0, 'R');
                        $pdf->cell(18, 5, "", 'R,B,B', 0, 'C');

                        $counter = 0;
                        $totalA  = 0;
                        $totalB  = 0;
                    } else if ($height1 == 10) {
                        $pdf->setFont('Arial', 'B', 10);
                        $pdf->cell(156, 5, "SUB TOTAL", 'L,B', 0, 'L');
                        $pdf->cell(30, 5, number_format($totalA, 2), 'B,B', 0, 'R');
                        $pdf->cell(95, 5, number_format($totalB, 2), 'B,B', 0, 'R');
                        $pdf->cell(38, 5, number_format($totalA - $totalB, 2), 0, 0, 'R');
                        $pdf->cell(18, 5, "", 'R,B,B', 0, 'C');

                        $counter = 0;
                        $totalA  = 0;
                        $totalB  = 0;
                    }
                }

                $pdf->ln();

                $unservedTotal = $unclosed;
                $closedTotal   = $closed;
                $previousName  = $lines['supplier_name'];
                $previousCRF   = $lines['crf_no'];
                $height1       = 5;
                $height2       = 5;
            }
        }

        $pdf->SetTextColor(0, 0, 0);
        $pdf->ln();
        $pdf->ln();

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(300, 0, 'Total Unserved:', 0, 0, 'R');
        $pdf->Cell(25, 0, $unservedTotal, 0, 0, 'R');
        $pdf->ln(5);
        $pdf->Cell(300, 0, 'Total Gross Amounts:', 0, 0, 'R');
        $pdf->Cell(25, 0, number_format($crfTotal, 2), 0, 0, 'R');
        $pdf->ln(5);
        $pdf->Cell(300, 0, 'Total Net Amounts:', 0, 0, 'R');
        $pdf->Cell(25, 0, number_format($piTotal, 2), 0, 0, 'R');
        $pdf->ln(5);
        $pdf->Cell(300, 0, 'Total Unclosed Balances:', 0, 0, 'R');

        if ($unclosedBalance > 0) {
            $pdf->SetTextColor(255, 0, 0);
        } else {
            $pdf->SetTextColor(0, 0, 0);
        }

        $pdf->Cell(25, 0, number_format($unclosedBalance, 2), 0, 0, 'R');

        $pdf->SetTextColor(0, 0, 0);
        $pdf->ln(5);
        $pdf->ln(5);
        $pdf->Cell(300, 0, 'Total Closed:', 0, 0, 'R');
        $pdf->Cell(25, 0, $closedTotal, 0, 0, 'R');

        $pdf->ln(30);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(25, 0, 'Audited By: ', 0, 0, 'R');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 0, '________________________', 0, 0, 'R');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(90, 0, 'Conformed By: ', 0, 0, 'R');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 0, '________________________', 0, 0, 'R');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(70, 0, 'Noted By: ', 0, 0, 'R');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 0, '________________________', 0, 0, 'R');

        $file_name =  'CWO_IAD_REPORT-' . time() . '.pdf';
        $pdf->Output();
    }

    public function cwoSlip()
    {
        $pdf = new FPDF('L', 'mm', 'Legal');
        $pdf->AddPage();
        $pdf->setDisplayMode('fullpage');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(340, 0, 'ALTURAS GROUP OF COMPANIES', 0, 0, 'C');
        $pdf->Ln(5);
        $pdf->Cell(340, 0, 'RETAIL 1', 0, 0, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(340, 0, 'CASH WITH ORDER (CWO) SLIP', 0, 0, 'C');
        $pdf->Ln(5);
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(40, 0, 'DATE: ' . date('m/d/Y'), 0, 0, 'L');
        $pdf->Cell(250, 0, '', 0, 0, 'L');
        $pdf->Cell(40, 0, 'CWO # : CWO-0000001', 0, 0, 'L');
        $pdf->Ln(5);
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(340, 0, 'MONDELEZ PHILIPPINES, INC.', 0, 0, 'C');

        $pdf->Ln(5);
        $pdf->Ln(5);

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(40, 0, 'Requesting Dept.: CDC', 0, 0, 'L');

        $pdf->Ln(5);

        $pdf->SetTextColor(201, 201, 201);
        $pdf->SetFillColor(35, 35, 35);

        $pdf->setFont('Arial', 'B', 12);
        $pdf->cell(60, 6, "PO NUMBER", 1, 0, 'C', TRUE);
        $pdf->cell(30, 6, "DATE", 1, 0, 'C', TRUE);
        $pdf->cell(60, 6, "INVOICE NUMBER", 1, 0, 'C', TRUE);
        $pdf->cell(30, 6, "DATE", 1, 0, 'C', TRUE);
        $pdf->cell(53, 6, "GROSS AMOUNT", 1, 0, 'C', TRUE);
        $pdf->cell(50, 6, "DISCOUNT", 1, 0, 'C', TRUE);
        $pdf->cell(53, 6, "NET AMOUNT", 1, 0, 'C', TRUE);
        // $pdf->cell(0, 5, "", 0, 0, 'C');

        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(6);
        $pdf->setFont('Arial', '', 12);
        $pdf->cell(60, 6, "0376928", 1, 0, 'C');
        $pdf->cell(30, 6, "09/08/21", 1, 0, 'C');
        $pdf->cell(60, 6, "6373292615", 1, 0, 'C');
        $pdf->cell(30, 6, "09/08/21", 1, 0, 'C');
        $pdf->cell(53, 6, "1,865,800.58", 1, 0, 'R');
        $pdf->cell(50, 6, "", 1, 0, 'C');
        $pdf->cell(53, 6, "", 1, 0, 'C');
        $pdf->cell(0, 5, "", 0, 0, 'C');

        $pdf->Ln(6);
        $pdf->setFont('Arial', '', 12);
        $pdf->cell(60, 6, "0376938", 1, 0, 'C');
        $pdf->cell(30, 6, "09/08/21", 1, 0, 'C');
        $pdf->cell(60, 6, "6373292614", 1, 0, 'C');
        $pdf->cell(30, 6, "09/08/21", 1, 0, 'C');
        $pdf->cell(53, 6, "97,027.45", 1, 0, 'R');
        $pdf->cell(50, 6, "", 1, 0, 'C');
        $pdf->cell(53, 6, "", 1, 0, 'C');
        $pdf->cell(0, 5, "", 0, 0, 'C');

        $pdf->Ln(6);
        $pdf->setFont('Arial', '', 12);
        $pdf->cell(60, 6, "", 1, 0, 'C');
        $pdf->cell(30, 6, "", 1, 0, 'C');
        $pdf->cell(60, 6, "6373292613", 1, 0, 'C');
        $pdf->cell(30, 6, "09/08/21", 1, 0, 'C');
        $pdf->cell(53, 6, "4,751.46", 1, 0, 'R');
        $pdf->cell(50, 6, "", 1, 0, 'C');
        $pdf->cell(53, 6, "", 1, 0, 'C');
        $pdf->cell(0, 5, "", 0, 0, 'C');

        $pdf->Ln(6);
        $pdf->setFont('Arial', '', 12);
        $pdf->cell(60, 6, "", 1, 0, 'C');
        $pdf->cell(30, 6, "", 1, 0, 'C');
        $pdf->cell(60, 6, "6373292611", 1, 0, 'C');
        $pdf->cell(30, 6, "09/08/21", 1, 0, 'C');
        $pdf->cell(53, 6, "6,834.96", 1, 0, 'R');
        $pdf->cell(50, 6, "", 1, 0, 'C');
        $pdf->cell(53, 6, "", 1, 0, 'C');
        $pdf->cell(0, 5, "", 0, 0, 'C');

        $pdf->Ln(6);
        $pdf->setFont('Arial', 'B', 12);
        $pdf->cell(60, 6, "", 1, 0, 'C');
        $pdf->cell(30, 6, "", 1, 0, 'C');
        $pdf->cell(60, 6, "", 1, 0, 'C');
        $pdf->cell(30, 6, "", 1, 0, 'C');
        $pdf->cell(53, 6, "1,974,414.45", 1, 0, 'R');
        $pdf->cell(50, 6, "58,837.50", 1, 0, 'R');
        $pdf->cell(53, 6, "P 1,915,576.95", 1, 0, 'R');
        $pdf->cell(0, 5, "", 0, 0, 'C');

        $pdf->Ln(6);
        $pdf->setFont('Arial', 'B', 12);
        $pdf->cell(60, 6, "", 1, 0, 'C');
        $pdf->cell(30, 6, "", 1, 0, 'C');
        $pdf->cell(60, 6, "", 1, 0, 'C');
        $pdf->cell(30, 6, "", 1, 0, 'C');
        $pdf->cell(53, 6, "", 1, 0, 'R');
        $pdf->cell(50, 6, "Less: WHT", 1, 0, 'R');
        $pdf->cell(53, 6, "P 17,103.36", 1, 0, 'R');
        $pdf->cell(0, 5, "", 0, 0, 'C');

        $pdf->Ln(6);
        $pdf->setFont('Arial', 'B', 12);
        $pdf->cell(60, 6, "", 1, 0, 'C');
        $pdf->cell(30, 6, "", 1, 0, 'C');
        $pdf->cell(60, 6, "", 1, 0, 'C');
        $pdf->cell(30, 6, "", 1, 0, 'C');
        $pdf->cell(53, 6, "", 1, 0, 'R');
        $pdf->cell(50, 6, "", 1, 0, 'R');
        $pdf->cell(53, 6, "P 1,898,473.59", 1, 0, 'R');
        $pdf->cell(0, 5, "", 0, 0, 'C');

        $pdf->Output();
    }

    public function authorize1()
    {
        $data = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $m    = array();

        if (isset($data)) {
            if ($data['username'] == 'admin123' && $data['password'] == 'Admin123') {
                $m = ['message' => 'Access Granted', 'info' => 'Auth'];
            } else {
                $m = ['message' => 'Access Denied.', 'info' => 'Incorrect'];
            }
        } else {
            $m = ['message' => 'Error, authenticating.', 'info' => 'Error'];
        }

        JSONResponse($m);
    }

    

    public function printSOP()
    {
        $this->load->model('sop_model');
        $supplier      = $this->sop_model->getData('SELECT s.supplier_code, s.supplier_name FROM sop_head sop INNER JOIN suppliers s ON s.supplier_id = sop.supplier_id WHERE sop.sop_id = 2890 ');
        $customer      = $this->sop_model->getData('SELECT c.customer_code, c.customer_name FROM sop_head sop INNER JOIN customers c ON c.customer_code = sop.customer_code WHERE sop.sop_id =  2890');
        $headData      = $this->sop_model->getHeadData(2890);
        $invoiceData   = $this->sop_model->getInvoiceData(2890);
        $deductionData = $this->sop_model->getDeductionData(2890);
        $chargesData   = $this->sop_model->getChargesData(2890);

        // create new PDF document
        $this->load->library('Pdf');
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
        $pdf->Cell(35, 5, date("m/d/Y", strtotime($headData['datetime_created'])), 0, 0, 'R');
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
        $pdf->Cell(0, 0, '', 'B', '', '', false);
        $pdf->Ln(5);

        $pdf->SetFont('helvetica', '', 9);

        $invTotal    = 0;
        $chargeTotal = 0;
        $dedTotal    = 0;
        $net         = 0;
        foreach ($invoiceData as $inv) {
            $pdf->Cell(40, 0, $inv['po_no'], 0, 0, 'L');
            $pdf->Cell(30, 0, date("m/d/Y", strtotime($inv['po_date'])), 0, 0, 'L');
            $pdf->Cell(40, 0, $inv['so_no'], 0, 0, 'L');
            $pdf->Cell(32, 0, date("m/d/Y", strtotime($inv['order_date'])), 0, 0, 'L');
            $pdf->Cell(40, 0, number_format($inv['invoice_amount'], 2), 0, 0, 'R');

            $invTotal += $inv['invoice_amount'];
            $pdf->Ln();
        }

        $pdf->Ln(2);
        $pdf->Cell(0, 0, '------------------------------', 0, 0, 'R');
        $pdf->Ln(3);
        $pdf->Cell(40, 0, '', 0, 0, 'L');
        $pdf->Cell(40, 0, 'PSI Total', 0, 0, 'L');
        $pdf->Cell(103, 0, 'P ' . number_format($invTotal, 2), 0, 0, 'R');
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'I', 7);
        $pdf->Cell(43, 0, '', 0, 0, 'L');
        $pdf->Cell(30, 0, 'PSI (Net of VAT)', 0, 0, 'L');
        $pdf->Cell(30, 0, 'P ' . number_format($invTotal / 1.12, 2), 0, 0, 'R');
        $pdf->Ln(4);
        $pdf->Cell(43, 0, '', 0, 0, 'L');
        $pdf->Cell(30, 0, 'VAT', 0, 0, 'L');
        $pdf->Cell(30, 0, 'P ' . number_format($invTotal - ($invTotal / 1.12), 2), 0, 0, 'R');
        $pdf->Ln(5);

        $pdf->SetFont('helvetica', '', 9);
        if (!empty($chargesData)) {
            $pdf->Cell(40, 0, '', 0, 0, 'L');
            $pdf->Cell(40, 0, 'Add : Charges', 0, 0, 'L');
            $pdf->Ln();
            foreach ($chargesData as $charge) {
                $pdf->Cell(45, 0, '', 0, 0, 'L');
                $pdf->Cell(80, 5, $charge['description'], 0, 0, 'L');
                $pdf->Cell(30, 5, number_format($charge['charge_amount'], 2), 0, 0, 'R');

                $chargeTotal += $charge['charge_amount'];
                $pdf->Ln();
            }

            $pdf->Cell(183, 0, 'P ' . number_format($chargeTotal, 2), 0, 0, 'R');
            $pdf->Ln(2);
            $pdf->Cell(0, 0, '------------------------------', 0, 0, 'R');
            $pdf->Ln(2);
        }

        if (!empty($deductionData)) {
            $pdf->Cell(40, 0, '', 0, 0, 'L');
            $pdf->Cell(40, 0, 'Less : Deductions', 0, 0, 'L');
            $pdf->Ln();
            foreach ($deductionData as $ded) {
                $pdf->Cell(45, 0, '', 0, 0, 'L');
                $pdf->Cell(80, 5, $ded['description'], 0, 0, 'L');
                $pdf->Cell(30, 5, number_format($ded['deduction_amount'], 2), 0, 0, 'R');

                $dedTotal += $ded['deduction_amount'];
                $pdf->Ln();
            }
            $pdf->Cell(183, 0, 'P ' . number_format($dedTotal, 2), 0, 0, 'R');
            $pdf->Ln(2);
            $pdf->Cell(0, 0, '------------------------------', 0, 0, 'R');
            $pdf->Ln(5);
        }

        $net = $invTotal + $chargeTotal + $dedTotal;

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(42, 0, '', 0, 0, 'L');
        $pdf->Cell(40, 0, 'NET PAYABLE AMOUNT', 0, 0, 'R');
        $pdf->Cell(102, 0, 'P ' . number_format($net, 2), 0, 0, 'R');

        $pdf->Ln(10);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(10, 0, 'Legend:', 0, 0, 'L');
        $pdf->Ln(3);
        $pdf->Cell(8, 0, '', 0, 0, 'L');
        $pdf->Cell(30, 0, 'PSI - Proforma Sales Invoice', 0, 0, 'L');
        $pdf->Ln(1);
        $pdf->Cell(0, 0, '', 'B', '', '', false);
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

        $pdf->Ln(10);
        $pdf->SetX(70);
        $pdf->Cell(20, 0, 'Approved by :', 0, 0, 'L');
        $pdf->Cell(50, 0, '', 'B', 0, 'L');
        $pdf->Ln();
        $pdf->SetX(90);
        $pdf->Cell(50, 0, '(Section/Department Head)', 0, 0, 'C');

        $pdf->Ln(10);
        $pdf->Cell(27, 0, 'Pricing Incharge :', 0, 0, 'L');
        $pdf->Cell(30, 0, '', 'B', 0, 'L');
        $pdf->Cell(25, 0, 'Inv. Clerk :', 0, 0, 'R');
        $pdf->Cell(30, 0, '', 'B', 0, 'L');
        $pdf->Cell(32, 0, 'Checked by :', 0, 0, 'R');
        $pdf->Cell(30, 0, '', 'B', 0, 'L');


        // Close and output PDF document
        $pdf->Output('CWO_SOP.pdf', 'I');
    }

    public function printProfVSCRF()
    {
        $sopDeduction      = $this->proformavspi_model->getSopDeduction(64);
        // var_dump($sopDeduction);
        // var_dump(count($sopDeduction));
        // die();

        $pdf = new FPDF('L', 'mm', array(594 , 841  )); /*  A1	420  × 594  mm	23.4 × 33.1 in   */
        $pdf->AddPage();
        $pdf->setDisplayMode('fullpage');


        $pdf->setX(30);
        $pdf->Cell(40, 0, 'PROFORMA SUPPLIER INVOICE', 0, 0, 'L');
        $pdf->setX(423);
        $pdf->Cell(0, 0, 'PURCHASE INVOICE', 0, 0, 'L');
        $pdf->Ln(5);

        $pdf->SetFont('Arial','',8);
        $pdf->SetTextColor(201, 201, 201);
        $pdf->SetFillColor(35, 35, 35);

        $pdf->setX(30);
        $pdf->cell(80, 8, "Proforma", 1, 0, 'C', TRUE);
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
        $pdf->cell(35, 8, "PI No", 1, 0, 'C', TRUE);
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
        $pdf->cell(18, 4, 'Variance', 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(782);
        $pdf->cell(18, 4, "(Overserved)", 'LBR', 0, 'C', TRUE);

        $pdf->Ln(-4);
        $pdf->setX(800);
        $pdf->cell(32, 4, "Variance", 'LTR', 0, 'C', TRUE);
        $pdf->Ln();
        $pdf->setX(800);
        $pdf->cell(32, 4, "(Discounted Amount)", 'LBR', 0, 'C', TRUE);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();
        
        $pdf->setX(30);
        $pdf->cell(80, 6, 'PROFORMA - SMG-CPO-0382565 ', 'LBR', 0, 'C');
        $pdf->cell(20, 6, 'TCR1080P', 'BR', 0, 'C', 0);
        $pdf->cell(82, 6, 'CLAY DOH 360 MOLDING CLAY 8', 'BR', 0, 'C', 0);
        $pdf->cell(15, 6, 'PCS', 'BR', 0, 'C', 0);
        $pdf->cell(12, 6, '96', 'BR', 0, 'C', 0);
        $pdf->cell(27, 6, '122.28', 'BR', 0, 'R', 0); //net price
        $pdf->cell(30, 6, '0', 'BR', 0, 'R', 0); //discounted price
        $pdf->cell(30, 6, '165.00', 'BR', 0, 'R', 0);  //gross price
        $pdf->cell(32, 6, '11,738.57', 'BR', 0, 'R', 0);  //net amount
        $pdf->cell(32, 6, '0', 'BR', 0, 'R', 0);  //discounted amount
        $pdf->cell(32, 6, '15,840.00', 'BR', 0, 'R', 0);  //gross amount

        $pdf->cell(2, 6, " ", 0, 0, 'L');

        $pdf->cell(35, 6, 'CDC-P4162911', 'LBR', 0, 'C');
        $pdf->cell(20, 6, '2021-12-02', 'BR', 0, 'C', 0);
        $pdf->cell(20, 6, '111424', 'BR', 0, 'C', 0);
        $pdf->cell(82, 6, 'BENCH FIX CLAY DOH 360 24x80G', 'BR', 0, 'C', 0);
        $pdf->cell(15, 6, 'PCS', 'BR', 0, 'C', 0);
        $pdf->cell(12, 6, '96', 'BR', 0, 'C', 0);
        $pdf->cell(18, 6, '165.00', 'BR', 0, 'R', 0);
        $pdf->cell(30, 6, '0', 'BR', 0, 'R', 0);
        $pdf->cell(30, 6, '0', 'BR', 0, 'R', 0);
        $pdf->cell(32, 6, '15,840.0', 'BR', 0, 'R', 0); //gross amount
        $pdf->cell(32, 6, '0', 'BR', 0, 'R', 0); //discounted amount
        $pdf->cell(32, 6, '0', 'BR', 0, 'R', 0); //net amount
        $pdf->cell(18, 6, '0' , 'BR', 0, 'C', 0); //variance qty
        $pdf->cell(32, 6, '0', 'BR', 0, 'R', 0); //variance gross amount

        $pdf->Ln();

        $pdf->setFont('Arial', 'B', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->setX(30);
        $pdf->cell(182, 5, "FULLY-SERVED TOTAL :", 'LB', 0, 'L');
        $pdf->cell(27, 5, '96', 'B', 0, 'R');       
        $pdf->cell(119, 5, "P 11,738.5" , 'B', 0, 'R');
        $pdf->cell(32, 5, "P 13,147.55", 'B', 0, 'R');
        $pdf->cell(32, 5, "P 15,840.00", 'BR', 0, 'R');

        $pdf->cell(2, 5, "", 0, 0, 'L');

        $pdf->cell(157, 5, "FULLY-RECEIVED TOTAL :", 'LB', 0, 'L');
        $pdf->cell(27, 5, '1', 'B', 0, 'R'); 
        $pdf->cell(110, 5, 'P 77,220.35', 'B', 0, 'R'); 
        $pdf->cell(32, 5, 'P 77,220.35' , 'B', 0, 'R');
        $pdf->cell(32, 5, 'P 77,220.35' , 'B', 0, 'R');
        $pdf->cell(18, 5, '0' , 'B', 0, 'C');
        $pdf->cell(32, 5, 'P 77,220.35' , 'BR', 0, 'R');      

        $pdf->Ln();
        $pdf->setX(30);
        $pdf->cell(25, 5, "Fully-Served/Received Item Count : 1" , '', 0, 'L');

        $pdf->Ln(5);

        /*   TOTAL OF PROFORMA && PURCHASE INVOICE     */
        $pdf->setX(30);
        $pdf->cell(150, 5, "PROFORMA SUPPLIER INVOICE (PSI) Total Item Count : " , 0, 0, 'L');
        $pdf->cell(242, 5, '', 0, 0, 'L');
        $pdf->cell(2, 5,  '', 0, 0, 'L');
        $pdf->cell(80, 5,  "PURCHASE INVOICE Total Item Count : 1" , 0, 0, 'L');
        $pdf->Ln(5);

        $pdf->setX(30);
        $pdf->cell(182, 5, "TOTAL PROFORMA SUPPLIER INVOICE (PSI) :", 0, 0, 'L');
        $pdf->cell(27, 5, '0', 0, 0, 'R');     
        $pdf->cell(119, 5, "P 62,052.04 ", 0, 0, 'R');
        $pdf->cell(32, 5, "P 13,147.55", 0, 0, 'R');
        $pdf->cell(32, 5, "P 15,840.00", 0, 0, 'R');

        $pdf->cell(2, 5, "", 0, 0, 'L');

        $pdf->cell(157, 5, "TOTAL PURCHASE INVOICE (PI) :", 0, 0, 'L');
        $pdf->cell(27, 5, '1', 0, 0, 'R'); 
        $pdf->cell(110, 5, 'P 77,220.35', 0, 0, 'R'); 
        $pdf->cell(32, 5, 'P 77,220.35' , 0, 0, 'R');
        $pdf->cell(32, 5, 'P 77,220.35' , 0, 0, 'R');
        $pdf->cell(18, 5, '0' , 0, 0, 'C');
        $pdf->cell(32, 5, 'P 77,220.35' , 0, 0, 'R');  
        $pdf->Ln(10);
        /*   TOTAL OF PROFORMA && PURCHASE INVOICE     */

        /** CM **/
        $pdf->setX(423);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 0, 'CREDIT MEMO', 0, 0, 'L');
        $pdf->Ln(3);
        
        $pdf->SetFont('Arial','',8);
        $pdf->SetTextColor(201, 201, 201);
        $pdf->SetFillColor(35, 35, 35);
        $pdf->setX(424);
        $pdf->cell(35, 8, "CM No", 1, 0, 'C', TRUE);
        $pdf->cell(20, 8, "Date", 1, 0, 'C', TRUE);
        $pdf->cell(30, 8, "Applied to PI", 1, 0, 'C', TRUE);
        $pdf->setFont('times', '', 7);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();
        $pdf->setX(424);
        $pdf->cell(35, 6, "CDC-P7004358", 'LBR', 0, 'C', 0);
        $pdf->cell(20, 6, "2021-11-06", 'BR', 0, 'C', 0);
        $pdf->cell(30, 6, "CDC-P4160511", 'BR', 0, 'C', 0);

        $pdf->Ln(10);

        $pdf->SetFont('Arial','',8);
        $pdf->SetTextColor(201, 201, 201);
        $pdf->SetFillColor(35, 35, 35);
        $pdf->setX(424);
        $pdf->cell(35, 8, "CM No", 1, 0, 'C', TRUE);
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
        $pdf->cell(50, 8, 'Applied PI', 'LTR', 0, 'C', TRUE);
        /** CM **/

        $pdf->Ln(10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->setX(30);
        $pdf->Cell(215, 0, 'TOTAL PROFORMA SUPPLIER INVOICE (PSI) (Discounted Amount) : ', 0, 0, 'L');
        $pdf->cell(30, 0, "", 0, 0, 'R');
        $pdf->cell(30, 0, "P 77,220.35" , 0, 0, 'R');
        $pdf->Ln(5); 
        $pdf->setX(30);
        $pdf->Cell(105, 0, 'TOTAL PURCHASE INVOICE (PI) (Discounted Amount) : ', 0, 0, 'L');
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->cell(170, 0, "P 77,220.35", 0, 0, 'R');
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Ln(5);
        $pdf->setX(30);
        $pdf->Cell(105, 0, 'Variance (Discounted Amount) : ', 0, 0, 'L');
        $pdf->cell(170, 0, "P 77,220.35", 0, 0, 'R');


        $pdf->Ln(10);
        $pdf->SetTextColor(201, 201, 201);
        $pdf->SetFillColor(35, 35, 35);
        $pdf->setFont('Arial', '', 9);
        $pdf->setX(30);
        $pdf->Cell(80, 6, 'Proforma', 1, 0, 'C', TRUE);
        $pdf->cell(25, 6, "Delivery Date", 1, 0, 'C', TRUE);
        $pdf->Cell(30, 6, 'SO No', 1, 0, 'C', TRUE);        
        $pdf->cell(20, 6, "Location", 1, 0, 'C', TRUE);
        $pdf->Cell(30, 6, 'PO No', 1, 0, 'C', TRUE);
        $pdf->Cell(45, 6, "Add'l & Deduction", 1, 0, 'C', TRUE);
        $pdf->Cell(30, 6, 'Amount', 1, 0, 'C', TRUE);
        $pdf->Ln(10);

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
            $i++;
        }

        $pdf->Ln();
        $pdf->setFont('Arial', 'B', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->setX(30);
        $pdf->cell(230, 5, "TOTAL SOP Deduction :", 'LBR', 0, 'L', 0);
        $pdf->cell(30, 5, "P -163,107.86" , 'BR', 0, 'R');
        $pdf->Ln(10);

        $pdf->setX(30);
        $pdf->Cell(105, 0, 'TOTAL PROFORMA SUPPLIER INVOICE (PSI) (Gross Amount) : ', 'LBR', 0, 'L');
        $pdf->cell(125, 0, "", 'LBR', 0, 'R');
        $pdf->cell(30, 0, "P 60,179.95 " , 'LBR', 0, 'R');
        $pdf->Ln(5); 
        $pdf->setX(30);
        $pdf->Cell(105, 0, 'Less: TOTAL SOP Deduction ', 'LBR', 0, 'R');
        $pdf->cell(125, 0, "", 'LBR', 0, 'R');
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->cell(30, 0, "P -5,857.46 ", 'LBR', 0, 'R');
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Ln(5); 
        $pdf->setX(30);
        $pdf->Cell(105, 0, 'TOTAL CRF/CV Amount : ', 'R', 0, 'L');
        $pdf->cell(125, 0, "", 'LBR', 0, 'R');
        $pdf->cell(30, 0, "P 54,322.49", 'R', 0, 'R');
        $pdf->Ln(10);



        $pdf->Output('werwerwer.pdf', 'I');
    }

    public function exportExcel()
    {
        $filename = 'sample'; //your file name
        $logo     =  getcwd().'/assets/img/alturas.png';

        $data1 = $this->db->query('SELECT h.proforma_header_id,count(dv.discount_id) as number_of_discs FROM cwo.proforma_header h inner join discountvat dv on dv.proforma_header_id = h.proforma_header_id
                                   where h.supplier_id = 9 group by  h.proforma_header_id')->result_array();
        $data2 = $this->db->query('SELECT * FROM cwo.discountvat where supplier_id = 9 order by proforma_header_id')->result_array();

        $objPHPExcel = new PHPExcel();
        $objDrawing  = new PHPExcel_Worksheet_Drawing();        

        /********************* SHEET Styles **********************/
        $systemTitleStyle  = array( 'font'=> array(
                                                'color' => array('rgb' => '000000'),
                                                'name'  =>  'Arial',
                                                'size'  =>  12,
                                                'bold'  =>  true ),
                                    'alignment' => array(
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT )  );
        $supplierStyle     = array( 'font'=> array(
                                        'color' => array('rgb' => '000000'),
                                        'name'  =>  'Arial',
                                        'size'  =>  12,
                                        'bold'  =>  true ) );
        $reportHeaderStyle = array( 'font'=> array(
                                              'color' => array('rgb' => '000000'),
                                              'name'  =>  'Arial',
                                              'size'  =>  10 ) );
        $columnHeaderStyle = array( 'font'=> array(
                                             'color' => array('rgb' => 'ffffff'),
                                             'name'  =>  'Arial',
                                             'size'  =>  9
                                    ),
                                    'fill' => array(
                                            'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                                            'color' => array('rgb' => '000000')
                                    ),
                                    'alignment' => array(
                                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)  );
        $amountAlignment   = array('alignment' => array(
                                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));
        
        $BStyle = array('borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN
                                )
                            ) );
        /********************* SHEET Styles **********************/

         /********************* SHEET Headings **********************/
         $active_sheet = $objPHPExcel->getActiveSheet();
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
                     ->setCellValue('T1', date("F d, Y - h:i:s A"))
                     ->getStyle('M1')->applyFromArray($supplierStyle);//
        $active_sheet->setCellValue('A3', 'MEAD JOHNSON NUTRITION PHILS., INC.')
                     ->getStyle('A3')->applyFromArray($supplierStyle);
        $active_sheet->setCellValue('A4', 'PROFORMA SUPPLIER INVOICE vs PURCHASE INVOICE - VARIANCE REPORT')
                     ->setCellValue('A6', 'PROFORMA SUPPLIER INVOICE')
                     ->setCellValue('M6', 'PURCHASE INVOICE')
                     ->getStyle("A4:M6")->applyFromArray($reportHeaderStyle);     
        /********************* SHEET Headings **********************/             
            
      
        $lastRow = $this->getHighestDataRow($active_sheet);
        $this->writeTableColumnHeader($active_sheet,$lastRow,$reportHeaderStyle,$columnHeaderStyle,'Fully-Served Item(s) :','Fully-Received Item(s) :','Variance');
        

        // $newRow    = $this->getHighestDataRow($active_sheet);
        // $active_sheet->setCellValue('A'.$newRow,'A'.$newRow);
        $previousProf = "";
            foreach($data1 as $one)
            {
                $height  = $one['number_of_discs'] - 1 ;
                $newRow  = $this->getHighestDataRow($active_sheet) ;
                            
                $height1 = $height + $newRow ;
                $active_sheet->mergeCells('A'.$newRow.':A'.$height1);
                $active_sheet->mergeCells('B'.$newRow.':B'.$height1);
                
                foreach($data2 as $two)
                {     
                    if($one['proforma_header_id'] == $two['proforma_header_id']){   
                        if( $two['proforma_header_id'] != $previousProf){
                            $active_sheet->setCellValue('A'.$newRow,$one['proforma_header_id']);
                            $active_sheet->setCellValue('B'.$newRow,$one['proforma_header_id']);
                            $active_sheet->setCellValue('C'.$newRow,$two['discount']);
                            $active_sheet->setCellValue('D'.$newRow,$two['total_discount']);
                            $active_sheet->getStyle("A".$newRow.":D".$newRow)->applyFromArray($BStyle);
                        } else if( $two['proforma_header_id'] == $previousProf) {
                            $newRow    = $this->getHighestDataRow($active_sheet);
                            $active_sheet->setCellValue('C'.$newRow,$two['discount']);
                            $active_sheet->setCellValue('D'.$newRow,$two['total_discount']);
                            $active_sheet->getStyle("A".$newRow.":D".$newRow)->applyFromArray($BStyle); 

                        }
                    }

                    $previousProf = $two['proforma_header_id'] ;
                        
                }
                
            }
  

        /********************* Autoresize column width depending upon contents **********************/
        foreach(range('A', 'Z') as $columnID) {
            $active_sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        /********************* Autoresize column width depending upon contents **********************/


        $active_sheet->setTitle('variance_report'); //give title to sheet
        // $active_sheet->getProtection()->setSelectLockedCells(true);
        // $active_sheet->getProtection()->setFormatRows(true);
        // $active_sheet->getProtection()->setFormatCells(true);
        // $active_sheet->getProtection()->setObjects(true);
        // $active_sheet->getProtection()->setPassword('password');
        $active_sheet->getProtection()->setSheet(true);
        $objPHPExcel->setActiveSheetIndex(0);        
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;Filename=$filename.xls");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    private function writeTableColumnHeader($active_sheet,$lastRow,$reportHeaderStyle,$columnHeaderStyle,$one,$two,$three)
    {
        $active_sheet->setCellValue('A'.$lastRow, $one)
                     ->setCellValue('M'.$lastRow, $two)
                     ->getStyle("A".$lastRow.":M".$lastRow)->applyFromArray($reportHeaderStyle);

        $lastRow = $this->getHighestDataRow($active_sheet);

        //Proforma
        $active_sheet->setCellValue('A'.$lastRow, 'Proforma')
                     ->setCellValue('B'.$lastRow, 'Item')
                     ->setCellValue('C'.$lastRow, 'Description')
                     ->setCellValue('D'.$lastRow, 'UOM')
                     ->setCellValue('E'.$lastRow, 'Qty')
                     ->setCellValue('F'.$lastRow, 'Net Price')
                     ->setCellValue('G'.$lastRow, 'Discounted Price')
                     ->setCellValue('H'.$lastRow, 'Gross Price')
                     ->setCellValue('I'.$lastRow, 'Net Amount')
                     ->setCellValue('J'.$lastRow, 'Discounted Amount')
                     ->setCellValue('K'.$lastRow, 'Gross Amount')
                     ->getStyle("A".$lastRow.":K".$lastRow)->applyFromArray($columnHeaderStyle);
        // //PI
        $active_sheet->setCellValue('M'.$lastRow, 'PI No')
                     ->setCellValue('N'.$lastRow, 'Date')
                     ->setCellValue('O'.$lastRow, 'Item')
                     ->setCellValue('P'.$lastRow, 'Description')
                     ->setCellValue('Q'.$lastRow, 'UOM')
                     ->setCellValue('R'.$lastRow, 'Qty')
                     ->setCellValue('S'.$lastRow, 'Unit Price')
                     ->setCellValue('T'.$lastRow, 'Discounted Price')
                     ->setCellValue('U'.$lastRow, 'Net Price')
                     ->setCellValue('V'.$lastRow, 'Gross Amount')
                     ->setCellValue('W'.$lastRow, 'Discounted Amount')
                     ->setCellValue('X'.$lastRow, 'Net Amount')
                     ->setCellValue('Y'.$lastRow, $three)
                     ->setCellValue('Z'.$lastRow, 'Variance')
                     ->getStyle("M".$lastRow.":Z".$lastRow)->applyFromArray($columnHeaderStyle);
        $active_sheet->getRowDimension($lastRow)->setRowHeight(15);

         //Proforma
        $lastRow = $this->getHighestDataRow($active_sheet);
        $active_sheet->setCellValue('A'.$lastRow, '')
                      ->setCellValue('B'.$lastRow, '')
                      ->setCellValue('C'.$lastRow, '')
                      ->setCellValue('D'.$lastRow, '')
                      ->setCellValue('E'.$lastRow, '')
                      ->setCellValue('F'.$lastRow, '(Net of VAT & Disct.)')
                      ->setCellValue('G'.$lastRow, '(Net of Disct. incl. VAT)')
                      ->setCellValue('H'.$lastRow, '(Gross of VAT & Disct.)')
                      ->setCellValue('I'.$lastRow, '')
                      ->setCellValue('J'.$lastRow, '')
                      ->setCellValue('K'.$lastRow, '')
                      ->getStyle("A".$lastRow.":K".$lastRow)->applyFromArray($columnHeaderStyle);
        $active_sheet->setCellValue('M'.$lastRow, '')
                     ->setCellValue('N'.$lastRow, '')
                     ->setCellValue('O'.$lastRow, '')
                     ->setCellValue('P'.$lastRow, '')
                     ->setCellValue('Q'.$lastRow, '')
                     ->setCellValue('R'.$lastRow, '')
                     ->setCellValue('S'.$lastRow, '(Gross)')
                     ->setCellValue('T'.$lastRow, '(Net of Disct. incl. VAT)')
                     ->setCellValue('U'.$lastRow, '(Net of VAT & Disct.)')
                     ->setCellValue('V'.$lastRow, '')
                     ->setCellValue('W'.$lastRow, '')
                     ->setCellValue('X'.$lastRow, '')
                     ->setCellValue('Y'.$lastRow, '(Qty)')
                     ->setCellValue('Z'.$lastRow, 'Discounted Amount')
                     ->getStyle("M".$lastRow.":Z".$lastRow)->applyFromArray($columnHeaderStyle);
        $active_sheet->getRowDimension($lastRow)->setRowHeight(15);
    }

    

    private function getHighestDataRow($active_sheet)
    {
        return $active_sheet->getHighestDataRow() + 1;
    }



   
}
