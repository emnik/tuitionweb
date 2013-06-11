<script type="text/javascript">

function toggleedit(togglecontrol) {
  var toggle = document.getElementById("mainform"); //get the fieldset by its id

  if ($(togglecontrol).hasClass('active')){
    toggle.disabled = true;
    $('#mainform :input').each(function(){
      $(this).attr('disabled', 'disabled');
      });
    $('#submitbtn').attr('disabled', 'disabled');
    $('#cancelbtn').attr('disabled', 'disabled');
    }
  
  else {
    toggle.disabled = false;
    $('#mainform :input').removeAttr('disabled');
    $('#submitbtn').removeAttr('disabled');
    $('#cancelbtn').removeAttr('disabled');
    };
}

$(document).ready(function(){

    $('#cancelbtn').click(function(){
      window.open("<?php echo base_url()?>student/cancel/card/<?php echo $student['id']?>", '_self', false);
    });

    //if it is a new registration the fields should be enabled
    <?php if(empty($student['surname'])):?>   
        $('#editform').addClass('active');
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
      content:"<div style=\"text-align:center;\"><button type=\"button\" class=\"btn btn-small\" onclick=\"paste_name('apy_receiver','name');\">Μαθητή</button><button type=\"button\" class=\"btn btn-small\" onclick=\"paste_name('apy_receiver','fathersname');\">Πατέρα</button><button type=\"button\" class=\"btn btn-small\" onclick=\"paste_name('apy_receiver','');\"><i class=\"icon-remove-circle\"></i></button></div>"
  });


    $('#afm_owner').popover({
      placement:"top",
      container:'body',
      trigger:"click",
      html:true,
      title:"<span style=\"text-align:center;font-weight:bold;font-size:95%;color:grey;\">Επικόληση ονόματος:</span>",
      content:"<div style=\"text-align:center;\"><button type=\"button\" class=\"btn btn-small\" onclick=\"paste_name('afm_owner','name');\">Μαθητή</button><button type=\"button\" class=\"btn btn-small\" onclick=\"paste_name('afm_owner','fathersname');\">Πατέρα</button><button type=\"button\" class=\"btn btn-small\" onclick=\"paste_name('afm_owner','');\"><i class=\"icon-remove-circle\"></i></button></div>"
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
            <li class="active"><a href="<?php echo base_url()?>registrations">Μαθητολόγιο</a></li>
              <li><a href="#employees">Προσωπικό</a></li>
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
    <h1>Καρτέλα Μαθητή</h1>
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
          <li><a href="<?php echo base_url()?>registrations">Μαθητολόγιο</a> <span class="divider">></span></li>
          <li class="active">Καρτέλα μαθητή</li>
        </ul>
          <!-- <a class="btn btn-mini" href="<?php echo base_url();?>"><i class="icon-arrow-left"></i> πίσω</a>         -->
        </div>
        
        

        <h3>
          <?php
          if(!empty($student['surname'])){
            echo $student['surname'].' '.$student['name'];
          }
          else {
            echo "Νέα εγγραφή";
          };?>
        </h3>
        

        <ul class="nav nav-tabs">
          <li class="active">
          <a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>">Στοιχεία</a>
          </li>
          <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/contact">Επικοινωνία</a></li>
          <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance">Φοίτηση</a></li>
          <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance">Οικονομικά</a></li>
        </ul>

        <div class="row-fluid">

          <div class="span12">
      
          <!--Body content-->
          <form action="<?php echo base_url()?>student/card/<?php echo $student['id']?>" method="post" accept-charset="utf-8">

            <div class="contentbox">
              <div class="title">
                  <span class="icon">
                    <i class="icon-tag"></i>
                  </span>
                  <h5>Βασικά στοιχεία μαθητολογίου</h5>
                  <div class="buttons">
                    <button enabled id="editform" type="button" class="btn btn-mini pull-right" data-toggle="button" onclick="toggleedit(this)"><i class="icon-edit"></i></button>
                </div>
              </div>
            <div class="content">  
          <div class="row-fluid">
            <fieldset id="mainform"> <!-- disabled for enabling/disabling all form controls in the fieldset-->
            <div class="span7"> <!--first column-->
              <label>Κωδ.μαθητή(id)</label><input disabled class="span2" type="text" placeholder="" name="id" value="<?php echo $regcard['id'];?>">
              
              <div class="row-fluid">
                 <div class="span6">
                  <label>Όνομα</label><input disabled class="span12" id="name" type="text" placeholder="" name="name" value="<?php echo $regcard['name'];?>">
                 </div>
                 <div class="span6">
                   <label>Επίθετο</label><input disabled class="span12" id="surname" type="text" placeholder="" name="surname" value="<?php echo $regcard['surname'];?>">
                 </div>
              </div>  
                <div class="row-fluid">
                  <div class="span6">
                    <label>Πατρώνυμο</label><input disabled class="span12" id="fathersname" type="text" placeholder="" name="fathers_name" value="<?php echo $regcard['fathers_name'];?>">
                  </div>
                  <div class="span6">
                    <label>Μητρώνυμο</label><input disabled class="span12" type="text" placeholder="" name="mothers_name" value="<?php echo $regcard['mothers_name'];?>">
                  </div>
                </div>

                <div class="row-fluid">
                  <div class="span6">
                    <label>Διεύθυνση</label><input disabled class="span12" type="text" placeholder="" name="address" value="<?php echo $regcard['address'];?>">
                  </div>
                  <div class="span6">
                    <label>Πόλη</label>
                    <select disabled class="span12" name="region">
                      <?php $sel=false;?>
                      <?php foreach ($region as $data):?>
                        <option value="<?php echo $data['region']?>"<?php if ($regcard['region'] == $data['region']){echo ' selected';$sel=true;}?>><?php echo $data['region']?></option>
                      <?php endforeach;?>
                      <option value='NULL' <?php if($sel==false) echo ' selected';?>></option>
                    </select>
                  </div>
                </div>

                <div class="row-fluid">
                  <div class="span6">
                    <label>Τάξη</label>
                    <select disabled id="classes" class="span12" name="class_id">
                      <?php $sel=false;?>
                      <?php foreach ($class as $data):?>
                        <option value="<?php echo $data['id']?>"<?php if ($regcard['class_id'] == $data['id']){echo ' selected'; $sel=true;}?>><?php echo $data['class_name'];?></option>
                      <?php endforeach;?>
                      <option value="" <?php if($sel==false) echo 'selected';?>></option>
                    </select>
                  </div>
                  <div class="span6">
                    <label>Κατεύθυνση</label>
                    <select disabled id="courses" class="span12" name="course_id">
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
                <label>Παρατηρήσεις</label>
                <textarea disabled class="span12" rows="3"></textarea>  

              </div> <!--end of first column group-->

              <!--second column-->
              <div class="span5">
                
                <label>Ημ/νία εγγραφής</label>
                <input disabled type="text" class="span5" placeholder="" name="reg_dt" value="<?php echo implode('-', array_reverse(explode('-', $regcard['reg_dt'])));?>" ></input>

                <div class="row-fluid">
                  <div class="span6">
                    <label>Ημ/νία έναρξης</label>
                    <input disabled  type="text"  class="span10"  placeholder="" name="start_lessons_dt" value="<?php echo implode('-', array_reverse(explode('-', $regcard['start_lessons_dt'])));?>"></input>
                  </div>
                  <div class="span6">
                    <label>Ημ/νία διαγραφής</label>
                    <input disabled type="text"  class="span10"  placeholder="" name="del_lessons_dt" value="<?php echo implode('-', array_reverse(explode('-', $regcard['del_lessons_dt'])));?>"></input>
                  </div>
                </div>

                <div class="row-fluid">
                  <div class="span12">
                    <label>Αρ. Μαθητολογίου</label>
                    <input disabled type="text" class="span3" placeholder="" name="std_book_no" value="<?php echo $regcard['std_book_no'];?>"></input>
                  </div>
                </div>

                <div class="row-fluid">
                  <div class="span12">
                    <label>Ποσό</label>
                    <div class="input-prepend">
                      <span class="add-on">€</span>
                       <input disabled type="text" class="span3" placeholder="" name="month_price" value="<?php echo $regcard['month_price'];?>"></input>
                    </div>
                  </div>
                </div>


                <div class="row-fluid" >
                  <div class="span12">
                    <label>Παραλήπτης ΑΠΥ</label>
                    <input disabled id="apy_receiver" type="text" class="span12" placeholder="" name="apy_receiver" value="<?php echo $regcard['apy_receiver'];?>"></input>
                  </div>
                </div>


                <div class="row-fluid">
                  <div class="span4">
                    <label>Α.Φ.Μ.</label>
                    <input disabled type="text" class="span12" placeholder="" name="afm" value="<?php echo $regcard['afm'];?>"></input>
                  </div>
                  <div class="span8">
                    <label>Κάτοχος Α.Φ.Μ.</label>
                    <input disabled id="afm_owner" type="text" class="span12" name="afm_owner" value="<?php echo $regcard['afm_owner'];?>"></input>
                  </div>
                </div>
            </fieldset>

              </div> <!--end of second column group-->


            </div>
          </div>


          <div class="form-actions">
            <button disabled id="submitbtn" type="submit" class="btn btn-primary">Αποθήκευση</button>
            <button disabled id="cancelbtn" type="button" class="btn">Ακύρωση</button>
          </div>
        </form>
      </div> <!--end of body-->
    </div>
    </div> <!--end of main fluid row container-->
   </div> <!-- /container -->

<div class="push"></div>
</div> <!-- end of body wrapper-->