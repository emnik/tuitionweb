<link href="<?php echo base_url('assets/select2/select2.css') ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/select2/select2-bootstrap.css') ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/select2/select2.js') ?>"></script>
<script src="<?php echo base_url('assets/select2/select2_locale_el.js') ?>"></script>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/datatables_bundle/datatables.css') ?>" />
<script type="text/javascript" src="<?php echo base_url('assets/datatables_bundle/datatables.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/datatables_bundle/Buttons-1.6.5/js/dataTables.buttons.js') ?>"></script>

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

  //Menu current active links and Title
  $('#menu-reports-summary').addClass('active');
  $('#menu-reports').addClass('active');
  $('#menu-header-title').text('Αναφορές');  

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
                    footer: true,
                    title: function() {
                        return "Αριθμός μαθητών ανά τάξη";
                    },
                    exportOptions: {
                        orthogonal: "exportCopy"
                    }
                },
                // 'excel', 
                {
                    extend: 'excel',
                    footer: true,
                    title: function() {
                        return "Αριθμός μαθητών ανά τάξη";
                    },
                    exportOptions: {
                        orthogonal: "exportExcel"
                    }
                },
                // 'pdf', 
                {
                    extend: 'pdf',
                    footer: true,
                    // add title to pdf
                    title: function() {
                        return "Αριθμός μαθητών ανά τάξη";
                    },
                    customize: function(doc) {
                        doc.styles.tableHeader = {
                            alignment: 'left',
                            bold: true,
                            fillColor: 'gray'
                        }
                        doc.styles.tableFooter = {
                            alignment: 'left',
                            bold: true,
                            fillColor: 'lightgray'
                        }
                        doc.styles.title = {
                            fontSize: 14
                        }

                        doc.content[1].table.widths = ['*', '*', '*', '*'];
                    },
                    exportOptions: {
                        orthogonal: "exportPdf"
                    }
                },
                // 'print'
                {
                    extend: 'print',
                    footer: true,
                    title: function() {
                        return "Αριθμός μαθητών ανά τάξη";
                    },
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
            paging: false,
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
                "search": "Aνάζήτηση:",
                "zeroRecords": "Δεν βρέθηκαν εγγραφές"
            },
            "footerCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
                /*
                 * Calculate the total market share for all browsers in this table (ie inc. outside
                 * the pagination)
                 * if Sort and/or Filter is enabled see http://www.datatables.net/examples/advanced_init/footer_callback.html
                 */
                var iTotalAvenues = 0;
                var iTotalDepts = 0;
                var iTotal = 0;
                for (var i = 0; i < aaData.length; i++) {
                    iTotalAvenues += parseInt(aaData[i]['Μαθητές']);
                    iTotalDepts += parseInt(aaData[i]['Διεγραμμένοι']);
                    iTotal += parseInt(aaData[i]['Ενεργός αριθμός μαθητών']);
                }

                /* Modify the footer row to match what we want */
                var nCells = nRow.getElementsByTagName('th');
                nCells[1].innerHTML = iTotalAvenues;
                nCells[2].innerHTML = iTotalDepts;
                nCells[3].innerHTML = iTotal;
            }
        })

        $('#link1').on('click', clicklink1);
        $('#tbl1').css('width', '100%');

        //------------------------------------------------------------------------------------


        oTable2 = $('#tbl2').DataTable({
            dom: "<'row'<'col-sm-9 col-xs-12 pull-right' B><'col-sm-3 col-xs-12 pull-left' l>><'row' <'col-sm-3 col-xs-5 ' <'#customfilter'>> r><'row'<'col-md-12't>><'row'<'col-md-6'i><'col-md-6'p>>",
            buttons: [
                // 'copy', 
                {
                    extend: 'copy',
                    title: function() {
                        return "Αριθμός μαθητών ανά μάθημα";
                    },
                    exportOptions: {
                        orthogonal: "exportCopy",
                        grouped_array_index: 'Τάξη',
                        columns: ':visible',
                    }
                },
                // 'excel', 
                {
                    extend: 'excel',
                    title: function() {
                        return "Αριθμός μαθητών ανά μάθημα";
                    },
                    exportOptions: {
                        orthogonal: "exportExcel",
                        grouped_array_index: 'Τάξη',
                        columns: ':visible',
                    }
                },
                // 'pdf', 
                {
                    extend: 'pdf',
                    title: function() {
                        return "Αριθμός μαθητών ανά μάθημα";
                    },
                    customize: function(doc) {
                        doc.styles.tableHeader = {
                            alignment: 'left',
                            bold: true,
                            fillColor: 'gray'
                        }
                        doc.styles.title = {
                            fontSize: 14
                        }
                        doc.footer = (function(page, pages) {
                            return {
                                columns: [
                                    {
                                        alignment: 'right',
                                        text: [
                                            'σελ. ',
                                            {
                                                text: page.toString(),
                                                italics: true
                                            },
                                            ' από ',
                                            {
                                                text: pages.toString(),
                                                italics: true
                                            }
                                        ]
                                    }
                                ],
                                margin: [50, 0]
                            }
                        });

                        doc.content[1].table.widths = ['*', '*'];

                        var tblBody = doc['content']['1'].table['body'];
                        
                        //show all pages to be able to use the jquery selector for highlighting the names!
                        oTable2.page.len( -1 ).draw(); 

                        // get all table rows from html table
                        $('#tbl2').find('tr').each(function(ix, row) {
                            // console.log(ix);
                            var index = ix;
                            // var rowElt = row;
                            $(row).find('td').each(function(ind, elt) {
                                // console.log([ind, elt]);
                                console.log(tblBody[index]);
                                // if the second cell (1) of each row is empty then it is a group header
                                if (tblBody[index][1].text == '') {
                                    //style the first cell (0)
                                    delete tblBody[index][ind].style;
                                    tblBody[index][ind].fillColor = 'lightgray';
                                    //style the second cell (1)
                                    delete tblBody[index][ind + 1].style;
                                    tblBody[index][ind + 1].fillColor = 'lightgray';
                                }
                            });
                        });
                        oTable2.page.len(10).draw(); //restore pagination length to default!
                    },                    
                    exportOptions: {
                        orthogonal: "exportPdf",
                        grouped_array_index: 'Τάξη',
                        columns: ':visible',
                    }
                },
                // 'print'
                {
                    extend: 'print',
                    title: function() {
                        return "Αριθμός μαθητών ανά μάθημα";
                    },
                    customize: function(win) {
                        $(win.document.body).find('td:empty').parent()
                            // .addClass( 'compact' )
                            .css('background-color', 'lightgray');
                    },
                    exportOptions: {
                        orthogonal: "exportPrint",
                        grouped_array_index: 'Τάξη',
                        columns: ':visible',
                    }
                },
            ],
            // dom: 'Blfrtip',
            processing: true,
            columns: [{
                    "data": "Μάθημα"
                },
                {
                    "data": "Τάξη",
                },
                {
                    "data": "Μαθητές"
                }
            ],
            // order: [
            //     [1, 'desc']
            // ],
            rowGroup: {
                dataSrc: "Τάξη"
            },
            ordering: false,
            columnDefs: [{
                targets: [1],
                visible: false
            }],
            language: {
                "paginate": {
                    "first": "Πρώτη",
                    "previous": "",
                    "next": "",
                    "last": "Τελευταία"
                },
                "info": "Εμφανίζονται οι _START_ έως _END_ από τις _TOTAL_ εγγραφές",
                "infoEmpty": "Εμφάνιζονται 0 εγγραφές",
                "infoFiltered": "Φιλτράρισμα από _MAX_ συνολικά εγγραφές",
                "lengthMenu": "Εγγραφές/σελ. _MENU_",
                "loadingRecords": "Φόρτωση καταλόγου ...",
                "processing": "Επεξεργασία...",
                "search": "Aνάζήτηση:",
                "zeroRecords": "Δεν βρέθηκαν οφειλές"
            },
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

        // $('#tbl2_filter').addClass("pull-left");
        $('#tbl2_length').css({
            "margin-top": "10px"
        });
        // $('#tbl2_search').addClass("pull-left");
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
            if (newWindowWidth < 750) {
                $(".dt-buttons").removeClass("pull-right");
                $(".dt-buttons").addClass("pull-left");
            } else {
                $(".dt-buttons").removeClass("pull-left");
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
                    // $('#customfilter').select2({
                    //     data: <?php echo $classes; ?>,
                    //     // multiple: true,
                    //     // closeOnSelect: false,
                    //     readonly: true,
                    //     // placeholder: "Επιλογή τάξεων",
                    // })
                    // $('#customfilter').addClass('form-control');
                    // $('#customfilter').on('change', function () {
                    //     var sdata = $('#customfilter').select2('data');
                    //     // console.log(sdata);
                    //     var sval = sdata.text;
                    //     // $("#tbl2 thead th").each(function(i) {
                    //     // oTable2.column(1).visible(false);    
                    //     // console.log(sval);
                    //     if (sval!='Όλα') {
                    //         oTable2.column(1)
                    //         .search(sval, true, false)
                    //         .draw();
                    //     }
                    //     else {
                    //         oTable2.columns()
                    //         .search('')
                    //         .draw();
                    //     }
                    // });
                }
            }
        });
    };
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
                    <li class="active"><a href="<?php echo base_url('reports/initial')?>">Συγκεντρωτικές Αναφορές</a></li>
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
                <li class="active"><a href="<?php echo base_url('reports/studentscount') ?>">Δυναμολόγιο</a></li>
                <li><a href="<?php echo base_url('reports/studentteachers') ?>">Διδάσκοντες ανά μαθητή / Λίστα μαθητών ανα τάξη</a></li>
            </ul>

            <p></p>


            <div class="row">

                <div class="col-xs-12">
                    <!-- <div id="schmessage" class="alert  alert-warning fade in"><span class="icon"><i class="icon-info-sign"> </i> Tελευταία ενημέρωση των οικονομικών δεδομένων για το σχολικό έτος : <?php $m = (!isset($schoolyear_update)) ? " Δεν υπάρχει!" : $schoolyear_update;
                                                                                                                                                                                                            echo '<strong>' . ' ' . $m . '</strong>'; ?></span></div> -->

                    <!-- <h4>Αναφορές</h4> -->

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
                                    <table id="tbl1" class="table table-striped datatable">
                                        <thead>
                                            <tr>
                                                <th>Τάξη</th>
                                                <th>Μαθητές</th>
                                                <th>Διεγραμμένοι</th>
                                                <th>Ενεργοί</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Σύνολο:</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
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
                                        <!-- <tfoot>
                                            <tr>
                                                <th></th>
                                                <th>Τάξη</th>
                                                <th></th>
                                            </tr>
                                        </tfoot> -->
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