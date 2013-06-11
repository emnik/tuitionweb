<style type="text/css">

/*responsive tables from http://dbushell.com/demos/tables/rt_05-01-12.html*/
  @media (max-width: 480px) {


    .dayprogram { display: block; position: relative; width: 100%; }
    .dayprogram thead { display: block; float: left; }
    .dayprogram tbody { display: block; width: auto; position: relative; overflow-x: auto; white-space: nowrap; }
    .dayprogram thead tr { display: block; }
    .dayprogram th { display: block; }
    .dayprogram tbody tr { display: inline-block; vertical-align: top; }
    .dayprogram td { display: block; min-height: 1.25em; }
    
}
</style>

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
        content:"<ul><li>Για προσθήκη ή διαγραφή μαθημάτων πηγαίνετε στην καρτέλα <a href='<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance/manage'> Φοίτηση / Διαχείριση προγράμματος σπουδών.</a></li><li>Για επεξεργασία ώρας και αίθουσας ενός μαθήματος πατήστε την επιλογή 'Eπεξεργασία' δεξιά του μαθήματος αυτού.</li></ul>"
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

  <div class="container" style="margin-bottom:60px;">
  
    <div class="container-fluid">
      
      <div style="margin-top:20px; margin-bottom:-15px;">
      <ul class="breadcrumb">
        <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a><span class="divider">></span></li>
        <li><a href="<?php echo base_url()?>registrations">Μαθητολόγιο</a> <span class="divider">/</span></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>">Καρτέλα μαθητή</a> <span class="divider">/</span></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance">Φοίτηση</a> <span class="divider">/</span></li>
        <li class="active">Εβδομαδιαίο Πρόγραμμα</li>
      </ul>
      </div>
      
      

      <h3><?php echo $student['surname'].' '.$student['name']?></h3>
      

      <ul class="nav nav-tabs">
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>">Στοιχεία</a></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/contact">Επικοινωνία</a></li>
        <li class="active"><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance">Φοίτηση</a></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance">Οικονομικά</a></li>
      </ul>

      <?php $day=array(1 => 'Δευτέρα', 2 => 'Τρίτη', 3 => 'Τετάρτη', 4 => 'Πέμπτη',
                       5 => 'Παρασκευή', 6 => 'Σάββατο', 7 => 'Κυριακή');?>

      <div class="row-fluid">
        <div class="span12">
          <div class="btn-toolbar">
            <a class="btn" href="<?php echo base_url();?>student/card/<?php echo $student['id']?>/attendance"><i class="icon-chevron-left"></i> πίσω</a>
            <div class="btn-group">
              <button type="button" id="toggledays" data-toggle="button" class="btn" onclick="toggledays(this)"><i class="icon-calendar"></i></button>
            </div>
            <buttton type="button" class="btn pull-right" id="help">Βοήθεια</button>            
          </div>
        </div>
      </div>

      <div class="row-fluid">
      	
        <div class="span4">
      		<h4>Εβδομαδιαίο πρόγραμμα :</h4>
        </div>

        <div class="span8">

          <ul class="nav nav-pills">
            <?php for ($i=1; $i <= 7 ; $i++):?>
            <li <?php if(date('N')==$i) echo ' class="active"'?>>
                <a id="dayli<?php echo $i?>" href="#day<?php echo $i?>"><?php echo $day[$i];?></a>
            </li>
            <?php endfor;?>
          </ul>

        </div>

      </div>


	      	<div class="row-fluid">
		      	<div class="span12"> 
			      		<?php if(empty($program)):?>
			      			<p class="text-info">
			      				Δεν έχει εισαχθεί το πρόγραμμα για το συγκεκριμένο μαθητή!
			      			</p>
			      		<?php else:?>
			      		<?php $i=$program[0]['priority'];?>

                <?php $k=0;?>
                <?php for ($j=1; $j<=7 ; $j++):?>
							    <div id="day<?php echo $j;?>" >
                  <?php if($j<$i || $k==count($program)):?>
                    <div class="contentbox nolesson">
                      <div class="title">
                        <span class="icon">
                          <i class="icon-calendar"></i>
                        </span>
                        <h5><?php echo $day[$j];?></h5>
                      </div>
                    <div class="content">
                    <p class="nolesson">Κανένα μάθημα</p>
                    <script type="text/javascript">
                      nodays.push(<?php echo $j;?>);
                    </script>
                  <?php else:?>
                    <div class="contentbox">
                      <div class="title">
                        <span class="icon">
                          <i class="icon-calendar"></i>
                        </span>
                        <h5><?php echo $day[$j];?></h5>
                      </div>
                    <div class="content">
                    <table class="dayprogram table table-striped table-condensed " >
  				      			<thead>
  				      				<th>Ώρα</th>
  				      				<th>Μάθημα</th>
  				      				<th>Διδάσκων</th>
  				      				<th>Τμήμα</th>
  				      				<th>Αίθουσα</th>
                        <th></th>
  				      			</thead>
  				      			<tbody>
                        <?php $stop=false;?>
                    		<?php while($k<count($program) && $stop==false):?>
                          <?php if($program[$k]['priority']==$i):?>
    				      					<tr>
    				      						<td><?php echo date('H:i',strtotime($program[$k]['start_tm'])).'-'.date('H:i',strtotime($program[$k]['end_tm']));?></td>
    				      						<td><?php echo $program[$k]['title'];?></td>
    				      						<td><?php echo $program[$k]['nickname'];?></td>
    				      						<td><?php echo $program[$k]['section'];?></td>
    				      						<td><?php echo $program[$k]['classroom'];?></td>
                              <td><button class="btn btn-small pull-right"><i class="icon-edit"></i><small></small></button></td>
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

  	</div> <!--end of fluid container-->

  </div> <!--end of main container-->

<div class="push"></div>
</div> <!-- end of body wrapper-->