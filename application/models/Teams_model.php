<?php

class Teams_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }

   public function insert_into_teams_table($data)
   {
       // Insert each user into the teams table
       foreach ($data as $user) {
           $this->db->replace('teams', array(
               'id' => $user['id'],
               'givenName' => $user['givenName'],
               'surname' => $user['surname'],
               'mail' => $user['mail'],
               'jobTitle' => $user['jobTitle']
           ));
       }
   }

   public function get_teams_users() {
      $query=$this
         ->db
         ->select('*')
         ->get('teams');

      if ($query->num_rows() > 0) 
      {
         return $query->result_array(); 
      }
      else 
      {
         return false;
      }
   }

}