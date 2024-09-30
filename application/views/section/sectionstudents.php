<script type="text/javascript">
  function toggleedit(togglecontrol, id) {

    if ($(togglecontrol).hasClass('active')) {
      // $('#submitform1btn').attr('disabled', 'disabled');
      $('#' + id).closest('.mainform').find(':input').each(function() {
        $(this).attr('disabled', 'disabled');
      });
      $('#' + id).closest('.mainform').find('.btn', function(){
        $(this).attr('disabled', 'disabled');
      });
      if(id=="editform2"){
        $('#selectstdsbox').select2('disable');
      }
    } else {
      // $('#submitform1btn').removeAttr('disabled');
      $('#' + id).closest('.mainform').find(':input').removeAttr('disabled');
      $('#' + id).closest('.mainform').find('.btn', function(){
        $(this).removeAttr('disabled');
      });
      if(id=="editform2"){
        $('#selectstdsbox').select2('enable');
      }
    };

  }


  function hideFootableExpandButtons() {
    if ($('.footable').hasClass('default')) {
      $('.buttons > a').addClass('hidden');
    } else {
      $('.buttons > a').removeClass('hidden');
    }
  }


  $(document).ready(function() {

    //Menu current active links and Title
    // $('#menu-management').addClass('active');
    $('#menu-section').addClass('active');
    $('#menu-header-title').text('Καρτέλα Τμήματος');

    $('.footable').footable();

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


    $('.toggle').click(function() {
      $('.toggle').toggle();
      $('table').trigger($(this).data('trigger')).trigger('footable_redraw');
    });

    $("body").on('click', '#editform1, #editform2', function() {
      toggleedit(this, this.id);
      $(this).removeAttr('disabled');

    });

    $("body").on('click', '#submitform1btn', function() {
      var selected_chkboxes = $('#form1').find(':input[type="checkbox"][name*="select"]:checked').length;
      if (selected_chkboxes > 0) {
        var msg = "Πρόκειται να αφαιρέσετε τους επιλεγμένους μαθητές από το τμήμα. Παρακαλώ επιβεβαιώστε.";
        var ans = confirm(msg);
        if (ans == true) {
          $('#form1').submit();
        };
      } else {
        alert("Δεν έχετε επιλέξει κανένα μαθητή για αφαίρεση από το τμήμα!");
      };


    });

    $('#checkall').click(function() {
      var selected_chkboxes = $('#form1').find(':input[type="checkbox"][name*="select"]:checked');
      if (selected_chkboxes.length < $('#form1').find(':input[type="checkbox"]').length - 1) {
        $('#form1').find(':input[type="checkbox"][name*="select"]').each(function() {
          $(this).prop('checked', true);
        });
      } else {
        $('#form1').find(':input[type="checkbox"][name*="select"]').each(function() {
          $(this).prop('checked', false);
        });
      };
    });

    $('#form1').find(':input[type="checkbox"][name*="select"]').click(function() {
      var selected_chkboxes = $('#form1').find(':input[type="checkbox"][name*="select"]:checked');
      if (selected_chkboxes.length == $('#form1').find(':input[type="checkbox"]').length - 1) {
        $('#checkall').prop('checked', true);
      } else {
        $('#checkall').prop('checked', false);
      };
    });

    $('#delsectionbtn').click(function() {
      var r = confirm("Το παρών τμήμα πρόκειται να διαγραφεί. Παρακαλώ επιβεβαιώστε.");
      if (r == true) {
        window.open("<?php echo base_url('section/delreg/' . $section['id']); ?>", '_self', false);
      }
      return false;
    });

    $('#selectstdsbox').select2({
      multiple: true,
      // disabled: true,
      minimumInputLength: 2,
      ajax: {
        //THIS URL GETS ALL THE STUDENTS FOR THE CURRENT SCHOOL YEAR. I SHOULD CONSTRICT IT TO SPECIFIC CLASS>COURSE !!!
        url: "<?php echo base_url() ?>section/student_list",
        dataType: 'json',
        data: function(term, page) {
          return {
            q: term //sends the typed letters to the controller
          };
        },
        results: function(data, page) {
          return {
            results: data
          }; //data needs to be {{id:"",text:""},{id:"",text:""}}...
        }
      }
    });


  }) //end of (document).ready(function())
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
          <li><a href="<?php echo base_url() ?>section">Τμήματα</a> </li>
          <li><a href="<?php echo base_url() ?>section/card/<?php echo $section['id']; ?>/">Καρτέλα Τμήματος</a> </li>
          <li class="active">Μαθητές</li>
        </ul>
      </div>

      <p>
        <h3>
          <?php echo $section['section'] . ' / ' . $section['title']; ?>
        </h3>
      </p>


      <ul class="nav nav-tabs">
        <li><a href="<?php echo base_url() ?>section/card/<?php echo $section['id'] ?>">Στοιχεία</a></li>
        <li class="active"><a href="<?php echo base_url() ?>section/card/<?php echo $section['id'] ?>/sectionstudents">Μαθητές</a></li>
      </ul>

      <p></p>


      <div class="row">

        <div class="col-md-12">

          <!-- <div class="alert alert-warning" style="display:none;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <span style="font-family:'Play';font-weight:700;">Θυμηθείτε! </span> Η εισαγωγή μαθητών σε τμήματα γίνεται από την καρτέλα φοίτησης του εκάστοτε μαθητή, επιλέγοντας την ενότητα "Διαχείριση".
          </div> -->


          <div class="row">
            <!-- students in section -->
            <div class="col-md-12" id="group1">
              <div class="mainform">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <span class="icon">
                      <i class="icon-group"></i>
                    </span>
                    <h3 class="panel-title">Μαθητές τμήματος</h3>
                    <?php if (!empty($students)) : ?>
                    <div class="buttons">
                      <button enabled id="editform1" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
                      <a enabled data-trigger="footable_expand_all" class="toggle btn btn-default btn-sm" href="#expandall"><i class="icon-angle-down"></i></a>
                      <a enabled data-trigger="footable_collapse_all" style="display: none" class="toggle btn btn-default btn-sm" href="#collapseall"><i class="icon-angle-up"></i></a>
                    </div>
                    <?php endif;?>
                  </div>
                  <div class="panel-body">
                    <?php if (!empty($students)) : ?>
                      <form id="form1" action="<?php echo base_url() ?>section/card/<?php echo $section['id'] ?>/sectionstudents/" method="post" accept-charset="utf-8" role="form">
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
                            <?php foreach ($students as $student) : ?>
                              <tr>
                                <td><input disabled type="checkbox" name="select[<?php echo $student['id']; ?>]"></td>
                                <td><?php echo $student['surname'] . ' ' . $student['name']; ?></td>
                                <td><?php echo $student['home_tel']; ?></td>
                                <td><?php echo $student['std_mobile']; ?></td>
                                <td><?php echo $student['mothers_name']; ?></td>
                                <td><?php echo $student['mothers_mobile']; ?></td>
                                <td><?php echo $student['fathers_name']; ?></td>
                                <td><?php echo $student['fathers_mobile']; ?></td>
                                <td><?php echo $student['work_tel']; ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                        <button disabled id="submitform1btn" type="button" class="btn btn-danger pull-right">Αφαίρεση από τμήμα</button>
                      </form>
                    <?php else : ?>
                      <div class="alert alert-danger">
                        Δεν έχουν αντιστοιχιστεί μαθητές στο συγκεκριμένο τμήμα.
                      </div>
                    <?php endif; ?>
                  </div> <!-- end of panel body -->
                </div> <!-- end of panel -->
              </div>
            </div>
          </div><!-- end of students section row   -->

          <div class="row">
            <!-- Add new students -->
            <div class="col-md-12" id="group2">
              <div class="mainform">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <span class="icon">
                      <i class="icon-group"></i>
                    </span>
                    <h3 class="panel-title">Προσθήκη μαθητών</h3>
                    <div class="buttons">
                      <button enabled id="editform2" type="button" class="btn btn-default btn-sm <?php if (empty($students)) echo 'active';?>" <?php if (empty($students)) echo 'aria-pressed="true"';?>data-toggle="button"><i class="icon-edit"></i></button>
                    </div>
                  </div>
                  <div class="panel-body">
                    <form id="form2" action="<?php echo base_url() ?>section/card/<?php echo $section['id'] ?>/sectionstudents/" method="post" accept-charset="utf-8" role="form">
                      <div class="form-group">
                        <label for="single" class="control-label">Επιλέξτε μαθητές/μαθήτριες:</label>
                        <input <?php if (!empty($students)) echo 'disabled';?> class="form-control" id="selectstdsbox" type="hidden" name="newstudents" />
                      </div>
                      <button <?php if (!empty($students)) echo 'disabled';?>  id="submitform2btn" type="submit" class="btn btn-primary pull-right">Προσθήκη στο τμήμα</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="btn-group pull-right">
                <a id="delsectionbtn" href="#" class="btn btn-default"><i class="icon-trash"></i></a>
                <a id="newsectionbtn" href="<?php echo base_url(); ?>section/newreg" class="btn btn-default"><i class="icon-plus"></i></a>
              </div>
            </div>
          </div>


          <!-- <div class="row">
            <div class="col-md-12">
              <ul class="pager">
                <li class="previous <?php if (empty($prevnext['prev'])) {
                                      echo 'disabled';
                                    }; ?>" <?php if (empty($prevnext['prev'])) {
                                              echo "onclick='return false;'";
                                            }; ?>><a href="<?php echo base_url('/section/card/' . $prevnext['prev']); ?>"><i class="icon-chevron-left"></i> Προηγούμενο</a></li>
                <li class="next <?php if (empty($prevnext['next'])) {
                                  echo 'disabled';
                                }; ?>" <?php if (empty($prevnext['next'])) {
                                          echo "onclick='return false;'";
                                        }; ?>><a href="<?php echo base_url('/section/card/' . $prevnext['next']); ?>">Επόμενο <i class="icon-chevron-right"></i></a></li>
              </ul>
            </div>
          </div> -->


        </div>
      </div>




    </div>
    <!--end of main container-->

    <div class="push"></div>

  </div> <!-- end of body wrapper-->