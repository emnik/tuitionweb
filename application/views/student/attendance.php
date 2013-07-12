<link href="<?php echo base_url('assets/css/dataTables.bootstrap.css') ?>" rel="stylesheet">

<script src="<?php echo base_url('assets/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/js/dataTables.bootstrap.js') ?>"></script>


<style type="text/css">

/*responsive tables from http://dbushell.com/demos/tables/rt_05-01-12.html*/
  @media (max-width: 480px) {


    #dayprogram { display: block; position: relative; width: 100%; }
    #dayprogram thead { display: block; float: left; }
    #dayprogram tbody { display: block; width: auto; position: relative; overflow-x: auto; white-space: nowrap; }
    #dayprogram thead tr { display: block; }
    #dayprogram th { display: block; }
    #dayprogram tbody tr { display: inline-block; vertical-align: top; }
    #dayprogram td { display: block; min-height: 1.25em; }
    
}
</style>

<?php if(!empty($attendance_general)){

  $tableData=array('aaData'=>$attendance_general);  
};?>


<script type="text/javascript">

$(document).ready(function(){

	var oTable;

	$('#savedayabsence').click(function(){
		var sData = oTable.$('input').serialize();
		sData = sData+'&'+'stdid='+<?php echo $student['id']?>;
        console.log(sData);
        $.ajax({  
                  type: "POST",  
                  url: "<?php echo base_url()?>student/updatedayabsencedata",  
                  data: sData,
                  success: function(result) {  
                      if (result!=false){
                          oTable.fnReloadAjax();
                      };
                  }
              });
	});


    /* Init the table */
    var newabsencecounter = 0;
    oTable = $('#absencestable').dataTable( {
    "bProcessing": true,
    //"aaData": sData,
    "sAjaxSource": "<?php echo base_url();?>student/getabsencesdata/<?php echo $student['id']?>/",
    //"aoColumnDefs": [/*stdlesson_id*/{ "bVisible": false, "aTargets": [5] }],
    "aoColumns": [
            { "mData": "title" },
            { "mData": "hours" },
            { "mData": "id",
              "sClass": "span2",
              "mRender": function (data, type, full) {
              	  if(data==''){
              	  	whetherchecked = "checked='checked'";
              	  	newabsencecounter = newabsencecounter -1;
                    excusedstatus="disabled";
              	  }
              	  else {
              	  	whetherchecked = "";
              	  	newabsencecounter=data;
                    excusedstatus="enabled";
              	  };
                  return '<label class="radio"><input type="radio" name="todaypresense['+newabsencecounter+']" value="present"' + whetherchecked + '></input></label>';
                  }
            },
            { "mData": "id",
              "sClass": "span2",
              "mRender": function (data, type, full) {
              	  if(data!==''){
              	  	whetherchecked = "checked='checked'";
              	  }
              	  else {
              	  	whetherchecked = "";	
              	  };
                  return '<label class="radio"><input type="radio" name="todaypresense['+newabsencecounter+']" value="absent"' + whetherchecked +'></input></label>';
                  }
            },
            { "mData": "excused",
              "sClass": "span2",
              "mRender": function (data, type, full) {
              	  if(data==1){
              	  	whetherchecked = "checked='checked'";
              	  }
              	  else {
              	  	whetherchecked = "";
              	  };
                  return '<label class="checkbox"><input type="checkbox" name="excused['+newabsencecounter+']"' + whetherchecked + ' ' + excusedstatus +' ></input></label>';
                  }
            },
            { "mData": "stdlesson_id",
              "sClass": "hidden",
              //I use a hidden form field in a visible datatable column to sent the stdlessonid in the controller.
              //If the datatable colum was set to non-visible then it doesnt sends the data!
              "mRender": function(data, type, full){
              		return '<input class="span1" type="text" name="stdlessonid['+newabsencecounter+']" value='+data+'></input>';
              }
        	}
        ],
    // "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
    // "sPaginationType": "bootstrap",
    "sDom": "<'row-fluid'<'span12'rt>>",
    "bSort": false,
    "bFilter": false,
    "bPaginate": false,
    "oLanguage": {"sZeroRecords": "Δεν βρέθηκαν εγγραφές"}
       } );

  $('#absencesform').on("click", 'input[type="radio"]', function(){
      //extract get id from the radio input clicked (it is the only numeric positive or negative) part of the name        
      id=$(this).prop('name').match(/[-0-9]+/g);
      if($(this).val()=='present'){
         $('input[name="excused['+id+']"]').prop('checked', false);
         $('input[name="excused['+id+']"]').prop('disabled', true);
      }
      else
      {
        $('input[name="excused['+id+']"]').prop('disabled', false); 
      };

    });


});

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
}


