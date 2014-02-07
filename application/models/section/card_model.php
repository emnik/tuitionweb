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
         ->join('weekday','section_program.day = weekday.name')
         ->where('section_id',$id)
         ->order_by('weekday.priority','asc')
         ->order_by('section_program.start_tm', 'asc')
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


   public function update_section_data($section_update, $id, $section_program_update){

      //replacing empty array values with NULL
      foreach ($section_update as $i => $value) {
         if ($value === "") $section_update[$i] = null;
      };

      $this->db->where('section.id',$id);
      $this->db->update('section', $section_update);
   

      foreach ($section_program_update as $i => $data) {
         $data['section_id']=$id;
         if ($i>0){
            $this->db->where('section_program.id',$i);
            $this->db->update('section_program', $data);
         }
         else {
            $this->db->insert('section_program', $data);  
         }
      }

   }


   public function delprogramday($id){
      $this->db->delete('section_program', array('id'=>$id));
      return true;
   }


   public function getsectionstudents($id){

      $query=$this
         ->db
         ->select(array('std_lesson.id','registration.surname', 'registration.name', 'contact.home_tel', 'contact.std_mobile', 'registration.mothers_name', 'contact.mothers_mobile', 'registration.fathers_name', 'contact.fathers_mobile','contact.work_tel'))
         ->from('std_lesson')
         ->join('registration', 'std_lesson.reg_id=registration.id')
         ->join('contact', 'registration.id = contact.reg_id')
         ->where('std_lesson.section_id',$id)
         ->get();

      if ($query->num_rows() > 0) 
      {
         foreach($query->result_array() as $row) 
         {
            $section_students[] = $row;
         }
         return $section_students; 
      }
      else 
      {
         return false;
      }

   }


   public function removefromsection($data){
      foreach ($data as $key => $value) {
         $this->db->delete('std_lesson',array('id'=>$key));
      }
   }


   public function get_prevnext_section_byname($sectionname, $id, $year)
   {
      $data=array('next'=>'', 'prev'=>'');
      $ids = $this->db->select('id')->where('section', $sectionname)->where('schoolyear', $year)->order_by('section, id')->get('section');
      if($ids->num_rows()>0)
         {
            foreach ($ids->result_array() as $row) {
               $tmp[]=$row['id'];
            }
            $c=0;
            while ($tmp[$c] != $id) {
               $c++;
            };

            if ($c+1<count($tmp))
            {
               $data['next']=$tmp[$c+1];
            }
            if ($c-1>=0)
            {
               $data['prev']=$tmp[$c-1];     
            }
         }
      return $data;
   }


}