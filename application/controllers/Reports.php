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

	public function initial(){
		$this->load->model('login_model');
	    $user=$this->login_model->get_user_name($this->session->userdata('user_id'));
        $data['user']=$user;
        
		$startsch=$this->session->userdata('startsch');
		
		switch ($this->input->post('submit')) {
			case 'submit4': // Οικονομικά
				redirect('finance');
				break;

			case 'submit7': // Τηλεφωνικοί Κατάλογοι
				redirect('telephones');
				break;

			case 'submit8': // Ιστορικό
				redirect('history');
				break;		

			case 'submit9': // Αναφορές
				redirect('reports');
				break;							
		}
	
		$this->load->view('include/header');
		$this->load->view('reports/reportsinit', $data);
		$footer_data['regs']=true;
		$this->load->view('include/footer', $footer_data);      		
	}

    public function studentscount()
    {
		$this->load->model('login_model');
	    $user=$this->login_model->get_user_name($this->session->userdata('user_id'));
		$data['user']=$user;
		
		$this->load->model('reports/studentscount_model');
		$classes=$this->studentscount_model->get_classes();
		if($classes){$data['classes']=json_encode($classes, JSON_UNESCAPED_UNICODE);}
		
		$startsch=$this->session->userdata('startsch');

		$this->load->view('include/header');
		$this->load->view('reports/studentscount', $data);
		$footer_data['regs']=true;
		$this->load->view('include/footer', $footer_data);   
    }

    
    public function studentteachers()
    {
		$this->load->model('login_model');
	    $user=$this->login_model->get_user_name($this->session->userdata('user_id'));
        $data['user']=$user;
		
		$this->load->model('reports/studentteachers_model');
		$classes=$this->studentteachers_model->get_classes();
		if($classes){$data['classes']=json_encode($classes, JSON_UNESCAPED_UNICODE);}
		
		// $this->load->library('firephp');
		// $this->firephp->info($data['classes']);
		
		$startsch=$this->session->userdata('startsch');

		$this->load->view('include/header');
		$this->load->view('reports/studentteachers', $data);
		$footer_data['regs']=true;
		$this->load->view('include/footer', $footer_data);     
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

	public function getstudentsTeachersPerClass(){
		header('Content-Type: application/x-json; charset=utf-8');
		$postdata = $this->input->post();
	
		// $this->load->library('firephp');
		// $this->firephp->info($postdata);

		$this->load->model('reports/studentteachers_model');
   		$res=$this->studentteachers_model->get_studentsTeachersPerClass($postdata);
		
		echo json_encode($res);		
	}

	public function getstudentsPerClass(){
		header('Content-Type: application/x-json; charset=utf-8');
		$postdata = $this->input->post();
	
		// $this->load->library('firephp');
		// $this->firephp->info($postdata);

		$this->load->model('reports/studentteachers_model');
   		$res=$this->studentteachers_model->get_studentsPerClass($postdata);
		
		echo json_encode($res);		
	}

}