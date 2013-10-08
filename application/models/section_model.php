<?php

class Section_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


   public function get_sections_data() {

   	$this->load->model('welcome_model');
   	$schyear=$this->welcome_model->get_selected_startschyear();

   	$query=$this
   		->db
         ->select(array('section.id','section.section', 'employee.name', 'catalog_lesson.title','course.course','class.class_name'))
         ->from('section')
         ->join('lesson_tutor', 'section.tutor_id=lesson_tutor.id')
         ->join('employee', 'lesson_tutor.employee_id=employee.id')
         ->join('catalog_lesson', 'lesson_tutor.cataloglesson_id=catalog_lesson.id')
         ->join('lesson','section.lesson_id=lesson.id')
         ->join('course','lesson.course_id=course.id')
         ->join('class', 'course.class_id=class.id')
         ->where('section.schoolyear', $schyear)
     	 ->get();

   	if ($query->num_rows() > 0) 
		{
			foreach($query->result_array() as $row) 
			{
				$section[] = $row;
			}
			return $section;
		}
		else 
		{
			return false;
		}

   }


   public function delreg($id)
   {      
      $this->db->delete('section', array('id' => $id)); 
   }



}