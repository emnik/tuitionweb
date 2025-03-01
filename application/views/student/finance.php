<link href="<?php echo base_url('assets/bootstrap-datepicker-1.3.0/css/datepicker3.css') ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/bootstrap-datepicker-1.3.0/js/bootstrap-datepicker.js') ?>" ></script>
<script src="<?php echo base_url('assets/bootstrap-datepicker-1.3.0/js/locales/bootstrap-datepicker.el.js') ?>" charset="UTF-8"></script>

<style type="text/css">
  input.checkall {
      margin-right: 7px; 
  }

  #moveApyModal .modal-body {
		/*max-height: 700px;*/
    	overflow: visible;
	}
</style>

<script type="text/javascript">

function my_curr_date() {      
    var currentDate = new Date()
    var day = currentDate.getDate();
    var month = currentDate.getMonth() + 1;
    var year = currentDate.getFullYear();
    var my_date = day+"-"+month+"-"+year;
    return my_date;
}

var firstmonthpay;

var monthnames={1:'Ιανουαρίου',
               2:'Φεβρουαρίου',
               3:'Μαρτίου',
               4:'Απριλίου',
               5:'Μαΐου',
               6:'Ιουνίου',
               7:'Ιουλίου',
               8:'Αυγούστου',
               9:'Σεπτεμβρίου',
               10:'Οκτωβρίου',
               11:'Νοεμβρίου',
               12:'Δεκεμβρίου'};

var firstpay = false; 

var newindex = 0;
var newpay = 0;

