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


	
	// $this->load->model('teams_model');
	// $teamsUsers=$this->teams_model->get_teams_users();

	// if ($teamsUsers) {
	// 	$data['teams'] = $teamsUsers;
	// 	}
	// else {
	// 	$data['teams'] = false;
	// }
	
	$this->load->view('include/header');
	$this->load->view('teams', $data);
	$footer_data['regs']=true;
	$this->load->view('include/footer', $footer_data);

}

public function getAllTeams()
{
	header('Content-Type: application/x-json; charset=utf-8');

	$this->load->model('teams_model');
	$res=$this->teams_model->getAllTeams();

	echo json_encode($res);
}

public function getCurrentStudents()
{
	header('Content-Type: application/x-json; charset=utf-8');

	$this->load->model('teams_model');
	$res=$this->teams_model->getCurrentStudents();

	echo json_encode($res);
}

public function getObsoleteUsers()
{
	header('Content-Type: application/x-json; charset=utf-8');

	$this->load->model('teams_model');
	$res=$this->teams_model->getObsoleteUsers();

	echo json_encode($res);
}

public function getCurrentTeachers()
{
	header('Content-Type: application/x-json; charset=utf-8');

	$this->load->model('teams_model');
	$res=$this->teams_model->getCurrentTeachers();

	echo json_encode($res);
}


public function getDeletedUsers(){
	header('Content-Type: application/x-json; charset=utf-8');

	$this->load->library('graphTeamsLibrary');
	$res=$this->graphteamslibrary->do('get_deleted_users');

	echo $res;	
}


public function resetTeamsData(){
	header('Content-Type: application/x-json; charset=utf-8');

	$this->load->library('graphTeamsLibrary');
	$res=$this->graphteamslibrary->do('reset');

	echo $res;
}

public function batchDeleteUsers(){
	$data = $this->input->post('data');

	$this->load->library('graphTeamsLibrary');
	$res=$this->graphteamslibrary->do('delete', $data);

	header('Content-Type: application/x-json; charset=utf-8');

	echo $res;
}

}