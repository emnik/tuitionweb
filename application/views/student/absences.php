<script type="text/javascript">

$(document).ready(function(){
    $('.footable').footable();

//delete multiple absences using the select checkboxes and the combobox below (the action fires through ajax)
  $('#select_action').change(function(){
      var act=$(this).val();
      var selected_chkboxes = $('form').find(':input[type="checkbox"][name*="select"]:checked');
      var allselected = false;
      if(selected_chkboxes.length == $('form').find(':input[type="checkbox"][name*="excused"]').length) allselected = true;
      if (act!='none' && selected_chkboxes.length>0) {
        var selected_chkboxes = $('form').find(':input[type="checkbox"][name*="select"]:checked');
        var sData = selected_chkboxes.serialize();
        switch(act){
          case 'delete':
            var msg="Πρόκειται να διαγράψετε τις επιλεγμένες απουσίες. Η ενέργεια αυτή δεν αναιρείται. Παρακαλώ επιβεβαιώστε.";
            var post_url = "<?php echo base_url();?>student/absences_batch_actions/delete";
            break;
        };
        var ans=confirm(msg);
        if (ans==true){
            $.ajax({
              type: "post",
              url: post_url,
              data : sData,
              dataType:'json', 
              success: function(){
                if (allselected==true){
                    window.location.href = window.location.href;  
                }
              }
            }); //end of ajax
            selected_chkboxes.each(function(){
              //if there is a hidden row with data generated from footable remove that as well!
              if ($(this).parents('tr').hasClass('footable-detail-show')){
                $(this).parents('tr').next('tr').remove();  
              };
              $(this).parents('tr').remove();  
            });
        } //end if ans
      $(this).prop('selectedIndex',0);
      } //end if act
  })


  $('#checkall').click(function(){
    var selected_chkboxes = $('form').find(':input[type="checkbox"][name*="select"]:checked');
    if(selected_chkboxes.length < $('form').find(':input[type="checkbox"][name*="excused"]').length)
    {
      $('form').find(':input[type="checkbox"][name*="select"]').each(function(){
        $(this).prop('checked', true);
      });
    }
    else
      {
        $('form').find(':input[type="checkbox"][name*="select"]').each(function(){
          $(this).prop('checked', false);
        });
      };
  });

  $('form').find(':input[type="checkbox"][name*="select"]').click(function(){
      var selected_chkboxes = $('form').find(':input[type="checkbox"][name*="select"]:checked');
      if(selected_chkboxes.length == $('form').find(':input[type="checkbox"][name*="excused"]').length)
      {
        $('#checkall').prop('checked', true);
      }
      else
      {
        $('#checkall').prop('checked', false); 
      };
  });

});

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
                <li class="dropdown-header">Νικηφορακης Μανος</li>
                <li><a href="#">Αλλαγή κωδικού</a></li>
                <li><a href="#admin">Διαχείριση</a></li>
                <li class="divider"></li>
                <li><a href="#">Αποσύνδεση</a></li>
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
          <li><a href="<?php echo base_url()?>student">Μαθητολόγιο</a></li>
          <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>">Καρτέλα μαθητή</a></li>
	      <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance">Φοίτηση</a></li>
          <li class="active">Απουσιολόγιο</li>
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

      <div style="margin:15px 0px;">
	      <div class="row">
	        <div class="col-md-12 col-sm-12">
	            <a class="btn btn-default" href="<?php echo base_url();?>student/card/<?php echo $student['id']?>/attendance"><i class="icon-chevron-left"></i> πίσω</a>
	        </div>
	      </div>
  	  </div>

      <p></p>

   	  <div class="row">
      	<div class="col-md-12 col-sm-12"> 
      		<div class="panel panel-default">
      			<div class="panel-heading">
        			<span class="icon">
          				<i class="icon-flag"></i>
        			</span>
    				<h3 class="panel-title">Απουσιολόγιο</h3>
      			</div>
    			<div class="panel-body">
  					<?php if (!empty($absences)):?>
            <form action="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance/absences" method="post" accept-charset="utf-8">
              <table class="table footable table-striped">
    						<thead>
    							<tr>
                    <th><input type="checkbox" class="checkbox" id="checkall"></th>
    								<th data-class="expand">Ημερομηνία</th>
    								<th data-hide="phone">Μάθημα</th>
                    <th data-hide="phone,tablet">Διδάσκων</th>
    								<th data-hide="phone">Ώρα</th>
    								<th>Δικαιολ/μένη</th>
    							</tr>
    						</thead>
    						<tbody>
    							<?php foreach ($absences as $data):?>
    								<tr>
                      <td><input type="checkbox" name="select[<?php echo $data['id'];?>]"></td>
    									<td><?php echo implode('-', array_reverse(explode('-', $data['date'])));?></td>
    									<td><?php echo $data['title'];?></td>
                      <td><?php echo $data['nickname'];?></td>
    									<td><?php echo $data['hours'];?></td>
    									<td><input type="checkbox" name="excused[<?php echo $data['id'];?>]" value="<?php echo $data['excused'];?>" <?php if($data['excused']==1) echo 'checked="checked"';?>></td>
    								</tr>
    							<?php endforeach;?>
    						</tbody>
    					</table>
          <?php else:?>
            <p>Δεν υπαρχει καμία απουσία καταχωρημένη!</p> 
          <?php endif;?>
          </div> <!-- end of panel body -->
			</div> <!-- end of panel -->
		
    <?php if (!empty($absences)):?>
      <div class="row">
        <div class="col-md-1 col-sm-1 hidden-xs">
          <span style="margin-left:25px;">
            <i class="icon-hand-up"></i>
          </span>
        </div>
        <div class="col-md-2 col-sm-3">
          <label>Με τα επιλεγμένα : </label>
        </div>
        <div class="col-md-3 col-sm-3">
           <select class="form-control" style="margin-bottom:15px;" name="select_action" id="select_action">
              <option value="none" selected>-</option>
              <option value="delete">Διαγραφή</option>
            </select>
        </div>
        <div class="col-md-6 col-sm-5">
          <button type="submit" class="btn btn-primary pull-right">Αποθήκευση</button>
        </div>
      </form>
    </div>  
  <?php endif;?>


    </div>
	</div>
  </div> <!--end of main container-->
<div class="push"></div>
</div> <!-- end of body wrapper-->