$(document).ready(function() {

    //Menu current active links and Title
    $('#menu-operation').addClass('active');
    $('#menu-student').addClass('active');
    $('#menu-header-title').text('Καρτέλα Μαθητή');


  var undobtn = document.getElementById('undo_payment');
  undobtn.disabled=true;  


 // Move apy --------------------------------------------------------

    $('#selectStdToMoveApy').select2({
      dropdownParent: $('#moveApyModal'),
      minimumInputLength: 2,
      ajax: {
        url: "<?php echo base_url()?>welcome/user_list",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            q: params.term //sends the typed letters to the controller
          };
        },
        processResults: function (data) {
          return {
            results: data //data needs to be [{id:"",text:""}]
          };
        }
      }
    });

    // Manually set the selected value when an option is chosen
    $('#selectStdToMoveApy').on('select2:select', function (e) {
        // Manually set and trigger change
      var data = e.params.data;
        console.log('Selected:', data);
      $(e.currentTarget).find("option[value='" + data.id + "']").attr('selected','selected');
    });

    $('#moveApyModal').on('shown.bs.modal', function(){
        $('#selectStdToMoveApy').select2("open");
    });

    $('#selectStdToMoveApy').on('change', function(){
        $(".alert").hide();
        $('.alert > p > span').remove();
        $('.alert > p > a').remove();
    })
 // -----------------------------------------------------------------






  $('#firstpayment').click(function(){
      $('#nopayments').hide();
      $('#startpayments').removeClass("hidden");
      $('#actions').removeClass("hidden");
      firstpay = true;
      undobtn.disabled=false;         
      newpay = newpay + 1;
      set_latest_apyno(newpay);
      set_pay_data(newpay);
      $('#apydate1').attr('value', my_curr_date());
      var $divs = $('#startpayments form > fieldset > legend').siblings();
      $divs.show();
  });
  


  $('#add_payment').click(function(){
          undobtn.disabled=false; 
          newpay = newpay + 1;
          newindex = - newpay;
          var lastfieldset = $(this).parents('form').find('fieldset:last');
          var newfieldset = lastfieldset.clone();
          newfieldset.insertAfter(lastfieldset);
          var fields = newfieldset.find('input[type="text"]');
          
          //-------------set new apyno---------------
          fields.eq(0).attr("name", "apy_no[" + newindex +"]");        
          fields.eq(0).attr('id', "apyno"+newpay);

          if (firstpay==false && newpay==1){
            set_latest_apyno(newpay);
          }
          else{
            var newapyno = parseInt(fields.eq(0).val(), 10)+1;
            fields.eq(0).prop('value', newapyno);
            fields.eq(0).attr('value', newapyno);  
          };

          //-------------set new date---------------
          fields.eq(1).attr("name", "apy_dt[" + newindex +"]");  
          if (newpay==1 && firstpay==false){
            //I use attr to set the new value because the input is "dirty". If I use val() then 
            //it changes the visible value but not the value property of the field!!!
            fields.eq(1).attr('value', my_curr_date());
            //when I use the datepicker in the last field (the one it gets cloned) then the attr
            //changes the value property but not the visible value!!! (exact the opposite of above)
            //so I also use val and in both cases it works as expected
            fields.eq(1).val(my_curr_date());
          };
          $(fields.eq(1)).
          datepicker({
              format: "dd-mm-yyyy",
              language: "el",
              autoclose: true,
              todayHighlight: true
          })
          .on('focus click tap vclick', function (event) {
          //stop keyboard events and focus on the datepicker widget to get the date.
          //this is most usefull in android where the android's keyboard was getting in the way...
              event.stopImmediatePropagation();
              event.preventDefault();
              $(this).blur();
          });
          
          //----------set new month ammount----------
          fields.eq(2).attr("name", "amount[" + newindex +"]");  


          //----------set new month num-------------
          var prevmonthsetstr = fields.eq(3).val();
          var prevmonthsetarr = prevmonthsetstr.split(',');
          var prevmonth = parseInt(prevmonthsetarr[prevmonthsetarr.length-1],10);
          //var prevmonth=parseInt(fields.eq(3).val(),10);
          var newmonth;
          if(prevmonth==12){
            newmonth=1;  
          } 
          else {
            newmonth=prevmonth+1;  
          };
          
          newfieldset.find('legend').empty();
          newfieldset.find('legend').append('<span><i class="icon-certificate"></i></span>');
          newfieldset.find('legend').append('<div class="legend-text"> Πληρωμή ' + monthnames[newmonth]+'</div>');
          if (firstpay==false) {
              newfieldset.find('legend').append('<div class="legend-selector"> <input class="pull-right" type="checkbox" name="select['+ newindex +'] value=\'0\'"></div>');
          };
          
          fields.eq(3).prop('value', newmonth);  
          fields.eq(3).attr('value', newmonth);  
          
          fields.eq(3).attr("name", "month_range[" + newindex +"]");

          //-----------set new notes textarea-------------
          var txtareafields = newfieldset.find('input[type="textarea"]');
          txtareafields.eq(0).attr("name", "notes[" + newindex +"]");  

          //----------set new is_credit checkbox----------
          var chkboxfields = newfieldset.find('input[type="checkbox"]');
      
          chkboxfields.eq(2).attr("name", "is_credit[" + newindex +"]");  
          chkboxfields.eq(2).removeAttr("checked");
          chkboxfields.eq(2).val(0);  

          //if there are no previous payments the select boxes have no meaning!
          if (firstpay==false){
              //----------set new select checkbox----------
              //normal view
              chkboxfields.eq(1).attr("name", "select[" + newindex +"]");  
              chkboxfields.eq(1).removeAttr("checked");
              chkboxfields.eq(1).val(0);
              chkboxfields.eq(1).prop('disabled', true);
              //responsive view
              chkboxfields.eq(0).attr("name", "select[" + newindex +"]");  
              chkboxfields.eq(0).removeAttr("checked");
              chkboxfields.eq(0).val(0); 
              chkboxfields.eq(0).prop('disabled', true);
          }


          //----------disable the buttons-------------
          // newfieldset.find('a.btn').attr('onclick', 'return false;').addClass('disabled');

          //----------the new payments should always start visible!------
          var $divs = newfieldset.find('legend').siblings();
          $divs.show();
          //newfieldset.find('legend > span').html('<i class="icon-certificate"></i>');
  });


  $('#undo_payment').click(function(){
    if (newpay > 0) {
      var lastfieldset = $(this).parents('form').find('fieldset:last');

      if (firstpay==false || (firstpay==true && newpay>1)){
        lastfieldset.remove();  
        newpay = newpay - 1;
      }
      else if(newpay==1){
      //I don't remove the last fieldset because then there would be no fieldset to clone!!!     
        newpay = newpay - 1;
      };
      
      if (newpay==0){
        var fieldsets = $(this).parents('fieldset').length;   
        undobtn.setAttribute('disabled','disabled'); 
        if (firstpay==true){
          $('#startpayments').addClass("hidden");
          $('#actions').addClass("hidden");
          $('#nopayments').show();
        }
        //if I select all payments to be erased or canceled and I already have inserted a new record
        //then the new record will stay in place BUT if one presses undo, then there will be no payment
        //and no fieldset to clone so we need a new page load from the server
        else if (fieldsets==0) {
          window.location.href=window.location.href;
        }
      }
    }
  });

  //when we click a select box in responsive view the corresponding hidden select box in the normal view should get the same value.
  $('body').on('click', '.legend-selector input[type="checkbox"]', function(){
      var alterchkbox = $(this).parents('fieldset').find('.selector:hidden input[type="checkbox"]');
      if($(this).val()==0){
        $(this).val(1);
        $(this).attr('checked', 'checked');
        alterchkbox.prop('checked', true); //makes the checkbox checked
        alterchkbox.attr('checked', 'checked'); //just adds the property checked but it doesn't check the chekbox!!!
        alterchkbox.val(1);
      }
      else
      {
        $(this).val(0); 
        $(this).removeAttr('checked');
        alterchkbox.val(0);
        alterchkbox.removeAttr('checked');
      };
      //check or uncheck the "allcheck" checkbox depending on whether all payments are checked or not!
      var fieldsets = $('form').find('fieldset');
      var selected_chkboxes = $('form').find(':input[type="checkbox"][name*="select"]:visible:checked');

      if(selected_chkboxes.length == fieldsets.length)
      {
        $('.checkall').each(function(){
          $(this).prop('checked', true);
        });
      }
      else
      {
        $('.checkall').each(function(){
          $(this).prop('checked', false);
        });
      };

  });

  //when we click a select box in normal view the corresponding hidden select box in the responsive view should get the same value.
  $('body').on('click', '.selector input[type="checkbox"]', function(){
      var alterchkbox = $(this).parents('fieldset').find('.legend-selector input[type="checkbox"]');
      if($(this).val()==0){
        $(this).val(1);
        $(this).attr('checked', 'checked');
        alterchkbox.prop('checked', true); //makes the checkbox checked
        alterchkbox.attr('checked', 'checked'); //just adds the property checked but it doesn't check the chekbox!!!
        alterchkbox.val(1);
      }
      else
      {
        $(this).val(0); 
        $(this).removeAttr('checked');
        alterchkbox.val(0);
        alterchkbox.removeAttr('checked');
      };

      //check or uncheck the "allcheck" checkbox depending on whether all payments are checked or not!
      var fieldsets = $('form').find('fieldset');
      var selected_chkboxes = $('form').find(':input[type="checkbox"][name*="select"]:visible:checked');

      if(selected_chkboxes.length == fieldsets.length)
      {
        $('.checkall').each(function(){
          $(this).prop('checked', true);
        });
      }
      else
      {
        $('.checkall').each(function(){
          $(this).prop('checked', false);
        });
      };

  });


 //delete or cancel multiple payments using the select checkboxes and the combobox below (the action fires through ajax)
  $('#select_action').change(function(){
      var act=$(this).val();
      var fieldsets = $('form').find('fieldset');
      var selected_chkboxes = $('form').find(':input[type="checkbox"][name*="select"]:visible:checked');
      var allselected = false;
      if(selected_chkboxes.length == fieldsets.length) allselected = true;
      if (act!='none' && selected_chkboxes.length>0) {
        var selected_chkboxes = $('form').find(':input[type="checkbox"][name*="select"]:visible:checked');
        var sData = selected_chkboxes.serialize();
        switch(act){
          case 'delete':
            var msg="Πρόκειται να διαγράψετε τις επιλεγμένες πληρωμές. Η ενέργεια αυτή δεν αναιρείται. Παρακαλώ επιβεβαιώστε.";
            var post_url = "<?php echo base_url();?>student/payment_batch_actions/delete";
            break;
          case 'cancel':
            var msg="Πρόκειται να ακυρώσετε τις επιλεγμένες πληρωμές. Η ενέργεια αυτή δεν αναιρείται. Παρακαλώ επιβεβαιώστε.";
            var post_url = "<?php echo base_url();?>student/payment_batch_actions/cancel";
            break;
          case 'move':
            $('#moveApyModal').modal();
            break;
        };
        if(act!='move'){
          var ans=confirm(msg);
          if (ans==true){
            $.ajax({
              type: "post",
              url: post_url,
              data : sData,
              dataType:'json', 
              success: function(){
                if (allselected==true && newpay==0){
                    window.location.href = window.location.href;  
                }
                selected_chkboxes.each(function(){
                  $(this).parents('fieldset').remove();
                });
              }
            }); //end of ajax
          }
        } //end if ans
      } //end if act
      $(this).prop('selectedIndex',0);
  })


    /* Collaplsible fieldsets jquery based on
    https://github.com/malteo/bootstrap-collapsible-fieldset*/

   $('fieldset.collapsible > legend').prepend('<span><i class=" icon-plus-sign-alt"></i></span> ');
        $('body').on('click', 'fieldset.collapsible .legend-text', function () {
          var $divs = $(this).parent().siblings();
          $divs.toggle();
          if (!$(this).parent().find('span > i').hasClass('icon-certificate')) {
            $(this).parent().find('span').html(($divs.is(":visible")) ? '<i class="icon-minus-sign-alt"></i>' : '<i class=" icon-plus-sign-alt"></i>');            
          };
    });

   //color red the credit payments
   $('input[name^="is_credit"]').each(function(){
    if($(this).val()==1){
      $(this).parent().parent().parent().parent().find('.paylegend').css('color','red');
    }
   });

 //check or uncheck all payments
  $('.checkall').click(function(){
    var fieldsets = $('form').find('fieldset');
    var selected_chkboxes = $('form').find(':input[type="checkbox"][name*="select"]:visible:checked');
    if(selected_chkboxes.length < fieldsets.length)
    {
      $('form').find(':input[type="checkbox"][name*="select"]').each(function(){
        $(this).prop('checked', true);
        $(this).attr('checked', 'checked');
        $(this).val(1);
      });
      $('.checkall').each(function(){
        $(this).prop('checked', true);
        $(this).attr('checked', 'checked');
      });
    }
    else
      {
        $('form').find(':input[type="checkbox"][name*="select"]').each(function(){
          $(this).removeAttr('checked');
          $(this).val(0);
        });
        $('.checkall').each(function(){
          $(this).removeAttr('checked');        
        });
      };
  });

    $('.datecontainer input')
    .datepicker({
        format: "dd-mm-yyyy",
        language: "el",
        autoclose: true,
        todayHighlight: true
    })
    .on('focus click tap vclick', function (event) {
    //stop keyboard events and focus on the datepicker widget to get the date.
    //this is most usefull in android where the android's keyboard was getting in the way...
        event.stopImmediatePropagation();
        event.preventDefault();
        $(this).blur();
    });


}) //end of (document).ready()


