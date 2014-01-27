<?php

class Exams_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


	public function get_exams_data($startsch){
		$query=$this->db
                  ->select(array('exam_schedule.id','exam_schedule.date', 'class.class_name', 'course.course', 'catalog_lesson.title', 'start_tm', 'end_tm', 'notes', 'supervisor_ids'))
                  ->from('exam_schedule')
                  ->join('class', 'exam_schedule.class_id=class.id')
                  ->join('course', 'exam_schedule.course_id=course.id')
                  ->join('lesson', 'exam_schedule.lesson_id=lesson.id')
                  ->join('catalog_lesson', 'lesson.cataloglesson_id=catalog_lesson.id')
                  ->where('exam_schedule.startschyear', $startsch)
                  ->get();

	   	if ($query->num_rows() > 0) 
		{
			foreach($query->result_array() as $row) 
			{
				$data[] = $row;
			}
			return $data;
		}
		else 
		{
			return false;
		}
	}


   public function newexam($startsch)
   {
      //insert new record in registration table
      $data = array('id' => 'null', 'startschyear'=> $startsch );
      $this->db->insert('exam_schedule', $data);
      $regid = $this->db->insert_id();

      //insert new record in contact table
      // $contact_data = array('id' => 'null', 'reg_id' => $regid);
      // $this->db->insert('contact', $contact_data);

      return $regid;
   }

   public function delexam($id)
   {      
      $this->db->delete('exam_schedule', array('id' => $id)); 
   }


   public function cancelexam($id)
   {      
      $query = $this->db->select('lesson_id')
               ->where('id',$id)
               ->get('exam_schedule');
      
      if ($query->num_rows() > 0)
      {
         $row = $query->row();

         if (is_null($row->lesson_id))
         {
            $this->db->delete('exam_schedule', array('id'=>$id));
            return true;
         }
         else
         {
            return false;
         };

      };
   }


}