<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap.min.js"></script>

<link href="<?php echo base_url('assets/css/dataTables.bootstrap.css') ?>" rel="stylesheet">

<script type="text/javascript">
  /* Table initialisation */

  var oTable;
  var asInitVals = new Array(); //for specific columns filtering with input field below

  $(document).ready(function() {

    //Menu current active links and Title
    $('#menu-operation').addClass('active');
    $('#menu-student').addClass('active');
    $('#menu-header-title').text('Μαθητολόγιο');

    /* Add/remove class to a row when clicked on */
    $('#stdbook tbody tr').click(function(e) {
      if ($(this).hasClass('row_selected')) {
        $(this).removeClass('row_selected');
      } else {
        oTable.$('tr.row_selected').removeClass('row_selected');
        $(this).addClass('row_selected');
      }
    });

    /* Add a click handler for the student-card btn */
    $('#student-card').click(function() {
      var anSelected = fnGetSelected(oTable);
      if (anSelected.length !== 0) {
        var aRow = anSelected[0];
        // var id=oTable.fnGetData( aRow, 0 );
        var id = oTable.row(aRow).data()[0];
        window.open('student/card/' + id, '_self', false);
        // alert(id);
      } else {
        alert("Δεν έχετε επιλέξει κανένα μαθητη.");
      }
    });

    /* Add a click handler for the new-reg btn */
    $('#new-reg').click(function() {
      window.open('student/newreg', '_self', false);
    });

    /* Add a click handler for the del-reg btn */
    $('#del-reg').click(function() {
      var anSelected = fnGetSelected(oTable);
      if (anSelected.length !== 0) {
        var r = confirm("Ο μαθητής που επιλέξατε πρόκειται να διαγραφεί. Παρακαλώ επιβεβαιώστε.");
        if (r == true) {
          var aRow = anSelected[0];
          // var id=oTable.fnGetData( aRow, 0 );
          var id = oTable.row(aRow).data()[0];
          window.open('student/delreg/' + id, '_self', false);
        }
      } else {
        alert("Δεν έχετε επιλέξει κανένα μαθητη.");
      }
    });


    // Code below will be used for resubscribe BUT I need to code a modal to ask at which term the registration will be!!!

    // /* Add a click handler for the del-reg btn */
    // $('#del-reg').click(function() {
    //   var anSelected = fnGetSelected(oTable);
    //   if (anSelected.length !== 0) {
    //     var r = confirm("Ο μαθητής που επιλέξατε πρόκειται να διαγραφεί. Παρακαλώ επιβεβαιώστε.");
    //     if (r == true) {
    //       var aRow = anSelected[0];
    //       // var id=oTable.fnGetData( aRow, 0 );
    //       var id = oTable.row(aRow).data()[0];
    //       window.open('student/delreg/' + id, '_self', false);
    //     }
    //   } else {
    //     alert("Δεν έχετε επιλέξει κανένα μαθητη.");
    //   }
    // });


    /* Init the table */
    oTable = $('#stdbook').DataTable({
      "responsive": true,
      // "sDom": "<'row'<'col-xs-6 pull-left' l><'col-xs-6 pull-right' f> r><'row'<'col-md-12't>><'row'<'col-md-6'i><'col-md-6'p>>",
      // "sPaginationType": "bootstrap",
      "columnDefs": [{
          "visible": false,
          "targets": [0, 1]
        }, //hide id and mathitologio columns
        {
          "searchable": true,
          "targets": [4, 5]
        } //don't filter class name and course
        //they will be filtered via input boxes in the table footer!
      ],
      "language": {
        "paginate": {
          "first": "Πρώτη",
          "previous": "",
          "next": "",
          "last": "Τελευταία"
        },
        "info": "Εμφανίζονται οι _START_ έως _END_ από τους _TOTAL_ μαθητές",
        "infoEmpty": "Εμφάνιζονται 0 εγγραφές",
        "infoFiltered": "Φιλτράρισμα από _MAX_ συνολικούς μαθητές",
        //"lengthMenu": "_MENU_ μαθητές ανά σελίδα",
        "lengthMenu": "_MENU_",
        "loadingRecords": "Φόρτωση μαθητολογίου...",
        "processing": "Επεξεργασία...",
        //"search": "Εύρεση μαθητή:",
        "search": "",
        "zeroRecords": "Δεν βρέθηκαν εγγραφές"
      }

    });

    <?php if (!$students) : ?>
      $('#myModal').modal('show');
    <?php endif; ?>

    //bootstrap3 style enchancements! 

    $('#stdbook_filter').find('input').addClass("form-control");
    $('#stdbook_filter label').contents().unwrap();
    var fgroupDiv = document.createElement('div');
    fgroupDiv.id = "fgroupDiv";
    fgroupDiv.className = 'form-group pull-right';
    $('#stdbook_filter').append(fgroupDiv);
    $('#stdbook_filter').find('input').prependTo('#fgroupDiv');
    $('#stdbook_filter').find('input').attr('id', 'inputid');
    $('#stdbook_filter').find('input').css({
      'margin-bottom': '10px'
    });
    var $searchlabel = $("<label>").attr('for', "#inputid");
    $searchlabel.css({
      'margin-top': '0px',
      'margin-bottom': '5px',
      'margin-left': '0px',
      'margin-right': '10px'
    })
    $searchlabel.addClass('pull-left');
    $searchlabel.text('Αναζήτηση:');
    $searchlabel.insertBefore('#inputid');

    $('#stdbook_length').find('select').addClass("form-control");
    $('#stdbook_length label').contents().unwrap();
    var lgroupDiv = document.createElement('div');
    lgroupDiv.id = "lgroupDiv";
    lgroupDiv.className = 'form-group pull-left';
    var innerlgroupDiv = document.createElement('div');
    innerlgroupDiv.id = "innerlgroupDiv"
    innerlgroupDiv.className = 'clearfix';
    $('#stdbook_length').append(lgroupDiv);
    $('#lgroupDiv').append(innerlgroupDiv);
    $('#stdbook_length').find('select').prependTo('#innerlgroupDiv');
    $('#stdbook_length').find('select').attr('id', 'selectid');
    $('#stdbook_length').find('select').css({
      'max-width': '75px'
    });
    var $sellabel = $("<label>").attr('for', "#selectid");
    $sellabel.css({
      'min-width': '110px',
      'margin-top': '5px'
    });
    $sellabel.text('Μαθητές/σελ.: ');
    $sellabel.insertBefore('#selectid');



    // HIDING COLUMNS FOR RESPONSIVE VIEW:

    $(window).on("load", resizeWindow);
    //If the User resizes the window, adjust the #container height
    $(window).on("resize", resizeWindow);

    function resizeWindow(e) {
      var newWindowWidth = $(window).width();

      if (newWindowWidth > 1024) {
        // oTable.fnSetColumnVis( 1, true );
        // oTable.fnSetColumnVis( 4, true );
        oTable.column(4).visible(true);
        oTable.column(5).visible(true);
        // oTable.fnSetColumnVis( 5, true );
      } else if ((newWindowWidth >= 600) && (newWindowWidth <= 1024)) {
        // oTable.fnSetColumnVis( 1, true );
        // oTable.fnSetColumnVis( 4, true );
        // oTable.fnSetColumnVis( 5, false );
        oTable.column(4).visible(true);
        oTable.column(5).visible(false);
      } else if ((newWindowWidth >= 440) && (newWindowWidth < 600)) {
        // oTable.fnSetColumnVis( 1, true );
        // oTable.fnSetColumnVis( 4, false );
        // oTable.fnSetColumnVis( 5, false );
        oTable.column(4).visible(false);
        oTable.column(5).visible(false);
      } else if (newWindowWidth < 440) {
        // oTable.fnSetColumnVis( 1, false );
        // oTable.fnSetColumnVis( 4, false );
        // oTable.fnSetColumnVis( 5, false );
        oTable.column(4).visible(false);
        oTable.column(5).visible(false);
      }

    };

    //INDIVIDUAL COLUMN FILTERING
    //To filter individual columns we add the input keys to the table footer (see table code)

    $("tfoot input").keyup(function() {
      /* Filter on the column (the index) of this element */
      //oTable.fnFilter( this.value, $("tfoot input").index(this)+4);//+4 is needed for getting the right  column index because I don't have input in every column!!! 
      oTable.column($("tfoot input").index(this) + 4).search(this.value).draw();
    });

    /*
     * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
     * the footer
     */
    // $("tfoot input").each(function(i) {
    //   asInitVals[i] = this.value;
    // });

    // $("tfoot input").focus(function() {
    //   if (this.className == "search_init form-control") {
    //     this.className = "form-control";
    //     this.value = "";
    //   }
    // });

    // $("tfoot input").blur(function(i) {
    //   if (this.value == "") {
    //     this.className = "search_init form-control";
    //     this.value = asInitVals[$("tfoot input").index(this)];
    //   }
    // });

    // $('li.dash').click(function() {
    //   $('#footerModal').modal();
    // });


  }); //end of document(ready) function



  function fnGetSelected(oTableLocal) {
    return oTableLocal.$('tr.row_selected');
  }
