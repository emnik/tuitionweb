<?php if (!defined('BASEPATH')) die();

Class Teams extends CI_Controller {
	
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
	/*
	If I want to pass a parameter to index through a uri segment then I would have to use
	a url such as: http://domain/tuitionweb/student/index/id
	It must have the index method specified!!! 
	*/

	$this->load->model('login_model');
	$user=$this->login_model->get_user_name($this->session->userdata('user_id'));
	$data['user']=$user;

	$this->load->model('Contact_config_model');
	// check the microsoft web services configuration
	$mainCheck = $this->Contact_config_model->get_settings();
	if (!empty($mainCheck[0]['tenantid']) && !empty($mainCheck[0]['mailclientsecret']) && !empty($mainCheck[0]['mailclientid'])){
		$data['configGraphAPI']='success';
	} else {
		$data['configGraphAPI']='error';
	}
	// check the SMS.to configuration
	$secondaryCheck = $this->Contact_config_model->get_sms_settings();
	if (!empty($secondaryCheck[0]['apikey'])){
		$data['configSMS']='success';
	} else {
		$data['configSMS']='error';
	}

	$this->load->view('include/header');
	$this->load->view('teams', $data);
	$footer_data['regs']=true;
	$this->load->view('include/footer', $footer_data);

}

public function getAllTeams()
{
	header('Content-Type: application/x-json; charset=utf-8');

	$this->load->model('teams_model');
	$res=$this->teams_model->getAllTeams();

	echo json_encode($res);
}

public function getCurrentStudents()
{
	header('Content-Type: application/x-json; charset=utf-8');

	$this->load->model('teams_model');
	$res=$this->teams_model->getCurrentStudents();

	echo json_encode($res);
}

public function getObsoleteUsers()
{
	header('Content-Type: application/x-json; charset=utf-8');

	$this->load->model('teams_model');
	$res=$this->teams_model->getObsoleteUsers();

	echo json_encode($res);
}

public function getCurrentTeachers()
{
	header('Content-Type: application/x-json; charset=utf-8');

	$this->load->model('teams_model');
	$res=$this->teams_model->getCurrentTeachers();

	echo json_encode($res);
}


public function getDeletedUsers(){
	header('Content-Type: application/x-json; charset=utf-8');

	$this->load->library('graphTeamsLibrary');
	$res=$this->graphteamslibrary->do('get_deleted_users');

	echo $res;	
}


public function resetTeamsData(){
	header('Content-Type: application/x-json; charset=utf-8');

	$this->load->library('graphTeamsLibrary');
	$res=$this->graphteamslibrary->do('reset');

	echo $res;
}

public function batchDeleteUsers(){
	$data = $this->input->post('data');

	$this->load->library('graphTeamsLibrary');
	$res=$this->graphteamslibrary->do('delete', $data);
	header('Content-Type: application/x-json; charset=utf-8');

	echo $res;
}

public function getDomain(){

	$this->load->library('graphTeamsLibrary');
	$res=$this->graphteamslibrary->do('get_domain');
	header('Content-Type: application/x-json; charset=utf-8');
	echo $res;
}

public function updateUser(){
	// Retrieve the posted data
	$data = $this->input->post(array(
		'userId', 'surname', 'givenName', 'displayName', 'mail', 'otherMails', 'mobilePhone', 'password', 'forceChangePasswordNextSignIn'
	));

	// Prepare the body for the graph update API request based on the data that are not null
	$updateData = array_filter($data, function($value) {
		return !is_null($value) && $value !== '';
	});

	// Map the input data to the Microsoft Graph API fields
	$updateDataMapped = array(
		'surname' => $updateData['surname'] ?? null,
		'givenName' => $updateData['givenName'] ?? null,
		'displayName' => $updateData['displayName'] ?? null,
		'mail' => $updateData['mail'] ?? null,
		'otherMails' => isset($updateData['otherMails']) ? array_map('trim', explode(',', $updateData['otherMails'])) : null,
		'mobilePhone' => $updateData['mobilePhone'] ?? null,
		'passwordProfile' => array(
			'password' => $updateData['password'] ?? null,
			'forceChangePasswordNextSignIn' => isset($updateData['forceChangePasswordNextSignIn']) ? filter_var($updateData['forceChangePasswordNextSignIn'], FILTER_VALIDATE_BOOLEAN) : null,
		),
	);

	// Remove null values, including nested array elements
	$updateDataMapped = array_filter($updateDataMapped, function($value) {
		if (is_array($value)) {
			return !empty(array_filter($value, function($nestedValue) {
				return !is_null($nestedValue) && $nestedValue !== '';
			}));
		}
		return !is_null($value) && $value !== '';
	});

	// Extract userId for the patch URL
	$userId = $updateData['userId'];
	unset($updateData['userId']);

	// Assuming the graphTeamsLibrary expects a JSON string
	$updateDataJson = json_encode($updateDataMapped);

	$this->load->library('graphTeamsLibrary');
	$res = $this->graphteamslibrary->do('update', $updateDataJson, $userId);

	header('Content-Type: application/x-json; charset=utf-8');

	echo $res;

	// testing response
	// echo json_encode(array(
	// 	'status' => 'success',
	// 	'message' => 'User updated successfully!'
	// ));
}