//ajax to get apy_no
    function set_latest_apyno(id){
        //post_url is the controller function where I want to post the data
        var post_url = "<?php echo base_url()?>student/getlastapy/";
        var apyno;
        $.ajax({
          type: "POST",
          url: post_url,
          data : '',
          dataType:'json',
          //apydata is just a name that gets the result of the controller's function I posted the data
          success: function(apydata)
            {
              //ajax cannot return a value! We must set the value where we want it!
              //$('#apyno'+id).val(parseInt(apydata.apy_no)+1);
              $('#apyno'+id).attr('value', parseInt(apydata.apy_no,10)+1);
              $('#apyno'+id).prop('value', parseInt(apydata.apy_no,10)+1);
            } //end success
          }); //end AJAX
    }
 

//ajax to get first pay data
    function set_pay_data(id){
        //post_url is the controller function where I want to post the data
        var post_url = "<?php echo base_url()?>student/getfirstpaydata/";
        var apyno;
        var postdata = {'stdid':<?php echo $student['id'];?>}
        $.ajax({
          type: "POST",
          url: post_url,
          data : postdata,
          dataType:'json',
          //apydata is just a name that gets the result of the controller's function I posted the data
          success: function(paydata)
            {
              //ajax cannot return a value! We must set the value where we want it!
                if (paydata.month_price==null) 
                {
                  alert ('Παρακαλώ ενημερώστε την καρτέλα του μαθητή με την τρέχουσα τιμή μήνα!');
                }
                else 
                {
                  //$('#apyamount'+id).val(paydata.month_price);
                  $('#apyamount'+id).attr('value', paydata.month_price+'€');
                  $('#apyamount'+id).prop('value', paydata.month_price+'€');
                }
                
              
              //id==1 is needed because this ajax function could be called if the amount is empty numerous times
              //We only need to set the value once
              if (firstpay==true && id==1){    
                var fields = paydata.start_lessons_dt.split('-');
                var startmonth = parseInt(fields[1],10);
                $('#apymonthrange'+id).attr('value', startmonth);
                $('#apymonthrange'+id).prop('value', startmonth);
                
                var fieldset = $('#startpayments form > fieldset');
                fieldset.find('legend').empty();
                fieldset.find('legend').append('<span><i class="icon-certificate"></i></span>');
                fieldset.find('legend').append('<div class="legend-text">Πληρωμή ' + monthnames[startmonth]+'</div>');
              };
              
            } //end success
          }); //end AJAX
    }

