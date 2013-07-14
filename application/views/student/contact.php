<script type="text/javascript">

function toggleedit(togglecontrol, id) {
  //var toggle = document.getElementById("mainform"); //get the fieldset by its id

  if ($(togglecontrol).hasClass('active')){
    //toggle.disabled = true;
    $('#' + id).closest('.mainform').find('input:text').each(function(){
      $(this).attr('disabled', 'disabled');
      });

    var sel = (id == "editform1" ? "#editform2" : "#editform1");
    if ($(sel).hasClass('active') == false)
      {
        $('#submitbtn').attr('disabled', 'disabled');
        $('#cancelbtn').attr('disabled', 'disabled');      
      };
    }
  
  else {
    //toggle.disabled = false;
    $('#' + id).closest('.mainform').find('input:text').removeAttr('disabled');
    $('#submitbtn').removeAttr('disabled');
    $('#cancelbtn').removeAttr('disabled');
    };

}

$(document).ready(function(){
    $('#cancelbtn').click(function(){
      window.open("<?php echo base_url()?>student/cancel/contact/<?php echo $student['id']?>", '_self', false);
    });

    $("body").on('click', '#editform1, #editform2', function(){
      toggleedit(this, this.id);
    });

    //we must enable all form fields to submit the form with no errors!
    $("body").on('click', '#submitbtn', function(){
        $('.mainform').find('input:disabled').removeAttr('disabled');
        $('form').submit();
    });
});

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
              <li><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
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
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>">Καρτέλα μαθητή</a> <span class="divider">></span></li>
        <li class="active">Επικοινωνία</li>
      </ul>
        <!-- <a class="btn btn-mini" href="<?php echo base_url();?>"><i class="icon-arrow-left"></i> πίσω</a>         -->
      </div>
      
      

      <h3><?php echo $student['surname'].' '.$student['name']?></h3>
      

      <ul class="nav nav-tabs">
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>">Στοιχεία</a></li>
        <li class="active"><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/contact">Επικοινωνία</a></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance">Φοίτηση</a></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance">Οικονομικά</a></li>
      </ul>

      <div class="row-fluid">
        <form action="<?php echo base_url()?>student/card/<?php echo $student['id']?>/contact" method="post" accept-charset="utf-8">
 
        <div class="span6"> <!--Στοιχεία επικοινωνίας μαθητή-->
          <div class="mainform">  
          <div class="contentbox">
            <div class="title">
              <span class="icon">
                <i class="icon-tag"></i>
              </span>
              <h5>Στοιχεία επικοινωνίας μαθητή</h5>
              <div class="buttons">
                  <button enabled id="editform1" type="button" class="btn btn-mini" data-toggle="button"><i class="icon-edit"></i></button>
              </div>
            </div>

            <div class="content">
            <label>Τηλέφωνο σπιτιού</label>
            <input disabled type="text" class="span6" placeholder="" name="home_tel" value="<?php echo $contact['home_tel'];?>"></input>
            <label>Κινητό τηλέφωνο</label>
            <input disabled  type="text" class="span6" placeholder="" name="std_mobile" value="<?php echo $contact['std_mobile'];?>"></input>
          </div>
        </div>
        </div>
      </div> <!--end of mainform-->

        <div class="span6"> <!--Στοιχεία επικοινωνίας γονιών-->
                <div class="mainform">
          <div class="contentbox">
            <div class="title">
              <span class="icon">
                <i class="icon-tag"></i>
              </span>
              <h5>Στοιχεία επικοινωνίας γονέων</h5>
              <div class="buttons">
                  <button enabled id="editform2" type="button" class="btn btn-mini" data-toggle="button"><i class="icon-edit"></i></button>
              </div>
            </div>
            <div class="content">
              <label>Κινητό πατέρα <?php if(!empty($secondary)) {if (!is_null($secondary['fathers_name'])) echo '('.$secondary['fathers_name'].')';}?></label>
              <input disabled  type="text" class="span6" placeholder="" name="fathers_mobile" value="<?php echo $contact['fathers_mobile'];?>"></input>
              <label>Κινητό μητέρας <?php if(!empty($secondary)) {if (!is_null($secondary['mothers_name'])) echo '('.$secondary['mothers_name'].')';}?></label>
              <input disabled  type="text" class="span6" placeholder="" name="mothers_mobile" value="<?php echo $contact['mothers_mobile'];?>"></input>
              <label>Τηλέφωνο εργασίας</label>
              <input disabled  type="text" class="span6" placeholder="" name="work_tel" value="<?php echo $contact['work_tel'];?>"></input>
            </div>
          </div>
        </div>
        </div> <!--end of mainform-->
      </div>
      <div class="form-actions">
        <button disabled id="submitbtn" type="button" class="btn btn-primary">Αποθήκευση</button>
        <button disabled id="cancelbtn" type="button" class="btn btn">Ακύρωση</button>
      </div>
    </form>
    </div>
  </div> <!--end of main container-->
<div class="push"></div>
</div> <!-- end of body wrapper-->