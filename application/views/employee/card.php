<!--https://github.com/hgoebl/mobile-detect.js -->
<script src="<?php echo base_url('assets/mobile-detect.js/mobile-detect.min.js')?>"></script>
<!-- Using https://github.com/ivaynberg/select2 with https://github.com/t0m/select2-bootstrap-css -->
<link href="<?php echo base_url('assets/select2/select2.css')?>" rel="stylesheet">
<link href="<?php echo base_url('assets/select2/select2-bootstrap.css')?>" rel="stylesheet">
<script src="<?php echo base_url('assets/select2/select2.js')?>"></script>
<script src="<?php echo base_url('assets/select2/select2_locale_el.js')?>"></script>
<script type="text/javascript">
var md = new MobileDetect(window.navigator.userAgent);

function toggleedit(togglecontrol, id) {

  if ($(togglecontrol).hasClass('active')){
    $('#' + id).closest('.mainform').find(':input').each(function(){
      $(this).attr('disabled', 'disabled');
      });
    }
  
  else {
      $('#' + id).closest('.mainform').find(':input').removeAttr('disabled');
    };

}

$('select').on()

$(document).ready(function(){

    $('select').select2();

    // $('select').select2().on('select2-removing', function(e){
    //   var c=confirm("Η διαγραφή ενός μαθήματος θα επηρρεάσει όλα τα τμήματα και τους μαθητές που έχουν αντιστοιχιστεί σε αυτό. Η ενέργεια αυτή δεν αναιρείται.Παρακαλώ επιβεβαιώστε.");
    //   if(c==false){
    //     e.preventDefault(); //a bug in the select2 ...
    //     // https://github.com/ivaynberg/select2/issues/2073
    //   }
    // });

    $('#cancelbtn').click(function(){
      window.open("<?php echo base_url()?>staff/cancel/card/<?php echo $employee['id']?>", '_self', false);
    });

    $("body").on('click', '#editform1, #editform2', function(){
      toggleedit(this, this.id);
      $(this).removeAttr('disabled');
    });

    //we must enable all form fields to submit the form with no errors!
    $("body").on('click', '#submitbtn', function(){
        $('.mainform').find(':input:disabled').removeAttr('disabled');
        $('form').submit();
    });

    //if it is a new employee all the fields should be enabled
    <?php if(empty($employee['surname'])):?>   
        $('#editform1').addClass('active');
        $('#editform2').addClass('active');
        $('.mainform').find(':input:disabled').removeAttr('disabled');
    <?php endif;?>

    //get the radio button value when clicked to the hidden input field to be submitted with the form
    $('input:radio').each(function(){
      $(this).parent().bind('click', function(){
        $("input[name=active]").val($(this).find('input').attr('value'));
        });
    });
    
    if($('#active').val()==0 || $('select[name=is_tutor]').val()==0){
      $('ul.nav.nav-tabs li').hide();
      $('ul.nav.nav-tabs li:first').show();
    };

    //if not on phone the makecall buttons become just decorative!
    if(md.phone()==null){
        $('.phonecall').attr('disabled', 'disabled');
     }

     //if the phone input is empty the button should be disabled (decorative)
     $('.phonecall').each(function(){
         if($(this).parent().next('input').val()==""){
           $(this).attr('disabled', 'disabled');
        }
     })

}) //end of (document).ready(function())


function makephonecall(phonenum){
  if(md.phone()!=null && phonenum!=""){
    if(md.os()=='AndroidOS' || md.os()=='iOS'){
       window.location = 'tel:'+phonenum;
    }
  }
}

</script>

</head>