</script>

</head>
<body>
 <div class="wrapper"> <!--body wrapper for css sticky footer-->

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <!--<a class="brand" href="#">Tuition manager</a>-->
          <div class="nav-collapse collapse">
            <ul class="nav">
            <li><a href="<?php echo base_url()?>">Αρχική</a></li> 
            <li class="active"><a href="<?php echo base_url()?>registrations">Μαθητολόγιο</a></li>
              <li><a href="#employees">Προσωπικό</a></li>
              <li><a href="#sections">Τμήματα</a></li>
              <li><a href="#finance">Οικονομικά</a></li>
              <li><a href="#reports">Αναφορές</a></li>
              <li><a href="#admin">Διαχείριση</a></li>
            </ul>
            <ul class="nav pull-right">
              <li><a href="#"><i class="icon-off"></i> Αποσύνδεση</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>


<!-- Subhead
================================================== -->
<div class="jumbotron subhead">
  <div class="container">
    <h1>Καρτέλα Μαθητή</h1>
    <p class="leap">tuition manager - πρόγραμμα διαχείρισης φροντιστηρίου.</p>
  </div>
</div>


<!-- main container
================================================== -->

  <div class="container"  style="margin-bottom:60px;">
  
    <div class="container-fluid">
      
      <div style="margin-top:20px; margin-bottom:-15px;">
      <ul class="breadcrumb">
        <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a><span class="divider">></span></li>
        <li><a href="<?php echo base_url()?>registrations">Μαθητολόγιο</a> <span class="divider">></span></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>">Καρτέλα μαθητή</a> <span class="divider">></span></li>
        <li class="active">Φοίτηση</li>
      </ul>
        <!-- <a class="btn btn-mini" href="<?php echo base_url();?>"><i class="icon-arrow-left"></i> πίσω</a>         -->
      </div>
      
      

      <h3><?php echo $student['surname'].' '.$student['name']?></h3>
      

      <ul class="nav nav-tabs">
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>">Στοιχεία</a></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/contact">Επικοινωνία</a></li>
        <li class="active"><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance">Φοίτηση</a></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance">Οικονομικά</a></li>
      </ul>
     
      <ul class="nav nav-pills">
        <li class="active"><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance">Σύνοψη</a></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance/manage">Διαχείριση</a></li>
      </ul>


      <div class="row-fluid"> <!--Συνοπτική ενημέρωση-->
      	<div class="span12">
	      	<div class="row-fluid"> <!--Πρόγραμμα ημέρας-->
		      	<div class="span12"> 
		      	<div class="contentbox">
              <div class="title">
                <span class="icon">
                  <i class="icon-time"></i>
                </span>
                <h5>Πρόγραμμα ημέρας</h5>
              </div>
            <div class="content">
			      		<?php if(empty($dayprogram)):?>
			      			<?php if(empty($program)):?>
				      			<p class="text-info">
				      				Δεν έχει εισαχθεί πρόγραμμα για το συγκεκριμένο μαθητή!
				      			</p>
				      		<?php else:?>
				      			<p class="text-info">
				      				Σήμερα δεν έχει κανένα μάθημα!
				      			</p>
				      		<?php endif;?>
			      		<?php else:?>
				      		<table id="dayprogram" class="table table-striped table-condensed " >
				      			<thead>
				      				<th>Ώρα</th>
				      				<th>Μάθημα</th>
				      				<th>Διδάσκων</th>
				      				<th>Τμήμα</th>
				      				<th>Αίθουσα</th>
				      			</thead>
				      			<tbody>
				      				<?php foreach ($dayprogram as $data):?>
				      					<tr>
				      						<td><?php echo date('H:i',strtotime($data['start_tm'])).'-'.date('H:i',strtotime($data['end_tm']))?></td>
				      						<td><?php echo $data['title']?></td>
				      						<td><?php echo $data['nickname']?></td>
				      						<td><?php echo $data['section']?></td>
				      						<td><?php echo $data['classroom']?></td>
				      					</tr>
				      				<?php endforeach;?>
				      			</tbody>
				      		</table>
				      	<?php endif;?>
				      	<div class="row-fluid">
			      			<div class="span12">	
			      				<!-- onclick="return false;" is needed as an a tag can't be disabled by the disabled property. I'm using the property just for it's css -->
			      				<a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance/program" class="btn btn-small pull-right" <?php if(empty($program)) echo 'disabled="disabled" onclick="return false;"';?> >Εβδομαδιαίο πρόγραμμα</a>
			      			</div>
			      		</div>
				      </div>
		      	</div>
          </div>
		      </div> <!--τέλος ημερησίου προγράμματος-->

		      <div class="row-fluid">
		      	<div class="span6"> <!--Πρόγραμμα Σπουδών-->
		      		<div class="contentbox">
              <div class="title">
                <span class="icon">
                  <i class=" icon-cog"></i>
                </span>
                <h5>Πρόγραμμα σπουδών</h5>
              </div>
            <div class="content">
		      			<?php if (empty($program)):?>
      					<div class="alert alert-block alert-error fade in">
				            <!-- <button type="button" class="close" data-dismiss="alert">&times;</button> -->
				            <h4 class="alert-heading"><i class="icon-exclamation-sign"></i> Δεν έχει εισαχθεί πρόγραμμα σπουδών!</h4>
				            <p>Για την εισαγωγή προγράμματος σπουδών (μαθημάτων που παρακολουθεί ο μαθητής, τμημάτων στα οποία ανήκει,
				            	διδασκόντων που του κάνουν μάθημα, ωρολόγιου πρόγράμματος, αιθουσιολογίου κ.ο.κ) απαιτείται να ενημερώσετε το πρόγραμμα σπουδών
				            	του μαθητή. Αυτό μπορείτε να το κάνετε επιλέγοντας "Διαχείριση".</p>
				        </div>
				    <?php else:?>
				    	<!-- I don't check if $attendance_general exists as it will be if $program exists! -->
				    	<h5>Αρ. μαθημάτων που παρακολουθεί : <span class="badge badge-success"><?php echo count($attendance_general);?></span></h5>
				    	<p><span class="label label-success">Ανάλυση:</span></p>
				    	<table class = "table table-striped table-condensed" width="100%">
				    		<thead>
				    			<th>Μάθημα</th>
				    			<th>Διδάσκων</th>
				    			<th>Τμήμα</th>
				    		</thead>
				    		<tbody>
				    			<?php foreach ($attendance_general as $data):?>
				    				<tr>
				    					<td><?php echo $data['title'];?></td>
				    					<td><?php echo $data['nickname'];?></td>
				    					<td><?php echo $data['section'];?></td>
				    				</tr>
				    			<?php endforeach;?>
				    		</tbody>
		      			</table>
		      		<?php endif;?>
				      	<div class="row-fluid">
			      			<div class="span12">	
			      				<a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance/manage" class="btn btn-danger btn-small pull-right">Διαχείριση</a>
				   			</div>
				   		</div>
  				    </div>
		      	</div>
          </div>

		      	<div class="span6"> <!--απουσίες & πρόοδος-->

			      <div class="row-fluid">
			      	<div class="span12"> <!--Σύνολο απουσιών (στο τέλος Περισότερα...) -->
			      	<div class="contentbox">
              <div class="title">
                <span class="icon">
                  <i class=" icon-flag"></i>
                </span>
                <h5>Απουσίες</h5>
              </div>
            <div class="content">
				      		<?php if(empty($dayprogram)):?>
				      			<?php if(empty($program)):?>
					      			<p class="text-info">
					      				Για την παρακολούθηση των απουσιών του μαθητή, απαιτείται να έχει ενημερωθεί το πρόγραμμα σπουδών του.
					      			</p>
							      	<div class="row-fluid">
						      			<div class="span12">	
						      				<a href="#" class="btn  btn-small pull-right disabled" onclick="return false;" >Απουσιολόγιο</a>
							   			</div>
							   		</div>
					      		<?php else:?>
					      			<h5>Δικαιολογημένες: <span class="badge badge-success">2</span> &nbsp Αδικαιολόγητες: <span class="badge badge-important">0</span></h5>
					      			<p><span class="label label-warning">Σήμερα:</span></p>
					      			<p class="text-info">
					      				Σήμερα δεν έχει κανένα μάθημα!
					      			</p>
							      	<div class="row-fluid">
						      			<div class="span12">	
						      				<a href="#" class="btn  btn-danger btn-small pull-right" onclick="return false;" >Απουσιολόγιο</a>
							   			</div>
							   		</div>
					      		<?php endif;?>
				      		<?php else:?>
				      			<h5>Δικαιολογημένες: <span class="badge badge-success">2</span> &nbsp Αδικαιολόγητες: <span class="badge badge-important">0</span></h5>
					      		<p><span class="label label-warning">Σήμερα:</span></p>
                    <form id="absencesform">
					      		<table id="absencestable" class="table table-striped table-condensed" width="100%">
					      			<thead>
					      				<th>Μάθημα</th>
					      				<th>Ώρα</th>
					      				<th>Παρών</th>
					      				<th>Απών</th>
					      				<th>Δικ</th>
					      				<th></th>
					      			</thead>
					      			<tbody>
					      			</tbody>
					      		</table>
                  </form>
					      	<div class="row-fluid">
				      			<div class="span12">	
				      				<button class="btn  btn-warning btn-small" id="savedayabsence" >Αποθήκευση</button>
				      				<a href="#" class="btn  btn-danger btn-small pull-right" onclick="return false;" >Απουσιολόγιο</a>
					   			</div>
					   		</div>
					      	<?php endif;?>
	  				    </div>
			      	</div>
			      </div>
          </div>

			      	<div class="row-fluid">
				      	<div class="span12"> <!--Βαθμολογία τελευταίου διαγωνίσματος - Μέσος όρος βαθμολογίας (στο τέλος Περισότερα...) -->
				      		<div class="contentbox">
              <div class="title">
                <span class="icon">
                  <i class="icon-random"></i>
                </span>
                <h5>Πρόοδος</h5>
              </div>
            <div class="content">
              <?php if(empty($progress)):?>
  								<p class="text-info">
				      				Δεν υπάρχουν δεδομένα για την πρόοδο του μαθητή!
             			</p>
              <?php else:?>
              ...
              <?php endif;?>
			      	<div class="row-fluid">
		      			<div class="span12">	
		      				<a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance/progress" class="btn  btn-small pull-right" >Βαθμολόγιο</a>
		      			</div>
		      		</div>
	      		</div>
	      	</div>
		    </div>
      </div>

   	</div>

  </div>
</div>


</div> <!--Τέλος συνοπτικής ενημέρωσης-->

</div> <!--end of fluid container-->

</div> <!--end of main container-->

<div class="push"></div>
</div> <!-- end of body wrapper-->