<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Schedule extends CI_Controller {
	
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
    
	public function index($daynum=null)
	{
		// redirect('schedule/<function>');
		if($daynum==0) $daynum=1; //if it's Sunday show the Mondays program!
		$this->load->model('login_model');
		$user=$this->login_model->get_user_name($this->session->userdata('user_id'));
		$data['user']=$user;
	
		$this->load->model('schedule_model');
		$schedule=$this->schedule_model->get_schedule_data($daynum);

		$program=[];
		if ($schedule) {
			foreach ($schedule as $row){
				$classroom = $row['classroom'];
				$program[$classroom][]=$row;
			}
			for ($i=1; $i <= 9; $i++) { 
				if (!isset($program[$i])){
					$program[$i]=[];
					ksort($program);
				}
			}
			$data['schedule'] = $program;
		}
		else {
			$data['schedule'] = false;
		}
		
		$this->load->view('include/header');
		$this->load->view('schedule', $data);
		$footer_data['regs']=true;
		$this->load->view('include/footer', $footer_data);
	
    }    
}
