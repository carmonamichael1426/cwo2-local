<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Masterfile_model extends CI_Model {


    function __construct() {
        parent::__construct();
    }



    public function addCustomer($customer, $acroname) {
        $insert = array(
            'customer_name' => strtoupper($customer),
            'l_acroname' => $acroname,
            'status' => 1
        );
        return $this->db->insert('customers', $insert);
    }

    public function updateCustomer($code, $name, $acroname) {
        $this->db->set('customer_name', strtoupper($name));
        $this->db->set('l_acroname', $acroname);
        $this->db->where('customer_code', $code);
        return $this->db->update('customers');
    }

    public function deactivateCustomer($code) {
        $this->db->set('status', 0);
        $this->db->where('customer_code', $code);
        return $this->db->update('customers');
    }

    public function getTableData($table) {
        $result = $this->db->SELECT('*')
            ->FROM($table)
            ->GET()
            ->result();
        return $result;
    }

    public function getData($id) {
        $result = $this->db->SELECT('*')
            ->FROM('suppliers')
            ->WHERE('supplier_id', $id)
            ->GET()
            ->ROW();
        return $result;
    }

    public function renameProformaHeader($acroname, $newAcroname) {
        $result = $this->db->QUERY("ALTER TABLE ".$acroname."_proforma_header RENAME TO ".$newAcroname."_proforma_header");
        return $result;
    }

    public function renameProformaLine($acroname, $newAcroname) {
        $result = $this->db->QUERY("ALTER TABLE ".$acroname."_proforma_line RENAME TO ".$newAcroname."_proforma_line");
        return $result;
    }

    public function createItemTable($tableName) {
        $result = $this->db->query('CREATE TABLE IF NOT EXISTS item'.$tableName.' (
                                            id int(100) NOT NULL AUTO_INCREMENT,
                                            itemcode_sup varchar(255),
                                            itemcode_cus varchar(255),
                                            description text,
                                            supplier_code int(100),
                                            customer_code int(100), PRIMARY KEY (id))');
        return $result;
    }

    public function checkDuplicate($table, $sName, $Aname) {
        $result = $this->db->SELECT('*')
            ->FROM($table)
            ->WHERE('supplier_name', $sName)
            ->WHERE('acroname', $Aname)
            ->GET()
            ->ROW();
        return $result;
    }

    public function createProformaHeader($acroname) {
        $headerQuery = $this->db->query("CREATE TABLE IF NOT EXISTS ".$acroname."_proforma_header ( proforma_header_id int(40) NOT NULL AUTO_INCREMENT, 
                                            order_date varchar(255), 
                                            delivery_date varchar(255), 
                                            so_no varchar(255),
                                            order_no varchar(255),
                                            proforma_code varchar(255),
                                            supplier_id int(45),
                                            customer_code int(45),
                                            po_header_id int(45),
                                            status varchar(255), 
                                            crf_id int(45),
                                            user_id int(45),
                                            date_uploaded varchar(255),
                                            PRIMARY KEY (proforma_header_id))");
        return $headerQuery;
    }

    public function createProformaLine($acroname) {
        $lineQuery = $this->db->query("CREATE TABLE IF NOT EXISTS ".$acroname."_proforma_line ( proforma_line_id int(45) NOT NULL AUTO_INCREMENT,
                                        item_code varchar(255),
                                        description text,
                                        qty int(255),
                                        uom varchar(255),
                                        price double(10,2),
                                        amount double(10, 2),
                                        supplier_id int(45),
                                        customer_code int(45),
                                        proforma_header_id int(45),
                                        PRIMARY KEY (proforma_line_id))");
        return $lineQuery;
    }

    public function update($id, $id_target, $table, $data) {
        $result = $this->db->where($id_target, $id)
            ->update($table, $data);

        return $result;
    }

    public function addNewType($typeName) {
        $query = $this->db->get_where('deduction_type', array('type' => $typeName));
        if($query->num_rows() == 0) {
            $type = ['type' => $typeName,
                'status' => 1];

            return $this->db->insert('deduction_type', $type);
        } else {
            return false;
        }
    }

    public function getType() {
        $query = $this->db->get('deduction_type');
        return $query->result_array();
    }

    public function saveNewDeduction($type, $name, $new, $supId) {
        $query = $this->db->get_where('deduction', array('deduction_type_id' => $type, 'name' => $name, 'supplier_id' => $supId));
        if($query->num_rows() == 0) {

            return $this->db->insert('deduction', $new);
        } else {
            return false;
        }
    }

    public function loadDeductions() {
        $query = $this->db->query("SELECT `d`.*, `t`.*, `d`.`status` as statuss,
                                   CASE WHEN `d`.`supplier_id` = 'All' THEN 'All'
                                        WHEN `d`.`supplier_id` != 'All' THEN `s`.`supplier_name`
                                   END as supplier
                                   FROM `deduction` `d` 
                                   LEFT JOIN `deduction_type` `t` ON `t`.`deduction_type_id` = `d`.`deduction_type_id` 
                                   LEFT JOIN `suppliers` `s` ON `s`.`supplier_id` = `d`.`supplier_id` 
                                   ORDER BY `supplier` ASC ");
        return $query->result_array();
    }

    public function getOldData($table, $where, $id) {
        $query = $this->db->get_where($table, array($where => $id));
        return $query->row_array();
    }

    public function loadVAT() {
        $query = $this->db->get('vat');
        return $query->result();
    }

    public function getChargesType($chargesType) {
        $this->db->select('*');
        $this->db->from('charges_type');
        $this->db->where('charges_type', $chargesType);
        $query = $this->db->get();

        if($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return null;
        }
    }
}
