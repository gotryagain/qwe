<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resources_model extends CI_Model {
	
	var $Usertable = 'users';

	 public function __construct()
     {
        $this->load->library(array('session','pagination'));
        $this->load->helper('url');
		$this->load->database();
     }


	/* FEATURED RESOURCES */
	public function featResources($id){
		$this->db->select('R.*, RT.resource_topic');
		$this->db->from('resources AS R');
		$this->db->join('resource_topics AS RT', 'RT.resource_topic_id = R.resource_topic_id', 'inner');
		$this->db->where('R.featured', 1);
		$this->db->where("R.industry_id IN (SELECT industry_id
FROM  user_industries
WHERE user_id =$id AND Is_Primary = 1)", NULL, FALSE);
		$this->db->order_by('R.date_submitted', 'asc');
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function Industries($id){
		$this->db->select('UI.*, I.industry_id, I.industry, I.industry_desc');
		$this->db->from('user_industries AS UI');
		$this->db->join('industries AS I', 'I.industry_id = UI.industry_id', 'inner');
		$this->db->WHERE('UI.user_id', $id);
		$this->db->WHERE('UI.Is_Primary', 1);
		$this->db->order_by('I.industry', 'asc');
			
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function AdvisoryBoard($Ind_id){
		$this->db->select('AB.ab_id, IND.industry, U.id, U.last_name, U.first_name, U.bio_image, U.company, U.title');
		$this->db->from('advisory_board AS AB');
		$this->db->join('users AS U', 'U.id = AB.user_id', 'inner');
		$this->db->join('industries AS IND', 'IND.industry_id = AB.industry_id', 'inner');
		$this->db->where('AB.industry_id', $Ind_id);
		$this->db->order_by('IND.industry', 'asc');
		$this->db->order_by('U.last_name', 'asc');
		$this->db->order_by('U.first_name', 'asc');	
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function ResourceExperts(){
		$this->db->select('RE.user_id, U.last_name, U.first_name, U.bio_image, U.company, U.title');
		$this->db->from('resource_experts AS RE');
		$this->db->join('users AS U', 'U.id = RE.user_id', 'inner');
		$this->db->order_by('U.last_name', 'asc');
		$this->db->order_by('U.first_name', 'asc');	
		$query = $this->db->get();
		return $query->result_array();
	}
	
	/* RESOURCES */
	public function resourceTopics($id){
		$this->db->select('resource_topic_id, resource_topic, description');
		$this->db->from('resource_topics');
		//$this->db->where('industry_id', $ind);
		$this->db->where("industry_id IN (SELECT industry_id
FROM  user_industries
WHERE user_id =$id AND Is_Primary = 1)", NULL, FALSE);
		$this->db->order_by('resource_topic', 'asc');	
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function resources($RTID){
		$this->db->select('R.*, U.last_name, U.first_name, U.bio_image');
		$this->db->from('resources AS R');
		$this->db->join('users AS U', 'U.id = R.recommended', 'inner');
		$this->db->where('R.resource_topic_id', $RTID);
		$this->db->order_by('R.date_submitted', 'asc');	
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function attachments($RID){
		$this->db->select('*');
		$this->db->from('resource_attachments');
		$this->db->where('resource_id', $RID);
		$this->db->order_by('date_submitted', 'desc');	
		$query = $this->db->get();
		return $query->result_array();
	}
	
	/***** PAGINATION *******/
	public function allrecord($title){
        if(!empty($title)){
            $this->db->like('R.resource_title', $title);
        }
        $this->db->select('R.*, U.last_name, U.first_name, U.bio_image');
		$this->db->from('resources AS R');
		$this->db->join('users AS U', 'U.id = R.recommended', 'inner');
		$this->db->order_by('R.date_submitted', 'asc');	
        $rs = $this->db->get();
        return $rs->num_rows();
    }
	public function data_list($limit, $offset, $title, $RTID){
        if(!empty($title)){
            $this->db->like('R.resource_title', $title);
        }
        $this->db->select('R.*, U.last_name, U.first_name, U.bio_image');
		$this->db->from('resources AS R');
		$this->db->join('users AS U', 'U.id = R.recommended', 'inner');
		$this->db->where('R.resource_topic_id', $RTID);
		$this->db->order_by('R.date_submitted', 'asc');	
        $this->db->limit($limit,$offset);
        $rs = $this->db->get();
        return $rs->result_array();
    }
	
	public function get_advisor_by_id($id)
	{
		$this->db->from($this->Usertable);
		$this->db->where('id',$id);
		$query = $this->db->get();
		return $query->row();
	}
	
	public function get_resource($id){
		$this->db->select('R.resource_id, R.featured, R.resource_title, R.description, R.link, R.image, R.recommended, RT.resource_topic, U.first_name, U.last_name, U.bio_image');
			$this->db->from('resources AS R');
			$this->db->join('users AS U', 'U.id = R.recommended', 'inner');
			$this->db->join('resource_topics AS RT', 'RT.resource_topic_id = R.resource_topic_id', 'inner');
			//$this->db->join('resource_attachments AS RA', 'RA.resource_id = R.resource_id', 'inner');
			$this->db->WHERE('R.resource_id', $id);
			$query = $this->db->get();
			return $query->result_array();
	}



}
