<?php if (!defined('BASEPATH')) die();

class Card_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }

   public function get_section_data($id) {
   	$query=$this
   		->db
         ->select('*')
         ->from('section')
         ->where('section.id',$id)
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


   // public function update_section_data($section_data, $id){

   //    //replacing empty array values with NULL
   //    foreach ($employee_data as $i => $value) {
   //       if ($value === "") $employee_data[$i] = null;
   //    };

   //    $this->db->where('employee.id',$id);
   //    $this->db->update('employee', $employee_data);
   // }


}