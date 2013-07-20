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
            <li class="active pull-right"><a href="<?php echo base_url()?>">Αρχική</a></li>
              <li><a href="#about">Περί</a></li>
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
    <h1>tuition manager</h1>
    <p class="leap"> Πρόγραμμα διαχείρισης φροντιστηρίου.</p>
  </div>
</div>


<!-- main container
================================================== -->
<div class="container"  style="margin-top:40px; margin-bottom:60px;">
  <div class="container-fluid">

    <div class="row-fluid"> <!--main row-->

      <div class="span4"> <!-- form container -->

        <form class="form" action="<?php echo base_url()?>welcome/" method="post" accept-charset="utf-8">
           <fieldset>
             <legend>Διαχειριστική Περίοδος</legend>
                <div class="control-group">
                  <label  class="control-label">Επιλέξτε σχολικό έτος: </label>
                  <div class="controls">
                    <select class="span6" name="startschoolyear">
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
<!--             <div class="control-group">
              <div class="controls">
                <button type="submit" class="btn">Είσοδος</button>
              </div>
            </div>
        </form>  -->
      
      </div> <!--end of form container-->

      <div class="span8">

        <div class="row-fluid"> <!--first row-->
            <div class="span4 welcome">
                <button type="submit" class="btn-link" name="submit" value="submit1">
                  <i class="icon-group icon-4x"></i>
                  <h4>Μαθητολόγιο</h4>
                </button>
            </div>

            <div class="span4 welcome">
                <button type="submit" class="btn-link" name="submit" value="submit2">
                  <i class="icon-user icon-4x"></i>
                  <h4>Προσωπικό</h4>
                </button>
            </div>
      
            <div class="span4 welcome">
              <button type="submit" class="btn-link" name="submit" value="submit3">
                <i class="icon-sitemap icon-4x"></i>
                <h4>Τμήματα</h4>
              </button>
            </div>

        </div> <!--end of first row-->

        <div class="row-fluid" > <!--second row-->
            <div class="span4 welcome">
              <button type="submit" class="btn-link" name="submit" value="submit4">
                <i class="icon-eur icon-4x"></i>
                <h4>Οικονομικά</h4>
              </button>
            </div>
            
            <div class="span4 welcome">
              <button type="submit" class="btn-link" name="submit" value="submit5">
                <i class="icon-edit icon-4x"></i>
                <h4>Αναφορές</h4>
              </button>
            </div>

            <div class="span4 welcome">
              <button type="submit" class="btn-link" name="submit" value="submit6">
                <i class="icon-cogs icon-4x"></i>
                <h4>Διαχείριση</h4>
              </button>
            </div>

        </div> <!--end of second row-->

      </div>
</form>


    </div> <!--end of main row-->

  </div> <!--end of container fluid -->
</div>

<div class="push"></div>
</div> <!-- end of body wrapper-->