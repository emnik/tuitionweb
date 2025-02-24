<style  type="text/css">
span.select2.select2-container.select2-container--default{
    display: inline;
}
</style>

<!-- https://ckeditor.com/ckeditor-5/builder/ -->
<!-- Free version does not have image / files capabilities. I can have links though to them if I upload the necessary files to the website! -->
<!-- After configuration choose Vanilla JS > Cloud CDN > Copy Code Snipsets  -->
<!-- https://ckeditor.com/docs/ckeditor5/latest/getting-started/setup/getting-and-setting-data.html -->

<script type="importmap">
		{
			"imports": {
				"ckeditor5": "https://cdn.ckeditor.com/ckeditor5/43.1.0/ckeditor5.js",
				"ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/43.1.0/"
			}
		}
</script>

<!-- ATTENTION Need to Add type="module" to the script tag below to work! Otherwise it doesn't work!!! -->
<script type="module" src="<?php echo base_url('assets/js/ckeditor.js'); ?>"></script>

<script type="module">
    // Added exports to the ckeditor.js to be able to initialize the editor here
    // I need this to access the editor instance so that I have access to the editor.getData() function
    import { ClassicEditor, editorConfig } from '<?php echo base_url('assets/js/ckeditor.js'); ?>';
    
    let editorInstance; // Declare the editor instance variable

    window.onload = () => {
        ClassicEditor.create(document.querySelector('#editor'), editorConfig).then(editor => {
            editorInstance = editor; // Store the editor instance for later use
        });
    }

    // Make submitForm accessible by attaching it to the window object so that I can use it outside the <script type="module">!!!
    window.submitForm = function(queryString) {
        // var queryString = prepareData();
        // console.log(queryString);
        if (queryString!==false){
            // Make the AJAX request
            $.ajax({
            type: "POST",
            url: "Mailinglist/setupmail",
            data: queryString,
            dataType: 'json',
            success: function(response) {
                // Handle the successful response
                // console.log(response);
                var rdata=response;
                console.log(rdata);
                if (rdata.status=='success'){
                    show_msg(rdata.message, 'success');
                } else {
                    show_msg('<b>Please send this error message to the administrator!</b><br>'+rdata.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error('Error: ' + error);
                    console.log('Response Text:', xhr.responseText); // Log the response text
                }
            });
        }
    }

// get the data, check them and prepare them for posting or cancel post and show an error!
    function prepareData(){
        let submit=true;
        let err_message='';

        // Check if there are receipients!
        if ($('#customaddress').val()===null && $('#selectClass').val()===null) {
            submit = false;
            err_message += "Η λίστα παραληπτών (τάξεις ή/και κοινοποίηση) δεν μπορεί να είναι κενή!" + "<br>";
        };


        // Serialize the form
        var formData = $('#MailinglistOptions').serializeArray();
        
        // Remove 'content' from formData as we get the ckeditor's data via getData().
        // content is used when submiting directly which I didn't want here to happen
        // as I need to handle data before submitting
        formData = formData.filter(field => field.name !== 'content');
        
        if (editorInstance) {
            const contentData = editorInstance.getData(); // Get the content of the editor
            if (contentData!==''){
            // Add editor data
            formData.push({ name: 'editorData', value: contentData });
            } else {
                err_message += "Το περιεχόμενο του email δεν μπορεί να είναι κενό!<br>";
                submit = false;
            }
        }
        
        if ($('#subject').val()===''){
            err_message += "Το θέμα του μηνύματος δεν μπορεί να είναι κενό! <br>";
            submit = false;
        }

        let errorTags = validateTags();
        // console.log(errorTags);
        if (errorTags===true){
            // Get selected values from Select2 customaddress as an array. Otherwise POST will get only the last value!
            const customAddresses = $('#customaddress').val(); // This will return an array of selected values
            if (customAddresses) {
                customAddresses.forEach(function(address) {
                    formData.push({ name: 'customaddress[]', value: address }); // Append each address to formData
                });
            }
        } else {
            submit = false;
            err_message += "Μη έγκυροι παραλήπτες: " + errorTags.join(", ") + "<br>";
        }

        // Convert the filtered formData back to a query string
        const queryString = $.param(formData);

        // console.log(queryString);
        if (submit) {
            return queryString;
        } else {
            err_message += "<b>Η αποστολή ακυρώθηκε!</b>";
            show_msg(err_message, 'error');
            return false;
        }
    }

    // Function to validate email
    function isValidEmail(email) {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Simple email regex
        return emailPattern.test(email);
    }

    // Function to validate tags before posting
    function validateTags() {
        const tags = $('#customaddress').val(); // Get selected tags
        let err = false;
        const invalidEmails = [];

        if (tags && tags.length > 0) {
            tags.forEach(tag => {
                if (!isValidEmail(tag)) {
                    invalidEmails.push(tag);
                }
            });

            if (invalidEmails.length > 0) {
                // show_error("<b>Μη έγκυροι παραλήπτες</b>: " + invalidEmails.join(", ") + "<br><b></b>");
                err = true; // Return true if there are invalid emails
            }
        }

        if(err) {
            return invalidEmails;
        }
        else {
            return true; // Return true if all emails are valid
        }
        
    }

    function show_msg(message, status){
        $('#result').removeClass("hidden alert-success alert-danger");
        if (status === 'error'){
            $('#result').addClass("alert-danger");
        } else {
            $('#result').addClass("alert-success");
        }
        $('#response-container').html(message);
    }

    $("button[id='cancelMail'], button[id='closeModal']").click(function() {
            $('#sendConfirm').modal('hide');
        });

    var mailData = '';
    $("button[id='submitbtn']").click(function() {
        mailData = prepareData();
        if (mailData!==false){
            $('#sendConfirm').modal();
        }
    });

    $("button[id='sendMail']").click(function() {
        $('#sendConfirm').modal('hide');
        window.submitForm(mailData);
        reset();
    });

    function reset() {
        editorInstance.setData('');
        $('#subject').val('');
        $('#customaddress').val(null).trigger('change');
        $('#result').addClass("hidden");
        $('#selectClass').val(null).trigger('change');
    }

</script>

<script type="text/javascript">

    $(document).ready(function() {
        //Menu current active links and Title
        $('#menu-operation').addClass('active');
        $('#menu-communication').addClass('active');
        $('#menu-header-title').text('Επικοινωνία');

        $('#selectClass').select2({
            // width: 'resolve',
            closeOnSelect: false,
            readonly: true,
            placeholder: "Επιλογή τάξεων",
        })

        $('#customaddress').select2({
            readonly: true,
            multiple: true,
            tags: true, // Enable tag creation
            placeholder: "Προσθέστε email",
            tokenSeparators: [' '], // Allow tag separation by spaces
        });


        $("button[id='getMailinglistData']").click(function() {
            // Get the selected values from the selectClass element
            var selectedClasses = $('#selectClass').val(); // This will return an array of selected values

            // Construct the URL with the selected classes as part of the path
            var baseUrl = '<?php echo base_url(); ?>mailinglist/getMailinglistData';
            var url = baseUrl + '/' + encodeURIComponent(selectedClasses.join(','));

            // Redirect to the constructed URL
            window.location = url;
        });

        $('#signature-label').popover({
            content: 'Τα στοιχεία για την υπογραφή ανακτώνται αυτόματα απο την καρτέλα <a href="<?php echo base_url('school'); ?>" target="_blank">Στοιχεία Φροντιστηρίου</a>.<br>Μπορείτε να επεξεργαστείτε την υποσημείωση από τις <a href="mailinglist/settings">Ρυθμίσεις</a>.',
            html: true,
            placement: 'right'
        });


        $('.close').click(function() {
            $('#result').addClass("hidden"); // Hide the alert instead of removing it
        });



    }) //end of (document).ready(function())
</script>



</head>

<body>

    <div class="modal fade" id="sendConfirm" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-target=".bs-example-modal-sm">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" id="closeModal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Επιβεβαίωση Αποστολής</h4>
                </div>
                <div class="modal-body">
                    <p>Είστε σίγουροι ότι θέλετε να στείλετε το email;</p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="cancelMail" class="btn btn-danger" data-dismiss="modal">Ακύρωση</button>
                    <button type="button" id="sendMail" class="btn btn-primary">Αποστολή</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


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
                    <!-- <li class="active"><a href="<?php echo base_url('reports/initial'); ?>">Συγκεντρωτικές Αναφορές</a></li> -->
                    <li class="active">Επικοινωνία</li>
                </ul>
            </div>


            <ul class="nav nav-tabs">
                <li><a href="<?php echo base_url('communication'); ?>">Ομαδικά SMS</a></li>
                <li class="active"><a href="<?php echo base_url('mailinglist'); ?>">Λίστα Ηλ. Ταχυδρομείου</a></li>
            </ul>

            <p></p>

            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <span class="icon">
                                <i class="icon-envelope-alt"></i>
                            </span>
                            <h4 class="panel-title">
                                Λίστα Ηλ. Ταχυδρομείου
                            </h4>
                            <div class="buttons">
                                <button enabled id="configbtn" type="button" class="btn btn-default btn-sm pull-right" data-toggle="button" onclick="window.location = '<?php echo base_url('mailinglist/settings'); ?>'"><i class="icon-cogs"></i> Ρυθμίσεις </button>
                            </div>
                        </div>
                        <div class="panel-body">
                                <div class="row">
                                    <div class='col-xs-12'>
                                        <?php if ('error' === $config) { ?>
                                            <div class="alert alert-danger" role="alert">
                                                Η αποστολή email πραγματοποιείται μέσω εφόσον έχει ρυθμιστεί το <a href="contact_config" target="_blank">αντίστοιχο υποσύστημα</a>!
                                                Σε διαφορετική περίπτωση μπορείτε να δημιουργήσετε τη λίστα διευθύνσεων επιλέγοντας τις επιθυμητές τάξεις και να κατεβάσετε το αντίστοιχο αρχείο 
                                                (σε μορφή CSV) πατώντας Download file για εισαγωγή της λίστας ηλ. ταχυδρομείου σε εξωτερική εφαρμογή και αποστολή των email από εκεί.
                                            </div>
                                        <?php }?>
                                        <?php if ('success' === $config & empty($sender)) { ?>
                                            <div class="alert alert-danger" role="alert">
                                                Το email αποστολέα δεν έχει καθοριστεί! Παρακαλώ εισάγετέ το στην καρτέλα <a href="mailinglist/settings">Ρυθμίσεις</a> για να είναι δυνατή η αποστολή email!
                                                Σε διαφορετική περίπτωση <b>μπορείτε να κατεβάσετε το αντίστοιχο αρχείο (σε μορφή CSV) πατώντας Download file</b> για εισαγωγή της λίστας ηλ. ταχυδρομείου
                                                σε εξωτερική εφαρμογή και αποστολή των email από εκεί.                                          
                                            </div>
                                        <?php }?>
                                        <?php if ('success' === $config & !empty($sender)) { ?>
                                            <p>
                                                Αποστολή email με επιλογή τάξεων μέσω Microsoft Web Services. Aποστολέας: <a class="label label-success" href="mailinglist/settings"><?php echo $sender; ?></a>
                                            </p>
                                        <?php }?>
                                    </div>
                                </div>
                                <form id='MailinglistOptions' method="post" accept-charset="utf-8">
                                <div class="row">
                                    <div class="col-xs-12" style="margin-top:10px;">
                                        <label>Επιλογή Τάξεων:</label>
                                    </div>
                                    <div class="col-xs-12">
                                        <select class="form-control" id='selectClass' name="classes[]" multiple="multiple" >
                                            <?php if (!empty($classes)) { ?>
                                                <?php foreach ($classes as $class) { ?>
                                                    <option value="<?php echo $class['id']; ?>"><?php echo $class['text']; ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                    </select>
                                    </div>
                                    <div class='col-xs-12' style="margin-top:10px;">
                                        <button class="pull-right btn btn-primary" id='getMailinglistData' type='button'>Download file</button>
                                    </div>
                                </div>
                                <?php if (!empty($sender) & 'success' == $config) { ?>
                                <div class="row">
                                    <div class="col-xs-12 form-group" >
                                        <label>Κοινοποίηση σε:</label>
                                        <select id="customaddress" class="select2 form-control" multiple="multiple"></select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 form-group" >
                                        <label>Θέμα:</label>
                                        <input class="form-control" type="text" name="subject" id="subject" placeholder="Γράψτε το θέμα εδώ!"></input>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class='col-xs-12  form-group' >    
                                        <label>Μήνυμα:</label>
                                        <textarea name="content" id="editor">
                                        </textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class='col-xs-12  form-group' >    
                                        <label>Προεπισκόπιση Υπογραφής: <i  class="icon-question-sign" id="signature-label"></i></label>
                                        <?php echo $signature; ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class='col-xs-12'  style="margin-top:10px;">
                                        <button class="btn btn-primary pull-right" id='submitbtn' type='button'>Αποστολή</button>
                                    </div>
                                    <div class='col-xs-12'  style="margin-top:10px;">
                                        <div id="result" class="alert alert-success alert-dismissible hidden" role="alert">
                                            <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <div id="response-container"></div>
                                        </div>                                    
                                    </div>
                                 </div>
                            </form>
                            <?php } // sender address check?> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end of main container-->

        <div class="push"></div>

    </div> <!-- end of body wrapper-->