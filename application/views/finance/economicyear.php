<link href="<?php echo base_url('assets/css/dataTables.bootstrap.css') ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/tabletools/css/TableTools.css') ?>" rel="stylesheet">

<script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/js/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/js/dataTables.bootstrap.js') ?>"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/js/jquery.dataTables.rowGrouping.js') ?>"></script>

<script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/tabletools/js/ZeroClipboard.js') ?>"></script> 
<script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/tabletools/js/TableTools.min.js') ?>"></script>

<style type="text/css">
  .dataTables_processing{padding-left: 16px;}
</style>

<script type="text/javascript">

var oTable1;
var oTable2;
//var oTable3;

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


  $('#btnecoyearupdate').click(function(){
        var selected_chkboxes = $('form').find(':input[type="checkbox"]:checked');
        var sData = selected_chkboxes.serialize();
        $.ajax({  
              type: "POST",  
              data: sData,
              url: "<?php echo base_url()?>finance/update_ecofinance_data",
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
                  $('#btnecoyearupdate').button('loading');
                  $('#ecomessage').html('<span class="icon"><i class="icon-spinner icon-spin"> </i> Παρακαλώ περιμένετε. Οι υπολογισμοί μπορεί να διαρκέσουν λίγη ώρα.<span>');  
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
                $('#btnecoyearupdate').button('reset');
                $('#ecomessage').html('<span class="icon"><i class="icon-info-sign"> </i> Τα οικονομικά στοιχεία για το οικονομικό έτος ενημερώθηκαν με τα τελευταία δεδομένα!</span>');  
              }
          });
    
    });


    oTable1 = $('#tbl1').dataTable({
    "sDom": "<'row'<'col-md-12'rTt>>",
    "oTableTools": {"sSwfPath": "../assets/tabletools/swf/copy_csv_xls_pdf.swf",
                    "aButtons": [ 
                                { "sExtends" : "copy",
                                  "mColumns": [ 0, 1, 2]
                                },
                                { "sExtends" : "xls",
                                  "sCharSet": "utf16le",
                                  "mColumns": [ 0, 1, 2],
                                  "sFileName": "Σύνοψη οικονομικού έτους.xls"
                                },
                                // { //tabletools use AlivePDF library and does not support greek or unicode characters!!!
                                //   "sExtends" : "pdf",
                                //   "sCharSet": "utf8"
                                // },
                                { "sExtends" : "print"
                                }
                                ]
                     },
        "bProcessing": true,
        "aaData":[],
        "aoColumns": [
            { "mData": "Μήνες",
              "sClass":"col-md-4"},
            { "mData": "Ποσό",
              "sClass":"col-md-4",
              "mRender": function (data, type, full) {
                if (data == null) {data=0};
                return data+'€';
              }
            },
            { "mData": "Κατηγορία",
            "sClass":"col-md-4"}
            ],
        "bSort": false,
        "bFilter": false,
        "bPaginate": false,
        "oLanguage": {"sZeroRecords": "Δεν βρέθηκαν εγγραφές"},
        "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
            /*
             * Calculate the total market share for all browsers in this table (ie inc. outside
             * the pagination)
             * if Sort and/or Filter is enabled see http://www.datatables.net/examples/advanced_init/footer_callback.html
             */
            var iTotal = 0;
            for ( var i=0 ; i<aaData.length ; i++ )
            {
                if (aaData[i]['Ποσό'] != null) {
                    iTotal += parseInt(aaData[i]['Ποσό']);  
                }
                
            }

            /* Modify the footer row to match what we want */
            var nCells = nRow.getElementsByTagName('th');
            nCells[1].innerHTML = iTotal+'€';
            }
     } );


 $('#link1').on('click', clicklink1);

