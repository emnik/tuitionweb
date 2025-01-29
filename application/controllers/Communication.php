<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Communication extends CI_Controller
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


	// // ------------------------------------telephones exports (bulkSMS / Google contacts)--------------------------	


	public function index()
	{
		$this->load->model('login_model');
		$user = $this->login_model->get_user_name($this->session->userdata('user_id'));
		$data['user'] = $user;

		$startsch = $this->session->userdata('startsch');

		$this->load->model('communication_model');
		$classes = $this->communication_model->get_classes();
		if ($classes) {
			 $data['classes'] = json_encode($classes, JSON_UNESCAPED_UNICODE);
		}

		$this->load->view('include/header');
		$this->load->view('communication/communication', $data);
		$footer_data['regs']=true;
		$this->load->view('include/footer', $footer_data);
	}



	public function getBulkSMSData()
	{
		$filename = "bulkSMS_export.csv";
		$countryCode = "+30";
		$postdata = $this->input->post();
		$classes = $postdata['classes'];
		$this->load->model('communication_model');
	
		$res = $this->communication_model->bulkSMS_export_data($classes);
	
		$convertToLatin = false;
		$includeHeaders = false;
		$setPhonePriorities = false;
		foreach ($postdata['options'] as $checkoption) {
			if ($checkoption['name'] == 'convertToLatin') $convertToLatin = true;
			if ($checkoption['name'] == 'includeHeaders') $includeHeaders = true;
			if ($checkoption['name'] == 'setPhonePriorities') $setPhonePriorities = true;
		}
	
		$data = $this->createBulkSMSContent($res, $postdata, $countryCode, $convertToLatin, $setPhonePriorities);
	
		$csv = $this->generateCSV($data, $includeHeaders);
	
		$output['missingPhones'] = $data['missingPhones'];
		$output['csv'] = $csv;
		$output['message'] = 'just a message';
	
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=$filename");
		echo json_encode($output, JSON_UNESCAPED_UNICODE);
	}
	

	public function prepareSMS(){
		$countryCode = "+30";
		$postdata = $this->input->post();
		$classes = $postdata['classes'];
		$list= $postdata['smsformdata'][0];

		$listName='';
		$listid = null;
		$smsList = '';
		$addPhones = '';
		$text = '';

		foreach ($postdata['smsformdata'] as $index => $arr) {
			switch ($arr['name']) {
				case 'list':
					$smsList = $arr['value'];
					if ($smsList !== '') {
						$listOutput = $this->createList($smsList);
						$listOutput = json_decode($listOutput, true);
						if ($listOutput['success'] === true) {
							$listid = $listOutput['data']['id'];
						}
					}
					break;
				case 'addPhones':
					$addPhones = $arr['value'];
					break;
				case 'text':
					$text = $arr['value'];
					break;
			}
		}
		if ($addPhones !== ''){
			$distinctPhones = explode(',', $addPhones);
		}
		

		if (!empty($classes)){
			$this->load->model('communication_model');
			$res = $this->communication_model->bulkSMS_export_data($classes);
		
			$convertToLatin = false;
			$includeHeaders = false;
			$setPhonePriorities = false;
			foreach ($postdata['options'] as $checkoption) {
				if ($checkoption['name'] == 'convertToLatin') $convertToLatin = true;
				if ($checkoption['name'] == 'includeHeaders') $includeHeaders = true;
				if ($checkoption['name'] == 'setPhonePriorities') $setPhonePriorities = true;
			}
		
			$data = $this->createBulkSMSContent($res, $postdata, $countryCode, $convertToLatin, $setPhonePriorities);
			$output['missingPhones'] = $data['missingPhones'];
		} else {
			$data = ['data'=>[]];
			$output['missingPhones'] = [];
		}

		if($listid!=null){
			// Transform the array
			$transformedData = array_map(function($entry) use ($listid) {
				return [
					'phone' => $entry[0],
					'firstname' => $entry[1],
					'list_id' => $listid
				];
			}, $data['data']);

			if ($addPhones !== ''){
				$c=0;
				foreach ($distinctPhones as $phone) {
					$c++;
					$transformedData[] = [
						'phone' => $phone,
						'firstname' => 'manual_added_phone_#'.$c,
						'list_id' => $listid
					];
				}
			}

			// Create the contacts
			$resContacts = $this->createContacts($transformedData);

			if ($resContacts['success'] === true) {
				$output['message'] = 'Η δημιουργία της λίστας επαφών ήταν επιτυχής!';
				$output['errors'] = null;
			} else {
				$output['message'] = 'Η δημιουργία της λίστας επαφών ήταν <b>μερικώς επιτυχής</b>.</br> Υπήρξαν σφάλματα:';
				$output['errors'] = $resContacts['errors'];
			}
			$output['list_id'] = $listid;
		} else {
			$output['message'] = 'Η δημιουργία της λίστας επαφών απέτυχε!';
			$output['errors'] = [['error'=>'required', 'name'=>'H ονομασία της λίστας επαφών είναι κενή!']];
			$output['list_id'] = null;
		}
		
		// Estimate the cost
		$estimateCost = $this->getListEstimate($listid, $text);
		if (isset($estimateCost['success'])) {
			// The communication with the API was successful but the response contains an error
			$output['estimateCost'] = null;
		} else {
			$estimateCost = json_decode($estimateCost, true); // Convert JSON string to PHP array
			$output['estimateCost'] = $estimateCost;
		}		
		
		// output headers
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($output, JSON_UNESCAPED_UNICODE);
	}

	public function sendSMS(){
		$list_id = $this->input->post('list_id');
		$message = $this->input->post('message');
		$this->load->library('SMSto_lib');
		$result = $this->smsto_lib->send_sms($list_id, $message);
		echo $result; // Encode it back to JSON to send as response

		// If the SMS was sent successfully, get the list of contacts and save the SMS to the history
		$result_json = json_decode($result, true); // Convert JSON string to PHP array
		if ($result_json['success'] === true){
			$get_send_data = $this->smsto_lib->get_list_contacts($list_id);
			$get_send_data_json = json_decode($get_send_data, true); // Convert JSON string to PHP array
			if (isset($get_send_data_json['success']) && $get_send_data_json['success'] === true){
				$contacts = [];
    			// Iterate through the data array
				foreach ($get_send_data_json['data'] as $item) {
					$contacts[] = [
						'firstname' => $item['firstname'],
						'phone' => $item['phone']
					];
				}
				$smsdata = [
					'subject' => $get_send_data_json['data'][0]['list_contacts'][0]['name'],
					'content' => $message,
					'recipients' => json_encode($contacts)
				];
				$this->addToSMSHistory($smsdata);
			}
		}
	}

	public function addToSMSHistory($smsdata){
	
		$this->load->model('Communication_model');
		$this->Communication_model->add_to_sms_history($smsdata);
	}	

	public function cancelSMS(){
		$list_id = $this->input->post('list_id');
		$this->load->library('SMSto_lib');
		$result = $this->smsto_lib->delete_list($list_id);
		$result_json = json_decode($result, true); // Convert JSON string to PHP array
		echo json_encode($result_json); // Encode it back to JSON to send as response
	}	

	public function getBalance(){
		$this->load->library('SMSto_lib');
		$result = $this->smsto_lib->get_balance();
		$result_json = json_decode($result, true); // Convert JSON string to PHP array
		echo json_encode($result_json); // Encode it back to JSON to send as response
	}

	public function getEstimate(){
		$this->load->library('SMSto_lib');
		$result = $this->smsto_lib->get_estimate();
		$result_json = json_decode($result, true); // Convert JSON string to PHP array
		echo json_encode($result_json); // Encode it back to JSON to send as response
	}

	public function getListEstimate($id, $message){
		$this->load->library('SMSto_lib');
		$result = $this->smsto_lib->estimate_list_message($id, $message);
		return $result; // Encode it back to JSON to send as response
	}

	public function createList($name){
		$this->load->library('SMSto_lib');
		$result = $this->smsto_lib->create_list($name);
		return $result;
	}

	public function createContacts($ndata){
		$this->load->library('SMSto_lib');

		// Loop through contacts and create each one
		$c=0;
		$errors =[];
		foreach ($ndata as $contact) {
			$res = $this->smsto_lib->create_contact($contact);
			$res = json_decode($res, true);
			
			if (isset($res['success'])){
				if($res['success']===true){
					// echo 'Contact created successfully';
					$c++;
				} 
			} else {
				// echo 'Contact creation failed';
				array_push($errors,['error'=>$res['errors']['phone'], 'name'=>$contact['firstname']]);
			}
		}
		
		if (empty($errors)){
			$result = ['success'=>true, 'errors'=>null];
		} else {
			$result = ['success'=>false, 'errors'=>$errors];
		}

		return $result;
	}

	private function createBulkSMSContent($res, $postdata, $countryCode, $convertToLatin, $setPhonePriorities)
	{
		$missingPhones = [];
		$data = [];
		$option = $postdata['options'][0];
		if ($option['name'] == 'exampleRadios') {
			switch ($option['value']) {
				case 'option1': // Γονείς
					foreach ($res['bulkSMS'] as $row) {
						$phone = ($row['mothers-mobile'] == null ? $row['fathers-mobile'] : $row['mothers-mobile']);
						if ($phone != null) {
							$phone = $countryCode . $phone;
							if ($convertToLatin) {
								$name = $this->make_greeklish($row['Name']);
								$name = $name . ' - Parent';
							} else {
								$name = $row['Name'];
								$name = $name . ' - Γονέας';
							}
							$data[] = array($phone, $name);
						} else {
							array_push($missingPhones, $row['Name']);
						}
					}
					break;
				case 'option2': // Μαθητές
					foreach ($res['bulkSMS'] as $row) {
						if ($setPhonePriorities) {
							$phone = ($row['mobile'] == null ? ($row['mothers-mobile'] == null ? $row['fathers-mobile'] : $row['mothers-mobile']) : $row['mobile']);
						} else {
							$phone = $row['mobile'];
						}
						if ($phone != null) {
							$phone = $countryCode . $phone;
							if ($convertToLatin) {
								$name = $this->make_greeklish($row['Name']);
							} else {
								$name = $row['Name'];
							}
							$data[] = array($phone, $name);
						} else {
							array_push($missingPhones, $row['Name']);
						}
					}
					break;
				case 'option3': // Γονείς και Μαθητές
					foreach ($res['bulkSMS'] as $row) {
						$phone = $row['mobile'];
						if ($phone != null) {
							$phone = $countryCode . $phone;
							if ($convertToLatin) {
								$name = $this->make_greeklish($row['Name']);
							} else {
								$name = $row['Name'];
							}
							$data[] = array($phone, $name); // Μαθητής
						} else {
							array_push($missingPhones, $row['Name']);
						}
						$parentPhone = ($row['mothers-mobile'] == null ? $row['fathers-mobile'] : $row['mothers-mobile']);
						if ($parentPhone != null) {
							$parentPhone = $countryCode . $parentPhone;
							if ($convertToLatin) {
								$name = $this->make_greeklish($row['Name']);
								$name = $name . ' - Parent';
							} else {
								$name = $row['Name'];
								$name = $name . ' - Γονέας';
							}
							$data[] = array($parentPhone, $name); // Γονέας
						} else {
							array_push($missingPhones, $row['Name'] . ' - Γονέας');
						}
					}
					break;
			}
		}
		return ['data' => $data, 'missingPhones' => $missingPhones];
	}
	
	private function generateCSV($data, $includeHeaders)
	{
		ob_clean();
		$stream = fopen('php://temp/maxmemory:' . (5 * 1024 * 1024), 'r+');
	
		if ($includeHeaders) {
			$header = array("Phone", "Name");
			fputcsv($stream, $header);
		}
	
		foreach ($data['data'] as $row) {
			fputcsv($stream, $row);
		}
	
		rewind($stream); //resets the file pointer to the beginning of the file
		return stream_get_contents($stream);
	}


	function make_greeklish($text)
	{
		// https://gist.github.com/teomaragakis/7580134
		// I made some changes regarding the uppercase letters as I only want it for names...
		$expressions = array(
			// '/[αΑ][ιίΙΊ]/u' => 'e',
			'/[α][ιί]/u' => 'e',
			'/[Α][ιίΙΊ]/u' => 'E',
			// '/[οΟΕε][ιίΙΊ]/u' => 'i',
			'/[οε][ιί]/u' => 'i',
			'/[ΟΕ][ιίΙΊ]/u' => 'I',
			// '/[αΑ][υύΥΎ]([θΘκΚξΞπΠσςΣτTφΡχΧψΨ]|\s|$)/u' => 'af$1',
			'/[α][υύ]([θκξπσςτφχψ]|\s|$)/u' => 'af$1',
			'/[Α][υύΥΎ]([θΘκΚξΞπΠσςΣτTφΡχΧψΨ]|\s|$)/u' => 'Af$1',
			// '/[αΑ][υύΥΎ]/u' => 'av',
			'/[α][υύ]/u' => 'av',
			'/[Α][υύΥΎ]/u' => 'Av',
			// '/[εΕ][υύΥΎ]([θΘκΚξΞπΠσςΣτTφΡχΧψΨ]|\s|$)/u' => 'ef$1',
			'/[ε][υύ]([θκξπσςτφχψ]|\s|$)/u' => 'ef$1',
			'/[Ε][υύΥΎ]([θΘκΚξΞπΠσςΣτTφΡχΧψΨ]|\s|$)/u' => 'Ef$1',
			// '/[εΕ][υύΥΎ]/u' => 'ev',
			'/[ε][υύ]/u' => 'ev',
			'/[Ε][υύΥΎ]/u' => 'Ev',
			// '/[οΟ][υύΥΎ]/u' => 'ou',
			'/[ο][υύ]/u' => 'ou',
			'/[Ο][υύΥΎ]/u' => 'Ou',
			// '/(^|\s)[μΜ][πΠ]/u' => '$1b',
			'/(^|\s)[μ][π]/u' => '$1b',
			'/(^|\s)[Μ][πΠ]/u' => '$1B',
			// '/[μΜ][πΠ](\s|$)/u' => 'b$1',
			'/[μ][π](\s|$)/u' => 'b$1',
			'/[Μ][πΠ](\s|$)/u' => 'B$1',
			// '/[μΜ][πΠ]/u' => 'mp',
			'/[μ][π]/u' => 'mp',
			'/[Μ][πΠ]/u' => 'Mp',
			// '/[νΝ][τΤ]/u' => 'nt',
			'/[ν][τ]/u' => 'nt',
			'/[Ν][τΤ]/u' => 'Nt',
			// '/[τΤ][σΣ]/u' => 'ts',
			'/[τ][σ]/u' => 'ts',
			'/[Τ][σΣ]/u' => 'Ts',
			// '/[τΤ][ζΖ]/u' => 'tz',
			'/[τ][ζ]/u' => 'tz',
			'/[Τ][ζΖ]/u' => 'Tz',
			// '/[γΓ][γΓ]/u' => 'ng',
			'/[γ][γ]/u' => 'ng',
			'/[Γ][γΓ]/u' => 'Ng',
			// '/[γΓ][κΚ]/u' => 'gk',
			'/[γ][κ]/u' => 'gk',
			'/[Γ][κΚ]/u' => 'Gk',
			// '/[ηΗ][υΥ]([θΘκΚξΞπΠσςΣτTφΡχΧψΨ]|\s|$)/u' => 'if$1',
			'/[η][υ]([θΘκΚξΞπΠσςΣτTφΡχΧψΨ]|\s|$)/u' => 'if$1',
			'/[Η][υΥ]([θΘκΚξΞπΠσςΣτTφΡχΧψΨ]|\s|$)/u' => 'If$1',
			// '/[ηΗ][υΥ]/u' => 'iu',
			'/[η][υ]/u' => 'iu',
			'/[Η][υΥ]/u' => 'Iu',
			// '/[θΘ]/u' => 'th',
			'/[θ]/u' => 'th',
			'/[Θ]/u' => 'Th',
			// '/[χΧ]/u' => 'ch',
			'/[χ]/u' => 'ch',
			'/[Χ]/u' => 'Ch',
			// '/[ψΨ]/u' => 'ps',
			'/[ψ]/u' => 'ps',
			'/[Ψ]/u' => 'Ps',
			// '/[αά]/u' => 'a',
			'/[αά]/u' => 'a',
			'/[ΑΆ]/u' => 'A',
			// '/[βΒ]/u' => 'v',
			'/[β]/u' => 'v',
			'/[Β]/u' => 'V',
			// '/[γΓ]/u' => 'g',
			'/[γ]/u' => 'g',
			'/[Γ]/u' => 'G',
			// '/[δΔ]/u' => 'd',
			'/[δ]/u' => 'd',
			'/[Δ]/u' => 'D',
			// '/[εέΕΈ]/u' => 'e',
			'/[εέ]/u' => 'e',
			'/[ΕΈ]/u' => 'E',
			// '/[ζΖ]/u' => 'z',
			'/[ζ]/u' => 'z',
			'/[Ζ]/u' => 'Z',
			// '/[ηήΗΉ]/u' => 'i',
			'/[ηή]/u' => 'i',
			'/[ΗΉ]/u' => 'I',
			// '/[ιίϊΙΊΪ]/u' => 'i',
			'/[ιίϊ]/u' => 'i',
			'/[ΙΊΪ]/u' => 'I',
			// '/[κΚ]/u' => 'k',
			'/[κ]/u' => 'k',
			'/[Κ]/u' => 'K',
			// '/[λΛ]/u' => 'l',
			'/[λ]/u' => 'l',
			'/[Λ]/u' => 'L',
			// '/[μΜ]/u' => 'm',
			'/[μ]/u' => 'm',
			'/[Μ]/u' => 'M',
			// '/[νΝ]/u' => 'n',
			'/[ν]/u' => 'n',
			'/[Ν]/u' => 'N',
			// '/[ξΞ]/u' => 'x',
			'/[ξ]/u' => 'x',
			'/[Ξ]/u' => 'X',
			// '/[οόΟΌ]/u' => 'o',
			'/[οό]/u' => 'o',
			'/[ΟΌ]/u' => 'O',
			// '/[πΠ]/u' => 'p',
			'/[π]/u' => 'p',
			'/[Π]/u' => 'P',
			// '/[ρΡ]/u' => 'r',
			'/[ρ]/u' => 'r',
			'/[Ρ]/u' => 'R',
			// '/[σςΣ]/u' => 's',
			'/[σς]/u' => 's',
			'/[σςΣ]/u' => 'S',
			// '/[τΤ]/u' => 't',
			'/[τ]/u' => 't',
			'/[Τ]/u' => 'T',
			// '/[υύϋΥΎΫ]/u' => 'i',
			'/[υύϋ]/u' => 'i',
			'/[ΥΎΫ]/u' => 'I',
			// '/[φΦ]/iu' => 'f',
			'/[φ]/u' => 'f',
			'/[Φ]/u' => 'F',
			// '/[ωώ]/iu' => 'o',
			'/[ωώ]/u' => 'o',
			'/[ΩΏ]/u' => 'O',
		);

		$text = preg_replace(array_keys($expressions), array_values($expressions), $text);
		return $text;
	}


}
