<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Po_model extends CI_Model
{


    function __construct()
    {
        parent::__construct();

        $this->load->library('session');
    }

    public function getSupplierData($value, $select, $where)
    {
        $query = $this->db->select($select)
                          ->get_where('suppliers', array($where => $value));
        return $query->row();
    }

    public function getCustomerPoExtension($cusId)
    {
        $query = $this->db->get_where('customer_po_extension', array('customer_code' => $cusId));
        return $query->result_array();
    }

    public function loadSupplier()
    {
        $query =  $this->db->select('supplier_id, supplier_name, acroname')
                           ->where('status', 1)
                           ->order_by('supplier_name', 'ASC')
                           ->get('suppliers');
        return $query->result_array();
    }

    public function loadCustomer()
    {
        $query =  $this->db->select('customer_code, customer_name')
                           ->where('status', 1)
                           ->order_by('customer_name', 'ASC')
                           ->get('customers');
        return $query->result_array();
    }

    public function loadPo($supId, $cusId, $from, $to)
    {
        $query = $this->db->select('po.po_header_id,po.po_no as poNo, po.po_reference as ref, po.order_date as orderDate, po.posting_date as postingDate, po.customer_code as poCusId, po.status as status, s.supplier_id, s.supplier_name as supName, c.customer_name as cusName')
                          ->from('po_header po')
                          ->join('suppliers s', 'po.supplier_id   = s.supplier_id', 'inner')
                          ->join('customers c', 'po.customer_code = c.customer_code', 'inner')
                          ->where('po.supplier_id', $supId)
                          ->where('po.customer_code', $cusId)
                          ->where('po.order_date >=', $from)
                          ->where('po.order_date <=', $to)
                          ->order_by('po.order_date','DESC')
                          ->get();
        return $query->result_array();
    }

    public function uploadHeader($poNo, $orderDate, $postingDate, $reference, $supId, $cusId)
    {
        $query = $this->db->get_where('po_header', array('po_no' => $poNo, 'po_reference' => $reference));

        if ($query->num_rows() > 0) {
            die("duplicate");
        } else {
            $header = array('po_no'         => $poNo,
                            'order_date'    => date("Y-m-d", strtotime($orderDate)),
                            'posting_date'  => date("Y-m-d", strtotime($postingDate)),
                            'po_reference'  => $reference,
                            'supplier_id'   => $supId,
                            'customer_code' => $cusId,
                            'user_id'       => $this->session->userdata('user_id'),
                            'date_uploaded' => date("Y-m-d H:i:s"),
                            'status'        => "PENDING" );
            $this->db->insert('po_header', $header);
            return $this->db->insert_id();
        }
    }

    public function uploadLine($barcode, $itemCode, $qty, $unitCost, $uom, $poHeaderId)
    {
        $line   = array('barcode'           => $barcode,
                        'item_code'         => $itemCode,
                        'qty'               => $qty,
                        'direct_unit_cost'  => $unitCost,
                        'uom'               => $uom,
                        'po_header_id'      => $poHeaderId);
        return $this->db->insert('po_line', $line);
    }

    public function poDetails($poId,$supId)
    {
        $query = $this->db->select('l.po_header_id, l.item_code, i.description, l.qty, l.uom, l.direct_unit_cost')
                          ->from('po_line l')
                          ->join('items i', 'i.itemcode_loc = l.item_code', 'left')
                          ->where('l.po_header_id', $poId)
                          ->where('i.supplier_id', $supId)
                          ->get();
        return $query->result_array();
    }

    public function checkItem($itemCode)
    {
        $query = $this->db->get_where('items', array('itemcode_loc' => $itemCode));
        return $query->result_array();
    }

    public function getItems($supId)
    {
        $query = $this->db->select('itemcode_loc,description')
                          ->from('items')
                          ->where('supplier_id', $supId)
                          ->get();
        return $query->result();
    }

    public function insertData($table,$data)
    {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    public function checkPono($pono)
    {
        $query = $this->db->get_where('po_header', array('po_no' => $pono));
        return $query->num_rows();
    }
}
