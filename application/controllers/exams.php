<?php if (!defined('BASEPATH')) die();

Class Exams extends CI_Controller {
	
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


public function details($id, $subsection=null){
	if(is_null($id)) redirect('exams');

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
	$data['employee'] = $this->details_model->get_employees();

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
				case 'notes':
					$examupdate[$key]=$value;
					break;
				case 'supervisor_ids':
					foreach ($value as $data1) {
						 if ($tmp==='')
						    {
						        $tmp = $data1;
						    }
						    else
						    {
						        $tmp .= ',' . $data1;
						    }
					}
					$examupdate[$key]=$tmp;
					break;
						
			}
		}

		// $this->load->library('firephp');
		// $this->firephp->info($examupdate);
		$this->details_model->update_exam($id, $examupdate);
		
		$exam = $examupdate;
	}
	else
	{
		$exam = $this->details_model->get_exam_data($id); //check again why I dont use  $examdata?
	}

	// $this->load->library('firephp');
	// $this->firephp->info($exam);

	$data['exam']=$exam;	
	

	if(!empty($exam['class_id'])){
		$data['course'] = $this->details_model->get_courses($exam['class_id']);
		$data['lesson'] = $this->details_model->get_lessons($exam['class_id'], $exam['course_id']);
	}

	
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
	redirect('exams');
	}

public function participants($id, $user, $examdata){
	$data['user']=$user;
	$data['exam']=$examdata;
	$this->load->model('exam/participants_model');
	$participants = $this->participants_model->	get_participants_data($examdata['id']);
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
	if (is_null($form) || is_null($id)) show_404();
	if ($form=='exam'){
		$this->load->model('exams_model');
		if($this->exams_model->cancelexam($id))
		{
			redirect('exams');
		}
		else
		{
			redirect('exams/details/'.$id);
		};
	}
	// else if ($form=='contact'){
	// 	redirect('student/card/'.$id.'/contact');
	// };	
	
}

}