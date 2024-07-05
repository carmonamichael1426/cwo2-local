<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Povsproforma_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    public function getDocNo($useNext = false)
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
                'table'     =>  'povprof_transaction',
                'column'    => 'tr_no'
            ],

            $useNext
        );

        return $sequence;
    }

    public function transactionCode($useNext = false)
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
                'table'     =>  'transaction1',
                'column'    => 'transaction_no'
            ],

            $useNext
        );

        return $sequence;
    }

    public function getDataSupplier()
    {
        $result = $this->db->SELECT('*')
            ->FROM('suppliers')
            ->ORDER_BY('supplier_name')
            ->GET()
            ->result();
        return $result;
    }

    public function getDataCustomer()
    {
        $result = $this->db->SELECT('*')
            ->FROM('customers')
            ->ORDER_BY('customer_name')
            ->GET()
            ->result();
        return $result;
    }

    public function getData($table)
    {
        $result = $this->db->SELECT('*')
            ->FROM($table)
            ->GET()
            ->result();
        return $result;
    }

    public function getData2($id, $id_target, $table)
    {
        $result = $this->db->SELECT('*')
            ->FROM($table)
            ->WHERE($id_target, $id)
            ->GET()
            ->ROW();
        return $result;
    }

    public function getData3($id, $id_target, $table)
    {
        $result = $this->db->SELECT('*')
            ->FROM($table)
            ->WHERE($id_target, $id)
            ->GET()
            ->result_array();
        return $result;
    }

    public function getDataLike($id){
        $result = $this->db->query("SELECT 
                                        distinct po.po_no, 
                                                po.po_header_id 
                                        FROM po_header po
                                        LEFT JOIN proforma_header pf 
                                        ON po.po_header_id = pf.po_header_id
                                        WHERE po.po_header_id IN (SELECT po_header_id FROM proforma_header) 
                                        AND po.po_no LIKE '%". $id ."%'LIMIT 10")->result_array();
        return $result;
    }

    public function getData4($id, $id_target, $table)
    {
        $result = $this->db->SELECT('*')
            ->FROM($table)
            ->WHERE($id_target, $id)
            ->GET()
            ->ROW_ARRAY();
        return $result;
    }

    public function getItems1($id, $headerid)
    {

        $result = $this->db->SELECT('pl.*')
            ->FROM('proforma_line pl')
            ->JOIN('proforma_header ph', 'pl.proforma_header_id = ph.proforma_header_id', 'LEFT')
            ->WHERE('pl.item_code', $id)
            ->WHERE('ph.po_header_id', $headerid)
            ->GET()
            ->ROW_ARRAY();

        // echo $this->db->last_query();
        return $result;
    }

    public function getPOHeader($type, $supplierid, $customerCode, $po)
    {
        if ($type == 'first') {
            $result = $this->db->query('SELECT po_header_id, po_no, po_reference FROM po_header WHERE supplier_id = "' . $supplierid . '" AND customer_code = "' . $customerCode . '" AND po_header_id = "' . $po . '"');
            $ret = $result->row();
            return $ret;
        } else {

            $result = $this->db->query("SELECT 
                                        po.* 
                                        FROM 
                                        po_header po 
                                        LEFT JOIN  report_status  r 
                                        ON  r.po_header_id = po.po_header_id 
                                        WHERE po.supplier_id = '" . $supplierid . "' 
                                        AND po.customer_code = '" . $customerCode . "' 
                                        AND (po.status = 'PENDING') 
                                        AND po.po_header_id NOT IN (SELECT po_header_id FROM report_status)");
            return $result->result_array();
        }
    }

    public function getAcroname($supplier_code)
    {
        $result = $this->db->SELECT('acroname')
            ->FROM('suppliers')
            ->WHERE('supplier_id', $supplier_code)
            ->GET()
            ->ROW();
        return $result;
    }

    public function getPendingMatches($supplier_id, $location_code, $from, $to)
    {
        $status = array('0', '3');
        $result = array();

        // $result = $this->db->SELECT('*, IF(pr.delivery_date IS NULL or pr.delivery_date = "", pr.date_uploaded, STR_TO_DATE(pr.delivery_date,"%m/%d/%Y")) as profDate')
        //     ->FROM('report_status r')
        //     ->JOIN('suppliers s', 'r.supplier_id = s.supplier_id', 'LEFT')
        //     ->JOIN('customers c', 'r.customer_code = c.customer_code', 'LEFT')
        //     ->JOIN('po_header p', 'r.po_header_id = p.po_header_id', 'LEFT')
        //     ->JOIN('proforma_header pr', 'r.proforma_header_id = pr.proforma_header_id', 'LEFT')
        //     ->WHERE_IN('proforma_stat', $status)
        //     ->WHERE('r.supplier_id', $supplier_id)
        //     ->WHERE('r.customer_code', $location_code)
        //     ->GET()
        //     ->RESULT();

       
        $result = $this->db->query("SELECT * FROM 
                                        (SELECT pr.*, r.rep_stat_id, r.proforma_stat, s.supplier_name, s.supplier_code, c.l_acroname AS acroname, c.customer_name,p.posting_date, p.po_no, p.po_reference,
                                        IF(pr.delivery_date IS NULL or pr.delivery_date = '', `pr`.`date_uploaded`, STR_TO_DATE(pr.delivery_date, '%m/%d/%Y')) AS profdate
                                        FROM `report_status` `r` 
                                        LEFT JOIN `suppliers` `s` ON `r`.`supplier_id` = `s`.`supplier_id` 
                                        LEFT JOIN `customers` `c` ON `r`.`customer_code` = `c`.`customer_code` 
                                        LEFT JOIN `po_header` `p` ON `r`.`po_header_id` = `p`.`po_header_id` 
                                        LEFT JOIN `proforma_header` `pr` ON `r`.`proforma_header_id` = `pr`.`proforma_header_id` ) AS t1 
                                    WHERE t1.`proforma_stat` IN('0','3') 
                                    AND profdate BETWEEN '".$from."' AND '".$to."' 
                                    AND `t1`.`supplier_id` = ".$supplier_id." 
                                    AND `t1`.`customer_code` = '".$location_code. "'" );

        return $result->result_array();
    }

    public function matchPOHeaders($po_header_id)
    {
        $result = $this->db->SELECT('*')
            ->FROM('po_header')
            ->WHERE('po_header_id', $po_header_id)
            ->GET()
            ->ROW_ARRAY();
        return $result;
    }

    public function matchProfHeaders($proforma_header_id)
    {
        $result = $this->db->SELECT('*')
            ->FROM('proforma_header')
            ->WHERE('proforma_header_id', $proforma_header_id)
            ->GET()
            ->ROW_ARRAY();
        return $result;
    }

    public function getPoLine($po_ref)
    {
        $result = $this->db->SELECT('*')
            ->FROM('po_line')
            ->WHERE('po_reference', $po_ref)
            ->GET()
            ->RESULT_ARRAY();
        return $result;
    }

    public function getProformaLine($proforma_header_id)
    {
        $result = $this->db->SELECT('m.*, s.acroname, po.po_no, po.po_reference, pr.so_no, pr.proforma_code')
            ->FROM('proforma_line m')
            ->JOIN('proforma_header pr', 'pr.proforma_header_id = m.proforma_header_id', 'LEFT')
            ->JOIN('po_header po', 'po.po_header_id = pr.po_header_id', 'LEFT')
            ->JOIN('suppliers s', 's.supplier_id = pr.supplier_id', 'LEFT')
            ->WHERE('m.proforma_header_id', $proforma_header_id)
            ->GET()
            ->RESULT_ARRAY();
        return $result;
    }

    public function checkItemCodesPO($item_codes)
    {
        $result = $this->db->query('SELECT itemcode_loc FROM items WHERE itemcode_loc = "' . $item_codes . '"');
        $ret = $result->row();
        return $ret;
    }

    public function getItemCodeSupplier($itemcode_sup)
    {
        $result = $this->db->SELECT('itemcode_loc, itemcode_sup')
            ->FROM('items')
            ->WHERE('itemcode_sup', $itemcode_sup)
            ->GET()
            ->ROW();
        return $result;
    }

    public function getItemCodes()
    {
        $result = $this->db->SELECT('*')
            ->FROM('items')
            ->GET()
            ->RESULT_ARRAY();
        return $result;
    }

    public function update($id, $id_target, $table, $data)
    {
        $result = $this->db->where($id_target, $id)
            ->update($table, $data);

        return $result;
    }

    public function transactionData($itemcodePO, $itemcodePR, $proformaHeaderID)
    {
        $result = $this->db->query("SELECT 
                                        po.item_code AS po_itemcode,
                                        po.qty AS po_qty,
                                        po.direct_unit_cost AS po_price,
                                        IFNULL(po.qty * po.direct_unit_cost, 0) AS po_amount,
                                        pf.item_code AS pf_itemcode,
                                        pf.description AS pf_description,
                                        pf.qty AS pf_qty,
                                        pf.price AS pf_price,
                                        pf.amount AS pf_amount,
                                        IFNULL(IFNULL(po.qty * po.direct_unit_cost, 0) - pf.amount, 0) as pf_v_amt
                                    FROM
                                        (SELECT * FROM po_line WHERE item_code = '" . $itemcodePO . "') po
                                    LEFT JOIN 
                                        (SELECT mm.item_code, mm.description, mm.qty, mm.price, mm.amount, m.proforma_header_id, m.po_header_id 
                                        FROM proforma_line mm
                                        LEFT JOIN (SELECT * FROM proforma_header) m
                                        ON mm.proforma_header_id = m.proforma_header_id
                                        WHERE item_code = '" . $itemcodePR . "') pf
                                    ON pf.po_header_id = po.po_header_id WHERE pf.po_header_id = '" . $proformaHeaderID . "'")->ROW();

        // echo $this->db->last_query();
        return $result;
    }

    public function delete($table, $target, $find)
    {
        $result = $this->db->WHERE($target, $find)
            ->DELETE($table);
        return $result;
    }

    public function getItems($po_header_id, $proforma_header_id)
    {
        // $result = $this->db->query("SELECT 
        //                             i.id,
        //                             po.item_code as po_item, 
        //                             i.itemcode_loc,
        //                             i.description as po_desc,
        //                             pr.description as prof_desc, 
        //                             i.itemcode_sup,
        //                             pr.item_code as pr_item
        //                         FROM items as i
        //                         LEFT JOIN (SELECT 
        //                                         pl.* 
        //                                     FROM po_line pl 
        //                                     LEFT JOIN items ii 
        //                                     ON pl.item_code = ii.itemcode_loc 
        //                                     WHERE pl.po_header_id = '" . $po_header_id . "' 
        //                                     GROUP BY pl.item_code) po
        //                         ON i.itemcode_loc = po.item_code
        //                         LEFT OUTER JOIN (SELECT * 
        //                                         FROM proforma_line 
        //                                         WHERE proforma_header_id = '" . $proforma_header_id . "') pr
        //                         ON i.itemcode_sup = pr.item_code
        //                         WHERE (po.item_code is NOT NULL OR pr.item_code is NOT NULL)
        //                         GROUP BY po.item_code ORDER BY i.id");

        $result = $this->db->query("SELECT 
                                    i.id,
                                    po.item_code as po_item, 
                                    i.itemcode_loc,
                                    i.description as po_desc,
                                    pr.description as prof_desc, 
                                    i.itemcode_sup,
                                    pr.item_code as pr_item
                                    FROM items as i
                                    LEFT JOIN (SELECT 
                                                pl.* 
                                                FROM po_line pl 
                                                LEFT JOIN items ii 
                                                ON pl.item_code = ii.itemcode_loc 
                                                WHERE pl.po_header_id = '" . $po_header_id . "') po
                                    ON i.itemcode_loc = po.item_code
                                    LEFT OUTER JOIN (SELECT pl.* 
                                                    FROM proforma_line pl 
                                                    LEFT JOIN proforma_header ph 
                                                    ON pl.proforma_header_id = ph.proforma_header_id 
                                                    WHERE ph.po_header_id = '" . $po_header_id . "' ) pr
                                    ON i.itemcode_sup = pr.item_code
                                    WHERE (po.item_code is NOT NULL OR pr.item_code is NOT NULL)
                                    GROUP BY po.item_code ORDER BY i.id");

        return $result->result_array();
    }

    public function reArray($data)
    {
        $items = array();

        if (isset($data)) {
            foreach ($data as $value) {
                $items[] = $value;
            }
        }

        return $items;
    }

    public function checkDuplicate($proforma)
    {
        $result = $this->db->SELECT('*')
            ->FROM('proforma_header')
            ->WHERE('proforma_code', $proforma)
            ->GET()
            ->ROW();
        return $result;
    }

    public function getHistory($id)
    {
        $result = $this->db->SELECT('p.*, u1.username as editted_by, u2.username as approved')
            ->FROM('proforma_line_history p')
            ->JOIN('(SELECT * FROM users) u1', 'u1.user_id = p.user_id', 'LEFT')
            ->JOIN('(SELECT * FROM users) u2', 'u2.user_id = p.approved_by', 'LEFT')
            ->WHERE('p.proforma_header_id', $id)
            ->GET()
            ->RESULT_ARRAY();
        return $result;
    }

    public function getAdditionalsAndDiscounts($proforma_code)
    {
        $result = $this->db->query("SELECT d.*, ph.proforma_code 
                                    FROM discountvat d 
                                    LEFT JOIN proforma_header ph 
                                    ON d.proforma_header_id = ph.proforma_header_id 
                                    WHERE ph.proforma_code = '$proforma_code'");

        return $result->result_array();
    }

    public function getProformaUploadedToPO($po_header_id)
    {
        $result = $this->db->query("SELECT * FROM proforma_header WHERE po_header_id = '$po_header_id'");
        return $result->result_array();
    }

    //===== CREATE PROFORMA =====//

    public function searchPo($string,$supId,$cusId)
    {
        $query = $this->db->select('*')
                          ->from('po_header')
                          ->where('supplier_id', $supId)
                          ->where('customer_code', $cusId)
                          ->group_start()
                            ->like('po_no', $string)
                            ->or_like('po_reference', $string)
                          ->group_end()
                          ->order_by('posting_date','ASC')
                          ->limit(10)
                          ->get();  
        return $query->result_array();
    }

    public function checkDuplicateProf($supId,$proforma)
    {
        $query = $this->db->select('*')
                          ->from('proforma_header')                         
                          ->group_start()
                            ->where('proforma_code', $proforma)
                            ->or_where('sales_invoice_no', $proforma)
                          ->group_end()
                          ->where('supplier_id', $supId)
                          ->get();
        return $query->num_rows();
        
    }
    //===== CREATE PROFORMA =====//

    public function checkItemInSetup($itemcodeLoc,$supplier_id)
    {    
        $result = $this->db->query('SELECT itemcode_loc FROM items WHERE supplier_id = "'.$supplier_id. '" AND  itemcode_loc = "' . $itemcodeLoc . '"');
        $ret = $result->row();
        return $ret;
       
        
    }

    public function getProfPriceCheckedStatistics()
    {
        $query = $this->db->query("SELECT COUNT(IF(pricing_status = 'PRICE CHECKED', 1, NULL)) 'Checked',
                                          COUNT(IF(pricing_status IS NULL OR pricing_status = '' , 1, NULL)) 'Pending',
                                          COUNT(*) AS 'All'
                                   FROM proforma_header" );
        return $query->row();
    }
}
