<?php

class Welcome_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


   public function get_schoolyears() {
   	$query=$this
   		->db
         ->from('term')
         ->select(array('term.id', 'term.name', 'term.active'))
   		->get();

   	if ($query->num_rows() > 0) 
		{
			foreach($query->result_array() as $row) 
			{
				$schoolyears[] = $row;
			}
			return $schoolyears;
		}
		else 
		{
			return false;
		}

   }

   public function get_school_data(){
      $query = $this->db->select(array('school.distinctive_title', 'school.facebookurl', 'school.twitterurl'))->get('school');
      if ($query->num_rows() > 0) 
      {
         $data = $query->row_array();
      } 
      else
      {
         $data = false;
      }
      return $data;
   }

   public function set_schoolyear($termid){
      $this->db->update('term', array('active'=>0));
      $this->db->where('id', $termid)->update('term', array('active'=>1));
   }


   public function get_student_names_ids($filter=null){

      if (!is_null($filter)){
         $this->db
         // ->select(array('registration.id', 'CONCAT_WS(" - ", CONCAT_WS(" ", registration.surname, registration.name), term.name) as stdname'))
         ->select(array('registration.id', 'CONCAT_WS(" ", registration.surname, registration.name)  as stdname', 'term.name as termname', 'term.id as termid'))
         ->from('registration')
         ->join('term', 'registration.term_id=term.id')
         //the next where clause is instead of the commented lines below because Codeigniter2 does not support group_start/end !
         // ->where("(`term`.`active`=1 AND (`registration`.`surname` LIKE '%".$filter."%' OR `registration`.`name` LIKE '%".$filter."%'))")
         ->where("((`registration`.`surname` LIKE '%".$filter."%' OR `registration`.`name` LIKE '%".$filter."%'))")
         // ->group_start()
            // ->like('registration.surname', $filter)
            // ->or_like('registration.name', $filter)
         // ->group_end()
         // ->where('term.active',1)
         ->order_by('stdname', 'ASC')
         ->order_by('termid', 'ASC');
      };
      
      $query=$this->db->get();

      if ($query->num_rows() > 0) 
      {
         foreach($query->result_array() as $row) 
         {
            $students[] = $row;
         }
         return $students;
      }
      else 
      {
         return false;
      }
   }


   public function get_termid(){
      //get active term
      $termid = $this->db->select('term.id')->where('term.active',1)->get('term')->row()->id;
      return $termid;
   }


}
