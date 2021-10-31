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
  /* .dataTables_processing{padding-left: 16px;} */
  .dtrg-group.dtrg-start{background-color:lightgray;}
</style>

<script type="text/javascript">

var oTable1;
var oTable2;
var oTable3;

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


  $('#btnschoolyearupdate').click(function(){
        var selected_chkboxes = $('form').find(':input[type="checkbox"]:checked');
        var sData = selected_chkboxes.serialize();
        console.log(sData);
        $.ajax({  
              type: "POST",  
              data: sData,
              url: "<?php echo base_url()?>finance/update_schfinance_data",
              beforeSend : function(){
              //Να κλείνουν όλα τα accordions και να απενεργοποιούνται τα clicks σε αυτά
              //μέχρι να τελειώσει η ανανέωση
                  $('#accordion .in').collapse('hide');
                  $('#link1').off('click');
                  $('#link1').prop('disabled', true);
                  $('#link2').off('click');
                  $('#link2').prop('disabled', true);
                  $('#link3').off('click');
                  $('#link3').prop('disabled', true);
                  $('#btnschoolyearupdate').button('loading');
                  $('#schmessage').html('<span class="icon"><i class="icon-spinner icon-spin"> </i> Παρακαλώ περιμένετε. Οι υπολογισμοί μπορεί να διαρκέσουν λίγη ώρα.</span>');  
              },
              success: function(result) {  
                  if (result!=false){
                       $('#link1').on('click', clicklink1);
                       $('#link2').on('click', clicklink2);
                       $('#link3').on('click', clicklink3);
                       $('#link1').prop('disabled', false);
                       $('#link2').prop('disabled', false);
                       $('#link3').prop('disabled', false);
                  };
              },
              complete: function(){
                $('#btnschoolyearupdate').button('reset');
                $('#schmessage').html('<span class="icon"><i class="icon-info-sign"> </i> Τα οικονομικά στοιχεία για το σχολικό έτος ενημερώθηκαν με τα τελευταία δεδομένα!</span>');  
              }
          });
    
    });


  oTable1 = $('#tbl1').DataTable({
    "buttons": [
        // 'copy', 
        {
          extend: 'copy',
            exportOptions: {
            orthogonal: "exportCopy"
          }
        },
        // 'excel', 
        {
          extend: 'excel',
            exportOptions: {
            orthogonal: "exportExcel"
          }
        },
        // 'pdf', 
        {
          extend: 'pdf',
          // add title to pdf
          // title: function () { return "Ιστορικό ΑΠΥ"; },
          exportOptions: {
            orthogonal: "exportPdf"
          }
        },
        // 'print'
        {
          extend: 'print',
            exportOptions: {
            orthogonal: "exportPrint"
          }
        },
        ],
    dom: 'Bfrtip',
    "processing": true,
    "columns": [
    { "data": "student" },
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
    { "data": "name" },
    { "data": "priority" }
    ],
    order: [[3, 'asc']],
    rowGroup: {
      dataSrc: "name"
    },
    columnDefs: [ {
            targets: [ 2,3 ],
            visible: false
        } ],
    "language": {
          "paginate": {
              "first":    "Πρώτη",
              "previous": "",
              "next":     "",
              "last":     "Τελευταία"
          },
          "info": "Εμφανίζονται οι _START_ έως _END_ από τις _TOTAL_ οφειλές",
          "infoEmpty": "Εμφάνιζονται 0 οφειλές",
          "infoFiltered": "Φιλτράρισμα από _MAX_ συνολικά οφειλές",
          "lengthMenu": "_MENU_",
          "loadingRecords": "Φόρτωση καταλόγου οφειλών...",
          "processing": "Επεξεργασία...",   
          "search": "Αναζήτηση:",
          "zeroRecords": "Δεν βρέθηκαν οφειλές"
        }
  })

 $('#link1').on('click', clicklink1);
 $('#tbl1').css('width','100%');

