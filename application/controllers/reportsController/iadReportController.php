<?php
defined('BASEPATH') or exit('No direct script access allowed');

class IadReportController extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('upload');
        $this->load->library('fpdf');
        $this->load->model('iadreportmodel');
        $this->load->library('session');
        $this->load->library('form_validation');
        date_default_timezone_set('Asia/Manila');


        //Disable Cache
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        ('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    public function getIadReports()
    {
        $data     = $this->input->post(NULL, FILTER_SANITIZE_STRING);
        $reports  = '';
        $supplier = '';
        $filename = '';
        $msg      = array();

        if (!empty($data)) {
            if ($data['searchBy'] == 'All Supplier') {
                $reports  = $this->iadreportmodel->getIadReports($data['searchBy'], null, $data['dateFrom1'], $data['dateTo1']);

                if (!empty($reports)) {

                    $filename = $this->perMonthReport($reports, $data['dateFrom1'], $data['dateTo1']);
                    $msg      = ['message' => 'IAD Report Generation Complete.', 'info' => 'Success', 'file' => $filename];
                } else {
                    $msg      = ['message' => 'No Data Found.', 'info' => 'No Data'];
                }
            } else if ($data['searchBy'] == 'Supplier') {

                $supplierID = str_replace(array('[', ']'), "", json_encode($data['supplierSelect']));
                $reports  = $this->iadreportmodel->getIadReports($data['searchBy'], $supplierID, $data['dateFrom1'], $data['dateTo1']);
                $supplier = $this->iadreportmodel->getSupplier($supplierID);

                if (!empty($reports)) {
                    $filename = $this->perMonthReport($reports, $data['dateFrom1'], $data['dateTo1']);
                    $msg      = ['message' => 'IAD Report Generation Complete.', 'info' => 'Success', 'file' => $filename];
                } else {
                    $msg      = ['message' => 'No Data Found.', 'info' => 'No Data'];
                }
            }
        } else {
            $msg = ['message' => 'Please input fields that are required.', 'info' => 'No Data'];
        }

        echo JSONResponse($msg);
    }

    public function perMonthReport($data, $date1, $date2)
    {
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
        $pdf->Cell(340, 0, 'AS OF ' . $dateTo, 0, 0, 'C');
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
        // $pdf->cell(0, 5, "", 0, 0, 'C');

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
        $totalDeductions = 0;

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
                    $deductions = $this->iadreportmodel->getDeductions($lines['crf_id']);
                    $c = $c + $deductions->amount_total;

                    $y = (float) $lines['total_crf_amount'];

                    if ($lines['crf_no'] != $previousCRF) {
                        $pdf->setFont('Arial', '', 9);
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

                        // if ($c > 0) {
                        //     $pdf->SetTextColor(255, 0, 0);
                        //     $pdf->cell(30, $height2, number_format($c, 2), 1, 0, 'R');
                        // } else {
                        //     $pdf->SetTextColor(0, 0, 0);
                        //     $pdf->cell(30, $height2, number_format($c, 2), 1, 0, 'R');
                        // }

                        if ($c > 0 || $c < 0) {
                            $pdf->SetTextColor(255, 0, 0);
                            $pdf->cell(30, $height2, number_format($c, 2), 1, 0, 'R');
                            $pdf->cell(18, $height2, "Unclosed", 1, 0, 'C');
                            $unclosed++;
                        } else if ($c == 0) {
                            $pdf->SetTextColor(0, 0, 0);
                            $pdf->cell(30, $height2, number_format($c, 2), 1, 0, 'R');
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
                        $totalDeductions += $deductions->amount_total;

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
                        $pdf->cell(20, 5, '', 0, 0, 'C');
                        $pdf->cell(30, 5, '', 0, 0, 'R');
                        $pdf->cell(18, 5, '', 0, 0, 'C');
                        $pdf->cell(0, 5, "", 0, 0, 'C');

                        $totalB  += $lines['amt_including_vat'];
                        $piTotal += $lines['amt_including_vat'];
                    } else {
                        $deductions = $this->iadreportmodel->getDeductions($lines['crf_id']);
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

                        if ($c > 0 || $c < 0) {
                            $pdf->SetTextColor(255, 0, 0);
                            $pdf->cell(30, $height2, number_format($c, 2), 1, 0, 'R');
                            $pdf->cell(18, $height2, "Unclosed", 1, 0, 'C');
                            $unclosed++;
                        } else if ($c == 0) {
                            $pdf->SetTextColor(0, 0, 0);
                            $pdf->cell(30, $height2, number_format($c, 2), 1, 0, 'R');
                            $pdf->cell(18, $height2, "Closed", 1, 0, 'C');
                            $closed++;
                        }


                        // if ($c > 0) {
                        //     $pdf->cell(18, $height2, "Unclosed", 1, 0, 'C');
                        //     $unclosed++;
                        // } else if ($c < 0) {
                        //     $pdf->cell(18, $height2, "Unclosed", 1, 0, 'C');
                        //     $unclosed++;
                        // } else if ($c == 0) {
                        //     $pdf->cell(18, $height2, "Closed", 1, 0, 'C');
                        //     $closed++;
                        // }

                        $pdf->cell(0, 5, "", 0, 0, 1);
                        $pdf->SetTextColor(0, 0, 0);

                        $totalA          += $lines['total_crf_amount'];
                        $totalB          += $lines['amt_including_vat'];
                        $totalB = $totalB - $deductions->amount_total;
                        $crfTotal        += $lines['total_crf_amount'];
                        $piTotal         += $lines['amt_including_vat'];
                        $unclosedBalance += $c;
                        $totalDeductions += $deductions->amount_total;

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
        $pdf->Cell(25, 0, number_format($piTotal - $totalDeductions, 2), 0, 0, 'R');
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
        $pdf->Output('files/Reports/IADReports/' . $file_name, 'F');

        return $file_name;
    }

    public function perSupplier($data, $date1, $date2)
    {
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

        $pdf->setFont('Arial', 'B', 10);
        $pdf->cell(40, 6, "CWO NO", 1, 0, 'C', TRUE);
        $pdf->cell(40, 6, "CRF NO", 1, 0, 'C', TRUE);
        $pdf->cell(40, 6, "POSTING DATE", 1, 0, 'C', TRUE);
        $pdf->cell(35, 6, "AMOUNT", 1, 0, 'C', TRUE);
        $pdf->cell(40, 6, "PI NO", 1, 0, 'C', TRUE);
        $pdf->cell(40, 6, "POSTING DATE", 1, 0, 'C', TRUE);
        $pdf->cell(35, 6, "AMOUNT", 1, 0, 'C', TRUE);
        $pdf->cell(40, 6, "UNCLOSED BALANCE", 1, 0, 'C', TRUE);
        $pdf->cell(25, 6, "REMARKS", 1, 0, 'C', TRUE);

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
                    $y = (float) $lines['total_crf_amount'];

                    if ($lines['crf_no'] != $previousCRF) {
                        $pdf->setFont('Arial', '', 10);
                        $pdf->cell(40, $height2, $lines['reference_no'], 1, 0, 'C');
                        $pdf->cell(40, $height2, $lines['crf_no'], 1, 0, 'C');
                        $pdf->cell(40, $height2, $lines['crf_date'], 1, 0, 'C');
                        $pdf->cell(35, $height2, number_format($lines['total_crf_amount'], 2), 1, 0, 'R');
                        $pdf->cell(40, 5, $lines['pi_no'], 1, 0, 'C');
                        $pdf->cell(40, 5, $lines['posting_date'], 1, 0, 'C');
                        $pdf->cell(35, 5, number_format($lines['amt_including_vat'], 2), 1, 0, 'R');

                        if ($c > 0) {
                            $pdf->SetTextColor(255, 0, 0);
                        } else {
                            $pdf->SetTextColor(0, 0, 0);
                        }

                        $pdf->cell(40, $height2, number_format($c, 2), 1, 0, 'R');
                        $pdf->cell(25, $height2, $lines['remarks'], 1, 0, 'C');
                        $pdf->cell(0, 5, "", 0, 0, 1);

                        $totalA          += $lines['total_crf_amount'];
                        $totalB          += $lines['amt_including_vat'];
                        $crfTotal        += $lines['total_crf_amount'];
                        $piTotal         += $lines['amt_including_vat'];
                        $unclosedBalance += $c;

                        if ($lines['remarks'] === 'Unserved') {
                            $unservedCount[]  = $lines['remarks'];
                        } else {
                            $closeTotalCount[] = $lines['remarks'];
                        }

                        if ($height1 <= 5) {
                            $counter = 0;
                        }

                        $pdf->SetTextColor(0, 0, 0);
                    }
                } else if ($lines['supplier_name'] == $previousName) {
                    if ($lines['crf_no'] == $previousCRF) {
                        $pdf->setFont('Arial', '', 10);
                        $pdf->cell(40, 5, '', 0, 0, 'C');
                        $pdf->cell(40, 5, '', 0, 0, 'C');
                        $pdf->cell(40, 5, '', 0, 0, 'C');
                        $pdf->cell(35, 5, '', 0, 0, 'R');
                        $pdf->cell(40, 5, $lines['pi_no'], 1, 0, 'C');
                        $pdf->cell(40, 5, $lines['posting_date'], 1, 0, 'C');
                        $pdf->cell(35, 5, number_format($lines['amt_including_vat'], 2), 1, 0, 'R');
                        $pdf->cell(40, 5, '', 0, 0, 'R');
                        $pdf->cell(25, 5, '', 0, 0, 'C');
                        $pdf->cell(0, 5, "", 0, 0, 1);

                        $totalB  += $lines['amt_including_vat'];
                        $piTotal += $lines['amt_including_vat'];
                    } else {
                        $pdf->setFont('Arial', '', 10);
                        $pdf->cell(40, $height2, $lines['reference_no'], 1, 0, 'C');
                        $pdf->cell(40, $height2, $lines['crf_no'], 1, 0, 'C');
                        $pdf->cell(40, $height2, $lines['crf_date'], 1, 0, 'C');
                        $pdf->cell(35, $height2, number_format($lines['total_crf_amount'], 2), 1, 0, 'R');
                        $pdf->cell(40, 5, $lines['pi_no'], 1, 0, 'C');
                        $pdf->cell(40, 5, $lines['posting_date'], 1, 0, 'C');
                        $pdf->cell(35, 5, number_format($lines['amt_including_vat'], 2), 1, 0, 'R');

                        if ($c > 0) {
                            $pdf->SetTextColor(255, 0, 0);
                        } else {
                            $pdf->SetTextColor(0, 0, 0);
                        }

                        $pdf->cell(40, $height2, number_format($c, 2), 1, 0, 'R');
                        $pdf->cell(25, $height2, $lines['remarks'], 1, 0, 'C');
                        $pdf->cell(0, 5, "", 0, 0, 1);
                        $pdf->SetTextColor(0, 0, 0);

                        $totalA          += $lines['total_crf_amount'];
                        $totalB          += $lines['amt_including_vat'];
                        $crfTotal        += $lines['total_crf_amount'];
                        $piTotal         += $lines['amt_including_vat'];
                        $unclosedBalance += $c;

                        if ($lines['remarks'] === 'Unserved') {
                            $unservedCount[]  = $lines['remarks'];
                        } else {
                            $closeTotalCount[] = $lines['remarks'];
                        }
                    }
                }

                $counter++;
                if ($counter >= $counter2) {
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->ln();
                    if ($height1 > 5) {
                        $pdf->setFont('Arial', 'B', 10);
                        $pdf->cell(120, 5, "SUB TOTAL", 'L,B', 0, 'L');
                        $pdf->cell(35, 5, number_format($totalA, 2), 'B,B', 0, 'R');
                        $pdf->cell(115, 5, number_format($totalB, 2), 'B,B', 0, 'R');
                        $pdf->cell(40, 5, number_format($totalA - $totalB, 2), 'B,B', 0, 'R');
                        $pdf->cell(25, 5, "", 'R,B,B', 0, 'C');

                        $counter = 0;
                        $totalA  = 0;
                        $totalB  = 0;
                    } else if ($height1 == 10) {
                        $pdf->setFont('Arial', 'B', 10);
                        $pdf->cell(120, 5, "SUB TOTAL", 'L,B', 0, 'L');
                        $pdf->cell(35, 5, number_format($totalA, 2), 'B,B', 0, 'R');
                        $pdf->cell(115, 5, number_format($totalB, 2), 'B,B', 0, 'R');
                        $pdf->cell(40, 5, number_format($totalA - $totalB, 2), 0, 0, 'R');
                        $pdf->cell(25, 5, "", 'R,B,B', 0, 'C');

                        $counter = 0;
                        $totalA  = 0;
                        $totalB  = 0;
                    }
                }

                $pdf->ln();

                $unservedTotal = count($unservedCount);
                $closedTotal   = count($closeTotalCount);
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
        $pdf->Cell(300, 0, 'Total CRF Amounts:', 0, 0, 'R');
        $pdf->Cell(25, 0, number_format($crfTotal, 2), 0, 0, 'R');
        $pdf->ln(5);
        $pdf->Cell(300, 0, 'Total PI Amounts:', 0, 0, 'R');
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

        $file_name =  'CWO-' . time() . '.pdf';
        $pdf->Output('files/Reports/IADReports/' . $file_name, 'F');

        return $file_name;
    }

    public function virtualData()
    {
        $data =
            array(
                array(
                    'supplier_name'     => 'MONDELEZ PHILS., INC.',
                    'crf_id'            => '1',
                    'crf_no'            => 'CRF2010867',
                    'crf_date'          => '2020-10-30',
                    'total_crf_amount'  =>  '1015833.86',
                    'proforma_code'     =>  'MONDELEZ-1',
                    'delivery_date'     =>  'NOV 03 2020',
                    'total_psi_amount'  => '1024985.52',
                    'pi_no'             =>  'ISM-P4233020',
                    'posting_date'      =>  '2020-11-09',
                    'amt_including_vat' =>  '683549.40',
                    'remarks'           =>  'Unserved'
                ), array(
                    'supplier_name'     => 'MONDELEZ PHILS., INC.',
                    'crf_id'            => '1',
                    'crf_no'            => 'CRF2010867',
                    'crf_date'          => '2020-10-30',
                    'total_crf_amount'  =>  '1015833.86',
                    'proforma_code'     =>  'MONDELEZ-1',
                    'delivery_date'     =>  'NOV 03 2020',
                    'total_psi_amount'  => '1024985.52',
                    'pi_no'             =>  'CDC-P4135339',
                    'posting_date'      =>  '2020-11-10',
                    'amt_including_vat' =>  '331671.53',
                    'remarks'           =>  'Unserved'
                ), array(
                    'supplier_name'     => 'ALASKA.',
                    'crf_id'            => '2',
                    'crf_no'            => 'CRF2010868',
                    'crf_date'          => '2020-10-30',
                    'total_crf_amount'  =>  '1000',
                    'proforma_code'     =>  'MONDELEZ-1',
                    'delivery_date'     =>  'NOV 03 2020',
                    'total_psi_amount'  => '1024765.52',
                    'pi_no'             =>  'ISM-P4233025',
                    'posting_date'      =>  '2020-11-09',
                    'amt_including_vat' =>  '800',
                    'remarks'           =>  'Unserved'
                ), array(
                    'supplier_name'     => 'DINOSAUR., INC.',
                    'crf_id'            => '3',
                    'crf_no'            => 'CRF2010869',
                    'crf_date'          => '2020-10-30',
                    'total_crf_amount'  =>  '5000.86',
                    'proforma_code'     =>  'MONDELEZ-1',
                    'delivery_date'     =>  'NOV 03 2020',
                    'total_psi_amount'  => '1024555.52',
                    'pi_no'             =>  'CDC-P4233023',
                    'posting_date'      =>  '2020-11-09',
                    'amt_including_vat' =>  '2000.40',
                    'remarks'           =>  'Unserved'
                ), array(
                    'supplier_name'     => 'DINOSAUR., INC.',
                    'crf_id'            => '3',
                    'crf_no'            => 'CRF2010869',
                    'crf_date'          => '2020-10-30',
                    'total_crf_amount'  =>  '5000.86',
                    'proforma_code'     =>  'MONDELEZ-1',
                    'delivery_date'     =>  'NOV 03 2020',
                    'total_psi_amount'  => '1024000.52',
                    'pi_no'             =>  'CD-P4233024',
                    'posting_date'      =>  '2020-11-09',
                    'amt_including_vat' =>  '100.40',
                    'remarks'           =>  'Unserved'
                ), array(
                    'supplier_name'     => 'ARTHURS EXCALIBUR',
                    'crf_id'            => '4',
                    'crf_no'            => 'CRF2021789',
                    'crf_date'          => '2020-10-15',
                    'total_crf_amount'  =>  '10000',
                    'proforma_code'     =>  'MONDELEZ-1',
                    'delivery_date'     =>  'NOV 03 2020',
                    'total_psi_amount'  => '1024000.52',
                    'pi_no'             =>  'CD-PI12345',
                    'posting_date'      =>  '2020-11-01',
                    'amt_including_vat' =>  '500',
                    'remarks'           =>  'Unserved'
                ), array(
                    'supplier_name'     => 'ARTHURS EXCALIBUR',
                    'crf_id'            => '4',
                    'crf_no'            => 'CRF2021790',
                    'crf_date'          => '2020-10-15',
                    'total_crf_amount'  =>  '60000',
                    'proforma_code'     =>  'MONDELEZ-1',
                    'delivery_date'     =>  'NOV 03 2020',
                    'total_psi_amount'  => '1024000.52',
                    'pi_no'             =>  'CD-PI12346',
                    'posting_date'      =>  '2020-11-01',
                    'amt_including_vat' =>  '10000',
                    'remarks'           =>  'Unserved'
                ), array(
                    'supplier_name'     => 'ARTHURS EXCALIBUR',
                    'crf_id'            => '5',
                    'crf_no'            => 'CRF2021799',
                    'crf_date'          => '2020-10-15',
                    'total_crf_amount'  =>  '50000',
                    'proforma_code'     =>  'MONDELEZ-1',
                    'delivery_date'     =>  'NOV 03 2020',
                    'total_psi_amount'  => '1024000.52',
                    'pi_no'             =>  'CD-PI12347',
                    'posting_date'      =>  '2020-11-01',
                    'amt_including_vat' =>  '20500',
                    'remarks'           =>  'Unserved'
                )
            );

        return $data;
    }
}
