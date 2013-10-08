<script type="text/javascript">

$(document).ready(function(){
   
  $('.footable').footable();
})
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
            <li><a href="<?php echo base_url()?>student">Μαθητολόγιο</a></li>
            <li class="active"><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
            <li><a href="<?php echo base_url()?>section">Τμήματα</a></li>
            <li><a href="#finance">Οικονομικά</a></li>
            <li><a href="#reports">Αναφορές</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Χρήστης<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header">Νικηφορακης Μανος</li>
                <li><a href="#">Αλλαγή κωδικού</a></li>
                <li><a href="#admin">Διαχείριση</a></li>
                <li class="divider"></li>
                <li><a href="#">Αποσύνδεση</a></li>
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
    <h1>Καρτέλα Εργαζομένου</h1>
    <p class="leap">Πρόγραμμα διαχείρισης φροντιστηρίου.</p>
  </div>
</div>


<!-- main container
================================================== -->

  <div class="container"  style="margin-bottom:60px;">
  
      <div>
        <ul class="breadcrumb">
          <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a></li>
          <li><a href="<?php echo base_url()?>staff">Προσωπικό</a> </li>
          <li><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>">Καρτέλα εργαζομένου</a> </li>
          <li class="active">Πλάνο Διδασκαλίας</li>
        </ul>
      </div>
      
      
      <p>
        <h3><?php echo $employee['surname'].' '.$employee['name']?></h3>
      </p>
      

      <ul class="nav nav-tabs">
        <li><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>">Στοιχεία</a></li>
        <li class="active"><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>/teachingplan">Πλάνο Διδασκαλίας</a></li>
      </ul>
     
      <div class="row">
        <div class="col-md-12">
          <h4>Σύνοψη:</h4>
        </div>
      </div>

      <div class="row"> <!--Συνοπτική ενημέρωση-->
      	<div class="col-md-12">
	      	<div class="row"> <!--Πρόγραμμα ημέρας-->
		      	<div class="col-md-12"> 
		      	<div class="panel panel-default">
              <div class="panel-heading">
                <span class="icon">
                  <i class="icon-time"></i>
                </span>
                <h3 class="panel-title">Πρόγραμμα ημέρας</h3>
              </div>
            <div class="panel-body">
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
				      	<div class="row">
			      			<div class="col-md-12">	
			      				<!-- onclick="return false;" is needed as an a tag can't be disabled by the disabled property. I'm using the property just for it's css -->
			      				<a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>/teachingplan/program" class="btn btn-default btn-sm pull-right" <?php if(empty($program)) echo 'disabled="disabled" onclick="return false;"';?> >Εβδομαδιαίο πρόγραμμα</a>
			      			</div>
			      		</div>
				      </div>
		      	</div>
          </div>
		      </div> <!--τέλος ημερησίου προγράμματος-->

		      <div class="row">
		      	<div class="col-md-6"> <!--Αριθμός/Ονομασία Τμημάτων - Ίσως και αρ. ατόμων ανα τμήμα-->
		      		<div class="panel panel-default">
              <div class="panel-heading">
                <span class="icon">
                  <i class=" icon-cog"></i>
                </span>
                <h3 class="panel-title">Τμήματα</h3>
              </div>
            <div class="panel-body">
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
				      	<div class="row">
			      			<div class="col-md-12">	
			      				<a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>/teachingplan/sections" class="btn btn-default btn-sm pull-right">Περισσότερα</a>
				   			</div>
				   		</div>
  				    </div>
		      	</div>
          </div>

		      	<div class="col-md-6"> <!--απουσίες & πρόοδος-->

			      	<div class="row">
				      	<div class="col-md-12"> <!--Βαθμολογία τελευταίου διαγωνίσματος - Μέσος όρος βαθμολογίας (στο τέλος Περισότερα...) -->
				      		<div class="panel panel-default">
              <div class="panel-heading">
                <span class="icon">
                  <i class="icon-random"></i>
                </span>
                <h3 class="panel-title">Καταγραφή προόδου</h3>
              </div>
            <div class="panel-body">
              <?php if(empty($progress)):?>
                <div class="row">
                  <div class="col-md-12">  
                    <p class="text-info">
                      Δεν υπάρχουν δεδομένα για την πρόοδο του μαθητή!
                    </p>
                    <!-- <a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance/progress" class="btn btn-default btn-sm pull-right disabled" onclick="return false;" >Βαθμολόγιο</a> -->
                  </div>
                </div>
              <?php else:?>
               <div class="row">
                <div class="col-md-12">  
                  <!-- <a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance/progress" class="btn btn-default btn-sm pull-right" >Βαθμολόγιο</a> -->
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

</div> <!--end of main container-->

<div class="push"></div>
</div> <!-- end of body wrapper-->