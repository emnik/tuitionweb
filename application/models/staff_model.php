<?php

class Staff_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


   public function get_staff_data() {
   	$query=$this
   		->db
         ->select(array('employee.id','employee.surname','employee.name','employee.speciality','employee.active'))
         ->from('employee')
     		->get();

   	if ($query->num_rows() > 0) 
		{
			foreach($query->result_array() as $row) 
			{
				$staff[] = $row;
			}
			return $staff;
		}
		else 
		{
			return false;
		}

   }

   public function get_employee_common_data($id) {
      $query=$this
         ->db
         ->select(array('employee.surname','employee.name','employee.id'))
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

   public function newreg()
   {
      //insert new record in employee table
      $data = array('id' => 'null' );
      $this->db->insert('employee', $data);
      $emplid = $this->db->insert_id();

      // //insert new record in contact table
      // $contact_data = array('id' => 'null', 'reg_id' => $regid);
      // $this->db->insert('contact', $contact_data);

      return $emplid;
   }


   public function delreg($id)
   {      
      $this->db->delete('employee', array('id' => $id)); 
   }

   public function cancelreg($id)
   {      
      $query = $this->db->select('surname')
               ->where('id',$id)
               ->get('employee');
      
      if ($query->num_rows() > 0)
      {
         $row = $query->row();

         if (is_null($row->surname))
         {
            $this->db->delete('employee', array('id'=>$id));
            return true;
         }
         else
         {
            return false;
         };

      };
   }


}
