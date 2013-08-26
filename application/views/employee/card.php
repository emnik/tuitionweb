<script type="text/javascript">

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

$(document).ready(function(){

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
    $("body").find(".btn-group > .btn").each(function(){
      $(this).bind('click', function(){
        $("input[name=active]").val(this.value);
        });
    });
 
}) //end of (document).ready(function())

</script>

</head>

<body>
 <div class="wrapper"> <!--body wrapper for css sticky footer-->

    <div class="navbar navbar-inverse navbar-fixed-top">
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
            <!-- <li><a href="<?php echo base_url()?>">Αρχική</a></li>  -->
            <li><a href="<?php echo base_url()?>student">Μαθητολόγιο</a></li>
            <li class="active"><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
            <li><a href="#sections">Τμήματα</a></li>
            <li><a href="#finance">Οικονομικά</a></li>
            <li><a href="#reports">Αναφορές</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Χρήστης<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header">Νικηφορακης Μανος</li>
                <li><a href="#">Αλλαγή κωδικού</a></li>
                <li><a href="#admin">Διαχείριση</a></li>
                <li class="divider"></li>
                <li><a href="#">Αποσύνδεση</a></li>
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
    <p class="leap">tuition manager - πρόγραμμα διαχείρισης φροντιστηρίου.</p>
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
      </ul>

      <p></p>

      <div class="visible-sm visible-xs">
        <div class="row">
          <div class="col-md-12">
            <div class="btn-group pull-left">  
              <a class="btn btn-default btn-sm" href="#group1">Εργαζομένου</a>
              <a class="btn btn-default btn-sm" href="#group2">Πρόσληψης</a>
            </div>
          </div>      
        </div>
      </div>
     
      <div style="margin:15px 0px;">
       <div class="row">
        <div class="col-md-12">
         <div class="btn-group pull-right" data-toggle="buttons">
          <label class="btn btn-sm btn-primary">
            <input type="radio" value='1' class="<?php if($emplcard['active']==1) echo 'active';?>">Ενεργός
          </label>
          <label class="btn btn-sm btn-primary">
            <input type="radio" value='0' class="<?php if($emplcard['active']==0) echo 'active';?>">Ανενεργός
          </label>
        </div>
      </div>
     </div>
   </div>

	<div class="row">

    	<div class="col-md-12">
        <form action="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>" method="post" accept-charset="utf-8" role="form">
        <input type="hidden" name="active" value="<?php echo $emplcard['active'];?>"> 
        
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

<!--             <div class="row"> -->
	            <div class="panel-body">
 	 		          <!-- <div class="row"> -->
	        	       <div class="row">	
	        	    		<div class="col-md-4">
	        	    			<div class="form-group">  
                        <label>Επώνυμο</label>
                        <input disabled class="form-control" id="surname" type="text" placeholder="" name="surname" value="<?php echo $emplcard['surname'];?>">
	        	    		  </div>
                    </div>
	        	    		<div class="col-md-4">
  	        	    		<div class="form-group">
                      	<label>Όνομα</label>
                        <input disabled class="form-control" id="name" type="text" placeholder="" name="name" value="<?php echo $emplcard['name'];?>">
	        	    		  </div>
                    </div>
	        	    		<div class="col-md-4">
                      <div class="form-group">
	        	    			  <label>Σύντομο</label>
                        <input disabled class="form-control" id="nickname" type="text" placeholder="για καθηγητές" name="nickname" value="<?php echo $emplcard['nickname'];?>">
	        	    		  </div>
                    </div>
	        	    	</div>

	           	     <div class="row">	
	        	    		<div class="col-md-6">
                      <div class="form-group">
	        	    	   	 <label>Σταθερό τηλ.</label>
                       <input disabled class="form-control" id="home_tel" type="text" placeholder="" name="home_tel" value="<?php echo $emplcard['home_tel'];?>">
	        	    		  </div>
                    </div>
	        	    		<div class="col-md-6">
	        	    			 <div class="form-group">
                          <label>Κινητό τηλ.</label>
                          <input disabled class="form-control" id="mobile" type="text" placeholder="" name="mobile" value="<?php echo $emplcard['mobile'];?>">
	        	    		   </div>
                    </div>
	        	    	</div>
		        	   </div> <!-- end of content row -->
<!-- 	            </div>
	          </div> -->
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
	              <h3 class="panel-title">Στοιχεία πρόσληψης</h3>
	              <div class="buttons">
	                  <button enabled id="editform2" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
	              </div>
	            </div>
	            <div class="panel-body">
	 		       <!-- <div class="row"> -->
	        	       <div class="row">	
			              <div class="col-md-4">
                      <div class="form-group">
		                    <label>Καθηγητής</label>
		                    <select disabled class="form-control" name="is_tutor">
		                        <option value=1 <?php if($emplcard["is_tutor"] === 1) echo "selected"; ?> >Ναι </option>
   		                      <option value=0 <?php if($emplcard["is_tutor"] === 0) echo "selected"; ?> >Όχι </option>
		                    </select>
		                  </div>
                    </div>
		              <div class="col-md-8">
	        	    	 <div class="form-group">
                  	 <label>Ειδικότητα</label>
                     <input disabled class="form-control" id="speciality" type="text" placeholder="" name="speciality" value="<?php echo $emplcard['speciality'];?>">
	        	    	  </div>
                  </div>
	        	       	<!-- (ΑΦΜ / Ταυτότητα / ΑΜΚΑ ...) -->
	        	    	</div>
	        	   </div> <!-- end of pabel-body -->
	            </div> <!-- end of panel -->
	          </div> <!-- end of mainform -->
  	      </div> <!-- end of group#2 -->
	      </div>     

        <div>
          <button id="submitbtn" type="submit" class="btn btn-primary">Αποθήκευση</button>
          <button id="cancelbtn" type="button" class="btn btn-default">Ακύρωση</button>
        </div>

      </form>

    </div> 
  </div>
</div><!--end of main container-->

<div class="push"></div>

</div> <!-- end of body wrapper-->