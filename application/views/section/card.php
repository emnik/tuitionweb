<link href="<?php echo base_url('assets/bootstrap3-timepicker-0.2.6/css/bootstrap-timepicker.min.css') ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/bootstrap3-timepicker-0.2.6/js/bootstrap-timepicker.min.js') ?>" ></script>

<style type="text/css">

/*  @media (max-width: 400px)
  {    
    .col-xs-3 {padding-left:5px;padding-right: 5px} 
    .col-xs-3 .form-control {padding-left:8px;padding-right: 3px}
  }   
*/
</style>


<script type="text/javascript">
    
    var newdayc = 1;
    var newdayindex = - newdayc;

function toggleedit(togglecontrol, id) {

  if ($(togglecontrol).hasClass('active')){
    $('#' + id).closest('.mainform').find(':input').each(function(){
      $(this).attr('disabled', 'disabled');
      $(this).find('btn').attr('disabled','disabled');
      });
    }
  
  else {
      $('#' + id).closest('.mainform').find(':input').removeAttr('disabled');
      $(this).find('btn').removeAttr('disabled');
      
      if(newdayc>1)
      {
        $('#undodaybtn').removeAttr('disabled');
      }
      else
      {
        $('#undodaybtn').attr('disabled', 'disabled');
      };
    };

}

