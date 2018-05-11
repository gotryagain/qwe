<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expert extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('expert_model','expert');
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
			//Primary Sector selected
			$data['primsector'] = $this->ion_auth->PrimSector($this->ion_auth->user()->row()->id);
			$this->load->view('templates/header', $data);
			
			//Check to see if the user has a default industry set. If not, send them to the selection pane.
			
			$IndCheck = $this->ion_auth->IndustrySetCheck($this->ion_auth->user()->row()->id);
			
			if(!$IndCheck){
				$data['my_ind'] = $this->ion_auth->IndustrySetCheck($this->ion_auth->user()->row()->industry_id);
				$data['industries'] = $this->ion_auth->getIndustry();
				$this->load->view('auth/add_industry', $data);
				
			} else {
				
				$data['indtitle'] = $this->expert->Industries($this->ion_auth->user()->row()->id);
				$this->load->view('expert/index', $data);
				
			}
			

			$this->load->view('templates/footer');
		}
	}
	
	public function getLawFirm($lfid){
		$data = $this->expert->get_lawfirm_by_id($lfid);
		echo json_encode($data);
	}
	public function getLawyer($lid){
		$data = $this->expert->get_lawyer_by_id($lid);
		echo json_encode($data);
	}
	
	public function add_ticket(){
		$ticketNumber = 'CR-'.date('mdy').'-'.mt_rand(100000,999999);
		$data = array(
			'ticket_type_id' => 2,
			'ticket_number' => $ticketNumber,
			'cat_id' => $this->input->post('cat_id'),
			'priority_id' => $this->input->post('priority_id'),
			'industry_id' => $this->input->post('industry_id'),
			'description' => $this->input->post('ticket_text'),
			'user_id' => $this->input->post('id'),
		);
		$this->expert->saveTicket($data);
		
		//Send Email Notification
		$first_name  = $this->input->post('first_name');
		$last_name  = $this->input->post('last_name');
		$user_email  = $this->input->post('email');
		
		//Send Email To Arete
		$this->email->from($user_email, $first_name.' '.$last_name);
		$this->email->to('greg.mahoney@gmail.com');
		$this->email->subject('CyberForum - Connect to an Expert (ticket No: '.$ticketNumber.')');
		$this->email->message($this->input->post('ticket_text'));
		$this->email->send();
		
		//Send Email to the User for their reference
		$this->email->from('gmahoney@areteadvisorsinc.com', 'CyberForum');
		$this->email->to($user_email);
		$this->email->subject('CyberForum - Connect to an Expert');
		$this->email->message('Your question has been submitted to an industry expert. Your ticket number is: '.$ticketNumber.'');
		$this->email->send();

		
		$data['usergroup'] = $this->ion_auth->getUserRole($this->ion_auth->user()->row()->id);
		$data['pass_update'] = $this->ion_auth->user()->row()->pass_update;
		$data['user'] = $this->ion_auth->user()->row();
		$data['ticket_number'] = $ticketNumber;
		$data['inddrop'] = $this->ion_auth->getMyIndDrop($this->ion_auth->user()->row()->id);
		//Primary Sector selected
		$data['primsector'] = $this->ion_auth->PrimSector($this->ion_auth->user()->row()->id);
		$this->load->view('templates/header', $data);
		$this->load->view('expert/thankyou', $data);
		$this->load->view('templates/footer');
	}
}