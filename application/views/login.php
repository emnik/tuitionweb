
</head>

<body>
 <div class="wrapper"> <!--body wrapper for css sticky footer-->
 <div class="navbar navbar-inverse navbar-fixed-top" role="navigation" id="slide-nav">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-toggle">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="navbar-brand" href="#">TuitionWeb</a>
        </div>
        <div id="slidemenu">
            <ul class="nav navbar-nav">
                <li><a href="<?php echo base_url() ?>">Αρχική</a></li>
                <li><a href="#about">Περί</a></li>
                <li><a href="#contact">Επικοινωνία</a></li>
            </ul>
        </div>
    </div>
</div>



<!-- Subhead
================================================== -->
<div class="jumbotron subhead">
  <div class="container">
    <h1>Φροντιστήριο ΣΠΟΥΔΗ</h1>
    <p class="leap"> Πρόγραμμα διαχείρισης φροντιστηρίου.</p>
  </div>
</div>


<!-- main container
================================================== -->
<div class="container" >

    <div class="row"> <!--main row-->

        <div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3">
          <div class="panel panel-default">
            <div class="panel-heading">
              <span class="icon">
                <i class="icon-unlock-alt"></i>
              </span>
              <h3 class="panel-title">Συνδεση χρήστη</h3>
<!--               <div class="buttons">
              </div> -->
            </div>

            <div class="panel-body">
              <form action="<?php echo base_url('login')?>" method="post" accept-charset="utf-8">
                <div class="form-group col-sm-12">
                  <!-- <label>Όνομα χρήστη</label> -->
                  <input type="text" class="form-control input-lg" placeholder="Όνομα χρήστη" name="username"></input>
                 </div>
                <div class="form-group col-sm-12">
                  <!-- <label>Κωδικός χρήστη</label> -->
                  <input type="password" class="form-control input-lg" placeholder="Κωδικός χρήστη" name="password"></input>
                </div>
                <div class="form-group col-sm-12">
                    <button type="submit" class="btn btn-success">Είσοδος</button>
                </div>
              </form>
            </div>
          </div>
        </div>



      </div>

      </div>


<div class="push"></div>  
</div>


</div> <!-- end of body wrapper-->