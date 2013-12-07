<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Login_model extends CI_Model
{
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
 

	public function verify_user($username, $password)
	{
		//previously sha1
		//$this->load->helper('security');
		//$password_sha1 = do_hash($password, TRUE);

		//Currently pbkdf2
		$this->load->helper('pbkdf2_helper');
		
		$this->load->helper('date');
		
		

		$q = $this
			->db
			->where('username', $username)
			//->where('password', $password_sha1)
			->bracket('open')
			->where('expires >', date('Y-m-d', now()))
			->or_where('expires','0000-00-00')
			->bracket('close')
			->limit(1)
			->get('user');
		
		if ($q->num_rows > 0)
		{
			//for pbkdf2
			$good_hash = $q->row()->password;
			if (validate_password($password, $good_hash) === true)
			{
				return $q->row();
			}
			//for sha1 just return $q->row();
			}
		return false;
	} 
	
	public function get_user_group($user_id)
	{
			
		$q = $this
			->db
			->select('group.name')
			->from('group')
			->join('user', 'user.group_id = group.id')
			->where('user.id', $user_id)
			->limit(1)
			->get();
		
		if ($q->num_rows > 0)
		{
			return $q->row();
		}
		
		return false;
	} 
	
	
		public function get_user_name($user_id)
	{
			
		$q = $this
			->db
			->select('user.surname, user.name')
			->from('user')
			->where('user.id', $user_id)
			->limit(1)
			->get();
		
		if ($q->num_rows > 0)
		{
			return $q->row();
		}
		
		return false;
	} 
	

}
