<script type="text/javascript">

//$(document).ready(function(){
//});

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
                    <input type="text" class="span12" name="reason[<?php echo $data['id'];?>]" value="<?php echo $data['reason'];?>">
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
                  <button type="button" class="btn btn-primary pull-right" id="firstchange">Μεταβολή</button>
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
                              <input type="text" id="reason1" class="span12" name="reason[-1]" value="">
                            </div>

                            <div class="span2">
                              <input type="text" id="notes1" class="span12" name="notes[-1]" value="">
                            </div>

                            <div class="span2">
                              <div class="btn-group pull-right">
                                <a class="btn delbtn" onclick="return false;" href="#" disabled><i class="icon-remove-circle"></i></a>
                              </div>
                            </div>

                        </div> <!--end main form row-->

                </fieldset> <!--end of fieldset-->

                <div id="actions" class="form-actions hidden">
                  <button type="button" class="btn btn-primary pull-right" name="add_payment" id="add_payment">Πληρωμή</button>
                  <button type="button" class="btn" name="undo_payment" id="undo_payment">Αναίρεση</button>
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