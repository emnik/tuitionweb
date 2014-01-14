<!-- https://github.com/hgoebl/mobile-detect.js -->
<script src="<?php echo base_url('assets/mobile-detect.js/mobile-detect.min.js')?>"></script>

<script type="text/javascript">
var md = new MobileDetect(window.navigator.userAgent);

function toggleedit(togglecontrol, id) {

  if ($(togglecontrol).hasClass('active')){
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

    $('li.dash').click(function(){
      $('#footerModal').modal();
    });

    //if not on phone the makecall buttons become just decorative!
    if(md.phone()==null){
        $('.phonecall').attr('disabled', 'disabled');
     }

     //if the phone input is empty the button should be disabled (decorative)
     $('.phonecall').each(function(){
         if($(this).parent().next('input').val()==""){
           $(this).attr('disabled', 'disabled');
        }
     })
});

function makephonecall(phonenum){
  if(md.phone()!=null && phonenum!=""){
    if(md.os()=='AndroidOS' || md.os()=='iOS'){
       window.location = 'tel:'+phonenum;
    }
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
            <li class="active"><a href="<?php echo base_url()?>student">Μαθητολόγιο</a></li>
            <li><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
            <li><a href="<?php echo base_url()?>section">Τμήματα</a></li>
            <li><a href="<?php echo base_url()?>finance">Οικονομικά</a></li>
            <li><a href="#reports">Αναφορές</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Χρήστης<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header"><?php echo $user->surname.' '.$user->name;?></li>
                <li><a href="#">Αλλαγή κωδικού</a></li>
                <li><a href="<?php echo base_url()?>student/logout">Αποσύνδεση</a></li>
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
          <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>">Καρτέλα μαθητή</a></li>
          <li class="active">Επικοινωνία</li>
          <li class="dash"><i class="icon-dashboard icon-small"></i></li>
        </ul>
      </div>
      
      
      <p>
        <h3><?php echo $student['surname'].' '.$student['name']?></h3>
      </p>

      <ul class="nav nav-tabs">
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>">Στοιχεία</a></li>
        <li class="active"><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/contact">Επικοινωνία</a></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance">Φοίτηση</a></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance">Οικονομικά</a></li>
      </ul>

      <p></p>

      <div class="row">

        <form action="<?php echo base_url()?>student/card/<?php echo $student['id']?>/contact" method="post" accept-charset="utf-8">
 
        <div class="col-md-6"> <!--Στοιχεία επικοινωνίας μαθητή-->
          <div class="mainform">  
          <div class="panel panel-default">
            <div class="panel-heading">
              <span class="icon">
                <i class="icon-tag"></i>
              </span>
              <h3 class="panel-title">Στοιχεία μαθητή</h3>
              <div class="buttons">
                  <button enabled id="editform1" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
              </div>
            </div>

            <div class="panel-body">
              <div class="form-group col-sm-6">
                <label>Τηλέφωνο σπιτιού</label>
                <div class="input-group">
                    <span class="input-group-btn">
                      <button type="button" class="phonecall btn btn-default" onclick="makephonecall(<?php echo $contact['home_tel'];?>);"><span class="icon"><i class="icon-phone"></i></span></button>
                    </span>
                    <input disabled type="text" class="form-control" placeholder="" name="home_tel" value="<?php echo $contact['home_tel'];?>">
               </div>
             </div>
             
             <div class="form-group col-sm-6">
                <label>Κινητό τηλέφωνο</label>
                <div class="input-group">
                  <span class="input-group-btn">
                     <button type="button" class="phonecall btn btn-default" onclick="makephonecall(<?php echo $contact['std_mobile'];?>);"><span class="icon"><i class="icon-mobile-phone"></i></span></button>
                  </span>
                  <input disabled  type="text" class="form-control" placeholder="" name="std_mobile" value="<?php echo $contact['std_mobile'];?>">
                </div>
              </div>
          </div>
        </div>
        </div><!--end of mainform-->
      </div> 

        <div class="col-md-6"> <!--Στοιχεία επικοινωνίας γονιών-->
                <div class="mainform">
          <div class="panel panel-default">
            <div class="panel-heading">
              <span class="icon">
                <i class="icon-tag"></i>
              </span>
              <h3 class="panel-title">Στοιχεία γονέων</h3>
              <div class="buttons">
                  <button enabled id="editform2" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
              </div>
            </div>
            <div class="panel-body">
              <div class="form-group col-sm-6">
                <label>Κινητό πατέρα <?php if(!empty($secondary)) {if (!is_null($secondary['fathers_name'])) echo '('.$secondary['fathers_name'].')';}?></label>
                  <div class="input-group">
                    <span class="input-group-btn">
                       <button type="button" class="phonecall btn btn-default" onclick="makephonecall(<?php echo $contact['fathers_mobile'];?>);"><span class="icon"><i class="icon-mobile-phone"></i></span></button>
                    </span>
                    <input disabled  type="text" class="form-control" placeholder="" name="fathers_mobile" value="<?php echo $contact['fathers_mobile'];?>">
                  </div>
              </div>
              <div class="form-group col-sm-6">
                <label>Κινητό μητέρας <?php if(!empty($secondary)) {if (!is_null($secondary['mothers_name'])) echo '('.$secondary['mothers_name'].')';}?></label>
                <div class="input-group">
                  <span class="input-group-btn">
                     <button type="button" class="phonecall btn btn-default" onclick="makephonecall(<?php echo $contact['mothers_mobile'];?>);"><span class="icon"><i class="icon-mobile-phone"></i></span></button>
                  </span>
                  <input disabled  type="text" class="form-control" placeholder="" name="mothers_mobile" value="<?php echo $contact['mothers_mobile'];?>">
                </div>
                </div>
              <div class="form-group col-sm-6">
                <label>Τηλέφωνο εργασίας</label>
                <div class="input-group">
                  <span class="input-group-btn">
                     <button type="button" class="phonecall btn btn-default" onclick="makephonecall(<?php echo $contact['work_tel'];?>);"><span class="icon"><i class="icon-phone"></i></span></button>
                  </span>
                  <input disabled  type="text" class="form-control" placeholder="" name="work_tel" value="<?php echo $contact['work_tel'];?>">
                </div>
              </div>
            </div>
          </div>
        </div><!--end of mainform-->
        </div> 
      </div>
      <div>
        <button disabled id="submitbtn" type="button" class="btn btn-primary">Αποθήκευση</button>
        <button disabled id="cancelbtn" type="button" class="btn btn-default">Ακύρωση</button>
      </div>
    </form>

  </div> <!--end of main container-->
<div class="push"></div>
</div> <!-- end of body wrapper-->