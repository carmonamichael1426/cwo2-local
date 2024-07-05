<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProformavPiHistoryModel extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    public function getTransactionHistory($filter, $supplierid, $locationid)
    {
        $result = array();
        if ($filter == 'All Transactions') :
            $result = $this->db->SELECT('tr.*, s.acroname, c.l_acroname,  GROUP_CONCAT(pr.proforma_code SEPARATOR ",") AS document1')
                               ->FROM('profvpi_transaction tr')
                               ->JOIN('crf_line crf', 'crf.crf_id = tr.crf_id', 'LEFT')
                               ->JOIN('proforma_header pr', 'pr.proforma_header_id = crf.proforma_header_id', 'LEFT')
                               ->JOIN('customers c', 'c.customer_code = tr.customer_code', 'LEFT')
                               ->JOIN('suppliers s', 's.supplier_id = tr.supplier_id', 'LEFT')
                               ->WHERE('crf.pi_head_id', 0)
                               ->GROUP_BY('tr.tr_id')
                               ->GET()
                               ->RESULT_ARRAY();
        elseif ($filter == 'By Supplier') :
            $result = $this->db->SELECT('tr.*, s.acroname, c.l_acroname,  GROUP_CONCAT(pr.proforma_code SEPARATOR ",") AS document1')
                               ->FROM('profvpi_transaction tr')
                               ->JOIN('crf_line crf', 'crf.crf_id = tr.crf_id', 'LEFT')
                               ->JOIN('proforma_header pr', 'pr.proforma_header_id = crf.proforma_header_id', 'LEFT')                               
                               ->JOIN('customers c', 'c.customer_code = tr.customer_code', 'LEFT')
                               ->JOIN('suppliers s', 's.supplier_id = tr.supplier_id', 'LEFT')
                               ->WHERE('tr.supplier_id = "' . $supplierid . '"')
                               ->WHERE('crf.pi_head_id', 0)
                               ->GROUP_BY('tr.tr_id')
                               ->GET()
                               ->RESULT_ARRAY();
        elseif ($filter == 'By Location') :
            $result = $this->db->SELECT('tr.*, s.acroname, c.l_acroname, GROUP_CONCAT(pr.proforma_code SEPARATOR ",") AS document1')
                               ->FROM('profvpi_transaction tr')
                               ->JOIN('crf_line crf', 'crf.crf_id = tr.crf_id', 'LEFT')
                               ->JOIN('proforma_header pr', 'pr.proforma_header_id = crf.proforma_header_id', 'LEFT')                               
                               ->JOIN('customers c', 'c.customer_code = tr.customer_code', 'LEFT')
                               ->JOIN('suppliers s', 's.supplier_id = tr.supplier_id', 'LEFT')
                               ->WHERE('tr.customer_code = "' . $locationid . '"')
                               ->WHERE('crf.pi_head_id', 0)
                               ->GROUP_BY('tr.tr_id')
                               ->GET()
                               ->RESULT_ARRAY();
        elseif ($filter == 'By Supplier and Location') :
            $result = $this->db->SELECT('tr.*, s.acroname, c.l_acroname,  GROUP_CONCAT(pr.proforma_code SEPARATOR ",") AS document1')
                               ->FROM('profvpi_transaction tr')
                               ->JOIN('crf_line crf', 'crf.crf_id = tr.crf_id', 'LEFT')
                               ->JOIN('proforma_header pr', 'pr.proforma_header_id = crf.proforma_header_id', 'LEFT')                               
                               ->JOIN('customers c', 'c.customer_code = tr.customer_code', 'LEFT')
                               ->JOIN('suppliers s', 's.supplier_id = tr.supplier_id', 'LEFT')
                               ->WHERE('tr.supplier_id = "' . $supplierid . '"')
                               ->WHERE('tr.customer_code = "' . $locationid . '"')
                               ->WHERE('crf.pi_head_id', 0)
                               ->GROUP_BY('tr.tr_id')
                               ->GET()
                               ->RESULT_ARRAY();
        endif;

        return $result;
    }

    public function getTransactionHistory2($filter, $supplierid, $locationid)
    {
        $result = array();
        if ($filter == 'All Transactions') :
            $result = $this->db->SELECT('tr.*, s.acroname, c.l_acroname, GROUP_CONCAT(pi.pi_no SEPARATOR ",") AS document2')
                               ->FROM('profvpi_transaction tr')
                               ->JOIN('crf_line crf', 'crf.crf_id = tr.crf_id', 'LEFT')
                               ->JOIN('purchase_invoice_header pi', 'pi.pi_head_id = crf.pi_head_id', 'LEFT')
                               ->JOIN('customers c', 'c.customer_code = tr.customer_code', 'LEFT')
                               ->JOIN('suppliers s', 's.supplier_id = tr.supplier_id', 'LEFT')
                               ->WHERE('crf.proforma_header_id', 0)
                               ->GROUP_BY('tr.tr_id')
                               ->GET()
                               ->RESULT_ARRAY();
        elseif ($filter == 'By Supplier') :
            $result = $this->db->SELECT('tr.*, s.acroname, c.l_acroname, GROUP_CONCAT(pi.pi_no SEPARATOR ",") AS document2')
                               ->FROM('profvpi_transaction tr')
                               ->JOIN('crf_line crf', 'crf.crf_id = tr.crf_id', 'LEFT')
                               ->JOIN('purchase_invoice_header pi', 'pi.pi_head_id = crf.pi_head_id', 'LEFT')
                               ->JOIN('customers c', 'c.customer_code = tr.customer_code', 'LEFT')
                               ->JOIN('suppliers s', 's.supplier_id = tr.supplier_id', 'LEFT')
                               ->WHERE('tr.supplier_id = "' . $supplierid . '"')
                               ->WHERE('crf.proforma_header_id', 0)
                               ->GROUP_BY('tr.tr_id')
                               ->GET()
                               ->RESULT_ARRAY();
        elseif ($filter == 'By Location') :
            $result = $this->db->SELECT('tr.*, s.acroname, c.l_acroname, GROUP_CONCAT(pi.pi_no SEPARATOR ",") AS document2')
                               ->FROM('profvpi_transaction tr')
                               ->JOIN('crf_line crf', 'crf.crf_id = tr.crf_id', 'LEFT')
                               ->JOIN('purchase_invoice_header pi', 'pi.pi_head_id = crf.pi_head_id', 'LEFT')
                               ->JOIN('customers c', 'c.customer_code = tr.customer_code', 'LEFT')
                               ->JOIN('suppliers s', 's.supplier_id = tr.supplier_id', 'LEFT')
                               ->WHERE('tr.customer_code = "' . $locationid . '"')
                               ->WHERE('crf.proforma_header_id', 0)
                               ->GROUP_BY('tr.tr_id')
                               ->GET()
                               ->RESULT_ARRAY();
        elseif ($filter == 'By Supplier and Location') :
            $result = $this->db->SELECT('tr.*, s.acroname, c.l_acroname, GROUP_CONCAT(pi.pi_no SEPARATOR ",") AS document2')
                               ->FROM('profvpi_transaction tr')
                               ->JOIN('crf_line crf', 'crf.crf_id = tr.crf_id', 'LEFT')
                               ->JOIN('purchase_invoice_header pi', 'pi.pi_head_id = crf.pi_head_id', 'LEFT')
                               ->JOIN('customers c', 'c.customer_code = tr.customer_code', 'LEFT')
                               ->JOIN('suppliers s', 's.supplier_id = tr.supplier_id', 'LEFT')
                               ->WHERE('tr.supplier_id = "' . $supplierid . '"')
                               ->WHERE('tr.customer_code = "' . $locationid . '"')
                               ->WHERE('crf.proforma_header_id', 0)
                               ->GROUP_BY('tr.tr_id')
                               ->GET()
                               ->RESULT_ARRAY();
        endif;

        return $result;
    }
}
