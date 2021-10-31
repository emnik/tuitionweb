<!-- <link href="<?php echo base_url('assets/css/dataTables.bootstrap.css') ?>" rel="stylesheet"> -->
<link href="<?php echo base_url('assets/tabletools/css/TableTools.css') ?>" rel="stylesheet">

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs-3.3.7/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-colvis-1.6.4/b-html5-1.6.4/b-print-1.6.4/fc-3.3.1/r-2.2.6/rg-1.1.2/sl-1.3.1/datatables.min.js"></script>

<!-- For date formating -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>

<script src="//netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<!-- <link href="<?php echo base_url('assets/css/dataTables.bootstrap.css') ?>" rel="stylesheet"> -->
<link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap.min.css" rel="stylesheet">


<style type="text/css">
    /* .dataTables_processing{padding-left: 16px;} */
    .dtrg-group.dtrg-start {
        background-color: lightgray;
    }
</style>

<script type="text/javascript">
    var oTable1;
    var oTable2;

    $(document).ready(function() {

        // add sorting methods for currency columns
        jQuery.extend(jQuery.fn.dataTableExt.oSort, {
            "currency-pre": function(a) {
                a = (a === "-") ? 0 : a.replace(/[^\d\-\.]/g, "");
                return parseFloat(a);
            },
            "currency-asc": function(a, b) {
                return a - b;
            },
            "currency-desc": function(a, b) {
                return b - a;
            }
        });


        oTable1 = $('#tbl1').DataTable({
            "buttons": [
                // 'copy', 
                {
                    extend: 'copy',
                    exportOptions: {
                        orthogonal: "exportCopy"
                    }
                },
                // 'excel', 
                {
                    extend: 'excel',
                    exportOptions: {
                        orthogonal: "exportExcel"
                    }
                },
                // 'pdf', 
                {
                    extend: 'pdf',
                    // add title to pdf
                    // title: function () { return "Ιστορικό ΑΠΥ"; },
                    exportOptions: {
                        orthogonal: "exportPdf"
                    }
                },
                // 'print'
                {
                    extend: 'print',
                    exportOptions: {
                        orthogonal: "exportPrint"
                    }
                },
            ],
            dom: 'Bfrtip',
            "processing": true,
            "columns": [{
                    "data": "Τάξη"
                },
                {
                    "data": 'Μαθητές'
                },
                {
                    "data": "Διεγραμμένοι"
                },
                {
                    "data": "Ενεργός αριθμός μαθητών"
                }
            ],
            // order: [
            //     [3, 'asc']
            // ],
            // rowGroup: {
            //     dataSrc: "name"
            // },
            // columnDefs: [{
            //     targets: [2, 3],
            //     visible: false
            // }],
            "language": {
                "paginate": {
                    "first": "Πρώτη",
                    "previous": "",
                    "next": "",
                    "last": "Τελευταία"
                },
                "info": "Εμφανίζονται οι _START_ έως _END_ από τις _TOTAL_ εγγραφές",
                "infoEmpty": "Εμφάνιζονται 0 εγγραφές",
                "infoFiltered": "Φιλτράρισμα από _MAX_ συνολικά εγγραφές",
                "lengthMenu": "_MENU_",
                "loadingRecords": "Φόρτωση καταλόγου οφειλών...",
                "processing": "Επεξεργασία...",
                "search": "ανάζήτηση:",
                "zeroRecords": "Δεν βρέθηκαν εγγραφές"
            }
        })

        $('#link1').on('click', clicklink1);
        $('#tbl1').css('width', '100%');

        //------------------------------------------------------------------------------------


        oTable2 = $('#tbl2').DataTable({
            "buttons": [
                // 'copy', 
                {
                    extend: 'copy',
                    exportOptions: {
                        orthogonal: "exportCopy"
                    }
                },
                // 'excel', 
                {
                    extend: 'excel',
                    exportOptions: {
                        orthogonal: "exportExcel"
                    }
                },
                // 'pdf', 
                {
                    extend: 'pdf',
                    // add title to pdf
                    // title: function () { return "Ιστορικό ΑΠΥ"; },
                    exportOptions: {
                        orthogonal: "exportPdf"
                    }
                },
                // 'print'
                {
                    extend: 'print',
                    exportOptions: {
                        orthogonal: "exportPrint"
                    }
                },
            ],
            dom: 'Bfrtip',
            "processing": true,
            "columns": [{
                    "data": "Μάθημα"
                },
                {
                    "data": "Τάξη",
                    //   "render": function (data, type, row, meta) {
                    //             if (data == null) {data='0'};
                    //             if (type ==="display" || type ==="exportPdf" || type ==="exportPrint" ){
                    //                 return data+'€';
                    //             }
                    //             else 
                    //             {
                    //               return data;
                    //             }
                    //           }
                },
                {
                    "data": "Μαθητές"
                }
            ],
            order: [
                [1, 'desc']
            ],
            rowGroup: {
                dataSrc: "Τάξη"
            },
            columnDefs: [{
                targets: [1],
                visible: false
            }],
            "language": {
                "paginate": {
                    "first": "Πρώτη",
                    "previous": "",
                    "next": "",
                    "last": "Τελευταία"
                },
                "info": "Εμφανίζονται οι _START_ έως _END_ από τις _TOTAL_ εγγραφές",
                "infoEmpty": "Εμφάνιζονται 0 εγγραφές",
                "infoFiltered": "Φιλτράρισμα από _MAX_ συνολικά εγγραφές",
                "lengthMenu": "_MENU_",
                "loadingRecords": "Φόρτωση καταλόγου ...",
                "processing": "Επεξεργασία...",
                "search": "ανάζήτηση:",
                "zeroRecords": "Δεν βρέθηκαν οφειλές"
            }
        })

        $('#link2').on('click', clicklink2);
        $('#tbl2').css('width', '100%');

        //------------------------------------------------------------------------------------

        //bootstrap3 style fixes until datatables 1.10 is released with bootstrap3 support


        $('#tbl1_filter').addClass("pull-left");
        $('#tbl1_length').css({
            "margin-top": "10px"
        });
        $('#tbl1_search').addClass("pull-left");
        $('#tbl1_length').css({
            "text-align": "left"
        });

        $('#tbl2_filter').addClass("pull-left");
        $('#tbl2_length').css({
            "margin-top": "10px"
        });
        $('#tbl2_search').addClass("pull-left");
        $('#tbl2_length').css({
            "text-align": "left"
        });

        $(".dt-buttons").css({
            "margin-bottom": "10px"
        })


        $(window).on("resize", function(e) {
            checkScreenSize();
        });
        checkScreenSize();

        function checkScreenSize() {
            var newWindowWidth = $(window).width();
            if (newWindowWidth < 481) {
                $(".dt-buttons").removeClass("pull-right");
            } else {
                $(".dt-buttons").addClass("pull-right");
            }
        }

    }) //end of (document).ready(function())

    function clicklink1() {
        $.ajax({
            type: "POST",
            url: "<?php echo base_url() ?>reports/getstdcountperclass",
            success: function(data) {
                if (data != false) {
                    oTable1.clear();
                    oTable1.rows.add(data.aaData).draw();
                    $('#link1').off('click');
                }
            }
        });

    };


    function clicklink2() {
        $.ajax({
            type: "POST",
            url: "<?php echo base_url() ?>reports/getstdcountperlesson",
            success: function(data) {
                if (data != false) {
                    oTable2.clear();
                    oTable2.rows.add(data.aaData).draw();
                    $('#link2').off('click');
                }
            }
        });
    };
