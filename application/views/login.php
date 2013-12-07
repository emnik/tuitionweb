
</head>

<body>
 <div class="wrapper"> <!--body wrapper for css sticky footer-->
  <div class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
          <a class="navbar-brand" href="<?php echo base_url()?>">TuitionWeb</a>
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
     </div>

      <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
          <li><a href="#about">Περί</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Χρήστης<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header">Νικηφορακης Μανος</li>
                <li><a href="#">Αλλαγή κωδικού</a></li>
                <li><a href="#admin">Διαχείριση</a></li>
                <li class="divider"></li>
                <!-- <li class="dropdown-header">Nav header</li> -->
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
    <h1>Φροντιστήριο 'σπουδή'</h1>
    <p class="leap"> Πρόγραμμα διαχείρισης φροντιστηρίου.</p>
  </div>
</div>


<!-- main container
================================================== -->
<div class="container" >

    <div class="row"> <!--main row-->

      <div class="col-md-4"> <!-- form container -->

        <!-- <form class="form" action="<?php echo base_url()?>welcome/" method="post" accept-charset="utf-8"> -->
           <fieldset>
             <legend>Διαχειριστική Περίοδος</legend>
                <div class="control-group">
                  <label  class="control-label">Επιλέξτε σχολικό έτος: </label>
                  <div>  <!-- class="input-group"> -->
                    <select class="form-control input-lg" name="startschoolyear" id="schoolyearcombo">
                      <?php foreach($schoolyears as $data):?>
                       <option value="<?php {$return=explode('-', $data['schoolyear']); echo $return[0];}?>" 
                        <?php if($return[0] == $selected_schstart){ echo "selected = 'selected'";}?> >
                        <?php echo $data['schoolyear'];?>
                      </option>
                      <?php endforeach;?>
                      <option value="addnextschoolyear">Προσθήκη επόμενου σχ. έτους</option>
                    </select>
<!-- 
                    <div class="input-group-btn">
                      <button type="button" class="input-lg btn btn-default dropdown-toggle" data-toggle="dropdown">Ενέργειες <span class="caret"></span></button>
                      <ul class="dropdown-menu pull-right">
                        <li><a href="#">Προσθηκη επόμενου σχολ. έτους</a></li>
                        <li><a href="#">Διαγραφή σχολ. έτους</a></li>
                     </div> -->

                  </div>
                </div>
          </fieldset>

      </div> <!--end of form container-->



      </div> <!-- end of submit buttons -->
    <!-- </form> -->

  </div> <!--end of main row-->




  </div> <!--end of container -->
<div class="push"></div>  
</div>


</div> <!-- end of body wrapper-->