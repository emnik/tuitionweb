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
      <div class="navbar-inner">
        <div class="container">
          <button class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <!--<a class="brand" href="#">Tuition manager</a>-->
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li><a href="<?php echo base_url()?>">Αρχική</a></li> 
              <li><a href="<?php echo base_url()?>student">Μαθητολόγιο</a></li>
              <li class="active"><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
              <li><a href="#sections">Τμήματα</a></li>
              <li><a href="#finance">Οικονομικά</a></li>
              <li><a href="#reports">Αναφορές</a></li>
              <li><a href="#admin">Διαχείριση</a></li>
            </ul>
            <ul class="nav pull-right">
              <li><a href="#"><i class="icon-off"></i> Αποσύνδεση</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
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
      
    <div class="container-fluid">
      
      <div style="margin-top:20px; margin-bottom:-15px;">
	      <ul class="breadcrumb">
	        <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a><span class="divider">></span></li>
	        <li><a href="<?php echo base_url()?>staff">Προσωπικό</a> <span class="divider">></span></li>
	        <li class="active">Καρτέλα εργαζομένου</li>
	      </ul>
      </div>
      
      
    <h3>
      <?php
      if(!empty($employee['surname'])){
        echo $employee['surname'].' '.$employee['name'];
      }
      else {
        echo "Νέος εργαζόμενος";
      };?>
    </h3>
        

      <ul class="nav nav-tabs">
        <li class="active"><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>">Στοιχεία</a></li>
        <li><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>/teachingplan">Πλάνο Διδασκαλίας</a></li>
      </ul>

      <div class="visible-phone">
        <div class="row-fluid">
          <div class="span12">
            <div class="btn-group pull-left">  
              <a class="btn btn-small" href="#group1">Εργαζομένου</a>
              <a class="btn btn-small" href="#group2">Πρόσληψης</a>
            </div>
          </div>      
        </div>
      </div>
     

     <div class="row-fluid">
      <div class="span12">
       <div class="btn-group pull-right" data-toggle="buttons-radio">
          <button type="button" value='1' class="btn btn-small btn-primary <?php if($emplcard['active']==1) echo 'active';?>">Ενεργός</button>
          <button type="button" value='0' class="btn btn-small btn-primary <?php if($emplcard['active']==0) echo 'active';?>">Ανενεργός</button>
        </div>
      </div>
     </div>

	<div class="row-fluid">
       

    	<div class="span12">
        <form action="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>" method="post" accept-charset="utf-8">
        <input type="hidden" name="active" value="<?php echo $emplcard['active'];?>"> 
        
      	<div class="row-fluid">
        <div class="span6" id="group1">
			<div class="mainform">
       
          <div class="contentbox">
            <div class="title">
              <span class="icon">
                <i class="icon-tag"></i>
              </span>
              <h5>Στοιχεία εργαζομένου</h5>
              <div class="buttons">
                  <button enabled id="editform1" type="button" class="btn btn-mini" data-toggle="button"><i class="icon-edit"></i></button>
              </div>
            </div>

            <div class="row-fluid">
	            <div class="content">
 	 		          <div class="row-fluid">
	        	       <div class="row-fluid">	
	        	    		<div class="span4">
	        	    			<label>Επώνυμο</label><input disabled class="span12" id="surname" type="text" placeholder="" name="surname" value="<?php echo $emplcard['surname'];?>">
	        	    		</div>
	        	    		<div class="span4">
	        	    			<label>Όνομα</label><input disabled class="span12" id="name" type="text" placeholder="" name="name" value="<?php echo $emplcard['name'];?>">
	        	    		</div>
	        	    		<div class="span4">
	        	    			<label>Σύντομο</label><input disabled class="span12" id="nickname" type="text" placeholder="για καθηγητές" name="nickname" value="<?php echo $emplcard['nickname'];?>">
	        	    		</div>
	        	    	</div>

	           	       <div class="row-fluid">	
	        	    		<div class="span6">
	        	    			<label>Σταθερό τηλ.</label><input disabled class="span12" id="home_tel" type="text" placeholder="" name="home_tel" value="<?php echo $emplcard['home_tel'];?>">
	        	    		</div>
	        	    		<div class="span6">
	        	    			<label>Κινητό τηλ.</label><input disabled class="span12" id="mobile" type="text" placeholder="" name="mobile" value="<?php echo $emplcard['mobile'];?>">
	        	    		</div>
	        	    	</div>
		        	   </div> <!-- end of content row -->
	            </div> <!-- end of content -->
	          </div> <!-- end of content box -->
	      </div>
	      </div>
	  </div>
	  	<div class="span6" id="group2">
	          <div class="mainform">
	          <div class="contentbox">
	            <div class="title">
	              <span class="icon">
	                <i class="icon-tag"></i>
	              </span>
	              <h5>Στοιχεία πρόσληψης</h5>
	              <div class="buttons">
	                  <button enabled id="editform2" type="button" class="btn btn-mini" data-toggle="button"><i class="icon-edit"></i></button>
	              </div>
	            </div>
	            <div class="content">
	 		       <div class="row-fluid">
	        	       <div class="row-fluid">	
			              <div class="span4">
		                    <label>Καθηγητής</label>
		                    <select disabled class="span12" name="is_tutor">
		                        <option value=1 <?php if($emplcard["is_tutor"] === 1) echo "selected"; ?> >Ναι </option>
   		                      <option value=0 <?php if($emplcard["is_tutor"] === 0) echo "selected"; ?> >Όχι </option>
		                    </select>
		                  </div>
		                  <div class="span8">
	        	    		<label>Ειδικότητα</label><input disabled class="span12" id="speciality" type="text" placeholder="" name="speciality" value="<?php echo $emplcard['speciality'];?>">
	        	    	  </div>
	        	       	<!-- (ΑΦΜ / Ταυτότητα / ΑΜΚΑ ...) -->
	        	    	</div>
	        	   </div> <!-- end of content row -->
	            </div> <!-- end of content -->
	          </div> <!-- end of content box -->
	      </div>
	      </div>     
        </div>
    </div>
</div>
</div>

          <div class="form-actions">
            <button id="submitbtn" type="submit" class="btn btn-primary">Αποθήκευση</button>
            <button id="cancelbtn" type="button" class="btn">Ακύρωση</button>
          </div>

        </form>

  </div> 

</div><!--end of main container-->

<div class="push"></div>

</div> <!-- end of body wrapper-->