<body>
 <div class="wrapper"> <!--body wrapper for css sticky footer-->

    <div class="navbar navbar-inverse navbar-top">
      <div class="container">
      <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo base_url()?>">TuitionWeb</a>
     </div>

      <div class="navbar-collapse collapse" role="navigation">
        <ul class="nav navbar-nav">
           <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Λειτουργία<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo base_url()?>student">Μαθητολόγιο</a></li>
                <li><a href="<?php echo base_url()?>exam">Διαγωνίσματα</a></li>
                <li><a href="<?php echo base_url()?>files">Αρχεία</a></li>
                <li><a href="<?php echo base_url()?>cashdesk">Ταμείο</a></li>
                <li><a href="<?php echo base_url()?>announcements">Ανακοινώσεις</a></li>
              </ul>
            </li>
           <li class="dropdown">
              <a href="#" class="dropdown-toggle active" data-toggle="dropdown">Οργάνωση/Διαχείριση<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="active"><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
                <li><a href="<?php echo base_url()?>section">Τμήματα</a></li>
                <li><a href="<?php echo base_url()?>curriculum/edit">Πρόγραμμα Σπουδών</a></li>
                <li><a href="<?php echo base_url()?>curriculum/edit/tutorsperlesson">Μαθήματα-Διδάσκωντες</a></li>
                <li><a href="<?php echo base_url()?>">Στοιχεία Φροντιστηρίου</a></li>
              </ul>
            </li>
           <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Συγκεντρωτικές Αναφορές<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo base_url()?>">Αναφορές</a></li>
                <li><a href="<?php echo base_url()?>">Ιστορικό</a></li>
                <li><a href="<?php echo base_url()?>">Τηλ. Κατάλογοι</a></li>
                <li><a href="<?php echo base_url()?>finance">Οικονομικά</a></li>
              </ul>
            </li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Χρήστης<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header"><?php echo $user->surname.' '.$user->name;?></li>
                <li><a href="#">Αλλαγή κωδικού</a></li>
                <li><a href="<?php echo base_url()?>staff/logout">Αποσύνδεση</a></li>
              </ul>
            </li>
        </ul>
      </div><!--/.navbar-collapse -->
    </div>
  </div>


<!-- Subhead
================================================== -->
<div class="jumbotron subhead">
  <div class="container">
    <h1>Καρτέλα Εργαζομένου</h1>
    <p class="leap">Πρόγραμμα διαχείρισης φροντιστηρίου.</p>
    <p style="font-size:13px; margin-top:15px; margin-bottom:-15px;">
      <?php 
      $s=$this->session->userdata('startsch');
      echo 'Διαχειριστική Περίοδος: '.$s.'-'.($s + 1);
      ?>
    </p>  
  </div>
</div>


