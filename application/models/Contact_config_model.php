<?php

class Contact_config_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }

    // Mail configuration
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

    //SMS configuration
    public function get_sms_settings() {
        $query = $this->db->select('*')->get('smsconf');
    
        if ($query->num_rows() > 0) 
            {
                foreach($query->result_array() as $row) 
                {
                    $smsconf[] = $row;
                }
    
                return $smsconf;
            }
            else 
            {
                return false;
            }
       }
    
       public function update_smsto_data($smsconfdata){
            foreach($smsconfdata as $key => $value){
                if ($value === "") $smsconfdata[$key] = null;
            }

            $this->db->update('smsconf', $smsconfdata);
        }    
}