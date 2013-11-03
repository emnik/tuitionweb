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
        var newdayc = 0;
        var newdayindex = - newdayc;
        $('#newdaybtn').click(function(){
          
          newdayc = newdayc + 1;
          newdayindex = - newdayc;
          var lastdayrow = $(this).closest('.row').prev('.row');
          var newday = lastdayrow.clone();
          newday.insertAfter(lastdayrow);
          var fields = newday.find('input[type="text"]');

          //Reset values for the cloned fields

          //-------------set new dayname---------------
          fields.eq(0).attr("name", "day[" + newdayindex +"]");        
          fields.eq(0).attr('id', "day"+newdayc);
          fields.eq(0).prop('value', '');  
          fields.eq(0).attr('value', '');  

          //-------------set new start_tm---------------
          fields.eq(1).attr("name", "start_tm[" + newdayindex +"]");        
          fields.eq(1).attr('id', "starttm"+newdayc);
          fields.eq(1).prop('value', '');  
          fields.eq(1).attr('value', '');  

          //-------------set new end_tm---------------
          fields.eq(2).attr("name", "end_tm[" + newdayindex +"]");        
          fields.eq(2).attr('id', "endtm"+newdayc);
          fields.eq(2).prop('value', '');  
          fields.eq(2).attr('value', '');

          //-------------set new classroom---------------
          fields.eq(3).attr("name", "classroom_id[" + newdayindex +"]");        
          fields.eq(3).attr('id', "classroomid"+newdayc);
          fields.eq(3).prop('value', '');  
          fields.eq(3).attr('value', '');
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
        var post_url = "<?php echo base_url()?>section/courses";
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
        var post_url = "<?php echo base_url()?>section/lessons";
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
        var post_url = "<?php echo base_url()?>section/tutors";
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
	        	   		<div class="col-md-3 col-xs-3">
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
                    <div class="row"> 

                      <div class="col-xs-3">
                        <div class="form-group">  
                                <label>Ημέρα</label>
                                <input disabled class="form-control" id="day[<?php echo $daysectionprog['id'];?>]" type="text" placeholder="" name="day[<?php echo $daysectionprog['id'];?>]" value="<?php echo $daysectionprog['day'];?>">
                        </div>
                          </div>

                      <div class="col-xs-3">
                          <div class="form-group">
                                <label>Έναρξη</label>
                                <input disabled class="form-control" id="starttm[<?php echo $daysectionprog['id'];?>]" type="text" placeholder="" name="start_tm[<?php echo $daysectionprog['id'];?>]" value="<?php echo date('H:i',strtotime($daysectionprog['start_tm']));?>">
                        </div>
                          </div>
                      <div class="col-xs-3">
                            <div class="form-group">
                          <label>Λήξη</label>
                                <input disabled class="form-control" id="endtm[<?php echo $daysectionprog['id'];?>]" type="text" placeholder="" name="end_tm[<?php echo $daysectionprog['id'];?>]" value="<?php echo date('H:i',strtotime($daysectionprog['end_tm']));?>">
                        </div>
                          </div>
                          <div class="col-xs-3">
                              <div class="form-group">
                                <label>Αίθουσα</label>
                                <input disabled class="form-control" id="classroomid[<?php echo $daysectionprog['id'];?>]" type="text" placeholder="" name="classroom_id[<?php echo $daysectionprog['id'];?>]" value="<?php echo $daysectionprog['classroom_id'];?>">
                              </div>
                          </div>

                    </div>
                  <?php endforeach;?>
                <?php endif;?>
                <div class="row">
                <div class="col-md-12">    
                  <div class="pull-right">
                    <div class="btn-group">
                      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        Διαγραφή <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu" role="menu">
                        <li><a href="#">Δευτέρας</a></li>
                        <li><a href="#">Τρίτης..</a></li>
                      </ul>
                    </div>
                    <div class="btn-group">
                    <button id="newdaybtn" type="button" class="btn btn-primary">Προσθήκη</button>
                    <button id="undodaybtn" type="button" class="btn btn-primary"><span class="icon"><i class="icon-undo"></i></span></button>
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