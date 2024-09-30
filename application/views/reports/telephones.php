
<!-- <link href="<?php echo base_url('assets/tabletools/css/TableTools.css') ?>" rel="stylesheet"> -->

<!-- <script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/js/jquery.dataTables.min.js') ?>"></script> -->
<!-- <script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/js/dataTables.bootstrap.js') ?>"></script> -->
<!-- <script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/js/jquery.dataTables.rowGrouping.js') ?>"></script> -->

<!-- <script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/tabletools/js/ZeroClipboard.js') ?>"></script>  -->
<!-- <script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/tabletools/js/TableTools.min.js') ?>"></script> -->

<!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs-3.3.7/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-colvis-1.6.4/b-html5-1.6.4/b-print-1.6.4/fc-3.3.1/r-2.2.6/rg-1.1.2/sl-1.3.1/datatables.min.css"/> -->
 
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs-3.3.7/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-colvis-1.6.4/b-html5-1.6.4/b-print-1.6.4/fc-3.3.1/r-2.2.6/rg-1.1.2/sl-1.3.1/datatables.min.js"></script>

<!-- <link href="<?php echo base_url('assets/css/dataTables.bootstrap.css') ?>" rel="stylesheet"> -->
<link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap.min.css" rel="stylesheet"
>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>

<style type="text/css">
  /* .dataTables_processing{padding-left: 16px;} */
  .dtrg-group.dtrg-start{background-color:lightgray;}
  .selected{background-color: #89A452;}
</style>



<script type="text/javascript">

var oTable1;
var oTable2;


$(document).ready(function(){ 

  //Menu current active links and Title
  $('#menu-reports-summary').addClass('active');
  $('#menu-telephones').addClass('active');
  $('#menu-header-title').text('Επικοινωνία');

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
    select: true,
    buttons: [
        // 'copy', 
        {
                extend: 'copy',
                title: function () { return "Τηλεφωνικός Κατάλογος Μαθητών"; },
                exportOptions: {
                    columns: ':visible'
                }
            },
        // 'excel', 
        {
                extend: 'excel',
                title: function () { return "Τηλεφωνικός Κατάλογος Μαθητών"; },
                exportOptions: {
                    columns: ':visible'
                }
            },
        // 'pdf', 
        {
                extend: 'pdf',
                // add title to pdf
                title: function () { return "Τηλεφωνικός Κατάλογος Μαθητών"; },
                // change page orientation
                orientation: 'landscape',
                // add page numbers to pdf 
                customize: function(doc) {
                  doc.styles.tableHeader = {
                            alignment: 'left',
                            bold: true,
                            fillColor: 'gray'
                        }
                        doc.styles.tableFooter = {
                            alignment: 'left',
                            bold: true,
                            fillColor: 'lightgray'
                        }
                        doc.styles.title = {
                            fontSize: 14
                        },
                  doc.footer = (function(page, pages) {
                    return {
                      columns: [
                      {
                        alignment: 'center',
                        text: [
                          { text: page.toString(), italics: true },
					                ' of ',
                          { text: pages.toString(), italics: true }
				                ] 
                      }],
                      margin: [10, 0]
                    }
                  });
                },
                // export only visible columns
                exportOptions: {
                    columns: ':visible'
                }
            },
        // 'print'
        {
                extend: 'print',
                title: function () { return "Τηλεφωνικός Κατάλογος Μαθητών"; },
                exportOptions: {
                    columns: ':visible'
                }
            },
        
    ],
    dom: 'Blfrtip',
    "columns": [
    { "data": "Επίθετο"  },
    { "data": "Όνομα" },
    { "data": "Κινητό παιδιού" },
    { "data": "Σταθερό σπιτιού" },
    { "data": "Μητρώνυμο" },
    { "data": "Κινητό μητέρας" },
    { "data": "Πατρώνυμο" },
    { "data": "Κινητό Πατέρα" },
    { "data": "Σταθερό δουλειάς" },
    { "data": "Initial_Letter", "class":"hidden"}
    ],
    order: [[9, 'asc'],[0, 'asc']],
    ordering: false,
    rowGroup: {
      dataSrc: "Initial_Letter",
    },
    "scrollX": true,
    columnDefs: [{
            targets: [ 9 ],
            visible: false }],
    "pageLength": 10,
    "language": {
          "paginate": {
              "first":    "Πρώτη",
              "previous": "",
              "next":     "",
              "last":     "Τελευταία"
          },
          "info": "Εμφανίζονται οι _START_ έως _END_ από τις _TOTAL_ εγγραφές ",
          "infoEmpty": "Εμφάνιζονται 0 εγγραφές",
          "infoFiltered": "Φιλτράρισμα από _MAX_ συνολικά εγγραφές",
          "lengthMenu": "Εγγραφές/σελ. _MENU_",
          "loadingRecords": "Φόρτωση καταλόγου ...",
          "processing": "Επεξεργασία...",   
          "search": "Αναζήτηση",
          "zeroRecords": "Δεν βρέθηκαν εγγραφές"
        }
  })

 $('#link1').on('click', clicklink1);
 $('#tbl1').css('width','100%');


 oTable2 = $('#tbl2').DataTable({
    buttons: [
        // 'copy', 
        {
          extend: 'copy',
          // add title to pdf
          title: function () { return "Τηλεφωνικός Κατάλογος Καθηγητών"; },
        },
        // 'excel', 
        {
          extend: 'excel',
          // add title to pdf
          title: function () { return "Τηλεφωνικός Κατάλογος Καθηγητών"; },
        },
        // 'pdf', 
        {
          extend: 'pdf',
          // add title to pdf
          title: function () { return "Τηλεφωνικός Κατάλογος Καθηγητών"; },
          customize: function(doc) {
                        doc.styles.tableHeader = {
                            alignment: 'left',
                            bold: true,
                            fillColor: 'gray'
                        }
                        doc.styles.tableFooter = {
                            alignment: 'left',
                            bold: true,
                            fillColor: 'lightgray'
                        }
                        doc.styles.title = {
                            fontSize: 14
                        }
                        doc.content[1].table.widths = ['*', '*', '*', '*'];
                    },
        },
        // 'print'
        {
          extend: 'print',
          // add title to pdf
          title: function () { return "Τηλεφωνικός Κατάλογος Καθηγητών"; },
        },
    ],
    dom: 'Bfrtip',
    "columns": [
    { "data": "surname", "class": "col-sm-4"  },
    { "data": "name", "class": "col-sm-4"  },
    { "data": "home_tel", "class": "hidden-xs col-sm-4"  },
    { "data": "mobile", "class": "col-sm-4"  }
    ],
    order: [[0, 'asc']],
    // "pageLength": 15,
    "paginate":false,
    ordering: false,
    "language": {
          "paginate": {
              "first":    "Πρώτη",
              "previous": "",
              "next":     "",
              "last":     "Τελευταία"
          },
          "info": "Εμφανίζονται οι _START_ έως _END_ από τις _TOTAL_ εγγραφές",
          "infoEmpty": "Εμφάνιζονται 0 εγγραφές",
          "infoFiltered": "Φιλτράρισμα από _MAX_ συνολικά εγγραφές",
          "lengthMenu": "_MENU_",
          "loadingRecords": "Φόρτωση καταλόγου ...",
          "processing": "Επεξεργασία...",   
          "search": "Αναζήτηση",
          "zeroRecords": "Δεν βρέθηκαν εγγραφές"
        }
  })

 $('#link2').on('click', clicklink2);
 $('#tbl2').css('width','100%');

//------------------------------------------------------------------------------------

   //bootstrap3 style fixes

$('#tbl1_filter').addClass("pull-left");
$('#tbl1_length').css({"margin-top":"10px"});
$('#tbl1_search').addClass("pull-left");
$('#tbl1_length').css({"text-align":"left"});
$(".dt-buttons").css({"margin-bottom":"10px"})

$('#tbl2_filter').addClass("pull-left");
$('#tbl2_length').css({"margin-top":"10px"});
$('#tbl2_search').addClass("pull-left");
$('#tbl2_length').css({"text-align":"left"});
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



function clicklink1(){
    $.ajax({  
              type: "POST", 
              url: "<?php echo base_url()?>telephones/getstudentphones",  
              success: function(data) {  
                   if (data!=false){
                      oTable1.clear();
                      oTable1.rows.add(data.aaData).draw();
              $('#link1').off('click');
              }
              }
          });
    
    };

    function clicklink2(){
    $.ajax({  
              type: "POST", 
              url: "<?php echo base_url()?>telephones/getemployeephones",  
              success: function(data) {  
                   if (data!=false){
                      oTable2.clear();
                      oTable2.rows.add(data.aaData).draw();
              $('#link2').off('click');
              }
              }
          });
    
    };

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
	        <li class="active">Επικοινωνία</li>
	      </ul>
      </div>
      
     <!-- <p> 
      <h3>
        Τηλέφωνα
      </h3>
    </p> -->
        
    <ul class="nav nav-tabs">
        <li class="active"><a href="<?php echo base_url('telephones')?>">Τηλέφωνα</a></li>
        <li><a href="<?php echo base_url('telephones/exports')?>">Ομαδικά SMS / Επαφές Google</a></li>
        <li><a href="<?php echo base_url('mailinglist')?>">Λίστα Ηλ. Ταχυδρομείου</a></li>
      </ul>

      <p></p>

	<div class="row">
   	<div class="col-xs-12">
      <!-- <h4>Κατάλογοι</h4> -->
      <p></p>
    
    <div class="panel-group" id="accordion">

  <div class="panel panel-default">
    <div class="panel-heading">
        <span class="icon">
          <i class="icon-file-text"></i>
        </span>
      <h4 class="panel-title">
        <a id="link1" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
          Μαθητών
        </a>
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse">
      <div class="panel-body">
          <div class="alert alert-info" role="alert">
          <p>
            <span class="icon icon-info-sign"></span>
               <b>Tip!</b> Μπορείτε να χρησιμοποιήσετε το πλαίσιο της αναζήτησης και για αντίστροφη αναζήτηση μαθητή βαση του τηλεφώνου!
            </p>
          </div>
        <table id="tbl1" class="table datatable table-condensed">
          <thead>
                <tr>
                  <!-- <th></th> -->
                  <th>Επίθετο</th>
                  <th>Όνομα</th>
                  <th>Κινητό παιδιού</th>
                  <th>Σταθερό σπιτιού</th>
                  <th>Μητρώνυμο</th>
                  <th>Κινητό Μητέρας</th>
                  <th>Πατρώνυμο</th>
                  <th>Κινητό Πατέρα</th>
                  <th>Σταθερό δουλειάς</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
      </div>
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading">
        <span class="icon">
          <i class="icon-file-text"></i>
        </span>
      <h4 class="panel-title">
        <a id="link2" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
          Καθηγητών
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse">
      <div class="panel-body">
        <table id="tbl2" class="table datatable">
    			<thead>
    		        <tr>
    		        	<th>Επίθετο</th>
    		        	<th>Όνομα</th>
    		        	<th>Τηλέφωνο σπιτιού</th>
                  <th>Κινητό</th>
    		        </tr>
    		    </thead>
            <tbody>
            </tbody>
          </table>
      </div>
    </div>
  </div>

</div>

<!-- ============================================================= -->
      	</div>
  	</div>


</div><!--end of main container-->

<div class="push"></div>

</div> <!-- end of body wrapper-->