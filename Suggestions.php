<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Suggestions extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('suggestion_model','suggestion');
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
			$data['pass_update'] = $this->ion_auth->user()->row()->pass_update;
			$data['usergroup'] = $this->ion_auth->getUserRole($this->ion_auth->user()->row()->id);
			$data['inddrop'] = $this->ion_auth->getMyIndDrop($this->ion_auth->user()->row()->id);
			$this->data['suggestion'] = array('name' => 'suggestion',
				'id'    => 'suggestion',
				'type'  => 'text',
				'class' => 'form-control',
			);
			
			$this->load->view('templates/header', $data);
			$this->load->view('suggestion/index', $data, $this->data);
			$this->load->view('templates/footer');
		}
	}
	
	public function add_suggestion(){
		$data = array(
				'suggestion' => $this->input->post('suggestion'),
				'user_id' => $this->input->post('id'),
		);
		$this->suggestion->saveSuggestion($data);
		
		//Send Email Notification
		$first_name  = $this->input->post('first_name');
		$last_name  = $this->input->post('last_name');
		$user_email  = $this->input->post('email');
		
		$data['user'] = $this->ion_auth->user()->row();
		$data['usergroup'] = $this->ion_auth->getUserRole($this->ion_auth->user()->row()->id);
		$data['inddrop'] = $this->ion_auth->getMyIndDrop($this->ion_auth->user()->row()->id);
		$this->load->view('templates/header', $data);
		$this->load->view('suggestion/thankyou');
		$this->load->view('templates/footer');
	}
}