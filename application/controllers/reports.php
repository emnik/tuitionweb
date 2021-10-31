<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends CI_Controller {
	
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
		redirect('reports/studentscount');
    }    

    public function studentscount()
    {
		$this->load->model('login_model');
	    $user=$this->login_model->get_user_name($this->session->userdata('user_id'));
        $data['user']=$user;
        
        $startsch=$this->session->userdata('startsch');

		$this->load->view('include/header');
		$this->load->view('reports/studentscount', $data);
		$this->load->view('include/footer');        
    }

    
    public function studentteachers()
    {
		$this->load->model('login_model');
	    $user=$this->login_model->get_user_name($this->session->userdata('user_id'));
        $data['user']=$user;
        
        $startsch=$this->session->userdata('startsch');

		$this->load->view('include/header');
		$this->load->view('reports/studentteachers', $data);
		$this->load->view('include/footer');        
    }

	public function getstdcountperlesson()
	{
		header('Content-Type: application/x-json; charset=utf-8');

		$this->load->model('reports/studentscount_model');
   		$res=$this->studentscount_model->get_stdcountperlesson();
		
		echo json_encode($res);
	}

    
	public function getstdcountperclass()
	{
		header('Content-Type: application/x-json; charset=utf-8');

		$this->load->model('reports/studentscount_model');
   		$res=$this->studentscount_model->get_stdcountperclass();
		
		echo json_encode($res);
	}

    public function logout()
	{
		$this->session->destroy();

		$this->load->view('include/header');		
		$this->load->view('login');
		$this->load->view('include/footer');
	}

}