<style>
  .thumbnails {
    border: none;
  }

  .thumbnail {
    border: none;
  }
</style>

<script type="text/javascript">
  var addedyear = false;
  $(document).ready(function() {
    
    //Menu current active links and Title
    $('#menu-operation').addClass('active');
    $('#menu-header-title').text('<?php echo $school['distinctive_title']?>');

    // $('.group2').toggle();
    // $('.group2').parent().parent().siblings().toggle();

    $('#schoolyearcombo').on('change', function() {
      // var sel = $(this).find('option:selected').val();
      // var prevpos = $(this).find('option:selected').prevAll().size() - 1;
      // var prevval = $(this).find('option:eq(' + prevpos + ")").val();
      // if (sel == "addnextschoolyear") {
      //   if (addedyear == false) {
      //     addedyear = true;
      //     var o = new Option((parseInt(prevval, 10) + 1) + "-" + (parseInt(prevval, 10) + 2), parseInt(prevval, 10) + 1);
      //     $(this).find('option:selected').before(o);
      //     $(this).val(parseInt(prevval, 10) + 1);
      //   } else {
      //     alert("Δεν μπορείτε να προσθέσετε περισσότερα του ενός έτη τη φορά.")
      //     $(this).val(parseInt(prevval, 10));
      //   }
      // };
      // console.log(sel);
      $('form[name="main"]').submit();
    });

    $('.welcome-title').click(function() {
      $(this).find('.icon').toggle();
      $(this).parent().siblings().toggle();
    });

  });
</script>

</head>

<body>
  <div class="wrapper">
    <!--body wrapper for css sticky footer-->

            <!-- Menu start -->
            <?php include(__DIR__ .'/include/menu.php');?>
        <!-- Menu end -->

    <!-- main container
