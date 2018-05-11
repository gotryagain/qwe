<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expert_model extends CI_Model {

	 var $table = 'tickets';
	 var $LawFirm = 'law_firm';
	 var $Users = 'users';
	 
	 public function __construct()
     {
        $this->load->database();
     }

	public function Industries($id){
		$this->db->select('UI.*, I.industry_id, I.industry, I.industry_desc');
		$this->db->from('user_industries AS UI');
		$this->db->join('industries AS I', 'I.industry_id = UI.industry_id', 'inner');
		$this->db->WHERE('UI.user_id', $id);
		$this->db->order_by('I.industry', 'asc');
			
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function LawExpert(){
		$this->db->select('UG.*, U.last_name, U.first_name, U.bio_image, U.company, U.title');
		$this->db->from('users_groups AS UG');
		$this->db->join('users AS U', 'U.id = UG.user_id', 'inner');
		$this->db->where('UG.group_id', 5);
		$this->db->order_by('U.last_name', 'asc');
		$this->db->order_by('U.first_name', 'asc');	
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function AllLawExperts(){
		$this->db->select('LF.law_firm, LF.lf_id, LF.lf_email, LF.lf_phone, LF.lf_desc, LF.lf_logo_file, U.id AS user_id, U.first_name, U.last_name, U.email, U.title, U.phone, U.company, U.bio_image, U.bio_text');
		$this->db->from('law_firm_users AS LFU');
		$this->db->join('law_firm AS LF', 'LF.lf_id = LFU.lf_id', 'inner');
		$this->db->join('users AS U', 'U.id = LFU.user_id', 'inner');
		$this->db->order_by('U.last_name', 'asc');
		$this->db->order_by('U.first_name', 'asc');	
		$query = $this->db->get();
		return $query->result_array();
	}
	public function AllTechExperts(){
		$this->db->select('U.id AS user_id, U.first_name, U.last_name, U.email, U.title, U.phone, U.company, U.bio_image, U.bio_text');
		$this->db->from('resource_experts AS RE');
		$this->db->join('users AS U', 'U.id = RE.user_id', 'inner');
		$this->db->order_by('U.last_name', 'asc');
		$this->db->order_by('U.first_name', 'asc');	
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function saveTicket($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}
	
	public function get_lawfirm_by_id($id)
	{
		$this->db->from($this->LawFirm);
		$this->db->where('lf_id',$id);
		$query = $this->db->get();
		return $query->row();
	}
	public function get_lawyer_by_id($id)
	{
		$this->db->from($this->Users);
		$this->db->where('id',$id);
		$query = $this->db->get();
		return $query->row();
	}



}
