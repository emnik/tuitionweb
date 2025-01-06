<style>
    .thumbnails {
        border: none;
    }

    .thumbnail {
        border: none;
    }
</style>

<script type="text/javascript">
  $(document).ready(function(){
    //Menu current active links and Title
    $('#menu-reports-summary').addClass('active');
    // $('#menu-history').addClass('active');
    $('#menu-header-title').text('Αναφορές');  
  });
</script>

</head>

<body>
    <div class="wrapper">
        <!--body wrapper for css sticky footer-->
    <!-- Menu start -->
    <!-- dirname(__DIR__) gives the path one level up by default -->
    <?php include(dirname(__DIR__).'/include/menu.php');?> 
    <!-- Menu end -->

        <!-- main container
================================================== -->
        <div class="container">

            <div>
                <ul class="breadcrumb">
                    <li><a href="<?php echo base_url() ?>"><i class="icon-home"> </i> Αρχική </a></li>
                    <li class="active">Συγκεντρωτικές Αναφορές</li>
                </ul>
            </div>


            <form class="form" action="<?php echo base_url('reports/initial') ?>" method="post" accept-charset="utf-8">
                <div class="row" style="margin-bottom:20px;">
                    <div class="col-xs-12">
                        <div class="welcome-title">
                            Συγκεντρωτικές αναφορές
                        </div>
                    </div>

                    <div class="col-sm-3 col-xs-6 welcome">
                        <button type="submit" class="btn-link" name="submit" value="submit9">
                            <i class="icon-bookmark-empty icon-4x"></i>
                            <h4>Αναφορές</h4>
                        </button>
                        <div class="small">
                            Αρ.μαθητών ανά τάξη / 
                            ανα μάθημα / 
                            Διδάσκοντες ανά μαθητή / 
                            Λίστα μαθητών
                        </div>
                    </div>


                    <div class="col-sm-3 col-xs-6 welcome">
                        <button type="submit" class="btn-link" name="submit" value="submit8">
                            <i class="icon-time icon-4x"></i>
                            <h4>Ιστορικό</h4>
                        </button>
                        <div class="small">
                            Απουσιών /
                            ΑΠΥ /
                            Ηλ.Ταχυδρομείου
                        </div>
                    </div>

                    <div class="clearfix visible-xs"></div>

                    <div class="col-sm-3 col-xs-6 welcome">
                        <button type="submit" class="btn-link" name="submit" value="submit7">
                            <i class="icon-comments-alt icon-4x"></i>
                            <h4>Επικοινωνία</h4>
                        </button>
                        <div class="small">
                        Τηλεφωνικοί Κατάλογοι
                        Μαθητών /
                        Προσωπικού /
                        Ομαδικά SMS / Επαφές Google, Λίστα Ηλ. Ταχυδρομείου
                        </div>
                    </div>

                    <div class="col-sm-3 col-xs-6 welcome">
                        <button type="submit" class="btn-link" name="submit" value="submit4">
                            <i class="icon-money icon-4x"></i>
                            <h4>Οικονομικά</h4>
                        </button>
                        <div class="small">
                            Σχολικό έτος /
                            Οικονομικό έτος
                        </div>
                    </div>

                </div> <!-- end of submit buttons -->
            </form>

        </div>
        <!--end of main row-->




    </div>
    <!--end of container -->
    <div class="push"></div>




</body> <!-- end of body wrapper-->