//delete or cancel a specific payment
  function actionpay(action, id){
    var sData = 'select%5B'+id+'%5D=1';
    var fieldsets = $('form').find('fieldset');

    if (action == 'cancel')
    {
        var res = confirm("Πρόκειται να ακυρώσετε την ΑΠΥ. Σίγουρα Θέλετε να συνεχίσετε;");
          //window.location.href = "<?php echo base_url()?>student/payment_actions/<?php echo $student['id']?>/cancel/" + id;
          var post_url = "<?php echo base_url();?>student/payment_batch_actions/cancel";
    }
    else if (action == 'del')
      {
        var res = confirm("Πρόκειται να διαγράψετε την ΑΠΥ. Σίγουρα Θέλετε να συνεχίσετε;");
          //window.location.href = "<?php echo base_url()?>student/payment_actions/<?php echo $student['id']?>/del/" + id;
          var post_url = "<?php echo base_url();?>student/payment_batch_actions/delete";
      };

      if (res==true){
          $.ajax({
            type: "post",
            url: post_url,
            data : sData,
            dataType:'json', 
            success: function(){
              if (fieldsets.length==1){
                  window.location.href = window.location.href;  
                  //window.location.reload(true); it pops up an alert message from the browser
              }
            }
          }); //end of ajax
          $('input[name="select['+id+']"]').parents('fieldset').remove();  
      }

  }

   
  function moveapy(){
      id=$('#selectStdToMoveApy').val();
        var fieldsets = $('form').find('fieldset');
        var selected_chkboxes = $('form').find(':input[type="checkbox"][name*="select"]:visible:checked');
        var allselected = false;
        if(selected_chkboxes.length == fieldsets.length) allselected = true;
        var sData = selected_chkboxes.serialize();
        $.ajax({
            url: "<?php echo base_url();?>student/payment_batch_actions/move",
            method: 'post',
            data: sData + '&regid=' + id,
            dataType: 'json',
            success: function(response){
              console.log(response);
              if (response.success=='true'){
                selected_chkboxes.each(function(){
                  $(this).parents('fieldset').remove();
                });
                if (allselected==true && newpay==0){
                    window.location.href = window.location.href;  
                } else {
                  $('#moveApyModal').modal('hide');
                  // $('.alert > p').append('<span>Η μεταφορά ήταν επιτυχής! Μπορείτε να κλείσετε το παρών πλαίσιο.</span>');
                  // $('#moveapyresultmsg').removeClass();
                  // $('#moveapyresultmsg').addClass('alert');
                  // $('#moveapyresultmsg').addClass('alert-success');
                  // $(".alert").fadeIn();
                }
              }
              else {
                  $('.alert > p').append('<span>ΣΦΑΛΜΑ: Η μεταφορά ΔΕΝ ήταν επιτυχής. </span>');
                  $('#moveapyresultmsg').removeClass();
                  $('#moveapyresultmsg').addClass('alert');
                  $('#moveapyresultmsg').addClass('alert-danger');
                  $(".alert").fadeIn();
              }
             }
        })
    }

