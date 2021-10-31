<?php

class Section_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


   public function get_sections_data() {

   	$query=$this
   		->db
         ->select(array('section.id','section.section', 'employee.name', 'catalog_lesson.title','course.course','class.class_name'))
         ->from('section')
         ->join('term', 'section.term_id=term.id')
         ->join('lesson_tutor', 'section.tutor_id=lesson_tutor.id')
         ->join('employee', 'lesson_tutor.employee_id=employee.id')
         ->join('catalog_lesson', 'lesson_tutor.cataloglesson_id=catalog_lesson.id')
         ->join('lesson','section.lesson_id=lesson.id')
         ->join('course','lesson.course_id=course.id')
         ->join('class', 'course.class_id=class.id')
         ->where('term.active',1)
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
      //get active term
      $termid = $this->db->select('term.id')->where('term.active',1)->get('term')->row()->id;
      
      //ΕΙΣΑΓΩΓΗ ΣΧΟΛΙΚΟΎ ΕΤΟΥΣ ΓΙΑ ΣΥΜΒΑΤΟΤΗΤΑ - ΠΡΟΣ ΑΦΑΙΡΕΣΗ...
      $schoolyear = $this->db->select('YEAR(`term`.`start`) AS year')->where('term.active',1)->get('term')->row()->year;

      //insert new record in section table
      $data = array('id' => 'null', 'term_id'=> $termid, 'schoolyear'=>$schoolyear); //ΠΡΟΣ  ΑΦΑΙΡΕΣΗ... (Αντικατάσταση με την ακόλουθη γραμμή.)
      // $data = array('id' => 'null', 'term_id'=> $termid );
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

   public function get_student_names_ids($filter=null){

      if (!is_null($filter)){
         $this->db
         ->select(array('registration.id', 'CONCAT_WS(" ", registration.surname, registration.name) as stdname'))
         ->from('registration')
         ->join('term', 'registration.term_id=term.id')
         //the next where clause is instead of the commented lines below because Codeigniter2 does not support group_start/end !
         // ->where("(`term`.`active`=1 AND (`registration`.`surname` LIKE '%".$filter."%' OR `registration`.`name` LIKE '%".$filter."%'))")
         ->where("((`registration`.`surname` LIKE '%".$filter."%' OR `registration`.`name` LIKE '%".$filter."%'))")
         // ->group_start()
            // ->like('registration.surname', $filter)
            // ->or_like('registration.name', $filter)
         // ->group_end()
         ->where('term.active',1)
         ->order_by('stdname', 'ASC');
      };
      
      $query=$this->db->get();

      if ($query->num_rows() > 0) 
      {
         foreach($query->result_array() as $row) 
         {
            $students[] = $row;
         }
         return $students;
      }
      else 
      {
         return false;
      }
   }

}