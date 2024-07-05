<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cwoslipmodel extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    public function getPOHeader($supplierid, $customerid)
    {
        $result = $this->db->query("SELECT po.* 
                                    FROM po_header po 
                                    WHERE po.po_header_id 
                                    NOT IN (SELECT po_header_id FROM cwo_slip) 
                                    AND po.supplier_id = '$supplierid' 
                                    AND po.customer_code = '$customerid'");

        // echo $this->db->last_query();
        return $result->result_array();
    }
}
