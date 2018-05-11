<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Emergency extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('emergency_model','emergency');
		$this->load->helper('url_helper');
		$this->load->library(array('ion_auth','form_validation'));
		$this->load->library('email');
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
			$this->load->view('templates/header', $data);
			
			//Check to see if the user has a default industry set. If not, send them to the selection pane.
			
			$IndCheck = $this->ion_auth->IndustrySetCheck($this->ion_auth->user()->row()->id);
			
			if(!$IndCheck){
				$data['my_ind'] = $this->ion_auth->IndustrySetCheck($this->ion_auth->user()->row()->industry_id);
				$data['industries'] = $this->ion_auth->getIndustry();
				$this->load->view('auth/add_industry', $data);
				
			} else {
				
				$data['indtitle'] = $this->emergency->Industries($this->ion_auth->user()->row()->id);
				$this->load->view('emergency/index', $data);
				
			}
			

			$this->load->view('templates/footer');
		}
	}
	
	public function view($slug = NULL)
	{
		if (!$this->ion_auth->logged_in()){
			redirect('login', 'refresh');
		} else {

			$data['user'] = $this->ion_auth->user()->row();
			$data['ticket_detail'] = $this->emergency->get_ticket($slug);

			if (empty($data['ticket_detail']))
			{
					show_404();
			}

			$data['user'] = $this->ion_auth->user()->row();
			$data['pass_update'] = $this->ion_auth->user()->row()->pass_update;
			$data['usergroup'] = $this->ion_auth->getUserRole($this->ion_auth->user()->row()->id);
			$data['inddrop'] = $this->ion_auth->getMyIndDrop($this->ion_auth->user()->row()->id);
			
			$this->load->view('templates/header', $data);
			$this->load->view('emergency/view', $data);
			$this->load->view('templates/footer');
		}
	}
	
	public function add_ticket(){
		
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('first_name', 'First Name', 'required');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required');
		$this->form_validation->set_rules('phone', 'Phone', 'required');
		$this->form_validation->set_rules('crisis_desc', 'Crisis Description', 'required');
		$this->form_validation->set_rules('crisis_function', 'Crisis Function', 'required');
		$this->form_validation->set_rules('crisis_business_impact', 'Business Impact', 'required');
		
		if ($this->form_validation->run() === FALSE){

			$this->load->view('templates/header');
			$this->load->view('emergency/index');
			$this->load->view('templates/footer');
	
		} else {
		//ADD TICKET
		$ticket_number = 'CR-'.date('mdy').'-'.mt_rand(100000,999999);
		$data = array(
			'ticket_type_id' => 1,
			'ticket_number' => $ticket_number,
			'cat_id' => $this->input->post('cat_id'),
			'priority_id' => $this->input->post('priority_id'),
			'description' => $this->input->post('crisis_desc'),
			'function' => $this->input->post('crisis_function'),
			'business_impact' => $this->input->post('crisis_business_impact'),
			'user_id' => $this->input->post('id'),
			'first_name' => $this->input->post('first_name'),
			'last_name' => $this->input->post('last_name'),
			'email' => $this->input->post('email'),
			'phone' => $this->input->post('phone')
			
		);
		
		$data2 = array(
				'ticket_number' => $ticket_number,
				'description' => 'Ticket Created'
		);
			
		$insert = $this->emergency->saveTicket($data);
		$insert = $this->emergency->saveTicketHistory($data2);
		
		/* SEND EMAIL TO ARETE */
			
		$emailMsg = '<b>CYBERFORUM EMERGENCY TICKET</b>
		<br /><br />
		Ticket Number: <b>'.$ticket_number.'</b><br />
		First Name: '.$this->input->post('first_name').'<br />
		Last Name: '.$this->input->post('last_name').'<br />
		Email: '.$this->input->post('email').'<br />
		Phone: '.$this->input->post('phone').'
		<br /><br />
		Issue Description<br />
		'.$this->input->post('crisis_desc').'
		<br /><br />
		Function<br />
		'.$this->input->post('crisis_function').'
		<br /><br />
		Business Impact<br />
		'.$this->input->post('crisis_business_impact').'
		<br /><br />
		THIS IS AN AUTOMATED EMAIL';
		
		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';
		$config['newline'] = '\r\n';
		
		$this->load->library('email');
		$this->email->initialize($config);
		
		//Send Email Notification
		$first_name  = $this->input->post('first_name');
		$last_name  = $this->input->post('last_name');
		$user_email  = $this->input->post('email');
		
		//Send Email To Arete
		$this->email->from($this->input->post('email'), $this->input->post('first_name').' '.$this->input->post('last_name'));
		$this->email->to('greg.mahoney@gmail.com');
		$this->email->subject('CyberShield Crisis Hotline Ticket ('.$ticket_number.')');
		$this->email->message($emailMsg);
		$this->email->send();
		
		//Send Email to the User for their reference
		$this->email->from('gmahoney@areteadvisorsinc.com', 'CyberForum');
		$this->email->to($user_email);
		$this->email->subject('CyberForum - Cyber Emergency');
		$this->email->message('Your Cyber Emergency ticket has been submitted. Your ticket number is: '.$ticket_number.'');
		$this->email->send();

		
		$data['user'] = $this->ion_auth->user()->row();
		$data['pass_update'] = $this->ion_auth->user()->row()->pass_update;
		$data['usergroup'] = $this->ion_auth->getUserRole($this->ion_auth->user()->row()->id);
		$data['ticket_number'] = $ticket_number;
		$data['inddrop'] = $this->ion_auth->getMyIndDrop($this->ion_auth->user()->row()->id);
		$this->load->view('templates/header', $data);
		$this->load->view('emergency/thankyou', $data);
		$this->load->view('templates/footer');
		}
	}
	
	//Get Category Types
	public function fetch_category_type() {
		$priority = $this->emergency->fetch_category_type(); 
		echo json_encode($priority);
	}
	//Get Priority Types
	public function fetch_priority_type() {
		$priority = $this->emergency->fetch_priority_type(); 
		echo json_encode($priority);
	}
}