<?php
defined('BASEPATH') or exit('No direct script access allowed');

class App_model extends CI_model
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    function sanitize($string)
    {
        $string = htmlentities($string, ENT_QUOTES, 'UTF-8');
        $string = trim($string);
        return $string;
    }

    public function getUsers($user)
    {
        $result = $this->db->SELECT('*')
            ->FROM('users')
            ->WHERE('username', $user)
            ->WHERE('status !=', 'Deactivated')
            ->GET()
            ->ROW();
        return $result;
    }

    public function getAuthenticate($user, $pass)
    {
        $result = $this->db->SELECT('*')
            ->FROM('users')
            ->WHERE('username', $user)
            ->WHERE('password', $pass)
            ->WHERE('status !=', 'Deactivated')
            ->GET()
            ->ROW();

        return $result;
    }

    // public function testingDataForReport()
    // {
    //     $result = $this->db->SELECT('*')
    //                         ->FROM('transaction1')
    // }
}
