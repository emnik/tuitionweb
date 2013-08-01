<script type="text/javascript">

$(document).ready(function(){
   
  $('.footable').footable();
})
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
              <li><a href="<?php echo base_url()?>student">Μαθητολόγιο</a></li>
              <li class="active"><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
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
    <h1>Καρτέλα Εργαζομένου</h1>
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
        <li><a href="<?php echo base_url()?>staff">Προσωπικό</a> <span class="divider">></span></li>
        <li><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>">Καρτέλα εργαζομένου</a> <span class="divider">></span></li>
        <li class="active">Πλάνο Διδασκαλίας</li>
      </ul>
        <!-- <a class="btn btn-mini" href="<?php echo base_url();?>"><i class="icon-arrow-left"></i> πίσω</a>         -->
      </div>
      
      

      <h3><?php echo $employee['surname'].' '.$employee['name']?></h3>
      

      <ul class="nav nav-tabs">
        <li><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>">Στοιχεία</a></li>
        <li class="active"><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>/teachingplan">Πλάνο Διδασκαλίας</a></li>
      </ul>
     

<!--       <ul class="nav nav-pills">
        <li class="active"><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>/program">Πρόγραμμα</a></li>
        <li><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>/sections">Τμήματα</a></li>
      </ul>
 -->

      <div class="row-fluid">
        <div class="span12">
          <h4>Σύνοψη:</h4>
        </div>
      </div>

      <div class="row-fluid"> <!--Συνοπτική ενημέρωση-->
      	<div class="span12">
	      	<div class="row-fluid"> <!--Πρόγραμμα ημέρας-->
		      	<div class="span12"> 
		      	<div class="contentbox">
              <div class="title">
                <span class="icon">
                  <i class="icon-time"></i>
                </span>
                <h5>Πρόγραμμα ημέρας</h5>
              </div>
            <div class="content">
			      		<?php if(empty($dayprogram)):?>
			      			<?php if(empty($program)):?>
				      			<p class="text-info">
				      				Δεν έχει εισαχθεί πρόγραμμα για το συγκεκριμένο καθηγητή!
				      			</p>
				      		<?php else:?>
				      			<p class="text-info">
				      				Σήμερα δεν έχει κανένα μάθημα!
				      			</p>
				      		<?php endif;?>
			      		<?php else:?>
				      		<table class="footable table table-striped table-condensed " >
				      			<thead>
                      <tr>
  				      				<th data-class="expand">Ώρα</th>
  				      				<th>Μάθημα</th>
  				      				<th data-hide="phone">Διδάσκων</th>
  				      				<th data-hide="phone">Τμήμα</th>
  				      				<th data-hide="phone,tablet">Αίθουσα</th>
                      </tr>
				      			</thead>
				      			<tbody>
				      				<?php foreach ($dayprogram as $data):?>
				      					<tr>
				      						<td><?php echo date('H:i',strtotime($data['start_tm'])).'-'.date('H:i',strtotime($data['end_tm']))?></td>
				      						<td><?php echo $data['title']?></td>
				      						<td><?php echo $data['nickname']?></td>
				      						<td><?php echo $data['section']?></td>
				      						<td><?php echo $data['classroom']?></td>
				      					</tr>
				      				<?php endforeach;?>
				      			</tbody>
				      		</table>
				      	<?php endif;?>
				      	<div class="row-fluid">
			      			<div class="span12">	
			      				<!-- onclick="return false;" is needed as an a tag can't be disabled by the disabled property. I'm using the property just for it's css -->
			      				<a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>/teachingplan/program" class="btn btn-small pull-right" <?php if(empty($program)) echo 'disabled="disabled" onclick="return false;"';?> >Εβδομαδιαίο πρόγραμμα</a>
			      			</div>
			      		</div>
				      </div>
		      	</div>
          </div>
		      </div> <!--τέλος ημερησίου προγράμματος-->

		      <div class="row-fluid">
		      	<div class="span6"> <!--Αριθμός/Ονομασία Τμημάτων - Ίσως και αρ. ατόμων ανα τμήμα-->
		      		<div class="contentbox">
              <div class="title">
                <span class="icon">
                  <i class=" icon-cog"></i>
                </span>
                <h5>Τμήματα</h5>
              </div>
            <div class="content">
		      			<?php if (empty($program)):?>
      					<div class="alert alert-block alert-error fade in">
				            <!-- <button type="button" class="close" data-dismiss="alert">&times;</button> -->
				            <h4 class="alert-heading"><i class="icon-exclamation-sign"></i> Δεν έχει εισαχθεί πρόγραμμα τμημάτων!</h4>
				            <p>Για την εισαγωγή τμημάτων ή/και προγράμματος αυτών επιλέξτε "Διαχείριση" από την αρχική καρτέλα.</p>
				        </div>
				    <?php else:?>
                  <?php $stdsum=0; $sectionsnum=0; $hourssum=0;?>
                  <table class="footable table table-striped table-condensed">
                    <thead>
                      <tr>
                        <th data-class="expand">Τμήμα</th>
                        <th data-hide="phone">Αρ. Ατόμων</th>
                        <th>Μάθημα</th>
                        <th>Ώρες</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($section as $data):?>
                        <tr>
                          <td><?php echo $data['section']?></td>
                          <td><?php echo $data['studentsnum']?></td>
                          <td><?php echo $data['title']?></td>
                          <td><?php echo $data['hours']?></td>
                          <?php $sectionsnum++; $stdsum+=$data['studentsnum']; $hourssum+=$data['hours'];?>
                        </tr>
                      <?php endforeach;?>
                    </tbody>
                    <tfoot>
                      <tr>
                          <th data-class="expand">Σύνολο:</th>
                          <th data-hide="phone"></th>
                          <th></th>
                          <th></th>
                      </tr>
                      <tr>
                          <td><?php echo $sectionsnum;?></td>
                          <td><?php echo $stdsum;?></td>
                          <td></td>
                          <td><?php echo $hourssum;?></td>
                      </tr>
                    </tfoot>
                  </table>
		      		<?php endif;?>
				      	<div class="row-fluid">
			      			<div class="span12">	
			      				<a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>/teachingplan/sections" class="btn btn-small pull-right">Περισσότερα</a>
				   			</div>
				   		</div>
  				    </div>
		      	</div>
          </div>

		      	<div class="span6"> <!--απουσίες & πρόοδος-->

			      	<div class="row-fluid">
				      	<div class="span12"> <!--Βαθμολογία τελευταίου διαγωνίσματος - Μέσος όρος βαθμολογίας (στο τέλος Περισότερα...) -->
				      		<div class="contentbox">
              <div class="title">
                <span class="icon">
                  <i class="icon-random"></i>
                </span>
                <h5>Καταγραφή προόδου</h5>
              </div>
            <div class="content">
              <?php if(empty($progress)):?>
                <div class="row-fluid">
                  <div class="span12">  
                    <p class="text-info">
                      Δεν υπάρχουν δεδομένα για την πρόοδο του μαθητή!
                    </p>
                    <!-- <a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance/progress" class="btn  btn-small pull-right disabled" onclick="return false;" >Βαθμολόγιο</a> -->
                  </div>
                </div>
              <?php else:?>
               <div class="row-fluid">
                <div class="span12">  
                  <!-- <a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance/progress" class="btn  btn-small pull-right" >Βαθμολόγιο</a> -->
                </div>
              </div>
              <?php endif;?>
	      		</div>
	      	</div>
		    </div>
      </div>

   	</div>

  </div>
</div>


</div> <!--Τέλος συνοπτικής ενημέρωσης-->

</div> <!--end of fluid container-->

</div> <!--end of main container-->

<div class="push"></div>
</div> <!-- end of body wrapper-->