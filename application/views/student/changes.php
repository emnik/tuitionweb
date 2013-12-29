<script type="text/javascript">

function my_curr_date() {      
    var currentDate = new Date()
    var day = currentDate.getDate();
    var month = currentDate.getMonth() + 1;
    var year = currentDate.getFullYear();
    var my_date = day+"-"+month+"-"+year;
    return my_date;
};

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
      set_change_data(newchange);
      $('#changedt1').attr('value', my_curr_date());

      var hiddenfieldset =  $('#startchanges form > fieldset');
      hiddenfieldset.find('legend').empty();
      hiddenfieldset.find('legend').append('<span><i class="icon-certificate"></i></span>');
      hiddenfieldset.find('legend').append('<div class="legend-text"> Μεταβολή ' + my_curr_date() +'</div>');

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
          

          //-------------set new date---------------
          //I use attr to set the new value because the input is "dirty". If I use val() then 
          //it changes the visible value but not the value property of the field!!!
          fields.eq(0).attr("name", "change_dt[" + newindex +"]");  
          if (newchange==1 && firstchange==false){
            fields.eq(0).attr('value', my_curr_date());
          };      
          

          //----------set prev month price----------
          var prevnew;
          prevnew = fields.eq(2).val();
          fields.eq(1).prop('value', prevnew);  
          fields.eq(1).attr('value', prevnew); 
          fields.eq(1).attr("name", "prev_month_price[" + newindex +"]");

          //----------set new month price----------          
          fields.eq(2).attr("name", "new_month_price[" + newindex +"]");
          fields.eq(2).prop('value', '');  
          fields.eq(2).attr('value', ''); 

          
          newfieldset.find('legend').empty();
          newfieldset.find('legend').append('<span><i class="icon-certificate"></i></span>');
          newfieldset.find('legend').append('<div class="legend-text"> Μεταβολή ' + my_curr_date() +'</div>');
          if (firstchange==false) {
              newfieldset.find('legend').append('<div class="legend-selector"> <input class="pull-right" type="checkbox" name="select['+ newindex +'] value=\'0\'"></div>');
          };
          

          //-----------set new reason textarea-------------
          var txtareafields = newfieldset.find('input[type="textarea"]');
          txtareafields.eq(0).attr("name", "reason[" + newindex +"]");  
          txtareafields.eq(0).attr("value",'');
          txtareafields.eq(0).prop("value",'');
          
          //-----------set new notes textarea-------------
          var txtareafields = newfieldset.find('input[type="textarea"]');
          txtareafields.eq(1).attr("name", "notes[" + newindex +"]");  
          txtareafields.eq(1).attr("value",'');
          txtareafields.eq(1).prop("value",'');

          //if there are no previous payments the select boxes have no meaning!
          var chkboxfields = newfieldset.find('input[type="checkbox"]');
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
          // newfieldset.find('a.btn').attr('onclick', 'return false;').addClass('disabled');

          //----------the new payments should always start visible!------
          var $divs = newfieldset.find('legend').siblings();
          $divs.show();
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

      //check or uncheck the "allcheck" checkbox depending on whether all changes are checked or not!
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
      }

      //check or uncheck the "allcheck" checkbox depending on whether all changes are checked or not!
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


//delete multiple changes using the select checkboxes and the combobox below (the action fires through ajax)
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
            var msg="Πρόκειται να διαγράψετε τις επιλεγμένες μεταβολές. Η ενέργεια αυτή δεν αναιρείται. Παρακαλώ επιβεβαιώστε.";
            var post_url = "<?php echo base_url();?>student/changes_batch_actions/delete";
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


    /* Collaplsible fieldsets jquery based on
    https://github.com/malteo/bootstrap-collapsible-fieldset*/

   $('fieldset.collapsible > legend').prepend('<span><i class=" icon-plus-sign"></i></span> ');
        $('body').on('click', 'fieldset.collapsible .legend-text', function () {
          var $divs = $(this).parent().siblings();
          $divs.toggle();
          if (!$(this).parent().find('span > i').hasClass('icon-certificate')) {
            $(this).parent().find('span').html(($divs.is(":visible")) ? '<i class="icon-minus-sign"></i>' : '<i class=" icon-plus-sign"></i>');            
          };
    });


//check or uncheck all changes
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


}); //end of $(document).ready()