================================================== -->
    <div class="container">

      <div class="row">
        <!--main row-->

        <div class="col-md-4">
          <!-- form container -->

          <form name="main" class="form" action="<?php echo base_url('welcome');?>" method="post" accept-charset="utf-8">
            <fieldset>
              <legend>Διαχειριστική Περίοδος</legend>
              <div class="form-horizontal">
              <div class="control-group col-xs-10" style="padding-left: 0px;">
                <label class="control-label">Επιλέξτε: </label>
                  <select class="form-control input-md" name="startschoolyear" id="schoolyearcombo">
                    <?php foreach($schoolyears as $data):?>
                      <option value="<?php echo $data['id'];?>" <?php if($data['active']==1) echo "selected = 'selected'";?>>
                        <?php echo $data['name'];?>
                      </option>                        
                    <?php endforeach;?>
                      }
                    <!-- <option value="addnextschoolyear">Προσθήκη επόμενου σχ. έτους</option> -->
                  </select>
              </div>
              <div class="control-group col-xs-2" style="margin-top: 26px;" >
              <button type="submit" name="submitbtn" value="submit0" class="btn btn-default btn-md pull-right" id="editermsbtn"><i class="icon icon-cogs"></i></button>
              </div>
              </div>
            </fieldset>

        </div>
        <!--end of form container-->

        <div class="col-md-8">
          <!-- submit buttons -->

          <div class="row">
            <!--first row-->
            <div class="col-xs-12">
              <div class="welcome-title">
                <span class="icon group1"><i class="icon-minus"></i></span>
                <span class="icon group1" style="display:none"><i class="icon-plus"></i></span>
                Λειτουργία Φροντιστηρίου
              </div>
            </div>
            <div class="col-sm-3 col-xs-6 welcome">
              <button type="submit" class="btn-link" name="submitbtn" value="submit1">
                <i class="icon-group icon-4x"></i>
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
              <!-- <div class="col-sm-3 col-sm-pull-6 col-xs-6 welcome"> -->
              <button type="submit" class="btn-link" name="submitbtn" value="submit10">
                <i class="icon-calendar icon-4x"></i>
                <h4>Πρόγραμμα</h4>
              </button>
              <div class="small">
                Ημερήσιο Πρόγραμμα
              </div>
            </div>

            <div class="clearfix visible-xs"></div>
            
            <div class="col-sm-3 col-xs-6 welcome">
              <button type="submit" class="btn-link" name="submitbtn" value="submit12">
                <i class="icon-pencil icon-4x"></i>
                <h4>Διαγωνίσματα</h4>
              </button>
              <div class="small">
                Προγραμματισμός /
                Επιτηρητές
              </div>
            </div>


            <!-- <div class="col-sm-3 col-xs-6 welcome">
              <button disabled type="submit" class="btn-link" name="submitbtn" value="submit8">
                <i class="icon-paper-clip icon-4x"></i>
                <h4>Αρχεία</h4>
              </button>
              <div class="small">
                    Διαχείριση αρχείων
              </div>
            </div> -->

            <div class="col-sm-3 col-xs-6 welcome">
              <button disabled type="submit" class="btn-link" name="submitbtn" value="submit9">
                <i class="icon-bullhorn  icon-4x"></i>
                <h4>Ανακοινώσεις</h4>
              </button>
              <div class="small">
                Προς υποσύστημα καθηγητών /
                γονέων / μαθητών
              </div>
            </div>


          </div>
          <!--end of first row-->

          <div class="row">
            <!--second row-->

            <div class="col-xs-12">
              <div class="welcome-title">
                <span class="icon group2"><i class="icon-minus"></i></span>
                <span class="icon group2" style="display:none"><i class="icon-plus"></i></span>
                Οργάνωση / Διαχείριση φροντιστηρίου
              </div>
            </div>

            <div class="col-sm-3 col-xs-6 welcome" >
              <button type="submit" class="btn-link" name="submitbtn" value="submit2">
                <i class="icon-user icon-4x"></i>
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
                <i class="icon-tags icon-4x"></i>
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
                <i class="icon-sitemap icon-4x"></i>
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
                <i class="icon-building icon-4x"></i>
                <h4>Στοιχεία Φροντιστηρίου</h4>
              </button>
              <div class="small">
                Στοιχεία Φροντιστηρίου / Φορολογικά / Διαδύκτιο
              </div>
            </div>

          </div>
          <!--end of second row-->

          <div class="row" style="margin-bottom:20px;">
            <!--third row-->
            <div class="col-xs-12">
              <div class="welcome-title">
                <span class="icon group3"><i class="icon-minus"></i></span>
                <span class="icon group3" style="display:none"><i class="icon-plus"></i></span>
                Συγκεντρωτικές αναφορές
              </div>
            </div>

            <div class="col-sm-3 col-xs-6 welcome" >
              <button type="submit" class="btn-link" name="submitbtn" value="submit9">
                <i class="icon-copy icon-4x"></i>
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
                <i class="icon-time icon-4x"></i>
                <h4>Ιστορικό</h4>
              </button>
              <div class="small">
                Απουσιών /
                ΑΠΥ
              </div>
            </div>

            <div class="clearfix visible-xs"></div>

            <div class="col-sm-3 col-xs-6 welcome">
              <button type="submit" class="btn-link" name="submitbtn" value="submit7">
                <i class="icon-phone icon-4x"></i>
                <h4>Τηλ. Κατάλογοι</h4>
              </button>
              <div class="small">
                Μαθητών /
                Προσωπικού /
                Ομαδικά SMS / Επαφές Google
              </div>
            </div>

            <div class="col-sm-3 col-xs-6 welcome" >
              <button type="submit" class="btn-link" name="submitbtn" value="submit4">
                <i class="icon-money icon-4x"></i>
                <h4>Οικονομικά</h4>
              </button>
              <div class="small">
                Σύνοψη /
                Οφειλές ανά μήνα /
                Οφειλες ανα σύνολο μηνών
              </div>
            </div>

          </div>
          <!--end of third row-->

        </div> <!-- end of submit buttons -->
        </form>

      </div>
      <!--end of main row-->




    </div>
    <!--end of container -->
    <div class="push"></div>
  </div> <!-- end of body wrapper -->
</body>