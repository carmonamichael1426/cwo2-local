<?php
defined('BASEPATH') or exit('No direct script access allowed');

class VendorsDealModel extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function getDataWhere($table, $where)
    {
        $query = $this->db->select('*')
                          ->from($table)
                          ->where($where)
                          ->get();
        return $query->result();
    }

    public function getDataWhereRow($table, $where)
    {
        $query = $this->db->select('*')
                          ->from($table)
                          ->where($where)
                          ->get();
        return $query->row();
    }

    public function getSupplier($id)
    {
        $result = $this->db->SELECT('*')
            ->FROM('suppliers')
            ->WHERE('supplier_id', $id)
            ->GET()
            ->ROW();

        return $result;
    }

    public function getDuplicate($code)
    {
        $result = $this->db->SELECT('*')
            ->FROM('vendors_deal_header')
            ->WHERE('vendor_deal_code', $code)
            ->GET()
            ->ROW();

        return $result;
    }

    public function getItemDeptCode($supId)
    {
        $result = $this->db->query('SELECT i.item_department_code, vl.description FROM cwo.items i 
                                    INNER JOIN vendors_deal_line vl ON vl.number = i.item_department_code 
                                    WHERE i.supplier_id = '.$supId. ' GROUP BY i.item_department_code');
        return $result->result_array();             
    }

    public function vdlinecodedupes($dealId,$code)
    {
        $query = $this->db->select('vendor_deal_line_id')
                          ->from('vendors_deal_line')
                          ->where('vendor_deal_head_id', $dealId)
                          ->where('number', $code)
                          ->get();
        return $query->num_rows();
    }

    public function update($table,$data,$where)
    {
        $query = $this->db->where($where)
                          ->update($table,$data);
        return $this->db->affected_rows();

    }

}
