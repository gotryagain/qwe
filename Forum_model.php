<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Forum_model extends CI_Model {

	var $table = 'discussion';
	var $Threadtable = 'discussion_thread';
	var $Topicstable = 'discussion_topics';
	var $Usertable = 'users';
	
	 public function __construct()
     {
        $this->load->database();
     }
	 
	 public function faqTitles($id){

		#Create main query
		$this->db->select('DT.discussion_id, DT.topic, DT.topic_desc');
		$this->db->from('discussion_topics AS DT');
		$this->db->join('industries AS IND', 'IND.industry_id = DT.industry_id', 'inner');
		$this->db->where("DT.industry_id IN (SELECT industry_id
FROM  user_industries
WHERE user_id =$id AND Is_Primary = 1)", NULL, FALSE);
		$this->db->WHERE('DT.Is_sub', 0);
		$this->db->order_by('DT.discussion_id', 'asc');

		$query = $this->db->get();
		return $query->result_array();
	}
	public function SubCats($fid) {
			$this->db->select('discussion_id, topic, topic_desc');
			$this->db->from('discussion_topics');
			$this->db->WHERE('parent_discussion_id', $fid);
			$this->db->order_by('topic', 'asc');
			$query = $this->db->get();
			return $query->result_array();
	}
	
	public function Limitpost($Did){

		#Create main query
		$this->db->select('DD.*, DT.topic, U.last_name, U.first_name, U.bio_image');
		$this->db->from('discussion AS DD');
		$this->db->join('discussion_topics AS DT', 'DT.discussion_id = DD.discussion_id', 'inner');
		$this->db->join('users AS U', 'U.id = DD.user_id', 'inner');
		$this->db->WHERE('DD.discussion_id', $Did);
		$this->db->WHERE('DD.visible', 1);
		$this->db->limit(5);
		$this->db->order_by('DD.date_submitted', 'desc');
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function post($Did){

		#Create main query
		$this->db->select('DD.*, DT.topic, U.last_name, U.first_name, U.bio_image, U.company, U.title');
		$this->db->from('discussion AS DD');
		$this->db->join('discussion_topics AS DT', 'DT.discussion_id = DD.discussion_id', 'inner');
		$this->db->join('users AS U', 'U.id = DD.user_id', 'inner');
		$this->db->WHERE('DD.discussion_id', $Did);
		$this->db->WHERE('DD.visible', 1);
		$this->db->order_by('DD.date_submitted', 'desc');
		$query = $this->db->get();
		return $query->result_array();
	}
	
	function fetch_topics($id) {
	
		$this->db->select('DT.discussion_id, DT.topic');
		$this->db->from('discussion_topics AS DT');
		$this->db->join('industries AS IND', 'IND.industry_id = DT.industry_id', 'inner');
		$this->db->where("DT.industry_id IN (SELECT industry_id
FROM  user_industries
WHERE user_id =$id AND Is_Primary = 1)", NULL, FALSE);
		$this->db->WHERE('DT.Is_sub', 0);
		$this->db->order_by('DT.topic', 'asc');
		
		$records=$this->db->get();
		$data=array();
		
		$dropDownList[''] = 'Please Select'; 
		foreach($records->result() as $row) {
            $dropDownList[$row->discussion_id] = $row->topic;
        }

        return $dropDownList;
		 
		/*foreach ($records->result() as $row){
			$data[$row->discussion_id] = $row->topic;
		}
		return ($data);*/
	}
	function fetch_sub_topics($id) {
	
		$this->db->select('discussion_id, topic');
		$this->db->from('discussion_topics');
		$this->db->WHERE('Is_sub', 1);
		$this->db->order_by('topic', 'desc');
		
		$records=$this->db->get();
		$data=array();
		 
		foreach ($records->result() as $row){
			$data[$row->discussion_id] = $row->topic;
		}
		return ($data);
	}


	
	public function Industries($id){
		$this->db->select('UI.*, I.industry_id, I.industry, I.industry_desc');
		$this->db->from('user_industries AS UI');
		$this->db->join('industries AS I', 'I.industry_id = UI.industry_id', 'inner');
		$this->db->WHERE('UI.user_id', $id);
		$this->db->WHERE('UI.Is_Primary', 1);
		//$this->db->WHERE('I.industry_id !=', 99999);
		$this->db->order_by('I.industry', 'asc');
			
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function myQuestions($id){
		$this->db->select('DS.post_id, DS.post_title, DS.date_submitted, DT.topic');
		$this->db->from('discussion AS DS');
		$this->db->join('discussion_topics AS DT', 'DT.discussion_id = DS.discussion_id', 'inner');
		$this->db->where('DS.user_id', $id);
		$this->db->where("DT.industry_id IN (SELECT industry_id
FROM  user_industries WHERE user_id = $id AND Is_Primary = 1)", NULL, FALSE);
		$this->db->order_by('DS.date_submitted', 'desc');	
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function ck_advisor_response($id, $did){
		$this->db->select('post_id');
		$this->db->from('discussion_assignment');
		$this->db->WHERE('user_id', $id);
		$this->db->WHERE('post_id', $did);
		$query = $this->db->get();
		if($query->num_rows()> 0){
   			return 1; 
		} else {
   			return 0; 
		}
	}
	
	public function disCount($did){
		$this->db->select('discussion_id');
		$this->db->from('discussion');
		$this->db->WHERE('discussion_id', $did);
		$query = $this->db->get();
		
		if($query->num_rows()> 0){
   			return $query->num_rows(); 
		} else {
   			return 0; 
		}
	}
	public function disDate($did){
		$this->db->select('date_submitted');
		$this->db->from('discussion');
		$this->db->WHERE('discussion_id', $did);
		$this->db->order_by('date_submitted', 'desc');
		$this->db->limit(1);
		
		$query = $this->db->get();
		if($query->num_rows()> 0){
			$reault_array = $query->result_array();
			return $reault_array[0]['date_submitted'];
   			//return 'Show Recent Date';
		} else {
   			return 0; 
		}
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
	
	public function get_forum($id){
		$this->db->from($this->Topicstable);
		$this->db->where('discussion_id',$id);
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function get_post($fid){
		$this->db->select('DS.*, DT.topic, U.last_name, U.first_name, U.email, U.bio_image, U.company, U.title');
		$this->db->from('discussion AS DS');
		$this->db->join('discussion_topics AS DT', 'DT.discussion_id = DS.discussion_id', 'inner');
		$this->db->join('users AS U', 'U.id = DS.user_id', 'inner');
		$this->db->WHERE('DS.post_id', $fid);
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function get_threads($fid){
		$this->db->select('DIT.*, U.id, U.first_name, U.last_name, U.email, U.company, U.title, U.bio_image');
		$this->db->from('discussion_thread AS DIT');
		$this->db->join('users AS U', 'U.id = DIT.user_id', 'inner');
		$this->db->where('DIT.post_id', $fid);
		$this->db->where('DIT.visible', 1);
		$this->db->order_by('DIT.date_submitted', 'asc');
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function getReplies($did){
		$this->db->select('thread_id');
		$this->db->from('discussion_thread');
		$this->db->where('post_id', $did);
		$this->db->where('visible', 1);
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	public function get_user_by_id($id)
	{
		$this->db->from($this->Usertable);
		$this->db->where('id',$id);
		$query = $this->db->get();
		return $query->row();
	}

	
	public function saveQuestion($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}
	public function saveThread($data)
	{
		$this->db->insert($this->Threadtable, $data);
		return $this->db->insert_id();
	}
	
	public function saveNewCat($data)
	{
		$this->db->insert($this->Topicstable, $data);
		return $this->db->insert_id();
	}
	
	public function saveNewSubCat($data)
	{
		$this->db->insert($this->Topicstable, $data);
		return $this->db->insert_id();
	}



}
