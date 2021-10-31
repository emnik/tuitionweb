<script type="text/javascript">
  $(document).ready(function() {

    //Menu current active links and Title
    // $('#menu-management').addClass('active');
    $('#menu-staff').addClass('active');
    $('#menu-header-title').text('Καρτέλα Εργαζομένου');

    $('#togglesections').click(function(e) {
      e.preventDefault();
      $(this).addClass('active');
      $(".nav.nav-pills li a").each(function() {
        $(this).removeClass('active');
      });
      // $(this).addClass('active');
      // e.preventDefault();
      $(".panel").each(function() {
        $(this).show();
      })
    })

    $('#togglesections').tooltip({
      title: 'Εμφάνιση τμημάτων όλων των τάξεων',
      trigger: 'hover',
      placement: 'right',
      container: 'body'
    });

    <?php foreach ($section_summary as $class => $classdata) : ?>
      $("li[data-classname='<?php echo $class; ?>'] > a").on('click', function(e) {
        $(".nav.nav-pills li a").each(function() {
          $(this).removeClass('active');
        });
        $('#togglesections').removeClass('active');
        $(this).addClass('active');
        e.preventDefault();
        $(".panel[data-classname='<?php echo $class; ?>']").each(function() {
          $(this).show();
        })
        $(".panel:not([data-classname='<?php echo $class; ?>'])").each(function() {
          $(this).hide();
        })
      })
    <?php endforeach; ?>

    $('#searchbox').on('change', function() {
      str = $(this).val();
      // console.log(str);
      window.find(str, 0, 0, 1, 0, 0);
      $(this).val('');
    })

    $('.footable').footable();
  });
</script>

</head>

<body>
  <div class="wrapper">
    <!--body wrapper for css sticky footer-->

    <!-- Menu start -->
    <!-- dirname(__DIR__) gives the path one level up by default -->
    <?php include(dirname(__DIR__) . '/include/menu.php'); ?>
    <!-- Menu end -->

    <!-- main container