//ajax to get current price
    function set_change_data(id){
        //post_url is the controller function where I want to post the data
        var post_url = "<?php echo base_url()?>student/getfirstchangedata/";
        var apyno;
        var postdata = {'stdid':<?php echo $student['id'];?>}
        $.ajax({
          type: "POST",
          url: post_url,
          data : postdata,
          dataType:'json',
          //apydata is just a name that gets the result of the controller's function I posted the data
          success: function(changedata)
            {
              //ajax cannot return a value! We must set the value where we want it!
                if (changedata.month_price==null) 
                {
                  alert ('Παρακαλώ ενημερώστε την καρτέλα του μαθητή με την τρέχουσα τιμή μήνα!');
                }
                else 
                {
                  //the id will be 1 as I call this ajax function only once when the first ever change is being made
                  //Maybe I should call it in every change instead of setting the previous new as currend old...
                  //In that case I should update the id of the prevmonthprice# field
                  $('#prevmonthprice'+id).attr('value', changedata.month_price+'€');
                  $('#prevmonthprice'+id).prop('value', changedata.month_price+'€');
                }
                              
            } //end success
          }); //end AJAX
    }

//delete a specific change
  function actionchange(action, id){
    var sData = 'select%5B'+id+'%5D=1';
    var fieldsets = $('form').find('fieldset');

      if (action == 'del')
      {
        var res = confirm("Πρόκειται να διαγράψετε τη μεταβολή. Σίγουρα Θέλετε να συνεχίσετε;");
        var post_url = "<?php echo base_url();?>student/changes_batch_actions/delete";
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
          <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance">Οικονομικά</a> </li>
          <li class="active">Μεταβολές</li>
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


      <ul class="nav nav-pills"   style="margin:15px 0px;">
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance">Πληρωμές</a></li>
        <li class="active"><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance/changes">Μεταβολές</a></li>        
      </ul>

      <div class="row">
 
        <div class="col-md-12 col-sm-12"> 

          <div class="panel panel-default">
            <div class="panel-heading">
              <span class="icon">
                <i class="icon-retweet"></i>
              </span>
              <h3 class="panel-title">Μεταβολές διδάκτρων</h3>
              <div class="buttons visible-xs" style="padding-top:3px;">
                <input type="checkbox" class="checkall">
              </div>
              <div class="buttons">
                  <!-- <button enabled id="editform1" type="button" class="btn btn-mini" data-toggle="button"><i class="icon-edit"></i></button> -->
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php if(!empty($change)):?>
        <div class="multiplefieldset-header">    
          <div class="row">
            <div class="col-md-1 col-sm-1"><input type="checkbox" class="checkall"></div>
            <div class="col-md-2 col-sm-2">Ημερ/νία</div>
            <div class="col-md-2 col-sm-2">Από ποσό</div>
            <div class="col-md-2 col-sm-2">Σε ποσό</div>
            <div class="col-md-2 col-sm-2">Αιτιολογία</div>
            <div class="col-md-3 col-sm-3">Σημειώσεις</div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 col-sm-12">
            <form action="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance/changes" method="post" accept-charset="utf-8">
              <?php foreach ($change as $data):?>
                <fieldset class="multiplefieldset collapsible"> <!--start of fieldset-->
                    <legend class="paylegend">
                      <div class='legend-text'>
                        Μεταβολή <?php echo implode('-', array_reverse(explode('-', $data['change_dt'])));?>
                      </div>
                      <div class='col-md-1 col-sm-1 legend-selector'>
                        <input  type="checkbox" name="select[<?php echo $data['id'];?>]" value="0">
                      </div>
                    </legend>
                    <div class="clearfix"></div>
                <div class="row"> 
                  
                  <div class="col-md-1 col-sm-1 hidden-xs selector">
                    <input type="checkbox" name="select[<?php echo $data['id'];?>]" value="0"></input>
                  </div>
                  
                  <div class="col-md-2 col-sm-2">
                    <input type="text" class="form-control" placeholder="Ημερομηνία μεταβολής" name="change_dt[<?php echo $data['id'];?>]" value="<?php echo implode('-', array_reverse(explode('-', $data['change_dt'])));?>"></input>
                  </div>

                  <div class="col-md-2 col-sm-2">
                    <input type="text" class="form-control" placeholder="Προηγούμενη τιμή διδάκτρων" name="prev_month_price[<?php echo $data['id'];?>]" value="<?php echo $data['prev_month_price'];?>€"></input>
                  </div>

                  <div class="col-md-2 col-sm-2">
                    <input type="text" class="form-control" placeholder="Νέα τιμή διδάκτρων" name="new_month_price[<?php echo $data['id'];?>]" value="<?php echo $data['new_month_price'];?>€">
                  </div>
               
                  <div class="col-md-2 col-sm-2">
                    <input type="textarea" class="form-control" placeholder="Αιτία μεταβολής" name="reason[<?php echo $data['id'];?>]" value="<?php echo $data['reason'];?>">
                  </div>

                  <div class="col-md-3 col-sm-3">
                    <input type="textarea" rows="1" class="form-control" placeholder="Σημειώσεις" name="notes[<?php echo $data['id'];?>]" value="<?php echo $data['notes'];?>">
                  </div>

                </div>
              </fieldset>
              <?php endforeach;?>

                <div class="row">
                  <div class="col-md-1 col-sm-1 hidden-xs">
                    <span style="margin-left:15px;">
                      <i class="icon-hand-up"></i>
                    </span>
                  </div>
                  <div class="col-md-2 col-sm-3">
                    <label>Με τα επιλεγμένα : </label>
                  </div>
                  <div class="col-md-3 col-sm-3">
                     <select class="form-control"  name="select_action" id="select_action">
                        <option value="none" selected>-</option>
                        <option value="delete">Διαγραφή</option>
                        <!-- <option value="cancel">Ακύρωση</option> -->
                      </select>
                  </div>
              </div>

              <div style="margin-top:30px;">
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-primary" name="add_change" id="add_change">Μεταβολή</button>
                    <button type="button" class="btn btn-primary" name="undo_change" id="undo_change"><span class="icon"><i class="icon-undo"></i></span></button>
                </div>
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
                  <div class="row">
                    <div class="col-md-2 col-sm-2">Ημερ/νία</div>
                    <div class="col-md-2 col-sm-2">Από ποσό</div>
                    <div class="col-md-2 col-sm-2">Σε ποσό</div>
                    <div class="col-md-3 col-sm-3">Αιτιολογία</div>
                    <div class="col-md-3 col-sm-3">Σημειώσεις</div>
                  </div>
                </div>
              <div class="row">
                <div class="col-md-12 col-sm-12">
                  <form action="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance/changes" method="post" accept-charset="utf-8">
                  <fieldset class="multiplefieldset collapsible"> <!--start of fieldset-->
                    <legend class="paylegend"></legend>  
                    <div class="clearfix"></div>   
                         <div class="row"> <!--main form row-->
                            <div class="col-md-2 col-sm-2">
                              <input type="text" id="changedt1" class="form-control" placeholder="Ημερομηνία μεταβολής" name="change_dt[-1]" value="">
                            </div>

                            <div class="col-md-2 col-sm-2">
                              <input type="text" id="prevmonthprice1" placeholder="Προηγούμενη τιμή διδάκτρων" class="form-control" name="prev_month_price[-1]" value=""></input>
                            </div>

                            <div class="col-md-2 col-sm-2">
                              <input type="text" id="nextmonthprice1" placeholder="Νέα τιμή διδάκτρων" class="form-control" name="new_month_price[-1]" value=""></input>
                            </div>

                            <div class="col-md-3 col-sm-3">
                              <input type="textarea" id="reason1" placeholder="Αιτία μεταβολής" class="form-control" name="reason[-1]" value="">
                            </div>

                            <div class="col-md-3 col-sm-3">
                              <input type="textarea" id="notes1" class="form-control" placeholder="Σημειώσεις" name="notes[-1]" value="">
                            </div>

                        </div> <!--end main form row-->

                </fieldset> <!--end of fieldset-->

                <div style="margin-top:30px;" id="actions" class="hidden">
                  <div class="btn-group  pull-right">
                      <button type="button" class="btn btn-primary" name="add_change" id="add_change">Μεταβολή</button>
                      <button type="button" class="btn btn-primary" name="undo_change" id="undo_change"><span class="icon"><i class="icon-undo"></i></span></button>
                  </div>
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
  </div> <!--end of main container-->

<div class="push"></div>
</div> <!-- end of body wrapper-->