</script>

</head>

<body>
    <div class="wrapper">
        <!--body wrapper for css sticky footer-->

        <div class="navbar navbar-inverse navbar-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?php echo base_url() ?>">TuitionWeb</a>
                </div>

                <div class="navbar-collapse collapse" role="navigation">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Λειτουργία<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url('student') ?>">Μαθητολόγιο</a></li>
                                <li><a href="<?php echo base_url('exam') ?>">Διαγωνίσματα</a></li>
                                <!-- <li><a href="<?php echo base_url() ?>cashdesk">Ταμείο</a></li> -->
                                <!-- <li><a href="<?php echo base_url() ?>announcements">ανάκοινώσεις</a></li> -->
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Οργάνωση/Διαχείριση<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url('staff') ?>">Προσωπικό</a></li>
                                <li><a href="<?php echo base_url('section') ?>">Τμήματα</a></li>
                                <li><a href="<?php echo base_url('curriculum/edit') ?>">Πρόγραμμα Σπουδών</a></li>
                                <li><a href="<?php echo base_url('curriculum/edit/tutorsperlesson') ?>">Μαθήματα-Διδάσκωντες</a></li>
                                <li><a href="<?php echo base_url() ?>">Στοιχεία Φροντιστηρίου</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle active" data-toggle="dropdown">Συγκεντρωτικές Αναφορές<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li class="active"><a href="<?php echo base_url('reports') ?>">Αναφορές</a></li>
                                <li><a href="<?php echo base_url('history') ?>">Ιστορικό</a></li>
                                <li><a href="<?php echo base_url('telephones') ?>">Τηλ. Κατάλογοι</a></li>
                                <li><a href="<?php echo base_url('finance') ?>">Οικονομικά</a></li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Χρήστης<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li class="dropdown-header"><?php echo $user->surname . ' ' . $user->name; ?></li>
                                <li><a href="#">Αλλαγή κωδικού</a></li>
                                <li><a href="<?php echo base_url('reports/logout') ?>">Αποσύνδεση</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <!--/.navbar-collapse -->
            </div>
        </div>


        <!-- Subhead