================================================== -->

    <div class="container" style="margin-bottom:60px;">

      <div>
        <ul class="breadcrumb">
          <li><a href="<?php echo base_url() ?>"><i class="icon-home"> </i> Αρχική </a></li>
          <li><a href="<?php echo base_url() ?>staff">Προσωπικό</a> </li>
          <li><a href="<?php echo base_url() ?>staff/card/<?php echo $employee['id'] ?>">Καρτέλα εργαζομένου</a> </li>
          <li class="active">Τμήματα</li>
        </ul>
      </div>

      <p>
        <h3><?php echo $employee['surname'] . ' ' . $employee['name'] ?></h3>
      </p>

      <ul class="nav nav-tabs">
        <li><a href="<?php echo base_url() ?>staff/card/<?php echo $employee['id'] ?>">Προφίλ</a></li>
        <li class="active"><a href="<?php echo base_url() ?>staff/card/<?php echo $employee['id'] ?>/teachingplan">Πλάνο Διδασκαλίας</a></li>
        <li><a href="<?php echo base_url() ?>staff/card/<?php echo $employee['id'] ?>/gradebook">Βαθμολόγιο</a></li>
      </ul>

      <div class="row">
        <div class="col-md-12">
          <div class="btn-toolbar" style="margin:15px 0px;">
            <a class="btn btn-default" href="<?php echo base_url(); ?>staff/card/<?php echo $employee['id'] ?>/teachingplan"><i class="icon-chevron-left"></i> πίσω</a>
            <a id="togglesections" class="btn btn-default active" href="#"><i class="icon-tags"></i></a>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-1 col-sm-2">
          <h4 style="margin-top: 17px; margin-bottom:0px;">Τάξεις:</h4>
        </div>
        <div class="col-md-8 col-sm-10">
          <ul class="nav nav-pills">
            <!-- <li data-classname="all">
              <a class="active" href="#">
                  Όλες
              </a>
            </li> -->
            <?php foreach ($section_summary as $class => $classdata) : ?>
              <li data-classname="<?php echo $class; ?>">
                <a href="#">
                  <?php echo $class; ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="col-md-3 col-sm-4 col-xs-6">
          <div class="input-group" style="margin-top: 10px;">
            <div class="input-group-addon"><i class="icon-search"></i></div>
            <input id="searchbox" type="text" class="form-control">
          </div>
        </div>
      </div>

      <p></p>

      <div class="row">

        <?php if (empty($section_summary)) : ?>
          <div class="col-md-12">
            <p class="text-info">
              Δεν έχουν αντιστοιχιστεί τμήματα για το συγκεκριμένο εργαζόμενο!
            </p>
          </div>
        <?php else : ?>
          <?php foreach ($section_summary as $class => $classdata) : ?>
            <div class="row" data-classname-category="<?php echo $class; ?>">
              <div class="col-md-12">
                <?php $c = 0;
                foreach ($classdata as $data) : ?>
                  <?php if ($c % 2 == 0) : ?>
                    <div class="row">
                    <?php endif; ?>
                    <div class="col-xs-12 col-md-6">
                      <div class="panel panel-default" data-classname="<?php echo $data['class_name']; ?>" id="<?php echo $data['id']; ?>">
                        <div class="panel-heading">
                          <span class="icon">
                            <i class="icon-tag"></i>
                          </span>
                          <h3 class="panel-title"><?php $c++;
                                                  echo $data['section'] . ' / ' . $data['title']; ?></h3>
                        </div>
                        <div class="panel-body">
                          <?php if($program):?>
                          <p class="section-info-divider">Πρόγραμμα</p>
                          <table class="table table-condensed">
                            <thead>
                              <tr>
                                <th>Μέρα</th>
                                <th>Ώρα</th>
                                <th>Αίθουσα</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php foreach ($program as $sectionprogram) : ?>
                                <?php if ($sectionprogram['id'] == $data['id']): ?>
                                  <?php if($sectionprogram['duration']>0):?>
                                    <tr>
                                      <td><?php echo $sectionprogram['day']; ?></td>
                                      <td><?php echo substr($sectionprogram['start_tm'], 0, 5) . ' - ' . substr($sectionprogram['end_tm'], 0, 5); ?></td>
                                      <td><?php echo $sectionprogram['classroom']; ?></td>
                                    </tr>
                                  <?php else:?>
                                    <tr>
                                      <td colspan="3"> Δεν έχει εισαχθεί πρόγραμμα για το τμήμα! </td>
                                    </tr>
                                <?php endif; ?>
                                  <?php endif;?>
                              <?php endforeach; ?>
                            </tbody>
                          </table>
                          <p class="section-info-divider"> Μαθητές Τμήματος (<?php echo count($section_data[$data['id']]);?>)</p>
                          <?php endif;?>
                          <table class="footable table table-striped table-condensed ">
                            <thead>
                              <tr>
                                <th data-class="expand">Ονοματεπώνυμο</th>
                                <th data-hide="phone">Σταθερό</th>
                                <th>Κινητό</th>
                                <th data-hide="phone,tablet">Πατρώνυμο</th>
                                <th data-hide="phone,tablet">Κινητό Πατέρα</th>
                                <th data-hide="phone, tablet">Μητρώνυμο</th>
                                <th data-hide="phone, tablet">Κινητό Μητέρας</th>
                                <th data-hide="phone,tablet">Δουλειάς</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php foreach ($section_data[$data['id']] as $sdata) : ?>
                                <tr>
                                  <td><?php echo $sdata['stdname']; ?></td>
                                  <td><?php echo $sdata['home_tel']; ?></td>
                                  <td><?php echo $sdata['std_mobile']; ?></td>
                                  <td><?php echo $sdata['fathers_name']; ?></td>
                                  <td><?php echo $sdata['fathers_mobile']; ?></td>
                                  <td><?php echo $sdata['mothers_name']; ?></td>
                                  <td><?php echo $sdata['mothers_mobile']; ?></td>
                                  <td><?php echo $sdata['work_tel']; ?></td>
                                </tr>
                              <?php endforeach; ?>
                            </tbody>
                          </table>
                          <a class="btn btn-default btn-sm pull-right" href="<?php echo base_url('section/card/' . $data['id']); ?>">Διαχείριση</a>
                        </div>
                      </div>
                    </div>
                    <?php if ($c % 2 == 0 || $c == count(array_keys($classdata))) : ?>
                    </div>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>
      </div>
    <?php endif; ?>

    </div>
    <!--end of main container-->

    <div class="push"></div>
  </div> <!-- end of body wrapper-->