<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Contact_config extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$session_user = $this->session->userdata('is_logged_in');
		if (!empty($session_user)) {
			// get the group and redirect to appropriate controller
			$this->load->model('login_model');
			$grp = $this
				->login_model
				->get_user_group($this->session->userdata('user_id'));

			switch ($grp->name) {
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
		} else {
			redirect('login');
		}
    }


    public function index(){
        $this->load->model('login_model');
		$user = $this->login_model->get_user_name($this->session->userdata('user_id'));
		$data['user'] = $user;

		$startsch = $this->session->userdata('startsch');

		$this->load->model('Contact_config_model');

		if (!empty($_POST)) {
			foreach ($_POST as $key => $value) 
			{
				$microsoftdata[$key]=$value;
			};
			$this->Contact_config_model->update_microsoft_data($microsoftdata);
			$data['ews'] = $microsoftdata;
		}
		else 
		{
			$microsoftdata=$this->Contact_config_model->get_settings();
            	
            if (!empty($microsoftdata)){
                $data['ews'] = $microsoftdata[0]; //[0] as I only have one record!!!
            }
		}


		// $this->load->library('Firephp_lib');
		// $this->firephp_lib->dump('data', $data['ews']);


		$this->load->view('include/header');
		$this->load->view('contact_config', $data);
		$footer_data['regs']=true;
		$this->load->view('include/footer', $footer_data);
    }



}
