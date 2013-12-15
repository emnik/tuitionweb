<!--<link href="<?php echo base_url('assets/css/demo_table.css') ?>" rel="stylesheet">-->
<link href="<?php echo base_url('assets/css/dataTables.bootstrap.css') ?>" rel="stylesheet">

<script src="<?php echo base_url('assets/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/js/dataTables.bootstrap.js') ?>"></script>


<script type="text/javascript">

/* Table initialisation */

var oTable; 
var asInitVals = new Array(); //for specific columns filtering with input field below

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
    
    /* Add a click handler for the student-card btn */
    $('#student-card').click( function() {
        var anSelected = fnGetSelected( oTable );
        if ( anSelected.length !== 0 ) {
            var aRow=anSelected[0];
            var id=oTable.fnGetData( aRow, 0 );
            window.open ('student/card/'+id,'_self',false);
            //alert(id);
        }
        else
        {
           alert("Δεν έχετε επιλέξει κανένα μαθητη.");
        }
    });

    /* Add a click handler for the new-reg btn */
    $('#new-reg').click(function(){
      window.open ('student/newreg','_self',false);
    });

    /* Add a click handler for the del-reg btn */
    $('#del-reg').click(function(){
        var anSelected = fnGetSelected( oTable );
        if ( anSelected.length !== 0 ) {
            var r=confirm("Ο μαθητής που επιλέξατε πρόκειται να διαγραφεί. Παρακαλώ επιβεβαιώστε.");
            if (r==true)
            {
                var aRow=anSelected[0];
                var id=oTable.fnGetData( aRow, 0 );
                window.open ('student/delreg/'+id,'_self',false);  
            }
         }
         else
         {
          alert("Δεν έχετε επιλέξει κανένα μαθητη.");
         }
    });

    /* Init the table */
    oTable = $('#stdbook').dataTable( {
    //"sDom": "<'row'<'col-md-6'l><'col-md-6' f> r>t<'row'<'col-md-6'i><'col-md-6'p>>",
    "sDom": "<'row'<'col-xs-6 pull-left' l><'col-xs-6 pull-right' f> r><'row'<'col-md-12't>><'row'<'col-md-6'i><'col-md-6'p>>",
    "sPaginationType": "bootstrap",
    "aoColumnDefs": [
      { "bVisible": false, "aTargets": [0] }, //hide id column
      { "bSearchable": false, "aTargets": [4,5] }  //don't filter class name and course
      //they will be filtered via input boxes in the table footer!
    ],
    "oLanguage": {
              "oPaginate": {
                  "sFirst":    "Πρώτη",
                  "sPrevious": "",
                  "sNext":     "",
                  "sLast":     "Τελευταία"
              },
              "sInfo": "Εμφανίζονται οι _START_ έως _END_ από τους _TOTAL_ μαθητές",
              "sInfoEmpty": "Εμφάνιζονται 0 εγγραφές",
              "sInfoFiltered": "Φιλτράρισμα από _MAX_ συνολικούς μαθητές",
              //"sLengthMenu": "_MENU_ μαθητές ανά σελίδα",
              "sLengthMenu": "_MENU_",
              "sLoadingRecords": "Φόρτωση μαθητολογίου...",
              "sProcessing": "Επεξεργασία...",   
              //"sSearch": "Εύρεση μαθητή:",
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
  
    <?php if(!$students):?>
      $('#myModal').modal('show');
    <?php endif;?>

   //bootstrap3 style fixes until datatables 1.10 is released with bootstrap3 support

   $('#stdbook_filter').find('input').addClass("form-control");
   $('#stdbook_filter label').contents().unwrap();
   var fgroupDiv = document.createElement('div');
   fgroupDiv.id="fgroupDiv"
   fgroupDiv.className = 'form-group pull-right';
   $('#stdbook_filter').append(fgroupDiv);
   $('#stdbook_filter').find('input').prependTo('#fgroupDiv');
   $('#stdbook_filter').find('input').attr('id','inputid');
   $('#stdbook_filter').find('input').css({'max-width':'250px','float':'right'});
   var $searchlabel = $("<label>").attr('for', "#inputid");
   $searchlabel.text('Αναζήτηση:');
   $searchlabel.insertBefore('#inputid');

   $('#stdbook_length').find('select').addClass("form-control");
   $('#stdbook_length label').contents().unwrap();
   var lgroupDiv = document.createElement('div');
   lgroupDiv.id="lgroupDiv"
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
   $sellabel.text('Μαθητές/σελ.: ');
   $sellabel.insertBefore('#selectid');

   $('#stdbook_filter').parent().parent().css({'padding-bottom':'8px'});


// HIDING COLUMNS FOR RESPONSIVE VIEW:

$(window).on("load", resizeWindow);
//If the User resizes the window, adjust the #container height
$(window).on("resize", resizeWindow);

function resizeWindow(e)
{
    var newWindowWidth = $(window).width();

    if(newWindowWidth > 1024)
    {
      oTable.fnSetColumnVis( 1, true );
      oTable.fnSetColumnVis( 4, true );
      oTable.fnSetColumnVis( 5, true );
    }
    else if((newWindowWidth >= 600) && (newWindowWidth <= 1024))
    {
      oTable.fnSetColumnVis( 1, true );
      oTable.fnSetColumnVis( 4, true );
      oTable.fnSetColumnVis( 5, false );
    }
    else if((newWindowWidth >= 440) && (newWindowWidth < 600))
    {
      oTable.fnSetColumnVis( 1, true );
      oTable.fnSetColumnVis( 4, false );
      oTable.fnSetColumnVis( 5, false );
    }
    else if(newWindowWidth < 440)
    {
      oTable.fnSetColumnVis( 1, false );
      oTable.fnSetColumnVis( 4, false );
      oTable.fnSetColumnVis( 5, false );
    }

};

  //INDIVIDUAL COLUMN FILTERING
  //To filter individual columns we add the input keys tou the table footer (see table code)

  $("tfoot input").keyup( function () {
    /* Filter on the column (the index) of this element */
    oTable.fnFilter( this.value, $("tfoot input").index(this)+4);//+4 is needed for getting the right  column index because I don't have input in every column!!! 
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
            <li class="active"><a href="<?php echo base_url()?>student">Μαθητολόγιο</a></li>
            <li><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
            <li><a href="<?php echo base_url()?>section">Τμήματα</a></li>
            <li><a href="#finance">Οικονομικά</a></li>
            <li><a href="#reports">Αναφορές</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Χρήστης<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header"><?php echo $user->surname.' '.$user->name;?></li>
                <li><a href="#">Αλλαγή κωδικού</a></li>
                <li><a href="#admin">Διαχείριση</a></li>
                <li class="divider"></li>
                <li><a href="<?php echo base_url()?>student/logout">Αποσύνδεση</a></li>
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
    <h1>Μαθητολόγιο</h1>
    <p class="leap">Πρόγραμμα διαχείρισης φροντιστηρίου.</p>
  </div>
</div>


<!-- main container
================================================== -->

    <div class="container" style="padding-top:10px; padding-bottom:70px;">
      
      <div>
      <ul class="breadcrumb">
        <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a></li>
        <li class="active">Μαθητολόγιο</li>
      </ul>
      </div>


    <div class="panel panel-default">
       <div class="panel-heading">
          <span class="icon">
            <i class="icon-book"></i>
          </span>
          <h3 class="panel-title">Μαθητολόγιο</h3>
       </div>
    <div class="panel-body">
      <div class="row" >
        <div class="col-md-12">
          <div class="btn-toolbar" role="toolbar" style="margin-bottom:10px;">
            <button class="btn btn-sm btn-danger pull-left" id="del-reg"><i class="icon-trash"></i></button>
             <div class="btn-group pull-left">
              <button class="btn btn-default btn-sm"><i class="icon-refresh"></i></button>
              <button class="btn btn-default btn-sm" id="new-reg"><i class="icon-plus"></i></button>
            </div>
            <button class="btn btn-sm btn-success pull-right" id="student-card"><i class="icon-user"> Καρτέλα Μαθητή</i></button>
          </div>
        </div>
        </div>
      <!--width="100%" option in the table is required when there are hidden columns in the table to resize properly on window change-->
      <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="stdbook" width="100%">
        <thead>
          <tr>
            <th>id</th>
            <th>Αρ.Μαθητολογίου</th>
            <th>Επώνυμο</th>
            <th>Όνομα</th>
            <th>Τάξη</th>
            <th>Κατεύθυνση</th>
          </tr>
        </thead>
        <tbody>
          <?php if($students):?>
            <?php foreach ($students as $data):?>
              <tr>
                <td><?php echo $data["id"];?></td>
                <td><?php echo $data["std_book_no"];?></td>
                <td><?php echo $data["surname"];?></td>
                <td><?php echo $data["name"];?></td>
                <td><?php echo $data["class_name"];?></td>
                <td><?php echo $data["course"];?></td>
              </tr>            
            <?php endforeach;?>
          <?php endif;?>
        </tbody>
        <tfoot>
          <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th><input type="text" class="search_init form-control" name="search_classnames" value="Φίλτρο τάξεων" class="search_init" /></th>
            <th><input type="text" class="search_init form-control" name="search_coursenames" value="Φίλτρο κατευθύνσεων" class="search_init" /></th>
          </tr>
        </tfoot>
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
          <h3 id="myModalLabel">Κενό Μαθητολόγιο</h3>
        </div>
        <div class="modal-body">
          <p>Δέν έχετε εισάγει καμία εγγραφή στο μαθητολόγιο για το σχολικό έτος που επιλέξατε. 
            Μπορείτε είτε να προχωρήσετε σε μια νέα εγγραφή, είτε να επιστρέψετε στην αρχική σελίδα και 
            να επιλέξετε ένα προηγούμενο σχολικό έτος για επανεγγραφή παλαιοτέρων μαθητών.</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Κλείσιμο</button>
          <a href="<?php echo base_url();?>" class="btn btn-default">Επιστροφή στην αρχ. σελίδα</a>
          <a href="#" class="btn btn-primary">Νέα εγγραφή</a>
        </div>
      </div>
    </div>
</div>