//------------------------------------------------------------------------------------


  oTable2 = $('#tbl2').dataTable({
    "sDom": "<'row'<'col-xs-6 pull-left' l><'col-xs-6 pull-right' f> r><'row'<'col-md-12'Tt>><'row'<'col-md-6'i><'col-md-6'p>>",
    "oTableTools": {"sSwfPath": "../assets/tabletools/swf/copy_csv_xls_pdf.swf",
                    "aButtons": [ 
                                { "sExtends" : "copy",
                                  "mColumns": [ 0, 1, 2]
                                },
                                { "sExtends" : "xls",
                                  "sCharSet": "utf16le",
                                  "mColumns": [ 0, 1, 2],
                                  "sFileName": "Επι πιστώσει οφειλές ανά μήνα.xls"
                                },
                                // { //tabletools use AlivePDF library and does not support greek or unicode characters!!!
                                //   "sExtends" : "pdf",
                                //   "sCharSet": "utf8"
                                // },
                                { "sExtends" : "print"
                                }
                                ]
                     },
    "sPaginationType": "bootstrap",
    "bProcessing": true,
    "aaData":[],
    "aoColumns": [
    { "mData": "student" },
    { "mData": "amount",
      "mRender": function (data, type, full) {
                if (data == null) {data=0};
                return data+'€';},
       "sType": "currency"},
    { "mData": "name" },
    { "mData": "report_priority" }
    ],
    "oLanguage": {
          "oPaginate": {
              "sFirst":    "Πρώτη",
              "sPrevious": "",
              "sNext":     "",
              "sLast":     "Τελευταία"
          },
          "sInfo": "Εμφανίζονται οι _START_ έως _END_ από τις _TOTAL_ οφειλές",
          "sInfoEmpty": "Εμφάνιζονται 0 οφειλές",
          "sInfoFiltered": "Φιλτράρισμα από _MAX_ συνολικά οφειλές",
          "sLengthMenu": "_MENU_",
          "sLoadingRecords": "Φόρτωση καταλόγου ...",
          "sProcessing": "Επεξεργασία...",   
          "sSearch": "",
          "sZeroRecords": "Δεν βρέθηκαν οφειλές"
        }
  }).rowGrouping({iGroupingColumnIndex:2,
              bHideGroupingColumn:true,
              sGroupingColumnSortDirection: "asc",
              iGroupingOrderByColumnIndex:3,
              bHideGroupingOrderByColumn:true,//default:true 
              sGroupBy: "name"});


  $('#link2').on('click', clicklink2);

//------------------------------------------------------------------------------------

    oTable3 = $('#tbl3').dataTable( {
    "sDom": "<'row'<'col-xs-6 pull-left' l><'col-xs-6 pull-right' f> r><'row'<'col-md-12'Tt>><'row'<'col-md-6'i><'col-md-6'p>>",
    "oTableTools": {"sSwfPath": "../assets/tabletools/swf/copy_csv_xls_pdf.swf",
                    "aButtons": [ 
                                { "sExtends" : "copy",
                                  "mColumns": [ 0, 1, 2]
                                },
                                { "sExtends" : "xls",
                                  "sCharSet": "utf16le",
                                  "mColumns": [ 0, 1, 2],
                                  "sFileName": "Επί πιστώσει οφειλές ανα σύνολο μηνών.xls"
                                },
                                // { //tabletools use AlivePDF library and does not support greek or unicode characters!!!
                                //   "sExtends" : "pdf",
                                //   "sCharSet": "utf8"
                                // },
                                { "sExtends" : "print"
                                }
                                ]
                     },
    "sPaginationType": "bootstrap",
    "bProcessing": true,
    "aaData":[],
    "aoColumns": [
    { "mData": "student" },
    { "mData": "Ποσό",
      "mRender": function (data, type, full) {
                if (data == null) {data=0};
                return data+'€';},
       "sType": "currency"},
    { "mData": "Μήνες" }
    ],
    "oLanguage": {
          "oPaginate": {
              "sFirst":    "Πρώτη",
              "sPrevious": "",
              "sNext":     "",
              "sLast":     "Τελευταία"
          },
          "sInfo": "Εμφανίζονται οι _START_ έως _END_ από τις _TOTAL_ εγγραφές",
          "sInfoEmpty": "Εμφάνιζονται 0 εγγραφές",
          "sInfoFiltered": "Φιλτράρισμα από _MAX_ συνολικά εγγραφές",
          "sLengthMenu": "_MENU_",
          "sLoadingRecords": "Φόρτωση καταλόγου ...",
          "sProcessing": "Επεξεργασία...",   
          "sSearch": "",
          "sZeroRecords": "Δεν βρέθηκαν οφειλές"
        }
  }).rowGrouping({iGroupingColumnIndex:2,
              bHideGroupingColumn:true,
              sGroupingColumnSortDirection: "desc",
              iGroupingOrderByColumnIndex:2,
              bHideGroupingOrderByColumn:true,//default:true 
              sGroupBy: "name"});

    $('#link3').on('click', clicklink3);

