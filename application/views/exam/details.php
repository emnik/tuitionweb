<link href="<?php echo base_url('assets/bootstrap3-timepicker-0.2.6/css/bootstrap-timepicker.min.css') ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/bootstrap3-timepicker-0.2.6/js/bootstrap-timepicker.min.js') ?>" ></script>
<link href="<?php echo base_url('assets/bootstrap-datepicker-1.3.0/css/datepicker3.css') ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/bootstrap-datepicker-1.3.0/js/bootstrap-datepicker.js') ?>" ></script>
<script src="<?php echo base_url('assets/bootstrap-datepicker-1.3.0/js/locales/bootstrap-datepicker.el.js') ?>" charset="UTF-8"></script>
<!-- Using https://github.com/ivaynberg/select2 with https://github.com/t0m/select2-bootstrap-css -->
<link href="<?php echo base_url('assets/select2/select2.css')?>" rel="stylesheet">
<link href="<?php echo base_url('assets/select2/select2-bootstrap.css')?>" rel="stylesheet">
<script src="<?php echo base_url('assets/select2/select2.js')?>"></script>
<script src="<?php echo base_url('assets/select2/select2_locale_el.js')?>"></script>

<script type="text/javascript">

var undoarr=[];
var newrowc=0;
    
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
      if (undoarr.length>0){
        $('#undolessonbtn').removeAttr('disabled');
      }
      else
      {
        $('#undolessonbtn').attr('disabled', 'disabled');
      }
    }

}

    $(document).on('change', '.classes', function(){
      //document.getElementById('lessons').options.length = 0;
      $(this).parent().next().next().find('select').empty();
      getcourses($(this));
    })

    $(document).on('change', '.courses', function(){
      getlessons($(this));
    }); //end change event 

    $(document).on('change', '.lessons', function(){
      check_sections_exist($(this));
    }); //end change event 


$(document).ready(function(){
    <?php if (empty($group)):?>
      addlesson();
    <?php endif;?>

    $("#supervisorids").select2();

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

    $('#undolessonbtn').click(function(){
      if (undoarr.length>0)
      {
        var remc=undoarr.pop();
        $('#newrow'+remc).remove();
      }
      if (undoarr.length==0)
      {
        $(this).attr('disabled', 'disabled');
      }
    });

    $('#newlessonbtn').click(function(){
      addlesson();
    });


    $('#cancelbtn').click(function(){
      window.open("<?php echo base_url();?>exam/cancel/exam/<?php echo $exam['id'];?>", '_self', false);
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
    <?php if(empty($exam['title'])):?>   
        $('#editform1').addClass('active');
        $('#editform2').addClass('active');
        $('.mainform').find(':input:disabled').removeAttr('disabled');
        $('#submitbtn').removeAttr('disabled');
        $('#cancelbtn').removeAttr('disabled');
    <?php endif;?>

    


        $('.timecontainer input').timepicker({
          showMeridian:false,
          showSeconds:false,
          minuteStep:15,
          defaultTime:false,
          disableFocus:false,
          showInputs:false,
        })
        <?php if($exam['start_tm']==''):?>
        .timepicker('setTime', '10:00');
        if($('.timecontainer input').attr('value')=='00:00'){
          $('.timecontainer input').attr('value','10:00');
        }
        <?php endif;?>


    $('#delexam').click(function(){
        var r=confirm("Το παρών διαγώνισμα πρόκειται να διαγραφεί. Παρακαλώ επιβεβαιώστε.");
          if (r==true)
          {
              window.open ("<?php echo base_url('exam/delexam/'.$exam['id']);?>",'_self',false);  
          }
          return false;
    });


}) //end of (document).ready(function())

function getcourses(myclass){
        var classid = $(myclass).find('option:selected').val();

        //clear options from course select input
        var mycourse = $(myclass).parent().next().find('select');
        mycourse.empty();

        //the following is ajax post to populate the course dropdown 
        var postdata = {'jsclassid': classid};
        //post_url is the controller function where I want to post the data
        var post_url = "<?php echo base_url()?>exam/courses";
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
                  mycourse.append(opt);
                });
              mycourse.find('option:first').removeAttr('selected').attr("selected", "selected");
            } //end success
          }); //end AJAX
   
            //if only one course, get the lessons too. The above ajax query MUST be async=false to work!!! this one...
            if (mycourse.get(0).options.length==1){
              getlessons(mycourse);
            }
            else
            //select none so once a user selection in that happens to trigger the get lessons function
            {
                var opt = $('<option />');
                opt.val('none');
                opt.text(" ");
                mycourse.prepend(opt); 
                mycourse.find('option:first').removeAttr('selected').attr("selected", "selected");
            };

}


