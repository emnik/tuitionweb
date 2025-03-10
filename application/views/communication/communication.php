<script type="text/javascript">
    $(document).ready(function() {

  //Menu current active links and Title
  $('#menu-operation').addClass('active');
  $('#menu-communication').addClass('active');
  $('#menu-header-title').text('Επικοινωνία');

  let data = <?php echo $classes; ?>;
//   console.log('Dropdown Data:', data); // Check if this logs properly
//   console.log($('#selectClass').length);
        $('#selectClass').select2({
            // data: <?php
            // echo $classes;
            ?>,
            data: data,
            multiple: true,
            closeOnSelect: false,
            readonly: true,
            // placeholder: "Επιλογή τάξεων",
        })

        $('#selectClass').on('change', function () {
            // let selectedValues = $(this).val();  // Get selected values as an array
            // console.log('Selected Values:', selectedValues);
            checkGenerateCSVBulkSMS();
        });

        function checkGenerateCSVBulkSMS() {
            if ($('#selectClass').val()!==null){
                $('#getBulkSMSData').prop('disabled', false);
            } else {
                $('#getBulkSMSData').prop('disabled', true);
            }
        }
        
        // Set initial state
        checkGenerateCSVBulkSMS();        

        $("button[id='prepareSMS']").click(function() {
            if ($('#selectClass').val()!==null || $('#addPhones').val()!==''){
                prepareSMS();
            } else {
                console.log('No classes selected or manual numbers added!');
                alert("Δεν έχετε επιλέξει κανέναν παραλήπτη!");
            }
        })


        function reset(){
            $('#smsText').val(null);
            $('#charCount').text('0');
            $('#smsCount').text('0');
            $('#smsCost').text('0.000€');
            $('#smsList').val(null);
            $('#addPhones').val(null);
            $('#selectClass').val(null).trigger('change');
            list_id = '';
            checkFields();
            // console.log('Reset!');
            // balance does not get updated immediatly after sending SMS but
            // when the SMSs are actually sent be SMS.to!!! So
            // there is no need to update the balance here.
            // get_balance(); 
        }

        $("button[id='sendSMS']").click(function() {
            sendSMS();
        })

        $("button[id='cancelSMS'], button[id='closeModal']").click(function() {
            cancelSMS();
        })

        $("button[id='getBulkSMSData']").click(function() {
            if ($('#selectClass').val()!==null){
                console.log($('#selectClass').val());    
                generateFile();
            } else {
                console.log('No classes selected');
                alert("Δεν έχετε επιλέξει τάξεις.");
            }
        })
        
    // Function to get the estimate
    let estimate = 0;

    function getEstimate(callback) {
        $.ajax({
            url: "<?php echo base_url();?>communication/getEstimate",
            method: "GET",
            timeout: 0,
            success: function(response) {
                var estimate_json = JSON.parse(response);
                estimate = parseFloat(estimate_json.estimated_cost);
                console.log("Estimated cost:", estimate);
                if (callback) callback();
            },
            error: function(xhr, status, error) {
                console.error("Error fetching estimate:", error);
            }
        });
    }    

    // Function to get the balance
    function get_balance() {
        $.ajax({
            url: "<?php echo base_url();?>communication/getBalance",
            method: "GET",
            timeout: 0,
            success: function(response) {
                var balanceData = JSON.parse(response);
                console.log("Balance:", balanceData.balance);
                $('#balance').text(balanceData.balance);
                $('#modalSMSBalance').text(balanceData.balance);
                if (estimate > 0) {
                    $('#sms_balance').text(Math.floor(balanceData.balance / estimate));
                } else {
                    console.error("Estimate is not available");
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching balance:", error);
            }
        });
    }

    // Fetch estimate and then balance
    getEstimate(get_balance);

    document.getElementById('smsText').addEventListener('input', function() {
        var text = this.value;
        var charCount = text.length;
        var smsCount = Math.ceil(charCount / 160);
        var cost = smsCount * estimate;
        
        document.getElementById('charCount').innerText = charCount;
        document.getElementById('smsCount').innerText = smsCount;
        document.getElementById('smsCost').innerText = cost.toFixed(3) + "€";
    });


    $('#addPhonesInfo').popover({
            content: 'Μπορείτε να προσθέσετε επιπλέον τηλέφωνα από εδώ! Πρέπει να έχουν κωδικό χώρας (+30) και να διαχωρίζονται με κόμμα.',
            title: 'Προσθήκη μεμονομένων αριθμών',
            html: false,
            trigger: 'hover',
            placement: 'right'
        });

    $('#balanceInfo').popover({
            // content: '<span style="color:black;">Προσθέστε χρήματα μεταβαίνοντας στο <a href="https://sms.to/app#/add-funds" target="_blank">SMS.to</a> (καρτέλα Αdd Funds).</span><div style="margin-top:10px;"><button class="btn btn-xs btn-primary" id="refresh_balance">Ανανέωση</button></div>',
            content: '<span style="color:black;">Προσθέστε χρήματα μεταβαίνοντας στο <a href="https://sms.to/app#/add-funds" target="_blank">SMS.to</a> (καρτέλα Αdd Funds).</span>',
            title: '<span style="color:black;">Προσθήκη χρημάτων</span>',
            html: true,
            trigger: 'click',
            placement: 'bottom'
        });        

     // Use event delegation to attach the event listener to the button inside the popover
    $(document).on('click', '#refresh_balance', function() {
        get_balance();
        $('#balanceInfo').popover('hide');
    });

    function checkFields() {
        const smsList = $('#smsList').val().trim();
        const smsText = $('#smsText').val().trim();

        if (smsList !== '' && smsText !== '') {
            $('#prepareSMS').prop('disabled', false);
        } else {
            $('#prepareSMS').prop('disabled', true);
        }
    }

    // Attach input event listeners to the fields
    $('#smsList, #smsText').on('input', checkFields);

    // Set initial state
    checkFields();


    let list_id = '';

    function sendSMS(){
        if(list_id === ''){
            console.error("List ID is empty!");
            return;
        } else {
            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>communication/sendSMS",
                data: {
                    'message' : $('#smsText').val(),
                    'list_id' : list_id
                    },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $('#smsReview').modal('hide');
                    if (data.success === true){
                        $('#result span').replaceWith('<span>Η αποστολή μηνυμάτων ήταν επιτυχής!</span>');
                    } else {
                        $('#result span').replaceWith('<span>Η αποστολή μηνυμάτων δεν ήταν επιτυχής.</span>');
                        $('#result').removeClass('alert-info alert-success').addClass('alert-danger');
                    }
                    $('#result').show();
                    reset();
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                    console.error("Status:", status);
                    console.error("Response Text:", xhr.responseText);
                }
            });
        }
    }

    function cancelSMS(){
        if(list_id === ''){
            console.error("List ID is empty!");
            return;
        } else {
            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>communication/cancelSMS",
                data: {
                    'list_id' : list_id
                    },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                    console.error("Status:", status);
                    console.error("Response Text:", xhr.responseText);
                }
            });
        }
    }


    function prepareSMS() {
        const selectedClasses =$('#selectClass').val();
        const options = $('form#SMSoptions').serializeArray();
        
        let smstext = $('#smsText').val();
        console.log(smstext);
        $('#modalSMSText').text(smstext);

        // Show busy cursor and loading message
        $('body').css('cursor', 'wait');
        $('#modalLoadingMessage span').replaceWith('<span><strong>Προετοιμασία αποστολής.</strong> Παρακαλώ περιμένετε...</span>');
        $('#modalLoadingMessage').removeClass('alert-info alert-danger').addClass('alert-info');
        $('#smsReview').modal();
        $('#modalLoadingMessage').show();

        $.ajax({
            type: "POST",
            url: "<?php echo base_url(); ?>communication/prepareSMS",
            data: {
                'options' : $('form#SMSoptions').serializeArray(),
                'classes' : selectedClasses,
                'smsformdata' : $('form#prepareSMSForm').serializeArray(),
                },
            dataType: 'json',
            success: function(data) {
                // Hide busy cursor and loading message
                $('body').css('cursor', 'default');

                if(data.missingPhones.length !== 0){
                    $('#phoneErrors span').replaceWith('<span><strong>Προσοχή!</strong> Δε βρέθηκαν ('+data.missingPhones.length +') τηλέφωνα: '+data.missingPhones.join(', ')+'</span>');
                    $('#phoneErrors').show();
                }
                if (data.errors && Array.isArray(data.errors) && data.errors.length !== 0) {
                    let errorMessages = data.errors.map(err => {
                        let errorText = Array.isArray(err.error) ? err.error.join(', ') : err.error;
                        return `<li>${errorText}: ${err.name}</li>`;
                    }).join('');
                    $('#modalLoadingMessage span').replaceWith(`<span><strong>Προσοχή!</strong> ${data.message} <ul>${errorMessages}</ul></span>`);
                    $('#modalLoadingMessage').removeClass('alert-info').addClass('alert-danger');
                } else {
                    $('#modalLoadingMessage span').replaceWith(`<span>${data.message}</span>`);
                    $('#modalLoadingMessage').removeClass('alert-info').addClass('alert-success');
                    console.log(data.estimateCost);
                }
                if (data.estimateCost!==null){
                        $('#modalSMSRecipients').text(data.estimateCost.contact_count);
                        $('#modalSMSTotal').text(data.estimateCost.contact_count*data.estimateCost.sms_count);
                        $('#modalSMSCost').text((data.estimateCost.estimated_cost).toFixed(3));
                    }
                if (data.list_id!==null){
                    list_id = data.list_id;
                }
            },
            error: function(xhr, status, error) {
                // Hide busy cursor and loading message
                $('body').css('cursor', 'default');
                console.error("Error fetching data:", error);
                console.error("Status:", status);
                console.error("Response Text:", xhr.responseText);
            }
        });
    }    

}) //end of (document).ready(function())

