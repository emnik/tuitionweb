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
               'mail' => $user['mail']
           ));
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
            $output['aaData'][] = $row;
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
         SELECT t.id, t.mail, t.surname, t.givenName
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
            $output['aaData'][] = $row;
         }
         return $output;
      }
      else 
      {
            return false;
      }
   }

   public function getObsoleteUsers(){
      $query = $this->db->query("
         SELECT t.id, t.mail, t.surname, t.givenName
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
         ORDER BY t.surname, t.givenName
      ");

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

   public function getCurrentTeachers(){
      $query = $this->db->query("
         SELECT DISTINCT t.id, t.mail, t.surname, t.givenName
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
            $output['aaData'][] = $row;
         }
         return $output;
      }
      else 
      {
            return false;
      }
   }   


}