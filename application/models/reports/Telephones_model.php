<?php

class Telephones_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }

   public function get_phonecatalog() 
   {
   //  $query=$this
   //     ->db
   //     ->select('*')
   //     ->order_by('Initial_Letter')
   //     ->get('vw_phonebook');

    $MySQL= "SELECT ";
    $MySQL = $MySQL."`registration`.`surname` AS `Επίθετο`,`registration`.`name` AS `Όνομα`,`contact`.`std_mobile` AS `Κινητό παιδιού`,`contact`.`home_tel` AS `Σταθερό σπιτιού`,`registration`.`mothers_name` AS `Μητρώνυμο`,`contact`.`mothers_mobile` AS `Κινητό μητέρας`,`registration`.`fathers_name` AS `Πατρώνυμο`,`contact`.`fathers_mobile` AS `Κινητό Πατέρα`,`contact`.`work_tel` AS `Σταθερό δουλειάς`,REPLACE(upper(left(`registration`.`surname`,1)),'Ά','Α') AS `Initial_Letter` " ;
    $MySQL = $MySQL."FROM `registration` ";
    $MySQL = $MySQL."JOIN `contact` ON `contact`.`reg_id`=`registration`.`id` ";
    $MySQL = $MySQL."JOIN `term` ON `term`.`id`=`registration`.`term_id` ";
    $MySQL = $MySQL."WHERE `term`.`active`=1 ";
    $MySQL = $MySQL."AND `registration`.`surname` != '' "; //this is needed as sometimes an error in the registration leaved empty registrations!!! [MIGHT BE CHECKED AT A LATER STAGE...]
    $MySQL = $MySQL."ORDER BY `Initial_Letter` ASC, `registration`.`surname` ASC, `registration`.`name` ASC;" ;
    
    $query = $this->db->query($MySQL);

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

   public function get_employeephones() 
   {
    $query=$this
       ->db
       ->select(array('surname', 'name', 'home_tel', 'mobile'))
       ->where('active',1)
       ->order_by('surname')
       ->get('employee');

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

   public function google_export_data()
   {
      $MySQL = "SELECT ";
      $MySQL = $MySQL."CONCAT_WS(', ', `registration`.`name`, `registration`.`surname`) AS `Name`,";
      $MySQL = $MySQL."`registration`.`name` AS `Given Name`,";
      $MySQL = $MySQL."`registration`.`surname` AS `Family Name`,";
      $MySQL = $MySQL."CONCAT_WS(' ', 'Display Name:', CONCAT_WS(' ', `registration`.`name`, `registration`.`surname`)) AS `Notes`, ";
      $MySQL = $MySQL."CONCAT_WS(' ', '* My Contacts ::: Μαθητές', '".$this->session->userdata('startsch')."') AS `Group Membership`,";
      $MySQL = $MySQL."'Home' AS `Address 1 - Type`,";
      $MySQL = $MySQL."CONCAT_WS(' ', `address`, `region`) AS `Address 1 - Formatted`,";
      $MySQL = $MySQL."`address` AS `Address 1 - Street`,";
      $MySQL = $MySQL."`region` AS `Address 1 - City`,";
      $MySQL = $MySQL."'Mobile' AS `Phone 1 - Type`,";
      $MySQL = $MySQL."`std_mobile` AS `Phone 1 - Value`,";
      $MySQL = $MySQL."'Home' AS `Phone 2 - Type`,";
      $MySQL = $MySQL."`home_tel` AS `Phone 2 - Value`,";
      $MySQL = $MySQL."'Work' AS `Phone 3 - Type`,";
      $MySQL = $MySQL."`work_tel` AS `Phone 3 - Value` ";
      $MySQL = $MySQL."FROM  `registration` left join `contact` ON `registration`.`id` = `contact`.`reg_id` ";
      $MySQL = $MySQL."JOIN `term` ON `registration`.`term_id`=`term`.`id` ";
      $MySQL = $MySQL."WHERE (`term`.`active` = 1) ";
      $MySQL = $MySQL."order by `registration`.`id` ASC;" ;
   
      $query = $this->db->query($MySQL);
      if ($query->num_rows() > 0) 
      {
        foreach($query->result_array() as $row) 
        {
           $output['google'][] = $row;
        }
        return $output;
      }
      else 
      {
          return false;
      }

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

}