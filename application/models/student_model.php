<?php

/*
 * Welcome_model
 * Just a model to start with codeigniter-bootstrap
 *
 * @author Nikiforakis Manos
 */

class Student_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


  //  public function get_registration_data($id) {
  //  	$query=$this
  //  		->db
  //  		->select('*')
  //  		->from('registration')
  //  		//->join('vw_schoolyear_reg_ids','registration.id=vw_schoolyear_reg_ids.id','right')
  //        ->where('registration.id',$id)
  //        ->limit(1)
  //  		->get();

  //  	if ($query->num_rows() > 0) 
		// {
   		 
  //        return $query->row_array(); 
		// }
		// else 
		// {
		// 	return false;
		// }

  //  }



   public function get_regions() {
      $query=$this
         ->db
         ->select('*')
         ->get('region');

      if ($query->num_rows() > 0) 
      {
         return $query->result_array(); 
      }
      else 
      {
         return false;
      }

   }



   public function get_classes() {
      $query=$this
         ->db
         ->select('*')
         ->get('class');

      if ($query->num_rows() > 0) 
      {
         return $query->result_array(); 
      }
      else 
      {
         return false;
      }

   }


   public function get_student_data($id) {
      //for common use in all student sections
      $query=$this
         ->db
         ->select(array('registration.surname','registration.name','registration.id'))
         ->from('registration')
         ->where('registration.id',$id)
         ->limit(1)
         ->get();

      if ($query->num_rows() > 0) 
      {
          
         return $query->row_array(); 
      }
      else 
      {
         return false;
      }

   }




}
