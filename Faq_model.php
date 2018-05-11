<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Faq_model extends CI_Model {

	var $table = 'faq';
	
	 public function __construct()
     {
		$this->load->library(array('session','pagination'));
        $this->load->helper('url');
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
	
	public function faqSubTitles($did){

		$this->db->select('*');
		$this->db->from('discussion_topics');
		$this->db->where("parent_discussion_id", $did);
		$this->db->order_by('discussion_id', 'asc');

		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function faqs($Did){

		#Create main query
		$this->db->select('faq.*, DT.topic');
		$this->db->from('faq AS faq');
		$this->db->join('discussion_topics AS DT', 'DT.discussion_id = faq.discussion_id', 'inner');
		$this->db->WHERE('faq.discussion_id', $Did);
		$this->db->WHERE('faq.visible', 1);
		$this->db->order_by('faq.date_submitted', 'desc');
		$query = $this->db->get();
		return $query->result_array();
	}
	function fetch_topics($id) {
	
		$this->db->select('DT.discussion_id, DT.topic');
		$this->db->from('discussion_topics AS DT');
		$this->db->join('industries AS IND', 'IND.industry_id = DT.industry_id', 'inner');
		$this->db->where("DT.industry_id IN (SELECT industry_id
FROM  user_industries
WHERE user_id =$id)", NULL, FALSE);
		$this->db->order_by('DT.discussion_id', 'asc');
		
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
	
	
	/***** PAGINATION *******/
	public function allrecord($title, $Did){
        if(!empty($title)){
            $this->db->like('faq.title', $title);
        }
        $this->db->select('faq.*, DT.topic');
		$this->db->from('faq AS faq');
		$this->db->join('discussion_topics AS DT', 'DT.discussion_id = faq.discussion_id', 'inner');
		$this->db->WHERE('faq.discussion_id', $Did);
		$this->db->WHERE('faq.visible', 1);
		$this->db->order_by('faq.date_submitted', 'desc');
        $rs = $this->db->get();
        return $rs->num_rows();
    }
    
    public function data_list($limit,$offset,$title, $Did){
        if(!empty($title)){
            $this->db->like('faq.title', $title);
        }
        $this->db->select('faq.*, DT.topic');
		$this->db->from('faq AS faq');
		$this->db->join('discussion_topics AS DT', 'DT.discussion_id = faq.discussion_id', 'inner');
		$this->db->WHERE('faq.discussion_id', $Did);
		$this->db->WHERE('faq.visible', 1);
        $this->db->limit($limit,$offset);
        $rs = $this->db->get();
        return $rs->result_array();
    }
	/*********** END PAGINATION **************/
	
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
		$this->db->select('faq.faq_id, faq.title, faq.date_submitted, DT.topic');
		$this->db->from('faq AS faq');
		$this->db->join('discussion_topics AS DT', 'DT.discussion_id = faq.discussion_id', 'inner');
		$this->db->WHERE('faq.user_id', $id);
		$this->db->order_by('faq.date_submitted', 'desc');	
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
	
	public function get_faq($fid){
		$this->db->select('faq.*, DT.topic, U.last_name, U.first_name, U.bio_image');
		$this->db->from('faq AS faq');
		$this->db->join('discussion_topics AS DT', 'DT.discussion_id = faq.discussion_id', 'inner');
		$this->db->join('users AS U', 'U.id = faq.user_id', 'inner');
		$this->db->WHERE('faq.faq_id', $fid);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function get_faq_attachments($FID){
		$this->db->select('*');
		$this->db->from('faq_attachments');
		$this->db->WHERE('faq_id', $FID);
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function get_threads($fid){
		$this->db->select('FAT.*, U.first_name, U.last_name, U.email, U.company, U.bio_image');
		$this->db->from('faq_thread AS FAT');
		$this->db->join('users AS U', 'U.id = FAT.user_id', 'inner');
		$this->db->where('FAT.faq_id', $fid);
		$this->db->order_by('FAT.date_submitted', 'desc');
		$query = $this->db->get();
		return $query->result_array();;
	}
	
	public function saveFAQ($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}
	
	/*** RATING ***/
	function get_rate_numbers($fid) {
        $rate_num = $this->db->query("select * from faq_rating where faq_rt_item_id='$fid'")->num_rows();
        return $rate_num;
    }
	function get_user_numrate($fid,$userid) {
        $rate_num = $this->db->query("
        select * from faq_rating
        INNER JOIN faq_rating_users ON faq_rtu_rate_id = faq_rt_id
        where faq_rt_item_id ='$fid'
        AND  faq_rtu_user_id ='$userid'");
        if ($rate_num->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }
	
	/*  This function get ratings related to specfic item id. */

    function get_article_rate($fid) {
		$query = $this->db->query("select * from faq_rating where faq_rt_item_id='$fid'");
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
        $this->db->query("insert into faq_rating values('','$id','1','$rate')");
		$last_id = $this->db->insert_id();
        $this->db->query("insert into faq_rating_users values('',$last_id,$user_id,UNIX_TIMESTAMP())");
        return true;
    }
	
	function update_rate($id,$rate,$user_id) {

        $update_rate1 = $this->db->query("select * from faq_rating where faq_rt_id='$id'")->row();
        $total_rates = $update_rate1->faq_rt_total_rates + 1;
        $total_points= $update_rate1->faq_rt_total_points + $rate;
        $rate_id= $update_rate1->faq_rt_id;
        $this->db->query("update faq_rating set faq_rt_total_rates='$total_rates', faq_rt_total_points='$total_points' where faq_rt_id='$rate_id'");
        $this->db->query("insert into faq_rating_users values('','$id',$user_id,UNIX_TIMESTAMP())");

        return true;
    }
	
	public function Popular($id){
		$this->db->select('faq.faq_id, faq.parent_discussion_id, DT1.topic AS ParentTopic, DT2.topic AS SubTopic, faq.discussion_id, faq.title, FAQT.faq_rt_total_points');
		$this->db->from('faq AS faq');
		$this->db->join('faq_rating AS FAQT', 'FAQT.faq_rt_item_id = faq.faq_id', 'inner');
		$this->db->join('discussion_topics AS DT1', 'DT1.discussion_id = faq.parent_discussion_id', 'inner');
		$this->db->join('discussion_topics AS DT2', 'DT2.discussion_id = faq.discussion_id', 'inner');
		$this->db->WHERE('faq.visible', 1);
		$this->db->where("DT1.industry_id IN (SELECT industry_id
FROM  user_industries
WHERE user_id =$id) OR DT1.industry_id = 99999", NULL, FALSE);
		$this->db->order_by('FAQT.faq_rt_total_points', 'DESC');
		$this->db->limit(5);
			
		$query = $this->db->get();
		return $query->result_array();
	}



}