<!-- main container
================================================== -->

  <div class="container"  style="margin-bottom:60px;">
           
      <div>
	      <ul class="breadcrumb">
	        <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a></li>
	        <li><a href="<?php echo base_url()?>staff">Προσωπικό</a> </li>
	        <li class="active">Καρτέλα εργαζομένου</li>
	      </ul>
      </div>
      
     <p> 
      <h3>
        <?php
        if(!empty($employee['surname'])){
          echo $employee['surname'].' '.$employee['name'];
        }
        else {
          echo "Νέος εργαζόμενος";
        };?>
      </h3>
    </p>
        

      <ul class="nav nav-tabs">
        <li class="active"><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>">Στοιχεία</a></li>
        <li><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>/teachingplan">Πλάνο Διδασκαλίας</a></li>
        <li><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>/gradebook" >Βαθμολόγιο</a></li>
      </ul>

      <p></p>

      <div class="visible-sm visible-xs">
        <div class="row">
          <div class="col-md-12">
            <div class="btn-group pull-left">  
              <a class="btn btn-default btn-sm" href="#group1">Εργαζομένου</a>
              <a class="btn btn-default btn-sm" href="#group2">Ειδικότητας</a>
              <!-- <a class="btn btn-default btn-sm" href="#group3">Πρόσληψης</a> -->
            </div>
          </div>      
        </div>
      </div>
     
      <div style="margin:15px 0px;">
       <div class="row">
        <div class="col-md-12">
         <div class="btn-group pull-right" data-toggle="buttons">
          <label class="btn btn-sm btn-primary <?php if($emplcard['active']==1) echo 'active';?>">
            <input type="radio" value='1'>Ενεργός
          </label>
          <label class="btn btn-sm btn-primary <?php if($emplcard['active']==0) echo 'active';?>">
            <input type="radio" value='0'>Ανενεργός
          </label>
        </div>
      </div>
     </div>
   </div>

	<div class="row">

    	<div class="col-md-12">
        <form action="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>" method="post" accept-charset="utf-8" role="form">
        <input type="hidden" id="active" name="active" value="<?php echo $emplcard['active'];?>"> 
        
      	<div class="row">
          <div class="col-md-6" id="group1">
			       <div class="mainform">
       
          <div class="panel panel-default">
            <div class="panel-heading">
              <span class="icon">
                <i class="icon-tag"></i>
              </span>
              <h3 class="panel-title">Στοιχεία εργαζομένου</h3>
              <div class="buttons">
                  <button enabled id="editform1" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
              </div>
            </div>
	            <div class="panel-body">
	        	       <div class="row">	
	        	    		<div class="col-md-4 col-sm-4">
	        	    			<div class="form-group">  
                        <label>Επώνυμο</label>
                        <input disabled class="form-control" id="surname" type="text" placeholder="" name="surname" value="<?php echo $emplcard['surname'];?>">
	        	    		  </div>
                    </div>
	        	    		<div class="col-md-4 col-sm-4">
  	        	    		<div class="form-group">
                      	<label>Όνομα</label>
                        <input disabled class="form-control" id="name" type="text" placeholder="" name="name" value="<?php echo $emplcard['name'];?>">
	        	    		  </div>
                    </div>
	        	    		<div class="col-md-4 col-sm-4">
                      <div class="form-group">
	        	    			  <label>Σύντομο</label>
                        <input disabled class="form-control" id="nickname" type="text" placeholder="για καθηγητές" name="nickname" value="<?php echo $emplcard['nickname'];?>">
	        	    		  </div>
                    </div>
	        	    	</div>

	           	     <div class="row">	
	        	    		<div class="col-md-6 col-sm-6">
                      <div class="form-group">
	        	    	   	 <label>Σταθερό τηλ.</label>
                       <div class="input-group">
                          <span class="input-group-btn">
                            <button type="button" class="phonecall btn btn-default" onclick="makephonecall(<?php echo $emplcard['home_tel'];?>);"><span class="icon"><i class="icon-phone"></i></span></button>
                          </span>
                          <input disabled class="form-control" id="home_tel" type="text" placeholder="" name="home_tel" value="<?php echo $emplcard['home_tel'];?>">
	        	    		    </div>
                      </div>
                    </div>
	        	    		<div class="col-md-6  col-sm-6">
	        	    			 <div class="form-group">
                          <label>Κινητό τηλ.</label>
                          <div class="input-group">
                            <span class="input-group-btn">
                              <button type="button" class="phonecall btn btn-default" onclick="makephonecall(<?php echo $emplcard['mobile'];?>);"><span class="icon"><i class="icon-mobile-phone"></i></span></button>
                            </span>
                            <input disabled class="form-control" id="mobile" type="text" placeholder="" name="mobile" value="<?php echo $emplcard['mobile'];?>">
	        	    		    </div>
                       </div>
                    </div>
	        	    	</div>
		        	   </div> <!-- end of content row -->
	         </div>
	      </div>
	  </div> <!-- end of group#1 -->
	  	<div class="col-md-6" id="group2">
          <div class="mainform">
	          <div class="panel panel-default">
	            <div class="panel-heading">
	              <span class="icon">
	                <i class="icon-tag"></i>
	              </span>
	              <h3 class="panel-title">Στοιχεία Ειδικότητας</h3>
	              <div class="buttons">
	                  <button enabled id="editform2" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
	              </div>
	            </div>
	            <div class="panel-body">
	        	       <div class="row">	
			              <div class="col-md-4  col-sm-4">
                      <div class="form-group">
		                    <label>Καθηγητής</label>
		                    <select disabled class="form-control" name="is_tutor">
		                        <option value=1 <?php if($emplcard["is_tutor"] == 1) echo "selected"; ?> >Ναι </option>
   		                      <option value=0 <?php if($emplcard["is_tutor"] == 0) echo "selected"; ?> >Όχι </option>
		                    </select>
		                  </div>
                    </div>
		              <div class="col-md-8 col-sm-8">
	        	    	 <div class="form-group">
                  	 <label>Ειδικότητα</label>
                     <input disabled class="form-control" id="speciality" type="text" placeholder="" name="speciality" value="<?php echo $emplcard['speciality'];?>">
	        	    	  </div>
                  </div>
                  </div>
                  <div class="row">
                    <div class="col-xs-12">
                      <div class="form-group">
                        <label>Διδασκώμενα μαθήματα</label>
                        <select placeholder="Επιλέξτε μαθήματα από τη λίστα..." multiple  disabled   name="lessons[]" class="form-control">
                        <?php if($lesson):?>
                          <?php foreach ($lesson as $id=>$title):?>
                              <option value="<?php echo $id;?>" 
                              <?php
                                if(!empty($selectedlessons))
                                {
                                if(in_array($id, $selectedlessons))
                                  {
                                    echo 'selected';
                                  }                                  
                                }?>>
                                <?php echo $title;?>
                              </option>
                          <?php endforeach;?>
                        <?php endif;?>
                        </select>
                      </div>
                    </div>
                  </div>
                  </div> <!-- end of pabel-body -->
	        	   </div> <!-- end of panel -->
	            </div> <!-- end of mainform -->
	          </div> <!-- end of group#2 --> 
  	      </div> 
	      </div>     
      </div>
        <div class="btn-group">
          <button id="submitbtn" type="submit" class="btn btn-primary">Αποθήκευση</button>
          <button id="cancelbtn" type="button" class="btn btn-default">Ακύρωση</button>
        </div>

      </form>
    </div> 
  </div>
</div><!--end of main container-->

<div class="push"></div>

</div> <!-- end of body wrapper-->