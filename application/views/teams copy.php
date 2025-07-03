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

    // Custom sorting function for the select column
    $.fn.dataTable.ext.order['dom-checkbox'] = function(settings, col) {
      return this.api().column(col, { order: 'index' }).nodes().map(function(td, i) {
        return $('input[type="checkbox"]', td).prop('checked') ? '1' : '0';
      });
    };

    // Ensure the table element exists
    if ($('#stdbook').length) {
      // Initialize the DataTable
      oTable = $('#stdbook').DataTable({
        "responsive": true,
        "processing": true,
        "paging": true,
        "pageLength": 10, // Number of records per page
        "columnDefs": [
          // { "orderable": false, "targets": [1] }, // Disable sorting on the second column (id)
          { "orderDataType": "dom-checkbox", "targets": 0 } // Custom sorting for the first column (select box)
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
          "infoFiltered": "Φιλτράρισμα από _MAX_ συνολικούς χρήστες",
          "lengthMenu": "_MENU_",
          "loadingRecords": "Φόρτωση καταλόγου...",
          "processing": "Επεξεργασία...",
          "search": "",
          "zeroRecords": "Δεν βρέθηκαν εγγραφές"
        }
      });

      // Handle row selection
      $('#stdbook tbody').on('change', 'input[type="checkbox"]', function() {
        var id = $(this).val();
        if ($(this).is(':checked')) {
          if (!selectedIds.includes(id)) {
            selectedIds.push(id);
          }
        } else {
          selectedIds = selectedIds.filter(function(value) {
            return value != id;
          });
        }
      });

      // Handle "Select All" checkbox
      $('#select-all').on('change', function() {
        var rows = oTable.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
        if (this.checked) {
          $('input[type="checkbox"]', rows).each(function() {
            var id = $(this).val();
            if (!selectedIds.includes(id)) {
              selectedIds.push(id);
            }
          });
        } else {
          $('input[type="checkbox"]', rows).each(function() {
            var id = $(this).val();
            selectedIds = selectedIds.filter(function(value) {
              return value != id;
            });
          });
        }
      });

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

      /* Add a click handler for the new-reg btn */
      $('#new-reg').click(function() {
        // window.open('student/newreg', '_self', false);
      });

      /* Add a click handler for the del-reg btn */
      $('#del-reg').click(function() {
        if (selectedIds.length > 0) {
          var r = confirm("Οι χρήστες που επιλέξατε πρόκειται να διαγραφούν. Παρακαλώ επιβεβαιώστε.");
          if (r == true) {
            // Perform the delete operation
            // Example: window.open('student/delreg/' + selectedIds.join(','), '_self', false);
            alert('Deleting users with IDs: ' + selectedIds.join(', '));
          }
        } else {
          alert("Δεν έχετε επιλέξει κανένα χρήστη.");
        }
      });

      <?php if (empty($teams)): ?>
        $('#myModal').modal('show');
      <?php endif; ?>
    } else {
      console.error("Table element with ID 'stdbook' not found.");
    }
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
                  <button class="btn btn-default btn-sm"><i class="icon-refresh"></i></button>
                  <button class="btn btn-default btn-sm" id="new-reg"><i class="icon-plus"></i></button>
                </div>
                <button class="btn btn-sm btn-openpage pull-right" id="student-card"><i class="icon-user"> </i>Reset Teams Data</button>
              </div>
            </div>
          </div>
          <table class="table table-striped table-bordered" id="stdbook" width="100%">
            <thead>
              <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th>id</th>
                <th>Όνομα</th>
                <th>Επώνυμο</th>
                <th>Username</th>
                <th>Ομάδα</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($teams)): ?>
                <?php foreach ($teams as $data): ?>
                  <tr>
                    <td><input type="checkbox" value="<?php echo $data['id']; ?>"></td>
                    <td><?php echo $data['id']; ?></td>
                    <td><?php echo $data['givenName']; ?></td>
                    <td><?php echo $data['surname']; ?></td>
                    <td><?php echo $data['mail']; ?></td>
                    <td><?php echo $data['jobTitle']; ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
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
</body>