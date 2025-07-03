<link href="<?php echo base_url('assets/schedule/schedule.css') ?>" rel="stylesheet">

<!-- There is no need to include select2's css & js files as this are included in the footer code!!! -->
<!-- 
<link href="<?php echo base_url('assets/select2/select2.css') ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/select2/select2-bootstrap.css') ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/select2/select2.js') ?>"></script>
<script src="<?php echo base_url('assets/select2/select2_locale_el.js') ?>"></script> 
-->


<style type="text/css">
</style>

<script type="text/javascript">

    $(document).ready(function() {

        $('#menu-operation').addClass('active');
        $('#menu-schedule').addClass('active');
        $('#menu-header-title').text('Ημερήσιο Πρόγραμμα');

        <?php if($schedule):?>        
        var segment_str = window.location.pathname;
        var segment_array = segment_str.split('/');
        var daynum = segment_array.pop();
        if(daynum==0) daynum=1;
        $('#day' + daynum).addClass("active");

        if (daynum == 6) {
            $('li.lecture-time').addClass('saturday');
            $('#timeline1>span').text("08:00");
            $('#timeline2>span').text("08:30");
            $('#timeline3>span').text("09:00");
            $('#timeline4>span').text("09:30");
            $('#timeline5>span').text("10:00");
            $('#timeline6>span').text("10:30");
            $('#timeline7>span').text("11:00");
            $('#timeline8>span').text("11:30");
            $('#timeline9>span').text("12:00");
            $('#timeline10>span').text("12:30");
            $('#timeline11>span').text("13:00");
            $('#timeline12>span').text("13:30");
            $('#timeline13>span').text("14:00");
            $('#timeline14>span').text("14:30");
            $('#timeline15>span').text("15:00");
            $('#timeline16>span').text("15:30");
            $('#timeline17>span').text("16:00");
        }

        var teacher_names_data = [<?php
                                    $name = array();
                                    foreach ($schedule as $row) {
                                        foreach ($row as $program) {
                                            if (!in_array($program['nickname'], $name)) {
                                                array_push($name, $program['nickname']);
                                            }
                                        }
                                    }
                                    sort($name);
                                    $c = 0;
                                    foreach ($name as $value) {
                                        $c += 1;
                                        echo '{' . 'id:"' . $c . '",text:"' . $value . '"},';
                                    }; ?>]

        var class_names_data = [<?php
                                $name = array();
                                $classid = array();
                                foreach ($schedule as $row) {
                                    foreach ($row as $program) {
                                        if (!in_array($program['class_name'], $name)) {
                                            array_push($name, $program['class_name']);
                                            array_push($classid, $program['class_id']);
                                        }
                                    }
                                }

                                $c = array_combine($classid, $name);
                                asort($c);
                                foreach ($c as $id => $text) {
                                    echo '{' . 'id:"' . $id . '",text:"' . $text . '"},';
                                }; ?>]

        $('#teacher_select').select2({
            data: teacher_names_data,
            // multiple: true,
            // closeOnSelect: false,
            minimumResultsForSearch: Infinity, //hide the searchbox
            readonly: true,
            placeholder: "Επιλογή καθηγητή",
        }).on('change', function() {
            var data = $('#teacher_select').select2('data');
            if (data.length > 0) {
                var selectedText = data[0].text; // Assuming single selection
                var filter = $('.lecture-time a:not(:contains("' + selectedText + '"))');
                $('.lecture-time').show();
                filter.parent().hide();
            }
        }).on('select2:opening', function() {
            $('#class_select').val(null).trigger('change');
            if ($('#teacher_select').val() == "") {
                $('.lecture-time').show();
            }
            //to prevent keyboard popup on mobile
            $(this).siblings('.select2-container').find('.select2-search, .select2-focusser').remove()
         });

        $('#class_select').select2({
            data: class_names_data,
            // multiple: true,
            // closeOnSelect: false,
            minimumResultsForSearch: Infinity, //hide the searchbox
            readonly: true,
            placeholder: "Επιλογή τάξης",
        }).on('change', function() {
            var data = $('#class_select').select2('data');
            if (data.length > 0) {
                var selectedId = data[0].id; // Assuming single selection
                var filter = $('.classid' + selectedId);
                $('.lecture-time').hide();
                filter.parent().show();
            }
        }).on('select2:opening', function() {
            $('#teacher_select').val(null).trigger('change');
            if ($('#class_select').val() == "") {
                $('.lecture-time').show();
            }
            //to prevent keyboard popup on mobile
            $(this).siblings('.select2-container').find('.select2-search, .select2-focusser').remove()
        })

        $('#filters button').on('click', function() {
            $('.lecture-time').show();
            $('#class_select').val(null).trigger('change');
            $('#teacher_select').val(null).trigger('change');
        });
        
        // on start set null on the filters
        $('#class_select').val(null).trigger('change');
        $('#teacher_select').val(null).trigger('change');

        <?php endif;?>

    }) //end of (document).ready(function())
</script>

</head>

<body>
    <div class="wrapper">
        <!--body wrapper for css sticky footer-->

        <!-- Menu start -->
        <?php include(__DIR__ .'/include/menu.php');?>
        <!-- Menu end -->


        <!-- main container
