<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tips extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('url_helper');
		$this->load->library(array('ion_auth','form_validation'));
		$this->load->helper(array('url','language'));
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

		$this->lang->load('auth');
	}

	public function index()
	{
		
		if (!$this->ion_auth->logged_in()){
			redirect('login', 'refresh');
		} else {
			$data['user'] = $this->ion_auth->user()->row();
			$this->load->view('templates/header', $data);
			$this->load->view('tips/index');
			$this->load->view('templates/footer');
		}
	}
}
