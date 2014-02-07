<?php if (!defined('BASEPATH')) die();

Class Section extends CI_Controller {
	
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

public function index() {
	//$this->output->enable_profiler(TRUE);
	
	$this->load->model('login_model');
	$user=$this->login_model->get_user_name($this->session->userdata('user_id'));
	$data['user']=$user;

	$this->load->model('section_model');
	$sections=$this->section_model->get_sections_data();

	if ($sections) {
		
		$data['section'] = $sections;
		}
	else {
		$data['section'] = false;
	}
	
	$this->load->view('include/header');
	$this->load->view('sections', $data);
	$this->load->view('include/footer');

	}

public function courses()
{
		$this->load->model('section/card_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this
						->card_model
						->get_courses($this->input->post('jsclassid'))
						)
			);

}

public function lessons()
{
		$this->load->model('section/card_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this
						->card_model
						->get_lessons($this->input->post('jsclassid'), $this->input->post('jscourseid'))
						)
			);

}

public function tutors()
{
		$this->load->model('section/card_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this
						->card_model
						->get_tutors($this->input->post('jsclassid'), $this->input->post('jscourseid'), $this->input->post('jslessonid'))
						)
			);

}


public function card($id, $subsection=null) {

	if(is_null($id)) redirect('section');

	$this->load->model('login_model');
	$user=$this->login_model->get_user_name($this->session->userdata('user_id'));
	$data['user']=$user;

	//get section's main data (name id ...) in an array to use everywhere in section page
	$this->load->model('section_model');
	$section = $this->section_model->get_section_common_data($id);
	if ($section) {
		$data['section'] = $section;	
	}
	else {
		//the following is needed to show greek in the die() message!
		$msg="  <!DOCTYPE html>
				<html lang='en'>
  				<head>
			  		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
			  	<head>
			  	<body>
			  		<p>Δεν υπάρχει τμήμα με κωδικό ".$id."</p>
			  	</body>
			  	</html>";

		die($msg);
	}
	
	$this->load->model('section/card_model');
	$prevnext = $this->card_model->get_prevnext_section_byname($section['section'], $id, $this->session->userdata('startsch'));
	$data['prevnext']=$prevnext;

	$this->load->library('firephp');
	$this->firephp->info($prevnext);
	
	switch ($subsection) {
	 	case 'sectionstudents':
	 		$this->sectionstudents($id, $section, $user, $prevnext);
	 		return 0;
	 		break;

 	
	 	default:
	 		# code...
	 		break;
	 }

	$this->load->model('section/card_model');
	$data['sectioncard']=array();
	if (!empty($_POST)) {
	 	foreach ($_POST as $key => $value) 
	 	{
	 		$section_data[$key]=$value;
	 	};
	 	
	 	$section_program_update = array();
	 	
	 	foreach ($section_data as $key => $value) {
	 		switch ($key) {
	 			case 'section':
	 				$data['section']['section'] = $value; //get the section name in the sections main data 
	 			case 'class_id':
	 			case 'course_id':
	 			case 'lesson_id':
	 			case 'tutor_id':
	 				$section_update[$key] = $value;
	 				break;
	 			
	 			case 'day':
	 			case'start_tm':
	 			case 'end_tm':
	 			case 'classroom_id':
	 				foreach ($value as $programid => $progvalue) {
	 					$section_program_update[$programid][$key]=$progvalue;
	 				}
	 				break;
	 		}
	 	}
	 	
	 	$this->load->model('welcome_model');
	 	$section_update['schoolyear'] = $this->welcome_model->get_selected_startschyear();


		$this->card_model->update_section_data($section_update, $id, $section_program_update);

	}
	else
	{
		$section_data = $this->card_model->get_section_data($id);		
	}

	$section_program = $this->card_model->get_section_program($id);

	$data['sectioncard'] = $section_data;
	$data['sectionprog'] = $section_program;

	$data['class'] = $this->card_model->get_classes();
	$data['course'] = $this->card_model->get_courses($section_data['class_id']);
	$data['lesson'] = $this->card_model->get_lessons($section_data['class_id'], $section_data['course_id']);
	$data['tutor'] = $this->card_model->get_tutors($section_data['class_id'], $section_data['course_id'], $section_data['lesson_id']);

	$this->load->view('include/header');
	$this->load->view('section/card', $data);
	$this->load->view('include/footer');

	}


	public function delreg($id){
		$this->load->model('section_model');
		$this->section_model->delreg($id);
		redirect('section');
	}


	public function newreg(){
		$this->load->model('section_model');
		$id = $this->section_model->newreg();
		$this-> card($id);
	}

	public function cancel($form=null, $id=null){
	if (is_null($form) || is_null($id)) show_404();
		if ($form=='card'){
			$this->load->model('section_model');
			if($this->section_model->cancelreg($id))
			{
				redirect('section');
			}
			else
			{
				redirect('section/card/'.$id);
			}
		}
	}

	public function delprogramday(){
		$this->load->model('section/card_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this
						->card_model
						->delprogramday($this->input->post('jsprogramid'))
						)
			);
	}


	public function sectionstudents($id, $section, $user, $prevnext){
		
		$data['prevnext'] = $prevnext;
		$data['user']=$user;
		$this->load->model('section/card_model');

		if(!empty($_POST)) {
			$this->card_model->removefromsection($this->input->post('select'));
		};
		
		$students = $this->card_model->getsectionstudents($id);
		if ($students){
			$data['students']=$students;
		}

		$data['section']=$section;

		$this->load->view('include/header');
		$this->load->view('section/sectionstudents', $data);
		$this->load->view('include/footer');

	}	

	public function logout()
	{

		$this->session->destroy();

		$this->load->view('include/header');		
		$this->load->view('login');
		$this->load->view('include/footer');
	}

}