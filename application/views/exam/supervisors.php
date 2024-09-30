<script type="text/javascript">

    function toggleedit(togglecontrol, id) {

        if ($(togglecontrol).hasClass('active')) {
            $('#' + id).closest('.mainform').find(':input').each(function() {
                $(this).attr('disabled', 'disabled');
                $(this).find('btn').attr('disabled', 'disabled');
            });
            // if(id=="editform1"){
            //     $('#supervisors').select2('disable');
            // }
        } else {
            $('#' + id).closest('.mainform').find(':input').removeAttr('disabled');
            $(this).find('btn').removeAttr('disabled');
            // if(id=="editform1"){
            //     $('#supervisors').select2('enable');
            // }
        };

    }

    $(document).ready(function() {

        //Menu current active links and Title
        $('#menu-exams').addClass('active');
        $('#menu-header-title').text('Επιτηρήσεις');


        $('#cancelbtn').click(function() {
            window.open("<?php echo base_url('exam/supervisors');?>", '_self', false);
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

        $('.tutors').select2();


        pageSize = 1;
        pagesCount = $(".content").length;
        var currentPage = 1;
        
        /////////// PREPARE NAV ///////////////
        var nav = '';
        var totalPages = Math.ceil(pagesCount / pageSize);
        for (var s=0; s<totalPages; s++){
            nav += '<li class="numbers"><a href="#">'+(s+1)+'</a></li>';
        }
        // $(".pag_prev").after(nav); //uncomment this for Previous / Next to be used in pagination
        $(".pagination").append(nav); //comment this for Previous / Next to be used in pagination
        $(".numbers").first().addClass("active");
        //////////////////////////////////////

        showPage = function() {
            $(".content").hide().each(function(n) {
                if (n >= pageSize * (currentPage - 1) && n < pageSize * currentPage)
                    $(this).show();
            });
        }
        showPage();


        $(".pagination li.numbers").click(function() {
            $(".pagination li").removeClass("active");
            $(this).addClass("active");
            currentPage = parseInt($(this).text());
            showPage();
        });

        //uncomment below for Previous / Next to be used in pagination

        // $(".pagination li.pag_prev").click(function() {
        //     if($(this).next().is('.active')) return;
        //     $('.numbers.active').removeClass('active').prev().addClass('active');
        //     currentPage = currentPage > 1 ? (currentPage-1) : 1;
        //     showPage();
        // });

        // $(".pagination li.pag_next").click(function() {
        //     if($(this).prev().is('.active')) return;
        //     $('.numbers.active').removeClass('active').next().addClass('active');
        //     currentPage = currentPage < totalPages ? (currentPage+1) : totalPages;
        //     showPage();
        // });

    }); //end of (document).ready(function())

</script>

<style>
    h5 {
        font-size: 14px;
        font-family: 'Play', sans-serif;
        font-weight: 700;
    }
</style>

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
                    <li class="active">Επιτηρήσεις</li>
                </ul>
            </div>

            <ul class="nav nav-tabs">
                <li><a href="<?php echo base_url('exam')?>/">Διαγωνίσματα</a></li>
                <li class="active"><a href="<?php echo base_url('exam/supervisors/')?>">Επιτηρήσεις</a></li>
            </ul>

            <p></p>


            <div class="row">
                <div class="col-md-12">
                    <form action="<?php echo base_url() ?>exam/supervisors/" method="post" accept-charset="utf-8" role="form">

                        <div class="row">
                            <div class="col-md-12" id="group1">
                                <div class="mainform">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <span class="icon">
                                                <i class="icon-tag"></i>
                                            </span>
                                            <h3 class="panel-title">Επιτηρήσεις</h3>
                                            <div class="buttons">
                                                <button enabled id="editform1" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
                                            </div>
                                        </div>
                                        <div class="panel-body">
                                            <?php if(!empty($exams)):?>
                                                <!-- TO BE IMPLEMENTED USING JS... -->
                                                <!-- <div class="row">
                                                    <div class="col-xs-6">
                                                        <label>Διαγωνίσματα ανά σελίδα:
                                                            <select id="examtable_length" aria-controls="examtable" class="form-control input-xs">
                                                                <option value="10">10</option>
                                                                <option value="25">25</option>
                                                                <option value="50">50</option>
                                                                <option value="100">100</option>
                                                            </select> 
                                                        </label>    
                                                    </div>
                                                </div> -->
                                                <?php $pagec = 0?>
                                                <div class="row">
                                                    <div class="col-xs-3"><h5>Ημερομηνία</h5></div>
                                                    <div class="col-xs-3"><h5>Ώρα</h5></div>
                                                    <div class="col-xs-6"><h5>Επιτηρητές</h5></div>
                                                </div>
                                            <?php foreach ($exams as $id => $exam):?>
                                                <!-- pagination start group -->
                                                <!-- I can add a select field to change the numbers of rows for each page. Now it defaults to 10! -->
                                                <?php if($pagec%10==0 || $pagec==0):?>
                                                    <div class='row content'>
                                                        <div class="col-xs-12">
                                                <?php endif;?>
                                                                                                    
                                            <div class="row">
                                                <div class="col-xs-3">
                                                    <?php echo implode('-', array_reverse(explode('-', $exam['date']))); ?>
                                                </div>
                                                <div class="col-xs-3">
                                                    <?php echo date('H:i', strtotime($exam['start'])).'-'.date('H:i', strtotime($exam['end'])); ?>
                                                </div>
                                                <div class="col-xs-6">
                                                    <div class="form-group">
                                                        <select placeholder="" multiple  disabled   name="supervisors[<?php echo $id;?>][]" class="form-control tutors">
                                                            <?php if($tutor):?>
                                                                <?php foreach ($tutor as $tid=>$title):?>
                                                                    <option value="<?php echo $title['id'];?>"
                                                                    <?php
                                                                        if(array_key_exists($id, $supervisor)) {
                                                                            if (in_array($title['id'], $supervisor[$id]))
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
                                            <!-- pagination end group -->
                                            <?php $pagec++;?>
                                            <?php if($pagec%10==0 && $pagec>1):?>
                                                    </div>
                                                </div>
                                            <?php endif;?>
                                            
                                            <?php endforeach;?>
                                            <?php else:?>
                                            <div class="alert alert-danger" role="alert">
                                                Δεν υπάρχουν προγραμματισμένα διαγωνίσματα! 
                                            </div>
                                            <?php endif;?>


                                        </div>



                                    </div> <!-- end of content row -->

                                    <nav class="text-right">
                                        <ul class="pagination">
                                        <!-- uncomment below for Previous / Next to be used in pagination -->
                                            <!-- <li class="pag_prev">
                                                <a href="#" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                            <li class="pag_next">
                                                <a href="#" aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li> -->
                                        </ul>
                                    </nav>

                                </div>
                            </div>
                        </div><!-- end of exam data    -->
                    



                </div> <!--end of panel-->
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="btn-toolbar">
                        <div class="btn-group">
                            <button id="submitbtn" type="submit" class="btn btn-primary">Αποθήκευση</button>
                            <button id="cancelbtn" type="button" class="btn btn-default">Ακύρωση</button>
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