function generateFile() {
        const selectedClasses =$('#selectClass').val();
        const options = $('form#SMSoptions').serializeArray();
        $.ajax({
            type: "POST",
            url: "<?php echo base_url(); ?>communication/getBulkSMSData",
            data: {
                'options' : $('form#SMSoptions').serializeArray(),
                'classes' : selectedClasses,
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
    <div class="modal fade" id="smsReview" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" id="closeModal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Επιβεβαίωση Αποστολής</h4>
                </div>
                <div class="modal-body">
                    <p class="modalSMStextHeader">Θα αποσταλεί το ακόλουθο μήνυμα:</p>
                    <p class="modalSMStext" id="modalSMSText">το κείμενο του μηνύματος</p>
                    <div class="alert alert-info alert-dismissible" id='modalLoadingMessage' style='display:none;' role="alert">
                        <a class="close" onclick="$('#modalLoadingMessage').hide()">×</a>   
                        <span></span>  
                    </div> 
                    <div class="smsInfo" id="modalSMSInfo">
                       <p>Παραλήπτες: <span id="modalSMSRecipients">0</span></p>
                       <p>Σύνολο SMS: <span id="modalSMSTotal">0</span></p>
                       <p>Εκτιμώμενο κόστος: <span id="modalSMSCost">0.000</span>€ / <span id="modalSMSBalance"></span>€</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="cancelSMS" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="button" id="sendSMS" class="btn btn-primary">Send SMS</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    <div class="wrapper">
        <!--body wrapper for css sticky footer-->

    <!-- Menu start -->
    <!-- dirname(__DIR__) gives the path one level up by default -->
    <?php include dirname(__DIR__).'/include/menu.php'; ?> 
    <!-- <?php include(__DIR__ .'/include/menu.php');?> -->
    <!-- Menu end -->

        <!-- main container
================================================== -->

        <div class="container" style="margin-bottom:60px;">

            <div>
                <ul class="breadcrumb">
                    <li><a href="<?php echo base_url(); ?>"><i class="icon-home"> </i> Αρχική </a></li>
                    <!-- <li class="active"><a href="<?php echo base_url('reports/initial'); ?>">Συγκεντρωτικές Αναφορές</a></li> -->
                    <li class="active">Επικοινωνία</li>
                </ul>
            </div>


            <ul class="nav nav-tabs">
                <!-- <li><a href="<?php echo base_url('telephones'); ?>">Τηλέφωνα</a></li> -->
                <li class="active"><a href="<?php echo base_url('communication'); ?>">Ομαδικά SMS</a></li>
                <li><a href="<?php echo base_url('mailinglist'); ?>">Λίστα Ηλ. Ταχυδρομείου</a></li>
            </ul>

            <p></p>

            <div class="row">
                <div class='<?php if ($config === 'success') {echo 'col-lg-6';} ?> col-xs-12'>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <span class="icon">
                                <i class="icon-file-text"></i>
                            </span>
                            <h4 class="panel-title">
                                Καθορισμός αποδεκτών bulk SMS
                            </h4>
                        </div>

                        <div class="panel-body">
                            <form id='SMSoptions'>
                                <div class="row">
                                    <div class='col-xs-12'>
                                        <p>
                                            Δημιουργία λίστας αποδεκτών για υπηρεσία αποστολής ομαδικών μηνυμάτων:
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
                                        <select class="form-control select2" id='selectClass' name="classes[]" multiple="multiple"></select>
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
                                        <button class="btn btn-primary pull-right" id='getBulkSMSData' type='button'>Download CSV file</button>
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
<?php if($config ==='success'):?>
                <div class="col-lg-6 col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <span class="icon">
                                <i class="icon-file-text"></i>
                            </span>
                            <h4 class="panel-title">
                                Αποστολή SMS
                                <span class="push-right"><i class="icon-info-sign" style="padding-right:10px;" id="balanceInfo"></i>Υπόλοιπο: <span id="balance">0</span>€ (<span id="sms_balance">0</span> SMS) </span>
                            </h4>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class='col-xs-12'>
                                    <p>
                                        Συμπληρώστε πρώτα στο πλαίσιο <b>Καθορισμός αποδεκτών bulk SMS</b>, τις τάξεις και το κοινό που θέλετε να επικοινωνήσετε.
                                    </p>
                                </div>
                            </div>
                            <form id='prepareSMSForm'>
                            <label for="smsList">Όνομα Αποστολής</label>
                            <input type="text" class="form-control" name="list" id="smsList">
                            <div class="row">
                                <div class="col-xs-12" style="margin-top:10px;">
                                    <label>Προσθήκη μεμονομένων αριθμών: <i class="icon-question-sign" id="addPhonesInfo"></i> </label>
                                    <input type="text" class="form-control" name="addPhones" id="addPhones">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12" style="margin-top:10px;">
                                    <label for="smstext">Κείμενο μηνύματος</label>
                                    <textarea class="form-control" rows="5" name="text" id="smsText"></textarea>
                                    <div class="smsInfo" id="smsInfo">
                                        <p>Χαρακτήρες: <span id="charCount">0</span></p>
                                        <p>Αριθμός SMS / παραλήπτη: <span id="smsCount">0</span></p>
                                        <p>Κόστος / παραλήπτη: <span id="smsCost">0.000€</span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12" style="margin-top:15px;">
                                    <div class="alert alert-success alert-dismissible" id='result' style='display:none;' role="alert">
                                        <a class="close" onclick="$('#result').hide()">×</a>   
                                        <span></span>  
                                    </div> 
                                </div>
                            </div>
                            </form>
                            <div class="row">
                                <div class="col-xs-12" style="margin-top:10px;">
                                    <button class="btn btn-primary pull-right" id='prepareSMS' type='button'>Send SMS</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<?php endif;?>                
            </div>     
            <!-- <div class="row">      
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
            </div> -->


        </div>
        <!--end of main container-->

        <div class="push"></div>

    </div> <!-- end of body wrapper-->