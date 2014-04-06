<!-- Using https://github.com/ivaynberg/select2 with https://github.com/t0m/select2-bootstrap-css -->
<link href="<?php echo base_url('assets/select2/select2.css')?>" rel="stylesheet">
<link href="<?php echo base_url('assets/select2/select2-bootstrap.css')?>" rel="stylesheet">
<script src="<?php echo base_url('assets/select2/select2.js')?>"></script>
<script src="<?php echo base_url('assets/select2/select2_locale_el.js')?>"></script>
<style type="text/css">
	.select2-result-label
  {
		font-family: "Play";
		font-weight: "600px";
	}
</style>

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
      $('#undobtn').attr('disabled', 'disabled');
    }
    else 
    {
    $('#' + id).closest('.mainform').find(':input').each(function(){
      $(this).removeAttr('disabled');
      });
      $(this).find('btn').removeAttr('disabled');
      $('#submitbtn').removeAttr('disabled');
      $('#cancelbtn').removeAttr('disabled');
      $(".alert").fadeIn();
      if(undoarr.length>0){
        $('#undobtn').removeAttr('disabled');
      }
      else
      {
        $('#undobtn').attr('disabled', 'disabled');
      }
    }
}


    //some characters needs to be escaped in order to recognize the id
    //http://learn.jquery.com/using-jquery-core/faq/how-do-i-select-an-element-by-an-id-that-has-characters-used-in-css-notation/
    function jq( myid ) {
    return "#" + myid.replace( /(:|\.|\[|\])/g, "\\$1" );
    }
    

    $(document).ready(function(){

      $(".select2inputs").select2();
	    
	    $('.mainform').find(':input').each(function(){
	      $(this).attr('disabled', 'disabled');
	      });
  		$('#editform1').removeAttr('disabled');


      $('#cancelbtn').click(function(){
        window.open("<?php echo base_url('curriculum/cancel/tutorsperlesson');?>", '_self', false);
      });

      $("body").on('click', '#editform1, #editform2', function(){
        toggleedit(this, this.id);
        $(this).removeAttr('disabled');

      });
        
    }) //end of (document).ready(function())

      $(document).on('click', '#btnaddrow', function(){
        newrowc++;
        var newrow=$('#template_row').clone();
        newrow.attr('id', 'newrow'+newrowc);
        var wheretoinsert = $(this).parent().parent().parent();
        var selectfield  = newrow.find('select');
        selectfield.attr('id', 'employeeids['+(-newrowc)+']');
        selectfield.attr('name', 'employees['+(-newrowc)+'][]');
        var inputfield  = newrow.find('input');
        inputfield.attr('id', 'lessonid['+(-newrowc)+']');
        inputfield.attr('name', 'lesson['+(-newrowc)+']');        
        newrow.removeClass('hidden');
        newrow.insertBefore(wheretoinsert);
        undoarr.push('newrow'+newrowc);
        $('#undobtn').removeAttr('disabled');
        $(jq('employeeids['+(-newrowc)+']')).select2();
      });

    $(document).on('click', '#undobtn', function(){
      var id = undoarr.pop();
      $('#'+id).remove();
      if(undoarr.length==0)
        {
          $('#undobtn').attr('disabled','disabled');
        }
    });


    $(document).on('click', '.dellessonbtn', function(){
      var thisrow = $(this).parents('.row');
      var thisrowid = thisrow.attr('id');

      //remove rowid from undoarr
      var a = $.inArray(thisrowid,undoarr);
      if(a!==-1){
        undoarr.splice(a,1);
      }
      if(undoarr.length==0)
      {
        $('#undobtn').attr('disabled','disabled');
      }

      if (thisrowid.substring(0,6)=='oldrow')
      {
          var r=confirm('Πρόκειται να διαγράψετε ένα μάθημα. Συνίσταται να μην το κάνετε αν το έχετε αντιστοιχίσει σε έστω και 1 κατεύθυνση. Το μαθημα θα αφαιρεθεί από τις κατευθύνσεις, τους διδάσκωντες και τους μαθητές στους οποίους έχει αντιστοιχιστεί. Η ενέργεια αυτή δεν αναιρείται. Παρακαλώ επιβεβαιώστε.')
          if (r==true)
          {
            catlessonid = thisrowid.substring(6);//removes the first 6 chars
            post_url = '<?php echo base_url("curriculum/delcataloglesson");?>'; 
            $.ajax({
              global: false,
              type: "post",
              url: post_url,
              data : {'jscatlessonid':catlessonid},
              dataType:'json', 
              success: function(){
                $('#'+thisrowid).remove();
              }
            }); //end of ajax
          }
      }
      else
      {
        $('#'+thisrowid).remove();
      }
    });


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
                <li><a href="<?php echo base_url('curriculum/edit')?>">Πρόγραμμα Σπουδών</a></li>
                <li class="active"><a href="<?php echo base_url('curriculum/edit/tutorsperlesson')?>">Μαθήματα-Διδάσκωντες</a></li>
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
	        <li><a href="<?php echo base_url('curriculum/edit')?>">Πρόγραμμα Σπουδών</a></li>
	        <li class="active">Διδάσκωντες</li>
	      </ul>
      </div>

     <p><h3>Επεξεργασία προγράμματος σπουδών</h3></p>
      
      <ul class="nav nav-tabs" style="margin-bottom:15px;">
        <li><a href="<?php echo base_url('/curriculum/edit')?>">Πρόγραμμα Σπουδών</a></li>
        <li class="active"><a href="<?php echo base_url('/curriculum/edit/tutorsperlesson')?>">Μαθήματα & Διδάσκωντες</a></li>
      </ul>


	<div class="row">
   <div class="col-md-12">
    <div class="alert alert-danger" style="display:none;">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <span style="font-family:'Play';font-weight:700;">ΠΡΟΣΟΧΗ! </span> Αποφεύγετε τις διαγραφές μαθημάτων ή/και διδασκώντων μιας και θα επηρρεάσουν άμεσα τα ήδη καταχωρημένα δεδομένα μαθητών και τμημάτων που έχουν αντιστοιχιστεί στα μαθήματα αυτά.
    </div>
     <form action="<?php echo base_url('/curriculum/edit/tutorsperlesson')?>" method="post" accept-charset="utf-8" role="form">
     	<div class="row"> 
        <div class="col-md-12" id="group1">
		    	 <div class="mainform">
              <div class="panel panel-default">
       	        <div class="panel-heading">
              		<span class="icon">
               			<i class="icon-paste "></i>
            			</span>
            			<h3 class="panel-title">Μαθήματα & Διδάσκωντες</h3>
              			<div class="buttons">
                			<button id="editform1" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
              			</div>
          		  </div>
	              <div class="panel-body">
                  <!--start of template row-->
                  <div id="template_row" class="row hidden">
                    <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3">
                      <div class="form-group">
                        <label>Μάθημα</label>
                        <div class="input-group">
                          <span class="input-group-btn">
                            <button class="btn btn-default dellessonbtn" type="button"><i class="icon-trash"></i></button>
                          </span>
                          <input type="text" class="form-control" value="" placeholder="Πληκτρ/στε ένα τίτλο...">
                        </div>
                      </div>
                    </div>
                    <div class="col-xs-6 col-sm-8 col-md-9 col-lg-9">
                      <div class="form-group">
                        <label>Διδάσκωντες</label>
                        <select multiple placeholder="Επιλέξτε διδάσκωντες..." class="form-control">
                          <!-- <optgroup label="Ενεργοί"> -->
                          <?php foreach ($alltutors['active'] as $data):?>
                            <option value="<?php echo $data['id'];?>"><?php echo $data['text'];?></option>
                          <?php endforeach;?>
                          <!-- </optgroup> -->
                          <!--When a new lesson with tutors is inserted we don't need to show the inactive students!-->
                          <!-- <optgroup label="Ανενεργοί">
                          <?php foreach ($alltutors['inactive'] as $data):?>
                            <option value="<?php echo $data['id'];?>"><?php echo $data['text'];?></option>
                          <?php endforeach;?>
                          </optgroup> -->
                        </select>
                      </div>
                    </div>
                  </div>                  
                  <!--end of template row-->
                  
                  <!-- start of recurring rows -->
                  <?php if(!empty($lesson)):?>
                  <?php foreach ($lesson as $lessonkey => $title):?>
                  <div class="row" id="<?php echo 'oldrow'.$lessonkey;?>">
                  	<div class="col-xs-6 col-sm-4 col-md-3 col-lg-3">
                        <div class="form-group">
                          <label>Μάθημα</label>
                          <div class="input-group">
