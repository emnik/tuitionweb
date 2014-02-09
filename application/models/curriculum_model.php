<?php

class Curriculum_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


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

   public function get_lessons($classid, $courseid)
   {
      $query = $this->db->select(array('catalog_lesson.title', 'lesson.id', 'lesson.hours'))
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
               $lessons_data[$row->id] = array('title'=>$row->title, 'hours'=>$row->hours);
         };
         return $lessons_data;   
      }
      
      else
      {
         return false;
      };
      
   }

   
}