<link href="<?php echo base_url('assets/css/dataTables.bootstrap.css') ?>" rel="stylesheet">

<script src="<?php echo base_url('assets/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/js/dataTables.bootstrap.js') ?>"></script>


<!-- 
<?php if(!empty($attendance_general)){

  $tableData=array('aaData'=>$attendance_general);  
};?>

 -->
<script type="text/javascript">

$(document).ready(function(){
    
  $('.footable').footable();

	var oTable;
  get_absences_count();

	$('#savedayabsence').click(function(){
		var sData = oTable.$('input').serialize();
		sData = sData+'&'+'stdid='+<?php echo $student['id'];?>;
    $.ajax({  
              type: "POST",  
              url: "<?php echo base_url()?>student/updatedayabsencedata",  
              data: sData,
              success: function(result) {  
                  if (result!=false){
                      oTable.fnReloadAjax();
                  };
              },
              complete: function(){
                get_absences_count();
              }

          });
    
	});


    /* Init the table */
    var absid=0;
    oTable = $('#absencestable').dataTable( {
    "bProcessing": false,
    //"aaData": sData,
    "sAjaxSource": "<?php echo base_url();?>student/getabsencesdata/<?php echo $student['id']?>/",
    //"aoColumnDefs": [/*stdlesson_id*/{ "bVisible": false, "aTargets": [5] }],
    "aoColumns": [
            { "mData": "title",
              "sClass":"col-md-3"},
            { "mData": "hours",
              "sClass":"col-md-2"},
            { "mData": "id",
              "sClass": "col-md-2",
              "mRender": function (data, type, full) {
              	  absid=data;
                  if(data<0){
              	  	whetherchecked = "checked='checked'";
                    excusedstatus="disabled='disabled'";
              	  }
              	  else {
              	  	whetherchecked = "";
                    excusedstatus="enabled='enabled'";
                  };
                  return '<input type="radio" name="todaypresense['+absid+']" value="present"' + whetherchecked + '>';
                  }
            },
            { "mData": "id",
              "sClass": "col-md-2",
              "mRender": function (data, type, full) {
              	  if(data>0){
              	  	whetherchecked = "checked='checked'";
                  }
              	  else {
              	  	whetherchecked = "";
              	  };
                  return '<input type="radio" name="todaypresense['+absid+']" value="absent"' + whetherchecked +'>';
                  }
            },
            { "mData": "excused",
              "sClass": "col-md-2",
              "mRender": function (data, type, full) {
              	  if(data==1){
              	  	whetherchecked = "checked='checked'";
              	  }
              	  else {
              	  	whetherchecked = "";
              	  };
                  return '<input type="checkbox" name="excused['+absid+']"' + whetherchecked + ' ' + excusedstatus +' >';
                  }
            },
            { "mData": "stdlesson_id",
              "sClass": "col-md-1 hidden",
              //I use a hidden form field in a visible datatable column to sent the stdlessonid in the controller.
              //If the datatable colum was set to non-visible then it doesnt sends the data!
              "mRender": function(data, type, full){
                  return '<input type="text" name="stdlessonid['+absid+']" value='+data+'>';
                  absid=0;
              }
        	}
        ],
    "sDom": "<'row'<'col-md-12'rt>>",
    "bSort": false,
    "bFilter": false,
    "bPaginate": false,
    "oLanguage": {"sZeroRecords": "Δεν βρέθηκαν εγγραφές"}
       } );

  $('#absencesform').on("click", 'input[type="radio"]', function(){
      //extract get id from the radio input clicked (it is the only numeric positive or negative) part of the name        
      var id=$(this).prop('name').match(/[-0-9]+/g);
      if($(this).val()=='present'){
         $('input[name="excused['+id+']"]').prop('checked', false);

         $('input[name="excused['+id+']"]').prop('disabled', true);
      }
      else
      {
        $('input[name="excused['+id+']"]').prop('disabled', false); 
      };

    });


  $('#absencesform').on("click", 'input[type="checkbox"]', function(){
      if ($(this).attr('checked')){
        $(this).removeAttr('checked');
      }
      else
      {
        $(this).attr("checked", 'checked'); 
      };
  });

  // HIDING COLUMNS FOR RESPONSIVE VIEW:

  $(window).on("load", resizeWindow);
  //If the User resizes the window, adjust the #container height
  $(window).on("resize", resizeWindow);

  function resizeWindow(e)
  {
      var newWindowWidth = $(window).width();

      if(newWindowWidth > 360)
      {
        oTable.fnSetColumnVis( 1, true );
      }
      else
      {
        oTable.fnSetColumnVis( 1, false );
      }

  };

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
};

    function get_absences_count(){
        //post_url is the controller function where I want to post the data
        var post_url = "<?php echo base_url()?>student/get_absences_count/<?php echo $student['id'];?>";
        $.ajax({
          type: "POST",
          url: post_url,
          data : '',
          dataType:'json',
          //absencesdata is just a name that gets the result of the controller's function I posted the data
          success: function(absencesdata)
            {
              //ajax cannot return a value! We must set the value where we want it!
              //console.log(absencesdata.excused, absencesdata.unexcused);
              $('#excusedbadged').html(absencesdata.excused);
              $('#unexcusedbadged').html(absencesdata.unexcused);
            } //end success
          }); //end AJAX
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
            <li><a href="<?php echo base_url()?>finance">Οικονομικά</a></li>
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
    <h1>Καρτέλα Μαθητή</h1>
    <p class="leap">Πρόγραμμα διαχείρισης φροντιστηρίου.</p>
  </div>
</div>


<!-- main container
================================================== -->

  <div class="container"  style="margin-bottom:60px;">
  
      <div>
        <ul class="breadcrumb">
          <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a></li>
          <li><a href="<?php echo base_url()?>student">Μαθητολόγιο</a> </li>
          <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>">Καρτέλα μαθητή</a> </li>
          <li class="active">Φοίτηση</li>
        </ul>
      </div>
      
      <p>
        <h3><?php echo $student['surname'].' '.$student['name']?></h3>
      </p>
      

      <ul class="nav nav-tabs">
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>">Στοιχεία</a></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/contact">Επικοινωνία</a></li>
        <li class="active"><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance">Φοίτηση</a></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance">Οικονομικά</a></li>
      </ul>
     
      <ul class="nav nav-pills" style="margin:15px 0px;">
        <li class="active"><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance">Σύνοψη</a></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance/manage">Διαχείριση</a></li>
      </ul>


      <div class="row"> <!--Συνοπτική ενημέρωση-->
      	<div class="col-md-12">
	      	<div class="row"> <!--Πρόγραμμα ημέρας-->
		      	<div class="col-md-12"> 
		      	<div class="panel panel-default">
              <div class="panel-heading">
                <span class="icon">
                  <i class="icon-time"></i>
                </span>
                <h3 class="panel-title">Πρόγραμμα ημέρας</h3>
              </div>
            <div class="panel-body">
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
				      		<table id="dayprogram" class="footable table table-striped table-condensed " >
				      			<thead>
                      <tr>
  				      				<th data-class="expand">Ώρα</th>
  				      				<th>Μάθημα</th>
  				      				<th data-hide="phone">Διδάσκων</th>
  				      				<th data-hide="phone">Τμήμα</th>
  				      				<th data-hide="phone,tablet">Αίθουσα</th>
                      </tr>
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
				      	<div class="row">
			      			<div class="col-md-12">	
			      				<!-- onclick="return false;" is needed as an a tag can't be disabled by the disabled property. I'm using the property just for it's css -->
			      				<a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance/program" class="btn btn-default btn-sm pull-right" <?php if(empty($program)) echo 'disabled="disabled" onclick="return false;"';?> >Εβδομαδιαίο πρόγραμμα</a>
			      			</div>
			      		</div>
				      </div>
		      	</div>
          </div>
		      </div> <!--τέλος ημερησίου προγράμματος-->

		      <div class="row">
		      	<div class="col-md-6"> <!--Πρόγραμμα Σπουδών-->
		      		<div class="panel panel-default">
              <div class="panel-heading">
                <span class="icon">
                  <i class=" icon-cog"></i>
                </span>
                <h3 class="panel-title">Πρόγραμμα σπουδών</h3>
              </div>
            <div class="panel-body">
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
				    	<h6>Αρ. μαθημάτων που παρακολουθεί : <span class="badge"><?php echo count($attendance_general);?></span></h6>
				    	<p><span class="label label-success">Ανάλυση:</span></p>
				    	<table class = "table table-striped table-condensed table-hover" width="100%">
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
				      	<div class="row">
			      			<div class="col-md-12">	
			      				<a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance/manage" class="btn btn-default btn-sm pull-right">Διαχείριση</a>
				   			</div>
				   		</div>
  				    </div>
		      	</div>
          </div>

		      	<div class="col-md-6"> <!--απουσίες & πρόοδος-->

			      <div class="row">
			      	<div class="col-md-12"> <!--Σύνολο απουσιών -->
			      	<div class="panel panel-default">
              <div class="panel-heading">
                <span class="icon">
                  <i class=" icon-flag"></i>
                </span>
                <h3 class="panel-title">Απουσίες</h3>
              </div>
            <div class="panel-body">
				      		<?php if(empty($dayprogram)):?>
				      			<?php if(empty($program)):?>
					      			<p class="text-info">
					      				Για την παρακολούθηση των απουσιών του μαθητή, απαιτείται να έχει ενημερωθεί το πρόγραμμα σπουδών του.
					      			</p>
							      	<div class="row">
						      			<div class="col-md-12">	
						      				<a href="#" class="btn btn-default btn-sm pull-right disabled" onclick="return false;" >Απουσιολόγιο</a>
							   			</div>
							   		</div>
					      		<?php else:?>
					      			<h6>Δικαιολογημένες: <span id="excusedbadged" class="badge">-</span> &nbsp Αδικαιολόγητες: <span id="unexcusedbadged" class="badge">-</span></h6>
					      			<p><span class="label label-warning">Σήμερα:</span></p>
					      			<p class="text-info">
					      				Σήμερα δεν έχει κανένα μάθημα!
					      			</p>
							      	<div class="row">
						      			<div class="col-md-12">	
						      				<a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance/absences" class="btn  btn-default btn-sm pull-right">Απουσιολόγιο</a>
							   			</div>
							   		</div>
					      		<?php endif;?>
				      		<?php else:?>
				      			<h6>Δικαιολογημένες: <span id="excusedbadged" class="badge">-</span> &nbsp Αδικαιολόγητες: <span id="unexcusedbadged" class="badge">-</span></h6>
					      		<p><span class="label label-warning">Σήμερα:</span></p>
                    <form id="absencesform">
					      		<table id="absencestable" class="table table-striped table-condensed" width="100%">
					      			<thead>
                        <tr>
  					      				<th>Μάθημα</th>
  					      				<th>Ώρα</th>
  					      				<th>Παρών</th>
  					      				<th>Απών</th>
  					      				<th>Δικ</th>
  					      				<th></th>
                        </tr>
					      			</thead>
					      			<tbody>
					      			</tbody>
					      		</table>
                  </form>
					      	<div class="row">
				      			<div class="col-md-12">	
				      				<button class="btn  btn-warning btn-sm" id="savedayabsence" >Αποθήκευση</button>
				      				<a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance/absences" class="btn  btn-default btn-sm pull-right">Απουσιολόγιο</a>
					   			</div>
					   		</div>
					      	<?php endif;?>
	  				    </div>
			      	</div>
			      </div>
          </div>

			      	<div class="row">
				      	<div class="col-md-12"> <!--Βαθμολογία τελευταίου διαγωνίσματος - Μέσος όρος βαθμολογίας (στο τέλος Περισότερα...) -->
				      		<div class="panel panel-default">
              <div class="panel-heading">
                <span class="icon">
                  <i class="icon-random"></i>
                </span>
                <h3 class="panel-title">Πρόοδος</h3>
              </div>
            <div class="panel-body">
              <?php if(empty($progress)):?>
                <div class="row">
                  <div class="col-md-12">  
                    <p class="text-info">
                      Δεν υπάρχουν δεδομένα για την πρόοδο του μαθητή!
                    </p>
                    <a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance/progress" class="btn btn-default btn-sm pull-right disabled" onclick="return false;" >Βαθμολόγιο</a>
                  </div>
                </div>
              <?php else:?>
               <div class="row">
                <div class="col-md-12">  
                  <a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance/progress" class="btn btn-default btn-sm pull-right" >Βαθμολόγιο</a>
                </div>
              </div>
              <?php endif;?>
	      		</div>
	      	</div>
		    </div>
      </div>

   	</div>

  </div>
</div>


</div> <!--Τέλος συνοπτικής ενημέρωσης-->


</div> <!--end of main container-->

<div class="push"></div>
</div> <!-- end of body wrapper-->