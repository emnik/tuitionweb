<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs-3.3.7/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-colvis-1.6.4/b-html5-1.6.4/b-print-1.6.4/fc-3.3.1/r-2.2.6/rg-1.1.2/sl-1.3.1/datatables.min.js"></script>

<!-- For date formating -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>

<script src="//netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap.min.css" rel="stylesheet">

<script type="text/javascript">
  var oTable;
  var selectedIds = [];

  $(document).ready(function() {
    //Menu current active links and Title
    $('#menu-operation').addClass('active');
    $('#menu-teams').addClass('active');
    $('#menu-header-title').text('Microsoft Teams');

    var dataTableOptions = {
      "language": {
          "paginate": {
              "first":    "Πρώτη",
              "previous": "",
              "next":     "",
              "last":     "Τελευταία"
          },
          "info": "Εμφανίζονται οι _START_ έως _END_ από τις _TOTAL_",
          "infoEmpty": "Εμφάνιζονται 0 εγγραφές",
          "infoFiltered": "Φιλτράρισμα από _MAX_ συνολικά εγγραφές",
          "lengthMenu": "Εγγραφές/σελ. _MENU_",
          "loadingRecords": "Φόρτωση καταλόγου ...",
          "processing": "Επεξεργασία...",   
          "search": "Αναζήτηση:"
      }
    };

    // Custom sorting function for the select column
    $.fn.dataTable.ext.order['dom-checkbox'] = function(settings, col) {
      return this.api().column(col, { order: 'index' }).nodes().map(function(td, i) {
        return $('input[type="checkbox"]', td).prop('checked') ? '1' : '0';
      });
    };

    $('#selectDataSrc').on('change', function() {
      $('#warning-alert').addClass('hidden');
      var select = $(this).val();
      var url;
      if (select === 'alldata'){
        url = '<?php echo base_url()?>teams/getAllTeams';
      } else if (select === 'curStudents'){
        url = '<?php echo base_url()?>teams/getCurrentStudents';
      } else if (select === 'olderStudents'){
        url = '<?php echo base_url()?>teams/getObsoleteUsers';
        $('#warning-msg').html('<strong>ΣΗΜΑΝΤΙΚΟ!</strong> Τα ακόλουθα δεδομένα βασίζονται στη σύγκριση των δεδομένων του Microsoft Teams με τους ενεργούς μαθητές και καθηγητές της επιλεγμένης διαχειριστικής περιόδου. <strong>Απαιτείται προσοχή στις διαγραφές</strong>!');
        $('#warning-alert').removeClass('hidden');
      } else if (select === 'curTeachers'){
        url = '<?php echo base_url()?>teams/getCurrentTeachers';
      } else if (select === 'deletedUsers'){
        url = '<?php echo base_url()?>teams/getDeletedUsers';
        $('#warning-msg').html('<strong>ΣΗΜΑΝΤΙΚΟ!</strong> Οι ακόλουθοι λογαριασμοί μπορούν ακόμη να ανακτηθούν');
        $('#warning-alert').removeClass('hidden');
      }
      getRemoteData(url);
    });

    function getRemoteData(selectedUrl){
      $.ajax({
        url: selectedUrl,
        method: 'GET',
        dataType: 'json',
        success: function(remoteData) {
            // console.log(remoteData);
            data=remoteData['data'];
            handleSuccess(data);
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error: ' + status + error);
        }
      });
    }

    function handleSuccess(data) {
      if (oTable) {
        oTable.destroy();
        selectedIds = [];
      }
      oTable = $('#teamsTbl').DataTable({
        data: data,
        processing: true,
        columns: [
          {
            data: "id",
            render: function(data, type, row) {
              return '<input type="checkbox" value="' + data + '">';
            },
            orderable: true,
            orderDataType: 'dom-checkbox'
          },
          { data: "surname" },
          { data: "givenName" },
          { data: "mail" },
          { data: "id" },          
        ],
        order: [[1, 'asc']], // Sort by the "surname" column (index 1) in ascending order
        filter: true,
        paginate: true,
        language: {
          paginate: dataTableOptions.language.paginate,
          info: dataTableOptions.language.info,
          infoEmpty: dataTableOptions.language.infoEmpty,
          infoFiltered: dataTableOptions.language.infoFiltered,
          lengthMenu: dataTableOptions.language.lengthMenu,
          loadingRecords: dataTableOptions.language.loadingRecords,
          processing: dataTableOptions.language.processing,
          search: dataTableOptions.language.search,
          zeroRecords: ''
        }
      });
    }

    // Handle row selection
    $('#teamsTbl tbody').on('change', 'input[type="checkbox"]', function() {
      var id = $(this).val();
      if ($(this).is(':checked')) {
        if (selectedIds.length >= 20) {
          alert('Batch selection is limited to 20 users');
          $(this).prop('checked', false);
        } else {
          selectedIds.push(id);
        }
      } else {
        selectedIds = selectedIds.filter(function(value) {
          return value != id;
        });
      }

      // Update "Select All" checkbox state
      var anyChecked = selectedIds.length > 0;
      $('#select-all').prop('checked', anyChecked);
      $('#select-all').prop('disabled', !anyChecked);
    });

    // Handle "Select All" checkbox
    $('#select-all').on('change', function() {
        var rows = oTable.rows().nodes(); // Get all rows of the table
        $('input[type="checkbox"]', rows).prop('checked', false);
        selectedIds = [];
        $('#select-all').prop('disabled', true);
      // }
    });

    // Initially disable the "Select All" checkbox
    $('#select-all').prop('disabled', true);

    /* Add a click handler for the student-card btn */
    $('#student-card').click(function() {
      if (selectedIds.length === 1) {
        var id = selectedIds[0]; // Assuming the ID is in the first column
        // window.open('student/card/' + id, '_self', false);
        alert(id);
      } else {
        alert("Για τη συγκεκριμένη ενέργεια πρέπει να έχετε επιλέξει ένα μονο χρήστη!");
      }
    });

    $('#reset').click(function() {
      $('body').css('cursor', 'wait');
      $.ajax({
        url: '<?php echo base_url()?>teams/resetTeamsData',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            $('body').css('cursor', 'default');
            console.log(data);
            if (data.status === 'success') {
              alert('Τα δεδομένα των Microsoft Teams λήφθηκαν επιτυχώς.');
              $('#selectDataSrc').val('alldata').trigger('change');
            } else {
              alert('Προέκυψε σφάλμα κατά την επαναφορά των δεδομένων των Microsoft Teams.');
              console.error('Error: ' + data.message);
            }
        },
        error: function(xhr, status, error) {
            $('body').css('cursor', 'default');
            alert('Προέκυψε σφάλμα κατά την επαναφορά των δεδομένων των Microsoft Teams.');
            console.error('AJAX Error: ' + status + error);
        }
      });
    });

    /* Add a click handler for the new-reg btn */
    $('#new-reg').click(function() {
      // window.open('student/newreg', '_self', false);
    });

    /* Add a click handler for the del-reg btn */
    $('#del-reg').click(function() {
      if (selectedIds.length > 0) {
        var r = confirm("Οι χρήστες που επιλέξατε πρόκειται να διαγραφούν. Παρακαλώ επιβεβαιώστε.");
        if (r == true) {
          $('body').css('cursor', 'wait');
          $.ajax({
            url: '<?php echo base_url()?>teams/batchDeleteUsers',
            method: 'POST',
            data: { data: selectedIds },
            dataType: 'json',
            success: function(data) {
                $('body').css('cursor', 'default');
                console.log(data);
                if (data.status === 'success') {
                  alert('Οι χρήστες διαγράφηκαν επιτυχώς.');
                  $('#selectDataSrc').val('alldata').trigger('change');
                  $('#select-all').prop('checked', false);
                  $('#select-all').prop('disabled', true);
                } else {
                  alert('Προέκυψε σφάλμα κατά τη διαγραφή των χρηστών.');
                  console.error('Error: ' + data.message);
                }
            },
            error: function(xhr, status, error) {
                $('body').css('cursor', 'default');
                alert('Προέκυψε σφάλμα κατά τη διαγραφή των χρηστών.');
                console.error('AJAX Error: ' + status + error);
            }
          });          
          // alert('Deleting users with IDs: ' + selectedIds.join(', '));
        }
      } else {
        alert("Δεν έχετε επιλέξει κανένα χρήστη.");
      }
    });

    // On load get all the data
    getRemoteData('<?php echo base_url()?>teams/getAllTeams');

  }); //end of document(ready) function
