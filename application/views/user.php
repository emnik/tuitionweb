<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/datatables_bundle/datatables.css') ?>" />
<script type="text/javascript" src="<?php echo base_url('assets/datatables_bundle/datatables.js') ?>"></script>
<link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap.min.css" rel="stylesheet">

<script type="text/javascript">



/* Table initialisation */

var oTable; 

$(document).ready(function() {

    //Menu current active links and Title
    $('#menu-users').addClass('active');
    $('#menu-header-title').text('Λογαριασμοί Χρηστών');
  
     /* Add/remove class to a row when clicked on */
    $('#usertable tbody tr').click( function( e ) {
        if ( $(this).hasClass('row_selected') ) {
            $(this).removeClass('row_selected');
          }
          else {
            oTable.$('tr.row_selected').removeClass('row_selected');
            $(this).addClass('row_selected');
          }
    } );
    
    /* Add a click handler for the user-card btn */
    $('#user-card').click( function() {
        var anSelected = fnGetSelected( oTable );
        if ( anSelected.length !== 0 ) {
            var aRow=anSelected[0];
            var id = oTable.row(aRow).data()[0];
            window.open ('<?php echo base_url("user/card");?>/'+id,'_self',false);
            //alert(id);
        }
        else
        {
           alert("Δεν έχετε επιλέξει κανένα λογαριασμό.");
        }
    });

    /* Add a click handler for the new-user btn */
    $('#new-user').click(function(){
      window.open ('<?php echo base_url("user/newuser");?>','_self',false);
    });

    /* Add a click handler for the del-user btn */
    $('#del-user').click(function(){
        var anSelected = fnGetSelected( oTable );
        if ( anSelected.length !== 0 ) {
            var aRow=anSelected[0];
            var id = oTable.row(aRow).data()[0];
            
            if (id == <?php echo $this->session->userdata('user_id');?>){
                alert('Δεν επιτρέπεται η διαγραφή του λογαρισμού που είναι σε χρήση!');
            }
            else {
                var r=confirm("Ο λογαριασμός που επιλέξατε πρόκειται να διαγραφεί. Παρακαλώ επιβεβαιώστε.");
                if (r==true)
                {
                    window.open ('<?php echo base_url("user/deluser");?>/'+id,'_self',false);  
                }
            }
         }
         else
         {
          alert("Δεν έχετε επιλέξει κανένα λογαριασμό.");
         }
    });

    /* Init the table */
    oTable = $('#usertable').DataTable( {
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
        "info": "Εμφανίζονται οι _START_ έως _END_ από τους _TOTAL_ λογαριαμούς χρηστών",
        "infoEmpty": "Εμφάνιζονται 0 εγγραφές",
        "infoFiltered": "Φιλτράρισμα από _MAX_ συνολικούς λογαριασμούς χρηστών",
        "lengthMenu": "Περίοδοι ανά σελίδα: _MENU_ ",
        "loadingRecords": "Φόρτωση λίστας λογαριασμών χρηστών...",
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
        <li style="font-size:12px;color: #8C7D7F;">Ρυθμίσεις</li>
        <li class="active">Λογαριασμοί χρηστών</li>
      </ul>
      </div>

      <div class="panel panel-default">
        <div class="panel-heading">
          <span class="icon">
            <i class="icon-shield"></i>
          </span>
          <h3 class="panel-title">Λογαριασμοί χρηστών</h3>
        </div>
      <div class="panel-body">
       <div class="row" >
        <div class="col-md-12">
          <div class="btn-toolbar" role="toolbar" style="margin-bottom:10px;">
            <button class="btn btn-sm btn-danger pull-left" id="del-user"><i class="icon-trash"></i></button>
             <div class="btn-group pull-left">
              <!-- <button class="btn btn-default btn-sm"><i class="icon-refresh"></i></button> -->
              <button class="btn btn-default btn-sm" id="new-user"><i class="icon-plus"></i></button>
            </div>
            <button class="btn btn-sm btn-openpage pull-right" id="user-card"><i class="icon-tag"> </i> Λογαριασμός χρήστη</button>
          </div>
        </div>
        </div>
      <!--width="100%" option in the table is required when there are hidden columns in the table to resize properly on window change-->
      <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" id="usertable" width="100%">
        <thead>
          <tr>
            <th>id</th>
            <th>Oνοματεπώνυμο</th>
            <th>Όνομα Χρήστη</th>
            <th>Ομάδα</th>
            <th>Hμερομηνία Δημιουργίας</th>
            <th>Ημερομηνία Λήξης</th>
          </tr>
        </thead>
        <tbody>
          <?php if($user):?>
            <?php foreach ($dbuser as $data):?>
              <tr>
                <td><?php echo $data["id"];?></td>
                <td><?php echo $data["surname"].' '.$data["name"];?></td>
                <td><?php echo $data["username"];?></td>
                <td><?php echo $dbgroup[$data["group_id"]];?></td>
                <td><?php echo date_format(date_create($data["created"]),"d/m/Y");?></td>
                <td><?php if ($data['expires']=='0000-00-00')
                        { 
                            echo 'Ποτέ';
                        } 
                        else {
                            echo date_format(date_create($data["expires"]),"d/m/Y");
                        }?></td>
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