<!--                             <div class="input-group-btn">
                              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
                              <ul class="dropdown-menu">
                                <li><a class="addlessonbtn" href="#" onclick="return false;"><i class="icon-plus"> </i>Προσθήκη Νέου</a></li>
                                <li><a class="dellessonbtn" href="#" onclick="return false;"><i class="icon-trash"> </i>Διαγραφή</a></li>
                              </ul>
                            </div> -->
                            <span class="input-group-btn">
                              <button class="btn btn-default dellessonbtn" type="button"><i class="icon-trash"></i></button>
                            </span>
                            <input id="lessonid[<?php echo $lessonkey;?>]" name="lesson[<?php echo $lessonkey;?>]" type="text" class="form-control" value="<?php echo $title;?>" placeholder="Πληκτρ/στε ένα τίτλο...">
                          </div>
                        </div>
                  	</div>
                  	<div class="col-xs-6 col-sm-8 col-md-9 col-lg-9">
                  		<div class="form-group">
                  			<label>Διδάσκωντες</label>
                  			<select multiple placeholder="Επιλέξτε διδάσκωντες..." class="form-control select2inputs" name="employees[<?php echo $lessonkey;?>][]" id="employeeids[<?php echo $lessonkey;?>]">
                  				<optgroup label="Ενεργοί">
                  				<?php foreach ($alltutors['active'] as $data):?>
                  					<option value="<?php echo $data['id'];?>" 
                                <?php if(!empty($tutor['active'][$lessonkey])){
                                if(in_array($data['id'],$tutor['active'][$lessonkey]))
                                {echo 'selected';}}?>><?php echo $data['text'];?></option>
                  				<?php endforeach;?>
                  				</optgroup>
                  				<?php if(!empty($tutor['inactive'])):?>
	                  				<optgroup label="Ανενεργοί">
	                  				<?php foreach ($alltutors['inactive'] as $data):?>
	                  					<option value="<?php echo $data['id'];?>" 
		                  					<?php 
		                  						if(!empty($tutor['inactive'][$lessonkey]))
		                  						{
			                  						if(in_array($data['id'],$tutor['inactive'][$lessonkey]))
		                  							{
		                  								echo 'selected';
	                  								}
		                  						}
	                  						?>>
	                  						<?php echo $data['text'];?>
	                  					</option>
	                  				<?php endforeach;?>
	                  				</optgroup>
                  				<?php endif;?>
                  			</select>
                  		</div>
                  	</div>
                  </div>
                  <?php endforeach;?>
                  <?php endif;?>
                  <!-- end of recurring rows -->
                  <div class="row">
                    <div class="col-xs-12">
                      <div class="btn-group pull-right">                    
                        <button disabled id="btnaddrow" class="btn btn-primary" type="button">Προσθήκη</button>
                        <button disabled id="undobtn" type="button" class="btn btn-primary pull-right"><i class="icon-undo"></i></button>  
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
         	<button disabled id="cancelbtn" type="button" class="btn btn-default">Ακύρωση</button>
          <button disabled id="submitbtn" type="submit" class="btn btn-primary pull-right">Αποθήκευση</button>
        </div>
      </div>

      </form>
    </div>
  </div>

  <div class="push"></div>

</div> <!-- end of body wrapper-->