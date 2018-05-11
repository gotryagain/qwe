<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Suggestion_model extends CI_Model {

	var $table = 'suggestions';
	
	 public function __construct()
     {
        $this->load->database();
     }
	 
	 public function saveSuggestion($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}
}
