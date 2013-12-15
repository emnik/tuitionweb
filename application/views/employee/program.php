<script type="text/javascript">
var nodays = new Array(7);

function toggledays(togglecontrol) {

  if ($(togglecontrol).hasClass('active')){
     $('#toggledays').attr('data-original-title', 'Εμφάνιση όλων των ημερών')
          .tooltip('fixTitle')
          .tooltip('show');

      $('.nolesson').parent().hide();

      for (var i = 0; i < nodays.length; i++) {
        if (typeof nodays[i] !== 'undefined') {
          $('#dayli'+nodays[i]).hide();
        };

      };
    }
  else 
  {
     $('#toggledays').attr('data-original-title', 'Απόκρυψη ημερών χωρίς μάθημα')
          .tooltip('fixTitle')
          .tooltip('show');

      $('.nolesson').parent().show();  

      for (var i = 0; i < nodays.length; i++) {
        if (typeof nodays[i] !== 'undefined') {
          $('#dayli'+nodays[i]).show();
        };

      };
    };
}


   $(document).ready(function() {

     $('#help').popover({
        placement:'bottom',
        container:'body',
        html:'true',       
        title:'<h4>Συνήθεις επεξεργασίες</h4>',
        content:"<ul><li>Για επεξεργασία ώρας και αίθουσας ενός μαθήματος πατήστε το κουμπί δεξιά του μαθήματος αυτού.</li></ul>"
     });

     $('#toggledays').tooltip({
        title:'Εμφάνιση όλων των ημερών',
        trigger:'hover',
        placement: 'right',
        container:'body'
     });

     $('.nolesson').parent().hide();
     
     for (var i = 0; i < nodays.length; i++) {
        if (typeof nodays[i] !== 'undefined') {
          $('#dayli'+nodays[i]).hide();
        };
      };

     $('.footable').footable();

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
                <li class="dropdown-header"><?php echo $user->surname.' '.$user->name;?></li>
                <li><a href="#">Αλλαγή κωδικού</a></li>
                <li><a href="#admin">Διαχείριση</a></li>
                <li class="divider"></li>
                <li><a href="<?php echo base_url()?>staff/logout">Αποσύνδεση</a></li>
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

  <div class="container" style="margin-bottom:60px;">
  
     
      <div>
        <ul class="breadcrumb">
          <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a></li>
          <li><a href="<?php echo base_url()?>staff">Προσωπικό</a> </li>
          <li><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>">Καρτέλα εργαζομένου</a> </li>
          <li class="active">Πρόγραμμα εργαζομένου</li>
        </ul>
      </div>
      
      
      <p>
        <h3><?php echo $employee['surname'].' '.$employee['name']?></h3>
      </p>

      <ul class="nav nav-tabs">
        <li><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>">Στοιχεία</a></li>
        <li class="active"><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>/teachingplan">Πλάνο Διδασκαλίας</a></li>
      </ul>

      <?php $day=array(1 => 'Δευτέρα', 2 => 'Τρίτη', 3 => 'Τετάρτη', 4 => 'Πέμπτη',
                       5 => 'Παρασκευή', 6 => 'Σάββατο', 7 => 'Κυριακή');?>

      <div class="row">
        <div class="col-md-12">
          <div class="btn-toolbar" style="margin:15px 0px;">
            <a class="btn btn-default" href="<?php echo base_url();?>staff/card/<?php echo $employee['id']?>/teachingplan"><i class="icon-chevron-left"></i> πίσω</a>
            <button type="button" id="toggledays" data-toggle="button" class="btn btn-default" onclick="toggledays(this)"><i class="icon-calendar"></i></button>
            <button type="button" class="btn btn-default pull-right" id="help">Βοήθεια</button>            
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
      		<h4>Εβδομαδιαίο πρόγραμμα :</h4>
        </div>
        <div class="col-md-8">
          <ul class="nav nav-pills">
            <?php for ($i=1; $i <= 7 ; $i++):?>
            <li <?php if(date('N')==$i) echo ' class="active"'?>>
                <a id="dayli<?php echo $i?>" href="#day<?php echo $i?>"><?php echo $day[$i];?></a>
            </li>
            <?php endfor;?>
          </ul>
        </div>
      </div>

      <p></p>

    	<div class="row">
      	<div class="col-md-12"> 
	      		<?php if(empty($program)):?>
	      			<p class="text-info">
	      				Δεν έχει εισαχθεί το πρόγραμμα για το συγκεκριμένο εργαζόμενο!
	      			</p>
	      		<?php else:?>
	      		<?php $i=$program[0]['priority'];?>

            <?php $k=0;?>
            <?php for ($j=1; $j<=7 ; $j++):?>
					    <div id="day<?php echo $j;?>" >
              <?php if($j<$i || $k==count($program)):?>
                <div class="panel panel-default nolesson">
                  <div class="panel-heading">
                    <span class="icon">
                      <i class="icon-calendar"></i>
                    </span>
                    <h3 class="panel-title"><?php echo $day[$j];?></h3>
                  </div>
                <div class="panel-body">
                <p class="nolesson">Κανένα μάθημα</p>
                <script type="text/javascript">
                  nodays.push(<?php echo $j;?>);
                </script>
              <?php else:?>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <span class="icon">
                      <i class="icon-calendar"></i>
                    </span>
                    <h3 class="panel-title"><?php echo $day[$j];?></h3>
                  </div>
                <div class="panel-body">
                <table class="footable table table-striped table-condensed " >
			      			<thead>
                    <tr>
				      				<th data-class="expand">Ώρα</th>
				      				<th>Μάθημα</th>
				      				<th data-hide="phone,tablet">Διδακτικές Ώρες</th>
				      				<th data-hide="phone">Τμήμα</th>
				      				<th data-hide="phone">Αίθουσα</th>
                      <th></th>
                    </tr>
			      			</thead>
			      			<tbody>
                    <?php $stop=false;?>
                		<?php while($k<count($program) && $stop==false):?>
                      <?php if($program[$k]['priority']==$i):?>
				      					<tr>
				      						<td><?php echo date('H:i',strtotime($program[$k]['start_tm'])).'-'.date('H:i',strtotime($program[$k]['end_tm']));?></td>
				      						<td><?php echo $program[$k]['title'];?></td>
				      						<td><?php echo $program[$k]['duration'];?></td>
				      						<td><?php echo $program[$k]['section'];?></td>
				      						<td><?php echo $program[$k]['classroom'];?></td>
                          <td><a class="btn btn-default btn-sm pull-right" href="<?php echo base_url()?>section/card/<?php echo $program[$k]['section_id'];?>"><i class="icon-edit"></i><small></small></a></td>
				      					</tr>
			      					  <?php $k++;?>
                      <?php else:?>
                        <?php $stop=true;?>
                      <?php endif;?>
			      				<?php endwhile;?>
			      			</tbody>
			      		</table>

                <?php if($k<count($program)):?>
                  <?php $i=$program[$k]['priority'];?>
                <?php endif;?>
			      	<?php endif;?>
              </div> <!--Close contentbox class-->     
            </div>
          </div>           
        <?php endfor;?>
      <?php endif;?>
    	</div>
    </div>

  </div> <!--end of main container-->

<div class="push"></div>
</div> <!-- end of body wrapper-->