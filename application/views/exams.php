<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/datatables_bundle/datatables.css') ?>" />
<script type="text/javascript" src="<?php echo base_url('assets/datatables_bundle/datatables.js') ?>"></script>
<link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap.min.css" rel="stylesheet">

<script type="text/javascript">



/* Table initialisation */

var oTable; 
// I deal with ordering by date/time in the model. If this breaks in the future see:
// https://stackoverflow.com/questions/37814191/how-to-sort-datatables-with-date-in-descending-order
// jQuery.extend(jQuery.fn.dataTableExt.oSort, {
//              "date-gr-pre": function ( a ) {
//               var ukDatea = a.split('-');
//               return (ukDatea[0] + ukDatea[1] + ukDatea[2]) * 1;
//            },

//             "date-gr-asc": function ( a, b ) {
//                 return ((a < b) ? -1 : ((a > b) ? 1 : 0));
//              },

//             "date-gr-desc": function ( a, b ) {
//                return ((a < b) ? 1 : ((a > b) ? -1 : 0));
//               }
//             }); 

$(document).ready(function() {

    //Menu current active links and Title
    $('#menu-exams').addClass('active');
    $('#menu-header-title').text('Διαγωνίσματα');
  
     /* Add/remove class to a row when clicked on */
    $('#examtable tbody tr').click( function( e ) {
        if ( $(this).hasClass('row_selected') ) {
            $(this).removeClass('row_selected');
          }
          else {
            oTable.$('tr.row_selected').removeClass('row_selected');
            $(this).addClass('row_selected');
          }
    } );
    
    /* Add a click handler for the exam-card btn */
    $('#exam-card').click( function() {
        var anSelected = fnGetSelected( oTable );
        if ( anSelected.length !== 0 ) {
            var aRow=anSelected[0];
            var id = oTable.row(aRow).data()[0];
            window.open ('<?php echo base_url("exam/card");?>/'+id,'_self',false);
            //alert(id);
        }
        else
        {
           alert("Δεν έχετε επιλέξει κανένα διαγώνισμα.");
        }
    });

    /* Add a click handler for the new-exam btn */
    $('#new-exam').click(function(){
      window.open ('<?php echo base_url("exam/newexam");?>','_self',false);
    });

    /* Add a click handler for the del-exam btn */
    $('#del-exam').click(function(){
        var anSelected = fnGetSelected( oTable );
        if ( anSelected.length !== 0 ) {
            var r=confirm("Το διαγώνισμα που επιλέξατε πρόκειται να διαγραφεί. Παρακαλώ επιβεβαιώστε.");
            if (r==true)
            {
                var aRow=anSelected[0];
                var id = oTable.row(aRow).data()[0];
                window.open ('<?php echo base_url("exam/delexam");?>/'+id,'_self',false);  
            }
         }
         else
         {
          alert("Δεν έχετε επιλέξει κανένα διαγώνισμα.");
         }
    });

    /* Init the table */
    oTable = $('#examtable').DataTable( {
        "responsive": true,
        "ordering": false,
        "bFilter": false,
        // "order": [[ 2, "asc" ]],
        "columnDefs": [
                { "visible": false, "targets": 0 },
                // { "type": "date-gr-asc", "targets": 2 }
            ],
        "language": {
            "paginate": {
            "first": "Πρώτη",
            "previous": "",
            "next": "",
            "last": "Τελευταία"
            },
        "info": "Εμφανίζονται τα _START_ έως _END_ από τα _TOTAL_ διαγωνίσματα",
        "infoEmpty": "Εμφάνιζονται 0 εγγραφές",
        "infoFiltered": "Φιλτράρισμα από _MAX_ διαγωνίσματα",
        "lengthMenu": "Διαγωνίσματα ανά σελίδα: _MENU_ ",
        "loadingRecords": "Φόρτωση λίστας διαγωνισμάτων...",
        "processing": "Επεξεργασία...",
        "search": "Αναζήτηση: ",
        "zeroRecords": "Δεν βρέθηκαν προγραμματισμένα διαγωνίσματα"
        }
        
    })




} ); //end of document(ready) function
 

 
  function fnGetSelected( oTableLocal )
    {
      return oTableLocal.$('tr.row_selected');
    }

</script>


</head>
<body>
  <div class="wrapper"> <!--body wrapper for css sticky footer-->

    <!-- Menu start -->
    <?php include(__DIR__ .'/include/menu.php');?>
    <!-- Menu end -->
        
    <!-- main container ================================================== -->

    <div class="container" style="padding-top:10px; padding-bottom:70px;">
      
      <div>
        <ul class="breadcrumb">
          <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a></li>
          <li class="active">Διαγωνίσματα</li>
        </ul>
      </div>

      <ul class="nav nav-tabs">
        <li class="active"><a href="<?php echo base_url() ?>exam">Διαγωνίσματα</a></li>
        <li><a href="<?php echo base_url() ?>exam/supervisors">Επιτηρήσεις</a></li>
      </ul>

      <p></p>

      <div class="row">
        <div class="col-xs-12">
          <div class="panel panel-default">
            <div class="panel-heading">
              <span class="icon">
                <i class="icon-tags"></i>
              </span>
              <h3 class="panel-title">Προγραμματισμός Διαγωνισμάτων</h3>
            </div>
            <div class="panel-body">
              <div class="row" >
                <div class="col-md-12">
                  <div class="btn-toolbar" role="toolbar" style="margin-bottom:10px;">
                    <button class="btn btn-sm btn-danger pull-left" id="del-exam"><i class="icon-trash"></i></button>
                    <div class="btn-group pull-left">
                      <!-- <button class="btn btn-default btn-sm"><i class="icon-refresh"></i></button> -->
                      <button class="btn btn-default btn-sm" id="new-exam"><i class="icon-plus"></i></button>
                    </div>
                    <button class="btn btn-sm btn-openpage pull-right" id="exam-card"><i class="icon-tag"> </i> Καρτέλα διαγωνίσματος</button>
                  </div>
                </div>
              </div>
              <!--width="100%" option in the table is required when there are hidden columns in the table to resize properly on window change-->
              <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" id="examtable" width="100%">
                <thead>
                  <tr>
                    <th>id</th>
                    <th>Όνομα</th>
                    <th>Ημερομηνία</th>
                    <th>Ώρα έναρξης</th>
                    <th>Ώρα λήξης</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if($exam):?>
                    <?php foreach ($exam as $data):?>
                      <tr>
                        <td><?php echo $data["id"];?></td>
                        <td><?php echo $data["name"];?></td>
                        <td><?php echo implode('-', array_reverse(explode('-', $data['date'])));?></td>
                        <td><?php echo date('H:i', strtotime($data['start']))?></td>
                        <td><?php echo date('H:i', strtotime($data['end']))?></td>
                      </tr>            
                    <?php endforeach;?>
                  <?php endif;?>
                </tbody>
              </table>
            </div>
          </div> <!--end of panel-->
        </div> 
      </div> <!--end of row-->
    </div> <!--end of main container-->

    <div class="push"></div>

  </div> <!-- end of body wrapper-->
