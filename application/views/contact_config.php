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
    $('#menu-operation').addClass('active');
    $('#menu-contact-services').addClass('active');
    $('#menu-header-title').text('Ρυθμίσεις Εξωτερικών API');



    $("body").on('click', '#editform1, #editform2', function() {
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
            window.open("<?php echo base_url('microsoft') ?>", '_self', false);
        });

        $("body").on('click', '#submitbtn', function() {
            $('.panel').find(':input:disabled').removeAttr('disabled');
            $('form').submit();
        })

    })
</script>

</head>

<body>
 <div class="wrapper"> <!--body wrapper for css sticky footer-->

         <!-- Menu start -->
         <?php include(__DIR__ .'/include/menu.php');?>
        <!-- Menu end -->
}
<!-- main container
================================================== -->

    <div class="container" style="padding-top:10px; padding-bottom:70px;">
      
        <div>
            <ul class="breadcrumb">
            <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a></li>
            <li>Ρυθμίσεις</li>
            <li class="active">Ρυθμίσεις Εξωτερικών API</li>
            </ul>
        </div>

        <form id='mainform' action="<?php echo base_url('contact_config') ?>" method="post" accept-charset="utf-8">
        
        <div class="row">
            <div class='col-lg-6 col-xs-12'> <!--first panel-->
                <div class="panel panel-default" id="group1"> 
                    <div class="panel-heading">
                        <span class="icon">
                            <i class="icon-envelope-alt"></i>
                        </span>
                        <h3 class="panel-title">Microsoft Teams / email (via Microsoft Graph)</h3>
                        <div class="buttons">
                            <button enabled id="editform1" type="button" class="btn btn-default btn-sm pull-right" data-toggle="button"><i class="icon-edit"></i></button>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Tenant ID</label>
                                    <input disabled type="text" class="form-control" placeholder="" id="tenantid" name="tenantid" value="<?php echo (!empty($ews['tenantid']))? $ews['tenantid']:''?>">
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Client ID</label>
                                    <input disabled type="text" class="form-control" placeholder="" id="mailclientid" name="mailclientid" value="<?php echo (!empty($ews['mailclientid']))?$ews['mailclientid']:''?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label>Client Secret</label>
                                    <input disabled type="text" class="form-control" placeholder="" id="mailclientsecret" name="mailclientsecret" value="<?php echo (!empty($ews['mailclientsecret']))?$ews['mailclientsecret']:''?>">
                                </div>
                            </div>
                            <!-- <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label>Client ID</label>
                                    <input disabled type="text" class="form-control" placeholder="" id="clientid" name="clientid" value="">
                                </div>
                            </div> -->
                        </div>                        
                    </div>
                </div>
            </div>

            <div class='col-lg-6 col-xs-12'> <!--second panel-->
                <div class="panel panel-default"  id="group2"> 
                    <div class="panel-heading">
                        <span class="icon">
                            <i class="icon-location-arrow"></i>
                        </span>
                        <h3 class="panel-title">SMS (via SMS.to)</h3>
                        <div class="buttons">
                            <button enabled id="editform2" type="button" class="btn btn-default btn-sm pull-right" data-toggle="button"><i class="icon-edit"></i></button>
                        </div>                        
                    </div>
                    <div class="panel-body">
                        <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>API Key</label>
                                <textarea rows="5" disabled class="form-control" id="apikey" name="apikey"><?php echo (!empty($smsconf['apikey'])) ? $smsconf['apikey'] : ''; ?></textarea>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
            
        </form>
        <div class="btn-group pull-right">
            <button disabled id="cancelbtn" type="button" class="btn btn-default">Ακύρωση</button>
            <button disabled id="submitbtn" type="button" class="btn btn-primary">Αποθήκευση</button>
        </div>

    </div> <!--end of main container-->
</div>
<div class="push"></div>
</div> <!-- end of body wrapper-->
