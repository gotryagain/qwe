<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crisis extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('crisis_model','crisis');
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
		$data['success'] = '';
		
		$this->load->view('templates/header', $data);
		$this->load->view('crisis/index', $data);
		$this->load->view('templates/footer');
		}
	}
	
	/* Crisis Hotline Form Submit */
	public function crisisTicket()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('first_name', 'First Name', 'required');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required');
		$this->form_validation->set_rules('phone', 'Phone', 'required');
		$this->form_validation->set_rules('crisis_desc', 'Crisis Description', 'required');
	
	
		if ($this->form_validation->run() === FALSE)
		{

			$this->load->view('templates/header');
			$this->load->view('crisis/index');
			$this->load->view('templates/footer');
	
		}
		else
		{
			
			$this->crisis->addCrisisForm();
			$data['user'] = $this->ion_auth->user()->row();
			$data['success'] = 'Your ticket has been submitted';
			
			$this->load->view('templates/header');
			$this->load->view('crisis/index', $data);
			$this->load->view('templates/footer');
		}
	}
}
