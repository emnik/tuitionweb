<!-- <link href="<?php echo base_url('assets/css/dataTables.bootstrap.css') ?>" rel="stylesheet"> -->
<link href="<?php echo base_url('assets/tabletools/css/TableTools.css') ?>" rel="stylesheet">

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs-3.3.7/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-colvis-1.6.4/b-html5-1.6.4/b-print-1.6.4/fc-3.3.1/r-2.2.6/rg-1.1.2/sl-1.3.1/datatables.min.js"></script>

<!-- For date formating -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>

<script src="//netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<!-- <link href="<?php echo base_url('assets/css/dataTables.bootstrap.css') ?>" rel="stylesheet"> -->
<link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap.min.css" rel="stylesheet">

<style type="text/css">
  .dataTables_processing{padding-left: 16px;}
  .dtrg-group.dtrg-start{background-color:lightgray;}
</style>

<script type="text/javascript">

var oTable1;
var asInitVals = new Array(); //for specific columns filtering with input field below

$(document).ready(function(){ 

  //Menu current active links and Title
  $('#menu-reports-summary').addClass('active');
  $('#menu-history').addClass('active');
  $('#menu-header-title').text('Ιστορικό');  
  

// add sorting methods for currency columns
jQuery.extend(jQuery.fn.dataTableExt.oSort, {
    "currency-pre": function (a) {
        a = (a === "-") ? 0 : a.replace(/[^\d\-\.]/g, "");
        return parseFloat(a);
    },
    "currency-asc": function (a, b) {
        return a - b;
    },
    "currency-desc": function (a, b) {
        return b - a;
    }
});

    // Custom sorting plugin for date format DD/MM/YYYY
    jQuery.extend(jQuery.fn.dataTableExt.oSort, {
        "date-eu-pre": function (a) {
            var x;
            if (a.trim() !== '') {
                var euDatea = a.trim().split('/');
                x = (euDatea[2] + euDatea[1] + euDatea[0]) * 1;
            } else {
                x = Infinity;
            }
            return x;
        },
        "date-eu-asc": function (a, b) {
            return a - b;
        },
        "date-eu-desc": function (a, b) {
            return b - a;
        }
    });

    oTable1 = $('#tbl1').DataTable({
        dom: 'Blfrtip',
        "scrollX":true,
        "buttons": [
        // 'copy', 
        {
          extend: 'copy',
          title: function () { return "Ιστορικό ΑΠΥ"; },
            exportOptions: {
            orthogonal: "exportCopy"
          }
        },
        // 'excel', 
        {
          extend: 'excel',
          title: function () { return "Ιστορικό ΑΠΥ"; },
            exportOptions: {
            orthogonal: "exportExcel"
          }
        },
        // 'pdf', 
        {
          extend: 'pdf',
          // add title to pdf
          title: function () { return "Ιστορικό ΑΠΥ"; },
          exportOptions: {
            orthogonal: "exportPdf"
          }
        },
        // 'print'
        {
          extend: 'print',
          title: function () { return "Ιστορικό ΑΠΥ"; },
            exportOptions: {
            orthogonal: "exportPrint"
          }
        },
        ],
        "ajax": {
        "url": "<?php echo base_url()?>history/gethistoryapydata",
        "dataSrc": function(data){
            if(data == false){
                return [];
            }
            else
            {
                return data['aaData'];
            }
            
        }
    },
        "processing": true,
        "columns": [
            { "data": "id"},
            { "data": "surname"},
            { "data": "name"},
            { "data": "apy_no"},
            { "data": "apy_dt", 
                      "mRender": function ( data, type, row ) {
                            return (moment(data).format("DD/MM/YYYY"));
                        }},
            { "data": "amount",
              "render": function (data, type, row, meta) {
                if (data == null) {data='0'};
                if (type ==="display" || type ==="exportPdf" || type ==="exportPrint" ){
                    return data+'€';
                }
                else 
                {
                    return data;
                }
              }
            },
            { "data": "is_credit", 
              "render": function ( data, type, row ) {
                    if (type === 'display'){
                        if (data === '1') {
                            return '<input type="checkbox" class="editor-active" onclick="return false;" checked>';
                        } else {
                            return '<input type="checkbox" onclick="return false;" class="editor-active">';
                        }
                    }
                    else {
                        return data;
                        }
                    }
                },
            { "data": "month_range"},
            { "data": "notes"},
            ],
        "order": [[0, 'desc']],    
        "sort": true,
        "filter": true,
        "columnDefs": [
            { // change visible to false if you don't want the payment_id visible.
              // I keep it for able to reset the sorting based on payment_id after
              // the user has sorted the table based on other columns.
                "targets": 0,
                "visible": true,
                "orderable": true
            },
            {
                "targets": 4,
                "type": "date-eu",
                "render": function (data, type, row) {
                    return moment(data).format("DD/MM/YYYY");
                }
            },
            { // Not all the the columns have meaning to be sortable
                "targets": [2,5,7,8],
                "orderable": false
            }
        ],
        "paginate": true,
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
          "search": "Αναζήτηση:",
          "zeroRecords": '<div class="alert alert-danger"><span style="font-family:\'Play\';font-weight:700;"></span>Δεν υπάρχουν καταχωρημένες ΑΠΥ!</div>'
        }
     } );



//------------------------------------------------------------------------------------


$('#tbl1_filter').addClass("pull-left");
$('#tbl1_length').css({"margin-top":"10px"});
$('#tbl1_search').addClass("pull-left");
$('#tbl1_length').css({"text-align":"left"});
$(".dt-buttons").css({"margin-bottom":"10px"})

$(window).on("resize", function (e) {
        checkScreenSize();
    });
    checkScreenSize();

    function checkScreenSize(){
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
          <li class="active">ΑΠΥ</li>
	      </ul>
      </div>
      
      

      <ul class="nav nav-tabs">
        <li class="active"><a href="<?php echo base_url()?>history/apy">ΑΠΥ</a></li>
        <li><a href="<?php echo base_url()?>history/absences">Απουσιών</a></li>
        <li><a href="<?php echo base_url()?>history/mail">Ηλ.Ταχυδρομείου</a></li>
        <li><a href="<?php echo base_url()?>history/sms">SMS</a></li>
      </ul>

      <p></p>


	<div class="row">

    	<div class="col-xs-12">
        <div class="panel panel-default">
       <div class="panel-heading">
          <span class="icon">
            <i class="icon-book"></i>
          </span>
          <h3 class="panel-title">Αποδείξεις</h3>
       </div> 
        <div class="panel-body">
          <div class="alert alert-danger alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            Οι αποδείξεις αφορούν πληρωμές για την <u>επιλεγμένη διαχειριστική περίοδο</u>. Αν έχει κοπεί απόδειξη που αφορά προηγούμενη διαχειριστική περίοδο <strong>δεν</strong> εμφανίζεται εδώ!
          </div>
        <table id="tbl1" class="table datatable table-striped" style="width:100%">
    			<thead>
    		        <tr>
                  <th>payment_id</th>
    		        	<th>Επίθετο</th>
    		        	<th>Όνομα</th>
                        <th>Αρ. ΑΠΥ</th>
                        <th>Ημερομηνία</th>
                        <th>Ποσό</th>
                        <th>Επι πιστώσει</th>
                        <th>Μήνας/-ες</th>
                        <th>Παρατηρήσεις</th>
    		        </tr>
    		    </thead>
            <tbody>
            </tbody>
            <tfoot>
          <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
          </tr>
        </tfoot>
          </table>

        </div>
    </div>
</div>

</div><!--end of main container-->


<div class="push"></div>

</div> <!-- end of body wrapper-->