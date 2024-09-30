<?php

class Registrations_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


   public function get_registration_data() {
   	$query=$this
   		->db
         ->select(array('registration.id','registration.name','registration.surname','registration.std_book_no','class.class_name','course.course'))
         ->from('registration')
         ->join('term', 'registration.term_id=term.id')
         ->join('class','registration.class_id=class.id', 'left')
         ->join('course','registration.course_id=course.id', 'left')
         ->where('term.active', 1)
   		->get();

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

   public function checkRegidInSelectedSchYear($id) {
      //check for resubscribe - ONLY students from previous periods are to be resubscribed to a new period!
      //there is no meaning in resubscribing a student in the period which is already subscribed !!!
      $query=$this
      ->db
      ->select('registration.id')
      ->from('registration')
      ->join('term', 'registration.term_id=term.id')
      ->where('registration.id', $id)
      ->where('term.active', 1)
      ->get();

   if ($query->num_rows() > 0) 
      {
         return true; //the student selected is active in the selected schoolyear - 
      }
      else 
      {
         return false;
      }
   }


   public function newreg()
   {
      //get active term
      $termid = $this->db->select('term.id')->where('term.active',1)->get('term')->row()->id;
      
      //insert new record in registration table
      $data = array('id' => 'null', 'term_id' => $termid );
      $this->db->insert('registration', $data);
      $regid = $this->db->insert_id();

      //insert new record in contact table
      $contact_data = array('id' => 'null', 'reg_id' => $regid);
      $this->db->insert('contact', $contact_data);

      return $regid;
   }


   public function delreg($id)
   {      
      $this->db->delete('registration', array('id' => $id)); 
   }

   public function cancelreg($id)
   {      
      $query = $this->db->select('surname')
               ->where('id',$id)
               ->get('registration');
      
      if ($query->num_rows() > 0)
      {
         $row = $query->row();

         if (is_null($row->surname))
         {
            $this->db->delete('registration', array('id'=>$id));
            return true;
         }
         else
         {
            return false;
         };

      };
   }

   public function resubscribe($regid){
   
      //get active term
      $termid = $this->db->select('term.id')->where('term.active',1)->get('term')->row()->id;

      //copy anything from registration that remains the same
      $MySQL1 = "INSERT INTO `registration` ";
      $MySQL1 = $MySQL1."(`id`, `surname`, `name`, `address`, `region`, `class_id`, `course_id`, `month_price`, `confirm`, `notes`, `fathers_name`, `mothers_name`, `apy_receiver`, `apy_to_std`, `std_book_no`, `start_lessons_dt`, `del_lessons_dt`, `reg_dt`, `term_id`) ";
      $MySQL1 = $MySQL1."SELECT NULL, `surname`, `name`, `address`, `region`, NULL, NULL, NULL, `confirm`, NULL, `fathers_name`, `mothers_name`, `apy_receiver`, `apy_to_std`, NULL, NULL, NULL,  CURDATE(), '".$termid."' ";
      $MySQL1 = $MySQL1."FROM `registration` WHERE `id` = '".$regid.";' ";
      $this->db->query($MySQL1);

      $newregid = $this->db->insert_id();

      //copy anything from contact that remains the same
      $MySQL2 = "INSERT INTO `contact` ";
      $MySQL2 = $MySQL2."(`id`, `mothers_mobile`, `fathers_mobile`, `std_mobile`, `home_tel`, `work_tel`, `email`, `reg_id`) ";
      $MySQL2 = $MySQL2."SELECT NULL, `mothers_mobile`, `fathers_mobile`, `std_mobile`, `home_tel`, `work_tel`, `email` '".$newregid."' ";
      $MySQL2 = $MySQL2."FROM `contact` WHERE `reg_id` = '".$regid."' ";
      $this->db->query($MySQL2);

      return $newregid;
   }

}
