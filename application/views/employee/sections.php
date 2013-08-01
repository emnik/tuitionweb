<script type="text/javascript">


   $(document).ready(function() {

     // $('#help').popover({
     //    placement:'bottom',
     //    container:'body',
     //    html:'true',       
     //    title:'<h4>Συνήθεις επεξεργασίες</h4>',
     //    content:"<ul><li>Για επεξεργασία ώρας και αίθουσας ενός μαθήματος πατήστε την επιλογή 'Eπεξεργασία' δεξιά του μαθήματος αυτού.</li></ul>"
     // });

     $('.footable').footable();

   });


</script>

</head>
<body>
 <div class="wrapper"> <!--body wrapper for css sticky footer-->

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <!--<a class="brand" href="#">Tuition manager</a>-->
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li><a href="<?php echo base_url()?>">Αρχική</a></li> 
              <li><a href="<?php echo base_url()?>student">Μαθητολόγιο</a></li>
              <li class="active"><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
              <li><a href="#sections">Τμήματα</a></li>
              <li><a href="#finance">Οικονομικά</a></li>
              <li><a href="#reports">Αναφορές</a></li>
              <li><a href="#admin">Διαχείριση</a></li>
            </ul>
            <ul class="nav pull-right">
              <li><a href="#"><i class="icon-off"></i> Αποσύνδεση</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>


<!-- Subhead
================================================== -->
<div class="jumbotron subhead">
  <div class="container">
    <h1>Καρτέλα Εργαζομένου</h1>
    <p class="leap">tuition manager - πρόγραμμα διαχείρισης φροντιστηρίου.</p>
  </div>
</div>


<!-- main container
================================================== -->

  <div class="container" style="margin-bottom:60px;">
  
    <div class="container-fluid">
      
      <div style="margin-top:20px; margin-bottom:-15px;">
      <ul class="breadcrumb">
        <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a><span class="divider">></span></li>
        <li><a href="<?php echo base_url()?>staff">Προσωπικό</a> <span class="divider">></span></li>
        <li><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>">Καρτέλα εργαζομένου</a> <span class="divider">></span></li>
        <li class="active">Τμήματα</li>
      </ul>
      </div>
      
      

      <h3><?php echo $employee['surname'].' '.$employee['name']?></h3>
      
      <ul class="nav nav-tabs">
        <li><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>">Στοιχεία</a></li>
        <li class="active"><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>/teachingplan">Πλάνο Διδασκαλίας</a></li>
      </ul>

      <div class="row-fluid">
        <div class="span12">
          <div class="btn-toolbar">
            <a class="btn" href="<?php echo base_url();?>staff/card/<?php echo $employee['id']?>/teachingplan"><i class="icon-chevron-left"></i> πίσω</a>
            <div class="btn-group">
            </div>
<!--             <buttton type="button" class="btn pull-right" id="help">Βοήθεια</button>    -->         
          </div>
        </div>
      </div>

      <div class="row-fluid">
      	
        <div class="span5">
      		<h4>Τμήματα:</h4>
        </div>

        <div class="span6">

          <ul class="nav nav-pills">
            <?php foreach ($section as $data):?>
            <li>
                <a href="#<?php echo $data['id']?>"><?php echo $data['section'].'/'.mb_substr($data['title'], 0, 1, 'UTF-8' /* (the correct encoding) */);?></a>
            </li>
            <?php endforeach;?>
          </ul>

        </div>

      </div>


	      	<div class="row-fluid">
		      	<div class="span12"> 
			      		<?php if(empty($section)):?>
			      			<p class="text-info">
			      				Δεν έχουν αντιστοιχιστεί τμήματα για το συγκεκριμένο εργαζόμενο!
			      			</p>
			      		<?php else:?>
                  <?php foreach ($section_data as $key=>$value):?>
                      <div class="contentbox" id="<?php echo $value[0]['id'];?>">
                       <div class="title">
                        <span class="icon">
                          <i class="icon-calendar"></i>
                        </span>
                        <h5><?php echo $key.' / '.$value[0]['title'];?></h5>
                      </div>
                    <div class="content">
                      <table class="footable table table-striped table-condensed " >
                      <thead>
                        <tr>
                          <th data-class="expand">Ονοματεπώνυμο</th>
                          <th data-hide="phone">Σταθερό</th>
                          <th>Κινητό</th>
                          <th data-hide="phone,tablet">Πατρώνυμο</th>
                          <th data-hide="phone,tablet">Κινητό Πατέρα</th>
                          <th data-hide="phone">Μητρώνυμο</th>
                          <th data-hide="phone">Κινητό Μητέρας</th>
                          <th data-hide="phone,tablet">Δουλειάς</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($value as $data):;?>
                        <tr>
                          <td><?php echo $data['stdname'];?></td>
                          <td><?php echo $data['home_tel'];?></td>
                          <td><?php echo $data['std_mobile'];?></td>
                          <td><?php echo $data['fathers_name'];?></td>
                          <td><?php echo $data['fathers_mobile'];?></td>
                          <td><?php echo $data['mothers_name'];?></td>
                          <td><?php echo $data['mothers_mobile'];?></td>
                          <td><?php echo $data['work_tel'];?></td>
                        </tr>
                      <?php endforeach;?>
                      </tbody>
                    </table>
                  </div>
                </div>
                  <?php endforeach;?>
            <?php endif;?>
      </div>
  	</div> <!--end of fluid container-->

  </div> <!--end of main container-->

<div class="push"></div>
</div> <!-- end of body wrapper-->