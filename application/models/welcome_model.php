<?php

class Welcome_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


   public function get_schoolyears() {
   	$query=$this
   		->db
         ->select('*')
         ->from('schoolyear')
   		->get();

   	if ($query->num_rows() > 0) 
		{
			foreach($query->result_array() as $row) 
			{
				$schoolyears[] = $row;
			}
			return $schoolyears;
		}
		else 
		{
			return false;
		}

   }

   public function get_selected_startschyear() {
      $query=$this
         ->db
         ->select('lookup.value_1')
         ->from('lookup')
         ->where('lookup.id',2)
         ->limit(1)
         ->get();

      if ($query->num_rows() > 0) 
      {
         //return selected schoolyear start
         $row = $query->row_array();
         return $row['value_1'];
      }
      else 
      {
         //if none selected return current schoolyear start
         $d=explode(' ', date('m Y'));
         if ($d[0]<=7){
            $cur_schoolyear_start=$d[1]-1;
         } 
         else {
            $cur_schoolyear_start=$d[1];  
         }
         return $cur_schoolyear_start;
      }

   }

   public function set_schoolyear($startsch){

      $data = array('value_1'=>$startsch);
      $this-> db
           -> where('lookup.id',2)
           -> update('lookup',$data);
   }

}
