<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Training_model extends CI_Model {

	 public function __construct()
     {
        $this->load->database();
     }


	public function mainSections() {
			$this->db->select('training_cat, category');
			$this->db->from('training_categories');
			$this->db->order_by('training_cat', 'asc');
			$query = $this->db->get();
			return $query->result_array();
	}
	public function catTraining($CID) {

			$this->db->select('T.*, TRT.training_type AS training_type_name, TRT.icon AS TypeIcon, EXP.name, EXP.title, EXP.img');
			$this->db->from('training AS T');
			$this->db->join('training_types AS TRT', 'TRT.training_type_id = T.training_type_id', 'inner');
			$this->db->join('experts AS EXP', 'EXP.expert_id = T.expert_id', 'inner');
			$this->db->WHERE('T.training_cat', $CID);
			$this->db->order_by('T.start_date', 'asc');
			
			$query = $this->db->get();
			return $query->result_array();
	}
	
	public function get_training($ID){
		$this->db->select('T.*, TRT.training_type AS training_type_name, TRT.icon AS TypeIcon, EXP.name, EXP.title, EXP.img');
		$this->db->from('training AS T');
		$this->db->join('training_types AS TRT', 'TRT.training_type_id = T.training_type_id', 'inner');
		$this->db->join('experts AS EXP', 'EXP.expert_id = T.expert_id', 'inner');
		$this->db->WHERE('T.training_id', $ID);
		$this->db->order_by('T.start_date', 'asc');
			
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function sideExperts() {
			$this->db->select('XES.*, EX.name, EX.title, EX.img');
			$this->db->from('x_expert_by_section AS XES');
			$this->db->join('experts AS EX', 'XES.expert_id = EX.expert_id', 'inner');
			$this->db->where('XES.section', 'training');
			$query = $this->db->get();
			return $query->result_array();
	}
		
	public function sideBar($EID) {

			$this->db->select('e.event_id, e.event_title, e.event_short_desc,  UNIX_TIMESTAMP(e.event_date) as event_date, et.event_type');
			$this->db->from('event AS e');
			$this->db->join('event_types AS et', 'et.event_type_id = e.event_type_id', 'inner');
			$this->db->WHERE('e.event_type_id', $EID);
			$this->db->order_by('et.event_type', 'desc');
			$this->db->order_by('e.event_date', 'asc');
			
			$query = $this->db->get();
			return $query->result_array();
		}
		
	public function get_attachments($ID){
		$this->db->select('attach_id, filename, date_submitted');
		$this->db->from('attachments');
		$this->db->WHERE('post_id', $ID);
		$this->db->WHERE('post_type', 'training');
		$this->db->order_by('date_submitted', 'asc');
		$query = $this->db->get();
		return $query->result_array();
	}

}
