<script type="text/javascript">

$(document).ready(function(){
   
 $('.footable').footable();

 $('.toggle1').click(function() {
                $('.toggle1').toggle();
                $('table:first').trigger($(this).data('trigger')).trigger('footable_redraw');
            });

 $('.toggle2').click(function() {
                $('.toggle2').toggle();
                $('table:last').trigger($(this).data('trigger')).trigger('footable_redraw');
            });

  $('.toggle3').click(function() {
                $('.toggle3').toggle();
                $('#examtable').trigger($(this).data('trigger')).trigger('footable_redraw');
            });

  $('input[type=radio]').click(function(){
    $('#examdetails').removeAttr('disabled');
  });

  $('#examdetails').click(function(){
    var examid = $('input[type=radio]:checked').val();
    window.open("<?php echo base_url('/exam/details');?>/"+examid,'_self', false)
  });

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


})

  function hideFootableExpandButtons(){
      $('.footable').each(function(){
        if($(this).hasClass('phone'))
        {
          $(this).parents('.panel-body').prev().children('.buttons').show();
        }
        else
        {
          $(this).parents('.panel-body').prev().children('.buttons').hide();
        }
      });
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
                <li class="active"><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
                <li><a href="<?php echo base_url()?>section">Τμήματα</a></li>
                <li><a href="<?php echo base_url()?>curriculum/edit">Πρόγραμμα Σπουδών</a></li>
                <li><a href="<?php echo base_url()?>curriculum/edit/tutorsperlesson">Μαθήματα-Διδάσκωντες</a></li>
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
          <li><a href="<?php echo base_url()?>staff">Προσωπικό</a> </li>
          <li><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>">Καρτέλα εργαζομένου</a> </li>
          <li class="active">Βαθμολόγιο</li>
        </ul>
      </div>
      
      
      <p>
        <h3><?php echo $employee['surname'].' '.$employee['name']?></h3>
      </p>
      

      <ul class="nav nav-tabs">
        <li><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>">Στοιχεία</a></li>
        <li><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>/teachingplan">Πλάνο Διδασκαλίας</a></li>
        <li class="active"><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>/gradebook">Βαθμολόγιο</a></li>
      </ul>
     
<!--       <div class="row">
        <div class="col-md-12">
          <h4>Σύνοψη:</h4>
        </div>
      </div> -->

      <div class="row" style="margin-top:15px;"> <!--Συνοπτική ενημέρωση-->
      	<div class="col-md-12">
          	<div class="panel panel-default">
              <div class="panel-heading">
                <span class="icon">
                  <i class="icon-pencil"></i>
                </span>
                <h3 class="panel-title">Διαγωνίσματα</h3>
                  <?php if(!empty($exam)):?>
                   <div class="buttons">
                    <a enabled data-trigger="footable_expand_all" class="toggle3 btn btn-default btn-sm" href="#expandall"><i class="icon-angle-down"></i></a>
                    <a enabled data-trigger="footable_collapse_all" style="display: none" class="toggle3 btn btn-default btn-sm" href="#collapseall"><i class="icon-angle-up"></i></a>
                  </div>
                  <?php endif;?>
              </div>
            <div class="panel-body">
                <div class="row">
                  <div class="col-md-12">  
                    <?php if(!empty($supervisor)):?>
                        <div class="alert alert-warning">
                           <strong>Eπιτήρηση</strong> στις ακόλουθες ημερομηνίες: <?php echo $supervisor;?>
                        </div>
                    <?php endif;?>
                    <?php if(empty($exam)):?>
                          <p class="text-info">
                            Δεν υπάρχουν προγραμματισμένα διαγωνίσματα!
                          </p>
                          <a href="<?php echo base_url('/exam')?>" class="btn btn-default btn-sm pull-right">Διαγωνίσματα</a>
                    <?php else:?>
                    <label class="label label-warning">Επιλέξτε:</label>
                    <table id="examtable" class="table footable table-striped table-condensed">
                        <thead>
                          <tr>
                            <th></th>
                            <th data-toggle="true">Ημερ/νία</th>
                            <th>Μάθημα</th>
                            <th>Τάξη</th>
                            <th data-hide="phone">Κατεύθυνση</th>
                            <th data-hide="phone">Τμήματα</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($exam as $data):?>
                              <tr>
                                <td><input class="checkbox" type="radio" name="select" value="<?php echo $data['id'];?>"></td>
                                <td><?php echo implode('-', array_reverse(explode('-', $data['date'])));?></td>
                                <td><?php echo $data['title'];?></td>
                                <td><?php echo $data['class_name'];?></td>
                                <td><?php echo $data['course'];?></td>
                                <td><?php if(!empty($data['sections']))
                                          {
                                            echo $data['sections'];
                                          }
                                          else
                                          {
                                            echo '-';
                                            
                                          }?>
                                </td>
                              </tr>
                          <?php endforeach;?>
                        </tbody>
                    </table>
                    <button disabled id="examdetails" class="btn btn-warning btn-sm pull-left">Λεπτομέρειες</button>
<!--                     <a href="<?php echo base_url('/exam')?>" class="btn btn-default btn-sm pull-right">Διαγωνίσματα</a> -->
              <?php endif;?>
                </div>
              </div>
	      		</div>
	      	</div>
		    </div>
      </div>


</div> <!--end of main container-->

<div class="push"></div>
</div> <!-- end of body wrapper-->