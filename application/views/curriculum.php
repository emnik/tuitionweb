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
    var courserowtemplate, lessonrowtemplate; //globals
    var newcoursec=0, newlessonc=0; //globals


    $(document).on('click','.addcoursebtn', function(){
        newcoursec++;
        newlessonc++; //every time we add a course it is also added one new lesson(+hours) field!
        var newcourserow = courserowtemplate.clone();
        newcourserow.removeClass('hidden');
        newcourserow.attr('id',-newcoursec);
        var fields=newcourserow.find('input[type=text]');
        fields.eq(0).attr('id', 'courseid['+(-newcoursec)+']');
        fields.eq(0).attr('name', 'course['+(-newcoursec)+']');
        fields.eq(1).attr('id', 'lessonid['+(-newlessonc)+']');
        fields.eq(1).attr('name', 'title['+(-newlessonc)+']');
        fields.eq(2).attr('name', 'hours['+(-newlessonc)+']');
        var whereaddcourserow = $(this).parents('.courserow');
        newcourserow.insertAfter(whereaddcourserow);        
        $('#'+(-newcoursec)).find('input:first').focus();
    });
 
    $(document).on('click', '.addlessonbtn', function(){
          newlessonc++;
          var newlessonrow = lessonrowtemplate.clone();
          newlessonrow.removeClass('hidden');
          var fields=newlessonrow.find('input[type=text]');
          fields.eq(0).attr('id', 'lessonid['+(-newlessonc)+']');
          fields.eq(0).attr('name', 'title['+(-newlessonc)+']');
          fields.eq(1).attr('name', 'hours['+(-newlessonc)+']');
          var whereaddlessonrow = $(this).parents('.lessonrow');
          newlessonrow.insertAfter(whereaddlessonrow);
    })


