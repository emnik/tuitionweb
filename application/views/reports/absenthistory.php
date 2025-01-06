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
    // "responsive":true,
    "buttons": [
    // 'copy', 
    {
    extend: 'copy',
    title: function () { return "Ιστορικό Απουσιών"; },
        exportOptions: {
        orthogonal: "exportCopy"
    }
    },
    // 'excel', 
    {
    extend: 'excel',
    title: function () { return "Ιστορικό Απουσιών"; },
        exportOptions: {
        orthogonal: "exportExcel"
    }
    },
    // 'pdf', 
    {
    extend: 'pdf',
    // add title to pdf
    title: function () { return "Ιστορικό Απουσιών"; },
    exportOptions: {
        orthogonal: "exportPdf"
    }
    },
    // 'print'
    {
    extend: 'print',
    title: function () { return "Ιστορικό Απουσιών"; },
        exportOptions: {
        orthogonal: "exportPrint"
    }
    },
    ],
    "ajax": {
        "url": "<?php echo base_url()?>history/getabsenthistorydata",
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
    // "processing": true,
    "columns": [
        { "data": "surname"},
        { "data": "name"},
        { "data": "date", 
            "mRender": function ( data, type, row ) {
                return (moment(data).format("D/M/YYYY"));
            }
        },
        { "data": "title"},
        { "data": "nickname"},    
        { "data": "hours"},
        { "data": "excused", 
        "mRender": function ( data, type, row ) {
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
            }
        ],
    "order": [[0, 'asc']],    
    "sort": true,
    "filter": true,
    "paginate": true,
    "drawCallback": function () {
            if ($(this).find('.dataTables_empty').length == 1) {
                $('th').hide();
                // $('#tbl1_filter').hide();
                $('#tbl1_search').hide();
                $('#tbl1_length').hide();
                $('#tbl1_info').hide();
                $('.dt-buttons').hide();
                $('#tbl1_paginate').hide();

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
        "zeroRecords": '<div class="alert alert-danger"><span style="font-family:\'Play\';font-weight:700;"></span>Δεν υπάρχουν καταχωρημένες απουσίες!</div>'
        }
});
        

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
          <li class="active">Απουσιών</li>
	      </ul>
      </div>
      
     <!-- <p> 
      <h3>
        Ιστορικό
      </h3>
    </p> -->
        

      <ul class="nav nav-tabs">
        <!-- <li><a href="<?php echo base_url()?>history">Σύνοψη</a></li> -->
        <li><a href="<?php echo base_url()?>history/apy">ΑΠΥ</a></li>
        <li class="active"><a href="<?php echo base_url()?>history/absences">Απουσιών</a></li>
        <li><a href="<?php echo base_url()?>history/mail">Ηλ.Ταχυδρομείου</a></li>
      </ul>

      <p></p>


	<div class="row">

    	<div id="main" class="col-xs-12">
        <div class="panel panel-default">
       <div class="panel-heading">
          <span class="icon">
            <i class="icon-book"></i>
          </span>
          <h3 class="panel-title">Απουσίες</h3>
       </div> 
        <div class="panel-body">
        <!-- <h4>Αναφορές</h4> -->
        <table id="tbl1" class="table datatable table-striped" style="width:100%">
    			<thead>
    		        <tr>
    		        	<th>Επίθετο</th>
                        <th>Όνομα</th>
                        <th>Ημερομηνία</th>
                        <th>Μάθημα</th>
                        <th>Διδάσκων</th>
                        <th>Ώρα</th>
                        <th>Δικαιολογημένη</th>
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