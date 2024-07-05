<?php
defined('BASEPATH') or exit('No direct script access allowed');

class itemCode_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function getData($supplierid)
    {
        $result = $this->db->SELECT('*')
            ->FROM('suppliers')
            ->WHERE('supplier_id', $supplierid)
            ->GET()
            ->ROW();
        return $result;
    }

    public function getItemData($itemcodedesc, $supplier_id, $customer_code)
    {
        $result = $this->db->SELECT('*')
            ->FROM('items')
            ->WHERE('description', $itemcodedesc)
            ->WHERE('supplier_id', $supplier_id)
            ->WHERE('customer_code', $customer_code)
            ->GET()
            ->ROW();

        return $result;
    }

    public function getItemDataDuplicate($itemcodesup, $supplier_id, $customer_code)
    {
        $result = $this->db->SELECT('*')
            ->FROM('items')
            ->WHERE('itemcode_sup', $itemcodesup)
            ->WHERE('supplier_id', $supplier_id)
            ->WHERE('customer_code', $customer_code)
            ->GET()
            ->ROW();
        return $result;
    }


    public function getNoMapItems($supplierid, $customerCode)
    {
        $result = $this->db->SELECT('*')
            ->FROM('items')
            ->WHERE('supplier_id', $supplierid)
            ->WHERE('customer_code', $customerCode)
            ->WHERE('itemcode_sup = "NO SET UP"')
            ->ORDER_BY('description')
            ->GET()
            ->RESULT_ARRAY();

        return $result;
    }

    public function getItems($supplierid, $customerid)
    {
        $result = $this->db->SELECT('*')
            ->FROM('items')
            ->WHERE('supplier_id', $supplierid)
            ->WHERE('customer_code', $customerid)
            ->ORDER_BY('id', 'ASC')
            ->GET()
            ->RESULT_ARRAY();
        return $result;
    }

    public function checkDuplicate($acroname, $itemsupplier, $itemlocation, $supplier, $location)
    {
        $result = $this->db->SELECT('*')
            ->FROM('items')
            ->WHERE('itemcode_sup', $itemsupplier)
            ->WHERE('itemcode_loc', $itemlocation)
            ->WHERE('supplier_id', $supplier)
            ->WHERE('customer_code', $location)
            ->GET()
            ->ROW();
        return $result;
    }

    public function checkDuplicateUpload($itemcus, $supplier, $location)
    {
        $result = $this->db->SELECT('*')
            ->FROM('items')
            ->WHERE('itemcode_loc', $itemcus)
            ->WHERE('supplier_id', $supplier)
            ->WHERE('customer_code', $location)
            ->GET()
            ->ROW();
        return $result;
    }

    public function checkDuplicateUploadUpdate($description, $supplier, $location)
    {
        $result = $this->db->SELECT('*')
            ->FROM('items')
            ->WHERE('description', $description)
            ->WHERE('supplier_id', $supplier)
            ->WHERE('customer_code', $location)
            ->GET()
            ->ROW();
        return $result;
    }

    public function checkDuplicateUpdate($data)
    {
        $result = $this->db->SELECT('*')
            ->FROM('items')
            ->WHERE($data)
            ->GET()
            ->ROW();

        return $result;
    }

    public function checkDocument($filename)
    {
        $result = $this->db->SELECT('*')
            ->FROM('uploaded_documents')
            ->WHERE('document_name', $filename)
            ->GET()
            ->ROW();

        // echo $this->db->last_query();
        return $result;
    }
}
