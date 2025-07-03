<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class History extends CI_Controller {
	
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
		redirect('history/apy');
    }
	
	public function apy()
	{
		$this->load->model('login_model');
	    $user=$this->login_model->get_user_name($this->session->userdata('user_id'));
        $data['user']=$user;
        
        $startsch=$this->session->userdata('startsch');

		$this->load->view('include/header');
		$this->load->view('reports/apyhistory', $data);
		$footer_data['regs']=true;
		$this->load->view('include/footer', $footer_data);
	}

	public function absences()
	{
		$this->load->model('login_model');
	    $user=$this->login_model->get_user_name($this->session->userdata('user_id'));
        $data['user']=$user;
        
		$startsch=$this->session->userdata('startsch');
		
		$this->load->view('include/header');
		$this->load->view('reports/absenthistory', $data);
		$footer_data['regs']=true;
		$this->load->view('include/footer', $footer_data);
	}

	public function mail()
	{
		$this->load->model('login_model');
	    $user=$this->login_model->get_user_name($this->session->userdata('user_id'));
        $data['user']=$user;
        
		$startsch=$this->session->userdata('startsch');
		
		$this->load->view('include/header');
		$this->load->view('reports/mailhistory', $data);
		$footer_data['regs']=true;
		$this->load->view('include/footer', $footer_data);
	}

	public function sms()
	{
		$this->load->model('login_model');
	    $user=$this->login_model->get_user_name($this->session->userdata('user_id'));
        $data['user']=$user;
        
		$startsch=$this->session->userdata('startsch');
		
		$this->load->view('include/header');
		$this->load->view('reports/smshistory', $data);
		$footer_data['regs']=true;
		$this->load->view('include/footer', $footer_data);
	}

	public function gethistoryapydata()
	{
		header('Content-Type: application/x-json; charset=utf-8');

		$this->load->model('reports/history_model');
   		$res=$this->history_model->get_apyhistorydata();
		
		echo json_encode($res);
	}

	public function getabsenthistorydata()
	{
		header('Content-Type: application/x-json; charset=utf-8');

		$this->load->model('reports/history_model');
   		$res=$this->history_model->get_absenthistorydata();
		
		echo json_encode($res);
	}

	public function getmailhistorydata()
	{
		header('Content-Type: application/x-json; charset=utf-8');

		$this->load->model('reports/history_model');
   		$res=$this->history_model->get_mailhistorydata();
		
		echo json_encode($res);
	}

	public function getsmshistorydata()
	{
		header('Content-Type: application/x-json; charset=utf-8');

		$this->load->model('reports/history_model');
   		$res=$this->history_model->get_smshistorydata();
		
		echo json_encode($res);
	}	
}