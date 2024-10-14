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
         ->select(array('registration.id', 'CONCAT_WS(" ", registration.surname, registration.name)  as stdname', 'term.name as termname', 'term.id as termid'))
         ->from('registration')
         ->join('term', 'registration.term_id=term.id')
         ->join('contact', 'registration.id=contact.reg_id')
         ->like('registration.surname', $filter, 'after') // 'after' is like $filter% that starts searching from the beginning!
         ->or_like('registration.name', $filter, 'after')
         //also search telephones from either with the whole num or with the first or last digits!!!
         // for the first digits
         ->or_like('contact.std_mobile', $filter, 'after') 
         ->or_like('contact.fathers_mobile', $filter, 'after')
         ->or_like('contact.mothers_mobile', $filter, 'after')
         ->or_like('contact.home_tel', $filter, 'after') 
         ->or_like('contact.work_tel', $filter, 'after')
         // for the last digits
         ->or_like('contact.std_mobile', $filter, 'before') // 'before' is like %$filter that starts searching from the end!
         ->or_like('contact.fathers_mobile', $filter, 'before')  
         ->or_like('contact.mothers_mobile', $filter, 'before')
         ->or_like('contact.home_tel', $filter, 'before')
         ->or_like('contact.work_tel', $filter, 'before')
         // the next 3 lines are the older implementation...
         //->where("((`registration`.`surname` LIKE '%".$filter."%' OR `registration`.`name` LIKE '%".$filter."%'))")
         // ->where("(`registration`.`surname` LIKE '%".$filter."%' )") // searching in every position
         //->where("(`registration`.`surname` LIKE '".$filter."%' )") //searching from the start
         ->order_by('registration.surname', 'ASC')
         ->order_by('registration.name', 'ASC')
         ->order_by('termid', 'DESC');
      };
      
      //$test_query = $this->db->get_compiled_select();
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
