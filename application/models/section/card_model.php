<?php if (!defined('BASEPATH')) die();

class Card_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }

   public function get_section_data($id) {
   	$query=$this
   		->db
         ->select(array('section.id','section.section', 'section.tutor_id', 'section.class_id', 'section.course_id', 'section.lesson_id'))
         ->from('section')
         ->where('section.id',$id)
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


   public function get_section_program($id) {
      $query=$this
         ->db
         ->select(array('section_program.day', 'section_program.classroom_id', 'section_program.start_tm', 'section_program.end_tm', 'section_program.duration'))
         ->from('section_program')
         ->where('section_id',$id)
         ->get();

      if ($query->num_rows() > 0) 
      {
         foreach($query->result_array() as $row) 
         {
            $section_program[] = $row;
         }
         return $section_program; 
      }
      else 
      {
         return false;
      }

   }

   // public function update_section_data($section_data, $id){

   //    //replacing empty array values with NULL
   //    foreach ($employee_data as $i => $value) {
   //       if ($value === "") $employee_data[$i] = null;
   //    };

   //    $this->db->where('employee.id',$id);
   //    $this->db->update('employee', $employee_data);
   // }


}