</script>

</head>

<body>
  <div class="wrapper">

    <!-- Menu start -->
    <?php include __DIR__ . '/include/menu.php'; ?>
    <!-- Menu end -->

    <!-- main container -->
    <div class="container" style="padding-top:10px; padding-bottom:70px;">
      <div>
        <ul class="breadcrumb">
          <li><a href="<?php echo base_url(); ?>"><i class="icon-home"> </i> Αρχική</a></li>
          <li class="active">Microsoft Teams</li>
        </ul>
      </div>

      <div class="panel panel-default">
        <div class="panel-heading">
          <span class="icon">
            <i class="icon-windows"></i>
          </span>
          <h3 class="panel-title">Microsoft Teams</h3>
        </div>
        <div class="panel-body">
          <div class="row">
            <div class="col-md-12">
                <div class="btn-toolbar" role="toolbar" style="margin-bottom:10px;">
                  <button class="btn btn-sm btn-danger pull-left" id="del-reg"><i class="icon-trash"></i></button>
                  <div class="btn-group pull-left">
                    <button class="btn btn-default btn-sm" id="reset" data-toggle="tooltip" title="Ανάκτηση δεδομένων από τον διακομιστή του Microsoft Teams"><i class="icon-refresh"></i> Reset</button>
                    <button class="btn btn-default btn-sm" id="new-reg"><i class="icon-plus"></i></button>
                  </div>
                  <div class="pull-left col-xs-3" style="margin-left: 10px;">
                    <select id="selectDataSrc" class="form-control input-sm">
                      <option value="alldata">Εμφάνιση όλων</option>
                      <option value="curStudents">Ενεργοί μαθητές</option>
                      <option value="curTeachers">Ενεργοί καθηγητές</option>
                      <option value="olderStudents">Παρωχημένοι λογαριασμοί</option>
                      <option disabled>──────────</option>
                      <option value="deletedUsers">Διαγραμμένοι χρήστες</option>
                    </select>
                <div>
              </div>
            </div>
            <button class="btn btn-openpage btn-sm pull-right" id="student-card"><i class="icon-user"> </i>Edit user</button>
          </div>
          <div class="alert alert-danger alert-dismissable hidden" id="warning-alert">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <span id="warning-msg"></span>
            <!-- <strong>ΣΗΜΑΝΤΙΚΟ!</strong> Τα ακόλουθα δεδομένα βασίζονται στη σύγκριση των δεδομένων του Microsoft Teams με τους ενεργούς μαθητές και καθηγητές της επιλεγμένης διαχειριστικής περιόδου. <strong>Απαιτείται προσοχή στις διαγραφές</strong>! -->
          </div>
          <table class="table table-striped table-bordered" id="teamsTbl" width="100%">
            <thead>
              <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th>Επώνυμο</th>
                <th>Όνομα</th>
                <th>Username</th>
                <th>Teams user id</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div> <!-- end of content -->
      </div> <!-- end of contentbox -->
    </div>
    </div>
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
</body>