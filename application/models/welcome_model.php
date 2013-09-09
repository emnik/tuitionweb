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

   public function insert_schoolyear($newstartyear){
      $data = array('schoolyear' => $newstartyear.'-'.($newstartyear+1));
      $this-> db
           -> insert('schoolyear',$data);
   }


   public function get_student_names_ids(){
      $this->db
            ->select(array('registration.id', 'CONCAT_WS(" ",registration.surname, registration.name) as stdname'))
            ->from('vw_schoolyear_reg_ids')
            ->join('registration', 'vw_schoolyear_reg_ids.id = registration.id', 'left')
            ->order_by('stdname', 'ASC');
      
      $query=$this->db->get();

      if ($query->num_rows() > 0) 
      {
         foreach($query->result_array() as $row) 
         {
            $students[] = $row;
         }
         return $students;
      }
      else 
      {
         return false;
      }

   }






}