//------------------------------------------------------------------------------------


  oTable2 = $('#tbl2').DataTable({
    "buttons": [
        // 'copy', 
        {
          extend: 'copy',
            exportOptions: {
            orthogonal: "exportCopy"
          }
        },
        // 'excel', 
        {
          extend: 'excel',
            exportOptions: {
            orthogonal: "exportExcel"
          }
        },
        // 'pdf', 
        {
          extend: 'pdf',
          // add title to pdf
          // title: function () { return "Ιστορικό ΑΠΥ"; },
          exportOptions: {
            orthogonal: "exportPdf"
          }
        },
        // 'print'
        {
          extend: 'print',
            exportOptions: {
            orthogonal: "exportPrint"
          }
        },
        ],
    dom: 'Bfrtip',
    "processing": true,
     "columns": [
    { "data": "student" },
    { "data": "totaldebt",
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
    { "data": "months"}
    ],
    order: [[2, 'desc']],
    rowGroup: {
      dataSrc: "months"
    },
    columnDefs: [ {
            targets: [ 2 ],
            visible: false
        } ],
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
          "search": "Αναζήτηση:",
          "zeroRecords": "Δεν βρέθηκαν οφειλές"
        }
  })

  $('#link2').on('click', clicklink2);
  $('#tbl2').css('width','100%');

// //------------------------------------------------------------------------------------

    oTable3 = $('#tbl3').DataTable( {
        "responsive": true,
        "processing": true,
        // "aaData":[], 
        "columns": [
            { "data": "Μήνας",
              "class":"col-md-3"},
            { "data": "Εισπράξεις",
              "class":"col-md-3",
              "render": function (data, type, row, meta) {
                return data+'€';
              }
            },
            { "data": "Οφειλές",
            "class":"col-md-3",
             "render": function (data, type, row, meta) {
                return data+'€';
              }
            },
            { "data": "Τζίρος",
            "class":"hidden-xs col-md-3",
             "render": function (data, type, row, meta) {
                return data+'€';
             }},
             ],
        // "dom": "<'row'<'col-md-12'rTt>>",
        "sort": false,
        "filter": false,
        "paginate": false,
        "language": {"zeroRecords": "Δεν βρέθηκαν εγγραφές"},
        "footerCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
            /*
             * Calculate the total market share for all browsers in this table (ie inc. outside
             * the pagination)
             * if Sort and/or Filter is enabled see http://www.datatables.net/examples/advanced_init/footer_callback.html
             */
            var iTotalAvenues = 0;
            var iTotalDepts = 0;
            var iTotal = 0;
            for ( var i=0 ; i<aaData.length ; i++ )
            {
                iTotalAvenues += parseInt(aaData[i]['Εισπράξεις']);
                iTotalDepts += parseInt(aaData[i]['Οφειλές']);
                iTotal += parseInt(aaData[i]['Τζίρος']);
            }

            /* Modify the footer row to match what we want */
            var nCells = nRow.getElementsByTagName('th');
            nCells[1].innerHTML = iTotalAvenues+'€';
            nCells[2].innerHTML = iTotalDepts+'€';
            nCells[3].innerHTML = iTotal+'€';
            }
     } );

    $('#link3').on('click', clicklink3);
    $('#tbl3').css('width','100%');
//------------------------------------------------------------------------------------

   //bootstrap3 style fixes until datatables 1.10 is released with bootstrap3 support


$('#tbl1_filter').addClass("pull-left");
$('#tbl1_length').css({"margin-top":"10px"});
$('#tbl1_search').addClass("pull-left");
$('#tbl1_length').css({"text-align":"left"});

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
              url: "<?php echo base_url()?>finance/getschoolreport1data",  
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
              url: "<?php echo base_url()?>finance/getschoolreport2data",  
              success: function(data) {  
                   if (data!=false){
                      oTable2.clear();
                      oTable2.rows.add(data.aaData).draw();
              $('#link2').off('click');
            }
              }
          });
  };