function getlessons(mycourse){
        var classid = $(mycourse).parent().parent().prev().find('option:selected').val();
        var courseid = $(mycourse).find('option:selected').val();

        //clear options from lessons select input
        var mylesson = $(mycourse).parent().parent().next().find('select')
        mylesson.empty();

        //the following is ajax post to populate the course dropdown 
        var postdata = {'jsclassid': classid, 'jscourseid': courseid};
        //post_url is the controller function where I want to post the data
        var post_url = "<?php echo base_url()?>exam/lessons";
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
                  mylesson.append(opt);
                });
                
                
                var opt = $('<option />'); // here we're creating a new select option for each group
                opt.val('none');
                opt.text(" ");
                mylesson.prepend(opt);

                mylesson.find('options:first').removeAttr('selected').attr("selected", "selected");

            } //end success
          }); //end AJAX
   
}

function check_sections_exist(mylesson){
        //var lessonid = $('#lessons option:selected').val();
        var lessonid = $(mylesson).find('option:selected').val();

        if (lessonid!=='none')
        {
          //the following is ajax post to populate the course dropdown 
          var postdata = {'jslessonid': lessonid};
          //post_url is the controller function where I want to post the data
          var post_url = "<?php echo base_url()?>exam/sections";
          $.ajax({
            type: "POST",
            url: post_url,
            data : postdata,
            dataType:'json',
            async: false,
            //courses is just a name that gets the result of the controller's function I posted the data
            success: function(sections) //we're calling the response json array 'courses data'
              {
                if(sections == false){
                  alert('Δεν υπάρχουν καταχωρημένα τμήματα για το μάθημα που επιλέξατε.\nΠαρόλα αυτά αν επιθυμείτε μπορείτε να αποθηκεύσετε το διαγώνισμα.');
                }
              } //end success
            }); //end AJAX
        }  
}

function addlesson(){
      newrowc++;
      undoarr.push(newrowc);
      $('#undolessonbtn').removeAttr('disabled'); 
      var template = $('.templaterow:hidden');
      var newrow = template.clone();
      var wheretoinsert = $('#lessonbtnrow').prev(); 
      newrow.attr('id', 'newrow'+newrowc);
      newrow.removeClass('hidden');
      newrow.removeClass('templaterow');
      var selects = newrow.find('select');
      selects[0].selectedIndex=selects[0].length;
      selects.eq(0).attr('name', 'class_id['+(-newrowc)+']');
      selects[1].options.length = 0;
      selects.eq(1).attr('name', 'course_id['+(-newrowc)+']');
      selects[2].options.length = 0;
      selects.eq(2).attr('name', 'lesson_id['+(-newrowc)+']');
      newrow.insertAfter(wheretoinsert);
}

