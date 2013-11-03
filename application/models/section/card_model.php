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
         ->select(array('section_program.id', 'section_program.day', 'section_program.classroom_id', 'section_program.start_tm', 'section_program.end_tm', 'section_program.duration'))
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


   public function get_lessons($classid, $courseid)
   {
      $query = $this->db->select(array('catalog_lesson.title', 'lesson.id'))
                        ->from('class')
                        ->join('course', 'course.class_id=class.id')
                        ->join('lesson', 'course.id = lesson.course_id')
                        ->join('catalog_lesson', 'lesson.cataloglesson_id = catalog_lesson.id')
                        ->where('class.id', $classid)
                        ->where('course.id', $courseid)
                        ->get();
      
      if ($query->num_rows() > 0)
      {
         foreach ($query->result() as $row)
         {
               $lessons_data[$row->id] = $row->title;
         };
         return $lessons_data;   
      }
      
      else
      {
         return false;
      };
      
   }

   public function get_tutors($classid, $courseid, $lessonid)
   {

      $query = $this->db->select(array('lesson_tutor.id', 'employee.nickname'))
                        ->from('class')
                        ->join('course', 'course.class_id=class.id')
                        ->join('lesson', 'course.id = lesson.course_id')
                        ->join('catalog_lesson', 'lesson.cataloglesson_id = catalog_lesson.id')
                        ->join('lesson_tutor', 'catalog_lesson.id = lesson_tutor.cataloglesson_id')
                        ->join('employee', 'lesson_tutor.employee_id = employee.id')
                        ->where('class.id', $classid)
                        ->where('course.id', $courseid)
                        ->where('lesson.id', $lessonid)
                        ->where('employee.is_tutor',1)
                        ->where('employee.active',1)
                        ->get();
      
      if ($query->num_rows() > 0)
      {
         foreach ($query->result() as $row)
         {
               $tutors_data[$row->id] = $row->nickname;
         };
         return $tutors_data;   
      }
      
      else
      {
         return false;
      };
      
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