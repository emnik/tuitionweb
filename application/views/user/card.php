<link href="<?php echo base_url('assets/bootstrap-datepicker-1.3.0/css/datepicker3.css') ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/bootstrap-datepicker-1.3.0/js/bootstrap-datepicker.js') ?>"></script>
<script src="<?php echo base_url('assets/bootstrap-datepicker-1.3.0/js/locales/bootstrap-datepicker.el.js') ?>" charset="UTF-8"></script>

<script type="text/javascript">
    function toggleedit(togglecontrol, id) {

        if ($(togglecontrol).hasClass('active')) {
            $('#' + id).closest('.panel').find(':input').each(function() {
                $(this).attr('disabled', 'disabled');
            });
            if(id=="editform1"){
                if ($('#usraccess').is(':visible')) {
                    $('#usraccess').prop("disabled", true);;
                }
            }
        } else {
            $('#' + id).closest('.panel').find(':input').removeAttr('disabled');
            if(id=="editform1"){
                if ($('#usraccess').is(':visible')) {
                    $('#usraccess').prop("disabled", false);
                }
            }
        };

        $(togglecontrol).removeAttr('disabled');
    }

    $(document).ready(function() {
        //Menu current active links and Title
        $('#menu-users').addClass('active');
        $('#menu-header-title').text('Λογαριασμός Χρήστη');
        
        <?php if (empty($userdata['group_id'])):?>
                $('#usraccess').parent().hide();
        <?php else:?> 
            <?php if($groupdata[$userdata['group_id']]=='admin'):?>
                $('#usraccess').parent().hide();
            <?php endif;?>
        <?php endif;?>
        
        //we must enable all form fields to submit the form with no errors!
        $("body").on('click', '#submitbtn', function() {
            $('.panel').find(':input:disabled').removeAttr('disabled');
            $('#usraccess').prop("disabled", false);
            
            if($('#usrpassword').val() === $('#usrpassword_check').val()) {
                $('#usrpassword').parent().removeClass('has-error');
                $('#usrpassword_check').parent().removeClass('has-error');
            }
            else {
                console.log('passwords do not match')
                $('#usrpassword').parent().addClass('has-error');
                $('#usrpassword_check').parent().addClass('has-error');
            }

            <?php if (empty($userdata['password'])):?>
            //Αν δεν υπάρχει αποθηκευμένος κωδικός σημαίνει ότι πρόκειται για νέο χρήστη. Στη περίπτωση αυτή
            //τα 2 πεδία για το νέο κωδικό και την επαλήθευση δε μπορεί να είναι κενά
            if ($('#usrpassword').val()==='' || $('#usrpassword_check').val()==='') {
                console.log('password cannot be empty!');
                $('#usrpassword').parent().addClass('has-error');
                $('#usrpassword_check').parent().addClass('has-error');
            }
            <?php else:?>
            //the following check is in case someone submits without changing nothing in the form!
            if ($('#usrcurpassword').val()==''){
                $('#usrcurpassword').parent().addClass('has-error');
            }
            <?php endif;?>
            
            //the following check is in case someone submits without changing nothing in the form!
            if ($('#usrgroupid').val()=='NULL'){
                console.log('no group selected');
                $('#usrgroupid').parent().addClass('has-error');
            }
            
            //the following check is in case someone submits without changing nothing in the form!
            if ($('#usrsurname').val()==''){
                console.log('no surname');
                $('#usrsurname').parent().addClass('has-error');
            }

            //the following check is in case someone submits without changing nothing in the form!
            if ($('#usrname').val()==''){
                console.log('no name');
                $('#usrname').parent().addClass('has-error');
            }
            

            var check = ['#usrname', '#usrsurname', '#usrgroupid', '#usrpassword', '#usrpassword_check', '#usrcurpassword'];
            var noerror = true;
            for (i=0; i<check.length; i++){
                if ($(check[i]).parent().hasClass('has-error')){
                    noerror = false;
                }
            }

            if (noerror){
                $('form').submit();
            }
            
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
        });

        $("#usrusername").focusout(function(){
            var reserved = <?php echo json_encode($reserved);?>;
            if (reserved.indexOf($(this).val())>=0 && $(this).val()!="<?php echo $userdata['username'];?>"){
                $(this).parent().addClass('has-error');
            }
            else {
                $(this).parent().removeClass('has-error');
            }
        })
        
        $('#usrsurname, #usrname, #usrgroupid, #usrcurpassword').focusout(function(){
            if ($(this).val()=='' || $(this).val()=='NULL'){
                $(this).parent().addClass('has-error');
            } 
        })

        $('#usrsurname, #usrname, #usrgroupid, #usrcurpassword').focus(function(){
            $(this).parent().removeClass('has-error');
        });

        $('#cancelbtn').click(function() {
            window.open("<?php echo base_url('user/cancel/card/' . $userdata['id']) ?>", '_self', false);
        });

        //if it is a new user the fields should be enabled
        <?php if (empty($userdata['name'])) : ?>
            $('#editform1').addClass('active');
            $('#editform2').addClass('active');
            $('#editform3').addClass('active');
            var toggle = document.getElementById("mainform");
            toggle.disabled = false;
            $('#mainform :input').removeAttr('disabled');
            $('#submitbtn').removeAttr('disabled');
            $('#cancelbtn').removeAttr('disabled');
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

            var select2dynamic = {
                disabled: false,
                minimumInputLength: 2,
                ajax: {
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: params.term // Sends the typed letters to the controller
                        };
                    },
                    processResults: function (data) {
                        return { results: data }; // Data needs to be in the format: [{id: "", text: ""}, ...]
                    }
                }
            };

        $('#usrgroupid').on('change', function(){
            // if ($('#usraccess').is(':visible')) {
            //     $('#usraccess').select2('destroy');
            // }
            switch ($(this).find('option:selected').text()) {
                case 'tutor':
                    $('#usraccess').parent().show();
                    select2dynamic.multiple = false;
                    select2dynamic.ajax.url = "<?php echo base_url('user/tutors_list')?>";
                    //TODO: implement user/tutors_list!
                    $('#usraccess').select2(select2dynamic);
                    break;
                case 'student':
                    $('#usraccess').parent().show();
                    select2dynamic.multiple = false;
                    select2dynamic.ajax.url = "<?php echo base_url('user/students_list')?>";
                    $('#usraccess').select2(select2dynamic);
                    break;
                case 'parent':
                    $('#usraccess').parent().show();
                    select2dynamic.multiple = true;
                    select2dynamic.ajax.url = "<?php echo base_url('user/students_list')?>";
                    $('#usraccess').select2(select2dynamic);
                    break;
                case 'admin':
                case '':
                    $('#usraccess').parent().hide();
                    break;             
            }
        })
        
    }); //end od document.ready()
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
                    <li>Ρυθμίσεις</li>
                    <li><a href="<?php echo base_url('user') ?>">Λογαριασμοί χρηστών</a></li>
                    <li class="active">Καρτέλα χρήστη</li>
                </ul>
            </div>

            <p>
                <h4>
                    <?php
                    if (!empty($userdata['username'])) {
                        echo "Επεξεργασία λογαριασμού χρήστη";
                    } else {
                        echo "Νέος λογαριασμός χρήστη";
                    }; ?>
                </h4>
            </p>


            <p></p>
            <form id='mainform' action="<?php echo base_url('user/card/' . $userdata['id']) ?>" method="post" accept-charset="utf-8">


                <div class="row">
                    <!-- first row -->
                    <div class="col-md-12">
                        <!-- first row left side -->
                        <div class="panel panel-default" id="group1">
                            <div class="panel-heading">
                                <span class="icon">
                                    <i class="icon-shield"></i>
                                </span>
                                <h3 class="panel-title">Στοιχεία / Δικαιώματα</h3>
                                <div class="buttons">
                                    <button enabled id="editform1" type="button" class="btn btn-default btn-sm pull-right" data-toggle="button"><i class="icon-edit"></i></button>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Επώνυμο</label>
                                            <input disabled class="form-control" id="usrsurname" type="text" placeholder="" name="surname" value="<?php echo $userdata['surname']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Όνομα</label>
                                            <input disabled class="form-control" id="usrname" type="text" placeholder="" name="name" value="<?php echo $userdata['name']; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Ομάδα</label>
                                            <select disabled class="form-control" id='usrgroupid' name="group_id">
                                                <?php $sel = false; ?>
                                                <?php foreach ($groupdata as $gid => $gname) : ?>
                                                <option value="<?php echo $gid ?>" <?php if ($userdata['group_id'] == $gid) {
                                                                                                echo ' selected="selected"';
                                                                                                $sel = true;
                                                                                                } ?>><?php echo $gname?></option>
                                                <?php endforeach; ?>
                                                <option value='NULL' <?php if ($sel == false) echo ' selected'; ?>></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Πρόσβαση στα δεδομένα των:</label>
                                            <select class="form-control select2" id="usraccess" type="text" placeholder="" name="dataaccess"></select>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end  of panel-body -->
                        </div> <!-- end of panel -->
                    </div>
                <!-- </div> end of right side embeded second row -->

                <div class="col-md-12">
                        <!-- first row left side -->
                        <div class="panel panel-default" id="group2">
                            <div class="panel-heading">
                                <span class="icon">
                                    <i class="icon-shield"></i>
                                </span>
                                <h3 class="panel-title">Διαπιστευτήρια</h3>
                                <div class="buttons">
                                    <button enabled id="editform2" type="button" class="btn btn-default btn-sm pull-right" data-toggle="button"><i class="icon-edit"></i></button>
                                </div>
                            </div>
                            <div class="panel-body">
                                <?php if(!empty($userdata['password'])):?>
                                <div class="alert alert-info alert-dismissable">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                    Αν δε συμπληρωθούν τα πεδία Νέου κωδικού και Επαλήθευσης, ο κωδικός θα παραμείνει ίδιος!
                                </div>
                                <?php endif;?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Όνομα χρήστη (μοναδικό!)</label>
                                            <input disabled class="form-control" id="usrusername" type="text" placeholder="" name="username" value="<?php echo $userdata['username']; ?>">
                                        </div>
                                    </div>
                                    <?php if (!empty($userdata['password'])):?>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Τρέχων κωδικός</label>
                                            <input disabled class="form-control" id="usrcurpassword" type="password" placeholder="" name="curpassword">
                                        </div>
                                    </div>
                                    <?php endif;?>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Νέος κωδικός</label>
                                            <input disabled class="form-control" id="usrpassword" type="password" placeholder="" name="password">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Επαλήθευση κωδικού</label>
                                            <input disabled class="form-control" id="usrpassword_check" type="password" placeholder="" name="password_check">
                                        </div>
                                    </div>
                                </div>                                                                
                                <div class="row">
                                    <div class="col-md-3 col-sm-3">
                                        <div class="form-group datecontainer">
                                            <label>Ημερομηνία λήξης</label>
                                            <input disabled class="form-control" id="usrexprires" type="text" placeholder="" name="expires" value="<?php 
                                                if (!is_null($userdata['expires'])) {
                                                    echo implode('-', array_reverse(explode('-', $userdata['expires'])));
                                                    } ?>">
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="checkbox">
                                            <label></label>
                                                <input disabled id="usrnoexpire" type="checkbox" name="noexpire" 
                                                <?php if(is_null($userdata['expires'])) echo 'checked';?>> Ο λογαριασμός αυτός δε λήγει πoτέ
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end  of panel-body -->
                        </div> <!-- end of panel -->
                    </div>
                </div> <!-- end of right side embeded second row -->


            </form>
            <div class="btn-group">
                <button disabled id="submitbtn" type="button" class="btn btn-primary">Αποθήκευση</button>
                <button disabled id="cancelbtn" type="button" class="btn btn-default">Ακύρωση</button>
            </div>
        </div>
    </div>




    <div class="push"></div>

    </div>
    <!--end of body wrapper-->