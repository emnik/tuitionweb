<link href="<?php echo base_url('assets/tabletools/css/TableTools.css') ?>" rel="stylesheet">

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs-3.3.7/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-colvis-1.6.4/b-html5-1.6.4/b-print-1.6.4/fc-3.3.1/r-2.2.6/rg-1.1.2/sl-1.3.1/datatables.min.js"></script>

<!-- For date formating -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>

<script src="//netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap.min.css" rel="stylesheet">

<!-- <style type="text/css">
  .dataTables_processing{padding-left: 16px;}
  .dtrg-group.dtrg-start{background-color:lightgray;}

</style> -->

<script type="text/javascript">

$(document).ready(function(){ 

  //Menu current active links and Title
  $('#menu-reports-summary').addClass('active');
  $('#menu-history').addClass('active');
  $('#menu-header-title').text('Ιστορικό');  
  
  var recipientsTable;
  $.ajax({
      url: '<?php echo base_url()?>history/getsmshistorydata',
      method: 'GET',
      dataType: 'json',
      success: function(data) {
          data=data['aaData'];
          console.log(data);
          handleSuccess(data);
          data.forEach(function(row) {
            var recipients = JSON.parse(row.recipients);
            var id = row.id;
            $('#recipients-' + id).on('click', function() {
                // console.log('Recipients clicked:', id, recipients);
                var recipientsTableBody = $('#recipients-table-body');
                recipientsTableBody.empty();
                if (Array.isArray(recipients)) {
                    recipients.forEach(function(recipient) {
                        var row = '<tr>' +
                                  '<td>' + recipient.firstname + '</td>' +
                                  '<td>' + recipient.phone + '</td>' +
                                  '</tr>';
                        recipientsTableBody.append(row);
                    });
                } else {
                    console.error('Recipients data is not an array:', recipients);
                }
                recipientsTable =$('#recipientsTable').DataTable({
                    destroy: true, // Destroy any existing table instance
                    data: recipients, // Set the data property with the recipients array
                    dom: 'lfBtip',
                    scrollX:true,
                    // sort: false,
                    buttons: dataTableButtons,
                    columns: [
                        { "data": "firstname" },
                        { "data": "phone" },
                    ],
                    paging: true, // Enable pagination
                    searching: true, // searching
                    lengthChange: true, // Length change input (rows per page)
                    pageLength: 6, // Set the number of rows per page
                    width: "100%",
                    language: {
                      paginate: dataTableOptions.language.paginate,
                      info: dataTableOptions.language.info,
                      infoEmpty: dataTableOptions.language.infoEmpty,
                      infoFiltered: dataTableOptions.language.infoFiltered,
                      lengthMenu: dataTableOptions.language.lengthMenu,
                      loadingRecords: dataTableOptions.language.loadingRecords,
                      processing: dataTableOptions.language.processing,
                      search: dataTableOptions.language.search,
                     },
                     initComplete: function() {
                      // $('#recipientsTable_filter').css({"margin-top":"10px"});
                      $('#recipientsTable_filter').addClass("pull-left");
                      $('#recipientsTable_length').css({"text-align":"left"});
                      $('#recipientsTable_wrapper > div.dt-buttons.btn-group').addClass('pull-right');
                    }                     
                  });
                $('#recipients').modal('show').on('shown.bs.modal', function () {
                    // Trigger a columns adjustment to force DataTables to recalculate its dimensions
                    recipientsTable.columns.adjust().draw();
                    checkScreenSize();
                });
            });
          });
      },
      error: function(xhr, status, error) {
          console.error('AJAX Error: ' + status + error);
      }
    });


    
var dataTableButtons = [
        // 'copy', 
        {
          extend: 'copy',
          title: function () { return "Ιστορικό μηνυμάτων SMS"; },
            exportOptions: {
            orthogonal: "exportCopy"
          }
        },
        // 'excel', 
        {
          extend: 'excel',
          title: function () { return "Ιστορικό μηνυμάτων SMS"; },
            exportOptions: {
            orthogonal: "exportExcel"
          }
        },
        // 'pdf', 
        {
          extend: 'pdf',
          // add title to pdf
          title: function () { return "Ιστορικό μηνυμάτων SMS"; },
          exportOptions: {
            orthogonal: "exportPdf"
          }
        },
        // 'print'
        {
          extend: 'print',
          title: function () { return "Ιστορικό μηνυμάτων SMS"; },
            exportOptions: {
            orthogonal: "exportPrint"
          }
        },
      ];

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
  }};

