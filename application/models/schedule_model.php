<?php

class Schedule_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }

   public function get_schedule_data($daynum){

        //get active term's startyear
        $termyear = $this->db->select('YEAR(`term`.`start`) AS startyear')->where('term.active',1)->get('term')->row()->startyear;

        $this->db->select(array('employee.id', 'employee.nickname', 'section_program.day', 'weekday.priority', 'section_program.start_tm',   'section_program.end_tm', 'section.section', 'catalog_lesson.title', 'classroom.classroom', 'section_program.section_id', 'section_program.duration', 'class.class_name', 'class.id AS class_id'))
            ->from('section_program')
            ->join('classroom','section_program.classroom_id=classroom.id')
            ->join('weekday','section_program.day=weekday.name')
            ->join('section','section_program.section_id=section.id')
            ->join('term', 'section.term_id=term.id')
            ->join('class', 'section.class_id=class.id')
            ->join('lesson_tutor','section.tutor_id=lesson_tutor.id')
            ->join('employee','lesson_tutor.employee_id=employee.id')
            ->join('catalog_lesson','lesson_tutor.cataloglesson_id=catalog_lesson.id')
            ->where('term.active', '1');
            // if the current term is old, show all tutor names!
            if ($termyear==date('Y')){$this->db->where('employee.active',1);};
            // ->where('employee.active', '1')
            $query = $this->db->where('weekday.priority', $daynum)
            ->order_by('weekday.priority', 'asc')
            ->order_by('classroom.classroom', 'asc')
            ->order_by('section_program.start_tm', 'asc')
            ->get();

        if ($query -> num_rows() > 0)
        {
            foreach($query->result_array() as $row) 
                {
                    $schedule[] = $row;
                }
            return $schedule;
        }
        else 
        {
            return false;
        }    
   }


}