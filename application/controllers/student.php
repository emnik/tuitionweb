<?php if (!defined('BASEPATH')) die();

Class Student extends CI_Controller {
	
public function __construct() {
		
		parent::__construct();
	}


public function index(){
	/*
	If I want to pass a parameter to index through a uri segment then I would have to use
	a url such as: http://domain/tuitionweb/student/index/id
	It must have the index method specified!!! 
	*/
	redirect('registrations');
}

public function newreg(){
	$this->load->model('registrations_model');
	$id = $this->registrations_model->newreg();
	$this-> card($id);
}


public function delreg($id){
	$this->load->model('registrations_model');
	$this->registrations_model->delreg($id);
	redirect('registrations');
}


public function cancel($form=null, $id=null){
	if (is_null($form) || is_null($id)) show_404();
	if ($form=='card'){
		$this->load->model('registrations_model');
		if($this->registrations_model->cancelreg($id))
		{
			redirect('registrations');
		}
		else
		{
			redirect('student/card/'.$id);
		};
	}
	else if ($form=='contact'){
		redirect('student/card/'.$id.'/contact');
	};	
	
}


public function courses()
{
		$this->load->model('student/card_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this
						->card_model
						->get_courses($this->input->post('jsclassid'))
						)
			);

}

public function card($id, $subsection=null, $innersubsection=null) {

	if(is_null($id)) redirect('registrations');

	//get student's main data (name surname id) in an array to use everywhere in student section
	$this->load->model('student_model');
	$student = $this->student_model->get_student_data($id);
	if ($student) {
		$data['student'] = $student;	
	}
	else {
		//the following is needed to show greek in the die() message!
		$msg="  <!DOCTYPE html>
				<html lang='en'>
  				<head>
			  		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
			  	<head>
			  	<body>
			  		<p>Δεν υπάρχει μαθητής με κωδικό ".$id."</p>
			  	</body>
			  	</html>";

		die($msg);
	}
	

	switch ($subsection) {
	 	case 'contact':
	 		$this->contact($id, $student);
	 		return 0;
	 		# code...
	 		break;

	 	case 'attendance':
	 		$this->attendance($id, $innersubsection, $student);
	 		# code...
	 		return 0;
	 		break;
	 	
	 	case 'finance':
	 		$this->finance($id, $innersubsection, $student);
	 		return 0;
	 		# code...
	 		break;
	 	
	 	default:
	 		# code...
	 		break;
	 }

	$this->load->model('student/card_model');
	$data['regcard']=array();
	if (!empty($_POST)) {
		$date_fields = array('start_lessons_dt','del_lessons_dt','reg_dt');
		foreach ($_POST as $key => $value) 
		{
			
			if (in_array($key,$date_fields))
			{
				$value = implode('-', array_reverse(explode('-', $value)));	
				if ($value=='0000-00-00') $value = null;
			};
			$student_data[$key]=$value;
		};
		$this->card_model->update_student_data($student_data);
	}
	else 
	{
		$student_data=$this->card_model->get_registration_data($id);
	}

	$data['regcard'] = $student_data;
	$data['region'] = $this->card_model->get_regions();
	$data['class'] = $this->card_model->get_classes();
	$data['course'] = $this->card_model->get_std_courses($id);
	//echo print_r($data);
	
	$this->load->view('include/header');
	$this->load->view('student/card', $data);
	$this->load->view('include/footer');

	}

public function contact($id, $student) {
	//$this->output->enable_profiler(TRUE);
	$this->load->model('student/contact_model');
	$secondary = $this->contact_model->get_secondary_data($id);

		
	if (!empty($_POST)) {
		foreach ($_POST as $key => $value) 
		{
			$contact[$key]=$value;
		};
		$this->contact_model->update_contact_data($id, $contact);
	}
	else 
	{
		$contact = $this->contact_model->get_contact_data($id);
	}

	$data['student'] = $student;

	if($contact){
		$data['contact'] = $contact;
	}

	if($secondary){
		$data['secondary'] = $secondary;
	}
	
	$this->load->view('include/header');
	$this->load->view('student/contact', $data);
	$this->load->view('include/footer');	
	}



