<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    public $db_pis;

    function __construct()
    {
        parent::__construct();

        $this->db_pis = $this->load->database('pis', TRUE);
    }

    public function getUsers()
    {
        $query = $this->db->select('u.user_id,u.emp_id,u.username,u.name,u.status,u.userType,u.userLoggedIn,u.province_id,u.position, GETCOMPANY(u.company_code) as comp, GETBUSINESSUNIT(u.company_code, u.bunit_code) as bu, GETDEPARTMENT(u.company_code, u.bunit_code, u.dept_code) as dep ')
            ->from('users u')
            ->get();
        return $query->result_array();
    }

    public function duplicate($username)
    {
        $result = $this->db->SELECT('*')
            ->FROM('users')
            ->WHERE('username', $username)
            ->GET()
            ->ROW();
        return $result;
    }

    public function getUserById($userid)
    {
        $query = $this->db->get_where('users', array('user_id' => $userid));
        return $query->row();
    }

    public function update($table, $data, $where)
    {
        $this->db->where($where)
            ->update($table, $data);
        return $this->db->affected_rows();
    }

    public function searchemp($str)
    {
        $query = $this->db_pis->select('e.*, c.company')
            ->from('employee3 e')
            ->join('locate_company c', 'c.company_code = e.company_code', 'inner')
            ->like('e.name', $str, 'both')
            ->where('e.current_status', 'Active')
            ->order_by('e.name', 'ASC')
            ->limit(10)
            ->get();
        return $query->result();
    }

    public function checkUserExistence($empId)
    {
        $query = $this->db->get_where('users', array('emp_id' => $empId));
        return $query->num_rows() > 0;
    }
}
