<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Faq extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('faq_model','faqs');
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
				
				if($this->input->post('title') != ""){
            		$title = trim($this->input->post('title'));
        		} else {
            		$title = str_replace("%20",' ',($this->uri->segment(3))?$this->uri->segment(3):0);
        		}
				$data['search_title'] = $title;
				
				$allrecord = $this->faqs->allrecord($title, $this->ion_auth->user()->row()->id);
				
        		$baseurl =  base_url().$this->router->class.'/'.$this->router->method."/".$title;
				
				$paging=array();
				$paging['base_url'] = $baseurl;
				$paging['total_rows'] = $allrecord;
				$paging['per_page'] = 25;
				$paging['uri_segment']= 4;
				$paging['num_links'] = 5;
				$paging['first_link'] = 'First';
				$paging['first_tag_open'] = '<li>>';
				$paging['first_tag_close'] = '</li>';
				$paging['num_tag_open'] = '<li>';
				$paging['num_tag_close'] = '</li>';
				$paging['prev_link'] = 'Prev';
				$paging['prev_tag_open'] = '<li>';
				$paging['prev_tag_close'] = '</li>';
				$paging['next_link'] = 'Next';
				$paging['next_tag_open'] = '<li>';
				$paging['next_tag_close'] = '</li>';
				$paging['last_link'] = 'Last';
				$paging['last_tag_open'] = '<li>';
				$paging['last_tag_close'] = '</li>';
				$paging['cur_tag_open'] = '<li class="active"><a href="javascript:void(0);">';
				$paging['cur_tag_close'] = '</a></li>';
				
				$this->pagination->initialize($paging);
				
				$data['limit'] = $paging['per_page'];
				$data['number_page'] = $paging['per_page']; 
				$data['offset'] = ($this->uri->segment(4)) ? $this->uri->segment(4):'0';    
				$data['nav'] = $this->pagination->create_links();
				$data['faqx'] = $this->faqs->data_list($data['limit'],$data['offset'],$title, $this->ion_auth->user()->row()->id);
				
				//Primary Sector selected
				$data['primsector'] = $this->ion_auth->PrimSector($this->ion_auth->user()->row()->id);
				
				$data['indtitle'] = $this->faqs->Industries($this->ion_auth->user()->row()->id);
				$data['faqTitles'] = $this->faqs->faqTitles($this->ion_auth->user()->row()->id);
				$data['myQuestions'] = $this->faqs->myQuestions($this->ion_auth->user()->row()->id);
				$data['popular'] = $this->faqs->Popular($this->ion_auth->user()->row()->id);
				$this->load->view('faq/index', $data);
				
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
			$data['faq_detail'] = $this->faqs->get_faq($slug);
			$data['myQuestions'] = $this->faqs->myQuestions($this->ion_auth->user()->row()->id);
			$data['inddrop'] = $this->ion_auth->getMyIndDrop($this->ion_auth->user()->row()->id);
			
			if (empty($data['faq_detail']))
			{
					show_404();
			}
			//RATING ****
			$data["post_id"] = $slug;
			$data["is_rated"] = $this->faqs->get_user_numrate($slug,$this->ion_auth->user()->row()->id);
			
	
			$total_rates = 0;
			$total_points = 0;
			
			$query1 = $this->faqs->get_article_rate($slug);

			// check if article has rate if yes get it
			if($query1 !== false){
				$total_rates = $query1->faq_rt_total_rates;
				$total_points = $query1->faq_rt_total_points;
			}
			// if rating greater than zero
			// dived total rats on total rates and send it to view
			// else send zero to view
			if($total_points > 0 and $total_rates > 0){
				$ratings = $total_points/$total_rates;
				$data["ratings"] = $total_points/$total_rates;
				$data["rates"] = $ratings;
			}else{
				$data["rates"] = 0;
			}
			
			//END RATING ***

			$this->load->view('templates/header', $data);
			$this->load->view('faq/view', $data);
			$this->load->view('templates/footer');
		}
	}
	
	public function askQuestion(){
		if (!$this->ion_auth->logged_in()){
			redirect('login', 'refresh');
		} else {
			
			$data['user'] = $this->ion_auth->user()->row();
			$data['inddrop'] = $this->ion_auth->getMyIndDrop($this->ion_auth->user()->row()->id);
			$this->load->view('templates/header', $data);
			$IndCheck = $this->ion_auth->IndustrySetCheck($this->ion_auth->user()->row()->id);
			
			if(!$IndCheck){
				$data['my_ind'] = $this->ion_auth->IndustrySetCheck($this->ion_auth->user()->row()->industry_id);
				$data['industries'] = $this->ion_auth->getIndustry();
				$this->load->view('auth/add_industry', $data);
				
			} else {
				
				$data['indtitle'] = $this->faqs->Industries($this->ion_auth->user()->row()->id);
				$data['faqTitles'] = $this->faqs->faqTitles($this->ion_auth->user()->row()->id);
				
				$data['faq_topics'] = $this->faqs->fetch_topics($this->ion_auth->user()->row()->id);
				
				$this->load->view('faq/ask_question', $data);
				
			}
			

			$this->load->view('templates/footer');
			
		}
	}
	public function add_question(){
		$data = array(
			'discussion_id' => $this->input->post('discussion_id'),
			'title' => $this->input->post('title'),
			'faq_text' => $this->input->post('question'),
			'user_id' => $this->input->post('id'),
		);
		$this->faqs->saveFAQ($data);
		
		//Send Email Notification
		$first_name  = $this->input->post('first_name');
		$last_name  = $this->input->post('last_name');
		$user_email  = $this->input->post('email');
		
		//Send Email To Arete
		$this->email->from($user_email, $first_name.' '.$last_name);
		$this->email->to('greg.mahoney@gmail.com');
		$this->email->subject('CyberForum - Ask a Question');
		$this->email->message($this->input->post('question'));
		$this->email->send();
		
		//Send Email to the User for their reference
		$this->email->from('gmahoney@areteadvisorsinc.com', 'CyberForum');
		$this->email->to($user_email);
		$this->email->subject('CyberForum - Ask a Question');
		$this->email->message('Your question has been submitted. Please allow up to 2 business days for a response.');
		$this->email->send();

		
		$data['user'] = $this->ion_auth->user()->row();
		$data['usergroup'] = $this->ion_auth->getUserRole($this->ion_auth->user()->row()->id);
		$data['inddrop'] = $this->ion_auth->getMyIndDrop($this->ion_auth->user()->row()->id);
		
		$this->load->view('templates/header', $data);
		$this->load->view('faq/thankyou', $data);
		$this->load->view('templates/footer');
	}
	
	// create new rate
    function create_rate(){

			$this->user_id = $this->ion_auth->user()->row()->id;
            $post_id = $this->input->post("pid");
            $rate =  $this->input->post("score");


            //check the article is rated already
			$rated = $this->faqs->get_rate_numbers($post_id);
            if($rated == 0 ) {
                // if no send new rate record
                $rate_query = $this->faqs->insert_rate($post_id,$rate,$this->user_id);
            }else {
                // else get rate id and update value
                $rate_id = $this->faqs->get_article_rate($post_id);
                $rate_query = $this->faqs->update_rate($rate_id->faq_rt_id,$rate,$this->user_id);

            }
    /// after this see Succesfull msg
        if($rate_query)
        {
            echo "Voting Succesfull";
        }
    }
}