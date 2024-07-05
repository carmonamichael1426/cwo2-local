<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cwoslipcontroller extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('upload');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->library('PHPExcel');
        $this->load->library('fpdf');
        $this->load->model('cwoslipmodel');
        date_default_timezone_set('Asia/Manila');

        //Disable Cache
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        ('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    function sanitize($string)
    {
        $string = htmlentities($string, ENT_QUOTES, 'UTF-8');
        $string = trim($string);
        return $string;
    }

    public function getPO()
    {
        $supplierid = $this->uri->segment(4);
        $customerID = $this->uri->segment(5);

        $result = $this->cwoslipmodel->getPOHeader($supplierid, $customerID);
        return JSONResponse($result);
    }
}
