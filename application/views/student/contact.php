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

function handleEllipsis() {
  $('.ellipsis').each(function() {
    const $this = $(this);

    // Check if the content is overflowing
    if (this.scrollWidth > this.clientWidth) {
      $this.tooltip(); // Initialize tooltip if ellipsis is applied
    } else {
      $this.tooltip('destroy'); // Destroy the tooltip if no overflow
      $this.removeAttr('title'); // Remove title to prevent native tooltips
    }
  });
}

$(window).on('resize', handleEllipsis);

$(document).ready(function(){
    // tooltip for mothers name if needed
    handleEllipsis();
  
    //Menu current active links and Title
    $('#menu-operation').addClass('active');
    $('#menu-student').addClass('active');
    $('#menu-header-title').text('Καρτέλα Μαθητή');

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

    // $('li.dash').click(function(){
    //   $('#footerModal').modal();
    // });

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
 
    <!-- Menu start -->
    <!-- dirname(__DIR__) gives the path one level up by default -->
    <?php include(dirname(__DIR__).'/include/menu.php');?> 
    <!-- Menu end -->

<!-- main container
================================================== -->

  <div class="container"  style="margin-bottom:60px;">
      
      <div>
        <ul class="breadcrumb">
          <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a></li>
          <li><a href="<?php echo base_url()?>student">Μαθητολόγιο</a></li>
          <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>">Καρτέλα μαθητή</a></li>
          <li class="active">Επικοινωνία</li>
          <!-- <li class="dash"><i class="icon-dashboard icon-small"></i></li> -->
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

      <?php if($student['active']==0):?>
        <div class="alert alert-danger" role="alert" style='margin-top:10px; margin-left:0px; margin-right:0px;'>
          <i class="icon-warning-sign"> </i><strong> ΠΡΟΣΟΧΗ! Τα δεδομένα αφορούν στη διαχειριστική περίοδο <u><?php echo($student['termname']);?></u> και όχι στην επιλεγμένη!</strong>
        </div>
      <?php endif;?>
            
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
                      <button type="button" class="phonecall btn btn-default" onclick="makephonecall(<?php if(!empty($contact)) {if (!is_null($contact['home_tel'])) echo $contact['home_tel'];}?>);"><span class="icon"><i class="icon-phone"></i></span></button>
                    </span>
                    <input disabled type="text" class="form-control" placeholder="" name="home_tel" value="<?php if(!empty($contact)) {if (!is_null($contact['home_tel'])) echo $contact['home_tel'];}?>">
               </div>
             </div>
             
             <div class="form-group col-sm-6">
                <label>Κινητό τηλέφωνο</label>
                <div class="input-group">
                  <span class="input-group-btn">
                     <button type="button" class="phonecall btn btn-default" onclick="makephonecall(<?php if(!empty($contact)) {if (!is_null($contact['std_mobile'])) echo $contact['std_mobile'];}?>);"><span class="icon"><i class="icon-mobile-phone"></i></span></button>
                  </span>
                  <input disabled  type="text" class="form-control" placeholder="" name="std_mobile" value="<?php if(!empty($contact)) {if (!is_null($contact['std_mobile'])) echo $contact['std_mobile'];}?>">
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
                <!-- <label>Κινητό πατέρα <?php if(!empty($secondary)) {if (!is_null($secondary['fathers_name'])) echo '('.$secondary['fathers_name'].')';}?></label> -->
                <label class="ellipsis" 
                      data-toggle="tooltip" 
                      data-placement="top" 
                      title="Κινητό πατέρα <?php if (!empty($secondary)) { 
                          if (!is_null($secondary['fathers_name'])) echo '('.$secondary['fathers_name'].')'; 
                      } ?>">
                    Κινητό πατέρα <?php if (!empty($secondary)) { 
                        if (!is_null($secondary['fathers_name'])) echo '('.$secondary['fathers_name'].')'; 
                    } ?>
                </label>                 
                  <div class="input-group">
                    <span class="input-group-btn">
                       <button type="button" class="phonecall btn btn-default" onclick="makephonecall(<?php if(!empty($contact)) {if (!is_null($contact['fathers_mobile'])) echo $contact['fathers_mobile'];}?>);"><span class="icon"><i class="icon-mobile-phone"></i></span></button>
                    </span>
                    <input disabled  type="text" class="form-control" placeholder="" name="fathers_mobile" value="<?php if(!empty($contact)) {if (!is_null($contact['fathers_mobile'])) echo $contact['fathers_mobile'];}?>">
                  </div>
              </div>
              <div class="form-group col-sm-6">
                <label class="ellipsis" 
                      data-toggle="tooltip" 
                      data-placement="top" 
                      title="Κινητό μητέρας <?php if (!empty($secondary)) { 
                          if (!is_null($secondary['mothers_name'])) echo '('.$secondary['mothers_name'].')'; 
                      } ?>">
                    Κινητό μητέρας <?php if (!empty($secondary)) { 
                        if (!is_null($secondary['mothers_name'])) echo '('.$secondary['mothers_name'].')'; 
                    } ?>
                </label>
                <div class="input-group">
                  <span class="input-group-btn">
                     <button type="button" class="phonecall btn btn-default" onclick="makephonecall(<?php if(!empty($contact)) {if (!is_null($contact['mothers_mobile'])) echo $contact['mothers_mobile'];}?>);"><span class="icon"><i class="icon-mobile-phone"></i></span></button>
                  </span>
                  <input disabled  type="text" class="form-control" placeholder="" name="mothers_mobile" value="<?php if(!empty($contact)) {if (!is_null($contact['mothers_mobile'])) echo $contact['mothers_mobile'];}?>">
                </div>
                </div>
              <div class="form-group col-sm-6">
                <label>Τηλέφωνο εργασίας</label>
                <div class="input-group">
                  <span class="input-group-btn">
                     <button type="button" class="phonecall btn btn-default" onclick="makephonecall(<?php if(!empty($contact)) {if (!is_null($contact['work_tel'])) echo $contact['work_tel'];}?>);"><span class="icon"><i class="icon-phone"></i></span></button>
                  </span>
                  <input disabled  type="text" class="form-control" placeholder="" name="work_tel" value="<?php if(!empty($contact)) {if (!is_null($contact['work_tel'])) echo $contact['work_tel'];}?>">
                </div>
              </div>
              <div class="form-group col-sm-6">
                <label>email επικοινωνίας</label>
                <div class="input-group">
                  <span class="input-group-btn">
                     <span class="btn btn-default disabled"><span class="icon"><i class="icon-envelope-alt"></i></span></span>
                  </span>
                  <input disabled  type="text" class="form-control" placeholder="" name="email" value="<?php if(!empty($contact)) {if (!is_null($contact['email'])) echo $contact['email'];}?>">
                </div>
              </div>              
            </div>
          </div>
        </div><!--end of mainform-->
        </div> 
      </div>
      <div class="btn-group">
        <button disabled id="submitbtn" type="button" class="btn btn-primary">Αποθήκευση</button>
        <button disabled id="cancelbtn" type="button" class="btn btn-default">Ακύρωση</button>
      </div>
    </form>

  </div> <!--end of main container-->
<div class="push"></div>
</div> <!-- end of body wrapper-->