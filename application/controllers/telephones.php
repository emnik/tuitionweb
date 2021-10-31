<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Telephones extends CI_Controller {
	
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
		redirect('telephones/catalog');
	}

	public function catalog()
	{
		$this->load->model('login_model');
	    $user=$this->login_model->get_user_name($this->session->userdata('user_id'));
        $data['user']=$user;
        
        $startsch=$this->session->userdata('startsch');

		$this->load->view('include/header');
		$this->load->view('reports/telephones', $data);
		$this->load->view('include/footer');
	}

    public function getstudentphones()
    {
        header('Content-Type: application/x-json; charset=utf-8');

		$this->load->model('reports/telephones_model');
        $res = $this->telephones_model->get_phonecatalog();
        
        // $this->load->library('firephp');
        // $this->firephp->info($res);
		//return results
        echo json_encode($res);
     
    }

    public function getemployeephones()
    {
        header('Content-Type: application/x-json; charset=utf-8');

		$this->load->model('reports/telephones_model');
        $res = $this->telephones_model->get_employeephones();

		//return results
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