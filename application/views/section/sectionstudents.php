<script type="text/javascript">
    

function toggleedit(togglecontrol, id) {

  if ($(togglecontrol).hasClass('active')){
  	$('#submitbtn').attr('disabled', 'disabled');
    $('#' + id).closest('.mainform').find(':input').each(function(){
      $(this).attr('disabled', 'disabled');
      });
    }
  else {
  	  $(".alert").fadeIn();
  	  $('#submitbtn').removeAttr('disabled');
      $('#' + id).closest('.mainform').find(':input').removeAttr('disabled');
    };

}


function hideFootableExpandButtons(){
      if($('.footable').hasClass('default'))
      {
        $('.buttons > a').addClass('hidden');
      }
      else
      {
        $('.buttons > a').removeClass('hidden');
      }
}


$(document).ready(function(){

	  $('.footable').footable();

   $(window).resize(function() {
          if(this.resizeTO) clearTimeout(this.resizeTO);
          this.resizeTO = setTimeout(function() {
              $(this).trigger('resizeEnd');
          }, 100);
      });


    $(window).on('resizeEnd', function() {
        //do something, window hasn't changed size in 100ms
        hideFootableExpandButtons();
    });

    $(window).on('load', function() {
         hideFootableExpandButtons();
    });


    $('.toggle').click(function() {
                $('.toggle').toggle();
                $('table').trigger($(this).data('trigger')).trigger('footable_redraw');
            });

    $("body").on('click', '#editform1, #editform2', function(){
      toggleedit(this, this.id);
      $(this).removeAttr('disabled');

    });

    $("body").on('click', '#submitbtn', function(){
        var selected_chkboxes = $('form').find(':input[type="checkbox"][name*="select"]:checked').length;
        if (selected_chkboxes > 0){
	        var msg="Πρόκειται να αφαιρέσετε τους επιλεγμένους μαθητές από το τμήμα. Παρακαλώ επιβεβαιώστε.";
	        var ans=confirm(msg);
	        if (ans==true){
	        	$('form').submit();
	    	};	
        }
        else{
        	alert("Δεν έχετε επιλέξει κανένα μαθητή για αφαίρεση από το τμήμα!");
        };
        
   	
    });

  $('#checkall').click(function(){
    var selected_chkboxes = $('form').find(':input[type="checkbox"][name*="select"]:checked');
    if(selected_chkboxes.length < $('form').find(':input[type="checkbox"]').length-1)
    {
      $('form').find(':input[type="checkbox"][name*="select"]').each(function(){
        $(this).prop('checked', true);
      });
    }
    else
      {
        $('form').find(':input[type="checkbox"][name*="select"]').each(function(){
          $(this).prop('checked', false);
        });
      };
  });

  $('form').find(':input[type="checkbox"][name*="select"]').click(function(){
      var selected_chkboxes = $('form').find(':input[type="checkbox"][name*="select"]:checked');
      if(selected_chkboxes.length == $('form').find(':input[type="checkbox"]').length-1)
      {
        $('#checkall').prop('checked', true);
      }
      else
      {
        $('#checkall').prop('checked', false); 
      };
  });

        $('#delsectionbtn').click(function(){
        var r=confirm("Το παρών τμήμα πρόκειται να διαγραφεί. Παρακαλώ επιβεβαιώστε.");
          if (r==true)
          {
              window.open ("<?php echo base_url('section/delreg/'.$section['id']);?>",'_self',false);  
          }
          return false;
        });
        
}) //end of (document).ready(function())

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
                <li><a href="<?php echo base_url()?>student">Μαθητολόγιο</a></li>
                <li><a href="<?php echo base_url()?>exam">Διαγωνίσματα</a></li>
                <li><a href="<?php echo base_url()?>files">Αρχεία</a></li>
                <li><a href="<?php echo base_url()?>cashdesk">Ταμείο</a></li>
                <li><a href="<?php echo base_url()?>announcements">Ανακοινώσεις</a></li>
              </ul>
            </li>
           <li class="dropdown">
              <a href="#" class="dropdown-toggle active" data-toggle="dropdown">Οργάνωση/Διαχείριση<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
                <li class="active"><a href="<?php echo base_url()?>section">Τμήματα</a></li>
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
                <li><a href="<?php echo base_url()?>section/logout">Αποσύνδεση</a></li>
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
    <h1>Καρτέλα Τμήματος</h1>
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
	        <li><a href="<?php echo base_url()?>section">Τμήματα</a> </li>
	        <li><a href="<?php echo base_url()?>section/card/<?php echo $section['id'];?>/">Καρτέλα Τμήματος</a> </li>
	        <li class="active">Μαθητές</li>
	      </ul>
      </div>
      
     <p> 
      <h3>
        <?php echo $section['section'].' / '.$section['title'];?>
      </h3>
    </p>
        

      <ul class="nav nav-tabs">
        <li><a href="<?php echo base_url()?>section/card/<?php echo $section['id']?>">Στοιχεία</a></li>
       	<li class="active"><a href="<?php echo base_url()?>section/card/<?php echo $section['id']?>/sectionstudents">Μαθητές</a></li>
      </ul>

      <p></p>


	<div class="row">

    	<div class="col-md-12">
 		
 		<div class="alert alert-warning" style="display:none;">
        	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        	<span style="font-family:'Play';font-weight:700;">Θυμηθείτε! </span> Η εισαγωγή μαθητών σε τμήματα γίνεται από την καρτέλα φοίτησης του εκάστοτε μαθητή, επιλέγοντας την ενότητα "Διαχείριση".
      	</div>

		<?php if(!empty($students)):?>
        <form action="<?php echo base_url()?>section/card/<?php echo $section['id']?>/sectionstudents/" method="post" accept-charset="utf-8" role="form">
        
      	<div class="row"> <!-- section data -->
          <div class="col-md-12" id="group1">
			 <div class="mainform">
                 <div class="panel panel-default">
       			     <div class="panel-heading">
              			<span class="icon">
                			<i class="icon-tag"></i>
              			</span>
              			<h3 class="panel-title">Μαθητές τμήματος</h3>
              			<div class="buttons">
                  			<button enabled id="editform1" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
		                    <a enabled data-trigger="footable_expand_all" class="toggle btn btn-default btn-sm" href="#expandall"><i class="icon-angle-down"></i></a>
                        <a enabled data-trigger="footable_collapse_all" style="display: none" class="toggle btn btn-default btn-sm" href="#collapseall"><i class="icon-angle-up"></i></a>
                    </div>
            		</div>
	            <div class="panel-body">
	            	<table class="table footable table-striped table-hover">
	            		<thead>
	            			<tr>
	            				<th><input disabled type="checkbox" class="checkbox" id="checkall"></th>
		            			<th data-toggle="true">Ονοματεπώνυμο</th>
		            			<th data-hide="phone">Σταθερό</th>
		            			<th>Κινητό</th>
		            			<th data-hide="phone,tablet">Μητρώνυμο</th>
		            			<th data-hide="phone">Κινητό μητέρας</th>
		            			<th data-hide="phone,tablet">Πατρώνυμο</th>
		            			<th data-hide="phone,tablet">Κινητό πατέρα</th>
		            			<th data-hide="phone,tablet">Τηλ. Εργασίας</th>
		            		</tr>
	            		</thead>
	            		<tbody>
	            			<?php foreach ($students as $student):?>
		            			<tr>
		            				<td><input disabled type="checkbox" name="select[<?php echo $student['id'];?>]"></td>
		            				<td><?php echo $student['surname'].' '.$student['name'];?></td>
		            				<td><?php echo $student['home_tel'];?></td>
		            				<td><?php echo $student['std_mobile'];?></td>
		            				<td><?php echo $student['mothers_name'];?></td>
		            				<td><?php echo $student['mothers_mobile'];?></td>
		            				<td><?php echo $student['fathers_name'];?></td>
		            				<td><?php echo $student['fathers_mobile'];?></td>
		            				<td><?php echo $student['work_tel'];?></td>
		            			</tr>
	            			<?php endforeach;?>
	            		</tbody>
	            	</table>
	           </div> <!-- end of panel body -->
		     </div> <!-- end of panel -->
	       </div>
	 	  </div>
		</div><!-- end of section data row   -->
  	 </div>
  	</div>

    <div class="row">
        <div class="col-md-12">
			     <button disabled id="submitbtn" type="button" class="btn btn-primary">Αφαίρεση από τμήμα</button>
            <div class="btn-group pull-right">
              <a id="delsectionbtn" href="#" class="btn btn-default" ><i class="icon-trash"></i></a>
              <a id="newsectionbtn" href="<?php echo base_url();?>section/newreg" class="btn btn-default"><i class="icon-plus"></i></a>
            </div>
        </div>
    </div> 
  </form>

<div class="row">
  <div class="col-md-12">   
  <ul class="pager">
      <li class="previous <?php if(empty($prevnext['prev'])){echo 'disabled';};?>"  <?php if(empty($prevnext['prev'])){echo "onclick='return false;'";};?>><a href="<?php echo base_url('/section/card/'.$prevnext['prev']);?>"><i class="icon-chevron-left"></i> Προηγούμενο</a></li>
      <li class="next <?php if(empty($prevnext['next'])){echo 'disabled';};?>"  <?php if(empty($prevnext['next'])){echo "onclick='return false;'";};?> ><a href="<?php echo base_url('/section/card/'.$prevnext['next']);?>">Επόμενο <i class="icon-chevron-right"></i></a></li>
      </ul>
   </div>
</div>

<?php else:?>
		<div class="well">
			Δεν έχουν αντιστοιχιστεί μαθητές στο συγκεκριμένο τμήμα. Μπορείτε να εισάγετε τον εκάστοτε μαθητή σε τμήματα από την καρτέλα φοίτησής του, επιλέγοντας την ενότητα "Διαχείριση".
		</div>
	</div>
</div>
<?php endif;?>



</div><!--end of main container-->

<div class="push"></div>

</div> <!-- end of body wrapper-->