//------------------------------------------------------------------------------------

   //bootstrap3 style fixes until datatables 1.10 is released with bootstrap3 support

   $('#tbl3_filter').find('input').addClass("form-control");
   $('#tbl3_filter label').contents().unwrap();
   var fgroupDiv3 = document.createElement('div');
   fgroupDiv3.id="fgroupDiv3";
   fgroupDiv3.className = 'form-group pull-right';
   $('#tbl3_filter').append(fgroupDiv3);
   $('#tbl3_filter').find('input').prependTo('#fgroupDiv3');
   $('#tbl3_filter').find('input').attr('id','inputid3');
   $('#tbl3_filter').find('input').css({'max-width':'200px'});
   var $searchlabel3 = $("<label>").attr('for', "#inputid3");
   $searchlabel3.css({'margin-top':'5px','margin-bottom':'5px','margin-left':'0px', 'margin-right':'10px'})
   $searchlabel3.addClass('pull-left');
   $searchlabel3.text('Αναζήτηση:');
   $searchlabel3.insertBefore('#inputid3');

   $('#tbl3_length').find('select').addClass("form-control");
   $('#tbl3_length label').contents().unwrap();
   var lgroupDiv3 = document.createElement('div');
   lgroupDiv3.id="lgroupDiv3";
   lgroupDiv3.className = 'form-group pull-left';
   var innerlgroupDiv3 = document.createElement('div');
   innerlgroupDiv3.id="innerlgroupDiv3"
   innerlgroupDiv3.className = 'clearfix';
   $('#tbl3_length').append(lgroupDiv3);
   $('#lgroupDiv3').append(innerlgroupDiv3);
   $('#tbl3_length').find('select').prependTo('#innerlgroupDiv3');
   $('#tbl3_length').find('select').attr('id','selectid3');
   $('#tbl3_length').find('select').css('max-width','75px');
   var $sellabel3 = $("<label>").attr('for', "#selectid3");
   $sellabel3.css({'min-width':'110px', 'margin-top':'5px'});
   $sellabel3.text('Εγγραφές/σελ.: ');
   $sellabel3.insertBefore('#selectid3');

   $('#tbl3_filter').parent().parent().css({'padding-bottom':'8px'});

   $('#tbl2_filter').find('input').addClass("form-control");
   $('#tbl2_filter label').contents().unwrap();
   var fgroupDiv2= document.createElement('div');
   fgroupDiv2.id="fgroupDiv2";
   fgroupDiv2.className = 'form-group pull-right';
   $('#tbl2_filter').append(fgroupDiv2);
   $('#tbl2_filter').find('input').prependTo('#fgroupDiv2');
   $('#tbl2_filter').find('input').attr('id','inputid2');
   $('#tbl2_filter').find('input').css({'max-width':'200px'});
   var $searchlabel = $("<label>").attr('for', "#inputid2");
   $searchlabel.css({'margin-top':'5px','margin-bottom':'5px','margin-left':'0px', 'margin-right':'10px'})
   $searchlabel.addClass('pull-left');
   $searchlabel.text('Αναζήτηση:');
   $searchlabel.insertBefore('#inputid2');

   $('#tbl2_length').find('select').addClass("form-control");
   $('#tbl2_length label').contents().unwrap();
   var lgroupDiv2= document.createElement('div');
   lgroupDiv2.id="lgroupDiv2"
   lgroupDiv2.className = 'form-group pull-left';
   var innerlgroupDiv2= document.createElement('div');
   innerlgroupDiv2.id="innerlgroupDiv2"
   innerlgroupDiv2.className = 'clearfix';
   $('#tbl2_length').append(lgroupDiv2);
   $('#lgroupDiv2').append(innerlgroupDiv2);
   $('#tbl2_length').find('select').prependTo('#innerlgroupDiv2');
   $('#tbl2_length').find('select').attr('id','selectid2');
   $('#tbl2_length').find('select').css('max-width','75px');
   var $sellabel = $("<label>").attr('for', "#selectid2");
   $sellabel.css({'min-width':'110px', 'margin-top':'5px'});
   $sellabel.text('Οφειλές/σελ.: ');
   $sellabel.insertBefore('#selectid2');

   $('#tbl2_filter').parent().parent().css({'padding-bottom':'8px'});

     
}) //end of (document).ready(function())

