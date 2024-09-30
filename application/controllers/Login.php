<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Αυτός είναι ο βασικός controller για authentication

class Login extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		// $this->load->library('session');
	}
	

	public function index()
	{
		// $this->output->enable_profiler(TRUE);
		// $this->load->library('firephp');
		// $this->load->helper('language');

		$session_user = $this->session->userdata('is_logged_in');
		// $this->firephp->info($session_user);
		// $this->firephp->info(empty($session_user));		

		if(!empty($session_user))
		{
			//get the group and redirect to appropriate controller
				$this->load->model('login_model');
				$grp = $this
					->login_model
					->get_user_group($this->session->userdata('user_id'));
				
				switch ($grp->name)
				{
					case 'admin':
						redirect('welcome');
						break;
					// case 'tutor':
					// 	redirect('tutor');
					// 	break;
					// case 'parent':
					// 	redirect('parent');
					// 	break;
				}
		}
		
		// $this->load->library('form_validation'); //autoloads by config
		$this->lang->load('fieldnames','greek'); //the file is application/language/greek/fieldnames_lang.php
		// for the greek messages to work we need to define greek as the default language in application/config/config.php
		
		//the lang:fieldname below is used by the application/language/greek/fieldnames_lang.php file to translate the field!
		$this->form_validation->set_rules('username', 'lang:username', 'required|min_length[5]|max_length[30]|alpha_numeric');
		$this->form_validation->set_rules('password', 'lang:password', 'required|min_length[8]|max_length[16]');
		

		$data['login_failed'] = false; 

		$f = $this->form_validation->run();
		
		if($f)
		{
			$this->load->model('login_model');
			$res = $this
					->login_model
					->verify_user
						(
							$this->input->post('username'), 
							$this->input->post('password')
						);

						// $this->load->library('firephp');
						// $this->firephp->info($res);						

			$data['login_failed'] = true; // default: treat login as failed!

			if ($res !== false)
			{
				if ($res['username_err']){
					$data['username_failed'] = true;
				} elseif ($res['password_err']) {
					$data['username_failed'] = false;
					$data['password_failed'] = true;
				} else {
					$data['login_failed'] = false;
					//get the schoolyear and store it as session variable
					$this->load->model('welcome_model');
					// $startsch = $this->welcome_model->get_selected_startschyear();
					// $this->session->set_userdata(array('startsch'=>$startsch));

					//logged in => set user data
					$this
						->session
						->set_userdata(
							array(
							'is_logged_in' => true,
							'user_id' =>  $res['data']->id
								)
									);
					//get the group and redirect to appropriate controller
					$grp = $this
						->login_model
						->get_user_group($this->session->userdata('user_id'));
					
					//sleep(5); //we need this to get the time to check the password!!!
					
					switch ($grp->name)
					{
						case 'admin':
							redirect('welcome');
							break;
						// case 'tutor':
						// 	redirect('tutor');
						// 	break;
						// case 'parent':
						// 	redirect('parent');
						// 	break;
					}
					
				}
			}
			
			else // probably not needed

			{
				//set a boolean variable to pass in the auth_login_view as to know when the server validation passed
				//but the user login verification failed!
				$data['login_failed'] = true;
			}
				
		}


		$this->load->view('include/header');
		$this->load->view('login', $data);
		$this->load->view('include/footer');
	}	


	public function logout()
	{
		$this->session->sess_destroy();

		$this->load->view('include/header');		
		$this->load->view('login');
		$this->load->view('include/footer');
	}

}

/* End of file auth.php */
/* Location: ./application/controllers/auth.php */