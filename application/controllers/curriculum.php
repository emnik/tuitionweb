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
		$this->load->model('curriculum/courselessons_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this
						->courselessons_model
						->get_lessons($this->input->post('jsclassid'), $this->input->post('jscourseid'))
						)
			);

}

public function lessontitles()
{
		$this->load->model('curriculum/courselessons_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this
						->courselessons_model
						->get_lessontitles()
						)
			);

}

public function dellesson()
{
		$this->load->model('curriculum/courselessons_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this
						->courselessons_model
						->dellesson($this->input->post('jslessonid'))
						)
			);

}

public function delcourse()
{
		$this->load->model('curriculum/courselessons_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this
						->courselessons_model
						->delcourse($this->input->post('jscourseid'))
						)
			);

}


public function delcataloglesson()
{
		$this->load->model('curriculum/tutorsperlesson_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this
						->tutorsperlesson_model
						->delcataloglesson($this->input->post('jscatlessonid'))
						)
			);

}


	public function index()
	{
	/*
	If I want to pass a parameter to index through a uri segment then I would have to use
	a url such as: http://domain/tuitionweb/student/index/id
	It must have the index method specified!!! 
	*/
		redirect('curriculum/edit');
	}

	public function edit($subsection=null)
	{
		$this->load->model('login_model');
		$user=$this->login_model->get_user_name($this->session->userdata('user_id'));
		$data['user']=$user;

		switch ($subsection) {
		 	case 'tutorsperlesson':
		 		$this->tutorsperlesson($user);
		 		return 0;
		 		# code...
		 		break;

		 	default:
		 		# code...
		 		break;
		 }


		$this->load->model('curriculum_model');
		$data['class'] = $this->curriculum_model->get_classes();


		if(!empty($_POST))
		{
		// $this->load->library('firephp');
		// $this->firephp->info($_POST);
		$updatedata=array();			
			foreach ($_POST as $key => $value) {
				switch ($key) {
					case 'course':
						foreach ($value as $courseid => $name) {
							$course[]=array('id'=>$courseid, 'class_id'=>$_POST['class_name'], 'course'=>(!empty($name))?$name:'-');
						}
						break;
					case 'title':
						foreach ($value as $courseid2 => $lessondata) {
							foreach ($lessondata as $lessonid => $cataloglessonid) {
								$lesson[]=array('id'=>$lessonid, 'course_id'=>$courseid2, 'cataloglesson_id'=>$cataloglessonid, 'hours'=>(!empty($_POST['hours'][$lessonid]))?$_POST['hours'][$lessonid]:null);
							}							
						}
						break;					
					default:
						# code...
						break;
				}
			 }
		$updatedata = array('coursedata'=>$course, 'lessondata'=>$lesson);
		// $this->firephp->info($updatedata);

		$this->courselessons_model->insertupdatedata($course, $lesson);
		}

		$this->load->view('include/header');
		$this->load->view('curriculum/courselessons', $data);
		$footer_data['regs']=false;
		$this->load->view('include/footer', $footer_data);
	}



	public function cancel($view=null){
		$this->edit($view);
	}


	public function tutorsperlesson($user){
		$data['user'] = $user;
		$this->load->model('curriculum/tutorsperlesson_model');
   		
   		// $this->load->library('firephp');		
		
		$lessons = $this->tutorsperlesson_model->get_cataloglessons();
		if($lessons){$data['lesson'] = $lessons;}
		
		$tutors = $this->tutorsperlesson_model->get_tutors();
		if($tutors){$data['tutor'] = $tutors;}

		$alltutors = $this->tutorsperlesson_model->get_employees();
		if($alltutors){$data['alltutors'] = $alltutors;}
		

		if(!empty($_POST))
		{
			// $this->firephp->info($_POST);	
			$lessonsdata = $_POST['lesson']; 
			$tutorsdata = $_POST['employees']; 
			$this->tutorsperlesson_model->update_lessontutors($lessonsdata, $tutorsdata, $tutors);
		
			//get the new data...
			$lessons = $this->tutorsperlesson_model->get_cataloglessons();
			if($lessons){$data['lesson'] = $lessons;}
			$tutors = $this->tutorsperlesson_model->get_tutors();
			if($tutors){$data['tutor'] = $tutors;}
			$alltutors = $this->tutorsperlesson_model->get_employees();
			if($alltutors){$data['alltutors'] = $alltutors;}
		}
		
		// $this->firephp->info($lessons);
		// $this->firephp->info($tutors);
		// $this->firephp->info($alltutors);

		$this->load->view('include/header');
		$this->load->view('curriculum/tutorsperlesson', $data);
		$footer_data['regs']=false;
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



