
<link href="<?php echo base_url('assets/css/dataTables.bootstrap.css') ?>" rel="stylesheet">

<script src="<?php echo base_url('assets/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/js/dataTables.bootstrap.js') ?>"></script>
<!-- 
<?php if(!empty($participants)){
  $tableData=array('aaData'=>$participants);  
};?> -->

<style>
    #selectall a{text-decoration: none;}
</style>


<script type="text/javascript">

var oTable;

$(document).ready(function(){

    $('#delsubmit').attr("disabled", "disabled");
    $('#sectionsmultiple').attr("disabled", "disabled");
    $('#sectiongroups').attr("disabled", "disabled");
    //$('#addsubmit').attr("disabled", "disabled");

    $('#radioall').click(function(){
        $('#sectionsmultiple option:selected').removeAttr('selected');
        $('#sectiongroups option:selected').removeAttr('selected');
        $('#sectionsmultiple').attr("disabled", "disabled");
        $('#sectiongroups').attr("disabled", "disabled");        
        $('#addsubmit').removeAttr('disabled');
    });

    $('#radiobytutors').click(function(){
        $('#sectiongroups').removeAttr('disabled');      
        $('#sectionsmultiple option:selected').removeAttr('selected');                
        $('#sectionsmultiple').attr("disabled", "disabled");
        $('#addsubmit').attr("disabled", "disabled");
    });

    $('#radiomultiple').click(function(){
        $('#sectionsmultiple').removeAttr('disabled');
        $('#sectiongroups option:selected').removeAttr('selected');
        $('#sectiongroups').attr("disabled", "disabled");
        $('#addsubmit').attr("disabled", "disabled");
    });


    $('#delsubmit').click( function() {
      var count = oTable.$('input:checked').length;
      var r = confirm('Πρόκειται να αφαιρέσετε τη συμμετοχή από '+count+' τμήματα. Η ενέργεια αυτή δεν αναιρείται. Θέλετε να συνεχίσετε;');
      if (r==true){
        var sData = oTable.$('input').serialize();
        sData = sData+'&'+'examid='+<?php echo $exam['id']?>;
        //console.log(sData);
        $.ajax({  
                  type: "POST",  
                  url: "<?php echo base_url()?>exams/removeparticipants",  
                  data: sData,
                  // beforeSend: function(){
                  //     $('#image').show();
                  // },
                  success: function(result) {  
                      if (result!=false){
                          $('#nodata').hide();
                          $('#data').show();
                          oTable.fnClearTable();
                          oTable.fnAddData(result);
                          oTable.fnDraw();  
                      }
                      else {
                        oTable.fnClearTable();
                        oTable.fnDraw(); 
                        $('#data').hide();
                        $('#nodata').show();
                      };
                      
                  }/*,
                  complete: function(){
                      $('#image').hide();
                  }*/
              });
        }
        //whatever happens after the button click the checkboxes should be unchecked and the button disabled!
        $('#deleteform input:checked').each(function(){
          $(this).removeAttr('checked');
        });
        $('#delsubmit').attr("disabled", "disabled");
        $('#selectall').html('<a href="#" class="btn btn-xs btn-default" onclick="selectall();return false;"><i class="icon-edit"></i> Eπιλογή όλων<a>');
    } );


    /* Init the table */
    <?php if(!empty($participants)):?>
      var sData = <?php echo json_encode($participants);?>;
      $('#nodata').hide();
      $('#data').show();
    <?php else:?>
      var sData = "";
      $('#nodata').show();
      $('#data').hide();
    <?php endif;?>

    oTable = $('#lessonstable').dataTable( {
    "bProcessing": true,
    "aaData": sData,
    "aoColumns": [
            { "mData": "id",
              //"sClass": "col-md-2",
              "mRender": function (data, type, full) {
                  return '<label class="checkbox"><input type="checkbox" name="selection['+data+']"></label>';
                  }
            },
            { "mData": "section" },
            { "mData": "nickname" },
            { "mData": "stdcount" }
        ],
    // "sPaginationType": "bootstrap",
    "sDom": "<'row'<'col-md-12'rt>>",
    "bSort": true,
    "bFilter": false,
    "bPaginate": false,
    "oLanguage": {"sZeroRecords": "Δεν βρέθηκαν εγγραφές"}
       } );



    $('#addsubmit').click( function() {
      var sRadio = $('#addform input[type=radio][name="insertoption"]:checked').val();
      if (sRadio=="all"){
        sectionNames = <?php echo json_encode($section);?>;
        var sData={
          'sectionNames':sectionNames,
          'examid':"<?php echo $exam['id'];?>"
        };
        var c = confirm("Πρόκειται να αντιστοιχίσετε όλα τα τμήματα που διδάσκονται το μάθημα στο αντίστοιχο διαγώνισμα. Θέλετε να συνεχίσετε;" );
        if (c==true){
            $.ajax({  
                      type: "POST",  
                      url: "<?php echo base_url()?>exams/insertallsections",  
                      data: sData,
                      success: function(result) {  
                          if(result!=false){
                              $('#nodata').hide();
                              $('#data').show();
                              oTable.fnClearTable();
                              oTable.fnAddData(result);
                              oTable.fnDraw();                        
                          }
                          else
                          {
                              $('#data').hide();                           
                              $('#nodata').show();
                          }

                      }  
                  });
        }//end if confirm
      }
      else if(sRadio=="multiple")
      {
        var sData={
          'sections_id':$('#sectionsmultiple').val(),
          'examid': "<?php echo $exam['id']?>"
        };
        var items = [];
        $('#sectionsmultiple option:selected').each(function(){ items.push($(this).text()); });
        var select = items.join('\n');
        var c = confirm("Πρόκειται να αντιστοιχίσετε τα ακόλουθα "+items.length+" τμήματα:\n" + select +"\nστο διαγώνισμα. Θέλετε να συνεχίσετε;");
        if (c==true){
            $.ajax({  
                      type: "POST",  
                      url: "<?php echo base_url()?>exams/insertmultiplesections",  
                      data: sData,
                      success: function(result) {  
                          if(result!=false){
                              $('#nodata').hide();
                              $('#data').show();
                              oTable.fnClearTable();
                              oTable.fnAddData(result);
                              oTable.fnDraw();  
                          }
                          else {
                              $('#data').hide();                           
                              $('#nodata').show();
                          }
                          
                      }
                  });          
        }
        $('#sectionsmultiple option:selected').removeAttr('selected');
        $('#addsubmit').attr("disabled", "disabled");
      }
      else if (sRadio=="bytutors")
      {
        var sData={
          'tutor_id':$('#sectiongroups').val(),
          'examid': "<?php echo $exam['id']?>",
          'lessonid' : "<?php echo $exam['lesson_id']?>"
        };
        var items = [];
        $('#sectiongroups option:selected').each(function(){ items.push($(this).text()); });
        var select = items.join('\n');
        var c = confirm("Πρόκειται να αντιστοιχίσετε τα τμήματα των ακόλουθων "+items.length+" διδασκόντων:\n" + select +"\nστο διαγώνισμα. Θέλετε να συνεχίσετε;");
        if (c==true){
            $.ajax({  
                      type: "POST",  
                      url: "<?php echo base_url()?>exams/insertbytutors",  
                      data: sData,
                      success: function(result) {  
                          if(result!=false){
                              $('#nodata').hide();
                              $('#data').show();
                              oTable.fnClearTable();
                              oTable.fnAddData(result);
                              oTable.fnDraw();  
                          }
                          else {
                              $('#data').hide();                           
                              $('#nodata').show();
                          }
                          
                      }
                  });          
        }
        $('#sectiongroups option:selected').removeAttr('selected');
        $('#addsubmit').attr("disabled", "disabled");
      }
    
    });


    //below I attach the click event in the form and I use the selector of the 'on' function
    //to pass the 'input' because all inputs get removed through ajax call
    $('#deleteform').on("click", 'input', function(){
      var count = oTable.$('input:checked').length;
      var Nodes=oTable.fnGetNodes().length;
      if (count>0 && count<Nodes){
        $('#delsubmit').removeAttr("disabled");  
      }
      else if (count==Nodes){
        $('#delsubmit').removeAttr("disabled");  
        $('#selectall').html('<a href="#" class="btn btn-xs btn-default" onclick="deselectall();return false;"><i class="icon-share"></i> Αποεπιλογή όλων<a>');
      }
      else
      {
        $('#delsubmit').attr("disabled", "disabled");
        $('#selectall').html('<a href="#" class="btn btn-xs btn-default" onclick="selectall();return false;"><i class="icon-check"></i> Eπιλογή όλων<a>');
      }
    });
    
    $('#sectiongroups').on("mouseup click", function(){
      var count = $('#sectiongroups option:selected').length;
      if (count>0){
        $('#addsubmit').removeAttr("disabled");  
      }
      else
      {
        $('#addsubmit').attr("disabled", "disabled");
      }
    });

    $('#sectionsmultiple').on("mouseup click", function(){
      //I use mouse up to catch items even if the user used dragging to select multiple items
      var count = $('#sectionsmultiple option:selected').length;
      if (count>0){
        $('#addsubmit').removeAttr("disabled");  
      }
      else
      {
        $('#addsubmit').attr("disabled", "disabled");
      }
    });


$(window).on("load", resizeWindow);
//If the User resizes the window, adjust the #container height
$(window).on("resize", resizeWindow);

function resizeWindow(e)
{
  var newWindowWidth = $(window).width();

  if(newWindowWidth < 440)
  {
    oTable.fnSetColumnVis( 2, false );
  }
  else
  {
    oTable.fnSetColumnVis( 2, true ); 
  }
}


});

