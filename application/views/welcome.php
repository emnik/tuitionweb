<style>
  .thumbnails {
    border: none;
  }

  .thumbnail {
    border: none;
  }
</style>

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

      <div class="col-sm-4"> <!-- form container -->

        <form class="form" action="<?php echo base_url()?>welcome/" method="post" accept-charset="utf-8">
           <fieldset>
             <legend>Διαχειριστική Περίοδος</legend>
                <div class="control-group">
                  <label  class="control-label">Επιλέξτε σχολικό έτος: </label>
                  <div class="controls">
                    <select class="form-control input-lg" name="startschoolyear">
                      <?php foreach($schoolyears as $data):?>
                       <option value="<?php {$return=explode('-', $data['schoolyear']); echo $return[0];}?>" 
                        <?php if($return[0] == $selected_schstart){ echo "selected = 'selected'";}?> >
                        <?php echo $data['schoolyear'];?>
                      </option>
                      <?php endforeach;?>
                    </select>
                  </div>
                </div>
          </fieldset>

      </div> <!--end of form container-->

      <div class="col-sm-8"> <!-- submit buttons -->

        <div class="row"> <!--first row-->
            <div class="col-sm-4 welcome">
                <button type="submit" class="btn-link" name="submit" value="submit1">
                  <i class="icon-group icon-4x"></i>
                  <h4>Μαθητολόγιο</h4>
                </button>
            </div>

            <div class="col-sm-4 welcome">
                <button type="submit" class="btn-link" name="submit" value="submit2">
                  <i class="icon-user icon-4x"></i>
                  <h4>Προσωπικό</h4>
                </button>
            </div>
      
            <div class="col-sm-4 welcome">
              <button type="submit" class="btn-link" name="submit" value="submit3">
                <i class="icon-sitemap icon-4x"></i>
                <h4>Τμήματα</h4>
              </button>
            </div>

        </div> <!--end of first row-->

        <div class="row" > <!--second row-->
            <div class="col-sm-4 welcome">
              <button type="submit" class="btn-link" name="submit" value="submit4">
                <i class="icon-eur icon-4x"></i>
                <h4>Οικονομικά</h4>
              </button>
            </div>
            
            <div class="col-sm-4 welcome">
              <button type="submit" class="btn-link" name="submit" value="submit5">
                <i class="icon-edit icon-4x"></i>
                <h4>Αναφορές</h4>
              </button>
            </div>

            <div class="col-sm-4 welcome">
              <button type="submit" class="btn-link" name="submit" value="submit6">
                <i class="icon-cogs icon-4x"></i>
                <h4>Διαχείριση</h4>
              </button>
            </div>

        </div> <!--end of second row-->

      </div> <!-- end of submit buttons -->
    </form>

  </div> <!--end of main row-->




  </div> <!--end of container -->
</div>

<div class="push"></div>
</div> <!-- end of body wrapper-->