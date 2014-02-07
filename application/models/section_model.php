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
         ->order_by('section.section, section.id')
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


   public function get_section_common_data($id) {
      $query=$this
         ->db
         ->select(array('section.section','section.id', 'catalog_lesson.title'))
         ->from('section')
         ->join('lesson','section.lesson_id=lesson.id','left')
         ->join('catalog_lesson','lesson.cataloglesson_id=catalog_lesson.id','left')
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


   public function newreg()
   {
      //insert new record in section table
      $data = array('id' => 'null' );
      $this->db->insert('section', $data);
      $sectionid = $this->db->insert_id();

      return $sectionid;
   }


   public function delreg($id)
   {      
      $this->db->delete('section', array('id' => $id)); 
   }


   public function cancelreg($id)
   {      
      $query = $this->db->select('section')
               ->where('id',$id)
               ->get('section');
      
      if ($query->num_rows() > 0)
      {
         $row = $query->row();

         if (is_null($row->section))
         {
            $this->db->delete('section', array('id'=>$id));
            return true;
         }
         else
         {
            return false;
         };

      };
   }



}