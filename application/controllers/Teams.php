<?php if (!defined('BASEPATH')) die();

Class Teams extends CI_Controller {
	
public function __construct() {
		
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


public function index(){
	/*
	If I want to pass a parameter to index through a uri segment then I would have to use
	a url such as: http://domain/tuitionweb/student/index/id
	It must have the index method specified!!! 
	*/

	$this->load->model('login_model');
	$user=$this->login_model->get_user_name($this->session->userdata('user_id'));
	$data['user']=$user;

	//$this->load->library('graphTeamsLibrary');
	//$teams=$this->graphteamslibrary->test_teams();
	
	$this->load->model('teams_model');
	$teamsUsers=$this->teams_model->get_teams_users();

	if ($teamsUsers) {
		$data['teams'] = $teamsUsers;
		}
	else {
		$data['teams'] = false;
	}
	
	$this->load->view('include/header');
	$this->load->view('teams', $data);
	$footer_data['regs']=true;
	$this->load->view('include/footer', $footer_data);

}


}