$(document).ready(function(){
    
    courserowtemplate = $('.courserow:hidden'); //store the template to be cloned
    lessonrowtemplate = $('.lessonrow:hidden'); //store the template to be cloned

    $('#cancelbtn').click(function(){
      window.open("<?php echo base_url('curriculum/cancel');?>", '_self', false);
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

    //if it is a new course all the fields should be enabled
    <?php if(empty($exam['lesson_id'])):?>   
        $('#editform1').addClass('active');
        $('#editform2').addClass('active');
        $('.mainform').find(':input:disabled').removeAttr('disabled');
        $('#submitbtn').removeAttr('disabled');
        $('#cancelbtn').removeAttr('disabled');
    <?php endif;?>

    
    $('#classes').change(function(){
      getcourses();
    })



    $('#delexam').click(function(){
        var r=confirm("Το παρών διαγώνισμα πρόκειται να διαγραφεί. Παρακαλώ επιβεβαιώστε.");
          if (r==true)
          {
              window.open ("<?php echo base_url('');?>",'_self',false);  
          }
          return false;
    });


}) //end of (document).ready(function())

function getcourses(){
        var classid = $('#classes option:selected').val();

        $('.courserow:visible').remove();

        var postdata = {'jsclassid': classid};
        var post_url = "<?php echo base_url()?>curriculum/courses";
        $.ajax({
          type: "POST",
          url: post_url,
          data : postdata,
          dataType:'json',
          // async: false,
          success: function(courses) 
            {
              if(courses){
              $.each(courses,function(id,course) 
                {
                  newcourserow = courserowtemplate.clone();
                  newcourserow.removeClass('hidden');
                  var whereaddcourserow = $('.courserow:last');
                  var fields = newcourserow.find('input[type="text"]');
                  fields.eq(0).attr("name", "course[" + id +"]");        
                  fields.eq(0).attr('id', "courseid["+id+"]");
                  fields.eq(0).prop('value', course);
                  fields.eq(0).attr('value', course);
                  newcourserow.attr('id', id);
                  newcourserow.insertAfter(whereaddcourserow);

                   var newpostdata = {'jsclassid': classid, 'jscourseid': id};
                          var post_url = "<?php echo base_url()?>curriculum/lessons";
                          $.ajax({
                            type: "POST",
                            url: post_url,
                            data : newpostdata,
                            dataType:'json',
                            // async:false,
                            success: function(lessons) 
                              {
                               // console.log(lessons);
                               var firsttitle = lessons[Object.keys(lessons)[0]]['title'];
                               var firsthours = lessons[Object.keys(lessons)[0]]['hours'];
                               var firstid = Object.keys(lessons)[0];
                               fields.eq(1).attr("name", "title[" + firstid +"]");
                               fields.eq(1).attr('id', "lessonid"+firstid);
                               fields.eq(1).prop('value', firsttitle);
                               fields.eq(1).attr('value', firsttitle);
                               fields.eq(2).attr("name", "hours[" + firstid +"]");
                               fields.eq(2).attr('id', "hoursid"+firstid);
                               fields.eq(2).prop('value', firsthours);
                               fields.eq(2).attr('value', firsthours);
                               for (var i = 1; i < Object.keys(lessons).length; i++) {
                                  var lessonid = Object.keys(lessons)[i];
                                  var lessontitle = lessons[Object.keys(lessons)[i]]['title'];
                                  var lessonhours = lessons[Object.keys(lessons)[i]]['hours'];
                                  
                                  var newlessonrow = lessonrowtemplate.clone();
                                  newlessonrow.removeClass('hidden');
                                  var subfields = newlessonrow.find('input[type=text]');

                                  subfields.eq(0).attr("name", "title[" + lessonid +"]");        
                                  subfields.eq(0).attr('id', "lessonid["+lessonid+"]");
                                  subfields.eq(0).prop('value', lessontitle);
                                  subfields.eq(0).attr('value', lessontitle);
                                  subfields.eq(1).attr("name", "hours[" + lessonid +"]");        
                                  subfields.eq(1).prop('value', lessonhours);
                                  subfields.eq(1).attr('value', lessonhours);

                                  var whereaddlessonrow = $('#'+id+' .lessonrow:visible:last');
                                  newlessonrow.insertAfter(whereaddlessonrow);
                                }
                              }
                          })
                })
              }
              else if(classid!=0)
              {
                newcoursec++;
                newlessonc++; //every time we add a course it is also added one new lesson(+hours) field!
                var newcourserow = courserowtemplate.clone();
                newcourserow.removeClass('hidden');
                newcourserow.attr('id',-newcoursec);
                var fields=newcourserow.find('input[type=text]');
                fields.eq(0).attr('id', 'courseid['+(-newcoursec)+']');
                fields.eq(0).attr('name', 'course['+(-newcoursec)+']');
                fields.eq(1).attr('id', 'lessonid['+(-newlessonc)+']');
                fields.eq(1).attr('name', 'title['+(-newlessonc)+']');
                fields.eq(2).attr('name', 'hours['+(-newlessonc)+']');
                var whereaddcourserow = $('.courserow:hidden');
                newcourserow.insertAfter(whereaddcourserow);    
              }
            } //end success
          }) //end AJAX
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
                <li><a href="<?php echo base_url('/student')?>">Μαθητολόγιο</a></li>
                <li><a href="<?php echo base_url('exam')?>">Διαγωνίσματα</a></li>
                <li><a href="<?php echo base_url()?>files">Αρχεία</a></li>
                <li><a href="<?php echo base_url()?>cashdesk">Ταμείο</a></li>
                <li><a href="<?php echo base_url()?>announcements">Ανακοινώσεις</a></li>
              </ul>
            </li>
           <li class="dropdown">
              <a href="#" class="dropdown-toggle active" data-toggle="dropdown">Οργάνωση/Διαχείριση<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo base_url('/staff')?>">Προσωπικό</a></li>
                <li><a href="<?php echo base_url('/section')?>">Τμήματα</a></li>
                <li class="active"><a href="<?php echo base_url('/curriculum')?>">Πρόγραμμα Σπουδών</a></li>
                <li><a href="<?php echo base_url()?>">Στοιχεία Φροντιστηρίου</a></li>
              </ul>
            </li>
           <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Συγκεντρωτικές Αναφορές<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo base_url()?>">Αναφορές</a></li>
                <li><a href="<?php echo base_url()?>">Ιστορικό</a></li>
                <li><a href="<?php echo base_url()?>">Τηλ. Κατάλογοι</a></li>
                <li><a href="<?php echo base_url('/finance')?>">Οικονομικά</a></li>
              </ul>
            </li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Χρήστης<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header"><?php echo $user->surname.' '.$user->name;?></li>
                <li><a href="#">Αλλαγή κωδικού</a></li>
                <li><a href="<?php echo base_url('/curriculum/logout')?>">Αποσύνδεση</a></li>
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
    <h1>Πρόγραμμα Σπουδών</h1>
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
	        <li class="active">Πρόγραμμα Σπουδών</li>
	      </ul>
      </div>

     <p><h3>Επεξεργασία προγράμματος σπουδών</h3></p>
      
      <ul class="nav nav-tabs" style="margin-bottom:15px;">
        <li class="active"><a href="<?php echo base_url('/curriculum')?>">Πρόγραμμα Σπουδών</a></li>
        <li><a href="<?php echo base_url('/curriculum/tutorsperlesson')?>">Διδάσκωντες</a></li>
      </ul>

    
	<div class="row">

    <div class="col-md-12">
        <!-- <form action="<?php echo base_url('/curriculum')?>" method="post" accept-charset="utf-8" role="form"> -->
     	<div class="row"> 
        <div class="col-md-12" id="group1">
		    	 <div class="mainform">
              <div class="panel panel-default">
       	        <div class="panel-heading">
              		<span class="icon">
               			<i class="icon-sitemap"></i>
            			</span>
            			<h3 class="panel-title">Κατευθύνσεις & Μαθήματα</h3>
              			<div class="buttons">
                			<button enabled id="editform1" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
              			</div>
          		  </div>
	              <div class="panel-body">
        	        <div class="row">	
                    <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3">
                      <div class="form-group">
                        <label>Επιλέξτε Τάξη:</label>
                        <select id="classes" class="form-control" name="class_name">
                          <option value="0"></option>
                          <?php if(!empty($class)):?>
                            <?php foreach ($class as $data):?>
                              <option value="<?php echo $data['id'];?>"><?php echo $data['class_name'];?></option>
                            <?php endforeach;?>
                          <?php endif;?>
                        </select>
                      </div>
                    </div>
                  </div>
<!-- template row start -->
                  <div class="row courserow hidden">
                    <div class="col-sm-6"> <!-- Κατεύθυνση -->
                      <div class="col-sm-12">
                        <div class="form-group">
                          <label>Κατεύθυνση</label>
                          <div class="input-group">
                            <div class="input-group-btn">
                              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
                              <ul class="dropdown-menu">
                                <li><a class="addcoursebtn" href="#" onclick="return false;"><i class="icon-plus"> </i>Προσθήκη Νέας</a></li>
                                <li><a class="delcoursebtn" href="#" onclick="return false;"><i class="icon-trash"> </i>Διαγραφή</a></li>
                              </ul>
                            </div>
                            <input type="text" class="form-control" value="">
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="col-xs-12 lessonrow"> <!-- Μαθημα -->
                        <div class="col-xs-10">
                          <div class="form-group">
                            <label>Μάθημα</label>
                            <div class="input-group">
                              <div class="input-group-btn">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                  <li><a class="addlessonbtn" href="#" onclick="return false;"><i class="icon-plus"> </i>Προσθήκη Νέου</a></li>
                                  <li><a class="dellessonbtn" href="#" onclick="return false;"><i class="icon-trash"> </i>Διαγραφή</a></li>
                                </ul>
                              </div>
                              <input type="text" class="form-control" value="">
                            </div>
                          </div>
                        </div>
                      <div class="col-xs-2" style="padding-left:0px;padding-right:0px;"> <!-- Ώρες -->
                        <div class="form-group">
                          <label>Ώρες</label>
                          <input type="text" class="form-control" name="course" value="">
                        </div>
                      </div>
                     </div>
                    </div>
                  </div> 
  <!-- template row end -->
         	    	</div>
              </div>
            </div>
   	      </div>
  	    </div>
      </div>
  	</div>

    <div class="row">
    	<div class="col-md-12">    
        <div class="btn-toolbar">
          <div class="btn-group">
          	<!-- <button disabled id="submitbtn" type="submit" class="btn btn-danger">Αποθήκευση</button> -->
          	<button disabled id="cancelbtn" type="button" class="btn btn-default">Ακύρωση</button>
          </div>
          <div class="btn-group pull-right">
            <button disabled id="submitbtn" type="submit" class="btn btn-primary">Αποθήκευση</button>
            <button disabled id="ubdobtn type="button class="btn btn-primary pull-right"><i class="icon-undo"></i></button>
          </div>
      	</div>
      </div>
    </div>

    <!-- </form> -->

  </div>

  <div class="push"></div>

</div> <!-- end of body wrapper-->