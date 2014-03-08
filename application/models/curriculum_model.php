<?php

class Curriculum_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


// ------------------------------------COMMON FUNCTIONS----------------------------------//
   public function get_classes() {
      $query=$this
         ->db
         ->select('*')
         ->order_by('priority')
         ->get('class');


      if ($query->num_rows() > 0) 
      {
         return $query->result_array(); 
      }
      else 
      {
         return false;
      }

   }


   public function get_courses($classid) {
      #for ajax to get courses based on class_id

      $query = $this->db->select(array('id', 'course'))
                        ->from('course')
                        ->where('course.class_id',$classid)
                        ->get();

      if ($query->num_rows() > 0)
      {
         foreach ($query->result() as $row)
         {
               $course_data[$row->id] = $row->course;
         };
         return $course_data;   
      }
      
      else
      {
         return false;
      };
   }

//--------------------------------END OF COMMON FUNCTIONS-----------------------------------//



}