function clicklink3(){
    $.ajax({  
              type: "POST",  
              url: "<?php echo base_url();?>finance/getschfinancedata",  
              success: function(data) {  
                   if (data!=false){
                      oTable3.clear();
                      oTable3.rows.add(data.aaData).draw();
              $('#link3').off('click');
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
                <li><a href="<?php echo base_url('telephones/students')?>">Τηλ. Κατάλογοι</a></li>
                <li class="active"><a href="<?php echo base_url('finance')?>">Οικονομικά</a></li>
              </ul>
            </li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Χρήστης<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header"><?php echo $user->surname.' '.$user->name;?></li>
                <li><a href="#">Αλλαγή κωδικού</a></li>
                <li><a href="<?php echo base_url('finance/logout')?>">Αποσύνδεση</a></li>
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
    <h1>Οικονομικά</h1>
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
	        <li class="active">Οικονομικά</li>
          <li class="active">Σχολικό έτος</li>
	      </ul>
      </div>
      
     <p> 
      <h3>
        Οικονομικά
      </h3>
    </p>
        

      <ul class="nav nav-tabs">
        <!-- <li><a href="<?php echo base_url()?>finance">Σύνοψη</a></li> -->
        <li class="active"><a href="<?php echo base_url()?>finance/schoolyear">Σχολικό έτος</a></li>
        <li><a href="<?php echo base_url()?>finance/economicyear">Οικονομικό έτος</a></li>
      </ul>

      <p></p>


	<div class="row">

    	<div class="col-xs-12">
        <div id="schmessage" class="alert  alert-warning fade in"><span class="icon"><i class="icon-info-sign"> </i> Tελευταία ενημέρωση των οικονομικών δεδομένων για το σχολικό έτος : <?php $m=(!isset($schoolyear_update)) ? " Δεν υπάρχει!" : $schoolyear_update; echo '<strong>'.' '.$m.'</strong>';?></span></div>
        <form>
        <div class="row">
          <div class="col-xs-12">
            <h4>Επιλογές</h4>
              <div class="checkbox">
                <label>
                  <input type="checkbox" value="1" name='chk0PayState'>
                  Να εξαιρεθούν οι 'μηδενικές οφειλές' (δωρεάν στο μαθητολόγιο) από τις αναφορές
                </label>
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" value="1" name="chkCurMonthState">
                  Να συμπεριληφθεί και ο τρέχων μήνας στον υπολογισμό οφειλών
                </label>
              </div>
            <button id="btnschoolyearupdate" type="button" data-loading-text="Ανανέωση..." class="btn btn-primary btn-lg pull-right">Ανανέωση Τώρα</button>
          </div>
        </div>
        </form>
        <h4>Αναφορές</h4>
<div class="panel-group" id="accordion">

  <div class="panel panel-default">
    <div class="panel-heading">
        <span class="icon">
          <i class="icon-file-text"></i>
        </span>
      <h4 class="panel-title">
        <a id="link3" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
          Σύνοψη σχολικού έτους
        </a>
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse">
      <div class="panel-body">
        <table id="tbl3" class="table datatable table-condensed">
          <thead>
                <tr>
                  <th>Μήνας</th>
                  <th>Εισπράξεις</th>
                  <th>Οφειλές</th>
                  <th>Τζίρος</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
                  <th>Σύνολο:</th> 
                  <th></th>
                  <th></th>
                  <th></th>
              </tr>
            </tfoot>
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
        <a id="link1" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
          Οφειλές μαθητών ανα μήνα
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse">
      <div class="panel-body">
        <table id="tbl1" class="table datatable">
    			<thead>
    		        <tr>
    		        	<!-- <th>id</th> -->
    		        	<th>Ονοματεπώνυμο</th>
    		        	<th>Ποσό</th>
    		        	<th>Μήνας</th>
                  <th></th>
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
        <a id="link2"  data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
          Οφειλές ανα σύνολο μηνών
        </a>
      </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse">
      <div class="panel-body">
          <table id="tbl2" class="table datatable">
          <thead>
                <tr>
                  <!-- <th>id</th> -->
                  <th>Ονοματεπώνυμο</th>
                  <th>Ποσό</th>
                  <th>Οφειλόμενοι Μήνες</th>
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
	       	<!-- </div> end of panel body -->
          <!-- </div> -->
      	</div>
  	</div>


</div><!--end of main container-->

<div class="push"></div>

</div> <!-- end of body wrapper-->