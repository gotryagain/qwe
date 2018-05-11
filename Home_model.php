<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_model extends CI_Model {

	 public function __construct()
     {
        $this->load->database();
     }


	public function myAccount($id) {
			$this->db->select('*');
			$this->db->from('users');
			$this->db->where('id', $id);
			$query = $this->db->get();
			return $query->result_array();
	}
	
	public function getMyDiscussions($id){
		$this->db->select('DS.*, DT.topic');
		$this->db->from('discussion_assignment AS DA');
		$this->db->join('discussion AS DS', 'ON DS.post_id = DA.post_id', 'inner');
		$this->db->join('discussion_topics AS DT', 'DT.discussion_id = DS.discussion_id', 'inner');
		$this->db->WHERE('DA.user_id', $id);
		$this->db->WHERE('DS.visible', 1);
		$this->db->order_by('DS.date_submitted', 'desc');
			
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
}
