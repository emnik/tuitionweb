<?php if (!defined('BASEPATH')) die();

Class Curriculum extends CI_Controller {
	
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


	public function courses()
	{
		$this->load->model('curriculum_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this
						->curriculum_model
						->get_courses($this->input->post('jsclassid'))
						)
			);

	}


public function lessons()
{
		$this->load->model('curriculum_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this
						->curriculum_model
						->get_lessons($this->input->post('jsclassid'), $this->input->post('jscourseid'))
						)
			);

}

	public function index()
	{
		$this->load->model('login_model');
		$user=$this->login_model->get_user_name($this->session->userdata('user_id'));
		$data['user']=$user;

		$this->load->model('curriculum_model');
		$data['class'] = $this->curriculum_model->get_classes();
		// $this->load->library('firephp');
		// $this->firephp->info($data['class']);

		$this->load->view('include/header');
		$this->load->view('curriculum', $data);
		$footer_data['regs']=false;
		$this->load->view('include/footer', $footer_data);
	}



	public function logout()
	{
		$this->session->destroy();

		$this->load->view('include/header');		
		$this->load->view('login');
		$this->load->view('include/footer');
	}

}



