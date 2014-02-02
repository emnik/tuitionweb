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
    
    /* Add a click handler for the employee-card btn */
    $('#employee-card').click( function() {
        var anSelected = fnGetSelected( oTable );
        if ( anSelected.length !== 0 ) {
            var aRow=anSelected[0];
            var id=oTable.fnGetData( aRow, 0 );
            window.open ('staff/card/'+id,'_self',false);
            //alert(id);
        }
        else
        {
           alert("Δεν έχετε επιλέξει κανέναν εργαζόμενο.");
        }
    });

    /* Add a click handler for the new-reg btn */
    $('#new-reg').click(function(){
      window.open ('staff/newreg','_self',false);
    });

    /* Add a click handler for the del-reg btn */
    $('#del-reg').click(function(){
        var anSelected = fnGetSelected( oTable );
        if ( anSelected.length !== 0 ) {
            var r=confirm("Ο εργαζόμενος που επιλέξατε πρόκειται να διαγραφεί. Παρακαλώ επιβεβαιώστε.");
            if (r==true)
            {
                var aRow=anSelected[0];
                var id=oTable.fnGetData( aRow, 0 );
                window.open ('staff/delreg/'+id,'_self',false);  
            }
         }
         else
         {
          alert("Δεν έχετε επιλέξει κανέναν εργαζόμενο.");
         }
    });

    /* Init the table */
    oTable = $('#stdbook').dataTable( {
    //"sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
    "sDom": "<'row'<'col-xs-6 pull-left' l><'col-xs-6 pull-right' f> r><'row'<'col-md-12't>><'row'<'col-md-6'i><'col-md-6'p>>",
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
              "sInfo": "Εμφανίζονται οι _START_ έως _END_ από τους _TOTAL_ εργαζομένους",
              "sInfoEmpty": "Εμφάνιζονται 0 εγγραφές",
              "sInfoFiltered": "Φιλτράρισμα από _MAX_ συνολικά εργαζομένους",
              //"sLengthMenu": "_MENU_ εργαζόμενοι ανά σελίδα",
              "sLengthMenu": "_MENU_",
              "sLoadingRecords": "Φόρτωση καταλόγου προσωπικού...",
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

       } );

   //bootstrap3 style fixes until datatables 1.10 is released with bootstrap3 support

   $('#stdbook_filter').find('input').addClass("form-control");
   $('#stdbook_filter label').contents().unwrap();
   var fgroupDiv = document.createElement('div');
   fgroupDiv.id="fgroupDiv";
   fgroupDiv.className = 'form-group pull-right';
   $('#stdbook_filter').append(fgroupDiv);
   $('#stdbook_filter').find('input').prependTo('#fgroupDiv');
   $('#stdbook_filter').find('input').attr('id','inputid');
   $('#stdbook_filter').find('input').css({'max-width':'200px'});
   var $searchlabel = $("<label>").attr('for', "#inputid");
   $searchlabel.css({'margin-top':'5px','margin-bottom':'5px','margin-left':'0px', 'margin-right':'10px'})
   $searchlabel.addClass('pull-left');
   $searchlabel.text('Αναζήτηση:');
   $searchlabel.insertBefore('#inputid');

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
   $sellabel.css({'min-width':'130px', 'margin-top':'5px'});
   $sellabel.text('Εργαζόμενοι/σελ.: ');
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
      oTable.fnSetColumnVis( 3, true );
    }
    else if((newWindowWidth >= 600) && (newWindowWidth < 800))
    {
      oTable.fnSetColumnVis( 3, true );
      oTable.fnSetColumnVis( 4, false );
    }
    else if(newWindowWidth < 600)
    {
      oTable.fnSetColumnVis( 3, false );
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
           <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Λειτουργία<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo base_url()?>student">Μαθητολόγιο</a></li>
                <li><a href="<?php echo base_url()?>exam">Διαγωνίσματα</a></li>
                <li><a href="<?php echo base_url()?>files">Αρχεία</a></li>
                <li><a href="<?php echo base_url()?>cashdesk">Ταμείο</a></li>
                <li><a href="<?php echo base_url()?>announcements">Ανακοινώσεις</a></li>
              </ul>
            </li>
           <li class="dropdown">
              <a href="#" class="dropdown-toggle active" data-toggle="dropdown">Οργάνωση/Διαχείριση<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="active"><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
                <li><a href="<?php echo base_url()?>section">Τμήματα</a></li>
                <li><a href="<?php echo base_url()?>">Πρόγραμμα Σπουδών</a></li>
                <li><a href="<?php echo base_url()?>">Μαθήματα-Διδάσκωντες</a></li>
                <li><a href="<?php echo base_url()?>">Στοιχεία Φροντιστηρίου</a></li>
              </ul>
            </li>
           <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Συγκεντρωτικές Αναφορές<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo base_url()?>">Αναφορές</a></li>
                <li><a href="<?php echo base_url()?>">Ιστορικό</a></li>
                <li><a href="<?php echo base_url()?>">Τηλ. Κατάλογοι</a></li>
                <li><a href="<?php echo base_url()?>finance">Οικονομικά</a></li>
              </ul>
            </li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Χρήστης<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header"><?php echo $user->surname.' '.$user->name;?></li>
                <li><a href="#">Αλλαγή κωδικού</a></li>
                <li><a href="<?php echo base_url()?>staff/logout">Αποσύνδεση</a></li>
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
    <h1>Προσωπικό Φροντιστηρίου</h1>
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
        <li class="active">Προσωπικό</li>
      </ul>
      </div>

      <div class="panel panel-default">
        <div class="panel-heading">
          <span class="icon">
            <i class="icon-book"></i>
          </span>
          <h3 class="panel-title">Προσωπικό</h3>
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
            <button class="btn btn-sm btn-success pull-right" id="employee-card"><i class="icon-user"> </i> Καρτέλα Εργαζομένου</button>
          </div>
        </div>
        </div>
      <!--width="100%" option in the table is required when there are hidden columns in the table to resize properly on window change-->
      <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="stdbook" width="100%">
        <thead>
          <tr>
            <th>id</th>
            <th>Επώνυμο</th>
            <th>Όνομα</th>
            <th>Ειδικότητα</th>
            <th>Κατάσταση</th>
          </tr>
        </thead>
        <tbody>
          <?php if($employees):?>
            <?php foreach ($employees as $data):?>
              <tr>
                <td><?php echo $data["id"];?></td>
                <td><?php echo $data["surname"];?></td>
                <td><?php echo $data["name"];?></td>
                <td><?php echo $data["speciality"];?></td>
                <td><?php if($data["active"]==0){echo 'Ανενεργός';} else {echo 'Ενεργός';}?></td>
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
