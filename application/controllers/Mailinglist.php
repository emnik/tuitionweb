<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mailinglist extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$session_user = $this->session->userdata('is_logged_in');
		if (!empty($session_user)) {
			// get the group and redirect to appropriate controller
			$this->load->model('login_model');
			$grp = $this
				->login_model
				->get_user_group($this->session->userdata('user_id'));

			switch ($grp->name) {
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
		} else {
			redirect('login');
		}
	}


	public function index($subsection=null){
		$this->load->model('login_model');
		$user = $this->login_model->get_user_name($this->session->userdata('user_id'));
		$data['user'] = $user;

		$startsch = $this->session->userdata('startsch');

		if (!empty($subsection)) redirect('settings');

		$this->load->model('Mailinglist_model');
		$this->load->model('Contact_config_model');

		// check the microsoft web services configuration
		$config = $this->Contact_config_model->get_settings();
		if (empty($config[0]['tenantid'])||empty($config[0]['mailclientsecret'])||empty($config[0]['mailclientid'])){
			$data['config']='error';
		} else {
			$data['config']='success';
		}

		// check the mail settings (mainly sender address)
		$settings = $this->Mailinglist_model->get_settings();
		$note = null;
		if(!empty($settings['senderaddress'])){
			$note=$settings['note'];
			$data['sender'] = $settings['senderaddress'];
		}
		
		$classes = $this->Mailinglist_model->get_classes();
		if ($classes) {
			$data['classes'] = $classes;
		}

		$data['signature'] = $this->generate_signature($note);

		$this->load->view('include/header');
		$this->load->view('communication/mailing_list', $data);
		$footer_data['regs']=true;
		$this->load->view('include/footer', $footer_data);
	}

	public function settings(){
		$this->load->model('login_model');
		$user = $this->login_model->get_user_name($this->session->userdata('user_id'));
		$data['user'] = $user;

		$startsch = $this->session->userdata('startsch');

		$this->load->model('Mailinglist_model');
		$postdata = $this->input->post();

		if (!empty($postdata)) {
			foreach ($postdata as $key => $value) 
			{
				$mailsettings[$key]=$value;
			};
			$this->Mailinglist_model->update_settings($mailsettings);
			$data['mailsettings'] = $mailsettings;
		}
		else 
		{
			$settings = $this->Mailinglist_model->get_settings();
			if ($settings) {
				$data['mailsettings'] = $settings;
			}
		}

		$this->load->view('include/header');
		$this->load->view('communication/mailing_list_settings', $data);
		$footer_data['regs']=true;
		$this->load->view('include/footer', $footer_data);		
	}


	public function generate_signature($note){
		$this->load->model('School_model');
		$schooldata=$this->School_model->get_school_data();
		$school = $schooldata[0]; //[0] as I only have one record!!!

		// Create the HTML variable
		$signature_html = '
			<div style="display: flex; align-items: center;">
				<div style="margin-right: 20px;">
					<img id="signature-preview-logo" src="' . $school['logourl'] . '" style="min-width: 40px; max-width: 40px;" width="40" alt="Logo">
				</div>
				<div style="margin-right: 20px;">
					<p id="signature-preview-maintitle" style="margin: 0; font-size: 15px; font-weight: 600;">' . $school['distinctive_title'] . '</p>
					<p id="signature-preview-sectitle" style="margin: 0; font-weight: 500; font-size: 13px; line-height: 22px;">' . $school['services'] . '</p>
				</div>
				<div style="border-left: 1px solid #f86295; height: 50px; margin-right: 20px;"></div>
				<div>
					<div class="contact-info">
						<a id="signature-preview-tel" href="tel:' . $school['phone'] . '"> ' . $school['phone'] . ' </a> | <a id="signature-preview-mobile" href="tel:' . $school['mobile'] . '">' . $school['mobile'] . '</a>
					</div>
					<div class="contact-info">
						<a id="signature-preview-email" href="mailto:' . $school['email'] . '">' . $school['email'] . '</a>
					</div>
					<div class="contact-info">
						<a id="signature-preview-siteurl" href="' . $school['siteurl'] . '">' . $school['siteurl'] . '</a>
					</div>
					<div class="contact-info">
						<a id="signature-preview-gmap" href="' . $school['googlemapsurl'] . '" target="_blank">
							<span id="signature-preview-address">' . $school['address'] . '</span>,
							<span id="signature-preview-city">' . $school['city'] . '</span>
						</a>
					</div>
				</div>
			</div>
			<p id="signature-preview-note" style="font-size: 10px; padding-top:10px;">
				<i>'. (!empty($note)?$note:'') .'</i>
			</p>
		';
		return $signature_html;
	}