</script>


<?php $monthnames=array('1'=>'Ιανουαρίου',
                        '2'=>'Φεβρουαρίου',
                        '3'=>'Μαρτίου',
                        '4'=>'Απριλίου',
                        '5'=>'Μαΐου',
                        '6'=>'Ιουνίου',
                        '7'=>'Ιουλίου',
                        '8'=>'Αυγούστου',
                        '9'=>'Σεπτεμβρίου',
                        '10'=>'Οκτωβρίου',
                        '11'=>'Νοεμβρίου',
                        '12'=>'Δεκεμβρίου');?>

</head>
<body>

<div id="moveApyModal" class="modal" role="dialog" aria-labelledby="moveApyModalLabel" aria-hidden="true">
	<div class="modal-dialog">
      <div class="modal-content">	
		<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="moveApyModalLabel">Μεταφορά ΑΠΥ σε άλλο μαθητή</h3>
		</div>
		<div class="modal-body">
			 <div class="form-group">
			 	<label for="single" class="control-label">Επιλέξτε μαθητή/μαθήτρια:</label>
 			 	<input style="color:black;" class="form-control" id="selectStdToMoveApy" type="hidden" name="optionvalue" />
			 </div>
		</div>
		<div class="modal-footer">
			<!-- <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button> -->
			<button class="btn btn-primary" onclick="moveapy();">Μεταφορά</button>
			<div class="alert alert-success" role="alert" id="moveapyresultmsg" style="display:none; margin-top:10px;">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      			<p style="text-align:left;"> </p> 
			</div>

		</div>
		</div>
	</div>
