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
        $('#menu-schooldetails').addClass('active');
        $('#menu-header-title').text('Στοιχεία Φροντιστηρίου');

        //we must enable all form fields to submit the form with no errors!
        $("body").on('click', '#submitbtn', function() {
            $('.panel').find(':input:disabled').removeAttr('disabled');
            $('form').submit();
        });


        $("body").on('click', '#editform1, #editform2, #editform3', function() {
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
            $('#schoolid').attr('disabled', 'disabled');
        });

        $('#cancelbtn').click(function() {
            window.open("<?php echo base_url('school') ?>", '_self', false);
        });

        //if it is a new school the fields should be enabled
        <?php if (empty($school['id'])) : ?>
            $('#editform1').addClass('active');
            $('#editform2').addClass('active');
            $('#editform3').addClass('active');
            var toggle = document.getElementById("mainform");
            toggle.disabled = false;
            $('#mainform :input').removeAttr('disabled');
            $('#submitbtn').removeAttr('disabled');
            $('#cancelbtn').removeAttr('disabled');
            $('#schoolid').attr('disabled', 'disabled');
        <?php endif; ?>
        
    });
</script>


</head>

<body>
    <div class="wrapper">
        <!--body wrapper for css sticky footer-->

        <!-- Menu start -->
        <?php include(__DIR__ . '/include/menu.php'); ?>
        <!-- Menu end -->


        <!-- main container
================================================== -->

        <div class="container" style="margin-bottom:60px;">

            <div>
                <ul class="breadcrumb">
                    <li><a href="<?php echo base_url() ?>"><i class="icon-home"> </i> Αρχική </a></li>
                    <li class="active">Στοιχεία Φροντιστηρίου</li>
                </ul>
            </div>

            <p>
                <h3>
                    <?php
                    if (!empty($school['id'])) {
                        echo $school['distinctive_title'];
                    } else {
                        echo "Νέα εγγραφή";
                    }; ?>
                </h3>
            </p>

            <div class="visible-xs visible-sm">
                <div class="row">
                    <div class="col-md-12">
                        <div class="btn-group pull-left">
                            <a class="btn btn-default btn-sm" href="#group1">Στοιχεία Φροντιστηρίου</a>
                            <a class="btn btn-default btn-sm" href="#group2">Φορολογικά Στοιχεία</a>
                            <a class="btn btn-default btn-sm" href="#group3">Διαδύκτιο</a>
                        </div>
                    </div>
                </div>
            </div>

            <p></p>
            <form id='mainform' action="<?php echo base_url('school') ?>" method="post" accept-charset="utf-8">


                <div class="row">
                    <!-- first row -->
                    <div class="col-md-6">
                        <!-- first row left side -->
                        <div class="panel panel-default" id="group1">
                            <div class="panel-heading">
                                <span class="icon">
                                    <i class="icon-building"></i>
                                </span>
                                <h3 class="panel-title">Στοιχεία Φροντιστηρίου</h3>
                                <div class="buttons">
                                    <button enabled id="editform1" type="button" class="btn btn-default btn-sm pull-right" data-toggle="button"><i class="icon-edit"></i></button>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 form-group">
                                        <label>Κωδ. Φροντιστηρίου(id)</label>
                                        <input disabled class="form-control" type="text" id="schoolid" placeholder="Αυτόματα" name="id" value="<?php echo $school['id']; ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <label>Επωνυμία</label>
                                            <input disabled class="form-control" id="name" type="text" placeholder="" name="name" value="<?php echo $school['name']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <label>Διακριτικός Τίτλος</label>
                                            <input disabled class="form-control" id="distinctive_title" type="text" placeholder="" name="distinctive_title" value="<?php echo $school['distinctive_title']; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <label>Διεύθυνση</label>
                                            <input disabled class="form-control" id="address" type="text" placeholder="" name="address" value="<?php echo $school['address']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <label>Πόλη</label>
                                            <input disabled class="form-control" type="text" id="city" placeholder="" name="city" value="<?php echo $school['city']; ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <label>Σταθ. Τηλέφωνο</label>
                                            <input disabled class="form-control" type="text" id="phone" placeholder="" name="phone" value="<?php echo $school['phone']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <label>Κινητό</label>
                                            <input disabled class="form-control" type="text" id="mobile" placeholder="" name="mobile" value="<?php echo $school['mobile']; ?>">
                                        </div>
                                    </div>
                                </div>


                            </div> <!-- end of panel-body -->
                        </div> <!-- end of panel -->
                    </div> <!-- end of left side -->


                    <div class="col-md-6">
                        <!-- first row right side -->
                        <div class="row">
                            <!-- right side embeded first row -->
                            <div class="col-md-12">
                                <div class="panel panel-default" id="group2">
                                    <div class="panel-heading">
                                        <span class="icon">
                                            <i class="icon-eur"></i>
                                        </span>
                                        <h3 class="panel-title">Φορολογικά Στοιχεία</h3>
                                        <div class="buttons">
                                            <button enabled id="editform2" type="button" class="btn btn-default btn-sm pull-right" data-toggle="button"><i class="icon-edit"></i></button>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6">
                                                <div class="form-group">
                                                    <label>Δ.Ο.Υ.</label>
                                                    <input disabled class="form-control" type="text" id="taxoffice" placeholder="" name="taxoffice" value="<?php echo $school['taxoffice']; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <div class="form-group">
                                                    <label>Α.Φ.Μ.</label>
                                                    <input disabled class="form-control" type="text" id="vatnumber" placeholder="" name="vatnumber" value="<?php echo $school['vatnumber']; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end of panel-body -->
                                </div> <!-- end of panel -->
                            </div>
                        </div> <!-- end of right side embeded first row -->

                        <div class="row">
                            <!-- right side embeded second row -->
                            <div class="col-md-12">
                                <div class="panel panel-default" id="group3">
                                    <div class="panel-heading">
                                        <span class="icon">
                                            <i class="icon-globe"></i>
                                        </span>
                                        <h3 class="panel-title">Διαδύκτιο / Social media</h3>
                                        <div class="buttons">
                                            <button enabled id="editform3" type="button" class="btn btn-default btn-sm pull-right" data-toggle="button"><i class="icon-edit"></i></button>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6">
                                                <div class="form-group">
                                                    <label>Διεύθυνση Ιστοσελίδας</label>
                                                    <div>
                                                        <input disabled type="text" class="form-control" placeholder="" id="siteurl" name="siteurl" value="<?php echo $school['siteurl']; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <div class="form-group">
                                                    <label>Διευθυνση email</label>
                                                    <input disabled type="text" class="form-control" placeholder="" id="email" name="email" value="<?php echo $school['email'] ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6">
                                                <div class="form-group">
                                                    <label>facebook</label>
                                                    <input disabled type="text" class="form-control" placeholder="" id="facebookurl" name="facebookurl" value="<?php echo $school['facebookurl']; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <div class="form-group">
                                                    <label>twitter</label>
                                                    <input disabled type="text" class="form-control" placeholder="" id="twitterurl" name="twitterurl" value="<?php echo $school['twitterurl']; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end  of panel-body -->
                                </div> <!-- end of panel -->
                            </div>
                        </div> <!-- end of right side embeded second row -->
                    </div>
                </div>
                <div class="btn-group">
                    <button disabled id="submitbtn" type="submit" class="btn btn-primary">Αποθήκευση</button>
                    <button disabled id="cancelbtn" type="button" class="btn btn-default">Ακύρωση</button>
                </div>

            </form>

            <div class="push"></div>

        </div>
        <!--end of body wrapper-->