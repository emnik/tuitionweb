<link href="<?php echo base_url('assets/bootstrap-datepicker-1.3.0/css/datepicker3.css') ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/bootstrap-datepicker-1.3.0/js/bootstrap-datepicker.js') ?>" ></script>
<script src="<?php echo base_url('assets/bootstrap-datepicker-1.3.0/js/locales/bootstrap-datepicker.el.js') ?>" charset="UTF-8"></script>
<!-- Using https://github.com/ivaynberg/select2 with https://github.com/t0m/select2-bootstrap-css -->
<link href="<?php echo base_url('assets/select2/select2.css')?>" rel="stylesheet">
<link href="<?php echo base_url('assets/select2/select2-bootstrap.css')?>" rel="stylesheet">
<script src="<?php echo base_url('assets/select2/select2.js')?>"></script>
<script src="<?php echo base_url('assets/select2/select2_locale_el.js')?>"></script>

<script type="text/javascript">
    
function toggleedit(togglecontrol, id) {

  if ($(togglecontrol).hasClass('active')){
    $('#' + id).closest('.mainform').find(':input').each(function(){
      $(this).attr('disabled', 'disabled');
      $(this).find('btn').attr('disabled','disabled');
      });
      $('#submitbtn').attr('disabled', 'disabled');
      $('#cancelbtn').attr('disabled', 'disabled');
    }
  
  else {
    $('#' + id).closest('.mainform').find(':input').each(function(){
      $(this).removeAttr('disabled');
      });
      $(this).find('btn').removeAttr('disabled');
      $('#submitbtn').removeAttr('disabled');
      $('#cancelbtn').removeAttr('disabled');
    }

}

$(document).ready(function(){

    
    $(".supervisorselect").select2();

    $('.datecontainer input')
    .datepicker({
        format: "dd-mm-yyyy",
        language: "el",
        autoclose: true,
        todayHighlight: true
    })
    .on('focus click', function (event) {
    //stop keyboard events and focus on the datepicker widget to get the date.
    //this is most usefull in android where the android's keyboard was getting in the way...
        event.stopImmediatePropagation();
        event.preventDefault();
        $(this).blur();
    });


    $('#cancelbtn').click(function(){
      window.open("<?php echo base_url();?>exam/cancel/supervisors", '_self', false);
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

   	$('#editform1').removeClass('active');

}) //end of (document).ready(function())
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
              <a href="#" class="dropdown-toggle active" data-toggle="dropdown">Λειτουργία<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo base_url()?>student">Μαθητολόγιο</a></li>
                <li class="active"><a href="<?php echo base_url()?>exam">Διαγωνίσματα</a></li>
                <li><a href="<?php echo base_url()?>files">Αρχεία</a></li>
                <li><a href="<?php echo base_url()?>cashdesk">Ταμείο</a></li>
                <li><a href="<?php echo base_url()?>announcements">Ανακοινώσεις</a></li>
              </ul>
            </li>
           <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Οργάνωση/Διαχείριση<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
                <li><a href="<?php echo base_url()?>section">Τμήματα</a></li>
                <li><a href="<?php echo base_url('curriculum/edit')?>">Πρόγραμμα Σπουδών</a></li>
                <li><a href="<?php echo base_url('curriculum/edit/tutorsperlesson')?>">Μαθήματα-Διδάσκωντες</a></li>
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
                <li><a href="<?php echo base_url()?>exam/logout">Αποσύνδεση</a></li>
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
    <h1>Διαγωνίσματα</h1>
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
	        <li><a href="<?php echo base_url()?>exam">Διαγωνίσματα</a> </li>
	        <li class="active">Επιτηρητές</li>
	      </ul>
      </div>

<!--      <p> 
      <h3>Επιτηρητές</h3>
    </p> -->
      
      <ul class="nav nav-tabs" style="margin-bottom:15px;">
        <li><a href="<?php echo base_url()?>exam/">Προγραμματισμός</a></li>
        <li class="active"><a href="<?php echo base_url()?>exam/supervisors">Επιτηρητές</a></li>
      </ul>

    
	<div class="row">

    	<div class="col-md-12">
        <form action="<?php echo base_url()?>exam/supervisors/" method="post" accept-charset="utf-8" role="form">
        
      	<div class="row"> <!-- section data -->
          <div class="col-md-12" id="group1">
			 <div class="mainform">
                 <div class="panel panel-default">
       			     <div class="panel-heading">
              			<span class="icon">
                			<i class="icon-calendar"></i>
              			</span>
              			<h3 class="panel-title">Επιτηρητές</h3>
              			<div class="buttons">
                  			<button enabled id="editform1" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
              			</div>
            		</div>
	            <div class="panel-body">
	            <?php if(!empty($date)):?>
		            <?php foreach ($date as $key => $examdate):?>
		        	    <div class="row">	
		        	   		<div class="col-md-2 col-sm-4 col-xs-6">
		        	   			<div class="form-group datecontainer">  
	                		        <label>Ημερομηνία</label>
	                        		<input disabled class="form-control" id="date" name="date[<?php echo $key;?>]" type="text" placeholder="" value="<?php  if($examdate!=='0000-00-00') echo implode('-', array_reverse(explode('-', $examdate)));?>">
		        	    		</div>
	                  		</div>
		        	    	<div class="col-md-10 col-sm-8 col-xs-6">
	                            <div class="form-group">
	                                <label>Επιτηρητές</label>
	                                <select multiple disabled class="form-control supervisorselect" placeholder="" name="supervisor_ids[<?php echo $key;?>][]">
	                                  <?php foreach($employee as $id => $name):?>
	                                    <option value="<?php echo $id;?>" <?php if(!empty($supervisor[$examdate])){if(in_array($id,$supervisor[$examdate])) echo 'selected';};?>><?php echo $name;?></option>
	                                  <?php endforeach;?>
	                                </select>
	                            </div>
	                        </div>
	                	</div>
	                <?php endforeach;?>
            	<?php else:?>
            		<div class="alert alert-info fade-in" id="nodata">
		          		<p><i class="icon-info-sign"></i> Δεν υπάρχουν προγραμματισμένα διαγωνίσματα ώστε να εισάγετε επιτηρήσεις.</p>
		      		</div> 
            	<?php endif;?>
		     	</div>
	       	</div>
	 	  </div>
		</div>
	</div>
</div>
</div>

    <div class="row">
    	<div class="col-md-12">   
    	<div class="btn-group"> 
        	<button disabled id="submitbtn" type="submit" class="btn btn-primary">Αποθήκευση</button>
        	<button disabled id="cancelbtn" type="button" class="btn btn-default">Ακύρωση</button>
        </div>
    	</div>
    </div>

    </form>

    </div> 

</div><!--end of main container-->

<div class="push"></div>

</div> <!-- end of body wrapper-->