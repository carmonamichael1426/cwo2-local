<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UploadedTransactionModel extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

   
    public function getUploadedTransactionsDocs($type,$supId,$cusId)
    {
        if($type == "All Transactions"){
            $query = $this->db->query("SELECT u.*, s.supplier_name, c.customer_name 
                                       FROM uploaded_transaction_documents u 
                                       INNER JOIN suppliers s ON s.supplier_id = u.supplier_id 
                                       INNER JOIN customers c ON c.customer_code = u.customer_code 
                                       ORDER BY u.document_type ");

        } else if ($type == "By Supplier") {
            $query = $this->db->query("SELECT u.*, s.supplier_name, c.customer_name 
                                       FROM uploaded_transaction_documents u 
                                       INNER JOIN suppliers s ON s.supplier_id = u.supplier_id 
                                       INNER JOIN customers c ON c.customer_code = u.customer_code 
                                       WHERE u.supplier_id = ".$supId. 
                                       " ORDER BY u.document_type");

        } else if ($type == "By Location") {
            $query = $this->db->query("SELECT u.*, s.supplier_name, c.customer_name 
                                       FROM uploaded_transaction_documents u 
                                       INNER JOIN suppliers s ON s.supplier_id = u.supplier_id 
                                       INNER JOIN customers c ON c.customer_code = u.customer_code 
                                       WHERE u.customer_code = ".$cusId. 
                                       " ORDER BY u.document_type ");

        } else if ($type == "By Supplier and Location") {
            $query = $this->db->query("SELECT u.*, s.supplier_name, c.customer_name 
                                       FROM uploaded_transaction_documents u 
                                       INNER JOIN suppliers s ON s.supplier_id = u.supplier_id 
                                       INNER JOIN customers c ON c.customer_code = u.customer_code 
                                       WHERE u.supplier_id = ".$supId. 
                                       " AND u.customer_code = ".$cusId.
                                       " ORDER BY u.document_type");
        }

        return $query->result_array();
    }

}
