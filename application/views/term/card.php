<link href="<?php echo base_url('assets/bootstrap-datepicker-1.3.0/css/datepicker3.css') ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/bootstrap-datepicker-1.3.0/js/bootstrap-datepicker.js') ?>"></script>
<script src="<?php echo base_url('assets/bootstrap-datepicker-1.3.0/js/locales/bootstrap-datepicker.el.js') ?>" charset="UTF-8"></script>

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
        // $('#menu-termdetails').addClass('active');
        $('#menu-header-title').text('Διαχειριστική Περίοδος');

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
            $('#termid').attr('disabled', 'disabled');
        });

        $('#cancelbtn').click(function() {
            window.open("<?php echo base_url('term/cancel/card/' . $termdata['id']) ?>", '_self', false);
        });

        //if it is a new term the fields should be enabled
        <?php if (empty($termdata['name'])) : ?>
            $('#editform1').addClass('active');
            $('#editform2').addClass('active');
            $('#editform3').addClass('active');
            var toggle = document.getElementById("mainform");
            toggle.disabled = false;
            $('#mainform :input').removeAttr('disabled');
            $('#submitbtn').removeAttr('disabled');
            $('#cancelbtn').removeAttr('disabled');
            $('#termid').attr('disabled', 'disabled');
        <?php endif; ?>

        var date = new Date();
        $('.datecontainer input')
            .datepicker({
                format: "dd-mm-yyyy",
                language: "el",
                autoclose: true,
                todayHighlight: true,
                defaultDate: new Date(date)
            })
            .on('focus click tap vclick', function(event) {
                //stop keyboard events and focus on the datepicker widget to get the date.
                //this is most usefull in android where the android's keyboard was getting in the way...
                event.stopImmediatePropagation();
                event.preventDefault();
                $(this).blur();
            });
    });
</script>


</head>

<body>
    <div class="wrapper">
        <!--body wrapper for css sticky footer-->

        <!-- Menu start -->
        <?php include(dirname(__DIR__) . '/include/menu.php'); ?>
        <!-- Menu end -->


        <!-- main container
================================================== -->

        <div class="container" style="margin-bottom:60px;">

            <div>
                <ul class="breadcrumb">
                    <li><a href="<?php echo base_url() ?>"><i class="icon-home"> </i> Αρχική </a></li>
                    <li class="active"><a href="<?php echo base_url('term') ?>">Διαχειριστικές Περίοδοι</a></li>
                    <li class="active">Καρτέλα Διαχ. Περιόδου</li>
                </ul>
            </div>

            <p>
                <h4>
                    <?php
                    if (!empty($termdata['name'])) {
                        echo "Επεξεργασία διαχειριστικής περιόδου";
                    } else {
                        echo "Νέα διαχειριστική περίοδος";
                    }; ?>
                </h4>
            </p>


            <p></p>
            <form id='mainform' action="<?php echo base_url('term/card/' . $termdata['id']) ?>" method="post" accept-charset="utf-8">


                <div class="row">
                    <!-- first row -->
                    <div class="col-md-12">
                        <!-- first row left side -->
                        <div class="panel panel-default" id="group1">
                            <div class="panel-heading">
                                <span class="icon">
                                    <i class="icon-cogs"></i>
                                </span>
                                <h3 class="panel-title">Διαχειριστική Περίοδος</h3>
                                <div class="buttons">
                                    <button enabled id="editform1" type="button" class="btn btn-default btn-sm pull-right" data-toggle="button"><i class="icon-edit"></i></button>
                                </div>
                            </div>
                            <div class="panel-body">
                                <!-- <div class="row">
                                    <div class="col-md-3 col-sm-6 form-group">
                                        <label>Κωδ. Περιόδου(id)</label>
                                        <input disabled class="form-control" type="text" id="termid" placeholder="Αυτόματα" name="id" value="<?php echo $termdata['id']; ?>">
                                    </div>
                                </div> -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Όνομα</label>
                                            <input disabled class="form-control" id="termname" type="text" placeholder="" name="name" value="<?php echo $termdata['name']; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group datecontainer">
                                            <label>Έναρξη Περιόδου</label>
                                            <input disabled class="form-control" id="termstart" type="text" placeholder="" name="start" value="<?php echo implode('-', array_reverse(explode('-', $termdata['start']))); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group datecontainer">
                                            <label>Λήξη Περιόδου</label>
                                            <input disabled class="form-control" id="termend" type="text" placeholder="" name="end" value="<?php echo implode('-', array_reverse(explode('-', $termdata['end']))); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end  of panel-body -->
                        </div> <!-- end of panel -->
                    </div>
                </div> <!-- end of right side embeded second row -->
            </form>
            <div class="btn-group">
                <button disabled id="submitbtn" type="submit" class="btn btn-primary">Αποθήκευση</button>
                <button disabled id="cancelbtn" type="button" class="btn btn-default">Ακύρωση</button>
            </div>
        </div>
    </div>




    <div class="push"></div>

    </div>
    <!--end of body wrapper-->