$(document).ready(function(){

    $('#cancelbtn').click(function(){
      window.open("<?php echo base_url('section/cancel/card/'.$section['id'])?>", '_self', false);
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

    
        $('#classes').change(function(){
          document.getElementById('lessons').options.length = 0;
          document.getElementById('tutors').options.length = 0;
          getcourses();
        })


        $('#courses').change(function(){
          document.getElementById('tutors').options.length = 0;
          getlessons();
        }); //end change event 


        $('#lessons').change(function(){
          gettutors();
        }); //end change event 
          
        //addind new days in program

        $('#newdaybtn').click(function(){
          
          $("#undodaybtn").removeAttr('disabled');
          newdayc = newdayc + 1;
          newdayindex = - newdayc;
          var lastdayrow = $(this).closest('.row').prev('.row');
          var newday = lastdayrow.clone();
          newday.insertAfter(lastdayrow);
          var inputfields = newday.find('input[type="text"]');
          var selfields = newday.find('select');

          //Reset values for the cloned inputfields

          //-------------set new dayname---------------
          selfields.eq(0).attr("name", "day[" + newdayindex +"]");        
          selfields.eq(0).attr('id', "day["+newdayindex+"]");
          selfields.eq(0).find('option:selected').removeAttr("selected");
          selfields.eq(0).find('option:first-child').attr('selected', 'selected');
          

          //-------------set new start_tm---------------
          inputfields.eq(0).attr("name", "start_tm[" + newdayindex +"]");        
          inputfields.eq(0).attr('id', "starttm["+newdayindex+"]");
          inputfields.eq(0).prop('value', '');  
          inputfields.eq(0).attr('value', '');  
          $(inputfields.eq(0))
          .timepicker({
            showMeridian:false,
            showSeconds:false,
            defaultTime:false,
            disableFocus:false, 
            showInputs:false,           
            minuteStep:15
          })
          .timepicker('setTime', '15:00');

          //-------------set new end_tm---------------
          inputfields.eq(1).attr("name", "end_tm[" + newdayindex +"]");        
          inputfields.eq(1).attr('id', "endtm["+newdayindex+"]");
          inputfields.eq(1).prop('value', '');  
          inputfields.eq(1).attr('value', '');
          $(inputfields.eq(1))
          .timepicker({
            showMeridian:false,
            showSeconds:false,
            defaultTime:false,
            disableFocus:false,
            showInputs:false,
            minuteStep:15
          })
          .timepicker('setTime', '15:00');

          //-------------set new classroom---------------
          inputfields.eq(2).attr("name", "classroom_id[" + newdayindex +"]");        
          inputfields.eq(2).attr('id', "classroomid["+newdayindex+"]");
          inputfields.eq(2).prop('value', '');  
          inputfields.eq(2).attr('value', '');
          });


        $('#undodaybtn').click(function(){
          if (newdayc > 1) {
            var lastdayrow = $(this).closest('.row').prev('.row');

            lastdayrow.remove();  
            newdayc = newdayc - 1;
            
            if (newdayc==1){
              $(this).attr('disabled','disabled'); 
            }
          }
        });

        $('.timecontainer input').timepicker({
          showMeridian:false,
          showSeconds:false,
          minuteStep:15,
          defaultTime:false,
          disableFocus:false,
          showInputs:false,
        });


        $('#delsectionbtn').click(function(){
        var r=confirm("Το παρών τμήμα πρόκειται να διαγραφεί. Παρακαλώ επιβεβαιώστε.");
          if (r==true)
          {
              window.open ("<?php echo base_url('section/delreg/'.$section['id']);?>",'_self',false);  
          }
          return false;
        });

}) //end of (document).ready(function())

function getcourses(){
          var classid = $('#classes option:selected').val();
        //alert(classid);

        //clear options from course select input
        document.getElementById('courses').options.length = 0;

        //the following is ajax post to populate the course dropdown 
        var postdata = {'jsclassid': classid};
        //post_url is the controller function where I want to post the data
        var post_url = "<?php echo base_url('section/courses')?>";
        $.ajax({
          type: "POST",
          url: post_url,
          data : postdata,
          dataType:'json',
          async: false,
          //courses is just a name that gets the result of the controller's function I posted the data
          success: function(courses) //we're calling the response json array 'courses data'
            {
              $.each(courses,function(id,course) 
                {
                  var opt = $('<option />'); // here we're creating a new select option for each group
                  opt.val(id);
                  opt.text(course);
                  $('#courses').append(opt); 
                  //console.log(opt);
                });
              //$("#courses option:first").prop("selected", "selected");
              $("#courses option:first").removeAttr('selected').attr("selected", "selected");
            } //end success
          }); //end AJAX
   
            //if only one course, get the lessons too. The above ajax query MUST be async=false to work!!! this one...
            if ($('#courses').get(0).options.length==1){
              getlessons();
            }
            else
            //select none so once a user selection in that happens to trigger the get lessons function
            {
                var opt = $('<option />');
                opt.val('none');
                opt.text(" ");
                $('#courses').prepend(opt); 
                $("#courses option:first").removeAttr('selected').attr("selected", "selected");
            };

}


function getlessons(){
        var classid = $('#classes option:selected').val();
        var courseid = $('#courses option:selected').val();

        //clear options from lessons select input
        document.getElementById('lessons').options.length = 0;

        //the following is ajax post to populate the course dropdown 
        var postdata = {'jsclassid': classid, 'jscourseid': courseid};
        //post_url is the controller function where I want to post the data
        var post_url = "<?php echo base_url('section/lessons')?>";
        $.ajax({
          type: "POST",
          url: post_url,
          data : postdata,
          dataType:'json',
          //lessons is just a name that gets the result of the controller's function I posted the data
          success: function(lessons) 
            {
              $.each(lessons,function(id, lesson) 
                {
                  
                  var opt = $('<option />'); // here we're creating a new select option for each group
                  opt.val(id);
                  opt.text(lesson);
                  $('#lessons').append(opt); 
                  //console.log(opt);
                });
                //$("#lessons option:first").prop("selected", "selected");
                
                
                var opt = $('<option />'); // here we're creating a new select option for each group
                opt.val('none');
                opt.text(" ");
                $('#lessons').prepend(opt); 

                $("#lessons option:first").removeAttr('selected').attr("selected", "selected");

            } //end success
          }); //end AJAX
   
}

function gettutors(){
        var classid = $('#classes option:selected').val();
        var courseid = $('#courses option:selected').val();
        var lessonid = $('#lessons option:selected').val();

        //clear options from tutors select input
        document.getElementById('tutors').options.length = 0;

        //the following is ajax post to populate the course dropdown 
        var postdata = {'jsclassid': classid, 'jscourseid': courseid, 'jslessonid': lessonid};
        //post_url is the controller function where I want to post the data
        var post_url = "<?php echo base_url('section/tutors')?>";
        $.ajax({
          type: "POST",
          url: post_url,
          data : postdata,
          dataType:'json',
          //lessons is just a name that gets the result of the controller's function I posted the data
          success: function(tutors) 
            {
              $.each(tutors,function(id, tutor) 
                {
                  
                  var opt = $('<option />'); // here we're creating a new select option for each group
                  opt.val(id);
                  opt.text(tutor);
                  $('#tutors').append(opt); 
                  //console.log(opt);
                });

                $("#tutors option:first").removeAttr('selected').attr("selected", "selected");

            } //end success
          }); //end AJAX
   
}


//delete a specific change
  function delprogamday(id, day){
    var sData = {'jsprogramid': id};
    var days = $('.programrow').length;

    var res = confirm("Πρόκειται να διαγράψετε μία ημέρα προγράμματος: "+day+". Σίγουρα Θέλετε να συνεχίσετε;");
    var post_url = "<?php echo base_url('section/delprogramday');?>";

      if (res==true){
          $.ajax({
            type: "post",
            url: post_url,
            data : sData,
            dataType:'json', 
            success: function(){
              if (days==1){
                  window.location.href = window.location.href;  
              }
            }
          }); //end of ajax
          $('select[name="day['+id+']"]').closest('.row').remove();  
          $('#deldaylist'+id).remove();
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
                <li><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
                <li class="active"><a href="<?php echo base_url()?>section">Τμήματα</a></li>
                <li><a href="<?php echo base_url()?>">Πρόγραμμα Σπουδών</a></li>
                <li><a href="<?php echo base_url()?>">Μαθήματα-Διδάσκωντες</a></li>
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
                <li><a href="<?php echo base_url()?>section/logout">Αποσύνδεση</a></li>
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
        	<li><a href="<?php echo base_url()?>section/card/<?php echo $section['id']?>/sectionstudents">Μαθητές</a></li>
      	<?php endif;?>
      </ul>

      <p></p>

      <div class="visible-sm visible-xs" style="margin:15px 0px;">
        <div class="row">
          <div class="col-md-12">
            <div class="btn-group pull-left">  
              <a class="btn btn-default btn-sm" href="#group1">Τμήματος</a>
              <a class="btn btn-default btn-sm" href="#group2">Προγράμματος</a>
            </div>
          </div>      
        </div>
      </div>
     

	<div class="row">

    	<div class="col-md-12">
        <form action="<?php echo base_url()?>section/card/<?php echo $section['id']?>" method="post" accept-charset="utf-8" role="form">
        
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
	        	   		<div class="col-md-3 col-xs-6">
	        	   			<div class="form-group">  
                		        <label>Όνομα τμήματος</label>
                        		<input disabled class="form-control" id="section" type="text" placeholder="" name="section" value="<?php echo $sectioncard['section'];?>">
	        	    		</div>
                  </div>
               </div> <!--end of row-->
                  <div class="row">
	        	    	<div class="col-md-3 col-xs-6">
                      <label>Τάξη</label>
                        <select disabled id="classes" class="form-control" name="class_id">
                          <?php $sel=false;?>
                          <?php foreach ($class as $data):?>
                            <option value="<?php echo $data['id']?>"<?php if ($sectioncard['class_id'] == $data['id']){echo 'selected="selected"'; $sel=true;}?>><?php echo $data['class_name'];?></option>
                          <?php endforeach;?>
                          <option value="" <?php if($sel==false) echo 'selected';?>></option>
                        </select>
                      </div>
                    <!-- </div> -->
	        	    	<div class="col-md-3  col-xs-6">
                        <div class="form-group">
                          <label>Κατεύθυνση</label>
                          <select disabled id="courses" class="form-control" name="course_id">
                            <?php if ($course):?>
                              <?php $sel=false;?>
                              <?php foreach ($course as $id=>$value):?>
                                <option value="<?php echo $id;?>"<?php if ($sectioncard['course_id'] == $id){echo 'selected="selected"'; $sel=true;}?>><?php echo $value;?></option>
                              <?php endforeach;?>
                            <?php endif;?>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3  col-xs-6">
                        <div class="form-group">
                          <label>Μάθημα</label>
                          <select disabled id="lessons" class="form-control" name="lesson_id">
                            <?php if ($lesson):?>
                              <?php $sel=false;?>
                              <?php foreach ($lesson as $id=>$value):?>
                                <option value="<?php echo $id;?>"<?php if ($sectioncard['lesson_id'] == $id){echo 'selected="selected"'; $sel=true;}?>><?php echo $value;?></option>
                              <?php endforeach;?>
                              <!-- <option value="none" <?php if($sel==false) echo 'selected';?>></option> -->
                            <?php endif;?>
                          </select>
                        </div>

                      </div>
                    	<div class="col-md-3  col-xs-6">
                      		<div class="form-group">
	        	    	   		    <!-- <label>Διδάσκων</label>
                       			<input disabled class="form-control" id="tutor_id" type="text" placeholder="" name="tutor_id" value="<?php echo $sectioncard['tutor_id'];?>"> -->
                            <label>Διδάσκων</label>
                            <select disabled id="tutors" class="form-control" name="tutor_id">
                              <?php if ($tutor):?>
                                <?php $sel=false;?>
                                <?php foreach ($tutor as $id=>$value):?>
                                  <option value="<?php echo $id;?>"<?php if ($sectioncard['tutor_id'] == $id){echo 'selected="selected"'; $sel=true;}?>><?php echo $value;?></option>
                                <?php endforeach;?>
                                <!-- <option value="none" <?php if($sel==false) echo 'selected';?>></option> -->
                              <?php endif;?>
                            </select>
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
	                		<i class="icon-calendar"></i>
	              		</span>
	              		<h3 class="panel-title">Πρόγραμμα τμήματος</h3>
	              		<div class="buttons">
	                  		<button enabled id="editform2" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
	              		</div>
	            	</div>
	            	<div class="panel-body">
                  <?php if(!empty($sectionprog)):?>
                  <?php foreach ($sectionprog as $daysectionprog):?>
                    <div class="row programrow"> 

                      <?php $days=array("Δευτέρα", "Τρίτη", "Τετάρτη", "Πέμπτη", "Παρασκευή", "Σάββατο", "Κυριακή");?>

                      <div class="col-sm-3 col-sm-offset-0 col-sm-pull-0 col-xs-8 col-xs-offset-4 col-xs-pull-4">
                        <div class="form-group">  
                                <label>Ημέρα</label>
                                <!-- <input disabled class="form-control" id="day[<?php echo $daysectionprog['id'];?>]" type="text" placeholder="" name="day[<?php echo $daysectionprog['id'];?>]" value="<?php echo $daysectionprog['day'];?>"> -->
                                <select disabled class="form-control" id="day[<?php echo $daysectionprog['id'];?>]" name="day[<?php echo $daysectionprog['id'];?>]">
                                    <option value=""></option>
                                    <?php foreach ($days as $day):?> 
                                      <option value="<?php echo $day;?>" <?php if($daysectionprog['day']==$day) echo " selected='selected'";?>><?php echo $day;?></option>
                                    <?php endforeach;?>
                                </select>
                        </div>
                      </div>

                      <div class="col-sm-3 col-xs-4">
                            <div class="form-group timecontainer">
                                  <label>Έναρξη</label>
                                  <input disabled class="form-control" id="starttm[<?php echo $daysectionprog['id'];?>]" type="text" placeholder="" name="start_tm[<?php echo $daysectionprog['id'];?>]" value="<?php echo date('H:i',strtotime($daysectionprog['start_tm']));?>">
                            </div>
                      </div>
                      <div class="col-sm-3 col-xs-4 timecontainer">
                         <div class="form-group">
                             <label>Λήξη</label>
                             <input disabled class="form-control" id="endtm[<?php echo $daysectionprog['id'];?>]" type="text" placeholder="" name="end_tm[<?php echo $daysectionprog['id'];?>]" value="<?php echo date('H:i',strtotime($daysectionprog['end_tm']));?>">
                          </div>
                      </div>
                      <div class="col-sm-3 col-xs-4">
                          <div class="form-group">
                             <label>Αίθουσα</label>
                             <input disabled class="form-control" id="classroomid[<?php echo $daysectionprog['id'];?>]" type="text" placeholder="" name="classroom_id[<?php echo $daysectionprog['id'];?>]" value="<?php echo $daysectionprog['classroom_id'];?>">
                          </div>
                      </div>
                    </div>
                  <?php endforeach;?>
                <?php else:?>
                    <div class="row programrow"> 

                      <div class="col-xs-3">
                        <div class="form-group">  
                                <label>Ημέρα</label>
                                <!-- <input disabled class="form-control" id="day[-1]" type="text" placeholder="" name="day[-1]" value=""> -->
                                <select disabled class="form-control" id="day[-1]" name="day[-1]">
                                    <option selected = 'selected' value=""></option>
                                    <option value="Δευτέρα">Δευτέρα</option>
                                    <option value="Τρίτη">Τρίτη</option>
                                    <option value="Τετάρτη">Τετάρτη</option>
                                    <option value="Πέμπτη">Πέμπτη</option>
                                    <option value="Παρασκευή">Παρασκευή</option>
                                    <option value="Σάββατο">Σάββατο</option>
                                    <option value="Κυριακή">Κυριακή</option>
                                </select>
                        </div>
                          </div>
                      <div class="col-xs-3">
                          <div class="form-group timecontainer">
                                <label>Έναρξη</label>
                                <input disabled class="form-control" id="starttm[-1]" type="text" placeholder="" name="start_tm[-1]" value="">
                          </div>
                      </div>
                      <div class="col-xs-3">
                            <div class="form-group timecontainer">
                                <label>Λήξη</label>
                                <input disabled class="form-control" id="endtm[-1]" type="text" placeholder="" name="end_tm[-1]" value="">
                            </div>
                      </div>
                          <div class="col-xs-3">
                              <div class="form-group">
                                <label>Αίθουσα</label>
                                <input disabled class="form-control" id="classroomid[-1]" type="text" placeholder="" name="classroom_id[-1]" value="">
                              </div>
                          </div>

                    </div>
                <?php endif;?>
                <div class="row">
                <div class="col-md-12">    
                  <div class="btn-toolbar">
                    <?php if(!empty($sectionprog)):?>
                      <div class="btn-group pull-left">
                        <button type="button" class="btn btn-default dropdown-toggle" disabled data-toggle="dropdown">
                          Διαγραφή <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                          <?php foreach ($sectionprog as $data):?>
                          <li id="deldaylist<?php echo $data['id'];?>"><a href="#" onclick="delprogamday(<?php echo $data['id'];?>,'<?php echo $data['day'].' ('.date('H:i',strtotime($data['start_tm'])).'-'.date('H:i',strtotime($data['end_tm'])).')';?>');return false;"><?php echo $data['day'].' ('.date('H:i',strtotime($data['start_tm'])).'-'.date('H:i',strtotime($data['end_tm'])).')';?></a></li>
                          <?php endforeach;?>
                        </ul>
                      </div>
                    <?php endif;?>
                    <div class="btn-group pull-right">
                    <button id="newdaybtn" type="button" class="btn btn-primary" disabled >Προσθήκη</button>
                    <button id="undodaybtn" type="button" class="btn btn-primary" disabled ><span class="icon"><i class="icon-undo"></i></span></button>
                  </div>
                </div>
              </div>
              </div>
        	   		</div> <!-- end of pabel-body -->
	            </div> <!-- end of panel -->
	        </div> <!-- end of mainform -->
  	     </div> <!-- end of group#2 -->
	</div>

    <div class="row">
    	<div class="col-md-12">  
        <div class="btn-toolbar">  
          <div class="btn-group">
          	<button id="submitbtn" type="submit" class="btn btn-primary">Αποθήκευση</button>
          	<button id="cancelbtn" type="button" class="btn btn-default">Ακύρωση</button>
          </div>
            <div class="btn-group pull-right">
            <a id="delsectionbtn" href="#" class="btn btn-default" ><i class="icon-trash"></i></a>
            <a id="newsectionbtn" href="<?php echo base_url();?>section/newreg" class="btn btn-default"><i class="icon-plus"></i></a>
            </div>
        </div>
    	</div>
    </div>

    </form>

      <div class="row">
        <div class="col-md-12">   
        <ul class="pager">
            <li class="previous <?php if(empty($prevnext['prev'])){echo 'disabled';};?>"  <?php if(empty($prevnext['prev'])){echo "onclick='return false;'";};?>><a href="<?php echo base_url('/section/card/'.$prevnext['prev']);?>"><i class="icon-chevron-left"></i> Προηγούμενο</a></li>
            <li class="next <?php if(empty($prevnext['next'])){echo 'disabled';};?>"  <?php if(empty($prevnext['next'])){echo "onclick='return false;'";};?> ><a href="<?php echo base_url('/section/card/'.$prevnext['next']);?>">Επόμενο <i class="icon-chevron-right"></i></a></li>
            </ul>
         </div>
      </div>

    </div> 
  </div>
</div><!--end of main container-->

<div class="push"></div>

</div> <!-- end of body wrapper-->