function selectall(){
    oTable.$('input[type="checkbox"]').each(function(){
        $(this).prop('checked', true);
    });
    $('#delsubmit').removeAttr('disabled');
    $('#selectall').html('<a href="#" class="btn btn-xs btn-default" onclick="deselectall();return false;"><i class="icon-share"></i> Αποεπιλογή όλων<a>');
};

function deselectall(){
    oTable.$('input[type="checkbox"]').each(function(){
        $(this).prop('checked', false);
    });
    $('#delsubmit').attr('disabled','disabled');
    $('#selectall').html('<a href="#" class="btn btn-xs btn-default" onclick="selectall();return false;"><i class="icon-check"></i> Eπιλογή όλων<a>');
};

function noprograminfo(){
  alert('Πρώτα πρέπει να εισάγετε το πρόγραμμα σπουδών του μαθητή!');
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
              <a href="#" class="dropdown-toggle active" data-toggle="dropdown">Λειτουργία<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo base_url()?>student">Μαθητολόγιο</a></li>
                <li class="active"><a href="<?php echo base_url()?>exams">Διαγωνίσματα</a></li>
                <li><a href="<?php echo base_url()?>files">Αρχεία</a></li>
                <li><a href="<?php echo base_url()?>cashdesk">Ταμείο</a></li>
                <li><a href="<?php echo base_url()?>announcements">Ανακοινώσεις</a></li>
              </ul>
            </li>
           <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Οργάνωση/Διαχείριση<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
                <li><a href="<?php echo base_url()?>section">Τμήματα</a></li>
                <li><a href="<?php echo base_url()?>">Πρόγραμμα Σπουδών</a></li>
                <li><a href="<?php echo base_url()?>">Μαθήματα-Διδάσκωντες</a></li>
                <li><a href="<?php echo base_url()?>">Στοιχεία Φροντιστηρίου</a></li>
              </ul>
            </li>
           <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Συγκεντρωτικές Αναφορές<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo base_url()?>report">Αναφορές</a></li>
                <li><a href="<?php echo base_url()?>history">Ιστορικό</a></li>
                <li><a href="<?php echo base_url()?>phonecatalog">Τηλ. Κατάλογοι</a></li>
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
                <li><a href="<?php echo base_url()?>exams/logout">Αποσύνδεση</a></li>
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

  <div class="container"  style="margin-bottom:60px;">
  
      <div>
        <ul class="breadcrumb">
          <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a></li>
          <li><a href="<?php echo base_url()?>exams">Διαγωνίσματα</a> </li>
          <li class="active">Συμμετέχοντες</li>
        </ul>
      </div>
      
      <p>
        <h3>Συμμετέχοντες</h3>
      </p>
      

      <ul class="nav nav-tabs" style="margin-bottom:15px;">
        <li><a href="<?php echo base_url()?>exams/details/<?php echo $exam['id']?>">Λεπτομέρειες</a></li>
        <li class="active"><a href="<?php echo base_url()?>exams/details/<?php echo $exam['id']?>/participants">Συμμετέχοντες</a></li>
      </ul>
     
      <div class="row"> <!--Προσθήκη μαθήματος-->
      	<div class="col-md-12">
            <div class="panel panel-default">
              <div class="panel-heading">
                <span class="icon">
                  <i class="icon-chevron-down"></i>
                </span>
                <h3 class="panel-title">Αντιστοίχιση τμημάτων στο διαγώνισμα</h3>
              </div>
            <div class="panel-body">
      			<form id="addform" accept-charset="utf-8" role="form">
  					<div class="row">
  						<div class="col-md-7 col-sm-7">
                <div class="radio">
	      					  <label>
	      						   <input type="radio" id="radioall" name="insertoption" value="all" checked>
	      						   Αντιστοίχιση <u><b>όλων των τμήματων</b> που διδάσκονται το μάθημα</u> :
	      					  </label>
	      				</div>
              </div>
              <div class="col-md-5 col-sm-5">
                <?php 
                  if(!empty($section))
                  {
                    $sectionlist="";
                    foreach ($section as $key => $value) {
                      if ($sectionlist==="")
                      {
                        $sectionlist = $value;
                      }
                      else
                      {
                        $sectionlist = $sectionlist.', '.$value;
                      }
                    }
                    echo '<input type="text" disabled class="form-control" value="'.$sectionlist.'">';  
                  }
                  else
                  {
                    echo "<p style='margin-top:10px;margin-left:20px;'>";
                    echo "<b>Δεν βρέθηκε κανένα τμήμα στο οποίο να διδάσκεται το συγκεκριμένο μάθημα!</b> Παρακαλώ ελέγξτε αν έχουν εισαχθεί τμήματα για αυτό το μάθημα ή αν πρόκειται για εσφαλμένη καταχώριση διαγωνίσματος.";
                    echo '</p>';
                  }
                ?>
              </div>
            </div>
            <div class="row">
              <div class="col-md-7 col-sm-7">
                <div class="radio">
                    <label>
                       <input type="radio" id="radiobytutors" name="insertoption" value="bytutors">
                       Αντιστοίχιση <u>όλων των τμήματων που διδάσκονται το μάθημα <b>από τους καθηγητές</b></u> :
                    </label>
                </div>
              </div>
                <div class="col-md-5 col-sm-5">
                  <select multiple id="sectiongroups" size="3" class="form-control pull-left" name="section">
                    <?php if(!empty($tutor)):?>
                      <?php foreach ($tutor as $key => $value):?>
                        <option value="<?php echo $key?>"><?php echo $value;?></option>
                      <?php endforeach;?>
                    <?php endif;?>
                  </select>
                </div>
              </div>
  					<div class="row" style="margin-top:5px;">
  						<div class="col-md-7 col-sm-7">
  	      			  <div class="radio">
                		<label>
  	      						<input type="radio" id="radiomultiple" name="insertoption" value="multiple">
  	      						Αντιστοίχιση των ακόλουθων τμημάτων (<b>επιλέξτε 1 ή περισσότερα</b>):
  	      					</label>
  	      			  </div>
              	</div>
	      				<div class="col-md-5 col-sm-5">
		      				<select multiple id="sectionsmultiple" size="3" class="form-control" name="sections[]">
                    <?php if(!empty($section)):?>
                      <?php foreach ($section as $key => $value):?>
                        <option value="<?php echo $key?>"><?php echo $value;?></option>
                      <?php endforeach;?>
                    <?php endif;?>
		      				</select>
		      			</div>
		      		</div>
      			<div class="row">
      				<div class="col-md-12">
                <div class="form-group"> <!--needed for margins... -->
      					   <button style="margin-top:10px;" type="button" class="btn btn-primary pull-right" id="addsubmit" name="addsubmit">Αντιστοίχιση</button>
      				  </div>
              </div>
      			</div>
      		</form>
      		</div>
      	</div>
      </div>
    </div>

		<div class="row"> <!--Πρόγραμμα Σπουδών-->
      	<div class="col-md-12"> 
            <div class="panel panel-default">
              <div class="panel-heading">
                <span class="icon">
                  <i class="icon-file"></i>
                </span>
                <h3 class="panel-title">Συμμετέχοντες</h3>
              </div>
            <div class="panel-body">
					<div class="alert alert-info fade-in" id="nodata">
		          <p><i class="icon-info-sign"></i> Αντιστοιχίστε τα επιθυμητά τμήματα στο παρών διαγώνισμα χρησιμοποιώντας τις παραπάνω επιλογές.</p>
		      </div> <!-- end of nodata div -->
          <div id="data">
  		    	<form id="deleteform" charset="utf-8">
  		    	<table cellpadding="0" cellspacing="0" border="0"  id="lessonstable" class = "table table-striped table-condensed table-hover" width="100%">
  		    		<thead>
  		    			<th>Επιλογή</th>
  		    			<th>Τμήμα</th>
                <th>Διδάσκων</th>
                <th>Αρ.Μαθητών</th>
  		    		</thead>
  		    		<tbody>
                  <!-- The table gets populated via ajax by datatables -->
  		    		</tbody>
        			</table>
        			<div class="row">
        				<div class="col-md-12">
                  <div id="selectall">
                    <a href="#" class="btn btn-xs btn-default" onclick="selectall();return false;"><i class="icon-check"></i> Επιλογή όλων<a>
                  </div>
        					<button type="button" class="btn btn-danger pull-right" id="delsubmit" name="delsubmit">Αφαίρεση επιλεγμένων</button>
        				</div>
        			</div>
        		</form>
          </div> <!-- end off data div -->
			  </div>
			</div>
		</div>
  </div>

  </div> <!--end of main container-->

<div class="push"></div>
</div> <!-- end of body wrapper-->