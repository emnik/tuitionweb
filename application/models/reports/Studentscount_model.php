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
       $statement = $statement."FROM ((`registration` join `class`) join `term`) ";
       $statement = $statement."WHERE `registration`.`class_id` = `class`.`id` ";
       $statement = $statement." AND `registration`.`term_id` = `term`.`id` ";
       $statement = $statement." AND `term`.`active` = 1 ";
       $statement = $statement." GROUP BY `class`.`class_name` ORDER BY count(`registration`.`id`) DESC";
     
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

   public function get_stdcountperlesson()
   {

       $statement="SELECT `catalog_lesson`.`title` AS `Μάθημα`, ";
       $statement=$statement."`class`.`class_name` AS `Τάξη`, ";
       $statement=$statement."COUNT(DISTINCT `registration`.`id`) AS `Μαθητές` ";
       $statement=$statement."FROM `registration` ";
       $statement=$statement."JOIN `term` ON `term`.`id` = `registration`.`term_id` ";
       $statement=$statement."JOIN `class` ON `class`.`id` = `registration`.`class_id` ";
       $statement=$statement."JOIN `std_lesson` ON `std_lesson`.`reg_id` = `registration`.`id` ";
       $statement=$statement."JOIN `lesson` ON `lesson`.`id` = `std_lesson`.`lesson_id` ";
       $statement=$statement."JOIN `catalog_lesson` ON `catalog_lesson`.`id` = `lesson`.`cataloglesson_id` ";
       $statement=$statement."WHERE `term`.`active` = 1 ";
       $statement=$statement."GROUP BY `class`.`id`, `catalog_lesson`.`id`";
       

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

   public function get_classes()
   {
    $query=$this->db->select('*')->get('class');
    if ($query->num_rows() > 0) 
    {
      $output=[];
      foreach($query->result_array() as $row) 
      {
         array_push($output, array('id' => $row['id'], 'text'=> $row['class_name']));
      }
      array_push($output, array('id' => end($output)['id']+1, 'text'=> 'Όλα'));

      // $this->load->library('firephp');
      // $this->firephp->info($output);
      
      return $output;
    }
    else 
    {
         return false;
    }

   }
}