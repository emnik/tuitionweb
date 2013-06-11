<?php if (!defined('BASEPATH')) die();

class Contact_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


public function get_contact_data($id){

	$query = $this
			->db
			->select('*')
			->from('contact')
			->where('contact.reg_id',$id)
			->limit(1)
			->get();


 	  if ($query->num_rows() > 0) 
      {
         return $query->row_array();  
      }
      else 
      {
         return false;
      };
	}

public function get_secondary_data($id){

	$query = $this
			->db
			->select(array('registration.fathers_name','registration.mothers_name'))
			->from('registration')
			->where('id',$id)
			->limit(1)
			->get();


 	  if ($query->num_rows() > 0) 
      {
         return $query->row_array();  
      }
      else 
      {
         return false;
      };
	}

   public function update_contact_data($id, $contact_data){

      //replacing empty array values with NULL
      foreach ($contact_data as $i => $value) {
         if ($value === "") $contact_data[$i] = null;
      };

      $this->db->where('contact.reg_id',$id);
      $this->db->update('contact', $contact_data);
   }

}

