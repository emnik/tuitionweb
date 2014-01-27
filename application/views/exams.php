<link href="<?php echo base_url('assets/css/dataTables.bootstrap.css') ?>" rel="stylesheet">

<script src="<?php echo base_url('assets/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/js/dataTables.bootstrap.js') ?>"></script>


<script type="text/javascript">

/* Table initialisation */

var oTable; 
var asInitVals = new Array(); //for specific columns filtering with input field below

$(document).ready(function() {
  


     /* Add/remove class to a row when clicked on */
    $('#examstbl tbody tr').click( function( e ) {
        if ( $(this).hasClass('row_selected') ) {
            $(this).removeClass('row_selected');
          }
          else {
            oTable.$('tr.row_selected').removeClass('row_selected');
            $(this).addClass('row_selected');
          }
    } );
    
    /* Add a click handler for the examdetails btn */
    $('#examdetails').click( function() {
        var anSelected = fnGetSelected( oTable );
        if ( anSelected.length !== 0 ) {
            var aRow=anSelected[0];
            var id=oTable.fnGetData( aRow, 0 );
            window.open ('exams/details/'+id,'_self',false);
            //alert(id);
        }
        else
        {
           alert("Δεν έχετε επιλέξει κανένα διαγώνισμα.");
        }
    });

    /* Add a click handler for the newexam btn */
    $('#newexam').click(function(){
      window.open ('exams/newexam','_self',false);
    });

    /* Add a click handler for the delexam btn */
    $('#delexam').click(function(){
        var anSelected = fnGetSelected( oTable );
        if ( anSelected.length !== 0 ) {
            var r=confirm("Το διαγώνισμα που επιλέξατε πρόκειται να διαγραφεί. Παρακαλώ επιβεβαιώστε.");
            if (r==true)
            {
                var aRow=anSelected[0];
                var id=oTable.fnGetData( aRow, 0 );
                window.open ('exams/delexam/'+id,'_self',false);  
            }
         }
         else
         {
          alert("Δεν έχετε επιλέξει κανένα διαγώνισμα.");
         }
    });

    /* Init the table */
    oTable = $('#examstbl').dataTable( {
    "sDom": "<'row'<'col-xs-6 pull-left' l><'col-xs-6 pull-right' f> r><'row'<'col-md-12't>><'row'<'col-md-6'i><'col-md-6'p>>",
    "sPaginationType": "bootstrap",
    "aoColumnDefs": [
      { "bVisible": false, "aTargets": [0] }, //hide id column
      { "bSearchable": false, "aTargets": [5,6] }  //don't filter time
    ],
    "oLanguage": {
              "oPaginate": {
                  "sFirst":    "Πρώτη",
                  "sPrevious": "",
                  "sNext":     "",
                  "sLast":     "Τελευταία"
              },
              "sInfo": "Εμφανίζονται τα _START_ έως _END_ από τα _TOTAL_ διαγωνίσματα",
              "sInfoEmpty": "Εμφάνιζονται 0 διαγωνίσματα",
              "sInfoFiltered": "Φιλτράρισμα από _MAX_ συνολικά διαγωνίσματα",
              "sLengthMenu": "_MENU_",
              "sLoadingRecords": "Φόρτωση προγραμματισμού...",
              "sProcessing": "Επεξεργασία...",   
              "sSearch": "",
              "sZeroRecords": "Δεν βρέθηκαν διαγωνίσματα"
            }

       } );
  
    <?php if(!$exams):?>
      $('#myModal').modal('show');
    <?php endif;?>

   //bootstrap3 style fixes until datatables 1.10 is released with bootstrap3 support

   $('#examstbl_filter').find('input').addClass("form-control");
   $('#examstbl_filter label').contents().unwrap();
   var fgroupDiv = document.createElement('div');
   fgroupDiv.id="fgroupDiv";
   fgroupDiv.className = 'form-group pull-right';
   $('#examstbl_filter').append(fgroupDiv);
   $('#examstbl_filter').find('input').prependTo('#fgroupDiv');
   $('#examstbl_filter').find('input').attr('id','inputid');
   $('#examstbl_filter').find('input').css({'max-width':'200px'});
   var $searchlabel = $("<label>").attr('for', "#inputid");
   $searchlabel.css({'margin-top':'5px','margin-bottom':'5px','margin-left':'0px', 'margin-right':'10px'})
   $searchlabel.addClass('pull-left');
   $searchlabel.text('Αναζήτηση:');
   $searchlabel.insertBefore('#inputid');

   $('#examstbl_length').find('select').addClass("form-control");
   $('#examstbl_length label').contents().unwrap();
   var lgroupDiv = document.createElement('div');
   lgroupDiv.id="lgroupDiv";
   lgroupDiv.className = 'form-group pull-left';
   var innerlgroupDiv = document.createElement('div');
   innerlgroupDiv.id="innerlgroupDiv"
   innerlgroupDiv.className = 'clearfix';
   $('#examstbl_length').append(lgroupDiv);
   $('#lgroupDiv').append(innerlgroupDiv);
   $('#examstbl_length').find('select').prependTo('#innerlgroupDiv');
   $('#examstbl_length').find('select').attr('id','selectid');
   $('#examstbl_length').find('select').css({'max-width':'75px'});
   var $sellabel = $("<label>").attr('for', "#selectid");
   $sellabel.css({'min-width':'110px', 'margin-top':'5px'});
   $sellabel.text('Μαθητές/σελ.: ');
   $sellabel.insertBefore('#selectid');

   $('#examstbl_filter').parent().parent().css({'padding-bottom':'8px'});


// HIDING COLUMNS FOR RESPONSIVE VIEW:

$(window).on("load", resizeWindow);
//If the User resizes the window, adjust the #container height
$(window).on("resize", resizeWindow);

function resizeWindow(e)
{
    var newWindowWidth = $(window).width();

    if (newWindowWidth >= 1024)
    {
      oTable.fnSetColumnVis( 3, true );
      oTable.fnSetColumnVis( 5, true );
      oTable.fnSetColumnVis( 6, true );
      oTable.fnSetColumnVis( 7, true );
    }
    else if((newWindowWidth >= 600) & (newWindowWidth < 1024) )
    {
      oTable.fnSetColumnVis( 3, true );
      oTable.fnSetColumnVis( 5, true );
      oTable.fnSetColumnVis( 6, true );
      oTable.fnSetColumnVis( 7, false );

    }
    else if((newWindowWidth >= 440) && (newWindowWidth < 600))
    {
      oTable.fnSetColumnVis( 3, true );
      oTable.fnSetColumnVis( 5, false );
      oTable.fnSetColumnVis( 6, false );
      oTable.fnSetColumnVis( 7, false );
    }
    else if(newWindowWidth < 440)
    {
      oTable.fnSetColumnVis( 3, false );
      oTable.fnSetColumnVis( 5, false );
      oTable.fnSetColumnVis( 6, false );
      oTable.fnSetColumnVis( 7, false );
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
            <!-- <li><a href="<?php echo base_url()?>">Αρχική</a></li>  -->
            <li class="active"><a href="<?php echo base_url()?>student">Μαθητολόγιο</a></li>
            <li><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
            <li><a href="<?php echo base_url()?>section">Τμήματα</a></li>
            <li><a href="<?php echo base_url()?>finance">Οικονομικά</a></li>
            <li><a href="#reports">Αναφορές</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Χρήστης<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header"><?php echo $user->surname.' '.$user->name;?></li>
                <li><a href="#">Αλλαγή κωδικού</a></li>
                <li><a href="<?php echo base_url()?>student/logout">Αποσύνδεση</a></li>
              </ul>
            </li>
        </ul>
      </div><!--/.navbar-collapse -->
    </div>
  </div>




<!-- Subhead
================================================== -->
<div class="jumbotron">
  <div class="container">
    <h1>Διαγωνίσματα</h1>
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
        <li class="active">Διαγωνίσματα</li>
      </ul>
      </div>


    <div class="panel panel-default">
       <div class="panel-heading">
          <span class="icon">
            <i class="icon-pencil"></i>
          </span>
          <h3 class="panel-title">Διαγωνίσματα</h3>
       </div>
    <div class="panel-body">
      <div class="row" >
        <div class="col-md-12">
          <div class="btn-toolbar" role="toolbar" style="margin-bottom:10px;">
            <button class="btn btn-sm btn-danger pull-left" id="delexam"><i class="icon-trash"></i></button>
             <div class="btn-group pull-left">
              <button class="btn btn-default btn-sm"><i class="icon-refresh"></i></button>
              <button class="btn btn-default btn-sm" id="newexam"><i class="icon-plus"></i></button>
            </div>
            <button class="btn btn-sm btn-success pull-right" id="examdetails"><i class="icon-pencil"> </i> Επεξεργασία</button>
          </div>
        </div>
        </div>
      <!--width="100%" option in the table is required when there are hidden columns in the table to resize properly on window change-->
      <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="examstbl" width="100%">
        <thead>
          <tr>
            <th>id</th>
            <th>Ημερομηνία</th>
            <th>Τάξη</th>
            <th>Κατεύθυνση</th>
            <th>Μάθημα</th>
            <th>Έναρξη</th>
            <th>Λήξη</th>
            <th>Παρατηρήσεις</th>
          </tr>
        </thead>
        <tbody>
          <?php if($exams):?>
            <?php foreach ($exams as $data):?>
              <tr>
                <td><?php echo $data["id"];?></td>
                <td><?php echo implode('-', array_reverse(explode('-', $data['date'])));?></td>
                <td><?php echo $data["class_name"];?></td>
                <td><?php echo $data["course"];?></td>
                <td><?php echo $data["title"];?></td>
                <td><?php echo date('H:i',strtotime($data["start_tm"]));?></td>
                <td><?php echo date('H:i',strtotime($data["end_tm"]));?></td>
                <td><?php echo $data["notes"];?></td>
              </tr>            
            <?php endforeach;?>
          <?php endif;?>
        </tbody>
      </table>
    </div> <!-- end of content -->
    </div> <!-- end of contentbox -->
    </div> <!--end of main container-->

<div class="push"></div>
</div> <!-- end of body wrapper-->

<!-- Modal -->
<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h3 id="myModalLabel">Δεν βρέθηκε προγραμματισμός</h3>
        </div>
        <div class="modal-body">
          <p>Δέν υπάρχουν καταχωρησεις διαγωνισμάτων. 
            Μπορείτε είτε να προχωρήσετε σε νέα καταχώριση, είτε να επιστρέψετε στην αρχική σελίδα.</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Κλείσιμο</button>
          <a href="<?php echo base_url();?>" class="btn btn-default">Επιστροφή στην αρχ. σελίδα</a>
          <a href="<?php echo base_url();?>exams/newexam" class="btn btn-primary">Νέα καταχώριση</a>
        </div>
      </div>
    </div>
</div>