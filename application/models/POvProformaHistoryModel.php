<?php
defined('BASEPATH') or exit('No direct script access allowed');

class POvProformaHistoryModel extends CI_Model
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
            $result = $this->db->SELECT('tr.*, po.po_no as document1, pr.proforma_code as document2, s.acroname, l.l_acroname, f.filename')
                ->FROM('povprof_transaction tr')
                ->JOIN('po_header po', 'tr.po_header_id = po.po_header_id', 'LEFT')
                ->JOIN('proforma_header pr', 'tr.proforma_header_id = pr.proforma_header_id', 'LEFT')
                ->JOIN('suppliers s', 'tr.supplier_id = s.supplier_id', 'LEFT')
                ->JOIN('customers l', 'tr.customer_code = l.customer_code', 'LEFT')
                ->JOIN('povprof_files f', 'tr.tr_id = f.tr_id', 'LEFT')
                ->GET()
                ->RESULT_ARRAY();
        elseif ($filter == 'By Supplier') :
            $result = $this->db->SELECT('tr.*, po.po_no as document1, pr.proforma_code as document2, s.acroname, l.l_acroname, f.filename')
                ->FROM('povprof_transaction tr')
                ->JOIN('po_header po', 'tr.po_header_id = po.po_header_id', 'LEFT')
                ->JOIN('proforma_header pr', 'tr.proforma_header_id = pr.proforma_header_id', 'LEFT')
                ->JOIN('suppliers s', 'tr.supplier_id = s.supplier_id', 'LEFT')
                ->JOIN('customers l', 'tr.customer_code = l.customer_code', 'LEFT')
                ->JOIN('povprof_files f', 'tr.tr_id = f.tr_id', 'LEFT')
                ->WHERE('tr.supplier_id = "' . $supplierid . '"')
                ->GET()
                ->RESULT_ARRAY();
        elseif ($filter == 'By Location') :
            $result = $this->db->SELECT('tr.*, po.po_no as document1, pr.proforma_code as document2, s.acroname, l.l_acroname, f.filename')
                ->FROM('povprof_transaction tr')
                ->JOIN('po_header po', 'tr.po_header_id = po.po_header_id', 'LEFT')
                ->JOIN('proforma_header pr', 'tr.proforma_header_id = pr.proforma_header_id', 'LEFT')
                ->JOIN('suppliers s', 'tr.supplier_id = s.supplier_id', 'LEFT')
                ->JOIN('customers l', 'tr.customer_code = l.customer_code', 'LEFT')
                ->JOIN('povprof_files f', 'tr.tr_id = f.tr_id', 'LEFT')
                ->WHERE('tr.customer_code = "' . $locationid . '"')
                ->GET()
                ->RESULT_ARRAY();
        elseif ($filter == 'By Supplier and Location') :
            $result = $this->db->SELECT('tr.*, po.po_no as document1, pr.proforma_code as document2, s.acroname, l.l_acroname, f.filename')
                ->FROM('povprof_transaction tr')
                ->JOIN('po_header po', 'tr.po_header_id = po.po_header_id', 'LEFT')
                ->JOIN('proforma_header pr', 'tr.proforma_header_id = pr.proforma_header_id', 'LEFT')
                ->JOIN('suppliers s', 'tr.supplier_id = s.supplier_id', 'LEFT')
                ->JOIN('customers l', 'tr.customer_code = l.customer_code', 'LEFT')
                ->JOIN('povprof_files f', 'tr.tr_id = f.tr_id', 'LEFT')
                ->WHERE('tr.supplier_id = "' . $supplierid . '"')
                ->WHERE('tr.customer_code = "' . $locationid . '"')
                ->GET()
                ->RESULT_ARRAY();
        endif;

        return $result;
    }
}
