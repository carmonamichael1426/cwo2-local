<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ledgermodel extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    public function getSupplierLedger($id)
    {
        $result = $this->db->SELECT('*')
            ->FROM('subsidiary_ledger')
            ->WHERE('supplier_id', $id)
            ->ORDER_BY('reference_no', 'ASC')
            ->GET()
            ->RESULT_ARRAY();

        // echo $this->db->last_query();
        return $result;
    }

    public function dataFetch1($id, $table, $target)
    {
        $result = $this->db->SELECT('*')
            ->FROM($table)
            ->WHERE($target, $id)
            ->GET()
            ->ROW();
        return $result;
    }

    public function dataFetch2($id, $table, $target)
    {
        $result = $this->db->SELECT('*')
            ->FROM($table)
            ->WHERE($target, $id)
            ->GET()
            ->RESULT_ARRAY();
        return $result;
    }

    public function getProformaLines($crf_id)
    {
        $result = $this->db->SELECT('m.*, c.crf_id')
            ->FROM('proforma_line m')
            ->JOIN('proforma_header c', 'c.proforma_header_id = m.proforma_header_id', 'LEFT')
            ->WHERE('m.proforma_header_id = c.proforma_header_id')
            ->WHERE('m.proforma_header_id = c.proforma_header_id')
            ->WHERE('c.crf_id', $crf_id)
            ->GET()
            ->RESULT_ARRAY();
        return $result;
    }

    public function getPlusMinus($crf_id)
    {
        $result = $this->db->SELECT('d.*, c.crf_id')
            ->FROM('discountvat d')
            ->JOIN('proforma_header c', 'c.proforma_header_id = d.proforma_header_id', 'LEFT')
            ->WHERE('c.crf_id', $crf_id)
            ->GET()
            ->RESULT_ARRAY();
        return $result;
    }

    public function getPIHeader($crf_id)
    {
        $result = $this->db->SELECT('pi.*, c.crf_id')
            ->FROM('purchase_invoice_header pi')
            ->JOIN('crf_line c', 'c.pi_head_id = pi.pi_head_id', 'LEFT')
            ->WHERE('c.crf_id', $crf_id)
            ->GET()
            ->RESULT_ARRAY();
        return $result;
    }

    public function getPILines($crf_id)
    {
        $result = $this->db->SELECT('pl.* ,c.crf_id')
            ->FROM('purchase_invoice_line pl')
            ->JOIN('purchase_invoice_header ph', 'ph.pi_head_id = pl.pi_head_id', 'LEFT')
            ->JOIN('items i', 'i.itemcode_loc = pl.item_code', 'LEFT')
            ->JOIN('proforma_line pfl', 'pfl.item_code = i.itemcode_sup', 'LEFT')
            ->JOIN('crf_line c', 'c.pi_head_id = ph.pi_head_id', 'LEFT')
            ->WHERE('pl.pi_head_id = ph.pi_head_id')
            ->WHERE('ph.pi_head_id = c.pi_head_id')
            ->WHERE('i.itemcode_loc = pl.item_code')
            ->WHERE('pfl.item_code = i.itemcode_sup')
            ->WHERE('c.crf_id', $crf_id)
            ->GET()
            ->RESULT_ARRAY();

        return $result;
    }
}
