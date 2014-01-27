<style>
  .thumbnails {
    border: none;
  }

  .thumbnail {
    border: none;
  }
</style>

<script type="text/javascript">
  var addedyear=false;
  $(document).ready(function(){
    $('select[name=startschoolyear]').change(function(){
        var sel=$(this).find('option:selected').val();
        var prevpos=$(this).find('option:selected').prevAll().size()-1;
        var prevval=$(this).find('option:eq('+ prevpos +")").val();
        if (sel=="addnextschoolyear") {
          if (addedyear==false) {
            addedyear=true;
            var o = new Option((parseInt(prevval,10)+1) + "-" + (parseInt(prevval,10)+2) , parseInt(prevval,10)+1);
            $(this).find('option:selected').before(o);
            $(this).val(parseInt(prevval,10)+1);  
          }
          else {
           alert("Δεν μπορείτε να προσθέσετε περισσότερα του ενός έτη τη φορά.")
           $(this).val(parseInt(prevval,10)); 
          }  
        }
        
    });

  $('.welcome-title').click(function(){
    $(this).find('.icon').toggle();
    $(this).parent().siblings().toggle();
  });

  });
</script>

</head>

<body>
 <div class="wrapper"> <!--body wrapper for css sticky footer-->
  <div class="navbar navbar-inverse navbar-top">
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
                <li class="dropdown-header"><?php echo $user->surname.' '.$user->name;?></li>
                <li><a href="#">Αλλαγή κωδικού</a></li>
                <li><a href="<?php echo base_url()?>welcome/logout">Αποσύνδεση</a></li>
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

        <form class="form" action="<?php echo base_url()?>welcome/" method="post" accept-charset="utf-8">
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
                  </div>
                </div>
          </fieldset>

      </div> <!--end of form container-->

      <div class="col-md-8"> <!-- submit buttons -->

        <div class="row"> <!--first row-->
            <div class="col-xs-12">
              <div class="welcome-title">
                <span class="icon"><i class="icon-minus"></i></span>
                <span class="icon" style="display:none"><i class="icon-plus"></i></span>
                Διαχείριση μαθητών
              </div>
            </div>
            <div class="col-sm-3 col-xs-6 welcome">
                <button type="submit" class="btn-link" name="submit" value="submit1">
                  <i class="icon-group icon-4x"></i>
                  <h4>Μαθητολόγιο</h4>
                </button>
                <div class="small">
                    Στοιχεία /
                    Επικοινωνία /
                    Φοίτηση /
                    Οικονομικά
                </div>
            </div>

            <div class="col-sm-3 col-xs-6 welcome">
              <button type="submit" class="btn-link" name="submit" value="submit6">
                <i class="icon-pencil icon-4x"></i>
                <h4>Διαγωνίσματα</h4>
              </button>
              <div class="small">
                    Προγραμματισμός /
                    Συμμετέχοντες
              </div>
            </div>
            
            <div class="clearfix visible-xs"></div>
            
           
            <div class="col-sm-3 col-xs-6 welcome ">
              <button disabled type="submit" class="btn-link" name="submit" value="submit8">
                <i class="icon-paper-clip icon-4x"></i>
                <h4>Αρχεία</h4>
              </button>
              <div class="small">
                    Διαχείριση αρχείων
              </div>
            </div>

        </div><!--end of first row-->

        <div class="row"><!--second row-->

            <div class="col-xs-12">
              <div class="welcome-title">
                <span class="icon"><i class="icon-plus"></i></span>
                <span class="icon" style="display:none"><i class="icon-minus"></i></span>
                Διαχείριση φροντιστηρίου
              </div>
            </div>

            <div class="col-sm-3 col-xs-6 welcome" style="display: none;">
                <button type="submit" class="btn-link" name="submit" value="submit2">
                  <i class="icon-user icon-4x"></i>
                  <h4>Προσωπικό</h4>
                </button>
                <div class="small">
                    Στοιχεία /
                    Πλάνο διδασκαλίας
                </div>
            </div>
      
            <div class="col-sm-3 col-xs-6 welcome" style="display: none;">
              <button type="submit" class="btn-link" name="submit" value="submit3">
                <i class="icon-sitemap icon-4x"></i>
                <h4>Τμήματα</h4>
              </button>
              <div class="small">
                    Στοιχεία /
                    Πρόγραμμα /
                    Μαθητές
              </div>
            </div>

            <div class="clearfix visible-xs"></div>

            <div class="col-sm-3 col-xs-6 welcome" style="display: none;">
              <button disabled type="submit" class="btn-link" name="submit" value="submit9">
                <i class="icon-eur icon-4x"></i>
                <h4>Ταμείο</h4>
              </button>
              <div class="small">
                    Έξοδα /
                    Μισθοδοσία
              </div>
            </div>

            <div class="col-sm-3 col-xs-6 welcome" style="display: none;">
              <button type="submit" class="btn-link" name="submit" value="submit4">
                <i class="icon-money icon-4x"></i>
                <h4>Οικονομικά</h4>
              </button>
              <div class="small">
                    Σχολικό έτος /
                    Οικονομικό έτος
              </div>
            </div>

        </div> <!--end of second row-->

        <div class="row" style="margin-bottom:20px;"> <!--third row-->
            <div class="col-xs-12">
              <div class="welcome-title">
                <span class="icon"><i class="icon-plus"></i></span>
                <span class="icon" style="display:none"><i class="icon-minus"></i></span>
                Ενημέρωση
              </div>
            </div>
            <div class="col-sm-3 col-xs-6 welcome" style="display: none;">
              <button disabled type="submit" class="btn-link" name="submit" value="submit5">
                <i class="icon-print icon-4x"></i>
                <h4>Αναφορές</h4>
              </button>
            </div>

            <div class="col-sm-3 col-xs-6 welcome" style="display: none;">
              <button disabled type="submit" class="btn-link" name="submit" value="submit7">
                <i class="icon-bookmark-empty icon-4x"></i>
                <h4>Απουσίες</h4>
              </button>
              <div class="small">
                    Ιστορικό απουσιών
              </div>
            </div>



        </div> <!--end of third row-->

      </div> <!-- end of submit buttons -->
    </form>

  </div> <!--end of main row-->




  </div> <!--end of container -->
<div class="push"></div>  
</div>


</div> <!-- end of body wrapper-->