function handleSuccess(data) {
  var recipientsCount=[];
    data.forEach(function(row) {
      var recipients = JSON.parse(row.recipients);
      var id = row.id;
      var recipientCount = Array.isArray(recipients) ? recipients.length : 0;
      console.log('Recipients:', id, recipientCount);
      recipientsCount[id]= recipientCount;
    });

    $('#tbl1').DataTable({
        // dom: 'lfBrtip',
        // buttons: dataTableButtons,
        dom: 'lrtip', // no Filter(f), no Buttons(B)
        scrollX:true,
        data: data,
        processing: true,
        columns: [
          { data: "created_at" },
          { data: "subject" },
          { data: "content" },
          { data: "id" ,
            mRender: function(data, type, row, meta){
              return '<button class="btn btn-sm" id=recipients-'+data+'>'+recipientsCount[data]+' <i class="icon-group"></i></button>';
              }
            }
          ],
        order: [[1, 'desc']],    
        sort: false,
        filter: true,
        columnDefs: [
            { "searchable": true} 
            ],
        paginate: true,
        drawCallback: function () {
            if ($(this).find('.dataTables_empty').length == 1 && $('#monthfilter').text()!="") {
                $('th').hide();
                // $('#tbl1_filter').hide();
                $('#tbl1_search').hide();
                $('#tbl1_length').hide();
                $('#tbl1_info').hide();
                $('.dt-buttons').hide();
                $('#tbl1_paginate').hide();
                $('#monthfilter').hide();

                // $('.dataTables_empty').css({ "border-top": "1px solid #111" });

            } else {
                $('th').show();
                $('#tbl1_filter').show();
                $('#tbl1_search').show();
                $('#tbl1_length').show();
                $('#tbl1_info').show();
                $('.dt-buttons').show();
                $('#tbl1_paginate').show();
            }
        },        
        language: {
          paginate: dataTableOptions.language.paginate,
          info: dataTableOptions.language.info,
          infoEmpty: dataTableOptions.language.infoEmpty,
          infoFiltered: dataTableOptions.language.infoFiltered,
          lengthMenu: dataTableOptions.language.lengthMenu,
          loadingRecords: dataTableOptions.language.loadingRecords,
          processing: dataTableOptions.language.processing,
          search: dataTableOptions.language.search,
          zeroRecords: '<div class="alert alert-danger"><span style="font-family:\'Play\';font-weight:700;"></span>Δεν έχουν αποσταλεί email!</div>'
        }
     })
     checkScreenSize();
    }


//------------------------------------------------------------------------------------


$(window).on("resize", function (e) {
        checkScreenSize();
    });

    
    function checkScreenSize(){
        $('#tbl1_filter').addClass("pull-left");
        $('#tbl1_length').css({"text-align":"left"});
        $('#tbl1_search').addClass("pull-left");
        var newWindowWidth = $(window).width();
        if (newWindowWidth < 481) {
          $(".dt-buttons").removeClass("pull-right");
        }
        else
        {
            $(".dt-buttons").addClass("pull-right");
        }
    }


     
}) //end of (document).ready(function())

</script>

</head>

<body>

    <div class="modal fade" id="recipients" tabindex="-1" role="dialog" aria-labelledby="recipientsLabel">
      <div class="modal-dialog" role="document">
      <!-- <div class="modal-dialog modal-lg"> -->
        <div class="modal-content">
          <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          <h3 class="modal-title" id="recipientsLabel">Παραλήπτες</h3>
          </div>
          <div class="modal-body">
            <table id="recipientsTable" class="table table-striped" style="width:100%">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Phone</th>
                </tr>
              </thead>
              <tbody id="recipients-table-body">
              </tbody>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Κλείσιμο</button>
          </div>
        </div>
      <!-- </div> -->
      </div>
    </div>

 <div class="wrapper"> <!--body wrapper for css sticky footer-->
    <!-- Menu start -->
    <!-- dirname(__DIR__) gives the path one level up by default -->
    <?php include(dirname(__DIR__).'/include/menu.php');?> 
    <!-- Menu end -->

<!-- main container
================================================== -->

  <div class="container"  style="margin-bottom:60px;">
           
      <div>
	      <ul class="breadcrumb">
	        <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a></li>
          <li class="active"><a href="<?php echo base_url('reports/initial')?>">Συγκεντρωτικές Αναφορές</a></li>
          <li class="active">Ιστορικό</li>
          <li class="active">SMS</li>
	      </ul>
      </div>
      

      <ul class="nav nav-tabs">
        <!-- <li><a href="<?php echo base_url()?>history">Σύνοψη</a></li> -->
        <li><a href="<?php echo base_url()?>history/apy">ΑΠΥ</a></li>
        <li><a href="<?php echo base_url()?>history/absences">Απουσιών</a></li>
        <li><a href="<?php echo base_url()?>history/mail">Ηλ.Ταχυδρομείου</a></li>
        <li class="active"><a href="<?php echo base_url()?>history/sms">SMS</a></li>
      </ul>

      <p></p>


	<div class="row">

    	<div class="col-xs-12">
        <div class="panel panel-default">
       <div class="panel-heading">
          <span class="icon">
            <i class="icon-book"></i>
          </span>
          <h3 class="panel-title">Μηνύματα SMS</h3>
       </div> 
        <div class="panel-body">
        <table id="tbl1" class="table datatable table-striped" style="width:100%">
    			<thead>
    		        <tr>
                        <th>Ημερομηνία</th>
                        <th>Θέμα</th>
                        <th>Μήνυμα</th>
                        <th>Παραλήπτες</th>
    		        </tr>
    		    </thead>
            <tbody>
            </tbody>
          </table>
        </div>
    </div>
</div>

</div><!--end of main container-->


<div class="push"></div>

</div> <!-- end of body wrapper-->

