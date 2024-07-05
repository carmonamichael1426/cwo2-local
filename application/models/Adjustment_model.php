<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Adjustment_model extends CI_Model
{


    function __construct()
    {
        parent::__construct();

    }

    public function getDocNo($prefix,$useNext = false)
    {
        $sequence = getSequenceNo(
            [
                'code'          => $prefix,
                'number'        => '1',
                'lpad'          => '7',
                'pad_string'    => '0',
                'description'   => "Variance Adjustment"
            ],
            [
                'table'     =>  'variance_adjustment',
                'column'    => 'ADJ_NO'
            ],

            $useNext
        );

        return $sequence;
    }

    public function searchCRFVar($string, $supId)
    {
        $query = $this->db->query('SELECT `v`.*, `c`.`crf_no`, `c`.`crf_date`, SUM(l.debit) - SUM(l.credit) as balance
                                    FROM `variance` `v` 
                                    INNER JOIN `crf` `c` ON `c`.`crf_id` = `v`.`crf_id` 
                                    INNER JOIN `variance_ledger` `l` ON `l`.`variance_id` = `v`.`variance_id` 
                                    WHERE `c`.`crf_no` LIKE "%' .$string. '%" AND `c`.`supplier_id` = ' .$supId. ' GROUP BY `v`.`variance_id` HAVING `balance` <> 0 ');
        return $query->result_array();
    }

    public function insertToTable($table, $data)
    {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    public function getDataRowArray($table, $where, $target)
    {
        $query = $this->db->get_where($table, array($where => $target));
        return $query->row_array();
    }

    public function updateToTable($table,$data, $field, $id)
    {
        $this->db->update($table,$data, array($field => $id));
        return $this->db->affected_rows();
    }

    public function getAdjustments($supId, $from, $to)
    {
        $query = $this->db->select('a.*, c.crf_no, c.crf_date')
                          ->from('variance_adjustment a')
                          ->join('crf c', 'c.crf_id = a.crf_id', 'inner')
                          ->where('a.supplier_id', $supId)  
                          ->where('a.adj_date >=', $from)  
                          ->where('a.adj_date <=', $to)                    
                          ->order_by('a.adj_date', 'DESC')
                          ->get();  
        return $query->result();
    }
   
}