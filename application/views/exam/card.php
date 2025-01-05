<link href="<?php echo base_url('assets/bootstrap-datepicker-1.3.0/css/datepicker3.css') ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/bootstrap-datepicker-1.3.0/js/bootstrap-datepicker.js') ?>"></script>
<script src="<?php echo base_url('assets/bootstrap-datepicker-1.3.0/js/locales/bootstrap-datepicker.el.js') ?>" charset="UTF-8"></script>
<link href="<?php echo base_url('assets/clockpicker-master/dist/bootstrap-clockpicker.min.css') ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/clockpicker-master/dist/bootstrap-clockpicker.min.js') ?>"></script>

<script type="text/javascript">
    var newlessonc = 1;
    var newlessonindex = -newlessonc;

    function toggleedit(togglecontrol, id) {

        if ($(togglecontrol).hasClass('active')) {
            $('#' + id).closest('.mainform').find(':input').each(function() {
                $(this).attr('disabled', 'disabled');
                $(this).find('btn').attr('disabled', 'disabled');
            });
            if(id=="editform1"){
                $('#supervisors').prop('disabled', true);
            }
        } else {
            $('#' + id).closest('.mainform').find(':input').removeAttr('disabled');
            $(this).find('btn').removeAttr('disabled');
            if(id=="editform1"){
                $('#supervisors').prop('disabled', false);
            }
            
            if (newlessonc > 1) {
                $('#undolessonbtn').removeAttr('disabled');
            } else {
                $('#undolessonbtn').attr('disabled', 'disabled');
            };
        };

    }

    $(document).ready(function() {

        //Menu current active links and Title
        $('#menu-exams').addClass('active');
        $('#menu-header-title').text('Καρτέλα Διαγωνίσματος');


        $('#cancelbtn').click(function() {
            window.open("<?php echo base_url('exam/cancel/card/'.$examcard['id']);?>", '_self', false);
        });

        $("body").on('click', '#editform1, #editform2', function() {
            toggleedit(this, this.id);
            $(this).removeAttr('disabled');

        });

        //we must enable all form fields to submit the form with no errors!
        $("body").on('click', '#submitbtn', function() {
            $('.mainform').find(':input:disabled').removeAttr('disabled');
            $('form').submit();
        });

        //if it is a new exam all the fields should be enabled
        <?php if (empty($examcard['name'])) : ?>
            $('#editform1').addClass('active');
            $('#editform2').addClass('active');
            $('.mainform').find(':input:disabled').removeAttr('disabled');
        <?php endif; ?>


        $('#supervisors').select2();
    //  $('#supervisors').select2({
	//     minimumInputLength: 2,
	//     ajax: {
	//       url: "<?php echo base_url()?>exam/supervisors_list",
	//       dataType: 'json',
	//       data: function (term, page) {
	//         return {
	//           q: term //sends the typed letters to the controller
	//         };
	//       },
	//       results: function (data, page) {
	//         return { results: data }; //data needs to be {{id:"",text:""},{id:"",text:""}}...
	//       }
	//     }
	//   });


        $(document).on('change', '.classes', function(){ //bind to the document to include the dynamically generated fields!!!
        // $('.classes').change(function() {
            thisid=$(this).attr('id');
            id = thisid.match(/\[(.*)\]/);
            console.log(thisid);
            document.getElementById('lessons['+id[1]+']').options.length = 0;
            getcourses(id[1]);
        })

        $(document).on('change', '.courses', function(){ //bind to the document to include the dynamically generated fields!!!
        // $('.courses').change(function() {
            thisid=$(this).attr('id');
            id = thisid.match(/\[(.*)\]/);
            getlessons(id[1]);
        }); //end change event 


        //addind new days in program

        $('#newlessonbtn').click(function() {

            $("#undolessonbtn").removeAttr('disabled');
            newlessonc = newlessonc + 1;
            newlessonindex = -newlessonc;
            var lastlessonrow = $(this).closest('.row').prev('.row');
            var newlesson = lastlessonrow.clone();
            newlesson.insertAfter(lastlessonrow);
            var inputfields = newlesson.find('input[type="checkbox"]');
            var selfields = newlesson.find('select');

            //Reset values for the cloned inputfields

              //-------------set new class---------------
              selfields.eq(0).attr("name", "class_id[" + newlessonindex + "]");
              selfields.eq(0).attr('id', "classes[" + newlessonindex + "]");
              selfields.eq(0).find('option:first-child').removeAttr("selected");
              selfields.eq(0).find('option:last-child').attr('selected', 'selected');
              

              //-------------set new course---------------
              selfields.eq(1).attr("name", "course_id[" + newlessonindex + "]");
              selfields.eq(1).attr('id', "courses[" + newlessonindex + "]");
              selfields.eq(1).find('option:first-child').removeAttr("selected");
              selfields.eq(1).find('option:last-child').attr('selected', 'selected');

              //-------------set new lesson---------------
              selfields.eq(2).attr("name", "lesson_id[" + newlessonindex + "]");
              selfields.eq(2).attr('id', "lessons[" + newlessonindex + "]");
              selfields.eq(2).find('option:first-child').removeAttr("selected");
              selfields.eq(2).find('option:last-child').attr('selected', 'selected');

            //   //-------------set new checkbox---------------
              inputfields.eq(0).attr("name", "select[" + newlessonindex + "]");
            //   inputfields.eq(0).attr('id', "select[" + newlessonindex + "]");
              inputfields.eq(0).prop('value', 0);
              inputfields.eq(0).attr('value', 0);
        });


        $('#undolessonbtn').click(function() {
            if (newlessonc > 1) {
                var lastlessonrow = $(this).closest('.row').prev('.row');

                lastlessonrow.remove();
                newlessonc = newlessonc - 1;

                if (newlessonc == 1) {
                    $(this).attr('disabled', 'disabled');
                }
            }
        });

        $('.timecontainer input')
        .clockpicker({
            autoclose: true,
            minutestep: 5
        })
        .on('focus click tap vclick', function(event) {
        //stop keyboard events and focus on the datepicker widget to get the date.
        //this is most usefull in android where the android's keyboard was getting in the way...
        event.stopImmediatePropagation();
        event.preventDefault();
        $(this).blur();
    });

        $('.datecontainer input')
        .datepicker({
            format: "dd-mm-yyyy",
            language: "el",
            autoclose: true,
            todayHighlight: true
        })
        .on('focus click tap vclick', function(event) {
            //stop keyboard events and focus on the datepicker widget to get the date.
            //this is most usefull in android where the android's keyboard was getting in the way...
            event.stopImmediatePropagation();
            event.preventDefault();
            $(this).blur();
        });


        $('#delexambtn').click(function() {
            var r = confirm("Το διαγώνισμα πρόκειται να διαγραφεί. Παρακαλώ επιβεβαιώστε.");
            if (r == true) {
                window.open("<?php echo base_url('exam/delexam/' . $examcard['id']); ?>", '_self', false);
            }
            return false;
        });

        //delete or cancel multiple payments using the select checkboxes and the combobox below (the action fires through ajax)
        $('#select_action').change(function(){
            var act=$(this).val();
            var fieldsets = $('form').find('.programrow');
            var selected_chkboxes = $('form').find(':input[type="checkbox"][name*="select"]:visible:checked');
            var allselected = false;
            if(selected_chkboxes.length == fieldsets.length) allselected = true;
            if (act!='none' && selected_chkboxes.length>0) {
                var selected_chkboxes = $('form').find(':input[type="checkbox"][name*="select"]:visible:checked');
                var sData = selected_chkboxes.serialize();
                switch(act){
                case 'delete':
                    var msg="Πρόκειται να αφαιρέσετε τα επιλεγμένα μαθήματα από το διαγώνισμα. Η ενέργεια αυτή δεν αναιρείται. Παρακαλώ επιβεβαιώστε.";
                    var post_url = "<?php echo base_url();?>exam/lesson_batch_actions/delete";
                    break;
                case 'move':
                    $('#moveLessonModal').modal();
                    break;
                };
                if(act!='move'){
                var ans=confirm(msg);
                if (ans==true){
                    $.ajax({
                    type: "post",
                    url: post_url,
                    data : sData,
                    dataType:'json', 
                    success: function(){
                        if (allselected==true){
                            window.location.href = window.location.href;  
                        }
                        selected_chkboxes.each(function(){
                            $(this).parents('.programrow').remove();
                        });
                    }
                    }); //end of ajax
                }
                } //end if ans
            } //end if act
            $(this).prop('selectedIndex',0);
        })


    }); //end of (document).ready(function())

    function getcourses(id) {
        //the [ ] characters need to be escaped in the jquery selector (not though in vanilla js!)
        var classid = $('#classes\\['+id+'\\] option:selected').val(); 
        // console.log(classid);

        //clear options from course select input
        document.getElementById('courses['+id+']').options.length = 0;

        //the following is ajax post to populate the course dropdown 
        var postdata = {
            'jsclassid': classid
        };
        //post_url is the controller function where I want to post the data
        var post_url = "<?php echo base_url('exam/courses') ?>";
        $.ajax({
            type: "POST",
            url: post_url,
            data: postdata,
            dataType: 'json',
            async: false,
            //courses is just a name that gets the result of the controller's function I posted the data
            success: function(courses) //we're calling the response json array 'courses data'
            {
                $.each(courses, function(cid, course) {
                    var opt = $('<option />'); // here we're creating a new select option for each group
                    opt.val(cid);
                    opt.text(course);
                    console.log(opt);
                    $("#courses\\["+id+"\\]").append(opt);
                });

                //$("#courses option:first").prop("selected", "selected");
                $("#courses\\["+id+"\\] option:first").removeAttr('selected').attr("selected", "selected");
            } //end success
        }); //end AJAX

        //if only one course, get the lessons too. The above ajax query MUST be async=false to work!!! this one...
        if ($('#courses\\['+id+'\\]').get(0).options.length == 1) {
            getlessons(id);
        } else
        //select none so once a user selection in that happens to trigger the get lessons function
        {
            var opt = $('<option />');
            opt.val('none');
            opt.text(" ");
            $('#courses\\['+id+'\\]').append(opt);
            $("#courses\\["+id+"\\] option:last").removeAttr('selected').attr("selected", "selected");
        };

    }


    function getlessons(id) {
        var classid = $('#classes\\['+id+'\\] option:selected').val();
        var courseid = $('#courses\\['+id+'\\] option:selected').val();

        //clear options from lessons select input
        document.getElementById('lessons['+id+']').options.length = 0;

        //the following is ajax post to populate the course dropdown 
        var postdata = {
            'jsclassid': classid,
            'jscourseid': courseid
        };
        //post_url is the controller function where I want to post the data
        var post_url = "<?php echo base_url('exam/lessons') ?>";
        $.ajax({
            type: "POST",
            url: post_url,
            data: postdata,
            dataType: 'json',
            //lessons is just a name that gets the result of the controller's function I posted the data
            success: function(lessons) {
                $.each(lessons, function(lid, lesson) {

                    var opt = $('<option />'); // here we're creating a new select option for each group
                    opt.val(lid);
                    opt.text(lesson);
                    $('#lessons\\['+id+'\\]').append(opt);
                    //console.log(opt);
                });
                // $("#lessons\\["+id+"\\] option:first").prop("selected", "selected");

                var opt = $('<option />'); // here we're creating a new select option for each group
                opt.val('none');
                opt.text(" ");
                $('#lessons\\['+id+'\\]').append(opt);
                $("#lessons\\["+id+"\\] option:last").prop("selected", "selected");
                //$("#lessons["+id+"] option:first").removeAttr('selected').attr("selected", "selected");

            } //end success
        }); //end AJAX

    }


    //delete a specific change
    //   function delprogamday(id, day) {
    //     var sData = {
    //       'jsprogramid': id,
    //       'sectionid': <?php echo('') ?>
    //     };
    //     var days = $('.programrow').length;

    //     var res = confirm("Πρόκειται να διαγράψετε μία ημέρα προγράμματος: " + day + ". Σίγουρα Θέλετε να συνεχίσετε;");
    //     var post_url = "<?php echo base_url('section/delprogramday'); ?>";

    //     if (res == true) {
    //       $.ajax({
    //         type: "post",
    //         url: post_url,
    //         data: sData,
    //         dataType: 'json',
    //         success: function() {
    //           if (days == 1) {
    //             window.location.href = window.location.href;
    //           }
    //         }
    //       }); //end of ajax
    //       $('select[name="day[' + id + ']"]').closest('.row').remove();
    //       $('#deldaylist' + id).remove();
    //     }
    //   }
