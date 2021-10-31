<?php

class Telephones_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }

   public function get_phonecatalog() 
   {
    $query=$this
       ->db
       ->select('*')
       ->order_by('Initial_Letter')
       ->get('vw_phonebook');

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

}