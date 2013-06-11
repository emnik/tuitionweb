<script type="text/javascript">

function my_curr_date() {      
    var currentDate = new Date()
    var day = currentDate.getDate();
    var month = currentDate.getMonth() + 1;
    var year = currentDate.getFullYear();
    var my_date = day+"-"+month+"-"+year;
    return my_date;
}


$(document).ready(function() {
  var firstpay = false; //to choose table id
  var newpay = 0; // needed for undo pays
  var newindex = 0;
  var undobtn = document.getElementById('undo_payment');
  undobtn.disabled=true;  
  
  $('#firstpayment').click(function(){
      $('#nopayments').hide();
      $('#startpayments').removeClass("hidden");
      firstpay = true;
      undobtn.disabled=false;         
      addpayrow();
  });
  

  $('#add_payment').click(function(){
    addpayrow();
  });


  function addpayrow(){
  //$('#add_payment').click(function(){
    
    newpay = newpay + 1;
    
    // positive values in the index represent the old records and negative the new ones
    newindex = - newpay;
    
    undobtn.disabled=false; 

    // var table = document.getElementById('paytable'+((firstpay)?'1':''));
    
    // var rowCount = table.rows.length;
    // var row = table.insertRow(rowCount);
    // row.style.border = "inset #DDDBD8 1px ";
    // row.style.borderStyle="dashed";
    var newElem = $('form fieldset:last-child').clone();
    $('form fieldset:last-child').after(newElem);
          

    // var cell1 = row.insertCell(0);
    // var element1 = document.createElement("input");
    // element1.className="span12";
    // element1.type = "text";
    // element1.name="apy_no["+ newindex +"]";
    // element1.id = 'apyno'+newpay;
    // cell1.appendChild(element1);
    // if (newpay>1 && table.rows[rowCount-1].cells[0].children[0].value){
    //     element1.value = parseInt(table.rows[rowCount-1].cells[0].children[0].value) + 1;  
    // }
    // else {
    //     set_latest_apyno(newpay);    
    // };
    

    // var cell2 = row.insertCell(1);
    // var element2 = document.createElement("input");
    // element2.className="span12";
    // element2.type = "text";
    // element2.name = "apy_dt["+ newindex +"]";
    // element2.value = my_curr_date();
    // cell2.appendChild(element2);

    // var cell3 = row.insertCell(2);
    // var element3 = document.createElement("input");
    // element3.className="span12";
    // element3.type = "text";
    // element3.name = "amount["+ newindex +"]";
    // element3.id="apyamount"+newpay;
    // cell3.appendChild(element3);
    // if (newpay>1 && table.rows[rowCount-1].cells[2].children[0].value){
    //     element3.value = parseInt(table.rows[rowCount-1].cells[2].children[0].value);  
    // }
    // else {
    //     set_pay_data(newpay);
    // };


    // var cell4 = row.insertCell(3);
    // var element4 = document.createElement("input");
    // element4.className="span12";
    // element4.type = "text";
    // element4.name = "month_range["+ newindex +"]";
    // element4.id = "apymonthrange"+newpay;
    // if (
    //       (firstpay==false || (firstpay==true && newpay>1)) && table.rows[rowCount-1].cells[3].children[0].value
    //    )
    // {
    //     var prevmomnth = parseInt(table.rows[rowCount-1].cells[3].children[0].value);
    //     if (prevmomnth==12)
    //     {
    //         element4.value = 1;  
    //     }
    //     else
    //     {
    //         element4.value = prevmomnth + 1;
    //     }
    //     //element4.value = parseInt(table.rows[rowCount-1].cells[3].children[0].value) + 1;  
    // };
    // cell4.appendChild(element4);

    // var cell5 = row.insertCell(4);
    // var element5 = document.createElement("input");
    // element5.type = "checkbox";
    // element5.name = "is_credit["+ newindex +"]";
    // element5.value="";
    // cell5.appendChild(element5);

    // var cell6 = row.insertCell(5);
    // var element6 = document.createElement("input");
    // element6.className="span12";
    // element6.type = "textarea";
    // element6.name = "notes["+ newindex +"]";
    // element6.value="";
    // cell6.appendChild(element6);


    // var cell7 = row.insertCell(6);
    // cellcode = ' <div class="btn-group">';
    // //I won't use data-toggle="dropdown" to achieve the disabled effect on the dropdown button
    // //as if the data don't get submitted the action in the dropdown have no meaning.
    // cellcode = cellcode + '<a href="#" class="btn dropdown-toggle" disabled onclick="return false;">Ενέργειες';
    // cellcode = cellcode + '   <span class="caret"></span>';
    // cellcode = cellcode + '</a>';
    // cellcode = cellcode + '<ul class="dropdown-menu">';
    // cellcode = cellcode + '   <li><a href="#"> Ακύρωση</a></li>';
    // cellcode = cellcode + '   <li><a href="#"> Διαγραφή</a></li>';
    // cellcode = cellcode + '</ul>';
    // cellcode = cellcode + '</div>';
    // cell7.innerHTML = cellcode;

//  });
};

  $('#undo_payment').click(function(){
    if (newpay >0) {
      var table = document.getElementById('paytable'+((firstpay)?'1':''));
      
      var rowCount = table.rows.length;
      table.deleteRow(rowCount -1);
      
      newpay = newpay - 1;
      
      if (newpay==0){
        undobtn.setAttribute('disabled','disabled');    
        if (firstpay){
          $('#startpayments').addClass("hidden");
          $('#nopayments').show();
        };

      };

    };

  });

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
              $('#apyno'+id).val(parseInt(apydata.apy_no)+1);
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
                  $('#apyamount'+id).val(paydata.month_price);
                }
                
              
              //id==1 is needed because this ajax function could be called if the amount is empty numerous times
              //We only need to set the value once
              if (firstpay==true && id==1){    
                var fields = paydata.start_lessons_dt.split('-');
                var startmonth = fields[1];
                $('#apymonthrange'+id).val(parseInt(startmonth));
              };
              
            } //end success
          }); //end AJAX
    }

});


  function actionpay(action, id){
    if (action == 'cancel')
    {
        var res = confirm("Πρόκειται να ακυρώσετε την ΑΠΥ. Σίγουρα Θέλετε να συνεχίσετε;");
        if (res==true)
        {
          window.location.href = "<?php echo base_url()?>student/payment_actions/<?php echo $student['id']?>/cancel/" + id;
        };  
    }
    else if (action == 'del')
      {
        var res = confirm("Πρόκειται να διαγράψετε την ΑΠΥ. Σίγουρα Θέλετε να συνεχίσετε;");
        if (res==true)
        {
          window.location.href = "<?php echo base_url()?>student/payment_actions/<?php echo $student['id']?>/del/" + id;
        }; 
      };
  };

