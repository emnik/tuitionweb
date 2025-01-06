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


    oTable1 = $('#tbl1').DataTable({
        dom: 'Blfrtip',
        "scrollX":true,
        "buttons": [
        // 'copy', 
        {
          extend: 'copy',
          title: function () { return "Ιστορικό Ηλ.Ταχυδρομείου"; },
            exportOptions: {
            orthogonal: "exportCopy"
          }
        },
        // 'excel', 
        {
          extend: 'excel',
          title: function () { return "Ιστορικό Ηλ.Ταχυδρομείου"; },
            exportOptions: {
            orthogonal: "exportExcel"
          }
        },
        // 'pdf', 
        {
          extend: 'pdf',
          // add title to pdf
          title: function () { return "Ιστορικό Ηλ.Ταχυδρομείου"; },
          exportOptions: {
            orthogonal: "exportPdf"
          }
        },
        // 'print'
        {
          extend: 'print',
          title: function () { return "Ιστορικό Ηλ.Ταχυδρομείου"; },
            exportOptions: {
            orthogonal: "exportPrint"
          }
        },
        ],
        "ajax": {
        "url": "<?php echo base_url()?>history/getmailhistorydata",
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
            { "data": "created_at"},
            { "data": "subject"},
            { "data": "content"},
            { "data": "recipients",
              "mRender": function(data, type, row){
                return '<div style="min-width:250px; max-width:800px; white-space:normal">'+data+'</div>';
              }
            },
            ],
        "order": [[1, 'desc']],    
        "sort": false,
        "filter": true,
        "columnDefs": [
            { "searchable": true}        ],
        "paginate": true,
        "drawCallback": function () {
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
          "zeroRecords": '<div class="alert alert-danger"><span style="font-family:\'Play\';font-weight:700;"></span>Δεν έχουν αποσταλεί email!</div>'
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

  //INDIVIDUAL COLUMN FILTERING
  //To filter individual columns we add the input keys to the table footer (see table code)

  $("tfoot input").keyup( function () {
    /* Filter on the column (the index) of this element */
    //oTable.fnFilter( this.value, $("tfoot input").index(this)+4);//+4 is needed for getting the right  column index because I don't have input in every column!!! 
    oTable1.column($("tfoot input").index(this)+5).search(this.value).draw();
  } );
  
  /*
   * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
   * the footer
   */
  $("tfoot input").each( function (i) {
     asInitVals[i] = this.value;
  } );
  
  $("tfoot input").focus( function () {
    if ( this.className == "search_init form-control" )
    {
      this.className = "form-control";
      this.value = "";
    }
  } );
  
  $("tfoot input").blur( function (i) {
    if ( this.value == "" )
    {
      this.className = "search_init form-control";
      this.value = asInitVals[$("tfoot input").index(this)];
    }
  } );   
     
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
          <li class="active">Ηλ.Ταχυδρομείου</li>
	      </ul>
      </div>
      

      <ul class="nav nav-tabs">
        <!-- <li><a href="<?php echo base_url()?>history">Σύνοψη</a></li> -->
        <li><a href="<?php echo base_url()?>history/apy">ΑΠΥ</a></li>
        <li><a href="<?php echo base_url()?>history/absences">Απουσιών</a></li>
        <li class="active"><a href="<?php echo base_url()?>history/mail">Ηλ.Ταχυδρομείου</a></li>
      </ul>

      <p></p>


	<div class="row">

    	<div class="col-xs-12">
        <div class="panel panel-default">
       <div class="panel-heading">
          <span class="icon">
            <i class="icon-book"></i>
          </span>
          <h3 class="panel-title">Ηλ. Ταχυδρομείο</h3>
       </div> 
        <div class="panel-body">
          <!-- <div class="alert alert-danger alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            Οι αποδείξεις αφορούν πληρωμές για την <u>επιλεγμένη διαχειριστική περίοδο</u>. Αν έχει κοπεί απόδειξη που αφορά προηγούμενη διαχειριστική περίοδο <strong>δεν</strong> εμφανίζεται εδώ!
          </div> -->
        <!-- <h4>Αναφορές</h4> -->
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
            <tfoot>
          <tr>
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