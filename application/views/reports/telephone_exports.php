
<script type="text/javascript">
    $(document).ready(function() {

  //Menu current active links and Title
  $('#menu-reports-summary').addClass('active');
  $('#menu-telephones').addClass('active');
  $('#menu-header-title').text('Επικοινωνία');

        $('#selectClass').select2({
            data: <?php echo $classes; ?>,
            multiple: true,
            closeOnSelect: false,
            readonly: true,
            // placeholder: "Επιλογή τάξεων",
        })


        $("button[id='getGoogleData']").click(function() {
            window.location = '<?php echo base_url() ?>telephones/getGoogleData';
        })

        $("button[id='getBulkSMSData']").click(function() {
            // window.location ='<?php echo base_url() ?>telephones/getBulkSMSData';
            if ($('#selectClass').val()!=""){
                generateFile();
            }
            
        })


    }) //end of (document).ready(function())

    function generateFile() {
        $.ajax({
            type: "POST",
            url: "<?php echo base_url()?>telephones/getBulkSMSData",
            data: {
                'options' : $('form#SMSoptions').serializeArray(),
                'classes' : $('#selectClass').val(),
                },
            dataType: 'json',
            success: function(data) {
                  console.log(data.missingPhones);
                  if(data.missingPhones.length !== 0){
                    $('#phoneErrors span').replaceWith('<span><strong>Προσοχή!</strong> Δε βρέθηκαν ('+data.missingPhones.length +') τηλέφωνα: '+data.missingPhones.join(', ')+'</span>');
                    // $('#phoneErrors').fadeIn();
                    $('#phoneErrors').show();
                  }
                   /*
                   * Make CSV downloadable
                   */
                  var downloadLink = document.createElement("a");
                  var fileData = ['\ufeff'+data.csv];

                  var blobObject = new Blob(fileData,{
                     type: "text/csv;charset=utf-8;"
                   });

                  var url = URL.createObjectURL(blobObject);
                  downloadLink.href = url;
                  downloadLink.download = "bulkSMS_export.csv";

                  /*
                   * Actually download CSV
                   */
                  document.body.appendChild(downloadLink);
                  downloadLink.click();
                  document.body.removeChild(downloadLink);
            }
        });
    }
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

        <div class="container" style="margin-bottom:60px;">

            <div>
                <ul class="breadcrumb">
                    <li><a href="<?php echo base_url() ?>"><i class="icon-home"> </i> Αρχική </a></li>
                    <li class="active"><a href="<?php echo base_url('reports/initial') ?>">Συγκεντρωτικές Αναφορές</a></li>
                    <li class="active">Επικοινωνία</li>
                </ul>
            </div>


            <ul class="nav nav-tabs">
                <li><a href="<?php echo base_url('telephones') ?>">Τηλέφωνα</a></li>
                <li class="active"><a href="<?php echo base_url('telephones/exports') ?>">Ομαδικά SMS / Επαφές Google</a></li>
                <li><a href="<?php echo base_url('mailinglist')?>">Λίστα Ηλ. Ταχυδρομείου</a></li>
            </ul>

            <p></p>

            <div class="row">
            <div class='col-lg-6 col-xs-12'>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <span class="icon">
                                <i class="icon-file-text"></i>
                            </span>
                            <h4 class="panel-title">
                                Ομαδικά SMS
                            </h4>
                        </div>

                        <div class="panel-body">
                            <form id='SMSoptions'>
                                <div class="row">
                                    <div class='col-xs-12'>
                                        <p>
                                            Δημιουργία CSV αρχείου για υπηρεσία αποστολής ομαδικών μηνυμάτων:
                                        </p>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
                                            <label class="form-check-label" for="exampleRadios1">
                                                Προς Γονείς
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option2">
                                            <label class="form-check-label" for="exampleRadios2">
                                                Προς Μαθητές
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios3" value="option3">
                                            <label class="form-check-label" for="exampleRadios3">
                                                Προς Γονείς και Μαθητές
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12" style="margin-top:10px;">
                                        <label>Επιλογή Τάξεων:</label>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="form-control select2" id='selectClass' name="classes[]" multiple="multiple"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class='col-xs-12' style="margin-top:20px;">
                                        <div class="form-check">
                                            <input class="form-check-input" name="includeHeaders" type="checkbox" value="" id="defaultCheck1">
                                            <label class="form-check-label" for="defaultCheck1">
                                                Η πρώτη γραμμή να περιέχει τις ετικέτες των στηλών.
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" name="convertToLatin" type="checkbox" value="" id="defaultCheck2">
                                            <label class="form-check-label" for="defaultCheck2">
                                                Να μετατραπούν τα ελληνικά ονόματα σε λατινικούς χαρακτήρες
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" name="setPhonePriorities" type="checkbox" value="" checked id="defaultCheck3">
                                            <label class="form-check-label" for="defaultCheck3">
                                                Αν δεν υπάρχει τηλέφωνο μαθητή να χρησιμοποιηθεί του γονέα
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-xs-12" style="margin-top:10px;">
                                        <button class="btn btn-primary pull-right" id='getBulkSMSData' type='button'>Download file</button>
                                    </div>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col-xs-12" style="margin-top:15px;">
                                <div class="alert alert-danger alert-dismissible" id='phoneErrors' style='display:none;' role="alert">
                                        <a class="close" onclick="$('#phoneErrors').hide()">×</a>   
                                        <span></span>  
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>            
                <div class="col-lg-6 col-xs-12">
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
                                            echo 'Μαθητές (' . $s . ')';
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