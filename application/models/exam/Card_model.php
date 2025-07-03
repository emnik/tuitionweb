<?php if (!defined('BASEPATH')) die();

class Card_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


   public function get_exam_data($examid){
      $query=$this->db->select('*')->where('exam.id', $examid)->get('exam');

      if ($query->num_rows() > 0) 
      {
         return $query->row_array(); 
      }
      else 
      {
         return false;
      }
   }


   public function update_exam_data($exam_data, $exam_lesson_data, $examid){

      //replacing empty array values with NULL
      foreach ($exam_data as $i => $value) {
         if ($value == "") $exam_data[$i] = null;
      };

      $this->db->where('exam.id', $examid);
      $data = array_diff($exam_data, array('id'));
      // $this->load->library('firephp');
      // $this->firephp->info($data);
      $this->db->update('exam', $data);

      foreach ($exam_lesson_data as $i => $data) {
         $data['exam_id']=$examid;
         if ($i>0){
            $this->db->where('exam_lesson.id',$i);
            $this->db->update('exam_lesson', $data);
         }
         else {
            $this->db->insert('exam_lesson', $data);  
         }
      }
   }
   
   public function update_supervisors_data($supervisor, $examid){

      $this->db->delete('exam_supervisor', array('exam_id'=>$examid));
      if (!empty($supervisor)){
         foreach ($supervisor as $key => $value) {
            $data[]=array('exam_id'=>$examid, 'employee_id'=>$value);
         }
         $this->db->insert_batch('exam_supervisor', $data);
      }
      // $this->load->library('firephp');
      // $this->firephp->info($data);
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

public function get_class_course($lessonid){
   //this is needed for get_exam_prog() to set the initial data in the view!!!
   //that's because I store the lesson_id and I need the corresponding course_id and class_id to populate the course and class fields in the form.
   $Myquery = "SELECT `class`.`class_name`,`catalog_lesson`.`title`, `lesson`.`id` AS `lesson_id`, `lesson`.`course_id` AS `course_id`, `class`.`id` AS `class_id` ";
   $Myquery=$Myquery."FROM `lesson` ";
   $Myquery=$Myquery."JOIN `catalog_lesson` ON `catalog_lesson`.`id` = `lesson`.`cataloglesson_id` ";
   $Myquery=$Myquery."JOIN `course` ON `lesson`.`course_id` = `course`.`id` ";
   $Myquery=$Myquery."JOIN `class` ON `course`.`class_id` = `class`.`id` ";
   $Myquery=$Myquery."WHERE `lesson`.`id` = ".$lessonid." ;";
   $query = $this->db->query($Myquery);
   
   if ($query->num_rows() > 0)
   {
      return $query->result_array();
   }
   
   else
   {
      return false;
   };
   
}

public function get_exam_prog($examid){
   $query = $this->db->select(array('exam_lesson.id', 'exam_lesson.lesson_id'))
                     ->from('exam_lesson')
                     ->join('exam', 'exam.id=exam_lesson.exam_id')
                     ->where('exam.id', $examid)
                     ->get();

   $lessonids=[];
   $data = $query->result_array();
   if ($query->num_rows() > 0)
   {
      //for each lessonid we get the classid and courseid
      for ($i=0; $i < $query->num_rows(); $i++) { 
         $lessonids[$data[$i]['id']] = $this->get_class_course($data[$i]['lesson_id']);
      }

      // $this->load->library('firephp');
      // $this->firephp->info($lessonids);
      return $lessonids; 
   }
   else
   {
      return false;
   }

}

   public function del_lesson($lessonid){
      $this->db->delete('exam_lesson', array('id'=>$lessonid));
   }


   public function get_supervisors_names_ids(){
      $query = $this->db
                     ->select(array('employee.id', 'employee.nickname'))
                     ->from('employee')
                     ->where('employee.active', 1)
                     ->where('employee.is_tutor', 1)
                     ->order_by('nickname', 'ASC')
                     ->get();

      if ($query->num_rows() > 0) 
      {
         foreach($query->result_array() as $row) 
         {
            $list[] = $row;
         }
         return $list;
      }
      else 
      {
         return false;
      }
   }


   public function get_supervisors($examid){
      $query = $this->db->select('employee_id')
                        ->where('exam_id', $examid)
                        ->get('exam_supervisor');

      if ($query->num_rows() > 0) 
      {                        
         $supervisors = $query->result_array();
         return $supervisors;
      }
      else 
      {
         return false;
      }      
   }

}