</script>

<style type="text/css">


</style>



</head>
<body>
 <div class="wrapper"> <!--body wrapper for css sticky footer-->

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
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
          <li><a href="<?php echo base_url()?>registrations">Μαθητολόγιο</a> <span class="divider">/</span></li>
          <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>">Καρτέλα μαθητή</a> <span class="divider">/</span></li>
          <li class="active">Οικονομικά</li>
        </ul>
      </div>
      
      

      <h3><?php echo $student['surname'].' '.$student['name']?></h3>
      

      <ul class="nav nav-tabs">
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>">Στοιχεία</a></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/contact">Επικοινωνία</a></li>
        <li><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/attendance">Φοίτηση</a></li>
        <li class="active"><a href="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance">Οικονομικά</a></li>
      </ul>

      <div class="row-fluid">
        <div class="span12"> <!--main column-->
          <h4>Πληρωμές διδάκτρων</h4>

            <?php if (!empty($payments)):?>

              <form action="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance" method="post" accept-charset="utf-8">

                <?php foreach ($payments as $data):?>

                  <fieldset> <!--start of fieldset-->
                    <div class="well well-small">                      
                      <div class="row-fluid"> <!--main form row-->
                        <div class="span2">
                          <input type="text" class="span12" name="apy_no[<?php echo $data['id'];?>]" value="<?php echo $data['apy_no'];?>">
                        </div>

                        <div class="span2">
                          <input type="text" class="span12" name="apy_dt[<?php echo $data['id'];?>]" value="<?php echo implode('-', array_reverse(explode('-', $data['apy_dt'])));?>"></input>
                        </div>

                        <div class="span2">
                          <input type="text" class="span12" name="amount[<?php echo $data['id'];?>]" value="<?php echo $data['amount'];?>€"></input>
                        </div>

                        <div class="span1">
                          <input type="text" class="span12" name="month_range[<?php echo $data['id'];?>]" value="<?php echo $data['month_range'];?>">
                        </div>

                        <div class="span1">
                          <!-- <label class="checkbox"> -->
                            <input type="checkbox" name="is_credit[<?php echo $data['id'];?>]" <?php if($data['is_credit']==true) echo "checked='yes'";?> value=<?php echo $data['is_credit'];?>></input>
                         <!-- </label> -->
                        </div>
                        
                        <div class="span2">
                          <input type="textarea" rows="1" class="span12" name="notes[<?php echo $data['id'];?>]" value="<?php echo $data['notes'];?>">
                        </div>

                        <div class="span2">
                            <div class="btn-group pull-right">
                                <a href="#" class="btn dropdown-toggle" data-toggle="dropdown">Ενέργειες
                                  <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a onclick="actionpay('cancel',<?php echo $data['id']?>);" href="#"> Ακύρωση</a></li>
                                    <li><a onclick="actionpay('del',<?php echo $data['id']?>);" href="#"> Διαγραφή</a></li>
                                </ul>
                            </div>
                        </div>

                    </div> <!--end main form row-->
                  </div>                            
                </fieldset> <!--end of fieldset-->
       
                <?php endforeach;?>

              <button type="button" class="btn pull-right" name="add_payment" id="add_payment">Πληρωμή</button>
              <button type="button" class="btn" name="undo_payment" id="undo_payment">Αναίρεση</button>
              <button type="submit" class="btn" name="submit">Αποθήκευση</button>

            </form>

            <?php else:?>
              <div id="nopayments">
                <p>Δεν έχει πραγματοποιηθεί ακόμα καμία πληρωμή!</p>
                <button type="button" class="btn pull-right" id="firstpayment">Πληρωμή</button>
                <br/>
              </div>
              <div id="startpayments" class="hidden">
                  <form action="<?php echo base_url()?>student/card/<?php echo $student['id']?>/finance" method="post" accept-charset="utf-8">
                  <table id="paytable1" class="table table-stripped">
                    <thead>
                        <th class="span1">Αριθμός ΑΠΥ</th>
                        <th class="span2">Ημερομηνία</th>
                        <th class="span2">Ποσό</th>
                        <th class="span2">Μήνας(-ες)</th>
                        <th class="span1">Επί Πιστώσει</th>
                        <th class="span2">Παρατηρήσεις</th>
                        <th class="span2"></th>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                  <button type="button" class="btn pull-right" name="add_payment" id="add_payment">Πληρωμή</button>
                  <button type="button" class="btn" name="undo_payment" id="undo_payment">Αναίρεση</button>
                  <button type="submit" class="btn" name="submit">Αποθήκευση</button>
                </form>
              </div>
            <?php endif;?>
          </div>
        <!-- </div>  end of well class-->

        <!--secondary (right) column-->
<!--         <div class="span2"> 
        <h4>Πληροφορίες</h4>
          <div class="alert alert-block alert-info fade in">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <p>Ο μαθητής χρωστάει ... μήνες.</p>
          </div>
        </div> -->

      </div>

    </div> <!--end of fluid container-->

  </div> <!--end of main container-->

<div class="push"></div>
</div> <!-- end of body wrapper-->