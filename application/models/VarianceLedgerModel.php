<?php
defined('BASEPATH') or exit('No direct script access allowed');

class VarianceLedgerModel extends CI_Model
{


    function __construct()
    {
        parent::__construct();

        $this->load->library('session');
    }


    public function getVarianceLedger($supId)
    {
       $query = $this->db->query('SELECT * FROM
                                    (SELECT v.variance_id, v.supplier_id, v.crf_id, c.crf_no, c.crf_date, l.debit, l.credit, debit_orig,
                                        CAST((@balance := @balance + l.debit - l.credit) AS decimal(16, 2)) AS balance
                                        FROM variance v
                                        JOIN (SELECT @balance := 0) AS tmp
                                        INNER JOIN variance_ledger l ON l.variance_id = v.variance_id
                                        INNER JOIN crf c ON c.crf_id = v.crf_id
                                        WHERE v.supplier_id = '.$supId.') s
                                    ORDER BY crf_date');
        return $query->result();
    }

    public function getDataRowArray($table,$id,$where)
    {
        $query = $this->db->get_where($table, array($where => $id));
        return $query->row_array();
    }

    public function getMentions($varianceId)
    {
        $query = $this->db->select('d.*, h.sop_no, h.date_created')
                          ->from('sop_deduction d')
                          ->join('sop_head h', 'h.sop_id = d.sop_id', 'inner')
                          ->where('d.variance_id', $varianceId)
                          ->get();
        return $query->result();
    }

    public function getAdjustments($varianceId)
    {
        $query = $this->db->get_where('variance_adjustment', array('variance_id' => $varianceId));
        return $query->result();
    }
}