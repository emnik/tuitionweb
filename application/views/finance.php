<link href="<?php echo base_url('assets/css/dataTables.bootstrap.css') ?>" rel="stylesheet">

<script src="<?php echo base_url('assets/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/js/dataTables.bootstrap.js') ?>"></script>


<script type="text/javascript">

$(document).ready(function(){ 

  var oTable1;
  var oTable2;

  $('#btnecoyearupdate').click(function(){

    $.ajaxSetup({
        beforeSend:function(){
            $('#btnecoyearupdate').button('loading');
            $('#ecomessage').html('Παρακαλώ περιμένετε. Οι υπολογισμοί μπορεί να διαρκέσουν λίγη ώρα...');
        },
        complete:function(){
            $('#btnecoyearupdate').button('reset');
            $('#ecomessage').html('Τα οικονομικά στοιχεία για το οικονομικό έτος ενημερώθηκαν με τα τελευταία δεδομένα!');
        }
    });

    $.ajax({  
              type: "POST",  
              url: "<?php echo base_url()?>finance/update_ecofinance_data",  
              success: function(result) {  
                  if (result!=false){
                      oTable2.fnReloadAjax();
                  };
              }
          });
    
    });



  $('#btnschoolyearupdate').click(function(){

    $.ajaxSetup({
        beforeSend:function(){
            $('#btnschoolyearupdate').button('loading');
            $('#schmessage').html('Παρακαλώ περιμένετε. Οι υπολογισμοί μπορεί να διαρκέσουν λίγη ώρα...');
        },
        complete:function(){
            $('#btnschoolyearupdate').button('reset');
            $('#schmessage').html('Τα οικονομικά στοιχεία για το σχολικό έτος ενημερώθηκαν με τα τελευταία δεδομένα!');
        }
    });

    $.ajax({  
              type: "POST",  
              url: "<?php echo base_url()?>finance/update_schfinance_data",  
              success: function(result) {  
                  if (result!=false){
                      oTable1.fnReloadAjax();
                  };
              }
          });
    
    });

    /* Init the schoolyear finance table */
    
    oTable1 = $('#schfinancetable').dataTable( {
        "bProcessing": true,
        "bServerSide": true, 
        "sAjaxSource": "<?php echo base_url();?>finance/getschfinancedata",
        //"aoColumnDefs": [/*stdlesson_id*/{ "bVisible": false, "aTargets": [5] }],
        "aoColumns": [
            { "mData": "Μήνας",
              "sClass":"col-md-3"},
            { "mData": "Εισπράξεις",
              "sClass":"col-md-3",
              "mRender": function (data, type, full) {
                return data+'€';
              }
            },
            { "mData": "Οφειλές",
            "sClass":"col-md-3",
             "mRender": function (data, type, full) {
                return data+'€';
              }
            },
            { "mData": "Τζίρος",
            "sClass":"col-md-3",
             "mRender": function (data, type, full) {
                return data+'€';
             }},
             ],
        "sDom": "<'row'<'col-md-12'rt>>",
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


    /* Init the economic year finance table */
    oTable2 = $('#ecofinancetable').dataTable( {
        "bProcessing": true,
        "sAjaxSource": "<?php echo base_url();?>finance/getecofinancedata",
        //"aoColumnDefs": [/*stdlesson_id*/{ "bVisible": false, "aTargets": [5] }],
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
        "sDom": "<'row'<'col-md-12'rt>>",
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

}) //end of (document).ready(function())

//fnReloadAjax is not part of DataTables core. As a plug-in, we need to add the following code
$.fn.dataTableExt.oApi.fnReloadAjax = function ( oSettings, sNewSource, fnCallback, bStandingRedraw )
{
    if ( typeof sNewSource != 'undefined' && sNewSource != null )
    {
        oSettings.sAjaxSource = sNewSource;
    }
    this.oApi._fnProcessingDisplay( oSettings, true );
    var that = this;
    var iStart = oSettings._iDisplayStart;
     
    oSettings.fnServerData( oSettings.sAjaxSource, [], function(json) {
        /* Clear the old information from the table */
        that.oApi._fnClearTable( oSettings );
         
        /* Got the data - add it to the table */
        for ( var i=0 ; i<json.aaData.length ; i++ )
        {
            that.oApi._fnAddData( oSettings, json.aaData[i] );
        }
         
        oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
        that.fnDraw();
         
        if ( typeof bStandingRedraw != 'undefined' && bStandingRedraw === true )
        {
            oSettings._iDisplayStart = iStart;
            that.fnDraw( false );
        }
         
        that.oApi._fnProcessingDisplay( oSettings, false );
         
        /* Callback user function - for event handlers etc */
        if ( typeof fnCallback == 'function' && fnCallback != null )
        {
            fnCallback( oSettings );
        }
    }, oSettings );
};


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
            <li><a href="<?php echo base_url()?>section">Τμήματα</a></li>
            <li class="active"><a href="<?php echo base_url()?>finance">Οικονομικά</a></li>
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
    <h1>Οικονομικά</h1>
    <p class="leap">Πρόγραμμα διαχείρισης φροντιστηρίου.</p>
  </div>
</div>


<!-- main container
================================================== -->

  <div class="container"  style="margin-bottom:60px;">
           
      <div>
	      <ul class="breadcrumb">
	        <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a></li>
	        <li class="active">Οικονομικά</li>
	      </ul>
      </div>
      
     <p> 
      <h3>
        Οικονομικά
      </h3>
    </p>
        

      <ul class="nav nav-tabs">
        <li class="active"><a href="<?php echo base_url()?>finance">Σύνοψη</a></li>
        <li><a href="<?php echo base_url()?>finance">Ανάλυση</a></li>
      </ul>

      <p></p>

<!--       <div class="visible-sm visible-xs">
        <div class="row">
          <div class="col-md-12">
            <div class="btn-group pull-left">  
              <a class="btn btn-default btn-sm" href="#group1">Εργαζομένου</a>
              <a class="btn btn-default btn-sm" href="#group2">Πρόσληψης</a>
            </div>
          </div>      
        </div>
      </div> -->
     

	<div class="row">

    	<div class="col-md-12">
        
      	<div class="row">
          <div class="col-md-6" id="group1">
			       <div class="mainform">
       
          <div class="panel panel-default">
            <div class="panel-heading">
              <span class="icon">
                <i class="icon-tag"></i>
              </span>
              <h3 class="panel-title">Σύνοψη σχολικού έτους</h3>
<!--               <div class="buttons">
                  <button enabled id="editform1" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
              </div> -->
            </div>
	            <div class="panel-body">
	        	       <div class="row">	
	        	    		<div class="col-md-12 col-sm-12">
                        <table id="schfinancetable" class="table table-striped table-condensed" width="100%">
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
                      <p id="schmessage" class="text-info">Tελευταία ενημέρωση των οικονομικών δεδομένων για το σχολικό έτος: <?php $m=(!isset($schoolyear_update)) ? "Δεν υπάρχει!" : $schoolyear_update; echo '<strong>'.$m.'</strong>';?></p>
                      <p><button id="btnschoolyearupdate" type="button" data-loading-text="Ανανέωση..." class="btn btn-primary">Ανανέωση τώρα</button></p>
                    </div>
	        	    	</div>
		        	   </div> <!-- end of panel body -->
	         </div>
	      </div>
	  </div> <!-- end of group#1 -->
	  	<div class="col-md-6" id="group2">
          <div class="mainform">
	          <div class="panel panel-default">
	            <div class="panel-heading">
	              <span class="icon">
	                <i class="icon-tag"></i>
	              </span>
	              <h3 class="panel-title">Σύνοψη οικονομικού έτους</h3>
<!-- 	              <div class="buttons">
	                  <button enabled id="editform2" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
	              </div> -->
	            </div>
	            <div class="panel-body">
	        	       <div class="row">	
			              <div class="col-md-12  col-sm-12">
                        <table id="ecofinancetable" class="table table-striped table-condensed" width="100%">
                        <thead>
                          <tr>
                            <th>Μήνες</th>
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
                      <p id="ecomessage" class="text-info">Tελευταία ενημέρωση των οικονομικών δεδομένων για το οικονομικό έτος: <?php $m=(!isset($economicyear_update)) ? "Δεν υπάρχει!" : $economicyear_update; echo '<strong>'.$m.'</strong>';?>
                      <p><button id="btnecoyearupdate" type="button" data-loading-text="Ανανέωση..." class="btn btn-primary">Ανανέωση τώρα</button></p>
                    </div>

	        	    	</div>
	        	   </div> <!-- end of pabel-body -->
	            </div> <!-- end of panel -->
	          </div> <!-- end of mainform -->
  	      </div> <!-- end of group#2 -->
	      </div>     


    </div> 
  </div>
</div><!--end of main container-->

<div class="push"></div>

</div> <!-- end of body wrapper-->