public function attendance($id, $innersubsection=null, $student) {

	$data['student']=$student;

	$this->load->model('student/attendance_model');
	$program = $this->attendance_model->get_program_data($id);
	$attendance_general = $this->attendance_model->get_attendance_general_data($id);

	if ($attendance_general){
		$data['attendance_general'] = $attendance_general;	
	}
	

	if($program){
		$data['program'] = $program;
		$dayprogram = array();
		$j=0;
		for ($i=0; $i < count($program); $i++) { 
			if ($program[$i]['priority']==date('N')){
				$dayprogram[$j]= $program[$i];
				$j++;
			}
		}
		$data['dayprogram'] = $dayprogram;
	}



	$this->load->view('include/header');

	switch ($innersubsection) {
		case 'program':
			$this->load->view('student/program', $data);	
			break;

		case 'manage':
			$possible_sections = $this->attendance_model->getpossiblesections($id);
			//$this->load->library('firephp');
 			//$this->firephp->error($possible_sections);	
 			if ($possible_sections!=false)
 			{
 				$data['group_sections'] = $possible_sections['groups'];
 				$data['all_sections'] = $possible_sections['all'];
 			}
			$this->load->view('student/manage', $data);	
			break;

		
		default:
			if(empty($program)){
				$possible_sections = $this->attendance_model->getpossiblesections($id);
	 			if ($possible_sections!=false)
	 			{
	 				$data['group_sections'] = $possible_sections['groups'];
	 				$data['all_sections'] = $possible_sections['all'];
	 			}
				$this->load->view('student/manage', $data);	
			}
			else {
				$this->load->view('student/attendance', $data);	
			}
			break;
	}

	$this->load->view('include/footer');	
	
	}


//--------------------------PAYMENTS----------------------------


public function finance($id, $innersubsection=null, $student) {
	//$this->output->enable_profiler(TRUE);
 	// $this->load->library('firephp');
 	// $this->firephp->error($_POST);	
	if (!empty($_POST)) {
		$sortedformdata=array();
		$selectors=array();

		//we deal with select action via ajax so we don't want it here...
		unset($_POST['select_action']);

		foreach ($_POST as $field => $formdata) {
			if (!empty($formdata)){
				foreach ($formdata as $key => $value) {
					switch ($field) {
						case 'apy_dt':
								$value = implode('-', array_reverse(explode('-', $value)));
							break;

						case 'amount':
								$value = str_replace("€", "", $value);
							break;

						case 'is_credit':
								//it doesn't matter what the return value of the checkbox is. 
								//If it is set then there is a value so it's checked
								$value = 1; 
							break;

						case 'select':
								$value = 1; 
								$selectors[]=$key;
							break;

						default:
							# code...
							break;
					}

					if ($field!='select'){
						$sortedformdata[$key][$field]=$value;	
					};
					
					//if it is not checked it is not set in the $_POST array
					if (!isset($sortedformdata[$key]['is_credit'])) 
					{
						$sortedformdata[$key]['is_credit']=0;
					};
				};	
			};
		};
		// $this->load->library('firephp');
		// $this->firephp->error($sortedformdata);
		// $this->firephp->error($selectors);
		//send the form data to the model to update the payment table
		$this->load->model('student/finance_model');
		$this->finance_model->update_payments($sortedformdata, $id);
	};

	$data['student']=$student;

	$this->load->model('student/finance_model');
	

	$this->load->view('include/header');
	if (is_null($innersubsection)){
		$payments = $this->finance_model->get_payments($id);
		if ($payments){
			$data['payments']=$payments;
		}
		$this->load->view('student/finance', $data);	
	}
	else if ($innersubsection=='changes')
	{
		$changes = $this->finance_model->get_changes($id);
		if ($changes){
			$data['change']=$changes;
		}
		$this->load->view('student/changes', $data);		
	}

		$this->load->view('include/footer');
	}




	public function getlastapy()
	{
		$this->load->model('student/finance_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this
						->finance_model
						->get_last_apy_no()
						)
			);
	
	}

	public function getfirstpaydata()
	{

		$this->load->model('student/finance_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this
						->finance_model
						->get_firstpay_data($this->input->post('stdid'))
						)
			);
	
	}


	public function payment_batch_actions($action){
		header('Content-Type: application/x-json; charset=utf-8');
		$this->load->model('student/finance_model','', TRUE);
		//$this->load->library('firephp');
 		if ($action=='delete'){
	 		foreach ($this->input->post('select') as $payid => $value) {		
				$this->finance_model->del_payment($payid);
				//$this->firephp->info($payid, 'deleted payment with id');
			};	
 		}
 		else
 		{
 			foreach ($this->input->post('select') as $payid => $value) {
				$this->finance_model->cancel_payment($payid);
				//$this->firephp->info($payid, 'canceled payment with id');
			};		
 		};
 		//MAYBE I'LL HAVE A TRY STATEMENT INSTEAD OF RETURNING SUCCESS...
 		$result=array('success'=>'true');
		echo json_encode($result);
	}


