<?php

class Studentscount_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


   public function get_stdcountperclass()
   {
       $statement = "SELECT `class`.`class_name` AS `Τάξη`, count(`registration`.`id`) AS `Μαθητές`,";
       $statement = $statement."count(`registration`.`del_lessons_dt`) AS `Διεγραμμένοι`,(count(`registration`.`id`) - count(`registration`.`del_lessons_dt`)) AS `Ενεργός αριθμός μαθητών` ";
       $statement = $statement."FROM ((`registration` join `class`) join `lookup`) ";
       $statement = $statement."WHERE ((`registration`.`class_id` = `class`.`id`) AND ";
       $statement = $statement."(";
       $statement = $statement."((`lookup`.`id` = 2) AND (month(`registration`.`start_lessons_dt`) >= 8) AND (year(`registration`.`start_lessons_dt`) = `lookup`.`value_1`))";
       $statement = $statement."OR ((`lookup`.`id` = 2) AND (month(`registration`.`start_lessons_dt`) <= 7) AND (year(`registration`.`start_lessons_dt`) = (`lookup`.`value_1` + 1)))";
       $statement = $statement.")";
       $statement = $statement.") GROUP BY `class`.`class_name` ORDER BY count(`registration`.`id`) DESC";
     
    $query=$this->db->query($statement);
    // ->select('*')
    // ->select(array('Τάξη', "'Αρ. Μαθητών' AS 'stdcount'", 'Διεγραμμένοι', 'Ενεργός αριθμός μαθητών'))
    // ->get('vw_count_std_per_class');


    if ($query->num_rows() > 0) 
    {
      foreach($query->result_array() as $row) 
      {
         $output['aaData'][] = $row;
      }
      return $output;
    }
    else 
    {
         return false;
    }
   //  $this->load->library('firephp');
   //  $this->firephp->info($res);
   }

   public function get_stdcountperlesson()
   {
       $statement="SELECT `catalog_lesson`.`title` AS `Μάθημα`,`class`.`class_name` AS `Τάξη`,";
       $statement=$statement."COUNT(`registration`.`surname`) AS `Μαθητές` ";
       $statement=$statement."FROM (((((((`lesson_tutor` join `catalog_lesson`) join `registration`) join `class`) join `course`) join `std_lesson`) join `section`) join `lookup`) ";
       $statement=$statement."WHERE ((`lesson_tutor`.`cataloglesson_id` = `catalog_lesson`.`id`) AND (`registration`.`class_id` = `class`.`id`) AND (`course`.`class_id` = `class`.`id`) AND (`std_lesson`.`tutor_id` = `lesson_tutor`.`id`) AND (`std_lesson`.`reg_id` = `registration`.`id`) AND (`std_lesson`.`section_id` = `section`.`id`) AND (`section`.`course_id` = `course`.`id`) AND (((month(`registration`.`start_lessons_dt`) >= 8) AND (`lookup`.`value_1` = year(`registration`.`start_lessons_dt`))) OR ((month(`registration`.`start_lessons_dt`) <= 7) AND (year(`registration`.`start_lessons_dt`) = (`lookup`.`value_1` + 1))))) group by `catalog_lesson`.`title`,`class`.`class_name`";


        $query=$this->db->query($statement);


    if ($query->num_rows() > 0) 
    {
      foreach($query->result_array() as $row) 
      {
         $output['aaData'][] = $row;
      }
      return $output;
    }
    else 
    {
         return false;
    }
   //  $this->load->library('firephp');
   //  $this->firephp->info($res);
   }


}