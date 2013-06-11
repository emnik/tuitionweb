<?php if (!defined('BASEPATH')) die();

class Card_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


   public function get_registration_data($id) {
   	$query=$this
   		->db
         ->select('*')
         ->from('registration')
         ->where('registration.id',$id)
         ->limit(1)
   		->get();

   	if ($query->num_rows() > 0) 
		{
   		 
         return $query->row_array(); 
		}
		else 
		{
			return false;
		}

   }



   public function get_regions() {
      $query=$this
         ->db
         ->select('*')
         ->get('region');

      if ($query->num_rows() > 0) 
      {
         return $query->result_array(); 
      }
      else 
      {
         return false;
      }

   }


   public function get_classes() {
      $query=$this
         ->db
         ->select('*')
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

   public function get_std_courses($id) {
      #for specific student to get courses from his class_id 
      $query=$this
         ->db
         ->select(array('course.id','course.course'))
         ->from('course')
         ->join('registration', 'registration.class_id=course.class_id')
         ->where('registration.id',$id)
         ->get();

      if ($query->num_rows() > 0) 
      {
         return $query->result_array(); 
      }
      else 
      {
         return false;
      }

   }

   public function update_student_data($student_data){

      //replacing empty array values with NULL
      foreach ($student_data as $i => $value) {
         if ($value === "") $student_data[$i] = null;
      };

      $this->db->where('registration.id',$student_data['id']);
      $data = array_diff($student_data, array('id'));
      $this->db->update('registration', $data);
   }

}
