<?php

class Mailinglist_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }

   public function get_emails(){
      //this will propbably be merged with mailinglist_export_data() function
      $MySQL = "SELECT ";
      // $MySQL = $MySQL."CONCAT_WS(', ', `registration`.`name`, `registration`.`surname`) AS `Name`,";
      $MySQL = $MySQL."`email` AS `email` ";
      $MySQL = $MySQL."FROM  `registration` left join `contact` ON `registration`.`id` = `contact`.`reg_id` ";
      $MySQL = $MySQL."JOIN `term` ON `registration`.`term_id`=`term`.`id` ";
      $MySQL = $MySQL."WHERE (`term`.`active` = 1) ";
      $MySQL = $MySQL."AND (`contact`.`email` IS NOT NULL) ";
      $MySQL = $MySQL."order by `registration`.`id` ASC;" ;
   
      $query = $this->db->query($MySQL);
      return $query->result_array();
   }

   public function mailinglist_export_data()
   {
      $MySQL = "SELECT ";
      $MySQL = $MySQL."CONCAT_WS(', ', `registration`.`name`, `registration`.`surname`) AS `Name`,";
      $MySQL = $MySQL."`email` AS `Email` ";
      $MySQL = $MySQL."FROM  `registration` left join `contact` ON `registration`.`id` = `contact`.`reg_id` ";
      $MySQL = $MySQL."JOIN `term` ON `registration`.`term_id`=`term`.`id` ";
      $MySQL = $MySQL."WHERE (`term`.`active` = 1) ";
      $MySQL = $MySQL."AND (`contact`.`email` is not NULL) ";
      $MySQL = $MySQL."order by `registration`.`id` ASC;" ;
   
      $query = $this->db->query($MySQL);
      if ($query->num_rows() > 0) 
      {
        foreach($query->result_array() as $row) 
        {
           $output['mailinglist'][] = $row;
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


// Mailing list settings (sender address, reply-to address, note)

public function get_settings() {
   $query = $this->db->select('*')->get('mailsettings');

   if ($query->num_rows() > 0) 
     {
        foreach($query->result_array() as $row) 
        {
           $mailsettings[] = $row;
           }

           return $mailsettings[0]; // 0 as I only one row ;-)
     }
     else 
     {
        return false;
     }
  }

  public function update_settings($mailsettings){
      $query = $this->db->select('id')->get('mailsettings');
      $result = $query->row(0);
      if (isset($result)){
         $id = $result->id;
      } else {
         $id = null;
      }


      foreach($mailsettings as $key => $value){
         if ($value === "") $mailsettings[$key] = null;
      }

      if (empty($id)){ //new record so insert!
         $this->db->insert('mailsettings', $mailsettings);
      }
      else {
         $this->db->where('id', $id);
         $this->db->update('mailsettings', $mailsettings);
      }
   }

   public function add_to_mail_history($data){
      $this->db->insert('mail_history', $data);
   }

}