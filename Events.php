<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Events extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('event_model','events');
		$this->load->helper('url_helper');
		$this->load->library('ion_auth');
		$this->load->library(array('ion_auth','form_validation'));
		$this->load->helper(array('url','language'));
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'			));
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
			$data['eventYear'] = $this->events->eventYear();
			
			//$data['mainTitles'] = $this->events->mainTitles();
			$data['sideBarTitles'] = $this->events->sideBarTitles();
			//Primary Sector selected
			$data['primsector'] = $this->ion_auth->PrimSector($this->ion_auth->user()->row()->id);

			$this->load->view('templates/header', $data);
			$this->load->view('events/index', $data);
			$this->load->view('templates/footer');
		}
		
		
	}
	public function view($slug = NULL)
	{
		if (!$this->ion_auth->logged_in()){
			redirect('login', 'refresh');
		} else {
			$data['user'] = $this->ion_auth->user()->row();
			$data['pass_update'] = $this->ion_auth->user()->row()->pass_update;
			$data['usergroup'] = $this->ion_auth->getUserRole($this->ion_auth->user()->row()->id);
			$data['event_detail'] = $this->events->get_event($slug);
			$data['sideBarTitles'] = $this->events->sideBarTitles();
			$data['inddrop'] = $this->ion_auth->getMyIndDrop($this->ion_auth->user()->row()->id);
			if (empty($data['event_detail']))
			{
					show_404();
			}
			//Primary Sector selected
			$data['primsector'] = $this->ion_auth->PrimSector($this->ion_auth->user()->row()->id);

			$this->load->view('templates/header', $data);
			$this->load->view('events/view', $data);
			$this->load->view('templates/footer');
		}
	}
}