//--------------------------END OF PAYMENTS-------------------------------


	public function getlessonplandata()
	{
        header('Content-Type: application/x-json; charset=utf-8');
		$this->load->model('student/attendance_model','', TRUE);
 		foreach ($this->input->post('selection') as $key => $value) {
			$this->attendance_model->delstdlesson($this->input->post('stdid'), $key);
		};

        echo(json_encode($this
						->attendance_model
						->get_attendance_general_data($this->input->post('stdid'))
						)
		);
	
	}

	public function insertmultiple(){
		header('Content-Type: application/x-json; charset=utf-8');
		$this->load->model('student/attendance_model','', TRUE);
		$sectionids = $this->input->post('sections_ids');
		$id = $this->input->post('stdid'); 
		$result = $this->attendance_model->insertmultiple($id, $sectionids);
        if ($result){
	        echo(json_encode($this
							->attendance_model
							->get_attendance_general_data($id)
							)
	        );        	
        };
	}


	public function insertallbyname(){
		header('Content-Type: application/x-json; charset=utf-8');
		$this->load->model('student/attendance_model','', TRUE);
		$sectionName = $this->input->post('sectionName');
		$id = $this->input->post('stdid'); 
		$result = $this->attendance_model->insertallbyname($id, $sectionName);
        if ($result){
	        echo(json_encode($this
							->attendance_model
							->get_attendance_general_data($id)
							)
	        );        	
        };
	}