================================================== -->
        <div class="jumbotron subhead">
            <div class="container">
                <h1>Αναφορές</h1>
                <p class="leap">Πρόγραμμα διαχείρισης φροντιστηρίου.</p>
                <p style="font-size:13px; margin-top:15px; margin-bottom:-15px;">
                    <?php
                    $s = $this->session->userdata('startsch');
                    echo 'Διαχειριστική Περίοδος: ' . $s . '-' . ($s + 1);
                    ?>
                </p>
            </div>
        </div>


        <!-- main container
================================================== -->

        <div class="container" style="margin-bottom:60px;">

            <div>
                <ul class="breadcrumb">
                    <li><a href="<?php echo base_url() ?>"><i class="icon-home"> </i> Αρχική </a></li>
                    <li class="active">Αναφορές</li>
                    <li class="active">Δυναμολόγιο</li>
                </ul>
            </div>

            <!-- <p>
                <h3>
                    Αναφορές
                </h3>
            </p> -->


            <ul class="nav nav-tabs">
                <!-- <li><a href="<?php echo base_url() ?>reports">Σύνοψη</a></li> -->
                <li class="active"><a href="<?php echo base_url() ?>reports/studentscount">Δυναμολόγιο</a></li>
                <li><a href="<?php echo base_url() ?>reports/studentteachers">Καθηγητές ανά μαθητή</a></li>
            </ul>

            <p></p>


            <div class="row">

                <div class="col-xs-12">
                    <!-- <div id="schmessage" class="alert  alert-warning fade in"><span class="icon"><i class="icon-info-sign"> </i> Tελευταία ενημέρωση των οικονομικών δεδομένων για το σχολικό έτος : <?php $m = (!isset($schoolyear_update)) ? " Δεν υπάρχει!" : $schoolyear_update;
                                                                                                                                                                                                            echo '<strong>' . ' ' . $m . '</strong>'; ?></span></div> -->

                    <h4>Αναφορές</h4>

                    <div class="panel-group" id="accordion">

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <span class="icon">
                                    <i class="icon-file-text"></i>
                                </span>
                                <h4 class="panel-title">
                                    <a id="link1" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                        Αριθμός μαθητών ανά τάξη
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseOne" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <table id="tbl1" class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Τάξη</th>
                                                <th>Αρ. Μαθητών</th>
                                                <th>Διεγραμμένοι</th>
                                                <th>Ενεργοί</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <span class="icon">
                                    <i class="icon-file-text"></i>
                                </span>
                                <h4 class="panel-title">
                                    <a id="link2" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                                        Αριθμός μαθητών ανά μάθημα
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseTwo" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <table id="tbl2" class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Μάθημα</th>
                                                <th>Τάξη</th>
                                                <th>Μαθητές</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>



                    <!-- ============================================================= -->
                    <!-- </div> end of panel body -->
                    <!-- </div> -->
                </div>
            </div>


        </div>
        <!--end of main container-->

        <div class="push"></div>

    </div> <!-- end of body wrapper-->