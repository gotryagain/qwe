<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crisis_model extends CI_Model {

	 public function __construct()
     {
        $this->load->database();
     }


	public function addCrisisForm(){
		$data = array(
			'type' => $this->input->post('form_type'),
			'priority' => 1,
			'first_name' => $this->input->post('first_name'),
			'last_name' => $this->input->post('last_name'),
			'email' => $this->input->post('email'),
			'phone' => $this->input->post('phone'),
			'description' => $this->input->post('crisis_desc')
		);
		
    	return $this->db->insert('tickets', $data);
		/* SEND EMAIL TO ARETE */
	}

}
