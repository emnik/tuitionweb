<?php

class Studentteachers_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
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
      
      // $this->load->library('firephp');
      // $this->firephp->info($output);
      
      return $output;
    }
    else 
    {
         return false;
    }

   }


   public function get_studentsTeachersPerClass($data)
   {
    $selList = $data['selList'];

    $cmdSQL = "SELECT `registration`.`class_id`, `catalog_lesson`.`title`, `employee`.`nickname`, ";
    $cmdSQL = $cmdSQL."CONCAT(CONCAT(CONCAT(CONCAT( CONCAT( `registration`.`surname`, ' ' ), `registration`.`name` ), ' ('), `class`.`class_name`), ')') AS `Ονοματεπώνυμο`  ";
    $cmdSQL = $cmdSQL."FROM `tuition_management`.`lesson_tutor` AS `lesson_tutor`, `tuition_management`.`employee` AS `employee`, ";
    $cmdSQL = $cmdSQL."`tuition_management`.`catalog_lesson` AS `catalog_lesson`, `tuition_management`.`registration` AS `registration`, ";
    $cmdSQL = $cmdSQL." `tuition_management`.`course` AS `course`, `tuition_management`.`class` AS `class`, ";
    $cmdSQL = $cmdSQL."`tuition_management`.`std_lesson` AS `std_lesson`, `tuition_management`.`term` AS `term` ";
    $cmdSQL = $cmdSQL."WHERE `lesson_tutor`.`employee_id` = `employee`.`id` ";
    $cmdSQL = $cmdSQL."AND `lesson_tutor`.`cataloglesson_id` = `catalog_lesson`.`id` ";
    $cmdSQL = $cmdSQL."AND `registration`.`course_id` = `course`.`id` ";
    $cmdSQL = $cmdSQL."AND `course`.`class_id` = `class`.`id` ";
    $cmdSQL = $cmdSQL."AND `registration`.`class_id` = `class`.`id` ";
    $cmdSQL = $cmdSQL."AND `class`.`class_name` IN (".$selList.") "; 
    $cmdSQL = $cmdSQL."AND `std_lesson`.`tutor_id` = `lesson_tutor`.`id` ";
    $cmdSQL = $cmdSQL."AND `std_lesson`.`reg_id` = `registration`.`id` ";
    $cmdSQL = $cmdSQL."AND `term`.`id` = `registration`.`term_id` ";
    $cmdSQL = $cmdSQL."AND `term`.`active` = 1 ";
    $cmdSQL = $cmdSQL."ORDER BY `registration`.`surname` ASC, `registration`.`name` ASC, `employee`.`nickname` ASC ";


 
    $query = $this->db->query($cmdSQL);
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

}


public function get_studentsPerClass($data)
{
  $fieldList = array();
  $selList = $data['selList'];
  if(isset($data['fieldList'])){
    $fieldList = $data['fieldList'];
  }
  
  // $this->load->library('firephp');
  // $this->firephp->info($fieldList);

  $cmdSQL = "SELECT CONCAT(`registration`.`surname`, ' ', `registration`.`name`) AS `stdname`, `class`.`class_name` ";
  if (isset($fieldList)){
    if (in_array('course', $fieldList)){
      $cmdSQL = $cmdSQL.",`course`.`course` ";
    }
    if (in_array('month_price', $fieldList)){
      $cmdSQL = $cmdSQL.",`registration`.`month_price` ";
    }  
    if (in_array('address', $fieldList)){
      $cmdSQL = $cmdSQL.",`registration`.`address` ";
    }      
    if (in_array('region', $fieldList)){
      $cmdSQL = $cmdSQL.",`registration`.`region` ";
    }  
  }
  $cmdSQL = $cmdSQL."FROM `tuition_management`.`registration` AS `registration`,";
	$cmdSQL = $cmdSQL."`tuition_management`.`course` AS `course`, `tuition_management`.`class` AS `class`, ";
	$cmdSQL = $cmdSQL."`tuition_management`.`term` AS `term` ";
	$cmdSQL = $cmdSQL."WHERE `registration`.`course_id` = `course`.`id` ";
	$cmdSQL = $cmdSQL."AND `course`.`class_id` = `class`.`id` ";
	$cmdSQL = $cmdSQL."AND `registration`.`class_id` = `class`.`id` ";
	$cmdSQL = $cmdSQL."AND `class`.`class_name` IN (".$selList.") " ;
  $cmdSQL = $cmdSQL."AND `registration`.`term_id` = `term`.`id` ";
  $cmdSQL = $cmdSQL."AND `term`.`active` = 1 ";
  $cmdSQL = $cmdSQL."ORDER BY `class`.`class_name` ASC, `stdname` ASC ";
    
  $query = $this->db->query($cmdSQL);

  if ($query->num_rows() > 0) 
    {
      foreach($query->result_array() as $row) 
      {
         $output['aaData'][] = $row;
      }
      // $this->firephp->info($output);
      return $output;
    }
    else 
    {
        return false;
    }

}

}