</script>

</head>

<body>
    <div class="wrapper">
        <!--body wrapper for css sticky footer-->
        <!-- Menu start -->
        <!-- dirname(__DIR__) gives the path one level up by default -->
        <?php include(dirname(__DIR__) . '/include/menu.php'); ?>
        <!-- Menu end -->


        <!-- main container  ================================================== -->

        <div class="container" style="margin-bottom:60px;">

            <div>
                <ul class="breadcrumb">
                    <li><a href="<?php echo base_url() ?>"><i class="icon-home"> </i> Αρχική </a></li>
                    <li><a href="<?php echo base_url('exam') ?>">Διαγωνίσματα</a> </li>
                    <li class="active">Καρτέλα διαγωνίσματος</li>
                </ul>
            </div>

            <p>
                <h3>
                    <?php
                    if (!empty($examcard['name'])) {
                        echo $examcard['name'];
                    } else {
                        echo "Νέο διαγώνισμα";
                    }; ?>
                </h3>
            </p>


            <!-- <ul class="nav nav-tabs">
                <li class="active"><a href="<?php echo base_url() ?>exam/card/<?php echo $examcard['id'] ?>">Προγραμματισμός</a></li>
            </ul> -->

            <p></p>

            <div class="visible-sm visible-xs" style="margin:15px 0px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="btn-group pull-left">
                            <a class="btn btn-default btn-sm" href="#group1">Προγραμματισμός</a>
                            <a class="btn btn-default btn-sm" href="#group2">Μαθήματα</a>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">

                <div class="col-md-12">
                    <form action="<?php echo base_url() ?>exam/card/<?php echo $examcard['id'] ?>" method="post" accept-charset="utf-8" role="form">

                        <div class="row">
                            <!-- section data -->
                            <div class="col-md-12" id="group1">
                                <div class="mainform">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <span class="icon">
                                                <i class="icon-tag"></i>
                                            </span>
                                            <h3 class="panel-title">Προγραμματισμός</h3>
                                            <div class="buttons">
                                                <button enabled id="editform1" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
                                            </div>
                                        </div>
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label>Όνομα</label>
                                                        <input disabled class="form-control" id="name" type="text" placeholder="Κύκλος [Α-Δ] - Ομάδα Διαγωνισμάτων #" name="name" value="<?php echo (!empty($examcard['name'])?$examcard['name']:'');?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 col-xs-12">
                                                    <div class="form-group datecontainer">
                                                        <label>Ημερομηνία</label>
                                                        <input disabled class="form-control" id="date" type="text" placeholder="" name="date" value="<?php echo (!empty($examcard['date'])?implode('-', array_reverse(explode('-', $examcard['date']))):''); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-xs-6">
                                                    <div class="form-group timecontainer">
                                                        <label>Ώρα έναρξης</label>
                                                        <input disabled class="form-control" id="start" type="text" placeholder="" name="start" value="<?php echo (!empty($examcard['start'])?date('H:i', strtotime($examcard['start'])):''); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-xs-6">
                                                    <div class="form-group timecontainer">
                                                        <label>Ώρα Λήξης</label>
                                                        <input disabled class="form-control" id="end" type="text" placeholder="" name="end" value="<?php echo (!empty($examcard['end'])?date('H:i', strtotime($examcard['end'])):''); ?>">
                                                    </div>
                                                </div>                                                
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Επιτηρητές</label>
                                                        <!-- <input disabled class="form-control" id="supervisors" type="hidden" name="supervisors"></input> -->
                                                        <select id='supervisors' placeholder="" multiple  disabled   name="supervisors[]" class="form-control">
                                                        <?php if($tutor):?>
                                                            <?php foreach ($tutor as $id=>$title):?>
                                                                <option value="<?php echo $title['id'];?>" 
                                                                <?php
                                                                    if(!empty($supervisor)) {
                                                                        if (in_array($title['id'], $supervisor))
                                                                        {
                                                                            echo " selected='selected'";
                                                                        }                                  
                                                                    };?>>
                                                                    <?php echo $title['text'];?>
                                                                </option>
                                                            <?php endforeach;?>
                                                        <?php endif;?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end of content row -->
                                </div>
                            </div>
                        </div><!-- end of exam data    -->

                        <div class="row">
                            <!-- add lessons -->
                            <div class="col-md-12" id="group2">
                                <div class="mainform">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <span class="icon">
                                                <i class="icon-edit"></i>
                                            </span>
                                            <h3 class="panel-title">Εξεταζόμενα μαθήματα</h3>
                                            <div class="buttons">
                                                <button enabled id="editform2" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
                                            </div>
                                        </div>
                                        <div class="panel-body">
                                        <?php if (!empty($examprog)) : ?>
                                            <?php foreach ($examprog as $examlessonid=>$examlessonval) : ?>
                                            <div class="row programrow" style="border-bottom:1px solid; border-color:lightgray; margin-bottom:15px;">
                                                <div class="col-xs-1 col-sm-1">
                                                    <div class="form-group selector">
                                                        <label class="hidden-xs">Επιλογή</label>
                                                        <input type="checkbox"  name="select[<?php echo $examlessonid;?>]" value="0">
                                                    </div>
                                                </div>
                                                <div class="col-xs-5 col-sm-3">
                                                <label>Τάξη</label>
                                                <select disabled id="classes[<?php echo $examlessonid;?>]" class="form-control classes" name="class_id[<?php echo $examlessonid;?>]">
                                                    <?php $sel = false; ?>
                                                    <?php foreach ($class[$examlessonid] as $data) : ?>
                                                    <option value="<?php echo $data['id'];?>" <?php if ($data['id']==$examlessonval[0]['class_id']) {echo 'selected="selected"'; $sel=true;}?> > <?php echo $data['class_name']; ?></option>
                                                    <?php endforeach; ?>
                                                    <option value="none" <?php if ($sel == false) echo 'selected="selected"'; ?>></option>
                                                </select>
                                                </div>
                                                <!-- </div> -->
                                                <div class="col-xs-6 col-sm-4">
                                                <div class="form-group">
                                                    <label>Κατεύθυνση</label>
                                                    <select disabled id="courses[<?php echo $examlessonid;?>]" class="form-control courses" name="course_id[<?php echo $examlessonid;?>]">
                                                    <?php if ($course) : ?>
                                                        <?php $sel = false; ?>
                                                        <?php foreach ($course[$examlessonid] as $id => $value) : ?>
                                                        <option value="<?php echo $id; ?>" <?php if ($id==$examlessonval[0]['course_id']) {echo 'selected="selected"'; $sel=true;}?>><?php echo $value; ?></option>
                                                        <?php endforeach; ?>
                                                        <option value="none" <?php if ($sel == false) echo 'selected="selected"'; ?>></option>
                                                    <?php endif; ?>
                                                    </select>
                                                </div>
                                                </div>
                                                <div class="col-sm-4 col-xs-11 col-xs-offset-1 col-sm-offset-0">
                                                    <div class="form-group">
                                                        <label>Μάθημα</label>
                                                        <select disabled id="lessons[<?php echo $examlessonid;?>]" class="form-control" name="lesson_id[<?php echo $examlessonid;?>]">
                                                        <?php if ($lesson) : ?>
                                                            <?php $sel = false; ?>
                                                            <?php foreach ($lesson[$examlessonid] as $id => $value) : ?>
                                                            <option value="<?php echo $id; ?>" <?php if ($id==$examlessonval[0]['lesson_id']) {echo 'selected="selected"'; $sel=true;}?>><?php echo $value; ?></option>
                                                            <?php endforeach; ?>
                                                            <option value="none" <?php if ($sel == false) echo 'selected="selected"'; ?>></option>
                                                        <?php endif; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach;?>
                                        <?php else:?>
                                            <div class="row programrow">
                                                <div class="col-xs-1">
                                                    <div class="form-group selector">
                                                        <label class="hidden-xs">Επιλογή</label>
                                                        <input type="checkbox"  name="select[-1]" value="0">
                                                    </div>
                                                </div>
                                                <div class="col-xs-3">
                                                    <label>Τάξη</label>
                                                    <select disabled id="classes[-1]" class="form-control classes" name="class_id[-1]">
                                                        <?php foreach ($defaultclass as $data) : ?>
                                                        <option value="<?php echo $data['id'];?>"> <?php echo $data['class_name']; ?></option>
                                                        <?php endforeach; ?>
                                                        <option value="none" selected="selected";></option>
                                                    </select>
                                                </div>
                                                <div class="col-xs-4">
                                                    <div class="form-group">
                                                        <label>Κατεύθυνση</label>
                                                        <select disabled id="courses[-1]" class="form-control courses" name="course_id[-1]">
                                                            <option value="none" selected="selected";></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4  col-xs-4">
                                                    <div class="form-group">
                                                        <label>Μάθημα</label>
                                                        <select disabled id="lessons[-1]" class="form-control" name="lesson_id[-1]">
                                                            <option value="none" selected="selected";></option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif;?>

                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="btn-toolbar">
                                                    <div class="col-md-1 col-sm-1 hidden-xs" style="padding-left: 5px;">
                                                        <i class="icon-hand-up"></i>
                                                    </div>
                                                    <div class="col-md-3 col-sm-3 col-xs-12" >
                                                        <label>Με τα επιλεγμένα : </label>
                                                    </div>
                                                    <div class="col-md-3 col-sm-3 col-xs-7">
                                                        <select class="form-control"  name="select_action" id="select_action">
                                                            <option value="none" selected>-</option>
                                                            <option value="move">Μεταφορά</option>
                                                            <option value="delete">Διαγραφή</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-5 col-sm-5 col-xs-5" style="padding-right:0px;">
                                                        <div class="btn-group pull-right">
                                                            <button id="newlessonbtn" type="button" class="btn btn-primary" disabled>Προσθήκη</button>
                                                            <button id="undolessonbtn" type="button" class="btn btn-primary" disabled><span class="icon"><i class="icon-undo"></i></span></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                    </div> <!--end of panel-->
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="btn-toolbar">
                                        <div class="btn-group">
                                            <button id="submitbtn" type="submit" class="btn btn-primary">Αποθήκευση</button>
                                            <button id="cancelbtn" type="button" class="btn btn-default">Ακύρωση</button>
                                        </div>
                                        <div class="btn-group pull-right">
                                            <a id="delexambtn" href="#" class="btn btn-default"><i class="icon-trash"></i></a>
                                            <a id="newexambtn" href="<?php echo base_url(); ?>exam/newexam" class="btn btn-default"><i class="icon-plus"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>



                    </form>

                    <!-- pager -->

                </div>
            </div>
        </div>
        <!--end of main container-->

        <div class="push"></div>

    </div> <!-- end of body wrapper-->
