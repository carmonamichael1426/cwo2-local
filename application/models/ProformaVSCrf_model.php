<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProformaVSCrf_model extends CI_Model
{


    function __construct()
    {
        parent::__construct();

        $this->load->library('session');
    }


    public function getSuppliers()
    {
        $query =  $this->db->select('supplier_id, supplier_name, acroname')
            ->where('status', 1)
            ->order_by('supplier_name', 'ASC')
            ->get('suppliers');
        return $query->result_array();
    }

    public function getCustomers()
    {
        $query =  $this->db->select('customer_code, customer_name')
            ->where('status', 1)
            ->order_by('customer_name', 'ASC')
            ->get('customers');
        return $query->result_array();
    }

    public function getSupplierData($select, $where, $target)
    {
        $query = $this->db->select($select)
            ->get_where('suppliers', array($where => $target));
        return $query->row_array();
    }

    public function getData2($table, $where, $target)
    {
        $query = $this->db->get_where($table, array($where => $target));
        return $query->row_array();
    }

    public function getVATData()
    {
        $query = $this->db->get('vat', array('status' => 1));
        return $query->row_array();
    }

    public function getCustomerData($cusId, $field)
    {
        $query = $this->db->select($field)
            ->get_where('customers', array('customer_code' => $cusId));
        return $query->row();
    }

    public function getCrf($crfId)
    {
        $query = $this->db->get_where('crf', array('crf_id' => $crfId));
        return $query->row_array();
    }

    public function getUnAppliedProforma($crfId, $supId, $str)
    {
        $query = $this->db->query(" SELECT prof.proforma_header_id,
                                    IF(prof.sales_invoice_no IS NULL OR prof.sales_invoice_no = '', 
                                        IF(prof.so_no IS NULL OR prof.so_no = '', 
                                            IF(prof.order_no IS NULL OR prof.order_no = '',po.po_reference, 
                                            prof.order_no), 
                                        prof.so_no), 
                                    prof.sales_invoice_no) AS so_no, po.po_no, po.posting_date
                                    FROM proforma_header prof 
                                    INNER JOIN po_header po ON po.po_header_id = prof.po_header_id 
                                    INNER JOIN customers c ON  c.customer_code = prof.customer_code  
                                    WHERE NOT EXISTS 
                                                (SELECT * FROM crf_line l WHERE l.proforma_header_id = prof.proforma_header_id ) 
                                    AND (prof.crf_id IS NULL or prof.crf_id = 0) 
                                    AND prof.supplier_id  = '" .$supId."'
                                    AND ( prof.proforma_code LIKE '%" .$str. "%'  OR 
                                       prof.so_no LIKE '%" .$str. "%' OR
                                       prof.order_no LIKE '%" .$str. "%' OR
                                       po.po_no LIKE '%" .$str. "%'  OR 
                                       po.po_reference LIKE '%" .$str. "%'  )
                                    ORDER BY prof.po_header_id DESC
                                    LIMIT 10 ");
        return $query->result_array();
    }

    public function getAppliedProforma($crfId, $supId)
    {
        // $query = $this->db->query('SELECT h.proforma_header_id, h.supplier_id, h.proforma_code, h.delivery_date, p.po_no, sum(l.amount)  as amount from proforma_header h 
        //                            INNER JOIN proforma_line l on l.proforma_header_id = h.proforma_header_id 
        //                            INNER JOIN po_header p on p.po_header_id = h.po_header_id
        //                            WHERE EXISTS 
        //                            (SELECT * from crf_line c WHERE c.crf_id = ' . $crfId . ' AND c.proforma_header_id = h.proforma_header_id 
        //                            AND c.pi_head_id = 0 AND c.supplier_id = ' . $supId . ') 
        //                            GROUP BY h.proforma_header_id');
        $query = $this->db->query('SELECT h.proforma_header_id, h.supplier_id, h.proforma_code, h.delivery_date, p.po_no, sum(l.amount)  as amount from proforma_header h 
                                    INNER JOIN proforma_line l on l.proforma_header_id = h.proforma_header_id 
                                    INNER JOIN po_header p on p.po_header_id = h.po_header_id
                                    INNER JOIN crf_line c on c.proforma_header_id = h.proforma_header_id
                                    WHERE c.crf_id = '.$crfId. ' AND c.pi_head_id = 0 AND c.supplier_id = '.$supId.' GROUP BY h.proforma_header_id');
        return $query->result_array();
    }

    public function getAppliedProformaInCrfLine($crfId)
    {
        $query = $this->db->get_where('crf_line', array('crf_id' => $crfId, 'pi_head_id' => 0));
        return $query->result_array();
    }

    public function getCrfs($supId, $cusId, $from, $to)
    {
        $query = $this->db->select('c.crf_id,c.crf_no,c.crf_date,c.crf_amt,c.remarks,c.status,c.audited,s.supplier_id,s.supplier_name,s.has_deal,h.sop_no,c.crfvspi')
                          ->from('crf c')
                          ->join('suppliers s', 's.supplier_id = c.supplier_id', 'inner')
                          ->join('sop_head h', 'h.sop_id = c.sop_id', 'left')
                          ->where('c.supplier_id', $supId)
                          ->where('c.customer_code', $cusId)
                          ->where('c.crf_date >=', $from)
                          ->where('c.crf_date <=', $to)
                          ->order_by('c.crf_date', 'DESC')
                          ->get();
        return $query->result_array();
    }

    public function proformaHead($proformaId)
    {
        $query  = $this->db->select('ph.proforma_header_id, ph.so_no, ph.proforma_code, ph.delivery_date, ph.proforma_code, c.l_acroname, ph.customer_code,o.po_no,count(dv.discount_id) as numberOfDiscount')
                           ->from('proforma_header ph')
                           ->join('po_header o', 'o.po_header_id = ph.po_header_id', 'inner')
                           ->join('customers c', 'c.customer_code = ph.customer_code', 'inner')
                           ->join('discountvat dv', 'dv.proforma_header_id = ph.proforma_header_id', 'left')
                           ->where('ph.proforma_header_id', $proformaId)
                           ->get();
        return $query->row_array();
    }

    public function proformaLine($profId, $supId)
    {
        $query = $this->db->select('l.*,i.id,i.itemcode_loc,i.itemcode_sup,i.item_division, i.item_department_code, i.item_group_code')
                          ->from('proforma_line l')
                          ->join('items i', 'i.itemcode_loc = l.itemcode_loc', 'left')
                          ->where('l.proforma_header_id', $profId)
                          ->where('i.supplier_id', $supId)
                          ->group_start()
                              ->where('l.free', 0)
                              ->or_where('l.free', null)
                          ->group_end()
                          ->get();
        return $query->result_array();
    }

    public function freeItems($profId)
    {
        $query = $this->db->select('p.proforma_code,l.*,i.id')
                          ->from('proforma_line l')
                          ->join('items i', 'i.itemcode_loc = l.itemcode_loc', 'left')
                          ->join('proforma_header p', 'p.proforma_header_id = l.proforma_header_id')
                          ->where('l.proforma_header_id', $profId)
                          ->where('l.free', 1)
                          ->get();
        return $query->result_array();
    }

    public function proformaDiscVat($proformaId)
    {
        $query = $this->db->get_where('discountvat', array('proforma_header_id' => $proformaId));
        return $query->result_array();
    }

    public function getDeals($dealId)
    {
        $query = $this->db->get_where('vendors_deal_line', array('vendor_deal_head_id' => $dealId));
        return $query->result_array();
    }

    public function getSumDiscVat($profId, $supId)
    {
        $query = $this->db->query('SELECT proforma_header_id, sum(total_discount) as discVat FROM discountvat WHERE proforma_header_id = ' . $profId . ' AND supplier_id = ' . $supId);
        return $query->row_array();
    }
    public function uploadCrf($crfNo, $supId, $cusId, $insert)
    {
        $query = $this->db->get_where('crf', array('crf_no' => $crfNo, 'supplier_id' => $supId, 'customer_code' => $cusId));

        if ($query->num_rows() == 0) {
            $this->db->insert('crf', $insert);
            return $this->db->insert_id();
        } else {

            die("exists");
        }
    }

    public function getProfData($profId)
    {
        $query = $this->db->get_where('proforma_header', array('proforma_header_id' => $profId));
        return $query->row_array();
    }

    public function getDocNo($useNext = false)
    {
        $sequence = getSequenceNo(
            [
                'code'          => "RN",
                'number'        => '1',
                'lpad'          => '7',
                'pad_string'    => '0',
                'description'   => "Reference Number"
            ],
            [
                'table'     =>  'subsidiary_ledger',
                'column'    => 'reference_no'
            ],

            $useNext
        );

        return $sequence;
    }

    public function getDocNo2($useNext = false)
    {
        $sequence = getSequenceNo(
            [
                'code'          => "TR",
                'number'        => '1',
                'lpad'          => '7',
                'pad_string'    => '0',
                'description'   => "Transaction"
            ],
            [
                'table'     =>  'profvcrf_transaction',
                'column'    => 'tr_no'
            ],

            $useNext
        );

        return $sequence;
    }

    public function untagProforma($profId)
    {
        $query = $this->db->set('crf_id', 0)
            ->where('proforma_header_id', $profId)
            ->update('proforma_header');
        return $query;
    }

    public function getSopDeduction($crfId) //old
    {
        $query = $this->db->query('SELECT f.crf_id, sop.sop_no, sop.date_created, ded.* FROM crf f 
                                   INNER JOIN sop_head sop ON sop.sop_id = f.sop_id
                                   INNER JOIN sop_deduction ded ON ded.sop_id = f.sop_id
                                   WHERE f.crf_id = ' . $crfId);
        return $query->result_array();
    }

    public function getSOpCharges($crfId)
    {
        $query = $this->db->query('SELECT f.crf_id, sop.sop_no, sop.date_created, charge.* FROM crf f 
                                   INNER JOIN sop_head sop ON sop.sop_id = f.sop_id
                                   INNER JOIN sop_charges charge ON charge.sop_id = f.sop_id
                                   WHERE f.crf_id = ' . $crfId);
        return $query->result_array();
    }

    public function getData($query1)
    {
        $query  = $this->db->query($query1);
        return $query->result_array();
    }

    public function getVendorsDealBySupplier($supId)
    {
        $query = $this->db->get_where('vendors_deal_header', array('supplier_id' => $supId));
        return $query->result_array();
    }

    public function loadProfInCrf($crfId)
    {
        $query = $this->db->query("SELECT l.crf_id,l.proforma_header_id, 
                                   IF(p.sales_invoice_no IS NULL or p.sales_invoice_no = '', 
                                       IF(p.so_no IS NULL or p.so_no = '', 
                                           IF(p.order_no IS NULL or p.order_no = '',po.po_reference, 
                                           p.order_no), 
                                       p.so_no), 
                                   p.sales_invoice_no) AS so_no, 
                                   IF(p.delivery_date IS NULL OR p.delivery_date = '', '',p.delivery_date) as profDate,
                                   l.po_header_id,po.po_no,po.po_reference
                                   FROM crf_line l
                                   INNER JOIN crf c ON c.crf_id = l.crf_id
                                   LEFT JOIN proforma_header p ON l.proforma_header_id = p.proforma_header_id 
                                   LEFT JOIN po_header po ON l.po_header_id = po.po_header_id
                                   WHERE l.pi_head_id = 0 AND l.crf_id = ".$crfId);
        return $query->result_array();
    }

    public function loadPiInCrf($crfId)
    {
        $query = $this->db->query("SELECT l.crf_id,l.pi_head_id, p.pi_no, p.posting_date, l.po_header_id,po.po_no,po.po_reference
                                   FROM crf_line l
                                   INNER JOIN crf c ON c.crf_id = l.crf_id
                                   LEFT JOIN purchase_invoice_header p ON l.pi_head_id = p.pi_head_id
                                   LEFT JOIN po_header po ON l.po_header_id = po.po_header_id
                                   WHERE l.crf_id = ".$crfId. " AND l.proforma_header_id = 0");
        return $query->result_array();
    }

    public function getCrfStatistics()
    {
        $query = $this->db->query("SELECT COUNT(IF(status = 'MATCHED', 1, NULL)) 'Matched',
                                    COUNT(IF(status = 'PENDING', 1, NULL)) 'Pending',
                                    COUNT(*) AS 'All'
                                   FROM crf" );
        return $query->row();
    }
}