public function sendUsingSMSto(){
	$data = $this->input->post(array(
		'to', 'message'
	));
	
	$to = $data['to'];
	$message = $data['message'];

	$this->load->library('SMSto_lib');
	$result = $this->smsto_lib->send_single_sms($to, $message);

	header('Content-Type: application/x-json; charset=utf-8');
	echo $result;	
}

public function send_single_email() {
	
	$this->load->model('Teams_model');
	$this->load->library('GraphEmailLibrary');

	// Get the POST data
	$email_address = $this->input->post('email_address');
	$email_body = $this->input->post('email_body');
	$email_subject = $this->input->post('email_subject');

	// Get the sender email from the Teams_model
	$mail_settings = $this->Teams_model->get_mail_settings();
	$sender_email = $mail_settings['senderaddress'];
	$replyto_email = $mail_settings['replytoaddress'];

	// Prepare the email list
	$email_list = array(
		array('email' => $email_address)
	);

	// Prepare the CC email list (empty in this case)
	$cc_email_list = array();

	// Send the email using GraphEmailLibrary
	$result = $this->graphemaillibrary->send_emails($email_subject, $email_body, $email_list, $cc_email_list, $sender_email, $replyto_email);

	// Return the result as JSON
	echo $result;
}

public function saveMessageToHistory() {
    // Retrieve the posted data
    $data = $this->input->post(array('id', 'message_body'));

    // Prepare the data for the model
    $id = $data['id'];
    $message_body = $data['message_body'];

    // Load the model
    $this->load->model('Teams_model');

    // Save the message history
    $result = $this->Teams_model->save_message_history($id, $message_body);

	$response = array();
	if ($result) {
		$response = array(
			'status' => 'success',
			'message' => 'Message saved successfully!'
		);
	} else {
		$response = array(
			'status' => 'error',
			'message' => 'An error occurred while saving the message!'
		);
	}

	// Return the result as JSON
	header('Content-Type: application/x-json; charset=utf-8');
	echo json_encode($response);
}

public function getMsgHistoryData() {
	// Retrieve the posted data
	$data = $this->input->post(array('id'));

	// Prepare the data for the model
	$id = $data['id'];

	// Load the model
	$this->load->model('Teams_model');

	// Get the message history
	$result = $this->Teams_model->get_message_history($id);

	$response = array();

	if ($result) {
		$response = array(
			'status' => 'success',
			'message' => 'Message history retrieved successfully!',
			'data' => $result
		);
	} else {
		$response = array(
			'status' => 'error',
			'message' => 'An error occurred while retrieving the message history!'
		);
	}

	// Return the result as JSON
	header('Content-Type: application/x-json; charset=utf-8');
	echo json_encode($response);
}

public function getStudentLocalData() {
    // Retrieve the posted data
    $data = $this->input->post(array('surname', 'givenName'));

    // Prepare the data for the model
    $surname = $data['surname'];
    $name = $data['givenName'];

    // Load the model
    $this->load->model('Teams_model');

    // Save the message history
    $result = $this->Teams_model->getStudentLocalData($surname, $name);

	$response = array();
	if ($result) {
		$response = array(
			'status' => 'success',
			'message' => 'Student local found!',
			'data' => $result
		);
	} else {
		$response = array(
			'status' => 'error',
			'message' => 'No local student data found!'
		);
	}

	// Return the result as JSON
	header('Content-Type: application/x-json; charset=utf-8');
	echo json_encode($response);
}

public function getDataForNewAccount(){
    // Retrieve the posted data
    $data = $this->input->post(array('id', 'group'));

    // Prepare the data for the model
    $id = $data['id'];
	$group = $data['group'];

	$this->load->model('Teams_model');
	$result=$this->Teams_model->getDataForNewAccount($id, $group);

	$response = array();
	if ($result) {
		$userPrincipalName = $this->convertToLatin($result['name'] . mb_substr($result['surname'], 0, 12));
		$userPrincipalName = strtolower($userPrincipalName);
		$result['userPrincipalName'] = $userPrincipalName;
		$response = array(
			'status' => 'success',
			'message' => 'Data retrieved successfully!',
			'data' => $result
		);
	} else {
		$response = array(
			'status' => 'error',
			'message' => 'An error occurred while retrieving the data!'
		);
	}

	header('Content-Type: application/x-json; charset=utf-8');
	echo json_encode($response);
}

private function convertToLatin($string) {
	$transliterationTable = array(
	  'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => 'Th',
	  'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => 'X', 'Ο' => 'O', 'Π' => 'P',
	  'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'Ch', 'Ψ' => 'Ps', 'Ω' => 'O',
	  'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => 'th',
	  'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => 'x', 'ο' => 'o', 'π' => 'p',
	  'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'u', 'φ' => 'f', 'χ' => 'ch', 'ψ' => 'ps', 'ω' => 'o',
	  'ς' => 's', 'ά' => 'a', 'έ' => 'e', 'ή' => 'h', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'u', 'ώ' => 'o',
	  'Ά' => 'A', 'Έ' => 'E', 'Ή' => 'H', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ώ' => 'O'
	);
	return strtr($string, $transliterationTable);
  }

}