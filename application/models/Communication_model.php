<?php

class Communication_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }

   public function bulkSMS_export_data($classes)
   {
      $classList = implode(",", $classes);
      $SQLcmd = "SELECT `std_mobile` AS `mobile`, `mothers_mobile` AS `mothers-mobile`, `fathers_mobile` AS `fathers-mobile`, ";
      $SQLcmd = $SQLcmd ."CONCAT_WS(' ', `registration`.`name`, `registration`.`surname`) AS `Name` ";
      $SQLcmd = $SQLcmd ."FROM `registration` LEFT JOIN `contact` ON `registration`.`id` = `contact`.`reg_id` ";
      $SQLcmd = $SQLcmd ."JOIN `term` ON `registration`.`term_id`=`term`.`id` ";
      $SQLcmd = $SQLcmd ."WHERE `term`.`active` = 1 ";
      $SQLcmd = $SQLcmd ."AND `registration`.`class_id` IN (".$classList.") ORDER BY `registration`.`id` ASC ";
      $query = $this->db->query($SQLcmd);
      if ($query->num_rows() > 0) 
      {
        foreach($query->result_array() as $row) 
        {
           $output['bulkSMS'][] = $row;
        }
        return $output;
      }
      else 
      {
          return false;
      }

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
      return $output;
    }
    else 
    {
         return false;
    }

   }


   public function add_to_sms_history($data){
      $this->db->insert('sms_history', $data);
   }

}