<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SOP_model extends CI_Model
{ 
  function __construct()
    {
        parent::__construct();

        $this->load->library('session');
    }

    public function getDocNo($prefix,$useNext = false)
    {
        $sequence = getSequenceNo(
            [
                'code'          => $prefix,
                'number'        => '1',
                'lpad'          => '7',
                'pad_string'    => '0',
                'description'   => "SOP Number"
            ],
            [
                'table'     =>  'sop_head',
                'column'    => 'sop_no'
            ],

            $useNext
        );

        return $sequence;
    }

    public function getSuppliers()
    {
        $query =  $this->db->select('supplier_id, supplier_name, acroname,has_deal,inputted_wht')
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

    public function getSupData($select, $where, $target)
    {
        $query = $this->db->select($select)
                          ->get_where('suppliers', array($target => $where));
        return $query->row();
    }

    public function getCusData($select, $where)
    {
        $query = $this->db->select($select)
                          ->get_where('customers', array('customer_code' => $where));
        return $query->row();
    }

    public function getVATData()
    {
        $query = $this->db->get_where('vat', array('status' => 1));
        return $query->row_array();
    }

    public function getData2($table, $where, $target)
    {
        $query = $this->db->get_where($table, array($where => $target));
        return $query->row_array();
    }
    
    public function getReference($crfId)
    {
        $query = $this->db->select('reference_no')->get_where('subsidiary_ledger', array('crf_id' => $crfId));
        return $query->row();
    }

    public function loadSONos($supId)
    {
        $query = $this->db->query("SELECT ph.proforma_header_id,
                                    IF(ph.sales_invoice_no IS NULL or ph.sales_invoice_no = '',
                                        IF(ph.so_no IS NULL or ph.so_no = '',
                                            IF(ph.order_no IS NULL or ph.order_no = '',po.po_reference,
                                            ph.order_no),
                                        ph.so_no),
                                    ph.sales_invoice_no) AS so_no,
                                   ph.delivery_date, po.po_no, po.order_date AS poDate, SUM(pl.amount) AS amount
                                   FROM proforma_header ph 
                                   INNER JOIN proforma_line pl ON pl.proforma_header_id = ph.proforma_header_id 
                                   INNER JOIN po_header po ON po.po_header_id = ph.po_header_id 
                                   WHERE NOT EXISTS (SELECT * FROM sop_invoice inv WHERE inv.proforma_header_id = ph.proforma_header_id )
                                   AND  ph.supplier_id = " . $supId . " AND ph.pricing_status = 'PRICE CHECKED' 
                                   GROUP BY ph.proforma_header_id ORDER BY so_no,po.posting_date DESC ");
        
        return $query->result_array();
    }

    public function getProformaItemAmount($supId)
    {
        $query = $this->db->query('SELECT ph.proforma_header_id, ph.so_no, pl.item_code,pl.description,pl.itemcode_loc,i.item_division,i.item_department_code, i.item_group_code,pl.amount 
                                   FROM proforma_line pl 
                                   INNER JOIN proforma_header ph ON ph.proforma_header_id = pl.proforma_header_id 
                                   LEFT JOIN items i ON i.itemcode_loc = pl.itemcode_loc 
                                   WHERE NOT EXISTS (SELECT * FROM sop_invoice inv WHERE inv.proforma_header_id = ph.proforma_header_id)
                                   AND  ph.supplier_id = ' . $supId . ' AND (pl.free = 0 OR ISNULL(pl.free))  ORDER BY ph.proforma_header_id') ;       
        return $query->result_array();
    } 

    public function getDeals($supId)
    {
        $query = $this->db->get_where('vendors_deal_header', array('supplier_id' => $supId));
        return $query->result_array();
    }

    public function getVendorsDealLine($dealId)
    {
        $query = $this->db->get_where('vendors_deal_line', array('vendor_deal_head_id' => $dealId));
        return $query->result_array();
    }

    public function findItemCodeInDeals($itemCode,$dealId)
    {
        $query = $this->db->get_where('vendors_deal_line', array('vendor_deal_head_id' => $dealId, 'number' => $itemCode ));
        return $query->row_array();
    }
    
    public function loadDeductionType()
    {
        $query = $this->db->select('*')
                          ->from('deduction_type')
                          ->where('status', 1)
                          ->order_by('type','ASC')
                          ->get();
        return $query->result_array();
    }

    public function getDeductionNames($typeId, $supId )
    {
        $query = $this->db->select('*')
                          ->from('deduction')
                          ->group_start()
                                ->where('supplier_id', $supId)
                                ->or_where('supplier_id','All')
                          ->group_end()
                          ->where('deduction_type_id', $typeId)
                          ->where('status', 1)
                          ->get();
        return $query->result_array();
    }

    public function getProformaLine($profId)
    {
        $query = $this->db->select('pl.proforma_line_id,pl.item_code, pl.amount,i.itemcode_loc,i.item_division,i.item_department_code,i.item_group_code')
                          ->from('proforma_line pl')          
                          ->join('proforma_header ph','ph.proforma_header_id = pl.proforma_header_id','inner')                
                          ->join('items i', 'i.itemcode_loc = pl.itemcode_loc', 'left')
                          ->where('pl.proforma_header_id',$profId)
                          ->get(); 
        return $query->result_array();
    }

    public function getQuantity($profId)
    {
        $query = $this->db->select('ph.proforma_header_id, sum(pl.qty) as qty')
                          ->from('proforma_line pl')
                          ->join('proforma_header ph','ph.proforma_header_id = pl.proforma_header_id','inner')
                          ->join('items i', 'i.itemcode_sup = pl.item_code', 'left')
                          ->where('ph.proforma_header_id',$profId)
                          ->group_by('ph.proforma_header_id')
                          ->get();
        return $query->row_array();
    }

    public function getTotalProformaAmountBaseOnDiscount($profId, $discount)
    {
        $query = $this->db->query('SELECT ROUND( SUM((pl.price / i.discount) * pl.qty * 1.12 ),2) AS amount
                                   FROM proforma_header pf 
                                   INNER JOIN proforma_line pl ON pl.proforma_header_id = pf.proforma_header_id
                                   INNER JOIN items i ON i.itemcode_sup = pl.item_code
                                   WHERE i.discount = ' . $discount . ' AND pf.proforma_header_id =' . $profId);
        return $query->row();
    }

    public function getDeductionFormula($deductionId)
    {
        $query = $this->db->select('formula')
                          ->from('deduction')
                          ->where('deduction_id', $deductionId)
                          ->get();
        return $query->row();
    }

    public function loadChargesType()
    {
        $query = $this->db->get('charges_type');
        return $query->result_array();
    }

    public function insertToTable($table, $data)
    {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    public function getData($query1)
    {
        $query  = $this->db->query($query1);
        return $query->row_array();
    }

    public function getHeadData($sopId)
    {
        $query = $this->db->get_where('sop_head', array('sop_id' => $sopId));
        return $query->row_array();
    }

    public function getInvoiceData($sopId)
    {
        $query = $this->db->query('SELECT i.sop_id, pf.proforma_header_id,
                                   IF(pf.sales_invoice_no IS NULL or pf.sales_invoice_no = "",
                                        IF(pf.so_no IS NULL or pf.so_no = "",
                                            IF(pf.order_no IS NULL or pf.order_no = "",po.po_reference,
                                            pf.order_no),
                                        pf.so_no),
                                   pf.sales_invoice_no) AS so_no,
                                   IF(pf.delivery_date IS NULL or pf.delivery_date = "", 
                                        IF(pf.order_date IS NULL or pf.order_date = "", "", 
                                        STR_TO_DATE(pf.order_date,"%Y/%m/%d")),
                                   STR_TO_DATE(pf.delivery_date,"%m/%d/%Y")) AS order_date, 
                                   po.po_no, po.order_date AS po_date, i.invoice_amount , GETFULLNAME(po.user_id) AS invclerk,  GETFULLNAME(pf.pricing_userid) AS pricing
                                   FROM  sop_invoice i 
                                   INNER JOIN proforma_header pf ON pf.proforma_header_id = i.proforma_header_id 
                                   INNER JOIN po_header po ON po.po_header_id = pf.po_header_id
                                   WHERE i.sop_id = ' . $sopId);
        return $query->result_array();
    }

    public function getDeductionData($sopId)
    {
        $query    = $this->db->select('*')
                             ->from('sop_deduction csd')
                             ->join('deduction d', 'd.deduction_id = csd.deduction_id', 'inner')
                             ->join('deduction_type t', 't.deduction_type_id = d.deduction_type_id', 'inner')
                             ->where('sop_id', $sopId)
                             ->order_by('csd.id','ASC')
                             ->get();
        return $query->result_array();
    }

    public function getChargesData($sopId)
    {
        $query = $this->db->get_where('sop_charges', array('sop_id' => $sopId));
        return $query->result_array();
    }

    public function loadCwoSop($supId, $cusId,$from,$to)
    {
        $query = $this->db->query("SELECT `sop`.*, DATE_FORMAT(sop.datetime_created, '%M %d, %Y') AS sop_date, `s`.`supplier_name`, `c`.`customer_name`, 
                                  (CASE WHEN sop.status = 0 THEN 'PENDING' 
                                        WHEN sop.status = 1 THEN 'AUDITED' 
                                        WHEN sop.status = 2 THEN 'CANCELLED' END) as statuss
                                   FROM `sop_head` `sop` 
                                   INNER JOIN `suppliers` `s` ON `s`.`supplier_id` = `sop`.`supplier_id` 
                                   INNER JOIN `customers` `c` ON `c`.`customer_code` = `sop`.`customer_code` 
                                   WHERE date_created BETWEEN  '" .$from."' AND '".$to."' AND `sop`.`supplier_id` = " . $supId . " AND `sop`.`customer_code` = " . $cusId );
                                 
        return $query->result_array();
    }

    public function getTransactionHistory($filter, $supplierid, $locationid)
    {
        $result = array();
        if ($filter == 'All Transactions') :
            $result = $this->db->SELECT('tr.*, s.acroname, c.l_acroname , GROUP_CONCAT(pf.so_no SEPARATOR ",") AS document1')
                ->FROM('sop_transaction tr')
                ->JOIN('sop_head sop', 'sop.sop_no = tr.tr_no', 'LEFT')
                ->JOIN('sop_invoice inv', 'inv.sop_id = sop.sop_id', 'LEFT')
                ->JOIN('proforma_header pf', 'pf.proforma_header_id = inv.proforma_header_id', 'LEFT')
                ->JOIN('customers c', 'c.customer_code = tr.customer_code', 'LEFT')
                ->JOIN('suppliers s', 's.supplier_id = tr.supplier_id', 'LEFT')
                ->GROUP_BY('tr.tr_id')
                ->GET()
                ->RESULT_ARRAY();
        elseif ($filter == 'By Supplier') :
            $result = $this->db->SELECT('tr.*, s.acroname, c.l_acroname , GROUP_CONCAT(pf.so_no SEPARATOR ",") AS document1')
                ->FROM('sop_transaction tr')
                ->JOIN('sop_head sop', 'sop.sop_no = tr.tr_no', 'LEFT')
                ->JOIN('sop_invoice inv', 'inv.sop_id = sop.sop_id', 'LEFT')
                ->JOIN('proforma_header pf', 'pf.proforma_header_id = inv.proforma_header_id', 'LEFT')
                ->JOIN('customers c', 'c.customer_code = tr.customer_code', 'LEFT')
                ->JOIN('suppliers s', 's.supplier_id = tr.supplier_id', 'LEFT')
                ->WHERE('tr.supplier_id = "' . $supplierid . '"')
                ->GROUP_BY('tr.tr_id')
                ->GET()
                ->RESULT_ARRAY();
        elseif ($filter == 'By Location') :
            $result = $this->db->SELECT('tr.*, s.acroname, c.l_acroname, GROUP_CONCAT(pf.so_no SEPARATOR ",") AS document1')
                ->FROM('sop_transaction tr')
                ->JOIN('sop_head sop', 'sop.sop_no = tr.tr_no', 'LEFT')
                ->JOIN('sop_invoice inv', 'inv.sop_id = sop.sop_id', 'LEFT')
                ->JOIN('proforma_header pf', 'pf.proforma_header_id = inv.proforma_header_id', 'LEFT')
                ->JOIN('customers c', 'c.customer_code = tr.customer_code', 'LEFT')
                ->JOIN('suppliers s', 's.supplier_id = tr.supplier_id', 'LEFT')
                ->WHERE('tr.customer_code = "' . $locationid . '"')
                ->GROUP_BY('tr.tr_id')
                ->GET()
                ->RESULT_ARRAY();
        elseif ($filter == 'By Supplier and Location') :
            $result = $this->db->SELECT('tr.*, s.acroname, c.l_acroname, GROUP_CONCAT(pf.so_no SEPARATOR ",") AS document1')
                ->FROM('sop_transaction tr')
                ->JOIN('sop_head sop', 'sop.sop_no = tr.tr_no', 'LEFT')
                ->JOIN('sop_invoice inv', 'inv.sop_id = sop.sop_id', 'LEFT')
                ->JOIN('proforma_header pf', 'pf.proforma_header_id = inv.proforma_header_id', 'LEFT')
                ->JOIN('customers c', 'c.customer_code = tr.customer_code', 'LEFT')
                ->JOIN('suppliers s', 's.supplier_id = tr.supplier_id', 'LEFT')
                ->WHERE('tr.supplier_id = "' . $supplierid . '"')
                ->WHERE('tr.customer_code = "' . $locationid . '"')
                ->GROUP_BY('tr.tr_id')
                ->GET()
                ->RESULT_ARRAY();
        endif;

        return $result;
    }

    public function tagAsAudited($sopId)
    {
        $query = $this->db->set('status', 1)
                          ->where('sop_id', $sopId)
                          ->update('sop_head');
        return $query;
    }

    public function getUnMentionSOPInv($supId,$string)
    {
        $query = $this->db->query('SELECT inv.id,inv.sop_id, h.sop_no,inv.proforma_header_id, pf.so_no  FROM sop_invoice inv 
                                   INNER JOIN sop_head h ON h.sop_id = inv.sop_id 
                                   INNER JOIN proforma_header pf ON pf.proforma_header_id = inv.proforma_header_id
                                   WHERE NOT EXISTS (SELECT * FROM sop_deduction d WHERE d.sop_invoice_id = inv.id)
                                   AND h.supplier_id = '.$supId.' AND (h.sop_no LIKE "%'.$string.'%" OR pf.so_no LIKE "%'.$string.'%") ');
        return $query->result_array();
    }

    public function getDeductionOrder($typeId)
    {
        $query = $this->db->select('order')
                          ->get_where('deduction_type', array('deduction_type_id' => $typeId));
        return $query->row();
    }

    public function searchCRFVar($string,$supId)
    {
        $query = $this->db->query('SELECT `v`.*, `c`.`crf_no`, SUM(l.debit) - SUM(l.credit) as balance
                                    FROM `variance` `v` 
                                    INNER JOIN `crf` `c` ON `c`.`crf_id` = `v`.`crf_id` 
                                    INNER JOIN `variance_ledger` `l` ON `l`.`variance_id` = `v`.`variance_id` 
                                    WHERE `c`.`crf_no` LIKE "%' .$string. '%" AND `c`.`supplier_id` = ' .$supId. ' GROUP BY `v`.`variance_id` HAVING `balance` <> 0 ');
        return $query->result_array();
    }

    public function getSops($type,$supId,$locationId)
    {
        if($type == "All Transactions"){
            $query = $this->db->query(" SELECT h.*, s.supplier_name, c.customer_name 
                                        FROM cwo.sop_head h 
                                        INNER JOIN suppliers s ON s.supplier_id = h.supplier_id 
                                        INNER JOIN customers c ON c.customer_code = h.customer_code
                                        ORDER BY h.sop_no");

        } else if ($type == "By Supplier") {
            $query = $this->db->query("SELECT h.*, s.supplier_name, c.customer_name 
                                       FROM cwo.sop_head h 
                                       INNER JOIN suppliers s ON s.supplier_id = h.supplier_id  
                                       INNER JOIN customers c ON c.customer_code = h.customer_code
                                       WHERE h.supplier_id = ".$supId. 
                                       " ORDER BY h.sop_no");

        } else if ($type == "By Location") {
            $query = $this->db->query("SELECT h.*, s.supplier_name, c.customer_name
                                       FROM cwo.sop_head h 
                                       INNER JOIN suppliers s ON s.supplier_id = h.supplier_id 
                                       INNER JOIN customers c ON c.customer_code = h.customer_code 
                                       WHERE h.customer_code = ".$locationId. 
                                       " ORDER BY h.sop_no ");

        } else if ($type == "By Supplier and Location") {
            $query = $this->db->query("SELECT h.*, s.supplier_name, c.customer_name
                                       FROM cwo.sop_head h 
                                       INNER JOIN suppliers s ON s.supplier_id = h.supplier_id 
                                       INNER JOIN customers c ON c.customer_code = h.customer_code 
                                       WHERE h.supplier_id = ".$supId. 
                                       " AND h.customer_code = ".$locationId.
                                       " ORDER BY h.sop_no");
        }

        return $query->result_array();
    }

    public function updateToTable($table,$data, $field, $id)
    {
        $this->db->update($table,$data, array($field => $id));
        return $this->db->affected_rows();
    }

    public function getInvoices($sopId)
    {
        $query = $this->db->select('id,proforma_header_id')
                          ->from('sop_invoice')
                          ->where('sop_id', $sopId)
                          ->get();
        return $query->result_array();
    }

    public function getSopStatistics()
    {        
        $query = $this->db->query("SELECT COUNT(IF(status = 2, 1, NULL)) 'Cancelled',
                                          COUNT(IF(status = 0, 1, NULL)) 'Pending',
                                          COUNT(IF(status = 1, 1, NULL)) 'Audited',
                                          COUNT(*) AS 'All'
                                   FROM sop_head" );
        return $query->row();
    }


}
