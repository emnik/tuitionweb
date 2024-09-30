<?php

class Contact_config_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


   public function get_settings() {
    $query = $this->db->select('*')->get('mailconf');

    if ($query->num_rows() > 0) 
		{
			foreach($query->result_array() as $row) 
			{
				$mailconf[] = $row;
            }

            return $mailconf;
		}
		else 
		{
			return false;
		}
   }

   public function update_microsoft_data($mailconfdata){
        foreach($mailconfdata as $key => $value){
            if ($value === "") $mailconfdata[$key] = null;
        }

        if (empty($mailconfdata['tenantid'])){ //new record so insert!
            $this->db->insert('mailconf', $mailconfdata);
        }
        else {
            $this->db->where('tenantid', $mailconfdata['tenantid']);
            $this->db->update('mailconf', $mailconfdata);
        
        }
    }

}