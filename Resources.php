<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resources extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('resources_model','resources');
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
				
				if($this->input->post('title') != ""){
            		$title = trim($this->input->post('title'));
        		} else {
            		$title = str_replace("%20",' ',($this->uri->segment(3))?$this->uri->segment(3):0);
        		}
				$data['search_title'] = $title;
				$allrecord = $this->resources->allrecord($title);
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

				$data['indtitle'] = $this->resources->Industries($this->ion_auth->user()->row()->id);
				$data['resourceTopics'] = $this->resources->resourceTopics($this->ion_auth->user()->row()->id);
				$data['featResource'] = $this->resources->featResources($this->ion_auth->user()->row()->id);
				
				$this->load->view('resources/index', $data);
				
			}
			

			$this->load->view('templates/footer');
		}
	}
	
	public function view($slug = NULL){
		if (!$this->ion_auth->logged_in()){
			redirect('login', 'refresh');
		} else {
			$data['pass_update'] = $this->ion_auth->user()->row()->pass_update;
			$data['usergroup'] = $this->ion_auth->getUserRole($this->ion_auth->user()->row()->id);
			$data['user'] = $this->ion_auth->user()->row();
			$data['full_resource'] = $this->resources->get_resource($slug);
			$data['inddrop'] = $this->ion_auth->getMyIndDrop($this->ion_auth->user()->row()->id);
			//Primary Sector selected
			$data['primsector'] = $this->ion_auth->PrimSector($this->ion_auth->user()->row()->id);
			
			if (empty($data['full_resource'])){
					show_404();
			}
			//RATING ****
			/*$data["post_id"] = $slug;
			$data["is_rated"] = $this->insight->get_user_numrate($slug,$this->ion_auth->user()->row()->id);
			$total_rates = 0;
			$total_points = 0;
			
			$query1 = $this->resources->get_insight_rate($slug);

			// check if article has rate if yes get it
			if($query1 !== false){
				$total_rates = $query1->insight_rt_total_rates;
				$total_points = $query1->insight_rt_total_points;
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
*/
			$this->load->view('templates/header', $data);
			$this->load->view('resources/resource', $data);
			$this->load->view('templates/footer');
		}
	}
	
	public function getExpert($id){
		$data = $this->resources->get_expert_by_id($id);
		echo json_encode($data);
	}
	public function getAdvisor($id){
		$data = $this->resources->get_advisor_by_id($id);
		echo json_encode($data);
	}
}