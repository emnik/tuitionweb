<?php

class Teams_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }

   public function insert_into_teams_table($data)
   {
       // Start a transaction
       $this->db->trans_start();

       // Empty the teams table
       $this->db->empty_table('teams');

       // Insert each user into the teams table
       foreach ($data as $user) {
           $this->db->replace('teams', array(
               'id' => $user['id'],
               'displayName' => $user['displayName'],
               'givenName' => $user['givenName'],
               'surname' => $user['surname'],
               'mail' => $user['mail'],
               'mobilePhone' => $user['mobilePhone'],
               'otherMails' => isset($user['otherMails']) && is_array($user['otherMails']) && !empty($user['otherMails']) ? json_encode($user['otherMails']) : null
           ));
       }

       // Complete the transaction
       $this->db->trans_complete();

       // Check if the transaction was successful
       if ($this->db->trans_status() === FALSE) {
           // Generate an error... or use the log_message() function to log your error
           return false;
       } else {
           return true;
       }
   }

   public function delete_from_teams_table($deleted_users){
      // Start a transaction
      $this->db->trans_start();

      // Delete each user from the teams table
      foreach ($deleted_users as $user_id) {
         $this->db->delete('teams', array('id' => $user_id));
      }

      // Complete the transaction
      $this->db->trans_complete();

      // Check if the transaction was successful
      if ($this->db->trans_status() === FALSE) {
         // Generate an error... or use the log_message() function to log your error
         return false;
      } else {
         return true;
      }
   }

   public function add_single_user_in_teams_table($data)
   {
      // Encode the otherMails field as a JSON string if it exists
      if (isset($data['otherMails']) && is_array($data['otherMails'])) {
      $data['otherMails'] = json_encode($data['otherMails']);
      } else {
         $data['otherMails'] = array();
      }

       // Insert the user data in the teams table
       $this->db->insert('teams', $data);

       // Check if the insert was successful
       if ($this->db->affected_rows() > 0) {
           return true;
       } else {
           return false;
       }
   }

   public function update_user_in_teams_table($userId, $data)
   {
       // Remove fields that should not be stored locally
       if (isset($data['passwordProfile'])) {
           unset($data['passwordProfile']['password']);
           unset($data['passwordProfile']['forceChangePasswordNextSignIn']);
           if (empty($data['passwordProfile'])) {
               unset($data['passwordProfile']);
           }
       }

       // Encode the otherMails field as a JSON string if it exists
       if (isset($data['otherMails']) && is_array($data['otherMails'])) {
           $data['otherMails'] = json_encode($data['otherMails']);
       }

       // Update the user data in the teams table
       $this->db->where('id', $userId);
       $this->db->update('teams', $data);

       // Check if the update was successful
       if ($this->db->affected_rows() > 0) {
           return true;
       } else {
           // Check if the data is the same
           $this->db->where('id', $userId);
           $query = $this->db->get('teams');
           $existingData = $query->row_array();
           $data['id'] = $userId; // Ensure the ID is set

           if ($existingData == $data) {
               return true; // The data is the same
           } else {
               return false;
           }
       }
   }

   public function getAllTeams() {
      $query=$this
         ->db
         ->select('*')
         ->get('teams');

      if ($query->num_rows() > 0) 
      {
         foreach($query->result_array() as $row) 
         {
            $output['data'][] = $row;
         }
         return $output;
      }
      else 
      {
            return false;
      }
   }

   public function getCurrentStudents(){
      $query = $this->db->query("
         SELECT t.id, t.displayName, t.mail, t.surname, t.givenName, t.mobilePhone, t.otherMails
         FROM teams t
         JOIN registration r ON t.surname = r.surname
         AND t.givenName = r.name 
         WHERE r.term_id = (SELECT id FROM term WHERE active = 1)
         ORDER BY r.surname, r.name
      ");

      if ($query->num_rows() > 0) 
      {
         foreach($query->result_array() as $row) 
         {
            $output['data'][] = $row;
         }
         return $output;
      }
      else 
      {
            return false;
      }
   }


   // public function getCurrentStudentsWithoutAccount(){
   //    $query = $this->db->query("
   //       SELECT r.surname, r.name
   //       FROM registration r
   //       WHERE r.term_id = (SELECT id FROM term WHERE active = 1)
   //       AND CONCAT(r.name, ' ', r.surname) NOT IN (
   //          SELECT CONCAT(t.givenName, ' ', t.surname)
   //          FROM teams t
   //       )
   //       ORDER BY r.surname, r.name
   //    ");

   //    if ($query->num_rows() > 0) 
   //    {
   //       foreach($query->result_array() as $row) 
   //       {
   //          $output['data'][] = $row;
   //       }
   //       return $output;
   //    }
   //    else 
   //    {
   //          return false;
   //    }
   // }


   public function getDataForNewAccount($id, $group)
   {
      if ($group === 'Μαθητές') {
         $query = $this->db->query("
         SELECT CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(r.name, ' ', 1), '-', 1), ' ', r.surname) AS 'displayName', r.surname, r.name, c.std_mobile as 'mobile'
         FROM contact c
         JOIN registration r ON c.reg_id = r.id
         WHERE r.id = ?
         ", array($id));
      } else {
         $query = $this->db->query("
         SELECT CONCAT(e.name, ' ', e.surname) AS 'displayName', e.surname, e.name, e.mobile
         FROM employee e
         WHERE e.id = ?
         ", array($id));      
      }

      if ($query->num_rows() === 1) 
      {
         return $query->row_array();
      }
      else 
      {
            return false;
      }
   }

   public function getStudentLocalData($surname, $givenName){
      // for getting the mobile phone of the student - TODO: also the email
      $query = $this->db->query("
         SELECT c.std_mobile
         FROM contact c
         JOIN registration r ON c.reg_id = r.id
         WHERE r.term_id = (SELECT id FROM term WHERE active = 1)
         AND r.surname = ?
         AND r.name = ?
      ", array($surname, $givenName));

      if ($query->num_rows() === 1) 
      {
         return $query->row_array();
      }
      else 
      {
            return false;
      }
   }

   public function getObsoleteUsers(){
      $query = $this->db->query("
         SELECT t.id, t.displayName, t.mail, t.surname, t.givenName, t.mobilePhone, t.otherMails
         FROM teams t
         WHERE t.id NOT IN (
          SELECT t1.id
          FROM teams t1
          JOIN registration r ON t1.surname = r.surname
          AND t1.givenName = r.name 
          WHERE r.term_id = (SELECT id FROM term WHERE active = 1)
         )
         AND t.id NOT IN (
          SELECT DISTINCT t2.id
          FROM teams t2
          JOIN employee e ON t2.surname = e.surname
          AND (t2.givenName = e.name OR t2.givenName = e.nickname)
          WHERE e.active = 1
         )
         AND t.mail != 'info@spoudh.gr'
         AND t.mail != 'manos@spoudh.gr'
         ORDER BY t.surname, t.givenName
      ");

      if ($query->num_rows() > 0) 
      {
         foreach($query->result_array() as $row) 
         {
            $output['data'][] = $row;
         }
         return $output;
      }
      else 
      {
            return false;
      }
   }

   public function getCurrentTeachers(){
      $query = $this->db->query("
         SELECT DISTINCT t.id, t.displayName, t.mail, t.surname, t.givenName, t.mobilePhone, t.otherMails
         FROM teams t
         JOIN employee e ON t.surname = e.surname
         AND (t.givenName = e.name
         OR t.givenName = e.nickname)
         WHERE e.active = 1
         ORDER BY e.surname, e.name
      ");

      if ($query->num_rows() > 0) 
      {
         foreach($query->result_array() as $row) 
         {
            $output['data'][] = $row;
         }
         return $output;
      }
      else 
      {
            return false;
      }
   }   

   public function get_mail_settings()
   {
       $query = $this->db->get('mailsettings');
       return $query->row_array(); // Assuming there's only one row
   }   


   public function save_message_history($id, $message_body) {
      // Check if a record with the same id exists
      $this->db->where('id', $id);
      $query = $this->db->get('teamsHistory');
  
      if ($query->num_rows() > 0) {
          // Update the existing record
          $this->db->where('id', $id);
          $result = $this->db->update('teamsHistory', array(
           'message' => $message_body,
           'datetime' => date('Y-m-d H:i:s') // Update the datetime field with the current date/time
          ));
      } else {
          // Insert a new record
          $result = $this->db->insert('teamsHistory', array('id' => $id, 'message' => $message_body));
      }
  
      return $result;
  }


  public function get_message_history($id) {
      $this->db->where('id', $id);
      $query = $this->db->get('teamsHistory');
  
      if ($query->num_rows() > 0) {
          return $query->row();
      } else {
          return false;
      }
  }

}