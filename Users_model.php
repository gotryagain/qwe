<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends CI_Model {
	
	var $UsersTable = 'users';
	var $SelectionTable = 'user_industries';

	 public function __construct()
     {
        $this->load->database();
     }

	public function IndustrySelected($id, $indID){
		$this->db->select('industry_id');
		$this->db->from('user_industries');
		$this->db->where('user_id', $id);
		$this->db->where('industry_id', $indID);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function save_selection($data) {
			$this->db->insert($this->SelectionTable, $data);
			return $this->db->insert_id();
	}
	
	public function updateUser($where, $data){
		$this->db->update($this->UsersTable, $data, $where);
		return $this->db->affected_rows();
	}
	
	public function remove_selection($id, $IndId)
	{

		$this->db->where('user_id', $id);
		$this->db->where('industry_id', $IndId);
		$this->db->delete($this->SelectionTable);
	}
	
	public function resetPrimService($where, $data){
		$this->db->update($this->SelectionTable, $data, $where);
		return $this->db->affected_rows();
	}


}
