<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Emergency_model extends CI_Model {
	
	var $table = 'tickets';
	var $ticketHistoryTable = 'ticket_history';

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
	
	public function saveTicket($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}
	public function saveTicketHistory($data){
		$this->db->insert($this->ticketHistoryTable, $data);
		return $this->db->insert_id();
	}
	public function getCrisisTickets($id){
		$this->db->select('ticket_id, ticket_number, date_created');
		$this->db->from('tickets');
		$this->db->where('user_id', $id);
		$this->db->where('ticket_status !=', 4);
		$this->db->order_by('date_created', 'asc');
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function get_ticket($TID){
		$this->db->select('T.*, TC.category, TP.priority AS Prio_name, TT.ticket_type');
		$this->db->from('tickets AS T');
		$this->db->join('ticket_category AS TC', 'TC.cat_id = T.cat_id', 'inner');
		$this->db->join('ticket_priority AS TP', 'TP.priority_id = T.priority_id', 'inner');
		$this->db->join('ticket_types AS TT', 'TT.ticket_type_id = T.ticket_type_id', 'inner');
		$this->db->where('T.ticket_id', $TID);	
		$query = $this->db->get();
		return $query->result_array();
	}
	public function ticketHistory($TNum){
		$this->db->select('*');
		$this->db->from('ticket_history');
		$this->db->where('ticket_number', $TNum);
		$this->db->order_by('date_created', 'asc');	
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function ticketNotes($Tid){
		$this->db->select('TN.*, U.first_name, U.last_name, U.company');
		$this->db->from('ticket_notes AS TN');
		$this->db->join('users AS U', 'U.id = TN.user_id', 'inner');
		$this->db->where('TN.ticket_id', $Tid);
		$this->db->order_by('TN.date_submitted', 'asc');	
		$query = $this->db->get();
		return $query->result_array();
	}
	
	function fetch_category_type() {
	
		$this->db->select('cat_id, category');
		$records=$this->db->get('ticket_category');
		$data=array();
		 
		foreach ($records->result() as $row){
			$data[$row->cat_id] = $row->category;
		}
		return ($data);
	}
	
	function fetch_priority_type() {
	
		$this->db->select('priority_id, priority');
		$records = $this->db->get('ticket_priority');
		$data=array();
		 
		foreach ($records->result() as $row){
			$data[$row->priority_id] = $row->priority;
		}
		return ($data);
	}



}