function deletelesson(id)
{
      var postdata = {'jsdellessonid': id};
      //post_url is the controller function where I want to post the data
      var post_url = "<?php echo base_url()?>exam/deletelesson";
      $.ajax({
        type: "POST",
        url: post_url,
        data : postdata,
        dataType:'json',
        async: true,
        success: function()
        {
          alert('ok');
        } //end success
        }); //end AJAX
    
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
	        <li class="active">Λεπτομέρειες</li>
	      </ul>
      </div>

     <p> 
      <h3>
        <?php
        if(!empty($exam['title'])){
          echo 'Επεξεργασία διαγωνίσματος';
        }
        else 
        {
          echo "Νέο διαγώνισμα";
        };?>
      </h3>
    </p>
      
      <ul class="nav nav-tabs">
        <li class="active"><a href="<?php echo base_url()?>exam/">Προγραμματισμός</a></li>
        <li><a href="<?php echo base_url()?>exam/supervisors">Επιτηρητές</a></li>
      </ul>

      <ul class="nav nav-pills" style="margin:15px 0px;">
        <li class="active"><a href="<?php echo base_url()?>exam/details/<?php echo $exam['id']?>">Λεπτομέρειες</a></li>
        <?php if(!empty($exam['lesson_id'])):?>
        	<li><a href="<?php echo base_url()?>exam/details/<?php echo $exam['id']?>/participants">Συμμετέχοντες</a></li>
      	<?php endif;?>
      </ul>



    
	<div class="row">
        <form action="<?php echo base_url()?>exam/details/<?php echo $exam['id']?>" method="post" accept-charset="utf-8" role="form">
    	<div class="col-md-12">
      	<div class="row"> <!-- section data -->
          <div class="col-md-12" id="group1">
			 <div class="mainform">
                 <div class="panel panel-default">
       			     <div class="panel-heading">
              			<span class="icon">
                			<i class="icon-pencil"></i>
              			</span>
              			<h3 class="panel-title">Διαγώνισμα</h3>
              			<div class="buttons">
                  			<button enabled id="editform1" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
              			</div>
            		</div>
	            <div class="panel-body">
	        	    <div class="row">	
	        	   		<div class="col-md-2 col-xs-12">
	        	   			<div class="form-group datecontainer">  
                		        <label>Ημερομηνία</label>
                        		<input disabled class="form-control" id="date" type="text" placeholder="" name="date" value="<?php  if($exam['date']!=='0000-00-00') echo implode('-', array_reverse(explode('-', $exam['date'])));?>">
	        	    		</div>
                  </div>
                  <div class="col-md-2 col-md-offset-6 col-xs-6">
                    <div class="form-group timecontainer">
                          <label>Έναρξη</label>
                          <input disabled class="form-control" id="starttm" type="text" placeholder="" name="start_tm" value="<?php echo $exam['start_tm'];?>">
                    </div>
                  </div>
                  <div class="col-md-2 col-xs-6 timecontainer">
                    <div class="form-group">
                        <label>Λήξη</label>
                        <input disabled class="form-control" id="endtm" type="text" placeholder="" name="end_tm" value="<?php echo $exam['end_tm'];?>">
                    </div>
                  </div>
               </div> <!--end of row-->

                <div class="row">
                    <div class="col-md-12 col-xs-12">
                      <div class="form-group">
                        <label>Τίτλος</label>
                        <input type="text" disabled class="form-control" id="title" placeholder="" cols="1" name="title" value="<?php echo $exam['title'];?>">
                      </div>
                  </div>
                    <div class="col-md-12 col-xs-12">
                      <div class="form-group">
                        <label>Περιγραφή</label>
                        <textarea disabled class="form-control" id="description" placeholder="" cols="3" name="description"><?php echo $exam['description'];?></textarea>
                      </div>
                  </div>
                </div>               


       	    	</div>
		     </div> <!-- end of content row -->
	       </div>
	 	  </div>
		</div><!-- end of section data    -->


</div>

      <div class="col-md-12">
        <div class="row"> <!-- section data -->
          <div class="col-md-12" id="group2">
            <div class="mainform">
              <div class="panel panel-default">
                 <div class="panel-heading">
                    <span class="icon">
                      <i class="icon-pencil"></i>
                    </span>
                    <h3 class="panel-title">Μαθήματα</h3>
                    <div class="buttons">
                        <button enabled id="editform2" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
                    </div>
                </div>
              <div class="panel-body">
<!-- start of template row-->              
                <div class="row templaterow hidden">
                  <div class="col-md-4 col-xs-6">
                  <div class="form-group">
                      <label>Τάξη</label>
                        <select disabled class="form-control classes" name="">
                          <?php foreach ($class as $data):?>
                            <option value="<?php echo $data['id']?>"><?php echo $data['class_name'];?></option>
                          <?php endforeach;?>
                        </select>
                        </div>
                      </div>
                  <div class="col-md-4 col-xs-6">
                        <div class="form-group">
                          <label>Κατεύθυνση</label>
                          <select disabled class="form-control courses" name="">
                          </select>
                        </div>
                      </div>
                      <div class="col-md-4 col-xs-6">
                        <div class="form-group">
                          <label>Μάθημα</label>
                          <select disabled class="form-control lessons" name="">
                          </select>
                        </div>
                      </div>
                    </div>
<!--end of template row-->
<!--group data if exists...-->
              <?php if(!empty($group)):?>
                <?php foreach ($group as $groupdata):?>
                <div class="row">
                  <div class="col-xs-6 col-md-4">
                  <div class="form-group">
                      <label>Τάξη</label>
                        <select disabled class="form-control classes" name="class_id[<?php echo $groupdata['id'];?>]">
                          <?php $sel=false;?>
                          <?php foreach ($class as $data):?>
                            <option value="<?php echo $data['id']?>"<?php if ($groupdata['class_id'] == $data['id']){echo 'selected="selected"'; $sel=true;}?>><?php echo $data['class_name'];?></option>
                          <?php endforeach;?>
                          <option value="" <?php if($sel==false) echo 'selected';?>></option>
                        </select>
                        </div>
                      </div>
                  <div class=" col-md-4 col-xs-6">
                        <div class="form-group">
                          <label>Κατεύθυνση</label>
                          <select disabled class="form-control courses" name="course_id[<?php echo $groupdata['id'];?>]">
                              <?php $sel=false;?>
                              <?php foreach ($groupdata['course'] as $id=>$value):?>
                                <option value="<?php echo $id;?>"<?php if ($groupdata['course_id'] == $id){echo 'selected="selected"'; $sel=true;}?>><?php echo $value;?></option>
                              <?php endforeach;?>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3 col-xs-6">
                        <div class="form-group">
                          <label>Μάθημα</label>
                          <select disabled class="form-control lessons" name="lesson_id[<?php echo $groupdata['id'];?>]">
                              <?php $sel=false;?>
                              <?php foreach ($groupdata['lesson'] as $id=>$value):?>
                                <option value="<?php echo $id;?>"<?php if ($groupdata['lesson_id'] == $id){echo 'selected="selected"'; $sel=true;}?>><?php echo $value;?></option>
                              <?php endforeach;?>
                              <option value="none" <?php if($sel==false) echo 'selected';?>></option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-1 col-xs-6">
                        <div style="padding-top:25px;">
                          <a href="#" onclick="deletelesson(<?php echo $groupdata['id'];?>);return false;" class="btn btn-default pull-right"><i class="icon-trash"></i></a>
                        </div>
                      </div>                      
                    </div>
                  <?php endforeach;?>
                  <?php endif;?>
<!--end of existing group data-->

                    <div class="row" id="lessonbtnrow">
                      <div class="col-md-12">
                      <div class="btn-toolbar">
                        <div class="btn-group pull-left">
                          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Διαγραφή <span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu">
<!--                           <?php foreach ($group as $key=>$value):?>
                            <li id="delexam<?php echo $value['id'];?>">
                              <a onclick="delexamlist();return false;" href="#">
                                <?php foreach ($class as $ckey => $cvalue) {
                                  if ($cvalue['id'] == $value['class_id']) $stringv=$class[$key]['class_name'];
                                }; 
                                $tmp1 = $value['course_id'];
                                $tmp2 = $value['lesson_id'];
                                echo $stringv.' ['.$value['course'][$tmp1].'] '.$value['lesson'][$tmp2];?>
                              </a>
                            </li>
                        <?php endforeach;?> -->
                        </ul>
                        </div>
                        <div class="btn-group pull-right">
                          <button id="newlessonbtn" type="button" class="btn btn-primary" disabled >Προσθήκη</button>
                          <button id="undolessonbtn" type="button" class="btn btn-primary" disabled ><span class="icon"><i class="icon-undo"></i></span></button>
                        </div>
                      </div>
                      </div>
                </div>

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
          	<button disabled id="submitbtn" type="submit" class="btn btn-primary">Αποθήκευση</button>
          	<button disabled id="cancelbtn" type="button" class="btn btn-default">Ακύρωση</button>
          </div>
          <div class="btn-group pull-right">
            <a id="delexam" href="#" class="btn btn-default" ><i class="icon-trash"></i></a>
            <a id="newexambtn" href="<?php echo base_url('exam/newexam');?>" class="btn btn-default"><i class="icon-plus"></i></a>
          </div>
      	</div>
      </div>
    </div>

    </form>
<!--       <div class="row">
        <div class="col-md-12">   
        <ul class="pager">
            <li class="previous <?php if(empty($prevnext['prev'])){echo 'disabled';};?>"  <?php if(empty($prevnext['prev'])){echo "onclick='return false;'";};?>><a href="<?php echo base_url().'exam/details/'.$prevnext['prev'];?>"><i class="icon-chevron-left"></i> Προηγούμενο</a></li>
            <li class="next <?php if(empty($prevnext['next'])){echo 'disabled';};?>"  <?php if(empty($prevnext['next'])){echo "onclick='return false;'";};?> ><a href="<?php echo base_url().'exam/details/'.$prevnext['next'];?>">Επόμενο <i class="icon-chevron-right"></i></a></li>
            </ul>
         </div>
      </div> -->
    </div> 
  </div>

</div><!--end of main container-->

<div class="push"></div>

</div> <!-- end of body wrapper-->