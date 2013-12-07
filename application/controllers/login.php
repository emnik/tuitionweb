<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Αυτός είναι ο βασικός controller για authentication

class Login extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
	}
	

	public function index()
	{
		//$this->output->enable_profiler(TRUE);


		$session_user = $this->session->userdata('is_logged_in');
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
					case 'tutor':
						redirect('tutor');
						break;
					case 'parent':
						redirect('parent');
						break;
				}
		}
		
		$this->load->library('form_validation');
		$this->lang->load('form_validation','greek');
		$this->form_validation->set_rules('username', 'Όνομα χρήστη', 'required|min_length[4]|max_length[12]|alpha_numeric');
		$this->form_validation->set_rules('password', 'Κωδικός χρήστη', 'required|min_length[4]|max_length[12]');
		$data['login_failed'] = false;

		if($this->form_validation->run())
		{
			$this->load->model('login_model');
			$res = $this
					->login_model
					->verify_user
						(
							$this->input->post('username'), 
							$this->input->post('password')
						);

			if ($res !== false)
			{
				//logged in => set user data
				$this
					->session
					->set_userdata(
						array(
						'is_logged_in' => true,
						'user_id' =>  $res->id
						  	 )
								  );
				//get the group and redirect to appropriate controller
				$grp = $this
					->login_model
					->get_user_group($this->session->userdata('user_id'));
				
				sleep(5); //we need this to get the time to check the password!!!
				
				switch ($grp->name)
				{
					case 'admin':
						redirect('welcome');
						break;
					case 'tutor':
						redirect('tutor');
						break;
					case 'parent':
						redirect('parent');
						break;
				}
				
			}
			
			else 

			{
				//set a boolean variable to pass in the auth_login_view as to know when the validation passed
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

		//$this->session->unset_userdata('is_logged_in');
		//$this->session->unset_userdata('user_id');
		$this->session->sess_destroy();

		$this->load->view('include/header');		
		$this->load->view('login');
		$this->load->view('include/footer');
	}

}

/* End of file auth.php */
/* Location: ./application/controllers/auth.php */