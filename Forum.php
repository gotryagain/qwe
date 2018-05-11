<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Forum extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('forum_model','forum');
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
			
			$this->load->view('templates/header', $data);
			
			//Check to see if the user has a default industry set. If not, send them to the selection pane.
			
			$IndCheck = $this->ion_auth->IndustrySetCheck($this->ion_auth->user()->row()->id);
			
			if(!$IndCheck){
				$data['my_ind'] = $this->ion_auth->IndustrySetCheck($this->ion_auth->user()->row()->industry_id);
				$data['industries'] = $this->ion_auth->getIndustry();
				$this->load->view('auth/add_industry', $data);
				
			} else {
				
				//Primary Sector selected
				$data['primsector'] = $this->ion_auth->PrimSector($this->ion_auth->user()->row()->id);
				$data['indtitle'] = $this->forum->Industries($this->ion_auth->user()->row()->id);
				$data['faqTitles'] = $this->forum->faqTitles($this->ion_auth->user()->row()->id);
				$data['myQuestions'] = $this->forum->myQuestions($this->ion_auth->user()->row()->id);
				
				
				
				$this->load->view('forum/index', $data);
				
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
			$data['pass_update'] = $this->ion_auth->user()->row()->pass_update;
			$data['usergroup'] = $this->ion_auth->getUserRole($this->ion_auth->user()->row()->id);
			$data['discussion_detail'] = $this->forum->get_post($slug);
			//$data['myQuestions'] = $this->forum->myQuestions($this->ion_auth->user()->row()->id);
			$data['inddrop'] = $this->ion_auth->getMyIndDrop($this->ion_auth->user()->row()->id);
			
			//Primary Sector selected
			$data['primsector'] = $this->ion_auth->PrimSector($this->ion_auth->user()->row()->id);
				
			if (empty($data['discussion_detail']))
			{
					show_404();
			}

			$this->load->view('templates/header', $data);
			$this->load->view('forum/view', $data);
			$this->load->view('templates/footer');
		}
	}
	
	public function full_forum($slug = NULL)
	{
		if (!$this->ion_auth->logged_in()){
			redirect('login', 'refresh');
		} else {
			$data['user'] = $this->ion_auth->user()->row();
			$data['pass_update'] = $this->ion_auth->user()->row()->pass_update;
			$data['usergroup'] = $this->ion_auth->getUserRole($this->ion_auth->user()->row()->id);
			$data['forum_detail'] = $this->forum->get_forum($slug);
			$data['myQuestions'] = $this->forum->myQuestions($this->ion_auth->user()->row()->id);
			$data['indtitle'] = $this->forum->Industries($this->ion_auth->user()->row()->id);
			$data['inddrop'] = $this->ion_auth->getMyIndDrop($this->ion_auth->user()->row()->id);
			//Primary Sector selected
			$data['primsector'] = $this->ion_auth->PrimSector($this->ion_auth->user()->row()->id);
			if (empty($data['forum_detail']))
			{
					show_404();
			}

			$this->load->view('templates/header', $data);
			$this->load->view('forum/full', $data);
			$this->load->view('templates/footer');
		}
	}
	
	public function getUser($id)
	{
		$data = $this->forum->get_user_by_id($id);
		echo json_encode($data);
	}
	
	
	public function askQuestion(){
		if (!$this->ion_auth->logged_in()){
			redirect('login', 'refresh');
		} else {
			
			$data['user'] = $this->ion_auth->user()->row();
			$data['pass_update'] = $this->ion_auth->user()->row()->pass_update;
			$data['usergroup'] = $this->ion_auth->getUserRole($this->ion_auth->user()->row()->id);
			$data['inddrop'] = $this->ion_auth->getMyIndDrop($this->ion_auth->user()->row()->id);
			$this->load->view('templates/header', $data);
			$IndCheck = $this->ion_auth->IndustrySetCheck($this->ion_auth->user()->row()->id);
			
			if(!$IndCheck){
				$data['my_ind'] = $this->ion_auth->IndustrySetCheck($this->ion_auth->user()->row()->industry_id);
				$data['industries'] = $this->ion_auth->getIndustry();
				$this->load->view('auth/add_industry', $data);
				
			} else {
				
				//Primary Sector selected
				$data['primsector'] = $this->ion_auth->PrimSector($this->ion_auth->user()->row()->id);
				$data['indtitle'] = $this->forum->Industries($this->ion_auth->user()->row()->id);
				$data['faqTitles'] = $this->forum->faqTitles($this->ion_auth->user()->row()->id);
				
				$data['faq_parent_topics'] = $this->forum->fetch_topics($this->ion_auth->user()->row()->id);
				$data['faq_topics'] = $this->forum->fetch_sub_topics($this->ion_auth->user()->row()->id);
				
				$this->load->view('forum/ask_question', $data);
				
			}
			

			$this->load->view('templates/footer');
			
		}
	}
	
	public function ask_anon_question(){
		if (!$this->ion_auth->logged_in()){
			redirect('login', 'refresh');
		} else {
			
			$data['user'] = $this->ion_auth->user()->row();
			$data['pass_update'] = $this->ion_auth->user()->row()->pass_update;
			$data['usergroup'] = $this->ion_auth->getUserRole($this->ion_auth->user()->row()->id);
			$data['inddrop'] = $this->ion_auth->getMyIndDrop($this->ion_auth->user()->row()->id);
			$this->load->view('templates/header', $data);
			$IndCheck = $this->ion_auth->IndustrySetCheck($this->ion_auth->user()->row()->id);
			
			if(!$IndCheck){
				$data['my_ind'] = $this->ion_auth->IndustrySetCheck($this->ion_auth->user()->row()->industry_id);
				$data['industries'] = $this->ion_auth->getIndustry();
				$this->load->view('auth/add_industry', $data);
				
			} else {
				
				//Primary Sector selected
				$data['primsector'] = $this->ion_auth->PrimSector($this->ion_auth->user()->row()->id);
				$data['indtitle'] = $this->forum->Industries($this->ion_auth->user()->row()->id);
				$data['faqTitles'] = $this->forum->faqTitles($this->ion_auth->user()->row()->id);
				
				$data['faq_parent_topics'] = $this->forum->fetch_topics($this->ion_auth->user()->row()->id);
				$data['faq_topics'] = $this->forum->fetch_sub_topics($this->ion_auth->user()->row()->id);
				
				$this->load->view('forum/ask_anon_question', $data);
				
			}
			

			$this->load->view('templates/footer');
			
		}
	}
	public function sub_topic_list($id) { 
       //$result = $this->db->where("parent_discussion_id",$id)->$this->db->order_by('topic', 'desc')->get("discussion_topics")->result();
	   $result = $this->db->query("SELECT discussion_id, topic FROM discussion_topics WHERE parent_discussion_id = $id ORDER BY topic asc")->result();
       echo json_encode($result);
   }
   
	public function add_question(){
	
	//Check to see if the user submitted a new category, If so, create the new category
	if($this->input->post('add_topic')<>''){
		$newCat = array(
		   	'Is_sub' => 0,
			'industry_id' => $this->input->post('industry_id'),
			'topic' => $this->input->post('add_topic'),
		);
		//New Topic ID
		$parent_discussion_id = $this->forum->saveNewCat($newCat);		
	} else {
		$parent_discussion_id = $this->input->post('parent_discussion_id');
	}
	
	//Check to see if the user submitted a new sub category. If so, create the new sub category
		if($this->input->post('add_sub_topic')<>''){
		   $newSubCat = array(
		   		'Is_sub' => 1,
				'parent_discussion_id' => $parent_discussion_id,
				'topic' => $this->input->post('add_sub_topic'),
			);
			//New Sub Topic ID
			$discussion_id = $this->forum->saveNewSubCat($newSubCat);
			
		} else if($this->input->post('discussion_id')<>''){
			$discussion_id = $this->input->post('discussion_id');
			
		} else if($this->input->post('discussion_id')==''){
			$discussion_id = $parent_discussion_id;
	   	}
		
		
		/*if(is_null($this->input->post('Is_anonymous'))){
		   $Is_anonymous = 1;
	   	} else {
		   $Is_anonymous = 0;
	   	}*/
		
		$data = array(
			'discussion_id' => $discussion_id,
			'Is_anonymous' => $this->input->post('Is_anonymous'),
			'question_direct' => $this->input->post('question_direct'),
			'post_title' => $this->input->post('post_title'),
			'post_text' => $this->input->post('post_text'),
			'user_id' => $this->input->post('id'),
			'visible' => 1,
		);

		$this->forum->saveQuestion($data);
		
		//SEND NEW EMAIL
		
		$first_name  = $this->input->post('first_name');
		$last_name  = $this->input->post('last_name');
		$user_email  = $this->input->post('email');
		$industry  = $this->input->post('industry');
		
		$config = Array(    
		'protocol' => 'sendmail',
		'smtp_host' => 'mail.gmahoney-sandbox.com',
		'smtp_port' => 25,
		'smtp_user' => 'cyberforum@gmahoney-sandbox.com',
		'smtp_pass' => 'W3lc0m31',
		'smtp_timeout' => '4',
		'mailtype' => 'html',
		'charset' => 'iso-8859-1'
		);
		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");
		//Send Notice to the submitter
		$this->email->from('cyberforum@gmahoney-sandbox.com', 'Cyberforum');
		$data = array(
			'first_name'=> $first_name,
			'last_name'=> $last_name
		);
		$this->email->to($user_email); 
		$this->email->subject('CyberForum - Ask a Question'); 
		$body = $this->load->view('emails/forum.php',$data,TRUE);
		$this->email->message($body); 
		$this->email->send();
		
		//Send Notice to the admin
		$this->email->from('cyberforum@gmahoney-sandbox.com', 'Cyberforum');
		$data = array(
			'admin_name'=> 'CyberForum Admin',
			'sub_first_name'=> $first_name,
			'sub_last_name'=> $last_name,
			'topic'=> $parent_discussion_id,
			'sub_topic'=> $discussion_id,
			'title'=> $this->input->post('post_title'),
			'question'=> $this->input->post('post_text')
		);
		$this->email->to($user_email); 
		$this->email->subject('CyberForum - Ask a Question'); 
		$body = $this->load->view('emails/forum_admin.php',$data,TRUE);
		$this->email->message($body); 
		$this->email->send();
		

		$data['user'] = $this->ion_auth->user()->row();
		$data['pass_update'] = $this->ion_auth->user()->row()->pass_update;
		$data['usergroup'] = $this->ion_auth->getUserRole($this->ion_auth->user()->row()->id);
		$data['inddrop'] = $this->ion_auth->getMyIndDrop($this->ion_auth->user()->row()->id);
		
		//Primary Sector selected
		$data['primsector'] = $this->ion_auth->PrimSector($this->ion_auth->user()->row()->id);
		
		$this->load->view('templates/header', $data);
		$this->load->view('forum/thankyou', $data);
		$this->load->view('templates/footer');
	}
	
	public function post_reponse(){
	
		$data = array(
			'post_id' => $this->input->post('post_id'),
			'Is_anonymous' => $this->input->post('response_is_anonymous'),
			'thread_text' => $this->input->post('thread_text'),
			'user_id' => $this->input->post('id'),
			'visible' => 1,
		);
			
		$this->forum->saveThread($data);
		
		$config = Array(    
		'protocol' => 'sendmail',
		'smtp_host' => 'mail.gmahoney-sandbox.com',
		'smtp_port' => 25,
		'smtp_user' => 'cyberforum@gmahoney-sandbox.com',
		'smtp_pass' => 'W3lc0m31',
		'smtp_timeout' => '4',
		'mailtype' => 'html',
		'charset' => 'iso-8859-1'
		);
		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");
		
		//Send Notice to the Original Poster
		$this->email->from('cyberforum@gmahoney-sandbox.com', 'Cyberforum');
		$data = array(
			'post_title'=> $this->input->post('post_title'),
			'Is_anonymous'=> $this->input->post('response_is_anonymous'),
			'res_first_name'=> $this->input->post('first_name'),
			'res_last_name'=> $this->input->post('last_name'),
			'thread_text'=> $this->input->post('thread_text')
		);
		$this->email->to($this->input->post('orig_email')); 
		$this->email->subject('CyberForum - Discussion Forum Response'); 
		$body = $this->load->view('emails/response.php',$data,TRUE);
		$this->email->message($body); 
		$this->email->send();


		$data['user'] = $this->ion_auth->user()->row();
		redirect('/forum/'.$this->input->post('post_id').'', 'refresh');
}
	
public function htmlmail(){
    $config = Array(    
		'protocol' => 'sendmail',
		'smtp_host' => 'mail.gmahoney-sandbox.com',
		'smtp_port' => 25,
		'smtp_user' => 'cyberforum@gmahoney-sandbox.com',
		'smtp_pass' => 'W3lc0m31',
		'smtp_timeout' => '4',
		'mailtype' => 'html',
		'charset' => 'iso-8859-1'
    );
	$this->load->library('email', $config);
	$this->email->set_newline("\r\n");
	$this->email->from('cyberforum@gmahoney-sandbox.com', 'Cyberforum');
	$data = array(
		'userName'=> 'Sample User'
	);
	$this->email->to($userEmail); 
	$this->email->subject($subject); 
	$body = $this->load->view('emails/forum.php',$data,TRUE);
	$this->email->message($body); 
	$this->email->send();

	}
}