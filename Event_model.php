<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Event_model extends CI_Model {

	 public function __construct()
     {
        $this->load->database();
     }


	/* Distinct Event Years */
	public function eventYear() {
			$this->db->select('DISTINCT(YEAR(event_date)) AS YEAR');
			$this->db->from('event');
			$this->db->order_by('event_date', 'asc');
			$query = $this->db->get();
			return $query->result_array();
	}
	public function eventPerYear($YR) {
			$this->db->select('e.event_id, e.event_title, e.event_short_desc,  UNIX_TIMESTAMP(e.event_date) as event_date, UNIX_TIMESTAMP(e.event_end) as event_end, event_venue, event_address, event_city, event_state, event_zip');
			$this->db->from('event AS e');
			$this->db->where('YEAR(event_date)', $YR);
			//$this->db->where('e.event_end > DATE_SUB( NOW( ) , INTERVAL 1 DAY ) ');
			$this->db->order_by('e.event_date', 'asc');
			$query = $this->db->get();
			return $query->result_array();
	}
	
	function count_eventPerYear($YR){
		$this->db->select('e.event_id');
		$this->db->from('event AS e');
		$this->db->where('YEAR(event_date)', $YR);
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	
	/*public function mainTitles() {
			$this->db->select('event_type_id, event_type');
			$this->db->from('event_types');
			$this->db->where('placement', 1);
			$this->db->order_by('event_type', 'asc');
			$query = $this->db->get();
			return $query->result_array();
	}
	public function mainEvent($EID) {

			$this->db->select('e.event_id, e.event_title, e.event_short_desc,  UNIX_TIMESTAMP(e.event_date) as event_date, UNIX_TIMESTAMP(e.event_end) as event_end, et.event_type');
			$this->db->from('event AS e');
			$this->db->join('event_types AS et', 'et.event_type_id = e.event_type_id', 'inner');
			$this->db->WHERE('e.event_type_id', $EID);
			$this->db->where('e.event_end > DATE_SUB( NOW( ) , INTERVAL 1 DAY ) ');
			$this->db->order_by('et.event_type', 'asc');
			$this->db->order_by('e.event_date', 'asc');
			
			$query = $this->db->get();
			return $query->result_array();
	}*/
	public function get_event($ID){
		$this->db->select('event_id, event_title, event_desc, event_link, UNIX_TIMESTAMP(event_date) as event_date, UNIX_TIMESTAMP(event_end) as event_end, event_venue, event_address, event_city, event_state, event_zip');
		$this->db->from('event');
		$this->db->WHERE('event_id', $ID);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function get_event_attachments($EID){
		$this->db->select('*');
		$this->db->from('event_attachments');
		$this->db->WHERE('event_id', $EID);
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function sideBarTitles() {
			$this->db->select('event_type_id, event_type');
			$this->db->from('event_types');
			$this->db->where('placement', 2);
			$query = $this->db->get();
			return $query->result_array();
	}
		
	public function sideBar($EID) {

			$this->db->select('e.event_id, e.event_title, e.event_short_desc,  UNIX_TIMESTAMP(e.event_date) as event_date, UNIX_TIMESTAMP(e.event_end) as event_end, et.event_type');
			$this->db->from('event AS e');
			$this->db->join('event_types AS et', 'et.event_type_id = e.event_type_id', 'inner');
			$this->db->WHERE('e.event_type_id', $EID);
			$this->db->order_by('et.event_type', 'desc');
			$this->db->order_by('e.event_date', 'asc');
			
			$query = $this->db->get();
			return $query->result_array();
		}

}