================================================== -->

        <div class="container" style="margin-bottom:60px;">

            <div>
                <ul class="breadcrumb">
                    <li><a href="<?php echo base_url() ?>"><i class="icon-home"> </i> Αρχική </a></li>
                    <li class="active">Πρόγραμμα</li>
                </ul>
            </div>

            <!-- <p>
                <h3>
                    Αναφορές
                </h3>
            </p> -->

            <?php if($schedule):?>
            <ul class="nav nav-tabs">
                <li id='day1'><a href="<?php echo base_url('schedule/index/1') ?>">Δευτέρα</a></li>
                <li id='day2'><a href="<?php echo base_url('schedule/index/2') ?>">Τρίτη</a></li>
                <li id='day3'><a href="<?php echo base_url('schedule/index/3') ?>">Τετάρτη</a></li>
                <li id='day4'><a href="<?php echo base_url('schedule/index/4') ?>">Πέμπτη</a></li>
                <li id='day5'><a href="<?php echo base_url('schedule/index/5') ?>">Παρασκευή</a></li>
                <li id='day6'><a href="<?php echo base_url('schedule/index/6') ?>">Σάββατο</a></li>
            </ul>

            <div class="row" id="filters">
                <div class="col-sm-3 col-xs-6">
                    <select type="text" class="form-control select2" id='class_select' style="width:100%;"></select>
                </div>
                <div class="col-sm-3 col-xs-6">
                    <select type="text" class="form-control select2" id='teacher_select' style="width:100%;"></select>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <button type="button" class="btn btn-reset">Επαναφορά</button>
                </div>
            </div>

            <!-- <p></p> -->
            <div class="container-lecture">
                <!-- <section class="header">
                    <h3 class="headline">Δευτέρα</h3>
                </section> -->


                <section class="section-list">

                    <div class="table-schedule">
                        <div class="timeline">
                            <ul>
                                <li id='timeline1'><span>14:00</span></li>
                                <li id='timeline2'><span>14:30</span></li>
                                <li id='timeline3'><span>15:00</span></li>
                                <li id='timeline4'><span>15:30</span></li>
                                <li id='timeline5'><span>16:00</span></li>
                                <li id='timeline6'><span>16:30</span></li>
                                <li id='timeline7'><span>17:00</span></li>
                                <li id='timeline8'><span>17:30</span></li>
                                <li id='timeline9'><span>18:00</span></li>
                                <li id='timeline10'><span>18:30</span></li>
                                <li id='timeline11'><span>19:00</span></li>
                                <li id='timeline12'><span>19:30</span></li>
                                <li id='timeline13'><span>20:00</span></li>
                                <li id='timeline14'><span>20:30</span></li>
                                <li id='timeline15'><span>21:00</span></li>
                                <li id='timeline16'><span>21:30</span></li>
                                <li id='timeline17'><span>22:00</span></li>
                            </ul>
                        </div>

                        <div class="table-schedule-subject">
                            <ul class="list-lecture-item">
                                <?php if ($schedule) {
                                    $prevClassNum = 0;
                                    foreach ($schedule as $classNum => $daydata) {
                                        if ($prevClassNum != $classNum) {
                                            echo '<li class="timeline-vertical">';
                                            echo '<div class="top-info today">';
                                            echo '<h4 class="day">Αίθουσα ';
                                            echo $classNum;
                                            echo '</h4>
                                                        </div>
                                                        <ul>';
                                        }
                                        foreach ($daydata as $data) {
                                            if (isset($data['duration'])) {
                                                echo '<li class="lecture-time ';
                                                if ($data['duration'] == '1.5') {
                                                    echo ' one-half-hr ';
                                                } elseif ($data['duration'] == '2') {
                                                    echo ' two-hr ';
                                                }
                                                echo 'hr-';
                                                $time = explode(':', $data['start_tm']);
                                                echo $time[0];
                                                if ($time[1] == '30') echo '-30';
                                                echo '" data-event="lecture-';
                                                echo $data['class_id'];
                                                echo '">';
                                                echo '    <a class="classid' . $data['class_id'] . ' target="_blank" rel="noopener noreferrer" " href="' . base_url('section/card/' . $data['section_id'] . '/sectionstudents') . '">
                                                                        <div class="lecture-info">
                                                                            <h6 class="lecture-title">';
                                                echo $data['title'];
                                                echo '</h6>
                                                                            <h6 class="lecture-location">Τμήμα: ';
                                                echo $data['section'];
                                                echo '</h6>
                                                                        </div>';


                                                echo '<div class="lecture-noti" data-toggle="tooltip" data-placement="top" title="" data-original-title="">
                                                                <i class="material-icons ic-lecture-noti">';
                                                echo substr($data['start_tm'], 0, 5) . '-' . substr($data['end_tm'], 0, 5) . " " . $data['nickname'] . '</i>
                                                                <span class="lecture-noti-title"></span>
                                                            </div></a>
                                                            </li>';
                                            }
                                        }
                                        $prevClassNum = $classNum;

                                        echo '</ul>
                                                </li>';
                                    }
                                } ?>
                            </ul>
                        </div>
                    </div>

                </section>
            </div>

        <?php else:?>
            <div class="alert alert-danger" role="alert">Δε βρέθηκε πρόγραμμα! Μπορείτε να εισάγετε το πρόγραμμα κάθε τμήματος από την ενότητα: <a href="<?php echo base_url('section');?>" >Οργάνωση/Διαχείριση φροντιστηρίου/Τμήματα.</a>
            επιλέγοντας το εκάστοτε τμήμα και πατώντας στη συνέχεια το κουμπί Καρτέλα Τμήματος!
        </div>
        <?php endif;?>
        </div>

        <!--end of main container-->

        <div class="push"></div>

    </div> <!-- end of body wrapper-->