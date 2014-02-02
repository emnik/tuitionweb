<?php if (!defined('BASEPATH')) die();

Class Exam extends CI_Controller {
	
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


public function index(){

	$this->load->model('login_model');
	$user=$this->login_model->get_user_name($this->session->userdata('user_id'));
	$data['user']=$user;

	$this->load->model('exams_model');
	$exams=$this->exams_model->get_exams_data($this->session->userdata('startsch'));

	if ($exams) {
		$data['exams'] = $exams;
		}
	else {
		$data['exams'] = false;
	}
	
	$this->load->view('include/header');
	$this->load->view('exams', $data);
	$footer_data['regs']=false;
	$this->load->view('include/footer', $footer_data);
	}


public function courses()
{
		$this->load->model('exam/details_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this
						->details_model
						->get_courses($this->input->post('jsclassid'))
						)
			);

}

public function lessons()
{
		$this->load->model('exam/details_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this
						->details_model
						->get_lessons($this->input->post('jsclassid'), $this->input->post('jscourseid'))
						)
			);

}


public function sections()
{
		$this->load->model('exam/participants_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this
						->participants_model
						->get_all_sections_by_lesson($this->input->post('jslessonid'), $this->session->userdata('startsch'))
						)
			);

}

public function details($id, $subsection=null){
	if(is_null($id)) redirect('exam');

	$this->load->model('login_model');
	$user=$this->login_model->get_user_name($this->session->userdata('user_id'));

	$data['user']=$user;


	//get student's main data (name surname id) in an array to use everywhere in student section
	$this->load->model('exam/details_model');
	$examdata = $this->details_model->get_exam_data($id);
	if (!$examdata) {
		//the following is needed to show greek in the die() message!
		$msg="  <!DOCTYPE html>
				<html lang='en'>
  				<head>
			  		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
			  	<head>
			  	<body>
			  		<p>Δεν υπάρχει καταχωρημένο διαγώνισμα με κωδικό ".$id."</p>
			  	</body>
			  	</html>";

		die($msg);
	}


	// $this->load->library('firephp');
	// $this->firephp->info($prevnext);

	switch ($subsection) {
	 	case 'participants':
	 		$this->participants($id, $user, $examdata);
	 		return 0;
	 		# code...
	 		break;

	 	default:
	 		# code...
	 		break;
	 }

	$data['class'] = $this->details_model->get_classes();

	$examupdate=array('id'=>$id);
	if(!empty($_POST)){
		$tmp='';
		foreach ($_POST as $key => $value) {
			switch ($key) {
				case 'date':
					$value = implode('-', array_reverse(explode('-', $value)));
				case 'class_id':
				case 'course_id':
				case 'lesson_id':
				case 'start_tm':
				case 'end_tm':
				case 'description':
					$examupdate[$key]=$value;
					break;
			}
		}
			$this->details_model->update_exam($id, $examupdate);	
		

		//after we post the data from view to controller if there is a lesson_id and the participants table is empty we map ALL the
		//available sections to the participants. If one wants can change the participants from the corresponting view!
		if(!empty($examupdate['lesson_id']))
		{
			$this->load->model('exam/participants_model');
			$getparticipants = $this->participants_model->get_participants_data($id);
				if ($getparticipants==false)
				{
					$sections = $this->participants_model->get_all_sections_by_lesson($examupdate['lesson_id'], $this->session->userdata('startsch'));	
					if($sections)
					{
						// $this->firephp->info($sections);
						foreach ($sections as $key => $value) 
						{
							$sectionids[]=$key;
						}
						// $this->firephp->info($sectionids);
						$this->participants_model->insertexamsectionids($id, $sectionids);
					}

				}
		}
		
		$exam = $examupdate;
		if (!isset($exam['description']))
		{
			$exam['description']="";	
		}
	}
	else
	{
		$exam = $this->details_model->get_exam_data($id); //check again why I dont use  $examdata?
	}

	$data['exam']=$exam;	
	

	if(!empty($exam['class_id'])){
		$data['course'] = $this->details_model->get_courses($exam['class_id']);
		$data['lesson'] = $this->details_model->get_lessons($exam['class_id'], $exam['course_id']);
	}
	
	if (is_null($exam['lesson_id'])) //if it is a new exam ...
	{
		$prevnext=array('prev'=>'', 'next'=>'');
	}
	else
	{
		$prevnext = $this->details_model->get_prevnext_exam_bydate($id, $this->session->userdata('startsch'));	
	}
	$data['prevnext']=$prevnext;
	
	$this->load->view('include/header');
	$this->load->view('exam/details', $data);
	$footer_data['regs']=false;
	$this->load->view('include/footer', $footer_data);
	}


public function newexam(){
	$this->load->model('exams_model');
	$id = $this->exams_model->newexam($this->session->userdata('startsch'));
	$this-> details($id);
	}


public function delexam($id){
	$this->load->model('exams_model');
	$this->exams_model->delexam($id);
	redirect('exam');
	}