</div>






 <div class="wrapper"> <!--body wrapper for css sticky footer-->
 
    <!-- Menu start -->
    <!-- dirname(__DIR__) gives the path one level up by default -->
    <?php include(dirname(__DIR__).'/include/menu.php');?> 
    <!-- Menu end -->


<!-- main container
================================================== -->

  <div class="container"  style="margin-bottom:60px;">
      
      <div>
        <ul class="breadcrumb">
          <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a></li>
          <li><a href="<?php echo base_url()?>student">Μαθητολόγιο</a> </li>
          <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>">Καρτέλα μαθητή</a> </li>
          <li class="active">Οικονομικά</li>
          <!-- <li class="dash"><i class="icon-dashboard icon-small"></i></li> -->
        </ul>
      </div>
      
      
      <p>
        <h3><?php echo $student['surname'].' '.$student['name']?></h3>
      </p>

      <ul class="nav nav-tabs">
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>">Στοιχεία</a></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/contact">Επικοινωνία</a></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance">Φοίτηση</a></li>
        <li class="active"><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance">Οικονομικά</a></li>
      </ul>

      <?php if($student['active']==0):?>
        <div class="alert alert-danger" role="alert" style='margin-top:10px; margin-left:0px; margin-right:0px;'>
          <i class="icon-warning-sign"> </i><strong> ΠΡΟΣΟΧΗ! Τα δεδομένα αφορούν στη διαχειριστική περίοδο <u><?php echo($student['termname']);?></u> και όχι στην επιλεγμένη!</strong>
        </div>
      <?php endif;?>

      <ul class="nav nav-pills"  style="margin:15px 0px;">
        <li class="active"><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance">Πληρωμές</a></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance/changes">Μεταβολές</a></li>        
      </ul>


      <div class="row">
        <div class="col-md-12"> <!--main column-->
          <div class="panel panel-default">
          <div class="panel-heading">
            <span class="icon">
              <i class="icon-shopping-cart"></i>
            </span>
            <h3 class="panel-title">Πληρωμές διδάκτρων</h3>
            <div class="buttons visible-xs" style="padding-top:3px;">
                <input type="checkbox" class="checkall">
            </div>
          </div>
        </div>
      </div>
    </div>
   <?php if (!empty($payments)):?>
      <div class="multiplefieldset-header">    
        <div class="row">
          <div class="col-md-1 col-sm-1"><input type="checkbox" class="checkall"></div>
          <div class="col-md-2 col-sm-2">Αρ. ΑΠΥ</div>
          <div class="col-md-2 col-sm-2">Ημερομηνία ΑΠΥ</div>
          <div class="col-md-2 col-sm-2">Ποσό Πληρωμής</div>
          <div class="col-md-2 col-sm-2">Μήνας</div>
          <div class="col-md-1 col-sm-1">Ε.Π.</div>
          <div class="col-md-2 col-sm-2">Παρατηρήσεις</div>
          <!-- <div class="col-md-1"><p class="pull-right">Ενέργειες</p></div> -->
        </div>
      </div>
        <div class="row">
          <div class="col-md-12 col-sm-12">
              <form action="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance" method="post" accept-charset="utf-8">
                <?php foreach ($payments as $data):?>
                  <fieldset class="multiplefieldset collapsible"> <!--start of fieldset-->
                    <legend class="paylegend">
                      <div class='legend-text'>
                        Πληρωμή 
                        <?php 
                        $monthrange = explode(',', $data['month_range']);
                        if (count($monthrange)>1){
                          $startmonth = $monthrange[0];
                          $endmonth = $monthrange[count($monthrange)-1];
                          echo mb_substr($monthnames[$startmonth], 0, 7, 'utf-8').'. - '.mb_substr($monthnames[$endmonth], 0, 7, 'utf-8').'.';
                        }
                        else{
                          $startmonth = $monthrange[0];
                          echo $monthnames[$startmonth];  
                        };?>
                      </div>
                      <div class='col-md-1 col-sm-1 legend-selector'>
                          <input type="checkbox" name="select[<?php echo $data['id'];?>]" value="0">
                      </div>
                    </legend>
                      <div class="clearfix"></div>
                         <div class="row"> <!--main form row-->
                            <div class="col-md-1 col-sm-1 hidden-xs selector">
                                <input type="checkbox" name="select[<?php echo $data['id'];?>]" value="0">  
                            </div>

                            <div class="col-md-2 col-sm-2">
                              <input type="text" class="form-control" placeholder="Αριθμός ΑΠΥ" name="apy_no[<?php echo $data['id'];?>]" value="<?php echo $data['apy_no'];?>">
                            </div>

                            <div class="col-md-2 col-sm-2 datecontainer">
                              <input type="text" class="form-control" placeholder="Ημερομηνια ΑΠΥ" name="apy_dt[<?php echo $data['id'];?>]" value="<?php echo implode('-', array_reverse(explode('-', $data['apy_dt'])));?>">
                            </div>

                            <div class="col-md-2 col-sm-2">
                              <input type="text" class="form-control" placeholder="Ποσό" name="amount[<?php echo $data['id'];?>]" value="<?php echo $data['amount'];?>€">
                            </div>

                            <div class="col-md-2 col-sm-2">
                              <input type="text" class="form-control" placeholder="Μήνας" name="month_range[<?php echo $data['id'];?>]" value="<?php echo $data['month_range'];?>">
                            </div>

                            <div class="col-md-1 col-sm-1">
                                <label class="checkbox" style="margin-left: 20px;">
                                  <input type="checkbox" name="is_credit[<?php echo $data['id'];?>]" <?php if($data['is_credit']==true) echo "checked='yes'";?> value=<?php echo $data['is_credit'];?>>
                                Ε.Π. </label>
                            </div>
                            
                            <div class="col-md-2 col-sm-2">
                              <input type="textarea" rows="1" class="form-control" placeholder="Παρατηρήσεις" name="notes[<?php echo $data['id'];?>]" value="<?php echo $data['notes'];?>">
                            </div>

                        </div> <!--end main form row-->

                </fieldset> <!--end of fieldset-->
       
                <?php endforeach;?>

                <div class="row">
                  <div class="col-md-1 col-sm-1 hidden-xs">
                    <span style="margin-left:15px;">
                      <i class="icon-hand-up"></i>
                    </span>
                  </div>
                  <div class="col-md-2 col-sm-3" style="padding-right:0px;">
                    <label>Με τα επιλεγμένα : </label>
                  </div>
                  <div class="col-md-3 col-sm-3">
                     <select class="form-control"  name="select_action" id="select_action">
                        <option value="none" selected>-</option>
                        <option value="move">Μεταφορά</option>
                        <option value="delete">Διαγραφή</option>
                        <option value="cancel">Ακύρωση</option>
                      </select>
                  </div>
                </div>


              <div style="margin-top:30px;">
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-primary" name="add_payment" id="add_payment">Πληρωμή</button>
                    <button type="button" class="btn btn-primary" name="undo_payment" id="undo_payment"><span class="icon"><i class="icon-undo"></i></span></button>
                </div>
                <button type="submit" class="btn btn-danger" name="submit">Αποθήκευση</button>
              </div>

            </form>

            <?php else:?>
              <div id="nopayments" >
                <div class="alert alert-info">
                  <p>Δεν έχει πραγματοποιηθεί ακόμα καμία πληρωμή!</p>
                </div>
                <div style="margin-bottom:70px;">
                  <button type="button" class="btn btn-primary pull-right" id="firstpayment">Πληρωμή</button>
                </div>
              </div>

              <div id="startpayments" class="hidden">
                <div class="multiplefieldset-header">    
                  <div class="row">
                    <div class="col-md-2">Αρ. ΑΠΥ</div>
                    <div class="col-md-2">Ημερομηνία ΑΠΥ</div>
                    <div class="col-md-2">Ποσό</div>
                    <div class="col-md-2">Μήνας</div>
                    <div class="col-md-1">Ε.Π.</div>
                    <div class="col-md-2">Παρατηρήσεις</div>
                  </div>
                </div>
                  <div class="row">
                    <div class="col-md-12">
                  <form action="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance" method="post" accept-charset="utf-8">
                  <fieldset class="multiplefieldset collapsible"> <!--start of fieldset-->
                    <legend class="paylegend"></legend>     
                         <div class="row"> <!--main form row-->
                            <div class="col-md-2">
                              <input type="text" id="apyno1" class="form-control" name="apy_no[-1]" value="">
                            </div>

                            <div class="col-md-2 datecontainer">
                              <input type="text" id="apydate1" class="form-control" name="apy_dt[-1]" value="">
                            </div>

                            <div class="col-md-2">
                              <input type="text" id="apyamount1" class="form-control" name="amount[-1]" value="">
                            </div>

                            <div class="col-md-2">
                              <input type="text" id="apymonthrange1" class="form-control" name="month_range[-1]" value="">
                            </div>

                            <div class="col-md-1">
                                <input type="checkbox" name="is_credit[-1]" value="0">
                            </div>
                            
                            <div class="col-md-2">
                              <input type="textarea" rows="1" class="form-control" name="notes[-1]" value="">
                            </div>

                        </div> <!--end main form row-->

                </fieldset> <!--end of fieldset-->

                <div id="actions" class="hidden">
                  <div class="btn-group pull-right">
                      <button type="button" class="btn btn-primary" name="add_payment" id="add_payment">Πληρωμή</button>
                      <button type="button" class="btn btn-primary" name="undo_payment" id="undo_payment"><span class="icon"><i class="icon-undo"></i></span></button>
                  </div>
                  <button type="submit" class="btn btn-danger" id="submit1" name="submit">Αποθήκευση</button>
                </div>

                </form>
              </div>
            </div>
          </div>
            <?php endif;?>
          </div>
        <!-- </div>  end of well class-->

      </div>
  </div> <!--end of main container-->

<div class="push"></div>
</div> <!-- end of body wrapper-->