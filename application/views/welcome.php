<script type="text/javascript">
  $(document).ready(function() {
    
    //Menu current active links and Title
    $('#menu-operation').addClass('active');
    $('#menu-header-title').text('<?php echo $school['distinctive_title']; ?>');

    // $('.group2, .group3 .group4').toggle();
    // $('.group2, .group3 .group4').parent().next().toggle();

    $('#schoolyearcombo').on('change', function() {
      $('form[name="main"]').submit();
    });

    // $('.welcome-section-title:not(.no-select)').click(function() {
    //   $(this).next().toggle();
    //   $(this).find('.icon').toggle();
    // });

    $('.welcome-section-title:not(.no-select)').click(function () {
    // Collapse all sections except the one that was clicked
    $('.welcome-section-title:not(.no-select)').not(this).next('.welcome-section-body').hide();
    $('.welcome-section-title:not(.no-select)').not(this).find('.icon-plus').show();
    $('.welcome-section-title:not(.no-select)').not(this).find('.icon-minus').hide();

    // Toggle the clicked section
    $(this).next('.welcome-section-body').toggle();
    $(this).find('.icon-plus').toggle();
    $(this).find('.icon-minus').toggle();
  });

  });
</script>

</head>

<body>
  <div class="wrapper">
    <!--body wrapper for css sticky footer-->

            <!-- Menu start -->
            <?php include __DIR__ . '/include/menu.php'; ?>
        <!-- Menu end -->

    <!-- main container
