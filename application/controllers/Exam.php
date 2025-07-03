<?php if (!defined('BASEPATH')) die();

class Exam extends CI_Controller
{

    public function __construct()
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

        $this->load->model('login_model');
        $user = $this->login_model->get_user_name($this->session->userdata('user_id'));
        $data['user'] = $user;

        $this->load->model('exam_model');
        $exams = $this->exam_model->get_exams_data();

        if ($exams) {
            $data['exam'] = $exams;
        } else {
            $data['exam'] = false;
        }

        $this->load->view('include/header');
        $this->load->view('exams', $data);
        $footer_data['regs'] = true;
        $this->load->view('include/footer', $footer_data);
    }

    public function newexam()
    {
        $this->load->model('exam_model');
        $id = $this->exam_model->newexam();
        $this->card($id);
    }


    public function delexam($examid)
    {
        $this->load->model('exam_model');
        $this->exam_model->delexam($examid);
        redirect('exam'); //maybe $this->index(); is better? Does it even work?
    }



    public function card($id, $subexam = null)
    {

        // $this->output->enable_profiler(TRUE);
        if (is_null($id)) redirect('exam');

        $this->load->model('login_model');
        $user = $this->login_model->get_user_name($this->session->userdata('user_id'));
        $data['user'] = $user;

        $this->load->model('exam/card_model');

        $data['examcard'] = array();


        if (!empty($_POST)) {
            //we deal with select action via ajax so we don't want it here...
            unset($_POST['select_action']);
            $supervisors = null; //this is needed if we delete all the supervisors from an exam!
            foreach ($_POST as $key => $value) {
                switch ($key) {
                    case 'date':
                        $value = implode('-', array_reverse(explode('-', $value)));
                        if ($value == '0000-00-00') $value = null;
                    case 'name':
                    case 'start':
                    case 'end':
                        $exam_data[$key] = $value;
                        break;
                    case 'lesson_id':
                        foreach ($value as $lid => $lval) {
                            $exam_lesson_data[$lid][$key] = $lval;
                        }
                        break;
                    case 'supervisors':
                        $supervisors = $value;
                        break;
                }
            };

            $this->card_model->update_supervisors_data($supervisors, $id);
            $this->card_model->update_exam_data($exam_data, $exam_lesson_data, $id);
        }
        $exam_data = $this->card_model->get_exam_data($id);


        $data['examcard'] = $exam_data;

        $class = $this->card_model->get_classes();
        $data['defaultclass'] = $class;

        $supervisors = $this->card_model->get_supervisors($id);
        if ($supervisors) {
            $s = array();
            foreach ($supervisors as $key => $value) {
                array_push($s, $value['employee_id']);
            }
            $data['supervisor'] = $s;
        }

        //get the names of all tutors to populate the supervisors select field
        $supervisorlist = $this->card_model->get_supervisors_names_ids();
        if ($supervisorlist) {
            foreach ($supervisorlist as $tut) {
                $data['tutor'][] = array("id" => $tut['id'], "text" => $tut['nickname']);
            }
        }
        $data['examprog'] = $this->card_model->get_exam_prog($id);
        if (!empty($data['examprog'])) {
            foreach ($data['examprog'] as $id => $value) {
                $data['class'][$id] = $class;
                $data['course'][$id] = $this->card_model->get_courses($value[0]['class_id']);
                $data['lesson'][$id] = $this->card_model->get_lessons($value[0]['class_id'], $value[0]['course_id']);
            }
        }

        $this->load->view('include/header');
        $this->load->view('exam/card', $data);
        $footer_data['regs'] = true;
        $this->load->view('include/footer', $footer_data);
    }


    public function cancel($form = null, $id = null)
    {
        if (is_null($form) || is_null($id)) show_404();
        if ($form == 'card') {
            $this->load->model('exam_model');
            if ($this->exam_model->cancelexam($id)) {
                redirect('exam');
            } else {
                redirect('exam/card/' . $id);
            };
        }
        // else if ($form=='contact'){
        //     redirect('student/card/'.$id.'/contact');
        // };	

    }


    public function courses()
    {
        $this->load->model('exam/card_model', '', TRUE);
        header('Content-Type: application/x-json; charset=utf-8');
        echo (json_encode(
            $this
                ->card_model
                ->get_courses($this->input->post('jsclassid'))
        )
        );
    }

    public function lessons()
    {
        $this->load->model('exam/card_model', '', TRUE);
        header('Content-Type: application/x-json; charset=utf-8');
        echo (json_encode(
            $this
                ->card_model
                ->get_lessons($this->input->post('jsclassid'), $this->input->post('jscourseid'))
        )
        );
    }


    public function lesson_batch_actions($action)
    {
        header('Content-Type: application/x-json; charset=utf-8');
        $this->load->model('exam/card_model', '', TRUE);

        if ($action == 'delete') {
            foreach ($this->input->post('select') as $lessonid => $value) {
                $this->card_model->del_lesson($lessonid);
            };
        } elseif ($action == 'move') { // action=='move'
            $examid = $this->input->post('examid');
            foreach ($this->input->post('select') as $lessonid => $value) {
                $this->card_model->move_lesson($lessonid, $examid);
            };
        }
        //MAYBE I'LL HAVE A TRY STATEMENT INSTEAD OF RETURNING SUCCESS...
        $result = array('success' => 'true');
        echo json_encode($result);
    }

    //-----------------------supervisors----------------------//

    public function supervisors()
    {

        $this->load->model('login_model');
        $user = $this->login_model->get_user_name($this->session->userdata('user_id'));
        $data['user'] = $user;

        $this->load->model('exam/supervisors_model');

        if (!empty($_POST)) {
            $supervisors = $this->input->post('supervisors'); //this is needed if we delete all the supervisors from an exam!
            $this->supervisors_model->update_supervisors_data($supervisors);
        }

        $exams = $this->supervisors_model->get_exams_data();

        //get the names of all tutors to populate the supervisors select field
        $supervisorlist = $this->supervisors_model->get_supervisors_names_ids();
        if ($supervisorlist) {
            foreach ($supervisorlist as $tut) {
                $data['tutor'][] = array("id" => $tut['id'], "text" => $tut['nickname']);
            }
        }

        if ($exams) {
            $data['supervisor'] = array();
            foreach ($exams as $key => $exam) {
                $supervisors = $this->supervisors_model->get_supervisors($exam['id']);
                if ($supervisors) {
                    $s = array();
                    foreach ($supervisors as $key => $value) {
                        array_push($s, $value['employee_id']);
                    }
                    $data['supervisor'][$exam['id']] = $s;
                }
                $data['exams'][$exam['id']] = $exam;
            }
        } else {
            $data['exams'] = false;
        }

        $this->load->view('include/header');
        $this->load->view('exam/supervisors', $data);
        $footer_data['regs'] = true;
        $this->load->view('include/footer', $footer_data);
    }
}
