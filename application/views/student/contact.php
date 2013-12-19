<script type="text/javascript">

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
});

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
                <li><a href="#admin">Διαχείριση</a></li>
                <li class="divider"></li>
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
              <h3 class="panel-title">Στοιχεία επικοινωνίας μαθητή</h3>
              <div class="buttons">
                  <button enabled id="editform1" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
              </div>
            </div>

            <div class="panel-body">
              <div class="form-group col-sm-6">
                <label>Τηλέφωνο σπιτιού</label>
                <input disabled type="text" class="form-control" placeholder="" name="home_tel" value="<?php echo $contact['home_tel'];?>"></input>
               </div>
              <div class="form-group col-sm-6">
                <label>Κινητό τηλέφωνο</label>
                <input disabled  type="text" class="form-control" placeholder="" name="std_mobile" value="<?php echo $contact['std_mobile'];?>"></input>
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
              <h3 class="panel-title">Στοιχεία επικοινωνίας γονέων</h3>
              <div class="buttons">
                  <button enabled id="editform2" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
              </div>
            </div>
            <div class="panel-body">
              <div class="form-group col-sm-6">
                <label>Κινητό πατέρα <?php if(!empty($secondary)) {if (!is_null($secondary['fathers_name'])) echo '('.$secondary['fathers_name'].')';}?></label>
                <input disabled  type="text" class="form-control" placeholder="" name="fathers_mobile" value="<?php echo $contact['fathers_mobile'];?>"></input>
              </div>
              <div class="form-group col-sm-6">
                <label>Κινητό μητέρας <?php if(!empty($secondary)) {if (!is_null($secondary['mothers_name'])) echo '('.$secondary['mothers_name'].')';}?></label>
                <input disabled  type="text" class="form-control" placeholder="" name="mothers_mobile" value="<?php echo $contact['mothers_mobile'];?>"></input>
              </div>
              <div class="form-group col-sm-6">
                <label>Τηλέφωνο εργασίας</label>
                <input disabled  type="text" class="form-control" placeholder="" name="work_tel" value="<?php echo $contact['work_tel'];?>"></input>
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