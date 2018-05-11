<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('home_model','home');
		$this->load->helper('url_helper');
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
			$act_industry = $this->ion_auth->user()->row()->industry_id;
			$pass_update = $this->ion_auth->user()->row()->pass_update;
			
			$data['usergroup'] = $this->ion_auth->getUserRole($this->ion_auth->user()->row()->id);
			$data['pass_update'] = $this->ion_auth->user()->row()->pass_update;
			
			if($pass_update == 0 ){
				
				redirect('update-pw/'.$this->ion_auth->user()->row()->id.'', 'refresh');
			
			} elseif ($pass_update == 1){
				
				if ($this->ion_auth->in_group("members")){        
        			//redirect them to the master controller
      				redirect('insights', 'refresh');
					
				} elseif ($this->ion_auth->in_group("advisory board")){
					
					$data['myinfo'] = $this->home->myAccount($this->ion_auth->user()->row()->id);
					$data['mydiscussions'] = $this->home->getMyDiscussions($this->ion_auth->user()->row()->id);
					$data['inddrop'] = $this->ion_auth->getMyIndDrop($this->ion_auth->user()->row()->id);
					$this->load->view('templates/header', $data);
					$this->load->view('pages/home', $data);
					$this->load->view('templates/footer');
					
				} elseif ($this->ion_auth->in_group("Law Expert")){
					
					$data['myinfo'] = $this->home->myAccount($this->ion_auth->user()->row()->id);
					$data['mydiscussions'] = $this->home->getMyDiscussions($this->ion_auth->user()->row()->id);
					$data['inddrop'] = $this->ion_auth->getMyIndDrop($this->ion_auth->user()->row()->id);
					$this->load->view('templates/header', $data);
					$this->load->view('pages/home', $data);
					$this->load->view('templates/footer');
					
				} elseif ($this->ion_auth->in_group("admin")){
					
					$data['myinfo'] = $this->home->myAccount($this->ion_auth->user()->row()->id);
					$data['mydiscussions'] = $this->home->getMyDiscussions($this->ion_auth->user()->row()->id);
					$data['inddrop'] = $this->ion_auth->getMyIndDrop($this->ion_auth->user()->row()->id);
					$this->load->view('templates/header', $data);
					$this->load->view('pages/home', $data);
					$this->load->view('templates/footer');
				}	

			}
			
		}
	}
}