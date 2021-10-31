
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
                exportOptions: {
                    columns: ':visible'
                }
            },
        'excel', 
        // 'pdf', 
        {
                extend: 'pdf',
                // add title to pdf
                title: function () { return "Τηλεφωνικός Κατάλογος Μαθητών"; },
                // change page orientation
                orientation: 'landscape',
                // add page numbers to pdf 
                customize: function(doc) {
                  doc['footer'] = (function(page, pages) {
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
        'print'
        
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
        'copy', 
        'excel', 
        // 'pdf', 
        {
          extend: 'pdf',
          // add title to pdf
          title: function () { return "Τηλεφωνικός Κατάλογος Καθηγητών"; },
        },
        'print'
    ],
    dom: 'Bfrtip',
    "columns": [
    { "data": "surname", "class": "col-sm-4"  },
    { "data": "name", "class": "col-sm-4"  },
    { "data": "home_tel", "class": "hidden-xs col-sm-4"  },
    { "data": "mobile", "class": "col-sm-4"  }
    ],
    order: [[0, 'asc']],
    "pageLength": 15,
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

    <div class="navbar navbar-inverse navbar-top">
      <div class="container">
      <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo base_url()?>">TuitionWeb</a>
     </div>

      <div class="navbar-collapse collapse" role="navigation">
        <ul class="nav navbar-nav">
           <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Λειτουργία<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo base_url('student')?>">Μαθητολόγιο</a></li>
                <li><a href="<?php echo base_url('exam')?>">Διαγωνίσματα</a></li>
                <!-- <li><a href="<?php echo base_url()?>files">Αρχεία</a></li> -->
                <!-- <li><a href="<?php echo base_url()?>cashdesk">Ταμείο</a></li> -->
                <!-- <li><a href="<?php echo base_url()?>announcements">Ανακοινώσεις</a></li> -->
              </ul>
            </li>
           <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Οργάνωση/Διαχείριση<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo base_url('staff')?>">Προσωπικό</a></li>
                <li><a href="<?php echo base_url('section')?>">Τμήματα</a></li>
                <li><a href="<?php echo base_url('curriculum/edit')?>">Πρόγραμμα Σπουδών</a></li>
                <li><a href="<?php echo base_url('curriculum/edit/tutorsperlesson')?>">Μαθήματα-Διδάσκωντες</a></li>
                <li><a href="<?php echo base_url()?>">Στοιχεία Φροντιστηρίου</a></li>
              </ul>
            </li>
           <li class="dropdown">
              <a href="#" class="dropdown-toggle active" data-toggle="dropdown">Συγκεντρωτικές Αναφορές<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo base_url('reports')?>">Αναφορές</a></li>
                <li><a href="<?php echo base_url('history')?>">Ιστορικό</a></li>
                <li class="active"><a href="<?php echo base_url('telephones/catalog')?>">Τηλ. Κατάλογοι</a></li>
                <li><a href="<?php echo base_url('finance')?>">Οικονομικά</a></li>
              </ul>
            </li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Χρήστης<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header"><?php echo $user->surname.' '.$user->name;?></li>
                <li><a href="#">Αλλαγή κωδικού</a></li>
                <li><a href="<?php echo base_url('telephones/logout')?>">Αποσύνδεση</a></li>
              </ul>
            </li>
        </ul>
      </div><!--/.navbar-collapse -->
    </div>
  </div>


<!-- Subhead
================================================== -->
<div class="jumbotron subhead">
  <div class="container">
    <h1>Τηλεφωνικοί Κατάλογοι</h1>
    <p class="leap">Πρόγραμμα διαχείρισης φροντιστηρίου.</p>
    <p style="font-size:13px; margin-top:15px; margin-bottom:-15px;">
      <?php 
      $s=$this->session->userdata('startsch');
      echo 'Διαχειριστική Περίοδος: '.$s.'-'.($s + 1);
      ?>
    </p>
  </div>
</div>


<!-- main container
================================================== -->

  <div class="container"  style="margin-bottom:60px;">
           
      <div>
	      <ul class="breadcrumb">
	        <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a></li>
	        <li class="active">Τηλεφωνικοί Κατάλογοι</li>
	      </ul>
      </div>
      
     <p> 
      <h3>
        Τηλέφωνα
      </h3>
    </p>
        

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