// this is the function that prepares the mail and sends it using the custom GraphEmailLibrary
	public function setupmail(){
		$postdata = $this->input->post();

		if(!empty($postdata['customaddress'])){
			$ccData = $postdata['customaddress'];
			$cc_email_list=array();
			foreach ($ccData as $key => $value) {
				$cc_email_list[]=array('email'=>$value);
			}
		} else {
			$cc_email_list = [];
		}

		$this->load->model('Mailinglist_model');
		if(!empty($postdata['classes'])){
			$email_list = $this->Mailinglist_model->get_emails($postdata['classes']);
		} else {
			$email_list = [];
		}

				
		// Load the GraphEmailLibrary
		$this->load->library('GraphEmailLibrary');

		$settings = $this->Mailinglist_model->get_settings();
		$note = $settings['note'];

		// Define email subject, body, recipient emails, cc_recipient emails, and sender email
		$email_subject = $postdata['subject'];
		$email_body = $postdata['editorData'];
		$sender_email = $settings['senderaddress'];
		$replyto_email = $settings['replytoaddress'];
		$recipient_emails = $email_list;
		$cc_recipient_emails = $cc_email_list;

		
		// Email signature HTML
		$email_signature = $this->generate_signature($note);

		// Append the signature to the email body
		$email_content = $email_body . $email_signature;


		// Test - DO NOT SEND - Simulate successful sending
		// $simulate = array( //testing
		// 	'status' => 'success',
		// 	'message' => 'Testing of email sending seems to be successful!'
		// );
		// $result = json_encode($simulate);
		
		// Use the library to SEND the email
		$result = $this->graphemaillibrary->send_emails($email_subject, $email_content, $recipient_emails, $cc_recipient_emails, $sender_email, $replyto_email);


		// If mail sending was a success, store the data to the mail_history table
		$res = json_decode($result);
		if ($res->status==='success'){
			if(!empty($cc_recipient_emails)){
				$recipients = array_merge($recipient_emails, $cc_recipient_emails);
			} else {
				$recipients = $recipient_emails;
			}
			// Extract the email addresses (first element of each inner array)
			$emailArray = array_map(function($recipient) {
				return $recipient['email']; 
			}, $recipients);

			$historyData = array(
				'subject' => $email_subject,
				'content' => $email_body,
				'recipients' => implode(', ', $emailArray)
			);
			$this->addToMailHistory($historyData);
		}
		header('Content-Type: application/json');
		echo $result; // $result is a JSON object
	}

	public function addToMailHistory($maildata){
	
		$this->load->model('Mailinglist_model');
		$this->Mailinglist_model->add_to_mail_history($maildata);
	}

	// Generate the CSV File for download the mailing list with the selected classes!
	public function getMailinglistData($data=null)
	{
		if ($data){
			$decodedData = urldecode($data);
			$classes = explode(",", $decodedData);
		} else {
			$classes = [];
		}

		if (!empty($classes)){
			$filename = "mailinglist_export.csv";
			// output headers so that the file is downloaded rather than displayed
			header('Content-Type: text/csv; charset=utf-8');
			header("Content-Description: File Transfer");
			header("Content-Disposition: attachment; filename=$filename");

			$this->load->model('Mailinglist_model');
			$res = $this->Mailinglist_model->mailinglist_export_data($classes);

			// I can use the Mailinglist_model/get_emails function if I want like below but the CSV file
			// will only have the emails. I prefer a seperate function so that I also have more fields like the name and/or
			// the class!

			//$output = $this->Mailinglist_model->mailinglist_export_data($classes);
			// foreach($output as $row) 
			// {
			// $res['mailinglist'][] = $row;
			// }

			// use ob_clean() to clean (erase) the output buffer. If not, I get blank lines at the start!!!
			ob_clean();
			$file = fopen('php://output', 'w');

			$header = array(
				"Name",
				"Email",
				"Τάξη"
			);
			fputcsv($file, $header);

			foreach ($res['mailinglist'] as $row) {
				$valuesArray = array();
				foreach ($row as $name => $value) {
					$valuesArray[] = $value;
				}
				fputcsv($file, $valuesArray);
			}

			fclose($file);
			exit;
		}
	}

}