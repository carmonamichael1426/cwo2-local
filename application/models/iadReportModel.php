<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Iadreportmodel extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    public function getIadReports($filter, $supplierID, $dateFrom, $dateTo)
    {
        if ($filter == 'All Supplier') {

            // $result = $this->db->query("SELECT
            //                         s.supplier_name,
            //                         l.l_acroname,
            //                         sl.reference_no,
            //                         c.crf_id,
            //                         c.crf_no,
            //                         c.crf_date, 
            //                         IFNULL(c.crf_amt, 0) as total_crf_amount,
            //                         pi.pi_no ,
            //                         pi.vendor_invoice_no,
            //                         pi.posting_date,
            //                         IFNULL(pi.amt_including_vat, 0) as amt_including_vat
            //                         FROM crf_line cl
            // 						LEFT JOIN crf c ON cl.crf_id = c.crf_id
            // 						LEFT JOIN purchase_invoice_header pi ON cl.pi_head_id = pi.pi_head_id
            // 						LEFT JOIN suppliers s ON cl.supplier_id = s.supplier_id
            // 						LEFT JOIN customers l ON cl.customer_code = l.customer_code
            //                         LEFT JOIN (SELECT s.crf_id, s.reference_no 
            // 									FROM subsidiary_ledger AS s 
            //                                     LEFT JOIN crf AS cr 
            //                                     ON cr.crf_id = s.crf_id) as sl
            //                         ON sl.crf_id = c.crf_id
            //                         WHERE pi.pi_no IS NOT NULL
            //                         AND c.crf_date BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "'
            //                         GROUP BY pi.pi_no
            //                         ORDER BY sl.reference_no");

            $result = $this->db->query("SELECT
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
                                    IFNULL(pi.amt_including_vat, 0) as amount,
                                    a.amt_including_vat
                                    FROM crf_line cl
									LEFT JOIN crf c ON cl.crf_id = c.crf_id
									LEFT JOIN purchase_invoice_header pi ON cl.pi_head_id = pi.pi_head_id
                                    LEFT JOIN (SELECT
ph.so_no,
ROUND(SUM( (pl.price / i.discount)  * pl.qty * 1.12 ),2) AS amt_including_vat 
FROM proforma_header ph 
INNER JOIN proforma_line pl ON pl.proforma_header_id = ph.proforma_header_id 
INNER JOIN po_header po ON po.po_header_id = ph.po_header_id
INNER JOIN items i ON i.itemcode_sup = pl.item_code GROUP BY ph.proforma_header_id) a ON a.so_no = pi.vendor_invoice_no
									LEFT JOIN suppliers s ON cl.supplier_id = s.supplier_id
									LEFT JOIN customers l ON cl.customer_code = l.customer_code
                                    LEFT JOIN (SELECT s.crf_id, s.reference_no 
												FROM subsidiary_ledger AS s 
                                                LEFT JOIN crf AS cr 
                                                ON cr.crf_id = s.crf_id) as sl
                                    ON sl.crf_id = c.crf_id
                                    WHERE pi.pi_no IS NOT NULL
                                    AND c.crf_date BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "'
                                    GROUP BY pi.vendor_invoice_no
                                    ORDER BY sl.reference_no");
        } else if ($filter == 'Supplier') {

            $result = $this->db->query("SELECT
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
                                    AND s.supplier_id IN (" . $supplierID . ") AND c.crf_date BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "'
                                    GROUP BY pi.pi_no
                                    ORDER BY sl.reference_no");
        }

        return $result->result_array();
    }
    public function getDeductions($crf_id)
    {
        $result = $this->db->query("SELECT IFNULL(SUM(sd.amount), 0) AS amount_total 
                                    FROM sop_deduction sd
                                    LEFT JOIN (SELECT * FROM sop_head) sh 
                                    ON sd.sop_id = sh.sop_id
                                    WHERE sh.crf_id = '$crf_id'")->ROW();
        return $result;
    }
    public function getSupplier($supplier_id)
    {
        $result = $this->db->SELECT('*')
            ->FROM('suppliers')
            ->WHERE('supplier_id', $supplier_id)
            ->GET()
            ->ROW();
        return $result;
    }
}
