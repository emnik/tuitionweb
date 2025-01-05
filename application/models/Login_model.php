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

		$result=array('username_err'=>true, 'password_err'=>true, 'data'=>false);

		$q = $this->db->query("SELECT * FROM `user` WHERE `username`='".$username."' AND ( (`expires` > '".date('Y-m-d')."' ) OR (`expires` = NULL) OR (`expires` = '0000-00-00')) Limit 1;");		
			
		if ($q->num_rows() > 0)
		{
			$result['username_err']=false;
			//for pbkdf2
			$good_hash = $q->row()->password;

			if (validate_password($password, $good_hash) === true)
			{
				// return $q->row();
				$result['password_err'] = false;
				$result['data'] = $q->row();
				return $result;
			} else {
				$result['password_err'] = true;
				return $result;
				}
			//for sha1 just return $q->row();
			} else {
				$result['username_err'] = true;
				return $result;
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
		
		if ($q->num_rows() > 0)
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
		
		if ($q->num_rows() > 0)
		{
			return $q->row();
		}
		
		return false;
	} 
	
}
