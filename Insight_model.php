<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Insight_model extends CI_Model {

	var $PopTable = 'popular_insights';
	var $Threadtable = 'insight_thread';
	var $Usertable = 'users';
	
	 public function __construct()
     {
		$this->load->library(array('session','pagination'));
        $this->load->helper('url');
        $this->load->database();
     }


	public function Popular($id){
		$this->db->select('IN.insight_id, I.industry, IN.title, INR.insight_rt_total_points');
		$this->db->from('insights AS IN');
		$this->db->join('insight_rating AS INR', 'INR.insight_rt_item_id = IN.insight_id', 'inner');
		$this->db->join('industries AS I', 'I.industry_id = IN.industry_id', 'inner');
		$this->db->WHERE("IN.industry_id IN (SELECT industry_id
FROM  user_industries
WHERE user_id =$id AND Is_Primary = 1)", NULL, FALSE);
		$this->db->order_by('INR.insight_rt_total_points', 'DESC');
		$this->db->limit(5);
			
		$query = $this->db->get();
		return $query->result_array();
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
	/* Get all articles for this user */
	public function getArticles($id){

		#Create main query
		$this->db->select('ART.insight_id, ART.industry_id, ART.title, ART.short_desc, ART.date_created, INDU.industry, U.first_name, U.last_name, U.bio_image');
		$this->db->from('insights AS ART');
		$this->db->join('users AS U', 'U.id = ART.user_id', 'inner');
		$this->db->join('industries AS INDU', 'INDU.industry_id = ART.industry_id', 'inner');
		$this->db->where("ART.industry_id IN (SELECT industry_id
FROM  user_industries
WHERE user_id =$id)", NULL, FALSE);
		$this->db->order_by('ART.date_created', 'desc');

		$query = $this->db->get();
		return $query->result_array();
	}
	
	/***** PAGINATION *******/
	public function allrecord($title, $id){
        if(!empty($title)){
            $this->db->or_like('ART.title', $title);
			$this->db->or_like('ART.short_desc', $title);
        }
        $this->db->select('ART.insight_id, ART.industry_id, ART.title, ART.short_desc, ART.date_created, INDU.industry, U.first_name, U.last_name, U.bio_image');
		$this->db->from('insights AS ART');
		$this->db->join('users AS U', 'U.id = ART.user_id', 'inner');
		$this->db->join('industries AS INDU', 'INDU.industry_id = ART.industry_id', 'inner');
		$this->db->where("ART.industry_id IN (SELECT industry_id
FROM  user_industries
WHERE user_id =$id)", NULL, FALSE);
        $rs = $this->db->get();
        return $rs->num_rows();
    }
    
    public function data_list($limit,$offset,$title, $id){
        if(!empty($title)){
            $this->db->or_like('ART.title',$title);
			$this->db->or_like('ART.short_desc', $title);
        }
        $this->db->select('ART.insight_id, ART.industry_id, ART.title, ART.short_desc, ART.date_created, INDU.industry, U.first_name, U.last_name, U.bio_image');
		$this->db->from('insights AS ART');
		$this->db->join('users AS U', 'U.id = ART.user_id', 'inner');
		$this->db->join('industries AS INDU', 'INDU.industry_id = ART.industry_id', 'inner');
		$this->db->where("ART.industry_id IN (SELECT industry_id
FROM  user_industries
WHERE user_id =$id AND Is_Primary = 1)", NULL, FALSE);
        $this->db->order_by('ART.date_created','desc');
        $this->db->limit($limit,$offset);
        $rs = $this->db->get();
        return $rs->result_array();
    }
	/*********** END PAGINATION **************/
	
	

	
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
	public function get_advisor_by_id($id)
	{
		$this->db->from($this->Usertable);
		$this->db->where('id',$id);
		$query = $this->db->get();
		return $query->row();
	}
	
	public function get_article($id){
		$this->db->select('ART.insight_id, ART.title, ART.text, ART.date_created, IND.industry, U.first_name, U.last_name, U.bio_image');
			$this->db->from('insights AS ART');
			$this->db->join('users AS U', 'U.id = ART.user_id', 'inner');
			$this->db->join('industries AS IND', 'IND.industry_id = ART.industry_id', 'inner');
			$this->db->WHERE('ART.insight_id', $id);
			$query = $this->db->get();
			return $query->result_array();
	}
	
	public function get_threads($fid){
		$this->db->select('INST.*, U.id, U.first_name, U.last_name, U.email, U.company, U.bio_image');
		$this->db->from('insight_thread AS INST');
		$this->db->join('users AS U', 'U.id = INST.user_id', 'inner');
		$this->db->where('INST.insight_id', $fid);
		$this->db->where('INST.visible', 1);
		$this->db->order_by('INST.date_submitted', 'asc');
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function getReplies($did){
		$this->db->select('thread_id');
		$this->db->from('insight_thread');
		$this->db->where('insight_id', $did);
		$this->db->where('visible', 1);
		$query = $this->db->get();
		return $query->num_rows();
	}
	

	/*** RATING ***/
	function get_rate_numbers($fid) {
        $rate_num = $this->db->query("select * from insight_rating where insight_rt_item_id='$fid'")->num_rows();
        return $rate_num;
    }
	function get_user_numrate($fid,$userid) {
        $rate_num = $this->db->query("
        select * from insight_rating
        INNER JOIN insight_rating_users ON insight_rtu_rate_id = insight_rt_id
        where insight_rt_item_id ='$fid'
        AND  insight_rtu_user_id ='$userid'");
        if ($rate_num->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }
	/*  This function get ratings related to specfic item id. */

    function get_insight_rate($fid) {
       $query = $this->db->query("select * from insight_rating where insight_rt_item_id='$fid'");
        if ($query->num_rows() > 0)
        {

			$result = $query->row();
            return $result;

        }else{
            return false;
        }
    }
    /*  This function insert ratings with item id and user id. */
	function insert_rate($id,$rate,$user_id) {
        $this->db->query("insert into insight_rating values('','$id','1','$rate')");
		//$last_id = $this->db->mysql_insert_id();
		$last_id = $this->db->insert_id();
		//console.log($last_id);
        $this->db->query("insert into insight_rating_users values('',$last_id,$user_id,UNIX_TIMESTAMP())");
        return true;
    }
	function update_rate($id,$rate,$user_id) {

        $update_rate1 = $this->db->query("select * from insight_rating where insight_rt_id='$id'")->row();
        $total_rates = $update_rate1->insight_rt_total_rates + 1;
		
        $total_points= $update_rate1->insight_rt_total_points + $rate;
        $rate_id= $update_rate1->insight_rt_id;
        $this->db->query("update insight_rating set insight_rt_total_rates='$total_rates', insight_rt_total_points='$total_points' where insight_rt_id='$rate_id'");
        $this->db->query("insert into insight_rating_users values('','$id',$user_id,UNIX_TIMESTAMP())");

        return true;
    }
	
	public function saveThread($data)
	{
		$this->db->insert($this->Threadtable, $data);
		return $this->db->insert_id();
	}
}
