<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Finance_model extends CI_Model
{
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


	public function get_term_data(){
		$query = $this->db->query("SELECT MONTH(`term`.`start`) AS `startmonth` ,
					YEAR(`term`.`start`) AS `startyear`,
					MONTH(`term`.`end`) AS `endmonth`,
					YEAR(`term`.`end`) AS `endyear`
					FROM `term` 
					WHERE `term`.`active`=1");

		if ($query->num_rows() > 0) 
		{
			foreach($query->result_array() as $row) 
			{
				$termdata = $row;
			}

			return $termdata;
		}
		else 
		{
			return false;
		}
	}


    //---------------------------------------------------------------------//
    //-------------------FUNCTIONS FOR SCHOOLYEAR FINANCE------------------//
    //---------------------------------------------------------------------//


    public function get_dept_update_date(){
		//get active term
		$termid = $this->db->select('term.id')->where('term.active',1)->get('term')->row()->id;

		$existingdata = $this->db->select(array('value_1','value_2'))->from('lookup')->where('id',3)->get();
    	$row = $existingdata->row();

	    if ($row->value_2 == $termid) 
	    {
	       return $row->value_1; 
	    }
	    else 
	    {
	       return false;
	    }
    }



    public function resetSchFinanceTables(){
    	$this->db->truncate('debt');
    	$this->db->truncate('post_payment');
    }


    public function insertSinglePays(){
    	$this->db->query("INSERT INTO `post_payment` (`reg_id`, `month_num`, `amount`, `is_credit`)
						   SELECT `payment`.`reg_id`,`payment`.`month_range`, `payment`.`amount`, `payment`.`is_credit`
						   FROM `payment`, `registration` 
						   JOIN `term` ON `registration`.`term_id`=`term`.`id`
						   WHERE `payment`.`reg_id` = `registration`.`id`  
						   AND `term`.`active` = 1 AND
						   LENGTH(`payment`.`month_range`)<=2 AND 
						   ((YEAR(`registration`.`start_lessons_dt`)=YEAR(`term`.`start`) AND MONTH(`registration`.`start_lessons_dt`)>=MONTH(`term`.`start`))
						   OR (YEAR(`registration`.`start_lessons_dt`)=YEAR(`term`.`end`) AND MONTH(`registration`.`start_lessons_dt`)<=MONTH(`term`.`end`)))");
    }


    public function getMultiplePays(){
    	$query = $this->db->query("SELECT `payment`.`reg_id`,`payment`.`month_range`, `payment`.`amount`, `payment`.`is_credit`, `registration`.`month_price`
									FROM `payment`, `registration`
									JOIN `term` ON `registration`.`term_id`=`term`.`id`
									WHERE `payment`.`reg_id` = `registration`.`id` 
									AND `term`.`active` = 1 AND
									LENGTH(`payment`.`month_range`)>2 AND
									((YEAR(`registration`.`start_lessons_dt`)=YEAR(`term`.`start`) AND MONTH(`registration`.`start_lessons_dt`)>=MONTH(`term`.`start`))
						   			OR (YEAR(`registration`.`start_lessons_dt`)=YEAR(`term`.`end`) AND MONTH(`registration`.`start_lessons_dt`)<=MONTH(`term`.`end`)))");
    	
    	if ($query->num_rows() > 0) 
		{
			foreach($query->result_array() as $row) 
			{
				$multiplePays[] = $row;
			}
			return $multiplePays;
		}
		else 
		{
			return false;
		}
    }


    public function insertMultiplePays($multipleData)
    {
    	$this->db->insert_batch('post_payment', $multipleData);

    }


	public function populateDebt($monthset, $endmonth, $yearspan, $termdata)
    {
		// $startmonth = $termdata['startmonth'];
		$startyear = $termdata['startyear'];
		$endyear = $termdata['endyear'];

    	foreach ($monthset as $monthnum) {
    		if ($monthnum<>0)
    		{
    			//Εισάγουμε στο πίνακα debt όλους εκείνους τους μαθητές του τρέχοντος σχολικού έτους, που για τον εκάστοτε μήνα από το σετ δεν έχουν αντίστοιχη εγγραφή στο πίνακα πληρωμών.
				//Σε αυτό το σημείο δε μας ενδιαφέρει πιο μήνα γράφτηκε ο μαθητής ή αν και πότε διαγράφηκε κλπ. Θα γίνουν αργότερα οι απαραίτητες διαγραφές!
    			$this->db->query("INSERT INTO `debt`(`reg_id`, `amount`)
						SELECT `registration`.`id`, `month_price` FROM `registration`
						JOIN `term` ON `registration`.`term_id`=`term`.`id`
						WHERE `term`.`active`=1 AND
						((YEAR(`registration`.`start_lessons_dt`)=YEAR(`term`.`start`) AND MONTH(`registration`.`start_lessons_dt`)>=MONTH(`term`.`start`))
						  OR (YEAR(`registration`.`start_lessons_dt`)=YEAR(`term`.`end`) AND MONTH(`registration`.`start_lessons_dt`)<=MONTH(`term`.`end`)))
						AND NOT EXISTS (SELECT * FROM `post_payment` WHERE `post_payment`.`reg_id` = `registration`.`id` AND `post_payment`.`is_credit` = '0' AND `post_payment`.`month_num` = ".$this->db->escape($monthnum).")");

				//Ενημερώνουμε το πεδίο <Μήνας> του πίνακα tbldebt για το μήνα που αναφερόμαστε...     		
    			$this->db->query("UPDATE `debt` SET `month_num` = ".$this->db->escape($monthnum)." WHERE `month_num` IS NULL AND NOT EXISTS (SELECT * FROM `post_payment` WHERE `post_payment`.`reg_id` = `debt`.`reg_id` AND `post_payment`.`is_credit` = '0' AND `post_payment`.`month_num` = ".$this->db->escape($monthnum).")");
    		}
    		else
    		{
    			break;
    		}

    	}

    	//Διαγραφή όσων έχουν εγγραφές στο πίνακα οφειλών για μήνες προ εγγραφής και για μήνες μετά τη διαγραφή!

		if ($yearspan==2 and date('Y')==$endyear) { //'Αν βρισκόμαστε στο νέο έτος...
			//[*] Η ακόλουθη διαγραφή αφορά όσους <γράφτηκαν> το προηγούμενο έτος. Όταν βρισκόμαστε σε μήνα του τρέχοντος έτους π.χ 2ο οι εγγραφές στο πίνακα οφειλών περιέχουν τους μήνες 8,9,10,11,12,1,2
			//Για κάποιον λοιπόν που γράφτηκε το 10ο πρέπει να διαγραφούν οι εγγραφές για μήνες 8 & 9. Άρα η συνθήκη είναι > τρέχων μήνα ΚΑΙ < μήνα έναρξης (>2 ΚΑΙ <10)
			//(The next is a trick to resolve the above issue. Instead of < FROM `tbldebt` > I use < FROM (SELECT * FROM `tbldebt`) AS `temp` >)
			$this->db->query("DELETE FROM `debt` WHERE `id` IN (SELECT `debt`.`id` FROM (SELECT * FROM `debt`) AS `temp`, `registration` WHERE `debt`.`reg_id` = `registration`.`id` AND ((`debt`.`month_num` < MONTH(`registration`.`start_lessons_dt`) AND `debt`.`month_num` > ".$this->db->escape($endmonth).")))");

			
			//[*] Διαγραφή όσων έχουν εγγραφές για μήνες μετά τη διαγραφή τους από το μαθητολόγιο
			//[-] Αν η διαγραφή από το μαθητολόγιο έγινε το νέο έτος τότε σβήνω τις οφειλές για τους μήνες μετά τη διαγραφή του μαθητολογίου και μέχρι τον τρέχων μήνα
			$this->db->query("DELETE FROM `debt` WHERE `debt`.`id` IN (SELECT `temp`.`id` FROM (SELECT * FROM `debt`) AS `temp` JOIN `registration` ON (`temp`.`reg_id` = `registration`.`id`) WHERE (YEAR(`del_lessons_dt`) =".$endyear.") AND (`temp`.`month_num` > MONTH(`del_lessons_dt`) AND `temp`.`month_num` <= ".$this->db->escape($endmonth)."))");
			
			//[*] Διαγραφή όσων γράφτηκαν και διέκοψαν την ίδια μέρα.
			$this->db->query("DELETE FROM `debt` WHERE `debt`.`id` IN (SELECT `temp`.`id` FROM (SELECT * FROM `debt`) AS `temp` JOIN `registration` ON (`temp`.`reg_id` = `registration`.`id`) WHERE `del_lessons_dt` = `start_lessons_dt`)");


			//--> Αν η διαγραφή από το μαθητολόγιο έγινε το προηγούμενο έτος τότε σβήνω τις οφειλές για τους μήνες μετά τη διαγραφή του μαθητολογίου και μέχρι και το Δεκέμβριο
			$this->db->query("DELETE FROM `debt` WHERE `debt`.`id` IN (SELECT `temp`.`id` FROM (SELECT * FROM `debt`) AS `temp` JOIN `registration` ON (`temp`.`reg_id` = `registration`.`id`) WHERE (YEAR(`del_lessons_dt`) = ".$startyear.") AND (`temp`.`month_num` > MONTH(`del_lessons_dt`) AND `temp`.`month_num`<= 12 ))");
			
			//--> όπως επίσης και από το Δεκέμβριο μέχρι τον τρέχων μήνα.
			$this->db->query("DELETE FROM `debt` WHERE `debt`.`id` IN (SELECT `temp`.`id` FROM (SELECT * FROM `debt`) AS `temp` JOIN `registration` ON (`temp`.`reg_id` = `registration`.`id`) WHERE (YEAR(`del_lessons_dt`) = ".$startyear.") AND (`temp`.`month_num` >=1 AND `temp`.`month_num`<= ".$this->db->escape($endmonth)." ))");
			
			
			//[*] Διαγραφή όσων γράφτηκαν το νέο έτος και έχουν στο πίνακα οφειλών εγγραφή για μήνα < από το μήνα έναρξης !
	    	$this->db->query("DELETE FROM `debt` WHERE `id` IN (SELECT `debt`.`id` FROM (SELECT * FROM `debt`) AS `temp`, `registration` WHERE `debt`.`reg_id` = `registration`.`id` AND (YEAR(`registration`.`start_lessons_dt`)=".$endyear." AND `debt`.`month_num` < MONTH(`registration`.`start_lessons_dt`)))");
			
			//[*] Διαγραφή όσων γράφτηκαν το νέο έτος και έχουν στο πίνακα οφειλών εγγραφή για μήνα > από τον τρέχων !
			$this->db->query("DELETE FROM `debt` WHERE `id` IN (SELECT `debt`.`id` FROM (SELECT * FROM `debt`) AS `temp`, `registration` WHERE `debt`.`reg_id` = `registration`.`id` AND (`debt`.`month_num` > ".$this->db->escape($endmonth)." and YEAR(`registration`.`start_lessons_dt`)=".$endyear."))");

		}
		else //Αν βρισκόμαστε στο έτος έναρξης της σχολικής χρονιάς...
		{
			$this->db->query("DELETE FROM `debt` WHERE `id` IN (SELECT `debt`.`id` FROM (SELECT * FROM `debt`) AS `temp`, `registration` WHERE `debt`.`reg_id` = `registration`.`id` AND ((`debt`.`month_num` < MONTH(`registration`.`start_lessons_dt`) ) OR  `debt`.`month_num` >= MONTH(`registration`.`del_lessons_dt`)))");
			// $this->db->query("DELETE FROM `debt` WHERE `id` IN (SELECT `debt`.`id` FROM (SELECT * FROM `debt`) AS `temp`, `registration` WHERE `debt`.`reg_id` = `registration`.`id` AND (MONTH(`registration`.`start_lessons_dt`) <= 7))");
		}


		//Διαγραφή όσων διαγράφηκαν το προηγούμενο έτος και έχουν εγγραφές στο πίνακα οφειλών για το νέο έτος (μήνες >=1)
		// $this->db->query("DELETE FROM `debt` WHERE `id` IN (SELECT `debt`.`id` FROM (SELECT * FROM `debt`) AS `temp`, `registration` WHERE `debt`.`reg_id` = `registration`.`id` AND (MONTH(`registration`.`del_lessons_dt`)>=8 AND `debt`.`month_num`<=7))");
    }


    public function delzerodebts(){
    	//delete debts with zero amount!
		$this->db->delete('debt',array('amount'=>'0'));
		$this->db->delete('debt',array('amount'=> NULL));
    }


    // public function UpdateDebtChanges($monthset)
    // //Update table debt with payment changes
    // {
    // 	foreach ($monthset as $monthnum) {
	// 		//για κάθε μήνα στο σετ επιλέγω τις μεταβολές με ημερομηνία μεταβολής στον προηγούμενο μήνα!
	// 		if ($monthnum <> 0) {
	// 			if ($monthnum <> 1) {	
	// 				$myquery1 = $this->db->query("SELECT DISTINCT `change`.* FROM `debt`, `change` WHERE  `debt`.`reg_id` = `change`.`reg_id` AND MONTH(`change`.`change_dt`)-1 = ".$this->db->escape($monthnum)." ");
	// 			}
	// 			else
	// 			{ 
	// 				$myquery1 = $this->db->query("SELECT DISTINCT `change`.* FROM `debt`, `change` WHERE  `debt`.`reg_id` = `change`.`reg_id` AND MONTH(`change`.`change_dt`)-1 = 0 ");
	// 			}
				
	// 			//για κάθε μεταβολή από αυτές ενημερώνω τον πίνακα των οφειλών με την τιμή ΑΠΟ εφόσον ο μήνας είναι πριν από το μήνα της μεταβολής!
	// 			foreach($myquery1->result_array() as $row) 
	// 			{
	// 				$Apo_Poso = $row['prev_month_price'];
	// 				$std_ID = $row['reg_id'];
	// 				if ($monthnum > 7) {
	// 					$this->db->query("UPDATE `debt` SET `debt`.`amount` = ".$this->db->escape($Apo_Poso)." WHERE  `debt`.`reg_id` = ".$this->db->escape($std_ID)." AND (`debt`.`month_num` >= ".$this->db->escape($monthnum)." OR `debt`.`month_num` <= 7) " );
	// 				}
	// 				elseif ($monthnum > 1) {
	// 					$this->db->query("UPDATE `debt` SET `debt`.`amount` = ".$this->db->escape($Apo_Poso)." WHERE  `debt`.`reg_id` = ".$this->db->escape($std_ID)." AND (`debt`.`month_num` >= ".$this->db->escape($monthnum)." AND `debt`.`month_num` <= 7) " );
	// 				}
	// 				elseif ($monthnum == 1) {
	// 					$this->db->query("UPDATE `debt` SET `debt`.`amount` = ".$this->db->escape($Apo_Poso)." WHERE  `debt`.`reg_id` = ".$this->db->escape($std_ID)." AND (`debt`.`month_num` =  12  OR ( `debt`.`month_num` >=1 AND `debt`.`month_num` <= 7)) " );
	// 				}
	// 			}
				

	// 			$myquery2 = $this->db->query("SELECT DISTINCT `change`.* FROM `debt`, `change` WHERE  `debt`.`reg_id` = `change`.`reg_id` AND MONTH(`change`.`change_dt`) = ".$this->db->escape($monthnum)." ");

	// 			foreach ($myquery2->result_array() as $row) 
	// 			{
	// 				$Se_Poso = $row['new_month_price'];
	// 				$std_ID = $row['reg_id'];
	// 				if ($monthnum > 7) {
	// 					$this->db->query("UPDATE `debt` SET `debt`.`amount` = ".$this->db->escape($Se_Poso)." WHERE  `debt`.`reg_id` = ".$this->db->escape($std_ID)." AND (`debt`.`month_num` >= ".$this->db->escape($monthnum)." OR `debt`.`month_num` <= 7) " );
	// 				}
	// 				elseif ($monthnum >= 1) {
	// 					$this->db->query("UPDATE `debt` SET `debt`.`amount` = ".$this->db->escape($Se_Poso)." WHERE  `debt`.`reg_id` = ".$this->db->escape($std_ID)." AND (`debt`.`month_num` >= ".$this->db->escape($monthnum)." AND `debt`.`month_num` <= 7) " );
	// 				}
	// 			}
	// 		}
	// 		else 
	// 		{
	// 			break;
	// 		}
		
	// 	} //end foreach

    // }


    public function schFinanceUpdateDate()
    //store date of this update and the schoolyear it refers
    {
		//get active term
		$termid = $this->db->select('term.id')->where('term.active',1)->get('term')->row()->id;

    	$data=array('value_1'=>date('d-m-Y'), 'value_2'=>$termid);
    	$this->db->where('id', 3);
		$this->db->update('lookup', $data);

    }


    //---------------------------------------------------------------------//
    //-----------------FUNCTIONS FOR ECONOMIC YEAR FINANCE-----------------//
    //---------------------------------------------------------------------//


    public function get_economicyear_update_date($startsch){
		$existingdata = $this->db->select(array('value_1','value_2'))->from('lookup')->where('id',4)->get();
    	$row = $existingdata->row();

	    if ($row->value_2 == $startsch) 
	    {
	       return $row->value_1; 
	    }
	    else 
	    {
	       return false;
	    }
    }


    public function delzeroyeardebts(){
    	//delete debts with zero amount!
    	$this->db->delete('finance_year_debt',array('amount'=>'0'));
    }


    public function resetEcoFinanceTables()
    {
    	//reset finance_year_pays table
    	$this->db->truncate('finance_year_debt');
    	$this->db->truncate('finance_year_pays');
    }


    public function insertEcoSinglePays($startsch)
    {
    	//Εισάγω στον πίνακα finance_year_pays όλες τις πληρωμές που αφορούν ένα μόνο μήνα
		$SinglePaysIns = "INSERT INTO `finance_year_pays` (`reg_id`, `paid_month`, `amount`) ";
		$SinglePaysIns = $SinglePaysIns."SELECT `payment`.`reg_id`, `payment`.`month_range`,`payment`.`amount` ";
		$SinglePaysIns = $SinglePaysIns."FROM `payment`, `registration` ";
		$SinglePaysIns = $SinglePaysIns."WHERE `payment`.`reg_id` = `registration`.`id` AND ";
		$SinglePaysIns = $SinglePaysIns."LENGTH(`payment`.`month_range`)<=2 AND ";
		$SinglePaysIns = $SinglePaysIns."`payment`.`is_credit` = 0 AND ";

		//Όσοι γράφτηκαν το τρέχων σχολικό έτος από τον αύγουστο και μετά και οι οφειλές τους μέχρι το τέλος του ημερολογιακού έτους (Δεκέμβριος)
		$SinglePaysIns = $SinglePaysIns."((YEAR(`registration`.`start_lessons_dt`)=".$this->db->escape($startsch)." AND MONTH(`registration`.`start_lessons_dt`)>=8 AND `payment`.`month_range`>=8 AND `payment`.`month_range`<=12 ) ";
		
		//Όσοι γράφτηκαν το προηγούμενο σχολικό έτος από τον Ιανουάριο και μετά και οι οφειλές τους μέχρι το τέλος του Ιουλίου
		$SinglePaysIns = $SinglePaysIns."OR (YEAR(`registration`.`start_lessons_dt`)=".$this->db->escape($startsch)." AND MONTH(`registration`.`start_lessons_dt`)<=7 AND `payment`.`month_range`>=1 AND `payment`.`month_range`<=7 ) ";
		
		//Όσοι γράφτηκαν το τρέχων προηγούμενο σχολικό έτος από τον αύγουστο και μετά και οι οφειλές τους από τον Ιανουάριο μέχρι τον Ιούλιο
		$SinglePaysIns = $SinglePaysIns."OR (YEAR(`registration`.`start_lessons_dt`)=".$this->db->escape($startsch-1)." AND MONTH(`registration`.`start_lessons_dt`)>=8 AND `payment`.`month_range`>=1 AND `payment`.`month_range`<=7 ) ) ";
		
		$this->db->query($SinglePaysIns);
    }


    public function insertEcoSingleDebt($startsch)
    {
		//Εισάγω στον πίνακα finance_year_debt όλες τις πληρωμές που αφορούν ένα μόνο μήνα
		$SingleDebtIns = "INSERT INTO `finance_year_debt` (`reg_id`, `credit_month`, `amount`) ";
		$SingleDebtIns = $SingleDebtIns."SELECT `payment`.`reg_id`,`payment`.`month_range`, `payment`.`amount` ";
		$SingleDebtIns = $SingleDebtIns."FROM `payment`, `registration` ";
		$SingleDebtIns = $SingleDebtIns."WHERE `payment`.`reg_id` = `registration`.`id` AND ";
		$SingleDebtIns = $SingleDebtIns."LENGTH(`payment`.`month_range`)<=2 AND ";
		$SingleDebtIns = $SingleDebtIns."`payment`.`is_credit` = 1 AND ";
	
		//Όσοι γράφτηκαν το τρέχων σχολικό έτος από τον αύγουστο και μετά και οι οφειλές τους μέχρι το τέλος του ημερολογιακού έτους (Δεκέμβριος)
		$SingleDebtIns = $SingleDebtIns."((YEAR(`registration`.`start_lessons_dt`)=".$this->db->escape($startsch)." AND MONTH(`registration`.`start_lessons_dt`)>=8 AND `payment`.`month_range`>=8 AND `payment`.`month_range`<=12 ) ";
	
		//Όσοι γράφτηκαν το προηγούμενο σχολικό έτος από τον Ιανουάριο και μετά και οι οφειλές τους μέχρι το τέλος του Ιουλίου
		$SingleDebtIns = $SingleDebtIns."OR (YEAR(`registration`.`start_lessons_dt`)=".$this->db->escape($startsch)." AND MONTH(`registration`.`start_lessons_dt`)<=7 AND `payment`.`month_range`>=1 AND `payment`.`month_range`<=7 ) ";

		//Όσοι γράφτηκαν το τρέχων προηγούμενο σχολικό έτος από τον αύγουστο και μετά και οι οφειλές τους από τον Ιανουάριο μέχρι τον Ιούλιο
		$SingleDebtIns = $SingleDebtIns."OR (YEAR(`registration`.`start_lessons_dt`)=".$this->db->escape($startsch-1)." AND MONTH(`registration`.`start_lessons_dt`)>=8 AND `payment`.`month_range`>=1 AND `payment`.`month_range`<=7 ) ) ";
		
		$this->db->query($SingleDebtIns);
    }



    public function multiEcoPaysSelect($startsch)
    {
    	//Επιλέγω τις πληρωμές που αφορούν περισσότερους του ενός μήνα
		$multiPaysSelect = "SELECT  `payment`.`reg_id`,  `payment`.`month_range` ,  `registration`.`month_price`, `payment`.`amount`  FROM `payment`, `registration` ";
		$multiPaysSelect = $multiPaysSelect."WHERE  `payment`.`reg_id` = `registration`.`id` AND ";
		$multiPaysSelect = $multiPaysSelect."length(`month_range`)>2 AND";
		$multiPaysSelect = $multiPaysSelect."(";
		$multiPaysSelect = $multiPaysSelect."	(";
		//Όσοι γράφτηκαν το τρέχων σχολικό έτος από τον αύγουστο και μετά και οι οφειλές τους μέχρι το τέλος του ημερολογιακού έτους (Δεκέμβριος)
		$multiPaysSelect = $multiPaysSelect."		(".$this->db->escape($startsch)."= year(registration.`start_lessons_dt`)) ";
		$multiPaysSelect = $multiPaysSelect."		and (month(registration.`start_lessons_dt`) >= 8) ";
		$multiPaysSelect = $multiPaysSelect."		and (payment.`is_credit` = 0) ";
		$multiPaysSelect = $multiPaysSelect."		and (payment.`month_range` >= 8) ";
		$multiPaysSelect = $multiPaysSelect."		and (payment.`month_range` <= 12) ";
		$multiPaysSelect = $multiPaysSelect."	)";
		$multiPaysSelect = $multiPaysSelect."		or ";
		$multiPaysSelect = $multiPaysSelect."	(";
		//Όσοι γράφτηκαν το τρέχων προηγούμενο σχολικό έτος από τον αύγουστο και μετά και οι οφειλές τους από τον Ιανουάριο μέχρι τον Ιούλιο
		$multiPaysSelect = $multiPaysSelect."		(month(registration.`start_lessons_dt`) >= 8) ";
		$multiPaysSelect = $multiPaysSelect."		and (payment.`is_credit` = 0) ";
		$multiPaysSelect = $multiPaysSelect."		and (payment.`month_range` >= 1) ";
		$multiPaysSelect = $multiPaysSelect."		and (payment.`month_range` <= 7) ";
		$multiPaysSelect = $multiPaysSelect."		and (year(registration.`start_lessons_dt`) = (".$this->db->escape($startsch-1).")) ";
		$multiPaysSelect = $multiPaysSelect."	)";
		$multiPaysSelect = $multiPaysSelect."		or";
		$multiPaysSelect = $multiPaysSelect."	(";
		//Όσοι γράφτηκαν το προηγούμενο σχολικό έτος από τον Ιανουάριο και μετά και οι οφειλές τους μέχρι το τέλος του Ιουλίου
		$multiPaysSelect = $multiPaysSelect."		(month(registration.`start_lessons_dt`) <= 7) ";
		$multiPaysSelect = $multiPaysSelect."		and (payment.`is_credit` = 0) ";
		$multiPaysSelect = $multiPaysSelect."		and (payment.`month_range` >= 1) ";
		$multiPaysSelect = $multiPaysSelect."		and (payment.`month_range` <= 7) ";
		$multiPaysSelect = $multiPaysSelect."		and (".$this->db->escape($startsch)."= year(registration.`start_lessons_dt`)) ";
		$multiPaysSelect = $multiPaysSelect."	)";
		$multiPaysSelect = $multiPaysSelect.")";
		

		$query = $this->db->query($multiPaysSelect);

		if ($query->num_rows() > 0) 
		{
			foreach($query->result_array() as $row) 
			{
				$multiEcoPays[] = $row;
			}
			return $multiEcoPays;
		}
		else 
		{
			return false;
		}
    }


    public function multiEcoDebtSelect($startsch)
    {
		//Επιλέγω τις πληρωμές που αφορούν περισσότερους του ενός μήνα

		$multiDebtSelect = "SELECT  `payment`.`reg_id`,  `payment`.`month_range` ,  `registration`.`month_price`, `payment`.`amount`   FROM `payment`, `registration` ";
		$multiDebtSelect = $multiDebtSelect."WHERE  `payment`.`reg_id` = `registration`.`id` AND ";
		$multiDebtSelect = $multiDebtSelect."length(`month_range`)>2 AND";
		$multiDebtSelect = $multiDebtSelect."(";
		$multiDebtSelect = $multiDebtSelect."	(";
		
		//Όσοι γράφτηκαν το τρέχων σχολικό έτος από τον αύγουστο και μετά και οι οφειλές τους μέχρι το τέλος του ημερολογιακού έτους (Δεκέμβριος)
		$multiDebtSelect = $multiDebtSelect."		(".$this->db->escape($startsch)."= year(registration.`start_lessons_dt`)) ";
		$multiDebtSelect = $multiDebtSelect."		and (month(registration.`start_lessons_dt`) >= 8) ";
		$multiDebtSelect = $multiDebtSelect."		and (payment.`month_range` >= 8) ";
		$multiDebtSelect = $multiDebtSelect."		and (payment.`month_range` <= 12) ";
		$multiDebtSelect = $multiDebtSelect."		and (payment.`is_credit` = 1) ";
		$multiDebtSelect = $multiDebtSelect."	)";
		$multiDebtSelect = $multiDebtSelect."		or ";
		$multiDebtSelect = $multiDebtSelect."	(";
		
		//Όσοι γράφτηκαν το τρέχων προηγούμενο σχολικό έτος από τον αύγουστο και μετά και οι οφειλές τους από τον Ιανουάριο μέχρι τον Ιούλιο
		$multiDebtSelect = $multiDebtSelect."		(month(registration.`start_lessons_dt`) >= 8) ";
		$multiDebtSelect = $multiDebtSelect."		and (payment.`month_range` >= 1) ";
		$multiDebtSelect = $multiDebtSelect."		and (payment.`month_range` <= 7) ";
		$multiDebtSelect = $multiDebtSelect."		and (payment.`is_credit` = 1) ";
		$multiDebtSelect = $multiDebtSelect."		and (year(registration.`start_lessons_dt`) = (".$this->db->escape($startsch-1).")) ";
		$multiDebtSelect = $multiDebtSelect."	)";
		$multiDebtSelect = $multiDebtSelect."		or";
		$multiDebtSelect = $multiDebtSelect."	(";
		
		//Όσοι γράφτηκαν το προηγούμενο σχολικό έτος από τον Ιανουάριο και μετά και οι οφειλές τους μέχρι το τέλος του Ιουλίου
		$multiDebtSelect = $multiDebtSelect."		(month(registration.`start_lessons_dt`) <= 7) ";
		$multiDebtSelect = $multiDebtSelect."		and (payment.`month_range` >= 1) ";
		$multiDebtSelect = $multiDebtSelect."		and (payment.`month_range` <= 7) ";
		$multiDebtSelect = $multiDebtSelect."		and (payment.`is_credit` = 1) ";
		$multiDebtSelect = $multiDebtSelect."		and (".$this->db->escape($startsch)."= year(registration.`start_lessons_dt`)) ";
		$multiDebtSelect = $multiDebtSelect."	)";
		$multiDebtSelect = $multiDebtSelect.")";
		

		$query = $this->db->query($multiDebtSelect);

		if ($query->num_rows() > 0) 
		{
			foreach($query->result_array() as $row) 
			{
				$multiEcoDebt[] = $row;
			}
			return $multiEcoDebt;
		}
		else 
		{
			return false;
		}
    }


    public function insertMultipleEcoPays($multiEcoData)
    {
    	$this->db->insert_batch('finance_year_pays', $multiEcoData);
    }


    public function insertMultipleEcoDebts($multiEcoData)
    {
    	$this->db->insert_batch('finance_year_debt', $multiEcoData);
    }

    public function schEcoFinanceUpdateDate($startsch)
    //store date of this update and the schoolyear it refers
    {
    	$data=array('value_1'=>date('d-m-Y'), 'value_2'=>$startsch);
    	$this->db->where('id', 4);
		$this->db->update('lookup', $data);

    }

//=============================SCHOOLYEAR REPORTS' DATA=======================//
    public function getschoolreport1data()
    {
		//get active term
		$termid = $this->db->select('term.id')->where('term.active',1)->get('term')->row()->id;

    	$existingdata = $this->db->select(array('value_1','value_2'))->from('lookup')->where('id',3)->get();
    	$row = $existingdata->row();
    	if ($row->value_2 == $termid ){
	    	$query = $this->db->select(array('CONCAT_WS(" ",registration.surname, registration.name) as student', 'debt.amount', 'month.name', 'month.priority'))
	    					->from('debt')
	    					->join('registration', 'registration.id=debt.reg_id')
	    					->join('month','debt.month_num = month.num')
	    					->order_by('month.priority')
	    					->order_by('student')
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
		else
		{
			return false;
		}
    }


    public function getschoolreport2data()
    {
		//get active term
		$termid = $this->db->select('term.id')->where('term.active',1)->get('term')->row()->id;

    	$existingdata = $this->db->select(array('value_1','value_2'))->from('lookup')->where('id',3)->get();
    	$row = $existingdata->row();
    	if ($row->value_2 == $termid ){
	    	$query = $this->db->select(array('CONCAT_WS(" ",registration.surname, registration.name) as student', 'SUM(debt.amount) AS totaldebt', "CONCAT_WS(' ', 'Οφειλόμενοι Μήνες:',COUNT(debt.month_num)) AS months"))
	    					->from('debt')
	    					->join('registration', 'registration.id=debt.reg_id')
	    					->group_by('student')
	    					->order_by('student')
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
	    else
	    {
	    	return false;
	    }
	}

    function get_schoolyear_finance(){
		//get active term
		$termid = $this->db->select('term.id')->where('term.active',1)->get('term')->row()->id;

		$existingdata = $this->db->select(array('value_1','value_2'))->from('lookup')->where('id',3)->get();
    	$row = $existingdata->row();

    	if ($row->value_2 == $termid ){
			// Payments summary per month
			$query1 = $this->db->select(array('month.name as monthname', 'SUM(post_payment.amount) as collected'))
							->from('post_payment')
							->join('registration', 'post_payment.reg_id = registration.id')
							->join('month', 'post_payment.month_num = month.num')
							->where('post_payment.is_credit', 0)
							->group_by('post_payment.month_num')
							->order_by('month.priority')
							->get();

			// Debt summary per month
			$query2 = $this->db->select(array('month.name as monthname', 'SUM(debt.amount) as due'))
							->from('debt')
							->join('month', 'debt.month_num = month.num')
							->group_by('debt.month_num')
							->order_by('month.priority')
							->get();

			if($query1->num_rows() >= $query2->num_rows()){
				$max = $query1->num_rows();
				$data1=$query1->result_array();
				$data2=$query2->result_array();
			} else {
				$max = $query2->num_rows();
				$data1=$query2->result_array();
				$data2=$query1->result_array();
			}
			$data=$data1;

			$j=0;
			for ($i=0; $i<$max; $i++){
				if(!empty($data2[$j])){
					if($data1[$i]['monthname'] == $data2[$j]['monthname']){
						if(array_key_exists('due',$data2[$j])){
							$data[$i]['due'] = $data2[$j]['due'];
							$data[$i]['turnover'] = strval($data1[$i]['collected']+$data2[$j]['due']);
						}
						else {
							$data[$i]['collected'] = $data2[$j]['collected'];
							$data[$i]['turnover'] = strval($data2[$j]['collected']+$data1[$i]['due']);
						}
						$j++;
					}
					else {
						if(array_key_exists('collected',$data1[$i])){
							$data[$i]['due'] = '0';
							$data[$i]['turnover'] = $data1[$i]['collected'];
						}
						else {
							$data[$i]['collected'] = '0';
							$data[$i]['turnover'] = $data1[$i]['due'];
						}
					}
				}
				else {
					if(array_key_exists('collected',$data1[$i])){
						$data[$i]['due'] = '0';
						$data[$i]['turnover'] = $data1[$i]['collected'];
					}
					else {
						$data[$i]['collected'] = '0';
						$data[$i]['turnover'] = $data1[$i]['due'];
					}
				}
				
			}

			// $this->load->library('firephp');
			// $this->firephp->info($data);

			if (sizeof($data)>0){
				return $data;
			}
			else {
				return false;
			}
		}
    	else
    	{
    		return false;
    	}
    }
//=========================ECONOMIC YEARS' REPORT DATA=====================//

    function get_economicyear_finance($startsch){
    	$existingdata = $this->db->select(array('value_1','value_2'))->from('lookup')->where('id',4)->get();
    	$row = $existingdata->row();
    	if ($row->value_2 == $startsch ){
	    	$query=$this->db->select(array('Μήνες', 'Ποσό', 'Κατηγορία'))
	    			->from('vw_finance_year')
	    			->order_by('Μήνες', 'desc')
	    			->order_by('Κατηγορία', 'desc')
	    			->get();

	    	if ($query->num_rows() > 0) 
			{
				foreach($query->result_array() as $row) 
				{
					$ecofinance[] = $row;
				}
				return $ecofinance;
			}
			else 
			{
				return false;
			}
		}
		else
		{
			return false;
		}
    }

    function getecoreport2data($startsch){
    	$existingdata = $this->db->select(array('value_1','value_2'))->from('lookup')->where('id',4)->get();
    	$row = $existingdata->row();
    	if ($row->value_2 == $startsch ){
	    	$query=$this->db->select(array('CONCAT_WS(" ",registration.surname, registration.name) as student', 'amount', 'month.name', 'credit_month as report_priority'))
	    			->from('finance_year_debt')
	    			->join('registration', 'finance_year_debt.reg_id = registration.id')
	    			->join('month', 'finance_year_debt.credit_month = month.num')
	    			->get();

	    	if ($query->num_rows() > 0) 
			{
				foreach($query->result_array() as $row) 
				{
					$ecofinance['aaData'][] = $row;
				}
				return $ecofinance;
			}
			else 
			{
				return false;
			}
		}
		else
		{
			return false;
		}
    }

    function getecoreport3data($startsch){
    	$existingdata = $this->db->select(array('value_1','value_2'))->from('lookup')->where('id',4)->get();
    	$row = $existingdata->row();
    	if ($row->value_2 == $startsch ){
	    	$query=$this->db->select(array('CONCAT_WS(" ",registration.surname, registration.name) as student', 'SUM(amount) AS Ποσό', 'CONCAT_WS(" ", "Μήνες:", COUNT(credit_month)) AS Μήνες'))
	    			->from('finance_year_debt')
	    			->join('registration', 'finance_year_debt.reg_id = registration.id')
	    			->group_by('student') //group by name as when a students has debts in 2 schoolyears he will have different reg ids
	    			->get();

	    	if ($query->num_rows() > 0) 
			{
				foreach($query->result_array() as $row) 
				{
					$ecofinance['aaData'][] = $row;
				}
				return $ecofinance;
			}
			else 
			{
				return false;
			}
		}
		else
		{
			return false;
		}
    }

}