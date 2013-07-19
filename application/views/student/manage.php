
<link href="<?php echo base_url('assets/css/dataTables.bootstrap.css') ?>" rel="stylesheet">

<script src="<?php echo base_url('assets/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/js/dataTables.bootstrap.js') ?>"></script>
<!-- 
<?php if(!empty($attendance_general)){
  $tableData=array('aaData'=>$attendance_general);  
};?> -->

<style>
    #selectall a{text-decoration: none;}
</style>


<script type="text/javascript">

var oTable;

$(document).ready(function(){

    $('#delsubmit').attr("disabled", "disabled");
    $('#sectionsmultiple').attr("disabled", "disabled");
    $('#addsubmit').attr("disabled", "disabled");

    $('#radiomultiple').click(function(){
        $('#sectionsmultiple').removeAttr('disabled');
        $('#sectiongroups').attr("disabled", "disabled");
        $('#sectiongroups option:selected').removeAttr('selected');
        $('#addsubmit').attr("disabled", "disabled");
    });

    $('#radioall').click(function(){
        $('#sectionsmultiple').attr("disabled", "disabled");
        $('#sectiongroups').removeAttr('disabled');   
        $('#sectionsmultiple option:selected').removeAttr('selected');
        $('#addsubmit').attr("disabled", "disabled");
    });

    $('#delsubmit').click( function() {
      var count = oTable.$('input:checked').length;
      var r = confirm('Πρόκειται να διαγράψετε '+count+' μαθήματα. Η ενέργεια αυτή δεν αναιρείται. Θέλετε να συνεχίσετε;');
      if (r==true){
        var sData = oTable.$('input').serialize();
        sData = sData+'&'+'stdid='+<?php echo $student['id']?>;
        //console.log(sData);
        $.ajax({  
                  type: "POST",  
                  url: "<?php echo base_url()?>student/getlessonplandata",  
                  data: sData,
                  // beforeSend: function(){
                  //     $('#image').show();
                  // },
                  success: function(result) {  
                      if (result!=false){
                          $('#nodata').hide();
                          $('#data').show();
                          $('ul.nav-pills > li:first').removeAttr('onclick');
                          oTable.fnClearTable();
                          oTable.fnAddData(result);
                          oTable.fnDraw();  
                      }
                      else {
                        oTable.fnClearTable();
                        oTable.fnDraw(); 
                        $('#data').hide();
                        $('#nodata').show();
                        $('ul.nav-pills > li:first').attr('onclick','noprograminfo();return false;');
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
        $('#selectall').html('<a href="#" onclick="selectall();return false;"><i class="icon-edit"> Eπιλογή όλων</i><a>');
    } );


    /* Init the table */
    <?php if(!empty($attendance_general)):?>
      var sData = <?php echo json_encode($attendance_general);?>;
      $('#nodata').hide();
      $('#data').show();
      $('ul.nav-pills > li:first').removeAttr('onclick');
    <?php else:?>
      var sData = "";
      $('#nodata').show();
      $('ul.nav-pills > li:first').attr('onclick','noprograminfo();return false;');
      $('#data').hide();
    <?php endif;?>

    oTable = $('#lessonstable').dataTable( {
    "bProcessing": true,
    "aaData": sData,
    "aoColumns": [
            { "mData": "id",
              "sClass": "span2",
              "mRender": function (data, type, full) {
                  return '<label class="checkbox"><input type="checkbox" name="selection['+data+']"></input></label>';
                  }
            },
            { "mData": "title" },
            { "mData": "nickname" },
            { "mData": "section" }
        ],
    // "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
    // "sPaginationType": "bootstrap",
    "sDom": "<'row-fluid'<'span12'rt>>",
    "bSort": true,
    "bFilter": false,
    "bPaginate": false,
    "oLanguage": {"sZeroRecords": "Δεν βρέθηκαν εγγραφές"}
       } );



    $('#addsubmit').click( function() {
      var sRadio = $('#addform input[type=radio][name="insertoption"]:checked').val();
      if (sRadio=="all"){
        sectionName = $('#sectiongroups').val();
        var sData={
          'sectionName':sectionName,
          'stdid':"<?php echo $student['id']?>"
        };
        var c = confirm("Πρόκειται να εισάγετε το μαθητή σε όλα τα μαθήματα του τμήματος "+sectionName+". Θέλετε να συνεχίσετε;" );
        if (c==true){
            $.ajax({  
                      type: "POST",  
                      url: "<?php echo base_url()?>student/insertallbyname",  
                      data: sData,
                      success: function(result) {  
                          if(result!=false){
                              $('#nodata').hide();
                              $('#data').show();
                              $('ul.nav-pills > li:first').removeAttr('onclick');
                              oTable.fnClearTable();
                              oTable.fnAddData(result);
                              oTable.fnDraw();                        
                          }
                          else
                          {
                              $('#data').hide();                           
                              $('#nodata').show();
                              $('ul.nav-pills > li:first').attr('onclick','noprograminfo();return false;');
                          }

                      }  
                  });
        }//end if confirm
        $('#sectiongroups option:selected').removeAttr('selected');
        $('#addsubmit').attr("disabled", "disabled");
      }
      else if(sRadio=="multiple")
      {
        var sData={
          'sections_ids':$('#sectionsmultiple').val(),
          'stdid': "<?php echo $student['id']?>"
        };
        var items = [];
        $('#sectionsmultiple option:selected').each(function(){ items.push($(this).text()); });
        var select = items.join('\n');
        var c = confirm("Πρόκειται να εισάγετε το μαθητή στα ακόλουθα "+items.length+" τμήματα:\n" + select +".\nΘέλετε να συνεχίσετε;");
        if (c==true){
            $.ajax({  
                      type: "POST",  
                      url: "<?php echo base_url()?>student/insertmultiple",  
                      data: sData,
                      success: function(result) {  
                          if(result!=false){
                              $('#nodata').hide();
                              $('#data').show();
                              $('ul.nav-pills > li:first').removeAttr('onclick');
                              oTable.fnClearTable();
                              oTable.fnAddData(result);
                              oTable.fnDraw();  
                          }
                          else {
                              $('#data').hide();                           
                              $('#nodata').show();
                              $('ul.nav-pills > li:first').attr('onclick','noprograminfo();return false;');
                          }
                          
                      }
                  });          
        }
        $('#sectionsmultiple option:selected').removeAttr('selected');
        $('#addsubmit').attr("disabled", "disabled");
      }

    });


    //below I attach the click event in the form and I use the selector of the 'on' function
    //(.on( events [, selector ] [, data ], handler(eventObject) ))
    //to pass the 'input' because all inputs get removed through ajax call
    $('#deleteform').on("click", 'input', function(){
      var count = oTable.$('input:checked').length;
      var Nodes=oTable.fnGetNodes().length;
      if (count>0 && count<Nodes){
        $('#delsubmit').removeAttr("disabled");  
      }
      else if (count==Nodes){
        $('#delsubmit').removeAttr("disabled");  
        $('#selectall').html('<a href="#" onclick="deselectall();return false;"><i class="icon-share"> Αποεπιλογή όλων</i><a>');
      }
      else
      {
        $('#delsubmit').attr("disabled", "disabled");
        $('#selectall').html('<a href="#" onclick="selectall();return false;"><i class="icon-check"> Eπιλογή όλων</i><a>');
      }
    });
    
    $('#sectiongroups').click(function(){
      var count = $('#sectiongroups option:selected').length;
      if (count>0){
        $('#addsubmit').removeAttr("disabled");  
      }
      else
      {
        $('#addsubmit').attr("disabled", "disabled");
      }
    });

    $('#sectionsmultiple').on("mouseup", function(){
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
    $('#selectall').html('<a href="#" onclick="deselectall();return false;"><i class="icon-share"> Αποεπιλογή όλων</i><a>');
};

function deselectall(){
    oTable.$('input[type="checkbox"]').each(function(){
        $(this).prop('checked', false);
    });
    $('#delsubmit').attr('disabled','disabled');
    $('#selectall').html('<a href="#" onclick="selectall();return false;"><i class="icon-check"> Eπιλογή όλων</i><a>');
};

function noprograminfo(){
  alert('Πρώτα πρέπει να εισάγετε το πρόγραμμα σπουδών του μαθητή!');
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
            <li class="active"><a href="<?php echo base_url()?>student">Μαθητολόγιο</a></li>
              <li><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
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
        <li><a href="<?php echo base_url()?>student">Μαθητολόγιο</a> <span class="divider">></span></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>">Καρτέλα μαθητή</a> <span class="divider">></span></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance">Φοίτηση</a> <span class="divider">></span></li>
        <li class="active">Διαχείριση</li>
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
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance">Σύνοψη</a></li>
        <li class="active"><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance/manage">Διαχείριση</a></li>
      </ul>


      <div class="row-fluid"> <!--Προσθήκη μαθήματος-->
      	<div class="span12">
            <div class="contentbox">
              <div class="title">
                <span class="icon">
                  <i class="icon-chevron-down"></i>
                </span>
                <h5>Εισαγωγή μαθητή σε τμήματα</h5>
              </div>
            <div class="content">
      			<form id="addform" accept-charset="utf-8">
  					<div class="row-fluid">
  						<div class="span5">
	      					<label class="radio">
	      						<input type="radio" id="radioall" name="insertoption" value="all" checked></input>
	      						Εισαγωγή σε <u>όλα τα μαθήματα του τμήματος</u> :
	      					</label>
	      				</div>
	      				<div class="span7">
		      				<select id="sectiongroups" class="span12 pull-left" name="section">
                      <?php foreach ($group_sections as $data):?>
                        <option value="<?php echo $data['section']?>"><?php echo $data['section'];?></option>
                      <?php endforeach;?>
		      				</select>
		      			</div>
		      		</div>
  					<div class="row-fluid">
  						<div class="span5">
	      					<label class="radio">
	      						<input type="radio" id="radiomultiple" name="insertoption" value="multiple"></input>
	      						Εισαγωγή στα ακόλουθα μαθήματα (<u>πολλαπλών τμήμάτων</u>) :
	      					</label>
	      				</div>
	      				<div class="span7">
		      				<select multiple id="sectionsmultiple" size="7" class="span12" name="sections[]">
                      <?php foreach ($all_sections as $data):?>
                        <option value="<?php echo $data['id']?>"><?php echo $data['section_title'];?></option>
                      <?php endforeach;?>
		      				</select>
		      			</div>
		      		</div>
      			<div class="row-fluid">
      				<div class="span12">
      					<button type="button" class="btn btn-primary pull-right" id="addsubmit" name="addsubmit">Εισαγωγή</button>
      				</div>
      			</div>
      		</form>
      		</div>
      	</div>
      </div>
    </div>

		<div class="row-fluid"> <!--Πρόγραμμα Σπουδών-->
      	<div class="span12"> 
            <div class="contentbox">
              <div class="title">
                <span class="icon">
                  <i class="icon-file"></i>
                </span>
                <h5>Πρόγραμμα σπουδών</h5>
              </div>
            <div class="content">
					<div class="alert alert-info fade-in" id="nodata">
		          <p><i class="icon-info-sign"></i> Επιλέξτε την εισαγωγή του μαθητή σε όλα τα μαθήματα ενός τμήματος ή σε μαθήματα διαφορετικών τμημάτων από τα παραπάνω πεδία επιλογής.</p>
		      </div> <!-- end of nodata div -->
          <div id="data">
  		    	<form id="deleteform" charset="utf-8">
  		    	<table cellpadding="0" cellspacing="0" border="0"  id="lessonstable" class = "table table-striped table-condensed" width="100%">
  		    		<thead>
  		    			<th>Επιλογή</th>
  		    			<th>Μάθημα</th>
  		    			<th>Διδάσκων</th>
  		    			<th>Τμήμα</th>
  		    		</thead>
  		    		<tbody>
                  <!-- The table gets populated via ajax by datatables -->
  		    		</tbody>
        			</table>
        			<div class="row-fluid">
        				<div class="span12">
                  <div id="selectall">
                    <a href="#" onclick="selectall();return false;"><i class="icon-check"> Επιλογή όλων</i><a>
                  </div>
        					<button type="button" class="btn btn-danger pull-right" id="delsubmit" name="delsubmit">Διαγραφή επιλεγμένων</button>
        				</div>
        			</div>
        		</form>
          </div> <!-- end off data div -->
			  </div>
			</div>
		</div>
  </div>

  	</div> <!--end of fluid container-->

  </div> <!--end of main container-->

<div class="push"></div>
</div> <!-- end of body wrapper-->