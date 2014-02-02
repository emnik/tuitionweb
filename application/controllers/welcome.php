<?php if (!defined('BASEPATH')) die();

Class Welcome extends CI_Controller {
	
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

public function user_list(){
	header('Content-Type: application/x-json; charset=utf-8');
	$this->load->model('welcome_model');
	$list=$this->welcome_model->get_student_names_ids($this->input->get('q'));
	if ($list) {
		foreach ($list as $stud) {
			$data[]=array("id"=>$stud['id'],"text"=>$stud['stdname']);
		}
	}
	else
	{
		$data=array("id"=>"0","text"=>"Κανένα αποτέλεσμα...");
	}
	echo(json_encode($data));
}


public function index() {
	//$this->output->enable_profiler(TRUE);
	
	$this->load->model('login_model');
	$data['user']=$this->login_model->get_user_name($this->session->userdata('user_id'));

	$this->load->model('welcome_model');

	$schoolyears=$this->welcome_model->get_schoolyears();
	if ($schoolyears) {
		$data['schoolyears'] = $schoolyears;
		}
	else {
		$data['schoolyears']=array();
	}
	
	$justyears=array();
	foreach ($schoolyears as $tmpdata) {
		array_push($justyears, $tmpdata['schoolyear']);
	};

	$selected_schstart = $this->welcome_model->get_selected_startschyear(); 
	$data['selected_schstart']= $selected_schstart; 

	$startsch = $this->input->post('startschoolyear');
	if (!empty($startsch) and $startsch!="addnextschoolyear") {
				if ($selected_schstart!=$startsch){
					$this->welcome_model->set_schoolyear($startsch);	
				};
			
				if (in_array($startsch.'-'.($startsch+1), $justyears)==false){
					$this->welcome_model->insert_schoolyear($startsch);
				};
		$this->session->set_userdata(array('startsch' => $startsch));

		switch ($this->input->post('submit')) {
			case 'submit1': //Μαθητολόγιο
				redirect('student');
				break;
			
			case 'submit2': // Προσωπικό
				redirect('staff');
				break;

			case 'submit3': // Τμήματα
				redirect('section');
				break;

			case 'submit4': // Οικονομικά
				redirect('finance');
				break;

			case 'submit5': // Αναφορές
				#code...
				break;

			case 'submit6': // Διαγωνίσματα
				redirect('exam');
				break;
		}
		
	}
	
	$this->load->view('include/header');
	$this->load->view('welcome', $data);
	$footer_data['regs']=true;
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