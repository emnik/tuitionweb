<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Telephones extends CI_Controller
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


	public function index()
	{
		redirect('telephones/catalog');
	}

	// ------------------------------------telephones catalog (students / teachers)--------------------------
	public function catalog()
	{
		$this->load->model('login_model');
		$user = $this->login_model->get_user_name($this->session->userdata('user_id'));
		$data['user'] = $user;

		$startsch = $this->session->userdata('startsch');

		$this->load->view('include/header');
		$this->load->view('reports/telephones', $data);
		$footer_data['regs']=true;
		$this->load->view('include/footer', $footer_data);
	}

	public function getstudentphones()
	{
		header('Content-Type: application/x-json; charset=utf-8');

		$this->load->model('reports/telephones_model');
		$res = $this->telephones_model->get_phonecatalog();

		// $this->load->library('firephp');
		// $this->firephp->info($res);
		//return results
		echo json_encode($res);
	}

	public function getemployeephones()
	{
		header('Content-Type: application/x-json; charset=utf-8');

		$this->load->model('reports/telephones_model');
		$res = $this->telephones_model->get_employeephones();

		//return results
		echo json_encode($res);
	}

	// ------------------------------------telephones exports (bulkSMS / Google contacts)--------------------------	


	public function exports()
	{
		$this->load->model('login_model');
		$user = $this->login_model->get_user_name($this->session->userdata('user_id'));
		$data['user'] = $user;

		$startsch = $this->session->userdata('startsch');

		$this->load->model('reports/telephones_model');
		$classes = $this->telephones_model->get_classes();
		if ($classes) {
			 $data['classes'] = json_encode($classes, JSON_UNESCAPED_UNICODE);
		}

		$this->load->view('include/header');
		$this->load->view('reports/telephone_exports', $data);
		$footer_data['regs']=true;
		$this->load->view('include/footer', $footer_data);
	}


	public function getGoogleData()
	{
		$filename = "gcontacts_export.csv";
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=$filename");

		$this->load->model('reports/telephones_model');
		$res = $this->telephones_model->google_export_data();

		// use ob_clean() to clean (erase) the output buffer. If not, I get blank lines at the start!!!
		ob_clean();
		$file = fopen('php://output', 'w');

		$header = array(
			"Name",
			"Given Name",
			"Family Name",
			"Notes",
			"Group Membership",
			"Address 1 - Type",
			"Address 1 - Formatted",
			"Address 1 - Street",
			"Address 1 - City",
			"Phone 1 - Type", "Phone 1 - Value",
			"Phone 2 - Type", "Phone 2 - Value",
			"Phone 3 - Type", "Phone 3 - Value"
		);
		fputcsv($file, $header);

		foreach ($res['google'] as $row) {
			$valuesArray = array();
			foreach ($row as $name => $value) {
				$valuesArray[] = $value;
			}
			fputcsv($file, $valuesArray);
		}

		fclose($file);
		exit;
	}


	public function getBulkSMSData()
	{
		$filename = "bulkSMS_export.csv";

		$postdata = $this->input->post();
		$classes = $postdata['classes'];
		$this->load->model('reports/telephones_model');

		$res = $this->telephones_model->bulkSMS_export_data($classes);

		// use ob_clean() to clean (erase) the output buffer. If not, I get blank lines at the start!!!
		ob_clean();
		$stream = fopen('php://temp/maxmemory:' . (5 * 1024 * 1024), 'r+');

		$convertToLatin = false;
		$includeHeaders = false;
		$setPhonePriorities = false;
		foreach ($postdata['options'] as $checkoption) {
			if ($checkoption['name'] == 'convertToLatin') $convertToLatin = true;
			if ($checkoption['name'] == 'includeHeaders') $includeHeaders = true;
			if ($checkoption['name'] == 'setPhonePriorities') $setPhonePriorities = true;
		}

		if ($includeHeaders) {
			$header = array("Phone", "Name");
			fputcsv($stream, $header);
		}

		$missingPhones=[];
		$option = $postdata['options'][0];
		if ($option['name'] == 'exampleRadios') {
			switch ($option['value']) {
				case 'option1': // Γονείς
					foreach ($res['bulkSMS'] as $row) {
						$phone = ($row['mothers-mobile'] == null ? $row['fathers-mobile'] : $row['mothers-mobile']);
						if ($phone != null) {
							if ($convertToLatin) {
								$name = $this->make_greeklish($row['Name']);
								$name = $name . ' - Parent';
							} else {
								$name = $row['Name'];
								$name = $name . ' - Γονέας';
							}
							fputcsv($stream, array(
								$phone,
								$name
							));
						}
						else
						{
							array_push($missingPhones, $row['Name']);
						}
					}
					break;
				case 'option2': // Μαθητές
					foreach ($res['bulkSMS'] as $row) {
						if ($setPhonePriorities){
							$phone = ($row['mobile'] == null ? ($row['mothers-mobile'] == null ? $row['fathers-mobile'] : $row['mothers-mobile']) : $row['mobile']);
						}
						else
						{
							$phone = $row['mobile'];
						}
							if ($phone != null) {
							if ($convertToLatin) {
								$name = $this->make_greeklish($row['Name']);
							} else {
								$name = $row['Name'];
							}
							fputcsv($stream, array(
								$phone,
								$name
							));
						}
						else
						{
							array_push($missingPhones, $row['Name']);
						}
					}
					break;
				case 'option3': // Γονείς και Μαθητές
					foreach ($res['bulkSMS'] as $row) {
						$phone = $row['mobile'];
						if ($phone != null) {
							if ($convertToLatin) {
								$name = $this->make_greeklish($row['Name']);
							} else {
								$name = $row['Name'];
							}
							fputcsv($stream, array($phone, $name)); // Μαθητής
						}
						else
						{
							array_push($missingPhones, $row['Name']);
						}
						$parentPhone = ($row['mothers-mobile'] == null ? $row['fathers-mobile'] : $row['mothers-mobile']);
						if ($parentPhone != null) {
							if ($convertToLatin) {
								$name = $this->make_greeklish($row['Name']);
								$name = $name . ' - Parent';
							} else {
								$name = $row['Name'];
								$name = $name . ' - Γονέας';
							}
							fputcsv($stream, array($parentPhone, $name)); // Γονέας
						}
						else
						{
							array_push($missingPhones, $row['Name'].' - Γονέας');
						}
					}
					break;
			}
		}

		rewind($stream); //resets the file pointer to the beginning of the file
		$output['missingPhones']=$missingPhones;
		$output['csv'] = stream_get_contents($stream);
		$output['message'] = 'just a message';
		
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=$filename");
		echo json_encode($output, JSON_UNESCAPED_UNICODE);
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
