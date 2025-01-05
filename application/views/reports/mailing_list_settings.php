<script type="text/javascript">

function toggleedit(togglecontrol, id) {
        if ($(togglecontrol).hasClass('active')) {
            $('#' + id).closest('.panel').find(':input').each(function() {
                $(this).attr('disabled', 'disabled');
            });
        } else {
            $('#' + id).closest('.panel').find(':input').removeAttr('disabled');
        };
        $(togglecontrol).removeAttr('disabled');
    }

$(document).ready(function() {
  //Menu current active links and Title
  $('#menu-reports-summary').addClass('active');
  $('#menu-telephones').addClass('active');
  $('#menu-header-title').text('Επικοινωνία');

  $("body").on('click', '#editform1', function() {
            toggleedit(this, this.id);

            var all = $('.panel-body').find(':input').length;
            var disabled = $('.panel-body').find(':input:disabled').length;

            if (all == disabled) {
                $('#submitbtn').attr('disabled', 'disabled');
                $('#cancelbtn').attr('disabled', 'disabled');
            } else {
                $('#submitbtn').removeAttr('disabled');
                $('#cancelbtn').removeAttr('disabled');
            }
        });

        $('#cancelbtn').click(function() {
            window.open("<?php echo base_url(
                "mailinglist/settings"
            ); ?>", '_self', false);
        });

        $("body").on('click', '#submitbtn', function() {
            $('.panel').find(':input:disabled').removeAttr('disabled');
            $('form').submit();
        })
  
  }) //end of (document).ready(function())
</script>



</head>

<body>
    <div class="wrapper">
     <!--body wrapper for css sticky footer-->

    <!-- Menu start -->
    <!-- dirname(__DIR__) gives the path one level up by default -->
    <?php include dirname(__DIR__) . "/include/menu.php"; ?> 
    <!-- Menu end -->

        <!-- main container
================================================== -->

        <div class="container" style="margin-bottom:60px;">

            <div>
                <ul class="breadcrumb">
                    <li><a href="<?php echo base_url(); ?>"><i class="icon-home"> </i> Αρχική </a></li>
                    <li class="active"><a href="<?php echo base_url(
                        "reports/initial"
                    ); ?>">Συγκεντρωτικές Αναφορές</a></li>
                    <li class="active">Επικοινωνία</li>
                </ul>
            </div>


            <ul class="nav nav-tabs">
                <li><a href="<?php echo base_url(
                    "telephones"
                ); ?>">Τηλέφωνα</a></li>
                <li><a href="<?php echo base_url(
                    "telephones/exports"
                ); ?>">Ομαδικά SMS / Επαφές Google</a></li>
                <li class="active"><a href="<?php echo base_url(
                    "mailinglist"
                ); ?>">Λίστα Ηλ. Ταχυδρομείου</a></li>
            </ul>

            <button enabled id="backbtn" style="margin-top:10px; margin-bottom:10px;" type="button" class="btn btn-default" data-toggle="button" onclick="window.location = '<?php echo base_url(
                "mailinglist"
            ); ?>'"> < Επιστροφή </button>
            
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <span class="icon">
                                <i class="icon-envelope-alt"></i>
                            </span>
                            <h4 class="panel-title">
                                Ρυθμίσεις αποστολής ηλ. ταχυδρομείου
                            </h4>
                            <div class="buttons">
                              <button enabled id="editform1" type="button" class="btn btn-default btn-sm pull-right" data-toggle="button"><i class="icon-edit"></i></button>
                            </div>
                        </div>
                        <div class="panel-body">
                        <form id='mainform' action="<?php echo base_url(
                            "mailinglist/settings"
                        ); ?>" method="post" accept-charset="utf-8">
                                <div class="row">
                                    <div class="col-sm-6 col-xs-12 form-group" >
                                        <label>Διεύθυνση Αποστολέα:</label>
                                        <input disabled id="senderaddress" name="senderaddress" class="form-control" value="<?php echo !empty(
                                            $mailsettings["senderaddress"]
                                        )
                                            ? $mailsettings["senderaddress"]
                                            : ""; ?>"></input>
                                    </div>
                                    <div class="col-sm-6 col-xs-12 form-group" >
                                        <label>Διεύθυνση Απάντησης:</label>
                                        <input disabled id="replytoaddress" name="replytoaddress" class="form-control" value="<?php echo !empty(
                                            $mailsettings["replytoaddress"]
                                        )
                                            ? $mailsettings["replytoaddress"]
                                            : ""; ?>"></input>
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- <div class="col-xs-12">

                                    </div> -->
                                    <div class="col-xs-12 form-group">
                                        <label>Κείμενο υποσημείωσης (html):</label>
                                        <div class="alert alert-info" role="alert">
                                            <span class="icon"><i class="icon-info-sign"></i></span>
                                             Η υποσημείωση μπαίνει <b>προαιρετικά</b> (με μικρή γραμματοσειρά) μετά την υπογραφή στο τέλος του μηνύματος.
                                             Μπορείτε να αφήσετε το πεδίο αυτό κενό.
                                        </div>
                                        <textarea disabled class="form-control" name="note" id="note" rows="3" cols="1" placeholder=""><?php echo !empty(
                                            $mailsettings["note"]
                                        )
                                            ? $mailsettings["note"]
                                            : ""; ?></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class='btn-group pull-right'>
                        <button class="btn btn-default" id='cancelbtn' type='button'>Ακύρωση</button>
                        <button class="btn btn-primary" id='submitbtn' type='submit'>Αποθήκευση</button>
                    </div>
                </div>
            </div>
        </div>
        <!--end of main container-->

        <div class="push"></div>

    </div> <!-- end of body wrapper-->