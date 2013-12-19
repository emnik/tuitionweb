<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Finance_model extends CI_Model
{
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


    public function get_dept_update_date(){
		$query = $this->db->select('value_1')
				->from('lookup')
				->where('id',3)
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


    function get_schoolyear_finance(){
    	$query=$this->db->select(array('Μήνας', 'Οφειλές', 'Εισπράξεις', 'Τζίρος'))
    			->from('vw_finance_schoolyear')
    			// ->order_by('Αρ.Μήνα')
    			->get();

    	if ($query->num_rows() > 0) 
		{
			foreach($query->result_array() as $row) 
			{
				$schfinance[] = $row;
			}
			return $schfinance;
		}
		else 
		{
			return false;
		}
    }

    function get_economicyear_finance(){
    	$query=$this->db->select(array('Μήνες', 'Ποσό', 'Κατηγορία'))
    			->from('vw_finance_year')
    			->order_by('Μήνες')
    			->order_by('Κατηγορία', 'desc')
    			->get();

    	if ($query->num_rows() > 0) 
		{
			foreach($query->result_array() as $row) 
			{
				$ecofinance[] = $row;
			}
			return $ecofinance;
		}
		else 
		{
			return false;
		}
    }


}