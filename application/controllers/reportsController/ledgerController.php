<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ledgercontroller extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('upload');
        $this->load->model('ledgermodel');
        $this->load->library('session');
        $this->load->library('form_validation');
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

    public function getLedger()
    {
        $supplierCode   = $this->uri->segment(4);
        $supplierLedger = $this->ledgermodel->getSupplierLedger($supplierCode);

        $result_withbalance = array();
        $prev_refno         = '';
        $running_balance    = 0;

        foreach ($supplierLedger as  $value) {
            if ($value['reference_no'] != $prev_refno) {
                $result_withbalance[] =
                    [
                        'ledger_id'        => $value['ledger_id'],
                        'reference_no'     => $value['reference_no'],
                        'posting_date'     => $value['posting_date'],
                        'transaction_date' => $value['transaction_date'],
                        'doc_type'         => $value['doc_type'],
                        'doc_no'           => $value['doc_no'],
                        'invoice_no'       => $value['invoice_no'],
                        'po_reference'     => $value['po_reference'],
                        'debit'            => ABS($value['debit']),
                        'credit'           => -1 * ABS($value['credit']),
                        'tag'              => $value['tag'],
                        'supplier_id'      => $value['supplier_id'],
                        'crf_id'           => $value['crf_id'],
                        'user_id'          => $value['user_id'],
                        'balance'          => $value['debit']
                    ];

                $running_balance = $value['debit'];
            } else {
                if ($value['debit'] == "0.00" || $value['debit'] == null) {
                    $new_running_balance = $running_balance - ABS(-1 * $value['credit']);

                    $result_withbalance[] =
                        [
                            'ledger_id'        => $value['ledger_id'],
                            'reference_no'     => $value['reference_no'],
                            'posting_date'     => $value['posting_date'],
                            'transaction_date' => $value['transaction_date'],
                            'doc_type'         => $value['doc_type'],
                            'doc_no'           => $value['doc_no'],
                            'invoice_no'       => $value['invoice_no'],
                            'po_reference'     => $value['po_reference'],
                            'debit'            => ABS($value['debit']),
                            'credit'           => -1 * ABS($value['credit']),
                            'tag'              => $value['tag'],
                            'supplier_id'      => $value['supplier_id'],
                            'crf_id'           => $value['crf_id'],
                            'user_id'          => $value['user_id'],
                            'balance'          => $new_running_balance,
                        ];

                    $running_balance = $new_running_balance;
                } else {
                    $new_running_balance = $running_balance + $value['debit'];

                    $result_withbalance[] =
                        [
                            'ledger_id'        => $value['ledger_id'],
                            'reference_no'     => $value['reference_no'],
                            'posting_date'     => $value['posting_date'],
                            'transaction_date' => $value['transaction_date'],
                            'doc_type'         => $value['doc_type'],
                            'doc_no'           => $value['doc_no'],
                            'invoice_no'       => $value['invoice_no'],
                            'po_reference'     => $value['po_reference'],
                            'debit'            => ABS($value['debit']),
                            'credit'           => -1 * ABS($value['credit']),
                            'tag'              => $value['tag'],
                            'supplier_id'      => $value['supplier_id'],
                            'crf_id'           => $value['crf_id'],
                            'user_id'          => $value['user_id'],
                            'balance'          => $new_running_balance,
                        ];

                    $running_balance = $new_running_balance;
                }
            }

            $prev_refno = $value['reference_no'];
        }

        echo json_encode($result_withbalance);
    }

    public function getDataLedger()
    {
        $data              = $this->input->post(NULL);
        $acroname          = $this->ledgermodel->dataFetch1($data['supplier_id'], 'suppliers', 'supplier_id');
        $details           = array();
        $proformaLineTotal = 0;
        $totalDiscount     = 0;
        $totalVAT          = 0;
        $totalPI           = 0;

        if (!empty($data)) {
            $details['crf']      = $this->ledgermodel->dataFetch1($data['crf_id'], 'crf', 'crf_id');
            $details['header']   = $this->ledgermodel->dataFetch2($data['crf_id'], 'proforma_header', 'crf_id');
            $details['lines']    = $this->ledgermodel->getProformaLines($data['crf_id']);
            $plusMinus           = $this->ledgermodel->getPlusMinus($data['crf_id']);
            $details['piHeader'] = $this->ledgermodel->getPIHeader($data['crf_id']);
            $details['piLines']  = $this->ledgermodel->getPILines($data['crf_id']);

            foreach ($details['lines'] as $value) {
                $details['total'] = $proformaLineTotal += $value['amount'];
            }

            foreach ($plusMinus as $value) {
                if ($value['discount'] != 'VAT') {
                    $totalDiscount += $value['total_discount'];
                } else {
                    $totalVAT += $value['total_discount'];
                }
            }

            foreach ($details['piLines'] as $value) {
                $details['totalPI'] = $totalPI += $value['amt_including_vat'];
            }

            $details['totalDiscount'] = $totalDiscount;
            $details['totalVAT']      = $totalVAT;

            return JSONResponse($details);
        }
    }

    public function getDataLedgerInvoice()
    {
        $data    = $this->input->post(NULL);
        $PIData  = array();
        $PITotal = 0;

        if (!empty($data)) {
            $PIData['PI_header'] = $this->ledgermodel->dataFetch1($data['doc_no'], 'purchase_invoice_header', 'pi_no');
            $PIData['PI_line']   = $this->ledgermodel->dataFetch2($PIData['PI_header']->pi_head_id, 'purchase_invoice_line', 'pi_head_id');

            foreach ($PIData['PI_line'] as $value) {
                $PIData['PI_Total'] = $PITotal += $value['amt_including_vat'];
            }

            return JSONResponse($PIData);
        }
    }
}
