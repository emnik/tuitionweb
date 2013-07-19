<?php if (!defined('BASEPATH')) die();

class Card_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }

   public function get_employee_data($id) {
   	$query=$this
   		->db
         ->select('*')
         ->from('employee')
         ->where('employee.id',$id)
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


   public function update_employee_data($employee_data, $id){

      //replacing empty array values with NULL
      foreach ($employee_data as $i => $value) {
         if ($value === "") $employee_data[$i] = null;
      };

      $this->db->where('employee.id',$id);
      $this->db->update('employee', $employee_data);
   }


}