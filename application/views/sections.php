<link href="<?php echo base_url('assets/css/dataTables.bootstrap.css') ?>" rel="stylesheet">

<script src="<?php echo base_url('assets/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/js/dataTables.bootstrap.js') ?>"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/js/jquery.dataTables.rowGrouping.js') ?>"></script>

<script type="text/javascript">

(function($) {
//for geting the data to populate the filter selectbox
/*
 * Function: fnGetColumnData
 * Purpose:  Return an array of table values from a particular column.
 * Returns:  array string: 1d data array 
 * Inputs:   object:oSettings - dataTable settings object. This is always the last argument past to the function
 *           int:iColumn - the id of the column to extract the data from
 *           bool:bUnique - optional - if set to false duplicated values are not filtered out
 *           bool:bFiltered - optional - if set to false all the table data is used (not only the filtered)
 *           bool:bIgnoreEmpty - optional - if set to false empty values are not filtered from the result array
 * Author:   Benedikt Forchhammer <b.forchhammer /AT\ mind2.de>
 */
$.fn.dataTableExt.oApi.fnGetColumnData = function ( oSettings, iColumn, bUnique, bFiltered, bIgnoreEmpty ) {
  // check that we have a column id
  if ( typeof iColumn == "undefined" ) return new Array();
  
  // by default we only wany unique data
  if ( typeof bUnique == "undefined" ) bUnique = true;
  
  // by default we do want to only look at filtered data
  if ( typeof bFiltered == "undefined" ) bFiltered = true;
  
  // by default we do not wany to include empty values
  if ( typeof bIgnoreEmpty == "undefined" ) bIgnoreEmpty = true;
  
  // list of rows which we're going to loop through
  var aiRows;
  
  // use only filtered rows
  if (bFiltered == true) aiRows = oSettings.aiDisplay; 
  // use all rows
  else aiRows = oSettings.aiDisplayMaster; // all row numbers

  // set up data array  
  var asResultData = new Array();
  
  for (var i=0,c=aiRows.length; i<c; i++) {
    iRow = aiRows[i];
    var aData = this.fnGetData(iRow);
    var sValue = aData[iColumn];
    
    // ignore empty values?
    if (bIgnoreEmpty == true && sValue.length == 0) continue;

    // ignore unique values?
    else if (bUnique == true && jQuery.inArray(sValue, asResultData) > -1) continue;
    
    // else push the value onto the result data array
    else asResultData.push(sValue);
  }
  
  return asResultData;
}}(jQuery));


function fnCreateSelect( aData )
{
  //added class for bootstrap integration
  var r='<select class="form-control pull-right"><option value=""></option>', i, iLen=aData.length;
  for ( i=0 ; i<iLen ; i++ )
  {
    r += '<option value="'+aData[i]+'">'+aData[i]+'</option>';
  }
  return r+'</select>';
}


/* Table initialisation */

var oTable; 

