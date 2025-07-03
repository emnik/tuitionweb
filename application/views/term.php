<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/datatables_bundle/datatables.css') ?>" />
<script type="text/javascript" src="<?php echo base_url('assets/datatables_bundle/datatables.js') ?>"></script>
<link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap.min.css" rel="stylesheet">

<script type="text/javascript">



/* Table initialisation */

var oTable; 

$(document).ready(function() {

    //Menu current active links and Title
    // $('#menu-section').addClass('active');
    $('#menu-header-title').text('Διαχειριστικές Περίοδοι');
  
     /* Add/remove class to a row when clicked on */
    $('#termtable tbody tr').click( function( e ) {
        if ( $(this).hasClass('row_selected') ) {
            $(this).removeClass('row_selected');
          }
          else {
            oTable.$('tr.row_selected').removeClass('row_selected');
            $(this).addClass('row_selected');
          }
    } );
    
    /* Add a click handler for the term-card btn */
    $('#term-card').click( function() {
        var anSelected = fnGetSelected( oTable );
        if ( anSelected.length !== 0 ) {
            var aRow=anSelected[0];
            var id = oTable.row(aRow).data()[0];
            window.open ('<?php echo base_url("term/card");?>/'+id,'_self',false);
            //alert(id);
        }
        else
        {
           alert("Δεν έχετε επιλέξει καμία διαχειριστική περίοδο.");
        }
    });

    /* Add a click handler for the new-term btn */
    $('#new-term').click(function(){
      window.open ('<?php echo base_url("term/newterm");?>','_self',false);
    });

    /* Add a click handler for the del-term btn */
    $('#del-term').click(function(){
        var anSelected = fnGetSelected( oTable );
        if ( anSelected.length !== 0 ) {
            var r=confirm("Η διαχειριστική περίοδος που επιλέξατε πρόκειται να διαγραφεί. Παρακαλώ επιβεβαιώστε.");
            if (r==true)
            {
                var aRow=anSelected[0];
                var id = oTable.row(aRow).data()[0];
                window.open ('<?php echo base_url("term/delterm");?>/'+id,'_self',false);  
            }
         }
         else
         {
          alert("Δεν έχετε επιλέξει καμία διαχειριστική περίοδο.");
         }
    });

    /* Init the table */
    oTable = $('#termtable').DataTable( {
        "responsive": true,
        "order": [[ 0, "desc" ]],
        "columnDefs": [
                { "visible": false, "targets": 0 }
            ],
        "language": {
            "paginate": {
            "first": "Πρώτη",
            "previous": "",
            "next": "",
            "last": "Τελευταία"
            },
        "info": "Εμφανίζονται οι _START_ έως _END_ από τους _TOTAL_ περιόδους",
        "infoEmpty": "Εμφάνιζονται 0 εγγραφές",
        "infoFiltered": "Φιλτράρισμα από _MAX_ συνολικές περιόδους",
        "lengthMenu": "Περίοδοι ανά σελίδα: _MENU_ ",
        "loadingRecords": "Φόρτωση λίστας περιόδων...",
        "processing": "Επεξεργασία...",
        "search": "Αναζήτηση: ",
        "zeroRecords": "Δεν βρέθηκαν εγγραφές"
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
        
<!-- main container
================================================== -->

    <div class="container" style="padding-top:10px; padding-bottom:70px;">
      
      <div>
      <ul class="breadcrumb">
        <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a></li>
        <li class="active">Διαχειριστικές Περίοδοι</li>
      </ul>
      </div>

      <div class="panel panel-default">
        <div class="panel-heading">
          <span class="icon">
            <i class="icon-tags"></i>
          </span>
          <h3 class="panel-title">Διαχειριστικές Περίοδοι</h3>
        </div>
      <div class="panel-body">
       <div class="row" >
        <div class="col-md-12">
          <div class="btn-toolbar" role="toolbar" style="margin-bottom:10px;">
            <button class="btn btn-sm btn-danger pull-left" id="del-term"><i class="icon-trash"></i></button>
             <div class="btn-group pull-left">
              <!-- <button class="btn btn-default btn-sm"><i class="icon-refresh"></i></button> -->
              <button class="btn btn-default btn-sm" id="new-term"><i class="icon-plus"></i></button>
            </div>
            <button class="btn btn-sm btn-openpage pull-right" id="term-card"><i class="icon-tag"> </i> Καρτέλα διαχ. περιόδου</button>
          </div>
        </div>
        </div>
      <!--width="100%" option in the table is required when there are hidden columns in the table to resize properly on window change-->
      <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" id="termtable" width="100%">
        <thead>
          <tr>
            <th>id</th>
            <th>Όνομα</th>
            <th>Έναρξη</th>
            <th>Λήξη</th>
            <th>Ενεργή</th>
          </tr>
        </thead>
        <tbody>
          <?php if($term):?>
            <?php foreach ($term as $data):?>
              <tr>
                <td><?php echo $data["id"];?></td>
                <td><?php echo $data["name"];?></td>
                <td><?php echo date_format(date_create(!empty($data["start"])?$data["start"]:""),"d/m/Y");?></td>
                <td><?php echo date_format(date_create(!empty($data["end"])?$data["end"]:""),"d/m/Y");?></td>
                <td><?php if($data["active"]==1){echo '<input type="checkbox" class="editor-active" onclick="return false;" checked>';} else {echo '<input type="checkbox" class="editor-active" onclick="return false;">';}?></td>
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
