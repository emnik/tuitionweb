<?php

class Courselessons_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }




   public function get_lessons($classid, $courseid)
   {
      $query = $this->db->select(array('catalog_lesson.title', 'cataloglesson_id', 'lesson.id', 'lesson.hours'))
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
               $lessons_data[$row->id] = array('title'=>$row->title, 'hours'=>$row->hours, 'cataloglesson_id'=>$row->cataloglesson_id);
         };
         return $lessons_data;   
      }
      
      else
      {
         return false;
      };
      
   }

   public function get_lessontitles()
   {
      $query=$this
         ->db
         ->select('*')
         ->order_by('title')
         ->get('catalog_lesson');


      if ($query->num_rows() > 0) 
      {
         foreach ($query->result_array() as $row) {
         	$data[$row['id']]=$row['title'];
         }
         return $data;
      }
      else 
      {
         return false;
      }   	
   }

   public function insertupdatedata($coursedata, $lessondata)
   {
   	$insertedcourseids = array();
   	foreach ($coursedata as $key => $value) {
   		$id = $value['id'];
   		if ($id>0){
   			$data = $value;
   			unset($data['id']);
   			$this->db->where('id', $id);
			$this->db->update('course', $data); 
   		}
   		else
   		{
   			$data = $value;
   			unset($data['id']);
			$this->db->insert('course', $data); 
			$insertedcourseids[$id]=$this->db->insert_id();
   		}
   	}
   	foreach ($lessondata as $key => $tmpdata) {
   		foreach ($tmpdata as $subkey => $value) {
   			if($subkey=='course_id' && $value<0){
   				$lessondata[$key]['course_id'] = $insertedcourseids[$value];
   			}
   		}
   	}
   	foreach ($lessondata as $key => $value) {
   		$id = $value['id'];
   		if ($id>0){
   			$data = $value;
   			unset($data['id']);
   			$this->db->where('id', $id);
			$this->db->update('lesson', $data); 
   		}
   		else
   		{
   			$data = $value;
   			unset($data['id']);
			$this->db->insert('lesson', $data); 
   		}
   	}
   }
   


   public function dellesson($id)
   {
	if($this->db->where('id', $id)->delete('lesson'))
		{
			return array('success'=>'true');
   	}
   }


   public function delcourse($id)
   {
	if($this->db->where('id', $id)->delete('course'))
		{
			return array('success'=>'true');
   	}
   }


}