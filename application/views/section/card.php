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
      window.open("<?php echo base_url()?>section/cancel/card/<?php echo $section['id']?>", '_self', false);
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

    //if it is a new section all the fields should be enabled
    <?php if(empty($section['section'])):?>   
        $('#editform1').addClass('active');
        $('#editform2').addClass('active');
        $('.mainform').find(':input:disabled').removeAttr('disabled');
    <?php endif;?>

    //get the radio button value when clicked to the hidden input field to be submitted with the form
    // $('input:radio').each(function(){
    //   $(this).parent().bind('click', function(){
    //     $("input[name=active]").val($(this).find('input').attr('value'));
    //     });
    // });
 
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
            <li><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
            <li class="active"><a href="<?php echo base_url()?>section">Τμήματα</a></li>
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
    <h1>Καρτέλα Τμήματος</h1>
    <p class="leap">Πρόγραμμα διαχείρισης φροντιστηρίου.</p>
  </div>
</div>


<!-- main container
================================================== -->

  <div class="container"  style="margin-bottom:60px;">
           
      <div>
	      <ul class="breadcrumb">
	        <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a></li>
	        <li><a href="<?php echo base_url()?>section">Τμήματα</a> </li>
	        <li class="active">Καρτέλα τμήματος</li>
	      </ul>
      </div>
      
     <p> 
      <h3>
        <?php
        if(!empty($section['section'])){
          echo $section['section'].' / '.$section['title'];
        }
        else {
          echo "Νέο τμήμα";
        };?>
      </h3>
    </p>
        

      <ul class="nav nav-tabs">
        <li class="active"><a href="<?php echo base_url()?>section/card/<?php echo $section['id']?>">Στοιχεία</a></li>
        <?php if(!empty($section['section'])):?>
        	<li><a href="<?php echo base_url()?>section/card/<?php echo $section['id']?>/students">Μαθητές</a></li>
      	<?php endif;?>
      </ul>

      <p></p>

      <div class="visible-sm visible-xs">
        <div class="row">
          <div class="col-md-12">
            <div class="btn-group pull-left">  
              <a class="btn btn-default btn-sm" href="#group1">Τμήματος</a>
              <a class="btn btn-default btn-sm" href="#group2">Προγράμματος</a>
            </div>
          </div>      
        </div>
      </div>
     
<!--       <div style="margin:15px 0px;">
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
   </div> -->

	<div class="row">

    	<div class="col-md-12">
        <form action="<?php echo base_url()?>section/card/<?php echo $section['id']?>" method="post" accept-charset="utf-8" role="form">
        <!-- <input type="hidden" name="active" value="<?php echo $emplcard['active'];?>">  -->
        
      	<div class="row"> <!-- section data -->
          <div class="col-md-12" id="group1">
			 <div class="mainform">
                 <div class="panel panel-default">
       			     <div class="panel-heading">
              			<span class="icon">
                			<i class="icon-tag"></i>
              			</span>
              			<h3 class="panel-title">Στοιχεία τμήματος</h3>
              			<div class="buttons">
                  			<button enabled id="editform1" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
              			</div>
            		</div>
	            <div class="panel-body">
	        	    <div class="row">	
	        	   		<div class="col-md-3">
	        	   			<div class="form-group">  
                		        <label>Όνομα τμήματος</label>
                        		<input disabled class="form-control" id="section" type="text" placeholder="" name="section" value="<?php echo $sectioncard['section'];?>">
	        	    		</div>
                    	</div>
	        	    	<div class="col-md-3">
  	        	    		<div class="form-group">
                      			<label>Τάξη</label>
                        		<input disabled class="form-control" id="class_id" type="text" placeholder="" name="class_id" value="<?php echo $sectioncard['class_id'];?>">
	        	    		</div>
                    	</div>
	        	    	<div class="col-md-3">
                    		<div class="form-group">
	        	    			<label>Κατεύθυνση</label>
                        		<input disabled class="form-control" id="course_id" type="text" placeholder="" name="course_id" value="<?php echo $sectioncard['course_id'];?>">
	        	    		</div>
                    	</div>
                    	<div class="col-md-3">
                      		<div class="form-group">
	        	    	   		<label>Διδάσκων.</label>
                       			<input disabled class="form-control" id="tutor_id" type="text" placeholder="" name="tutor_id" value="<?php echo $sectioncard['tutor_id'];?>">
	        	    		</div>
                    	</div>
	        	    </div>
       	    	</div>
		     </div> <!-- end of content row -->
	       </div>
	 	  </div>
		</div><!-- end of section data    -->

    <div class="row"> <!-- section program -->
	  	<div class="col-md-12" id="group2">
     	    <div class="mainform">
	        	<div class="panel panel-default">
	            	<div class="panel-heading">
	              		<span class="icon">
	                		<i class="icon-tag"></i>
	              		</span>
	              		<h3 class="panel-title">Πρόγραμμα τμήματος</h3>
	              		<div class="buttons">
	                  		<button enabled id="editform2" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
	              		</div>
	            	</div>
	            	<div class="panel-body">

        	   		</div> <!-- end of pabel-body -->
	            </div> <!-- end of panel -->
	        </div> <!-- end of mainform -->
  	     </div> <!-- end of group#2 -->
	</div>

    <div class="row">
    	<div class="col-md-12">    
        	<button id="submitbtn" type="submit" class="btn btn-primary">Αποθήκευση</button>
        	<button id="cancelbtn" type="button" class="btn btn-default">Ακύρωση</button>
    	</div>
    </div>

    </form>

    </div> 
  </div>
</div><!--end of main container-->

<div class="push"></div>

</div> <!-- end of body wrapper-->