<link href="<?php echo base_url('assets/bootstrap-datepicker-1.3.0/css/datepicker3.css') ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/bootstrap-datepicker-1.3.0/js/bootstrap-datepicker.js') ?>" ></script>
<script src="<?php echo base_url('assets/bootstrap-datepicker-1.3.0/js/locales/bootstrap-datepicker.el.js') ?>" charset="UTF-8"></script>
<script type="text/javascript">

function toggleedit(togglecontrol, id) {

  if ($(togglecontrol).hasClass('active')){
    $('#' + id).closest('.panel').find(':input').each(function(){
        $(this).attr('disabled', 'disabled');
      });
    }
  else 
    {
      $('#' + id).closest('.panel').find(':input').removeAttr('disabled');
    };
    
    $(togglecontrol).removeAttr('disabled');
}

$(document).ready(function(){

    //we must enable all form fields to submit the form with no errors!
    $("body").on('click', '#submitbtn', function(){
        $('.panel').find(':input:disabled').removeAttr('disabled');
        $('form').submit();
    });


    $("body").on('click', '#editform1, #editform2, #editform3', function(){
      toggleedit(this, this.id);

      var all=$('.panel-body').find(':input').length;
      var disabled=$('.panel-body').find(':input:disabled').length;
      
      if(all==disabled){
          $('#submitbtn').attr('disabled', 'disabled');
          $('#cancelbtn').attr('disabled', 'disabled');
        }
      else
        {
          $('#submitbtn').removeAttr('disabled');
          $('#cancelbtn').removeAttr('disabled');
        }
    });

    $('#cancelbtn').click(function(){
      window.open("<?php echo base_url()?>student/cancel/card/<?php echo $student['id']?>", '_self', false);
    });

    //if it is a new registration the fields should be enabled
    <?php if(empty($student['surname'])):?>   
        $('#editform1').addClass('active');
        $('#editform2').addClass('active');
        $('#editform3').addClass('active');
        var toggle = document.getElementById("mainform");
        toggle.disabled = false;
        $('#mainform :input').removeAttr('disabled');
        $('#submitbtn').removeAttr('disabled');
        $('#cancelbtn').removeAttr('disabled');
    <?php endif;?>
    

    $('#classes').change(function(){
        var classid = $('#classes option:selected').val();
        //alert(classid);

        //clear options from course select input
        document.getElementById('courses').options.length = 0;

        //the following is ajax post to populate the course dropdown 
        var postdata = {'jsclassid': classid};
        //post_url is the controller function where I want to post the data
        var post_url = "<?php echo base_url()?>student/courses";
        $.ajax({
          type: "POST",
          url: post_url,
          data : postdata,
          dataType:'json',
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
            } //end success
          }); //end AJAX
      
    }); //end change event 

  $('#apy_receiver').popover({
      placement:"top",
      trigger:"click",
      container:'body',
      html:true,
      title:"<span style=\"text-align:center;font-weight:bold;font-size:95%;color:grey;\">Επικόληση ονόματος:</span>",
      content:"<div style=\"text-align:center;\"><button type=\"button\" class=\"btn btn-default btn-sm\" onclick=\"paste_name('apy_receiver','name');\">Μαθητή</button><button type=\"button\" class=\"btn btn-default btn-sm\" onclick=\"paste_name('apy_receiver','fathersname');\">Πατέρα</button><button type=\"button\" class=\"btn btn-default btn-sm\" onclick=\"paste_name('apy_receiver','');\"><i class=\"icon-remove-circle\"></i></button></div>"
  });


    $('#afm_owner').popover({
      placement:"top",
      container:'body',
      trigger:"click",
      html:true,
      title:"<span style=\"text-align:center;font-weight:bold;font-size:95%;color:grey;\">Επικόληση ονόματος:</span>",
      content:"<div style=\"text-align:center;\"><button type=\"button\" class=\"btn btn-default btn-sm\" onclick=\"paste_name('afm_owner','name');\">Μαθητή</button><button type=\"button\" class=\"btn btn-default btn-sm\" onclick=\"paste_name('afm_owner','fathersname');\">Πατέρα</button><button type=\"button\" class=\"btn btn-default btn-sm\" onclick=\"paste_name('afm_owner','');\"><i class=\"icon-remove-circle\"></i></button></div>"
  });

    $('li.dash').click(function(){
      $('#footerModal').modal();
    });


    $('.datecontainer input')
    .datepicker({
        format: "dd-mm-yyyy",
        language: "el",
        autoclose: true,
        todayHighlight: true
    })
    .on('focus click tap vclick', function (event) {
    //stop keyboard events and focus on the datepicker widget to get the date.
    //this is most usefull in android where the android's keyboard was getting in the way...
        event.stopImmediatePropagation();
        event.preventDefault();
        $(this).blur();
    });
    

}); //end of (document).ready

