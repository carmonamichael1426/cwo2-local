<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DeductionReportModel extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    public function getDeductionReport($filter, $deductionType, $supId, $dateFrom, $dateTo)
    {
        if ($filter == 'All Supplier' && $deductionType !== 'All') {
            $query = $this->db->query("SELECT d.*, h.sop_id,h.sop_no, h.date_created AS sop_date, h.supplier_id, s.supplier_name, t.deduction_type_id, t.type 
                                       FROM sop_deduction d 
                                       INNER JOIN sop_head h ON h.sop_id = d.sop_id 
                                       INNER JOIN deduction ded ON ded.deduction_id = d.deduction_id 
                                       INNER JOIN deduction_type t ON t.deduction_type_id = ded.deduction_type_id
                                       LEFT JOIN suppliers s ON s.supplier_id = h.supplier_id
                                       WHERE h.date_created BETWEEN '".$dateFrom."' AND '".$dateTo."' AND  
                                       t.deduction_type_id = " .$deductionType. " ORDER BY s.supplier_name, h.sop_no ASC ");

        } else if ($filter == 'Supplier' && $deductionType !== 'All' ) {
            $query = $this->db->query("SELECT d.*, h.sop_id,h.sop_no, h.date_created AS sop_date, h.supplier_id, s.supplier_name, t.deduction_type_id, t.type 
                                       FROM sop_deduction d
                                       INNER JOIN sop_head h ON h.sop_id = d.sop_id 
                                       INNER JOIN deduction ded ON ded.deduction_id = d.deduction_id 
                                       INNER JOIN deduction_type t ON t.deduction_type_id = ded.deduction_type_id
                                       LEFT JOIN suppliers s ON s.supplier_id = h.supplier_id
                                       WHERE h.date_created BETWEEN '".$dateFrom."' AND '".$dateTo."' AND  
                                       h.supplier_id = " .$supId. "  AND
                                       t.deduction_type_id = " .$deductionType. "  ORDER BY s.supplier_name, h.sop_no ASC ");
          
        } else if ($filter == 'All Supplier' && $deductionType == 'All') {
            $query = $this->db->query("SELECT d.*, h.sop_id,h.sop_no, h.date_created AS sop_date, h.supplier_id, s.supplier_name, t.deduction_type_id, t.type 
                                       FROM sop_deduction d
                                       INNER JOIN sop_head h ON h.sop_id = d.sop_id 
                                       INNER JOIN deduction ded ON ded.deduction_id = d.deduction_id 
                                       INNER JOIN deduction_type t ON t.deduction_type_id = ded.deduction_type_id
                                       LEFT JOIN suppliers s ON s.supplier_id = h.supplier_id
                                       WHERE h.date_created BETWEEN '".$dateFrom."' AND '".$dateTo."' 
                                       ORDER BY s.supplier_name, h.sop_no, d.id ");

        } else if ($filter == 'Supplier' && $deductionType == 'All') {
            $query = $this->db->query("SELECT d.*, h.sop_id,h.sop_no, h.date_created AS sop_date, h.supplier_id, s.supplier_name, t.deduction_type_id, t.type 
                                       FROM sop_deduction d
                                       INNER JOIN sop_head h ON h.sop_id = d.sop_id 
                                       INNER JOIN deduction ded ON ded.deduction_id = d.deduction_id 
                                       INNER JOIN deduction_type t ON t.deduction_type_id = ded.deduction_type_id
                                       LEFT JOIN suppliers s ON s.supplier_id = h.supplier_id
                                       WHERE h.date_created BETWEEN '".$dateFrom."' AND '".$dateTo."' AND 
                                       h.supplier_id = " .$supId. " ORDER BY s.supplier_name, h.sop_no, d.id");
        }
        return $query->result_array();
    }
    
    public function deductionTypeData($typeId)
    {
        $query = $this->db->get_where('deduction_type', array('deduction_type_id' => $typeId));
        return $query->row_array();
    }

    public function countDeductions($sopId,$typeId)
    {
        $query = $this->db->query("SELECT count(t.deduction_type_id) AS dcount
                                   FROM `sop_deduction` `d` 
                                   INNER JOIN `sop_head` `h` ON `h`.`sop_id` = `d`.`sop_id` 
                                   INNER JOIN `deduction` `ded` ON `ded`.`deduction_id` = `d`.`deduction_id` 
                                   INNER JOIN `deduction_type` `t` ON `t`.`deduction_type_id` = `ded`.`deduction_type_id` 
                                   INNER JOIN `suppliers` `s` ON `s`.`supplier_id` = `h`.`supplier_id` 
                                   WHERE `h`.`sop_id` = ".$sopId."
                                   AND `t`.`deduction_type_id` = '".$typeId."' 
                                   GROUP BY `t`.`deduction_type_id` ");
        return $query->row_array();
    }

    public function countSop($supId,$sopId){
        $query = $this->db->query("SELECT count(`d`.`id`) AS scount
                                   FROM `sop_deduction` `d` 
                                   INNER JOIN `sop_head` `h` ON `h`.`sop_id` = `d`.`sop_id` 
                                   INNER JOIN `deduction` `ded` ON `ded`.`deduction_id` = `d`.`deduction_id` 
                                   INNER JOIN `deduction_type` `t` ON `t`.`deduction_type_id` = `ded`.`deduction_type_id` 
                                   INNER JOIN `suppliers` `s` ON `s`.`supplier_id` = `h`.`supplier_id` 
                                   WHERE `s`.`supplier_id` = '".$supId."' 
                                   AND `h`.`sop_id` = ".$sopId);
        return $query->row_array();
    }

    public function countSopByType($supId,$sopId,$typeId,$from,$to){
        $query = $this->db->query("SELECT count(`d`.`id`) AS scount
                                   FROM `sop_deduction` `d` 
                                   INNER JOIN `sop_head` `h` ON `h`.`sop_id` = `d`.`sop_id` 
                                   INNER JOIN `deduction` `ded` ON `ded`.`deduction_id` = `d`.`deduction_id` 
                                   INNER JOIN `deduction_type` `t` ON `t`.`deduction_type_id` = `ded`.`deduction_type_id` 
                                   INNER JOIN `suppliers` `s` ON `s`.`supplier_id` = `h`.`supplier_id` 
                                   WHERE `s`.`supplier_id` = '".$supId."' 
                                   AND `h`.`sop_id` = ".$sopId."
                                   AND `t`.`deduction_type_id` =  ".$typeId."
                                   AND `h`.`date_created` 
                                   BETWEEN '".$from."' 
                                   AND '".$to."' ");
        return $query->row_array();
    }


}
