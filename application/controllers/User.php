<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
	
	function __construct()
	{
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
		$user = $this->login_model->get_user_name($this->session->userdata('user_id'));
		$data['user'] = $user;

        $this->load->model('user_model');
        $userdata = $this->user_model->get_user_data();
        $groupdata = $this->user_model->get_group_data();
        $data['dbuser'] = $userdata;
        $data['dbgroup'] = $groupdata;

        // $this->load->library('firephp');
        // $this->firephp->info($groupdata);

		$this->load->view('include/header');
		$this->load->view('user', $data);
		$footer_data['regs']=true;
		$this->load->view('include/footer', $footer_data);
    }


    public function card($id) {

        if(is_null($id)) redirect('user');
    
        $this->load->model('login_model');
        $user=$this->login_model->get_user_name($this->session->userdata('user_id'));
        $data['user']=$user;

        //get term's data in an array
        $this->load->model('user_model');
        
        // $this->load->library('firephp');
        // $this->firephp->info($_POST);
        
        
        if (!empty($_POST)) {
            $validation = false;
            unset($_POST['password_check']);
            $this->load->helper('pbkdf2_helper');
            $date_fields = array('expires');
			foreach ($_POST as $key => $value) 
			{
                if (in_array($key,$date_fields))
			    {
				    $value = implode('-', array_reverse(explode('-', $value)));	
			    };
                if ($key == 'curpassword') {
                    $dbuser = $this->user_model->get_user_data($id);
                    $good_hash = $dbuser[0]['password'];
                    if (validate_password($value, $good_hash)){
                        $validation=true;
                    }
                }
                
                if ($key=='password' && $value!='') $value = create_hash($value); //create new hash only if password is not empty!
                
                $formdata[$key]=$value;
            };

            if (isset($formdata['noexpire'])){
                if ($formdata['noexpire']=='on') {$formdata['expires']=NULL;}
                unset($formdata['noexpire']);
            }
            
            if ($formdata['password']=='') unset ($formdata['password']); //if password is empty keep the old one by not updating the password field!

            if (isset($formdata['curpassword'])){
                unset($formdata['curpassword']);
            } else {
                $validation = true;
            }

            unset($formdata['dataaccess']); //to be removed!

            // $this->firephp->info($formdata);
            
            if ($validation){
                $this->user_model->update_user_data($formdata, $id);
            }
        }
        //get the data (if there was a POST they will have been updated)
        $dbuser = $this->user_model->get_user_data($id);
        $data['userdata']=$dbuser[0];

        $groupdata = $this->user_model->get_group_data();
        $data['groupdata'] = $groupdata;
        $reserved_usernames = $this->user_model->get_usernames();
        $data['reserved'] = $reserved_usernames;

        $this->load->view('include/header');
        $this->load->view('user/card', $data);
        $footer_data['regs']=true;
        $this->load->view('include/footer', $footer_data);
    }

	public function deluser($id){
		$this->load->model('user_model');
		$this->user_model->deluser($id);
		redirect('user');
	}


	public function newuser(){
		$this->load->model('user_model');
		$id = $this->user_model->newuser();
		$this-> card($id);
	}

	public function cancel($form=null, $id=null){
	if (is_null($form) || is_null($id)) show_404();
		if ($form=='card'){
			$this->load->model('user_model');
			if($this->user_model->canceluser($id))
			{
				redirect('user');
			}
			else
			{
				redirect('user/card/'.$id);
			}
		}
	}


    public function students_list(){
        header('Content-Type: application/x-json; charset=utf-8');
        $this->load->model('user_model');
        $termid = $this->user_model->get_termid();
        $list=$this->user_model->get_student_names_ids($this->input->get('q'));
        if ($list) {
            foreach ($list as $stud) {
                if ($stud['termid']==$termid){
                    $currentStds[] = array("id"=>$stud['id'],"text"=>$stud['stdname']);
                } else {
                    $prevStds[] = array("id"=>$stud['id'],"text"=>$stud['stdname'].'-'.$stud['termname']);
                }
            }
            $data[0]=array("text"=>"Επιλεγμένη διαχ. περίοδος", "children"=>$currentStds);
            $data[1]=array("text"=>"Υπόλοιπες διαχ. περίοδοι", "children"=>$prevStds);
        }
        else
        {
            $data=array("id"=>"0","text"=>"Κανένα αποτέλεσμα...");
        }
        echo(json_encode($data));
    }    

}