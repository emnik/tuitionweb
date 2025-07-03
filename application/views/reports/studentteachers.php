<link href="<?php echo base_url('assets/select2/select2.css') ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/select2/select2-bootstrap.css') ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/select2/select2.js') ?>"></script>
<script src="<?php echo base_url('assets/select2/select2_locale_el.js') ?>"></script>

<!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/datatables_bundle/datatables.css') ?>" />
<script type="text/javascript" src="<?php echo base_url('assets/datatables_bundle/datatables.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/datatables_bundle/Buttons-1.6.5/js/dataTables.buttons.js') ?>"></script> -->

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs-3.3.7/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-colvis-1.6.4/b-html5-1.6.4/b-print-1.6.4/fc-3.3.1/r-2.2.6/rg-1.1.2/sl-1.3.1/datatables.min.js"></script>

<!-- For date formating -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>

<script src="//netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap.min.css" rel="stylesheet">



<style>
    /* .dataTables_processing{padding-left: 16px;} */
    .dtrg-group.dtrg-start {
        background-color: lightgray;
    }
</style>

<script type="text/javascript">
    var oTable1;
    var DTcolumns=[];
    var DTlanguage = {
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
                "loadingRecords": "Φόρτωση καταλόγου οφειλών...",
                "processing": "Επεξεργασία...",
                "search": "Aνάζήτηση:",
                "zeroRecords": "Δεν βρέθηκαν εγγραφές"
            };

    $(document).on('touchend', function() {
        $(".select2-search-field input").remove();
    })

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



    $(document).ready(function() {
        
        //Menu current active links and Title
        $('#menu-reports-summary').addClass('active');
        $('#menu-reports').addClass('active');
        $('#menu-header-title').text('Αναφορές');

        $('#selectClass').select2({
            data: <?php echo $classes; ?>,
            multiple: true,
            closeOnSelect: false,
            readonly: true
            // placeholder: "Επιλογή τάξεων",
        })

        $('#selectFields').select2({
            data: [{
                id: 'course',
                text: 'Κατεύθυνση',
                InitSelection: true
            },
            {
                id: 'address',
                text: 'Διεύθυνση'              
            },
            {
                id: 'region',
                text: 'Περιοχή'              
            },
            {
                id: 'month_price',
                text: 'Τιμή'              
            }],
            multiple: true,
            closeOnSelect: false,
            readonly: true
        })

        $('input[name=optionsRadios]').on('change', function(){
            var val = $(this)[0].value;
            if (val=='studentsPerClass'){
                $('#selectClass').parent().parent().removeClass();
                $('#selectClass').parent().parent().addClass('col-sm-5 col-xs-12');
                $('#selectFields').parent().parent().show();

            } else {
                $('#selectFields').parent().parent().hide();
                $('#selectClass').parent().parent().removeClass();
                $('#selectClass').parent().parent().addClass('col-sm-10 col-xs-12');
            }
        })

        $("button[id='generateReport']").click(function() {
            var classes = $('#selectClass').select2('data');
            var optionList = $('form input[name=optionsRadios]:radio:checked');
            var reportType= optionList[0].value;
            var selList="";
            if (classes.length>0){
                selList = "'" + classes[0].text;
                for (let index = 1; index < classes.length; index++) {
                    const element = classes[index];
                    selList = selList + "','" + element.text;
                }
                selList = selList + "'";
                // classes.forEach(element => {
                // console.log(element.id);
                // console.log(element.text);
                // });
                // console.log(selList);
            }
            var extraFields=$('#selectFields').select2('data');
            // console.log(extraFields);
            
            DTcolumns = [{"data": 'stdname', "title":"Ονοματεπώνυμο"},{"data": 'class_name', "title":"Τάξη"}];
            fieldList=[];
            extraFields.forEach(element => {
                fieldList.push(element.id);
                DTcolumns.push({"data": element.id, "title": element.text});
            });
            if (selList!=""){
                    generateTable(selList, reportType, fieldList);
                }
        });

        oTable1 = $('#tbl1').DataTable({
            buttons: [
                // 'copy', 
                {
                    extend: 'copy',
                    footer: true,
                    title: function() {
                        return "Διδάσκοντες ανα μαθητή";
                    },
                    exportOptions: {
                        orthogonal: "exportCopy",
                        grouped_array_index: 'Ονοματεπώνυμο',
                        columns: ':visible',
                    }
                },
                // 'excel', 
                {
                    extend: 'excel',
                    title: function() {
                        return "Διδάσκοντες ανα μαθητή";
                    },
                    exportOptions: {
                        orthogonal: "exportExcel",
                        grouped_array_index: 'Ονοματεπώνυμο',
                        columns: ':visible',
                    }
                },
                // 'pdf', 
                {
                    extend: 'pdf',
                    title: function() {
                        return "Διδάσκοντες ανα μαθητή";
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
                        oTable1.page.len( -1 ).draw(); //show all pages to be able to use the jquery selector for highlighting the names!
                        // get all table rows from html table
                        $('#tbl1').find('tr').each(function(ix, row) {
                            // console.log(ix);
                            var index = ix;
                            // var rowElt = row;
                            $(row).find('td').each(function(ind, elt) {
                                // console.log([ind, elt]);
                                // console.log(tblBody[index][1].text);
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
                        oTable1.page.len(10).draw(); //restore pagination length to default!
                    },
                    exportOptions: {
                        orthogonal: "exportPdf",
                        // columns: [2, 0, 1],
                        grouped_array_index: 'Ονοματεπώνυμο',
                        columns: ':visible',
                    }
                },
                // 'print'
                {
                    extend: 'print',
                    // autoPrint: false,
                    customize: function(win) {
                        $(win.document.body).find('td:empty').parent()
                            // .addClass( 'compact' )
                            .css('background-color', 'lightgray');
                    },
                    title: function() {
                        return "Διδάσκοντες ανα μαθητή";
                    },
                    exportOptions: {
                        orthogonal: "exportPrint",
                        grouped_array_index: 'Ονοματεπώνυμο',
                        columns: ':visible',
                    }
                },
            ],
            dom: 'Blrtip',
            processing: true,
            columns: [{
                    "data": "title"
                },
                {
                    "data": 'nickname'
                },
                {
                    "data": "Ονοματεπώνυμο"
                },
                // {
                //     "data": "class_name"
                // }
            ],
            // paging: false,
            ordering: false,
            order: [
                [2, 'asc']
            ],
            rowGroup: {
                dataSrc: "Ονοματεπώνυμο"
            },
            columnDefs: [{
                targets: [2],
                visible: false
            }],
            language: DTlanguage
        })

        //bootstrap3 style fixes until datatables 1.10 is released with bootstrap3 support


        $('#tbl1').css('width', '100%');
        $(".dt-buttons").hide();
        $('#tbl1_length').hide();
        $('#tbl1_info').hide();
        //------------------------------------------------------------------------------------


        //bootstrap3 style fixes until datatables 1.10 is released with bootstrap3 support


        $('#tbl1_length').css({"margin-top": "10px"});
        $('#tbl1_length').css({"text-align": "left"});
        $(".dt-buttons").css({"margin-bottom": "10px"})


        $(window).on("resize", function(e) {
            checkScreenSize();
        });
        checkScreenSize();




    }) //end of (document).ready(function())



    function generateTable(selList, reportType, fieldList=null) {
        // console.log(DTcolumns);
        // console.log(reportType);
        if (reportType=='teachersPerStudent'){
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('reports/getstudentsTeachersPerClass') ?>",
                data: {
                    selList: selList
                },
                dataType: 'json',
                success: function(data) {
                    if (data != false) {
                        $('#tbl2').hide();
                        $('#tbl2_length').hide();
                        $('#tbl2_info').hide();
                        $("#tbl2_wrapper .dt-buttons").hide();
                        $('#tbl2_paginate').hide();

                        $('#tbl1').show();
                        $('#tbl1_length').show();
                        $('#tbl1_info').show();
                        $("#tbl1_wrapper .dt-buttons").show();
                        $('#tbl1_paginate').show();
                        oTable1.clear();
                        oTable1.rows.add(data.aaData).draw();
                    }
                }
            });
        } else  {
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('reports/getstudentsPerClass') ?>",
                data: {
                    selList: selList,
                    fieldList: fieldList
                },
                dataType: 'json',
                success: function(data) {
                    if (data != false) {
                        // console.log(data.aaData);
                        $('#tbl1').hide();
                        $('#tbl1_length').hide();
                        $('#tbl1_info').hide();
                        $("#tbl1_wrapper .dt-buttons").hide();
                        $('#tbl1_paginate').hide();
                        // oTable1.clear();
                        
                        if ($.fn.DataTable.isDataTable('#tbl2')) {
                             $('#tbl2').DataTable().clear().destroy(); //in order to be re-initialized with different number of columns
                             $('#tbl2>thead').remove(); //this is needed to prevent the headerCells[i] is undefined error
                        }
                        $('#tbl2').DataTable({
                            data:data.aaData,
                            dom: 'Blrtip',
                            processing: true,
                            columns: DTcolumns,
                            // paging: false,
                            ordering: false,
                            order: [
                                [1, 'asc']
                            ],
                            rowGroup: {
                                dataSrc: "class_name"
                            },
                            columnDefs: [{
                                targets: [1],
                                visible: false
                            }],
                            language: DTlanguage
                        });
                        
                        $('#tbl2').css('width', '100%');
                        $('#tbl2_length').hide();
                        $('#tbl2_info').hide();
                        $('#tbl2_length').css({"margin-top": "10px"});
                        $('#tbl2_length').css({"text-align": "left"});
                        $('#tbl2').show();
                        $('#tbl2_length').show();
                        $('#tbl2_info').show();
                        $("#tbl2_wrapper .dt-buttons").show();
                        $('#tbl2_paginate').show();
                        checkScreenSize();
                    }
                }
            });
        }
    }

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
                    <li class="active"><a href="<?php echo base_url('reports/initial') ?>">Συγκεντρωτικές Αναφορές</a></li>
                    <li class="active">Αναφορές</li>
                    <li class="active">Διδάσκοντες ανά μαθητή / Λίστα μαθητών ανα τάξη</li>
                </ul>
            </div>

            <!-- <p>
                <h3>
                    Αναφορές
                </h3>
            </p> -->


            <ul class="nav nav-tabs">
                <li><a href="<?php echo base_url('reports/studentscount') ?>">Δυναμολόγιο</a></li>
                <li class="active"><a href="<?php echo base_url('reports/studentteachers') ?>">Διδάσκοντες ανά μαθητή / Λίστα μαθητών ανα τάξη</a></li>
            </ul>

            <p></p>


            <div class="row">

                <div class="col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <span class="icon">
                                <i class="icon-file-text"></i>
                            </span>
                            <h4 class="panel-title">
                                Διδάσκοντες ανά μαθητή / Λίστα μαθητών ανα τάξη
                            </h4>
                        </div>

                        <div class="panel-body">
                        <form>
                            <div class="row">
                                    <div class="col-xs-12">
                                        <label>Επιλογή Αναφοράς:</label>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="optionsRadios" id="optionsRadios1" value="teachersPerStudent">
                                                Διδάσκοντες ανά μαθητή
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="optionsRadios" id="optionsRadios2" value="studentsPerClass" checked>
                                                Λίστα μαθητών ανά τάξη
                                            </label>
                                        </div>
                                    </div>
                            </div>
                            <div class="row">
                                    <!-- <div class="col-xs-12">
                                    <label>Επιλογή τάξεων:</label>
                                    </div> -->
                                    <div class="col-sm-5 col-xs-12">
                                        <div class="form-group">
                                            <label for="selectClass">Επιλογή τάξεων:</label>
                                            <input type="text" class="form-control" style="margin-bottom: 10px;" id="selectClass"></input>
                                        </div>
                                    </div>
                              
                                    <div class="col-sm-5 col-xs-12">
                                        <div class="form-group">
                                            <label for="selectFields">Επιλογή επιπλέον πεδίων:</label>
                                            <input type="text" class="form-control" style="margin-bottom: 10px;" id="selectFields"></input>
                                        </div>
                                    </div>                                      
                                    <div class="col-sm-2 col-xs-12">
                                        <button type="button" id='generateReport'  style="margin-top: 24px;" class="btn btn-primary pull-right">Δημιουργία</button>
                                    </div>

                            </div>
                        </form>

                            <table id="tbl1" class="table datatable" style="display:none;">
                                <thead>
                                    <tr>
                                        <th>Μάθημα</th>
                                        <th>Διδάσκων</th>
                                        <th>Ονοματεπώνυμο</th>
                                        <!-- <th>Τάξη</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>


                            <table id="tbl2" class="table datatable" style="display:none;">
                                <!-- <thead>
                                    <tr>
                                    </tr>
                                </thead> -->
                                <tbody>
                                </tbody>
                            </table>

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