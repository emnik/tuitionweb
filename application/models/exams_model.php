<?php

class Exams_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


	public function get_exams_data($startsch){
      //get the exams data that have participants mapped
		$query_with_participants=$this->db
                  ->select(array('exam_schedule.id','exam_schedule.date', 'class.class_name', 'course.course', 'catalog_lesson.title', 'count(std_lesson.id) as participantsnum', 'description'))
                  ->from('exam_schedule')
                  ->join('exam_participant', 'exam_schedule.id=exam_participant.exam_id')
                  ->join('section', 'exam_participant.section_id = section.id')
                  ->join('std_lesson', 'section.id=std_lesson.section_id')
                  ->join('class', 'exam_schedule.class_id=class.id')
                  ->join('course', 'exam_schedule.course_id=course.id')
                  ->join('lesson', 'exam_schedule.lesson_id=lesson.id')
                  ->join('catalog_lesson', 'lesson.cataloglesson_id=catalog_lesson.id')
                  ->where('exam_schedule.startschyear', $startsch)
                  ->group_by('exam_schedule.id')
                  ->get();

      if ($query_with_participants->num_rows() > 0) 
		{
			foreach($query_with_participants->result_array() as $row) 
			{
				$data[] = $row;
            //get the exams ids in a new table
            $with_participants_ids[]=$row['id'];
			}
      }
      else
      {
         $data=array();
      }
   
       //get the exams ids that don't have participants mapped 
       $this->db->select(array('exam_schedule.id','exam_schedule.date', 'class.class_name', 'course.course', 'catalog_lesson.title', 'description'))
      ->from('exam_schedule')
      ->join('class', 'exam_schedule.class_id=class.id')
      ->join('course', 'exam_schedule.course_id=course.id')
      ->join('lesson', 'exam_schedule.lesson_id=lesson.id')
      ->join('catalog_lesson', 'lesson.cataloglesson_id=catalog_lesson.id')
      ->where('exam_schedule.startschyear', $startsch);
      if(!empty($with_participants_ids))
      {
         $this->db->where_not_in('exam_schedule.id', $with_participants_ids);
      }
      $query_without_participants=$this->db->get();

      if($query_without_participants->num_rows()>0)
      {
         $c=0;
         foreach ($query_without_participants->result_array() as $row) 
         {
            $moredata[$c]=$row;
            $moredata[$c]['participantsnum']='-';
            $c++;
         }
      }
      else
      {
         $moredata=array();
      }

      //if the 2 data tables are empty return false
		if (empty($data) && empty($moredata))
      {
         return false;
      }
		else 
		{
         //merge the 2 data tables
         $alldata = array_merge($data,$moredata); 
         return $alldata;
		}
	}


   public function newexam($startsch)
   {
      //insert new record in registration table
      $data = array('id' => 'null', 'startschyear'=> $startsch );
      $this->db->insert('exam_schedule', $data);
      $examid = $this->db->insert_id();

      return $examid;
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