$(document).ready(function() {
  
     /* Add/remove class to a row when clicked on */
    $('#stdbook tbody tr').click( function( e ) {
        if ( $(this).hasClass('row_selected') ) {
            $(this).removeClass('row_selected');
          }
          else {
            oTable.$('tr.row_selected').removeClass('row_selected');
            $(this).addClass('row_selected');
          }
    } );
    
    /* Add a click handler for the section-card btn */
    $('#section-card').click( function() {
        var anSelected = fnGetSelected( oTable );
        if ( anSelected.length !== 0 ) {
            var aRow=anSelected[0];
            var id=oTable.fnGetData( aRow, 0 );
            window.open ('section/card/'+id,'_self',false);
            //alert(id);
        }
        else
        {
           alert("Δεν έχετε επιλέξει κανένα τμήμα.");
        }
    });

    /* Add a click handler for the new-reg btn */
    $('#new-reg').click(function(){
      window.open ('section/newreg','_self',false);
    });

    /* Add a click handler for the del-reg btn */
    $('#del-reg').click(function(){
        var anSelected = fnGetSelected( oTable );
        if ( anSelected.length !== 0 ) {
            var r=confirm("Το τμήμα που επιλέξατε πρόκειται να διαγραφεί. Παρακαλώ επιβεβαιώστε.");
            if (r==true)
            {
                var aRow=anSelected[0];
                var id=oTable.fnGetData( aRow, 0 );
                window.open ('section/delreg/'+id,'_self',false);  
            }
         }
         else
         {
          alert("Δεν έχετε επιλέξει κανένα τμήμα.");
         }
    });

    /* Init the table */
    oTable = $('#stdbook').dataTable( {
    "sDom": "<'row'<'col-xs-6 pull-left' l><'col-xs-6 pull-right' <'customfilter'>> r><'row'<'col-md-12't>><'row'<'col-md-6'i><'col-md-6'p>>",
    "sPaginationType": "bootstrap",
    "aoColumnDefs": [
      { "bVisible": false, "aTargets": [0] }, //hide id column
      { "bSearchable": false, "aTargets": [4] },
      { "bSortable": false, "aTargets": [1,2,3,4,5]}
    ],
    "oLanguage": {
              "oPaginate": {
                  "sFirst":    "Πρώτη",
                  "sPrevious": "",
                  "sNext":     "",
                  "sLast":     "Τελευταία"
              },
              "sInfo": "Εμφανίζονται οι _START_ έως _END_ από τα _TOTAL_ τμήματα",
              "sInfoEmpty": "Εμφάνιζονται 0 εγγραφές",
              "sInfoFiltered": "Φιλτράρισμα από _MAX_ συνολικά τμήματα",
              //"sLengthMenu": "_MENU_ τμήματα ανά σελίδα",
              "sLengthMenu": "_MENU_",
              "sLoadingRecords": "Φόρτωση καταλόγου τμημάτων...",
              "sProcessing": "Επεξεργασία...",   
              //"sSearch": "Αναζήτηση:",
              "sSearch": "",
              "sZeroRecords": "Δεν βρέθηκαν εγγραφές"
              
              // ΑΛΛΕΣ ΕΠΙΛΟΓΕΣ
              
              // "oAria": {
              //    "sSortAscending": "",
              //   "sSortDescending": ""
              // },
              // "sEmptyTable": " πίνακας είναι κενός",
              // "sInfoPostFix": "",
              // "sInfoThousands": ",",
              // "sUrl": "",
            }
       })
       .rowGrouping({
                          iGroupingColumnIndex: 1,
                          sGroupingColumnSortDirection: "asc",
                          iGroupingOrderByColumnIndex: 1
                          // bExpandableGrouping: true
                });

   //Add the custom selectbox filter (I used a customfilter class in the dom where the filter f option was)
   $('div.customfilter').html(fnCreateSelect( oTable.fnGetColumnData(1)));
   $('div.customfilter').css('padding-bottom','42px');
   var fgroupDiv = document.createElement('div');
   fgroupDiv.id="fgroupDiv";
   fgroupDiv.className = 'form-group pull-right';
   $('div.customfilter').append(fgroupDiv);
   $('div.customfilter').find('select').prependTo('#fgroupDiv');
   $('div.customfilter').find('select').attr('id','selectid1');
   $('div.customfilter select').css('min-width','137px');
   var $searchlabel = $("<label>").attr('for', "#selectid1");
   $searchlabel.css({'margin-top':'5px','margin-bottom':'5px','margin-left':'0px', 'margin-right':'10px'})
   $searchlabel.addClass('pull-left');
   $searchlabel.text('Φίλτρο:');
   $searchlabel.insertBefore('#selectid1');

   //activate the selectbox to filter the table
    $('select', 'div.customfilter').change( function () {
        oTable.fnFilter($(this).val(), 1); //1 is the classname column (0 is the hidden id column)
      } );


   //bootstrap3 style fixes until datatables 1.10 is released with bootstrap3 support

   $('#stdbook_length').find('select').addClass("form-control");
   $('#stdbook_length label').contents().unwrap();
   var lgroupDiv = document.createElement('div');
   lgroupDiv.id="lgroupDiv";
   lgroupDiv.className = 'form-group pull-left';
   var innerlgroupDiv = document.createElement('div');
   innerlgroupDiv.id="innerlgroupDiv"
   innerlgroupDiv.className = 'clearfix';
   $('#stdbook_length').append(lgroupDiv);
   $('#lgroupDiv').append(innerlgroupDiv);
   $('#stdbook_length').find('select').prependTo('#innerlgroupDiv');
   $('#stdbook_length').find('select').attr('id','selectid');
   $('#stdbook_length').find('select').css('max-width','75px');
   var $sellabel = $("<label>").attr('for', "#selectid");
   $sellabel.css({'min-width':'110px', 'margin-top':'5px'});
   $sellabel.text('Τμήματα/σελ.: ');
   $sellabel.insertBefore('#selectid');

   $('#stdbook_filter').parent().parent().css({'padding-bottom':'8px'});
    
// HIDING COLUMNS FOR RESPONSIVE VIEW:

$(window).on("load", resizeWindow);
//If the User resizes the window, adjust the #container height
$(window).on("resize", resizeWindow);

function resizeWindow(e)
{
    var newWindowWidth = $(window).width();

    if(newWindowWidth >= 800)
    {
      oTable.fnSetColumnVis( 4, true );
      oTable.fnSetColumnVis( 5, true );
    }
    else if((newWindowWidth >= 600) && (newWindowWidth < 800))
    {
      oTable.fnSetColumnVis( 5, true );
      oTable.fnSetColumnVis( 4, false );
    }
    else if(newWindowWidth < 600)
    {
      oTable.fnSetColumnVis( 5, false );
      oTable.fnSetColumnVis( 4, false );
    }
    

};


} ); //end of document(ready) function
 

 
  function fnGetSelected( oTableLocal )
    {
      return oTableLocal.$('tr.row_selected');
    }

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
            <li><a href="<?php echo base_url()?>student">Μαθητολόγιο</a></li>
            <li><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
            <li class="active"><a href="<?php echo base_url()?>section">Τμήματα</a></li>
            <li><a href="<?php echo base_url()?>finance">Οικονομικά</a></li>
            <li><a href="#reports">Αναφορές</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Χρήστης<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header"><?php echo $user->surname.' '.$user->name;?></li>
                <li><a href="#">Αλλαγή κωδικού</a></li>
                <li><a href="<?php echo base_url()?>section/logout">Αποσύνδεση</a></li>
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
    <h1>Τμήματα Φροντιστηρίου</h1>
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

    <div class="container" style="padding-top:10px; padding-bottom:70px;">
      
      <div>
      <ul class="breadcrumb">
        <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a></li>
        <li class="active">Τμήματα</li>
      </ul>
      </div>

      <div class="panel panel-default">
        <div class="panel-heading">
          <span class="icon">
            <i class="icon-sitemap"></i>
          </span>
          <h3 class="panel-title">Τμήματα</h3>
        </div>
      <div class="panel-body">
       <div class="row" >
        <div class="col-md-12">
          <div class="btn-toolbar" role="toolbar" style="margin-bottom:10px;">
            <button class="btn btn-sm btn-danger pull-left" id="del-reg"><i class="icon-trash"></i></button>
             <div class="btn-group pull-left">
              <!-- <button class="btn btn-default btn-sm"><i class="icon-refresh"></i></button> -->
              <button class="btn btn-default btn-sm" id="new-reg"><i class="icon-plus"></i></button>
            </div>
            <button class="btn btn-sm btn-success pull-right" id="section-card"><i class="icon-tag"> </i> Καρτέλα Τμήματος</button>
          </div>
        </div>
        </div>
      <!--width="100%" option in the table is required when there are hidden columns in the table to resize properly on window change-->
      <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" id="stdbook" width="100%">
        <thead>
          <tr>
            <th>id</th>
            <th>Τμήμα</th>
            <th>Μάθημα</th>
            <th>Διδάσκων</th>
            <th>Τάξη</th>
            <th>Κατεύθυνση</th>
          </tr>
        </thead>
        <tbody>
          <?php if($section):?>
            <?php foreach ($section as $data):?>
              <tr>
                <td><?php echo $data["id"];?></td>
                <td><?php echo $data["section"];?></td>
                <td><?php echo $data["title"];?></td>
                <td><?php echo $data["name"];?></td>
                <td><?php echo $data["class_name"];?></td>
                <td><?php echo $data["course"];?></td>
              </tr>            
            <?php endforeach;?>
          <?php endif;?>
        </tbody>
<!--         <tfoot>
        <tr>
          <th></th>
          <th></th>
         </tr>
        </tfoot> -->
      </table>
    </div>
    </div>
    </div> <!--end of main container-->

<div class="push"></div>
</div> <!-- end of body wrapper-->