function clicklink1(){
    $.ajax({  
              type: "POST", 
              url: "<?php echo base_url()?>finance/getecofinancedata",  
              success: function(data) {  
                   if (data.aaData!=false){
                      oTable1.fnClearTable();
                      oTable1.fnAddData(data.aaData);
                      resizeFlashTableToolsBtn();
              $('#link1').off('click');
              }
              }
          });
    
    };


function clicklink2(){
    $.ajax({  
              type: "POST",  
              url: "<?php echo base_url()?>finance/getecoreport2data",  
              success: function(data) {  
                   if (data!=false){
                      oTable2.fnClearTable();
                      oTable2.fnAddData(data.aaData);
                      resizeFlashTableToolsBtn();
              $('#link2').off('click');
            }
              }
          });
  };


function clicklink3(){
    $.ajax({  
              type: "POST",  
              url: "<?php echo base_url();?>finance/getecoreport3data",  
              success: function(data) {  
                   if (data!=false){
                      oTable3.fnClearTable();
                      oTable3.fnAddData(data.aaData);
                      resizeFlashTableToolsBtn();
              $('#link3').off('click');
            }
              }
          });
  };


function resizeFlashTableToolsBtn(){
       //the following is needed when the table is hidden on load!
      //see http://stackoverflow.com/questions/11848593/datatables-tabletools-multiple-tables-on-the-same-page/18588847#18588847
      var tableInstances = TableTools.fnGetMasters(), instances = tableInstances.length;
      while (instances--)
      {
          var dataTable = tableInstances[instances];
          if (dataTable.fnResizeRequired())
          {
              dataTable.fnResizeButtons();
          }
      }
}

// Set the classes that TableTools uses to something suitable for Bootstrap
$.extend( true, $.fn.DataTable.TableTools.classes, {
  "container": "DTTT_container btn-group",
  "buttons": {
    "normal": "btn btn-default btn-sm",
    "disabled": "btn disabled"
  },
  "collection": {
    "container": "ul DTTT_dropdown dropdown-menu",
    "buttons": {
      "normal": "",
      "disabled": "disabled"
    }
  }
} );

