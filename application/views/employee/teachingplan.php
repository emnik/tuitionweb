<script type="text/javascript">
  $(document).ready(function() {

    //Menu current active links and Title
    $('#menu-management').addClass('active');
    $('#menu-staff').addClass('active');
    $('#menu-header-title').text('Καρτέλα Εργαζομένου');

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

    $('#examtable input[type=radio]').click(function() {
      $('#examdetails').removeAttr('disabled');
    });

    $('#supervisorstable input[type=radio]').click(function() {
      $('#supervisordetails').removeAttr('disabled');
    });

    $('#examdetails, #supervisordetails').click(function() {
      var examid = $('input[type=radio]:checked').val();
      window.open("<?php echo base_url('/exam/card'); ?>/" + examid, '_self', false)
    });

    $(window).resize(function() {
      if (this.resizeTO) clearTimeout(this.resizeTO);
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

  function hideFootableExpandButtons() {
    $('.footable').each(function() {
      if ($(this).hasClass('phone')) {
        $(this).parents('.panel-body').prev().children('.buttons').show();
      } else {
        $(this).parents('.panel-body').prev().children('.buttons').hide();
      }
    });
  }
</script>

</head>

<body>
  <div class="wrapper">
    <!--body wrapper for css sticky footer-->

    <!-- Menu start -->
    <!-- dirname(__DIR__) gives the path one level up by default -->
    <?php include(dirname(__DIR__) . '/include/menu.php'); ?>
    <!-- Menu end -->

    <!-- main container
================================================== -->

    <div class="container" style="margin-bottom:60px;">

      <div>
        <ul class="breadcrumb">
          <li><a href="<?php echo base_url() ?>"><i class="icon-home"> </i> Αρχική </a></li>
          <li><a href="<?php echo base_url() ?>staff">Προσωπικό</a> </li>
          <li><a href="<?php echo base_url() ?>staff/card/<?php echo $employee['id'] ?>">Καρτέλα εργαζομένου</a> </li>
          <li class="active">Πλάνο Διδασκαλίας</li>
        </ul>
      </div>


      <p>
        <h3><?php echo $employee['surname'] . ' ' . $employee['name'] ?></h3>
      </p>


      <ul class="nav nav-tabs">
        <li><a href="<?php echo base_url() ?>staff/card/<?php echo $employee['id'] ?>">Προφίλ</a></li>
        <li class="active"><a href="<?php echo base_url() ?>staff/card/<?php echo $employee['id'] ?>/teachingplan">Πλάνο Διδασκαλίας</a></li>
        <li><a href="<?php echo base_url() ?>staff/card/<?php echo $employee['id'] ?>/gradebook">Βαθμολόγιο</a></li>
      </ul>

      <!-- <div class="row">
        <div class="col-md-12">
          <h4>Σύνοψη:</h4>
        </div>
      </div> -->
      <p></p>

      <div class="row">
        <!--Συνοπτική ενημέρωση-->
        <div class="col-md-12">
          <div class="row">
            <!--Πρόγραμμα ημέρας-->
            <div class="col-md-12">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <span class="icon">
                    <i class="icon-time"></i>
                  </span>
                  <h3 class="panel-title">Πρόγραμμα ημέρας</h3>
                  <?php if (!empty($dayprogram) && !empty($program)) : ?>
                    <div class="buttons">
                      <a enabled data-trigger="footable_expand_all" class="toggle1 btn btn-default btn-sm" href="#expandall"><i class="icon-angle-down"></i></a>
                      <a enabled data-trigger="footable_collapse_all" style="display: none" class="toggle1 btn btn-default btn-sm" href="#collapseall"><i class="icon-angle-up"></i></a>
                    </div>
                  <?php endif; ?>
                </div>
                <div class="panel-body">
                  <?php if (empty($dayprogram)) : ?>
                    <?php if (empty($program)) : ?>
                      <p class="text-info">
                        Δεν έχει εισαχθεί πρόγραμμα για το συγκεκριμένο καθηγητή!
                      </p>
                    <?php else : ?>
                      <p class="text-info">
                        Σήμερα δεν έχει κανένα μάθημα!
                      </p>
                    <?php endif; ?>
                  <?php else : ?>
                    <table class="footable table table-striped table-condensed ">
                      <thead>
                        <tr>
                          <th data-toggle="true">Ώρα</th>
                          <th>Μάθημα</th>
                          <th>Τμήμα</th>
                          <th data-hide="phone">Αίθουσα</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($dayprogram as $data) : ?>
                          <tr>
                            <td><?php echo date('H:i', strtotime($data['start_tm'])) . '-' . date('H:i', strtotime($data['end_tm'])) ?></td>
                            <td><?php echo $data['title'] ?></td>
                            <!-- <td><?php echo $data['nickname'] ?></td> -->
                            <td>
                              <a href="<?php echo base_url('section/card/' . $data['id']); ?>">
                                <span class="label label-section">
                                  <?php echo $data['section'] ?>
                                </span>
                              </a>
                            </td>
                            <td><?php echo $data['classroom'] ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  <?php endif; ?>
                  <div class="row">
                    <div class="col-md-12">
                      <!-- onclick="return false;" is needed as an a tag can't be disabled by the disabled property. I'm using the property just for it's css -->
                      <a href="<?php echo base_url() ?>staff/card/<?php echo $employee['id'] ?>/teachingplan/program" class="btn btn-default btn-sm pull-right" <?php if (empty($program)) echo 'disabled="disabled" onclick="return false;"'; ?>>Εβδομαδιαίο πρόγραμμα</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!--τέλος ημερησίου προγράμματος-->

          <div class="row">
            <div class="col-xs-12">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <span class="icon">
                    <i class=" icon-bar-chart"></i>
                  </span>
                  <h3 class="panel-title">Στατιστικά</h3>
                </div>
                <div class="panel-body">
                  <div class="row">
                    <div class="col-xs-6 col-md-3 stats">
                    <div class="stats-icon"> 
                        <!-- <img src="https://www.flaticon.com/svg/static/icons/svg/3721/3721097.svg"> -->
                        <img src="<?php echo base_url('/assets/img/class-icon.svg');?>">
                      </div>
                      <h3>
                        Τάξεις:
                        <?php echo $stats['classcount']; ?>
                      </h3>
                    </div>
                    <div class="col-xs-6 col-md-3 stats">
                      <div class="stats-icon"> 
                        <!-- <img src="https://www.flaticon.com/svg/static/icons/svg/1436/1436766.svg"> -->
                        <img src="<?php echo base_url('/assets/img/section-icon.svg');?>">
                      </div>
                    <h3>
                        Τμήματα:
                        <?php echo $stats['sectionscount']; ?>
                        </h3>
                    </div>
                    <div class="col-xs-6 col-md-3 stats">
                    <div class="stats-icon"> 
                        <!-- <img src="https://www.flaticon.com/svg/static/icons/svg/1170/1170188.svg"> -->
                        <img src="<?php echo base_url('/assets/img/students-icon.svg');?>">
                      </div>
                      <h3>
                        Μαθητές:
                        <?php echo $stats['stdcount']; ?>
                      </h3>
                    </div>
                    <div class="col-xs-6 col-md-3 stats">
                    <div class="stats-icon"> 
                        <!-- <img src="https://www.flaticon.com/svg/static/icons/svg/1171/1171977.svg"> -->
                        <img src="<?php echo base_url('/assets/img/hours-icon.svg');?>">
                      </div>
                      <h3>
                        Εβδομαδιαίες Ώρες:
                        <?php echo $stats['hourscount']; ?>
                      </h3>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-7">
              <!--Αριθμός/Ονομασία Τμημάτων - Ίσως και αρ. ατόμων ανα τμήμα-->
              <div class="panel panel-default">
                <div class="panel-heading">
                  <span class="icon">
                    <i class=" icon-tags"></i>
                  </span>
                  <h3 class="panel-title">Σύνοψη τμημάτων</h3>
                </div>
                <div class="panel-body">
                  <?php if (empty($program)) : ?>
                    <div class="alert alert-block alert-error fade in">
                      <!-- <button type="button" class="close" data-dismiss="alert">&times;</button> -->
                      <!-- <h5 class="alert-heading"><i class="icon-exclamation-sign"></i> Δεν έχει εισαχθεί πρόγραμμα τμημάτων!</h5> -->
                      <p class="text-info">Δεν έχει εισαχθεί πρόγραμμα τμημάτων! Για την εισαγωγή τμημάτων επιλέξτε "Τμήματα" από το μενού ή την αρχική σελίδα.</p>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <a href="#" class="btn btn-default btn-sm pull-right" onclick="return false;" disabled>Περισσότερα</a>
                      </div>
                    </div>
                  <?php else : ?>

                    <div class="visible-sm visible-xs">
                      <div class="row">
                        <div class="col-md-12">
                          <div class="btn-group pull-left">
                            <?php $d = 1;
                            foreach ($section_summary as $class => $classdata) : ?>
                              <a class="btn btn-default btn-sm" style="margin:0px 5px 5px 0px;" href="#group<?php echo $d;
                                                                                                            $d++; ?>"><?php echo $class; ?></a>
                            <?php endforeach; ?>
                          </div>
                        </div>
                      </div>
                    </div>
                    <p></p>
                    <?php $c = 1;
                    foreach ($section_summary as $class => $classdata) : ?>
                      <p id="group<?php echo $c;
                                  $c++ ?>" class="bg-danger" style="padding: 5px 0px 5px 10px;">
                        <i class="icon-folder-close-alt"></i>
                        <span style="padding-left:10px;">
                          <?php echo $class; ?></p>
                      </span>
                      <?php
                      $stdsum = 0;
                      $sectionsnum = 0;
                      $hourssum = 0; ?>
                      <table class="table table-striped table-condensed">
                        <thead>
                          <tr>
                            <th>Τμήμα</th>
                            <th>Μαθητές</th>
                            <th>Μάθημα</th>
                            <th>Ώρες</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($classdata as $data) : ?>
                            <tr>
                              <td>
                                <a href="<?php echo base_url('section/card/' . $data['id'].'/sectionstudents'); ?>">
                                  <span class="label label-section">
                                    <?php echo $data['section'] ?>
                                  </span>
                                </a>
                              </td>
                              <td><?php echo $data['studentsnum'] ?></td>
                              <td><?php echo $data['title'] ?></td>
                              <td><?php echo $data['hours'] ?></td>
                              <?php $sectionsnum++;
                              $stdsum += $data['studentsnum'];
                              $hourssum += $data['hours']; ?>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                          <tr>
                            <th data-class="expand">Σύνολο:</th>
                            <th data-hide="phone"></th>
                            <th></th>
                            <th></th>
                          </tr>
                          <tr>
                            <td>Τμήματα: <?php echo $sectionsnum; ?> </td>
                            <td>Μαθητές: <?php echo $stdsum; ?> </td>
                            <td></td>
                            <td>Ώρες: <?php echo $hourssum; ?> </td>
                          </tr>
                        </tfoot>
                      </table>
                    <?php endforeach; ?>
                    <div class="row">
                      <div class="col-md-12">
                        <a href="<?php echo base_url() ?>staff/card/<?php echo $employee['id'] ?>/teachingplan/sections" class="btn btn-default btn-sm pull-right">Αναλυτικά</a>
                      </div>
                    </div>
                  <?php endif; ?>

                </div>
              </div>
            </div>
            <div class="col-md-5">
              <!--Διαγωνίσματα-->
              <div class="row">
                <div class="col-md-12">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <span class="icon">
                        <i class="icon-pencil"></i>
                      </span>
                      <h3 class="panel-title">Διαγωνίσματα</h3>
                      <?php if (!empty($exam)) : ?>
                        <div class="buttons">
                          <a enabled data-trigger="footable_expand_all" class="toggle3 btn btn-default btn-sm" href="#expandall"><i class="icon-angle-down"></i></a>
                          <a enabled data-trigger="footable_collapse_all" style="display: none" class="toggle3 btn btn-default btn-sm" href="#collapseall"><i class="icon-angle-up"></i></a>
                        </div>
                      <?php endif; ?>
                    </div>
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-md-12">
                          <?php if (empty($exam)) : ?>
                            <p class="text-info">
                              Δεν υπάρχουν προγραμματισμένα διαγωνίσματα!
                            </p>
                            <a href="<?php echo base_url('/exam') ?>" class="btn btn-default btn-sm pull-right">Διαγωνίσματα</a>
                          <?php else : ?>
                            <label class="label label-warning">Προγραμματισμένα:</label>
                            <table id="examtable" class="table footable table-striped table-condensed">
                              <thead>
                                <tr>
                                  <th></th>
                                  <th>Ημερ/νία</th>
                                  <th data-toggle="true">Ώρα</th>
                                  <th>Μάθημα</th>
                                  <th>Τάξη</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php foreach ($exam as $data) : ?>
                                  <tr>
                                    <td><input class="checkbox" type="radio" name="examselect" value="<?php echo $data['id']; ?>"></td>
                                    <td><?php echo implode('-', array_reverse(explode('-', $data['date']))); ?></td>
                                    <td><?php echo date('H:i', strtotime($data['start'])).'-'.date('H:i', strtotime($data['end']));?></td>
                                    <td><?php echo $data['title']; ?></td>
                                    <td><?php echo $data['class_name']; ?></td>
                                  </tr>
                                <?php endforeach; ?>
                              </tbody>
                            </table>
                            <button disabled id="examdetails" class="btn btn-warning btn-sm pull-left">Λεπτομέρειες</button>
                            <a href="<?php echo base_url('/exam') ?>" class="btn btn-default btn-sm pull-right">Διαγωνίσματα</a>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!--Επιτηρήσεις-->
              <div class="row">
                <div class="col-md-12">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <span class="icon">
                        <i class="icon-calendar"></i>
                      </span>
                      <h3 class="panel-title">Επιτηρήσεις</h3>
                    </div>
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-md-12">
                          <?php if (empty($supervisor)) : ?>
                            <p class="text-info">
                              Δεν υπάρχουν προγραμματισμένες επιτηρήσεις!
                            </p>
                            <a href="<?php echo base_url('/exam/supervisors') ?>" class="btn btn-default btn-sm pull-right">Επιτηρήσεις</a>
                          <?php else : ?>
                            <label class="label label-warning">Επιτηρήσεις:</label>
                            <table id="supervisorstable" class="table footable table-striped table-condensed">
                              <thead>
                                <tr>
                                  <th></th>
                                  <th>Ημερ/νία</th>
                                  <th data-toggle="true">Ώρα</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php foreach ($supervisor as $data) : ?>
                                  <tr>
                                    <td><input class="checkbox" type="radio" name="supervisorselect" value="<?php echo $data['id']; ?>"></td>
                                    <td><?php echo implode('-', array_reverse(explode('-', $data['date']))); ?></td>
                                    <td><?php echo date('H:i', strtotime($data['start'])).'-'.date('H:i', strtotime($data['end']));?></td>
                                  </tr>
                                <?php endforeach; ?>
                              </tbody>
                            </table>
                            <button disabled id="supervisordetails" class="btn btn-warning btn-sm pull-left">Λεπτομέρειες</button>
                            <a href="<?php echo base_url('/exam/supervisors') ?>" class="btn btn-default btn-sm pull-right">Επιτηρήσεις</a>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>              

            </div>

          </div>
        </div>


      </div>
      <!--Τέλος συνοπτικής ενημέρωσης-->

    </div>
    <!--end of main container-->

    <div class="push"></div>
  </div> <!-- end of body wrapper-->