</script>


</head>

<body>
  <div class="wrapper">

        <!-- Menu start -->
        <?php include(__DIR__ .'/include/menu.php');?>
        <!-- Menu end -->

    <!-- main container
================================================== -->

    <div class="container" style="padding-top:10px; padding-bottom:70px;">

      <div>
        <ul class="breadcrumb">
          <li><a href="<?php echo base_url() ?>"><i class="icon-home"> </i> Αρχική</a></li>
          <li class="active">Μαθητολόγιο</li>
          <!-- <li class="dash" style><i class="icon-dashboard icon-small"></i></li> -->
        </ul>
      </div>


      <div class="panel panel-default">
        <div class="panel-heading">
          <span class="icon">
            <i class="icon-book"></i>
          </span>
          <h3 class="panel-title">Μαθητολόγιο</h3>
        </div>
        <div class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <div class="btn-toolbar" role="toolbar" style="margin-bottom:10px;">
                <button class="btn btn-sm btn-danger pull-left" id="del-reg"><i class="icon-trash"></i></button>
                <div class="btn-group pull-left">
                  <button class="btn btn-default btn-sm"><i class="icon-refresh"></i></button>
                  <button class="btn btn-default btn-sm" id="new-reg"><i class="icon-plus"></i></button>
                </div>
                <button class="btn btn-sm btn-openpage pull-right" id="student-card"><i class="icon-user"> </i> Καρτέλα Μαθητή</button>
              </div>
            </div>
          </div>
          <!--width="100%" option in the table is required when there are hidden columns in the table to resize properly on window change-->
          <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="stdbook" width="100%">
            <thead>
              <tr>
                <th>id</th>
                <th>Αρ.Μαθητολογίου</th>
                <th>Επώνυμο</th>
                <th>Όνομα</th>
                <th>Τάξη</th>
                <th>Κατεύθυνση</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($students) : ?>
                <?php foreach ($students as $data) : ?>
                  <tr>
                    <td><?php echo $data["id"]; ?></td>
                    <td><?php echo $data["std_book_no"]; ?></td>
                    <td><?php echo $data["surname"]; ?></td>
                    <td><?php echo $data["name"]; ?></td>
                    <td><?php echo $data["class_name"]; ?></td>
                    <td><?php echo $data["course"]; ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
            <!-- <tfoot>
              <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th><input type="text" class="search_init form-control" name="search_classnames" value="Φίλτρο τάξεων" class="search_init" /></th>
                <th><input type="text" class="search_init form-control" name="search_coursenames" value="Φίλτρο κατευθύνσεων" class="search_init" /></th>
              </tr>
            </tfoot> -->
          </table>
        </div> <!-- end of content -->
      </div> <!-- end of contentbox -->
    </div>
    <!--end of main container-->

    <div class="push"></div>
  </div> <!-- end of body wrapper-->

  <!-- Modal -->
  <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h3 id="myModalLabel">Κενό Μαθητολόγιο</h3>
        </div>
        <div class="modal-body">
          <p>Δέν έχετε εισάγει καμία εγγραφή στο μαθητολόγιο για το σχολικό έτος που επιλέξατε.
            Μπορείτε είτε να προχωρήσετε σε μια νέα εγγραφή, είτε να επιστρέψετε στην αρχική σελίδα και
            να επιλέξετε ένα προηγούμενο σχολικό έτος για επανεγγραφή παλαιοτέρων μαθητών.</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Κλείσιμο</button>
          <a href="<?php echo base_url(); ?>" class="btn btn-default">Επιστροφή στην αρχ. σελίδα</a>
          <a href="#" class="btn btn-primary">Νέα εγγραφή</a>
        </div>
      </div>
    </div>
  </div>