<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class School extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
        $session_user = $this->session->userdata('is_logged_in');
		if(!empty($session_user))
		{
			// get the group and redirect to appropriate controller
				$this->load->model('login_model');
				$grp = $this
					->login_model
					->get_user_group($this->session->userdata('user_id'));
				
				switch ($grp->name)
				{
					case 'admin':
						// redirect('welcome');
						break;
					// case 'tutor':
					// 	redirect('tutor');
					// 	break;
					// case 'parent':
					// 	redirect('parent');
					// 	break;
				}
		}
		else
		{
			redirect('login');
		}
	}
    
	public function index()
	{
        $this->load->model('login_model');
		$user=$this->login_model->get_user_name($this->session->userdata('user_id'));
        $data['user']=$user;
        
        $this->load->model('school_model');
		

		if (!empty($_POST)) {
			foreach ($_POST as $key => $value) 
			{
				$schooldata[$key]=$value;
			};
			$this->school_model->update_school_data($schooldata);
			$data['school'] = $schooldata;
		}
		else 
		{
			$schooldata=$this->school_model->get_school_data();
			if (!empty($schooldata)){
				$data['school'] = $schooldata[0]; //[0] as I only have one record!!!
			}
		}

		// $this->load->library('firephp');
		// $this->firephp->info($schooldata);

		$this->load->view('include/header');
		$this->load->view('school', $data);
		$footer_data['regs']=true;
		$this->load->view('include/footer', $footer_data);
    }
}