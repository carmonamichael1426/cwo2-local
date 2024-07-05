<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller 
{
	function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
    }

	public function index()
	{
        if ( ! file_exists(APPPATH.'views/login/login.php')){ show_404(); }
		$this->load->view('login/login');
	}
}