// Have the collection use a bootstrap compatible dropdown
$.extend( true, $.fn.DataTable.TableTools.DEFAULTS.oTags, {
  "collection": {
    "container": "ul",
    "button": "li",
    "liner": "a"
  }
} );

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
                <li><a href="<?php echo base_url()?>student">Μαθητολόγιο</a></li>
                <li><a href="<?php echo base_url()?>exams">Διαγωνίσματα</a></li>
                <li><a href="<?php echo base_url()?>files">Αρχεία</a></li>
                <li><a href="<?php echo base_url()?>cashdesk">Ταμείο</a></li>
                <li><a href="<?php echo base_url()?>announcements">Ανακοινώσεις</a></li>
              </ul>
            </li>
           <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Οργάνωση/Διαχείριση<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
                <li><a href="<?php echo base_url()?>section">Τμήματα</a></li>
                <li><a href="<?php echo base_url()?>">Πρόγραμμα Σπουδών</a></li>
                <li><a href="<?php echo base_url()?>">Μαθήματα-Διδάσκωντες</a></li>
                <li><a href="<?php echo base_url()?>">Στοιχεία Φροντιστηρίου</a></li>
              </ul>
            </li>
           <li class="dropdown">
              <a href="#" class="dropdown-toggle active" data-toggle="dropdown">Συγκεντρωτικές Αναφορές<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo base_url()?>">Αναφορές</a></li>
                <li><a href="<?php echo base_url()?>">Ιστορικό</a></li>
                <li><a href="<?php echo base_url()?>">Τηλ. Κατάλογοι</a></li>
                <li class="active"><a href="<?php echo base_url()?>finance">Οικονομικά</a></li>
              </ul>
            </li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Χρήστης<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header"><?php echo $user->surname.' '.$user->name;?></li>
                <li><a href="#">Αλλαγή κωδικού</a></li>
                <li><a href="<?php echo base_url()?>finance/logout">Αποσύνδεση</a></li>
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
          <li class="active">Οικονομικό έτος</li>
	      </ul>
      </div>
      
     <p> 
      <h3>
        Οικονομικά
      </h3>
    </p>
        

      <ul class="nav nav-tabs">
        <!-- <li><a href="<?php echo base_url()?>finance">Σύνοψη</a></li> -->
        <li><a href="<?php echo base_url()?>finance/schoolyear">Σχολικό έτος</a></li>
        <li class="active"><a href="<?php echo base_url()?>finance/economicyear">Οικονομικό έτος</a></li>
      </ul>

      <p></p>


	<div class="row">

    	<div class="col-xs-12">
        <div id="ecomessage" class="alert  alert-warning fade in"><span class="icon"><i class="icon-info-sign"> </i> Tελευταία ενημέρωση των οικονομικών δεδομένων για το οικονομικό έτος : <?php $m=(!isset($economicyear_update)) ? " Δεν υπάρχει!" : $economicyear_update; echo '<strong>'.' '.$m.'</strong>';?></span></div>
        <form>
        <div class="row">
          <div class="col-xs-12">
            <h4>Επιλογές</h4>
              <div class="checkbox">
                <label>
                  <input type="checkbox" value="1" name='chk0PayState'>
                  Να εξαιρεθούν οι 'μηδενικές Ε.Π. οφειλές' (δωρεάν στο μαθητολόγιο) από τις αναφορές
                </label>
              </div>
              <!-- <div class="checkbox">
                <label>
                  <input type="checkbox" value="1" name="chkCurMonthState">
                  Να συμπεριληφθεί και ο τρέχων μήνας στον υπολογισμό οφειλών
                </label>
              </div> -->
            <button id="btnecoyearupdate" type="button" data-loading-text="Ανανέωση..." class="btn btn-primary btn-lg pull-right">Ανανέωση Τώρα</button>
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
        <a id="link1" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
          Σύνοψη οικονομικού έτους
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse">
      <div class="panel-body">
        <table id="tbl1" class="table datatable">
    			<thead>
    		        <tr>
    		        	<th>Μήνας</th>
    		        	<th>Ποσό</th>
    		        	<th>Κατηγορία</th>
    		        </tr>
    		    </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
                  <th>Σύνολο:</th> 
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
        <a id="link2"  data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
          Επί πιστώσει οφειλές ανα μήνα
        </a>
      </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse">
      <div class="panel-body">
          <table id="tbl2" class="table datatable">
          <thead>
                <tr>
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
        <a id="link3" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
          Ε.Π οφειλές ανα σύνολο μηνών
        </a>
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse">
      <div class="panel-body">
        <table id="tbl3" class="table datatable table-condensed">
          <thead>
                <tr>
                  <th>Ονοματεπώνυμο</th>
                  <th>Ποσό</th>
                  <th>Μήνες</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
<!--             <tfoot>
              <tr>
                  <th>Σύνολο:</th> 
                  <th></th>
                  <th></th>
                  <th></th>
              </tr>
            </tfoot> -->
          </table>
      </div>
    </div>
  </div>
</div>


      	</div>
  	</div>
</div>

</div><!--end of main container-->

<div class="push"></div>

</div> <!-- end of body wrapper-->