//---------------------ABSENCES IN ATTENDANCE GENERAL--------------------

	public function getabsencesdata($id){
		header('Content-Type: application/x-json; charset=utf-8');
		$this->load->model('student/attendance_model','', TRUE);

		//1. get possible absences for current day
		$dayabsences = $this->attendance_model->get_dayabsences($id);	

		//2. if there are absences then get the stdlesson's ids to get the REST lessons of the day if there are any
		$stdlessonsids=array();
		if($dayabsences!=false){
			foreach ($dayabsences as $data) {
				$stdlessonsids[]=$data['stdlesson_id'];
			};
		};

		//3. get the day lessons (excluding the lessons that already exists in the absences table)
		$daylessons = $this->attendance_model->get_daylessonshours($id, $stdlessonsids);

		$dayabsencesdata=array();
		if($daylessons!=false){
			//4. Make a new array from daylessons array by adding in the excused key and set the id as stdlessons_id whereas id will be empty string
			$i=0;
			foreach ($daylessons as $data) {
					$dayabsencesdata[$i]['id']="";
					$dayabsencesdata[$i]['stdlesson_id']=$data['id'];
					$dayabsencesdata[$i]['excused']=0;
					$dayabsencesdata[$i]['title']=$data['title'];
					$dayabsencesdata[$i]['hours']=$data['hours'];
					$i++;
					};				
		
			//5. merge the data from the absences table and the stdlessons table in one
			if($dayabsences!=false){
				$dayabsencesdata = array_merge($dayabsences, $dayabsencesdata);	
			};

		}
		else
		{
			$dayabsencesdata = $dayabsences;
		};
		
		$tableData=array('aaData'=>$dayabsencesdata);
		echo json_encode($tableData);

		//$this->load->library('firephp');
 		//$this->firephp->error($tableData);	
	}

	public function updatedayabsencedata()
	{
		header('Content-Type: application/x-json; charset=utf-8');
		$this->load->library('firephp');
		
		//I get the keys from whatever table. The positive ones exist in the absence array and
		//maybe altered while the negative ones are new data
		$ids = $this->input->post('stdlessonid');

		//counters for the 2 tables (existing and nonexeisting we are about to create)
		$exc=0;
		$nexc=0;
		foreach ($ids as $key => $value) {
			if ($key>0)
			{
				//existing table has all the records that exist in the absence array and
				//will get updated or deleted
				$existing[$exc]['id']=$key;
				$existing[$exc]['stdlesson_id']=$this->input->post('stdlessonid')[$key];
				if(isset($this->input->post('excused')[$key])){
					$existing[$exc]['excused']=$this->input->post('excused')[$key];	
				};
				$existing[$exc]['todaypresense']=$this->input->post('todaypresense')[$key];
				$exc++;
			}
			else
			{
				//nonexisting table has the new data to be inserted
				$nonexisting[$nexc]['id']=$key;
				$nonexisting[$nexc]['stdlesson_id']=$this->input->post('stdlessonid')[$key];
				if(isset($this->input->post('excused')[$key])){
					$nonexisting[$nexc]['excused']=$this->input->post('excused')[$key];	
				};
				$nonexisting[$nexc]['todaypresense']=$this->input->post('todaypresense')[$key];
				$nexc++;				
			}
		};

		$this->load->model('student/attendance_model');
		$updatedata=array();
		$deletedata=array();
		$insertdata=array();

		if (!empty($existing)){
			//counters for tables $deletedata and $updatedata
			$delc=0;
			$updc=0;
			foreach ($existing as $data) 
			{
				//if the student is present the corresponding record in the absences table needs
				//to be deleted
				if($data['todaypresense']=='present')
				{
					$deletedata[$delc]['id']=$data['id'];
					$delc++;
				}
				else 
				//if is still absent then it gets updated with the new data
				{
					$updatedata[$updc]['id']=$data['id'];
					$updatedata[$updc]['reg_id']=$this->input->post('stdid');
					$updatedata[$updc]['stdlesson_id']=$data['stdlesson_id'];
					$updatedata[$updc]['date']=date('Y-m-d');
					if (isset($data['excused']))
					{
						$updatedata[$updc]['excused']=1;
					}
					else
					{
						$updatedata[$updc]['excused']=0;	
					};
					$updc++;
				};
			};
		};

		if(!empty($nonexisting))
		{
			//if we have new data
			$insc=0;
			foreach ($nonexisting as $data) {
				if($data['todaypresense']=='absent')
				//if absent we prepare the insert statements else we do nothing!
				{
					$insertdata[$insc]['id']=null;
					$insertdata[$insc]['stdlesson_id']=$data['stdlesson_id'];
					$insertdata[$insc]['reg_id']=$this->input->post('stdid');
					$insertdata[$insc]['date']=date('Y-m-d');
					if (isset($data['excused']))
					{
						$insertdata[$insc]['excused']=1;
					}
					else
					{
						$insertdata[$insc]['excused']=0;	
					};
					$insc++;					
				};
			};
		};

		//Send the data to be inserted-updated-deleted to the model
		echo json_encode($this->attendance_model->ins_del_upd_absences($insertdata, $updatedata, $deletedata));


		//echo json_encode(array('status'=>'success'));	
		//$this->firephp->error($nonexisting);
		//$this->firephp->error($deletedata);
		//$this->firephp->error($updatedata);
		//$this->firephp->error($insertdata);
	}

//---------------------END OF ABSENCES IN ATTENDANCE GENERAL-------------------


//-----------------------------PAYMENT CHANGES---------------------------------
	public function getfirstchangedata()
	{

		$this->load->model('student/finance_model','', TRUE);    
        header('Content-Type: application/x-json; charset=utf-8');
        echo(json_encode($this
						->finance_model
						->get_firstchange_data($this->input->post('stdid'))
						)
			);
	
	}


	public function changes_batch_actions($action){
		header('Content-Type: application/x-json; charset=utf-8');
		$this->load->model('student/finance_model','', TRUE);
 		if ($action=='delete'){
	 		foreach ($this->input->post('select') as $changeid => $value) {		
				$this->finance_model->del_change($changeid);
			};	
 		};
 		//MAYBE I'LL HAVE A TRY STATEMENT INSTEAD OF RETURNING SUCCESS...
 		$result=array('success'=>'true');
		echo json_encode($result);
	}

//----------------------------END OF PAYMENT CHANGES--------------------------

}