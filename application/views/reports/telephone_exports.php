<script type="text/javascript">
    $(document).ready(function() {

  //Menu current active links and Title
  $('#menu-reports-summary').addClass('active');
  $('#menu-telephones').addClass('active');
  $('#menu-header-title').text('Τηλέφωνα');


    $("button[id='getGoogleData']").click(function() {
        window.location = '<?php echo base_url(); ?>telephones/getGoogleData';
    })

}) //end of (document).ready(function())
</script>

</head>

<body>
    <div class="wrapper">
    <!--body wrapper for css sticky footer-->

    <!-- Menu start -->
    <!-- dirname(__DIR__) gives the path one level up by default -->
    <?php include dirname(__DIR__).'/include/menu.php'; ?> 
    <!-- Menu end -->

        <!-- main container
================================================== -->

        <div class="container" style="margin-bottom:60px;">

            <div>
                <ul class="breadcrumb">
                    <li><a href="<?php echo base_url(); ?>"><i class="icon-home"> </i> Αρχική </a></li>
                    <li class="active"><a href="<?php echo base_url('reports/initial'); ?>">Συγκεντρωτικές Αναφορές</a></li>
                    <li class="active">Τηλέφωνα</li>
                </ul>
            </div>


            <ul class="nav nav-tabs">
                <li><a href="<?php echo base_url('telephones'); ?>">Τηλέφωνα</a></li>
                <li class="active"><a href="<?php echo base_url('telephones/exports'); ?>">Επαφές Google</a></li>
                <!-- <li><a href="<?php echo base_url('mailinglist'); ?>">Λίστα Ηλ. Ταχυδρομείου</a></li> -->
            </ul>

            <p></p>

            <div class="row">      
                <div class="col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <span class="icon">
                                <i class="icon-file-text"></i>
                            </span>
                            <h4 class="panel-title">
                                Επαφές Google
                            </h4>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class='col-xs-12'>
                                    <p>
                                        Δημιουργία CSV αρχείου για εισαγωγή τηλεφώνων στο Google Contacts:
                                    </p>
                                    <div class="alert alert-info alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        Το αρχείο προς εισαγωγή επαφών περιέχει τα κινητά & σταθερά τηλέφωνα <b>όλων</b> των μαθητών (όχι γονέων) και ομαδοποιούνται
                                        σε μια ομάδα με όνομα:<b>
                                            <?php
                                              $s = $this->session->userdata('startsch');
                                            echo 'Μαθητές ('.$s.')';
                                            ?></b>
                                        </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class='col-xs-12'>
                                    <button class="btn btn-primary pull-right" id='getGoogleData' type='button'>Download file</button>
                                </div>

                                <div class="row">
                                    <div class='col-xs-12'>
                                        <p style="margin-left: 15px;">
                                            <span class="label label-success">Οδηγίες:</span>
                                        </p>

                                        <ol>
                                            <li>Κατεβάστε το αρχείο πατώντας στο παραπάνω κουμπί.</li>
                                            <li>Mεταβείτε στις <a href="https://contacts.google.com/">Επαφές Google.</a></li>
                                            <li>Στα αριστερά, κάντε κλικ στην επιλογή <b>Εισαγωγή.</b></li>
                                            <li>Κάντε κλικ στην <b>Επιλογή αρχείου.</b></li>
                                            <li>Επιλέξτε το αρχείο σας.</li>
                                            <li>Κάντε κλικ στην <b>Εισαγωγή.</b></li>
                                        </ol>
                                        <p style="margin-left: 15px;">
                                            * Οι επαφές μπορούν να διαγραφούν όλες μαζί (αν επιθυμείτε) διαγράφοντας την ομάδα από τις επαφές Google!
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <!--end of main container-->

        <div class="push"></div>

    </div> <!-- end of body wrapper-->