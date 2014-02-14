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

public function lessontitles()
{
		$this->load->model('curriculum_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this
						->curriculum_model
						->get_lessontitles()
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


		if(!empty($_POST))
		{
		$this->load->library('firephp');
		$this->firephp->info($_POST);
		$updatedata=array();			
			foreach ($_POST as $key => $value) {
				switch ($key) {
					case 'course':
						foreach ($value as $courseid => $name) {
							$course[]=array('id'=>$courseid, 'class_id'=>$_POST['class_name'], 'course'=>$name);
						}
						break;
					case 'title':
						foreach ($value as $courseid2 => $lessondata) {
							foreach ($lessondata as $lessonid => $cataloglessonid) {
								$lesson[]=array('id'=>$lessonid, 'course_id'=>$courseid2, 'cataloglesson_id'=>$cataloglessonid, 'hours'=>$_POST['hours'][$lessonid]);
							}							
						}
						break;					
					default:
						# code...
						break;
				}
			 }
		$updatedata = array('coursedata'=>$course, 'lessondata'=>$lesson);
		$this->firephp->info($updatedata);
		}

		$this->load->view('include/header');
		$this->load->view('curriculum', $data);
		$footer_data['regs']=false;
		$this->load->view('include/footer', $footer_data);
	}



	public function cancel(){
		$this->index();
	}

	public function logout()
	{
		$this->session->destroy();

		$this->load->view('include/header');		
		$this->load->view('login');
		$this->load->view('include/footer');
	}

}