function paste_name(where,who){
    if (who==''){
      $('#'+where).val('');  
    }
    else
    {
      $('#'+where).val($('#surname').val()+' '+$('#'+who).val());  
      $('#'+where).popover('hide');
    };

}


</script>


</head>
<body>
 <div class="wrapper"> <!--body wrapper for css sticky footer-->

    <div class="navbar navbar-inverse navbar-top">
      <div class="container">
      <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <col-md- class="icon-bar"></col-md->
            <col-md- class="icon-bar"></col-md->
            <col-md- class="icon-bar"></col-md->
          </button>
          <a class="navbar-brand" href="<?php echo base_url()?>">TuitionWeb</a>
     </div>

      <div class="navbar-collapse collapse" role="navigation">
        <ul class="nav navbar-nav">
           <li class="dropdown">
              <a href="#" class="dropdown-toggle active" data-toggle="dropdown">Λειτουργία<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="active"><a href="<?php echo base_url('student')?>">Μαθητολόγιο</a></li>
                <li><a href="<?php echo base_url('exam')?>">Διαγωνίσματα</a></li>
                <!-- <li><a href="<?php echo base_url()?>files">Αρχεία</a></li> -->
                <!-- <li><a href="<?php echo base_url()?>cashdesk">Ταμείο</a></li> -->
                <!-- <li><a href="<?php echo base_url()?>announcements">Ανακοινώσεις</a></li> -->
              </ul>
            </li>
           <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Οργάνωση/Διαχείριση<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo base_url('staff')?>">Προσωπικό</a></li>
                <li><a href="<?php echo base_url('section')?>">Τμήματα</a></li>
                <li><a href="<?php echo base_url('curriculum/edit')?>">Πρόγραμμα Σπουδών</a></li>
                <li><a href="<?php echo base_url('curriculum/edit/tutorsperlesson')?>">Μαθήματα-Διδάσκωντες</a></li>
                <li><a href="<?php echo base_url()?>">Στοιχεία Φροντιστηρίου</a></li>
              </ul>
            </li>
           <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Συγκεντρωτικές Αναφορές<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo base_url('reports')?>">Αναφορές</a></li>
                <li><a href="<?php echo base_url('history')?>">Ιστορικό</a></li>
                <li><a href="<?php echo base_url('telephones')?>">Τηλ. Κατάλογοι</a></li>
                <li><a href="<?php echo base_url('finance')?>">Οικονομικά</a></li>
              </ul>
            </li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Χρήστης<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header"><?php echo $user->surname.' '.$user->name;?></li>
                <li><a href="#">Αλλαγή κωδικού</a></li>
                <li><a href="<?php echo base_url('student/logout')?>">Αποσύνδεση</a></li>
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
    <h1>Καρτέλα Μαθητή</h1>
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
            <li><a href="<?php echo base_url()?>student">Μαθητολόγιο</a></li>
            <li class="active">Καρτέλα μαθητή</li>
            <li class="dash"><i class="icon-dashboard icon-small"></i></li>
          </ul>
        </div>
        
        <p>
          <h3>
            <?php
            if(!empty($student['surname'])){
              echo $student['surname'].' '.$student['name'];
            }
            else {
              echo "Νέα εγγραφή";
            };?>
          </h3>
        </p>        

        <ul class="nav nav-tabs">
          <li class="active">
          <a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>">Στοιχεία</a>
          </li>
          <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/contact">Επικοινωνία</a></li>
          <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance">Φοίτηση</a></li>
          <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance">Οικονομικά</a></li>
        </ul>

        <p></p>

        <div class="visible-xs visible-sm">
          <div class="row">
            <div class="col-md-12">
              <div class="btn-group pull-left">  
                <a class="btn btn-default btn-sm" href="#group1">Μαθητή</a>
                <a class="btn btn-default btn-sm" href="#group2">Μαθητολογίου</a>
                <a class="btn btn-default btn-sm" href="#group3">Οικονομμικών</a>
              </div>
            </div>      
          </div>
        </div>


        <form id='mainform' action="<?php echo base_url()?>student/card/<?php echo $student['id']?>" method="post" accept-charset="utf-8">
       
       <p></p>

        <div class="row"> <!-- first row --> 
          <div class="col-md-6"> <!-- first row left side -->
            <div class="panel panel-default" id="group1">
              <div class="panel-heading">
                  <span class="icon">
                    <i class="icon-user"></i>
                  </span>
                  <h3 class="panel-title">Στοιχεία μαθητή</h3>
                  <div class="buttons">
                    <button enabled id="editform1" type="button" class="btn btn-default btn-sm pull-right" data-toggle="button"><i class="icon-edit"></i></button>
                </div>
              </div>
            <div class="panel-body">  
              <div class="row">
                <div class="col-md-3 col-sm-6 form-group">
                  <label>Κωδ.μαθητή(id)</label>
                  <input disabled class="form-control" type="text" placeholder="" name="id" value="<?php echo $regcard['id'];?>">
                </div>
              </div>
              <div class="row">
                 <div class="col-md-6 col-sm-6">
                    <div class="form-group"> 
                      <label>Όνομα</label>
                      <input disabled class="form-control" id="name" type="text" placeholder="" name="name" value="<?php echo $regcard['name'];?>">
                    </div>
                 </div>
                 <div class="col-md-6 col-sm-6">
                    <div class="form-group"> 
                      <label>Επίθετο</label>
                      <input disabled class="form-control" id="surname" type="text" placeholder="" name="surname" value="<?php echo $regcard['surname'];?>">
                    </div>
                 </div>
              </div>  
                <div class="row">
                  <div class="col-md-6 col-sm-6">
                    <div class="form-group">  
                      <label>Πατρώνυμο</label>
                      <input disabled class="form-control" id="fathersname" type="text" placeholder="" name="fathers_name" value="<?php echo $regcard['fathers_name'];?>">
                    </div>
                  </div>
                  <div class="col-md-6 col-sm-6">
                    <div class="form-group">
                      <label>Μητρώνυμο</label>
                      <input disabled class="form-control" type="text" placeholder="" name="mothers_name" value="<?php echo $regcard['mothers_name'];?>">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 col-sm-6">
                      <div class="form-group">
                        <label>Διεύθυνση</label>
                        <input disabled class="form-control" type="text" placeholder="" name="address" value="<?php echo $regcard['address'];?>">
                      </div>
                  </div>
                  <div class="col-md-6 col-sm-6">
                    <div class="form-group">
                      <label>Πόλη</label>
                      <select disabled class="form-control" name="region">
                        <?php $sel=false;?>
                        <?php foreach ($region as $data):?>
                          <option value="<?php echo $data['region']?>"<?php if ($regcard['region'] == $data['region']){echo ' selected';$sel=true;}?>><?php echo $data['region']?></option>
                        <?php endforeach;?>
                        <option value='NULL' <?php if($sel==false) echo ' selected';?>></option>
                      </select>
                      </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 col-sm-6">
                    <div class="form-group">
                      <label>Τάξη</label>
                      <select disabled id="classes" class="form-control" name="class_id">
                        <?php $sel=false;?>
                        <?php foreach ($class as $data):?>
                          <option value="<?php echo $data['id']?>"<?php if ($regcard['class_id'] == $data['id']){echo ' selected'; $sel=true;}?>><?php echo $data['class_name'];?></option>
                        <?php endforeach;?>
                        <option value="" <?php if($sel==false) echo 'selected';?>></option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6 col-sm-6">
                    <div class="form-group">
                      <label>Κατεύθυνση</label>
                      <select disabled id="courses" class="form-control" name="course_id">
                        <?php if ($course):?>
                          <?php $sel=false;?>
                          <?php foreach ($course as $data):?>
                            <option value="<?php echo $data['id']?>"<?php if ($regcard['course_id'] == $data['id']){echo ' selected'; $sel=true;}?>><?php echo $data['course'];?></option>
                          <?php endforeach;?>
                          <!-- <option value="none" <?php if($sel==false) echo 'selected';?>></option> -->
                        <?php endif;?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                  <!-- <div class="form-group"> -->
                    <label>Παρατηρήσεις</label>
                    <textarea disabled class="form-control" rows="3" name="notes">
                      <?php echo $regcard['notes'];?>
                    </textarea>  
                  <!-- </div> -->
                </div>
                </div>
            </div> <!-- end of panel-body -->
          </div> <!-- end of panel -->
        </div> <!-- end of left side -->

          <div class="col-md-6"> <!-- first row right side -->
            <div class="row"> <!-- right side embeded first row -->
              <div class="col-md-12">
                <div class="panel panel-default"  id="group2">
                  <div class="panel-heading">
                      <span class="icon">
                        <i class="icon-tag"></i>
                      </span>
                      <h3 class="panel-title">Στοιχεία μαθητολογίου</h3>
                      <div class="buttons">
                        <button enabled id="editform2" type="button" class="btn btn-default btn-sm pull-right" data-toggle="button"><i class="icon-edit"></i></button>
                    </div>
                  </div>
                <div class="panel-body">  
                  <div class="row">
                      <div class="col-md-6 col-sm-6">
                        <div class="form-group">
                          <label>Αρ. Μαθητολογίου</label>
                          <div >
                            <input disabled type="text" class="form-control" placeholder="" name="std_book_no" value="<?php echo $regcard['std_book_no'];?>">
                          </div>
                       </div>
                     </div>
                    <div class="col-md-6 col-sm-6">
                      <div class="form-group datecontainer">
                        <label>Ημ/νία εγγραφής</label>  
                        <input disabled type="text" class="form-control" placeholder="" name="reg_dt" value="<?php echo implode('-', array_reverse(explode('-', $regcard['reg_dt'])));?>" >
                      </div>
                    </div>
                  </div>                  
                    <div class="row">
                      <div class="col-md-6 col-sm-6">
                        <div class="form-group datecontainer">
                          <label>Ημ/νία έναρξης</label>
                          <input disabled  type="text"  class="form-control"  placeholder="" name="start_lessons_dt" value="<?php echo implode('-', array_reverse(explode('-', $regcard['start_lessons_dt'])));?>">
                        </div>
                      </div>
                      <div class="col-md-6 col-sm-6">
                        <div class="form-group datecontainer">
                          <label>Ημ/νία διαγραφής</label>
                          <input disabled type="text"  class="form-control"  placeholder="" name="del_lessons_dt" value="<?php echo implode('-', array_reverse(explode('-', $regcard['del_lessons_dt'])));?>">
                        </div>
                      </div>
                    </div>
                </div> <!-- end of panel-body -->
              </div> <!-- end of panel -->
            </div>
          </div> <!-- end of right side embeded first row -->

          <div class="row"><!-- right side embeded second row -->
            <div class="col-md-12">
              <div class="panel panel-default"  id="group3">
                <div class="panel-heading">
                    <span class="icon">
                      <i class="icon-money"></i>
                    </span>
                    <h3 class="panel-title">Στοιχεία οικονομικών</h3>
                    <div class="buttons">
                      <button enabled id="editform3" type="button" class="btn btn-default btn-sm pull-right" data-toggle="button"><i class="icon-edit"></i></button>
                    </div>
                  </div>
                <div class="panel-body">  
                  <div class="row">
                    <div class="col-md-4 col-sm-4">
                      <div class="form-group">
                        <label>Ποσό</label>
                        <div class="input-group">
                          <span class="input-group-addon">€</span>
                          <input disabled type="text" class="form-control" placeholder="" name="month_price" value="<?php echo $regcard['month_price'];?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-8 col-sm-8">
                      <div class="form-group">
                        <label>Παραλήπτης ΑΠΥ</label>
                        <input disabled id="apy_receiver" type="text" class="form-control" placeholder="" name="apy_receiver" value="<?php echo $regcard['apy_receiver'];?>">
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-4 col-sm-4">
                      <div class="form-group">
                        <label>Α.Φ.Μ.</label>
                        <input disabled type="text" class="form-control" placeholder="" name="afm" value="<?php echo $regcard['afm'];?>">
                      </div>
                    </div>
                    <div class="col-md-8 col-sm-8">
                      <div class="form-group">
                        <label>Κάτοχος Α.Φ.Μ.</label>
                        <input disabled id="afm_owner" type="text" class="form-control" name="afm_owner" value="<?php echo $regcard['afm_owner'];?>">
                      </div>
                    </div>
                  </div>
                </div> <!-- end  of panel-body -->
              </div> <!-- end of panel -->
            </div>
          </div> <!-- end of right side embeded second row -->


        </div> <!-- end of right side -->
      </div> <!-- end of first row -->

      <div class="btn-group">
        <button disabled id="submitbtn" type="submit" class="btn btn-primary">Αποθήκευση</button>
        <button disabled id="cancelbtn" type="button" class="btn btn-default">Ακύρωση</button>
      </div>
    </form>
  </div>
</div> <!-- /container -->

<div class="push"></div>
</div> <!-- end of body wrapper-->