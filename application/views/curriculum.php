<!-- for styling select in webkit browsers where there are problems when used with input-addon
http://silviomoreto.github.io/bootstrap-select/3/ -->
<link href="<?php echo base_url('assets/bootstrap-select/bootstrap-select.css')?>" rel="stylesheet">
<script src="<?php echo base_url('assets/bootstrap-select/bootstrap-select.js')?>"></script>
<style type="text/css">
  /*for styling bootstrap-select as the other fields when disabled!*/
  button.selectpicker:disabled{
    background-color: #EEEEEE;
    color: #555555;
    opacity: 1;
  }
</style>
<script type="text/javascript">
var undoarr=[]; //undo array

function toggleedit(togglecontrol, id) {
  if ($(togglecontrol).hasClass('active')){
    $('#' + id).closest('.mainform').find(':input').each(function(){
      $(this).attr('disabled', 'disabled');
      $(this).find('btn').attr('disabled','disabled');
      });
      $('#submitbtn').attr('disabled', 'disabled');
      $('#cancelbtn').attr('disabled', 'disabled');
      $('#undobtn').attr('disabled', 'disabled');
    }
    else {
    $('#' + id).closest('.mainform').find(':input').each(function(){
      $(this).removeAttr('disabled');
      });
      $(this).find('btn').removeAttr('disabled');
      $('#submitbtn').removeAttr('disabled');
      $('#cancelbtn').removeAttr('disabled');
      if(undoarr.length>0){
        $('#undobtn').removeAttr('disabled');
      }
    }
    $('#classes').removeAttr('disabled');
}


    var courserowtemplate, lessonrowtemplate; //globals
    var newcoursec=0, newlessonc=0; //globals



    $(document).on('click','.addcoursebtn', function(){
        newcoursec++;
        newlessonc++; //every time we add a course it is also added one new lesson(+hours) field!
        var newcourserow = courserowtemplate.clone();
        newcourserow.removeClass('hidden');
        newcourserow.attr('id',-newcoursec);
        var fields=newcourserow.find('input');
        var selectfields = newcourserow.find('select');
        fields.eq(0).attr('id', 'courseid['+(-newcoursec)+']');
        undoarr.push('courseid['+(-newcoursec)+']');
        fields.eq(0).attr('name', 'course['+(-newcoursec)+']');
        selectfields.eq(0).attr('id', 'lessonid['+(-newcoursec)+"]["+(-newlessonc)+']');
        selectfields.eq(0).attr('name', 'title['+(-newcoursec)+"]["+(-newlessonc)+']');
        fields.eq(1).attr('name', 'hours['+(-newlessonc)+']');
        var whereaddcourserow = $(this).parents('.courserow');
        newcourserow.insertAfter(whereaddcourserow);      
        var newlessonid = 'lessonid['+(-newcoursec)+"]["+(-newlessonc)+']'
        $(jq(newlessonid)).prop('disabled',false);
        $(jq(newlessonid)).selectpicker('mobile');
        $('#'+(-newcoursec)).find('input:first').focus();
        $('#undobtn').removeAttr('disabled');
    });
 
    $(document).on('click', '.addlessonbtn', function(){
          newlessonc++;
          var courseid = $(this).parents('.courserow').attr('id');
          var newlessonrow = lessonrowtemplate.clone();
          newlessonrow.removeClass('hidden');
          var fields=newlessonrow.find('input');
          var selectfields = newlessonrow.find('select');
          selectfields.eq(0).attr('id', 'lessonid['+courseid+']['+(-newlessonc)+']');
          undoarr.push('lessonid['+courseid+']['+(-newlessonc)+']');
          selectfields.eq(0).attr('name', 'title['+courseid+']['+(-newlessonc)+']');
          fields.eq(0).attr('name', 'hours['+courseid+']['+(-newlessonc)+']');
          var whereaddlessonrow = $(this).parents('.lessonrow');
          newlessonrow.insertAfter(whereaddlessonrow);
          var newlessonid = 'lessonid['+courseid+']['+(-newlessonc)+']';
          $(jq(newlessonid)).prop('disabled',false);
          $(jq(newlessonid)).selectpicker('mobile');
          $('#undobtn').removeAttr('disabled');
    })
    
    
    //some characters needs to be escaped in order to recognize the id
    //http://learn.jquery.com/using-jquery-core/faq/how-do-i-select-an-element-by-an-id-that-has-characters-used-in-css-notation/
    function jq( myid ) {
    return "#" + myid.replace( /(:|\.|\[|\])/g, "\\$1" );
    }
    

    $(document).on('click', '#undobtn', function(){
      var id = undoarr.pop();
      if(id.substring(0,8)=='lessonid'){
        $(jq(id)).parents('.lessonrow').remove();
      }
      else if (id.substring(0,8)=='courseid'){
        $(jq(id)).parents('.courserow').remove(); 
      }
      if(undoarr.length==0)
        {
          $('#undobtn').attr('disabled','disabled');
          var visiblecourses = $('.courserow:visible');
          if(visiblecourses.length==0) {
            $("#classes option").removeAttr('selected');
            $("#classes option:first").attr("selected", "selected");
          }
        }
    });


    $(document).on('click', '.delcoursebtn', function(){
      var r=confirm('Πρόκειται να διαγράψετε μία κατεύθυνση. Συνίσταται να μην το κάνετε αν έχετε αντιστοιχίσει έστω και 1 μαθητή σε αυτήν, ακόμα και σε παλαιότερη σχολική χρονιά. Μαζί με την κατεύθυνση θα διαγραφούν και όλα τα μαθήματα που τυχών έχετε αντιστοιχίσει σε αυτήν. Η ενέργεια αυτή δεν αναιρείται. Παρακαλώ επιβεβαιώστε.')
      if (r==true)
      {
        var courses = $('.courserow:visible');        
        var whereaddcourserow = $(this).parents('.panel-body');
        // window.open ('<?php echo base_url("curriculum/delcourse");?>/'+id,'_self',false);
        $(this).parents('.courserow').remove();

        //if no courses remain in a class we have to insert a new course/lesson field ready to be populated!
        if (courses.length==1)
        {
          newcoursec++;
          newlessonc++; //every time we add a course it is also added one new lesson(+hours) field!
          var newcourserow = courserowtemplate.clone();
          newcourserow.removeClass('hidden');
          newcourserow.attr('id',-newcoursec);
          var fields=newcourserow.find('input');
          var selectfields=newcourserow.find('select');
          fields.eq(0).attr('id', 'courseid['+(-newcoursec)+']');
          undoarr.push('courseid['+(-newcoursec)+']');
          fields.eq(0).attr('name', 'course['+(-newcoursec)+']');
          selectfields.eq(0).attr('id', 'lessonid['+(-newcoursec)+']['+(-newlessonc)+']');
          selectfields.eq(0).attr('name', 'title['+(-newcoursec)+']['+(-newlessonc)+']');
          fields.eq(1).attr('name', 'hours['+(-newcoursec)+']['+(-newlessonc)+']');
          newcourserow.appendTo(whereaddcourserow);
          var newlessonid = 'lessonid['+(-newcoursec)+']['+(-newlessonc)+']';
          $(jq(newlessonid)).prop('disabled',false);
          $(jq(newlessonid)).selectpicker('mobile');
          $('#'+(-newcoursec)).find('input:first').focus();
          $('#undobtn').removeAttr('disabled');
        }
      }
    });

    $(document).on('click', '.dellessonbtn', function(){
      var r=confirm('Πρόκειται να διαγράψετε ένα μάθημα. Συνίσταται να μην το κάνετε αν έχετε αντιστοιχίσει έστω και 1 μαθητή σε αυτό, ακόμα και σε παλαιότερη σχολική χρονιά. Η ενέργεια αυτή δεν αναιρείται. Παρακαλώ επιβεβαιώστε.')
      if (r==true)
      {
        var courselessons = $(this).parents('.courserow').find('.lessonrow');
        var whereaddlessonrow = $(this).parents('.col-sm-6');
        var courseid = $(this).parents('.courserow').attr('id');
        // window.open ('<?php echo base_url("curriculum/dellesson");?>/'+id,'_self',false);        
        $(this).parents('.lessonrow').remove();

        //if no lessons remain in a course we have to insert a new lesson field ready to be populated with a new lesson!
        if (courselessons.length==1)
        {
          newlessonc++;
          var newlessonrow = lessonrowtemplate.clone();
          newlessonrow.removeClass('hidden');
          var fields=newlessonrow.find('input');
          var selectfields=newlessonrow.find('select');
          selectfields.eq(0).attr('id', 'lessonid['+courseid+']['+(-newlessonc)+']');
          selectfields.eq(0).attr('name', 'title['+courseid+']['+(-newlessonc)+']');
          fields.eq(0).attr('name', 'hours['+courseid+']['+(-newlessonc)+']');
          newlessonrow.appendTo(whereaddlessonrow);
          var newlessonid = 'lessonid['+courseid+']['+(-newlessonc)+']';
          $(jq(newlessonid)).prop('disabled',false);
          $(jq(newlessonid)).selectpicker('mobile');
        }
      }
    });

    $(document).ready(function(){

        courserowtemplate = $('.courserow:hidden'); //store the template to be cloned
        lessonrowtemplate = $('.lessonrow:hidden'); //store the template to be cloned

        // $('#classes').selectpicker();
        
        $.ajax({
          type: "POST",
          url: "<?php echo base_url()?>curriculum/lessontitles/",
          dataType:'json',
          success: function(lessontitles){
            // titles = lessontitles;
            $.each(lessontitles,function(id,text) 
            {
              // here we're creating a new select option for each group
              var opt = $('<option />');
              opt.val(id);
              opt.text(text);
              $('select:hidden').append(opt); 
            });
            $('select:hidden').val("");
            $('select:hidden').prop('value', "");
            $('select:hidden').attr('value', "");
            } 
        });

        $('#cancelbtn').click(function(){
          window.open("<?php echo base_url('curriculum/cancel');?>", '_self', false);
        });

        $("body").on('click', '#editform1, #editform2', function(){
          toggleedit(this, this.id);
          $(this).removeAttr('disabled');

        });

        
        $('#classes').change(function(){
            getcourses();
            $( document ).ajaxComplete(function() {
                $('.mainform').find(':input:visible').attr('disabled','disabled');
                $('#submitbtn').attr('disabled','disabled');
                $('#cancelbtn').attr('disabled','disabled');
                $('#undobtn').attr('disabled','disabled');
                $('#classes').removeAttr('disabled');
                $('#editform1').removeAttr('disabled');
                $('#editform1').removeClass('active');
            });
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
                  var fields = newcourserow.find('input');
                  var selectfields = newcourserow.find('select');
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
                        if(lessons){
                           var firstcataloglessonid = lessons[Object.keys(lessons)[0]]['cataloglesson_id'];
                           var firstlessontext = lessons[Object.keys(lessons)[0]]['title'];
                           var firsthours = lessons[Object.keys(lessons)[0]]['hours'];
                           var firstid = Object.keys(lessons)[0];
                           selectfields.eq(0).attr("name", "title[" + id +"][" + firstid +"]");
                           selectfields.eq(0).attr('id', 'lessonid['+id+']['+firstid+']');
                           fields.eq(1).attr("name", "hours[" + firstid +"]");
                           fields.eq(1).attr('id', "hoursid"+firstid);
                           fields.eq(1).prop('value', firsthours);
                           fields.eq(1).attr('value', firsthours);
                           for (var i = 1; i < Object.keys(lessons).length; i++) {
                              var lessonid = Object.keys(lessons)[i];
                              var cataloglessonid = lessons[Object.keys(lessons)[i]]['cataloglesson_id'];
                              var lessontext = lessons[Object.keys(lessons)[i]]['title'];
                              var lessonhours = lessons[Object.keys(lessons)[i]]['hours'];
                              
                              var newlessonrow = lessonrowtemplate.clone();
                              newlessonrow.removeClass('hidden');
                              var subfields = newlessonrow.find('input');
                              var subselectfields = newlessonrow.find('select');

                              subselectfields.eq(0).attr("name", "title["+id+"]["+lessonid +"]");        
                              subselectfields.eq(0).attr('id', "lessonid["+id+"]["+lessonid+"]");
                              subfields.eq(0).attr("name", "hours[" + lessonid +"]");        
                              subfields.eq(0).prop('value', lessonhours);
                              subfields.eq(0).attr('value', lessonhours);

                              var whereaddlessonrow = $('#'+id+' .lessonrow:visible:last');
                              newlessonrow.insertAfter(whereaddlessonrow);

                              var nextlessonid = "lessonid["+id+"]["+lessonid+"]";
                              $(jq(nextlessonid)).val(cataloglessonid);
                              $(jq(nextlessonid)).prop('value', cataloglessonid);
                              $(jq(nextlessonid)).attr('value', cataloglessonid);
                              $(jq(nextlessonid)).prop('disabled',false);
                              $(jq(nextlessonid)).selectpicker('mobile');
                            }

                            var firstlessonid = 'lessonid['+id+']['+firstid+']';
                            $(jq(firstlessonid)).val(firstcataloglessonid);
                            $(jq(firstlessonid)).prop('value', firstcataloglessonid);
                            $(jq(firstlessonid)).attr('value', firstcataloglessonid);
                            $(jq(firstlessonid)).prop('disabled',false);
                            $(jq(firstlessonid)).selectpicker('mobile');
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
                var selectfields = newcourserow.find('select');
                fields.eq(0).attr('id', 'courseid['+(-newcoursec)+']');
                undoarr.push('courseid['+(-newcoursec)+']');
                fields.eq(0).attr('name', 'course['+(-newcoursec)+']');
                selectfields.eq(0).attr('id', 'lessonid['+(-newcoursec)+"]["+(-newlessonc)+']');
                selectfields.eq(0).attr('name', 'title['+(-newcoursec)+"]["+(-newlessonc)+']');
                fields.eq(1).attr('name', 'hours['+(-newlessonc)+']');
                var whereaddcourserow = $('.courserow:hidden');
                newcourserow.insertAfter(whereaddcourserow);

                $('#undobtn').removeAttr('disabled');
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
        <li><a href="<?php echo base_url('/curriculum/tutorsperlesson')?>">Μαθήματα & Διδάσκωντες</a></li>
      </ul>

    
	<div class="row">

    <div class="col-md-12">
     <form action="<?php echo base_url('/curriculum')?>" method="post" accept-charset="utf-8" role="form">
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
                              <select class="form-control">
                                <option></option>
                              </select>
                            </div>
                          </div>
                        </div>
                      <div class="col-xs-2" style="padding-left:0px;padding-right:0px;"> <!-- Ώρες -->
                        <div class="form-group">
                          <label>Ώρες</label>
                          <input type="text" class="form-control" name="" value="">
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
            <button disabled id="undobtn" type="button" class="btn btn-primary pull-right"><i class="icon-undo"></i></button>
          </div>
      	</div>
      </div>
    </div>

   </form>

  </div>

  <div class="push"></div>

</div> <!-- end of body wrapper-->