================================================== -->
    <div class="container">

      <div class="row">
        <!--main row-->

        <div class="col-md-4">
          <!-- form container -->
          <div class="row">
            <div class="col-xs-12">
            <form name="main" class="form" action="<?php echo base_url('welcome'); ?>" method="post" accept-charset="utf-8">
              <fieldset>
                <div class="welcome-section-title no-select">
                  Διαχειριστική Περίοδος
                </div>

                <div class="col-xs-12 welcome-section-body manage-period">
                <div class="form-inline">
                <p class="form-control-static"> Επιλέξτε:</p>
                  <div class="form-group" >
                    <!-- <label class="control-label">Επιλέξτε: </label> -->
                      <select class="form-control input-md" name="startschoolyear" id="schoolyearcombo">
                        <?php foreach ($schoolyears as $data) { ?>
                          <option value="<?php echo $data['id']; ?>" <?php if (1 == $data['active']) {
  echo "selected = 'selected'";
} ?>>
                            <?php echo $data['name']; ?>
                          </option>                        
                        <?php } ?>
                          }
                      </select>
                        </div>
                  <div class="form-group">
                    <button type="submit" name="submitbtn" value="submit0" class="btn btn-danger btn-md pull-right" id="editermsbtn"><i class="icon icon-edit"></i></button>
                  </div>
                        </div>
              </fieldset>
            </div> 
          </div> <!--end of row-->
        </div> <!--end or col-md-4-->
  
        <div class="col-md-7 col-md-offset-1">
          <!-- submit buttons -->
          <div class="row">
            <div class="col-xs-12">
            <!--first row-->
            <div class="row">
              <div class="col-xs-12">
                <div class="welcome-section-title">
                  <i class="icon-minus"></i>
                  <i class="icon-plus" style="display:none"></i>
                    Λειτουργία Φροντιστηρίου
                </div>

                <div class="col-xs-12 welcome-section-body">    
                  <div class="col-sm-3 col-xs-6 welcome">
                    <button type="submit" class="btn-link" name="submitbtn" value="submit1">
                      <i class="icon-group icon-3x"></i>
                      <h4>Μαθητολόγιο</h4>
                    </button>
                    <div class="small">
                      Στοιχεία /
                      Επικοινωνία /
                      Φοίτηση /
                      Οικονομικά
                    </div>
                  </div>

                 <div class="col-sm-3 col-xs-6 welcome">
                  <button type="submit" class="btn-link" name="submitbtn" value="submit10">
                    <i class="icon-calendar icon-3x"></i>
                    <h4>Πρόγραμμα</h4>
                  </button>
                  <div class="small">
                    Ημερήσιο Πρόγραμμα
                  </div>
                 </div>

                 <div class="clearfix visible-xs"></div>
            
                 <div class="col-sm-3 col-xs-6 welcome">
                  <button type="submit" class="btn-link" name="submitbtn" value="submit12">
                    <i class="icon-pencil icon-3x"></i>
                    <h4>Διαγωνίσματα</h4>
                  </button>
                  <div class="small">
                    Προγραμματισμός /
                    Επιτηρητές
                  </div>
                 </div>


                <!-- <div class="col-sm-3 col-xs-6 welcome">
                  <button disabled type="submit" class="btn-link" name="submitbtn" value="submit8">
                    <i class="icon-paper-clip icon-3x"></i>
                    <h4>Αρχεία</h4>
                  </button>
                  <div class="small">
                        Διαχείριση αρχείων
                  </div>
                </div> -->

                <!-- <div class="col-sm-3 col-xs-6 welcome">
                  <button disabled type="submit" class="btn-link" name="submitbtn" value="submit9">
                    <i class="icon-bullhorn  icon-3x"></i>
                    <h4>Ανακοινώσεις</h4>
                  </button>
                  <div class="small">
                    Προς υποσύστημα καθηγητών /
                    γονέων / μαθητών
                  </div>
                </div> -->

                <div class="col-sm-3 col-xs-6 welcome">
                  <button type="submit" class="btn-link" name="submitbtn" value="submit15">
                    <i class="icon-comments-alt  icon-3x"></i>
                    <h4>Επικοινωνία</h4>
                  </button>
                  <div class="small">
                    Αποστολή μηνυμάτων SMS /
                    Λίστα Ηλ. Ταχυδρομείου
                  </div>
                </div>                
              
                <div class="row"> <!--Second row inside the first group-->
                  <div class="col-xs-12 welcome-section-body">    
                    <div class="col-sm-3 col-xs-6 welcome">
                      <button type="submit" class="btn-link" name="submitbtn" value="submit16">
                        <i class="icon-windows icon-3x"></i>
                        <h4>Microsoft Teams</h4>
                      </button>
                      <div class="small">
                        Διαχείριση χρηστών
                      </div>
                    </div>
                  </div>
                </div>

              </div>




              
            </div>
          </div>
          <!--end of first row-->

          <div class="row">
            <!--second row-->

            <div class="col-xs-12">
              <div class="welcome-section-title">
                <i class="icon-minus" style="display:none"></i>
                <i class="icon-plus"></i>
                 Οργάνωση / Διαχείριση φροντιστηρίου
              </div>
              <div class="col-xs-12 welcome-section-body" style="display:none;">  
            <!-- </div> -->

            <div class="col-sm-3 col-xs-6 welcome" >
              <button type="submit" class="btn-link" name="submitbtn" value="submit2">
                <i class="icon-user icon-3x"></i>
                <h4>Προσωπικό</h4>
              </button>
              <div class="small">
                Προφίλ /
                Πλάνο διδασκαλίας /
                Βαθμολόγιο
              </div>
            </div>

            <div class="col-sm-3 col-xs-6 welcome" >
              <button type="submit" class="btn-link" name="submitbtn" value="submit3">
                <i class="icon-tags icon-3x"></i>
                <h4>Τμήματα</h4>
              </button>
              <div class="small">
                Στοιχεία /
                Πρόγραμμα /
                Μαθητές
              </div>
            </div>

            <div class="clearfix visible-xs"></div>

            <div class="col-sm-3 col-xs-6 welcome" >
              <button type="submit" class="btn-link" name="submitbtn" value="submit5">
                <i class="icon-sitemap icon-3x"></i>
                <h4>Πρόγραμμα σπουδών</h4>
              </button>
              <div class="small">
                Κατευθύνσεις /
                Μαθήματα /
                Διδάσκοντες
              </div>
            </div>


            <div class="col-sm-3 col-xs-6 welcome" >
              <button type="submit" class="btn-link" name="submitbtn" value="submit11">
                <i class="icon-building icon-3x"></i>
                <h4>Στοιχεία Φροντιστηρίου</h4>
              </button>
              <div class="small">
                Στοιχεία Φροντιστηρίου / Φορολογικά / Διαδύκτιο
              </div>
            </div>
                          </div>
                    </div>
                          </div>
          <!--end of second row-->

          <div class="row">
            <!--third row-->
            <div class="col-xs-12">
              <div class="welcome-section-title">
                <i class="icon-minus" style="display:none"></i>
                <i class="icon-plus"></i>
                 Συγκεντρωτικές αναφορές
              </div>
            <!-- </div> -->
            <div class="col-xs-12 welcome-section-body" style="display:none;">  
            <div class="col-sm-3 col-xs-6 welcome" >
              <button type="submit" class="btn-link" name="submitbtn" value="submit9">
                <i class="icon-copy icon-3x"></i>
                <h4>Αναφορές</h4>
              </button>
              <div class="small">
                Αρ.μαθητών ανά τάξη /
                ανα μάθημα /
                Διδάσκοντες ανά μαθητή / 
                Λίστα μαθητών
              </div>
            </div>


            <div class="col-sm-3 col-xs-6 welcome" >
              <button type="submit" class="btn-link" name="submitbtn" value="submit8">
                <i class="icon-time icon-3x"></i>
                <h4>Ιστορικό</h4>
              </button>
              <div class="small">
                Απουσιών /
                ΑΠΥ /
                Ηλ.Ταχυδρομείου / 
                SMS
              </div>
            </div>

            <div class="clearfix visible-xs"></div>

            <div class="col-sm-3 col-xs-6 welcome">
              <button type="submit" class="btn-link" name="submitbtn" value="submit7">
                <i class="icon-phone icon-3x"></i>
                <h4>Τηλέφωνα</h4>
              </button>
              <div class="small">
                Τηλεφωνικοί Κατάλογοι
                Μαθητών /
                Προσωπικού /
                Επαφές Google
              </div>
            </div>

            <div class="col-sm-3 col-xs-6 welcome" >
              <button type="submit" class="btn-link" name="submitbtn" value="submit4">
                <i class="icon-money icon-3x"></i>
                <h4>Οικονομικά</h4>
              </button>
              <div class="small">
                Σύνοψη /
                Οφειλές ανά μήνα /
                Οφειλες ανα σύνολο μηνών
              </div>
            </div>
            <!-- </div> -->
          </div>
          <!--end of third row-->





          <div class="row" style="margin-bottom:20px;">
            <!--forth row-->
            <div class="col-xs-12">
              <div class="welcome-section-title">
                <i class="icon-minus" style="display:none"></i>
                <i class="icon-plus"></i>
                 Διαχείριση εφαρμογής / Ρυθμίσεις
              </div>
            <!-- </div> -->
            <div class="col-xs-12 welcome-section-body" style="display:none;">  
            <div class="col-sm-3 col-xs-6 welcome" >
              <button type="submit" class="btn-link" name="submitbtn" value="submit13">
                <i class="icon-shield icon-3x"></i>
                <h4>Λογαριασμοί χρηστών</h4>
              </button>
              <div class="small">
                Λογαριασμοί πρόσβασης χρηστών
              </div>
            </div>     
            
            <div class="col-sm-3 col-xs-6 welcome" >
              <button type="submit" class="btn-link" name="submitbtn" value="submit14">
                <i class="icon-puzzle-piece icon-3x"></i>
                <h4>Contact Services Configuration</h4>
              </button>
              <div class="small">
                Ρυθμίσεις για αποστολή SMS / Email / ...
              </div>
            </div>            

            <div class="col-sm-3 col-xs-6 welcome" >
              <button disabled type="submit" class="btn-link" name="submitbtn" value="submit15">
                <i class="icon-book icon-3x"></i>
                <h4>GDPR</h4>
              </button>
              <div class="small">
                Ενέργειες GDPR / Αποστολή / Διαγραφή / ... αποθηκευμένων δεδομένων
              </div>
            </div>                
            
          </div>


                          </div>
                          </div>
          </div>
          <!--end of forth row-->
                          </div>
                          </div>


        </div> <!-- end of submit buttons -->
        </form>

      </div>
      <!--end of main row-->



                        </div>
    </div>
    <!--end of container -->
    <div class="push"></div>
    </div> <!-- end of body wrapper -->
    </body>