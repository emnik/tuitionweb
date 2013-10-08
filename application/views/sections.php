<!--<link href="<?php echo base_url('assets/css/demo_table.css') ?>" rel="stylesheet">-->
<link href="<?php echo base_url('assets/css/dataTables.bootstrap.css') ?>" rel="stylesheet">

<script src="<?php echo base_url('assets/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/js/dataTables.bootstrap.js') ?>"></script>


<script type="text/javascript">

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
    "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
    "sPaginationType": "bootstrap",
    "aoColumnDefs": [
      { "bVisible": false, "aTargets": [0] }, //hide id column
      { "bSearchable": false, "aTargets": [4] } 
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
              "sLengthMenu": "_MENU_ τμήματα ανά σελίδα",
              "sLoadingRecords": "Φόρτωση καταλόγου τμημάτων...",
              "sProcessing": "Επεξεργασία...",   
              "sSearch": "Εύρεση:",
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

       } );

    //bootstrap3 add class="form-control" to inputs"
    $('#stdbook_filter').find('input').addClass("form-control");
    $('#stdbook_length').find('select').addClass("form-control");
    
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

    <div class="navbar navbar-inverse navbar-fixed-top">
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
            <!-- <li><a href="<?php echo base_url()?>">Αρχική</a></li>  -->
            <li><a href="<?php echo base_url()?>student">Μαθητολόγιο</a></li>
            <li><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
            <li class="active"><a href="<?php echo base_url()?>section">Τμήματα</a></li>
            <li><a href="#finance">Οικονομικά</a></li>
            <li><a href="#reports">Αναφορές</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Χρήστης<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header">Νικηφορακης Μανος</li>
                <li><a href="#">Αλλαγή κωδικού</a></li>
                <li><a href="#admin">Διαχείριση</a></li>
                <li class="divider"></li>
                <li><a href="#">Αποσύνδεση</a></li>
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
    <p class="leap">tuition manager - πρόγραμμα διαχείρισης φροντιστηρίου.</p>
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
          <div class="buttons">
            <button class="btn btn-sm btn-danger " id="del-reg"><i class="icon-trash"></i></button>
             <div class="btn-group">
              <button class="btn btn-default btn-sm" id="new-reg"><i class="icon-plus"></i></button>
            </div>
            <button class="btn btn-sm btn-success" id="section-card"><i class="icon-tag"> Καρτέλα Τμήματος</i></button>
          </div>
        </div>
      <div class="panel-body">
      <!--width="100%" option in the table is required when there are hidden columns in the table to resize properly on window change-->
      <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="stdbook" width="100%">
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
      </table>
    </div>
    </div>
    </div> <!--end of main container-->

<div class="push"></div>
</div> <!-- end of body wrapper-->