public function participants($id, $user, $examdata){
	$data['user']=$user;
	$data['exam']=$examdata;

	if (is_null($examdata['lesson_id'])) //if it is a new exam ...
	{
		$prevnext=array('prev'=>'', 'next'=>'');
	}
	else
	{
		$this->load->model('details_model');
		$prevnext = $this->details_model->get_prevnext_exam_bydate($id, $this->session->userdata('startsch'));	
	}
	$data['prevnext']=$prevnext;

	$this->load->model('exam/participants_model');
	$participants = $this->participants_model->	get_participants_data($id);
	if($participants)
	{
		$data['participants']=$participants;
	}

	$lessonid=$examdata['lesson_id'];
	if(!empty($lessonid)){
		$sections = $this->participants_model->get_all_sections_by_lesson($lessonid, $this->session->userdata('startsch'));
		$tutors = $this->participants_model->get_tutors_by_lesson($lessonid, $this->session->userdata('startsch'));
		if($tutors) {$data['tutor'] = $tutors;}
		if($sections) {$data['section'] = $sections;}
	}

	$this->load->view('include/header');
	$this->load->view('exam/participants', $data);
	$footer_data['regs']=false;
	$this->load->view('include/footer', $footer_data);
	}


public function supervisors(){
	$this->load->model('login_model');
	$user=$this->login_model->get_user_name($this->session->userdata('user_id'));
	$data['user']=$user;

	$this->load->model('exam/supervisors_model');
	$employees = $this->supervisors_model->get_employees();
	if($employees) $data['employee']=$employees;


	if(!empty($_POST))
	{
		foreach ($_POST as $key => $value) {
			switch ($key) {
				case 'date':
					foreach ($value as $datekey => $date) {
						$datenewdata[]=implode('-', array_reverse(explode('-', $date)));
					}
					break;
				
				case 'supervisor_ids':
					foreach ($value as $datekey => $idsarray) {
						foreach ($idsarray as $key => $id) {
							$insertdata[]=array('date'=>implode('-', array_reverse(explode('-', $_POST['date'][$datekey]))), 'employee_id'=>$id);
						}
					}
				break;

				default:
					# code...
					break;
			}
		}

		if(!empty($insertdata))
		{
			$this->supervisors_model->insert_supervisors($insertdata, $datenewdata);	
		}
	}

	$dates = $this->supervisors_model->get_exam_dates();
	if($dates)
	{
		$data['date'] = $dates;
		$supervisors = $this->supervisors_model->get_supervisors($dates);
		if($supervisors)
		{
			$data['supervisor']=$supervisors;
		}	
	}


	$this->load->view('include/header');
	$this->load->view('exam/supervisors', $data);
	$footer_data['regs']=false;
	$this->load->view('include/footer', $footer_data);
}

public function insertallsections()
{
		$this->load->model('exam/participants_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
		$section = $this->input->post('sectionNames');
		foreach ($section as $key => $value) {
			$sectionids[]=$key;
		}
		$id = $this->input->post('examid'); 
		$result = $this->participants_model->insertexamsectionids($id, $sectionids);
        if ($result){
	        echo(json_encode($this
							->participants_model
							->get_participants_data($id)
							)
	        );      
	    }     
}

public function insertmultiplesections()
{
		$this->load->model('exam/participants_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
		$section = $this->input->post('sections_id');
		foreach ($section as $key => $value) {
			$sectionids[]=$value;
		}
		$id = $this->input->post('examid'); 
		$result = $this->participants_model->insertexamsectionids($id, $sectionids);
        if ($result){
	        echo(json_encode($this
							->participants_model
							->get_participants_data($id)
							)
	        );      
	    }
}

public function insertbytutors()
{
		$this->load->model('exam/participants_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
		$tutors = $this->input->post('tutor_id');
		$id = $this->input->post('examid');
		$lessonid = $this->input->post('lessonid');
		$sectionids = $this->participants_model->get_sections_by_tutors($tutors, $lessonid, $this->session->userdata('startsch'));
		if ($sectionids)
		{
			$result = $this->participants_model->insertexamsectionids($id, $sectionids);
        	if ($result){
	        echo(json_encode($this
							->participants_model
							->get_participants_data($id)
							)
	        	);      
	  		}
		}
}

public function removeparticipants()
{
	    header('Content-Type: application/x-json; charset=utf-8');
		$this->load->model('exam/participants_model','', TRUE); 
		$id=$this->input->post('examid');
 		foreach ($this->input->post('selection') as $key => $value) {
			$this->participants_model->delexamparticipant($id, $key);
		};
        echo(json_encode($this
				->participants_model
				->get_participants_data($id)
				)
			);   
}

public function cancel($form=null, $id=null){
	if (is_null($form) && is_null($id)) show_404();
	if ($form=='exam'){
		if(!is_null($id)){
			$this->load->model('exams_model');
			if($this->exams_model->cancelexam($id))
			{
				redirect('exam');
			}
			else
			{
				redirect('exam/details/'.$id);
			}	
		}
	}
	elseif ($form=='supervisors') 
	{
		redirect('exam/supervisors');
	}
}

	public function logout()
	{

		$this->session->destroy();

		$this->load->view('include/header');		
		$this->load->view('login');
		$this->load->view('include/footer');
	}
}