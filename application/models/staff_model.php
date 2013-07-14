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


   // public function newreg()
   // {
   //    //insert new record in registration table
   //    $data = array('id' => 'null' );
   //    $this->db->insert('registration', $data);
   //    $regid = $this->db->insert_id();

   //    //insert new record in contact table
   //    $contact_data = array('id' => 'null', 'reg_id' => $regid);
   //    $this->db->insert('contact', $contact_data);

   //    return $regid;
   // }


   // public function delreg($id)
   // {      
   //    $this->db->delete('registration', array('id' => $id)); 
   // }

   // public function cancelreg($id)
   // {      
   //    $query = $this->db->select('surname')
   //             ->where('id',$id)
   //             ->get('registration');
      
   //    if ($query->num_rows() > 0)
   //    {
   //       $row = $query->row();

   //       if (is_null($row->surname))
   //       {
   //          $this->db->delete('registration', array('id'=>$id));
   //          return true;
   //       }
   //       else
   //       {
   //          return false;
   //       };

   //    };
   // }

}
