<?php

class History_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }

   public function get_apyhistorydata()
   // if a recent apy is due to a previous term it will not show up here!!! 
   // for a example if one pays for a previous term and the apy is issued with the current date and number!!
   {
    $query=$this->db
    ->select(array('registration.surname', 'registration.name', 'payment.id', 'payment.apy_no', 'payment.apy_dt', 'payment.amount', 'payment.is_credit', 'payment.reg_id', 'payment.month_range', 'payment.notes' ))
    ->from('payment')
    ->join('registration', 'registration.id=payment.reg_id')
    ->join('term', 'term.id=registration.term_id')
    ->where('term.active', 1)
    ->order_by('payment.id', 'desc')
    ->get();


    if ($query->num_rows() > 0) 
    {
      foreach($query->result_array() as $row) 
      {
         $output['aaData'][] = $row;
      }
      return $output;
    }
    else 
    {
         return false;
    }
   }

   public function get_absenthistorydata()
   {
    $this->db->distinct();
    $query=$this->db->select(array('registration.surname', 'registration.name', 'absences.id','absences.date', 'absences.excused', 'catalog_lesson.title', "CONCAT_WS('-', DATE_FORMAT(`section_program`.`start_tm`, '%H:%i'), DATE_FORMAT(`section_program`.`end_tm`, '%H:%i')) AS 'hours'", 'employee.nickname'))
    ->from('absences')
    ->join('registration', 'registration.id=absences.reg_id')
    ->join('std_lesson', 'absences.stdlesson_id=std_lesson.id')
    ->join('lesson', 'std_lesson.lesson_id=lesson.id')	
    ->join('section', 'std_lesson.section_id=section.id')
    ->join('section_program', 'section.id=section_program.section_id')
    ->join('catalog_lesson', 'lesson.cataloglesson_id=catalog_lesson.id')
    ->join('lesson_tutor', 'std_lesson.tutor_id=lesson_tutor.id')
    ->join('employee', 'lesson_tutor.employee_id=employee.id')
    ->join('weekday', 'section_program.day=weekday.name')
    ->where('weekday.priority', 'DAYOFWEEK(`absences`.`date`)-1', false)
    ->get();


    if ($query->num_rows() > 0) 
    {
      foreach($query->result_array() as $row) 
      {
         $output['aaData'][] = $row;
      }
      return $output;
    }
    else 
    {
        return false;
    }

   }

   public function get_mailhistorydata()
   {
    $query=$this->db
    ->select(array('id', 'created_at', 'subject', 'content', 'recipients'))
    ->from('mail_history')
    ->order_by('id', 'desc')
   //  ->limit(20)
    ->get();


    if ($query->num_rows() > 0) 
    {
      foreach($query->result_array() as $row) 
      {
         $output['aaData'][] = $row;
      }
      return $output;
    }
    else 
    {
         return false;
    }
   }


   public function get_smshistorydata()
   {
    $query=$this->db
    ->select(array('id', 'created_at', 'subject', 'content', 'recipients'))
    ->from('sms_history')
    ->order_by('id', 'desc')
    ->get();


    if ($query->num_rows() > 0) 
    {
      foreach($query->result_array() as $row) 
      {
         $output['aaData'][] = $row;
      }
      return $output;
    }
    else 
    {
         return false;
    }
   }
   

}