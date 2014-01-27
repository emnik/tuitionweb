<?php

class Details_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


	public function get_exam_data($examid){
		$query=$this->db
					->select('*')
					->from('exam_schedule')
					->where('id', $examid)
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
 

   public function get_employees() {
      $query=$this
         ->db
         ->select(array('id', 'CONCAT_WS(" ",`surname`,`name`) as employee'))
         ->where('active','1')
         ->get('employee');

      if ($query->num_rows() > 0) 
      {
        //return $query->result_array(); 
        foreach ($query->result_array() as $row) {
        	$data[$row['id']]=$row['employee'];
        }
         // foreach ($query->result_array() as $row) {
         // 	$data[]=array('id'=>$row['id'],'text'=>$row['employee']);
         // }
         return $data;
      }
      else 
      {
         return false;
      }

   }

   public function update_exam($id, $data)
   {
   		$this->db->where('id', $id);
		$this->db->update('exam_schedule', $data); 
   }

}