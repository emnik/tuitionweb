<script type="text/javascript">

function my_curr_date() {      
    var currentDate = new Date()
    var day = currentDate.getDate();
    var month = currentDate.getMonth() + 1;
    var year = currentDate.getFullYear();
    var my_date = day+"-"+month+"-"+year;
    return my_date;
};


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

var firstchange = false; 

$(document).ready(function() {
  var newindex = 0;
  var newchange = 0;
  var undobtn = document.getElementById('undo_change');
  undobtn.disabled=true;  


  $('#firstchangebtn').click(function(){
      $('#nochanges').hide();
      $('#startchanges').removeClass("hidden");
      $('#actions').removeClass("hidden");
      firstchange = true;
      undobtn.disabled=false;         
      newchange = newchange + 1;
      set_latest_apyno(newchange);
      set_pay_data(newchange);
      $('#apydate1').attr('value', my_curr_date());
      var $divs = $('#startchanges form > fieldset > legend').siblings();
      $divs.show();
  });
  


  $('#add_change').click(function(){
          undobtn.disabled=false; 
          newchange = newchange + 1;
          newindex = - newchange;
          var lastfieldset = $(this).parents('form').find('fieldset:last');
          var newfieldset = lastfieldset.clone();
          newfieldset.insertAfter(lastfieldset);
          var fields = newfieldset.find('input[type="text"]');
          
          //-------------set new apyno---------------
          // fields.eq(0).attr("name", "apy_no[" + newindex +"]");        
          // fields.eq(0).attr('id', "apyno"+newchange);

          // if (firstchange==false && newchange==1){
          //   set_latest_apyno(newchange);
          // }
          // else{
          //   var newapyno = parseInt(fields.eq(0).val(), 10)+1;
          //   fields.eq(0).prop('value', newapyno);
          //   fields.eq(0).attr('value', newapyno);  
          // };


          //-------------set new date---------------
          //I use attr to set the new value because the input is "dirty". If I use val() then 
          //it changes the visible value but not the value property of the field!!!
          fields.eq(0).attr("name", "change_dt[" + newindex +"]");  
          if (newchange==1 && firstchange==false){
            fields.eq(0).attr('value', my_curr_date());
          };      
          
          //----------set new month price----------
          fields.eq(2).attr("name", "new_month_price[" + newindex +"]");  


          //----------set new month num-------------
          // var prevmonth=parseInt(fields.eq(3).val(),10);
          // var newmonth;
          // if(prevmonth==12){
          //   newmonth=1;  
          // } 
          // else {
          //   newmonth=parseInt(fields.eq(3).val(),10)+1;  
          // };
          
          newfieldset.find('legend').empty();
          newfieldset.find('legend').append('<span><i class="icon-certificate"></i></span>');
          newfieldset.find('legend').append('<div class="legend-text"> Μεταβολή ' + my_curr_date() +'</div>');
          if (firstchange==false) {
              newfieldset.find('legend').append('<div class="legend-selector"> <input class="pull-right" type="checkbox" name="select['+ newindex +'] value=\'0\'"></div>');
          };
          
          // fields.eq(3).prop('value', newmonth);  
          // fields.eq(3).attr('value', newmonth);  
          
          // fields.eq(3).attr("name", "month_range[" + newindex +"]");

          //-----------set new reason textarea-------------
          var txtareafields = newfieldset.find('input[type="textarea"]');
          txtareafields.eq(0).attr("name", "reason[" + newindex +"]");  

          //-----------set new notes textarea-------------
          var txtareafields = newfieldset.find('input[type="textarea"]');
          txtareafields.eq(1).attr("name", "notes[" + newindex +"]");  

          //----------set new is_credit checkbox----------
          // var chkboxfields = newfieldset.find('input[type="checkbox"]');
      
          // chkboxfields.eq(2).attr("name", "is_credit[" + newindex +"]");  
          // chkboxfields.eq(2).removeAttr("checked");
          // chkboxfields.eq(2).val(0);  

          //if there are no previous payments the select boxes have no meaning!
          if (firstchange==false){
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
          };


          //----------disable the buttons-------------
          newfieldset.find('a.btn').attr('onclick', 'return false;').addClass('disabled');

          //----------the new payments should always start visible!------
          var $divs = newfieldset.find('legend').siblings();
          $divs.show();
          //newfieldset.find('legend > span').html('<i class="icon-certificate"></i>');
  });


    $('.delbtn').tooltip({
      title:"Διαγραφή ΑΠΥ",
      trigger:'hover',
      placement: 'top',
      container:'body'
   });


  $('#undo_change').click(function(){
    if (newchange > 0) {
      var lastfieldset = $(this).parents('form').find('fieldset:last');

      if (firstchange==false || (firstchange==true && newchange>1)){
        lastfieldset.remove();  
        newchange = newchange - 1;
      }
      else if(newchange==1){
      //I don't remove the last fieldset because then there would be no fieldset to clone!!!     
        newchange = newchange - 1;
      };
      
      if (newchange==0){
        var fieldsets = $(this).parents('fieldset').length;   
        undobtn.setAttribute('disabled','disabled'); 
        if (firstchange==true){
          $('#startchanges').addClass("hidden");
          $('#actions').addClass("hidden");
          $('#nochanges').show();
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
      }
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
      }
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
        };
        var ans=confirm(msg);
        if (ans==true){
            $.ajax({
              type: "post",
              url: post_url,
              data : sData,
              dataType:'json', 
              success: function(){
                if (allselected==true && newchange==0){
                    window.location.href = window.location.href;  
                    //window.location.reload(true); it pops up an alert message from the browser
                }
              }
            }); //end of ajax
            selected_chkboxes.each(function(){
              $(this).parents('fieldset').remove();
            });
            $(this).prop('selectedIndex',0);
        } //end if ans
      } //end if act
  })




});

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
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance">Οικονομικά</a> <span class="divider">></span></li>
        <li class="active">Μεταβολές</li>
      </ul>
        <!-- <a class="btn btn-mini" href="<?php echo base_url();?>"><i class="icon-arrow-left"></i> πίσω</a>         -->
      </div>
      
      

      <h3><?php echo $student['surname'].' '.$student['name']?></h3>
      

      <ul class="nav nav-tabs">
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>">Στοιχεία</a></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/contact">Επικοινωνία</a></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance">Φοίτηση</a></li>
        <li class="active"><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance">Οικονομικά</a></li>
      </ul>


      <ul class="nav nav-pills">
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance">Πληρωμές</a></li>
        <li class="active"><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance/changes">Μεταβολές</a></li>        
      </ul>

      <div class="row-fluid">
 
        <div class="span12"> 

          <div class="contentbox">
            <div class="title">
              <span class="icon">
                <i class="icon-retweet"></i>
              </span>
              <h5>Μεταβολές διδάκτρων</h5>
              <div class="buttons">
                  <!-- <button enabled id="editform1" type="button" class="btn btn-mini" data-toggle="button"><i class="icon-edit"></i></button> -->
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php if(!empty($change)):?>
        <div class="multiplefieldset-header">    
          <div class="row-fluid">
            <div class="selector"></div>
            <div class="span2">Ημερ/νία</div>
            <div class="span1">Από</div>
            <div class="span1">Σε</div>
            <div class="span2">Αιτιολογία</div>
            <div class="span2">Σημειώσεις</div>
            <div class="span2"><p class="pull-right">Ενέργειες</p></div>
          </div>
        </div>
        <div class="row-fluid">
          <div class="span12">
            <form action="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance/changes" method="post" accept-charset="utf-8">
              <?php foreach ($change as $data):?>
                <fieldset class="multiplefieldset collapsible"> <!--start of fieldset-->
                    <legend class="paylegend">
                      <div class='legend-text'>
                        Μεταβολή <?php echo $data['change_dt'];?>
                      </div>
                      <div class='legend-selector'>
                        <input class="pull-right" type="checkbox" name="select[<?php echo $data['id'];?>]" value="0">
                      </div>
                    </legend>

                <div class="row-fluid"> 
                  
                  <div class="selector">
                    <input type="checkbox" class="checkbox" name="select[<?php echo $data['id'];?>]" value="0"></input>
                  </div>
                  
                  <div class="span2">
                    <input type="text" class="span12" name="change_dt[<?php echo $data['id'];?>]" value="<?php echo implode('-', array_reverse(explode('-', $data['change_dt'])));?>"></input>
                  </div>

                  <div class="span1">
                    <input type="text" class="span12" name="prev_month_price[<?php echo $data['id'];?>]" value="<?php echo $data['prev_month_price'];?>€"></input>
                  </div>

                  <div class="span1">
                    <input type="text" class="span12" name="new_month_price[<?php echo $data['id'];?>]" value="<?php echo $data['new_month_price'];?>€">
                  </div>
               
                  <div class="span2">
                    <input type="textarea" class="span12" name="reason[<?php echo $data['id'];?>]" value="<?php echo $data['reason'];?>">
                  </div>

                  <div class="span2">
                    <input type="textarea" rows="1" class="span12" name="notes[<?php echo $data['id'];?>]" value="<?php echo $data['notes'];?>">
                  </div>

                  <div class="span2">
                    <div class="btn-group pull-right">
                      <a class="btn delbtn" onclick="actionchange('del',<?php echo $data['id']?>);return false;" href="#"><i class="icon-remove-circle"></i></a>
                    </div>
                  </div>

                </div>
              </fieldset>
              <?php endforeach;?>
                <div class="payments-selected">
                  <span><i class="icon-hand-up"></i></span>
                  <p>Με τα επιλεγμένα :  
                    <select class="input-medium" style="margin:3px 0px 5px 6px;" name="select_action" id="select_action">
                      <option value="none" selected>-</option>
                      <option value="delete">Διαγραφή</option>
                    </select>
                  </p>
                </div>

              <div class="form-actions">
                <button type="button" class="btn btn-primary pull-right" name="add_change" id="add_change">Μεταβολή</button>
                <button type="button" class="btn" name="undo_change" id="undo_change">Αναίρεση</button>
                <button type="submit" class="btn btn-danger" name="submit">Αποθήκευση</button>
              </div>

            </form>


            <?php else:?>
            
              <div id="nochanges" >
               <div class="alert alert-info">
                  <p>Δεν υπάρχει καμία μεταβολή στα δίδακτρα!</p>
                </div>
                <div style="margin-bottom:70px;">
                  <button type="button" class="btn btn-primary pull-right" id="firstchangebtn">Μεταβολή</button>
                </div>
              </div>


             <div id="startchanges" class="hidden">
                <div class="multiplefieldset-header">    
                  <div class="row-fluid">
                    <div class="span2">Ημερ/νία</div>
                    <div class="span2">Από</div>
                    <div class="span2">Σε</div>
                    <div class="span2">Αιτιολογία</div>
                    <div class="span2">Σημειώσεις</div>
                    <div class="span2"><p class="pull-right">Ενέργειες</p></div>
                  </div>
                </div>
              <div class="row-fluid">
                <div class="span12">
                  <form action="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance/changes" method="post" accept-charset="utf-8">
                  <fieldset class="multiplefieldset collapsible"> <!--start of fieldset-->
                    <legend class="paylegend"></legend>     
                         <div class="row-fluid"> <!--main form row-->
                            <div class="span2">
                              <input type="text" id="changedt1" class="span12" name="change_dt[-1]" value="">
                            </div>

                            <div class="span2">
                              <input type="text" id="prevmonthprice1" class="span12" name="prev_month_price[-1]" value=""></input>
                            </div>

                            <div class="span2">
                              <input type="text" id="nextmonthprice1" class="span12" name="new_month_price[-1]" value=""></input>
                            </div>

                            <div class="span2">
                              <input type="textarea" id="reason1" class="span12" name="reason[-1]" value="">
                            </div>

                            <div class="span2">
                              <input type="textarea" id="notes1" class="span12" name="notes[-1]" value="">
                            </div>

                            <div class="span2">
                              <div class="btn-group pull-right">
                                <a class="btn delbtn" onclick="return false;" href="#" disabled><i class="icon-remove-circle"></i></a>
                              </div>
                            </div>

                        </div> <!--end main form row-->

                </fieldset> <!--end of fieldset-->

                <div id="actions" class="form-actions hidden">
                  <button type="button" class="btn btn-primary pull-right" name="add_change" id="add_change">Πληρωμή</button>
                  <button type="button" class="btn" name="undo_change" id="undo_change">Αναίρεση</button>
                  <button type="submit" class="btn btn-danger" id="submit1" name="submit">Αποθήκευση</button>
                </div>

                </form>
              </div>
            </div>
          </div> <!--end of startpages-->
            



            <?php endif;?>

            </div>
      </div> 
    </form>
    </div> <!--end of row-->
   </div> <!--end of fluid container--> 
  </div> <!--end of main container-->

<div class="push"></div>
</div> <!-- end of body wrapper-->